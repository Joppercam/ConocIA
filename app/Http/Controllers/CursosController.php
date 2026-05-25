<?php

namespace App\Http\Controllers;

class CursosController extends Controller
{
    public function index()
    {
        $courses = $this->coursesData();
        return view('cursos.index', compact('courses'));
    }

    public function show(string $slug)
    {
        $courses = $this->coursesData();
        $course  = collect($courses)->firstWhere('slug', $slug);

        abort_if(!$course, 404);

        $others = collect($courses)->where('slug', '!=', $slug)->values()->take(3)->all();

        return view('cursos.show', compact('course', 'others'));
    }

    public function lesson(string $slug, int $module, int $lesson)
    {
        $courses    = $this->coursesData();
        $course     = collect($courses)->firstWhere('slug', $slug);

        abort_if(!$course, 404);

        $moduleData = $course['modules'][$module - 1] ?? null;
        abort_if(!$moduleData, 404);

        $lessonTitle = $moduleData['lessons'][$lesson - 1] ?? null;
        abort_if($lessonTitle === null, 404);

        $content = $this->lessonContent($slug, $module, $lesson);

        $prevLesson = $lesson > 1
            ? ['module' => $module, 'lesson' => $lesson - 1]
            : ($module > 1
                ? ['module' => $module - 1, 'lesson' => count($course['modules'][$module - 2]['lessons'])]
                : null);

        $totalLessons = count($moduleData['lessons']);
        $nextLesson = $lesson < $totalLessons
            ? ['module' => $module, 'lesson' => $lesson + 1]
            : (isset($course['modules'][$module])
                ? ['module' => $module + 1, 'lesson' => 1]
                : null);

        return view('cursos.lesson', compact(
            'course', 'moduleData', 'lessonTitle',
            'module', 'lesson', 'content',
            'prevLesson', 'nextLesson'
        ));
    }

    public static function lessonHasContent(string $slug, int $module, int $lesson): ?bool
    {
        $key = "{$slug}_{$module}_{$lesson}";
        $defined = [
            'ia-para-derecho_1_1', 'ia-para-derecho_1_2',
            'ia-para-derecho_1_3', 'ia-para-derecho_1_4',
        ];
        return in_array($key, $defined) ? true : null;
    }

