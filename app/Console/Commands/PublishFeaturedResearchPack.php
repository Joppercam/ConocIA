<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Research;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublishFeaturedResearchPack extends Command
{
    protected $signature = 'content:publish-featured-research-pack
                            {--user-id= : ID del usuario autor}
                            {--force : Sobrescribe el contenido si ya existe}';

    protected $description = 'Publica o actualiza un pack editorial de investigaciones destacadas sobre IA';

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
                'title' => 'La próxima revolución científica podría no venir de un modelo, sino de agentes supervisados por humanos',
                'slug' => 'proxima-revolucion-cientifica-agentes-supervisados-humanos',
                'summary' => 'Un paper reciente propone que la ciencia está entrando en una nueva fase: investigadores humanos guiando sistemas multiagente capaces de interpretar intención científica, coordinar flujos analíticos y escalar descubrimiento en disciplinas desbordadas por datos.',
                'excerpt' => 'Un paper reciente propone que la ciencia está entrando en una nueva fase: investigadores humanos guiando sistemas multiagente capaces de interpretar intención científica, coordinar flujos analíticos y escalar descubrimiento en disciplinas desbordadas por datos.',
                'abstract' => 'Un paper reciente propone que la ciencia está entrando en una nueva fase: investigadores humanos guiando sistemas multiagente capaces de interpretar intención científica, coordinar flujos analíticos y escalar descubrimiento en disciplinas desbordadas por datos.',
                'content' => <<<'HTML'
<p>Durante años, la conversación sobre inteligencia artificial en ciencia estuvo dominada por una idea bastante concreta: usar modelos para acelerar tareas específicas. Clasificar imágenes médicas, predecir estructuras de proteínas, ordenar literatura, optimizar simulaciones. Pero un nuevo trabajo publicado en arXiv sugiere que esa etapa puede ser solo el principio. El paper <strong>AI Agents, Language, Deep Learning and the Next Revolution in Science</strong> propone algo más ambicioso: que la próxima evolución del método científico podría apoyarse en <strong>agentes de IA supervisados por humanos</strong>, capaces de interpretar intención científica, diseñar flujos de trabajo analíticos y escalar descubrimiento en disciplinas donde la complejidad ya superó la capacidad humana tradicional.</p>

<p>La tesis central del artículo parte de un diagnóstico difícil de discutir: la ciencia contemporánea produce más datos de los que puede entender. Desde la física de partículas hasta la astronomía, la genómica y el modelado climático, los instrumentos generan señales, relaciones e interdependencias a una velocidad que ya no encaja bien con las formas clásicas de análisis. El cuello de botella ya no está solo en medir o recolectar, sino en traducir esa avalancha de información en hipótesis, flujos reproducibles y conocimiento verificable.</p>

<h2>De herramientas aisladas a sistemas que coordinan investigación</h2>

<p>La novedad del paper no está en afirmar que la IA puede ayudar a la ciencia. Eso ya lo sabemos. Lo importante es que los autores plantean una transición desde herramientas puntuales hacia <strong>sistemas multiagente con supervisión humana</strong>. En este modelo, la IA no aparece solo como asistente para una subtarea, sino como una capa capaz de recibir objetivos científicos, descomponerlos, coordinar análisis y documentar cada paso con suficiente trazabilidad como para mantener responsabilidad humana.</p>

<p>Eso cambia mucho el marco. Una cosa es usar un modelo para resumir papers. Otra muy distinta es usar una arquitectura de agentes que pueda interpretar una intención experimental, elegir procedimientos, ejecutar pasos intermedios y dejar un rastro explícito de decisiones. El paper insiste en que esto no equivale a “sacar al científico del circuito”. Al contrario: su argumento es que la supervisión humana se vuelve todavía más importante, precisamente porque la escala del trabajo empieza a desbordar lo que una sola persona o incluso un equipo pequeño puede seguir manualmente.</p>

<h2>El lenguaje como interfaz del nuevo método científico</h2>

<p>Un punto especialmente interesante del artículo es su énfasis en el lenguaje. Los autores no ven a los agentes científicos solo como motores de automatización, sino como sistemas que convierten intención humana en procedimientos técnicos ejecutables. Ahí el lenguaje natural, combinado con lenguajes específicos de dominio, actúa como puente entre científicos y máquinas.</p>

<p>La idea es potente porque resuelve un problema práctico: gran parte del conocimiento científico no está completamente formalizado en código. Está distribuido entre papers, convenciones, experiencia tácita, documentación y práctica experimental. Un sistema multiagente apoyado en lenguaje puede funcionar como capa de traducción entre esa experiencia humana y flujos analíticos reproducibles.</p>

<p>El paper subraya además la necesidad de <strong>trazabilidad</strong>. No se trata de darle a la IA más autonomía a ciegas, sino de asegurarse de que sus pasos queden expresados en estructuras que preserven supervisión, auditoría y responsabilidad. Esa combinación entre automatización y trazabilidad es probablemente la parte más seria del argumento, porque evita caer en la fantasía simplista de la “ciencia autónoma” sin control.</p>

<h2>Por qué la física de partículas aparece como laboratorio ideal</h2>

<p>El caso que organiza el paper es la física de partículas. No es casual. Históricamente, esta disciplina fue incubadora de varias transiciones computacionales profundas: grandes infraestructuras de datos, trabajo colaborativo masivo, simulación a escala y herramientas analíticas altamente especializadas. Ahora vuelve a presentarse como terreno ideal para probar una nueva transición: la de los agentes científicos.</p>

<p>Los autores presentan el sistema <strong>Dr. Sai</strong>, desplegado en investigación de colisionadores en el <strong>CEPC</strong>, como ejemplo de esta visión. No lo describen como un sustituto del científico, sino como un framework de razonamiento multiagente que amplía alcance cognitivo en un entorno donde la complejidad experimental y analítica crece más rápido que los métodos convencionales. El mensaje de fondo es claro: cuando una disciplina opera al límite de la complejidad humana manejable, el valor de la IA ya no está solo en acelerar una tarea, sino en ayudar a coordinar sistemas completos de investigación.</p>

<h2>Lo importante no es la automatización, sino el cambio de escala</h2>

<p>La lectura más potente de este paper es que la IA para ciencia no debería entenderse únicamente como eficiencia incremental. Los autores argumentan que estamos frente a una posible reconfiguración del propio proceso de descubrimiento. Si los agentes pueden absorber parte del trabajo de organización, navegación analítica y ejecución reproducible, entonces los científicos podrían reasignar más tiempo a formular preguntas, controlar supuestos, evaluar resultados y tomar decisiones conceptuales de mayor nivel.</p>

<p>Eso no elimina riesgos. Los desplaza. Un sistema así depende de buenos mecanismos de supervisión, control de errores, evaluación de evidencia y diseño institucional. También obliga a pensar problemas delicados: cómo verificar resultados generados por cadenas multiagente, cómo repartir responsabilidad, cómo evitar sesgos silenciosos en workflows complejos y cómo impedir que una automatización opaca termine erosionando la confiabilidad científica que supuestamente busca ampliar.</p>

<h2>La promesa: descubrir a la velocidad de la complejidad</h2>

<p>El paper es, en buena medida, una pieza de perspectiva. No entrega un benchmark masivo ni una prueba definitiva de que esta transformación ya ocurrió. Lo que hace es articular una tesis estratégica: la ciencia está llegando a un punto en el que sus instrumentos y datos crecen más rápido que su capacidad de comprensión. En ese contexto, los agentes de IA podrían convertirse en la infraestructura que permita que el conocimiento siga escalando sin colapsar bajo su propio peso.</p>

<p>Hay algo importante en esa idea. Durante mucho tiempo se habló del método científico como una secuencia relativamente estable: observación, hipótesis, experimento, análisis, validación. Este paper sugiere que la era de sistemas complejos podría exigir una nueva capa intermedia: agentes capaces de organizar y ejecutar parte de ese recorrido sin romper la primacía humana sobre la interpretación y la responsabilidad.</p>

<p>Si esa visión prospera, el próximo gran salto de la IA en ciencia no vendrá solo de un modelo más fuerte o una arquitectura más grande. Podría venir de algo menos espectacular para el marketing, pero mucho más transformador para la investigación real: <strong>sistemas que permitan descubrir a la velocidad de la complejidad</strong>.</p>

<p><strong>Fuente principal:</strong> Ke Li et al., <em>AI Agents, Language, Deep Learning and the Next Revolution in Science</em>, arXiv, 9 de marzo de 2026. Publicado también en <em>Frontiers of Physics</em>. Disponible en <a href="https://arxiv.org/abs/2603.07940" target="_blank" rel="noopener noreferrer">arxiv.org/abs/2603.07940</a>.</p>
HTML,
                'research_type' => 'analysis',
                'type' => 'Research Perspective',
                'author' => $author->name,
                'status' => 'published',
                'is_published' => true,
                'category_id' => $categoryId,
                'user_id' => $author->id,
                'featured' => true,
                'views' => 0,
                'comments_count' => 0,
                'citations' => 0,
                'institution' => 'arXiv / Frontiers of Physics / Institute of High Energy Physics, CAS',
                'references' => 'https://arxiv.org/abs/2603.07940',
                'additional_authors' => 'Ke Li, Beijiang Liu, Bruce Mellado, Changzheng Yuan, Zhengde Zhang',
                'editorial_tags' => ['IA', 'Agentes', 'Ciencia', 'AI for Science', 'Investigación', 'Física'],
            ],
            [
                'title' => 'La matemática sigue siendo una frontera dura para la IA: MathNet expone los límites reales del razonamiento y el retrieval',
                'slug' => 'mathnet-limites-reales-razonamiento-retrieval-ia',
                'summary' => 'MathNet reúne más de 30 mil problemas de olimpíada de 47 países y 17 idiomas para medir algo más exigente que un benchmark habitual: no solo si los modelos resuelven matemáticas difíciles, sino si son capaces de recuperar problemas equivalentes y mejorar con contexto útil.',
                'excerpt' => 'MathNet reúne más de 30 mil problemas de olimpíada de 47 países y 17 idiomas para medir algo más exigente que un benchmark habitual: no solo si los modelos resuelven matemáticas difíciles, sino si son capaces de recuperar problemas equivalentes y mejorar con contexto útil.',
                'abstract' => 'MathNet reúne más de 30 mil problemas de olimpíada de 47 países y 17 idiomas para medir algo más exigente que un benchmark habitual: no solo si los modelos resuelven matemáticas difíciles, sino si son capaces de recuperar problemas equivalentes y mejorar con contexto útil.',
                'content' => <<<'HTML'
<p>La discusión sobre razonamiento en inteligencia artificial suele quedar atrapada entre dos exageraciones. Por un lado, está la idea de que los modelos actuales ya “razonan” casi como un humano entrenado. Por otro, la postura de que todo lo que hacen es una especie de imitación estadística sin profundidad real. <strong>MathNet</strong>, un nuevo benchmark presentado en arXiv y aceptado en ICLR 2026, es valioso precisamente porque obliga a abandonar ese falso binario y mirar un terreno más exigente: la matemática de nivel olimpíada, en múltiples idiomas, con problemas complejos, soluciones largas y tareas de retrieval que no se resuelven con simple coincidencia semántica.</p>

<p>El dataset es impresionante por escala y diseño. Reúne <strong>30.676 problemas</strong> de competencia con sus soluciones, cubriendo <strong>47 países</strong>, <strong>17 idiomas</strong> y décadas de material oficial. Pero lo más importante no es solo el tamaño. MathNet fue pensado para medir tres habilidades conectadas pero distintas: si un modelo puede <strong>resolver</strong> problemas exigentes, si puede <strong>recuperar</strong> problemas matemáticamente equivalentes o estructuralmente similares, y si puede <strong>mejorar</strong> su resolución cuando recibe contexto recuperado de calidad.</p>

<h2>No es solo otro benchmark de matemáticas</h2>

<p>Ese diseño importa mucho. Muchos benchmarks matemáticos evalúan si un modelo acierta una respuesta. MathNet agrega una capa más realista y más difícil: la de encontrar problemas comparables dentro de un corpus grande. Eso acerca la evaluación a un tipo de trabajo que sí existe en práctica matemática y educativa: reconocer estructuras, identificar analogías útiles y usar precedentes correctos para razonar mejor.</p>

<p>En ese sentido, el benchmark no mide solo “capacidad de contestar”. Mide algo más profundo: si los sistemas entienden la <strong>estructura matemática</strong> suficiente como para vincular problemas equivalentes, no solo similares en superficie. Esa distinción es clave, porque muchas veces el desafío en matemáticas no es recordar una respuesta sino reconocer qué clase de problema tienes delante.</p>

<h2>Los mejores modelos siguen siendo desafiados</h2>

<p>Los resultados dejan dos mensajes simultáneos. El primero es que los mejores modelos han mejorado mucho. MathNet reporta <strong>78,4%</strong> para Gemini-3.1-Pro y <strong>69,3%</strong> para GPT-5 en la tarea de resolución, cifras que serían impensables hace no demasiado tiempo en un benchmark de este nivel. El segundo mensaje, igual de importante, es que esos números no significan que el problema esté resuelto.</p>

<p>El paper y el sitio oficial del proyecto muestran que el retrieval sigue siendo una gran debilidad. En los experimentos reportados, <strong>Recall@1 se mantiene por debajo del 5%</strong> para todos los modelos evaluados en la recuperación de problemas equivalentes. Dicho de otro modo: los sistemas pueden acertar bastante al resolver, pero todavía fallan mucho al encontrar el antecedente matemático correcto dentro de un corpus amplio.</p>

<p>Esa brecha es importante porque desmonta una idea cómoda: que si un modelo resuelve bastante bien, entonces también “entiende” bien qué recuperar. MathNet sugiere lo contrario. Resolver y recuperar no son la misma capacidad. Y cuando el retrieval es malo, el rendimiento de la resolución aumentada también se resiente.</p>

<h2>El verdadero cuello de botella: recuperar bien</h2>

<p>Uno de los hallazgos más interesantes del benchmark es que el rendimiento con RAG depende muchísimo de la calidad del contexto recuperado. Cuando el retrieval es bueno, los resultados suben. Cuando es malo, el contexto puede aportar poco o incluso desviar. El paper destaca, por ejemplo, mejoras de hasta <strong>12%</strong> en ciertos escenarios de retrieval-augmented problem solving, con DeepSeek-V3.2-Speciale como mejor sistema en esa dimensión.</p>

<p>Esto convierte a MathNet en una pieza especialmente útil para leer el momento actual de la IA. El mercado suele concentrarse en la capacidad del modelo generativo: cuán bien redacta, cuán convincentemente explica, cuán a menudo acierta una solución. MathNet recuerda que, en tareas difíciles, la calidad del razonamiento está entrelazada con otra capa menos glamorosa pero decisiva: la del <strong>acceso a evidencia realmente útil</strong>.</p>

<h2>Un benchmark más global y más exigente</h2>

<p>También hay un valor metodológico en el tipo de datos elegidos. MathNet no se apoya solo en fuentes informales o crowdsourcing. Su material viene de competencias oficiales, incluyendo archivos físicos escaneados, luego procesados con OCR, normalización y verificación humana. El resultado es un benchmark más limpio, más internacional y más representativo de una matemática exigente que no está diseñada para la comodidad de los modelos.</p>

<p>El sitio del proyecto destaca además que las soluciones de MathNet son considerablemente más largas que las de otros benchmarks. Eso incrementa la dificultad tanto para generación como para evaluación. No es un detalle menor: las tareas largas castigan errores de consistencia, capacidad de seguimiento y control de pasos intermedios, justo donde todavía suelen aparecer muchas fragilidades.</p>

<h2>Qué dice MathNet sobre el razonamiento en IA</h2>

<p>La lectura de fondo es bastante sobria. Los modelos actuales ya no están en un punto rudimentario. Pueden resolver una fracción muy significativa de problemas avanzados. Pero eso no equivale a haber cerrado la cuestión del razonamiento matemático. De hecho, MathNet muestra que todavía hay mucho espacio entre “resolver bastante” y “comprender con suficiente profundidad como para recuperar, transferir y reutilizar estructura matemática de manera fiable”.</p>

<p>Eso es importante porque gran parte de la próxima ola de IA para ciencia, educación y asistencia técnica va a depender menos de benchmarks vistosos y más de este tipo de capacidades compuestas: recuperar bien, usar bien el contexto y razonar de forma estable con evidencia correcta. En ese sentido, MathNet no es solo una prueba sobre matemáticas. Es una prueba sobre el tipo de inteligencia que realmente vamos a necesitar de los sistemas avanzados.</p>

<p>La conclusión, entonces, no es que la IA “fracase” en matemáticas. Sería injusto decirlo. La conclusión más interesante es otra: incluso cuando los mejores modelos parecen muy fuertes, la parte más difícil del problema puede estar en un lugar menos visible. No necesariamente en generar una solución elegante, sino en encontrar el <strong>problema correcto al que esa solución debería parecerse</strong>.</p>

<p><strong>Fuentes principales:</strong> Shaden Alshammari et al., <em>MathNet: a Global Multimodal Benchmark for Mathematical Reasoning and Retrieval</em>, arXiv, 20 de abril de 2026; sitio oficial del proyecto <a href="https://mathnet.mit.edu/" target="_blank" rel="noopener noreferrer">mathnet.mit.edu</a>.</p>
HTML,
                'research_type' => 'study',
                'type' => 'Benchmark Analysis',
                'author' => $author->name,
                'status' => 'published',
                'is_published' => true,
                'category_id' => $categoryId,
                'user_id' => $author->id,
                'featured' => true,
                'views' => 0,
                'comments_count' => 0,
                'citations' => 0,
                'institution' => 'arXiv / ICLR 2026 / MathNet project',
                'references' => 'https://arxiv.org/abs/2604.18584' . PHP_EOL . 'https://mathnet.mit.edu/',
                'additional_authors' => 'Shaden Alshammari, Kevin Wen, Abrar Zainal, Mark Hamilton, Navid Safaei, Sultan Albarakati, William T. Freeman, Antonio Torralba',
                'editorial_tags' => ['IA', 'Matemáticas', 'Benchmark', 'Razonamiento', 'Retrieval', 'Investigación'],
            ],
            [
                'title' => 'La industria real todavía frena a los agentes: AEC-Bench muestra que el problema empieza antes del razonamiento',
                'slug' => 'aec-bench-industria-real-frena-agentes-ia',
                'summary' => 'AEC-Bench lleva a los agentes de IA a un terreno donde los errores cuestan tiempo y dinero: arquitectura, ingeniería y construcción. El resultado es incómodo para el hype actual: muchos sistemas fallan antes de razonar, porque ni siquiera recuperan bien el contexto visual y documental que necesitan.',
                'excerpt' => 'AEC-Bench lleva a los agentes de IA a un terreno donde los errores cuestan tiempo y dinero: arquitectura, ingeniería y construcción. El resultado es incómodo para el hype actual: muchos sistemas fallan antes de razonar, porque ni siquiera recuperan bien el contexto visual y documental que necesitan.',
                'abstract' => 'AEC-Bench lleva a los agentes de IA a un terreno donde los errores cuestan tiempo y dinero: arquitectura, ingeniería y construcción. El resultado es incómodo para el hype actual: muchos sistemas fallan antes de razonar, porque ni siquiera recuperan bien el contexto visual y documental que necesitan.',
                'content' => <<<'HTML'
<p>Hay una diferencia importante entre que un agente de IA funcione bien en demos, código o navegación controlada, y que funcione bien en una industria donde la información crítica vive en planos, especificaciones, submittals y documentos densos, visuales y cruzados. <strong>AEC-Bench</strong>, un nuevo benchmark para arquitectura, ingeniería y construcción, es valioso precisamente por eso: obliga a los agentes a salir del terreno cómodo y entrar en un entorno donde el error no solo baja una métrica, sino que puede traducirse en retrasos, inconsistencias y costos reales.</p>

<p>El benchmark fue presentado en arXiv a fines de marzo de 2026 y complementado con una publicación técnica de Nomic. Su idea base es simple, pero importante: medir agentes multimodales sobre tareas reales de coordinación en el mundo construido. En lugar de pedirles resumen o clasificación genérica, AEC-Bench los enfrenta a problemas como entender planos, verificar títulos de detalles, seguir referencias entre hojas, comparar índices contra title blocks, ubicar documentos correctos o detectar conflictos entre especificaciones y dibujos.</p>

<h2>Cuando un plano no se parece a un repositorio de código</h2>

<p>La crítica más interesante del benchmark no apunta tanto al tamaño de los modelos como al tipo de herramientas y hábitos con los que llegan a estas tareas. Según la publicación de Nomic, muchos agentes generalistas tratan documentos de construcción como si fueran archivos de texto o código: extraen texto, hacen keyword search y renderizan imágenes, pero pierden la estructura espacial que contiene buena parte del significado real.</p>

<p>Esa observación no es menor. Un plano no es una página de texto lineal. Está lleno de relaciones geométricas, anotaciones posicionadas, callouts, referencias cruzadas y convenciones visuales que colapsan cuando se los aplana. La nota técnica que acompaña AEC-Bench señala un dato especialmente revelador: <strong>77% de las trayectorias evaluadas usaron <code>pdftotext</code> como estrategia primaria de extracción</strong>. En agentes basados en Codex, el <strong>100%</strong> de las interacciones fue vía Bash. Es decir: buena parte del problema no es que los agentes “razonen mal” sobre el documento correcto, sino que intentan resolver un problema multimodal con un repertorio de herramientas pensado para otra clase de artefacto.</p>

<h2>AEC-Bench mide dificultad creciente y trabajo real</h2>

<p>Una de las fortalezas del benchmark es su estructura. Los autores reportan <strong>196 instancias</strong> repartidas en <strong>9 familias de tareas</strong> y <strong>3 niveles de alcance</strong>. El primer nivel, <em>Intra-Sheet</em>, cubre tareas que se resuelven dentro de una sola hoja, como verificar si un callout corresponde al elemento que referencia o si un título describe correctamente el detalle dibujado. El segundo, <em>Intra-Drawing</em>, exige navegar varias hojas del mismo set y rastrear relaciones entre ellas. El tercero, <em>Intra-Project</em>, ya trabaja a escala de proyecto y combina planos, especificaciones y submittals.</p>

<p>Ese diseño importa porque reproduce un gradiente muy cercano al trabajo real. No todo error ocurre dentro de una hoja. Muchas inconsistencias decisivas aparecen cuando hay que cruzar documentos distintos o mantener continuidad entre fuentes que fueron producidas en momentos, equipos o formatos diferentes.</p>

<h2>El hallazgo clave: el bottleneck es retrieval, no solo reasoning</h2>

<p>La conclusión más interesante de AEC-Bench es probablemente esta: <strong>el principal cuello de botella no es el razonamiento puro, sino la recuperación del contexto correcto</strong>. La publicación de Nomic lo dice con bastante claridad: los agentes fallan muchas veces antes de llegar al paso central del razonamiento, porque no logran localizar de forma fiable la hoja, el detalle o el documento relevante.</p>

<p>Eso es importante porque cambia el diagnóstico habitual. Cuando un agente se equivoca, solemos pensar que “razonó mal”. AEC-Bench muestra que en dominios industriales complejos el problema puede empezar antes: en cómo navega, qué extrae, cómo representa visualmente el documento y cómo decide qué parte vale la pena leer. Una vez que el contexto correcto aparece, el desempeño mejora de manera visible. Pero llegar a ese contexto sigue siendo la parte frágil.</p>

<p>La evidencia que presentan los autores va en esa dirección. Al agregar herramientas y representaciones más específicas del dominio, los resultados mejoran fuerte en tareas sensibles a recuperación. La publicación reporta mejoras promedio de <strong>+32,2 puntos</strong> en <em>detail-technical-review</em>, <strong>+20,8</strong> en <em>spec-drawing-sync</em> y <strong>+18,75</strong> en <em>drawing-navigation</em>, con algunos modelos alcanzando <strong>100%</strong> en este último tipo de tarea.</p>

<h2>La lección incómoda para el hype agente</h2>

<p>Hay una lección bastante incómoda en estos resultados. La narrativa dominante suele sugerir que, si un modelo es suficientemente fuerte, entonces podrá adaptarse a casi cualquier flujo profesional con un poco de prompting y tool use. AEC-Bench sugiere algo distinto: en ciertos dominios, la diferencia no la hace solo el modelo, sino el <strong>harness</strong>, las herramientas, la representación documental y el conocimiento operativo específico.</p>

<p>De hecho, el trabajo muestra que un agente diseñado para el dominio puede superar a configuraciones más generales de familias conocidas como Codex o Claude Code. Eso no invalida a los modelos fundacionales, pero sí pone límites claros a la idea de que bastan “más parámetros” para resolver industrias densas en documentos visuales y flujos coordinados.</p>

<h2>El built world como prueba dura de utilidad real</h2>

<p>La arquitectura, la ingeniería y la construcción tienen algo que las vuelve especialmente relevantes como test de agente útil. Son industrias donde la información no vive de manera limpia y lineal. Vive fragmentada, jerarquizada, visualmente codificada y distribuida entre múltiples artefactos. Si los agentes quieren ser realmente útiles fuera de chats y sandboxes, tarde o temprano tendrán que enfrentarse a este tipo de complejidad.</p>

<p>AEC-Bench por eso vale más que como benchmark sectorial. Funciona como recordatorio de una verdad más general: hay muchos dominios donde la inteligencia útil no depende solo de responder bien, sino de <strong>encontrar bien</strong>, <strong>representar bien</strong> y <strong>mantener contexto</strong> en artefactos multimodales difíciles.</p>

<p>La conclusión no es que la IA aplicada a industrias complejas sea una promesa vacía. La conclusión más útil es otra: estamos viendo con más claridad dónde están los verdaderos límites. Y esos límites no empiezan necesariamente en la deducción lógica de alto nivel. A veces empiezan en algo más básico, pero también más decisivo: saber cuál documento mirar, qué región leer y qué estructura visual no se puede destruir antes de pensar.</p>

<p><strong>Fuentes principales:</strong> Harsh Mankodiya et al., <em>AEC-Bench: A Multimodal Benchmark for Agentic Systems in Architecture, Engineering, and Construction</em>, arXiv, 31 de marzo de 2026; publicación técnica de Nomic <a href="https://www.nomic.ai/news/aec-bench-a-multimodal-benchmark-for-agentic-systems-in-architecture-engineering-and-construction" target="_blank" rel="noopener noreferrer">nomic.ai</a>.</p>
HTML,
                'research_type' => 'study',
                'type' => 'Industry Benchmark',
                'author' => $author->name,
                'status' => 'published',
                'is_published' => true,
                'category_id' => $categoryId,
                'user_id' => $author->id,
                'featured' => true,
                'views' => 0,
                'comments_count' => 0,
                'citations' => 0,
                'institution' => 'arXiv / Nomic / AEC-Bench',
                'references' => 'https://arxiv.org/abs/2603.29199' . PHP_EOL . 'https://www.nomic.ai/news/aec-bench-a-multimodal-benchmark-for-agentic-systems-in-architecture-engineering-and-construction',
                'additional_authors' => 'Harsh Mankodiya, Chase Gallik, Theodoros Galanos, Andriy Mulyar',
                'editorial_tags' => ['IA', 'Agentes', 'Industria', 'Arquitectura', 'Ingeniería', 'Benchmark'],
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
