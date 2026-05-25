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
            'ia-para-derecho_1_1', 'ia-para-derecho_1_2', 'ia-para-derecho_1_3', 'ia-para-derecho_1_4',
            'ia-para-derecho_2_1', 'ia-para-derecho_2_2', 'ia-para-derecho_2_3', 'ia-para-derecho_2_4',
            'ia-para-derecho_3_1', 'ia-para-derecho_3_2', 'ia-para-derecho_3_3', 'ia-para-derecho_3_4',
            'ia-para-derecho_4_1', 'ia-para-derecho_4_2', 'ia-para-derecho_4_3', 'ia-para-derecho_4_4',
            'ia-para-derecho_5_1', 'ia-para-derecho_5_2', 'ia-para-derecho_5_3', 'ia-para-derecho_5_4',
            'ia-para-docentes_1_1', 'ia-para-docentes_1_2', 'ia-para-docentes_1_3', 'ia-para-docentes_1_4',
            'ia-para-docentes_2_1', 'ia-para-docentes_2_2', 'ia-para-docentes_2_3', 'ia-para-docentes_2_4',
            'ia-para-docentes_3_1', 'ia-para-docentes_3_2', 'ia-para-docentes_3_3', 'ia-para-docentes_3_4',
            'ia-para-docentes_4_1', 'ia-para-docentes_4_2', 'ia-para-docentes_4_3', 'ia-para-docentes_4_4',
            'ia-para-docentes_5_1', 'ia-para-docentes_5_2', 'ia-para-docentes_5_3', 'ia-para-docentes_5_4',
            'ia-para-periodistas_1_1', 'ia-para-periodistas_1_2', 'ia-para-periodistas_1_3', 'ia-para-periodistas_1_4',
            'ia-para-periodistas_2_1', 'ia-para-periodistas_2_2', 'ia-para-periodistas_2_3', 'ia-para-periodistas_2_4',
            'ia-para-periodistas_3_1', 'ia-para-periodistas_3_2', 'ia-para-periodistas_3_3', 'ia-para-periodistas_3_4',
            'ia-para-periodistas_4_1', 'ia-para-periodistas_4_2', 'ia-para-periodistas_4_3', 'ia-para-periodistas_4_4',
            'ia-para-periodistas_5_1', 'ia-para-periodistas_5_2', 'ia-para-periodistas_5_3', 'ia-para-periodistas_5_4',
            'ia-para-rrhh_1_1', 'ia-para-rrhh_1_2', 'ia-para-rrhh_1_3', 'ia-para-rrhh_1_4',
            'ia-para-rrhh_2_1', 'ia-para-rrhh_2_2', 'ia-para-rrhh_2_3', 'ia-para-rrhh_2_4',
            'ia-para-rrhh_3_1', 'ia-para-rrhh_3_2', 'ia-para-rrhh_3_3', 'ia-para-rrhh_3_4',
            'ia-para-rrhh_4_1', 'ia-para-rrhh_4_2', 'ia-para-rrhh_4_3', 'ia-para-rrhh_4_4',
            'ia-para-rrhh_5_1', 'ia-para-rrhh_5_2', 'ia-para-rrhh_5_3', 'ia-para-rrhh_5_4',
            'ia-para-salud_1_1', 'ia-para-salud_1_2', 'ia-para-salud_1_3', 'ia-para-salud_1_4',
            'ia-para-salud_2_1', 'ia-para-salud_2_2', 'ia-para-salud_2_3', 'ia-para-salud_2_4',
            'ia-para-salud_3_1', 'ia-para-salud_3_2', 'ia-para-salud_3_3', 'ia-para-salud_3_4',
            'ia-para-salud_4_1', 'ia-para-salud_4_2', 'ia-para-salud_4_3', 'ia-para-salud_4_4',
            'ia-para-salud_5_1', 'ia-para-salud_5_2', 'ia-para-salud_5_3', 'ia-para-salud_5_4',
            'ia-para-pymes_1_1', 'ia-para-pymes_1_2', 'ia-para-pymes_1_3', 'ia-para-pymes_1_4',
            'ia-para-pymes_2_1', 'ia-para-pymes_2_2', 'ia-para-pymes_2_3', 'ia-para-pymes_2_4',
            'ia-para-pymes_3_1', 'ia-para-pymes_3_2', 'ia-para-pymes_3_3', 'ia-para-pymes_3_4',
            'ia-para-pymes_4_1', 'ia-para-pymes_4_2', 'ia-para-pymes_4_3', 'ia-para-pymes_4_4',
            'ia-para-pymes_5_1', 'ia-para-pymes_5_2', 'ia-para-pymes_5_3', 'ia-para-pymes_5_4',
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


            // ── DERECHO · MÓDULO 2 ──────────────────────────────────────────

            'ia-para-derecho_2_1' => <<<HTML
<p>La revisión automática de contratos no es magia: es procesamiento de lenguaje natural (NLP) aplicado a documentos legales. Entender cómo funciona ayuda a saber qué esperar de ella, cuándo confiar en sus resultados y cuándo verificar de forma independiente.</p>

<h2>El proceso en tres pasos</h2>
<p><strong>Paso 1: Ingesta y parsing.</strong> El documento se carga al sistema —PDF, Word, o directo desde el data room— y se convierte en texto estructurado. El sistema identifica la jerarquía del documento: cláusulas, subcláusulas, definiciones, anexos. Esta etapa parece trivial pero importa: un PDF escaneado mal digitalizado puede producir errores en todo lo que sigue.</p>
<p><strong>Paso 2: Extracción y clasificación.</strong> El modelo busca cláusulas específicas según una lista predefinida: cambio de control, limitación de responsabilidad, confidencialidad, jurisdicción, plazos, penalidades, indemnización, terminación. Para cada cláusula encontrada, extrae el texto relevante y lo clasifica. Los modelos más avanzados también asignan un nivel de riesgo basado en cómo se redacta la cláusula respecto a un estándar de mercado.</p>
<p><strong>Paso 3: Reporte y revisión humana.</strong> El sistema entrega un resumen estructurado: qué cláusulas encontró, cuáles faltan, cuáles tienen redacción inusual o riesgosa. El abogado revisa este reporte —no el documento desde cero— y decide qué merece atención profunda. Esto es lo que genera el ahorro de tiempo real.</p>

<div style="background:#faf5ff;border:1px solid #e9d5ff;border-left:4px solid #a78bfa;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#6d28d9;">¿Qué tan preciso es?</p>
    <p style="color:#6d28d9;margin:0;font-size:.93rem;">Los sistemas maduros alcanzan tasas de extracción del 90-95% para cláusulas estándar en contratos en inglés. En español y en contratos atípicos, los números bajan. Ningún sistema es 100% preciso, razón por la cual el paso de revisión humana no es opcional: es parte del flujo de trabajo diseñado.</p>
</div>

<h2>Qué cambia en la práctica</h2>
<p>En un proceso de due diligence tradicional, un equipo de abogados junior puede revisar 50-100 contratos por día. Con IA, el mismo equipo puede cubrir 500-1.000. No porque el trabajo desaparezca, sino porque el tiempo se redirige: en lugar de leer cada cláusula, el abogado analiza las excepciones y toma decisiones sobre los hallazgos que el sistema ya identificó.</p>
<p>El resultado es que las firmas pueden ofrecer due diligence más exhaustiva en menos tiempo y a menor costo. Eso cambia las expectativas de los clientes y, eventualmente, los honorarios que se pueden cobrar por un trabajo que antes tomaba semanas.</p>
HTML,

            'ia-para-derecho_2_2' => <<<HTML
<p>El mercado de herramientas de IA para revisión de contratos creció rápidamente y hay diferencias importantes entre productos. Conocer las opciones principales te permite evaluar cuál se adapta mejor a tu tipo de práctica antes de adoptar cualquiera.</p>

<h2>Harvey</h2>
<p>Es el sistema de IA legal de mayor perfil hoy. Construido sobre modelos de lenguaje grandes (inicialmente GPT-4, ahora modelos propios combinados), Harvey está diseñado para el trabajo legal generalista: revisión de contratos, investigación jurídica, redacción de documentos, análisis de due diligence. A diferencia de herramientas más especializadas, Harvey funciona como un asistente conversacional: le haces preguntas en lenguaje natural y responde con razonamiento legal.</p>
<p>Su adopción por Allen & Overy, Paul Weiss y Milbank lo convirtió en el estándar de referencia para firmas grandes. Está orientado a empresas: no tiene versión libre ni precios publicados, y se vende a través de acuerdos corporativos.</p>

<h2>Kira Systems (ahora parte de Litera)</h2>
<p>Kira es más especializada: fue diseñada específicamente para extracción de cláusulas en contratos. Su punto fuerte es la precisión en contratos estándar y la posibilidad de entrenar el modelo en cláusulas específicas de tu práctica. Es la herramienta de referencia para due diligence en M&A y revisión de portfolios de contratos. La adquirió Litera en 2022, lo que la integró en una suite más amplia de tecnología legal.</p>

<h2>ContractPodAi</h2>
<p>Se orienta a la gestión del ciclo de vida completo de contratos (CLM), no solo a la revisión. Útil para departamentos legales corporativos que quieren automatizar desde la solicitud hasta el archivo de contratos. Incluye funciones de IA para extracción, análisis de riesgo y alertas de renovación.</p>

<div style="background:#f0f9ff;border:1px solid #bae6fd;border-left:4px solid #38b6ff;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#0369a1;">Cómo elegir</p>
    <ul style="color:#0369a1;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li><strong>Si haces due diligence M&A a escala:</strong> Kira o Luminance</li>
        <li><strong>Si buscas un asistente legal general:</strong> Harvey o equivalentes</li>
        <li><strong>Si gestionas contratos corporativos internos:</strong> ContractPodAi o Ironclad</li>
        <li><strong>Si investigas jurisprudencia:</strong> Lexis+ IA o Westlaw Precision</li>
        <li><strong>Si quieres empezar con poco presupuesto:</strong> ChatGPT Enterprise con prompts estructurados (menos preciso, pero accesible)</li>
    </ul>
</div>

<h2>Antes de adoptar cualquier herramienta</h2>
<p>Tres preguntas imprescindibles para cualquier proveedor: (1) ¿Cómo se tratan los datos de mis clientes — se usan para entrenar el modelo? (2) ¿Dónde se almacenan los datos y bajo qué legislación? (3) ¿Qué garantías contractuales de confidencialidad ofrecen? Si el proveedor no responde estas preguntas con claridad, es una señal de alerta.</p>
HTML,

            'ia-para-derecho_2_3' => <<<HTML
<p>Uno de los riesgos más subestimados al implementar IA en revisión de contratos es confiar en que el sistema encontró todo lo relevante. Los sistemas actuales son poderosos, pero tienen puntos ciegos consistentes que todo abogado que los use debe conocer.</p>

<h2>Ambigüedad que requiere contexto</h2>
<p>La IA extrae texto. No entiende el contexto de la negociación, la historia de la relación entre las partes, ni las intenciones no escritas que un abogado experimentado detectaría. Una cláusula que parece estándar puede ser problemática dado el contexto específico del cliente — y ese contexto la IA no lo tiene.</p>
<p>Ejemplo: una cláusula de confidencialidad que excluye "información públicamente disponible" puede ser perfectamente razonable en un contrato comercial pero extremadamente problemática si el cliente opera en un sector donde hay filtraciones frecuentes de información que termina siendo "pública" sin autorización. La IA marca la cláusula como estándar. El abogado con contexto la marcaría como riesgosa.</p>

<h2>Inconsistencias entre documentos</h2>
<p>En un proceso de M&A, los riesgos no siempre están dentro de un contrato: están en las tensiones entre contratos. Un contrato de arrendamiento que prohíbe el cambio de control puede invalidar una compraventa que otro contrato asume como ejecutable. Detectar estas inconsistencias entre cientos de documentos requiere un nivel de síntesis que los sistemas actuales hacen mal.</p>

<h2>El problema de la confianza excesiva</h2>
<p>Quizás el riesgo más grave no es lo que la IA no encuentra, sino la falsa confianza que genera. Un reporte de due diligence asistido por IA que dice "no se identificaron cláusulas de cambio de control" lleva al lector a asumir que revisó bien. Si el sistema lo pasó por alto, la responsabilidad sigue siendo del abogado que firmó el trabajo.</p>

<div style="background:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #f43f5e;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#9f1239;">Los puntos ciegos más comunes</p>
    <ul style="color:#9f1239;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>Cláusulas en idiomas distintos al idioma principal del contrato</li>
        <li>Obligaciones incorporadas por referencia a otros documentos</li>
        <li>Anexos o schedules que modifican el cuerpo principal</li>
        <li>Redacción ambigua que la IA clasifica como estándar por su forma, no por su efecto</li>
        <li>Riesgos que dependen de hechos externos al contrato</li>
    </ul>
</div>

<h2>El protocolo correcto</h2>
<p>La IA debe usarse para identificar dónde mirar, no para concluir que ya se miró todo. El flujo correcto es: IA identifica y prioriza → abogado revisa los hallazgos de alto riesgo con profundidad → abogado hace un muestreo adicional de lo que la IA marcó como sin riesgo. Este último paso es el que más se omite y el más importante para mantener la calidad del trabajo.</p>
HTML,

            'ia-para-derecho_2_4' => <<<HTML
<p>Cuando la IA comete un error en un trabajo legal — y tarde o temprano lo hará — la pregunta central es: ¿quién responde? La respuesta bajo el derecho actual es clara: el abogado. El software no tiene responsabilidad profesional. El profesional que lo usó, sí.</p>

<h2>El deber de competencia tecnológica</h2>
<p>Las reglas éticas de la profesión legal en la mayoría de los países exigen que el abogado mantenga competencia en su área de práctica. En EE.UU., el Comentario 8 de la Regla 1.1 del ABA Model Rules establece explícitamente que esa competencia incluye "los beneficios y riesgos de la tecnología relevante". En Chile, el Código de Ética del Colegio de Abogados no menciona la IA directamente, pero el deber de diligencia profesional abarca el uso adecuado de todas las herramientas disponibles.</p>
<p>La implicancia práctica es que un abogado no puede exculparse diciendo "la herramienta me lo dijo". Si la herramienta era inadecuada para la tarea, si no se verificó su output o si el abogado no tenía suficiente conocimiento para supervisarla, la responsabilidad es del abogado.</p>

<h2>Documentación: tu mejor protección</h2>
<p>Cuando usas IA en un trabajo, documenta: qué sistema usaste, en qué etapa del trabajo, qué verificación hiciste del output. No porque esto elimine tu responsabilidad, sino porque en caso de disputa demuestra que el proceso fue razonable y supervisado. Un proceso bien documentado es evidencia de diligencia profesional.</p>

<div style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#92400e;">Lo que debes documentar</p>
    <ul style="color:#78350f;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>Nombre y versión de la herramienta de IA utilizada</li>
        <li>Fecha y tipo de tarea realizada con IA</li>
        <li>Qué verificación adicional realizaste sobre el output</li>
        <li>Si informaste al cliente del uso de IA (y su consentimiento)</li>
        <li>Cualquier limitación del sistema que afectó el alcance del trabajo</li>
    </ul>
</div>

<h2>¿Y el proveedor del software?</h2>
<p>Los contratos de licencia de herramientas de IA legal casi universalmente excluyen la responsabilidad del proveedor por errores en el output. Harvey, Kira, y todos los demás tienen cláusulas que hacen recaer la responsabilidad del uso en el cliente. Esto no va a cambiar pronto. La regulación de IA en la UE (AI Act) introduce algunas obligaciones de transparencia, pero la responsabilidad profesional del abogado sigue siendo del abogado.</p>
<p>Conclusión práctica: usa IA con el mismo rigor con que usarías cualquier otra herramienta de trabajo. Si delegarías una tarea a un practicante, necesitas supervisarla. Si la delegas a una IA, también.</p>
HTML,

            // ── DERECHO · MÓDULO 3 ──────────────────────────────────────────

            'ia-para-derecho_3_1' => <<<HTML
<p>El AI Act europeo, promulgado en 2024 y aplicable de forma escalonada hasta 2027, es la primera regulación integral de inteligencia artificial del mundo. Para los abogados, entenderlo es urgente por dos razones: los sistemas de IA que usan en su práctica pueden estar sujetos a él, y sus clientes van a necesitar asesoría sobre cómo cumplirlo.</p>

<h2>La lógica del enfoque por riesgo</h2>
<p>El AI Act no regula toda la IA de la misma manera. Clasifica los sistemas según el riesgo que representan y establece obligaciones proporcionales. Cuatro categorías:</p>
<ul>
<li><strong>Riesgo inaceptable:</strong> prohibidos directamente. Incluyen sistemas de puntuación social por parte de gobiernos, manipulación subliminal, identificación biométrica en tiempo real en espacios públicos (con excepciones).</li>
<li><strong>Riesgo alto:</strong> permitidos pero con requisitos estrictos. Aquí están los sistemas de IA en el ámbito de justicia, recursos humanos, crédito y servicios esenciales.</li>
<li><strong>Riesgo limitado:</strong> obligaciones de transparencia. Chatbots deben identificarse como IA.</li>
<li><strong>Riesgo mínimo:</strong> sin restricciones específicas. La mayoría de las aplicaciones de IA caen aquí.</li>
</ul>

<h2>Los sistemas legales son de "alto riesgo"</h2>
<p>El Anexo III del AI Act incluye en la categoría de alto riesgo los sistemas de IA "utilizados en la administración de justicia y procesos democráticos". Específicamente: sistemas que asisten a autoridades judiciales en la investigación y resolución de disputas, y sistemas de predicción de riesgos en personas acusadas.</p>

<div style="background:#faf5ff;border:1px solid #e9d5ff;border-left:4px solid #a78bfa;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#6d28d9;">Obligaciones para sistemas de alto riesgo</p>
    <ul style="color:#6d28d9;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li><strong>Sistema de gestión de riesgos</strong> durante todo el ciclo de vida</li>
        <li><strong>Datos de entrenamiento de calidad</strong>, representativos y sin sesgos no intencionados</li>
        <li><strong>Documentación técnica</strong> detallada del sistema</li>
        <li><strong>Supervisión humana</strong> efectiva — el sistema no puede tomar decisiones finales autónomamente</li>
        <li><strong>Exactitud, robustez y ciberseguridad</strong> verificadas</li>
        <li><strong>Registro en base de datos de la UE</strong> antes de comercializarse</li>
    </ul>
</div>

<h2>Implicancias para abogados en Chile</h2>
<p>Si trabajas con clientes europeos o en transacciones que involucran la UE, el AI Act ya te afecta. Si tu firma usa herramientas de IA desarrolladas por empresas europeas o que procesan datos de ciudadanos europeos, también. Para los demás, el AI Act es la señal más clara de hacia dónde va la regulación global: Chile y otros países latinoamericanos van a seguir este modelo. Entenderlo hoy es prepararse para asesorar bien mañana.</p>
HTML,

            'ia-para-derecho_3_2' => <<<HTML
<p>Que una IA ayude a un juez a decidir una sentencia no es ciencia ficción: es una realidad en varios países, y un debate muy activo en casi todos los demás. Para el abogado, este debate importa porque eventualmente afectará cómo se preparan los argumentos, qué recursos son relevantes y cómo se protegen los derechos de los clientes.</p>

<h2>Qué existe hoy</h2>
<p><strong>EE.UU.:</strong> COMPAS (Correctional Offender Management Profiling for Alternative Sanctions) es el caso más documentado. Se usa para predecir la probabilidad de reincidencia en decisiones de libertad condicional y sentencia. En 2016, ProPublica publicó un análisis mostrando que el sistema clasificaba incorrectamente a personas negras como de alto riesgo a una tasa dos veces mayor que a personas blancas. El debate sobre su uso continúa en varios estados.</p>
<p><strong>Estonia:</strong> desarrolló un prototipo de "juez robótico" para causas menores (disputas de menos de 7.000 euros). El sistema toma una decisión automatizada que cualquiera de las partes puede apelar ante un juez humano. En 2019 fue piloteado; el debate sobre escalarlo sigue abierto.</p>
<p><strong>China:</strong> los tribunales chinos usan sistemas de IA para análisis de casos y asistencia en redacción de sentencias. La transparencia sobre el alcance de la autonomía de estos sistemas es limitada.</p>

<div style="background:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #f43f5e;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#9f1239;">Los problemas del fondo</p>
    <ul style="color:#9f1239;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li><strong>Opacidad:</strong> el acusado o su abogado no pueden interrogar al algoritmo como interrogarían a un perito.</li>
        <li><strong>Sesgos históricos:</strong> si los datos de entrenamiento reflejan discriminación pasada, el sistema la replica.</li>
        <li><strong>Dificultad de apelación:</strong> "el algoritmo lo dijo" no es un fundamento recurrible de la misma forma que un razonamiento jurídico explícito.</li>
        <li><strong>Responsabilidad difusa:</strong> si la IA influye en una sentencia injusta, ¿quién responde?</li>
    </ul>
</div>

<h2>Chile y el poder judicial</h2>
<p>El Poder Judicial chileno ha avanzado en digitalización pero no en IA decisional. La Corte Suprema y los tribunales inferiores usan sistemas de gestión de casos y búsqueda de jurisprudencia, pero no sistemas que asistan en la decisión de fondo. El debate sobre si hacerlo está en etapa académica y de política pública. Como abogado, vale la pena seguirlo: los sistemas que se adopten en los próximos años van a afectar cómo se litiga.</p>
HTML,

            'ia-para-derecho_3_3' => <<<HTML
<p>Chile tiene hoy una de las leyes de protección de datos más antiguas de América Latina: la Ley N° 19.628, promulgada en 1999. Fue diseñada para un mundo sin redes sociales, sin smartphones y sin IA generativa. Su actualización lleva años en tramitación y el proyecto que está más avanzado introduce cambios fundamentales que los abogados deben conocer.</p>

<h2>El problema con la ley actual</h2>
<p>La Ley 19.628 tiene limitaciones severas para el contexto actual: no contempla el concepto de "responsable del tratamiento" con las obligaciones modernas, no regula la transferencia internacional de datos de forma robusta, no establece una autoridad de control con facultades sancionatorias reales, y no aborda decisiones automatizadas. Es estructuralmente incapaz de regular el uso de datos personales en sistemas de IA.</p>

<h2>El proyecto en tramitación</h2>
<p>El nuevo proyecto de ley (Boletín 11144-07 y sus modificaciones) introduce un modelo similar al GDPR europeo. Los elementos más relevantes:</p>

<div style="background:#faf5ff;border:1px solid #e9d5ff;border-left:4px solid #a78bfa;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#6d28d9;">Principales novedades del proyecto</p>
    <ul style="color:#6d28d9;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li><strong>Agencia de Protección de Datos Personales:</strong> entidad autónoma con facultades fiscalizadoras y sancionatorias (multas de hasta 20.000 UTM).</li>
        <li><strong>Bases de licitud ampliadas:</strong> consentimiento, interés legítimo, contrato, obligación legal.</li>
        <li><strong>Derechos ARCO ampliados:</strong> acceso, rectificación, cancelación, oposición y nuevos derechos de portabilidad y oposición a decisiones automatizadas.</li>
        <li><strong>Transferencia internacional:</strong> restricciones para países sin nivel adecuado de protección.</li>
        <li><strong>Delegado de Protección de Datos:</strong> obligatorio para ciertos responsables de tratamiento.</li>
    </ul>
</div>

<h2>Implicancias para tu práctica</h2>
<p>Cuando la ley entre en vigor — y está cerca — varias cosas cambian para los despachos legales. Primero, los propios abogados son responsables del tratamiento de los datos personales de sus clientes: expedientes, comunicaciones, información incluida en los encargos. Segundo, las herramientas de IA que usen para procesar esa información van a requerir análisis de cumplimiento. Tercero, la asesoría en protección de datos va a ser un área de práctica de alta demanda.</p>
<p>El mejor momento para prepararse es antes de que la ley esté vigente. Los clientes que lleguen con problemas de cumplimiento después de la promulgación van a necesitar abogados que ya entiendan bien el marco.</p>
HTML,

            'ia-para-derecho_3_4' => <<<HTML
<p>Cada vez más clientes llegan con preguntas sobre IA: quieren implementar un chatbot de atención al cliente, usar IA para tomar decisiones de crédito, automatizar la revisión de documentos o simplemente entender qué riesgos tienen con las herramientas que ya usan. Asesorar bien en este contexto requiere un marco de análisis claro.</p>

<h2>Los escenarios más comunes</h2>
<p><strong>Cliente que quiere implementar IA en su empresa.</strong> Las preguntas relevantes: ¿qué datos va a usar el sistema y de quién son? ¿Está procesando datos personales de chilenos o europeos? ¿El sistema toma decisiones que afectan a personas? ¿Hay riesgo de discriminación algorítmica? ¿Qué pasa si el sistema falla?</p>
<p><strong>Cliente que va a contratar un servicio de IA a un tercero.</strong> El contrato de servicio de IA tiene cláusulas críticas que hay que revisar: propiedad de los datos, qué puede hacer el proveedor con ellos, qué garantías de confidencialidad existen, quién responde si el sistema causa daños, qué auditorías son posibles.</p>
<p><strong>Cliente que recibió una decisión automatizada que lo perjudica.</strong> ¿Tiene derecho a explicación? ¿Puede impugnar? ¿Hay responsabilidad del operador del sistema? Estas son preguntas de derecho que todavía están evolucionando en Chile.</p>

<div style="background:#f0f9ff;border:1px solid #bae6fd;border-left:4px solid #38b6ff;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#0369a1;">Preguntas de due diligence para contratos de IA</p>
    <ul style="color:#0369a1;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>¿Qué datos personales procesa el sistema y bajo qué base de licitud?</li>
        <li>¿Dónde se almacenan los datos y se transfieren internacionalmente?</li>
        <li>¿El proveedor usa los datos del cliente para entrenar sus modelos?</li>
        <li>¿Qué mecanismos de auditoría e inspección están disponibles?</li>
        <li>¿Cómo se maneja un incidente de seguridad o una brecha de datos?</li>
        <li>¿Qué garantías de exactitud ofrece el proveedor y cuáles excluye?</li>
        <li>¿Qué cláusula de responsabilidad aplica si el sistema causa daño a terceros?</li>
    </ul>
</div>

<h2>La oportunidad de práctica</h2>
<p>La asesoría en IA es una práctica emergente que todavía tiene poco expertise consolidado en Chile. Los abogados que desarrollen conocimiento en la intersección entre tecnología, privacidad de datos y responsabilidad civil van a tener ventaja de posicionamiento en los próximos años. No hace falta ser un experto técnico: hace falta entender el suficiente marco tecnológico para hacer las preguntas correctas y traducir los riesgos en términos legales que los clientes entiendan.</p>
HTML,

            // ── DERECHO · MÓDULO 4 ──────────────────────────────────────────

            'ia-para-derecho_4_1' => <<<HTML
<p>El secreto profesional del abogado es uno de los pilares del ejercicio legal. Cuando empiezas a usar herramientas de IA con información de tus clientes, ese pilar enfrenta riesgos nuevos que no existían hace cinco años. Ignorarlos no los hace desaparecer.</p>

<h2>Qué pasa con la información que ingresas a una IA</h2>
<p>Cuando usas ChatGPT en su versión gratuita o standard para redactar un escrito o analizar un contrato, la información que ingresas puede ser usada por OpenAI para mejorar sus modelos. Eso está en los términos de servicio. La mayoría de los abogados no los leyeron, y la mayoría de los que los leyeron no cambiaron su comportamiento.</p>
<p>En versiones empresariales (ChatGPT Enterprise, Azure OpenAI con acuerdo corporativo), los datos del cliente están contractualmente protegidos: no se usan para entrenamiento, se almacenan en entornos aislados y el proveedor asume obligaciones de confidencialidad. La diferencia entre el servicio gratuito y el empresarial no es solo de funcionalidades: es de marco jurídico.</p>

<div style="background:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #f43f5e;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#9f1239;">Nunca ingreses esto en una IA de uso general</p>
    <ul style="color:#9f1239;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>Datos identificatorios del cliente (nombre, RUT, domicilio)</li>
        <li>Hechos confidenciales del caso tal como los relató el cliente</li>
        <li>Estrategia procesal o negocial</li>
        <li>Documentos firmados o en proceso de firma</li>
        <li>Comunicaciones privilegiadas cliente-abogado</li>
    </ul>
</div>

<h2>El secreto profesional en la nube</h2>
<p>El problema no es nuevo: existe desde que los abogados empezaron a usar servicios de correo electrónico, almacenamiento en la nube y software de gestión de casos. La lógica regulatoria establece que el abogado debe tomar medidas razonables para proteger la confidencialidad, incluyendo evaluar los sistemas que usa. Eso mismo aplica a la IA.</p>

<h2>Protocolo de uso seguro</h2>
<p>Un protocolo mínimo razonable: (1) anonimiza los documentos antes de subirlos a herramientas de IA (reemplaza nombres por "Cliente A", "Empresa B"); (2) usa versiones empresariales o locales para trabajo sensible; (3) revisa las políticas de privacidad de cada herramienta que adoptes; (4) informa a tu cliente si usas IA en su asunto y obtén su consentimiento. Este último punto todavía no está regulado en Chile, pero es una práctica de ética que se está convirtiendo en estándar en otros mercados.</p>
HTML,

            'ia-para-derecho_4_2' => <<<HTML
<p>Los sistemas de IA aprenden de datos históricos. Si esos datos reflejan desigualdades — en acceso a la justicia, en cómo los tribunales trataron a distintos grupos, en qué casos llegaron a ser registrados — el sistema aprende esas desigualdades y las replica. Para el abogado, esto tiene consecuencias directas.</p>

<h2>El caso COMPAS: la referencia obligada</h2>
<p>COMPAS es un sistema de evaluación de riesgos usado en el sistema penal de varios estados de EE.UU. para informar decisiones de libertad condicional, fianza y sentencia. En 2016, ProPublica analizó 7.000 casos en Florida y encontró que el sistema clasificaba incorrectamente a acusados negros como de alto riesgo el doble de veces que a acusados blancos con perfiles similares.</p>
<p>La empresa desarrolladora (Equivant) rechazó la metodología de ProPublica. El debate académico sobre cómo medir el sesgo en estos sistemas continúa. Pero el punto importante no es quién tiene razón metodológicamente: es que el sistema produce resultados dispares por raza y eso es suficiente para que sea un problema de justicia.</p>

<h2>¿Cómo entra el sesgo?</h2>
<p>El sesgo en sistemas legales de IA no suele ser intencional: surge de los datos. Si históricamente ciertos grupos fueron más arrestados, más procesados, más condenados — aunque no sean más culpables — los datos reflejan ese patrón y el modelo lo aprende como predictivo. El sistema no discrimina por diseño: discrimina porque los datos que lo entrenaron describen un sistema que discriminó.</p>

<div style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#92400e;">Lo que el abogado defensor necesita saber</p>
    <ul style="color:#78350f;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>Tienes derecho a saber si un sistema de IA influyó en decisiones del fiscal o del tribunal en el caso de tu cliente.</li>
        <li>Puedes solicitar la metodología del sistema como parte del descubrimiento probatorio.</li>
        <li>El argumento de "opacidad del algoritmo" puede sustentar recursos de nulidad en jurisdicciones que reconocen el derecho a conocer la base de las decisiones.</li>
        <li>Existen peritos especializados en auditoría de sistemas de IA que pueden apoyar un caso de este tipo.</li>
    </ul>
</div>

<h2>Chile y el futuro</h2>
<p>Chile no usa actualmente sistemas de predicción de reincidencia como COMPAS en su sistema penal. Pero el debate sobre digitalización del poder judicial está avanzando. Los abogados que entiendan hoy los problemas de sesgo algorítmico van a estar mejor posicionados para defender a sus clientes cuando estos sistemas lleguen, y para participar en el debate regulatorio sobre cómo deben diseñarse.</p>
HTML,

            'ia-para-derecho_4_3' => <<<HTML
<p>Los errores de IA en la práctica legal ya han producido consecuencias disciplinarias reales. Conocer estos casos es la mejor vacuna contra repetirlos.</p>

