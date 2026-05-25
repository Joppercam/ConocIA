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
