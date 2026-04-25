<?php

namespace App\Console\Commands;

use App\Models\AnalisisFondo;
use App\Models\ConceptoIa;
use App\Models\ConocIaPaper;
use App\Models\EstadoArte;
use App\Models\User;
use Illuminate\Console\Command;

class PublishProfundizaRetrievalPack extends Command
{
    protected $signature = 'content:publish-profundiza-retrieval-pack
                            {--user-id= : Autor opcional para el análisis}
                            {--force : Sobrescribe slugs existentes}';

    protected $description = 'Publica un pack editorial de Profundiza sobre retrieval multimodal, agentes y benchmarks recientes.';

    public function handle(): int
    {
        $author = $this->resolveAuthor();

        $this->upsertConcept();
        $this->upsertAnalysis($author?->id);
        $this->upsertPapers();
        $this->upsertDigest();

        $this->info('Pack Profundiza publicado.');

        return self::SUCCESS;
    }

    private function upsertConcept(): void
    {
        $payload = [
            'title' => 'Retrieval Multimodal',
            'slug' => 'retrieval-multimodal',
            'definition' => 'El retrieval multimodal es la capacidad de un sistema para recuperar evidencia relevante desde texto, imágenes, audio, video u otras fuentes antes de razonar sobre una tarea.',
            'excerpt' => 'Cada vez más benchmarks muestran que el gran cuello de botella de la IA ya no es solo responder, sino encontrar la evidencia correcta en distintos formatos.',
            'content' => <<<'HTML'
<p>Durante bastante tiempo, buena parte de la conversación sobre inteligencia artificial se concentró en un solo momento del problema: la respuesta. Si un modelo respondía bien, razonaba mejor o generaba una salida más convincente, asumíamos que había avanzado. Pero la irrupción de agentes, sistemas multimodales y benchmarks más realistas obligó a mover el foco. Hoy, en muchos escenarios importantes, el verdadero cuello de botella ya no está únicamente en generar una respuesta, sino en <strong>encontrar primero la evidencia correcta</strong>. Ahí es donde entra el retrieval multimodal.</p>

<h2>¿Qué es?</h2>
<p>Retrieval multimodal es el proceso mediante el cual un sistema identifica y recupera información útil desde más de una modalidad. No se limita a buscar texto en un corpus. Puede implicar localizar una figura en un PDF, reconocer que la evidencia decisiva está en una tabla, encontrar un clip de video, un fragmento de audio o una imagen específica, y luego poner todo eso a disposición del sistema que va a razonar.</p>
<p>La idea es simple de explicar, pero difícil de resolver. En un entorno real, la información relevante rara vez está toda en el mismo formato. Un paper puede tener la explicación en texto, el hallazgo crítico en una figura y el detalle metodológico en una tabla. Un agente web puede necesitar una página, una captura, un fragmento de video y un documento técnico al mismo tiempo. Recuperar bien deja de ser un problema de palabras clave y pasa a ser un problema de <strong>criterio, representación y contexto</strong>.</p>

<h2>¿Cómo funciona internamente?</h2>
<p>Un sistema de retrieval multimodal suele apoyarse en representaciones compartidas o coordinadas entre modalidades. En lugar de tratar texto, imagen y audio como universos completamente separados, intenta mapearlos a espacios comparables para que una consulta pueda recuperar evidencia útil aunque la forma original sea distinta. Esto puede hacerse con embeddings multimodales, indexación híbrida, metadatos enriquecidos y pipelines donde primero se recupera y luego se reranquea.</p>
<p>En la práctica, el flujo rara vez es lineal. Primero aparece una consulta o una tarea. Luego el sistema decide qué modalidad conviene explorar. Después recupera candidatos, los reranquea, descarta ruido y recién entonces arma el contexto que usará el modelo generativo o el agente. Cada una de esas etapas puede fallar. De hecho, muchos de los problemas que hoy atribuimos al “razonamiento” empiezan antes: en una mala recuperación del contexto.</p>

<h2>¿Por qué importa?</h2>
<p>Importa porque gran parte del valor real de la IA avanzada depende de ello. Un agente que navega la web, un asistente que trabaja con documentación compleja, un sistema para ciencia, medicina, industria o investigación técnica no puede depender solo del texto que tiene enfrente. Necesita saber dónde buscar y qué fuente merece atención. Si esa recuperación es pobre, el sistema puede razonar perfectamente sobre evidencia equivocada. Y ese error es más peligroso porque da la ilusión de inteligencia.</p>
<p>Los benchmarks recientes van en esa dirección. MERRIN, ARK y otros trabajos publicados en 2026 muestran que incluso modelos muy fuertes siguen tropezando cuando tienen que localizar evidencia relevante en entornos ruidosos, abiertos y multimodales. El hallazgo es consistente: el progreso en IA no se mide solo por responder mejor, sino por <strong>recuperar mejor</strong>.</p>

<h2>Historia y evolución</h2>
<p>El retrieval clásico viene del mundo de los buscadores, los sistemas de información y el NLP. Durante años, la discusión giró alrededor de BM25, embeddings, dense retrieval, rerankers y, más recientemente, RAG. Pero el paso a escenarios multimodales cambió la exigencia. El problema dejó de ser “encontrar un párrafo” y pasó a ser “encontrar la evidencia útil sin asumir que está escrita como texto continuo”.</p>
<p>La aceleración reciente vino por dos lados. Por un lado, modelos multimodales capaces de representar mejor diferentes formatos. Por otro, benchmarks más exigentes que dejaron de premiar solo el resultado final y empezaron a observar el proceso de recuperación. Esa combinación hizo visible una verdad incómoda: muchas demos espectaculares se sostienen sobre entornos demasiado limpios. En la práctica, la IA todavía tropieza al buscar.</p>

<h2>Conceptos relacionados</h2>
<ul>
  <li><strong>RAG</strong> — Cuando el modelo recupera contexto antes de responder, aunque muchas implementaciones siguen siendo fuertemente textuales.</li>
  <li><strong>Embeddings multimodales</strong> — Representaciones que permiten comparar texto, imagen u otras modalidades en espacios compatibles.</li>
  <li><strong>Agentes de IA</strong> — Porque gran parte de su utilidad depende de recuperar bien contexto antes de actuar.</li>
  <li><strong>Ventana de contexto</strong> — No sirve de mucho tener más contexto si el sistema no sabe qué meter primero.</li>
  <li><strong>Reranking</strong> — La etapa que decide qué evidencia recuperada merece realmente pasar al modelo.</li>
</ul>

<h2>Para profundizar</h2>
<ul>
  <li><strong>MERRIN</strong> — Benchmark reciente para agentes con búsqueda web multimodal en entornos ruidosos.</li>
  <li><strong>ARK</strong> — Trabajo que muestra por qué la recuperación multimodal con conocimiento especializado sigue siendo una deuda.</li>
  <li><strong>MM-BRIGHT</strong> — Benchmark que empuja hacia razonamiento con evidencia de alta complejidad y retrieval multimodal más exigente.</li>
</ul>
HTML,
            'category' => 'Modelos de Lenguaje',
            'related_concepts' => ['rag', 'agentes-ia', 'multimodal-ai', 'embeddings'],
            'key_players' => [
                ['name' => 'Investigadores de retrieval y RAG', 'role' => 'Base conceptual del campo'],
                ['name' => 'Equipos de benchmarks multimodales', 'role' => 'Quienes están tensionando el problema hacia escenarios reales'],
            ],
            'further_reading' => [
                ['title' => 'MERRIN', 'description' => 'Benchmark para agentes con búsqueda multimodal en web abierta'],
                ['title' => 'ARK', 'description' => 'Retrieval multimodal con conocimiento especializado'],
                ['title' => 'MM-BRIGHT', 'description' => 'Evaluación de retrieval y razonamiento multimodal complejo'],
            ],
            'featured' => true,
            'status' => 'published',
            'reading_time' => 6,
            'published_at' => now(),
        ];

        $this->upsertModel(ConceptoIa::class, $payload, $payload['slug']);
    }