<h2>Mata v. Avianca: el caso que lo cambió todo</h2>
<p>En junio de 2023, el juez federal Kevin Castel de la Corte del Distrito Sur de Nueva York sancionó a dos abogados —Steven Schwartz y Peter LoDuca— por presentar escritos que citaban jurisprudencia inexistente. Los casos habían sido "investigados" con ChatGPT, que los inventó con nombres, tribunales, años y citas plausibles pero completamente ficticios. Los abogados no los verificaron en Westlaw ni Lexis antes de presentarlos.</p>
<p>La sanción incluyó multas y la publicación de la resolución — una de las peores consecuencias de reputación posibles. El juez fue explícito: "La firma de un escrito por un abogado certifica que, en la medida de su conocimiento y creencia razonable, las alegaciones fácticas tienen respaldo probatorio y las argumentaciones legales tienen sustento en la ley existente o en un argumento no frívolo para su modificación."</p>

<div style="background:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #f43f5e;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#9f1239;">Qué salió mal en Mata v. Avianca</p>
    <ul style="color:#9f1239;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>Usaron ChatGPT (versión pública) para investigación jurisprudencial</li>
        <li>No verificaron las citas en fuentes primarias</li>
        <li>Cuando la contraparte cuestionó las citas, le preguntaron a ChatGPT si eran reales — y ChatGPT confirmó que sí</li>
        <li>No informaron a la corte del error cuando lo descubrieron internamente</li>
    </ul>
</div>

<h2>Otros casos documentados</h2>
<p>El caso de Mata v. Avianca fue el más resonado, pero no el único. En el Reino Unido, el Solicitors Regulation Authority documentó múltiples casos de abogados que presentaron jurisprudencia inventada por IA. En Australia y Canadá hay casos similares. La tendencia es clara: a medida que más abogados usan IA para investigación, más ocurren estos errores.</p>

<h2>La regla que no falla</h2>
<p>Cualquier cita jurisprudencial generada por IA debe verificarse en la fuente original antes de ser usada en un documento judicial o extrajudicial. No existe atajos válidos aquí. Las herramientas especializadas en investigación legal (Lexis+ IA, Westlaw Precision) tienen mecanismos de verificación integrados precisamente porque los proveedores generales (ChatGPT, Claude, Gemini) no están diseñados para garantizar la existencia de las fuentes que citan.</p>
HTML,

            'ia-para-derecho_4_4' => <<<HTML
<p>La ética profesional del abogado no se diseñó pensando en IA. Pero sus principios fundamentales — competencia, diligencia, confidencialidad, transparencia con el cliente, independencia — son perfectamente aplicables al uso de estas herramientas. Lo que hace falta es traducirlos.</p>

<h2>El deber de competencia</h2>
<p>No saber que existe una herramienta relevante para tu práctica puede ser una falla de competencia. No saber usarla adecuadamente, también. A medida que la IA se convierte en parte del estándar de la industria legal, los colegios de abogados van a ir precisando estas obligaciones. El ABA en EE.UU. ya lo hizo. En Chile todavía no hay guías formales del Colegio de Abogados, pero la interpretación razonable del deber de diligencia incluye mantenerse al día con las herramientas disponibles.</p>

<h2>Transparencia con el cliente</h2>
<p>¿Debes decirle a tu cliente que usaste IA en su asunto? Esta pregunta no tiene una respuesta legal definitiva en Chile hoy. Pero hay argumentos éticos fuertes a favor de la transparencia: el cliente tiene derecho a saber cómo se trabaja su caso, especialmente cuando eso involucra tecnología que procesa su información confidencial.</p>
<p>La tendencia global es hacia exigir esta transparencia. Algunos colegios en EE.UU. y Europa ya tienen guías que recomiendan o exigen informar al cliente. En Chile conviene adoptar esta práctica ahora, antes de que sea obligatoria: genera confianza y evita conflictos futuros.</p>

<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #00c896;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#166534;">Principios éticos para el uso de IA en derecho</p>
    <ul style="color:#166534;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li><strong>Supervisión efectiva:</strong> tú eres responsable del output, no la IA. Revisa siempre.</li>
        <li><strong>Confidencialidad activa:</strong> elige herramientas que garanticen protección de datos de clientes.</li>
        <li><strong>Transparencia con el cliente:</strong> informa del uso de IA cuando sea relevante para su asunto.</li>
        <li><strong>Verificación de fuentes:</strong> nunca uses jurisprudencia o doctrina generada por IA sin verificarla.</li>
        <li><strong>Actualización continua:</strong> la tecnología cambia rápido; el deber de competencia exige mantenerse al día.</li>
    </ul>
</div>

<h2>La independencia profesional</h2>
<p>Un riesgo más sutil: la IA puede influir en el juicio profesional si se le da demasiada autoridad. Si tu análisis legal comienza con "la IA dice que...", estás subvirtiendo el proceso. La IA debe ser insumo, no conclusión. El juicio legal sigue siendo tuyo: eso es lo que el cliente contrata y lo que la ética exige.</p>
HTML,

            // ── DERECHO · MÓDULO 5 ──────────────────────────────────────────

            'ia-para-derecho_5_1' => <<<HTML
<p>La investigación jurisprudencial es una de las tareas donde la IA aporta más valor de forma más inmediata. Lo que antes tomaba horas de búsqueda en bases de datos ahora puede completarse en minutos, con mayor cobertura y resúmenes automáticos. Pero el cómo importa tanto como el qué.</p>

<h2>Cómo funciona la investigación legal con IA</h2>
<p>Los sistemas tradicionales de búsqueda jurisprudencial (Westlaw, Lexis, en Chile el buscador del Poder Judicial) requieren que construyas la consulta en términos jurídicos precisos: búsqueda por palabras clave, por norma citada, por tribunal, por fecha. El resultado es una lista de documentos que debes leer y evaluar.</p>
<p>Los sistemas de IA modernos permiten consultas en lenguaje natural: "¿Cuál es la tendencia de la Corte de Apelaciones de Santiago en casos de responsabilidad contractual por productos defectuosos en los últimos cinco años?" El sistema procesa la pregunta, busca en su base de datos y entrega un resumen con las conclusiones y las fuentes que las sustentan.</p>

<h2>Herramientas disponibles</h2>
<p><strong>Lexis+ AI:</strong> integra IA generativa sobre la base de datos de LexisNexis. Incluye un asistente conversacional que responde preguntas legales citando casos reales verificables. Disponible principalmente en EE.UU. y Reino Unido, con cobertura limitada para Chile.</p>
<p><strong>Westlaw Precision:</strong> similar de Thomson Reuters, con integración de IA para síntesis de jurisprudencia. Misma limitación geográfica.</p>
<p><strong>Harvey para research:</strong> permite hacer investigación legal conversacional con contexto más amplio.</p>
<p><strong>Para Chile específicamente:</strong> el buscador del Poder Judicial (pjud.cl) y el buscador del BCN (bcn.cl) no tienen IA integrada aún, pero son las fuentes primarias para verificar cualquier resultado que obtengas de otra herramienta.</p>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">📌 Flujo de trabajo recomendado</p>
    <ol style="color:#475569;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>Formula la pregunta de investigación en lenguaje natural en la herramienta de IA</li>
        <li>Revisa el resumen y las fuentes que cita el sistema</li>
        <li>Verifica cada caso citado en la fuente primaria (pjud.cl, BCN, Westlaw, Lexis)</li>
        <li>Lee los fallos completos de los más relevantes para el caso</li>
        <li>Construye tu análisis desde los documentos verificados, no desde el resumen de IA</li>
    </ol>
</div>
HTML,

            'ia-para-derecho_5_2' => <<<HTML
<p>La redacción de documentos legales es una de las tareas más consumidoras de tiempo en la práctica del derecho. La IA puede acelerar significativamente el primer borrador — pero el resultado final sigue dependiendo del criterio del abogado.</p>

<h2>Qué produce bien la IA en redacción legal</h2>
<p><strong>Contratos estándar.</strong> NDA, contratos de prestación de servicios, acuerdos de confidencialidad, contratos de compraventa de bienes estándar. Para contratos con mucha negociación o términos inusuales, el valor de la IA baja.</p>
<p><strong>Borradores de escritos de trámite.</strong> Solicitudes de prórroga, escritos de acompañamiento de documentos, recursos formales estándar. Donde hay poca creatividad requerida y mucho formato.</p>
<p><strong>Resúmenes ejecutivos.</strong> La IA es muy buena condensando documentos largos en versiones cortas. Un contrato de 80 páginas puede convertirse en un resumen de dos páginas con las cláusulas clave identificadas.</p>
<p><strong>Cláusulas modelo.</strong> Si necesitas redactar una cláusula de limitación de responsabilidad y quieres ver variantes del mercado, la IA puede generar varias opciones con distintos niveles de protección para discutir con el cliente.</p>

<h2>El proceso correcto</h2>
<p>El error más común es usar el borrador de la IA como documento final con pequeños ajustes. El proceso correcto es: (1) usa la IA para generar un borrador estructurado, (2) revisa cada cláusula con criterio propio, (3) ajusta según el contexto específico del cliente y la transacción, (4) verifica que el conjunto sea coherente internamente, (5) revisa bajo el derecho aplicable.</p>

<div style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#92400e;">Errores frecuentes en documentos generados por IA</p>
    <ul style="color:#78350f;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>Cláusulas que asumen el derecho de otro país (la IA mezcla jurisdicciones)</li>
        <li>Definiciones inconsistentes entre distintas partes del documento</li>
        <li>Omisión de cláusulas esenciales para el tipo de transacción</li>
        <li>Lenguaje que suena legal pero es ambiguo o vacío de contenido</li>
        <li>Referencia a normativa desactualizada</li>
    </ul>
</div>
HTML,

            'ia-para-derecho_5_3' => <<<HTML
<p>El due diligence es quizás el área donde la IA genera el mayor impacto mensurable en la práctica legal. Los números son contundentes: lo que tomaba semanas a equipos grandes ahora puede hacerse en días, con mayor cobertura y trazabilidad.</p>

<h2>Cómo la IA transforma el due diligence</h2>
<p>En una transacción M&A, el data room puede contener miles de documentos: contratos comerciales, laborales, inmobiliarios, societarios, regulatorios, ambientales. El equipo de due diligence debe revisar todos para identificar riesgos que afecten el precio, la estructura o la decisión de comprar.</p>
<p>Tradicionalmente, esto se hacía con equipos de abogados junior leyendo cada documento. Con IA, el sistema hace la primera pasada en horas: extrae cláusulas clave, identifica riesgos (cambio de control, limitaciones de responsabilidad, plazos próximos a vencer), y genera un reporte estructurado. Los abogados se concentran en revisar los hallazgos de alto riesgo y tomar decisiones sobre ellos.</p>

<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #00c896;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#166534;">Qué hace la IA en due diligence</p>
    <ul style="color:#166534;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li>Clasificar y organizar documentos por tipo y relevancia</li>
        <li>Extraer cláusulas clave (cambio de control, no competencia, confidencialidad)</li>
        <li>Identificar contratos con plazos próximos a vencer</li>
        <li>Marcar cláusulas inusuales o que se desvían del estándar de mercado</li>
        <li>Generar resúmenes por contrato y resumen ejecutivo del portfolio</li>
        <li>Identificar inconsistencias entre documentos</li>
    </ul>
</div>

<h2>Lo que sigue siendo humano</h2>
<p>La síntesis estratégica. El due diligence no termina en identificar los riesgos: termina en evaluar su materialidad para la transacción, negociar representaciones y garantías que los cubran, y recomendar al cliente si la transacción tiene sentido a la luz de lo encontrado. Todo eso requiere juicio que la IA no tiene.</p>

<h2>Implicancias en Chile</h2>
<p>Las firmas chilenas que participan en M&A de tamaño relevante ya están adoptando estas herramientas o evaluando hacerlo. Los clientes corporativos que hacen due diligence frecuente van a empezar a preguntar por qué su asesor legal cobra las mismas horas si puede hacer el trabajo en menos tiempo con IA. La conversación sobre honorarios y eficiencia tecnológica va a llegar.</p>
HTML,

            'ia-para-derecho_5_4' => <<<HTML
<p>Terminaste el curso. Ahora la pregunta práctica: ¿cómo integras esto en tu trabajo real, con los recursos que tienes, a partir de la próxima semana?</p>

<h2>Paso 1: identifica tu punto de entrada</h2>
<p>No intentes adoptar IA en toda tu práctica al mismo tiempo. Elige una tarea específica donde el potencial de ahorro de tiempo es claro y el riesgo de un error es bajo: investigación preliminar de temas, redacción de borradores de escritos no críticos, resúmenes de documentos extensos. Empieza ahí.</p>

<h2>Paso 2: elige una herramienta y aprende a usarla bien</h2>
<p>La proliferación de herramientas es parte del problema. No necesitas probar todas. Elige una herramienta general (ChatGPT Enterprise, Claude, o Microsoft Copilot si tu firma usa Office 365) y úsala con regularidad durante un mes. El aprendizaje viene de la práctica frecuente, no de probar muchas herramientas una vez.</p>
<p>Invierte tiempo en aprender a escribir buenos prompts para el trabajo legal. La calidad del output depende directamente de la calidad de la instrucción que le das al sistema.</p>

<h2>Paso 3: establece tu protocolo</h2>
<p>Define tus reglas antes de necesitarlas: qué información nunca ingresarás a una IA de uso general, cómo documentarás el uso de IA en los expedientes, cómo verificarás los outputs antes de usarlos en documentos finales. Escríbelas. Si tienes equipo, compártelas.</p>

<div style="background:#faf5ff;border:1px solid #e9d5ff;border-left:4px solid #a78bfa;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
    <p class="fw-bold mb-2" style="color:#6d28d9;">Tu hoja de ruta en 90 días</p>
    <ul style="color:#6d28d9;margin:0;font-size:.93rem;padding-left:1.2rem;">
        <li><strong>Días 1-15:</strong> elige una herramienta, crea una cuenta, completa un tutorial básico</li>
        <li><strong>Días 15-30:</strong> úsala en una tarea real de bajo riesgo, evalúa el output críticamente</li>
        <li><strong>Días 30-60:</strong> amplía a una segunda tarea, establece tu protocolo por escrito</li>
        <li><strong>Días 60-90:</strong> evalúa qué ahorro real de tiempo has logrado, decide si ampliar el uso</li>
    </ul>
</div>

<h2>Mantenerse al día</h2>
<p>El campo evoluciona rápido. Las herramientas de hoy no son las de hace un año, y las de dentro de un año serán distintas. Reserva tiempo mensual para revisar novedades: qué nuevas herramientas aparecen, qué decisiones regulatorias afectan su uso, qué errores están documentando tus colegas. ConocIA y publicaciones especializadas como Law Technology Today o Artificial Lawyer son buenos puntos de partida.</p>

<div style="background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);border:1px solid #bbf7d0;border-radius:.75rem;padding:1.75rem;margin-top:2rem;text-align:center;">
    <div style="font-size:2rem;margin-bottom:.75rem;">🎓</div>
    <h3 class="fw-bold" style="color:#166534;font-size:1.1rem;margin-bottom:.5rem;">Completaste IA para Profesionales del Derecho</h3>
    <p style="color:#166534;font-size:.92rem;margin:0 0 1.25rem;">Tienes ahora una base sólida para entender, usar y asesorar sobre IA en el ámbito legal. El campo sigue evolucionando — vuelve pronto.</p>
    <a href="{{ route('cursos.index') }}" class="btn btn-sm" style="background:#00c896;color:white;font-size:.85rem;border:none;padding:.5rem 1.25rem;border-radius:.5rem;text-decoration:none;">
        Ver otros cursos
    </a>
</div>
HTML,

            // ── DOCENTES · MÓDULO 1 ─────────────────────────────────────────

            'ia-para-docentes_1_1' => <<<HTML
<p>Entre el 60 y el 70% de los estudiantes universitarios en América Latina reconocen haber usado IA generativa en sus trabajos académicos. En educación media, la cifra es más difícil de medir pero los datos cualitativos apuntan en la misma dirección: ChatGPT, Copilot y herramientas similares forman parte de la rutina de estudio de una generación entera.</p>
<p>Lo primero que necesitas entender es <strong>cómo usan la IA, no solo cuánto</strong>. No es lo mismo usar ChatGPT para generar un ensayo completo que usarlo para explicar un concepto que no se entendió en clases, o para corregir la gramática de un texto propio. El uso varía enormemente según la asignatura, el nivel educativo y la cultura del establecimiento.</p>
<h2>Los tres patrones de uso más frecuentes</h2>
<p><strong>1. Generación completa de tareas:</strong> el estudiante entrega el prompt al modelo, copia la respuesta y la entrega. Este es el caso que más preocupa a los docentes y es, según los datos, el menos frecuente de los tres —aunque no marginal.</p>
<p><strong>2. Apoyo y complemento:</strong> el estudiante hace su trabajo, luego pide a la IA que lo revise, lo mejore, lo amplíe o lo explique de otra manera. Aquí la IA actúa como un tutor disponible 24/7. Es el uso más extendido en estudiantes de rendimiento medio-alto.</p>
<p><strong>3. Exploración y comprensión:</strong> el estudiante usa la IA para entender un concepto antes de enfrentarse a la tarea. "Explícame qué es la fotosíntesis como si tuviera 12 años." Este uso es el más cercano a lo pedagógicamente valioso y el menos discutido en las políticas escolares.</p>
<h2>Qué implica esto para tu práctica docente</h2>
<p>Que el fenómeno ya ocurre con o sin tu autorización. La pregunta ya no es "¿permito la IA?" sino "¿tengo claridad sobre qué usos son aceptables y cuáles no, y mis estudiantes también la tienen?"</p>
<p>El docente que sigue diseñando evaluaciones como si la IA no existiera no está siendo más exigente: está siendo menos realista. Y el que prohíbe sin política clara crea un doble estándar donde quienes hacen trampa son los que menos pueden permitirse el tiempo de hacer el trabajo completo por su cuenta.</p>
HTML,

            'ia-para-docentes_1_2' => <<<HTML
<p>Para poder tomar decisiones informadas sobre el uso de IA en tu aula, necesitas conocer el ecosistema de herramientas al que tus estudiantes tienen acceso. No es un catálogo tecnológico: es el mapa de lo que ya está en sus bolsillos.</p>
<h2>Las herramientas más usadas por estudiantes</h2>
<p><strong>ChatGPT (OpenAI):</strong> la más popular por lejos. En su versión gratuita (GPT-3.5 y ahora GPT-4o) permite generar texto, resumir documentos, resolver ejercicios matemáticos, escribir código y mucho más. La versión gratuita es suficiente para la mayoría de las tareas académicas de educación media y universitaria.</p>
<p><strong>Microsoft Copilot:</strong> integrado en Edge, Bing y Office 365. Si tu establecimiento usa cuentas Microsoft para estudiantes, Copilot puede estar disponible directamente en Word. Muchos estudiantes ni siquiera saben que lo están usando cuando piden "sugerencias" en un documento.</p>
<p><strong>Google Gemini:</strong> integrado en el ecosistema Google. Si tus estudiantes usan Google Docs, Gmail o Google Classroom, Gemini puede aparecer como asistente de escritura. Relevante especialmente para establecimientos que usan Google Workspace for Education.</p>
<p><strong>Quillbot y similares:</strong> herramientas de paráfrasis que permiten reescribir texto manteniendo el sentido pero cambiando las palabras. Muy usadas para "humanizar" textos generados por IA o evitar la detección de plagio tradicional.</p>
<p><strong>Wolfram Alpha:</strong> no es IA generativa, pero lleva años resolviendo problemas matemáticos paso a paso. Cualquier evaluación de matemáticas que no considere esta herramienta tiene décadas de atraso.</p>
<h2>Lo que cambia en 2025</h2>
<p>Las IA multimodales ya permiten que un estudiante fotografíe un ejercicio con su celular y reciba la solución explicada en segundos. Las búsquedas con IA integrada (Google AI Overviews, Perplexity) están reemplazando la lectura de fuentes primarias. El estudiante de hoy no googlea: pregunta.</p>
<p>El conocer estas herramientas no es para que las uses todas: es para que no te sorprendan y para que tus políticas y evaluaciones sean realistas.</p>
HTML,

            'ia-para-docentes_1_3' => <<<HTML
<p>Una de las causas de malas decisiones pedagógicas frente a la IA es la fantasía —en ambas direcciones. Algunos docentes creen que la IA "lo puede todo" y que cualquier evaluación es inútil. Otros creen que la IA comete tantos errores que sus estudiantes igual aprenden al detectarlos. Ni uno ni otro extremo es útil.</p>
<h2>Qué pueden hacer estas herramientas hoy</h2>
<p><strong>Generar texto coherente y fluido:</strong> a nivel de redacción superficial, los modelos actuales escriben mejor que muchos humanos. Pueden producir ensayos, informes, resúmenes, correos formales y narrativas con coherencia gramatical y estructura lógica.</p>
<p><strong>Resumir y sintetizar:</strong> a partir de un documento largo, pueden extraer los puntos clave con alta precisión. Esto hace irrelevante cualquier evaluación que pida "resumir el capítulo".</p>
<p><strong>Resolver problemas estructurados:</strong> matemáticas, física, química, código de programación. Problemas con respuesta única y verificable son altamente manejables para los modelos actuales.</p>
<p><strong>Responder preguntas factuales conocidas:</strong> historia, geografía, biología, conceptos establecidos. Si la respuesta existe en el entrenamiento del modelo, lo más probable es que la responda bien.</p>
<h2>Qué no pueden hacer (todavía) bien</h2>
<p><strong>Experiencias personales auténticas:</strong> no pueden describir lo que TÚ viviste, lo que TÚ observaste, lo que TÚ construiste. Las evaluaciones basadas en experiencias de aprendizaje concretas y situadas siguen siendo robustas.</p>
<p><strong>Razonamiento en situaciones inéditas:</strong> los modelos son brillantes en patrones conocidos y débiles ante problemas que requieren adaptación creativa genuina a un contexto específico y local.</p>
<p><strong>Opinión fundamentada en lectura real:</strong> pueden simular haber leído, pero si pides una opinión sobre cómo un texto específico cambió la perspectiva del estudiante, o qué pasó en la discusión del martes pasado en clases, la IA no puede saberlo.</p>
<p><strong>Consistencia factual en dominios especializados:</strong> cometen errores con fechas, citas, estadísticas recientes y detalles técnicos específicos. No siempre los detecta el estudiante, pero sí un docente que conoce bien su disciplina.</p>
HTML,

            'ia-para-docentes_1_4' => <<<HTML
<p>La discusión sobre IA en educación en Chile tiene particularidades que hay que considerar. No es lo mismo hablar de esto en una universidad privada de Santiago que en un liceo técnico de La Araucanía o una escuela rural de la Región de Los Ríos.</p>
<h2>Los datos que tenemos</h2>
<p>El Ministerio de Educación de Chile no tiene aún estadísticas oficiales publicadas sobre uso de IA en establecimientos. Los datos disponibles vienen de estudios de universidades (especialmente PUC y UAI) y de organismos internacionales como UNESCO y OCDE.</p>
<p>Un estudio de la UAI (2024) encontró que el <strong>73% de los estudiantes universitarios chilenos</strong> había usado ChatGPT al menos una vez para tareas académicas. Entre los que la usaban regularmente, el 44% declaró no saber bien si lo que hacían estaba permitido o no.</p>
<p>Esa última cifra es clave: el problema no es solo la herramienta, es la ausencia de política clara.</p>
<h2>La brecha digital que complica todo</h2>
<p>Chile tiene una de las tasas de penetración de smartphone más altas de América Latina (sobre el 80% de la población). Pero el acceso a internet de calidad en el hogar sigue siendo desigual. Eso significa que en muchos contextos, la IA generativa es accesible principalmente desde el celular con datos móviles, no desde un computador con conexión estable.</p>
<p>Esto tiene implicancias directas: las políticas de "no IA" en el aula que no consideran que el estudiante puede acceder igual desde su teléfono son ingenuamente ineficaces.</p>
<h2>Qué está haciendo el sistema educativo chileno</h2>
<p>A 2025, el Mineduc tiene lineamientos generales sobre uso ético de tecnología pero no una política específica sobre IA generativa. Algunas universidades (UAI, UDD, Diego Portales) han publicado sus propias políticas. La mayoría de los establecimientos escolares opera sin directrices claras.</p>
<p>Eso te deja a ti, docente, en una posición de mayor responsabilidad y también de mayor oportunidad: puedes ser quien lidera esta conversación en tu establecimiento antes de que llegue desde arriba con una política que no se adapta a tu realidad.</p>
HTML,

            // ── DOCENTES · MÓDULO 2 ─────────────────────────────────────────

            'ia-para-docentes_2_1' => <<<HTML
<p>La pregunta que más escuchan los docentes hoy es también la más difícil: "¿usar ChatGPT para hacer la tarea es trampa?" La respuesta honesta es: depende. Y esa ambigüedad, si no la resuelves tú explícitamente, la resolverán tus estudiantes a su favor.</p>
<h2>El problema con la definición tradicional de trampa</h2>
<p>La deshonestidad académica siempre tuvo una definición relativamente clara: copiar el trabajo de otro sin atribución, usar material no autorizado en una evaluación, fabricar datos. La IA lo complica porque:</p>
<ul>
<li>El texto generado no pertenece a ninguna persona identificable</li>
<li>El estudiante sí tomó la decisión de qué preguntar y qué entregar</li>
<li>La herramienta está disponible legalmente y de forma gratuita</li>
<li>No hay consenso institucional claro sobre qué está permitido</li>
</ul>
<h2>Un marco más útil: ¿qué aprendizaje estás evaluando?</h2>
<p>En lugar de preguntarte "¿es trampa?", pregúntate: <strong>¿qué proceso de aprendizaje quería que ocurriera, y la IA lo reemplazó o lo potenció?</strong></p>
<p>Si pediste que redactaran un ensayo para que practicaran la argumentación escrita, y la IA escribió el ensayo, el aprendizaje no ocurrió. Si pediste que analizaran un caso y usaron la IA para buscar jurisprudencia pero el análisis es suyo, puede ser perfectamente válido.</p>
<h2>Los tres niveles de uso según su impacto en el aprendizaje</h2>
<p><strong>Uso instrumental legítimo:</strong> corrección gramatical, traducción de términos, búsqueda de sinónimos, organización de ideas propias. No compromete el aprendizaje.</p>
<p><strong>Uso de apoyo discutible:</strong> usar IA para entender un concepto antes de hacer la tarea, pedir que revise el argumento y mejore la estructura. Depende del objetivo de la evaluación.</p>
<p><strong>Uso sustitutivo:</strong> la IA produce el trabajo que el estudiante debía producir. Esto sí compromete el aprendizaje, independientemente de si "se nota" o no.</p>
<p>La trampa ya no se define solo por el origen del texto, sino por si el proceso que debía generar aprendizaje realmente ocurrió.</p>
HTML,

            'ia-para-docentes_2_2' => <<<HTML
<p>Uno de los errores más costosos que puede cometer un establecimiento es basar su política de integridad académica en detectores de IA. Estos sistemas —Turnitin AI, GPTZero, Copyleaks, y otros— tienen limitaciones fundamentales que los hacen inadecuados como base de una política justa.</p>
<h2>Cómo funcionan los detectores de IA</h2>
<p>Analizan patrones estadísticos en el texto: perplejidad (qué tan predecible es la secuencia de palabras) y explosividad (variabilidad en la longitud de oraciones). Los textos de IA tienden a ser más uniformes, más predecibles. Los detectores buscan esa uniformidad.</p>
<p>El problema: estos mismos patrones aparecen en escritura humana clara y directa, en textos técnicos, en personas que escriben en su segunda lengua, en estudiantes con diferente estilo de redacción del promedio.</p>
<h2>La evidencia sobre falsos positivos</h2>
<p>Un estudio de la Universidad de Stanford (2023) encontró que GPTZero marcaba como IA el 61% de los ensayos escritos por estudiantes cuya lengua nativa no era el inglés. En español, estudios similares muestran tasas de error significativas con textos técnicos o formales.</p>
<p>Turnitin ha señalado que su detector tiene una tasa de falsos positivos del 4% cuando se calibra para identificar más del 80% del contenido IA. Eso puede sonar bajo, pero en un curso de 200 estudiantes son 8 estudiantes acusados falsamente.</p>
<h2>Lo que sí puedes hacer</h2>
<p>Los detectores pueden ser un <strong>indicador de alerta</strong>, no una prueba de culpabilidad. Si un detector señala un texto, el paso siguiente debe ser una conversación: pedir al estudiante que defienda su trabajo, que explique su argumento, que desarrolle el razonamiento en tiempo real.</p>
<p>Un estudiante que realmente hizo el trabajo puede hacer eso. Uno que copió y pegó sin entender, no.</p>
<p>La defensa oral —aunque sea informal— es el detector más confiable que existe.</p>
HTML,

            'ia-para-docentes_2_3' => <<<HTML
<p>La política de integridad académica más efectiva no es la más restrictiva: es la más clara y la más coherente con los objetivos de aprendizaje que declaras. Una política vaga aplicada de forma inconsistente genera más injusticia y más confusión que no tener ninguna.</p>
<h2>Los cuatro elementos de una política efectiva</h2>
<p><strong>1. Especificidad por evaluación:</strong> en lugar de una regla general de "no IA", definir para cada evaluación qué usos son permitidos. Ejemplo: "En este ensayo, puedes usar IA para revisar gramática pero no para generar ideas o estructura. Debes declarar si la usaste."</p>
<p><strong>2. Declaración de uso:</strong> pedir que los estudiantes declaren cómo usaron (o no usaron) IA, de la misma manera que se declaran las fuentes bibliográficas. Esto crea responsabilidad explícita y normaliza la honestidad sobre el proceso.</p>
<p><strong>3. Consecuencias proporcionales:</strong> distinguir entre un estudiante que usó IA más de lo permitido (error de juicio, consecuencia académica moderada) y uno que fabricó datos o copió trabajo de otro (deshonestidad grave). No todo merece la misma sanción.</p>
<p><strong>4. Evaluación del proceso además del producto:</strong> pedir borradores, incluir instancias de defensa oral, exigir reflexión sobre el proceso de aprendizaje. Esto hace la política más robusta sin necesidad de detectores.</p>
<h2>Un ejemplo concreto</h2>
<p>Algunos docentes están adoptando el modelo de "declaración de uso de IA" al final de cada entrega: un párrafo breve donde el estudiante explica qué herramientas usó y para qué. Esta práctica, tomada del mundo científico (Nature y otras revistas ya lo exigen), educa en transparencia y responsabilidad sin prohibir.</p>
<p>La política que funciona es la que tus estudiantes entienden, sienten como justa y pueden cumplir sin sentirse perseguidos.</p>
HTML,

            'ia-para-docentes_2_4' => <<<HTML
<p>Ninguna política de integridad funciona si no hay una conversación real detrás. No un discurso, no un reglamento leído en voz alta: una conversación donde los estudiantes puedan hacer preguntas, expresar dudas y entender el razonamiento detrás de las reglas.</p>
<h2>Por qué esta conversación importa</h2>
<p>Los estudiantes que usan IA para hacer trampa rara vez lo hacen porque son deshonestos por naturaleza. Lo hacen porque están sobrepasados, porque no entienden por qué importa el proceso, porque nadie les explicó que el objetivo de la tarea no era el texto sino el aprendizaje que ocurre al escribirlo.</p>
<p>Si la única razón que diste para no usar IA fue "porque las reglas lo dicen", esa es una razón frágil. Si explicas por qué ese proceso de aprendizaje específico importa para su desarrollo, estás construyendo algo más sólido.</p>
<h2>Cómo estructurar la conversación</h2>
<p><strong>Empieza con curiosidad, no con acusación:</strong> "¿Cuántos de ustedes han usado ChatGPT para alguna tarea este semestre?" Levantamiento de mano, sin consecuencias. Normalizar la honestidad.</p>
<p><strong>Explora los usos, no los juzgues de entrada:</strong> "¿Para qué lo usaron?" Deja que describan. Probablemente descubras usos que nunca habías considerado, algunos muy legítimos.</p>
<p><strong>Comparte tu propia experiencia con la IA:</strong> si la usas (o si la probaste y te pareció útil o decepcionante), cuéntalo. Elimina la distancia entre "yo que sé" y "ustedes que hacen trampa".</p>
<p><strong>Construye las reglas juntos:</strong> "Dado lo que acabamos de conversar, ¿cómo creen que deberíamos manejar esto en este curso?" La participación en el diseño de la norma aumenta el compromiso con cumplirla.</p>
<p>Esta conversación no es un evento único: es algo que debe ocurrir al inicio del año y revisarse cuando aparezcan casos concretos.</p>
HTML,

            // ── DOCENTES · MÓDULO 3 ─────────────────────────────────────────

            'ia-para-docentes_3_1' => <<<HTML
