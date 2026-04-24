<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Research;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublishChileAiResearchPack extends Command
{
    protected $signature = 'content:publish-chile-ai-research-pack
                            {--user-id= : ID del usuario autor}
                            {--force : Sobrescribe el contenido si ya existe}';

    protected $description = 'Publica o actualiza un pack editorial de investigaciones sobre IA en Chile';

    public function handle(): int
    {
        $author = $this->resolveAuthor();

        if (!$author) {
            $this->error('No encontré un usuario autor. Usa --user-id=<id> o crea un usuario primero.');
            return self::FAILURE;
        }

        $category = Category::firstOrCreate(
            ['slug' => 'investigacion'],
            [
                'name' => 'Investigación',
                'description' => 'Investigaciones, estudios y papers clave sobre IA.',
                'is_active' => true,
            ]
        );

        $items = $this->researchDefinitions($author, $category->id);

        foreach ($items as $index => $item) {
            $existing = Research::where('slug', $item['slug'])->first();

            if ($existing && !$this->option('force')) {
                $this->warn("La investigación ya existe con slug {$item['slug']}. Usa --force para actualizarla.");
                continue;
            }

            $payload = $item;
            $payload['published_at'] = now()->subMinutes(max(0, count($items) - $index - 1));

            if ($existing) {
                $existing->update($payload);
                $research = $existing->fresh();
                $this->info("Investigación actualizada: {$research->title}");
            } else {
                $research = Research::create($payload);
                $this->info("Investigación publicada: {$research->title}");
            }

            $tagIds = collect($item['editorial_tags'])->map(function (string $name) {
                return Tag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                )->id;
            })->all();

            if (method_exists($research, 'tags')) {
                $research->tags()->sync($tagIds);
            }

            Research::clearFrontCache();
            Cache::forget("research_article_{$research->slug}");

            $this->line("Slug: {$research->slug}");
        }

        $this->line("Categoría: {$category->name}");
        $this->line("Autor asignado: {$author->name} <{$author->email}>");

        return self::SUCCESS;
    }

    private function researchDefinitions(User $author, int $categoryId): array
    {
        return [
            [
                'title' => 'IA para reconstruir memoria: un paper chileno propone grafos de conocimiento sobre archivos de la dictadura',
                'slug' => 'ia-reconstruir-memoria-grafos-conocimiento-archivos-dictadura-chile',
                'summary' => 'Un equipo vinculado a la UC, el IMFD y la UAH presentó un paper que usa modelos de lenguaje para construir grafos de conocimiento a partir de documentos históricos de la dictadura chilena. El proyecto combina IA, memoria y justicia transicional en uno de los casos más relevantes de investigación aplicada en Chile.',
                'excerpt' => 'Un equipo vinculado a la UC, el IMFD y la UAH presentó un paper que usa modelos de lenguaje para construir grafos de conocimiento a partir de documentos históricos de la dictadura chilena. El proyecto combina IA, memoria y justicia transicional en uno de los casos más relevantes de investigación aplicada en Chile.',
                'abstract' => 'Un equipo vinculado a la UC, el IMFD y la UAH presentó un paper que usa modelos de lenguaje para construir grafos de conocimiento a partir de documentos históricos de la dictadura chilena. El proyecto combina IA, memoria y justicia transicional en uno de los casos más relevantes de investigación aplicada en Chile.',
                'content' => <<<'HTML'
<p>Gran parte de la conversación sobre inteligencia artificial en Chile oscila entre la promesa productiva, la discusión regulatoria y la adopción en educación o empresas. Pero hay una línea de trabajo mucho más singular, y probablemente más profunda, que está emergiendo desde el ecosistema académico chileno: usar IA no para automatizar tareas genéricas, sino para reconstruir memoria histórica a partir de archivos complejos, fragmentados y masivos. En ese marco, el paper <strong>Automatic knowledge-graph creation from historical documents: The Chilean dictatorship as a case study</strong> se vuelve especialmente importante.</p>

<p>El trabajo, subido a arXiv en agosto de 2024, presenta resultados sobre la <strong>construcción automática de grafos de conocimiento</strong> desde documentos históricos relacionados con la dictadura chilena. El equipo está integrado por investigadoras e investigadores ligados a la <strong>Universidad Católica</strong>, el <strong>Instituto Milenio Fundamentos de los Datos (IMFD)</strong> y la <strong>Universidad Alberto Hurtado</strong>, y se conecta directamente con el proyecto <strong>Nuestra MemorIA</strong>, una iniciativa que busca rescatar, organizar e interpretar archivos de derechos humanos con apoyo de inteligencia artificial.</p>

<h2>Qué hace realmente este paper</h2>

<p>El objetivo del trabajo no es producir un chatbot sobre memoria histórica ni resumir documentos. El paper se enfoca en un problema más estructural: cómo convertir documentos dispersos en una red formal de entidades y relaciones que permita investigar mejor. En términos concretos, el sistema usa modelos de lenguaje para <strong>reconocer entidades</strong>, <strong>extraer relaciones entre ellas</strong> y <strong>resolver referencias</strong> entre esos valores, con el fin de construir un grafo de conocimiento utilizable.</p>

<p>Ese punto importa mucho. En contextos como archivos de la dictadura, el problema no es solo que haya demasiados documentos, sino que la información relevante aparece repartida entre testimonios, informes, registros judiciales, fotografías, escaneos y otras fuentes incompletas o heterogéneas. Lo que hace valioso a este enfoque es su capacidad de integrar fragmentos dispersos en una estructura relacional que ayude a historiadores, juristas e investigadores sociales a navegar mejor un corpus extremadamente complejo.</p>

<h2>Una IA con restricciones para evitar alucinaciones</h2>

<p>Uno de los aspectos más serios del paper es que no se limita a “usar LLMs” de manera abierta. Los autores explican que, para reducir alucinaciones, la interacción con el modelo se apoya en una <strong>ontología simple con 4 tipos de entidades y 7 tipos de relaciones</strong>. Es decir, el sistema no deja al modelo inventar libremente la estructura del mundo, sino que lo fuerza a operar dentro de un marco semántico controlado.</p>

<p>Ese diseño es clave porque toca uno de los puntos más sensibles del uso de IA en dominios históricos y jurídicos: la confiabilidad. Cuando se trabaja con memoria, derechos humanos y justicia transicional, una alucinación no es un error simpático. Puede alterar interpretaciones, desviar búsquedas o contaminar procesos de reconstrucción documental. El paper toma en serio ese problema y por eso vale más que como simple caso de uso tecnológico.</p>

<h2>El valor chileno del caso de estudio</h2>

<p>A diferencia de muchas piezas sobre IA que usan datasets abstractos o benchmarks internacionales, aquí el caso de estudio es Chile y su propia historia. Esa especificidad lo vuelve editorialmente muy potente. El proyecto Nuestra MemorIA, presentado además por el IMFD en 2024, plantea una tarea casi imposible de hacer manualmente: revisar miles de documentos escaneados, fotografías, grabaciones y registros repartidos entre distintas instituciones para reconstruir conocimiento histórico y apoyar procesos de verdad y justicia.</p>

<p>Según el IMFD, la iniciativa cuenta con apoyo del <strong>Museo de la Memoria</strong>, el <strong>INDH</strong>, la <strong>Vicaría de la Solidaridad</strong> y la <strong>Subsecretaría de Derechos Humanos</strong>. Eso cambia completamente el peso del proyecto. Ya no estamos frente a una demo académica aislada, sino ante una línea de trabajo que conecta ciencia de datos, archivos públicos, memoria institucional y responsabilidad democrática.</p>

<h2>IA para humanidades y ciencias sociales, no solo para industria</h2>

<p>Otro punto relevante del paper es que empuja una conversación poco habitual en el ecosistema local. La IA suele presentarse en clave de productividad, salud, retail, minería o educación. Este trabajo muestra otra posibilidad: que la inteligencia artificial también puede convertirse en una <strong>infraestructura de investigación para humanidades y ciencias sociales</strong>, siempre que se diseñe con suficiente rigor metodológico y ético.</p>

<p>El propio paper subraya que los resultados obtenidos permiten reconocer una parte importante de las entidades del grafo patrón construido manualmente. Y cuando el sistema falla, muchas veces no lo hace por no detectar una entidad crucial, sino por diferencias en el nivel de granularidad de la representación. Esa observación es importante porque muestra que el desafío ya no es solo “si el modelo encuentra algo”, sino cómo representar el conocimiento de manera útil y consistente para investigación real.</p>

<h2>Lo que este caso dice sobre la IA chilena</h2>

<p>En un país donde todavía discutimos mucho la adopción y menos la orientación estratégica, este proyecto entrega una señal interesante. La IA chilena puede ser relevante cuando se conecta con problemas donde el contexto local importa de verdad. Y pocos contextos importan tanto como la memoria histórica, los archivos de violaciones a los derechos humanos y la capacidad de reconstruir verdad a partir de evidencia dispersa.</p>

<p>Este no es un caso de IA chilena compitiendo por el benchmark más espectacular. Es un caso de IA aplicada a un problema donde la tecnología tiene que convivir con historia, ética, instituciones y cuidado metodológico. Y justamente por eso tiene tanto valor.</p>

<h2>La lectura de fondo</h2>

<p>Tal vez la idea más interesante aquí no sea que los LLMs sirvan para construir grafos. La idea más importante es otra: que la IA empieza a mostrar su mejor versión cuando deja de buscar brillo superficial y entra en tareas donde ayudar a organizar conocimiento puede cambiar la capacidad de una sociedad para entenderse a sí misma.</p>

<p>En ese sentido, este trabajo tiene algo poco común. No usa Chile solo como escenario. Usa a Chile como problema real, con memoria real, instituciones reales y archivos que todavía siguen diciendo cosas que no terminamos de escuchar. Si la IA puede contribuir ahí, entonces su valor público se vuelve mucho más concreto que cualquier promesa abstracta de automatización.</p>

<p><strong>Fuentes principales:</strong> Camila Díaz et al., <em>Automatic knowledge-graph creation from historical documents: The Chilean dictatorship as a case study</em>, arXiv, 21 de agosto de 2024. Disponible en <a href="https://arxiv.org/abs/2408.11975" target="_blank" rel="noopener noreferrer">arxiv.org/abs/2408.11975</a>. Proyecto relacionado: <a href="https://imfd.cl/proyecto-nuestra-memoria-rescatara-informacion-de-archivos-de-dd-hh-usando-inteligencia-artificial-2/" target="_blank" rel="noopener noreferrer">IMFD / Nuestra MemorIA</a>.</p>
HTML,
                'research_type' => 'study',
                'type' => 'Chile AI Research',
                'author' => $author->name,
                'status' => 'published',
                'is_published' => true,
                'category_id' => $categoryId,
                'user_id' => $author->id,
                'featured' => false,
                'views' => 0,
                'comments_count' => 0,
                'citations' => 0,
                'institution' => 'Pontificia Universidad Católica de Chile / IMFD / Universidad Alberto Hurtado / arXiv',
                'references' => 'https://arxiv.org/abs/2408.11975' . PHP_EOL . 'https://imfd.cl/proyecto-nuestra-memoria-rescatara-informacion-de-archivos-de-dd-hh-usando-inteligencia-artificial-2/' . PHP_EOL . 'https://www.uc.cl/noticias/inteligencia-artificial-realidad-extendida-y-aprendizaje-transformador-entre-ganadores-de-exploracion-2025/',
                'additional_authors' => 'Camila Díaz, Jocelyn Dunstan, Lorena Etcheverry, Antonia Fonck, Alejandro Grez, Domingo Mery, Juan Reutter, Hugo Rojas',
                'editorial_tags' => ['IA', 'Chile', 'Memoria', 'Derechos Humanos', 'Universidad Católica', 'Investigación'],
            ],
            [
                'title' => 'IA contra uno de los cánceres más letales de Chile: la USM busca apoyar decisiones clínicas sobre cáncer biliar',
                'slug' => 'ia-cancer-biliar-chile-usm-decisiones-clinicas',
                'summary' => 'La Universidad Técnica Federico Santa María desarrolla una investigación para apoyar diagnóstico, tratamiento y pronóstico del cáncer de vesícula biliar usando inteligencia artificial adaptada a la realidad chilena. El proyecto pone el foco en un problema donde Chile tiene una incidencia especialmente alta y donde faltan datos clínicos estandarizados.',
                'excerpt' => 'La Universidad Técnica Federico Santa María desarrolla una investigación para apoyar diagnóstico, tratamiento y pronóstico del cáncer de vesícula biliar usando inteligencia artificial adaptada a la realidad chilena. El proyecto pone el foco en un problema donde Chile tiene una incidencia especialmente alta y donde faltan datos clínicos estandarizados.',
                'abstract' => 'La Universidad Técnica Federico Santa María desarrolla una investigación para apoyar diagnóstico, tratamiento y pronóstico del cáncer de vesícula biliar usando inteligencia artificial adaptada a la realidad chilena. El proyecto pone el foco en un problema donde Chile tiene una incidencia especialmente alta y donde faltan datos clínicos estandarizados.',
                'content' => <<<'HTML'
<p>La inteligencia artificial aplicada a salud suele presentarse como una promesa global, casi intercambiable entre contextos. Pero cuando aterriza en enfermedades concretas y sistemas de salud reales, el desafío cambia por completo. En Chile, uno de los casos más interesantes hoy no está en un benchmark internacional ni en una gran plataforma hospitalaria extranjera, sino en una investigación liderada por la <strong>Universidad Técnica Federico Santa María</strong> para apoyar decisiones clínicas sobre <strong>cáncer de vesícula biliar</strong>, una enfermedad especialmente crítica para el país.</p>

<p>Según informó la USM a fines de 2025, el proyecto busca desarrollar un <strong>sistema de recomendación asistido por inteligencia artificial</strong> que ayude a profesionales de la salud en diagnóstico, tratamiento y pronóstico. La iniciativa fue seleccionada en el concurso <strong>FONIS 2025</strong> de la ANID, lo que ya entrega una señal de relevancia pública: no se trata solo de una exploración académica, sino de una línea de investigación orientada a fortalecer decisiones clínicas en un problema de alto impacto sanitario.</p>

<h2>Por qué este caso importa especialmente en Chile</h2>

<p>El cáncer de vesícula biliar no es una enfermedad cualquiera en el contexto chileno. La propia nota de la USM recuerda que Chile presenta una de las incidencias más altas del mundo en este tipo de cáncer, con un impacto especialmente severo en mortalidad. Esa condición vuelve mucho más significativo el proyecto: no estamos hablando de aplicar IA a un problema abstracto, sino a una patología donde el país tiene una necesidad concreta y una carga epidemiológica singular.</p>

<p>En otras palabras, esta investigación tiene algo que muchas iniciativas de IA en salud todavía no logran: una conexión directa entre capacidad técnica y realidad local. Eso la vuelve editorialmente muy valiosa, porque permite hablar de IA chilena no como adopción tardía de herramientas externas, sino como intento serio de construir modelos ajustados a condiciones nacionales.</p>

<h2>Qué propone la investigación</h2>

<p>La idea central del proyecto es crear una plataforma que permita ingresar parámetros clínicos y devolver información predictiva y recomendaciones que complementen el análisis profesional. Según explicó el académico Werner Kristjanpoller, la iniciativa se organiza en dos grandes ejes. El primero se enfoca en <strong>identificación de riesgo</strong>: modelos que estimen la probabilidad de desarrollar la enfermedad. El segundo se activa una vez confirmado el diagnóstico y busca entregar apoyo para orientar alternativas de tratamiento, intervenciones quirúrgicas y pronósticos de sobrevivencia.</p>

<p>La arquitectura combina <strong>machine learning</strong> y <strong>transfer learning</strong>. Este último punto es especialmente importante porque toca una dificultad muy concreta del ecosistema local: la escasez de datos nacionales robustos y estandarizados. En vez de esperar a contar con datasets perfectos, el proyecto plantea arrancar con bases más generales y luego ajustar los modelos incorporando variables específicas del contexto chileno.</p>

<h2>El verdadero problema no es solo el modelo, sino los datos</h2>

<p>Aquí aparece uno de los aspectos más interesantes del caso. La IA en salud muchas veces se vende como un problema de capacidad algorítmica. Pero esta investigación muestra que, en Chile, el cuello de botella puede estar antes: en la disponibilidad y calidad de los datos clínicos. La propia USM reconoce que existe una limitada disponibilidad de información confiable y estandarizada sobre esta enfermedad.</p>

<p>Esa observación cambia el diagnóstico. El valor de la IA no depende solo de usar un modelo sofisticado, sino de construir condiciones para que ese modelo aprenda algo clínicamente útil. Y eso obliga a pensar interoperabilidad, recolección, validación, sesgos y adaptación institucional. En otras palabras: la investigación no trata solo de “hacer IA”, sino de qué significa hacer IA médica en un sistema donde los datos todavía están fragmentados.</p>

<h2>Transfer learning como apuesta realista</h2>

<p>En ese contexto, el uso de transfer learning funciona como una decisión metodológica bastante sensata. Permite aprovechar patrones aprendidos en otras cohortes y luego personalizarlos con información local a medida que el proyecto avance. No es una solución mágica, pero sí una forma pragmática de empezar a construir capacidad clínica asistida por IA sin depender de un volumen inicial de datos que hoy simplemente no existe en la escala ideal.</p>

<p>Ese enfoque tiene además una virtud editorial importante: transmite una visión menos espectacular y más madura de la IA. En vez de prometer automatización total o precisión milagrosa desde el primer día, el proyecto asume que la utilidad real se construye paso a paso, integrando evidencia internacional con ajuste nacional.</p>

<h2>Una investigación que dice mucho sobre la IA chilena</h2>

<p>Este caso también permite leer algo más amplio sobre el desarrollo de la IA en Chile. Los proyectos más interesantes probablemente no serán los que imiten el discurso global de moda, sino los que enfrenten problemas donde el contexto local importa de verdad. Salud pública, enfermedades prevalentes, escasez de datos clínicos, decisiones médicas bajo incertidumbre: ahí la IA deja de ser demostración y pasa a convertirse en infraestructura potencial de apoyo.</p>

<p>Eso sí, hay que leer el proyecto con cuidado. No estamos frente a una herramienta ya desplegada en producción masiva, sino ante una línea de investigación aplicada que todavía debe validar desempeño, confiabilidad y utilidad clínica. Y eso es una buena noticia. Significa que el desarrollo se está pensando con un grado de prudencia que en salud no es opcional.</p>

<h2>La lectura de fondo</h2>

<p>Quizás el valor más interesante de esta iniciativa no esté solo en el uso de IA, sino en el tipo de conversación que obliga a tener. ¿Qué significa construir medicina asistida por datos en Chile? ¿Cómo se pasa de modelos entrenados en otras realidades a herramientas realmente útiles para población chilena? ¿Cuánto del problema es algorítmico y cuánto es institucional?</p>

<p>Esas preguntas son más importantes que el hype. Y justamente por eso esta investigación merece atención. Porque muestra que la IA chilena puede ser más valiosa cuando deja de perseguir titulares globales y empieza a resolver, con realismo metodológico, problemas donde el país necesita respuestas propias.</p>

<p><strong>Fuente principal:</strong> Universidad Técnica Federico Santa María, <em>Investigación utiliza inteligencia artificial para apoyar decisiones clínicas sobre el cáncer biliar</em>, 30 de diciembre de 2025. Disponible en <a href="https://usm.cl/noticias/investigacion-utiliza-inteligencia-artificial-para-apoyar-decisiones-clinicas-sobre-el-cancer-biliar/" target="_blank" rel="noopener noreferrer">usm.cl</a>.</p>
HTML,
                'research_type' => 'study',
                'type' => 'Chile AI Health Research',
                'author' => $author->name,
                'status' => 'published',
                'is_published' => true,
                'category_id' => $categoryId,
                'user_id' => $author->id,
                'featured' => false,
                'views' => 0,
                'comments_count' => 0,
                'citations' => 0,
                'institution' => 'Universidad Técnica Federico Santa María / ANID-FONIS',
                'references' => 'https://usm.cl/noticias/investigacion-utiliza-inteligencia-artificial-para-apoyar-decisiones-clinicas-sobre-el-cancer-biliar/',
                'additional_authors' => 'Werner Kristjanpoller, Eduardo Vega',
                'editorial_tags' => ['IA', 'Chile', 'Salud', 'USM', 'Cáncer', 'Investigación'],
            ],
            [
                'title' => 'Chile quiere jugar en la primera línea de la astronomía con IA: UdeC apuesta por procesamiento en tiempo real para la era Rubin',
                'slug' => 'chile-astronomia-ia-udec-procesamiento-tiempo-real-era-rubin',
                'summary' => 'Un proyecto adjudicado por la Universidad de Concepción busca escalar el broker astronómico chileno ALeRCE para integrar y clasificar alertas en tiempo real provenientes de observatorios clave. Es una de las apuestas más sólidas para entender cómo Chile puede usar inteligencia artificial no solo para observar el cielo, sino para construir infraestructura científica de clase mundial.',
                'excerpt' => 'Un proyecto adjudicado por la Universidad de Concepción busca escalar el broker astronómico chileno ALeRCE para integrar y clasificar alertas en tiempo real provenientes de observatorios clave. Es una de las apuestas más sólidas para entender cómo Chile puede usar inteligencia artificial no solo para observar el cielo, sino para construir infraestructura científica de clase mundial.',
                'abstract' => 'Un proyecto adjudicado por la Universidad de Concepción busca escalar el broker astronómico chileno ALeRCE para integrar y clasificar alertas en tiempo real provenientes de observatorios clave. Es una de las apuestas más sólidas para entender cómo Chile puede usar inteligencia artificial no solo para observar el cielo, sino para construir infraestructura científica de clase mundial.',
                'content' => <<<'HTML'
<p>Si uno quisiera encontrar un lugar donde la inteligencia artificial y Chile tienen una oportunidad especialmente seria de encontrarse con ventaja comparativa real, la astronomía aparecería muy arriba en la lista. No solo porque el país concentra algunos de los cielos más relevantes del planeta, sino porque la próxima generación de observatorios producirá tal volumen de datos que mirar ya no bastará: habrá que <strong>clasificar, priorizar e interpretar en tiempo real</strong>. En ese contexto, el proyecto impulsado desde la <strong>Universidad de Concepción</strong> para escalar el broker astronómico chileno <strong>ALeRCE</strong> merece mucha más atención de la que suele recibir fuera del circuito científico.</p>

<p>La noticia fue reportada por UdeC tras la adjudicación de cinco proyectos en el <strong>Fondo ALMA-ANID 2025</strong>. Entre ellos destaca <strong>Scaling ALeRCE into a Multisurvey Broker for the Rubin Era</strong>, dirigido por Guillermo Cabrera Vives. Su objetivo es consolidar y escalar una infraestructura capaz de <strong>integrar, procesar y clasificar en tiempo real alertas astronómicas</strong> provenientes del Observatorio Vera C. Rubin, el survey LS4 y el Telescopio Espacial Roman.</p>

<h2>Por qué esto no es solo “IA para astronomía”</h2>

<p>La manera más fácil de leer este proyecto sería decir que es un nuevo caso de IA aplicada a ciencia. Pero se queda corto. Lo que está en juego aquí no es solo usar machine learning para analizar imágenes del cielo, sino construir una <strong>infraestructura científica basada en inteligencia artificial</strong>. Esa diferencia importa mucho. Cuando los observatorios empiezan a emitir flujos inmensos de alertas y eventos, el problema deja de ser únicamente la observación. El problema pasa a ser qué procesar primero, qué evento puede ser científicamente relevante, cómo descartar ruido y cómo reaccionar antes de que una señal efímera desaparezca.</p>

<p>En otras palabras, ALeRCE no compite en el terreno de una demo llamativa. Compite en el terreno más serio de todos: el de convertirse en una pieza de infraestructura sin la cual el volumen de información astronómica futura sería simplemente inmanejable.</p>

<h2>La era Rubin cambia la escala del problema</h2>

<p>El proyecto se inscribe explícitamente en la llegada de la llamada <strong>era Rubin</strong>, en referencia al Observatorio Vera C. Rubin, uno de los sistemas que más va a tensionar la capacidad de procesamiento en astronomía observacional. El cambio de escala aquí es decisivo. Los próximos surveys no van a producir solo más datos: van a producir más <strong>alertas</strong>, más eventos variables, más señales transitorias y más necesidad de clasificación inmediata.</p>

<p>Ahí es donde la inteligencia artificial se vuelve menos una herramienta opcional y más una condición de posibilidad. Sin sistemas capaces de filtrar, priorizar y clasificar automáticamente, gran parte del valor científico potencial podría perderse por pura saturación operativa.</p>

<h2>Chile como observatorio y como infraestructura</h2>

<p>Este punto es especialmente importante para leer el proyecto en clave país. Durante años, Chile fue visto sobre todo como plataforma de observación: un territorio excepcional para instalar telescopios y producir datos. La apuesta de ALeRCE sugiere algo más ambicioso: que Chile también puede convertirse en un actor fuerte en la capa de <strong>infraestructura computacional e inteligencia artificial</strong> que permitirá que esos datos se conviertan en descubrimiento.</p>

<p>La formulación que aparece en la nota de UdeC va justamente en esa línea. Cabrera Vives plantea que este avance permitirá consolidar en Chile, y en particular en la Universidad de Concepción, una línea de investigación de frontera basada en IA para astronomía de dominio temporal, posicionando al país como uno de los polos internacionales en desarrollo de brokers astronómicos.</p>

<h2>Una investigación que mezcla ciencia básica e ingeniería de sistemas</h2>

<p>Eso vuelve al proyecto doblemente interesante. No estamos frente a una investigación puramente teórica ni a una aplicación meramente instrumental. Es una zona híbrida donde convergen ciencia básica, ingeniería de datos, aprendizaje automático, procesamiento en tiempo real y arquitectura de sistemas. Ese tipo de convergencia es exactamente donde suele aparecer la infraestructura científica más valiosa: la que no solo responde preguntas, sino que habilita muchas preguntas futuras.</p>

<p>Además, el hecho de que esta línea reciba apoyo a través de ALMA-ANID muestra algo importante sobre el ecosistema chileno. La IA en astronomía no está apareciendo como moda importada de última hora, sino como prolongación natural de una ventaja histórica del país en observación, instrumentación y colaboración científica internacional.</p>

<h2>Qué dice esto sobre la IA chilena</h2>

<p>Editorialmente, esta es una historia mucho más potente de lo que parece. En vez de repetir que Chile “usa IA”, este caso permite decir algo más serio: Chile puede participar en la construcción de las plataformas que harán posible ciencia de frontera en la próxima década. Y eso es distinto a adoptar herramientas ajenas. Significa diseñar sistemas, sostener infraestructura y convertirse en nodo relevante dentro de una red global de descubrimiento.</p>

<p>También obliga a corregir un sesgo habitual de la conversación tecnológica local. La IA no tiene por qué medirse solo en chatbots, productividad corporativa o automatización administrativa. Puede medirse también en la capacidad de procesar el universo en tiempo real. Puede medirse en si un país es capaz de construir sistemas que hagan legible un volumen de datos que, sin ellos, sería demasiado grande para la ciencia humana tradicional.</p>

<h2>La lectura de fondo</h2>

<p>Hay algo particularmente simbólico en esta historia. Chile lleva décadas siendo territorio clave para observar el cielo. Lo que proyectos como ALeRCE sugieren es que el próximo salto no estará solo en mirar mejor, sino en <strong>entender más rápido</strong>. Y ahí la inteligencia artificial deja de ser una capa adicional para convertirse en el mecanismo que organiza la atención científica.</p>

<p>Si esta línea prospera, el país no solo seguirá siendo una ventana privilegiada al universo. También podría transformarse en uno de los lugares donde se define cómo convertir ese universo observable en conocimiento accionable. Y para la IA chilena, eso sería una señal poderosa: que su lugar más prometedor no está únicamente en seguir tendencias globales, sino en potenciar con tecnología propia uno de los activos científicos más extraordinarios del país.</p>

<p><strong>Fuente principal:</strong> Universidad de Concepción, <em>Cinco proyectos UdeC se adjudican Fondo ALMA-ANID 2025 para fortalecer la astronomía chilena</em>, 27 de enero de 2026. Disponible en <a href="https://noticias.udec.cl/cinco-proyectos-udec-se-adjudican-fondo-alma-anid-2025-para-fortalecer-la-astronomia-chilena/" target="_blank" rel="noopener noreferrer">noticias.udec.cl</a>.</p>
HTML,
                'research_type' => 'analysis',
                'type' => 'Chile AI Astronomy Research',
                'author' => $author->name,
                'status' => 'published',
                'is_published' => true,
                'category_id' => $categoryId,
                'user_id' => $author->id,
                'featured' => true,
                'views' => 0,
                'comments_count' => 0,
                'citations' => 0,
                'institution' => 'Universidad de Concepción / ALMA-ANID / ALeRCE',
                'references' => 'https://noticias.udec.cl/cinco-proyectos-udec-se-adjudican-fondo-alma-anid-2025-para-fortalecer-la-astronomia-chilena/',
                'additional_authors' => 'Guillermo Cabrera Vives',
                'editorial_tags' => ['IA', 'Chile', 'Astronomía', 'UdeC', 'ALeRCE', 'Investigación'],
            ],
        ];
    }

    private function resolveAuthor(): ?User
    {
        $userId = $this->option('user-id');

        if ($userId) {
            return User::find($userId);
        }

        return User::query()
            ->with('role')
            ->get()
            ->first(fn (User $user) => $user->isAdmin()) ?? User::query()->first();
    }
}
