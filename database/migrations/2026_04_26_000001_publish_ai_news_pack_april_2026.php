<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $categoryMeta = [
        'Modelos de IA' => [
            'description' => 'Noticias sobre nuevos modelos de inteligencia artificial, benchmarks y capacidades emergentes.',
            'color' => '#4285F4',
            'icon' => 'fa-brain',
        ],
        'Industria IA' => [
            'description' => 'Movimientos de mercado, inversiones, alianzas y estrategia empresarial en inteligencia artificial.',
            'color' => '#0F9D58',
            'icon' => 'fa-chart-line',
        ],
        'Infraestructura IA' => [
            'description' => 'Noticias sobre cloud, chips, centros de datos y plataformas para inteligencia artificial.',
            'color' => '#673AB7',
            'icon' => 'fa-server',
        ],
        'IA en Dispositivos' => [
            'description' => 'Avances de inteligencia artificial integrada en moviles, asistentes, hardware y experiencias de usuario.',
            'color' => '#FF9800',
            'icon' => 'fa-mobile-alt',
        ],
    ];

    public function up(): void
    {
        $now = Carbon::now();

        $editorId = $this->editorId();

        foreach ($this->newsItems() as $item) {
            $categoryId = $this->categoryId($item['category'], $now);
            $payload = [
                'title' => $item['title'],
                'excerpt' => $item['excerpt'],
                'summary' => $item['summary'],
                'keywords' => $item['keywords'],
                'content' => $item['content'],
                'image' => null,
                'category_id' => $categoryId,
                'author' => 'Editor',
                'views' => 0,
                'tags' => $item['tags'],
                'featured' => false,
                'is_published' => true,
                'status' => 'published',
                'source' => $item['source'],
                'source_url' => $item['source_url'],
                'published_at' => $now,
                'reading_time' => $this->readingTime($item['content']),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($editorId !== null && Schema::hasColumn('news', 'author_id')) {
                $payload['author_id'] = $editorId;
            }

            if ($editorId !== null && Schema::hasColumn('news', 'user_id')) {
                $payload['user_id'] = $editorId;
            }

            DB::table('news')->updateOrInsert(
                ['slug' => $item['slug']],
                $payload
            );
        }

        $this->clearNewsCache();
    }

    public function down(): void
    {
        DB::table('news')
            ->whereIn('slug', array_column($this->newsItems(), 'slug'))
            ->delete();

        $this->clearNewsCache();
    }

    private function categoryId(string $name, Carbon $now): int
    {
        $slug = Str::slug($name);
        $meta = $this->categoryMeta[$name] ?? [
            'description' => 'Noticias sobre inteligencia artificial.',
            'color' => '#38b6ff',
            'icon' => 'fa-brain',
        ];

        $category = DB::table('categories')->where('slug', $slug)->first();

        if ($category) {
            DB::table('categories')->where('id', $category->id)->update([
                'name' => $name,
                'description' => $meta['description'],
                'color' => $meta['color'],
                'icon' => $meta['icon'],
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $category->id;
        }

        return (int) DB::table('categories')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'description' => $meta['description'],
            'color' => $meta['color'],
            'icon' => $meta['icon'],
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function editorId(): ?int
    {
        $roleIds = DB::table('roles')
            ->whereIn('slug', ['editor', 'admin'])
            ->pluck('id');

        $editorId = DB::table('users')
            ->whereIn('role_id', $roleIds)
            ->where(function ($query) {
                $query->where('email', 'editor@conocia.com')
                    ->orWhere('username', 'editor')
                    ->orWhere('name', 'Editor');
            })
            ->orderByRaw("CASE WHEN email = 'editor@conocia.com' THEN 0 ELSE 1 END")
            ->value('id');

        if ($editorId) {
            return (int) $editorId;
        }

        $fallbackId = DB::table('users')
            ->whereIn('role_id', $roleIds)
            ->orderBy('id')
            ->value('id');

        return $fallbackId ? (int) $fallbackId : null;
    }

    private function readingTime(string $content): int
    {
        $words = str_word_count(strip_tags($content));

        return max(1, (int) ceil($words / 220));
    }

    private function clearNewsCache(): void
    {
        foreach ([
            'home_page_data',
            'home_page_data_v2',
            'all_published_news',
            'all_published_news_v2',
            'popular_news',
            'secondary_news',
            'trending_ids',
            'news_index_list',
            'most_read_articles',
            'popular_tags',
            'all_categories',
        ] as $key) {
            Cache::forget($key);
        }
    }

    private function newsItems(): array
    {
        return [
            [
                'title' => 'DeepSeek lanza V4 y reabre la competencia entre China y Estados Unidos en IA',
                'slug' => 'deepseek-lanza-v4-competencia-china-estados-unidos-ia',
                'category' => 'Modelos de IA',
                'tags' => 'DeepSeek, DeepSeek V4, China, Huawei, modelos abiertos, agentes IA',
                'keywords' => 'DeepSeek V4, IA China, modelos abiertos, Huawei, agentes IA, inteligencia artificial',
                'source' => 'AP News',
                'source_url' => 'https://apnews.com/article/d2ed33f2521917193616e061674d5f92',
                'excerpt' => 'La startup china presento versiones preview de su nuevo modelo, con mejoras en razonamiento y tareas agenticas, en medio de la competencia tecnologica con OpenAI, Anthropic y Google.',
                'summary' => 'DeepSeek lanzo versiones preview de V4, su actualizacion mas esperada desde el impacto global de R1. El modelo llega con mejoras en conocimiento, razonamiento y capacidades agenticas, ademas de soporte parcial en chips Huawei, un dato clave para la estrategia china de reducir dependencia de Nvidia.',
                'content' => <<<'TEXT'
DeepSeek volvio al centro de la carrera global por la inteligencia artificial. La startup china lanzo versiones preview de su familia V4, una actualizacion largamente esperada por usuarios, desarrolladores e inversionistas que siguen la competencia entre China y Estados Unidos en modelos frontera.

Segun AP, V4 sucede a V3, publicado a fines de 2024, y llega despues del impacto que tuvo R1 en enero de 2025. Ese modelo de razonamiento sorprendio al mercado por mostrar que una compania china podia competir con laboratorios estadounidenses a menor costo y con una estrategia mas abierta.

La nueva familia V4 incluye versiones orientadas a distintos balances entre potencia y velocidad. DeepSeek afirma que hay avances en conocimiento, razonamiento y capacidades agenticas, es decir, la posibilidad de ejecutar flujos complejos con mayor autonomia.

Uno de los puntos mas sensibles es la infraestructura. AP reporta que V4 tiene soporte parcial en chips de Huawei, lo que apunta a reducir la dependencia de aceleradores estadounidenses como los de Nvidia. En el contexto de restricciones tecnologicas entre Washington y Beijing, esa decision tiene tanto peso geopolitico como tecnico.

Para empresas y desarrolladores, la pregunta sera si V4 logra sostener rendimiento competitivo con costos atractivos. Si DeepSeek vuelve a mostrar una relacion precio-capacidad agresiva, podria presionar a proveedores cerrados y acelerar la adopcion de alternativas abiertas o semiabiertas.

En Latinoamerica, el impacto podria sentirse en startups, universidades y equipos tecnicos que buscan modelos potentes sin depender exclusivamente de planes enterprise de laboratorios estadounidenses. Aun asi, la adopcion debera considerar privacidad, cumplimiento, soporte, calidad multilingue y riesgos de gobernanza.

DeepSeek V4 no solo es otro lanzamiento de modelo. Es una senal de que la competencia por la IA se esta moviendo en tres frentes al mismo tiempo: calidad del modelo, costo de inferencia e independencia de la cadena de chips.
TEXT,
            ],
            [
                'title' => 'Google prepara una inversion de hasta US$40.000 millones en Anthropic',
                'slug' => 'google-inversion-40000-millones-anthropic',
                'category' => 'Industria IA',
                'tags' => 'Google, Anthropic, Claude, inversion IA, compute, Google Cloud',
                'keywords' => 'Google Anthropic, inversion IA, Claude, Google Cloud, computo IA, modelos frontera',
                'source' => 'TechCrunch',
                'source_url' => 'https://techcrunch.com/2026/04/24/google-to-invest-up-to-40b-in-anthropic-in-cash-and-compute/',
                'excerpt' => 'La operacion refuerza la alianza entre Google y Anthropic en un momento en que la carrera por modelos frontera depende cada vez mas del acceso a infraestructura computacional.',
                'summary' => 'Google planea invertir hasta US$40.000 millones en Anthropic, segun reportes citados por TechCrunch. La operacion combinaria una inversion inicial con compromisos adicionales ligados a metas de crecimiento, ademas de apoyo computacional para escalar Claude.',
                'content' => <<<'TEXT'
Google estaria profundizando una de las alianzas mas importantes del mercado de inteligencia artificial. Segun TechCrunch, la compania planea invertir hasta US$40.000 millones en Anthropic, combinando capital y capacidad computacional para apoyar el crecimiento del laboratorio detras de Claude.

La estructura reportada contempla US$10.000 millones iniciales y hasta US$30.000 millones adicionales si Anthropic alcanza determinadas metas. La operacion aparece en un momento de fuerte presion por infraestructura: entrenar y operar modelos frontera exige cantidades crecientes de energia, chips, centros de datos y acuerdos cloud.

El movimiento tambien confirma que la competencia en IA no se ordena de forma simple. Google desarrolla Gemini, pero al mismo tiempo fortalece a Anthropic, un rival directo en modelos empresariales y agentes de codigo. La razon estrategica es clara: si Claude crece sobre infraestructura de Google, Alphabet tambien participa en la expansion del mercado.

Anthropic viene de presentar Claude Mythos Preview bajo acceso restringido y Project Glasswing para ciberseguridad defensiva. La compania tambien ha ganado traccion con Claude Code y modelos orientados a tareas profesionales, lo que aumenta su necesidad de compute estable y a gran escala.

Para el ecosistema, la noticia muestra que el cuello de botella de la IA ya no esta solo en investigacion. La ventaja competitiva depende de quien puede financiar inferencia, entrenar nuevas generaciones de modelos y ofrecer capacidad confiable a empresas.

En Chile y Latinoamerica, esta concentracion puede tener efectos indirectos: precios de APIs, disponibilidad regional, alianzas cloud y opciones para empresas que buscan adoptar IA generativa con garantias de continuidad.

La inversion de Google en Anthropic refuerza una idea central de 2026: la IA ya es una carrera de capital, energia e infraestructura tanto como de algoritmos.
TEXT,
            ],
            [
                'title' => 'Google Cloud Next 2026 resume su apuesta por agentes empresariales y nuevos TPU',
                'slug' => 'google-cloud-next-2026-agentes-empresariales-tpu',
                'category' => 'Infraestructura IA',
                'tags' => 'Google Cloud, Gemini, agentes IA, TPU, Vertex AI, empresa',
                'keywords' => 'Google Cloud Next 2026, Gemini, agentes IA, TPU, Vertex AI, Google Cloud',
                'source' => 'Google Blog',
                'source_url' => 'https://blog.google/innovation-and-ai/infrastructure-and-cloud/google-cloud/google-cloud-next-26-recap/',
                'excerpt' => 'El resumen oficial del evento muestra como Google quiere llevar la IA generativa desde pilotos aislados hacia plataformas empresariales integradas.',
                'summary' => 'Google publico los principales anuncios de Cloud Next 2026, destacando su estrategia para agentes empresariales, infraestructura de IA y nuevos TPU. La compania busca posicionar Google Cloud como plataforma para desplegar IA generativa a escala corporativa.',
                'content' => <<<'TEXT'
Google cerro Cloud Next 2026 con un mensaje claro: la adopcion empresarial de inteligencia artificial necesita plataformas completas, no solo modelos potentes. En su recapitulacion oficial del evento, la compania destaco siete lineas de anuncios que combinan agentes, infraestructura, datos y herramientas para empresas.

Uno de los focos principales fue la transicion hacia agentes empresariales. Google busca que Gemini y su ecosistema cloud permitan construir sistemas capaces de conectarse a datos, herramientas y procesos internos bajo controles corporativos.

La infraestructura tambien tuvo protagonismo. Google presento avances vinculados a su nueva generacion de TPU, los chips disenados para entrenar y ejecutar modelos de IA. En un mercado donde el costo computacional condiciona la adopcion, controlar chips y cloud se vuelve una ventaja estrategica.

El evento tambien mostro casos de uso empresariales. Google menciono organizaciones que usan herramientas de IA para investigacion cuantitativa, marketing, productividad y operaciones, senal de que la conversacion ya no gira solo en torno a pruebas de concepto.

Para las empresas chilenas, la lectura es directa: implementar IA generativa implica tomar decisiones de arquitectura. Hay que definir donde viven los datos, que proveedor cloud se usa, como se controlan permisos, como se auditan acciones de agentes y que costos de inferencia se pueden sostener.

Google Cloud Next 2026 confirma que la IA empresarial se esta industrializando. Los agentes empiezan a pasar de demos llamativas a piezas del stack corporativo, y la infraestructura vuelve a ocupar el centro de la estrategia.
TEXT,
            ],
            [
                'title' => 'OpenAI lanza GPT-5.5 y empuja la carrera por agentes mas capaces',
                'slug' => 'openai-lanza-gpt-5-5-agentes-mas-capaces',
                'category' => 'Modelos de IA',
                'tags' => 'OpenAI, GPT-5.5, Codex, agentes IA, productividad, investigacion cientifica',
                'keywords' => 'GPT-5.5, OpenAI, Codex, agentes IA, modelos de lenguaje, inteligencia artificial empresarial',
                'source' => 'OpenAI',
                'source_url' => 'https://openai.com/index/introducing-gpt-5-5/',
                'excerpt' => 'Aunque queda justo en el borde de la ventana de 48 horas segun la fecha usada, el lanzamiento sigue siendo una de las noticias centrales de la semana por su impacto en agentes, codigo y empresas.',
                'summary' => 'OpenAI anuncio GPT-5.5, su modelo mas avanzado para flujos agenticos en Codex y ChatGPT. La compania destaca mejoras en programacion, trabajo profesional, investigacion cientifica y controles para areas sensibles como ciberseguridad y biologia.',
                'content' => <<<'TEXT'
OpenAI presento GPT-5.5, una nueva version de su familia de modelos orientada a tareas complejas de programacion, investigacion y trabajo profesional. El lanzamiento se produce en una semana marcada por la aceleracion de la competencia entre laboratorios de IA.

La compania posiciona GPT-5.5 como un modelo especialmente fuerte para Codex y flujos agenticos. Esto significa que no solo responde preguntas, sino que puede avanzar en tareas de multiples pasos, usar herramientas, revisar resultados y sostener contexto durante trabajos extensos.

OpenAI destaca mejoras en programacion, analisis documental, tareas de oficina e investigacion cientifica. En la practica, esto apunta a equipos que quieren delegar partes completas de un flujo: revisar codigo, generar reportes, analizar datos, preparar documentos o apoyar investigaciones tecnicas.

El lanzamiento tambien llega con una lectura de seguridad. OpenAI reconoce que modelos mas capaces requieren salvaguardas mas estrictas, especialmente en ciberseguridad y biologia. La compania anuncio ademas un programa de bio bug bounty para probar defensas frente a posibles jailbreaks.

Para empresas en Chile y Latinoamerica, GPT-5.5 marca otro paso hacia agentes que puedan integrarse en procesos reales. La oportunidad esta en acelerar desarrollo, analisis y operaciones; el riesgo, en desplegar capacidades sin gobernanza, trazabilidad ni revision humana.

La carrera ya no se trata solo de mejores respuestas. GPT-5.5 muestra que el valor se esta moviendo hacia modelos capaces de ejecutar trabajo completo sobre herramientas, datos y sistemas.
TEXT,
            ],
            [
                'title' => 'Google confirma que Gemini ayudara a impulsar la proxima generacion de Siri',
                'slug' => 'google-gemini-proxima-generacion-siri-apple-intelligence',
                'category' => 'IA en Dispositivos',
                'tags' => 'Google, Apple Intelligence, Siri, Gemini, IA movil, asistentes',
                'keywords' => 'Apple Intelligence, Siri, Gemini, Google Cloud, IA movil, asistentes virtuales',
                'source' => 'T3',
                'source_url' => 'https://www.t3.com/tech/ai/new-apple-intelligence-and-siri-confirmed-by-google',
                'excerpt' => 'La confirmacion aparecio en el contexto de Google Cloud Next 2026 y apunta a una version mas personalizada de Siri durante 2026.',
                'summary' => 'Google confirmo que colabora con Apple como proveedor cloud preferido para desarrollar la proxima generacion de Apple Foundation Models basada en tecnologia Gemini. La alianza apunta a futuras funciones de Apple Intelligence, incluyendo una Siri mas personalizada.',
                'content' => <<<'TEXT'
Google confirmo publicamente una colaboracion relevante con Apple en inteligencia artificial. Segun T3, el CEO de Google Cloud, Thomas Kurian, dijo durante Cloud Next 2026 que Google trabaja con Apple como proveedor cloud preferido para desarrollar la siguiente generacion de modelos fundacionales de Apple basados en tecnologia Gemini.

La declaracion es llamativa porque Apple suele comunicar con mucho cuidado sus alianzas estrategicas. En este caso, Google puso sobre la mesa que Gemini ayudara a impulsar futuras funciones de Apple Intelligence, incluida una Siri mas personalizada prevista para este ano.

La noticia muestra hasta que punto la carrera de asistentes personales se esta reordenando. Apple controla hardware, sistema operativo y experiencia de usuario, pero necesita modelos capaces de competir con ChatGPT, Gemini y Claude. Google, por su parte, gana una posicion privilegiada en uno de los ecosistemas moviles mas valiosos del mundo.

Para usuarios, el cambio podria traducirse en una Siri con mejor comprension contextual, respuestas mas utiles y mayor capacidad de integrarse con tareas cotidianas. Para desarrolladores, abre preguntas sobre privacidad, procesamiento en la nube, APIs y el rol de Apple Intelligence dentro de apps.

En Latinoamerica, el impacto dependera de disponibilidad de idioma, despliegue regional y compatibilidad con dispositivos. Si Apple logra mejorar Siri sin romper su promesa de privacidad, podria reactivar la competencia por asistentes moviles, un terreno donde hasta ahora habia quedado detras de sus rivales.

La colaboracion entre Apple y Google tambien confirma una tendencia de 2026: incluso las grandes tecnologicas estan combinando fortalezas. La ventaja ya no esta solo en tener un modelo propio, sino en integrarlo en productos masivos con infraestructura confiable.
TEXT,
            ],
        ];
    }
};