<p>La IA no solo es un desafío para la evaluación: es también una herramienta poderosa para reducir la carga de preparación que consume horas que podrías dedicar a lo que más importa: la relación con tus estudiantes y la reflexión sobre tu práctica pedagógica.</p>
<h2>Preparar clases con IA</h2>
<p><strong>Generar explicaciones alternativas:</strong> si tienes dificultad para que un grupo entienda un concepto, puedes pedir a la IA que lo explique de cinco formas diferentes —con analogías, con ejemplos históricos, con una metáfora visual. No todas serán útiles, pero una o dos probablemente sí.</p>
<p><strong>Adaptar materiales por nivel:</strong> pega un texto técnico y pide que lo adapte para distintos niveles de comprensión. Útil para grupos con diferentes ritmos de aprendizaje o para preparar lecturas de introducción y lectura avanzada del mismo tema.</p>
<p><strong>Buscar contraejemplos y casos:</strong> "Dame tres casos históricos donde esta teoría económica falló en la práctica." La IA puede generar una lista de punto de partida que luego verificas y seleccionas.</p>
<h2>Crear materiales didácticos</h2>
<p><strong>Presentaciones:</strong> describe el tema y el nivel, pide una estructura de presentación con puntos clave por diapositiva. No reemplaza tu diseño pedagógico, pero elimina el tiempo en blanco frente a una presentación vacía.</p>
<p><strong>Guías de lectura:</strong> a partir de un texto, pide que genere preguntas de comprensión, de análisis y de opinión fundamentada. En diez minutos tienes una guía completa que normalmente te tomaría una hora.</p>
<p><strong>Glosarios y resúmenes:</strong> pide que identifique los términos clave de un capítulo y los defina en lenguaje accesible para tu nivel de estudiantes.</p>
<h2>El paso crítico: verificar siempre</h2>
<p>La IA comete errores. No uses materiales generados por IA sin revisarlos. Pero la diferencia entre revisar un borrador y crear desde cero es enorme en términos de tiempo. Usa la IA como primer borrador, tú como editor con criterio pedagógico.</p>
HTML,

            'ia-para-docentes_3_2' => <<<HTML
<p>Generar buenas evaluaciones es uno de los trabajos más costosos en tiempo para un docente. La IA puede reducir ese tiempo sustancialmente —pero la calidad del resultado depende de qué tan bien le describes lo que necesitas.</p>
<h2>Cómo pedir evaluaciones útiles</h2>
<p>Un prompt vago produce resultados genéricos. Un prompt específico produce resultados utilizables. La diferencia está en cuánta información le das al modelo.</p>
<p><strong>Prompt débil:</strong> "Crea 10 preguntas sobre la Revolución Francesa."</p>
<p><strong>Prompt efectivo:</strong> "Crea 10 preguntas para un examen de Historia de 2° medio sobre la Revolución Francesa. Los estudiantes ya vieron causas, etapas y consecuencias. Quiero 4 preguntas de comprensión factual, 4 de análisis causal y 2 de opinión fundamentada. El examen dura 45 minutos."</p>
<p>Con el segundo prompt obtienes algo que puedes revisar y usar con ajustes menores.</p>
<h2>Tipos de evaluación que puedes generar</h2>
<p><strong>Preguntas de opción múltiple con distractores:</strong> pide que genere las alternativas incorrectas basadas en errores comunes de comprensión. Esto hace la evaluación más diagnóstica.</p>
<p><strong>Casos para análisis:</strong> "Genera un caso ficticio pero realista sobre un conflicto ético en el lugar de trabajo para que los estudiantes de Formación Ciudadana analicen usando los criterios que vimos."</p>
<p><strong>Rúbricas:</strong> describe el tipo de trabajo que vas a evaluar y pide una rúbrica con criterios y niveles de desempeño. Ajústala a tu contexto.</p>
<h2>Lo que la IA no puede hacer por ti</h2>
<p>No puede saber qué aprendieron específicamente tus estudiantes este semestre, qué ejemplos usaste en clases, qué errores conceptuales observaste en trabajos anteriores. Esa información contextual es la que transforma una evaluación genérica en una evaluación tuya. Ponla tú.</p>
HTML,

            'ia-para-docentes_3_3' => <<<HTML
<p>Dar retroalimentación de calidad a 30 o 40 estudiantes sobre un trabajo escrito es uno de los grandes cuellos de botella de la práctica docente. La IA puede ayudarte a escalarlo sin sacrificar la personalización.</p>
<h2>El modelo de retroalimentación asistida por IA</h2>
<p>No se trata de que la IA escriba la retroalimentación. Se trata de que te ayude a hacerla más rápido y más consistente.</p>
<p><strong>Paso 1 — Define tus criterios:</strong> escribe o pega la rúbrica o los criterios de evaluación que usarás.</p>
<p><strong>Paso 2 — Pega el trabajo del estudiante:</strong> junto con los criterios, pide a la IA que identifique fortalezas y debilidades según esos criterios. No que ponga nota: que señale dónde el argumento es sólido y dónde no.</p>
<p><strong>Paso 3 — Tú editas y personalizas:</strong> la IA te da un borrador. Tú lo ajustas con lo que conoces del estudiante, su historia de aprendizaje, el contexto de la clase.</p>
<p>Con este modelo, lo que antes tomaba 10 minutos por trabajo puede tomar 3 o 4 —sin perder profundidad.</p>
<h2>Retroalimentación diferenciada para grupos distintos</h2>
<p>Si tienes estudiantes con necesidades educativas especiales o con diferentes ritmos de aprendizaje, puedes pedir a la IA que adapte la retroalimentación al nivel de lenguaje apropiado o que enfatice distintos aspectos según el perfil del estudiante.</p>
<h2>Un uso que funciona especialmente bien</h2>
<p>Pedir a la IA que genere "preguntas de reflexión" en lugar de solo señalar errores. En lugar de "tu argumento es débil", la IA puede generar: "¿Qué evidencia agregarías para reforzar este punto?" o "¿Cómo responderías a alguien que argumente lo contrario?" Esto convierte la retroalimentación en un diálogo, no en un juicio.</p>
HTML,

            'ia-para-docentes_3_4' => <<<HTML
<p>Una encuesta de la OCDE encontró que los docentes dedican en promedio el 40% de su tiempo laboral a tareas administrativas: planificaciones, informes, comunicaciones, registros. La IA puede devolverte parte de ese tiempo.</p>
<h2>Comunicaciones con familias</h2>
<p>Redactar comunicaciones para apoderados que sean claras, respetuosas y efectivas toma tiempo. Puedes pedir a la IA que redacte una primera versión a partir de puntos clave que tú le das.</p>
<p>Ejemplo: "Redacta una comunicación para apoderados informando que el promedio de la unidad 2 fue más bajo de lo esperado, que vamos a hacer una evaluación recuperativa el viernes y que los estudiantes pueden acercarse a consultar. Tono profesional pero cercano."</p>
<p>Tú revisas, ajustas el tono según tu estilo y envías. Lo que antes tomaba 20 minutos, toma 5.</p>
<h2>Informes de evaluación</h2>
<p>Pide a la IA que te ayude a redactar observaciones cualitativas a partir de notas tuyas. "El estudiante muestra dificultades en comprensión lectora pero participa activamente en discusiones. Redacta una observación para el informe de notas en tono constructivo y orientado a la mejora."</p>
<h2>Planificaciones y programaciones</h2>
<p>Describe la unidad, los objetivos de aprendizaje y las semanas disponibles. Pide una propuesta de secuencia de clases. No la uses tal cual —nadie conoce tu grupo como tú— pero elimina el tiempo en blanco frente a un documento vacío.</p>
<h2>El límite importante</h2>
<p>Nunca pegues información personal identificable de estudiantes en herramientas de IA comerciales. Nombres, RUT, historial académico detallado, situaciones familiares. Usa descripciones genéricas ("un estudiante con dificultad en lectura") o sistemas institucionales con garantías de privacidad adecuadas.</p>
HTML,

            // ── DOCENTES · MÓDULO 4 ─────────────────────────────────────────

            'ia-para-docentes_4_1' => <<<HTML
<p>La pregunta que más libera a los docentes cuando piensan en IA y evaluación no es "¿cómo detecto si usaron IA?" sino "¿qué tipo de trabajo no puede hacer la IA por un estudiante sin que el aprendizaje ocurra de todas formas?"</p>
<h2>El principio central</h2>
<p>La IA es excelente en producir output a partir de conocimiento que ya existe. Es débil ante tareas que requieren experiencia vivida, posicionamiento personal fundamentado y síntesis creativa en contextos específicos e irrepetibles.</p>
<h2>Tipos de evaluación más robustos frente a la IA</h2>
<p><strong>Evaluación basada en experiencia propia:</strong> "Entrevista a alguien de tu familia o comunidad sobre X y analiza la entrevista usando los conceptos del módulo." La IA no puede hacer la entrevista ni describir lo que pasó en ese momento específico.</p>
<p><strong>Trabajo situado en el contexto local:</strong> "Analiza cómo se aplica este principio económico en la empresa donde trabaja alguien de tu entorno cercano." Requiere acceso a información que no está en internet.</p>
<p><strong>Defensa oral:</strong> cualquier evaluación que incluya una instancia donde el estudiante debe explicar su trabajo, responder preguntas, defender una posición. No se puede delegar a la IA.</p>
<p><strong>Diario de aprendizaje o portafolio reflexivo:</strong> si pides reflexión genuina y continua sobre el proceso de aprendizaje —qué costó, qué cambió, qué conectó con la vida real— es difícil falsificarlo de forma coherente a lo largo del tiempo.</p>
<p><strong>Producción con restricciones específicas:</strong> "Escribe un texto argumentativo usando solo los tres textos que leímos en clases, citándolos directamente." La IA no tiene acceso a esos textos específicos (a menos que el estudiante los pegue).</p>
<h2>La conclusión práctica</h2>
<p>No necesitas eliminar todos los trabajos escritos. Necesitas añadir una dimensión que anclé el trabajo a la experiencia real del estudiante —ya sea con una instancia oral, una reflexión sobre el proceso o un elemento de contexto local que la IA no puede inventar de forma creíble.</p>
HTML,

            'ia-para-docentes_4_2' => <<<HTML
<p>Durante décadas, la evaluación educativa se centró en el producto: el ensayo terminado, el examen respondido, el proyecto entregado. La IA hace obsoleta esa única dimensión de evaluación, pero abre la puerta a algo más pedagógicamente valioso: evaluar el proceso de aprendizaje mismo.</p>
<h2>Por qué el proceso importa más que nunca</h2>
<p>Si lo que te importa es si el estudiante aprendió —no si produjo texto— entonces necesitas evidencia de que el aprendizaje ocurrió. Esa evidencia está en el proceso: las decisiones que tomó, los errores que cometió y corrigió, cómo cambió su comprensión a lo largo del trabajo.</p>
<h2>Estrategias para evaluar el proceso</h2>
<p><strong>Borradores progresivos:</strong> pedir versiones sucesivas de un trabajo con reflexión sobre qué cambió y por qué. Un estudiante que copió de la IA en la versión 1 tendrá dificultad para mostrar evolución coherente en las versiones 2 y 3.</p>
<p><strong>Bitácora de trabajo:</strong> "Anota al final de cada sesión de trabajo qué hiciste, qué dificultad encontraste y cómo la resolviste." Cinco minutos por sesión, evidencia valiosa de proceso real.</p>
<p><strong>Defensa de decisiones:</strong> no preguntar qué dice el trabajo, sino "¿por qué elegiste este argumento y no otro?" o "¿qué descartaste antes de llegar a esta conclusión?" Estas preguntas revelan si hay comprensión real detrás del texto.</p>
<p><strong>Error productivo documentado:</strong> pedir que el estudiante incluya en la entrega un error que cometió y cómo lo resolvió. Esto valoriza la equivocación como parte del aprendizaje y hace el proceso visible.</p>
<h2>El cambio de mentalidad necesario</h2>
<p>Evaluar el proceso requiere confiar más en la conversación y la observación que en el texto terminado. Es un cambio que muchos docentes encuentran más rico pedagógicamente —y más justo— que la evaluación solo por producto. La IA no lo facilita solo porque complica la evaluación tradicional: lo hace necesario.</p>
HTML,

            'ia-para-docentes_4_3' => <<<HTML
<p>Una alternativa a tratar la IA como amenaza es incorporarla explícitamente como parte de la evaluación. En lugar de preguntarte cómo evitar que los estudiantes la usen, preguntarte cómo hacer que el uso de IA sea parte del aprendizaje que evalúas.</p>
<h2>El modelo de "IA como herramienta del trabajo"</h2>
<p>En muchas profesiones, usar IA eficazmente ya es una competencia laboral. Enseñar a los estudiantes a usarla bien y evaluarlos en eso no es bajar el estándar: es preparar para la realidad que van a encontrar.</p>
<h2>Ejemplos de evaluaciones que incorporan IA</h2>
<p><strong>Análisis crítico de output de IA:</strong> "Usa ChatGPT para que genere un ensayo sobre X. Luego identifica tres errores, imprecisiones o puntos débiles del texto generado y corrígelos con fundamentación." Requiere comprensión profunda del tema para identificar los errores.</p>
<p><strong>Prompt engineering como evaluación:</strong> "Diseña cinco prompts para obtener de la IA información útil sobre este tema. Evalúa qué tan buenas fueron las respuestas y por qué." Evalúa pensamiento crítico y comprensión del tema.</p>
<p><strong>IA como punto de partida, análisis propio como llegada:</strong> "Parte con lo que te dio la IA sobre este tema histórico. Luego contrástalo con las fuentes primarias que leímos y señala qué añade, qué simplifica y qué distorsiona la IA." Requiere lectura real de fuentes.</p>
<p><strong>Proyecto con declaración de uso:</strong> cualquier trabajo donde el estudiante declara qué usó de IA, cómo lo adaptó y qué aportó él. La declaración es parte de la evaluación.</p>
<h2>La competencia que estás desarrollando</h2>
<p>Usar IA críticamente —saber qué pedirle, evaluar la calidad de lo que devuelve, identificar sus límites— es una competencia del siglo XXI tan valiosa como leer comprensivamente o argumentar por escrito. Los estudiantes que salgan de tu aula sabiendo hacer esto tendrán ventaja.</p>
HTML,

            'ia-para-docentes_4_4' => <<<HTML
<p>Las estrategias de evaluación no son neutrales respecto a la asignatura ni al nivel. Lo que funciona en un curso universitario de Literatura no funciona igual en matemáticas de 7° básico o en Ciencias de 4° medio. A continuación, ejemplos concretos por área.</p>
<h2>Lenguaje y Comunicación</h2>
<p>La IA es especialmente poderosa en producción de texto, lo que hace más urgente el rediseño. Opciones robustas: escritura en tiempo real en clases (circunstancia que elimina el uso de IA externa), debates y argumentación oral, análisis de textos específicos leídos en clases con preguntas que requieren cita directa, escritura creativa anclada en experiencia personal.</p>
<h2>Matemáticas y Ciencias</h2>
<p>Wolfram Alpha resuelve ecuaciones hace décadas y los modelos actuales van mucho más lejos. Opciones robustas: resolución de problemas con explicación del razonamiento paso a paso en clases, diseño de experimentos (la IA no puede hacer el experimento), problemas aplicados a datos locales o situaciones específicas del contexto del estudiante, evaluaciones de diseño donde el proceso de pensamiento es más importante que la respuesta.</p>
<h2>Ciencias Sociales e Historia</h2>
<p>Las preguntas factuales son las más vulnerables. Opciones robustas: análisis de fuentes primarias con preguntas que requieren lectura real, trabajo con material audiovisual que la IA no puede analizar fácilmente (documentales, entrevistas), proyectos de historia oral o memoria local, posicionamiento argumentado sobre debates historiográficos con fundamentación en fuentes específicas del curso.</p>
<h2>Formación Ciudadana y Educación Cívica</h2>
<p>Terreno donde el pensamiento propio importa especialmente. Opciones robustas: simulaciones de debate, análisis de casos reales actuales con toma de posición fundamentada, proyectos comunitarios con impacto real, reflexión sobre experiencias cívicas propias.</p>
<h2>Educación Técnico-Profesional</h2>
<p>Contexto con ventaja: muchas competencias técnicas se evalúan haciendo, no describiendo. El desafío está en los componentes teóricos y de comprensión conceptual, donde aplican estrategias similares a las demás áreas.</p>
HTML,

            // ── DOCENTES · MÓDULO 5 ─────────────────────────────────────────

            'ia-para-docentes_5_1' => <<<HTML
<p>La política de IA más efectiva para tu aula no es la del establecimiento (que puede tardar años en llegar y quizás no se adapte a tu contexto). Es la tuya: la que tú defines, comunicas y aplicas con coherencia en tus propias asignaturas.</p>
<h2>Los cinco elementos de tu política personal</h2>
<p><strong>1. Posición general:</strong> ¿Cuál es tu postura de base sobre el uso de IA en tus cursos? No tiene que ser única para todas las evaluaciones, pero sí coherente con tus objetivos pedagógicos.</p>
<p><strong>2. Usos permitidos explícitos:</strong> enumera qué usos de IA son siempre aceptables en tu curso (corrección gramatical, búsqueda de términos, generación de ideas iniciales) sin importar la evaluación.</p>
<p><strong>3. Usos que requieren declaración:</strong> qué usos son aceptables solo si el estudiante lo declara explícitamente y describe cómo lo usó.</p>
<p><strong>4. Usos no permitidos:</strong> qué constituye uso inaceptable en tu curso, con la justificación pedagógica (no solo "porque no").</p>
<p><strong>5. Consecuencias y proceso:</strong> qué pasa si detectas un uso no declarado o no permitido. Sé específico y proporcional.</p>
<h2>Un template para empezar</h2>
<p>Tu política no necesita ser un documento legal. Puede ser un párrafo en el programa del curso o un slide en la primera clase: "En este curso, la IA es una herramienta que pueden usar para [X]. No está permitido [Y]. Si usan IA de cualquier forma, deben declararlo en la entrega. El objetivo de este curso es que desarrollen [competencia], y esa es la razón detrás de cada regla."</p>
<h2>Revisarla, no solo escribirla</h2>
<p>Una política de IA tiene que actualizarse. Lo que escribas hoy puede ser obsoleto en seis meses porque las herramientas cambian. Ponle una fecha de revisión y ajústala con lo que aprendiste al aplicarla.</p>
HTML,

            'ia-para-docentes_5_2' => <<<HTML
<p>La política de IA en el aula no es solo un acuerdo entre tú y tus estudiantes. En educación básica y media, también involucra a las familias. Comunicarlo bien evita malentendidos y genera alianza con los apoderados en lugar de conflicto.</p>
<h2>Qué saben (y no saben) los apoderados sobre IA</h2>
<p>La mayoría de los apoderados tienen una imagen de la IA formada principalmente por noticias de pánico ("la IA reemplazará todos los trabajos") o de entusiasmo acrítico ("la IA resolverá todos los problemas"). Pocas veces han pensado en cómo se aplica esto al aprendizaje de sus hijos.</p>
<p>Eso te da la oportunidad de enmarcar la conversación desde el principio.</p>
<h2>Cómo comunicar tu política a los apoderados</h2>
<p><strong>En la reunión de apoderados de inicio de año:</strong> explica brevemente qué es la IA generativa, qué pueden hacer tus estudiantes con ella y cuál es tu postura. No tienes que ser exhaustivo: necesitas que entiendan el marco general.</p>
<p><strong>Por escrito, en lenguaje accesible:</strong> evita el tecnicismo. "ChatGPT es una herramienta que puede escribir textos por ellos. En mi curso, pueden usarla para [X], pero deben declararlo. No pueden usarla para [Y], porque el objetivo es que desarrollen [competencia] que van a necesitar."</p>
<p><strong>Anticipa las preguntas más comunes:</strong> "¿Por qué no la prohíben directamente?" (porque existe igual y es más útil aprender a usarla bien), "¿Cómo saben si la usaron?" (no siempre, pero eso no cambia la responsabilidad del estudiante), "¿Y si mi hijo no tiene acceso a IA?" (ventaja en ciertas evaluaciones, no desventaja).</p>
<h2>El mensaje central para los apoderados</h2>
<p>El objetivo no es que su hijo use más o menos IA: es que aprenda a hacerlo con criterio. Eso es una competencia que necesitará en cualquier carrera y trabajo que elija.</p>
HTML,

            'ia-para-docentes_5_3' => <<<HTML
<p>Preparar a los estudiantes para el mundo laboral siempre fue parte del mandato implícito de la educación. Hoy ese mundo laboral incluye la IA como infraestructura cotidiana, y los docentes que lo ignoren están enviando a sus estudiantes con una brecha real.</p>
<h2>Qué habilidades necesitarán en el mercado laboral</h2>
<p><strong>Saber qué pedirle a la IA (prompting):</strong> la calidad del output depende de la calidad del input. Formular instrucciones claras, específicas y con contexto suficiente es una habilidad que se practica y que puede enseñarse.</p>
<p><strong>Evaluar críticamente el output:</strong> saber que la IA comete errores, identificar cuáles, verificar datos, reconocer sesgos. Sin esto, la IA amplifica los errores en lugar de reducirlos.</p>
<p><strong>Combinar IA con criterio humano:</strong> saber cuándo delegar a la IA y cuándo no. La IA es excelente para primer borrador, síntesis inicial, búsqueda de alternativas. Es débil en decisiones éticas complejas, relaciones humanas, contexto cultural específico.</p>
<p><strong>Mantener autoría intelectual:</strong> poder explicar, defender y desarrollar las ideas que uno presenta, aunque hayan sido elaboradas con apoyo de IA. Esto requiere comprensión real, no solo edición de output.</p>
<h2>Cómo incorporarlo en el aula</h2>
<p>No necesitas un módulo especial sobre IA. Puedes integrar estas habilidades en tus evaluaciones existentes: pide que usen IA para una parte del trabajo, luego pide que evalúen críticamente lo que la IA produjo, luego que lo mejoren con sus propios criterios. Eso es exactamente lo que van a hacer en sus trabajos.</p>
<h2>La meta real</h2>
<p>No es que tus estudiantes sepan usar ChatGPT. Es que sepan pensar con claridad, comunicar con precisión y tomar decisiones fundamentadas —habilidades que la IA potencia en quien ya las tiene, y que se vuelven más urgentes exactamente porque la IA existe.</p>
HTML,

            'ia-para-docentes_5_4' => <<<HTML
<p>Has llegado al final de este curso con un mapa claro del desafío y herramientas concretas para enfrentarlo. El último paso es saber cómo seguir aprendiendo, porque este campo cambia rápido y lo que aprendiste hoy tendrá actualizaciones en seis meses.</p>
<h2>Recursos recomendados para docentes</h2>
<p><strong>UNESCO — Guía de IA para educadores (2023):</strong> el documento más completo disponible en español sobre IA en educación, con marco ético y recomendaciones prácticas. Disponible gratuitamente en el sitio de UNESCO.</p>
<p><strong>CEPPE UC — Centro de Políticas y Prácticas en Educación:</strong> produce investigación sobre educación chilena, incluyendo trabajo emergente sobre tecnología y aprendizaje.</p>
<p><strong>Common Sense Education:</strong> recurso en inglés (con mucho material en español) orientado a docentes K-12 sobre tecnología, privacidad y pensamiento crítico digital.</p>
<p><strong>AI for Education (aiforeducation.io):</strong> comunidad y recursos en inglés orientados a docentes, con prompts específicos por asignatura y nivel.</p>
<h2>Comunidades de práctica en Chile</h2>
<p>Las redes de docentes que están pensando este tema existen, aunque no siempre son visibles. Busca grupos en LinkedIn de docentes de tu área, participa en instancias de perfeccionamiento del CPEIP (Centro de Perfeccionamiento del Magisterio), y considera proponer en tu establecimiento un espacio de conversación entre docentes sobre IA —aunque sea informal.</p>
<h2>Tu contribución</h2>
<p>El conocimiento que generaste en este curso —sobre tu contexto específico, tus estudiantes, tus asignaturas— no está en ningún manual. Compartirlo con colegas multiplica su valor. El docente más valioso en este momento no es el que más sabe sobre IA: es el que más ha pensado en cómo integrarla con criterio pedagógico en su práctica real.</p>
<div style="background:linear-gradient(135deg,#00c896,#38b6ff);border-radius:1rem;padding:2rem;text-align:center;margin-top:2rem;">
    <div style="font-size:2rem;margin-bottom:.5rem;">🎓</div>
    <h2 style="color:#fff;font-size:1.15rem;margin-bottom:.5rem;">Curso completado</h2>
    <p style="color:rgba(255,255,255,.9);font-size:.9rem;max-width:420px;margin:0 auto 1rem;">Terminaste <strong>IA para Docentes</strong>. Tienes ahora un marco concreto para tomar decisiones pedagógicas informadas en el aula con IA.</p>
    <a href="/cursos" style="display:inline-block;background:#fff;color:#0f172a;font-weight:700;padding:.6rem 1.5rem;border-radius:.5rem;text-decoration:none;font-size:.88rem;">Ver otros cursos</a>
</div>
HTML,

            // ── PERIODISTAS · MÓDULO 1 ──────────────────────────────────────

            'ia-para-periodistas_1_1' => <<<HTML
<p>La IA no es el futuro del periodismo: es el presente. Los medios más importantes del mundo llevan años integrando herramientas de inteligencia artificial en sus redacciones, y los resultados son tan variados como las formas en que lo están haciendo.</p>
<h2>Medios que ya usan IA de forma sistemática</h2>
<p><strong>Associated Press:</strong> desde 2014, AP usa IA para generar automáticamente reportes de resultados deportivos, ganancias corporativas trimestrales y datos electorales. El sistema —desarrollado con Automated Insights— produce miles de artículos al mes que serían inviables de escribir manualmente. AP estima que esto libera a sus periodistas para trabajo de mayor valor.</p>
<p><strong>The Washington Post:</strong> su sistema interno "Heliograf" cubrió los Juegos Olímpicos de Río 2016 y las elecciones de EE.UU. con alertas y reportes automatizados. En 2020 expandió su uso a cobertura local donde la redacción no puede estar presente físicamente.</p>
<p><strong>Bloomberg:</strong> usa IA para procesar y resumir información financiera en tiempo real. Cerca del 30% de su contenido tiene algún grado de automatización, según datos del propio medio.</p>
<p><strong>Reuters:</strong> su herramienta Lynx Insight analiza grandes conjuntos de datos en tiempo real y sugiere historias a los periodistas basándose en anomalías estadísticas. No escribe la nota: alerta al periodista de que algo vale la pena investigar.</p>
<h2>En América Latina y Chile</h2>
<p>La adopción es más lenta pero existe. El Mercurio usa sistemas de automatización para ciertos datos de mercado. Medios digitales como La Tercera y Emol experimentan con herramientas de IA para SEO y personalización de contenidos. Agencias como EFE América han comenzado a explorar generación automática de noticias de datos.</p>
<p>El patrón común: la IA se integra primero donde hay datos estructurados y volumen alto. Las historias de investigación, análisis político y cobertura humana siguen siendo territorio de periodistas.</p>
HTML,

            'ia-para-periodistas_1_2' => <<<HTML
<p>La desinformación existía antes de la IA. Pero la IA ha cambiado la ecuación de una manera fundamental: ha eliminado la barrera de entrada. Crear contenido falso convincente ya no requiere equipos, presupuesto ni habilidades técnicas avanzadas. Requiere un celular y cinco minutos.</p>
<h2>La escala del problema</h2>
<p>En 2023, el Centro de Contrainformación de la Unión Europea documentó más de 6.000 casos de desinformación vinculados a actores estatales. El crecimiento de casos con componente de IA generativa fue del 300% entre 2022 y 2024, según el informe de NewsGuard sobre "noticias falsas con IA".</p>
<p>En Chile, el Observatorio de Desinformación de la Universidad de Chile ha identificado redes de cuentas que usan contenido generado por IA para amplificar narrativas durante períodos electorales y conflictos sociales.</p>
<h2>Las nuevas herramientas de la desinformación</h2>
<p><strong>Granjas de contenido automatizado:</strong> sitios web que publican cientos de artículos diarios generados por IA con información falsa o engañosa, optimizados para buscadores. Muchos parecen medios legítimos.</p>
<p><strong>Deepfakes de figuras públicas:</strong> videos y audios falsos de políticos, empresarios y figuras públicas. En 2024, circularon en redes sociales chilenas audios deepfake atribuidos a candidatos durante el período electoral.</p>
<p><strong>Imágenes sintéticas de eventos falsos:</strong> fotografías de "manifestaciones", "accidentes" o "crisis" que nunca ocurrieron, generadas con Midjourney, DALL-E o Stable Diffusion y distribuidas como si fueran reales.</p>
<h2>El impacto en el periodismo</h2>
<p>El periodismo ya no solo compite por la atención: compite por la credibilidad en un ambiente donde cualquier imagen, video o texto puede ser cuestionado. Eso eleva el estándar de verificación y hace que la transparencia sobre las fuentes sea más crítica que nunca.</p>
HTML,

            'ia-para-periodistas_1_3' => <<<HTML
<p>La relación entre IA y periodismo no es solo de amenaza. Hay capacidades concretas que la IA aporta al oficio periodístico y que, bien usadas, mejoran la calidad del trabajo y amplían lo que una redacción puede hacer.</p>
<h2>Procesamiento de grandes volúmenes de información</h2>
<p>El periodismo de datos e investigación enfrenta un problema estructural: hay más información disponible que capacidad humana para procesarla. Filtros de la Unión Europea, contratos públicos del Estado chileno, declaraciones de patrimonio de funcionarios, actas de directorios. Revisar todo esto manualmente tomaría semanas.</p>
<p>La IA puede procesar miles de documentos en minutos, identificar anomalías, comparar cifras, detectar inconsistencias. El periodista define qué buscar; la IA hace la búsqueda exhaustiva.</p>
<h2>Transcripción y análisis de entrevistas</h2>
<p>Herramientas como Whisper (OpenAI), Otter.ai o Notta transcriben audio a texto con alta precisión, incluido español con acento chileno. Lo que antes tomaba horas de trabajo manual ahora toma minutos. Más aún: algunos sistemas permiten análisis temático de la entrevista, identificar contradicciones entre declaraciones, o comparar lo que una fuente dijo en distintas instancias.</p>
<h2>Monitoreo y alertas</h2>
<p>La IA puede monitorear en tiempo real redes sociales, publicaciones oficiales, Diario Oficial, actas del Congreso y generar alertas cuando aparece información relevante para un tema de cobertura. Esto permite al periodista estar más informado sin revisar fuentes manualmente todo el día.</p>
<h2>Traducción y acceso a fuentes internacionales</h2>
<p>Los traductores basados en IA han alcanzado una calidad que antes tomaba especialistas. Un periodista chileno puede hoy acceder, en tiempo real y con traducción de alta calidad, a publicaciones en mandarín, árabe, ruso o alemán que antes simplemente no estaban disponibles para su trabajo.</p>
HTML,

            'ia-para-periodistas_1_4' => <<<HTML
