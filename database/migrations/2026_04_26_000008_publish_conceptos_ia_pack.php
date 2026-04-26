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

        foreach ($this->concepts() as $concept) {
            $exists = DB::table('conceptos_ia')->where('slug', $concept['slug'])->exists();

            $payload = array_merge($concept, [
                'related_concepts' => $this->json($concept['related_concepts'] ?? []),
                'key_players' => $this->json($concept['key_players'] ?? []),
                'further_reading' => $this->json($concept['further_reading'] ?? []),
                'status' => 'published',
                'published_at' => $concept['published_at'] ?? $now,
                'updated_at' => $now,
            ]);

            unset($payload['published_at_override']);

            if (!$exists) {
                $payload['views'] = 0;
                $payload['created_at'] = $now;
            }

            DB::table('conceptos_ia')->updateOrInsert(['slug' => $concept['slug']], $payload);
        }

        $this->clearCaches();
    }

    public function down(): void
    {
        DB::table('conceptos_ia')
            ->whereIn('slug', array_column($this->concepts(), 'slug'))
            ->delete();

        $this->clearCaches();
    }

    private function concepts(): array
    {
        return [
            [
                'title' => 'Agentes de IA',
                'slug' => 'agentes-de-ia',
                'definition' => 'Un agente de IA es un sistema capaz de observar un contexto, decidir pasos intermedios y ejecutar acciones usando herramientas, APIs o interfaces digitales.',
                'excerpt' => 'Los agentes de IA prometen pasar de responder preguntas a completar tareas. Su valor esta en coordinar razonamiento, herramientas y accion; su riesgo esta en la confiabilidad de procesos largos.',
                'category' => 'Agentes',
                'content' => $this->content(
                    'Agentes de IA',
                    'Un agente de IA no es solo un chatbot con mejor memoria. Es un sistema que puede recibir un objetivo, dividirlo en pasos, consultar herramientas, tomar decisiones intermedias y ejecutar acciones. En vez de limitarse a generar texto, intenta operar sobre un entorno: una aplicacion, una base de datos, un navegador, una API o un flujo de trabajo.',
                    'El concepto importa porque muchas empresas quieren automatizar procesos completos, no solo producir respuestas. Un agente puede revisar informacion, preparar documentos, actualizar registros, enviar solicitudes o coordinar tareas entre sistemas. Por eso aparece en salud, soporte, ventas, programacion, finanzas y operaciones.',
                    'La dificultad esta en la confiabilidad. Resolver subtareas no significa completar bien un flujo de punta a punta. Un agente puede equivocarse al interpretar una pantalla, omitir un paso, usar datos desactualizados o ejecutar una accion irreversible. Por eso los agentes necesitan permisos acotados, trazabilidad, evaluaciones realistas y supervision humana.',
                    'Un ejemplo simple es un agente que gestiona una solicitud: lee el caso, busca antecedentes, completa un formulario, adjunta documentos y deja registro. Si cualquiera de esos pasos falla, el resultado completo falla. Esa es la diferencia entre una demo atractiva y una automatizacion lista para produccion.'
                ),
                'related_concepts' => ['evaluacion-de-modelos', 'ventana-de-contexto', 'rag'],
                'key_players' => [
                    ['name' => 'OpenAI', 'role' => 'Impulso de agentes y herramientas de uso computacional'],
                    ['name' => 'Anthropic', 'role' => 'Investigacion en seguridad y uso de herramientas'],
                    ['name' => 'Stanford HAI', 'role' => 'Evaluacion de impacto y confiabilidad'],
                ],
                'further_reading' => [
                    ['title' => 'HealthAdminBench en arXiv', 'url' => 'https://arxiv.org/abs/2604.09937'],
                ],
                'featured' => true,
                'reading_time' => 4,
            ],
            [
                'title' => 'RAG',
                'slug' => 'rag',
                'definition' => 'RAG, o generacion aumentada por recuperacion, combina busqueda de informacion externa con generacion de lenguaje para responder con contexto mas actualizado y verificable.',
                'excerpt' => 'RAG permite que un modelo consulte documentos, bases de conocimiento o fuentes internas antes de responder. Es una arquitectura clave para empresas que necesitan respuestas ancladas en informacion propia.',
                'category' => 'Arquitecturas',
                'content' => $this->content(
                    'RAG',
                    'RAG significa Retrieval-Augmented Generation. La idea es sencilla: antes de responder, el sistema busca informacion relevante en una fuente externa y entrega ese contexto al modelo generativo. Asi el modelo no depende solo de lo aprendido durante entrenamiento.',
                    'Es importante porque muchas aplicaciones empresariales necesitan responder sobre politicas internas, contratos, manuales, papers, tickets o documentos que cambian con frecuencia. RAG permite conectar un modelo de lenguaje con conocimiento vivo sin reentrenarlo cada vez.',
                    'Sus limites aparecen cuando la recuperacion falla. Si el sistema trae documentos equivocados, fragmentos incompletos o informacion contradictoria, el modelo puede responder con seguridad pero sobre una base debil. La calidad de RAG depende tanto del buscador, los embeddings y el chunking como del modelo generativo.',
                    'Un caso comun es un asistente interno que responde preguntas sobre procedimientos de una empresa. El modelo recibe la consulta, recupera fragmentos de la base documental y genera una respuesta citando o usando ese material como evidencia.'
                ),
                'related_concepts' => ['embeddings', 'ventana-de-contexto', 'alucinacion-en-ia'],
                'key_players' => [
                    ['name' => 'Meta AI', 'role' => 'Investigacion inicial sobre retrieval-augmented generation'],
                    ['name' => 'Pinecone, Weaviate, Milvus', 'role' => 'Infraestructura vectorial usada en sistemas RAG'],
                ],
                'further_reading' => [
                    ['title' => 'Retrieval-Augmented Generation for Knowledge-Intensive NLP Tasks', 'url' => 'https://arxiv.org/abs/2005.11401'],
                ],
                'featured' => true,
                'reading_time' => 4,
            ],
            [
                'title' => 'Embeddings',
                'slug' => 'embeddings',
                'definition' => 'Un embedding es una representacion numerica que captura relaciones semanticas entre textos, imagenes, productos, usuarios u otros objetos.',
                'excerpt' => 'Los embeddings convierten informacion compleja en vectores que pueden compararse matematicamente. Son la base de busqueda semantica, recomendaciones, clustering y sistemas RAG.',
                'category' => 'Machine Learning',
                'content' => $this->content(
                    'Embeddings',
                    'Un embedding transforma un objeto en una lista de numeros. Ese objeto puede ser una palabra, una frase, un documento, una imagen, un producto o incluso un usuario. La gracia es que objetos con significado parecido quedan cerca dentro de un espacio vectorial.',
                    'Esto permite hacer busqueda semantica. En vez de buscar solo palabras exactas, el sistema puede encontrar contenido por significado. Si alguien pregunta por "costos de entrenamiento de modelos", puede recuperar documentos sobre computo, eficiencia o infraestructura aunque no usen exactamente esas palabras.',
                    'El riesgo es olvidar que los embeddings no entienden en sentido humano. Capturan patrones estadisticos y relaciones aprendidas. Pueden arrastrar sesgos, mezclar conceptos cercanos pero no equivalentes o degradarse si el dominio es muy especifico y el modelo no fue ajustado para el contexto.',
                    'En una aplicacion RAG, cada documento se divide en fragmentos, se convierte en embeddings y se guarda en una base vectorial. Cuando llega una pregunta, tambien se convierte en embedding y se buscan los fragmentos mas cercanos.'
                ),
                'related_concepts' => ['rag', 'evaluacion-de-modelos', 'ventana-de-contexto'],
                'key_players' => [
                    ['name' => 'Google', 'role' => 'Investigacion historica en representaciones vectoriales'],
                    ['name' => 'OpenAI', 'role' => 'Modelos de embeddings usados en aplicaciones empresariales'],
                ],
                'further_reading' => [
                    ['title' => 'Efficient Estimation of Word Representations in Vector Space', 'url' => 'https://arxiv.org/abs/1301.3781'],
                ],
                'featured' => true,
                'reading_time' => 4,
            ],
            [
                'title' => 'Ventana de contexto',
                'slug' => 'ventana-de-contexto',
                'definition' => 'La ventana de contexto es la cantidad de informacion que un modelo puede considerar al mismo tiempo durante una interaccion.',
                'excerpt' => 'Una ventana de contexto amplia permite trabajar con documentos largos, historiales extensos o multiples fuentes. Pero mas contexto no siempre significa mejor razonamiento.',
                'category' => 'Modelos de lenguaje',
                'content' => $this->content(
                    'Ventana de contexto',
                    'La ventana de contexto define cuanta informacion puede recibir y mantener activa un modelo en una solicitud. Incluye el prompt del usuario, instrucciones del sistema, historial de conversacion, documentos adjuntos y cualquier contenido recuperado por herramientas.',
                    'Su importancia crecio porque muchas tareas reales requieren leer contratos, expedientes, repositorios de codigo, papers o historiales de conversacion. Una ventana amplia permite que el modelo trabaje con mas material sin resumirlo previamente.',
                    'El limite es que una ventana grande no garantiza que el modelo use toda la informacion de forma perfecta. Puede perder detalles, sobreponderar partes recientes, ignorar instrucciones escondidas o confundirse con contexto redundante. Tambien aumenta costo y latencia.',
                    'En una revision legal, por ejemplo, un modelo con contexto amplio puede recibir un contrato completo y varias politicas internas. Aun asi, conviene estructurar la tarea, pedir citas y verificar puntos criticos.'
                ),
                'related_concepts' => ['rag', 'modelos-de-frontera', 'alucinacion-en-ia'],
                'key_players' => [
                    ['name' => 'Anthropic', 'role' => 'Modelos con ventanas de contexto extensas'],
                    ['name' => 'Google DeepMind', 'role' => 'Investigacion y productos con contexto largo'],
                ],
                'further_reading' => [
                    ['title' => 'Long-context language models overview', 'url' => 'https://arxiv.org/search/cs?query=long+context+language+models&searchtype=all'],
                ],
                'featured' => false,
                'reading_time' => 4,
            ],
            [
                'title' => 'Modelos de frontera',
                'slug' => 'modelos-de-frontera',
                'definition' => 'Los modelos de frontera son sistemas de IA ubicados cerca del limite actual de capacidades, costo, escala e impacto potencial.',
                'excerpt' => 'El termino se usa para modelos avanzados con capacidades generales relevantes y riesgos potenciales mayores. Aparece en debates de regulacion, seguridad y competencia tecnologica.',
                'category' => 'Gobernanza',
                'content' => $this->content(
                    'Modelos de frontera',
                    'Un modelo de frontera es un sistema que se ubica cerca del limite conocido de capacidades en inteligencia artificial. No se define solo por cantidad de parametros, sino por rendimiento general, autonomia potencial, capacidad multimodal, uso de herramientas e impacto social o economico.',
                    'El concepto es clave para regulacion y seguridad. Los modelos mas capaces pueden crear valor en ciencia, programacion, salud o educacion, pero tambien amplifican riesgos como ciberabuso, desinformacion, dependencia tecnologica o uso indebido de capacidades avanzadas.',
                    'No existe una frontera fija. Lo que hoy parece avanzado puede volverse comun en pocos meses. Por eso algunas propuestas regulatorias hablan de umbrales dinamicos, evaluaciones externas, reportes de seguridad y monitoreo despues del despliegue.',
                    'Cuando se dice que GPT, Claude, Gemini o modelos similares compiten en la frontera, se esta hablando de sistemas cuyo rendimiento marca la pauta del mercado y empuja al resto del ecosistema.'
                ),
                'related_concepts' => ['evaluacion-de-modelos', 'alucinacion-en-ia', 'agentes-de-ia'],
                'key_players' => [
                    ['name' => 'OpenAI, Anthropic, Google DeepMind', 'role' => 'Laboratorios asociados a modelos de frontera'],
                    ['name' => 'AI Safety Institute', 'role' => 'Evaluacion y seguridad de modelos avanzados'],
                ],
                'further_reading' => [
                    ['title' => 'Frontier AI regulation: managing emerging risks', 'url' => 'https://arxiv.org/abs/2307.03718'],
                ],
                'featured' => false,
                'reading_time' => 4,
            ],
            [
                'title' => 'Evaluacion de modelos',
                'slug' => 'evaluacion-de-modelos',
                'definition' => 'La evaluacion de modelos mide capacidades, limites, riesgos y rendimiento de un sistema de IA bajo tareas, datos y condiciones especificas.',
                'excerpt' => 'Evaluar IA no es solo mirar benchmarks. Tambien implica medir robustez, seguridad, sesgos, uso real, confiabilidad end-to-end y comportamiento bajo presion.',
                'category' => 'Evaluacion',
                'content' => $this->content(
                    'Evaluacion de modelos',
                    'Evaluar un modelo de IA significa comprobar que puede hacer, en que falla y bajo que condiciones. Los benchmarks tradicionales miden tareas definidas, pero las aplicaciones reales suelen exigir continuidad, manejo de errores, interpretacion de contexto y decisiones confiables.',
                    'La evaluacion importa porque una demo puede verse convincente y aun asi fallar en produccion. En salud, finanzas, educacion o gobierno no basta con respuestas promedio buenas: hay que conocer casos limite, tasas de error, sesgos, estabilidad y trazabilidad.',
                    'Un limite de muchos benchmarks es que se saturan rapido o no representan flujos reales. Por eso crecen evaluaciones end-to-end, pruebas con usuarios, auditorias de seguridad, red teaming y metricas especificas por dominio.',
                    'HealthAdminBench es un buen ejemplo: no pregunta si un modelo sabe sobre administracion de salud, sino si puede completar tareas de principio a fin en entornos simulados con multiples pasos verificables.'
                ),
                'related_concepts' => ['modelos-de-frontera', 'agentes-de-ia', 'alucinacion-en-ia'],
                'key_players' => [
                    ['name' => 'Stanford HAI', 'role' => 'Reportes y benchmarks sobre impacto de IA'],
                    ['name' => 'METR', 'role' => 'Evaluaciones de capacidades en agentes y automatizacion'],
                ],
                'further_reading' => [
                    ['title' => 'HealthAdminBench', 'url' => 'https://arxiv.org/abs/2604.09937'],
                ],
                'featured' => false,
                'reading_time' => 4,
            ],
            [
                'title' => 'Alucinacion en IA',
                'slug' => 'alucinacion-en-ia',
                'definition' => 'Una alucinacion ocurre cuando un modelo genera informacion falsa, inventada o no respaldada por la evidencia disponible.',
                'excerpt' => 'Las alucinaciones son uno de los riesgos centrales de los modelos generativos. Pueden sonar convincentes, incluir detalles precisos y aun asi ser incorrectas.',
                'category' => 'Riesgos',
                'content' => $this->content(
                    'Alucinacion en IA',
                    'Una alucinacion es una salida generada por un modelo que parece plausible pero no corresponde a hechos, fuentes o datos reales. Puede ser una cita inexistente, una explicacion equivocada, una fecha falsa o una conclusion sin soporte.',
                    'El problema es especialmente delicado porque los modelos de lenguaje son buenos produciendo texto coherente. La forma puede transmitir seguridad incluso cuando el contenido es incorrecto. Esto afecta periodismo, educacion, salud, derecho y cualquier flujo donde la precision importe.',
                    'RAG, herramientas de busqueda y citacion ayudan, pero no eliminan el riesgo. Si el sistema recupera malas fuentes o el modelo interpreta mal la evidencia, la respuesta puede seguir fallando. Por eso se necesitan verificaciones, fuentes visibles y limites claros.',
                    'Un ejemplo comun es pedir referencias academicas y recibir autores, titulos o DOI inventados. La respuesta puede parecer formal, pero al buscar las fuentes no existen.'
                ),
                'related_concepts' => ['rag', 'evaluacion-de-modelos', 'modelos-de-frontera'],
                'key_players' => [
                    ['name' => 'Investigacion en NLP', 'role' => 'Estudio de factualidad y confiabilidad'],
                    ['name' => 'Equipos de seguridad de IA', 'role' => 'Reduccion de errores en sistemas desplegados'],
                ],
                'further_reading' => [
                    ['title' => 'Survey of hallucination in natural language generation', 'url' => 'https://arxiv.org/abs/2202.03629'],
                ],
                'featured' => false,
                'reading_time' => 4,
            ],
            [
                'title' => 'Fine-tuning',
                'slug' => 'fine-tuning',
                'definition' => 'Fine-tuning es el proceso de ajustar un modelo preentrenado con datos adicionales para adaptarlo a una tarea, estilo o dominio especifico.',
                'excerpt' => 'El fine-tuning permite especializar modelos sin entrenarlos desde cero. Es util cuando se necesita comportamiento consistente, formato estable o conocimiento de dominio controlado.',
                'category' => 'Entrenamiento',
                'content' => $this->content(
                    'Fine-tuning',
                    'Fine-tuning significa tomar un modelo ya entrenado y continuar su entrenamiento con un conjunto de datos mas especifico. En vez de partir desde cero, se aprovecha el conocimiento general del modelo base y se ajusta a una necesidad concreta.',
                    'Es util cuando una organizacion requiere un tono, formato o comportamiento repetible. Tambien puede servir para clasificacion, extraccion de informacion, respuestas con estructura fija o adaptacion a lenguaje de dominio.',
                    'No siempre es la mejor primera opcion. Para muchas aplicaciones, RAG, buen prompting o herramientas externas pueden ser suficientes. Fine-tuning exige datos de calidad, evaluacion y mantenimiento; si los datos son malos, el modelo aprende patrones malos.',
                    'Un caso practico es ajustar un modelo para transformar tickets de soporte en categorias internas con un formato exacto. Si la tarea es repetitiva y hay ejemplos buenos, fine-tuning puede mejorar consistencia y reducir costo por llamada.'
                ),
                'related_concepts' => ['rag', 'evaluacion-de-modelos', 'embeddings'],
                'key_players' => [
                    ['name' => 'Hugging Face', 'role' => 'Ecosistema abierto para entrenamiento y ajuste de modelos'],
                    ['name' => 'OpenAI', 'role' => 'Servicios de fine-tuning para modelos comerciales'],
                ],
                'further_reading' => [
                    ['title' => 'Parameter-Efficient Fine-Tuning overview', 'url' => 'https://arxiv.org/abs/2303.15647'],
                ],
                'featured' => false,
                'reading_time' => 4,
            ],
            [
                'title' => 'Mixture of Experts',
                'slug' => 'mixture-of-experts',
                'definition' => 'Mixture of Experts es una arquitectura donde distintas partes especializadas del modelo se activan segun la entrada, reduciendo costo frente a activar todo el modelo.',
                'excerpt' => 'MoE permite escalar capacidad total sin usar todos los parametros en cada inferencia. Es clave en varios modelos modernos por eficiencia y especializacion.',
                'category' => 'Arquitecturas',
                'content' => $this->content(
                    'Mixture of Experts',
                    'Mixture of Experts, o MoE, es una arquitectura donde el modelo contiene multiples expertos y un mecanismo decide cuales activar para cada entrada. En vez de usar todos los parametros siempre, solo se activa una parte relevante.',
                    'La ventaja es eficiencia. Un modelo puede tener mucha capacidad total, pero usar menos computo por token o por consulta. Esto permite escalar modelos grandes sin que cada inferencia tenga el costo completo de activar toda la red.',
                    'Sus desafios incluyen balancear carga entre expertos, evitar que algunos queden subutilizados y mantener estabilidad durante entrenamiento. Tambien puede hacer mas compleja la interpretacion del modelo y su despliegue en infraestructura.',
                    'Un ejemplo intuitivo es un sistema con especialistas: uno para codigo, otro para matematicas, otro para lenguaje natural. El enrutador decide a quienes consultar segun la pregunta, aunque en la practica los expertos no son tan humanos ni tan separables.'
                ),
                'related_concepts' => ['modelos-de-frontera', 'inferencia', 'evaluacion-de-modelos'],
                'key_players' => [
                    ['name' => 'Google', 'role' => 'Investigacion temprana en Sparsely-Gated Mixture-of-Experts'],
                    ['name' => 'Mistral AI', 'role' => 'Modelos abiertos con arquitectura MoE'],
                ],
                'further_reading' => [
                    ['title' => 'Outrageously Large Neural Networks', 'url' => 'https://arxiv.org/abs/1701.06538'],
                ],
                'featured' => false,
                'reading_time' => 4,
            ],
            [
                'title' => 'Inferencia',
                'slug' => 'inferencia',
                'definition' => 'Inferencia es el proceso por el cual un modelo ya entrenado genera una respuesta, prediccion o accion frente a una entrada nueva.',
                'excerpt' => 'Entrenar crea el modelo; inferir lo usa. La inferencia define costo, latencia, experiencia de usuario y viabilidad economica de muchas aplicaciones de IA.',
                'category' => 'Infraestructura',
                'content' => $this->content(
                    'Inferencia',
                    'Inferencia es el uso de un modelo entrenado para producir una salida. Cuando escribes una pregunta a un chatbot, cuando un sistema clasifica un documento o cuando una aplicacion genera una imagen, esta ocurriendo inferencia.',
                    'Es importante porque la mayor parte del costo operativo de una aplicacion de IA puede aparecer despues del entrenamiento. Cada usuario, consulta, token, imagen o accion consume computo. Por eso la eficiencia de inferencia afecta precio, velocidad y escalabilidad.',
                    'Optimizar inferencia puede incluir cuantizacion, caching, modelos mas pequenos, batching, hardware especializado, arquitectura MoE o limitar la ventana de contexto. Pero cada optimizacion puede traer trade-offs de calidad, latencia o complejidad.',
                    'Un ejemplo simple: dos modelos pueden responder igual de bien, pero uno tarda 300 ms y cuesta una fraccion del otro. En una app con millones de consultas, esa diferencia define si el producto es viable.'
                ),
                'related_concepts' => ['mixture-of-experts', 'ventana-de-contexto', 'modelos-de-frontera'],
                'key_players' => [
                    ['name' => 'NVIDIA', 'role' => 'Hardware y software para inferencia acelerada'],
                    ['name' => 'Groq, Cerebras, cloud providers', 'role' => 'Infraestructura especializada para servir modelos'],
                ],
                'further_reading' => [
                    ['title' => 'Efficient inference in large language models', 'url' => 'https://arxiv.org/search/cs?query=efficient+LLM+inference&searchtype=all'],
                ],
                'featured' => false,
                'reading_time' => 4,
            ],
        ];
    }

    private function content(string $title, string $intro, string $why, string $limits, string $example): string
    {
        return <<<HTML
<h2>Que es {$title}</h2>
<p>{$intro}</p>

<h2>Por que importa</h2>
<p>{$why}</p>

<h2>Limites y riesgos</h2>
<p>{$limits}</p>

<h2>Ejemplo practico</h2>
<p>{$example}</p>
HTML;
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
            'home_latest_conceptos',
            'conceptos_featured',
            'conceptos_categories',
        ] as $key) {
            Cache::forget($key);
        }
    }
};
