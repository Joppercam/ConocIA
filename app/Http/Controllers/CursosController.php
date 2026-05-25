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