<p>El periodismo también tiene razones legítimas para preocuparse por la IA. No como excusa para ignorar la tecnología, sino como marco realista para entender qué está en juego en el oficio.</p>
<h2>La amenaza a los empleos en periodismo</h2>
<p>El contenido automatizable —reportes de datos, síntesis de información pública, cobertura de eventos rutinarios— ya está siendo reemplazado en muchas redacciones. Buzzfeed cerró su división de noticias en 2023 y explicó que la IA era parte del reordenamiento. Sports Illustrated publicó artículos generados por IA bajo nombres de autores ficticios —un escándalo que reveló las presiones hacia la automatización en redacciones bajo presión financiera.</p>
<h2>La erosión de la confianza</h2>
<p>Cuando los lectores no pueden distinguir qué fue escrito por un periodista y qué por una máquina, la credibilidad del medio se erosiona. La promesa implícita del periodismo —que hay un humano responsable que verificó, que tiene criterio, que puede ser interpelado— se diluye.</p>
<h2>La concentración del poder informativo</h2>
<p>Los modelos de IA más poderosos son desarrollados por unas pocas empresas tecnológicas. Eso significa que la infraestructura que determina cómo se procesa y presenta la información está controlada por actores con intereses propios, sin el mandato de servicio público que tienen (o deberían tener) los medios de comunicación.</p>
<h2>La pérdida de la huella humana</h2>
<p>El periodismo no es solo información: es punto de vista, contexto cultural, empatía con la fuente, presencia en terreno. Un periodista que cubrió el estallido social en Santiago en 2019 trae una comprensión que ningún modelo entrenado con texto puede replicar. La pregunta es si las redacciones van a valorar eso lo suficiente como para mantenerlo.</p>
HTML,

            // ── PERIODISTAS · MÓDULO 2 ──────────────────────────────────────

            'ia-para-periodistas_2_1' => <<<HTML
<p>La imagen que no es real ya no necesita fotomontaje de Photoshop ni horas de trabajo. Las herramientas de generación de imágenes por IA producen fotografías hiperrealistas en segundos. Saber identificarlas es una competencia básica del periodista contemporáneo.</p>
<h2>Señales de alerta en imágenes generadas por IA</h2>
<p><strong>Manos y dedos:</strong> históricamente el punto más débil de los modelos de imagen. Aunque ha mejorado mucho con modelos de 2024 y 2025, todavía es frecuente ver manos con números incorrectos de dedos, proporciones extrañas o uniones anatómicamente imposibles. Examina las manos siempre.</p>
<p><strong>Texto en la imagen:</strong> letras, carteles, señales, ropa con texto. Los modelos generativos tienen dificultad para producir texto coherente dentro de las imágenes. Las letras suelen ser ilegibles, estar invertidas o ser combinaciones sin sentido.</p>
<p><strong>Fondo y bordes:</strong> objetos en el fondo que se funden, estructuras que no tienen sentido arquitectónico, bordes de cabello que se mezclan con el fondo de forma irreal. Los fondos complejos suelen revelar imprecisiones.</p>
<p><strong>Simetría facial y física exagerada:</strong> las imágenes generadas por IA tienden a la perfección simétrica poco natural. Las caras reales tienen asimetrías; las generadas por IA pueden verse "demasiado perfectas".</p>
<h2>Herramientas de detección</h2>
<p><strong>Google Lens y búsqueda inversa:</strong> el primer paso siempre. Si la imagen fue tomada de otro contexto, la búsqueda inversa lo revela. No detecta imágenes generadas por IA, pero sí imágenes recicladas de otros contextos.</p>
<p><strong>Hive Moderation y AI or Not:</strong> herramientas web que analizan una imagen y estiman la probabilidad de que sea generada por IA. No son infalibles, pero son útiles como primera señal.</p>
<p><strong>Metadata (EXIF):</strong> las imágenes fotográficas reales tienen metadata de cámara. Las imágenes generadas por IA típicamente no. Revisar la metadata con herramientas como Jeffrey's Exif Viewer puede ser revelador.</p>
<h2>El principio fundamental</h2>
<p>Ninguna herramienta es definitiva. La detección de imágenes IA es una práctica, no un proceso automático. La combinación de análisis visual, búsqueda inversa y contexto editorial es lo que funciona.</p>
HTML,

            'ia-para-periodistas_2_2' => <<<HTML
<p>Los deepfakes de video y audio representan el desafío de verificación más complejo que enfrenta el periodismo hoy. A diferencia de las imágenes estáticas, el video en movimiento activa mecanismos cognitivos de confianza muy fuertes: "lo vi con mis propios ojos."</p>
<h2>El estado actual de la tecnología</h2>
<p>Los deepfakes de alta calidad ya no requieren equipos de producción ni meses de entrenamiento. Herramientas como HeyGen, ElevenLabs y Runway ML permiten crear videos sintéticos y voces clonadas en minutos. En 2024, una llamada de audio deepfake clonando la voz del director financiero de una empresa multinacional convenció a un empleado en Hong Kong de transferir 25 millones de dólares a cuentas fraudulentas.</p>
<h2>Señales de alerta en video deepfake</h2>
<p><strong>Sincronización labial:</strong> busca desincronía entre el movimiento de los labios y el audio, especialmente en consonantes labiales (p, b, m). Los modelos actuales han mejorado pero todavía cometen errores en secuencias rápidas.</p>
<p><strong>Parpadeo y movimiento ocular:</strong> los primeros modelos deepfake fallaban en parpadeos naturales. Los actuales han mejorado, pero el movimiento ocular en momentos de emoción intensa puede verse antinatural.</p>
<p><strong>Iluminación inconsistente:</strong> si la fuente de luz en el rostro no coincide con la del resto de la escena, puede indicar superposición de imágenes.</p>
<p><strong>Bordes del rostro:</strong> en movimiento rápido o rotación de cabeza, los bordes del rostro pueden mostrar artefactos o difuminación poco natural.</p>
<h2>Señales de alerta en audio deepfake</h2>
<p>Pausas poco naturales entre palabras, cambios de calidad de audio dentro de la misma grabación, pronunciación de nombres propios o términos técnicos de forma extraña, falta de "ruido de fondo" consistente.</p>
<h2>El protocolo mínimo antes de usar un video o audio</h2>
<p>Verificar la fuente original. Buscar el video en bases de datos de verificación (AFP Fact Check, Reuters Fact Check, Maldita.es). Consultar al protagonista o su equipo. Si el video llegó por Telegram o WhatsApp sin fuente clara, ese es ya un factor de alerta máxima.</p>
HTML,

            'ia-para-periodistas_2_3' => <<<HTML
<p>Un artículo generado por IA puede ser indistinguible de uno escrito por un humano para un lector casual —y en muchos casos, también para un periodista experimentado. Entender las señales de alerta y los límites de la detección es parte del oficio.</p>
<h2>Por qué la detección de texto IA es difícil</h2>
<p>Los detectores de texto IA funcionan sobre patrones estadísticos: los textos de IA tienden a ser predecibles, uniformes, con variabilidad menor en la longitud de oraciones. Pero esos mismos patrones aparecen en escritura técnica clara, en personas no nativas del idioma, o en textos editados para ser más directos.</p>
<p>Más importante: el texto generado por IA puede ser editado para "humanizarse". Herramientas como Quillbot, Undetectable.ai y otras están específicamente diseñadas para modificar texto de IA hasta evadir detectores. La carrera armamentista entre generadores y detectores la están ganando los generadores.</p>
<h2>Señales editoriales de texto sintético</h2>
<p><strong>Ausencia de especificidad:</strong> el texto de IA tiende a ser correcto en términos generales pero vago en detalles. Fechas aproximadas ("en los últimos años"), cifras sin fuente, afirmaciones plausibles pero no verificables.</p>
<p><strong>Equilibrio artificial:</strong> los modelos tienden a presentar "ambos lados" de cualquier argumento de forma mecánica, incluso cuando hay consenso claro. Un texto que equilibra artificialmente posiciones asimétrica puede ser señal.</p>
<p><strong>Ausencia de voz y perspectiva:</strong> el texto de IA carece de la voz específica de un autor, del juicio editorial que decide qué incluir y qué dejar fuera, de la anécdota que solo existe si alguien estuvo ahí.</p>
<p><strong>Citas sin verificación posible:</strong> el texto de IA puede generar citas de personas reales que nunca las dijeron, o citar estudios que no existen. Verificar citas específicas es crítico.</p>
<h2>Lo que sí funciona para detectar</h2>
<p>Verificar las afirmaciones específicas contra fuentes primarias. Buscar las citas. Preguntar a los supuestos autores si escribieron el texto. Contrastar con el estilo previo del supuesto autor. La detección de texto sintético es edición periodística rigurosa, no tecnología mágica.</p>
HTML,

            'ia-para-periodistas_2_4' => <<<HTML
<p>La verificación no puede ser un proceso ad hoc que se activa cuando algo "se ve raro". Necesita ser un flujo integrado en la rutina diaria de la redacción, especialmente en un ambiente donde el contenido sintético es cada vez más común y menos distinguible.</p>
<h2>El flujo de verificación en cuatro pasos</h2>
<p><strong>Paso 1 — ¿De dónde viene esto?</strong> Antes de ver el contenido, verifica la fuente. ¿Es un medio con trayectoria verificable? ¿Una cuenta con historial? ¿Llegó por un canal sin identificación clara? La procedencia es la primera señal.</p>
<p><strong>Paso 2 — ¿Existe evidencia independiente?</strong> Si la historia es real, debe haber otras fuentes que la confirmen o al menos que puedan confirmarse. Una historia exclusiva puede ser real; una historia que solo existe en una fuente sin respaldo es una alerta.</p>
<p><strong>Paso 3 — Verificación técnica del material:</strong> para imágenes, búsqueda inversa + análisis de metadata + revisión visual de anomalías. Para video, búsqueda de la fuente original + revisión de señales de deepfake. Para texto, verificación de citas y afirmaciones específicas.</p>
<p><strong>Paso 4 — Consulta a la fuente primaria:</strong> si el material involucra a una persona, organización o evento específico, la fuente primaria debe confirmarlo antes de publicar. No como cortesía: como protocolo.</p>
<h2>Herramientas para integrar en la redacción</h2>
<p><strong>InVID/WeVerify:</strong> extensión de navegador para verificar videos, imágenes y noticias. Estándar en redacciones de fact-checking internacionales.</p>
<p><strong>TinEye y Google Images:</strong> búsqueda inversa de imágenes para identificar si fueron tomadas de otro contexto.</p>
<p><strong>Botometer:</strong> analiza si una cuenta de Twitter/X tiene comportamiento de bot.</p>
<p><strong>Perplexity y búsqueda con fuentes:</strong> para verificar afirmaciones específicas con citas a fuentes identificables.</p>
<h2>El principio de velocidad vs. exactitud</h2>
<p>La presión del tiempo real es real. Pero publicar primero algo falso tiene un costo reputacional muy superior al de llegar segundos después con información verificada. La política editorial que prioriza la verificación sobre la velocidad es la que construye credibilidad a largo plazo.</p>
HTML,

            // ── PERIODISTAS · MÓDULO 3 ──────────────────────────────────────

            'ia-para-periodistas_3_1' => <<<HTML
<p>Cubrir inteligencia artificial mal es fácil. La combinación de tecnología compleja, intereses económicos enormes, narrativas de ciencia ficción arraigadas y fuentes con agendas propias produce un ambiente donde los errores se replican constantemente. Identificarlos es el primer paso para no cometerlos.</p>
<h2>Error 1: Antropomorfizar la IA</h2>
<p>"La IA decidió", "el sistema pensó", "el algoritmo eligió". Estas formulaciones atribuyen intención y agencia a sistemas que no tienen ninguna. Los modelos de IA predicen tokens basándose en patrones estadísticos: no deciden, no piensan, no eligen. El lenguaje importa porque forma cómo el público entiende quién es responsable cuando algo sale mal.</p>
<h2>Error 2: Generalizar de un caso a "la IA"</h2>
<p>Un chatbot médico que da malos consejos no dice nada sobre "la IA en salud" en general, así como un auto que falla los frenos no dice nada sobre "los autos". Los sistemas de IA son extremadamente heterogéneos. Una nota que salta de un caso específico a conclusiones sobre "la IA" está generalizando sin fundamento.</p>
<h2>Error 3: Confundir demostración con deploym ent</h2>
<p>Las demostraciones de laboratorio y los sistemas en producción son cosas distintas. Un video de un robot haciendo volteretas en el laboratorio de Boston Dynamics no significa que esos robots están en uso generalizado. Un modelo que resuelve exámenes de medicina en condiciones controladas no es lo mismo que un sistema diagnóstico clínico. Pregunta siempre: ¿esto está en uso real, dónde y bajo qué condiciones?</p>
<h2>Error 4: Reproducir los comunicados de prensa de empresas de IA</h2>
<p>Las empresas de IA tienen incentivos poderosos para exagerar sus capacidades. Los benchmarks que publican son elegidos para mostrarlos favorablemente. Busca evaluaciones independientes, investigación académica, y fuentes críticas antes de reproducir afirmaciones de capacidad.</p>
<h2>Error 5: Ignorar el contexto geopolítico y económico</h2>
<p>La IA no es una tecnología neutral. Los modelos más poderosos están concentrados en pocas empresas de EE.UU. y China. Las decisiones sobre qué datos usan, qué idiomas priorizan, qué sesgos perpetúan tienen consecuencias geopolíticas. Una cobertura que ignora esto es incompleta.</p>
HTML,

            'ia-para-periodistas_3_2' => <<<HTML
<p>El periodista que cubre IA enfrenta un desafío de traducción: cómo explicar sistemas técnicamente complejos a audiencias que no tienen formación en ciencias de la computación, sin simplificar hasta el punto de distorsionar.</p>
<h2>Los conceptos que más se malentienden y cómo explicarlos</h2>
<p><strong>Modelo de lenguaje / LLM:</strong> evita la definición técnica. Úsala así: "un sistema que predice, palabra por palabra, cuál es el texto más probable como respuesta a una pregunta, basándose en patrones en billones de textos humanos." Esto captura lo esencial sin requerir formación técnica.</p>
<p><strong>Entrenamiento:</strong> "el proceso por el que el sistema aprende a asociar patrones en los datos, de manera similar a cómo un estudiante aprende a reconocer patrones en ejemplos." Evita el lenguaje de "la IA aprende como los humanos" —no es preciso.</p>
<p><strong>Alucinación:</strong> el término técnico es útil y ya está extendido, pero explícalo: "cuando el sistema genera información que suena convincente pero es incorrecta o inventada, como si tuviera confianza en algo que no sabe."</p>
<p><strong>Sesgo algorítmico:</strong> "cuando el sistema reproduce o amplifica discriminaciones que estaban en los datos con que fue entrenado. Si entrenas un sistema para predecir desempeño laboral con datos históricos donde las mujeres accedían menos a ciertas posiciones, el sistema aprende esa discriminación como si fuera una verdad."</p>
<h2>Analogías que funcionan y las que no</h2>
<p>Funciona: comparar LLMs con autocomplete muy sofisticado, porque captura la naturaleza predictiva sin sugerir comprensión.</p>
<p>No funciona: comparar la IA con el cerebro humano, porque sugiere procesos cognitivos que no existen en estos sistemas.</p>
<h2>El test de la audiencia</h2>
<p>Antes de publicar, pregúntate: "¿Podría alguien leer esto y tener una imagen correcta de cómo funciona el sistema y qué implicaciones tiene?" Si la respuesta es no, revisa el nivel de abstracción.</p>
HTML,

            'ia-para-periodistas_3_3' => <<<HTML
<p>Una de las dificultades de cubrir IA en Chile y América Latina es la escasez de fuentes locales con criterio independiente. El ecosistema de expertos es pequeño, muchos tienen vínculos con empresas de IA, y las voces críticas son menos visibles. Construir un mapa de fuentes confiables es trabajo editorial necesario.</p>
<h2>Fuentes académicas en Chile</h2>
<p><strong>Centro Nacional de Inteligencia Artificial (CENIA):</strong> institución financiada por el Estado chileno con investigadores en machine learning, procesamiento de lenguaje natural y ética de IA. Sus publicaciones son rigorosas y los investigadores suelen estar disponibles para consultas periodísticas.</p>
<p><strong>Instituto de Sistemas Complejos de Ingeniería (ISCI) — U. de Chile:</strong> trabaja en optimización, análisis de datos y sistemas. Perspectiva más aplicada, con trabajo en sectores como salud, energía y transporte.</p>
<p><strong>Departamento de Ciencias de la Computación — PUC:</strong> investigación en machine learning, visión computacional e IA. Referentes para temas técnicos específicos.</p>
<h2>Voces críticas e independientes</h2>
<p>Busca investigadores que trabajen en ética de IA, derecho digital y sociología de la tecnología. En Chile: académicos del Centro de Estudios de Derecho Informático (CEDI) de la U. de Chile, investigadores del Observatorio de Inteligencia Artificial de la UDP.</p>
<h2>Fuentes internacionales confiables</h2>
<p><strong>AI Now Institute:</strong> investigación crítica sobre impactos sociales de IA. <strong>Partnership on AI:</strong> foco en uso responsable. <strong>MIT Technology Review:</strong> periodismo tecnológico riguroso. <strong>The Markup:</strong> periodismo de datos sobre tecnología y poder.</p>
<h2>Señales de una fuente con conflicto de interés</h2>
<p>Investigador financiado directamente por una empresa de IA cuyo producto está cubriendo. Consultor que cobra a empresas de IA y también opina públicamente sobre regulación. Representantes de asociaciones empresariales de tecnología. No invalida su perspectiva automáticamente, pero hay que declararlo y buscar voces adicionales independientes.</p>
HTML,

            'ia-para-periodistas_3_4' => <<<HTML
<p>El periodismo sobre IA requiere estándares editoriales específicos. No porque la IA sea más difícil de cubrir que otras áreas, sino porque las consecuencias de cubrirla mal —amplificar pánico infundado o exagerar capacidades— son particularmente dañinas en un momento de formación de política pública.</p>
<h2>Estándares básicos para informar sobre IA</h2>
<p><strong>Identificar el sistema específico:</strong> no "la IA", sino "GPT-4 de OpenAI" o "el sistema de reconocimiento facial de X empresa desplegado en Y contexto." La especificidad permite verificación y crítica.</p>
<p><strong>Separar demostración de uso real:</strong> señalar siempre si lo que se describe es un experimento de laboratorio, un proyecto piloto o un sistema en uso generalizado. Son cosas fundamentalmente distintas.</p>
<p><strong>Incluir voces críticas:</strong> la cobertura de IA que solo cita a las empresas que desarrollan los sistemas o a sus inversores no es periodismo equilibrado. Siempre busca expertos independientes y personas afectadas.</p>
<p><strong>Declarar las limitaciones del conocimiento:</strong> el campo cambia rápidamente. Es periodísticamente honesto señalar cuando algo no se sabe, cuando hay incertidumbre científica, o cuando las afirmaciones son disputadas.</p>
<h2>Sobre las afirmaciones de capacidad</h2>
<p>Cuando una empresa afirma que su sistema "supera a los humanos" en alguna tarea, pregunta: ¿en qué benchmark específico? ¿Quién validó el benchmark? ¿Bajo qué condiciones? ¿Qué falla el sistema que los humanos no? Las afirmaciones de superioridad de IA rara vez sobreviven a estas preguntas sin matices importantes.</p>
<h2>Sobre el impacto en personas</h2>
<p>Las mejores coberturas de IA no son sobre la tecnología: son sobre las personas afectadas por ella. Quién perdió el trabajo, quién fue discriminado por un algoritmo, quién se benefició de un diagnóstico más temprano. La IA tiene consecuencias humanas concretas; ese es el eje del periodismo que importa.</p>
HTML,

            // ── PERIODISTAS · MÓDULO 4 ──────────────────────────────────────

            'ia-para-periodistas_4_1' => <<<HTML
<p>La transcripción automática era, hasta hace pocos años, una promesa frustrantemente imprecisa. Hoy es una herramienta de producción real que ya forma parte de la rutina de muchas redacciones. Saber usarla bien multiplica lo que puedes hacer con el material de tus entrevistas.</p>
<h2>Herramientas de transcripción disponibles</h2>
<p><strong>Whisper (OpenAI):</strong> modelo de código abierto con excelente desempeño en español, incluyendo español chileno con sus particularidades fonéticas. Puede instalarse localmente (sin enviar audio a la nube) o usarse a través de interfaces como Whisper.ai o Replicate. Gratuito en su versión base.</p>
<p><strong>Otter.ai:</strong> orientado al inglés pero con soporte para español. Permite transcripción en tiempo real durante entrevistas telefónicas o en persona (con el micrófono del teléfono). Integración con Zoom y Google Meet.</p>
<p><strong>Notta:</strong> alternativa con mejor soporte para español latinoamericano. Permite subir archivos de audio y obtener transcripción editable con marcas de tiempo.</p>
<h2>Flujo de trabajo práctico</h2>
<p>Graba la entrevista (siempre con consentimiento informado y explícito). Sube el audio a la herramienta de transcripción. Obtén el texto base —que tendrá errores, especialmente en nombres propios, términos técnicos y habla informal. Edita y verifica contra el audio en los fragmentos que vas a citar directamente.</p>
<p>La transcripción automática no reemplaza la escucha cuidadosa: reemplaza la transcripción manual y te permite enfocarte en los fragmentos que realmente importan.</p>
<h2>Análisis de entrevistas con IA</h2>
<p>Con la transcripción en texto, puedes usar modelos de lenguaje para: identificar los temas principales, encontrar contradicciones internas en el discurso, comparar lo que una fuente dijo en distintas entrevistas, extraer todas las citas sobre un tema específico en una entrevista larga. Esto es especialmente útil en periodismo de investigación con múltiples fuentes y mucho material.</p>
HTML,

            'ia-para-periodistas_4_2' => <<<HTML
<p>El periodismo de investigación siempre fue un trabajo intensivo en lectura de documentos. La IA no cambia el objetivo —encontrar la historia en los datos— pero cambia radicalmente la escala a la que puede hacerse.</p>
<h2>Analizar documentos masivos</h2>
<p>Los grandes filtros de documentos —Panama Papers, Pandora Papers, archivos Snowden— requirieron equipos internacionales y meses de trabajo. Hoy, herramientas de IA permiten que un periodista solo pueda hacer análisis similares, aunque a menor escala, en días.</p>
<p><strong>Casos de uso concretos en Chile:</strong> analizar contratos de compras públicas para detectar patrones de adjudicación directa, procesar declaraciones de patrimonio de funcionarios en busca de inconsistencias, revisar actas de sesiones del Congreso para rastrear cambios de posición de parlamentarios, analizar bases de datos de sanciones o multas.</p>
<h2>Herramientas para análisis de documentos</h2>
<p><strong>ChatGPT con función de carga de archivos:</strong> permite subir PDFs y hacer preguntas sobre su contenido. Útil para documentos individuales o pequeños conjuntos. Verificar siempre las afirmaciones del modelo contra el documento original.</p>
<p><strong>Claude.ai (Anthropic):</strong> mayor ventana de contexto, permite procesar documentos más largos. Especialmente útil para análisis de contratos, normativas extensas o actas largas.</p>
<p><strong>Elicit y Consensus:</strong> diseñados para análisis de literatura científica. Si el reportaje involucra evidencia científica, estas herramientas permiten revisar cientos de papers en poco tiempo.</p>
<h2>El límite crítico: verificación de lo que dice la IA</h2>
<p>Los modelos de lenguaje pueden "confundir" información entre documentos, inventar citas que no existen o simplificar de forma que distorsiona. Todo hallazgo obtenido con IA en análisis documental debe verificarse contra el documento fuente antes de publicar. La IA te lleva al hallazgo; tú verificas que sea real.</p>
HTML,

            'ia-para-periodistas_4_3' => <<<HTML
<p>El periodismo de datos siempre requirió habilidades técnicas que no todos los periodistas tienen. La IA está democratizando una parte de ese proceso: crear visualizaciones que antes requerían conocimiento de código o diseño especializado.</p>
<h2>Qué puede hacer la IA en visualización</h2>
<p><strong>Generar código para gráficos:</strong> puedes describir los datos que tienes y el tipo de gráfico que necesitas, y modelos como GPT-4 o Claude generan el código Python (matplotlib, plotly) o JavaScript (D3.js, Chart.js) para crearlo. Sin necesidad de saber programar.</p>
<p><strong>Sugerir el tipo de visualización más adecuado:</strong> describe tu conjunto de datos y tu historia, y la IA puede recomendarte si necesitas un gráfico de barras, un mapa de calor, un diagrama de dispersión o un gráfico de líneas de tiempo —y por qué.</p>
<p><strong>Limpiar y estructurar datos:</strong> los datos públicos del Estado chileno suelen venir en formatos inconsistentes, con errores de digitación, campos vacíos. La IA puede ayudar a generar scripts de limpieza de datos sin que el periodista domine pandas o Excel avanzado.</p>
<h2>Herramientas específicas</h2>
<p><strong>Datawrapper:</strong> la herramienta estándar para periodismo de datos. No es IA per se, pero tiene integración con asistentes que sugieren tipos de gráfico. Usada por NYT, BBC, Der Spiegel.</p>
<p><strong>Flourish:</strong> permite crear visualizaciones interactivas más complejas sin código. Con IA puedes generar el script de datos estructurado para Flourish.</p>
<p><strong>Observable con IA:</strong> entorno de código para periodismo de datos donde puedes usar asistentes de IA directamente en el flujo de trabajo.</p>
<h2>Lo que la IA no puede hacer</h2>
<p>Decidir qué historia cuentan los datos. Elegir qué mostrar y qué omitir. Evaluar si una visualización es engañosa o si distorsiona la realidad. Esas decisiones editoriales siguen siendo del periodista.</p>
HTML,

            'ia-para-periodistas_4_4' => <<<HTML
<p>Más allá del análisis y la producción de contenido, la IA puede automatizar una serie de tareas operativas que consumen tiempo en cualquier redacción sin añadir valor periodístico. Identificarlas y automatizarlas libera tiempo para el trabajo que realmente importa.</p>
<h2>Tareas automatizables en la redacción</h2>
<p><strong>Monitoreo de fuentes:</strong> en lugar de revisar manualmente el Diario Oficial, el Congreso, boletines de ministerios y redes sociales de fuentes clave, puedes configurar alertas automáticas y sistemas de síntesis que te notifiquen cuando aparece información relevante. Herramientas como Zapier con feeds RSS, o soluciones más avanzadas como Feedly + IA, permiten esto.</p>
<p><strong>Resumen de reuniones y eventos:</strong> grabación + transcripción automática + resumen con IA de puntos clave de conferencias de prensa, sesiones del Congreso o reuniones de directorio.</p>
<p><strong>SEO básico y metadata:</strong> generación de títulos alternativos para SEO, meta descripciones, extractos para redes sociales. Contenido que el periodista produce pero que la redacción necesita en múltiples formatos para distintas plataformas.</p>
<p><strong>Clasificación y archivo:</strong> etiquetar automáticamente el archivo del medio por temas, personas y organizaciones mencionadas. Útil para redacciones con mucho contenido histórico que quieren hacerlo buscable.</p>
<h2>El principio de automatización responsable</h2>
<p>Automatiza las tareas que no requieren juicio periodístico. No automatices las que sí lo requieren: la selección de qué cubrir, cómo enmarcar la historia, qué fuentes incluir, qué tono usar. La automatización bien aplicada libera al periodista; mal aplicada lo vacía del oficio.</p>
HTML,

            // ── PERIODISTAS · MÓDULO 5 ──────────────────────────────────────

            'ia-para-periodistas_5_1' => <<<HTML
<p>La pregunta de cuándo declarar el uso de IA en una pieza periodística es una de las más debatidas en las redacciones del mundo hoy. No hay consenso universal, pero hay principios que permiten tomar decisiones coherentes.</p>
<h2>El principio de transparencia periodística aplicado a la IA</h2>
<p>El periodismo siempre ha declarado sus métodos cuando hay razones para que el lector los conozca. Declaramos si una fuente pidió anonimato, si el periodista tiene relación con el tema, si el medio tiene intereses económicos en la materia cubierta. La IA es otro caso del mismo principio.</p>
<h2>Cuándo debes declararlo</h2>
<p><strong>Cuando la IA generó contenido que aparece en la pieza:</strong> si la IA redactó párrafos que publicaste sin reescribirlos sustancialmente, eso debe declararse. El lector tiene derecho a saber que el texto no fue escrito íntegramente por el periodista.</p>
<p><strong>Cuando la IA fue determinante en el análisis o hallazgo:</strong> si un sistema de IA identificó la irregularidad que es el corazón de la investigación, declarar el método es parte de la transparencia metodológica, como en periodismo de datos.</p>
<p><strong>Cuando el uso es inédito o podría generar dudas:</strong> si usas una herramienta de IA de una manera que tu audiencia no esperaría, es mejor declararlo y explicar el proceso.</p>
<h2>Cuándo no es necesario declararlo</h2>
<p>Cuando la IA fue usada para tareas instrumentales que no afectan el contenido editorial: corrección ortográfica, transcripción de audio, búsqueda en archivo, traducción de un documento que luego verificaste y reescribiste. De la misma manera que no declaras que usaste Google para buscar un dato, no necesitas declarar que usaste una herramienta de transcripción automática.</p>
<h2>El estándar en construcción</h2>
<p>Organizaciones como SPJ (Society of Professional Journalists), Reuters y AP ya han publicado sus primeras políticas. En Chile, el Colegio de Periodistas no tiene aún lineamientos específicos. Seguir los estándares internacionales más exigentes es la postura más defensible.</p>
HTML,

            'ia-para-periodistas_5_2' => <<<HTML
<p>Si hay un campo donde los límites éticos de la IA son más claros, es el periodismo. El oficio descansa sobre valores —verificación, independencia, responsabilidad humana— que no pueden delegarse a sistemas automáticos sin consecuencias graves.</p>
<h2>Lo que la IA no debe hacer en periodismo</h2>
<p><strong>Inventar o fabricar información:</strong> el límite más básico. La IA genera texto convincente que puede ser factualmente falso. Publicar contenido de IA sin verificación factual es equivalente a fabricar información —con la diferencia de que el sistema no lo hace intencionalmente, pero el daño es el mismo.</p>
<p><strong>Sustituir el contacto con las fuentes:</strong> la entrevista no es solo un mecanismo para obtener información: es una instancia donde el periodista observa, juzga, construye relación y ejerce criterio. Sintetizar lo que "probablemente diría" una fuente con IA es una violación ética fundamental, independientemente de qué tan plausible suene el resultado.</p>
<p><strong>Generar imágenes o audio falso para ilustrar historias reales:</strong> usar deepfakes o imágenes de IA para "ilustrar" eventos que no pueden fotografiarse es fabricar evidencia visual. La línea entre ilustración artística declarada y fabricación de evidencia debe ser explícita y consistente.</p>
<p><strong>Personalizar contenido de forma manipuladora:</strong> usar IA para adaptar el mismo artículo a diferentes sesgos políticos de la audiencia, maximizando el engagement emocional. Esto no es periodismo: es propaganda microfocalizada.</p>
<h2>El criterio de responsabilidad humana</h2>
<p>Una regla útil: si algo sale mal con el contenido publicado, ¿hay un periodista que pueda dar cuenta de cada decisión editorial relevante? Si la respuesta es no porque un sistema automatizado tomó esas decisiones, hay un problema ético. La responsabilidad periodística requiere agencia humana en las decisiones que importan.</p>
HTML,

            'ia-para-periodistas_5_3' => <<<HTML
