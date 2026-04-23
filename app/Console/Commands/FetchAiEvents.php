<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\ClaudeService;
use App\Services\GeminiQuotaGuard;
use App\Services\OpenAIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchAiEvents extends Command
{
    protected $signature = 'events:fetch
                            {--months=4 : Buscar eventos en los próximos N meses}
                            {--limit=15 : Máximo de eventos a importar por ejecución}
                            {--dry-run  : Mostrar sin guardar}';

    protected $description = 'Importa próximos eventos de IA usando Gemini/Claude y Eventbrite';

    public function handle(): int
    {
        $months  = (int) $this->option('months');
        $limit   = (int) $this->option('limit');
        $dryRun  = $this->option('dry-run');
        $guard   = app(GeminiQuotaGuard::class);

        $this->info("Buscando eventos de IA para los próximos {$months} meses...");

        $events = $this->fetchFromEventbrite($months, $limit);

        if (empty($events)) {
            $this->warn('Eventbrite sin resultados, usando IA como fuente...');
        }

        $aiEvents = $this->fetchFromAI($months, $guard);
        $events   = array_merge($events, $aiEvents);

        if (empty($events)) {
            $this->warn('Sin eventos encontrados.');
            return Command::SUCCESS;
        }

        $imported = 0;
        foreach (array_slice($events, 0, $limit) as $ev) {
            if (empty($ev['title']) || empty($ev['start_date'])) continue;

            $exists = Event::where('url', $ev['url'] ?? '')
                ->orWhere(function ($q) use ($ev) {
                    $q->where('title', $ev['title'])
                      ->where('start_date', $ev['start_date']);
                })->exists();

            if ($exists) {
                $this->line("  DUPL: {$ev['title']}");
                continue;
            }

            if ($dryRun) {
                $this->line("  [dry-run] {$ev['title']} ({$ev['start_date']})");
                $imported++;
                continue;
            }

            $slug = $this->uniqueSlug(Str::slug($ev['title']));

            Event::create([
                'title'       => $ev['title'],
                'slug'        => $slug,
                'description' => $ev['description'] ?? null,
                'type'        => $ev['type'] ?? 'conference',
                'start_date'  => $ev['start_date'],
                'end_date'    => $ev['end_date'] ?? null,
                'location'    => $ev['location'] ?? null,
                'is_online'   => $ev['is_online'] ?? false,
                'url'         => $ev['url'] ?? null,
                'image'       => $ev['image'] ?? null,
                'organizer'   => $ev['organizer'] ?? null,
                'is_free'     => $ev['is_free'] ?? false,
                'price'       => $ev['price'] ?? null,
                'featured'    => $ev['featured'] ?? false,
                'active'      => true,
            ]);

            $this->info("  OK: {$ev['title']} ({$ev['start_date']})");
            $imported++;
        }

        $this->info("Total eventos importados: {$imported}");
        return Command::SUCCESS;
    }

    protected function fetchFromEventbrite(int $months, int $limit): array
    {
        $token = env('EVENTBRITE_TOKEN', '');
        if (empty($token)) return [];

        try {
            $r = Http::timeout(20)->withToken($token)->get('https://www.eventbriteapi.com/v3/events/search/', [
                'q'            => 'artificial intelligence',
                'start_date.range_start' => now()->format('Y-m-d\TH:i:s\Z'),
                'start_date.range_end'   => now()->addMonths($months)->format('Y-m-d\TH:i:s\Z'),
                'categories'   => '102',
                'sort_by'      => 'date',
                'expand'       => 'venue',
                'page_size'    => min($limit, 50),
            ]);

            if ($r->failed()) return [];

            $events = [];
            foreach ($r->json('events', []) as $item) {
                $events[] = [
                    'title'       => $item['name']['text'] ?? '',
                    'description' => strip_tags($item['description']['text'] ?? ''),
                    'type'        => 'conference',
                    'start_date'  => substr($item['start']['utc'] ?? '', 0, 10),
                    'end_date'    => substr($item['end']['utc'] ?? '', 0, 10) ?: null,
                    'location'    => $item['venue']['address']['localized_address_display'] ?? null,
                    'is_online'   => (bool) ($item['online_event'] ?? false),
                    'url'         => $item['url'] ?? null,
                    'is_free'     => (bool) ($item['is_free'] ?? false),
                    'organizer'   => null,
                    'featured'    => false,
                ];
            }
            return $events;
        } catch (\Exception $e) {
            Log::warning('FetchAiEvents Eventbrite error: ' . $e->getMessage());
            return [];
        }
    }

    protected function fetchFromAI(int $months, GeminiQuotaGuard $guard): array
    {
        $today  = now()->format('Y-m-d');
        $until  = now()->addMonths($months)->format('Y-m-d');

        $prompt = <<<PROMPT
Hoy es {$today}. Necesito un listado de conferencias, webinars, summits y workshops importantes sobre Inteligencia Artificial que ocurran entre {$today} y {$until}.

Incluye principalmente:
- Conferencias académicas: NeurIPS, ICML, ICLR, ACL, CVPR, ICCV, ECCV, AAAI, IJCAI, etc.
- Summits de industria: AI Summit, World Summit AI, etc.
- Eventos de empresas: OpenAI DevDay, Google I/O (si tiene componente IA relevante), etc.
- Webinars y workshops relevantes sobre LLMs, agentes IA, ética IA

Para cada evento responde SOLO en JSON (array de objetos) con estos campos exactos:
- title: nombre completo del evento
- type: uno de conference|webinar|workshop|summit|deadline
- start_date: YYYY-MM-DD
- end_date: YYYY-MM-DD o null
- location: ciudad y país, o null si es online
- is_online: true|false
- url: URL oficial conocida o null
- organizer: organización principal
- description: 2 oraciones describiendo el evento en español
- is_free: true|false
- featured: true si es de los más importantes del año, false si no

Devuelve solo el array JSON, sin texto adicional. Máximo 20 eventos.
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
        $openai      = app(OpenAIService::class);

        try {
            if ($openai->isAvailable()) {
                $data = $openai->generateJson($prompt, 2000, 0.2);
                if (is_array($data) && !empty($data)) {
                    return isset($data[0]) ? $data : ($data['events'] ?? []);
                }
            }
        } catch (\Exception) {}

        try {
            if (!empty($geminiKey) && $guard->canCall('high')) {
                $r = Http::timeout(45)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 3000, 'responseMimeType' => 'application/json'],
                    ]
                );

                if ($r->successful()) {
                    $raw  = $r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                    $data = json_decode($raw, true);
                    if (is_array($data)) {
                        $guard->record();
                        return $data;
                    }
                }
            }
        } catch (\Exception) {}

        try {
            $claude = app(ClaudeService::class);
            if ($claude->isAvailable()) {
                $data = $claude->generateJson($prompt, 2000, 0.2);
                if (is_array($data) && !empty($data)) {
                    // Claude puede devolver {"events": [...]} o directamente [...]
                    return isset($data[0]) ? $data : ($data['events'] ?? []);
                }
            }
        } catch (\Exception) {}

        return [];
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
