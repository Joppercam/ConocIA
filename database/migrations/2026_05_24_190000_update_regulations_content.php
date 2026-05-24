<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $regulations = [
            [
                'slug'    => 'proyecto-de-ley-de-sistemas-de-inteligencia-artificial-boletin-16821-19',
                'summary' => 'Chile está a punto de tener su primera ley de inteligencia artificial. El proyecto clasifica los sistemas de IA según su nivel de riesgo, prohíbe los usos más peligrosos y establece reglas para proteger los derechos de las personas. Ya fue aprobado por la Cámara de Diputados y se discute en el Senado con urgencia suma.',
                'content' => <<<'HTML'
<h2>¿Qué es este proyecto de ley?</h2>
<p>El 7 de mayo de 2024, el Gobierno del Presidente Gabriel Boric presentó a la Cámara de Diputados un proyecto de ley para regular la inteligencia artificial en Chile (Boletín 16821-19). Fue impulsado por el Ministerio de Ciencia en conjunto con otros ministerios, y se unificó con una propuesta parlamentaria previa para crear una regulación más completa.</p>
<p>El proyecto tiene 31 artículos organizados en 10 títulos. Su objetivo es triple: promover la innovación responsable en IA, proteger los derechos fundamentales de las personas, y crear instituciones que puedan fiscalizar y sancionar malas prácticas.</p>
<p>Fue aprobado por la Cámara de Diputados en octubre de 2025 y actualmente se encuentra en segundo trámite constitucional en el Senado, en la Comisión de Desafíos del Futuro, Ciencia, Tecnología e Innovación. Tiene calificación de urgencia suma, lo que significa que el Congreso debe despacharlo en un plazo breve.</p>

<h2>¿A quién aplica?</h2>
<p>La ley aplicaría a todo el que desarrolle, comercialice o use sistemas de IA en Chile, incluyendo:</p>
<ul>
  <li><strong>Proveedores</strong> que introduzcan sistemas de IA en el mercado chileno</li>
  <li><strong>Implementadores</strong> que usen IA en sus operaciones dentro de Chile</li>
  <li><strong>Proveedores extranjeros</strong> cuyos sistemas generen resultados utilizados en territorio chileno</li>
</ul>
<p>Es decir, si una empresa de Silicon Valley ofrece un servicio de IA que afecta a chilenos, también estaría regulada.</p>

<h2>Las 4 categorías de riesgo</h2>
<p>Inspirado en el Reglamento Europeo de IA (EU AI Act), el proyecto clasifica los sistemas de IA en cuatro niveles según el riesgo que representan para las personas:</p>

<h3>1. Riesgo inaceptable — PROHIBIDOS</h3>
<p>Son sistemas cuyo uso está directamente prohibido porque son incompatibles con los derechos fundamentales. Los ejemplos concretos incluyen:</p>
<ul>
  <li><strong>Sistemas de manipulación subliminal</strong> diseñados para alterar el comportamiento de las personas sin que se den cuenta</li>
  <li><strong>Sistemas de calificación social genérica</strong>, es decir, puntuar a ciudadanos por su "comportamiento" como ocurre en China con el social credit system</li>
  <li><strong>Categorización biométrica</strong> basada en datos sensibles como raza, orientación sexual o creencias políticas</li>
  <li><strong>Identificación biométrica remota en tiempo real en espacios públicos</strong>, como usar cámaras con reconocimiento facial para identificar personas en la calle sin su consentimiento</li>
  <li><strong>Extracción masiva de imágenes faciales</strong> desde internet o cámaras de seguridad para crear bases de datos de reconocimiento facial</li>
  <li><strong>Evaluación o inferencia de estados emocionales</strong> en contextos laborales, educativos, de fronteras o de aplicación de la ley penal</li>
</ul>
<p>La excepción es que algunos de estos usos se permiten para fines de seguridad pública y persecución penal, con autorización judicial previa.</p>

<h3>2. Alto riesgo — REGULADOS CON EXIGENCIAS ESTRICTAS</h3>
<p>Son sistemas que pueden afectar significativamente los derechos de las personas si fallan o se usan mal. Estos sí están permitidos, pero deben cumplir requisitos rigurosos durante todo su ciclo de vida: gestión de riesgos documentada, gobernanza de datos para evitar sesgos, documentación técnica completa, registros de actividad, supervisión humana permanente, y transparencia con los usuarios.</p>
<p>Ejemplos de alto riesgo:</p>
<ul>
  <li>IA usada en selección de personal o contratación</li>
  <li>IA para evaluar solicitudes de crédito o seguros</li>
  <li>IA en el sistema judicial o policial</li>
  <li>IA en sistemas de salud que toman decisiones diagnósticas</li>
  <li>IA en infraestructura crítica como energía o agua</li>
</ul>

<h3>3. Riesgo limitado — OBLIGACIONES DE TRANSPARENCIA</h3>
<p>Sistemas cuyo uso presenta riesgos no significativos de manipulación, engaño o error al interactuar con personas. La principal obligación es la transparencia: el usuario debe saber que está interactuando con una IA. Por ejemplo, un chatbot de servicio al cliente debe informar que es un sistema automatizado, no una persona.</p>

<h3>4. Sin riesgo evidente — REGULACIÓN GENERAL</h3>
<p>Sistemas cuyos usos no entran en las categorías anteriores. Solo les aplica la normativa general del proyecto. Por ejemplo, un filtro de spam en tu correo electrónico o un sistema de recomendación de música.</p>

<h2>¿Quién fiscaliza?</h2>
<p>El proyecto crea dos instituciones clave:</p>
<ul>
  <li>La <strong>Agencia de Protección de Datos Personales</strong> sería la encargada de fiscalizar el cumplimiento de la ley y aplicar sanciones administrativas a quienes la incumplan.</li>
  <li>Un <strong>Consejo Asesor Técnico de IA</strong>, dependiente del Ministerio de Ciencia, que asesoraría al gobierno en materia de políticas públicas, estándares técnicos y clasificación de riesgos.</li>
</ul>

<h2>¿Qué derechos tendrías como ciudadano?</h2>
<p>Si esta ley se aprueba, como ciudadano tendrías derecho a:</p>
<ul>
  <li>Saber cuándo estás interactuando con una IA (no con una persona)</li>
  <li>No ser sometido a decisiones automatizadas que afecten tus derechos sin supervisión humana</li>
  <li>No ser discriminado por un algoritmo sesgado</li>
  <li>Reclamar si un sistema de IA te causa un daño</li>
  <li>Que no se use tu cara, tu voz o tus emociones para categorizarte sin tu consentimiento</li>
</ul>

<h2>¿Qué críticas ha recibido?</h2>
<p>El proyecto no está exento de debates. Desde el mundo empresarial, se ha cuestionado si la regulación podría frenar la innovación al imponer cargas excesivas a las empresas. Desde el ámbito jurídico, se ha criticado que el mecanismo de resolución de incidentes deja al propio operador como "juez y parte", sin un proceso formal que proteja a las víctimas. También hay dudas sobre la capacidad real de Chile para fiscalizar sistemas de IA complejos, considerando que la Agencia de Protección de Datos aún no está operativa.</p>

<h2>¿En qué estado está hoy?</h2>
<p>A mayo de 2026, el proyecto se encuentra en su segundo trámite constitucional en el Senado, con urgencia suma. Se esperan modificaciones en la Comisión de Desafíos del Futuro antes de su votación en sala.</p>

<h2>¿Por qué te debería importar?</h2>
<p>Porque la IA ya toma decisiones que te afectan: si te aprueban un crédito, si te seleccionan para un empleo, si te muestran determinadas noticias, o si una cámara te identifica en la calle. Esta ley busca que esas decisiones sean transparentes, justas y con supervisión humana. Es la diferencia entre que la tecnología trabaje para ti o que trabaje sobre ti sin que te enteres.</p>
HTML,
            ],
            [
                'slug'    => 'reglamento-de-inteligencia-artificial-de-la-ue-eu-ai-act',
                'summary' => 'La Unión Europea aprobó la primera ley integral de inteligencia artificial del mundo. Establece un marco regulatorio basado en niveles de riesgo que sirve de modelo para el proyecto chileno y para legislaciones en todo el planeta.',
                'content' => <<<'HTML'
<h2>¿Qué es el EU AI Act?</h2>
<p>El Reglamento Europeo de Inteligencia Artificial, conocido como EU AI Act, es la primera legislación integral sobre IA en el mundo. Fue aprobado por el Parlamento Europeo el 13 de marzo de 2024 y entró en vigor de forma progresiva. Es importante para Chile porque el proyecto de ley chileno se inspira directamente en su estructura de clasificación por riesgo.</p>

<h2>¿Por qué importa para Chile?</h2>
<p>Hay tres razones fundamentales:</p>
<ul>
  <li><strong>Es el modelo que Chile está siguiendo.</strong> La clasificación de riesgo inaceptable, alto, limitado y mínimo del proyecto chileno es prácticamente la misma del EU AI Act.</li>
  <li><strong>Afecta a empresas chilenas que exportan a Europa.</strong> Cualquier empresa chilena que quiera ofrecer servicios de IA en Europa deberá cumplir con esta regulación.</li>
  <li><strong>Establece un estándar global.</strong> Así como el GDPR se convirtió en la referencia mundial para protección de datos, el EU AI Act está marcando la pauta para la regulación de IA en todo el planeta.</li>
</ul>

<h2>¿Qué prohíbe?</h2>
<p>El EU AI Act prohíbe directamente:</p>
<ul>
  <li>Los sistemas de puntuación social (social scoring)</li>
  <li>La manipulación subliminal dañina</li>
  <li>La explotación de vulnerabilidades de grupos específicos (niños, personas con discapacidad)</li>
  <li>Ciertos usos de identificación biométrica remota</li>
</ul>
<p>Las similitudes con el proyecto chileno son evidentes, porque la inspiración fue directa.</p>

<h2>¿Qué exige para IA de alto riesgo?</h2>
<p>Los sistemas de IA de alto riesgo deben cumplir con:</p>
<ul>
  <li>Evaluaciones de conformidad antes de salir al mercado</li>
  <li>Documentación técnica detallada</li>
  <li>Supervisión humana obligatoria</li>
  <li>Robustez y precisión técnica demostrable</li>
  <li>Transparencia con los usuarios</li>
</ul>
<p>Las sanciones pueden llegar hasta <strong>35 millones de euros o el 7% de la facturación global</strong> de la empresa, lo que lo convierte en uno de los marcos regulatorios más exigentes del mundo.</p>

<h2>¿Cómo se implementa progresivamente?</h2>
<p>La entrada en vigor del EU AI Act fue escalonada para dar tiempo a la industria de adaptarse. Las prohibiciones absolutas (riesgo inaceptable) aplicaron primero; las obligaciones para sistemas de alto riesgo tienen plazos más largos. Este modelo de implementación gradual también es algo que Chile podría considerar.</p>
HTML,
            ],
            [
                'slug'    => 'politica-nacional-de-inteligencia-artificial-de-chile',
                'summary' => 'La hoja de ruta de Chile para el desarrollo y adopción de IA. Define las prioridades del país en formación de talento, infraestructura, investigación y marco ético. Es la base sobre la que se construye todo el ecosistema de IA chileno.',
                'content' => <<<'HTML'
<h2>¿Qué es la Política Nacional de IA?</h2>
<p>Es el documento estratégico que define hacia dónde quiere ir Chile en materia de inteligencia artificial. Fue publicada originalmente en noviembre de 2021 y actualizada en 2024. No es una ley, sino una hoja de ruta que orienta las decisiones del gobierno sobre inversión, regulación, educación y desarrollo tecnológico en IA.</p>

<h2>¿Qué ejes tiene?</h2>
<p>La política se organiza en ejes estratégicos:</p>
<ul>
  <li><strong>Formación de talento:</strong> cómo preparar a los chilenos para trabajar con y junto a la IA</li>
  <li><strong>Infraestructura tecnológica:</strong> supercomputación, data centers, conectividad</li>
  <li><strong>Investigación y desarrollo:</strong> fortalecer centros como CENIA</li>
  <li><strong>Adopción productiva:</strong> llevar la IA a la minería, agricultura, salud y otros sectores clave</li>
  <li><strong>Marco ético y regulatorio:</strong> que derivó en el actual proyecto de ley</li>
</ul>

<h2>¿Por qué importa?</h2>
<p>Porque de esta política nacieron iniciativas concretas. Sin este documento, Chile no tendría la estructura institucional de IA que tiene hoy. Entre los resultados concretos:</p>
<ul>
  <li><strong>CENIA</strong> — el Centro Nacional de Inteligencia Artificial, referente de investigación en IA en Chile</li>
  <li><strong>Chile PotencIA</strong> — programa de capacitación masiva en IA</li>
  <li><strong>Inversión en supercomputación</strong> — US$7 millones de CORFO para el Centro para la Supercomputación e Inteligencia Artificial Aplicada (CSIAA)</li>
  <li><strong>El proyecto de ley de regulación</strong> — que hoy se debate en el Senado</li>
</ul>

<h2>¿Cómo se relaciona con la ley de IA?</h2>
<p>La Política Nacional establece los valores y principios que luego se traducen en la ley. No es casual que ambos documentos compartan los mismos principios rectores: transparencia, no discriminación, supervisión humana y rendición de cuentas. La política fue el marco conceptual; la ley es el instrumento jurídico.</p>

<h2>¿Está actualizada?</h2>
<p>La política fue actualizada en 2024 para reflejar los avances del sector y alinearse con los nuevos estándares internacionales, especialmente el EU AI Act. La actualización incorporó perspectivas de género, sostenibilidad ambiental del sector tecnológico y una mayor énfasis en la distribución equitativa de los beneficios de la IA.</p>
HTML,
            ],
            [
                'slug'    => 'orden-ejecutiva-sobre-ia-segura-eeuu',
                'summary' => 'Estados Unidos optó por un enfoque diferente al europeo: en lugar de una ley integral, emitió una orden ejecutiva que establece estándares de seguridad y transparencia para sistemas de IA, sin crear un marco regulatorio completo.',
                'content' => <<<'HTML'
<h2>¿Qué es?</h2>
<p>A diferencia de Europa y Chile, Estados Unidos no ha aprobado una ley integral de IA. En su lugar, el presidente firmó en octubre de 2023 una orden ejecutiva que establece directrices para el desarrollo seguro de IA.</p>
<p>La diferencia es importante: una orden ejecutiva es una instrucción del presidente a las agencias federales, no una ley aprobada por el Congreso. Puede ser modificada o revocada por el siguiente presidente sin necesidad de debate parlamentario.</p>

<h2>¿Qué enfoque tiene?</h2>
<p>El enfoque estadounidense privilegia la <strong>autorregulación de la industria</strong> por sobre la regulación estatal. La orden establece:</p>
<ul>
  <li>Estándares de seguridad para modelos de IA de gran escala</li>
  <li>Requisitos de transparencia para sistemas que interactúan con ciudadanos</li>
  <li>Directrices para el uso gubernamental de IA</li>
  <li>Compromisos voluntarios de las empresas tecnológicas</li>
</ul>

<h2>¿Por qué importa para Chile?</h2>
<p>Porque muestra un camino alternativo al europeo. Mientras la UE y Chile optan por legislar con un marco basado en riesgos y obligaciones legales, EE.UU. apuesta por la flexibilidad y la autorregulación. Chile eligió seguir el modelo europeo, lo que implica obligaciones más claras pero también más carga regulatoria para las empresas.</p>
<p>El contraste también es relevante porque las empresas de IA más poderosas del mundo (OpenAI, Google, Meta, Microsoft) son estadounidenses. Que operen bajo un régimen de autorregulación mientras venden sus servicios en mercados con regulación estricta como la UE o potencialmente Chile crea tensiones que aún no están resueltas.</p>

<h2>¿Cuál es el debate de fondo?</h2>
<p>Hay dos visiones en tensión a nivel global:</p>
<ul>
  <li><strong>El modelo europeo-chileno:</strong> regulación fuerte, obligaciones legales, sanciones. Protege derechos pero puede ralentizar la innovación.</li>
  <li><strong>El modelo estadounidense:</strong> autorregulación, flexibilidad, confianza en la industria. Acelera la innovación pero puede dejar sin protección a los ciudadanos.</li>
</ul>
<p>El tiempo dirá cuál modelo produce mejores resultados. Lo que está claro es que Chile tomó una decisión consciente de apostar por el modelo europeo, priorizando la protección de derechos sobre la velocidad de adopción tecnológica.</p>
HTML,
            ],
            [
                'slug'    => 'plan-nacional-de-data-centers',
                'summary' => 'Chile se está posicionando como hub de data centers en Latinoamérica, con 22 centros operativos y 28 más proyectados. El Plan Nacional busca que este crecimiento sea sostenible y que la infraestructura beneficie al ecosistema de IA chileno.',
                'content' => <<<'HTML'
<h2>¿Qué es?</h2>
<p>Es una hoja de ruta coordinada por el Ministerio de Ciencia para el desarrollo sostenible de centros de datos en Chile. No es una ley sino una estrategia de planificación que busca ordenar el crecimiento acelerado de la infraestructura de data centers en el país.</p>

<h2>¿Por qué Chile atrae data centers?</h2>
<p>Chile tiene ventajas naturales y geopolíticas que lo hacen atractivo como hub de datos para Latinoamérica:</p>
<ul>
  <li><strong>Estabilidad política y económica</strong> — un factor clave para inversiones de largo plazo</li>
  <li><strong>Energía renovable abundante</strong> — solar en el norte, eólica en el sur, lo que reduce la huella de carbono de los centros de datos</li>
  <li><strong>Conectividad submarina</strong> — cables del Pacífico que conectan con Asia y América del Norte</li>
  <li><strong>Tratados de libre comercio</strong> con las principales economías del mundo</li>
  <li><strong>Clima frío en el sur</strong> — reduce costos de refrigeración, uno de los mayores gastos operativos de un data center</li>
</ul>

<h2>¿Cuáles son las cifras?</h2>
<p>Chile alberga actualmente <strong>22 data centers operativos</strong> y se proyectan <strong>28 más</strong>. Empresas como Google, AWS y Microsoft han anunciado inversiones en infraestructura de datos en el país, lo que confirma a Chile como el destino preferido de Sudamérica para infraestructura digital.</p>

<h2>¿Qué tiene que ver con la IA?</h2>
<p>Todo. La inteligencia artificial necesita capacidad de cómputo enorme para entrenarse y operar. Sin data centers, no hay IA. El plan busca que esta infraestructura no solo sirva a empresas extranjeras, sino que también:</p>
<ul>
  <li>Beneficie a la investigación chilena (universidades, CENIA, startups)</li>
  <li>Permita que Chile acceda a capacidad de cómputo a precios competitivos</li>
  <li>Sea compatible con los objetivos medioambientales del país</li>
</ul>

<h2>¿Qué desafíos enfrenta?</h2>
<p>El crecimiento acelerado también trae riesgos. Los data centers consumen grandes cantidades de agua para refrigeración y energía eléctrica. El plan intenta ordenar este crecimiento para que el beneficio económico no venga a costa del medioambiente o de los recursos hídricos, especialmente en zonas donde el agua escasea.</p>
HTML,
            ],
            [
                'slug'    => 'certificacion-chilevalora-en-ia',
                'summary' => 'Chile creó un estándar nacional para certificar competencias en inteligencia artificial a través de ChileValora. El objetivo es que el mercado laboral tenga una forma confiable de verificar que un profesional realmente sabe de IA.',
                'content' => <<<'HTML'
<h2>¿Qué es ChileValora?</h2>
<p>ChileValora es el sistema nacional de certificación de competencias laborales de Chile. Funciona así: se definen perfiles de competencias para diferentes oficios y profesiones, y luego las personas pueden certificar que poseen esas competencias mediante evaluaciones estandarizadas. Es como un "sello de calidad" para tus habilidades profesionales, independiente de dónde las hayas aprendido.</p>

<h2>¿Qué cambió con la IA?</h2>
<p>El Ministerio de Ciencia impulsó que ChileValora creara <strong>perfiles de competencia específicos para inteligencia artificial</strong>. Esto significa que Chile tendrá un estándar oficial para definir qué significa "saber de IA" a nivel profesional: qué conocimientos, habilidades y capacidades debe tener una persona para ser considerada competente en distintos roles de IA.</p>

<h2>¿Para qué sirve?</h2>
<p>La certificación cumple tres funciones:</p>
<ul>
  <li><strong>Para los profesionales:</strong> es una forma de validar sus habilidades más allá de un título universitario o un curso online. En un mercado laboral donde todos dicen "saber de IA", una certificación oficial diferencia al que realmente sabe.</li>
  <li><strong>Para las empresas:</strong> es una herramienta para contratar con más certeza, sin depender solo de la reputación de la institución donde estudió el candidato.</li>
  <li><strong>Para el país:</strong> es un mecanismo para medir cuánto talento en IA tiene Chile realmente, un dato fundamental para la política pública y la inversión educativa.</li>
</ul>

<h2>¿Qué problema resuelve?</h2>
<p>El mercado de la capacitación en IA está lleno de cursos de calidad muy variable. Cualquiera puede decir que hizo un "bootcamp de IA", pero sin un estándar de referencia, esa frase no dice nada. ChileValora en IA crea ese estándar: un punto de referencia común que ordena el mercado y protege tanto a quienes buscan aprender como a quienes buscan contratar.</p>

<h2>¿Cómo se relaciona con la Política Nacional de IA?</h2>
<p>La certificación ChileValora en IA es una de las medidas concretas del eje de "formación de talento" de la Política Nacional de Inteligencia Artificial. Es la institucionalización de la formación en IA: el paso de los cursos dispersos a un sistema ordenado y reconocido por el Estado.</p>
HTML,
            ],
        ];

        foreach ($regulations as $data) {
            DB::table('regulations')
                ->where('slug', $data['slug'])
                ->update([
                    'summary'    => $data['summary'],
                    'content'    => $data['content'],
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        DB::table('regulations')->update(['content' => null]);
    }
};
