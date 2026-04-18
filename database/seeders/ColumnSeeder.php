<?php

namespace Database\Seeders;

use App\Models\Column;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ColumnSeeder extends Seeder
{
    public function run(): void
    {
        // Resolver autores reales disponibles en esta BD
        // Los IDs locales (14,15,16,18) pueden no existir en producción
        $availableUsers = User::pluck('id')->toArray();
        if (empty($availableUsers)) {
            $this->command->error('No hay usuarios en la BD. Crea al menos uno antes de sembrar columnas.');
            return;
        }

        // Mapear los IDs "editoriales" a usuarios reales de forma circular
        // Si hay 4+ usuarios se asignan distintos; si hay menos, se reutilizan
        $authorMap = [];
        $editorialIds = [14, 15, 16, 18];
        foreach ($editorialIds as $i => $localId) {
            $authorMap[$localId] = $availableUsers[$i % count($availableUsers)];
        }

        $columns = [

            // ── 1 ──────────────────────────────────────────────────────────────────
            [
                'title'        => 'GPT-5 y el umbral que nadie sabe cómo cruzar',
                'excerpt'      => 'OpenAI lleva meses prometiendo el salto a GPT-5. Pero la pregunta que nadie responde en voz alta es si realmente sabemos lo que estamos construyendo.',
                'author_id'    => 15, // Pablo Fernández
                'category_id'  => 8,  // OpenAI
                'featured'     => true,
                'views'        => 1420,
                'published_at' => now()->subDays(2),
                'content'      => <<<HTML
<p>Hay una frase que circula en los pasillos de los laboratorios de IA y que rara vez llega a los titulares: <em>"No sabemos exactamente por qué funciona tan bien"</em>. Se dice en voz baja, con una mezcla de asombro y vértigo. Y es el mejor resumen del momento en que vivimos.</p>

<p>OpenAI lleva meses prometiendo GPT-5. Los rumores hablan de un salto cualitativo comparable al que hubo entre GPT-3 y GPT-4, pero más pronunciado. Se menciona razonamiento multimodal avanzado, capacidad de planificación a largo plazo y un manejo de la incertidumbre que los modelos actuales apenas rozan. Todo eso suena extraordinario. El problema es que ninguna de esas promesas viene acompañada de una respuesta satisfactoria a la pregunta más básica: ¿qué significa que una máquina "razone"?</p>

<h2>El problema del benchmark</h2>

<p>Cada vez que un nuevo modelo llega al mercado, el ritual es el mismo: una batería de benchmarks que el nuevo sistema supera con comodidad. MMLU, HumanEval, MATH, GSM8K. Los números suben, las gráficas se disparan hacia arriba y los comunicados de prensa celebran el "nuevo estado del arte". Lo que rara vez se explica es que esos benchmarks se van quedando obsoletos a medida que los modelos aprenden a optimizar para ellos, no para los problemas reales que se supone que miden.</p>

<p>El investigador de Google DeepMind François Chollet lleva años argumentando que la inteligencia general no puede medirse con tareas memorísticas, por complejas que sean. Su ARC-AGI benchmark, diseñado para medir razonamiento inductivo genuino, sigue siendo un muro para los mejores modelos actuales. GPT-4 ronda el 30% de aciertos. Los humanos medios superan el 85%.</p>

<p>Eso no significa que GPT-5 vaya a fracasar. Significa que el éxito depende de cómo definamos el objetivo.</p>

<h2>La trampa del producto</h2>

<p>OpenAI ya no es solo un laboratorio de investigación. Es una empresa con valoraciones que superan los 150.000 millones de dólares y compromisos con inversores que exigen crecimiento. Esa tensión entre investigación pura y producto comercial es la que más me preocupa cuando pienso en GPT-5.</p>

<p>Un modelo lanzado para cumplir un calendario de inversores es un modelo diferente a uno lanzado cuando el equipo de seguridad dice que está listo. Y la historia reciente de OpenAI —las salidas de figuras clave, las filtraciones internas, las cartas abiertas firmadas por exempleados— sugiere que esa tensión no se ha resuelto, sino que se ha normalizado.</p>

<h2>Lo que sí sabemos</h2>

<p>A pesar de todo, hay razones para el optimismo calibrado. GPT-4 demostró que los modelos de lenguaje grande pueden ser herramientas de productividad genuinas cuando se integran bien en flujos de trabajo reales. La programación asistida, la síntesis de documentos largos, la generación de código estructurado: estas aplicaciones funcionan y generan valor. GPT-5, sea lo que sea, probablemente las mejore de forma significativa.</p>

<p>Pero el problema no es si GPT-5 va a ser útil. Es si vamos a tener los marcos conceptuales, regulatorios y éticos para gestionarlo antes de que llegue. Y ahí, honestamente, la respuesta es no.</p>

<p>El umbral no es técnico. Es institucional. Y ese es el que nadie sabe cómo cruzar.</p>
HTML,
            ],

            // ── 2 ──────────────────────────────────────────────────────────────────
            [
                'title'        => 'El AI Act europeo: una ley para el presente que regulará el futuro',
                'excerpt'      => 'Europa aprobó la primera regulación integral de inteligencia artificial del mundo. Es imperfecta, tardía y probablemente insuficiente. También es lo mejor que tenemos.',
                'author_id'    => 14, // María Pérez
                'category_id'  => 21, // Regulación de IA
                'featured'     => true,
                'views'        => 980,
                'published_at' => now()->subDays(5),
                'content'      => <<<HTML
<p>El 13 de marzo de 2024, el Parlamento Europeo aprobó el AI Act con 523 votos a favor y 46 en contra. Fue celebrado como un hito histórico por casi todos, criticado como insuficiente por los más exigentes y tachado de excesivo por la industria tecnológica. Esa es, paradójicamente, la mejor señal de que probablemente estaba bien calibrado.</p>

<p>Llevo meses revisando el texto y hablando con juristas, ingenieros e investigadores de política pública. Mi conclusión es ambivalente: el AI Act es la regulación más seria que existe sobre inteligencia artificial en el mundo, y al mismo tiempo llegará tarde a casi todos los problemas que intenta resolver.</p>

<h2>La arquitectura de riesgos</h2>

<p>El enfoque central del AI Act es una clasificación por niveles de riesgo. Los sistemas de IA de riesgo inaceptable —como el reconocimiento facial en tiempo real en espacios públicos o los sistemas de puntuación social al estilo chino— están directamente prohibidos. Los de alto riesgo —IA en decisiones de crédito, contratación, diagnóstico médico, infraestructura crítica— requieren auditorías, documentación técnica y supervisión humana obligatoria. Los de bajo riesgo solo necesitan transparencia básica.</p>

<p>Esta arquitectura es sensata. El problema es que la realidad tecnológica rara vez encaja en casillas limpias. Un modelo de lenguaje general como GPT-4 puede ser una herramienta de bajo riesgo para generar textos de marketing y una herramienta de alto riesgo si se integra en un sistema de evaluación de candidatos de empleo. La misma tecnología, dos categorías distintas según el uso.</p>

<h2>El problema de los modelos de propósito general</h2>

<p>La batalla más intensa en la negociación final del AI Act fue sobre los GPAI (General Purpose AI) models: modelos grandes como GPT-4, Claude o Gemini. Francia, Alemania e Italia presionaron para suavizar las exigencias sobre estos modelos, argumentando que regulaciones demasiado estrictas dañarían a empresas europeas como Mistral AI. El lobby funcionó parcialmente: los requisitos para los GPAI quedaron más ligeros de lo que propuso la Comisión inicialmente.</p>

<p>Eso es un problema. Los modelos de propósito general son precisamente los que más dificultad presentan para la auditoría y el control. Son la base sobre la que se construyen miles de aplicaciones de alto riesgo. Regularlos superficialmente y luego exigir requisitos estrictos a las aplicaciones es como regular la química de los explosivos solo en el nivel del producto final.</p>

<h2>Lo que el AI Act no puede hacer</h2>

<p>El AI Act regula la IA que se deploya en la Unión Europea. No regula la IA que se desarrolla fuera de ella, aunque sus productos lleguen al mercado europeo a través de APIs y servicios digitales. No regula la carrera armamentística en IA entre EEUU y China. No establece ningún mecanismo global de coordinación.</p>

<p>Esas limitaciones no son culpa de los legisladores europeos. Son la consecuencia de que la regulación es inherentemente nacional o regional, mientras la tecnología es globalmente difusa.</p>

<p>Dicho esto: tener una regulación imperfecta es infinitamente mejor que no tener ninguna. El AI Act crea precedente, fuerza a las empresas a documentar sus sistemas, da a los ciudadanos mecanismos de recurso y envía una señal clara de que la IA no está por encima de la ley. Eso, en 2024, no es poca cosa.</p>
HTML,
            ],

            // ── 3 ──────────────────────────────────────────────────────────────────
            [
                'title'        => 'DeepSeek cambió las reglas del juego. ¿Alguien está mirando?',
                'excerpt'      => 'Un laboratorio chino lanzó un modelo competitivo con GPT-4 por una fracción del costo. Las implicaciones geopolíticas y técnicas son más profundas de lo que los titulares sugieren.',
                'author_id'    => 18, // José García
                'category_id'  => 1,  // Inteligencia Artificial
                'featured'     => true,
                'views'        => 2150,
                'published_at' => now()->subDays(1),
                'content'      => <<<HTML
<p>En enero de 2025, DeepSeek publicó DeepSeek-R1: un modelo de razonamiento que rivaliza con o1 de OpenAI en benchmarks de matemáticas y programación, entrenado según sus creadores con aproximadamente 6 millones de dólares. Para comparar: se estima que entrenar GPT-4 costó entre 50 y 100 millones. Las acciones de Nvidia cayeron un 17% en un solo día.</p>

<p>Esa reacción del mercado dice todo sobre cómo el mundo entendió el acontecimiento: como una señal de que el moat de las grandes tecnológicas americanas —construido sobre enormes inversiones en compute— podría ser mucho más frágil de lo que se pensaba.</p>

<h2>Qué hizo DeepSeek diferente</h2>

<p>No fue magia. Fue ingeniería extremadamente eficiente aplicada sobre técnicas conocidas. DeepSeek usó una arquitectura Mixture of Experts (MoE), donde solo una fracción de los parámetros del modelo se activa para cada token. También aplicó cuantización agresiva y una técnica llamada Multi-Head Latent Attention que reduce significativamente el uso de memoria. Nada de esto es secreto: estaba en papers públicos. La diferencia es que lo ejecutaron con una disciplina de eficiencia que los laboratorios con acceso ilimitado a compute raramente necesitan desarrollar.</p>

<p>Es el mismo principio que explica por qué los mejores optimizadores de código suelen venir de entornos con recursos limitados. La necesidad es la madre de la eficiencia.</p>

<h2>La dimensión geopolítica</h2>

<p>DeepSeek es una subsidiaria de High-Flyer, un hedge fund chino cuantitativo. No es una empresa de IA en el sentido convencional: es un fondo de inversión que construyó capacidad de IA como ventaja competitiva interna y luego la publicó como open source. Eso es inusual y revelador.</p>

<p>Las restricciones de exportación de chips avanzados que EEUU impuso a China obligaron a los investigadores chinos a desarrollar modelos más eficientes con hardware menos potente. Las sanciones diseñadas para frenar el avance chino en IA podrían haber acelerado inadvertidamente el desarrollo de técnicas que ahora amenazan la ventaja competitiva americana.</p>

<p>Eso no significa que China haya "ganado" la carrera de la IA. Significa que la carrera es más compleja de lo que los narrativos simplistas —"EEUU lidera, China sigue"— sugieren.</p>

<h2>Open source como vector estratégico</h2>

<p>DeepSeek publicó sus pesos del modelo bajo una licencia relativamente abierta. Eso es una decisión estratégica, no un acto de altruismo. Al hacer el modelo accesible globalmente, DeepSeek gana adopción, influencia en la comunidad de investigación y una reputación que ninguna campaña de marketing podría comprar.</p>

<p>Meta hizo lo mismo con Llama. La diferencia es que Meta tiene motivaciones defensivas claras —no quiere que OpenAI y Google monopolicen el ecosistema— mientras que las motivaciones de DeepSeek son más opacas y más interesantes de analizar.</p>

<p>En cualquier caso, el resultado para el ecosistema es positivo: modelos más capaces, más eficientes y más accesibles. El debate sobre las implicaciones de seguridad es legítimo, pero no debería oscurecer el hecho de que la competencia global en IA está produciendo beneficios técnicos reales y acelerados.</p>
HTML,
            ],

            // ── 4 ──────────────────────────────────────────────────────────────────
            [
                'title'        => 'IA y trabajo: el miedo equivocado y el miedo que sí deberíamos tener',
                'excerpt'      => 'Los estudios dicen que la IA destruirá millones de empleos. Otros dicen que creará aún más. Ambos pueden tener razón, y eso debería preocuparnos más, no menos.',
                'author_id'    => 16, // Sofía Romero
                'category_id'  => 22, // Impacto Laboral
                'featured'     => false,
                'views'        => 760,
                'published_at' => now()->subDays(7),
                'content'      => <<<HTML
<p>El informe del Foro Económico Mundial publicado en 2025 proyecta que la inteligencia artificial desplazará 85 millones de empleos en los próximos cinco años pero creará 97 millones nuevos. Un saldo neto positivo de 12 millones de puestos. Suena tranquilizador hasta que uno se pregunta: ¿quiénes son las personas del primer grupo y cuántas de ellas estarán en el segundo?</p>

<p>Esa pregunta es la que los titulares optimistas evitan sistemáticamente.</p>

<h2>La brecha de la transición</h2>

<p>Las revoluciones tecnológicas anteriores —la mecanización agrícola, la automatización industrial— también "crearon más empleos de los que destruyeron" en el largo plazo. Pero el largo plazo duró décadas, y las personas que perdieron sus trabajos en 1975 no pudieron esperar hasta 1995 para reciclarse en técnicos de mantenimiento de robots. Muchas simplemente quedaron fuera del mercado laboral, formando las "comunidades olvidadas" que siguen definiendo la política electoral en el midwest americano y en el norte industrial europeo.</p>

<p>La IA comprime esos ciclos, pero también los hace más disruptivos porque afecta simultáneamente a sectores que históricamente habían sido refugios en las transiciones: trabajo cognitivo de nivel medio, servicios profesionales, tareas de análisis y síntesis de información.</p>

<h2>Los trabajos que nadie menciona</h2>

<p>Hay una categoría de empleo que crece silenciosamente gracias a la IA y que rara vez aparece en los análisis de alto nivel: el trabajo de anotación, verificación y corrección de datos que alimenta los sistemas de entrenamiento. Son empleos reales, muchas veces mal pagados, concentrados en economías de bajos ingresos, realizados por personas que etiquetan imágenes, transcriben audio o califican respuestas de modelos.</p>

<p>El investigador de Oxford Callum Cant ha documentado extensamente estas condiciones en su trabajo sobre "ghost work": la mano de obra invisible que hace posible que la IA parezca autónoma. Esos trabajadores existen fuera de las estadísticas brillantes sobre "empleos del futuro" y dentro de las mismas condiciones precarias que la automatización supuestamente iba a eliminar.</p>

<h2>El miedo que sí tiene sentido</h2>

<p>No es el miedo a quedarse sin empleos. Es el miedo a la concentración de valor. Si la IA permite que una empresa de 50 personas genere el valor económico que antes requería 500, la pregunta no es si habrá empleos sino quién capturará los beneficios de esa productividad.</p>

<p>Esa concentración ya está ocurriendo. Las siete mayores empresas tecnológicas de EEUU representan más del 30% de la capitalización total del S&P 500. Sus márgenes crecen mientras los salarios medios en los sectores que automatizan se estancan o caen.</p>

<p>El debate sobre el futuro del trabajo no es un debate tecnológico. Es un debate político sobre distribución. Y en ese debate, la tecnología es solo la excusa.</p>
HTML,
            ],

            // ── 5 ──────────────────────────────────────────────────────────────────
            [
                'title'        => 'Anthropic y la paradoja del "safe AI": construir lo que temes',
                'excerpt'      => 'La empresa detrás de Claude afirma que el riesgo existencial de la IA es real pero sigue construyendo. Es una postura filosóficamente interesante y prácticamente inquietante.',
                'author_id'    => 18, // José García
                'category_id'  => 13, // Anthropic
                'featured'     => false,
                'views'        => 1100,
                'published_at' => now()->subDays(3),
                'content'      => <<<HTML
<p>Dario Amodei, CEO de Anthropic, ha declarado públicamente que la inteligencia artificial podría ser "una de las tecnologías más transformadoras y potencialmente peligrosas de la historia de la humanidad". En la misma entrevista describió a Claude como "el producto de IA más seguro del mercado". Y luego siguió construyéndolo.</p>

<p>Esa paradoja —creer sinceramente en el peligro y seguir adelante igualmente— define a Anthropic de una manera que no define a sus competidores. OpenAI habla de seguridad pero su discurso corporativo enfatiza el "beneficio para la humanidad" a través del progreso. Google DeepMind tiene un historial de investigación en seguridad sólido pero está integrado en la estructura de incentivos de una empresa de publicidad. Meta simplemente no pretende que su motivación principal sea la seguridad.</p>

<h2>La lógica del "si no lo hacemos nosotros"</h2>

<p>El argumento más común que esgrimen los investigadores de seguridad que trabajan en laboratorios de IA de frontera es lo que podríamos llamar la tesis del "actor responsable en la carrera": si los sistemas más avanzados van a existir de todas formas —porque China, porque OpenAI, porque la presión del capital— es mejor que los construyan personas que se tomen en serio la alineación.</p>

<p>Es una posición filosóficamente respetable. También es la misma lógica que usaron los físicos nucleares en Los Álamos. Y el resultado de Los Álamos fue que existieron las bombas atómicas, que las construyeron actores menos cuidadosos, y que el mundo lleva ochenta años gestionando ese riesgo existencial con un éxito que podríamos calificar como "accidentalmente bueno".</p>

<h2>Constitutional AI y los límites del alineamiento técnico</h2>

<p>La contribución técnica más interesante de Anthropic al campo es Constitutional AI (CAI): una técnica en la que el modelo es entrenado para autoevaluar sus respuestas contra un conjunto de principios, reduciendo la necesidad de retroalimentación humana en el proceso de RLHF. Es elegante y genuinamente útil.</p>

<p>Pero la constitución que define esos principios la escribe un equipo de personas con sus propios sesgos, valores y puntos ciegos. La pregunta de a quién le corresponde escribir la constitución ética de los sistemas de IA que usarán mil millones de personas no tiene respuesta técnica. Es una pregunta política, y Anthropic —como todos los laboratorios líderes— la responde unilateralmente.</p>

<h2>Por qué importa de todas formas</h2>

<p>A pesar de mis reservas, creo que Anthropic hace algo valioso: mantiene vivo en el discurso público la posibilidad de que los riesgos de la IA sean reales y merrezcan atención seria. En un ecosistema dominado por el entusiasmo desenfrenado y el optimismo de producto, esa voz tiene valor.</p>

<p>La paradoja no se resuelve. Pero nombrarla con honestidad es mejor que ignorarla.</p>
HTML,
            ],

            // ── 6 ──────────────────────────────────────────────────────────────────
            [
                'title'        => 'Deepfakes en elecciones: el arma que ya no necesita ser perfecta',
                'excerpt'      => 'El audio falso de Biden desaconsejando votar en primarias demostró algo perturbador: un deepfake no necesita ser convincente para ser efectivo. Solo necesita crear duda.',
                'author_id'    => 14, // María Pérez
                'category_id'  => 23, // Privacidad y Seguridad
                'featured'     => false,
                'views'        => 890,
                'published_at' => now()->subDays(9),
                'content'      => <<<HTML
<p>En enero de 2024, días antes de las primarias de New Hampshire, miles de votantes demócratas recibieron una llamada automatizada con la voz de Joe Biden diciéndoles que no fueran a votar en las primarias porque hacerlo "solo ayudaría a los republicanos". La voz era claramente sintética para cualquiera que prestara atención. No importó: la llamada llegó a más de 25.000 personas.</p>

<p>Ese incidente captura algo que los debates sobre deepfakes frecuentemente ignoran: la efectividad de la desinformación sintética no depende de su calidad técnica. Depende de su distribución y del contexto de incertidumbre en que llega.</p>

<h2>La economía de la duda</h2>

<p>Un deepfake perfecto es difícil y caro de producir. Pero un deepfake que simplemente siembre la duda —¿es real esto o no?— puede ser de baja calidad y bajo costo. En un entorno mediático donde la confianza institucional está en mínimos históricos, el beneficio del atacante no es que la gente crea el contenido falso. Es que deje de saber qué es real.</p>

<p>Los investigadores llaman a esto "el dividendo del mentiroso": cuando la desinformación prolifera, los actores deshonestos se benefician no porque sus mentiras sean creídas, sino porque la verdad deja de ser verificable a un costo razonable para el ciudadano promedio.</p>

<h2>Qué tienen preparado los estados</h2>

<p>En 2024, al menos 16 estados americanos aprobaron legislación relacionada con deepfakes electorales. La Unión Europea introdujo obligaciones de etiquetado para contenido sintético en su AI Act. X (Twitter) anunció políticas de etiquetado de contenido generado por IA que aplica de forma inconsistente.</p>

<p>Ninguna de estas medidas resuelve el problema fundamental: el etiquetado solo funciona en plataformas que lo exigen, y el contenido más dañino se distribuye precisamente por los canales menos regulados —grupos de WhatsApp, Telegram, foros alternativos— donde ninguna política de plataforma tiene alcance.</p>

<h2>La solución que nadie quiere escuchar</h2>

<p>No existe una solución tecnológica al problema de la desinformación sintética. Los detectores de deepfakes funcionan como juego del gato y el ratón: cada mejora en detección estimula mejoras en generación. El C2PA (Coalition for Content Provenance and Authenticity) está desarrollando estándares de procedencia para contenido digital que podrían ayudar a largo plazo, pero dependen de que toda la cadena —cámara, software de edición, plataforma de distribución— los adopte voluntariamente.</p>

<p>La respuesta más efectiva es antigua: alfabetización mediática, instituciones periodísticas fuertes y ciudadanos con hábitos de verificación. Nada de eso es escalable al ritmo al que escala la generación de contenido sintético. Y esa asimetría es el verdadero problema.</p>
HTML,
            ],

            // ── 7 ──────────────────────────────────────────────────────────────────
            [
                'title'        => 'Los modelos de razonamiento y el fin de la "alucinación" tal como la conocemos',
                'excerpt'      => 'OpenAI o1, DeepSeek-R1, Gemini Thinking. La nueva generación de modelos "que piensan antes de responder" promete reducir errores. ¿O solo los transforma?',
                'author_id'    => 15, // Pablo Fernández
                'category_id'  => 1,  // IA
                'featured'     => false,
                'views'        => 1320,
                'published_at' => now()->subDays(4),
                'content'      => <<<HTML
<p>Durante años, "alucinación" fue la palabra que definía el límite más frustrante de los modelos de lenguaje: su tendencia a generar información falsa con la misma confianza con que generan información verdadera. El término es antropomórfico y técnicamente impreciso, pero captura algo real sobre cómo falla la generación de texto estadística cuando no tiene acceso a verdad de terreno.</p>

<p>Los modelos de razonamiento —o1 de OpenAI, R1 de DeepSeek, Gemini 2.0 Thinking de Google— prometen cambiar eso. La idea es simple en superficie: antes de dar una respuesta, el modelo genera una cadena de razonamiento interna (visible en algunos casos, oculta en otros) que descompone el problema en pasos más verificables. Los benchmarks muestran mejoras sustanciales en matemáticas, lógica formal y programación.</p>

<h2>Qué cambia realmente</h2>

<p>El razonamiento encadenado (chain-of-thought) no elimina las alucinaciones. Las transforma. En lugar de afirmar directamente algo falso, el modelo puede construir una cadena de razonamiento coherente internamente pero basada en una premisa incorrecta que nadie cuestionó. El resultado es el mismo: información falsa presentada con confianza. La diferencia es que ahora hay un recorrido visible que puede ser auditado.</p>

<p>Eso es un progreso real, no nominal. Ver el razonamiento del modelo permite identificar dónde falló el proceso, no solo que falló. Para debugging, para auditoría de sistemas críticos, para educación: la trazabilidad del error tiene valor.</p>

<h2>El problema del "razonamiento" opaco</h2>

<p>o1 de OpenAI oculta sus cadenas de razonamiento internas al usuario final. La justificación oficial es que mostrarlo completo degrada la experiencia de usuario. La justificación real, según varios investigadores consultados de forma anónima, es que las cadenas de razonamiento a veces contienen pasos que parecen manipuladores o que revelan estrategias que la empresa prefiere no hacer explícitas.</p>

<p>Eso es un problema serio. Un modelo que razona de forma opaca y solo muestra su conclusión no es más auditable que un modelo sin razonamiento. Es exactamente igual de opaco, con la ventaja de marketing de poder decir que "tiene cadena de pensamiento".</p>

<h2>Lo que los benchmarks no miden</h2>

<p>Los modelos de razonamiento brillan en problemas formales con respuestas verificables: ecuaciones, código, lógica proposicional. Esos son precisamente los dominios donde los LLMs sin razonamiento ya eran bastante buenos. El impacto en los dominios donde las alucinaciones son más peligrosas —medicina, derecho, asesoramiento financiero, análisis de política pública— es mucho menos claro.</p>

<p>En esos dominios, el conocimiento relevante es a menudo ambiguo, contextual y dependiente de juicio experto que no puede reducirse a pasos formales. Un modelo que sabe resolver integrales perfectamente pero sigue malinterpretando los matices de una directiva clínica no ha resuelto el problema que importa.</p>

<p>La alucinación no muere con los modelos de razonamiento. Evoluciona. Y como siempre, la mejor defensa sigue siendo el criterio humano informado.</p>
HTML,
            ],

            // ── 8 ──────────────────────────────────────────────────────────────────
            [
                'title'        => 'IA en el aula: entre la promesa y el pánico, hay una conversación pendiente',
                'excerpt'      => 'Las escuelas prohíben ChatGPT, las universidades implementan detectores de IA que discriminan a estudiantes no nativos. Mientras tanto, nadie pregunta qué quieren aprender los estudiantes.',
                'author_id'    => 16, // Sofía Romero
                'category_id'  => 19, // IA en Educación
                'featured'     => false,
                'views'        => 640,
                'published_at' => now()->subDays(11),
                'content'      => <<<HTML
<p>En septiembre de 2023, el sistema escolar de Nueva York prohibió ChatGPT en dispositivos y redes escolares. En enero de 2024 revirtió la decisión. En ese intervalo de cuatro meses, ningún estudiante dejó de usar ChatGPT fuera de los dispositivos y redes escolares. La prohibición fue exactamente tan efectiva como prohibir la calculadora y esperar que los alumnos no supieran aritmética.</p>

<p>Esa historia resume el estado de la conversación sobre IA en educación: reactiva, asustada y completamente desconectada de lo que los estudiantes ya están haciendo.</p>

<h2>El detector que discrimina</h2>

<p>Turnitin lanzó un detector de IA que las universidades adoptaron masivamente. El problema: los estudios independientes muestran que tiene tasas de falsos positivos significativamente más altas para textos escritos por hablantes no nativos de inglés. Estudiantes internacionales con escritura formalmente correcta pero sintácticamente predecible son marcados como "posiblemente generados por IA" a tasas muy superiores a estudiantes nativos.</p>

<p>Una herramienta diseñada para proteger la integridad académica termina reproduciendo sesgos lingüísticos. Varias universidades han pausado su uso tras quejas documentadas. Otras lo siguen usando sin cuestionarlo.</p>

<h2>Qué dice la investigación</h2>

<p>Los estudios más rigurosos sobre el impacto de los LLMs en el aprendizaje muestran resultados matizados. Para estudiantes con acceso limitado a tutoría personalizada —primera generación universitaria, estudiantes de bajos recursos— la asistencia de IA puede reducir significativamente la brecha de soporte. Para estudiantes que ya tienen acceso a buenos recursos, el riesgo de sustitución del esfuerzo cognitivo es más alto.</p>

<p>La variable más importante no es la herramienta sino el diseño de la tarea. Un trabajo que puede hacerse enteramente con ChatGPT sin que el estudiante aprenda nada era ya un trabajo mal diseñado antes de ChatGPT. La IA solo lo hizo visible.</p>

<h2>La conversación que falta</h2>

<p>Las instituciones educativas están teniendo conversaciones sobre IA entre profesores, administradores y tecnólogos. Rara vez están teniendo esas conversaciones con estudiantes. Eso es un error metodológico grave: los principales usuarios de estas herramientas tienen perspectivas, necesidades y preocupaciones que los adultos en la sala no pueden adivinar completamente.</p>

<p>Los estudiantes no son objetos del cambio tecnológico en educación. Son agentes de él. Y hasta que las instituciones lo entiendan así, las políticas de IA educativa seguirán siendo reactivas, inconsistentes y fácilmente eludibles.</p>

<p>La pregunta no es cómo evitar que los estudiantes usen IA. Es cómo diseñar experiencias de aprendizaje que sean valiosas con IA disponible. Esa es la pregunta más difícil, y también la única que importa.</p>
HTML,
            ],

        ];

        // Nuevas columnas añadidas en abril 2026
        $columns = array_merge($columns, [
            [
                'title'        => 'El mito de la neutralidad algorítmica',
                'excerpt'      => 'Creer que los algoritmos son objetivos es quizás el error más costoso de nuestra era digital. Los sesgos no son bugs: son el reflejo de las decisiones humanas que los construyeron.',
                'author_id'    => 14,
                'category_id'  => 20,
                'featured'     => true,
                'views'        => 312,
                'published_at' => now()->subDays(3),
                'content'      => '<p>Hay una frase que se repite en los pasillos de Silicon Valley con la convicción de un dogma: "el algoritmo no miente". Se dice en reuniones de producto, en presentaciones ante inversores, en artículos de divulgación. Y es, probablemente, una de las mentiras más exitosas de nuestra era.</p><p>Los algoritmos no son entidades flotantes que emergen del éter con verdades objetivas. Son sistemas diseñados por personas, entrenados con datos generados por personas, optimizados para métricas elegidas por personas. Cada decisión en ese proceso —qué datos incluir, qué optimizar, qué considerar "error"— está cargada de valores, prioridades y, con demasiada frecuencia, sesgos no examinados.</p><h2>El problema no es técnico</h2><p>Cuando en 2018 Amazon descartó su sistema de reclutamiento automatizado porque penalizaba sistemáticamente a mujeres, la narrativa dominante fue la de un fallo técnico. Se habló de datos de entrenamiento sesgados, de modelos que aprendieron correlaciones incorrectas. Todo eso es cierto. Pero el error más profundo fue anterior: creer que era posible automatizar la selección de talento sin que los prejuicios históricos del sector tecnológico contaminaran el proceso.</p><h2>La responsabilidad que no podemos delegar</h2><p>La pregunta que debemos hacernos no es si los algoritmos pueden ser neutrales —no pueden— sino quién se responsabiliza de los daños que causan cuando no lo son. Necesitamos marcos regulatorios que traten los sistemas de IA de alto impacto con la misma seriedad con que tratamos los medicamentos o los vehículos. Y necesitamos, sobre todo, dejar de fingir que la neutralidad es posible.</p>',
            ],
            [
                'title'        => 'Claude, GPT y la ilusión de la conversación',
                'excerpt'      => 'Los modelos de lenguaje no piensan, no sienten, no comprenden. Y sin embargo, conversamos con ellos como si lo hicieran. ¿Qué nos dice eso de nosotros?',
                'author_id'    => 16,
                'category_id'  => 1,
                'featured'     => true,
                'views'        => 278,
                'published_at' => now()->subDays(5),
                'content'      => '<p>La primera vez que alguien le preguntó a ChatGPT si se sentía solo, el modelo respondió con algo parecido a la introspección. No afirmó ni negó. Navegó la ambigüedad con una destreza que dejó a muchos interlocutores desconcertados. Y en ese desconcierto hay algo importante que examinar.</p><p>Los grandes modelos de lenguaje —GPT-4, Claude, Gemini— son, en términos técnicos, máquinas de predicción de texto. Aprenden distribuciones estadísticas sobre enormes corpus de lenguaje humano y generan tokens que son, en términos probabilísticos, continuaciones plausibles de una entrada dada. No hay comprensión. No hay experiencia subjetiva. No hay nadie en casa.</p><h2>El problema del espejo</h2><p>Y sin embargo, hablamos con ellos como si la hubiera. No es irracionalidad: es una respuesta cognitiva profundamente arraigada. El cerebro humano está calibrado para detectar agencia, intención y emoción en todo lo que parece comportarse como si las tuviera.</p><h2>Una postura necesariamente incómoda</h2><p>Los modelos de lenguaje son herramientas extraordinariamente útiles. Pueden democratizar el acceso al conocimiento, ayudar a personas con barreras de idioma, asistir en tareas cognitivas complejas. Pero sostener simultáneamente que son útiles y que no son lo que parecen ser exige una honestidad intelectual que el mercado no incentiva.</p>',
            ],
            [
                'title'        => 'Open source vs. IA cerrada: la batalla que definirá la próxima década',
                'excerpt'      => 'Llama, Mistral, DeepSeek. El modelo abierto está ganando terreno. Pero "abierto" en IA no siempre significa lo que creemos.',
                'author_id'    => 15,
                'category_id'  => 14,
                'featured'     => true,
                'views'        => 445,
                'published_at' => now()->subDays(8),
                'content'      => '<p>En enero de 2025, DeepSeek publicó R1, un modelo de razonamiento que igualaba a GPT-4o en múltiples benchmarks, desarrollado con una fracción del presupuesto de los laboratorios occidentales y publicado con pesos abiertos. La reacción en los mercados fue inmediata: las acciones de Nvidia cayeron un 17% en un día.</p><h2>¿Qué significa "abierto" en IA?</h2><p>El término "open source" tiene una definición técnica precisa en software. En IA, la situación es más ambigua. Los modelos de "código abierto" publican los pesos —los parámetros del modelo— pero no necesariamente los datos de entrenamiento, el código de entrenamiento completo ni los procesos de alineación.</p><h2>Una tensión sin resolución fácil</h2><p>No hay una respuesta correcta aquí. Hay una tensión real entre los beneficios de la apertura —democratización, investigación, diversidad— y los riesgos de la proliferación sin control. Lo que sí es claro: las decisiones sobre cuánto abrir y cuánto controlar no deberían ser tomadas exclusivamente por los laboratorios que tienen interés económico en el resultado.</p>',
            ],
            [
                'title'        => 'La trampa del trabajo aumentado',
                'excerpt'      => 'Nos prometieron que la IA nos liberaría del trabajo repetitivo para dedicarnos a lo creativo. Pero la línea entre asistencia y sustitución es más delgada de lo que nos contaron.',
                'author_id'    => 18,
                'category_id'  => 22,
                'featured'     => false,
                'views'        => 198,
                'published_at' => now()->subDays(7),
                'content'      => '<p>Hay una narrativa que domina la conversación sobre IA y trabajo, tan repetida que casi se ha vuelto invisible: la automatización destruirá algunos empleos, pero creará otros nuevos. Lo hemos visto con la Revolución Industrial, con la informatización. Esta vez no será diferente. Adáptate o muere.</p><h2>El argumento de la complementariedad</h2><p>La posición estándar de los economistas optimistas es que la IA complementará el trabajo humano en lugar de sustituirlo. Pero hay algo que ese argumento no dice: cuando un abogado atiende el doble de casos con el mismo tiempo, el bufete necesita la mitad de abogados. La productividad individual puede aumentar mientras la demanda de trabajo disminuye. Son cosas compatibles.</p><h2>Lo que no está en el menú</h2><p>La conversación que no estamos teniendo es sobre redistribución. Si la IA va a generar enormes incrementos de productividad —y todo indica que lo hará— la pregunta política central es cómo se distribuyen esos beneficios.</p>',
            ],
            [
                'title'        => 'Regulación de IA: Europa apostó, el mundo espera',
                'excerpt'      => 'El AI Act europeo ya es ley. Ahora viene la parte difícil: implementarlo en un ecosistema global que no pidió permiso para moverse más rápido que la burocracia.',
                'author_id'    => 14,
                'category_id'  => 21,
                'featured'     => false,
                'views'        => 221,
                'published_at' => now()->subDays(10),
                'content'      => '<p>El 13 de marzo de 2024, el Parlamento Europeo aprobó el Reglamento de Inteligencia Artificial, el primero en su tipo en el mundo. Tres años de negociaciones, centenares de enmiendas, una pandemia y una revolución de los LLMs en medio del proceso. El resultado es un texto de 458 artículos que intenta hacer algo extraordinariamente difícil: regular una tecnología que evoluciona más rápido que los ciclos legislativos.</p><h2>El problema de la velocidad</h2><p>La crítica más legítima al AI Act no es ideológica sino temporal. Los modelos de lenguaje de propósito general —la tecnología más disruptiva del momento— apenas existían en su forma actual cuando empezaron las negociaciones.</p><h2>¿Quien seguirá a Europa?</h2><p>La pregunta estratégica es si el AI Act se convertirá en estándar global por el efecto Bruselas o si fragmentará el mercado global de IA. Lo que está claro es que la era de la IA sin regulación ha terminado, al menos en Europa.</p>',
            ],
            [
                'title'        => 'Agentes de IA: el cambio de paradigma que nadie está mirando',
                'excerpt'      => 'Los chatbots fueron solo el calentamiento. La siguiente ola de IA no responderá preguntas: actuará en el mundo por cuenta propia.',
                'author_id'    => 15,
                'category_id'  => 1,
                'featured'     => false,
                'views'        => 334,
                'published_at' => now()->subDays(12),
                'content'      => '<p>Cuando alguien le pregunta a Claude o a ChatGPT cómo organizar su bandeja de entrada, obtiene una respuesta. Cuando le pide a un agente de IA que organice su bandeja de entrada, el agente lee los correos, los categoriza, redacta respuestas a los urgentes, archiva los procesables y elimina el spam. No explica cómo hacerlo. Lo hace.</p><h2>El problema del control</h2><p>La diferencia entre un chatbot y un agente, desde la perspectiva del riesgo, es fundamental. Un chatbot dice. Un agente hace. Los errores de un chatbot son textuales y reversibles. Los errores de un agente que tiene acceso a tu correo, tu calendario, tus cuentas bancarias y tu sistema de archivos pueden tener consecuencias reales, inmediatas e irreversibles.</p><h2>La pregunta que no estamos haciendo</h2><p>El debate público sobre agentes de IA está dominado por la promesa de productividad. Pero hay una pregunta que casi no aparece: ¿qué perdemos cuando dejamos de hacer las cosas nosotros mismos?</p>',
            ],
            [
                'title'        => 'La IA que diagnostica y el médico que decide',
                'excerpt'      => 'Los modelos de visión por computadora superan a los radiólogos en la detección de ciertos cánceres. Pero eso no significa que los médicos sobren.',
                'author_id'    => 16,
                'category_id'  => 17,
                'featured'     => false,
                'views'        => 287,
                'published_at' => now()->subDays(15),
                'content'      => '<p>En 2017, un estudio publicado en Nature demostró que un sistema de deep learning podía diagnosticar melanoma con una precisión equivalente a la de dermatólogos certificados. Era una demostración impresionante. También fue el inicio de una narrativa simplificada —"la IA reemplazará a los médicos"— que desde entonces ha oscurecido más de lo que ha iluminado.</p><h2>Lo que la IA hace bien</h2><p>Los sistemas de IA para diagnóstico médico han demostrado capacidades genuinamente impresionantes en tareas específicas. En contextos de escasez de especialistas —que es la situación de la mayor parte del mundo— los sistemas de IA pueden funcionar como un primer filtro.</p><h2>El médico que la IA necesita</h2><p>El escenario más probable no es el reemplazo sino la redefinición del rol médico. Los médicos que trabajen con IA como herramienta diagnóstica deberán entender suficientemente los sistemas para evaluar sus outputs con criterio, identificar sus límites y no delegar la responsabilidad clínica final.</p>',
            ],
        ]);

        // Mapa de categorías: nombre → ID real en esta BD
        $catByName = Category::pluck('id', 'name')->toArray();
        $catById   = Category::pluck('name', 'id')->toArray();
        $fallbackCat = Category::first()?->id ?? 1;

        $created = 0;
        foreach ($columns as $data) {
            $data['slug'] = Str::slug($data['title']);

            // Reasignar author_id al usuario real disponible en esta BD
            $localAuthor = $data['author_id'];
            $data['author_id'] = $authorMap[$localAuthor] ?? $availableUsers[0];

            // Reasignar category_id usando el nombre de la categoría local
            // Si la categoría con ese ID existe, la usamos; si no, buscamos por nombre
            $localCatId   = $data['category_id'];
            $localCatName = $catById[$localCatId] ?? null;
            if ($localCatName && isset($catByName[$localCatName])) {
                $data['category_id'] = $catByName[$localCatName];
            } elseif (Category::find($localCatId)) {
                // el ID sí existe en esta BD (coincide)
                $data['category_id'] = $localCatId;
            } else {
                $data['category_id'] = $fallbackCat;
            }

            Column::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
            $created++;
        }

        $this->command->info("✓ {$created} columnas procesadas (creadas si no existían).");
    }
}