<p>Una política editorial de IA no es un documento legal ni un manual de procedimientos. Es un marco que ayuda a los periodistas a tomar decisiones coherentes en situaciones que cambian rápidamente, y que da a la audiencia la transparencia que merece.</p>
<h2>Los componentes de una política editorial de IA</h2>
<p><strong>Posición sobre generación de contenido:</strong> ¿puede la IA generar texto que se publique sin reescritura sustancial? ¿En qué circunstancias y con qué condiciones? La mayoría de los medios serios responden que no en general, con excepciones muy acotadas para contenido automatizable de bajo riesgo (resúmenes de datos, alertas).</p>
<p><strong>Política de declaración:</strong> cuándo se declara el uso de IA en el pie de cada pieza o en el manual de estilo. Adoptar un estándar claro y aplicarlo consistentemente.</p>
<p><strong>Herramientas permitidas y prohibidas:</strong> un inventario de qué herramientas de IA están autorizadas para uso interno y para qué propósitos. Esto no es restrictivo por naturaleza: es ordenado y permite que los periodistas trabajen con claridad.</p>
<p><strong>Proceso de verificación adicional para contenido asistido por IA:</strong> qué pasos adicionales se requieren cuando la IA jugó un rol en la producción de una pieza. Quién es responsable de esa verificación.</p>
<h2>Cómo construir la política</h2>
<p>No la construyas solo desde la dirección editorial. Los periodistas que usan (o no usan) las herramientas en su práctica diaria tienen información que los editores no tienen. Una política construida con su participación tiene más probabilidades de ser adoptada y más de reflejar la realidad del trabajo.</p>
<h2>Un ejemplo de política mínima</h2>
<p>"La IA puede usarse como herramienta de apoyo en transcripción, investigación documental y corrección. No puede generar texto que se publique sin edición sustancial del periodista. Todo uso de IA en la producción de una pieza debe declararse en el proceso interno. El periodista responsable de la firma asume responsabilidad editorial por todo el contenido, independientemente de las herramientas usadas."</p>
HTML,

            'ia-para-periodistas_5_4' => <<<HTML
<p>El periodismo ha sobrevivido la imprenta, la radio, la televisión, internet y las redes sociales. No porque se adaptó pasivamente, sino porque redefinió su valor en cada transición. La IA es el siguiente ciclo de esa redefinición.</p>
<h2>Lo que no va a cambiar</h2>
<p>Las funciones que la IA no puede replicar son exactamente las que definen el periodismo en su sentido más profundo: la presencia en el lugar de los hechos, la decisión editorial sobre qué importa y por qué, la construcción de relación con las fuentes, el criterio para distinguir lo verdadero de lo falso cuando ambos suenan igual, la responsabilidad ante la audiencia y la historia.</p>
<p>Estas funciones no son menos valiosas porque la IA existe: son más valiosas.</p>
<h2>Lo que va a cambiar</h2>
<p>El periodista que sepa usar la IA para procesar información a mayor escala, verificar más rápido, acceder a más fuentes y producir más formatos va a tener ventaja. No porque la IA lo haga mejor, sino porque amplifica las capacidades humanas que ya tiene.</p>
<p>Las redacciones van a ser más pequeñas en términos de personal que hace tareas rutinarias, y más especializadas en términos de quienes hacen el trabajo de mayor valor. Eso no es una predicción tranquilizadora para todos, pero es honesta.</p>
<h2>El periodista que más valor tendrá</h2>
<p>El que combine criterio editorial sólido con capacidad de trabajar con IA de forma crítica. Que sepa qué pedirle a la IA, evalúe lo que devuelve, y construya encima de eso con el juicio y la perspectiva que solo viene de la experiencia y la presencia humana.</p>
<div style="background:linear-gradient(135deg,#f59e0b,#ef4444);border-radius:1rem;padding:2rem;text-align:center;margin-top:2rem;">
    <div style="font-size:2rem;margin-bottom:.5rem;">🎓</div>
    <h2 style="color:#fff;font-size:1.15rem;margin-bottom:.5rem;">Curso completado</h2>
    <p style="color:rgba(255,255,255,.9);font-size:.9rem;max-width:420px;margin:0 auto 1rem;">Terminaste <strong>IA para Periodistas y Comunicadores</strong>. Tienes ahora las herramientas para cubrir IA con rigor, verificar contenido sintético y usar IA en tu oficio con criterio editorial.</p>
    <a href="/cursos" style="display:inline-block;background:#fff;color:#0f172a;font-weight:700;padding:.6rem 1.5rem;border-radius:.5rem;text-decoration:none;font-size:.88rem;">Ver otros cursos</a>
</div>
HTML,

            // ── RRHH · MÓDULO 1 ─────────────────────────────────────────────

            'ia-para-rrhh_1_1' => <<<HTML
<p>La inteligencia artificial ya no es una tecnología que RRHH está evaluando adoptar. Para la mayoría de las organizaciones medianas y grandes, ya está presente en al menos uno de sus procesos de personas —aunque el área no siempre lo sepa ni lo haya elegido conscientemente.</p>
<h2>El mapa de la IA en el ciclo del empleado</h2>
<p><strong>Reclutamiento y selección:</strong> filtros automáticos de CV, análisis de entrevistas en video, tests de personalidad con scoring algorítmico, matching de candidatos con perfiles. Es donde la IA más penetró en RRHH y donde más controversia ha generado.</p>
<p><strong>Onboarding:</strong> chatbots que responden preguntas frecuentes de nuevos empleados, plataformas de aprendizaje adaptativas que personalizan el contenido de inducción, sistemas que identifican cuándo un nuevo empleado muestra señales de desconexión temprana.</p>
<p><strong>Gestión del desempeño:</strong> sistemas que recopilan datos de productividad, asistencia, interacciones digitales y generan scores de desempeño, alertas de riesgo de renuncia, sugerencias de feedback.</p>
<p><strong>Desarrollo y aprendizaje:</strong> plataformas de L&D que recomiendan contenidos de formación según el perfil del empleado, identifican brechas de competencias y predicen necesidades de upskilling.</p>
<p><strong>Desvinculación:</strong> modelos predictivos de renuncia voluntaria, análisis de entrevistas de salida, identificación de patrones que preceden la rotación.</p>
<h2>La brecha de conciencia en RRHH chileno</h2>
<p>Muchas organizaciones chilenas usan plataformas internacionales (SAP, Workday, Oracle HCM) que tienen IA integrada como feature estándar, sin que el equipo de RRHH haya tomado una decisión explícita de activar esas funciones. El primer paso es hacer un inventario: ¿qué sistemas usa tu organización y qué componentes de IA tienen activos?</p>
HTML,

            'ia-para-rrhh_1_2' => <<<HTML
<p>El mercado de herramientas de IA para RRHH creció un 40% entre 2022 y 2024 según reportes del sector. Hay cientos de productos disponibles, con niveles de madurez y confiabilidad muy variables. Conocer las categorías principales te permite navegar ese ecosistema con criterio.</p>
<h2>Categorías de herramientas y ejemplos relevantes</h2>
<p><strong>ATS con IA (Applicant Tracking Systems):</strong> Greenhouse, Lever, SmartRecruiters, BambooHR. Gestionan el proceso de selección e incorporan scoring automático de candidatos, filtros de CV y análisis de fit cultural. Son la categoría más extendida en Chile en empresas medianas y grandes.</p>
<p><strong>Análisis de entrevistas en video:</strong> HireVue, Spark Hire, Willo. Analizan la grabación de una entrevista y generan scores sobre competencias, estabilidad emocional y ajuste al perfil. HireVue es el más conocido y también el más criticado por sus metodologías.</p>
<p><strong>Plataformas de people analytics:</strong> Visier, Crunchhr, ChartHop. Consolidan datos de distintos sistemas de RRHH y generan dashboards de rotación, clima, desempeño y riesgo. Permiten cruzar variables que manualmente serían imposibles de analizar.</p>
<p><strong>Herramientas de engagement y clima:</strong> Glint (Microsoft), Culture Amp, Officevibe. Pulsos de clima frecuentes con análisis de sentimiento, identificación de equipos en riesgo, sugerencias de acción para líderes.</p>
<p><strong>Asistentes de RRHH basados en LLM:</strong> la generación más reciente. Plataformas que permiten hacer preguntas en lenguaje natural sobre los datos de personas ("¿cuáles son los equipos con mayor riesgo de rotación este trimestre?") y reciben respuestas con análisis.</p>
<h2>El criterio para elegir</h2>
<p>No el que tiene mejor marketing. El que puede demostrar: validez del modelo (¿cómo saben que predice lo que dice predecir?), transparencia del scoring (¿puedes explicar por qué un candidato fue filtrado?), y cumplimiento con la regulación local de privacidad de datos.</p>
HTML,

            'ia-para-rrhh_1_3' => <<<HTML
<p>El mercado de IA para RRHH tiene un problema estructural: los proveedores tienen fuertes incentivos para exagerar sus capacidades, y los compradores (equipos de RRHH) rara vez tienen la formación técnica para evaluarlas críticamente. El resultado es una brecha entre lo que prometen los demos y lo que entregan en producción.</p>
<h2>Las promesas más frecuentes y la realidad</h2>
<p><strong>"Predice con X% de precisión qué candidatos tendrán mejor desempeño":</strong> el problema es que el "desempeño" suele medirse contra las evaluaciones de desempeño históricas de la empresa, que ya tienen sesgos. Si históricamente los gerentes puntuaron mejor a hombres de cierto perfil, el modelo aprende eso como éxito. La precisión puede ser real; lo que predice puede no ser lo que crees.</p>
<p><strong>"Elimina el sesgo humano del proceso de selección":</strong> lo más frecuente es lo contrario. La IA automatiza los sesgos históricos a escala. Que el proceso sea automático no lo hace objetivo; lo hace más rápido y más difícil de cuestionar.</p>
<p><strong>"Analiza la personalidad y fit cultural desde el video de la entrevista":</strong> no hay evidencia científica robusta de que el análisis de expresión facial, tono de voz y microgestos sea predictor confiable de desempeño laboral. La American Psychological Association y la SIOP (Society for Industrial-Organizational Psychology) han expresado serias reservas sobre estas metodologías.</p>
<h2>Las preguntas que debes hacer a cualquier proveedor</h2>
<p>¿Puedes mostrarme los estudios de validación del modelo, realizados por terceros independientes? ¿Cómo fue definida la variable de "éxito" con la que entrenaron el modelo? ¿Han hecho auditorías de sesgo por género, etnia y edad? ¿Qué pasa si un candidato quiere saber por qué fue filtrado?</p>
<p>Si un proveedor no puede responder estas preguntas con documentación concreta, esa es información suficiente para tu decisión.</p>
HTML,

            'ia-para-rrhh_1_4' => <<<HTML
<p>Antes de tomar decisiones sobre adoptar más IA o gestionar mejor la existente, necesitas un diagnóstico honesto de qué hay en tu organización hoy. Muchas áreas de RRHH descubren en este proceso que usan más IA de lo que pensaban —o que procesos que creían manuales tienen componentes algorítmicos que no conocían.</p>
<h2>El inventario en cuatro pasos</h2>
<p><strong>Paso 1 — Lista todos los sistemas y plataformas:</strong> ATS, HRMS, plataformas de nómina, herramientas de evaluación de desempeño, encuestas de clima, plataformas de aprendizaje, sistemas de control de asistencia. Para cada uno, revisa si tiene features de IA activos (la documentación del proveedor o el equipo de TI pueden ayudar).</p>
<p><strong>Paso 2 — Identifica qué decisiones de personas toman esos sistemas:</strong> ¿el sistema genera scores que influyen en qué candidatos pasan a la siguiente etapa? ¿Genera alertas de riesgo de renuncia que determinan qué empleados reciben intervención? ¿Produce recomendaciones de desarrollo que los líderes siguen sin cuestionarlas?</p>
<p><strong>Paso 3 — Evalúa la transparencia de cada sistema:</strong> ¿sabes cómo se calculan esos scores? ¿Puedes explicarlo a un empleado o candidato si lo pregunta? ¿Hay un humano que toma la decisión final con base en esos datos, o el sistema decide solo?</p>
<p><strong>Paso 4 — Evalúa el cumplimiento de privacidad:</strong> ¿los empleados y candidatos saben qué datos se recopilan sobre ellos y para qué? ¿Hay consentimiento explícito? ¿Los datos se procesan o almacenan fuera de Chile, y bajo qué marcos legales?</p>
<h2>Lo que suele sorprender en el diagnóstico</h2>
<p>Que el proveedor de nómina usa machine learning para detectar "anomalías" en los datos sin que RRHH lo supiera. Que la plataforma de video entrevistas analiza expresión facial aunque esa feature no fue el motivo de la compra. Que el ATS tiene un score de "fit" que los reclutadores usan como referencia sin entender cómo se calcula. El diagnóstico es el punto de partida de la gestión responsable.</p>
HTML,

            // ── RRHH · MÓDULO 2 ─────────────────────────────────────────────

            'ia-para-rrhh_2_1' => <<<HTML
<p>Los filtros automáticos de CV son la aplicación de IA más extendida en RRHH y, por lejos, la que más consecuencias tiene sobre candidatos individuales. Entender cómo funcionan es indispensable para usarlos responsablemente y para defender las decisiones de selección ante candidatos o autoridades.</p>
<h2>Cómo funcionan los filtros de CV con IA</h2>
<p>Los modelos de screening de CV trabajan en dos capas principales:</p>
<p><strong>Extracción de información:</strong> el sistema lee el CV y extrae campos estructurados —nombre, educación, experiencia, habilidades, idiomas. Aquí ya hay fuente de error: el sistema puede no reconocer formatos de CV inusuales, universidades latinoamericanas poco conocidas, o títulos que no coinciden con los nombres que espera.</p>
<p><strong>Scoring contra el perfil:</strong> compara la información extraída con el perfil del cargo y asigna un score de match. Los criterios de scoring son los que definen si el modelo reproduce o amplifica sesgos. Si el perfil incluye "experiencia en empresa Fortune 500", el modelo discrimina sistemáticamente a candidatos de economías emergentes.</p>
<h2>Los sesgos más documentados en filtros de CV</h2>
<p><strong>Sesgo por nombre:</strong> estudios en EE.UU. (Bertrand & Mullainathan, replicado múltiples veces) muestran que CVs idénticos con nombres de mayoría étnica reciben menos callbacks. Los modelos entrenados sobre decisiones históricas de contratación reproducen este patrón.</p>
<p><strong>Sesgo por institución educativa:</strong> los modelos aprenden que candidatos de ciertas universidades "tienen mejor desempeño" —cuando en realidad los candidatos de esas universidades tenían más acceso a redes de mentores y recursos de desarrollo.</p>
<p><strong>Sesgo por gaps en el CV:</strong> períodos de ausencia laboral son sistemáticamente penalizados, afectando desproporcionadamente a personas que cuidaron familiares, atravesaron enfermedades o interrupciones no voluntarias.</p>
<h2>Qué puede hacer RRHH</h2>
<p>Revisar qué variables usa el modelo para el scoring. Auditar si los resultados del filtro producen disparidades por género o perfil demográfico. Nunca usar el score automático como único criterio —sino como herramienta de primera revisión con supervisión humana obligatoria.</p>
HTML,

            'ia-para-rrhh_2_2' => <<<HTML
<p>Las entrevistas en video analizadas por IA son, de todas las herramientas de selección, la que genera más debate ético y científico. En Chile, su uso creció significativamente entre 2020 y 2023 impulsado por la pandemia y la adopción de herramientas como HireVue.</p>
<h2>Qué analizan estos sistemas</h2>
<p>Los sistemas más avanzados analizan múltiples dimensiones simultáneamente: contenido verbal (qué se dice), tono y velocidad del habla, expresión facial (frecuencia de sonrisa, contacto visual, microexpresiones), lenguaje corporal y movimiento de cabeza. Generan scores en dimensiones como "comunicación", "entusiasmo", "estabilidad emocional" y "ajuste al rol".</p>
<h2>La base científica: muy cuestionada</h2>
<p>La teoría detrás del análisis de expresión facial como predictor de personalidad y desempeño laboral proviene principalmente del trabajo de Paul Ekman sobre emociones universales —trabajo que ha sido ampliamente criticado y cuya replicabilidad está en duda. Una revisión sistemática de 2019 en la revista Psychological Science in the Public Interest concluyó que la expresión facial no es un indicador confiable del estado emocional interno.</p>
<p>Dicho de otro modo: que el sistema detecte que alguien sonríe más no significa que esa persona sea más entusiasta, más confiable o mejor candidata para el cargo.</p>
<h2>Los problemas prácticos documentados</h2>
<p>Candidatos con neurodivergencia (autismo, TDAH), con acento no nativo, con condiciones de salud que afectan la expresión facial, o simplemente con estilos de comunicación no anglófonos son sistemáticamente perjudicados. Illinois fue el primer estado de EE.UU. en regular estas herramientas (2020), exigiendo consentimiento explícito y prohibición de usarlas como único criterio.</p>
<h2>La postura responsable para RRHH</h2>
<p>Si usas este tipo de herramientas: asegúrate de que el candidato lo sepa con anticipación y dé consentimiento. No uses el score como criterio definitivo. Siempre combina con entrevista humana. Y evalúa si el modelo ha sido auditado para sesgos en el contexto latinoamericano.</p>
HTML,

            'ia-para-rrhh_2_3' => <<<HTML
<p>Los tests de personalidad y aptitud existen en selección desde hace décadas. Lo que cambió con la IA es la escala, la velocidad, la sofisticación de los análisis —y la opacidad de los modelos que procesan los resultados.</p>
<h2>Tipos de evaluaciones predictivas con componente IA</h2>
<p><strong>Tests de personalidad con scoring algorítmico:</strong> plataformas como Pymetrics, HackerRank Personality o Predictive Index aplican tests y usan ML para predecir ajuste al cargo o al equipo. Pymetrics usa juegos cognitivos y emocionales en lugar de cuestionarios directos, lo que reduce pero no elimina el gaming de respuestas.</p>
<p><strong>Perfiles de comportamiento digital:</strong> algunos sistemas analizan el comportamiento del candidato durante el proceso de postulación —velocidad de respuesta, patrones de revisión, hora del día— para inferir características de personalidad. Esto ocurre sin declaración explícita en muchos casos.</p>
<p><strong>Tests de aptitud cognitiva adaptativos:</strong> el nivel de dificultad cambia en función de las respuestas anteriores. Más precisos que los tests lineales, pero igualmente dependientes de que el constructo que miden esté validado para el cargo específico.</p>
<h2>La pregunta crítica: ¿válidos para qué?</h2>
<p>La validez de cualquier evaluación psicométrica depende de dos condiciones: que mida lo que dice medir (validez de constructo) y que ese constructo prediga el desempeño en el cargo específico (validez predictiva). Los proveedores suelen tener estudios de validez en muestras norteamericanas o europeas. ¿Esos estudios son relevantes para tu cargo, en tu industria, en Chile? Esa pregunta debe hacerse siempre.</p>
<h2>Los límites que RRHH debe establecer</h2>
<p>Ningún test de personalidad o perfil de IA debe ser condición eliminatoria por sí solo. Deben complementar, no reemplazar, la evaluación por competencias y la entrevista por comportamientos. Los candidatos tienen derecho a conocer si fueron evaluados con estas herramientas y qué información se procesó.</p>
HTML,

            'ia-para-rrhh_2_4' => <<<HTML
<p>Los casos documentados de fallas en sistemas de IA para selección no son anécdotas: son patrones que muestran qué pasa cuando se adoptan estas herramientas sin la evaluación y supervisión adecuadas.</p>
<h2>Amazon: el caso que cambió la conversación</h2>
<p>En 2018, Reuters reveló que Amazon había desarrollado internamente un sistema de IA para screening de CV que resultó discriminar sistemáticamente a mujeres. El sistema fue entrenado con CVs de candidatos contratados en los diez años anteriores, período en que la gran mayoría de los contratados en roles técnicos eran hombres. El modelo aprendió que "hombre" era un predictor de éxito y penalizaba CVs con términos como "equipo de mujeres" o con graduación de universidades femeninas. Amazon descontinuó el sistema en 2017, pero la historia se conoció un año después.</p>
<h2>HireVue y el sesgo en expresión facial</h2>
<p>Múltiples investigaciones independientes encontraron que el sistema de análisis facial de HireVue producía scores más bajos para personas de tez oscura, personas con discapacidades visibles y personas con estilos de comunicación no anglosajones. En 2021, HireVue descontinuó el análisis de expresión facial tras presión regulatoria y de investigadores.</p>
<h2>El caso de los algoritmos de riesgo de renuncia</h2>
<p>Varias empresas usan sistemas que predicen qué empleados tienen mayor probabilidad de renunciar en los próximos 6-12 meses, con el objetivo de intervenir con retención. El problema documentado: estos sistemas pueden crear profecías autocumplidas —empleados identificados como "riesgo de renuncia" que no reciben oportunidades de desarrollo porque la empresa ya los da por perdidos, lo que efectivamente los lleva a renunciar.</p>
<h2>La lección transversal</h2>
<p>Ningún sistema de IA opera en un vacío. Opera sobre datos históricos con sesgos históricos, en organizaciones con culturas y estructuras de poder específicas. El rol de RRHH es entender esos contextos y no delegar la responsabilidad sobre las decisiones de personas a un algoritmo que no puede asumir esa responsabilidad.</p>
HTML,

            // ── RRHH · MÓDULO 3 ─────────────────────────────────────────────

            'ia-para-rrhh_3_1' => <<<HTML
<p>El sesgo algorítmico en selección no es un bug técnico ni un accidente. Es el resultado predecible de entrenar modelos sobre datos que reflejan desigualdades históricas. Entender de dónde viene es el primer paso para poder identificarlo y mitigarlo.</p>
<h2>La fuente primaria: los datos de entrenamiento</h2>
<p>La mayoría de los sistemas de IA para selección son entrenados sobre datos históricos de la propia organización o del sector: quiénes fueron contratados, quiénes tuvieron buen desempeño, quiénes fueron promovidos. Si esos datos reflejan una historia donde ciertos grupos tuvieron menos acceso a oportunidades, el modelo aprende esa historia como norma.</p>
<p>El ejemplo más claro: si en los últimos diez años el 80% de las personas contratadas para roles de liderazgo eran hombres, el modelo aprende que el perfil de liderazgo exitoso es masculino. No porque el algoritmo tenga prejuicios conscientes, sino porque eso es lo que dicen los datos.</p>
<h2>La amplificación del sesgo</h2>
<p>Los algoritmos no solo reproducen el sesgo histórico: tienden a amplificarlo. Esto ocurre porque el modelo optimiza la similitud con los candidatos históricamente exitosos, creando un ciclo donde los perfiles diversos son sistemáticamente descontados aunque tengan las competencias necesarias.</p>
<h2>Sesgos proxy: el problema de las variables indirectas</h2>
<p>El sesgo no siempre viene de variables protegidas directas (género, etnia, edad). Viene de variables proxy que se correlacionan con ellas: el código postal correlaciona con nivel socioeconómico y, en muchos países, con etnia. La universidad correlaciona con género en ciertas carreras. El gap en el CV correlaciona con maternidad. El modelo puede discriminar usando estas variables sin que aparezca ninguna variable protegida explícita.</p>
<h2>Por qué RRHH debe entender esto</h2>
<p>Porque cuando un candidato, un empleado o una autoridad regulatoria pregunta "¿por qué fue descartado?", la respuesta "el sistema lo filtró" no es jurídicamente ni éticamente suficiente. RRHH es responsable de las decisiones de personas, independientemente de si las tomó un humano o un algoritmo.</p>
HTML,

            'ia-para-rrhh_3_2' => <<<HTML
<p>Detectar si un sistema de IA está produciendo resultados discriminatorios no requiere ser científico de datos. Requiere saber qué mirar, cómo organizar los datos disponibles y qué patrones son señales de alerta.</p>
<h2>El análisis de impacto dispar (disparate impact)</h2>
<p>El concepto legal y estadístico más relevante es el de impacto dispar: cuando una práctica de selección produce tasas de aprobación significativamente distintas entre grupos protegidos, esa práctica es potencialmente discriminatoria —independientemente de si fue diseñada para serlo.</p>
<p>La regla del 80%: si la tasa de aprobación de un grupo es menor al 80% de la tasa del grupo con mayor aprobación, hay una señal de alerta estadística. Ejemplo: si el sistema aprueba al 50% de los hombres que postulan a una etapa pero solo al 30% de las mujeres, la tasa es 30/50 = 60%, bajo el umbral del 80%.</p>
<h2>Cómo hacer el análisis con los datos que ya tienes</h2>
<p>Para cada etapa del proceso donde opera la IA, necesitas saber: ¿cuántas personas de cada grupo (género, rango etario, perfil educativo) entran? ¿Cuántas pasan? La diferencia entre esas tasas es el impacto dispar.</p>
<p>Si tu ATS tiene reportería, este análisis puede hacerse con los datos que ya tienes. Si no tiene reportería adecuada, esa es en sí misma una señal de que el proveedor no está diseñado para la transparencia.</p>
<h2>Señales de alerta cualitativas</h2>
<p>¿Los reclutadores están anulando manualmente los filtros del sistema con frecuencia? Eso puede indicar que el sistema está eliminando candidatos que los humanos evalúan como buenos. ¿Los candidatos de ciertos perfiles se quejan sistemáticamente de haber sido filtrados sin razón? ¿La diversidad en las etapas iniciales del proceso desaparece hacia las etapas finales?</p>
HTML,

            'ia-para-rrhh_3_3' => <<<HTML
<p>Una auditoría de sesgo es el proceso formal de evaluar si un sistema de IA produce resultados discriminatorios. No es un trámite burocrático: es el mecanismo que permite a RRHH defender sus procesos ante candidatos, empleados, reguladores y líderes de la organización.</p>
<h2>Qué es una auditoría de sesgo y qué no es</h2>
<p>Una auditoría de sesgo NO es que el proveedor de la herramienta te diga que su sistema "está diseñado para ser justo". Los proveedores tienen conflicto de interés para autoauditarse favorablemente. Una auditoría real es realizada por un tercero independiente, con acceso a los datos de entrenamiento, el código del modelo o sus outputs, y produce un informe con metodología, hallazgos y limitaciones explícitas.</p>
<h2>Los componentes de una auditoría robusta</h2>
<p><strong>Auditoría de datos de entrenamiento:</strong> ¿qué datos se usaron? ¿Qué sesgos históricos contienen? ¿Están documentados?</p>
<p><strong>Auditoría de impacto dispar:</strong> análisis estadístico de si el modelo produce tasas de aprobación distintas por grupos protegidos.</p>
<p><strong>Auditoría de validez predictiva:</strong> ¿el modelo realmente predice lo que dice predecir? ¿Con qué precisión y en qué condiciones?</p>
<p><strong>Auditoría de explicabilidad:</strong> ¿pueden los operadores del sistema explicar por qué se tomó una decisión específica sobre un candidato específico?</p>
<h2>Cómo pedirla a un proveedor</h2>
<p>Antes de contratar: pide el reporte de auditoría de sesgo más reciente, realizado por terceros. Si no existe o no está disponible, pondera eso en tu decisión. En el contrato: incluye el derecho a solicitar auditorías periódicas y a recibir documentación actualizada sobre cambios en el modelo. Algunos reguladores (Nueva York, Illinois, UE) ya lo exigen legalmente; en Chile no es obligatorio aún, pero es buena práctica.</p>
HTML,

            'ia-para-rrhh_3_4' => <<<HTML
<p>Cuando un sistema de IA discrimina en la selección, ¿quién es responsable? La respuesta que muchas organizaciones esperan —"el proveedor de la tecnología"— no es la que da la ley. Y entender eso cambia cómo RRHH debe relacionarse con estas herramientas.</p>
<h2>El principio de responsabilidad del empleador</h2>
<p>En el derecho laboral chileno, como en la mayoría de los marcos legales comparados, el empleador es responsable de sus procesos de selección. Si un proceso discrimina —sea porque lo ejecutó un humano o porque lo delegó a un algoritmo— la responsabilidad legal recae sobre la organización que tomó la decisión, no sobre el proveedor de software.</p>
<p>La ley 20.609 (Antidiscriminación) y el Código del Trabajo establecen prohibiciones de discriminación en el acceso al empleo. La eventual Ley de IA que está en proceso de elaboración en Chile probablemente reforzará la responsabilidad del "deployer" (quien usa el sistema) versus el "developer" (quien lo crea).</p>
<h2>El AI Act europeo como referencia</h2>
<p>Chile no tiene aún regulación específica de IA. Pero el AI Act de la Unión Europea (2024) clasifica los sistemas de IA para selección, evaluación de desempeño y monitoreo laboral como "alto riesgo", con obligaciones específicas de transparencia, documentación, evaluación de impacto y supervisión humana. Muchas empresas chilenas con operaciones en Europa o que usan proveedores europeos ya están siendo afectadas por este marco.</p>
<h2>Lo que RRHH debe documentar</h2>
<p>Para cada sistema de IA que use en procesos de personas: qué decisiones toma o informa, qué evidencia hay de validez y ausencia de sesgo, qué supervisión humana existe sobre esas decisiones, y qué información reciben los candidatos y empleados. Esta documentación es tu escudo ante una reclamación de discriminación.</p>
HTML,

            // ── RRHH · MÓDULO 4 ─────────────────────────────────────────────

            'ia-para-rrhh_4_1' => <<<HTML
<p>El monitoreo de empleados siempre existió. Lo que ha cambiado es la escala, la continuidad y la granularidad con que puede hacerse gracias a la tecnología digital e IA. Entender qué existe hoy es el punto de partida para decidir qué es aceptable en tu organización.</p>
<h2>El espectro del monitoreo con IA</h2>
<p><strong>Monitoreo de productividad:</strong> sistemas que registran el tiempo activo en el computador, las aplicaciones usadas, los sitios visitados, la velocidad de escritura, el volumen de trabajo procesado. Herramientas como Hubstaff, Time Doctor o Teramind están diseñadas explícitamente para esto. Algunas plataformas de gestión de proyectos tienen features similares integrados.</p>
<p><strong>Análisis de comunicaciones internas:</strong> sistemas que analizan el contenido, frecuencia y tono de los emails, mensajes de Slack o Teams para detectar señales de desconexión, conflicto o riesgo de renuncia. Microsoft Viva Insights incluye features de este tipo, aunque con limitaciones de privacidad.</p>
<p><strong>Monitoreo físico:</strong> control de acceso, cámaras con análisis de presencia y movimiento, sistemas de reconocimiento facial para marcaje de asistencia. Más comunes en manufactura, retail y logística.</p>
<p><strong>Análisis de comportamiento en herramientas de trabajo:</strong> quién responde emails más rápido, quién participa más en reuniones, quién trabaja fuera de horario. Datos que los sistemas de colaboración registran y que algunos productos de people analytics agregan y analizan.</p>
<h2>La distinción clave</h2>
<p>No es lo mismo monitorear resultados (¿se completaron los objetivos?) que monitorear procesos (¿cuántos minutos pasó activo?). La primera es gestión del desempeño tradicional. La segunda es vigilancia que puede afectar fundamentalmente la dignidad y autonomía del empleado —con consecuencias sobre clima, confianza y rotación.</p>
HTML,

            'ia-para-rrhh_4_2' => <<<HTML
<p>Chile tiene un marco legal sobre privacidad y vigilancia laboral que muchas organizaciones no conocen en detalle. El desconocimiento no exime de responsabilidad, y las consecuencias de violar esos límites van desde multas hasta conflictos laborales complejos.</p>
<h2>La Constitución y el Código del Trabajo</h2>
<p>El artículo 19 de la Constitución garantiza el respeto a la vida privada y la honra de las personas. El Código del Trabajo, en su artículo 154, exige que el reglamento interno de la empresa establezca las normas de higiene y seguridad —y por extensión, cualquier política de monitoreo debe ser conocida por los trabajadores.</p>
<p>El artículo 5° del Código del Trabajo es fundamental: establece que el empleador, en ejercicio de sus facultades, no puede afectar la dignidad del trabajador, sus derechos fundamentales ni su vida privada. El monitoreo que invade la privacidad sin justificación legítima viola este principio.</p>
<h2>La Ley 19.628 de Protección de Datos Personales</h2>
<p>Es la ley vigente que regula el tratamiento de datos personales en Chile. Para el contexto laboral, establece que los datos de los trabajadores solo pueden recopilarse y usarse para fines legítimos, específicos y proporcionales —y que los titulares deben ser informados de qué datos se recopilan y para qué.</p>
<p>Chile está en proceso de reemplazar esta ley por una nueva (en tramitación legislativa a 2025) que se alinea con el RGPD europeo y será significativamente más exigente, incluyendo la figura de la Agencia de Protección de Datos Personales con capacidad sancionatoria.</p>
<h2>El criterio de proporcionalidad y finalidad legítima</h2>
<p>Cualquier medida de monitoreo debe cumplir dos condiciones para ser legalmente defensible: proporcionalidad (el nivel de vigilancia debe ser proporcional al objetivo legítimo que se busca) y finalidad legítima (el objetivo debe ser claramente justificable, no la vigilancia por sí misma). Registrar las pulsaciones por minuto de un trabajador administrativo no cumple estos criterios.</p>
HTML,

            'ia-para-rrhh_4_3' => <<<HTML