    private function upsertAnalysis(?int $authorId): void
    {
        $payload = [
            'title' => 'El nuevo cuello de botella de la IA ya no es responder, sino recuperar evidencia',
            'slug' => 'nuevo-cuello-botella-ia-recuperar-evidencia',
            'excerpt' => 'Los benchmarks más interesantes de 2026 están mostrando algo incómodo para el hype actual: la IA falla muchas veces antes de razonar, porque ni siquiera encuentra bien el contexto que necesita.',
            'content' => <<<'HTML'
<p>Durante meses, una parte importante de la conversación sobre inteligencia artificial giró alrededor de una promesa muy concreta: modelos que razonan mejor, agentes que navegan la web, sistemas que investigan por su cuenta y asistentes capaces de resolver tareas complejas con cada vez menos supervisión humana. Pero si uno mira con atención los trabajos más interesantes que salieron en 2026, aparece una conclusión menos vistosa y probablemente más importante: el gran cuello de botella de la IA ya no está solo en la respuesta. Está, cada vez más, en la recuperación del contexto correcto.</p>

<p>Dicho de otro modo: muchos sistemas no fracasan porque “piensen mal” una vez que tienen la evidencia delante. Fracasan antes. Fallan al elegir fuentes, al decidir qué modalidad conviene explorar, al encontrar el documento correcto o al distinguir una señal importante de una pista lateral. Y esa diferencia cambia bastante la conversación, porque mueve el centro del debate desde el razonamiento abstracto hacia algo más concreto y más difícil de vender en demos: <strong>el buen juicio a la hora de buscar</strong>.</p>

<h2>La generación ya no alcanza para explicar el progreso</h2>
<p>Buena parte del optimismo reciente se apoya en un hecho real: los modelos son claramente mejores generando texto, resolviendo tareas estructuradas y respondiendo preguntas complejas que hace dos o tres años. Pero ese progreso creó un sesgo en la forma en que medimos inteligencia útil. Empezamos a premiar mucho la calidad de la respuesta final y poco la calidad del camino que llevó hasta ella.</p>
<p>Eso funcionaba razonablemente bien en benchmarks limpios, con contexto dado o con tareas donde la evidencia relevante ya estaba servida. El problema aparece cuando el sistema tiene que salir al mundo, navegar ruido, interpretar modalidades distintas, recuperar documentos, descartar material engañoso y recién después integrar todo eso en una cadena de razonamiento. Ahí las diferencias se vuelven evidentes.</p>

<h2>Lo que muestran los benchmarks recientes</h2>
<p>MERRIN, por ejemplo, lleva a los agentes a un entorno web abierto y multimodal. El mensaje central es directo: incluso sistemas avanzados siguen rindiendo mal cuando deben recuperar evidencia relevante y luego razonar sobre ella en un escenario ruidoso. El hallazgo no es solo que fallen. Es que muchas veces consumen más pasos, más herramientas y más exploración que un humano, pero con peor criterio.</p>

<p>ARK empuja en una dirección parecida, aunque con otro énfasis. Lo que muestra es que la recuperación multimodal con conocimiento especializado sigue siendo una deuda seria. No basta con que un sistema “vea” y “lea”. Tiene que saber qué modalidad importa, cómo integrar conocimiento técnico y cómo decidir qué fragmento realmente vale la pena incorporar. En la práctica, ese filtro sigue siendo frágil.</p>

<p>Y cuando el problema se traslada a contextos industriales, como en AEC-Bench, la lectura se vuelve incluso más interesante. Allí el gran bottleneck tampoco aparece únicamente en el razonamiento. Aparece en retrieval. Los agentes muchas veces ni siquiera logran localizar de forma fiable la hoja, el plano o el documento correcto antes de empezar a operar. Una vez más, el error importante ocurre antes de la “respuesta”.</p>

<blockquote>La IA no solo necesita pensar mejor. Necesita aprender a buscar con más criterio.</blockquote>

<h2>Por qué esto cambia la conversación sobre agentes</h2>
<p>La industria empuja fuerte la idea de agentes autónomos. Y no sin razón: la combinación entre modelos potentes, herramientas y ejecución multistep abre una frontera real. Pero los nuevos benchmarks sugieren que seguimos subestimando una pieza central del problema. Un agente no es útil solo porque pueda ejecutar pasos. Es útil si sabe elegir qué pasos merecen ser ejecutados y sobre qué evidencia conviene actuar.</p>

<p>Eso tiene consecuencias prácticas. Si un agente selecciona una fuente secundaria, si lee una modalidad equivocada o si incorpora ruido como si fuera señal, puede producir una respuesta impecable en forma pero equivocada en sustancia. Ese tipo de error es más difícil de detectar, precisamente porque la salida parece convincente.</p>

<h2>El verdadero problema es de criterio</h2>
<p>En el fondo, retrieval ya no es solo un problema de infraestructura o de indexación. Se está volviendo un problema de criterio. No basta con tener acceso a más documentos, más ventanas de contexto o más herramientas de búsqueda. Hay que decidir bien qué ignorar, qué priorizar, qué modalidad conviene abrir primero y qué evidencia merece entrar al contexto final.</p>

<p>Eso es especialmente importante en áreas como ciencia, salud, industria o investigación técnica, donde la información relevante suele estar distribuida en formatos distintos y donde el error no solo baja una métrica, sino que puede alterar decisiones reales. En esos contextos, recuperar mal es casi tan problemático como razonar mal.</p>

<h2>Qué viene después</h2>
<p>Si este diagnóstico es correcto, la próxima etapa de progreso no se va a definir solo por modelos más grandes o respuestas más fluidas. Va a depender de sistemas que coordinen mejor recuperación, filtrado, reranking y razonamiento multimodal. Es decir, de arquitecturas más disciplinadas y con mejor juicio operativo.</p>

<p>Eso también cambia cómo deberíamos leer el hype actual. Tal vez la pregunta más importante ya no sea si la IA “piensa” como un humano. Tal vez la pregunta correcta sea si sabe <strong>buscar con la disciplina mínima necesaria</strong> para que ese pensamiento tenga sentido en el mundo real.</p>

<p>Y hoy, si uno mira la evidencia con honestidad, la respuesta todavía parece ser: no del todo.</p>
HTML,
            'topic' => 'Retrieval multimodal, agentes y benchmarks de 2026',
            'category' => 'Modelos de Lenguaje',
            'key_players' => [
                ['name' => 'Han Wang et al.', 'role' => 'Autores de MERRIN'],
                ['name' => 'Equipos detrás de ARK y MM-BRIGHT', 'role' => 'Investigación sobre retrieval multimodal y conocimiento especializado'],
                ['name' => 'Laboratorios de agentes', 'role' => 'Quienes están tensionando el problema en web abierta e industria'],
            ],
            'featured' => true,
            'status' => 'published',
            'views' => 0,
            'reading_time' => 8,
            'author_id' => $authorId,
            'published_at' => now(),
        ];

        $this->upsertModel(AnalisisFondo::class, $payload, $payload['slug']);
    }