    private function lessonContent(string $slug, int $module, int $lesson): ?string
    {
        $key = "{$slug}_{$module}_{$lesson}";

        $contents = [

            // ── DERECHO · MÓDULO 1 ──────────────────────────────────────────

            'ia-para-derecho_1_1' => <<<HTML
<p>La inteligencia artificial lleva más de una década entrando a los despachos legales, pero el salto que ocurrió entre 2022 y 2025 no tiene precedentes. Lo que antes era una promesa de eficiencia se convirtió en una realidad que reorganiza cómo se ejerce el derecho, qué hacen los abogados junior y qué esperan los clientes de su firma.</p>
<p>El cambio más visible está en la investigación jurídica y la revisión de documentos. Tareas que antes tomaban días a un equipo de abogados ahora se hacen en horas —o minutos— con herramientas de IA. Harvey, la startup legal de IA más financiada del mundo (más de 100 millones de dólares recaudados), ya es usada por firmas como Paul Weiss, Allen & Overy y Milbank. No es un producto experimental: es infraestructura de trabajo cotidiano.</p>

<div style="background:#faf5ff;border:1px solid #e9d5ff;border-left:4px solid #a78bfa;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#6d28d9;">¿Qué tan rápido está creciendo esto?</p>
    <p style="color:#6d28d9;margin:0;font-size:.93rem;">Según McKinsey, el 23% del trabajo legal es automatizable con la tecnología actual. Goldman Sachs estimó que la IA podría desplazar el equivalente a 44.000 empleos de abogados solo en EE.UU. en los próximos años. Pero la misma investigación señala que el volumen total de trabajo legal probablemente aumente, compensando parte de ese desplazamiento.</p>
</div>

<p>En América Latina y Chile la adopción es más lenta, pero el movimiento es real. Algunas firmas grandes ya usan herramientas de revisión de contratos y due diligence asistido por IA. Las que no lo hacen están evaluando si hacerlo. Ninguna firma relevante ignora ya el tema.</p>
<p>Para el abogado individual, el riesgo no está en desaparecer sino en quedarse obsoleto. El que entienda estas herramientas —qué pueden hacer, cuándo fallan, qué riesgos implican— va a tener ventaja sobre el que las ignore. Y el que las use con criterio va a tener ventaja sobre el que las use a ciegas.</p>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.5rem;padding:1.25rem;margin-top:1.5rem;">
    <p class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">📌 Para recordar</p>
    <p style="color:#475569;margin:0;font-size:.9rem;">La IA no reemplaza el derecho. Reemplaza las tareas legales más repetitivas. Los abogados que entiendan esa distinción y la aprovechen van a ser los más valiosos de sus equipos.</p>
</div>
HTML,

            'ia-para-derecho_1_2' => <<<HTML
<p>Una de las confusiones más comunes al hablar de IA en el derecho es tratar la tecnología como si fuera todo o nada: o "lo hace todo" o "no puede hacer nada real". La realidad es más útil: hay tareas legales que la IA hace muy bien hoy, y hay tareas donde sigue siendo indispensable el juicio de un abogado.</p>

<h2>Lo que la IA puede hacer bien</h2>
<p><strong>Revisión y análisis de contratos.</strong> Identificar cláusulas problemáticas, comparar versiones, extraer obligaciones clave, verificar consistencia interna. Herramientas como Kira o Ironclad hacen esto más rápido y con menos errores que un equipo de abogados junior revisando cientos de páginas.</p>
<p><strong>Investigación jurisprudencial.</strong> Buscar precedentes relevantes, identificar la tendencia de los tribunales en un tema, resumir fallos extensos. Lexis+ IA y Westlaw Precision integran modelos de lenguaje que permiten hacer preguntas en lenguaje natural y obtener respuestas fundamentadas en jurisprudencia real.</p>
<p><strong>Due diligence en M&A.</strong> Revisar miles de documentos en poco tiempo para identificar riesgos en una transacción. Lo que antes tomaba semanas a equipos grandes ahora puede hacerse en días.</p>
<p><strong>Primer borrador de documentos.</strong> Contratos estándar, escritos de trámite, resúmenes ejecutivos, cartas de demanda tipo. La IA genera un borrador razonable que el abogado revisa y ajusta.</p>

<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #00c896;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#166534;">Tareas que la IA hace bien hoy</p>
    <ul style="color:#166534;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>Revisión y comparación de contratos</li>
        <li>Investigación jurisprudencial y doctrinal</li>
        <li>Due diligence documental en transacciones</li>
        <li>Borradores de documentos estándar</li>
        <li>Resúmenes de fallos y normativa</li>
        <li>Clasificación y organización de expedientes</li>
    </ul>
</div>

<h2>Lo que la IA no puede hacer</h2>
<p><strong>Juicio ético en situaciones ambiguas.</strong> Cuando los hechos son complejos y los valores están en tensión, la IA no puede razonar moralmente. Puede simular el razonamiento, pero no asume responsabilidad.</p>
<p><strong>Negociación estratégica.</strong> Leer la sala, gestionar la relación con la contraparte, adaptar la posición en tiempo real a señales no verbales. Esto sigue siendo profundamente humano.</p>
<p><strong>Argumentos jurídicos genuinamente originales.</strong> La IA recombina lo que existe. Las teorías jurídicas nuevas que cambian la interpretación del derecho siguen viniendo de personas.</p>
<p><strong>Representación ante tribunales.</strong> Además de las restricciones regulatorias obvias, el litigio involucra dinámica humana que la IA no puede manejar.</p>

<div style="background:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #f43f5e;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#9f1239;">El riesgo más subestimado: las alucinaciones</p>
    <p style="color:#9f1239;margin:0;font-size:.93rem;">Los modelos de IA pueden inventar jurisprudencia que no existe con total confianza. En 2023, dos abogados de Nueva York fueron sancionados por presentar ante un tribunal citas de fallos inventados por ChatGPT que nadie verificó. Siempre valida la jurisprudencia que genera la IA en fuentes primarias.</p>
</div>
HTML,

            'ia-para-derecho_1_3' => <<<HTML
<p>Más allá de las promesas de los proveedores, vale la pena mirar qué está pasando en firmas reales con herramientas reales. Los casos concretos son más útiles que los prospectos de ventas para entender qué esperar de estas tecnologías.</p>

<h2>Harvey: el caso de referencia global</h2>
<p>Harvey es la startup de IA legal más prominente del momento. Construida sobre modelos de OpenAI pero entrenada específicamente en datos jurídicos, fue adoptada por Allen & Overy (una de las firmas más grandes del mundo, con más de 40 oficinas globales) antes de salir al mercado amplio. Hoy la usan firmas como Paul Weiss, Milbank y Macfarlanes.</p>
<p>¿Para qué la usan? Principalmente para investigación jurídica, revisión de contratos y generación de borradores. Los abogados reportan ahorros significativos de tiempo en tareas de due diligence y análisis de documentos. Allen & Overy implementó Harvey para sus más de 3.500 abogados en todo el mundo.</p>

<h2>Kira Systems: revisión de contratos a escala</h2>
<p>Kira fue una de las primeras herramientas de IA legal en demostrar valor real en due diligence. Identifica y extrae automáticamente cláusulas clave de contratos —cambio de control, limitaciones de responsabilidad, confidencialidad, plazos— y puede procesar miles de documentos simultáneamente. KPMG, Deloitte y numerosas firmas legales la usan en procesos de M&A.</p>

<h2>Luminance: e-discovery y compliance</h2>
<p>Luminance usa IA para revisar contratos y documentos en procesos de due diligence y e-discovery. Desarrollada por matemáticos de Cambridge, analiza el lenguaje legal con modelos entrenados exclusivamente en documentos jurídicos. Más de 400 organizaciones en 60 países la usan, incluyendo Slaughter and May y Kirkland & Ellis.</p>

<div style="background:#faf5ff;border:1px solid #e9d5ff;border-left:4px solid #a78bfa;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#6d28d9;">¿Y en América Latina?</p>
    <p style="color:#6d28d9;margin:0;font-size:.93rem;">La adopción es más lenta pero existe. Algunas firmas grandes de Brasil, México y Chile ya usan herramientas de revisión de contratos y research con IA. En Chile, el mercado está en etapa de exploración: los despachos medianos y grandes están evaluando, los pequeños recién se informan. Quien llegue primero con una implementación que funcione tendrá ventaja de posicionamiento frente a sus clientes corporativos.</p>
</div>

<h2>La lección de los casos reales</h2>
<p>Lo que emerge de todos estos casos es un patrón consistente: la IA acelera el trabajo repetitivo y de alto volumen, pero el trabajo estratégico —la interpretación, la asesoría, la negociación— sigue siendo humano. Las firmas que van bien con IA son las que la usan para hacer más de lo que ya hacen bien, no para reemplazar el juicio de sus abogados.</p>
HTML,

            'ia-para-derecho_1_4' => <<<HTML
<p>Ningún otro sector del mercado laboral ha analizado tanto su propio futuro frente a la IA como el derecho. Hay datos, proyecciones y debates intensos. Pero más allá de los números, lo que importa es entender qué significa esto para quienes ejercen hoy o estudian para ejercer mañana.</p>

<h2>¿Quién está más expuesto?</h2>
<p>El trabajo legal más expuesto a la automatización es el de los abogados junior y los paralegal: revisión de documentos, investigación jurídica, borradores estándar, organización de expedientes. Son tareas de alto volumen, relativamente predecibles y con criterios claros de éxito. Exactamente lo que la IA aprende a hacer bien.</p>
<p>Los socios y abogados senior están menos expuestos en el corto plazo: su trabajo es mayoritariamente estratégico, relacional y de juicio. Pero incluso ellos verán cambiar su trabajo: si los junior procesan documentos con IA diez veces más rápido, la estructura de las firmas cambia. Se necesitan menos junior para el mismo volumen de trabajo, o los mismos junior producen mucho más.</p>

<div style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#92400e;">Los datos que circulan</p>
    <ul style="color:#78350f;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li><strong>Goldman Sachs (2023):</strong> estima que la IA podría automatizar el 44% de las tareas legales actuales en EE.UU.</li>
        <li><strong>McKinsey (2023):</strong> el 23% del trabajo legal es altamente automatizable con tecnología existente.</li>
        <li><strong>World Economic Forum (2023):</strong> proyecta que el derecho perderá empleos netos por IA, aunque surgirán nuevos roles.</li>
    </ul>
</div>

<h2>Los roles que crecen</h2>
<p>La misma transformación que comprime algunos roles crea otros. El de <strong>legal technologist</strong> —alguien que entiende tanto el derecho como las herramientas tecnológicas que lo asisten— es hoy uno de los perfiles más demandados en firmas grandes. Los abogados que saben seleccionar, evaluar y supervisar sistemas de IA son más valiosos que los que no.</p>
<p>También crecen los roles relacionados con asesoría en regulación de IA: empresas que desarrollan o usan IA necesitan abogados que entiendan el AI Act, las implicancias de la ley de datos personales, la responsabilidad civil de los sistemas automatizados. Es un área de práctica nueva que no existía hace cinco años.</p>

<h2>¿Qué deberían hacer las facultades de derecho?</h2>
<p>Esta es la pregunta que más debate genera. La mayoría de las facultades aún no han incorporado la IA de forma sistemática en sus currículos. Harvard Law, Stanford y algunas europeas ya tienen cursos de derecho y tecnología. En Chile, el movimiento está empezando.</p>
<p>Lo que sí es claro: los estudiantes de derecho que salgan hoy sin entender estas herramientas van a llegar a un mercado laboral que ya las usa. La alfabetización en IA no es opcional para la nueva generación de abogados.</p>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.5rem;padding:1.25rem;margin-top:1.5rem;">
    <p class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">📌 Para recordar</p>
    <p style="color:#475569;margin:0;font-size:.9rem;">La IA no va a eliminar a los abogados. Va a eliminar a los abogados que no saben usarla, y va a amplificar a los que sí saben. La pregunta no es "¿me va a quitar el trabajo?" sino "¿qué voy a poder hacer yo que la IA no pueda?"</p>
</div>
HTML,

        ];

        return $contents[$key] ?? null;
    }

