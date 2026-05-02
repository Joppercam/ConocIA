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
        $exists = DB::table('conocia_papers')->where('arxiv_id', '2505.12786')->exists();

        $payload = [
            'arxiv_url' => 'https://arxiv.org/abs/2505.12786',
            'original_title' => 'Forewarned is Forearmed: A Survey on Large Language Model-based Agents in Autonomous Cyberattacks',
            'original_abstract' => 'With the continuous evolution of Large Language Models (LLMs), LLM-based agents have advanced beyond passive chatbots to become autonomous cyber entities capable of performing complex tasks, including web browsing, malicious code and deceptive content generation, and decision-making. By significantly reducing the time, expertise, and resources, AI-assisted cyberattacks orchestrated by LLM-based agents have led to a phenomenon termed Cyber Threat Inflation, characterized by a significant reduction in attack costs and a tremendous increase in attack scale. To provide actionable defensive insights, this survey focuses on the potential cyber threats posed by LLM-based agents across diverse network systems, reviews autonomous attack capabilities, compares effectiveness across networks, analyzes bottlenecks, and outlines future defensive strategies.',
            'authors' => $this->json([
                'Minrui Xu',
                'Jiani Fan',
                'Xinyu Huang',
                'Conghao Zhou',
                'Jiawen Kang',
                'Dusit Niyato',
                'Shiwen Mao',
                'Zhu Han',
                'Xuemin (Sherman) Shen',
                'Kwok-Yan Lam',
            ]),
            'arxiv_published_date' => '2025-05-19',
            'arxiv_category' => 'cs.NI',
            'title' => 'La nueva frontera de la ciberseguridad: cuando la IA baja el costo de atacar',
            'slug' => 'ia-agentes-ciberseguridad-cyber-threat-inflation',
            'excerpt' => 'Un survey academico sobre agentes LLM en ciberataques autonomos propone una idea clave: la IA no solo mejora la defensa, tambien reduce el costo, la experiencia necesaria y la escala de los ataques. El resultado es una inflacion de amenazas que obliga a repensar seguridad, red teaming y gobernanza.',
            'content' => $this->content(),
            'key_contributions' => $this->json([
                'Introduce el concepto de Cyber Threat Inflation para describir ataques mas baratos, escalables y accesibles mediante agentes LLM.',
                'Ordena las capacidades ofensivas de agentes: reconocimiento, memoria, razonamiento, accion, uso de herramientas y colaboracion.',
                'Conecta la discusion de seguridad de modelos con seguridad de redes, infraestructura y operaciones reales.',
                'Muestra que la defensa tradicional queda desbalanceada frente a agentes capaces de operar con autonomia creciente.',
                'Sirve como puente entre papers sobre prompt injection, benchmarks de ciberseguridad y taxonomias oficiales como NIST AML.',
            ]),
            'practical_implications' => $this->json([
                'Empresas y Estado deben tratar a los agentes de IA como nuevos sujetos operativos con permisos, logs, limites y supervision.',
                'Los equipos de seguridad necesitan evaluar agentes con benchmarks, sandboxes y red teaming continuo.',
                'La separacion entre instrucciones, datos y herramientas se vuelve una condicion critica de seguridad.',
                'MFA, principio de minimo privilegio, segmentacion y monitoreo deben extenderse a flujos automatizados por IA.',
                'America Latina puede usar estos marcos para anticipar riesgos antes de desplegar agentes conectados a sistemas publicos o corporativos.',
            ]),
            'difficulty_level' => 'intermedio',
            'image' => null,
            'featured' => true,
            'status' => 'published',
            'views' => 0,
            'reading_time' => 9,
            'published_at' => $now,
            'updated_at' => $now,
        ];

        if (!$exists) {
            $payload['arxiv_id'] = '2505.12786';
            $payload['created_at'] = $now;
        }

        DB::table('conocia_papers')->updateOrInsert(['arxiv_id' => '2505.12786'], $payload);

        $this->clearCaches();
    }

    public function down(): void
    {
        DB::table('conocia_papers')->where('arxiv_id', '2505.12786')->delete();

        $this->clearCaches();
    }

    private function content(): string
    {
        return <<<'HTML'
<h2>La IA ya no es solo una herramienta defensiva</h2>
<p>Durante anos, la promesa mas visible de la inteligencia artificial en ciberseguridad fue defensiva: detectar anomalias, priorizar vulnerabilidades, resumir alertas, asistir a equipos SOC y acelerar la respuesta a incidentes. Esa promesa sigue siendo real. Pero la literatura academica reciente muestra la otra mitad del problema: la misma IA tambien reduce el costo de atacar.</p>
<p>El paper <em>Forewarned is Forearmed: A Survey on Large Language Model-based Agents in Autonomous Cyberattacks</em> propone una lectura inquietante: los agentes basados en grandes modelos de lenguaje ya no deben entenderse como chatbots pasivos, sino como entidades capaces de navegar, usar herramientas, razonar, recordar pasos anteriores, generar codigo, producir contenido enganoso y ejecutar secuencias de accion.</p>
<p>Los autores llaman a este fenomeno <strong>Cyber Threat Inflation</strong>: una inflacion de amenazas causada por la reduccion simultanea de tres barreras historicas del ciberataque: tiempo, conocimiento experto y recursos.</p>

<h2>Fuentes academicas principales</h2>
<ul>
<li><a href="https://arxiv.org/abs/2505.12786" target="_blank" rel="noopener noreferrer">Forewarned is Forearmed: A Survey on Large Language Model-based Agents in Autonomous Cyberattacks</a></li>
<li><a href="https://arxiv.org/abs/2505.12567" target="_blank" rel="noopener noreferrer">A Survey of Attacks on Large Language Models</a></li>
<li><a href="https://arxiv.org/abs/2402.06664" target="_blank" rel="noopener noreferrer">LLM Agents can Autonomously Hack Websites</a></li>
<li><a href="https://arxiv.org/abs/2408.08926" target="_blank" rel="noopener noreferrer">Cybench: A Framework for Evaluating Cybersecurity Capabilities and Risks of Language Models</a></li>
<li><a href="https://arxiv.org/abs/2511.15759" target="_blank" rel="noopener noreferrer">Securing AI Agents Against Prompt Injection Attacks</a></li>
<li><a href="https://www.nist.gov/publications/adversarial-machine-learning-taxonomy-and-terminology-attacks-and-mitigations-0" target="_blank" rel="noopener noreferrer">NIST AI 100-2e2025: Adversarial Machine Learning</a></li>
</ul>

<h2>Del chatbot al agente operativo</h2>
<p>La diferencia entre un modelo conversacional y un agente es critica. Un chatbot responde. Un agente puede planificar, llamar herramientas, consultar documentos, usar un navegador, ejecutar comandos, interactuar con APIs y adaptar su estrategia si falla. Esa arquitectura convierte al modelo en una capa de decision encima de sistemas reales.</p>
<p>El survey de Xu y colaboradores organiza las capacidades ofensivas de estos agentes en componentes como scouting, memoria, razonamiento y accion. En terminos de ciberseguridad, eso se parece cada vez mas a una cadena de ataque: reconocer el entorno, formular hipotesis, probar vectores, observar resultados y continuar.</p>
<p>La novedad no es que la IA "quiera" atacar. La novedad es que puede disminuir el esfuerzo humano necesario para convertir instrucciones generales en pasos tecnicos ejecutables. Eso cambia la economia del ataque.</p>

<h2>La evidencia experimental: agentes que hackean sitios</h2>
<p>Uno de los papers fundacionales de esta discusion es <em>LLM Agents can Autonomously Hack Websites</em>. Sus autores muestran que agentes LLM pueden explotar vulnerabilidades web sin conocerlas previamente, incluyendo tareas como extraccion ciega de esquemas de base de datos e inyecciones SQL, usando herramientas y contexto extendido.</p>
<p>El punto relevante no es convertir el paper en alarma sensacionalista. Los resultados dependen de modelos frontier, entornos especificos y condiciones controladas. Pero marcan una direccion: la capacidad ofensiva ya no esta limitada a que un humano escriba cada paso. La IA puede explorar, fallar, corregir y probar de nuevo.</p>
<p>Esto se conecta con el caso Rutify y otras alertas recientes solo como contexto general: cuando credenciales, APIs o tokens quedan expuestos, un agente con herramientas podria ayudar a automatizar reconocimiento, correlacion de datos o abuso de accesos. La diferencia entre tener datos filtrados y convertirlos en una campana efectiva se achica.</p>

<h2>Benchmarks: medir antes de opinar</h2>
<p>La investigacion seria no se queda en afirmaciones generales. Por eso benchmarks como Cybench son importantes. Cybench propone 40 tareas profesionales tipo Capture the Flag, provenientes de cuatro competencias, con entornos donde un agente puede ejecutar comandos y observar salidas. Ademas, divide tareas complejas en subtareas para medir progreso parcial.</p>
<p>Este enfoque ayuda a responder una pregunta central: que tan capaces son los modelos en tareas de ciberseguridad, y bajo que scaffolds o estructuras de agente mejoran o fallan? Sin benchmarks, la conversacion se vuelve puro marketing o puro miedo. Con benchmarks, se puede comparar modelos, medir limites y construir politicas de despliegue mas realistas.</p>
<p>CAIBench, otro marco reciente, empuja en la misma direccion: evaluar agentes en dominios ofensivos y defensivos para medir relevancia laboral, riesgo operativo y capacidades reales. La seguridad de la IA necesitara cada vez mas este tipo de evaluaciones continuas, no auditorias unicas antes de produccion.</p>

<h2>Prompt injection: el viejo problema de separar datos e instrucciones</h2>
<p>Los agentes conectados a herramientas abren una vulnerabilidad particular: prompt injection. En sistemas RAG o agentes que leen correos, paginas web, tickets, documentos o respuestas de APIs, un atacante puede insertar instrucciones maliciosas dentro del contexto que el agente procesara como si fuera informacion normal.</p>
<p>El paper <em>Securing AI Agents Against Prompt Injection Attacks</em> propone un benchmark con 847 casos adversariales repartidos en cinco categorias: inyeccion directa, manipulacion de contexto, override de instrucciones, exfiltracion de datos y contaminacion entre contextos. Tambien evalua defensas multicapa como filtrado de contenido, deteccion de anomalias por embeddings, guardrails jerarquicos y verificacion de respuesta.</p>
<p>Su resultado es util como senal, mas que como receta final: las defensas combinadas reducen fuertemente la tasa de ataques exitosos, pero no eliminan el problema. En agentes con acceso a informacion sensible, una reduccion estadistica no basta si no hay control de permisos, sandboxing, auditoria y confirmacion humana para acciones de alto impacto.</p>

<h2>La taxonomia de NIST: ordenar el mapa de amenazas</h2>
<p>NIST AI 100-2e2025 entrega un marco oficial para hablar de adversarial machine learning. Su valor es ordenar un campo que suele mezclarse: ataques de evasion, poisoning, privacidad, extraccion de modelos, manipulacion de entradas y mitigaciones. Para equipos no academicos, esa taxonomia sirve como idioma comun entre seguridad, datos, legal, producto y direccion.</p>
<p>En el auge de la IA generativa, esta taxonomia debe ampliarse en la practica hacia agentes: no solo proteger el modelo, sino el sistema completo que lo rodea. Eso incluye prompts del sistema, herramientas, conectores, credenciales, memorias, bases vectoriales, logs, politicas de autorizacion y usuarios humanos.</p>

<h2>El cambio de fondo: inflacion de amenaza</h2>
<p>Cyber Threat Inflation no significa que todos los atacantes se vuelvan expertos de inmediato. Significa que el piso minimo sube. Un actor con poca experiencia puede apoyarse en modelos para escribir phishing mas convincente, entender errores, generar scripts, traducir documentacion tecnica, resumir dumps de datos o automatizar pasos de reconocimiento.</p>
<p>Tambien significa que los defensores enfrentan mas volumen. Si atacar cuesta menos, se intentan mas ataques. Si probar variantes cuesta menos, aparecen mas campanas personalizadas. Si correlacionar datos filtrados cuesta menos, aumenta el riesgo de fraude dirigido.</p>
<p>La consecuencia para organizaciones publicas y privadas es clara: no basta con comprar herramientas de IA para defensa. Hay que redisenar procesos de seguridad bajo la premisa de que el atacante tambien tiene IA.</p>

<h2>Que deberian hacer empresas y Estado</h2>
<p>Primero, tratar a los agentes como usuarios privilegiados. Si un agente puede leer correo, consultar bases, llamar APIs o ejecutar acciones, debe tener identidad, permisos minimos, logs, limites de tasa y revocacion de acceso. No es "solo un asistente". Es una superficie operacional.</p>
<p>Segundo, separar instrucciones, datos y herramientas. La seguridad de agentes requiere que el sistema no confunda contenido externo con ordenes. Esto implica filtros, politicas de tool use, verificacion de contexto, listas de acciones permitidas y aprobacion humana para operaciones sensibles.</p>
<p>Tercero, evaluar continuamente. Benchmarks internos, red teaming, pruebas de prompt injection, simulaciones de fuga de datos y ejercicios de abuso de herramientas deben formar parte del ciclo de vida. Un modelo seguro hoy puede no serlo despues de conectarlo a nuevos datos o permisos.</p>
<p>Cuarto, mantener fundamentos clasicos: MFA, minimo privilegio, segmentacion, rotacion de secretos, monitoreo de credenciales filtradas, SBOM, gestion de vulnerabilidades y respuesta a incidentes. La IA no reemplaza higiene basica; la vuelve mas urgente.</p>

<h2>Lectura para Chile y America Latina</h2>
<p>La region esta en un momento delicado. Empresas, universidades y servicios publicos quieren adoptar IA rapidamente, pero muchas organizaciones aun arrastran deuda tecnica: sistemas heredados, integraciones fragiles, baja madurez de logs, cuentas compartidas y controles de acceso inconsistentes.</p>
<p>Agregar agentes de IA sobre esa base puede amplificar productividad, pero tambien ampliar el radio de error. Un agente conectado a datos tributarios, salud, educacion, municipalidades o banca no puede desplegarse con la misma ligereza que un chatbot de preguntas frecuentes.</p>
<p>La oportunidad esta en usar la investigacion academica antes del accidente. Estos papers ofrecen un mapa: medir capacidades, asumir que el atacante tambien automatiza, proteger herramientas y credenciales, y construir gobernanza desde el diseno.</p>

<h2>Conclusion</h2>
<p>La ciberseguridad en el auge de la IA no se reduce a bloquear prompts peligrosos. Es una transformacion de la economia del ataque y la defensa. Los modelos reducen friccion, los agentes convierten lenguaje en accion, y las organizaciones deben decidir cuanto poder delegan a sistemas que todavia pueden confundir instrucciones, datos y objetivos.</p>
<p>La leccion del paper es sobria: estar advertidos es estar armados. No porque la IA vuelva inevitable el desastre, sino porque permite anticipar una nueva clase de riesgo antes de desplegar agentes sobre infraestructura critica.</p>
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
            'home_latest_papers',
            'papers_featured',
            'papers_arxiv_cats',
        ] as $key) {
            Cache::forget($key);
        }
    }
};