    private function upsertPapers(): void
    {
        $papers = [
            [
                'arxiv_id' => '2604.13418',
                'arxiv_url' => 'https://arxiv.org/abs/2604.13418',
                'original_title' => 'MERRIN: A Benchmark for Multimodal Evidence Retrieval and Reasoning in Noisy Web Environments',
                'original_abstract' => 'Benchmark para agentes con recuperación de evidencia multimodal y razonamiento en entornos web ruidosos.',
                'authors' => ['Han Wang', 'David Wan', 'Hyunji Lee', 'Thinh Pham', 'Mikaela Cankosyan', 'Weiyuan Chen', 'Elias Stengel-Eskin', 'Tu Vu', 'Mohit Bansal'],
                'arxiv_published_date' => '2026-04-15',
                'arxiv_category' => 'cs.AI',
                'title' => 'MERRIN: cuando los agentes salen a la web real y descubren que buscar bien sigue siendo difícil',
                'slug' => 'merrin-agentes-web-real-benchmark',
                'excerpt' => 'MERRIN lleva a los agentes a un entorno web ruidoso y multimodal. El resultado es un baño de realidad: todavía fallan mucho más de lo que el mercado sugiere.',
                'content' => <<<'HTML'
<p>MERRIN es uno de esos benchmarks que importan no porque confirmen lo que la industria quiere escuchar, sino porque tensan justo donde la narrativa más promete. El trabajo fue publicado en arXiv en abril de 2026 y plantea una evaluación para agentes con búsqueda web aumentada en entornos ruidosos, multimodales y abiertos. La pregunta no es solamente si un modelo puede llegar a una respuesta. La pregunta es si puede encontrar primero la evidencia correcta, integrarla y razonar sobre ella cuando la web se parece más al mundo real que a un entorno limpio de laboratorio.</p>
<p>Ahí está el valor del paper. En lugar de premiar al sistema que mejor responde con contexto ya disponible, MERRIN premia algo más difícil: buen criterio de recuperación. Los autores reportan un benchmark exigente incluso para agentes avanzados, con resultados que dejan claro que el desempeño todavía está lejos de lo que sugiere el hype actual sobre “deep research” y automatización de investigación.</p>
<p>La lectura editorial más importante es simple: el problema no es solo de conocimiento ni de generación, sino de selección de evidencia. Los agentes todavía exploran de más, se distraen con señales parcialmente relevantes y muestran una preferencia excesiva por el texto incluso cuando otra modalidad podría contener la pista decisiva.</p>
HTML,
                'key_contributions' => [
                    'Benchmark para recuperación multimodal y razonamiento en web abierta',
                    'Evalúa agentes en contextos ruidosos, ambiguos y multimodales',
                    'Muestra que más pasos de exploración no garantizan mejores respuestas',
                ],
                'practical_implications' => [
                    'Los agentes aún no son confiables como investigadores autónomos',
                    'Recuperación y filtrado de evidencia son el verdadero cuello de botella',
                ],
                'difficulty_level' => 'Intermedio',
                'featured' => true,
                'status' => 'published',
                'views' => 0,
                'reading_time' => 4,
                'published_at' => now(),
            ],
            [
                'arxiv_id' => '2602.09839',
                'arxiv_url' => 'https://arxiv.org/abs/2602.09839',
                'original_title' => 'ARK: A Benchmark for Multimodal Retrieval-Augmented Reasoning with Knowledge',
                'original_abstract' => 'Benchmark para retrieval-augmented reasoning multimodal con fuerte componente de conocimiento especializado.',
                'authors' => ['Autores del paper ARK'],
                'arxiv_published_date' => '2026-02-10',
                'arxiv_category' => 'cs.AI',
                'title' => 'ARK: por qué recuperar contexto multimodal con conocimiento sigue siendo una deuda en IA',
                'slug' => 'ark-retrieval-multimodal-conocimiento-deuda-ia',
                'excerpt' => 'ARK apunta a una debilidad menos visible: los modelos todavía tropiezan cuando deben recuperar evidencia compleja y combinarla con conocimiento especializado.',
                'content' => <<<'HTML'
<p>ARK parte de una intuición poderosa: recuperar bien no es suficiente si el sistema no sabe integrar lo recuperado con conocimiento especializado. El benchmark se enfoca en retrieval-augmented reasoning multimodal, un terreno especialmente importante para tareas técnicas, científicas o profesionales donde la evidencia relevante puede estar distribuida entre texto, diagramas, imágenes y conocimiento de dominio.</p>
<p>La importancia del paper está en que empuja la evaluación hacia un problema compuesto. No basta con “ver” ni con “leer”. Hay que decidir qué modalidad conviene explorar, qué evidencia es la correcta y cómo conectarla con el conocimiento que ya posee el sistema. Esa combinación sigue siendo frágil incluso en modelos fuertes.</p>
<p>Editorialmente, ARK sirve para desmontar una ilusión común: que multimodalidad equivale automáticamente a comprensión. El paper sugiere más bien lo contrario. Los modelos pueden procesar más formatos, sí, pero todavía no muestran suficiente disciplina para convertir esa capacidad en una recuperación de evidencia realmente robusta.</p>
HTML,
                'key_contributions' => [
                    'Evalúa retrieval-augmented reasoning multimodal con conocimiento',
                    'Mide la integración entre recuperación y razonamiento técnico',
                    'Expone límites en tareas donde la modalidad correcta no es obvia',
                ],
                'practical_implications' => [
                    'Sistemas para ciencia, salud e industria siguen necesitando supervisión alta',
                    'La próxima mejora no es solo de modelo, sino de coordinación entre retrieval y reasoning',
                ],
                'difficulty_level' => 'Intermedio',
                'featured' => true,
                'status' => 'published',
                'views' => 0,
                'reading_time' => 4,
                'published_at' => now()->subMinute(),
            ],
            [
                'arxiv_id' => '2603.07940',
                'arxiv_url' => 'https://arxiv.org/abs/2603.07940',
                'original_title' => 'AI Agents, Language, Deep Learning and the Next Revolution in Science',
                'original_abstract' => 'Trabajo de fondo sobre agentes científicos y supervisión humana.',
                'authors' => ['Autores del paper sobre agentes científicos'],
                'arxiv_published_date' => '2026-03-09',
                'arxiv_category' => 'cs.AI',
                'title' => 'Agentes científicos: la próxima frontera depende tanto de buscar bien como de razonar',
                'slug' => 'agentes-cientificos-buscar-bien-razonar',
                'excerpt' => 'El paper sobre agentes científicos es estratégico por una razón simple: muestra que escalar ciencia con IA exige no solo modelos mejores, sino retrieval, trazabilidad y supervisión humana.',
                'content' => <<<'HTML'
<p>El paper sobre agentes científicos no es estrictamente un benchmark de retrieval, pero importa mucho en esta conversación porque lleva el problema a un terreno donde buscar bien es tan importante como razonar. La tesis del trabajo es que la ciencia está entrando en una fase donde agentes supervisados por humanos podrían coordinar análisis complejos, traducir intención científica y escalar descubrimiento.</p>
<p>Esa promesa, sin embargo, depende de una condición fuerte: que el sistema sea capaz de recuperar el contexto correcto, documentar sus pasos y operar con suficiente trazabilidad. Si no puede hacer eso, la idea de “agente científico” se vuelve demasiado frágil para sostener responsabilidad real.</p>
<p>Por eso este paper encaja tan bien en el panorama de 2026. No solo habla del futuro de la ciencia con IA. También deja entrever cuál es la infraestructura cognitiva que hará falta para llegar allí: buen retrieval, trazabilidad explícita y supervisión humana sostenida.</p>
HTML,
                'key_contributions' => [
                    'Plantea agentes supervisados por humanos como nueva capa del método científico',
                    'Destaca lenguaje y trazabilidad como interfaz entre intención y ejecución',
                    'Conecta automatización científica con responsabilidad operativa',
                ],
                'practical_implications' => [
                    'La IA científica exige sistemas auditables, no solo productivos',
                    'Recuperación y documentación del contexto importarán tanto como el razonamiento',
                ],
                'difficulty_level' => 'Intermedio',
                'featured' => false,
                'status' => 'published',
                'views' => 0,
                'reading_time' => 4,
                'published_at' => now()->subMinutes(2),
            ],
        ];

        foreach ($papers as $paper) {
            $this->upsertModel(ConocIaPaper::class, $paper, $paper['slug']);
        }
    }

