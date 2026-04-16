<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProfundizaSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedConceptosIa();
        $this->seedAnalisisFondo();
        $this->seedConocIaPapers();
        $this->seedEstadoArte();
    }

    private function seedConceptosIa(): void
    {
        DB::table('conceptos_ia')->truncate();

        $conceptos = [
            [
                'title'            => 'Arquitectura Transformer',
                'slug'             => 'arquitectura-transformer',
                'category'         => 'arquitecturas',
                'excerpt'          => 'El mecanismo de atención que revolucionó el procesamiento del lenguaje natural y se convirtió en la base de modelos como GPT y BERT.',
                'definition'       => 'Una arquitectura de red neuronal basada completamente en mecanismos de atención (self-attention) que permite modelar dependencias globales entre entradas y salidas sin recurrir a redes recurrentes.',
                'content'          => '<h2>¿Qué es un Transformer?</h2><p>Introducido en el influyente paper "Attention Is All You Need" (Vaswani et al., 2017), el Transformer reemplazó las arquitecturas recurrentes (RNN, LSTM) que dominaban el NLP hasta ese momento. La innovación central fue demostrar que la atención, por sí sola, era suficiente para capturar las relaciones contextuales en el lenguaje.</p><h2>El mecanismo de Self-Attention</h2><p>La pieza clave del Transformer es el <em>scaled dot-product attention</em>. Para cada token de entrada, el modelo computa tres vectores: Query (Q), Key (K) y Value (V). La atención se calcula como:</p><blockquote>Attention(Q,K,V) = softmax(QK<sup>T</sup>/√d<sub>k</sub>)V</blockquote><p>Esto permite que cada token "atienda" a todos los demás tokens de la secuencia simultáneamente, capturando dependencias de largo alcance sin el problema del gradiente desvaneciente que afectaba a las RNN.</p><h2>Encoder-Decoder y sus variantes</h2><p>La arquitectura original tiene dos componentes: un <strong>encoder</strong> que procesa la secuencia de entrada y un <strong>decoder</strong> que genera la salida. Sin embargo, modelos posteriores simplificaron esta arquitectura según el caso de uso:</p><ul><li><strong>Solo encoder</strong>: BERT y sus derivados — ideales para tareas de comprensión (clasificación, NER, QA).</li><li><strong>Solo decoder</strong>: GPT y sus derivados — ideales para generación de texto autoregressiva.</li><li><strong>Encoder-Decoder</strong>: T5, BART — ideales para traducción y resumen.</li></ul><h2>Atención multi-cabeza</h2><p>El Transformer no usa una sola función de atención, sino múltiples "cabezas" que aprenden a atender diferentes tipos de relaciones en paralelo. Cada cabeza opera en un subespacio dimensional diferente, permitiendo al modelo capturar simultáneamente relaciones sintácticas, semánticas y de correferencia.</p><h2>Positional Encoding</h2><p>A diferencia de las RNN, los Transformers procesan todos los tokens en paralelo y, por lo tanto, no tienen noción inherente del orden. Para resolver esto, se suman codificaciones posicionales (senos y cosenos de diferentes frecuencias) a los embeddings de entrada, inyectando información sobre la posición relativa de cada token.</p><h2>Impacto y legado</h2><p>El Transformer es, sin duda, la arquitectura más influyente en IA de la última década. Toda la familia de modelos de lenguaje de gran escala — GPT-4, Llama, Gemini, Claude — está construida sobre esta base. Su escalabilidad, paralelismo durante el entrenamiento y capacidad para capturar contexto de largo alcance lo convirtieron en el pilar de la IA generativa moderna.</p>',
                'key_players'      => json_encode(['Ashish Vaswani', 'Noam Shazeer', 'Niki Parmar', 'Google Brain']),
                'related_concepts' => json_encode(['atencion-multi-cabeza', 'bert', 'gpt', 'large-language-models']),
                'further_reading'  => json_encode([
                    ['title' => 'Attention Is All You Need (paper original)', 'url' => 'https://arxiv.org/abs/1706.03762'],
                    ['title' => 'The Illustrated Transformer — Jay Alammar', 'url' => 'https://jalammar.github.io/illustrated-transformer/'],
                ]),
                'featured'         => true,
                'status'           => 'published',
                'views'            => 1240,
                'reading_time'     => 8,
                'published_at'     => now()->subDays(10),
            ],
            [
                'title'            => 'Aprendizaje por Refuerzo',
                'slug'             => 'aprendizaje-por-refuerzo',
                'category'         => 'paradigmas',
                'excerpt'          => 'Paradigma de aprendizaje donde un agente aprende a tomar decisiones interactuando con un entorno y recibiendo señales de recompensa.',
                'definition'       => 'Rama del machine learning donde un agente aprende una política óptima de comportamiento mediante la interacción con un entorno, maximizando una señal de recompensa acumulada a lo largo del tiempo.',
                'content'          => '<h2>El paradigma agente-entorno</h2><p>El Aprendizaje por Refuerzo (Reinforcement Learning, RL) modela el aprendizaje como un ciclo continuo: el <strong>agente</strong> observa el estado actual del <strong>entorno</strong>, toma una <strong>acción</strong>, recibe una <strong>recompensa</strong> y transiciona a un nuevo estado. El objetivo es aprender una <strong>política</strong> (π) que maximice la recompensa acumulada esperada.</p><h2>Componentes clave</h2><p>El marco formal del RL se basa en los <em>Procesos de Decisión de Markov</em> (MDP), definidos por: Estados (S), Acciones (A), Transiciones (P), Recompensas (R) y un factor de descuento (γ) que pondera las recompensas futuras.</p><h2>Q-Learning y Deep RL</h2><p>El algoritmo Q-Learning aprende el valor de pares (estado, acción) sin necesitar un modelo del entorno. La revolución del <em>Deep Reinforcement Learning</em> llegó cuando DeepMind combinó Q-Learning con redes neuronales profundas para crear DQN, capaz de jugar videojuegos de Atari a nivel humano.</p><h2>RLHF: RL para alinear LLMs</h2><p>Una de las aplicaciones más relevantes del RL hoy es el <em>Reinforcement Learning from Human Feedback</em> (RLHF), la técnica detrás de ChatGPT e InstructGPT. En lugar de definir una función de recompensa manualmente, se entrena un modelo de recompensa a partir de preferencias humanas, logrando que los LLMs sean más útiles y seguros.</p>',
                'key_players'      => json_encode(['Richard Sutton', 'Andrew Barto', 'DeepMind', 'OpenAI']),
                'related_concepts' => json_encode(['rlhf', 'q-learning', 'politica-de-gradiente']),
                'further_reading'  => json_encode([
                    ['title' => 'Reinforcement Learning: An Introduction — Sutton & Barto', 'url' => 'http://incompleteideas.net/book/the-book.html'],
                ]),
                'featured'         => false,
                'status'           => 'published',
                'views'            => 876,
                'reading_time'     => 7,
                'published_at'     => now()->subDays(7),
            ],
            [
                'title'            => 'Redes Generativas Adversariales (GAN)',
                'slug'             => 'redes-generativas-adversariales',
                'category'         => 'arquitecturas',
                'excerpt'          => 'Arquitectura de dos redes que compiten entre sí — un generador y un discriminador — logrando generar datos sintéticos de alta calidad.',
                'definition'       => 'Arquitectura de aprendizaje profundo compuesta por dos redes neuronales (generador y discriminador) entrenadas simultáneamente en un juego minimax, donde el generador aprende a producir datos indistinguibles de los reales.',
                'content'          => '<h2>La idea central: competencia como aprendizaje</h2><p>Propuesta por Ian Goodfellow en 2014, la GAN es elegante en su concepción: dos redes compiten. El <strong>generador</strong> aprende a crear datos falsos que parezcan reales; el <strong>discriminador</strong> aprende a distinguir lo real de lo falso. A medida que el discriminador mejora, el generador se ve forzado a mejorar también. Este proceso adversarial continúa hasta que el generador produce muestras que el discriminador ya no puede distinguir del dato real.</p><h2>El juego minimax</h2><p>Formalmente, el entrenamiento busca un equilibrio de Nash entre ambas redes. El generador minimiza la probabilidad de ser detectado; el discriminador la maximiza. El equilibrio teórico se alcanza cuando el generador replica perfectamente la distribución de los datos reales.</p><h2>De imágenes a vídeos</h2><p>Las GAN transformaron la generación de imágenes fotorrealistas: StyleGAN (NVIDIA) generó rostros humanos indistinguibles de fotos reales; CycleGAN permitió transferencia de estilo imagen-a-imagen; Pix2Pix habilitó síntesis condicional. Aunque los modelos de difusión han tomado el liderazgo en generación de imágenes, las GAN siguen siendo relevantes en aplicaciones de baja latencia.</p>',
                'key_players'      => json_encode(['Ian Goodfellow', 'NVIDIA Research', 'DeepMind']),
                'related_concepts' => json_encode(['modelos-de-difusion', 'aprendizaje-no-supervisado', 'vae']),
                'further_reading'  => json_encode([
                    ['title' => 'Generative Adversarial Nets — Goodfellow et al., 2014', 'url' => 'https://arxiv.org/abs/1406.2661'],
                ]),
                'featured'         => true,
                'status'           => 'published',
                'views'            => 654,
                'reading_time'     => 6,
                'published_at'     => now()->subDays(5),
            ],
        ];

        foreach ($conceptos as $c) {
            DB::table('conceptos_ia')->insert(array_merge($c, [
                'image'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Conceptos IA: ' . count($conceptos) . ' registros creados.');
    }

    private function seedAnalisisFondo(): void
    {
        DB::table('analisis_fondo')->truncate();

        $userId = DB::table('users')->value('id');

        $analyses = [
            [
                'title'       => 'El dilema del código abierto en la era de los LLMs: democratización vs. riesgo sistémico',
                'slug'        => 'codigo-abierto-llms-democratizacion-riesgo',
                'topic'       => 'open-source-ia',
                'category'    => 'politica-ia',
                'excerpt'     => 'La liberación de Llama 3, Mistral y DeepSeek ha reabierto el debate sobre si el open source en IA es una fuerza democratizadora o un riesgo que no podemos permitirnos.',
                'content'     => '<h2>El momento bisagra del open source en IA</h2><p>En febrero de 2024, Meta lanzó Llama 2 bajo una licencia que, con ciertas restricciones, permitía uso comercial. Fue un giro radical en la industria. De golpe, empresas que no podían pagar las facturas de OpenAI tenían acceso a un modelo capaz. El ecosistema que emergió en meses — Ollama, LM Studio, decenas de fine-tunes especializados — fue una demostración de que la energía de la comunidad open source, aplicada a los LLMs, podía mover la aguja.</p><h2>El argumento democratizador</h2><p>El caso a favor del código abierto en IA descansa en tres pilares. Primero, el <strong>acceso</strong>: investigadores en economías emergentes, startups sin capital de riesgo, universidades con presupuestos ajustados pueden ahora construir sobre modelos de frontera. Segundo, la <strong>transparencia</strong>: cuando los pesos son abiertos, la comunidad puede auditar sesgos, detectar vulnerabilidades y proponer correcciones. Tercero, la <strong>soberanía tecnológica</strong>: países y organizaciones pueden desplegar modelos on-premise sin depender de APIs extranjeras ni preocuparse por cambios de política comercial.</p><h2>El argumento del riesgo</h2><p>La contra-narrativa, articulada con mayor fuerza después del lanzamiento de DeepSeek-R1, sostiene que liberar pesos de modelos capaces es cualitativamente diferente a liberar código de software. Un modelo que puede razonar sobre síntesis química, planificar operaciones de ciberataque o generar desinformación persuasiva no puede ser "parchado" una vez liberado. A diferencia del software, los pesos de un modelo son, en la práctica, irrevocables.</p><blockquote>"Open sourcing a capable AI model is closer to releasing a bioweapon blueprint than to releasing Linux." — Argumento recurrente en el debate de seguridad de IA</blockquote><h2>El caso DeepSeek y sus implicaciones geopolíticas</h2><p>El lanzamiento de DeepSeek-R1 en enero de 2025 añadió una dimensión geopolítica al debate. Un modelo chino de código abierto con rendimiento comparable a o1-preview de OpenAI representó tres cosas simultáneamente: una demostración de que las restricciones de chips de EEUU no habían detenido el progreso chino en IA, una presión competitiva sobre las valoraciones de las grandes tecnológicas americanas, y un dilema de seguridad nacional genuino — ¿debería el gobierno americano restringir el uso de un modelo cuyo código está disponible públicamente?</p><h2>¿Existe una solución de compromiso?</h2><p>Algunos investigadores proponen un modelo de "responsible release" escalonado: divulgar la arquitectura y el paper técnico, pero retrasar la liberación de los pesos completos hasta que se completen auditorías de seguridad. Otros abogan por licencias que explícitamente prohíban usos de alto riesgo, aunque la aplicación de tales restricciones es prácticamente imposible una vez que el modelo está en internet.</p><h2>Conclusión: un debate que recién comienza</h2><p>El open source en IA no es ni puramente bueno ni puramente malo. Es un multiplicador de capacidades que amplifica tanto el potencial democratizador como los riesgos de mal uso. La pregunta no es si abrir o cerrar, sino qué abrir, cuándo, y con qué mecanismos de responsabilidad. Es, en esencia, la misma pregunta que la sociedad ha tenido que hacerse frente a cada tecnología transformadora — solo que esta vez, el ciclo de iteración es de meses, no de décadas.</p>',
                'key_players' => json_encode(['Meta AI', 'Mistral AI', 'DeepSeek', 'OpenAI', 'Anthropic']),
                'featured'    => true,
                'status'      => 'published',
                'views'       => 2130,
                'reading_time'=> 12,
                'author_id'   => $userId,
                'published_at'=> now()->subDays(8),
            ],
            [
                'title'       => 'Agentes de IA en 2025: el año en que pasamos de chatbots a sistemas que actúan',
                'slug'        => 'agentes-ia-2025-de-chatbots-a-sistemas-que-actuan',
                'topic'       => 'agentes-ia',
                'category'    => 'tendencias',
                'excerpt'     => 'Los agentes autónomos dejaron de ser demos académicas para convertirse en herramientas productivas. Analizamos qué cambió, qué sigue siendo difícil y por qué importa.',
                'content'     => '<h2>El giro hacia la agencia</h2><p>Durante 2023 y gran parte de 2024, la promesa de los "agentes de IA" vivió atrapada entre el hype y las demos fallidas. AutoGPT capturó la imaginación de internet en 2023, pero en la práctica se perdía en bucles infinitos a los pocos pasos. La brecha entre lo que un LLM podía razonar y lo que podía <em>ejecutar</em> de forma confiable era enorme.</p><p>2025 marcó un punto de inflexión. No porque los modelos sean perfectos — no lo son — sino porque emergió una ingeniería de sistemas alrededor de ellos que hace que la imperfección sea manejable.</p><h2>Qué cambió técnicamente</h2><p>Tres avances convergieron. Primero, los modelos mejoraron en <strong>seguir instrucciones complejas</strong> y en el uso de herramientas (tool use / function calling). Segundo, surgieron <strong>frameworks de orquestación</strong> maduros — LangGraph, CrewAI, AutoGen — que permiten definir flujos de trabajo multi-agente con manejo de estado y recuperación de errores. Tercero, el <strong>contexto largo</strong> (128k, 200k tokens) eliminó muchos de los problemas de "pérdida de memoria" que hacían que los agentes se olvidaran de su tarea original a mitad de una sesión larga.</p><h2>Casos de uso que funcionan hoy</h2><p>La clave es entender que los agentes no son mejores en todo — son radicalmente mejores en tareas estructuradas y concretas. Los casos de uso con mejor retorno en 2025 son: automatización de pipelines de datos, generación y revisión de código, investigación y síntesis de documentos, y atención al cliente con escalada a humanos.</p><blockquote>El agente no reemplaza al analista. El analista que usa agentes reemplaza al analista que no los usa.</blockquote><h2>Lo que sigue siendo difícil</h2><p>La confiabilidad en tareas de largo horizonte sigue siendo el talón de Aquiles. Un agente que completa el 95% de los pasos de una tarea de 20 pasos todavía falla el 64% de las veces (0.95^20 ≈ 0.36). Esto hace que los sistemas de supervisión humana y los "checkpoints" de validación sean no opcionales para cualquier despliegue en producción.</p>',
                'key_players' => json_encode(['Anthropic', 'OpenAI', 'LangChain', 'Microsoft AutoGen']),
                'featured'    => false,
                'status'      => 'published',
                'views'       => 1567,
                'reading_time'=> 10,
                'author_id'   => $userId,
                'published_at'=> now()->subDays(3),
            ],
        ];

        foreach ($analyses as $a) {
            DB::table('analisis_fondo')->insert(array_merge($a, [
                'image'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Análisis de Fondo: ' . count($analyses) . ' registros creados.');
    }

    private function seedConocIaPapers(): void
    {
        DB::table('conocia_papers')->truncate();

        $papers = [
            [
                'arxiv_id'               => '2312.11805',
                'arxiv_url'              => 'https://arxiv.org/abs/2312.11805',
                'original_title'         => 'Gemini: A Family of Highly Capable Multimodal Models',
                'original_abstract'      => 'This report introduces a new family of multimodal models, Gemini, that exhibit remarkable capabilities across image, audio, video, and text understanding. The Gemini family consists of Ultra, Pro, and Nano sizes...',
                'authors'                => json_encode(['Gemini Team', 'Rohan Anil', 'Sebastian Borgeaud']),
                'arxiv_published_date'   => '2023-12-19',
                'arxiv_category'         => 'cs.AI',
                'title'                  => 'Gemini explicado: el modelo multimodal de Google que procesa texto, imagen, audio y vídeo',
                'slug'                   => 'gemini-modelo-multimodal-google-explicado',
                'excerpt'                => 'Google presenta una familia de modelos multimodales que procesan nativamente texto, imágenes, audio y vídeo. Explicamos la arquitectura, los benchmarks y qué significa esto para el futuro de la IA.',
                'content'                => '<h2>¿Qué propone este paper?</h2><p>Gemini es la respuesta de Google al GPT-4 de OpenAI. Pero más allá de la competencia de marketing, el paper técnico revela decisiones de arquitectura significativas. A diferencia de GPT-4V, que procesa imágenes mediante un sistema separado, Gemini fue diseñado desde cero como un modelo nativo multimodal — los diferentes tipos de datos (texto, imagen, audio, vídeo) se procesan dentro del mismo transformer, no como sistemas separados que se "conectan".</p><h2>Las tres versiones y para qué sirve cada una</h2><p><strong>Ultra</strong> (el más capaz) superó a expertos humanos en MMLU — el primer modelo en lograrlo — con una puntuación de 90.0%. <strong>Pro</strong> ofrece el mejor balance capacidad/coste para escalar a millones de usuarios. <strong>Nano</strong>, con dos subversiones (1.8B y 3.25B parámetros), está diseñado para ejecutarse directamente en dispositivos móviles.</p><h2>Lo técnico en términos simples</h2><p>La clave de Gemini es su tokenización unificada. Tanto el texto como las imágenes se convierten en secuencias de tokens que el mismo transformer procesa. Para vídeo, Gemini usa muestreo temporal — no procesa cada frame, sino que selecciona frames representativos a diferentes tasas según el contenido. Esto le permite "entender" vídeos de varios minutos sin explotar el contexto disponible.</p><blockquote>Gemini Ultra es el primer modelo en superar a expertos humanos en MMLU (90.0% vs. 89.8%), un benchmark de comprensión multidisciplinar considerado difícil incluso para especialistas.</blockquote><h2>Implicaciones prácticas</h2><p>Para desarrolladores, Gemini Pro (hoy disponible como Gemini 1.5 Pro) ofrece 1 millón de tokens de contexto — suficiente para procesar libros completos, una hora de vídeo o repositorios de código enteros. Las aplicaciones más interesantes emergen de combinar estos modalidades: enviar un vídeo de un error de instalación y pedir instrucciones de depuración, o analizar gráficos financieros junto con el informe anual.</p>',
                'key_contributions'      => json_encode([
                    'Primer modelo en superar a expertos humanos en MMLU (90.0%)',
                    'Arquitectura nativa multimodal — no un ensamble de modelos especializados',
                    'Gemini Nano ejecutable en dispositivos móviles con <4B parámetros',
                ]),
                'practical_implications' => json_encode([
                    'APIs de Google Cloud con contexto de 1M tokens para aplicaciones empresariales',
                    'Análisis de vídeo nativo sin necesidad de transcripción previa',
                    'On-device AI sin dependencia de servidor para casos de privacidad',
                ]),
                'difficulty_level'       => 'intermedio',
                'featured'               => true,
                'status'                 => 'published',
                'views'                  => 987,
                'reading_time'           => 7,
                'published_at'           => now()->subDays(12),
            ],
            [
                'arxiv_id'               => '2402.06196',
                'arxiv_url'              => 'https://arxiv.org/abs/2402.06196',
                'original_title'         => 'Scaling Laws for Neural Language Models',
                'original_abstract'      => 'We study empirical scaling laws for language model performance on the cross-entropy loss. The loss scales as a power-law with model size, dataset size, and the amount of compute...',
                'authors'                => json_encode(['Jared Kaplan', 'Sam McCandlish', 'Tom Henighan', 'Tom B. Brown']),
                'arxiv_published_date'   => '2020-01-23',
                'arxiv_category'         => 'cs.LG',
                'title'                  => 'Leyes de escala: por qué más datos y más parámetros casi siempre mejoran los LLMs',
                'slug'                   => 'leyes-de-escala-llms-kaplan-2020',
                'excerpt'                => 'El paper que cambió cómo la industria decide cuánto invertir en modelos. Las leyes de escala muestran que el rendimiento de los LLMs mejora de forma predecible con más cómputo, datos y parámetros.',
                'content'                => '<h2>El hallazgo central</h2><p>Kaplan et al. descubrieron algo que parece simple pero tiene consecuencias enormes: el rendimiento de un modelo de lenguaje sigue <em>leyes de potencia</em> respecto al tamaño del modelo (parámetros), el volumen de datos de entrenamiento y la cantidad de cómputo utilizada. Esto significa que el progreso en LLMs no es aleatorio ni impredecible — es, dentro de ciertos rangos, <strong>extrapolable</strong>.</p><h2>¿Qué es una ley de potencia aquí?</h2><p>Si graficamos pérdida del modelo vs. parámetros en escala logarítmica, obtenemos una línea recta. Esto implica que duplicar los parámetros reduce la pérdida en un factor constante. La relación no se aplana en los rangos estudiados — la mejora marginal no desaparece.</p><h2>La distribución óptima compute-datos-parámetros</h2><p>Una de las implicaciones prácticas más importantes: dado un presupuesto de cómputo fijo, ¿cómo distribuirlo entre parámetros y datos? Las leyes de Kaplan sugerían que era mejor entrenar modelos más grandes con menos pasos. Esto motivó la tendencia a GPT-3 (175B parámetros). Sin embargo, el paper posterior de Chinchilla (Hoffmann et al., 2022) refutó parcialmente esto, mostrando que el ratio óptimo exigía muchos más datos de los que Kaplan había considerado.</p><blockquote>Las leyes de escala convirtieron el entrenamiento de LLMs de un arte en (parcialmente) una ingeniería. Por primera vez, era posible predecir cuánto costaría alcanzar un nivel de rendimiento determinado antes de entrenar el modelo.</blockquote>',
                'key_contributions'      => json_encode([
                    'Demostración empírica de leyes de potencia para LLMs en tres ejes: tamaños, datos, cómputo',
                    'Framework para estimar el rendimiento esperado antes de entrenar un modelo',
                    'Base teórica que justificó la inversión masiva en modelos cada vez más grandes',
                ]),
                'practical_implications' => json_encode([
                    'Planificación de entrenamientos con presupuestos de cómputo fijos',
                    'Predicción de cuándo un modelo será "suficientemente bueno" para una tarea',
                    'Justificación de la carrera de escala que produjo GPT-3, GPT-4 y sus competidores',
                ]),
                'difficulty_level'       => 'avanzado',
                'featured'               => false,
                'status'                 => 'published',
                'views'                  => 743,
                'reading_time'           => 9,
                'published_at'           => now()->subDays(6),
            ],
        ];

        foreach ($papers as $p) {
            DB::table('conocia_papers')->insert(array_merge($p, [
                'image'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('ConocIA Papers: ' . count($papers) . ' registros creados.');
    }

    private function seedEstadoArte(): void
    {
        DB::table('estado_arte')->truncate();

        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek()->subWeek();
        $weekEnd   = $weekStart->copy()->endOfWeek();

        $digests = [
            [
                'title'          => 'IA Generativa — Semana del ' . $weekStart->format('d/m/Y'),
                'slug'           => 'ia-generativa-' . $weekStart->format('Y-W'),
                'subfield'       => 'ia-generativa',
                'subfield_label' => 'IA Generativa',
                'period_label'   => 'Semana del ' . $weekStart->format('d') . ' al ' . $weekEnd->format('d \d\e F Y'),
                'week_start'     => $weekStart->toDateString(),
                'week_end'       => $weekEnd->toDateString(),
                'excerpt'        => 'GPT-4o Mini llega al mercado con precios 15x menores, los modelos de vídeo alcanzan coherencia temporal y Claude 3.5 Sonnet supera benchmarks de codificación.',
                'content'        => '<h2>Resumen ejecutivo de la semana</h2><p>Fue una semana dominada por dos dinámicas: la presión competitiva sobre los precios de los modelos de texto y los avances notables en generación de vídeo. La combinación de ambas señala que el mercado de IA generativa está transitando de una fase de exploración a una fase de commoditización acelerada.</p><h2>Modelos de texto: la guerra de precios</h2><p>OpenAI lanzó GPT-4o Mini, posicionándolo como el sucesor de GPT-3.5 Turbo con una reducción de precio de aproximadamente 15x respecto a GPT-4o. La estrategia es clara: mantener GPT-4o para casos de uso premium y capturar el mercado masivo de aplicaciones de bajo coste con un modelo más pequeño pero competente. Anthropic respondió con actualizaciones de pricing para Claude Haiku, y Google ajustó Gemini Flash.</p><p>La implicación para desarrolladores es significativa: construir aplicaciones con LLMs ya no requiere justificar costes elevados. Aplicaciones con millones de llamadas diarias son ahora económicamente viables para startups bootstrapped.</p><h2>Generación de vídeo: coherencia temporal como nueva frontera</h2><p>Runway ML presentó Gen-3 Alpha con mejoras notables en coherencia temporal — el problema histórico de los modelos de vídeo donde los objetos "teletransportan" entre frames. Aunque el modelo todavía no es comercialmente generalizable, los ejemplos demostrados muestran coherencia de varios segundos en escenas con movimiento complejo.</p><blockquote>La coherencia temporal es al vídeo lo que la coherencia semántica fue al texto. Una vez resuelta consistentemente, el mercado se abre exponencialmente.</blockquote><h2>Codificación: Claude 3.5 Sonnet establece nuevo estándar</h2><p>Anthropic publicó benchmarks que muestran que Claude 3.5 Sonnet supera a GPT-4o en SWE-Bench, el benchmark de resolución autónoma de bugs en repositorios reales de GitHub. Con un 49% de issues resueltos (vs. 38.8% de GPT-4o), es el primer modelo en superar el umbral del 40% en este benchmark considerado difícil para medir agencia de código real.</p><h2>Tendencia a vigilar</h2><p>El patrón de la semana — presión de precios + mejora de capacidades especializadas — es consistente con la dinámica que se observó en el mercado de chips gráficos y cloud computing. El commodity llega primero a las capas de infraestructura; las capas de aplicación conservan márgenes más tiempo. Los desarrolladores que construyan sobre las APIs de bajo nivel competirán en un mercado de margen cero; los que construyan diferenciación en la capa de aplicación tendrán más tiempo.</p>',
                'source_news_ids'  => json_encode([]),
                'key_developments' => json_encode([
                    'GPT-4o Mini reduce precios 15x respecto a GPT-4o',
                    'Runway Gen-3 Alpha mejora coherencia temporal en vídeo',
                    'Claude 3.5 Sonnet: 49% en SWE-Bench, nuevo récord',
                    'Guerra de precios entre OpenAI, Anthropic y Google',
                ]),
                'featured'       => true,
                'status'         => 'published',
                'views'          => 1432,
                'reading_time'   => 8,
                'published_at'   => $weekEnd->copy()->addDay(),
            ],
            [
                'title'          => 'NLP — Semana del ' . $weekStart->format('d/m/Y'),
                'slug'           => 'nlp-' . $weekStart->format('Y-W'),
                'subfield'       => 'nlp',
                'subfield_label' => 'Procesamiento del Lenguaje Natural',
                'period_label'   => 'Semana del ' . $weekStart->format('d') . ' al ' . $weekEnd->format('d \d\e F Y'),
                'week_start'     => $weekStart->toDateString(),
                'week_end'       => $weekEnd->toDateString(),
                'excerpt'        => 'Nuevas técnicas de RAG con grafos de conocimiento, benchmarks de comprensión multilingüe y el debate sobre la saturación de los benchmarks de NLP.',
                'content'        => '<h2>RAG evoluciona hacia grafos de conocimiento</h2><p>La semana estuvo marcada por publicaciones sobre GraphRAG, la extensión de Retrieval-Augmented Generation que incorpora grafos de conocimiento. Microsoft publicó su implementación open source de GraphRAG, que en lugar de simplemente recuperar chunks de texto relevantes, construye un grafo de entidades y relaciones que permite responder preguntas sobre conexiones implícitas entre conceptos.</p><p>La ventaja práctica: GraphRAG responde mejor preguntas del tipo "¿cómo se relaciona X con Y?" que los sistemas RAG tradicionales, que son mejores en preguntas factuales directas.</p><h2>El problema de la saturación de benchmarks</h2><p>Varios papers de esta semana tocan un tema incómodo: los principales benchmarks de NLP (MMLU, HellaSwag, ARC) están siendo "saturados" por los modelos actuales. Cuando GPT-4 y Claude 3 Opus tienen scores superiores al 90% en benchmarks diseñados para ser difíciles para los LLMs de 2020, el benchmark deja de discriminar entre modelos.</p><blockquote>Un benchmark que no distingue entre los mejores modelos actuales ya no mide progreso — mide historia.</blockquote><h2>Comprensión multilingüe: la brecha que persiste</h2><p>Un paper de Google publicado esta semana analiza el rendimiento de los LLMs en 100 idiomas en tareas de razonamiento. El hallazgo no es sorprendente pero sí importante: la degradación de rendimiento es pronunciada para idiomas de baja representación en los datos de entrenamiento. Para español e idiomas romances, la brecha respecto al inglés es manejable (5-10%). Para idiomas como swahili, tamil o quechua, la brecha puede superar el 30%.</p>',
                'source_news_ids'  => json_encode([]),
                'key_developments' => json_encode([
                    'Microsoft lanza GraphRAG open source con soporte de grafos de conocimiento',
                    'Saturación de benchmarks tradicionales: MMLU ya no discrimina modelos de frontera',
                    'Brecha de rendimiento multilingüe: 30%+ para idiomas de baja representación',
                ]),
                'featured'       => false,
                'status'         => 'published',
                'views'          => 876,
                'reading_time'   => 7,
                'published_at'   => $weekEnd->copy()->addDay(),
            ],
            [
                'title'          => 'Regulación IA — Semana del ' . $weekStart->format('d/m/Y'),
                'slug'           => 'regulacion-ia-' . $weekStart->format('Y-W'),
                'subfield'       => 'regulacion-ia',
                'subfield_label' => 'Regulación e Impacto Social',
                'period_label'   => 'Semana del ' . $weekStart->format('d') . ' al ' . $weekEnd->format('d \d\e F Y'),
                'week_start'     => $weekStart->toDateString(),
                'week_end'       => $weekEnd->toDateString(),
                'excerpt'        => 'La AI Act europea entra en fase de implementación, California debate SB 1047 y el G7 publica un marco de gobernanza para IA en infraestructuras críticas.',
                'content'        => '<h2>La AI Act: de papel a realidad</h2><p>El Reglamento europeo de IA (AI Act) entró en vigor en agosto de 2024, pero sus disposiciones más importantes se aplican de forma escalonada. Esta semana se publicaron las primeras guías técnicas de la Oficina de IA Europea sobre cómo clasificar sistemas de IA de "alto riesgo", la categoría que incluye IA usada en reclutamiento, educación, crédito y aplicaciones de justicia.</p><p>Para las empresas tech con operaciones en Europa, el impacto práctico es comenzar ya: los sistemas desplegados antes de la fecha de aplicación tienen un período de gracia, pero los nuevos sistemas deben cumplir desde el día uno.</p><h2>California SB 1047: el debate sobre la regulación preventiva</h2><p>El proyecto de ley californiano SB 1047, que requeriría que las empresas de IA implementen "kill switches" y realicen evaluaciones de seguridad antes de entrenar modelos sobre ciertos umbrales de cómputo, fue aprobado por el legislativo pero vetado por el gobernador Newsom. El veto reavivó el debate: ¿es posible regular la IA antes de que los daños ocurran, o la regulación preventiva inevitablemente frenará la innovación?</p><blockquote>El veto de Newsom no es el fin del debate sobre regulación de IA en EEUU — es el comienzo de la siguiente ronda. Los estados que no regulen serán el destino preferido de las empresas que huyen de los que sí lo hacen.</blockquote>',
                'source_news_ids'  => json_encode([]),
                'key_developments' => json_encode([
                    'AI Act: primeras guías técnicas europeas para sistemas de "alto riesgo"',
                    'Gobernador Newsom veta SB 1047, reabriendo el debate sobre regulación preventiva',
                    'G7 publica marco de gobernanza para IA en infraestructuras críticas',
                ]),
                'featured'       => false,
                'status'         => 'published',
                'views'          => 654,
                'reading_time'   => 6,
                'published_at'   => $weekEnd->copy()->addDay(),
            ],
        ];

        foreach ($digests as $d) {
            DB::table('estado_arte')->insert(array_merge($d, [
                'image'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Estado del Arte: ' . count($digests) . ' registros creados.');
    }
}