<p>El debate sobre monitoreo de empleados tiene una tensión real y legítima en su centro: las organizaciones tienen intereses válidos en verificar el cumplimiento de objetivos y proteger sus activos; los empleados tienen derechos fundamentales a la privacidad y la dignidad. La IA intensifica esa tensión al hacer el monitoreo más continuo y más invasivo que nunca.</p>
<h2>Por qué el monitoreo excesivo es contraproducente</h2>
<p>La investigación en psicología organizacional es consistente: el monitoreo percibido como invasivo reduce la motivación intrínseca, aumenta el estrés y la rotación, destruye la confianza y genera comportamientos de conformidad superficial en lugar de compromiso genuino. Los empleados monitoreados en exceso aprenden a optimizar las métricas visibles, no a hacer buen trabajo.</p>
<p>Un estudio de MIT (2021) encontró que las organizaciones que implementaron monitoreo intensivo durante el trabajo remoto pandémico tuvieron rotación 30% mayor en los 18 meses siguientes que las que confiaron en los resultados.</p>
<h2>Cuándo el monitoreo tiene sentido</h2>
<p>Hay contextos donde el monitoreo es justificado y esperado: roles con acceso a información sensible, cumplimiento de normativas regulatorias, seguridad física. El principio es que el nivel de monitoreo sea proporcional al nivel de riesgo o al requisito normativo, no una práctica generalizada aplicada a todos.</p>
<h2>La pregunta que debes hacerte como líder de RRHH</h2>
<p>¿El monitoreo que estás considerando existe porque confías en los datos más que en los líderes para gestionar el desempeño? Si la respuesta es sí, el problema puede no ser falta de vigilancia: puede ser falta de capacidad de liderazgo para gestionar sin métricas de proceso. El monitoreo que reemplaza la gestión directa no es una solución —es una señal de que hay otro problema que resolver.</p>
HTML,

            'ia-para-rrhh_4_4' => <<<HTML
<p>Si tu organización va a usar tecnología de monitoreo —y muchas tienen razones legítimas para hacerlo en ciertos contextos— la diferencia entre una política aceptable y una que destruye la cultura organizacional está en cómo se diseña, comunica y aplica.</p>
<h2>Los cinco principios de una política de monitoreo aceptable</h2>
<p><strong>1. Transparencia absoluta:</strong> los empleados deben saber exactamente qué se monitorea, cómo se usan los datos, quién tiene acceso y por cuánto tiempo se almacena. Sin excepciones. La vigilancia encubierta es ilegal en Chile y éticamente indefendible.</p>
<p><strong>2. Proporcionalidad:</strong> el nivel de monitoreo debe justificarse con un objetivo legítimo específico. Monitorear el cumplimiento de horario es distinto de monitorear cada pulsación de teclado. Define qué necesitas realmente y monitorea solo eso.</p>
<p><strong>3. Finalidad limitada:</strong> los datos recopilados para un fin no pueden usarse para otro. Si recopilas datos de acceso a sistemas por seguridad informática, no puedes usarlos para evaluar el desempeño del empleado sin que ese sea un uso declarado explícitamente.</p>
<p><strong>4. Participación de los empleados en el diseño:</strong> las políticas de monitoreo que se construyen con participación de los trabajadores (a través de representantes, comités o consultas) tienen más legitimidad y más probabilidades de ser percibidas como justas.</p>
<p><strong>5. Revisión periódica:</strong> las tecnologías cambian y los contextos organizacionales también. Una política de monitoreo no debe ser permanente sin revisión. Define plazos de evaluación y mecanismos para que los empleados planteen preocupaciones.</p>
<h2>El test final</h2>
<p>Antes de implementar cualquier tecnología de monitoreo, hazte esta pregunta: ¿estarías dispuesto a explicar públicamente —a los empleados, a la prensa, a un regulador— exactamente qué monitoreas y por qué? Si la respuesta es no, reconsidera.</p>
HTML,

            // ── RRHH · MÓDULO 5 ─────────────────────────────────────────────

            'ia-para-rrhh_5_1' => <<<HTML
<p>El mercado de IA para RRHH está lleno de promesas atractivas y de proveedores con distintos niveles de seriedad, transparencia y madurez tecnológica. Saber evaluar a un proveedor antes de firmar un contrato es una competencia crítica para el área de personas.</p>
<h2>Las dimensiones de evaluación</h2>
<p><strong>Validez del modelo:</strong> ¿el sistema ha sido validado para predecir lo que dice predecir? ¿En qué industria, qué país, qué tamaño de empresa? ¿Los estudios de validación fueron realizados por terceros independientes o por el propio proveedor?</p>
<p><strong>Transparencia y explicabilidad:</strong> ¿puedes saber por qué el sistema tomó una decisión específica sobre un candidato o empleado? Si la respuesta es "el modelo es una caja negra", eso es un riesgo legal y ético que debes ponderar.</p>
<p><strong>Auditorías de sesgo:</strong> ¿el proveedor tiene documentación de auditorías de impacto dispar por grupos protegidos? ¿Qué encontraron y qué hicieron al respecto?</p>
<p><strong>Privacidad y seguridad de datos:</strong> ¿dónde se almacenan los datos? ¿Bajo qué marco legal? ¿Cómo se manejan las brechas de seguridad? ¿Pueden los datos ser usados para entrenar modelos que comparte con otros clientes?</p>
<p><strong>Cumplimiento regulatorio:</strong> ¿el proveedor conoce la regulación chilena de protección de datos y las tendencias regulatorias globales? ¿Puede documentar cumplimiento con marcos como RGPD si corresponde?</p>
<h2>Las preguntas que revelan más</h2>
<p>"¿Pueden mostrarme un caso donde su sistema produjo resultados discriminatorios y qué hicieron?" Los proveedores serios tienen respuestas honestas. Los que dicen que nunca ocurrió están mintiendo o no lo saben —ambas respuestas son señales de alerta.</p>
HTML,

            'ia-para-rrhh_5_2' => <<<HTML
<p>La decisión de implementar un sistema de IA para personas no debería tomarse solo en base a la propuesta comercial del proveedor. Hay un conjunto de preguntas internas y externas que deben responderse antes de firmar cualquier contrato.</p>
<h2>Preguntas internas antes de implementar</h2>
<p><strong>¿Cuál es el problema específico que estamos resolviendo?</strong> "Queremos usar IA en RRHH" no es suficiente. "Necesitamos reducir el tiempo de screening de CV en procesos con más de 500 candidatos sin bajar la calidad" es un problema específico que puede tener una solución específica y evaluable.</p>
<p><strong>¿Tenemos los datos necesarios para que el sistema funcione?</strong> Muchos sistemas de IA requieren datos históricos de calidad para operar bien. Si tu organización no tiene datos estructurados y completos de selección o desempeño, el sistema puede no producir los resultados prometidos.</p>
<p><strong>¿Quién tiene la decisión final sobre las recomendaciones del sistema?</strong> Definir esto antes de implementar es crítico. El sistema recomienda; ¿quién decide? ¿Qué pasa si el humano decide diferente al algoritmo?</p>
<p><strong>¿Cómo lo vamos a comunicar a los candidatos y empleados?</strong> Si el sistema va a afectar a personas, esas personas tienen derecho a saberlo. ¿Cuál es el plan de comunicación?</p>
<h2>Preguntas al proveedor antes de contratar</h2>
<p>¿Qué pasa con mis datos si dejo de usar el servicio? ¿Puedo exportarlos? ¿Se eliminarán de sus sistemas? ¿El contrato me permite auditar el sistema o exigir cambios si detecto sesgo? ¿Qué soporte existe para entender los outputs del sistema, no solo para instalarlo?</p>
<h2>La implementación por fases</h2>
<p>Implementa primero en un proceso o área piloto. Mide el impacto antes de escalar. Los sistemas que funcionan perfectamente en la demo pueden tener comportamientos inesperados con tus datos y tus procesos específicos.</p>
HTML,

            'ia-para-rrhh_5_3' => <<<HTML
<p>Una de las principales causas de resistencia y conflicto cuando se implementa IA en RRHH es la falta de comunicación oportuna y transparente con los empleados. Las personas que sienten que les aplican tecnologías sin explicación desarrollan desconfianza que es muy difícil de revertir.</p>
<h2>Qué deben saber los empleados</h2>
<p><strong>Qué sistemas se usan:</strong> qué herramientas de IA están activas en procesos que los afectan. No el detalle técnico, pero sí la información suficiente para entender cómo funciona el proceso.</p>
<p><strong>Qué datos se recopilan:</strong> qué información personal o de comportamiento se procesa, con qué finalidad y durante cuánto tiempo.</p>
<p><strong>Cómo afecta las decisiones sobre ellos:</strong> si el sistema influye en decisiones de contratación, evaluación, desarrollo o desvinculación, los empleados tienen derecho a saberlo. Que una decisión sea informada por IA no la hace menos sujeta a cuestionamiento.</p>
<p><strong>Cómo pueden obtener información o apelar:</strong> si un candidato fue filtrado por un sistema de IA, ¿puede pedir explicación? ¿Puede apelar? ¿Cuál es el proceso?</p>
<h2>Cómo comunicarlo sin generar pánico</h2>
<p>El framing importa. "Estamos implementando IA para vigilar la productividad" genera rechazo. "Estamos usando herramientas de análisis de datos para entender mejor cómo trabajan los equipos y qué apoyo necesitan" puede generar lo mismo, pero en tono colaborativo —siempre que sea verdad.</p>
<p>La comunicación debe ser honesta, específica y abierta a preguntas. Los empleados que sienten que se les explica con respeto tienen más probabilidades de colaborar que los que sienten que la información se les esconde.</p>
<h2>El rol de los representantes de trabajadores</h2>
<p>Si tu organización tiene sindicato o comité de empresa, involucrarlos en el proceso de evaluación e implementación antes de hacerlo público general reduce el riesgo de conflicto y aumenta la legitimidad del proceso.</p>
HTML,

            'ia-para-rrhh_5_4' => <<<HTML
<p>Chile no tiene aún una ley específica de inteligencia artificial, pero el marco regulatorio que viene está tomando forma. Los profesionales de RRHH que entiendan esa trayectoria podrán anticiparse y evitar tener que hacer cambios costosos de urgencia cuando la regulación llegue.</p>
<h2>El panorama regulatorio actual en Chile</h2>
<p>La Ley 19.628 de Protección de Datos Personales (actualmente en proceso de reemplazo) establece el marco base. El Código del Trabajo establece los límites del poder de dirección del empleador. La Ley 20.609 prohíbe la discriminación en el acceso al empleo. Ninguna de estas leyes menciona explícitamente la IA, pero todas aplican a sus usos.</p>
<p>La nueva ley de datos personales en tramitación fortalecerá significativamente los derechos de los titulares, incluyendo el derecho a oponerse a decisiones automatizadas que los afecten significativamente —lo que impactará directamente los procesos de selección y evaluación con IA.</p>
<h2>Lo que viene: tendencias globales que llegarán a Chile</h2>
<p><strong>Derecho a explicación:</strong> el derecho a saber por qué un sistema de IA tomó una decisión sobre ti (ya existe en el RGPD europeo y en la nueva ley de datos chilena en tramitación).</p>
<p><strong>Prohibición de decisiones automatizadas exclusivas:</strong> en procesos que afecten significativamente a personas, siempre debe haber una instancia de revisión humana.</p>
<p><strong>Obligación de evaluación de impacto:</strong> antes de implementar sistemas de IA de alto riesgo en procesos de personas, documentar los riesgos identificados y las medidas de mitigación.</p>
<h2>Cómo prepararse hoy</h2>
<p>Documenta todos los sistemas de IA que usas en procesos de personas. Establece supervisión humana sobre las decisiones que influyen. Comunica a candidatos y empleados el uso de IA que los afecta. Exige a los proveedores evidencia de validez y auditoría de sesgo. Las organizaciones que hacen esto hoy no tendrán que rehacer sus procesos cuando la regulación llegue.</p>
<div style="background:linear-gradient(135deg,#38b6ff,#0ea5e9);border-radius:1rem;padding:2rem;text-align:center;margin-top:2rem;">
    <div style="font-size:2rem;margin-bottom:.5rem;">🎓</div>
    <h2 style="color:#fff;font-size:1.15rem;margin-bottom:.5rem;">Curso completado</h2>
    <p style="color:rgba(255,255,255,.9);font-size:.9rem;max-width:420px;margin:0 auto 1rem;">Terminaste <strong>IA para RRHH y Gestión de Personas</strong>. Tienes ahora las herramientas para implementar IA en procesos de personas con criterio ético, legal y estratégico.</p>
    <a href="/cursos" style="display:inline-block;background:#fff;color:#0f172a;font-weight:700;padding:.6rem 1.5rem;border-radius:.5rem;text-decoration:none;font-size:.88rem;">Ver otros cursos</a>
</div>
HTML,

            // ── SALUD · MÓDULO 1 ────────────────────────────────────────────

            'ia-para-salud_1_1' => <<<HTML
<p>La inteligencia artificial lleva más de una década aplicándose en medicina. Pero entre 2020 y 2025 ocurrió un salto cualitativo: los sistemas pasaron de ser herramientas de investigación a convertirse en dispositivos aprobados regulatoriamente para uso clínico real. Entender ese estado del arte es el punto de partida para cualquier profesional de la salud.</p>
<h2>Las áreas donde la IA clínica tiene más madurez</h2>
<p><strong>Diagnóstico por imágenes:</strong> es donde la IA médica tiene los resultados más sólidos y los más regulados. Sistemas de análisis de radiografías de tórax, mamografías, imágenes de retina, dermatoscopia y patología digital tienen aprobación de la FDA y la CE y están en uso clínico en múltiples países.</p>
<p><strong>Análisis de señales fisiológicas:</strong> algoritmos de ECG que detectan fibrilación auricular (el de Apple Watch tiene aprobación FDA), análisis de patrones en UCI, detección de sepsis en tiempo real mediante análisis de parámetros vitales.</p>
<p><strong>Medicina de precisión y genómica:</strong> IA para análisis de variantes genéticas, predicción de respuesta a tratamientos oncológicos, identificación de subtipos de enfermedad con implicaciones terapéuticas distintas.</p>
<p><strong>Asistencia al diagnóstico clínico:</strong> sistemas de apoyo a la decisión clínica (CDSS) que analizan la historia clínica completa y alertan sobre diagnósticos diferenciales, interacciones medicamentosas o indicadores de deterioro.</p>
<h2>Lo que distingue la madurez clínica de la promesa tecnológica</h2>
<p>Que un modelo tenga buen desempeño en un benchmark académico no significa que sea clínicamente útil. La madurez clínica requiere validación en poblaciones diversas, integración al flujo de trabajo real, estudios de impacto en outcomes del paciente —no solo en métricas del modelo— y aprobación regulatoria. Muchos productos que se anuncian como "IA médica" no han pasado por este proceso.</p>
HTML,

            'ia-para-salud_1_2' => <<<HTML
<p>Las tres especialidades donde la IA ha producido los avances clínicamente más validados son radiología, patología y cardiología. Conocer el estado actual en cada una permite al profesional evaluar qué herramientas son reales y cuáles son principalmente marketing.</p>
<h2>Radiología</h2>
<p>Es la especialidad con mayor adopción de IA clínica. Los algoritmos de detección de nódulos pulmonares en TC de tórax, fracturas en radiografías y hallazgos críticos (neumotórax, hemorragia intracraneal) tienen rendimiento validado en múltiples estudios clínicos. El sistema Annalise Enterprise, aprobado por la FDA, analiza radiografías de tórax y detecta más de 120 hallazgos.</p>
<p>El modelo de uso más extendido no es que la IA reemplace al radiólogo, sino que prioriza la lista de trabajo: estudios con hallazgos críticos pasan primero a revisión humana, reduciendo el tiempo hasta la decisión clínica.</p>
<h2>Patología</h2>
<p>La patología digital —escanear preparados histológicos y analizarlos con IA— permite cuantificación precisa de marcadores tumorales, clasificación de grado en cáncer de próstata y predicción de respuesta a inmunoterapia desde la imagen de la biopsia. Paige Prostate, aprobado por la FDA, detecta cáncer de próstata con sensibilidad comparable a patólogos expertos.</p>
<h2>Cardiología</h2>
<p>Más allá del ECG, la IA permite análisis automático de ecocardiografías (fracción de eyección, valvulopatías), detección de fibrilación auricular subclínica y predicción de riesgo de eventos cardiovasculares a partir de imágenes de retina —un hallazgo que abrió un campo de investigación nuevo.</p>
<h2>El patrón común</h2>
<p>En las tres especialidades, la IA funciona mejor como amplificador de la capacidad del especialista —no como sustituto. Los sistemas más exitosos clínicamente son los que se integraron al flujo de trabajo real con un rol definido, no los que intentaron reemplazar el juicio clínico.</p>
HTML,

            'ia-para-salud_1_3' => <<<HTML
<p>Las urgencias y la UCI son los entornos clínicos donde la velocidad y la cantidad de datos son más críticas —y donde la IA predictiva tiene el potencial de mayor impacto. También donde los errores tienen consecuencias más inmediatas.</p>
<h2>Predicción de deterioro y sepsis</h2>
<p>El sepsis es una de las principales causas de muerte hospitalaria y su mortalidad aumenta significativamente con cada hora de retraso en el tratamiento. Los sistemas de alerta temprana basados en IA —que monitorean en tiempo real parámetros vitales, resultados de laboratorio e historia clínica— han mostrado en estudios prospectivos reducción de la mortalidad cuando se integran correctamente al flujo de trabajo.</p>
<p>Epic Sepsis Model, uno de los más usados en EE.UU., fue sometido a una auditoría independiente publicada en JAMA en 2021 que encontró que generaba demasiados falsos positivos en condiciones reales, provocando fatiga de alarma y reduciendo la respuesta del personal. El caso ilustra perfectamente la diferencia entre desempeño en desarrollo y utilidad en producción.</p>
<h2>Triaje asistido por IA</h2>
<p>Sistemas que analizan los datos del paciente al ingreso de urgencias para predecir qué pacientes requieren atención inmediata, tienen mayor riesgo de hospitalización o de mortalidad intrahospitalaria. Permiten priorizar recursos en entornos de alta demanda.</p>
<h2>Monitoreo continuo en UCI</h2>
<p>Los pacientes de UCI generan volúmenes de datos imposibles de monitorear manualmente en su totalidad. Los sistemas de IA pueden detectar patrones en esos datos —cambios en la variabilidad de la frecuencia cardíaca, tendencias en parámetros respiratorios— que preceden eventos adversos horas antes de que sean clínicamente evidentes.</p>
<h2>El desafío de la fatiga de alarma</h2>
<p>El mayor obstáculo práctico no es técnico: es el exceso de alertas. Los sistemas de IA en UCI y urgencias pueden generar decenas de alertas por paciente por turno. Cuando la mayoría son falsos positivos, el personal aprende a ignorarlos —anulando el beneficio clínico. El diseño de la interfaz clínica y la calibración del umbral de alerta son tan importantes como el modelo subyacente.</p>
HTML,

            'ia-para-salud_1_4' => <<<HTML
<p>Chile tiene un ecosistema de IA en salud en desarrollo, con algunos proyectos de vanguardia y múltiples desafíos estructurales. Conocer el panorama local permite al profesional tomar decisiones informadas sobre qué herramientas considerar y en qué contexto.</p>
<h2>Proyectos destacados en el sistema público chileno</h2>
<p><strong>FONASA y digitalización clínica:</strong> el Ministerio de Salud ha impulsado la historia clínica electrónica (HCE) como infraestructura base para el análisis de datos de salud. Sin datos digitales estructurados, la IA clínica no puede funcionar. La digitalización es el paso previo al que Chile ha dedicado más esfuerzo.</p>
<p><strong>Detección de retinopatía diabética:</strong> proyectos piloto en varios servicios de salud han implementado sistemas de IA para cribado de retinopatía diabética mediante fotografía de fondo de ojo, permitiendo evaluación en atención primaria sin necesidad de derivar a oftalmólogo en cada caso.</p>
<p><strong>CENIA y proyectos de investigación:</strong> el Centro Nacional de Inteligencia Artificial (CENIA) tiene líneas de trabajo en salud, incluyendo análisis de imágenes médicas y sistemas de apoyo diagnóstico en colaboración con hospitales universitarios.</p>
<h2>Los desafíos estructurales</h2>
<p><strong>Fragmentación de datos:</strong> Chile no tiene un sistema único de historia clínica electrónica. Los datos de un mismo paciente pueden estar en sistemas incompatibles entre el sistema público y el privado, entre distintos establecimientos, o incluso entre servicios dentro del mismo hospital.</p>
<p><strong>Brechas de infraestructura tecnológica:</strong> la implementación de IA clínica requiere conectividad, hardware y capacidad de procesamiento que no están disponibles de forma uniforme en todos los niveles del sistema de salud.</p>
<p><strong>Marco regulatorio en formación:</strong> el ISP (Instituto de Salud Pública) no tiene aún un proceso específico para aprobación de software como dispositivo médico (SaMD) con IA, aunque está en proceso de desarrollo alineándose con la FDA y la CE.</p>
HTML,

            // ── SALUD · MÓDULO 2 ────────────────────────────────────────────

            'ia-para-salud_2_1' => <<<HTML
<p>El profesional de salud que lee titulares sobre IA médica —"sistema supera a radiólogos en detección de cáncer"— necesita herramientas para evaluar críticamente esos estudios antes de cambiar su práctica clínica. Leer un estudio de validación de IA médica es una competencia nueva que el currículo médico tradicional no enseña.</p>
<h2>Las preguntas clave al evaluar un estudio</h2>
<p><strong>¿Cuál fue la población de entrenamiento vs. la de prueba?</strong> Un modelo entrenado y evaluado en la misma población puede mostrar desempeño excelente que no se replica en tu contexto. Busca estudios con sets de prueba externos e independientes, idealmente de múltiples instituciones.</p>
<p><strong>¿Qué se usó como "ground truth"?</strong> El estándar de referencia con el que se comparó la IA define qué puede concluirse. Un modelo comparado con el diagnóstico de un solo radiólogo de guardia no es lo mismo que uno comparado con panel de expertos o con seguimiento histopatológico.</p>
<p><strong>¿Cuál es el umbral de decisión y por qué?</strong> Todo modelo de clasificación binaria tiene un umbral entre positivo y negativo. Mover ese umbral cambia la sensibilidad y la especificidad. Los estudios deben reportar qué umbral usaron y por qué —o mostrar la curva ROC completa.</p>
<p><strong>¿Se reporta desempeño por subgrupos?</strong> Un modelo con AUC de 0.92 en promedio puede tener AUC de 0.75 en mujeres, en pacientes mayores de 70 años o en imágenes de cierta calidad. El desempeño agregado puede ocultar fallas graves en subpoblaciones.</p>
<h2>Métricas que debes entender</h2>
<p><strong>Sensibilidad:</strong> de todos los casos positivos reales, cuántos detecta el sistema. Crítica cuando el costo del falso negativo es alto (cáncer en estadio precoz).</p>
<p><strong>Especificidad:</strong> de todos los casos negativos reales, cuántos identifica correctamente. Crítica cuando el costo del falso positivo es alto (cirugías innecesarias, ansiedad del paciente).</p>
<p><strong>VPP y VPN:</strong> valor predictivo positivo y negativo, que dependen de la prevalencia de la condición en tu población específica —no solo del modelo.</p>
HTML,

            'ia-para-salud_2_2' => <<<HTML
<p>"El sistema de IA es comparable a un especialista" es una de las afirmaciones más repetidas en el campo de la IA médica —y una de las más mal comprendidas. Entender qué significa realmente es esencial para no sobrevalorar ni subvalorar estas herramientas.</p>
<h2>Comparable en qué métrica y en qué condición</h2>
<p>La comparabilidad siempre es específica a una tarea, una métrica y un contexto. "Comparable a un radiólogo" en sensibilidad para nódulos pulmonares en una TC de alta calidad con protocolo estandarizado no es lo mismo que "comparable a un radiólogo" en el conjunto completo de lo que hace un radiólogo en un turno real.</p>
<p>Un sistema puede tener sensibilidad comparable o superior en la tarea específica para la que fue entrenado, y ser completamente incapaz de manejar la variabilidad del mundo real: imágenes de calidad subóptima, hallazgos incidentales fuera de su entrenamiento, variantes anatómicas infrecuentes.</p>
<h2>El problema del "comparison shopping" de especialistas</h2>
<p>Muchos estudios comparan la IA con médicos generales, residentes o especialistas que trabajan fuera de su área principal. Cuando el estudio dice "supera a los médicos", frecuentemente significa "supera a médicos en una tarea específica fuera de su especialidad". Comparar con especialistas expertos en su campo habitualmente produce resultados más matizados.</p>
<h2>Lo que el "comparable a un especialista" no captura</h2>
<p>El especialista que lee una imagen no solo detecta la anomalía: contextualiza en la historia clínica, evalúa la calidad de la imagen, reconoce cuándo no sabe, comunica la incertidumbre, prioriza hallazgos, decide el siguiente paso. Ninguno de esos elementos está capturado en una métrica de AUC o sensibilidad/especificidad.</p>
<h2>La pregunta clínica correcta</h2>
<p>No "¿es la IA tan buena como el especialista?" sino "¿el sistema de IA, integrado en este flujo de trabajo específico, mejora los outcomes de los pacientes en este contexto?" Esa pregunta requiere estudios de implementación clínica, no solo estudios de validación de modelo.</p>
HTML,

            'ia-para-salud_2_3' => <<<HTML
<p>Los sistemas de IA médica fallan. No son herramientas perfectas que eventualmente cometen algún error: son herramientas con patrones de falla predecibles que el clínico debe conocer para compensarlos con su juicio.</p>
<h2>Fallas por distribución shift</h2>
<p>El problema más frecuente: el modelo fue entrenado en una población y se aplica en otra. Un modelo entrenado con imágenes de mamografía de equipos de alta gama en hospitales universitarios de EE.UU. puede tener un desempeño muy inferior cuando se aplica a imágenes de equipos más antiguos en establecimientos de atención primaria.</p>
<p>Esto aplica a diferencias de equipo, de protocolo, de características de la población (edad, comorbilidades, prevalencia de la condición) y de calidad de imagen. Cualquier cambio en el contexto de implementación respecto al contexto de entrenamiento puede degradar el desempeño.</p>
<h2>Fallas en casos infrecuentes</h2>
<p>Los modelos de ML aprenden de patrones frecuentes. Las variantes raras, las presentaciones atípicas y los hallazgos infrecuentes están subrepresentados en el entrenamiento y son los casos donde el modelo falla con mayor probabilidad. Paradójicamente, son también los casos donde el diagnóstico es más difícil para los humanos y donde el apoyo de la IA sería más valioso.</p>
<h2>Fallas por retroalimentación positiva</h2>
<p>Cuando los clínicos confían en el sistema y rara vez anulan sus recomendaciones, el sistema no recibe señales correctivas. Los errores del sistema se consolidan en la práctica clínica sin mecanismo de corrección. Esto es especialmente peligroso en sistemas que "aprenden" de los datos de uso real.</p>
<h2>El rol del clínico como detector de fallas</h2>
<p>El profesional que usa IA clínica debe conocer los patrones de falla de la herramienta que usa y ser especialmente crítico cuando el output del sistema no coincide con su impresión clínica. "El sistema dice X pero yo pienso Y" no es un error: es la supervisión humana funcionando exactamente como debe.</p>
HTML,

            'ia-para-salud_2_4' => <<<HTML
<p>Los datos clínicos con que se entrenan los modelos de IA médica no son neutrales. Reflejan décadas de práctica médica que tuvo acceso desigual, documentación sesgada y representación inequitativa de distintas poblaciones. Esos sesgos se transfieren directamente a los modelos.</p>
<h2>El sesgo por representación en datos de entrenamiento</h2>
<p>Los grandes datasets de imágenes médicas con que se entrenan los modelos provienen principalmente de hospitales universitarios de países de altos ingresos, con poblaciones predominantemente blancas. Un sistema entrenado en esa distribución puede tener desempeño inferior en poblaciones de mayor diversidad étnica —exactamente las poblaciones que tienen menor acceso a especialistas y que más podrían beneficiarse de herramientas de apoyo diagnóstico.</p>
<p>Un estudio publicado en Nature Medicine (2019) encontró que algoritmos de diagnóstico dermatológico tenían desempeño significativamente inferior en lesiones cutáneas de pacientes de tez oscura, que estaban subrepresentados en el dataset de entrenamiento.</p>
<h2>El sesgo de documentación</h2>
<p>Los modelos que aprenden de texto clínico (historia clínica, notas de evolución) heredan los sesgos de quienes escribieron esas notas. Si históricamente las quejas de dolor de mujeres fueron subestimadas en la documentación, el modelo puede aprender a subestimar los síntomas reportados por mujeres como predictores de condiciones serias.</p>
<h2>El sesgo de prevalencia</h2>
<p>Un modelo entrenado en una institución con alta prevalencia de una enfermedad puede sobrestimar la probabilidad de esa enfermedad cuando se aplica en contextos de menor prevalencia. El valor predictivo positivo depende fuertemente de la prevalencia real en el contexto de uso.</p>
<h2>Lo que el clínico puede hacer</h2>
<p>Conocer en qué población fue entrenado el sistema que usa. Ser especialmente crítico con los outputs del sistema cuando el paciente tiene características que lo alejan de la población de entrenamiento. Reportar al proveedor cuando el sistema falla en patrones específicos —esa retroalimentación es el mecanismo de mejora.</p>
HTML,

            // ── SALUD · MÓDULO 3 ────────────────────────────────────────────

            'ia-para-salud_3_1' => <<<HTML
<p>Los sistemas de IA médica necesitan datos para funcionar. Pero "datos de salud" es una categoría amplia que incluye información extremadamente sensible, y entender exactamente qué datos recopila y procesa el sistema que usas es una responsabilidad clínica y legal.</p>
<h2>Categorías de datos que procesan los sistemas de IA médica</h2>
<p><strong>Datos clínicos directos:</strong> imágenes médicas (radiografías, TC, RM, ecografías), señales fisiológicas (ECG, EEG, oximetría), resultados de laboratorio, texto de historia clínica, notas de evolución, medicamentos prescritos, diagnósticos codificados.</p>
<p><strong>Datos biométricos:</strong> características faciales (en sistemas de reconocimiento de pacientes), voz (en asistentes de documentación clínica), datos genómicos (en medicina de precisión).</p>
<p><strong>Datos de comportamiento clínico:</strong> algunos sistemas aprenden del comportamiento de los médicos —qué recomendaciones aceptan, cuáles rechazan, cómo documentan— para personalizar o mejorar sus sugerencias. Esto significa que tus decisiones clínicas también son datos que procesa el sistema.</p>
<h2>El problema del "procesamiento en la nube"</h2>
<p>La mayoría de los sistemas de IA médica más sofisticados procesan datos en servidores remotos ("la nube"). Eso significa que los datos de tus pacientes salen del sistema de salud y viajan a servidores del proveedor, que pueden estar en otros países con marcos legales distintos.</p>
<p>¿El proveedor usa esos datos para mejorar sus modelos? ¿Con qué datos de otros clientes se combinan? ¿Se pueden re-identificar los datos "anonimizados"? Estas preguntas deben tener respuesta en el contrato con el proveedor antes de implementar.</p>
<h2>Los datos que NO deberían procesarse</h2>
<p>Identificadores directos innecesarios para la función clínica del sistema: nombre, RUT, dirección. Si el sistema puede funcionar con datos seudoanonimizados (donde solo el establecimiento puede re-identificar), esa es la configuración más segura para el paciente.</p>
HTML,

            'ia-para-salud_3_2' => <<<HTML
