<?php

namespace App\Console\Commands;

use App\Models\Startup;
use App\Services\ClaudeService;
use App\Services\GeminiQuotaGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeatureWeeklyStartup extends Command
{
    protected $signature = 'startups:feature-weekly
                            {--force    : Regenerar perfil aunque ya haya una startup destacada esta semana}
                            {--id=      : Forzar una startup específica por ID}
                            {--dry-run  : Mostrar sin guardar}';

    protected $description = 'Selecciona la startup de la semana y genera su perfil profundo con IA';

    public function handle(): int
    {
        $force  = $this->option('force');
        $forceId = $this->option('id');
        $dryRun = $this->option('dry-run');
        $guard  = app(GeminiQuotaGuard::class);

        $weekStart = now()->startOfWeek()->toDateString();

        // Verificar si ya hay una destacada esta semana
        if (!$force && !$forceId) {
            $existing = Startup::where('featured_week', $weekStart)->first();
            if ($existing) {
                $this->info("Ya hay una startup destacada esta semana: {$existing->name}");
                return Command::SUCCESS;
            }
        }

        // Seleccionar la startup a destacar
        if ($forceId) {
            $startup = Startup::find($forceId);
            if (!$startup) {
                $this->error("Startup ID {$forceId} no encontrada.");
                return Command::FAILURE;
            }
        } else {
            $startup = $this->selectBestCandidate();
        }

        if (!$startup) {
            $this->warn('No hay startups disponibles para destacar. Ejecutá startups:fetch primero.');
            return Command::SUCCESS;
        }

        $this->info("Startup seleccionada: {$startup->name}");
        $this->info('Generando perfil profundo con IA...');

        $profile = $this->generateDeepProfile($startup, $guard);

        if (empty($profile)) {
            $this->error('La IA no pudo generar el perfil. Verificá la cuota de Gemini/Claude.');
            return Command::FAILURE;
        }

        if ($dryRun) {
            $this->line("\n--- PREVIEW ---");
            $this->line("Key quote: " . ($profile['key_quote'] ?? '—'));
            $this->line("Why it matters: " . Str_limit($profile['why_it_matters'] ?? '—', 200));
            $this->line("Profile content: " . strlen($profile['profile_content'] ?? '') . " chars");
            return Command::SUCCESS;
        }

        // Limpiar featured_week de semanas anteriores sin perder los datos
        Startup::where('featured_week', $weekStart)->update(['featured_week' => null]);

        $startup->update([
            'profile_content' => $profile['profile_content'] ?? null,
            'key_quote'       => $profile['key_quote'] ?? null,
            'why_it_matters'  => $profile['why_it_matters'] ?? null,
            'founder_names'   => $profile['founder_names'] ?? null,
            'featured_week'   => $weekStart,
            'featured'        => true,
        ]);

        $this->info("Perfil guardado. Startup de la semana: {$startup->name}");
        return Command::SUCCESS;
    }

    protected function selectBestCandidate(): ?Startup
    {
        // Priorizar: con funding reciente + que nunca han sido destacadas + activas
        $candidate = Startup::active()
            ->whereNull('featured_week')
            ->whereNotNull('description')
            ->orderByDesc('total_funding_usd')
            ->orderByDesc('last_funding_date')
            ->first();

        // Fallback: cualquier startup activa que no fue destacada esta semana
        if (!$candidate) {
            $candidate = Startup::active()
                ->where(fn($q) => $q->whereNull('featured_week')
                    ->orWhere('featured_week', '<', now()->subWeeks(4)->toDateString()))
                ->first();
        }

        return $candidate;
    }

    protected function generateDeepProfile(Startup $startup, GeminiQuotaGuard $guard): array
    {
        $fundingStr = $startup->total_funding_usd
            ? "ha recaudado {$startup->funding_label} en financiamiento"
            : 'con financiamiento no divulgado';

        $investorsStr = !empty($startup->investors)
            ? 'Inversores: ' . implode(', ', (array) $startup->investors) . '.'
            : '';

        $productsStr = !empty($startup->products)
            ? 'Productos: ' . implode(', ', (array) $startup->products) . '.'
            : '';

        $stageStr = $startup->stage_label ?? '';
        $sectorLabels = \App\Models\Startup::sectorLabels();
        $sectorStr = $sectorLabels[$startup->sector] ?? $startup->sector ?? 'IA';

        $prompt = <<<PROMPT
Eres un periodista especializado en tecnología e inteligencia artificial, con el estilo de TechCrunch y Wired en español. Tu audiencia son profesionales y emprendedores del ecosistema IA en América Latina.

DATOS DE LA STARTUP:
Nombre: {$startup->name}
Tagline: {$startup->tagline}
Descripción: {$startup->description}
Sector: {$sectorStr}
Etapa: {$stageStr}
Financiamiento: {$fundingStr}
País: {$startup->country}
Fundada: {$startup->founded_year}
{$investorsStr}
{$productsStr}
Website: {$startup->website_url}

OBJETIVO: Escribir el perfil editorial de la "Startup de la Semana" de ConocIA. Debe sentirse como una nota periodística de fondo, no como una ficha técnica.

ESTRUCTURA OBLIGATORIA (usa HTML semántico):

<p class="lead">Párrafo de apertura impactante (2-3 oraciones). Presenta la startup con un ángulo que genere curiosidad. Puede empezar con el problema que resuelve o con algo sorprendente de su historia.</p>

<h3>El problema que nadie quería resolver</h3>
<p>Explica el pain point que identificaron los fundadores. Por qué existía este vacío. 2-3 párrafos.</p>

<h3>La solución y el enfoque técnico</h3>
<p>Cómo lo están resolviendo. Qué hace diferente a su tecnología o enfoque. Evita jerga innecesaria pero sin simplificar en exceso. 2-3 párrafos.</p>

<blockquote class="startup-quote">
Una cita representativa sobre la visión de la empresa. Puede ser del CEO, de un inversor, o una frase editorial que capture la esencia de lo que hacen. Entre comillas.
<cite>— Fuente o contexto de la cita</cite>
</blockquote>

<h3>El equipo detrás del proyecto</h3>
<p>Quiénes son los fundadores (si hay info pública). Qué background tienen. Por qué están en posición de resolver este problema. Si no hay datos concretos, habla del tipo de equipo que se necesita para este desafío. 1-2 párrafos.</p>

<h3>Tracción y señales del mercado</h3>
<p>Rondas de inversión, clientes notables, métricas públicas, reconocimientos. Qué dice el mercado sobre su propuesta. 1-2 párrafos.</p>

<h3>El momento oportuno</h3>
<p>Por qué ahora. Qué tendencias del ecosistema IA favorecen su crecimiento. Qué riesgos enfrenta. 1-2 párrafos.</p>

REQUISITOS:
- Extensión mínima del profile_content: 700 palabras
- Tono: periodístico, crítico pero justo, no un PR release
- En español neutro (América Latina + España)
- Solo HTML, sin markdown

Responde SOLO en JSON con estas claves:
- profile_content: el HTML completo del perfil
- key_quote: UNA frase de máximo 180 caracteres que sea el quote más poderoso sobre esta startup (para mostrar en el card del home)
- why_it_matters: 2-3 oraciones en texto plano explicando por qué esta startup importa para el ecosistema IA (para el card del home)
- founder_names: array de strings con nombres de fundadores conocidos, o array vacío si no hay datos
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');

        // Intentar con Gemini primero
        try {
            if (!empty($geminiKey) && $guard->canCall('high')) {
                $r = Http::timeout(90)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 5000, 'responseMimeType' => 'application/json'],
                    ]
                );
                if ($r->successful()) {
                    $raw  = $r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                    $data = json_decode($raw, true);
                    if (!empty($data['profile_content'])) {
                        $guard->record();
                        return $data;
                    }
                }
            }
        } catch (\Exception) {}

        // Fallback Claude
        try {
            $claude = app(ClaudeService::class);
            if ($claude->isAvailable()) {
                $data = $claude->generateJson($prompt, 5000, 0.7);
                if (!empty($data['profile_content'])) {
                    Log::info('FeatureWeeklyStartup: perfil generado con Claude.');
                    return $data;
                }
            }
        } catch (\Exception) {}

        return [];
    }
}
