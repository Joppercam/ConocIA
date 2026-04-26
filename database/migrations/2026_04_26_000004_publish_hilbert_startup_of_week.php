<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek()->toDateString();

        DB::table('startups')
            ->where('featured_week', $weekStart)
            ->where('slug', '!=', 'hilbert')
            ->update([
                'featured_week' => null,
                'featured' => false,
                'updated_at' => $now,
            ]);

        $exists = DB::table('startups')->where('slug', 'hilbert')->exists();

        $payload = [
            'name' => 'Hilbert',
            'tagline' => 'Infraestructura de crecimiento con IA para empresas B2C',
            'description' => 'Hilbert desarrolla una plataforma de inteligencia artificial que conecta datos de producto, marketing, finanzas y comportamiento de usuarios para ayudar a empresas B2C a detectar oportunidades, priorizar decisiones y ejecutar acciones de crecimiento con mayor precision.',
            'logo' => null,
            'website_url' => 'https://hilberts.ai',
            'founded_year' => 2025,
            'country' => 'Estados Unidos',
            'city' => 'San Francisco',
            'sector' => 'productivity',
            'stage' => 'series-a',
            'total_funding_usd' => 28.00,
            'last_funding_date' => '2026-04-15',
            'investors' => $this->json([
                'Andreessen Horowitz',
                'ScaleX Ventures',
                'TIBAS Ventures',
                'SV Angel',
                'Asylum Ventures',
                'Gunderson Ventures',
            ]),
            'products' => $this->json([
                'Hilbert Growth Engine para unificar datos de crecimiento B2C',
                'AI Growth Brain para razonamiento, deteccion de oportunidades y ejecucion agentica',
                'Metric layer con visibilidad del ciclo de vida del cliente',
                'Programa para startups con integracion guiada y pricing por etapa',
            ]),
            'profile_content' => $this->profileContent(),
            'key_quote' => 'Hilbert apunta a que la IA deje de ser solo una capa de analisis y se convierta en una infraestructura operativa para tomar y ejecutar decisiones de crecimiento.',
            'why_it_matters' => 'La startup llega en un momento en que muchas empresas invierten en IA sin traducirla en retorno operativo. Su apuesta es mover la conversacion desde dashboards y copilotos hacia sistemas que conectan datos, razonamiento y accion concreta sobre metricas de negocio.',
            'founder_names' => $this->json([
                'Ceyda Erten',
                'Murat Cenk Batman',
                'Nazli Tan',
                'Ozgur Akaoglu',
            ]),
            'featured_week' => $weekStart,
            'source_url' => 'https://www.axios.com/2026/04/15/exclusive-a16z-backed-hilbert-raises-28-million',
            'featured' => true,
            'active' => true,
            'auto_generated' => false,
            'last_synced_at' => $now,
            'updated_at' => $now,
        ];

        if (!$exists) {
            $payload['created_at'] = $now;
        }

        DB::table('startups')->updateOrInsert(['slug' => 'hilbert'], $payload);

        $this->clearCaches();
    }

    public function down(): void
    {
        DB::table('startups')->where('slug', 'hilbert')->delete();

        $this->clearCaches();
    }

    private function profileContent(): string
    {
        return <<<'HTML'
<h2>Por que Hilbert es la startup de la semana</h2>
<p>Hilbert entra al radar de ConocIA porque representa una de las tesis mas interesantes de la inteligencia artificial empresarial en 2026: pasar de la generacion de insights a la ejecucion de decisiones de negocio. La compania acaba de levantar una Serie A de US$28 millones liderada por Andreessen Horowitz, con el objetivo de acelerar producto, expansion global y crecimiento del equipo.</p>
<p>Su foco no esta en crear otro asistente generico ni un dashboard mas atractivo. Hilbert quiere construir una capa de infraestructura para crecimiento B2C: un sistema que tome datos dispersos de producto, marketing, finanzas y comportamiento de usuarios, los convierta en una base entendible para modelos de IA y sugiera acciones concretas sobre metricas comerciales.</p>

<h2>El problema que ataca</h2>
<p>En muchas empresas de consumo, el crecimiento depende de equipos que trabajan con datos fragmentados, definiciones distintas de una misma metrica y ciclos lentos de analisis. Un equipo detecta una anomalia en retencion, otro analiza cohortes, otro revisa presupuesto de adquisicion y otro intenta transformar todo eso en una decision operativa. El resultado suele ser conocido: muchas reuniones, varias versiones de la verdad y oportunidades que llegan tarde.</p>
<p>Hilbert propone reemplazar ese flujo por una infraestructura comun. Su software conecta datos entre equipos, estructura senales para que los modelos puedan leerlas y permite razonar sobre variables como churn, valor de vida del cliente, conversion, presupuesto, cohortes y oportunidades de expansion. La promesa es que la IA no solo explique que esta ocurriendo, sino que ayude a priorizar que hacer y cual podria ser el impacto economico de esa accion.</p>

<h2>Que producto esta construyendo</h2>
<p>La compania describe su plataforma como un motor de crecimiento B2C nativo en IA. En la practica, eso significa una combinacion de capa de datos, modelos de prediccion, razonamiento sobre metricas y agentes capaces de recomendar o ejecutar acciones de alto impacto. Hilbert habla de un sistema que observa, razona, actua y evoluciona a medida que llegan nuevos datos.</p>
<p>Un elemento importante es que su propuesta no se limita a grandes empresas. La compania tambien promueve un programa para startups que busca dar acceso temprano a una infraestructura de crecimiento que normalmente estaria reservada para organizaciones con equipos grandes de datos, producto y growth.</p>

<h2>Traccion y mercado</h2>
<p>Segun Axios, Walmart ya utiliza la plataforma para entender mejor las necesidades de sus compradores y definir acciones. La misma publicacion menciona clientes como FreshDirect, Blank Street y Levain. Para una startup en Serie A, ese tipo de adopcion es relevante porque valida una demanda concreta: las empresas quieren que la IA impacte metricas de negocio, no solo productividad individual.</p>
<p>El mercado al que apunta Hilbert es especialmente atractivo porque combina tres dolores persistentes: datos dispersos, presion por eficiencia de capital y necesidad de demostrar retorno real en proyectos de IA. En un contexto donde muchos pilotos quedan atrapados en pruebas de concepto, una plataforma orientada directamente a crecimiento puede tener una narrativa fuerte frente a directores de producto, marketing, finanzas y CEOs.</p>

<h2>Por que importa para Latinoamerica</h2>
<p>Para empresas latinoamericanas, Hilbert es interesante incluso si todavia no opera de forma masiva en la region. Su tesis anticipa hacia donde puede moverse el mercado: herramientas que no solo ayudan a escribir, resumir o consultar informacion, sino que conectan con sistemas internos, entienden metricas y proponen decisiones accionables.</p>
<p>Eso tiene implicancias para startups, retailers, fintechs, marketplaces y companias de suscripcion en Chile y America Latina. Muchas organizaciones ya tienen datos suficientes para aprender mas rapido, pero no necesariamente tienen equipos grandes de data science ni procesos maduros para convertir esos datos en acciones. Si la nueva generacion de plataformas logra reducir esa brecha, la ventaja competitiva podria moverse desde &quot;quien tiene mas datos&quot; hacia &quot;quien aprende y ejecuta mas rapido&quot;.</p>

<h2>La lectura de ConocIA</h2>
<p>Hilbert no debe leerse solo como una ronda de financiamiento llamativa. Es una senal de una etapa mas madura de la IA aplicada: menos demos aisladas y mas sistemas operativos de negocio. Su desafio sera demostrar que los agentes pueden operar sobre datos empresariales complejos con confiabilidad, trazabilidad y resultados medibles.</p>
<p>Si lo consigue, su categoria podria volverse una de las mas relevantes del software B2C: infraestructura de crecimiento aumentada por IA, donde la inteligencia artificial no vive en un chat, sino en el corazon de las decisiones comerciales.</p>
HTML;
    }

    private function json(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function clearCaches(): void
    {
        foreach ([
            'startup_of_week',
            'recent_startups_fallback',
            'home_page_data',
            'home_page_data_v2',
        ] as $key) {
            Cache::forget($key);
        }
    }
};