    private function upsertDigest(): void
    {
        $payload = [
            'title' => 'Estado del arte: retrieval multimodal y agentes, abril de 2026',
            'slug' => 'estado-arte-retrieval-multimodal-agentes-abril-2026',
            'subfield' => 'retrieval-multimodal',
            'subfield_label' => 'Retrieval Multimodal y Agentes',
            'period_label' => 'Abril de 2026',
            'week_start' => '2026-04-20',
            'week_end' => '2026-04-26',
            'excerpt' => 'Los benchmarks más interesantes del momento apuntan en la misma dirección: la IA todavía necesita recuperar mejor evidencia antes de poder razonar con consistencia.',
            'content' => <<<'HTML'
<p>Si uno intenta resumir en una sola idea lo que están mostrando varios trabajos recientes, sería esta: la IA avanzó mucho en generación, pero todavía está lejos de dominar la recuperación de evidencia en escenarios abiertos, multimodales y técnicamente exigentes. Esa es, probablemente, una de las señales más importantes de abril de 2026.</p>

<h2>Desarrollos clave esta semana</h2>
<ul>
  <li><strong>MERRIN</strong> consolidó la idea de que los agentes aún rinden mal en web abierta cuando la recuperación multimodal importa de verdad.</li>
  <li><strong>ARK</strong> reforzó otra dimensión crítica: recuperar contexto útil con conocimiento especializado sigue siendo un punto débil.</li>
  <li><strong>La conversación sobre agentes científicos</strong> mostró que la próxima ola de automatización seria dependerá tanto de la trazabilidad y el retrieval como del razonamiento.</li>
</ul>

<h2>La señal de fondo</h2>
<p>Lo interesante es que estos trabajos no vienen exactamente del mismo lugar ni miden lo mismo, pero convergen en un diagnóstico parecido. La frontera ya no está solo en “responder mejor”. Está en recuperar mejor, filtrar mejor y decidir mejor qué evidencia merece entrar al contexto final. En otras palabras, en construir sistemas con más disciplina cognitiva.</p>

<h2>Qué mirar en las próximas semanas</h2>
<p>Hay tres señales a seguir. Primero, nuevos benchmarks que dejen de asumir contexto textual limpio y empujen tareas más cercanas al trabajo real. Segundo, arquitecturas que combinen retrieval, reranking y razonamiento con menos improvisación. Tercero, casos de uso industriales o científicos donde el error en la recuperación deje de ser tolerable y obligue a elevar el estándar.</p>

<p>Si esta tendencia se consolida, la próxima etapa de progreso en IA no se definirá solo por modelos más grandes. Se definirá por sistemas más rigurosos a la hora de buscar.</p>
HTML,
            'source_news_ids' => [],
            'key_developments' => [
                'MERRIN refuerza los límites reales de los agentes en web abierta',
                'ARK expone la deuda entre retrieval multimodal y conocimiento',
                'La ciencia con agentes necesita trazabilidad además de modelos fuertes',
            ],
            'featured' => true,
            'status' => 'published',
            'views' => 0,
            'reading_time' => 3,
            'published_at' => now(),
        ];

        $this->upsertModel(EstadoArte::class, $payload, $payload['slug'], ['subfield', 'week_start']);
    }

    private function upsertModel(string $modelClass, array $payload, string $slug, array $alternateKeys = []): void
    {
        $existing = $modelClass::query()->where('slug', $slug)->first();

        if (!$existing && $alternateKeys !== []) {
            $query = $modelClass::query();
            foreach ($alternateKeys as $key) {
                $query->where($key, $payload[$key]);
            }
            $existing = $query->first();
        }

        if ($existing && !$this->option('force')) {
            $this->warn("Ya existe {$slug}. Usa --force para actualizar.");
            return;
        }

        if ($existing) {
            $existing->update($payload);
            $this->info("Actualizado: {$payload['title']}");
            return;
        }

        $modelClass::create($payload);
        $this->info("Publicado: {$payload['title']}");
    }

    private function resolveAuthor(): ?User
    {
        $userId = $this->option('user-id');

        if ($userId) {
            return User::find($userId);
        }

        return User::query()->where('email', 'editor@conocia.com')->first();
    }
}
