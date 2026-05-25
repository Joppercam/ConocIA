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
