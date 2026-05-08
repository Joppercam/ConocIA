<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private string $researchSlug = 'infraestructura-ia-anthropic-spacex-colossus-computo-frontera';
    private string $paperSlug = 'medir-potencia-ia-generativa-data-centers';
    private string $paperArxivId = '2604.07345';

    public function up(): void
    {
        $now = Carbon::now();

        $this->publishResearch($now);
        $this->publishPaper($now);
        $this->clearCaches();
    }

    public function down(): void
    {
        if (Schema::hasTable('research')) {
            DB::table('research')->where('slug', $this->researchSlug)->delete();
        }

        if (Schema::hasTable('conocia_papers')) {
            DB::table('conocia_papers')->where('arxiv_id', $this->paperArxivId)->delete();
        }

        $this->clearCaches();
    }

    private function publishResearch(Carbon $now): void
    {
        if (!Schema::hasTable('research')) {
            return;
        }

        $content = $this->researchContent();
        $payload = [
            'title' => 'El nuevo mapa del computo de IA: Anthropic, SpaceX y la presion sobre energia, chips y soberania digital',
            'slug' => $this->researchSlug,
            'excerpt' => 'Investigacion de ConocIA sobre el acuerdo Anthropic-SpaceX y lo que revela: la IA frontera ya no se decide solo por modelos, sino por capacidad electrica, clusters GPU, inferencia regional, residencia de datos y poder de negociacion sobre infraestructura critica.',
            'content' => $content,
            'abstract' => 'Esta investigacion analiza la alianza entre Anthropic y SpaceX/xAI como senal de una transformacion mayor: los modelos frontera se estan convirtiendo en sistemas industriales dependientes de gigawatts, clusters GPU, redes de baja latencia y disponibilidad energetica. A partir de anuncios oficiales de Anthropic, xAI, Amazon y Google/Broadcom, se propone una matriz tecnica para evaluar compute partnerships: capacidad inmediata, diversidad de hardware, riesgo de proveedor, cumplimiento, residencia de datos, costo de inferencia y efectos territoriales.',
            'summary' => 'La alianza Anthropic-SpaceX no debe leerse solo como una noticia corporativa. Es una evidencia de que la competencia en IA se desplaza hacia infraestructura fisica: energia, GPUs, interconexion, data centers y capacidad de inferencia. Para empresas, Estados y America Latina, la pregunta estrategica deja de ser que modelo usar y pasa a ser bajo que infraestructura, gobernanza y dependencia se opera ese modelo.',
            'image' => 'research-4.jpg',
            'type' => 'Infraestructura IA',
            'research_type' => 'analysis',
            'author' => 'Juan Pablo Basualdo',
            'views' => 0,
            'comments_count' => 0,
            'citations' => 1200,
            'featured' => true,
            'is_published' => true,
            'status' => 'published',
            'category_id' => $this->categoryId('Infraestructura IA', $now),
            'institution' => 'ConocIA Research Desk',
            'references' => implode(PHP_EOL, [
                'https://www.anthropic.com/news/higher-limits-spacex',
                'https://x.ai/news/anthropic-compute-partnership',
                'https://www.anthropic.com/news/anthropic-amazon-compute',
                'https://www.anthropic.com/news/google-broadcom-partnership-compute',
                'https://arxiv.org/abs/2604.07345',
            ]),
            'additional_authors' => 'ConocIA Research Desk',
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $authorId = $this->juanPabloId();

        if ($authorId !== null && Schema::hasColumn('research', 'user_id')) {
            $payload['user_id'] = $authorId;
        }

        DB::table('research')->updateOrInsert(['slug' => $this->researchSlug], $payload);
    }

    private function publishPaper(Carbon $now): void
    {
        if (!Schema::hasTable('conocia_papers')) {
            return;
        }

        $content = $this->paperContent();
        $payload = [
            'arxiv_id' => $this->paperArxivId,
            'arxiv_url' => 'https://arxiv.org/abs/2604.07345',
            'original_title' => 'Measurement of Generative AI Workload Power Profiles for Whole-Facility Data Center Infrastructure Planning',
            'original_abstract' => 'The rapid growth of generative artificial intelligence (AI) has introduced unprecedented computational demands, driving significant increases in the energy footprint of data centers. However, existing power consumption data is largely proprietary and reported at varying resolutions, creating challenges for estimating whole-facility energy use and planning infrastructure. This work presents a methodology that links high-resolution workload power measurements to whole-facility energy demand using AI training, fine-tuning and inference workloads.',
            'authors' => $this->json([
                'Roberto Vercellino',
                'Jared Willard',
                'Gustavo Campos',
                'Weslley da Silva Pereira',
                'Olivia Hull',
                'Matthew Selensky',
                'Juliane Mueller',
            ]),
            'arxiv_published_date' => '2026-04-08',
            'arxiv_category' => 'eess.SY',
            'title' => 'Medir la potencia real de la IA generativa: por que los data centers ya son parte del modelo',
            'slug' => $this->paperSlug,
            'excerpt' => 'Un paper de abril de 2026 propone medir perfiles de potencia de cargas de entrenamiento, fine-tuning e inferencia a 0,1 segundos y llevarlos a modelos de energia a nivel de instalacion. La lectura para la IA frontera es directa: sin datos electricos finos, no hay planificacion seria de compute.',
            'content' => $content,
            'key_contributions' => $this->json([
                'Mide cargas de IA generativa con resolucion de 0,1 segundos sobre GPUs NVIDIA H100.',
                'Separa entrenamiento, fine-tuning e inferencia para observar perfiles electricos distintos.',
                'Conecta mediciones de workload con demanda energetica de una instalacion completa.',
                'Usa benchmarks MLCommons y vLLM para perfilar cargas reproducibles.',
                'Publica perfiles de potencia que pueden informar planificacion de red, microgrids y generacion onsite.',
            ]),
            'practical_implications' => $this->json([
                'Los proveedores de IA necesitan planificar capacidad electrica con datos temporales finos, no solo promedios mensuales.',
                'La inferencia masiva puede producir variabilidad operacional relevante para data centers y redes electricas.',
                'Los acuerdos de compute como Anthropic-SpaceX deben evaluarse por potencia, disponibilidad, latencia y resiliencia.',
                'America Latina debe discutir data centers de IA junto con energia, agua, red electrica, residencia de datos y desarrollo local.',
                'El costo por token depende tanto del modelo como de la arquitectura fisica que lo sostiene.',
            ]),
            'difficulty_level' => 'intermedio',
            'image' => null,
            'featured' => true,
            'status' => 'published',
            'views' => 0,
            'reading_time' => $this->readingTime($content),
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('conocia_papers')->updateOrInsert(['arxiv_id' => $this->paperArxivId], $payload);
    }

    private function categoryId(string $name, Carbon $now): ?int
    {
        if (!Schema::hasTable('categories')) {
            return null;
        }

        $slug = Str::slug($name);
        $category = DB::table('categories')->where('slug', $slug)->first();

        $payload = [
            'name' => $name,
            'description' => 'Infraestructura, energia, chips, data centers y plataformas para inteligencia artificial.',
            'color' => '#38b6ff',
            'icon' => 'fa-server',
            'is_active' => true,
            'updated_at' => $now,
        ];

        if ($category) {
            DB::table('categories')->where('id', $category->id)->update($payload);

            return (int) $category->id;
        }

        $payload['slug'] = $slug;
        $payload['created_at'] = $now;

        return (int) DB::table('categories')->insertGetId($payload);
    }

    private function juanPabloId(): ?int
    {
        if (!Schema::hasTable('users')) {
            return null;
        }

        $authorId = DB::table('users')
            ->whereRaw('LOWER(name) = ?', ['juan pablo basualdo'])
            ->value('id');

        if ($authorId) {
            return (int) $authorId;
        }

        $fallbackId = DB::table('users')
            ->whereRaw('LOWER(name) LIKE ?', ['%juan%basualdo%'])
            ->orderBy('id')
            ->value('id');

        return $fallbackId ? (int) $fallbackId : null;
    }

    private function readingTime(string $content): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($content)) / 220));
    }

    private function json(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function clearCaches(): void
    {
        foreach ([
            'home_page_data',
            'home_page_data_v2',
            'research_page_data',
            'research_articles',
            'featured_research',
            'most_commented_research',
            "research_article_{$this->researchSlug}",
            'home_latest_papers',
            'home_featured_paper',
            'papers_featured',
            'papers_arxiv_cats',
            'all_categories',
            'featured_categories',
        ] as $key) {
            Cache::forget($key);
        }
    }

    private function researchContent(): string
    {
        return <<<'HTML'
<p>La alianza entre Anthropic y SpaceX/xAI alrededor de Colossus 1 es mas que una noticia de proveedores. Es una senal de que la inteligencia artificial frontera entro en una fase industrial: los limites de producto empiezan a depender de capacidad electrica, disponibilidad de GPUs, interconexion, refrigeracion, permisos, residencia de datos y velocidad para poner infraestructura en produccion.</p>

<p>Anthropic anuncio el 6 de mayo de 2026 que firmo un acuerdo con SpaceX para usar toda la capacidad de computo del data center Colossus 1. La compania afirmo que esto le da acceso a mas de 300 megawatts de nueva capacidad y mas de 220.000 GPUs NVIDIA dentro del mes, con impacto directo en Claude Pro, Claude Max, Claude Code y limites de API para modelos Opus. xAI describio Colossus 1 como infraestructura para entrenamiento, fine-tuning, inferencia y cargas HPC.</p>

<h2>Pregunta de investigacion</h2>
<p>Que revela este acuerdo sobre la evolucion tecnica y economica de la IA frontera? La hipotesis de ConocIA es que los modelos lideres ya no pueden analizarse como artefactos puramente algoritimicos. Deben entenderse como sistemas socio-tecnicos apoyados por una cadena de infraestructura fisica: energia, chips, redes, regiones, proveedores cloud y marcos regulatorios.</p>

<h2>1. El compute se vuelve una capa competitiva visible</h2>
<p>Durante la primera ola de IA generativa, la conversacion publica se concentro en calidad del modelo: razonamiento, contexto, multimodalidad, benchmarks y experiencia conversacional. En 2026, la competencia se desplaza hacia otra pregunta: quien puede servir esos modelos de manera confiable, con baja latencia, costos sostenibles y menos restricciones para usuarios intensivos.</p>

<p>Anthropic no esta actuando como si la respuesta fuera un unico proveedor. Su estrategia combina NVIDIA GPUs, AWS Trainium, Google TPUs, acuerdos con Amazon, Google/Broadcom, Microsoft/NVIDIA, Fluidstack y ahora SpaceX/xAI. Esa diversidad reduce dependencia, pero aumenta complejidad operacional. La arquitectura de IA moderna se parece menos a "un modelo en una nube" y mas a una malla de hardware heterogeneo, regiones y contratos.</p>

<h2>2. Inferencia: el nuevo cuello de botella</h2>
<p>El anuncio de Anthropic esta directamente conectado con limites de uso. Eso es importante: no se trata solo de entrenar modelos mas grandes. Se trata de operar inferencia a escala. Claude Code, Claude Max y la API de Opus requieren capacidad para sesiones largas, tool use, razonamiento prolongado, contexto amplio y picos de demanda. En esos casos, el cuello de botella no es solamente FLOPs; tambien es scheduling, colas, memoria, KV cache, latencia y aislamiento de workloads.</p>

<p>Una inferencia lenta o limitada degrada el producto aunque el modelo sea excelente. Por eso la capacidad computacional se vuelve parte de la experiencia de usuario. En IA aplicada, disponibilidad tambien es inteligencia percibida.</p>

<h2>3. Energia como variable de producto</h2>
<p>El numero de 300 MW no es decorativo. En data centers de IA, la energia deja de ser un costo de fondo y pasa a ser una restriccion estrategica. Quien consigue megawatts, permisos, conexion a red y capacidad de refrigeracion puede lanzar, escalar y vender antes. Quien no lo consigue, debe limitar usuarios, subir precios o degradar disponibilidad.</p>

<p>El paper arXiv 2604.07345, publicado en abril de 2026, ayuda a entender por que esto importa. Sus autores proponen medir perfiles de potencia de cargas de IA generativa con resolucion de 0,1 segundos y escalarlos a nivel de instalacion completa. La conclusion tecnica es clara: los promedios de consumo no bastan para planificar infraestructura. Las cargas de entrenamiento, fine-tuning e inferencia producen fluctuaciones temporales que afectan diseno de red, generacion onsite, microgrids y operacion del data center.</p>

<h2>4. Matriz para evaluar compute partnerships</h2>
<p>A partir de los anuncios revisados, proponemos evaluar este tipo de acuerdos con ocho dimensiones:</p>
<ul>
<li><strong>Capacidad inmediata:</strong> cuantos megawatts y aceleradores llegan en semanas, no en anos.</li>
<li><strong>Hardware:</strong> que mezcla existe entre NVIDIA GPUs, Trainium, TPUs u otros aceleradores.</li>
<li><strong>Workload fit:</strong> que parte se usa para entrenamiento, fine-tuning, inferencia interactiva o batch.</li>
<li><strong>Interconexion:</strong> que tan bien escala la red interna para modelos grandes y cargas multiusuario.</li>
<li><strong>Residencia de datos:</strong> que regiones permiten cumplir salud, finanzas, gobierno y datos sensibles.</li>
<li><strong>Riesgo de proveedor:</strong> que pasa si una capacidad queda indisponible o politicamente toxica.</li>
<li><strong>Costo por token:</strong> como se traduce energia, hardware, utilizacion y latencia a economia del producto.</li>
<li><strong>Impacto territorial:</strong> que efectos hay sobre redes electricas, comunidades, agua, empleo y soberania.</li>
</ul>

<h2>5. Riesgo reputacional y gobernanza</h2>
<p>La alianza con el ecosistema Musk es incomoda para Anthropic porque la compania ha construido una narrativa de seguridad, prudencia y despliegue responsable. Sin embargo, usar capacidad de computo no implica necesariamente compartir datos de clientes ni alinear politicas de producto. Un contrato de infraestructura puede operar con aislamiento, cifrado, controles de acceso, auditoria y segmentacion.</p>

<p>El problema es que la percepcion publica no siempre separa proveedor de computo, socio estrategico y co-desarrollador. Para clientes regulados, la pregunta concreta no deberia ser si aparece Musk en la foto. Deberia ser donde se procesan datos, que workloads corren en esa capacidad, como se audita el acceso, que SLAs aplican, que controles de residencia existen y como se evita lock-in operacional.</p>

<h2>6. Orbital compute: senal estrategica, no solucion inmediata</h2>
<p>Anthropic y SpaceX tambien mencionaron interes en explorar computo orbital de multiples gigawatts. La idea no debe descartarse como ciencia ficcion, pero tampoco debe confundirse con capacidad productiva inmediata. Computo en orbita exige resolver lanzamiento, mantenimiento, radiacion, disipacion termica, latencia, conectividad, reemplazo de hardware y seguridad fisica.</p>

<p>Para entrenamiento batch podria existir un caso futuro si la energia y la economia orbital son favorables. Para inferencia interactiva, especialmente agentes conversacionales o de codigo, la latencia y confiabilidad son barreras mas duras. Hoy, el valor principal de la idea es senalar que la demanda de IA esta presionando los limites terrestres de energia, suelo y refrigeracion.</p>

<h2>7. America Latina: de consumidores de API a estrategia de infraestructura</h2>
<p>La noticia tambien deberia leerse desde America Latina. Si la IA frontera depende de megawatts, data centers y acuerdos de compute, la region debe decidir si sera solo cliente de APIs extranjeras o si construira una estrategia propia de infraestructura digital.</p>

<p>Chile, por ejemplo, tiene ventajas en energia renovable, conectividad, estabilidad institucional relativa y talento tecnico. Pero eso no se convierte automaticamente en capacidad de IA. Hace falta coordinar energia, data centers, regulacion de datos, permisos, investigacion aplicada y condiciones para que la infraestructura genere valor local, no solo consumo electrico.</p>

<h2>Conclusiones</h2>
<p>Primero, la IA frontera se esta industrializando. Los laboratorios ya no son solo organizaciones de investigacion; son operadores de infraestructura critica.</p>

<p>Segundo, el compute es ahora una capa del producto. Un buen modelo con poca capacidad disponible pierde competitividad frente a uno con experiencia estable, limites razonables y menor latencia.</p>

<p>Tercero, los paises que quieran participar de esta economia deben pensar en energia, chips, red electrica, datos y talento como una sola agenda. La IA no vive en la nube. Vive en lugares concretos, consume energia concreta y depende de decisiones politicas concretas.</p>

<p>La alianza Anthropic-SpaceX muestra una verdad incomoda: la inteligencia artificial parece intangible, pero su poder se esta decidiendo en infraestructura fisica.</p>

<h2>Fuentes principales</h2>
<ul>
<li><a href="https://www.anthropic.com/news/higher-limits-spacex" target="_blank" rel="noopener noreferrer">Anthropic: Higher usage limits for Claude and a compute deal with SpaceX</a></li>
<li><a href="https://x.ai/news/anthropic-compute-partnership" target="_blank" rel="noopener noreferrer">xAI: New Compute Partnership with Anthropic</a></li>
<li><a href="https://www.anthropic.com/news/anthropic-amazon-compute" target="_blank" rel="noopener noreferrer">Anthropic and Amazon expand collaboration for up to 5 GW</a></li>
<li><a href="https://www.anthropic.com/news/google-broadcom-partnership-compute" target="_blank" rel="noopener noreferrer">Anthropic, Google and Broadcom compute partnership</a></li>
<li><a href="https://arxiv.org/abs/2604.07345" target="_blank" rel="noopener noreferrer">Measurement of Generative AI Workload Power Profiles for Whole-Facility Data Center Infrastructure Planning</a></li>
</ul>
HTML;
    }

    private function paperContent(): string
    {
        return <<<'HTML'
<h2>Por que este paper importa ahora</h2>
<p>La alianza entre Anthropic y SpaceX/xAI puso una cifra en el centro de la conversacion: mas de 300 MW de capacidad y mas de 220.000 GPUs para expandir el uso de Claude. Esa escala hace visible un problema tecnico que muchas veces queda escondido detras de la palabra "cloud": la IA generativa consume energia en patrones variables, intensos y dificiles de planificar con promedios gruesos.</p>

<p>El paper <em>Measurement of Generative AI Workload Power Profiles for Whole-Facility Data Center Infrastructure Planning</em>, presentado en arXiv el 8 de abril de 2026, aborda exactamente esa brecha. Su objetivo no es proponer otro modelo de lenguaje, sino medir como se comportan electricamente las cargas de IA generativa y como esas mediciones pueden escalarse a planificacion de un data center completo.</p>

<h2>La pregunta tecnica</h2>
<p>La pregunta del paper es sencilla y profunda: como se traduce una carga de IA especifica, ejecutada sobre GPUs modernas, en demanda electrica real para una instalacion completa?</p>

<p>Esto importa porque la informacion publica sobre consumo de data centers suele ser propietaria, agregada o publicada con resoluciones temporales distintas. Para planificar conexion a red, generacion onsite, microgrids o refrigeracion, no basta saber el consumo promedio. Hay que entender los perfiles temporales: cuanto sube, cuanto baja, con que frecuencia, bajo que tipo de workload y como se combinan usuarios y trabajos simultaneos.</p>

<h2>Metodologia</h2>
<p>Los autores trabajan con un data center HPC equipado con GPUs NVIDIA H100. Miden consumo de cargas de entrenamiento, fine-tuning e inferencia a resolucion de 0,1 segundos. Para que los perfiles sean reproducibles, caracterizan workloads usando benchmarks MLCommons para entrenamiento y fine-tuning, y benchmarks vLLM para inferencia.</p>

<p>Luego llevan esas mediciones a un modelo bottom-up, event-driven, a nivel de instalacion. Esa parte es clave: el aporte no termina en medir una GPU o un servidor. El paper conecta el comportamiento de cargas de IA con energia de todo el facility, incluyendo fluctuaciones temporales producidas por workloads y comportamiento de usuarios.</p>

<h2>Resultados que conviene leer desde la industria</h2>
<p>El primer resultado practico es que no todas las cargas de IA se parecen. Entrenamiento, fine-tuning e inferencia tienen perfiles distintos. Una instalacion que sirve APIs de baja latencia no opera igual que un cluster dedicado a entrenamiento batch. Esa diferencia afecta potencia instantanea, utilizacion, refrigeracion, colas y diseno de capacidad.</p>

<p>El segundo resultado es que la variabilidad importa. La infraestructura electrica no se dimensiona solo por energia acumulada, sino por picos, rampas, simultaneidad y resiliencia. Un cluster que parece eficiente en promedio puede generar exigencias duras si muchas cargas entran y salen al mismo tiempo.</p>

<p>El tercer resultado es que los datos abiertos de perfiles de potencia pueden ayudar a reguladores, utilities, operadores de data centers y compradores de compute a hablar con mas precision. Sin mediciones comparables, la conversacion sobre energia de IA se vuelve demasiado dependiente de marketing o estimaciones opacas.</p>

<h2>Conexion con Anthropic, SpaceX y Colossus 1</h2>
<p>El paper no trata sobre Anthropic ni sobre SpaceX, pero entrega el marco tecnico para entender por que acuerdos como Colossus 1 son tan relevantes. Si un proveedor agrega cientos de megawatts de capacidad, la pregunta no es solamente cuantas GPUs tiene. Tambien importa como se perfilan las cargas, que fraccion sera inferencia interactiva, que fraccion sera fine-tuning, que variabilidad tendra la demanda y como se integrara con energia, enfriamiento y red.</p>

<p>Para Claude Code, Claude Max y la API, el problema de inferencia es especialmente importante. Los agentes de codigo y razonamiento pueden producir sesiones largas, llamadas a herramientas, contextos extensos y patrones de uso irregulares. Eso vuelve mas valioso medir la energia con granularidad fina, porque el comportamiento real de usuarios no siempre se parece a un benchmark simple.</p>

<h2>Implicancias para empresas</h2>
<p>Para una empresa que compra IA, el paper sugiere una lectura mas madura del proveedor. No basta preguntar que modelo es mejor. Tambien hay que preguntar que estabilidad tiene la capacidad, que regiones estan disponibles, que limites existen, que latencia se espera, que compromisos de continuidad hay y como se gestionan picos de demanda.</p>

<p>En contratos enterprise, la infraestructura empieza a ser parte del due diligence. Un proveedor puede tener un modelo excelente, pero si no tiene capacidad suficiente para servir cargas criticas, la adopcion se vuelve fragil.</p>

<h2>Implicancias para Chile y Latinoamerica</h2>
<p>Para la region, este tipo de investigacion abre una pregunta incomoda: si queremos atraer data centers de IA o construir capacidad regional, necesitamos discutir energia con datos reales. No sirve mirar solo inversiones anunciadas o cantidad de racks. Hay que analizar potencia, agua, red electrica, ubicacion, resiliencia, empleo local, impuestos, residencia de datos y transferencia de capacidades.</p>

<p>Chile podria tener un rol en infraestructura de IA por energia renovable y conectividad, pero debe evitar una estrategia pasiva. Un data center de IA no es automaticamente desarrollo tecnologico local. Puede serlo si se conecta con universidades, talento, startups, investigacion aplicada, regulacion de datos y beneficios territoriales claros.</p>

<h2>Lectura final</h2>
<p>Este paper es valioso porque baja la IA generativa al suelo. No habla de inteligencia en abstracto, sino de potencia electrica, tiempos, perfiles, instalaciones y planificacion. En una industria que cada vez mide su ventaja en GPUs y gigawatts, esa mirada es imprescindible.</p>

<p>La conclusion para ConocIA es directa: el futuro de la IA no se decide solo en el laboratorio de modelos. Tambien se decide en el tablero electrico del data center.</p>
HTML;
    }
};