<p>Chile tiene un marco legal de protección de datos de salud que el profesional debe conocer para operar de forma legalmente correcta cuando usa sistemas de IA clínica. El desconocimiento no exime de responsabilidad, y las consecuencias de una violación en datos de salud son significativas.</p>
<h2>La Ley 19.628 y los datos sensibles</h2>
<p>La Ley 19.628 de Protección de Datos Personales clasifica los datos de salud como datos sensibles, con protecciones especiales. Su tratamiento requiere consentimiento expreso del titular o autorización legal específica. Esta ley está siendo reemplazada por una nueva normativa (en tramitación) que se alinea con el estándar RGPD europeo y será significativamente más exigente.</p>
<h2>La Ley 20.584 de derechos del paciente</h2>
<p>Esta ley, específica del contexto de salud, establece el derecho del paciente a la confidencialidad de su información clínica. Toda la información contenida en la historia clínica es reservada. El uso de esa información en sistemas de IA debe ser compatible con este principio —lo que implica que el paciente debe ser informado si su información clínica se usa para entrenar modelos o mejorar sistemas.</p>
<h2>Lo que el profesional debe verificar antes de usar un sistema de IA</h2>
<p>¿El sistema procesa datos identificados de pacientes fuera del establecimiento? Si sí, ¿existe un marco contractual con el proveedor que proteja esos datos según la ley chilena? ¿Los pacientes son informados de que su información puede ser procesada por sistemas de IA? ¿Existe un mecanismo para que el paciente se oponga?</p>
<h2>El contexto de la nueva ley de datos</h2>
<p>La nueva ley de protección de datos personales en tramitación en Chile creará la Agencia de Protección de Datos Personales con capacidad sancionatoria real, elevará las multas y establecerá derechos adicionales para los titulares, incluyendo el derecho a oponerse a decisiones automatizadas. Los establecimientos que implementen sistemas de IA clínica hoy deben diseñarlos con ese estándar futuro en mente.</p>
HTML,

            'ia-para-salud_3_3' => <<<HTML
<p>El consentimiento informado es uno de los pilares éticos y legales de la práctica médica. La introducción de la IA en el proceso clínico genera preguntas nuevas sobre qué debe saber el paciente y cómo debe expresar su consentimiento.</p>
<h2>¿Debe el paciente saber que hay IA en su atención?</h2>
<p>La respuesta legal y ética es sí, cuando la IA tiene un rol en decisiones que le afectan significativamente. El derecho a la información del paciente establecido en la Ley 20.584 incluye la información sobre los procedimientos y tecnologías que se usan en su atención.</p>
<p>Esto no significa que cada interacción con un sistema de apoyo a la decisión requiera un formulario específico. Significa que el paciente debe poder saber, si pregunta, que se usan herramientas de IA en su evaluación y cuál es su rol.</p>
<h2>Los casos que sí requieren consentimiento explícito</h2>
<p><strong>Cuando los datos del paciente se usan para entrenar modelos:</strong> usar la imagen de un paciente para mejorar el modelo del proveedor es un uso secundario de sus datos que requiere consentimiento explícito separado del consentimiento clínico.</p>
<p><strong>Cuando el sistema toma decisiones sin supervisión médica directa:</strong> un sistema que envía automáticamente resultados al paciente sin revisión humana previa requiere que el paciente conozca y acepte ese flujo.</p>
<p><strong>Cuando el sistema analiza datos biométricos:</strong> reconocimiento facial, análisis de voz, análisis genómico. Estos tipos de datos tienen sensibilidad especial y el consentimiento debe ser específico.</p>
<h2>Cómo adaptar el consentimiento informado</h2>
<p>Para la mayoría de los usos de IA como apoyo al diagnóstico con supervisión médica, puede ser suficiente incluir una cláusula en el consentimiento general de atención. Para usos de datos secundarios o sistemas más autónomos, se requiere un consentimiento separado y específico. La asesoría legal del establecimiento debe definir el estándar para cada tipo de sistema.</p>
HTML,

            'ia-para-salud_3_4' => <<<HTML
<p>Evaluar la privacidad de un proveedor de IA médica antes de contratar o recomendar su uso es una responsabilidad que recae sobre los establecimientos y, en muchos casos, sobre los profesionales que los adoptan. No es suficiente con que el proveedor diga que "cumple con todas las normativas".</p>
<h2>Preguntas que debes hacer a cualquier proveedor</h2>
<p><strong>¿Dónde se almacenan los datos de mis pacientes?</strong> El país y el marco legal que aplica. Si los datos se procesan en EE.UU., aplica HIPAA. Si es en Europa, RGPD. Si es en Chile, Ley 19.628 y la nueva ley en tramitación. Cada marco tiene obligaciones distintas.</p>
<p><strong>¿Los datos se usan para entrenar o mejorar modelos?</strong> Si la respuesta es sí, ¿con consentimiento explícito de los pacientes? ¿Los datos se comparten con terceros? ¿Se pueden re-identificar?</p>
<p><strong>¿Qué pasa con los datos si termina el contrato?</strong> El proveedor debe comprometerse a eliminar o devolver los datos de forma verificable. Esto debe estar en el contrato, no solo en la política de privacidad.</p>
<p><strong>¿El proveedor tiene certificaciones de seguridad?</strong> ISO 27001, SOC 2 Type II u equivalentes son estándares mínimos de seguridad de la información para sistemas que manejan datos de salud.</p>
<h2>Las señales de alerta</h2>
<p>Un proveedor que no puede responder estas preguntas con documentación concreta. Políticas de privacidad genéricas que no especifican el tratamiento de datos de salud. Contratos que reservan al proveedor el derecho de usar los datos para "mejorar sus servicios" sin especificar límites. Ausencia de un DPA (Data Processing Agreement) específico.</p>
<h2>El principio práctico</h2>
<p>Si no puedes explicarle a tu paciente exactamente qué hace el proveedor con sus datos de forma que el paciente lo entienda y lo acepte, probablemente hay un problema con el sistema que estás usando.</p>
HTML,

            // ── SALUD · MÓDULO 4 ────────────────────────────────────────────

            'ia-para-salud_4_1' => <<<HTML
<p>Cuando un sistema de IA médica contribuye a un diagnóstico erróneo o a una decisión clínica que daña al paciente, ¿quién responde? Esta pregunta no tiene una respuesta legal definitiva en Chile todavía, pero el análisis de los marcos legales disponibles y los casos internacionales permite construir una posición fundamentada.</p>
<h2>El principio de responsabilidad médica vigente</h2>
<p>En Chile, la Ley 19.966 (AUGE) y el Código Sanitario establecen la responsabilidad del médico por las decisiones clínicas que toma. La introducción de una herramienta de apoyo —sea una calculadora de riesgo, una guía clínica o un sistema de IA— no transfiere esa responsabilidad al fabricante de la herramienta. El médico que usa un sistema de IA para apoyar su diagnóstico y toma la decisión final sigue siendo el responsable de esa decisión.</p>
<h2>La cadena de responsabilidad</h2>
<p><strong>El fabricante del sistema:</strong> puede tener responsabilidad civil extracontractual si el sistema tenía defectos de diseño o no cumplía las especificaciones declaradas. Bajo el marco de productos defectuosos (Ley 19.496 de protección al consumidor en su aplicación más amplia), el fabricante puede ser demandado si el sistema funcionó fuera de sus especificaciones.</p>
<p><strong>El establecimiento de salud:</strong> que implementó el sistema sin la debida diligencia en la evaluación, entrenamiento del personal o supervisión puede compartir responsabilidad. Si el sistema no tenía las aprobaciones regulatorias correspondientes, la responsabilidad del establecimiento es mayor.</p>
<p><strong>El profesional:</strong> que tomó la decisión clínica basada en el output del sistema sin el nivel apropiado de revisión crítica. "El sistema lo dijo" no es una defensa jurídica suficiente cuando el clínico tenía o debía tener los conocimientos para cuestionar ese output.</p>
<h2>El principio práctico</h2>
<p>Usa la IA como lo que es: una herramienta de apoyo que informa tu juicio clínico, no que lo reemplaza. Documenta que tomaste una decisión clínica fundamentada, con el apoyo de la herramienta como uno de los insumos considerados. Eso es lo que protege al paciente y a ti.</p>
HTML,

            'ia-para-salud_4_2' => <<<HTML
<p>La documentación clínica cuando hay IA involucrada sirve dos propósitos: permite la continuidad asistencial (otro profesional debe poder entender qué se hizo y por qué) y protege al profesional ante una eventual disputa. Ambos propósitos requieren documentar el uso de IA con suficiente especificidad.</p>
<h2>Qué documentar cuando usas IA en una decisión clínica</h2>
<p><strong>Qué sistema se usó:</strong> el nombre y versión del sistema, no solo "se usó IA". Los sistemas tienen versiones, y las versiones tienen rendimientos y limitaciones distintas.</p>
<p><strong>Qué input se proporcionó al sistema:</strong> qué imagen, qué parámetros, qué datos clínicos procesó el sistema para generar su output.</p>
<p><strong>Cuál fue el output del sistema:</strong> qué sugirió, con qué nivel de confianza, qué alternativas presentó.</p>
<p><strong>Cuál fue tu decisión clínica y por qué:</strong> si seguiste la recomendación del sistema, por qué. Si te apartaste de ella, cuál fue el razonamiento clínico. Esto es lo más importante: mostrar que hubo juicio médico, no delegación a un algoritmo.</p>
<h2>Un ejemplo concreto</h2>
<p>En lugar de: "Se realizó radiografía de tórax. Se detectó nódulo pulmonar. Se deriva a neumología." Documenta: "Radiografía de tórax analizada con sistema [nombre] v[X]. El sistema identificó nódulo en lóbulo superior derecho con probabilidad de malignidad reportada de 18%. La evaluación clínica integrada —considerando factores de riesgo del paciente (tabaquismo 20 paquetes/año, 58 años) y las características morfológicas de la imagen— motivó decisión de derivación a neumología para evaluación especializada y eventual TC de tórax."</p>
<h2>El nivel de detalle apropiado</h2>
<p>No toda interacción con un sistema de apoyo requiere documentación exhaustiva. Un sistema que alerta sobre interacción medicamentosa que el médico verifica y descarta no requiere más que el registro habitual. Un sistema cuyo output fue determinante en una decisión diagnóstica o terapéutica significativa sí requiere documentación explícita.</p>
HTML,

            'ia-para-salud_4_3' => <<<HTML
<p>Los casos legales que involucran IA médica están comenzando a aparecer en distintas jurisdicciones. Aunque Chile no tiene jurisprudencia específica en este campo, los casos internacionales iluminan qué preguntas van a plantearse cuando los tribunales chilenos enfrenten estas situaciones.</p>
<h2>El caso del algoritmo de sepsis (EE.UU., 2021)</h2>
<p>Una demanda en EE.UU. alegó que un hospital confió en un sistema de alerta de sepsis que no activó la alarma en un paciente que desarrolló sepsis fulminante. El hospital argumentó que el sistema funcionó dentro de sus especificaciones. El demandante argumentó que la implementación del sistema sin supervisión adecuada constituyó negligencia. El caso se resolvió extrajudicialmente, pero estableció el precedente de que la adopción de sistemas de IA sin protocolos de supervisión clara puede ser fuente de responsabilidad.</p>
<h2>Responsabilidad por sobredependencia</h2>
<p>En múltiples casos de derecho médico comparado, los tribunales han evaluado si el profesional ejerció juicio clínico independiente o delegó la decisión a una herramienta. El concepto de "automation bias" —la tendencia humana a aceptar la recomendación automatizada sin cuestionarla— es reconocido en la literatura y está comenzando a aparecer en argumentos legales.</p>
<h2>El debate sobre el "learned intermediary"</h2>
<p>En derecho de responsabilidad por productos, la doctrina del "learned intermediary" establece que el fabricante cumple su deber de advertencia cuando informa al profesional (no directamente al paciente), y el profesional ejerce su juicio. Si el fabricante informó adecuadamente al médico de los límites del sistema y el médico no los consideró, la responsabilidad se desplaza al médico.</p>
<h2>La trayectoria en Chile</h2>
<p>Chile no tiene jurisprudencia específica sobre IA médica a 2025. Pero a medida que la adopción aumenta, los litigios llegarán. Los profesionales y establecimientos que documentan sus procesos de evaluación e implementación de IA estarán en mejor posición cuando eso ocurra.</p>
HTML,

            'ia-para-salud_4_4' => <<<HTML
<p>Una de las preguntas más filosóficamente profundas que plantea la IA médica es cuánta autonomía puede tener un sistema para tomar decisiones clínicas sin intervención humana directa. El debate no es solo académico: tiene implicaciones regulatorias, legales y éticas concretas.</p>
<h2>Los niveles de autonomía en sistemas médicos</h2>
<p>La FDA ha desarrollado un marco que clasifica los sistemas de IA médica por su nivel de autonomía: desde herramientas de información que el clínico usa libremente, pasando por sistemas de apoyo a la decisión, hasta sistemas que toman decisiones con mínima intervención humana.</p>
<p>El nivel máximo de autonomía aprobado hoy —IDx-DR, para diagnóstico autónomo de retinopatía diabética— puede dar un diagnóstico sin que intervenga un oftalmólogo, con el resultado enviado directamente al médico de atención primaria. Es el único sistema con aprobación FDA para diagnóstico completamente autónomo en su categoría.</p>
<h2>Los argumentos a favor de mayor autonomía</h2>
<p>En contextos de escasez de especialistas —que describe la realidad de gran parte del sistema de salud chileno, especialmente en regiones— un sistema autónomo puede proveer diagnóstico donde de otra forma no habría ninguno. Un screening autónomo de retinopatía en atención primaria rural es mejor que ningún screening.</p>
<h2>Los argumentos contra la autonomía no supervisada</h2>
<p>Los sistemas autónomos fallan sin que haya un humano que detecte y corrija el error. En medicina, los errores del sistema se convierten directamente en daño al paciente. La complejidad clínica real —comorbilidades, contexto social, preferencias del paciente— no está bien representada en los datos con que se entrenan estos sistemas.</p>
<h2>El consenso actual</h2>
<p>La posición dominante en regulación y bioética médica es que la supervisión humana debe mantenerse en decisiones que afectan significativamente la salud del paciente. La IA como amplificador del juicio clínico humano, no como su reemplazante. Ese equilibrio puede cambiar a medida que los sistemas mejoren —pero el estándar de hoy es supervisión humana significativa.</p>
HTML,

            // ── SALUD · MÓDULO 5 ────────────────────────────────────────────

            'ia-para-salud_5_1' => <<<HTML
<p>El médico generalista y de familia es el profesional de salud con mayor potencial de impacto de la IA, y también el que enfrenta el mayor desafío de implementación: trabaja en el nivel más amplio del sistema, con el mayor volumen de pacientes y la mayor variedad de presentaciones clínicas.</p>
<h2>Las herramientas más relevantes para medicina general</h2>
<p><strong>Sistemas de apoyo diagnóstico:</strong> herramientas que analizan los síntomas y signos ingresados y sugieren diagnósticos diferenciales ordenados por probabilidad. Isabel DDx y Symptoma son los más usados internacionalmente. Útiles especialmente en presentaciones atípicas o en condiciones de baja prevalencia que el médico puede no tener presente.</p>
<p><strong>Alertas de interacción medicamentosa e indicadores de riesgo:</strong> integrados en muchos sistemas de historia clínica electrónica, alertan sobre interacciones, contraindicaciones o indicadores de deterioro. El desafío es calibrar el umbral para reducir fatiga de alarma sin perder señales relevantes.</p>
<p><strong>Apoyo a la gestión crónica:</strong> sistemas que monitorizan parámetros de pacientes con enfermedades crónicas (diabetes, HTA, EPOC) y alertan cuando hay desviaciones que requieren intervención. Especialmente útiles en modelos de atención a distancia o en equipos de salud familiar con alta carga.</p>
<p><strong>Documentación asistida:</strong> herramientas que transcriben la consulta médica y generan un borrador de la nota clínica, liberando al médico de la carga administrativa para enfocarse en el paciente. Nuance DAX (Microsoft) es el más usado en contexto anglosajón; soluciones adaptadas al español están emergiendo.</p>
<h2>El rol del médico general en la IA clínica</h2>
<p>El médico de familia es frecuentemente el primero en recibir el output de un sistema de IA —el resultado de un screening automático, la alerta de un wearable, el diagnóstico de una aplicación. Saber interpretar esos outputs con criterio clínico, comunicarlos adecuadamente al paciente y decidir los próximos pasos es una competencia que se vuelve parte del oficio.</p>
HTML,

            'ia-para-salud_5_2' => <<<HTML
<p>La salud mental es una de las áreas donde la IA genera más esperanza y más cautela simultáneamente. La esperanza viene de la brecha enorme entre la demanda de atención y la oferta de profesionales; la cautela, de la sensibilidad de los datos, la complejidad del vínculo terapéutico y los riesgos únicos de esta especialidad.</p>
<h2>Las aplicaciones que existen hoy</h2>
<p><strong>Chatbots de apoyo emocional:</strong> Woebot, Wysa, Youper. Basados en técnicas de terapia cognitivo-conductual, ofrecen intervención de baja intensidad de forma continua y accesible. No son psicoterapia: son apoyo entre sesiones, primeros auxilios emocionales o herramientas para personas que no tienen acceso a profesionales.</p>
<p><strong>Detección de riesgo en texto y habla:</strong> sistemas que analizan patrones lingüísticos en texto o voz para detectar señales de depresión, ansiedad o riesgo suicida. Usados principalmente en contextos de investigación y, con mucha cautela, en algunos sistemas de crisis.</p>
<p><strong>Apoyo a la documentación clínica:</strong> transcripción de sesiones (con consentimiento), análisis de progreso del paciente, alertas de deterioro en variables objetivas.</p>
<h2>Los límites que el profesional debe conocer</h2>
<p>El vínculo terapéutico —la relación entre el profesional y el paciente— es en sí mismo un elemento terapéutico en salud mental. Ningún sistema de IA puede replicarlo. Los chatbots de apoyo emocional pueden ser útiles como complemento pero representan un riesgo si se usan como sustituto de atención profesional en condiciones que la requieren.</p>
<p>Los datos de salud mental son extraordinariamente sensibles. Las brechas de seguridad en aplicaciones de salud mental han ocurrido: en 2020, una filtración de datos de la aplicación Vastaamo en Finlandia expuso registros de sesiones de terapia de decenas de miles de pacientes, con consecuencias devastadoras. El estándar de privacidad para estas herramientas debe ser el más alto posible.</p>
HTML,

            'ia-para-salud_5_3' => <<<HTML
<p>El profesional de salud que quiere incorporar una herramienta de IA en su práctica tiene que hacer una evaluación que va más allá de "¿funciona?" La pregunta es "¿funciona en mi contexto, con mis pacientes, de forma que mejora su atención y no la compromete?"</p>
<h2>El marco de evaluación en cinco dimensiones</h2>
<p><strong>1. Validez clínica:</strong> ¿el sistema ha sido validado en una población similar a la tuya en términos de demografía, prevalencia de la condición y contexto? ¿Los estudios de validación son independientes del fabricante? ¿En qué tipo de imágenes o datos funciona y cuáles están fuera de su alcance?</p>
<p><strong>2. Aprobación regulatoria:</strong> en Chile, el ISP regula los dispositivos médicos incluyendo software como dispositivo médico (SaMD). ¿El sistema tiene la categorización y aprobación regulatoria correspondiente para el uso que pretendes darle? En ausencia de regulación específica chilena, ¿tiene aprobación FDA o CE?</p>
<p><strong>3. Integración al flujo de trabajo:</strong> ¿cómo se integra en tu práctica real? ¿Agrega fricción o la reduce? Un sistema que técnicamente funciona pero que interrumpe el flujo de trabajo de forma que los clínicos lo evitan no genera valor.</p>
<p><strong>4. Privacidad y seguridad:</strong> ya cubierto en el módulo anterior, pero aplicado a la evaluación específica: ¿dónde van los datos de tus pacientes, bajo qué marco legal, con qué garantías?</p>
<p><strong>5. Soporte y actualizaciones:</strong> ¿el proveedor tiene presencia y soporte en Chile o América Latina? ¿Cómo se maneja cuando el sistema falla? ¿Con qué frecuencia se actualiza el modelo y cómo se comunican los cambios de rendimiento?</p>
<h2>El piloto antes del deploy</h2>
<p>Antes de incorporar definitivamente una herramienta, úsala en paralelo con tu proceso habitual durante un período definido. ¿Genera hallazgos adicionales útiles? ¿Produce alertas que cambian tu manejo? ¿Los falsos positivos son manejables? Esa evidencia propia, en tu contexto, vale más que cualquier estudio publicado.</p>
HTML,

            'ia-para-salud_5_4' => <<<HTML
<p>La integración de la IA en la práctica clínica no es un evento sino un proceso. Hacerlo bien requiere atención a cómo cambia la relación con el paciente, cómo se mantiene el juicio clínico independiente y cómo se gestiona la incertidumbre que toda herramienta nueva introduce.</p>
<h2>Mantener el juicio clínico como referencia</h2>
<p>El riesgo más documentado de los sistemas de apoyo a la decisión es el automation bias: la tendencia a aceptar la recomendación automatizada sin el nivel de cuestionamiento que aplicaríamos a la recomendación de un colega. Contrarrestarlo requiere un hábito activo: antes de ver el output del sistema, formarte tu propia impresión clínica. Luego compara. Si el sistema coincide, refuerza tu confianza. Si discrepa, examina la razón del desacuerdo con cuidado.</p>
<h2>La comunicación con el paciente</h2>
<p>Cuando uses IA en la atención, considera cómo lo comunicas. Algunos pacientes valoran saber que hay una "segunda opinión tecnológica"; otros pueden sentirse incómodos. No hay una fórmula universal, pero la honestidad sobre las herramientas que usas en su atención es siempre la posición ética correcta.</p>
<p>Evita presentar el output de la IA como más certero de lo que es. "El sistema sugiere que..." es más honesto que "el sistema dice que..." —la primera formulación mantiene la incertidumbre apropiada; la segunda la elimina artificialmente.</p>
<h2>El aprendizaje continuo como parte del uso</h2>
<p>Las herramientas de IA clínica mejoran, cambian y a veces empeoran con las actualizaciones. Mantener atención activa sobre el desempeño del sistema que usas —¿sigue produciendo outputs útiles? ¿Ha cambiado el tipo de alertas? ¿Los colegas reportan experiencias distintas?— es parte de la responsabilidad de usarla.</p>
<div style="background:linear-gradient(135deg,#f43f5e,#e11d48);border-radius:1rem;padding:2rem;text-align:center;margin-top:2rem;">
    <div style="font-size:2rem;margin-bottom:.5rem;">🎓</div>
    <h2 style="color:#fff;font-size:1.15rem;margin-bottom:.5rem;">Curso completado</h2>
    <p style="color:rgba(255,255,255,.9);font-size:.9rem;max-width:420px;margin:0 auto 1rem;">Terminaste <strong>IA para Profesionales de la Salud</strong>. Tienes ahora el marco clínico, legal y ético para integrar IA en tu práctica sin comprometer a tus pacientes.</p>
    <a href="/cursos" style="display:inline-block;background:#fff;color:#0f172a;font-weight:700;padding:.6rem 1.5rem;border-radius:.5rem;text-decoration:none;font-size:.88rem;">Ver otros cursos</a>
</div>
HTML,

            // ── PYMES · MÓDULO 1 ────────────────────────────────────────────

            'ia-para-pymes_1_1' => <<<HTML
<p>La IA no es solo para empresas grandes con equipos de tecnología. Hay pymes y emprendimientos chilenos que ya la están usando para competir en mejores condiciones —y en la mayoría de los casos, empezaron sin ningún conocimiento técnico previo.</p>
<h2>Casos reales de pymes usando IA en Chile</h2>
<p><strong>E-commerce de moda:</strong> una tienda online de ropa femenina en Santiago usa IA para responder automáticamente las preguntas frecuentes en Instagram y WhatsApp (horarios, tallas, disponibilidad, envíos). Lo que antes tomaba 3 horas diarias al dueño ahora está automatizado. Resultado: más tiempo para compras y diseño, respuestas más rápidas, menos carritos abandonados.</p>
<p><strong>Clínica veterinaria:</strong> una clínica de 4 veterinarios en Providencia usa IA para recordatorios automáticos de vacunas y controles. El sistema identifica qué mascotas están próximas a su fecha de control y envía WhatsApp personalizados. La tasa de reactivación de pacientes inactivos subió un 35% en seis meses.</p>
<p><strong>Agencia de marketing:</strong> una agencia pequeña de 6 personas usa IA para generar los primeros borradores de contenido para redes sociales de sus clientes. No publican el texto sin revisarlo —pero el tiempo de producción de contenido se redujo en un 60%, lo que les permitió aumentar el número de clientes sin contratar más personas.</p>
<p><strong>Restaurante de comida saludable:</strong> usa IA para analizar qué platos tienen mejor y peor rotación en distintos días y horarios, y ajusta el menú y los pedidos al proveedor en consecuencia. Redujo el desperdicio de alimentos en un 20%.</p>
<h2>El patrón común</h2>
<p>Ninguno de estos casos requirió contratar un programador ni pagar por sistemas complejos. Todos empezaron con herramientas existentes, aplicadas a un problema concreto, con resultados medibles. Eso es lo que vas a aprender a hacer en este curso.</p>
HTML,

            'ia-para-pymes_1_2' => <<<HTML
<p>No todo proceso de una empresa es igual de fácil de automatizar. Hay características que hacen que algunos procesos sean candidatos naturales para la IA, mientras que otros requieren mucho más esfuerzo del que vale la pena. Identificar los candidatos correctos es la diferencia entre una implementación que genera valor y una que genera frustración.</p>
<h2>Los procesos que se automatizan más fácilmente</h2>
<p><strong>Procesos repetitivos con reglas claras:</strong> si puedes describir el proceso como "si pasa X, haz Y", es automatizable. Ejemplos: responder preguntas frecuentes, enviar recordatorios, clasificar correos entrantes, generar reportes periódicos con los mismos datos.</p>
<p><strong>Procesos basados en texto:</strong> generar contenido, resumir información, redactar respuestas, clasificar reseñas. Los modelos de lenguaje son especialmente buenos aquí.</p>
<p><strong>Procesos con datos estructurados:</strong> análisis de ventas, predicción de inventario, detección de patrones en transacciones. Si los datos están en una planilla, hay una herramienta de IA que puede analizarlos.</p>
<p><strong>Comunicaciones de seguimiento:</strong> emails de bienvenida, recordatorios de pago, seguimiento post-venta, encuestas de satisfacción. Secuencias que se disparan por eventos específicos son ideales para automatización.</p>
<h2>Los procesos que NO conviene automatizar todavía</h2>
<p><strong>Decisiones que requieren juicio y contexto:</strong> la negociación con un cliente difícil, la decisión de contratar a alguien, la respuesta a una crisis de reputación. La IA puede informar, pero no reemplazar el juicio humano aquí.</p>
<p><strong>Procesos que no están bien definidos:</strong> si no puedes describir exactamente cómo funciona el proceso hoy, automatizarlo solo va a hacerlo más caótico. Primero documenta, luego automatiza.</p>
<p><strong>Interacciones de alto valor emocional:</strong> el seguimiento a un cliente que tuvo una mala experiencia, la atención a un paciente en una situación delicada. La automatización aquí puede empeorar la relación.</p>
HTML,

            'ia-para-pymes_1_3' => <<<HTML
<p>Antes de implementar cualquier herramienta de IA, vale la pena hacer una estimación básica de si el retorno justifica la inversión. No necesitas un modelo financiero sofisticado — necesitas honestidad sobre cuánto tiempo te cuesta el proceso hoy y cuánto ahorrarías.</p>
<h2>El cálculo básico de retorno</h2>
<p>La fórmula es simple: <strong>(tiempo ahorrado × costo de ese tiempo) − costo de la herramienta = retorno mensual</strong>.</p>
<p>Ejemplo concreto: tienes un proceso de atención al cliente que toma 2 horas diarias de tu tiempo (o de un empleado). A $5.000 la hora (costo laboral aproximado de un empleado promedio en Chile), eso son $10.000 diarios, $200.000 mensuales. Si una herramienta de chatbot que cuesta $30.000 mensuales automatiza el 70% de esas consultas, ahorras $140.000 y gastas $30.000: retorno de $110.000 mensuales. El sistema se paga en el primer mes.</p>
<h2>Más allá del tiempo: los beneficios difíciles de medir</h2>
<p><strong>Velocidad de respuesta:</strong> responder en 2 minutos en lugar de 4 horas puede ser la diferencia entre ganar o perder una venta. ¿Cuánto vale eso para tu negocio?</p>
<p><strong>Consistencia:</strong> un proceso automatizado hace siempre lo mismo. Un proceso manual varía con el estado de ánimo, el nivel de cansancio y quién esté de turno.</p>
<p><strong>Escalabilidad:</strong> si tu negocio crece, el proceso automatizado crece con él sin costo adicional proporcional. El proceso manual requiere contratar más personas.</p>
<h2>Cuándo el retorno no justifica</h2>
<p>Si el proceso ocurre menos de 5 veces por semana, si requiere mucha personalización por caso, o si el costo de implementación y mantenimiento es alto respecto al volumen, probablemente no vale la pena ahora. Empieza por los procesos de mayor volumen y mayor repetición.</p>
HTML,

            'ia-para-pymes_1_4' => <<<HTML
<p>Hay creencias sobre la IA que llevan a las pymes a no empezar, o a empezar en la dirección equivocada. Identificar estos mitos permite tomar decisiones más realistas.</p>
<h2>Mito 1: "La IA es para empresas grandes con equipos de tecnología"</h2>
<p>Falso. Las herramientas de IA más útiles para pymes son exactamente las que no requieren equipo técnico: interfaces de chat, plataformas de automatización sin código, integraciones listas para usar. Un dueño de negocio con conocimientos básicos de computación puede implementar un chatbot funcional en un día.</p>
<h2>Mito 2: "Implementar IA es muy caro"</h2>
<p>Depende de qué implementas. ChatGPT con acceso de API tiene un costo de centavos por consulta. Zapier tiene un plan gratuito que permite automatizar cientos de tareas al mes. Make (ex-Integromat) tiene planes desde $9 USD mensuales. Las herramientas de IA más útiles para pymes cuestan entre $0 y $100 USD mensuales —una fracción del costo de una hora de consultoría.</p>
<h2>Mito 3: "Necesito mis datos perfectamente ordenados para empezar"</h2>
<p>Los datos perfectos no existen. Puedes empezar con los datos que tienes, imperfectos como están, y mejorar la calidad gradualmente. Las herramientas de IA para pymes no requieren infraestructura de datos corporativa para generar valor desde el primer día.</p>
<h2>Mito 4: "La IA va a reemplazar a mis empleados y crea mal clima"</h2>
<p>La experiencia más común en pymes que implementan IA es que los empleados se liberan de las tareas más tediosas y pueden enfocarse en lo que hace la diferencia: la relación con el cliente, la creatividad, las decisiones. La IA rara vez reemplaza empleados en pymes — más frecuentemente permite que el equipo actual haga más sin contratar.</p>
<h2>Mito 5: "Si espero un poco, la tecnología va a ser mejor y más barata"</h2>
<p>Siempre va a ser verdad que la próxima versión va a ser mejor. Pero cada mes que esperas, tu competencia puede estar implementando. El costo de esperar es la ventaja competitiva que no acumulas.</p>
HTML,

            // ── PYMES · MÓDULO 2 ────────────────────────────────────────────

            'ia-para-pymes_2_1' => <<<HTML
