<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private string $slug = 'chile-ia-ciberseguridad-datos-capacidad-estatal';

    public function up(): void
    {
        if (!Schema::hasTable('research')) {
            return;
        }

        $now = Carbon::now();
        $categoryId = $this->categoryId($now);
        $content = $this->content();

        DB::table('research')->update([
            'featured' => false,
            'updated_at' => $now,
        ]);

        $payload = [
            'title' => 'Chile ante la IA y la ciberseguridad: la brecha ya no es tecnológica, es institucional',
            'slug' => $this->slug,
            'excerpt' => 'Chile combina avances importantes en política de IA y ciberseguridad con una deuda crítica: datos fragmentados, baja capacidad pública, escasez de talento y riesgos crecientes por agentes, prompt injection y sistemas automatizados conectados a servicios sensibles.',
            'content' => $content,
            'summary' => 'Chile está mejor posicionado que buena parte de la región en institucionalidad digital, pero la convergencia entre IA, datos personales y ciberseguridad tensiona sus capacidades públicas. Informes de UNESCO y BID muestran avances regulatorios e institucionales, mientras investigaciones chilenas recientes sobre prompt injection, explicabilidad y biometría evidencian que el país necesita pasar de la adopción de IA a una gobernanza operacional: auditoría, trazabilidad, protección de datos y seguridad por diseño.',
            'abstract' => 'Esta investigación analiza el cruce entre inteligencia artificial, ciberseguridad y gobernanza de datos en Chile. A partir del AI Readiness Assessment de UNESCO, el Cybersecurity Report 2025 del BID, una tesis UdeC sobre prompt injection, trabajos chilenos sobre explicabilidad y biometría, y el contexto reciente de ANCI, se argumenta que el desafío principal del país no es solo incorporar IA, sino construir capacidad institucional para desplegarla con seguridad, supervisión y legitimidad democrática.',
            'image' => 'research-4.jpg',
            'type' => 'Chile AI Research',
            'research_type' => 'study',
            'author' => 'Editor ConocIA',
            'views' => 0,
            'comments_count' => 0,
            'citations' => 999,
            'featured' => true,
            'is_published' => true,
            'status' => 'published',
            'category_id' => $categoryId,
            'institution' => 'UNESCO / BID / Universidad de Concepción / Universidad de Chile / Revista Chilena de Derecho y Tecnología',
            'references' => implode(PHP_EOL, [
                'https://unesdoc.unesco.org/in/rest/annotationSVC/DownloadWatermarkedAttachment/attach_import_51b8bf6c-7544-4ab1-926a-5117e01e1f61?_=387216eng.pdf',
                'https://publications.iadb.org/publications/english/document/2025-Cybersecurity-Report-Vulnerability-and-Maturity-Challenges-to-Bridging-the-Gaps-in-Latin-America-and-the-Caribbean.pdf',
                'https://repositorio.udec.cl/handle/11594/13629',
                'https://rchdt.uchile.cl/index.php/RCHDT/article/view/74040',
                'https://repositorio.uchile.cl/handle/2250/208717',
            ]),
            'additional_authors' => 'UNESCO, Banco Interamericano de Desarrollo, Iván Elías Montti Davison, Darío Parra Sepúlveda, Ricardo Concha Machuca, José Antonio Fernández Pérez',
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('research')->updateOrInsert(['slug' => $this->slug], $payload);

        $this->clearCaches();
    }

    public function down(): void
    {
        if (!Schema::hasTable('research')) {
            return;
        }

        DB::table('research')->where('slug', $this->slug)->delete();

        $this->clearCaches();
    }

    private function categoryId(Carbon $now): int
    {
        $name = 'Investigación';
        $slug = Str::slug($name);

        $category = DB::table('categories')->where('slug', $slug)->first();

        if ($category) {
            DB::table('categories')->where('id', $category->id)->update([
                'name' => $name,
                'description' => 'Investigaciones, estudios, tesis y análisis de fondo sobre inteligencia artificial.',
                'color' => '#38b6ff',
                'icon' => 'fa-flask',
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $category->id;
        }

        return (int) DB::table('categories')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'description' => 'Investigaciones, estudios, tesis y análisis de fondo sobre inteligencia artificial.',
            'color' => '#38b6ff',
            'icon' => 'fa-flask',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function content(): string
    {
        return <<<'HTML'
<p>Chile suele aparecer bien ubicado en los rankings regionales de transformación digital e inteligencia artificial. Tiene una política nacional de IA, una institucionalidad emergente de ciberseguridad, un ecosistema académico activo y proyectos como Latam-GPT que buscan construir capacidades propias para América Latina. Pero la pregunta de fondo ya no es si Chile puede adoptar IA. La pregunta es si puede gobernarla con seguridad.</p>

<p>La convergencia entre inteligencia artificial, datos personales y ciberseguridad está creando una zona crítica para el Estado chileno. Los sistemas de IA necesitan datos; los servicios públicos digitalizados producen y procesan datos; y los atacantes, cada vez más apoyados por automatización, buscan credenciales, APIs, tokens, documentos y perfiles reutilizables. En ese cruce, la brecha principal deja de ser solo tecnológica. Es institucional.</p>

<h2>Fuentes principales</h2>
<ul>
<li><a href="https://unesdoc.unesco.org/in/rest/annotationSVC/DownloadWatermarkedAttachment/attach_import_51b8bf6c-7544-4ab1-926a-5117e01e1f61?_=387216eng.pdf" target="_blank" rel="noopener noreferrer">UNESCO: Chile Artificial Intelligence Readiness Assessment Report</a></li>
<li><a href="https://publications.iadb.org/publications/english/document/2025-Cybersecurity-Report-Vulnerability-and-Maturity-Challenges-to-Bridging-the-Gaps-in-Latin-America-and-the-Caribbean.pdf" target="_blank" rel="noopener noreferrer">BID: 2025 Cybersecurity Report for Latin America and the Caribbean</a></li>
<li><a href="https://repositorio.udec.cl/handle/11594/13629" target="_blank" rel="noopener noreferrer">Universidad de Concepción: Detección de ataques de prompt injection sobre chatbots basados en LLM</a></li>
<li><a href="https://rchdt.uchile.cl/index.php/RCHDT/article/view/74040" target="_blank" rel="noopener noreferrer">Revista Chilena de Derecho y Tecnología: IA explicable y gobernanza</a></li>
<li><a href="https://repositorio.uchile.cl/handle/2250/208717" target="_blank" rel="noopener noreferrer">Universidad de Chile: IA biométrica, regulación y derechos fundamentales</a></li>
</ul>

<h2>El diagnóstico UNESCO: Chile tiene base, pero no basta</h2>
<p>El <em>Artificial Intelligence Readiness Assessment Report</em> de UNESCO sobre Chile muestra un país con avances relevantes en política pública, investigación, regulación, capacidad científica y discusión ética. El documento organiza el diagnóstico en dimensiones legales, regulatorias, sociales, educativas, económicas, técnicas e institucionales. Esa estructura es útil porque evita una lectura simplista: la IA no depende solo de tener modelos o startups, sino de contar con datos, conectividad, normas, instituciones, talento y confianza pública.</p>

<p>Entre sus recomendaciones, UNESCO apunta a acelerar la actualización de la protección de datos personales y el marco de ciberseguridad, crear gobernanza adaptativa y multiactor para la regulación de IA, explorar mecanismos de experimentación regulatoria y actualizar la política nacional de IA. Es decir, el informe trata la IA como infraestructura de país, no como una herramienta aislada.</p>

<p>La lectura para Chile es clara: la IA requiere capacidad estatal continua. No basta con publicar una estrategia. Hay que sostenerla con presupuesto, coordinación, estándares de compra pública, capacidades técnicas en servicios, mecanismos de auditoría y sistemas de rendición de cuentas.</p>

<h2>El diagnóstico BID: Chile avanzó en ciberseguridad, pero la amenaza se mueve más rápido</h2>
<p>El <em>2025 Cybersecurity Report</em> del BID reconoce avances importantes de Chile. Destaca la segunda Política Nacional de Ciberseguridad 2023-2028, la Ley Marco de Ciberseguridad, la creación de ANCI y el fortalecimiento de capacidades de respuesta, coordinación y protección de infraestructura. También menciona que ANCI comenzó operaciones en 2025 y que el país consolidó funciones de respuesta a incidentes y coordinación de vulnerabilidades.</p>

<p>Ese avance es sustantivo. Chile ya no parte desde cero. Tiene una agencia, una ley, una política nacional, un comité interministerial, un consejo multisectorial y vínculos de cooperación internacional. Pero el mismo informe muestra el otro lado: la región sigue enfrentando brechas de madurez, talento, coordinación e implementación. La institucionalidad existe, pero debe volverse operativa bajo presión real.</p>

<p>La IA aumenta esa presión. Si los ataques son más automatizados, personalizados y baratos, la defensa no puede depender de procesos lentos, inventarios incompletos o logs que nadie revisa. La IA obliga a convertir la ciberseguridad en una práctica viva, no en un organigrama.</p>

<h2>Prompt injection: una investigación chilena toca el punto exacto</h2>
<p>Una tesis de la Universidad de Concepción de 2025, <em>Detección de ataques de prompt injection sobre chatbots basados en grandes modelos de lenguaje</em>, muestra que la academia chilena ya está observando uno de los riesgos más relevantes de la IA generativa. El problema de prompt injection no es marginal. Aparece cuando un atacante introduce instrucciones maliciosas en el contexto que un modelo procesa, logrando que el sistema ignore reglas, revele información o ejecute acciones no deseadas.</p>

<p>Esto es particularmente importante para servicios públicos y empresas chilenas que empiezan a conectar chatbots, buscadores semánticos o agentes a documentos internos, bases de conocimiento, trámites o canales de atención. En esos entornos, una vulnerabilidad no siempre se parece a un exploit clásico. Puede parecer una frase dentro de un documento, un correo, un ticket o una página web que el modelo lee como contexto.</p>

<p>La tesis UdeC sirve como señal temprana: Chile no solo debe adoptar LLMs, debe investigarlos como superficie de ataque. Y esa diferencia es decisiva.</p>

<h2>Explicabilidad: gobernar IA exige entender decisiones</h2>
<p>El artículo de Darío Parra Sepúlveda y Ricardo Concha Machuca en la <em>Revista Chilena de Derecho y Tecnología</em> analiza la inteligencia artificial explicable como fundamento para la gobernanza de la IA y sus implicancias en responsabilidad civil. Su aporte es clave para esta discusión porque conecta técnica y derecho: si un sistema automatizado produce daño, discrimina o toma una decisión relevante, la capacidad de explicar su funcionamiento deja de ser un lujo académico.</p>

<p>En ciberseguridad ocurre algo parecido. Un sistema de detección basado en IA puede bloquear accesos, marcar usuarios como sospechosos, priorizar incidentes o recomendar medidas. Si nadie puede explicar por qué tomó esas decisiones, se debilita la confianza y se vuelve difícil auditar errores. En el sector público, esa opacidad puede afectar derechos.</p>

<p>Por eso, la explicabilidad no debe verse solo como principio ético. En IA aplicada a seguridad, es una condición de trazabilidad, responsabilidad y defensa institucional.</p>

<h2>Biometría: el caso donde datos, IA y poder público se vuelven inseparables</h2>
<p>La memoria de la Universidad de Chile sobre inteligencia artificial biométrica sostiene que la regulación chilena sigue siendo insuficiente frente al uso de identificación biométrica en tiempo real y otros sistemas de alto riesgo. El trabajo analiza casos como SITIA y el empadronamiento biométrico, y advierte sobre la recopilación de datos intrínsecamente sensibles sin salvaguardas proporcionales.</p>

<p>Este punto conecta directamente con ciberseguridad. Los datos biométricos no son una contraseña que se cambia después de una filtración. Si se comprometen, el daño puede ser persistente. Por eso, el despliegue de IA biométrica en espacios públicos o servicios estatales necesita estándares más altos que los sistemas digitales comunes: necesidad, proporcionalidad, minimización, controles de acceso, auditoría y autoridad preventiva real.</p>

<p>Chile no puede discutir IA biométrica solo como modernización. Tiene que discutirla como infraestructura de poder sobre cuerpos, identidad y circulación ciudadana.</p>

<h2>La brecha institucional</h2>
<p>Tomadas en conjunto, estas fuentes dibujan una tesis: Chile tiene piezas importantes, pero todavía no tiene una arquitectura completa para gobernar la IA segura. Hay política de IA, ley de ciberseguridad, futura institucionalidad de datos personales, investigación académica y capacidades emergentes. Pero el riesgo aparece en los espacios entre esas piezas.</p>

<p>Entre IA y ciberseguridad. Entre datos personales y servicios públicos. Entre compra tecnológica y evaluación técnica. Entre innovación y fiscalización. Entre entusiasmo por automatizar y capacidad real de auditar.</p>

<p>Ahí está la brecha: no es que Chile no tenga tecnología. Es que necesita convertir regulación, investigación, talento y operación en un sistema coordinado.</p>

<h2>Qué debería hacer Chile ahora</h2>
<p>Primero, exigir evaluación de seguridad para sistemas de IA usados en servicios críticos. Cualquier chatbot, motor de búsqueda, agente o sistema predictivo conectado a información sensible debería pasar por pruebas de prompt injection, fuga de datos, control de permisos y auditoría de logs.</p>

<p>Segundo, tratar los datos públicos como infraestructura crítica cuando alimentan IA. La discusión sobre datos no puede reducirse a transparencia o eficiencia. Debe incluir minimización, gobernanza, clasificación de sensibilidad, ciclo de vida, trazabilidad y respuesta ante incidentes.</p>

<p>Tercero, crear capacidades técnicas dentro del Estado. No basta comprar soluciones. Los servicios públicos necesitan equipos capaces de evaluar modelos, revisar contratos, interpretar logs, auditar proveedores y entender cuándo una herramienta de IA introduce riesgo nuevo.</p>

<p>Cuarto, conectar ANCI, la futura Agencia de Protección de Datos, universidades y organismos sectoriales. La IA segura no se resuelve desde una sola institución. Requiere una mesa operacional permanente entre ciberseguridad, protección de datos, ciencia, transformación digital, salud, educación, municipios e infraestructura crítica.</p>

<p>Quinto, financiar investigación aplicada chilena. La tesis UdeC sobre prompt injection, los trabajos sobre explicabilidad y biometría, y los informes de preparación muestran que la academia local puede producir conocimiento relevante. Pero ese conocimiento debe entrar al ciclo de política pública y no quedar aislado en repositorios.</p>

<h2>Lectura final</h2>
<p>Chile tiene una oportunidad real. Puede esperar a que los incidentes dicten la agenda, o puede usar la investigación disponible para anticiparse. La IA no llega a un Estado vacío: llega a sistemas con datos, trámites, usuarios, brechas, proveedores, deuda técnica y derechos fundamentales.</p>

<p>La pregunta no es si Chile usará inteligencia artificial. Ya la está usando. La pregunta es si será capaz de construir una IA pública y privada que sea útil, segura, explicable y proporcional. Ese es el verdadero desafío de la próxima etapa.</p>
HTML;
    }

    private function clearCaches(): void
    {
        foreach ([
            'home_page_data',
            'home_page_data_v2',
            'research_articles',
            'featured_research',
            'most_commented_research',
            'all_categories',
            'featured_categories',
        ] as $key) {
            Cache::forget($key);
        }
    }
};