    private function coursesData(): array
    {
        return [
            [
                'slug'          => 'ia-para-derecho',
                'title'         => 'IA para Profesionales del Derecho',
                'subtitle'      => 'Entiende cómo la IA transforma la práctica legal y qué responsabilidades implica',
                'badge'         => 'Derecho',
                'badge_color'   => '#a78bfa',
                'icon'          => 'fa-balance-scale',
                'audience'      => 'Abogados, estudiantes de derecho, jueces, notarios y asistentes legales',
                'level'         => 'Sin requisitos técnicos',
                'modules_count' => 5,
                'lessons_count' => 20,
                'status'        => 'disponible',
                'description'   => 'La IA ya está revisando contratos, analizando jurisprudencia y asistiendo en due diligence en despachos de todo el mundo. Herramientas como Harvey, Kira o Lexis+ IA están entrando a las firmas legales, cambiando qué hacen los abogados junior y qué esperan los clientes. Este curso te da las herramientas para entender qué hace la IA en el ámbito legal, qué riesgos implica, cómo te afecta como profesional y cómo puedes usarla sin comprometer tu ética ni la confidencialidad de tus clientes.',
                'why_now'       => 'El AI Act europeo ya es ley —la regulación de IA más completa del mundo— y clasifica varios sistemas legales como de alto riesgo. Chile debate su propia normativa de datos y IA. Los clientes ya preguntan qué hace su firma con IA. Los abogados que no entiendan este ecosistema van a asesorar mal, asumir riesgos innecesarios o simplemente quedar fuera de mercado.',
                'outcomes'      => [
                    'Entender cómo funciona la revisión automática de contratos y qué puede fallar',
                    'Conocer el AI Act y sus implicancias para la práctica legal en Chile',
                    'Identificar riesgos de confidencialidad al usar IA con datos de clientes',
                    'Evaluar herramientas de IA para investigación jurisprudencial y redacción',
                    'Saber cuándo y cómo aconsejar a tus clientes sobre el uso de IA',
                ],
                'modules' => [
                    [
                        'number'      => 1,
                        'title'       => 'La IA está cambiando el derecho',
                        'description' => 'Qué está pasando hoy en los despachos, qué tareas legales puede hacer la IA y cuáles no, y cómo está impactando el mercado laboral legal.',
                        'lessons'     => ['IA en el ejercicio legal: panorama actual', 'Qué tareas legales puede (y no puede) hacer la IA', 'Casos reales: firmas que ya usan IA', 'Impacto en el mercado laboral del derecho'],
                    ],
                    [
                        'number'      => 2,
                        'title'       => 'Revisión de contratos y documentos',
                        'description' => 'Cómo funciona la IA para revisar contratos, qué herramientas existen, qué puede pasar por alto y qué responsabilidad tienes cuando la IA se equivoca.',
                        'lessons'     => ['Cómo funciona la revisión automática de contratos', 'Herramientas disponibles: Harvey, Kira, ContractPodAi', 'Qué puede pasar por alto la IA (y por qué)', 'Responsabilidad profesional cuando la IA falla'],
                    ],
                    [
                        'number'      => 3,
                        'title'       => 'Marco regulatorio: AI Act y Chile',
                        'description' => 'Qué dice el AI Act sobre sistemas legales de alto riesgo, el debate sobre IA en el ámbito judicial, la nueva ley de datos personales en Chile y cómo asesorar a tus clientes.',
                        'lessons'     => ['El AI Act: qué dice sobre sistemas legales', 'IA en el ámbito judicial: el debate global', 'La nueva ley de datos personales en Chile', 'Cómo asesorar a clientes que usan IA'],
                    ],
                    [
                        'number'      => 4,
                        'title'       => 'Riesgos éticos y responsabilidad',
                        'description' => 'Confidencialidad y IA, sesgos en sistemas judiciales, casos de abogados sancionados por errores de IA, y los estándares éticos del abogado en la era IA.',
                        'lessons'     => ['Confidencialidad: qué pasa con los datos de tus clientes', 'Sesgos algorítmicos en sistemas judiciales', 'Casos: abogados sancionados por errores de IA', 'Estándares éticos del abogado en la era IA'],
                    ],
                    [
                        'number'      => 5,
                        'title'       => 'Herramientas para tu práctica',
                        'description' => 'Investigación jurisprudencial con IA, redacción y revisión de escritos, due diligence y M&A, y tu hoja de ruta para integrar IA hoy.',
                        'lessons'     => ['Investigación jurisprudencial con IA', 'Redacción y revisión de escritos con IA', 'Due diligence y M&A asistido por IA', 'Tu hoja de ruta: cómo integrar IA en tu práctica'],
                    ],
                ],
            ],

            [
                'slug'          => 'ia-para-docentes',
                'title'         => 'IA para Docentes',
                'subtitle'      => 'Cómo adaptar tu enseñanza y evaluación cuando la IA ya está en el aula',
                'badge'         => 'Educación',
                'badge_color'   => '#00c896',
                'icon'          => 'fa-chalkboard-teacher',
                'audience'      => 'Profesores de educación básica, media y universitaria, directores académicos',
                'level'         => 'Sin requisitos técnicos',
                'modules_count' => 5,
                'lessons_count' => 20,
                'status'        => 'disponible',
                'description'   => 'ChatGPT ya está en tus aulas, lo uses tú o no. Entre el 60 y el 70% de los estudiantes universitarios reconocen haber usado IA en sus trabajos académicos. Este curso no es sobre prohibir la IA: es sobre entender qué está cambiando en la educación, cómo rediseñar la evaluación para que siga midiendo lo que importa, cómo usar la IA como herramienta didáctica y cómo preparar a tus estudiantes para un mundo donde la IA es parte de su vida profesional.',
                'why_now'       => 'Los establecimientos sin política clara sobre IA están tomando decisiones improvisadas. Los profesores que lideren este cambio —que entiendan la tecnología y la integren críticamente— van a ser los más valiosos de sus equipos. El que espere a que "esto pase" va a llegar tarde.',
                'outcomes'      => [
                    'Entender qué herramientas de IA usan tus estudiantes y cómo las usan',
                    'Rediseñar evaluaciones que no puedan ser resueltras solo con IA',
                    'Usar IA como herramienta de preparación de clases y retroalimentación',
                    'Desarrollar políticas de integridad académica acordes al contexto actual',
                    'Preparar a tus estudiantes para trabajar con IA de forma crítica',
                ],
                'modules' => [
                    [
                        'number'      => 1,
                        'title'       => 'Lo que está pasando en tus aulas',
                        'description' => 'Cómo y cuánto usan la IA los estudiantes hoy, qué herramientas conocen, y qué dicen los datos sobre el uso real en Chile y América Latina.',
                        'lessons'     => ['Cuánto usa IA tu alumnado (y cómo)', 'Las herramientas que usan tus estudiantes', 'Qué pueden y no pueden hacer estas herramientas', 'El contexto en Chile: datos y realidad'],
                    ],
                    [
                        'number'      => 2,
                        'title'       => 'Integridad académica en la era IA',
                        'description' => 'El nuevo escenario ético, qué constituye deshonestidad académica cuando existe IA, cómo detectarla y cómo construir políticas que funcionen.',
                        'lessons'     => ['¿Qué es trampa cuando existe la IA?', 'Detectores de IA: alcances y límites', 'Cómo construir una política de integridad que funcione', 'La conversación que hay que tener con los estudiantes'],
                    ],
                    [
                        'number'      => 3,
                        'title'       => 'IA como herramienta didáctica',
                        'description' => 'Cómo usar la IA para preparar clases, generar ejercicios, dar retroalimentación personalizada y diferenciada, y reducir carga administrativa.',
                        'lessons'     => ['IA para preparar clases y materiales', 'Generar ejercicios y evaluaciones con IA', 'Retroalimentación personalizada a escala', 'Reducir carga administrativa con IA'],
                    ],
                    [
                        'number'      => 4,
                        'title'       => 'Cómo evaluar diferente',
                        'description' => 'Tipos de evaluación que siguen midiendo aprendizaje real, cómo incorporar el uso de IA en la evaluación misma, y casos prácticos por asignatura.',
                        'lessons'     => ['Evaluaciones que no puede responder solo la IA', 'Evaluar el proceso, no solo el producto', 'Incorporar IA como parte de la evaluación', 'Casos prácticos por asignatura y nivel'],
                    ],
                    [
                        'number'      => 5,
                        'title'       => 'Tu plan de acción como docente',
                        'description' => 'Cómo construir tu política personal de uso de IA, cómo presentarla a estudiantes y apoderados, y cómo preparar a tus alumnos para el mundo laboral que viene.',
                        'lessons'     => ['Construye tu política de IA en el aula', 'Cómo comunicarlo a estudiantes y apoderados', 'Preparar estudiantes para el mundo laboral con IA', 'Recursos y comunidades para seguir aprendiendo'],
                    ],
                ],
            ],

            [
                'slug'          => 'ia-para-periodistas',
                'title'         => 'IA para Periodistas y Comunicadores',
                'subtitle'      => 'Verificar, cubrir y usar IA con rigor en el oficio periodístico',
                'badge'         => 'Periodismo',
                'badge_color'   => '#f59e0b',
                'icon'          => 'fa-newspaper',
                'audience'      => 'Periodistas, comunicadores, editores, relacionadores públicos, estudiantes de periodismo',
                'level'         => 'Sin requisitos técnicos',
                'modules_count' => 5,
                'lessons_count' => 20,
                'status'        => 'disponible',
                'description'   => 'Deepfakes de candidatos, artículos generados por bots, fuentes sintéticas, datos fabricados con apariencia de rigor. El periodismo enfrenta la mayor crisis de verificación de su historia. Al mismo tiempo, la IA ofrece herramientas que pueden hacer mejor y más rápido el trabajo periodístico. Este curso te da criterios concretos para detectar contenido generado por IA, cubrir el tema con rigor sin caer en hype ni catastrofismo, y usar IA en tu trabajo de forma transparente.',
                'why_now'       => 'Las elecciones de 2024 y 2025 en varios países fueron las primeras con desinformación generada por IA a escala industrial. Chile no es la excepción. Los medios que no capaciten a sus equipos en verificación de contenido sintético van a publicar errores que antes eran imposibles. Y los periodistas que no sepan usar IA van a producir menos con más esfuerzo.',
                'outcomes'      => [
                    'Detectar imágenes, videos y textos generados por IA con herramientas concretas',
                    'Cubrir temas de IA con precisión técnica y sin alarmismo',
                    'Usar IA para investigación, transcripción y análisis de datos periodísticos',
                    'Establecer estándares éticos de transparencia cuando usas IA en tu trabajo',
                    'Construir rutinas de verificación para el contexto de desinformación actual',
                ],
                'modules' => [
                    [
                        'number'      => 1,
                        'title'       => 'La IA está cambiando el periodismo',
                        'description' => 'Cómo la IA está transformando la producción de contenido, qué medios ya la usan, qué riesgos plantea para la credibilidad y qué oportunidades abre.',
                        'lessons'     => ['Medios que ya usan IA: casos reales', 'Desinformación a escala industrial: el nuevo escenario', 'Lo que la IA puede hacer por el periodismo', 'Lo que la IA amenaza en el periodismo'],
                    ],
                    [
                        'number'      => 2,
                        'title'       => 'Verificar en la era del contenido IA',
                        'description' => 'Herramientas y metodologías para detectar imágenes, videos y textos generados por IA, y cómo integrar la verificación en el flujo de trabajo diario.',
                        'lessons'     => ['Cómo detectar imágenes generadas por IA', 'Deepfakes de video y audio: señales de alerta', 'Textos sintéticos: cómo identificarlos', 'Flujo de verificación para la redacción diaria'],
                    ],
                    [
                        'number'      => 3,
                        'title'       => 'Cómo cubrir IA con rigor',
                        'description' => 'Marcos conceptuales para informar sobre IA sin caer en hype ni catastrofismo, cómo explicar conceptos técnicos a audiencias generales, y errores frecuentes al cubrir IA.',
                        'lessons'     => ['Los errores más comunes al cubrir IA', 'Cómo explicar IA a una audiencia general', 'Fuentes confiables y expertos en IA en Chile', 'Estándares editoriales para informar sobre IA'],
                    ],
                    [
                        'number'      => 4,
                        'title'       => 'Herramientas de IA para periodistas',
                        'description' => 'Transcripción automática, análisis de documentos, investigación asistida, generación de visualizaciones de datos y automatización de tareas repetitivas.',
                        'lessons'     => ['IA para transcripción y análisis de entrevistas', 'Investigar con IA: documentos, datos y fuentes', 'Visualización de datos asistida por IA', 'Automatización de tareas repetitivas en la redacción'],
                    ],
                    [
                        'number'      => 5,
                        'title'       => 'Ética y transparencia',
                        'description' => 'Cuándo y cómo declarar el uso de IA en tus publicaciones, los límites éticos del uso de IA en periodismo, y cómo construir una política editorial de IA.',
                        'lessons'     => ['¿Cuándo declaras que usaste IA?', 'Límites éticos: lo que la IA no debe hacer en periodismo', 'Construir una política editorial de IA', 'El futuro del oficio: periodista + IA'],
                    ],
                ],
            ],

            [
                'slug'          => 'ia-para-rrhh',
                'title'         => 'IA para RRHH y Gestión de Personas',
                'subtitle'      => 'Cómo implementar IA en procesos de personas de forma ética y responsable',
                'badge'         => 'RRHH',
                'badge_color'   => '#38b6ff',
                'icon'          => 'fa-users-cog',
                'audience'      => 'Gerentes y analistas de RRHH, reclutadores, psicólogos organizacionales, jefes de área',
                'level'         => 'Sin requisitos técnicos',
                'modules_count' => 5,
                'lessons_count' => 20,
                'status'        => 'disponible',
                'description'   => 'Los algoritmos ya están filtrando currículums, evaluando entrevistas en video y prediciendo desempeño en miles de empresas de todo el mundo, incluyendo Chile. Este curso te explica cómo funcionan esos sistemas, qué sesgos pueden tener, qué responsabilidad legal asumes al usarlos y cómo implementar IA en gestión de personas de forma que sea efectiva, ética y sostenible ante una eventual revisión regulatoria.',
                'why_now'       => 'El AI Act europeo clasifica los sistemas de IA para selección de personal como de alto riesgo, con obligaciones específicas de transparencia y auditoría. Chile avanza en regulación similar. Las empresas que implementen IA en RRHH sin entender estos riesgos van a tener problemas legales y reputacionales que hoy parecen invisibles.',
                'outcomes'      => [
                    'Entender cómo funcionan los sistemas de IA de selección y evaluación',
                    'Identificar sesgos algorítmicos en procesos de contratación',
                    'Conocer los límites legales del monitoreo de empleados con tecnología',
                    'Evaluar y seleccionar proveedores de IA para RRHH con criterio',
                    'Diseñar procesos de IA en personas que sean auditables y justos',
                ],
                'modules' => [
                    [
                        'number'      => 1,
                        'title'       => 'La IA ya entró a tus procesos',
                        'description' => 'Qué sistemas de IA se usan en RRHH hoy, en qué etapas del ciclo del empleado está presente, y qué promete versus qué entrega realmente.',
                        'lessons'     => ['Dónde está la IA en el ciclo del empleado', 'Herramientas más usadas en RRHH con IA', 'Lo que prometen los proveedores vs. la realidad', 'Cómo evaluar si tu empresa ya usa IA en personas'],
                    ],
                    [
                        'number'      => 2,
                        'title'       => 'Selección con IA: oportunidades y riesgos',
                        'description' => 'Cómo funcionan los filtros de CVs con IA, el análisis de entrevistas en video, los tests predictivos y qué puede salir mal en cada etapa.',
                        'lessons'     => ['Filtros de CV con IA: cómo funcionan', 'Entrevistas en video analizadas por IA', 'Tests predictivos y perfiles de personalidad IA', 'Qué puede salir mal: casos documentados'],
                    ],
                    [
                        'number'      => 3,
                        'title'       => 'Sesgos algorítmicos en contratación',
                        'description' => 'De dónde vienen los sesgos en sistemas de selección, cómo detectarlos, cómo mitigarlos y qué responsabilidad tiene RRHH cuando ocurren.',
                        'lessons'     => ['De dónde vienen los sesgos en IA de selección', 'Cómo detectar si tu sistema discrimina', 'Auditorías de sesgo: qué son y cómo pedirlas', 'Responsabilidad de RRHH ante discriminación algorítmica'],
                    ],
                    [
                        'number'      => 4,
                        'title'       => 'Monitoreo de empleados: límites legales y éticos',
                        'description' => 'Qué tecnologías de monitoreo existen, qué dice la ley en Chile sobre vigilancia laboral, y cómo construir una política de monitoreo que sea legal y no destruya el clima organizacional.',
                        'lessons'     => ['Tecnologías de monitoreo: qué existe hoy', 'Qué dice la ley chilena sobre vigilancia laboral', 'El equilibrio entre control y confianza', 'Construir una política de monitoreo aceptable'],
                    ],
                    [
                        'number'      => 5,
                        'title'       => 'Implementar IA en RRHH responsablemente',
                        'description' => 'Cómo evaluar y seleccionar proveedores, qué preguntar antes de contratar un sistema, cómo comunicarlo a los empleados y cómo prepararte para la regulación que viene.',
                        'lessons'     => ['Cómo evaluar proveedores de IA para RRHH', 'Qué preguntar antes de implementar un sistema', 'Comunicar el uso de IA a los empleados', 'Prepararse para la regulación que viene'],
                    ],
                ],
            ],

            [
                'slug'          => 'ia-para-salud',
                'title'         => 'IA para Profesionales de la Salud',
                'subtitle'      => 'Entender la IA clínica, proteger a tus pacientes y conocer tu responsabilidad',
                'badge'         => 'Salud',
                'badge_color'   => '#f43f5e',
                'icon'          => 'fa-heartbeat',
                'audience'      => 'Médicos, enfermeras, kinesiólogos, psicólogos, nutricionistas y estudiantes de ciencias de la salud',
                'level'         => 'Sin requisitos técnicos',
                'modules_count' => 5,
                'lessons_count' => 20,
                'status'        => 'disponible',
                'description'   => 'La IA ya detecta cánceres en imágenes, predice sepsis en UCI, asiste en diagnósticos radiológicos y lee electrocardiogramas. Estas herramientas llegan a los hospitales y clínicas con promesas enormes, pero también con riesgos reales: errores que parecen confiables, privacidad de datos clínicos en riesgo y preguntas sin respuesta sobre responsabilidad médica. Este curso te da las bases para evaluar estas herramientas con criterio clínico, no solo técnico.',
                'why_now'       => 'Chile tiene proyectos piloto de IA diagnóstica en hospitales públicos. Los fabricantes de equipos médicos ya integran IA en sus productos. Los profesionales de salud que no entiendan estas herramientas van a usarlas sin criterio o rechazarlas por miedo, en ambos casos perdiendo la oportunidad de mejorar la atención a sus pacientes.',
                'outcomes'      => [
                    'Entender cómo funcionan los principales sistemas de IA diagnóstica',
                    'Evaluar herramientas de IA clínica con criterio basado en evidencia',
                    'Conocer tus obligaciones sobre privacidad de datos clínicos en Chile',
                    'Saber cuándo confiar en una recomendación de IA y cuándo cuestionarla',
                    'Entender la responsabilidad médica cuando participa la IA',
                ],
                'modules' => [
                    [
                        'number'      => 1,
                        'title'       => 'IA en medicina: qué ya existe',
                        'description' => 'Panorama de los sistemas de IA clínica disponibles hoy, en qué especialidades están más avanzados y qué está llegando a los centros de salud en Chile.',
                        'lessons'     => ['IA diagnóstica: el estado del arte', 'IA en radiología, patología y cardiología', 'Sistemas predictivos en urgencias y UCI', 'IA clínica en Chile: proyectos y realidad'],
                    ],
                    [
                        'number'      => 2,
                        'title'       => 'IA diagnóstica: alcances y límites',
                        'description' => 'Cómo leer los estudios de validación de IA médica, qué significa "comparable a un especialista", cuándo la IA falla y por qué.',
                        'lessons'     => ['Cómo leer un estudio de validación de IA médica', 'Qué significa "comparable a un especialista"', 'Cuándo y cómo falla la IA diagnóstica', 'Sesgos en datos clínicos y sus consecuencias'],
                    ],
                    [
                        'number'      => 3,
                        'title'       => 'Privacidad y datos clínicos',
                        'description' => 'Qué pasa con los datos de tus pacientes cuando usan herramientas de IA, qué dice la ley chilena, y cómo proteger la privacidad sin renunciar a las herramientas.',
                        'lessons'     => ['Qué datos clínicos recopilan los sistemas de IA', 'Marco legal en Chile: protección de datos en salud', 'Consentimiento informado cuando hay IA', 'Cómo evaluar la privacidad de un proveedor de IA médica'],
                    ],
                    [
                        'number'      => 4,
                        'title'       => 'Responsabilidad médica y IA',
                        'description' => 'Quién es responsable cuando la IA falla, cómo documentar el uso de IA en la toma de decisiones clínicas, y casos reales de disputas legales.',
                        'lessons'     => ['¿Quién responde cuando la IA falla?', 'Cómo documentar el uso de IA en la historia clínica', 'Casos reales: disputas legales con IA médica', 'El debate sobre la autonomía de la IA en medicina'],
                    ],
                    [
                        'number'      => 5,
                        'title'       => 'Herramientas para tu especialidad',
                        'description' => 'Aplicaciones prácticas de IA por área clínica, cómo evaluar un sistema antes de adoptarlo y cómo integrar IA en tu flujo de trabajo sin perder el juicio clínico.',
                        'lessons'     => ['IA para médicos generalistas y de familia', 'IA en salud mental: oportunidades y límites', 'Cómo evaluar una herramienta de IA antes de usarla', 'Integrar IA en tu práctica sin perder el juicio clínico'],
                    ],
                ],
            ],

            [
                'slug'          => 'ia-para-pymes',
                'title'         => 'IA para Emprendedores y Pymes',
                'subtitle'      => 'Automatiza, compite y crece usando IA sin necesitar un equipo técnico',
                'badge'         => 'Negocios',
                'badge_color'   => '#f472b6',
                'icon'          => 'fa-store',
                'audience'      => 'Dueños de empresa, emprendedores, gerentes de pymes y startups en etapa temprana',
                'level'         => 'Sin requisitos técnicos',
                'modules_count' => 5,
                'lessons_count' => 20,
                'status'        => 'disponible',
                'description'   => 'Las grandes empresas llevan años automatizando con IA. Las pymes tienen ahora acceso a las mismas herramientas —muchas gratuitas o de bajo costo— pero sin equipos técnicos que las implementen. Este curso te enseña a identificar qué procesos de tu empresa se pueden automatizar, qué herramientas usar sin necesidad de programar, cómo ahorrar tiempo y costos reales, y cómo hacerlo cumpliendo las obligaciones legales que ya aplican en Chile.',
                'why_now'       => 'Las pymes que incorporen IA en los próximos 12 a 24 meses van a tener ventaja competitiva real frente a las que no lo hagan. No se trata de ciencia ficción: se trata de dejar de hacer manualmente lo que un sistema puede hacer en segundos, y dedicar ese tiempo a lo que tú haces mejor que cualquier algoritmo.',
                'outcomes'      => [
                    'Identificar los procesos de tu empresa que más se beneficiarían de IA',
                    'Usar herramientas de IA sin saber programar (atención al cliente, marketing, administración)',
                    'Estimar el ahorro real de tiempo y costo que puede generar la IA en tu caso específico',
                    'Conocer los riesgos legales de implementar IA con datos de clientes',
                    'Construir un plan de implementación de IA en 90 días para tu negocio',
                ],
                'modules' => [
                    [
                        'number'      => 1,
                        'title'       => 'Qué puede hacer la IA por tu empresa',
                        'description' => 'Casos reales de pymes que ya usan IA, qué tareas se automatizan más fácilmente y cómo estimar el retorno antes de invertir tiempo en implementar.',
                        'lessons'     => ['Casos reales: pymes chilenas que usan IA', 'Qué procesos se automatizan más fácilmente', 'Cómo estimar el retorno antes de implementar', 'Mitos sobre la IA en pequeñas empresas'],
                    ],
                    [
                        'number'      => 2,
                        'title'       => 'Automatizar sin programar',
                        'description' => 'Herramientas no técnicas para automatizar tareas de administración, comunicación y operaciones, con ejemplos concretos y costos reales.',
                        'lessons'     => ['Herramientas de automatización sin código', 'Automatizar emails, reportes y seguimientos', 'Integrar sistemas sin contratar un programador', 'Cuánto cuesta implementar IA en una pyme'],
                    ],
                    [
                        'number'      => 3,
                        'title'       => 'IA para atención al cliente y ventas',
                        'description' => 'Chatbots, respuestas automáticas, personalización de ofertas y análisis de conversaciones para mejorar la experiencia del cliente y aumentar las ventas.',
                        'lessons'     => ['Chatbots para pymes: qué funcionan y qué no', 'Automatizar la atención sin perder el trato humano', 'IA para personalizar ofertas y seguimientos', 'Analizar conversaciones con clientes con IA'],
                    ],
                    [
                        'number'      => 4,
                        'title'       => 'Datos, privacidad y cumplimiento',
                        'description' => 'Qué obligaciones legales aplican cuando usas datos de clientes con IA, cómo cumplir sin un equipo jurídico, y qué riesgos evitar.',
                        'lessons'     => ['Qué dice la ley chilena sobre datos de clientes', 'Obligaciones básicas cuando usas IA con datos', 'Qué no debes hacer con datos de clientes', 'Cómo protegerte legalmente sin un abogado de planta'],
                    ],
                    [
                        'number'      => 5,
                        'title'       => 'Tu plan de IA en 90 días',
                        'description' => 'Cómo priorizar, por dónde empezar, cómo medir si está funcionando y cómo escalar gradualmente sin caer en el error de sobreimplementar.',
                        'lessons'     => ['Cómo priorizar: qué automatizar primero', 'Los primeros 30 días: implementación mínima viable', 'Cómo medir si la IA está generando valor', 'Escalar gradualmente: el camino sostenible'],
                    ],
                ],
            ],
        ];
    }
}