<p>Hasta hace pocos años, automatizar procesos requería programadores. Hoy existe una categoría completa de herramientas diseñadas específicamente para que cualquier persona —sin saber programar— pueda conectar aplicaciones, automatizar tareas y construir flujos de trabajo inteligentes.</p>
<h2>Las plataformas de automatización sin código</h2>
<p><strong>Zapier:</strong> la más popular y con mayor cantidad de integraciones (más de 6.000 aplicaciones). Permite crear "Zaps" que conectan aplicaciones entre sí: cuando ocurre algo en una app (un nuevo formulario, un pago, un mensaje), hace algo en otra (envía un email, actualiza una planilla, manda una notificación). Plan gratuito disponible con limitaciones.</p>
<p><strong>Make (ex-Integromat):</strong> más flexible que Zapier para flujos complejos, con mejor visualización del proceso. Ideal cuando necesitas lógica condicional, loops o manejo de datos más sofisticado. Más económico que Zapier en planes pagos.</p>
<p><strong>n8n:</strong> alternativa de código abierto que puede instalarse en tu propio servidor. Gratuita en autoservicio, con planes cloud. Requiere algo más de configuración pero no tiene límites de uso en la versión propia.</p>
<p><strong>Notion AI, ClickUp AI, Monday AI:</strong> plataformas de gestión de proyectos con IA integrada que permiten generar resúmenes, asignar tareas automáticamente, identificar cuellos de botella y generar reportes sin salir de la herramienta de trabajo.</p>
<h2>Cómo elegir</h2>
<p>Si recién empiezas: Zapier por su facilidad y cantidad de integraciones. Si quieres más control y presupuesto ajustado: Make. Si manejas volúmenes altos y te incomoda pagar por uso: n8n. No necesitas elegir de por vida — empieza con uno y migra si lo necesitas.</p>
HTML,

            'ia-para-pymes_2_2' => <<<HTML
<p>Los emails, reportes y seguimientos son tres de las tareas más repetitivas en cualquier negocio — y tres de las más fáciles de automatizar. Hacerlo bien libera horas semanales sin sacrificar la calidad de la comunicación.</p>
<h2>Automatizar emails</h2>
<p><strong>Emails de bienvenida:</strong> cuando alguien se registra, compra por primera vez o completa un formulario, un email automático de bienvenida personalizado mejora la experiencia y establece el tono de la relación. Herramientas como Mailchimp, Brevo (ex-Sendinblue) o ActiveCampaign permiten configurar esto en menos de una hora.</p>
<p><strong>Seguimiento post-venta:</strong> 3 días después de una compra, un email automático preguntando cómo está el producto genera reseñas positivas y detecta problemas antes de que el cliente lo publique en redes. 30 días después, una oferta de recompra o complemento.</p>
<p><strong>Carritos abandonados:</strong> si tienes e-commerce, el email de carrito abandonado es probablemente la automatización con mayor retorno inmediato. Tasas de recuperación del 5-15% son comunes con un email bien diseñado enviado entre 1 y 3 horas después del abandono.</p>
<h2>Automatizar reportes</h2>
<p>En lugar de construir manualmente el reporte semanal de ventas, configura una automatización que recopile los datos de tu sistema de ventas, los procese y te envíe el resumen todos los lunes a las 8am. Zapier + Google Sheets + Gmail puede hacer esto sin costo adicional.</p>
<h2>Automatizar seguimientos comerciales</h2>
<p>Un CRM simple (HubSpot tiene plan gratuito, Pipedrive es muy usado en Chile) con secuencias de email automatizadas puede hacer seguimiento a prospectos sin que tengas que recordar manualmente a quién llamar. El sistema alerta cuando un prospecto abre tu email o visita tu sitio.</p>
HTML,

            'ia-para-pymes_2_3' => <<<HTML
<p>Uno de los mayores dolores de las pymes es que los datos están en silos: las ventas en una planilla, los clientes en el email, los pedidos en WhatsApp, la contabilidad en otro software. La IA y las herramientas de integración permiten conectar esos sistemas sin contratar un programador.</p>
<h2>El concepto de integración sin código</h2>
<p>Integrar dos sistemas significa que cuando pasa algo en uno, el otro se actualiza automáticamente. Un nuevo pedido en tu tienda online actualiza automáticamente el inventario en tu planilla. Un pago confirmado en Transbank dispara el envío del comprobante y actualiza el estado en tu CRM. Un formulario de contacto en tu web crea automáticamente un lead en tu sistema de seguimiento.</p>
<h2>Las integraciones más útiles para pymes chilenas</h2>
<p><strong>Tienda online → Planilla de inventario:</strong> cada venta descuenta del stock en tiempo real. Shopify, WooCommerce y Jumpseller tienen integraciones nativas con Google Sheets via Zapier.</p>
<p><strong>Formulario de contacto → CRM → Email de respuesta:</strong> un lead que completa un formulario en tu web aparece automáticamente en tu CRM y recibe un email de respuesta en segundos. Con Tally (formularios) + HubSpot (CRM) + Gmail, esto se configura en media hora.</p>
<p><strong>WhatsApp Business → Planilla de seguimiento:</strong> las conversaciones de WhatsApp Business pueden integrarse con Google Sheets para tener registro de todos los clientes y conversaciones. Herramientas como Kommo (ex-amoCRM) están diseñadas para esto.</p>
<p><strong>Sistema de pagos → Facturación:</strong> Transbank, Mercado Pago o Flow pueden integrarse con sistemas de facturación electrónica (SII-compatible en Chile) para generar facturas automáticamente cuando se confirma un pago.</p>
<h2>Por dónde empezar</h2>
<p>Identifica el proceso que más tiempo te toma porque tienes que copiar datos de un sistema a otro. Esa es tu primera integración. Generalmente se resuelve en menos de una hora con Zapier o Make.</p>
HTML,

            'ia-para-pymes_2_4' => <<<HTML
<p>Una de las preguntas más frecuentes cuando se empieza a explorar la IA para el negocio es cuánto cuesta realmente. La respuesta honesta: mucho menos de lo que imaginas para empezar, y escala gradualmente según el uso.</p>
<h2>El rango de costos reales</h2>
<p><strong>Nivel gratuito (costo: $0):</strong> ChatGPT gratuito para generar contenido, responder preguntas y apoyar la escritura. Zapier gratuito para hasta 100 tareas automatizadas al mes. HubSpot CRM gratuito. Google Sheets con fórmulas básicas de análisis. Con estas herramientas gratuitas puedes resolver el 70% de los casos de uso más frecuentes de una pyme.</p>
<p><strong>Nivel básico (costo: $20–$80 USD mensuales):</strong> ChatGPT Plus (~$20/mes) para acceso a GPT-4 y uso más intensivo. Zapier Starter (~$20/mes) para más tareas y más integraciones. Un chatbot básico para tu web (Tidio, Crisp) desde $0 a $25/mes. Este nivel es suficiente para la mayoría de las pymes en crecimiento.</p>
<p><strong>Nivel intermedio (costo: $100–$300 USD mensuales):</strong> plataformas de email marketing con automatización avanzada (ActiveCampaign, Klaviyo). CRM con IA integrada (Pipedrive, Zoho). Chatbot más sofisticado con integración a WhatsApp Business. Adecuado para empresas con mayor volumen de clientes y procesos más complejos.</p>
<h2>Los costos ocultos que hay que considerar</h2>
<p><strong>Tiempo de configuración:</strong> aunque la herramienta sea gratuita, configurarla toma tiempo. Cuenta 2 a 10 horas para una automatización simple, dependiendo de tu familiaridad con la plataforma.</p>
<p><strong>Mantenimiento:</strong> cuando cambia algo en tu negocio (un proceso nuevo, una integración que se rompe), hay que actualizar la automatización. No es costoso, pero sí requiere atención periódica.</p>
<p><strong>API de IA:</strong> si usas la API de OpenAI directamente (para integraciones más avanzadas), el costo depende del volumen de uso. Para la mayoría de las pymes, los costos de API son inferiores a $20 USD mensuales.</p>
HTML,

            // ── PYMES · MÓDULO 3 ────────────────────────────────────────────

            'ia-para-pymes_3_1' => <<<HTML
<p>Los chatbots son la aplicación de IA que más pymes están explorando, y también la que más decepciones genera cuando se implementa sin el criterio correcto. La diferencia entre un chatbot que genera valor y uno que frustra a tus clientes está en entender bien qué puede y qué no puede hacer.</p>
<h2>Los casos donde los chatbots funcionan bien</h2>
<p><strong>Preguntas frecuentes con respuestas claras:</strong> horarios, precios, disponibilidad, formas de pago, políticas de devolución, información de envíos. Si la respuesta es siempre la misma, el chatbot puede responderla perfectamente.</p>
<p><strong>Captura inicial de datos:</strong> "¿En qué ciudad estás? ¿Cuántas personas son? ¿Para qué fecha necesitas?" El chatbot recoge la información y la pasa al humano ya con el contexto.</p>
<p><strong>Atención fuera de horario:</strong> el chatbot que responde a las 11pm no reemplaza al humano de las 9am — complementa la atención en horarios donde no hay nadie disponible.</p>
<p><strong>Primero filtro para derivar al humano correcto:</strong> identifica si el cliente necesita soporte técnico, ventas o administración, y deriva al canal correcto.</p>
<h2>Los casos donde los chatbots fallan</h2>
<p><strong>Consultas complejas con muchas variables:</strong> si la respuesta depende de 5 factores distintos y cada caso es único, el chatbot va a frustrar al cliente que necesita una respuesta específica.</p>
<p><strong>Clientes molestos:</strong> un cliente que tuvo un problema serio necesita sentir que hay un humano que lo escucha. Un chatbot en esa situación generalmente empeora la experiencia.</p>
<p><strong>Ventas de alta consideración:</strong> compras importantes donde el cliente necesita confianza y asesoría personalizada. El chatbot puede ser el primer contacto, pero el cierre debe ser humano.</p>
<h2>El criterio práctico</h2>
<p>Antes de implementar un chatbot, registra las 30 últimas consultas que recibiste. ¿Cuántas de ellas tienen respuestas estándar? Esas son las que el chatbot puede manejar. Las demás necesitan humano.</p>
HTML,

            'ia-para-pymes_3_2' => <<<HTML
<p>El mayor miedo de los dueños de pymes al automatizar la atención al cliente es perder el trato humano que los diferencia de las empresas grandes. Es un miedo legítimo — y evitable si se diseña la automatización correctamente.</p>
<h2>El principio del "escalamiento inteligente"</h2>
<p>La automatización no tiene que ser todo o nada. El modelo que funciona mejor es el que usa la IA para las partes rutinarias y reserva el tiempo humano para lo que realmente importa: las consultas complejas, los clientes con problemas, las oportunidades de venta de alto valor.</p>
<p>Un chatbot que responde las preguntas frecuentes pero escala inmediatamente al humano cuando detecta frustración, una queja o una consulta que no puede resolver bien, ofrece lo mejor de ambos mundos.</p>
<h2>Cómo mantener la voz y el estilo del negocio</h2>
<p>Los chatbots modernos son entrenables con el tono de tu negocio. Si tu marca es cercana y usa lenguaje informal, el chatbot puede hablar así. Si es más formal, también. El error más común es dejar la configuración por defecto —que suena genérico y corporativo— sin personalizarla.</p>
<p>Invierte tiempo en escribir las respuestas del chatbot como las escribirías tú. Eso es lo que determina si suena como parte de tu negocio o como un robot impersonal.</p>
<h2>El "botón del humano" siempre disponible</h2>
<p>En cualquier flujo automatizado, el cliente debe poder en cualquier momento pedir hablar con una persona. Bloquear ese camino genera frustración y daño a la marca. El chatbot que dice "para hablar con una persona, escribe HUMANO" en cada respuesta da control al cliente y reduce la percepción de que están siendo esquivados.</p>
<h2>La honestidad sobre la automatización</h2>
<p>¿Debe el chatbot presentarse como humano o como bot? La práctica ética —y en varios países ya la práctica legal— es que el cliente sepa que está hablando con un sistema automático. En la práctica, cuando el bot está bien configurado, a los clientes generalmente no les molesta — les molesta cuando el bot falla y nadie les dijo que era un bot.</p>
HTML,

            'ia-para-pymes_3_3' => <<<HTML
<p>La personalización era antes un privilegio de las grandes empresas con equipos de marketing y datos. La IA lo democratizó: una pyme puede hoy personalizar sus comunicaciones y ofertas con un nivel de relevancia que antes era imposible sin tecnología costosa.</p>
<h2>Qué significa personalizar con IA para una pyme</h2>
<p>No se trata de llamar al cliente por su nombre en el email — eso ya lo hacía el marketing de los años 90. Se trata de mostrar el producto correcto, en el momento correcto, con el mensaje correcto, basándose en el comportamiento real del cliente.</p>
<p><strong>Personalización de ofertas por comportamiento:</strong> si un cliente compró zapatos de running, el próximo email relevante para él es sobre medias técnicas, no sobre sandalias de verano. Las plataformas de email marketing con IA (Klaviyo, ActiveCampaign) pueden segmentar y enviar automáticamente según el historial de compras.</p>
<p><strong>Recomendaciones de productos:</strong> si tienes e-commerce, los sistemas de recomendación ("también te puede gustar") pueden implementarse con herramientas como Wiser, LimeSpot o directamente con las funciones de recomendación que ofrecen Shopify y WooCommerce.</p>
<p><strong>Timing personalizado:</strong> enviar el mensaje cuando ese cliente específico está más receptivo. Las plataformas de email con IA aprenden a qué hora abre los emails cada suscriptor y optimizan el envío individualmente.</p>
<h2>Personalización en WhatsApp Business</h2>
<p>Con la API de WhatsApp Business (disponible a través de plataformas como Twilio, Infobip o Vonage), puedes enviar mensajes personalizados a escala: el recordatorio de una cita que menciona el servicio específico, el seguimiento post-venta que nombra el producto que compró, la oferta de cumpleaños que llega el día correcto.</p>
HTML,

            'ia-para-pymes_3_4' => <<<HTML
<p>Cada conversación con un cliente es una fuente de información sobre qué le importa, qué le preocupa, qué le frena de comprar y qué lo hace volver. La IA permite analizar esas conversaciones a escala y extraer insights que de otra forma quedarían perdidos.</p>
<h2>Qué puedes aprender de tus conversaciones con clientes</h2>
<p><strong>Las preguntas más frecuentes:</strong> si el 30% de los clientes pregunta lo mismo antes de comprar, esa información debería estar visible en tu web o en tu comunicación de marketing. Cada pregunta frecuente es una objeción que puedes resolver proactivamente.</p>
<p><strong>Las quejas que se repiten:</strong> si múltiples clientes mencionan lo mismo (demora en la entrega, embalaje deficiente, un producto específico que defrauda), eso es un problema operacional que la analítica de conversaciones puede hacer visible antes de que escale.</p>
<p><strong>El vocabulario que usan los clientes:</strong> cómo describen tus productos, qué palabras usan para referirse a su problema. Ese vocabulario debería aparecer en tu marketing — es el lenguaje que resuena con tu audiencia porque es el suyo.</p>
<h2>Herramientas para analizar conversaciones</h2>
<p><strong>ChatGPT como analizador:</strong> exporta tus conversaciones de WhatsApp Business, email o soporte, y pega un lote en ChatGPT con la instrucción "identifica las 10 temas más frecuentes y las principales quejas". No es automatizado, pero es inmediato y gratuito.</p>
<p><strong>Tidio, Crisp y similares:</strong> plataformas de chat con funciones de análisis de conversaciones integradas. Muestran los topics más frecuentes, la satisfacción del cliente y los puntos donde la conversación se interrumpe.</p>
<p><strong>Plataformas de CRM con IA:</strong> HubSpot y Zoho tienen funciones de análisis de interacciones que identifican patrones en el pipeline de ventas, momentos de alta conversión y señales de riesgo de pérdida.</p>
HTML,

            // ── PYMES · MÓDULO 4 ────────────────────────────────────────────

            'ia-para-pymes_4_1' => <<<HTML
<p>Cuando usas datos de clientes en herramientas de IA, hay un marco legal que aplica. Muchas pymes operan sin saberlo, lo que las expone a riesgos que son perfectamente evitables con información básica.</p>
<h2>La Ley 19.628 de Protección de Datos Personales</h2>
<p>Es la ley vigente en Chile que regula cómo las empresas pueden recopilar, almacenar y usar datos personales de sus clientes. Sus principios clave:</p>
<p><strong>Finalidad:</strong> los datos solo pueden usarse para el propósito por el que fueron recopilados. Si un cliente te da su email para recibir su boleta, no puedes usarlo automáticamente para enviarle publicidad sin su consentimiento adicional.</p>
<p><strong>Proporcionalidad:</strong> solo puedes recopilar los datos que realmente necesitas para la finalidad declarada. Pedir el RUT, dirección, teléfono y fecha de nacimiento para una suscripción a newsletter que no los requiere es desproporcionado.</p>
<p><strong>Seguridad:</strong> debes adoptar medidas razonables para proteger los datos de acceso no autorizado. No hay una definición exacta de "razonables", pero incluye contraseñas seguras, acceso limitado a los datos y backups.</p>
<h2>La nueva ley en tramitación</h2>
<p>Chile está procesando una reforma sustancial a esta ley que la alineará con el RGPD europeo. Cuando entre en vigencia (proceso aún en curso a 2025), incluirá: derechos más fuertes para los titulares, obligación de notificar brechas de seguridad, creación de una Agencia de Protección de Datos con capacidad sancionatoria y multas significativamente mayores.</p>
<h2>Lo que esto significa en la práctica</h2>
<p>Si usas una herramienta de IA que procesa datos de clientes (nombres, emails, comportamiento de compra), esa herramienta y tú deben cumplir con estos principios. La responsabilidad no recae solo en el proveedor de la herramienta: recae también en tu empresa como responsable del tratamiento de los datos.</p>
HTML,

            'ia-para-pymes_4_2' => <<<HTML
<p>Cumplir con la protección de datos no requiere un abogado de planta ni sistemas complejos. Para la mayoría de las pymes, hay un conjunto básico de obligaciones que con atención y orden se pueden manejar sin dificultad.</p>
<h2>Las obligaciones básicas en la práctica</h2>
<p><strong>Informar qué datos recopilas y para qué:</strong> tu sitio web debe tener una política de privacidad que explique qué datos recopilas (nombre, email, datos de navegación, comportamiento de compra), para qué los usas (gestionar pedidos, enviar comunicaciones, mejorar el servicio) y con quién los compartes (herramientas de email marketing, plataformas de pago).</p>
<p>No necesita ser un documento legal denso. Puede ser una página sencilla escrita en lenguaje claro. ChatGPT puede ayudarte a redactar una política de privacidad básica adaptada a tu negocio en minutos — solo asegúrate de que refleje realmente lo que haces.</p>
<p><strong>Obtener consentimiento para comunicaciones de marketing:</strong> el checkbox "acepto recibir comunicaciones comerciales" no puede estar pre-marcado. El cliente debe marcarlo activamente. Esto aplica a emails de marketing, SMS y WhatsApp con fines comerciales.</p>
<p><strong>Permitir que los clientes accedan a sus datos y los eliminen:</strong> si un cliente te pide que elimines sus datos de tu base, debes poder hacerlo. Tener un proceso claro para esto (aunque sea tan simple como un email a una dirección específica) es suficiente para la mayoría de las pymes.</p>
<h2>Los pasos mínimos para empezar a cumplir</h2>
<ol style="color:#475569;font-size:.97rem;line-height:1.85;padding-left:1.5rem;margin-bottom:1rem;">
<li>Publica una política de privacidad en tu sitio web</li>
<li>Revisa que tus formularios tengan consentimiento explícito para marketing</li>
<li>Identifica qué herramientas de terceros procesan datos de tus clientes</li>
<li>Verifica que esas herramientas tengan sus propias políticas de privacidad adecuadas</li>
</ol>
HTML,

            'ia-para-pymes_4_3' => <<<HTML
<p>Además de cumplir con las obligaciones, hay prácticas específicas con datos de clientes que pueden generar consecuencias legales, reputacionales o de confianza que afecten directamente al negocio.</p>
<h2>Lo que definitivamente no debes hacer</h2>
<p><strong>Comprar listas de emails y usarlas para marketing masivo:</strong> ilegal bajo la ley chilena de datos y bajo los términos de uso de prácticamente todas las plataformas de email marketing. Además de ineficaz (tasas de apertura mínimas, alta tasa de spam) y dañino para la reputación de tu dominio.</p>
<p><strong>Compartir datos de clientes con terceros sin base legal:</strong> si un socio comercial, proveedor o afiliado quiere acceso a tu base de clientes, eso requiere consentimiento de los clientes o una relación contractual clara que justifique el tratamiento compartido.</p>
<p><strong>Pegar datos de clientes en herramientas de IA públicas sin precaución:</strong> si copias conversaciones con clientes que incluyen nombres, RUTs o información sensible y las pegas en ChatGPT u otras herramientas públicas, esos datos se envían a servidores de terceros. Usa descripciones genéricas o herramientas con garantías de privacidad adecuadas.</p>
<p><strong>Almacenar datos de tarjetas de crédito:</strong> esto está prohibido por las normas PCI-DSS y puede generar responsabilidad civil grave. Usa siempre pasarelas de pago certificadas (Transbank, Mercado Pago, Flow) que manejan esos datos en su infraestructura segura.</p>
<p><strong>Usar datos de clientes para propósitos distintos a los declarados:</strong> si recopilaste el email para enviar una boleta y luego lo usas para una campaña de marketing sin consentimiento adicional, eso viola el principio de finalidad de la ley de datos.</p>
<h2>La regla práctica</h2>
<p>Antes de hacer algo con datos de clientes, pregúntate: "¿El cliente esperaría que hiciera esto con sus datos? ¿Lo aceptaría si se lo preguntara?" Si la respuesta es no o no sé, es mejor no hacerlo o pedir consentimiento explícito primero.</p>
HTML,

            'ia-para-pymes_4_4' => <<<HTML
<p>Muchas pymes asumen que la protección legal requiere un abogado de planta o consultoría cara. Para la mayoría de los casos prácticos, hay medidas de protección básica que puedes implementar sin asesoría legal permanente.</p>
<h2>Los documentos mínimos que necesitas</h2>
<p><strong>Política de privacidad:</strong> ya mencionada. Publicada en el sitio web, actualizada cuando cambia cómo usas los datos. ChatGPT puede generar un borrador que un abogado revisa por horas — no por días.</p>
<p><strong>Términos y condiciones de servicio:</strong> qué ofreces, en qué condiciones, qué pasa si hay disputas, limitaciones de responsabilidad. Especialmente importante si vendes online. Nuevamente, el borrador puede generarse con IA y revisarse por un abogado.</p>
<p><strong>Contratos con proveedores que procesan datos de tus clientes:</strong> si usas plataformas de email marketing, CRM o chatbots que acceden a datos de tus clientes, verifica que esas plataformas tengan términos de servicio que establezcan cómo manejan esos datos. La mayoría de las plataformas serias los tienen — solo necesitas leerlos y conservar el registro.</p>
<h2>Cuándo sí necesitas asesoría legal</h2>
<p>Cuando manejas datos especialmente sensibles (salud, información financiera). Cuando recibes una reclamación formal sobre el uso de datos. Cuando tu negocio crece al punto de tener más de 50 clientes activos cuya información guardas de forma centralizada. Cuando implementas sistemas de IA con lógica de decisión que afecta directamente a las personas.</p>
<h2>El activo más valioso: la confianza del cliente</h2>
<p>Más allá de lo legal, proteger bien los datos de tus clientes es una ventaja competitiva. Los clientes que confían en que sus datos están seguros son más leales y más dispuestos a compartir información que te permite servirlos mejor. La privacidad no es solo cumplimiento — es reputación.</p>
HTML,

            // ── PYMES · MÓDULO 5 ────────────────────────────────────────────

            'ia-para-pymes_5_1' => <<<HTML
<p>Una de las trampas más comunes cuando una pyme decide "implementar IA" es querer hacer todo a la vez. El resultado suele ser varios proyectos a medio terminar, cansancio y la sensación de que "la IA no funcionó". La clave es priorizar con criterio y empezar por lo que genera valor más rápido.</p>
<h2>El framework de priorización: impacto vs. esfuerzo</h2>
<p>Para cada proceso que consideras automatizar, evalúa dos dimensiones:</p>
<p><strong>Impacto:</strong> ¿cuánto tiempo o dinero te cuesta hoy? ¿Cuánto mejoraría la experiencia del cliente? ¿Qué tan frecuente es el proceso?</p>
<p><strong>Esfuerzo de implementación:</strong> ¿hay herramientas disponibles que lo resuelvan sin código? ¿Cuánto tiempo tomaría configurarlo? ¿Requiere integración con sistemas que ya tienes?</p>
<p>Empieza por los procesos de <strong>alto impacto y bajo esfuerzo</strong>. Esos son tu "fruta baja": generan valor rápido y te dan confianza y aprendizaje para proyectos más complejos.</p>
<h2>Los tres candidatos más frecuentes en pymes chilenas</h2>
<p><strong>1. Respuesta automática a preguntas frecuentes por WhatsApp:</strong> alto impacto (libera horas diarias), bajo esfuerzo (herramientas como Respond.io o Kommo se configuran en un día).</p>
<p><strong>2. Generación de contenido para redes sociales:</strong> alto impacto (libera tiempo creativo), bajo esfuerzo (ChatGPT + Canva + un calendario editorial básico).</p>
<p><strong>3. Seguimiento automático de clientes que no volvieron:</strong> alto impacto (recuperación de clientes perdidos), bajo esfuerzo (una secuencia de email automatizada en Mailchimp o Brevo).</p>
<h2>Lo que dejas para después</h2>
<p>Los proyectos de alto impacto pero alto esfuerzo (implementar un CRM completo, integrar todos tus sistemas, construir un chatbot avanzado) son para la fase 2, cuando ya tienes el músculo de la implementación y sabes lo que funciona en tu negocio específico.</p>
HTML,

            'ia-para-pymes_5_2' => <<<HTML
<p>Los primeros 30 días son los más críticos en cualquier implementación de IA. Es cuando se establece si la herramienta va a generar valor real o va a quedar como un experimento abandonado. El objetivo de este mes no es hacer todo — es demostrar valor con algo concreto.</p>
<h2>La implementación mínima viable (IMV)</h2>
<p>El concepto viene del mundo de los startups: lanza la versión más pequeña posible que te permite aprender si vas en la dirección correcta. Aplicado a la IA para pymes: implementa la versión más simple de la automatización que te permite ver si funciona, antes de invertir más tiempo en hacerla perfecta.</p>
<h2>Un plan concreto para los primeros 30 días</h2>
<p><strong>Días 1–5 — Elige un proceso y una herramienta:</strong> elige el proceso de mayor impacto y menor esfuerzo que identificaste. Investiga 2 o 3 herramientas que lo resuelven. Elige una y regístrate en el plan gratuito o de prueba.</p>
<p><strong>Días 6–10 — Configura y prueba:</strong> configura la herramienta con datos reales pero en modo de prueba (no lanzada a clientes). Prueba tú mismo como si fueras cliente. Identifica qué falta y ajusta.</p>
<p><strong>Días 11–20 — Lanza con un grupo pequeño:</strong> activa la automatización para un subconjunto de clientes o un canal específico. Monitorea activamente los resultados. ¿Funciona como esperabas? ¿Qué edge cases no habías considerado?</p>
<p><strong>Días 21–30 — Evalúa y decide:</strong> ¿generó valor medible? ¿Los clientes respondieron bien? ¿Qué ajustes necesita? Con esa información, decide si escalar, ajustar o descartar y probar otra cosa.</p>
<h2>El indicador de éxito de los primeros 30 días</h2>
<p>No es que la automatización sea perfecta. Es que puedas responder honestamente: "¿Esta herramienta genera más valor del que cuesta (en dinero y tiempo)?" Si la respuesta es sí, escala. Si es no, aprende por qué y ajusta.</p>
HTML,

            'ia-para-pymes_5_3' => <<<HTML
<p>Sin medición, no sabes si la IA está funcionando o si solo estás pagando por una herramienta que no hace diferencia. Medir no tiene que ser complejo — tiene que ser honesto y consistente.</p>
<h2>Define la métrica antes de implementar</h2>
<p>El error más frecuente es implementar y luego buscar qué medir. La métrica debe definirse antes: "el éxito de esta automatización se mide por X". Si no puedes definir ese X antes de implementar, probablemente no tienes claro qué problema estás resolviendo.</p>
<h2>Las métricas más relevantes para automatizaciones de pymes</h2>
<p><strong>Tiempo ahorrado:</strong> ¿cuántas horas por semana libera la automatización? Mídelo antes y después. Si antes dedicabas 10 horas semanales a responder preguntas frecuentes y ahora dedicas 3, ahorraste 7 horas.</p>
<p><strong>Tasa de conversión:</strong> si automatizaste el seguimiento de leads, ¿qué porcentaje de leads convierte ahora vs. antes? Un seguimiento más rápido y consistente debería mejorar esta tasa.</p>
<p><strong>Tiempo de respuesta al cliente:</strong> si automatizaste la atención inicial, ¿en cuánto tiempo recibe el cliente su primera respuesta? ¿Mejoró la satisfacción del cliente?</p>
<p><strong>Tasa de reactivación:</strong> si automatizaste los emails a clientes inactivos, ¿qué porcentaje vuelve a comprar?</p>
<p><strong>Costo por lead o por cliente:</strong> si la IA ayudó a captar más leads con el mismo presupuesto de marketing, el costo por lead baja.</p>
<h2>El reporte mínimo mensual</h2>
<p>Una vez al mes, revisa: ¿la herramienta sigue funcionando correctamente? ¿Las métricas que definiste mejoraron? ¿El costo sigue siendo proporcional al valor generado? Ese reporte de 30 minutos mensuales es lo que te dice si seguir, ajustar o cambiar.</p>
HTML,

            'ia-para-pymes_5_4' => <<<HTML
<p>Una vez que tienes la primera automatización funcionando y generando valor medible, viene la pregunta de cómo crecer. El error más común es escalar demasiado rápido — implementar todo a la vez antes de tener el aprendizaje suficiente. El camino sostenible es gradual.</p>
<h2>El principio de escalamiento gradual</h2>
<p>Cada nueva implementación debe construir sobre lo que aprendiste en la anterior. No des el siguiente paso hasta que el paso actual esté estabilizado y midiendo bien. Una empresa con 3 automatizaciones que funcionan perfecto es más fuerte que una con 10 que funcionan a medias.</p>
<h2>El orden natural de escalamiento para pymes</h2>
<p><strong>Fase 1 — Automatizar lo repetitivo:</strong> preguntas frecuentes, seguimientos, reportes. Lo que hace la misma cosa muchas veces. Aquí es donde empieza la mayoría.</p>
<p><strong>Fase 2 — Conectar los sistemas:</strong> integrar las herramientas que ya usas para que los datos fluyan sin intervención manual. El pedido que actualiza el inventario, el pago que genera la factura, el lead que entra al CRM.</p>
<p><strong>Fase 3 — Personalizar a escala:</strong> usar los datos que ya tienes para comunicaciones más relevantes. Segmentación por comportamiento, ofertas basadas en historial, timing optimizado.</p>
<p><strong>Fase 4 — Analizar y aprender:</strong> usar la IA para entender qué está pasando en el negocio. Qué productos tienen más rotación, qué clientes tienen mayor valor de vida, qué procesos tienen cuellos de botella.</p>
<h2>El activo más valioso que construyes en el camino</h2>
<p>No son las herramientas — son los datos y el conocimiento sobre tu negocio que acumulas al medirlo. Una pyme que lleva dos años midiendo sus procesos con IA tiene un activo de inteligencia de negocio que le permite tomar mejores decisiones, más rápido, que sus competidores que operan a ciegas.</p>
<div style="background:linear-gradient(135deg,#f472b6,#ec4899);border-radius:1rem;padding:2rem;text-align:center;margin-top:2rem;">
    <div style="font-size:2rem;margin-bottom:.5rem;">🎓</div>
    <h2 style="color:#fff;font-size:1.15rem;margin-bottom:.5rem;">¡Curso completado!</h2>
    <p style="color:rgba(255,255,255,.9);font-size:.9rem;max-width:420px;margin:0 auto 1rem;">Terminaste <strong>IA para Emprendedores y Pymes</strong>. Tienes ahora un plan concreto para implementar IA en tu negocio, medir su impacto y escalar gradualmente.</p>
    <a href="/cursos" style="display:inline-block;background:#fff;color:#0f172a;font-weight:700;padding:.6rem 1.5rem;border-radius:.5rem;text-decoration:none;font-size:.88rem;">Ver otros cursos</a>
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
