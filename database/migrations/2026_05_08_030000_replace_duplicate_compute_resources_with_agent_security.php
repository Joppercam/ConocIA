<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private string $oldResearchSlug = 'infraestructura-ia-anthropic-spacex-colossus-computo-frontera';
    private string $newResearchSlug = 'seguridad-agentes-ia-mcp-prompt-injection-tool-poisoning';
    private string $oldPaperArxivId = '2604.07345';
    private string $newPaperArxivId = '2604.24118';
    private string $newPaperSlug = 'agentvisor-agentes-ia-prompt-injection-virtualizacion-semantica';

    public function up(): void
    {
        $now = Carbon::now();

        if (Schema::hasTable('research')) {
            DB::table('research')->where('slug', $this->oldResearchSlug)->delete();
            $this->publishResearch($now);
        }

        if (Schema::hasTable('conocia_papers')) {
            DB::table('conocia_papers')->where('arxiv_id', $this->oldPaperArxivId)->delete();
            $this->publishPaper($now);
        }

        $this->clearCaches();
    }

    public function down(): void
    {
        if (Schema::hasTable('research')) {
            DB::table('research')->where('slug', $this->newResearchSlug)->delete();
        }

        if (Schema::hasTable('conocia_papers')) {
            DB::table('conocia_papers')->where('arxiv_id', $this->newPaperArxivId)->delete();
        }

        $this->clearCaches();
    }

    private function publishResearch(Carbon $now): void
    {
        $content = $this->researchContent();
        $payload = [
            'title' => 'Agentes de IA bajo ataque: MCP, tool poisoning y el nuevo frente de la seguridad empresarial',
            'slug' => $this->newResearchSlug,
            'excerpt' => 'La seguridad de la IA ya no se juega solo en el prompt. Cuando un agente puede descubrir herramientas, llamar APIs y leer datos externos, aparecen riesgos nuevos: tool poisoning, prompt injection indirecta, abuso de permisos y cadenas de suministro de herramientas.',
            'content' => $content,
            'abstract' => 'Esta investigacion analiza el Model Context Protocol y la seguridad de agentes de IA desde una perspectiva empresarial. A partir de investigaciones recientes sobre threat modeling de MCP, Secure MCP y defensas contra prompt injection, se propone una matriz practica para evaluar agentes conectados a herramientas: identidad, permisos, procedencia de datos, validacion de metadatos, auditoria, separacion de contexto y supervision humana.',
            'summary' => 'Los agentes de IA conectados a herramientas cambian la superficie de ataque. MCP simplifica la integracion entre modelos, datos y servicios, pero tambien puede exponer a las organizaciones a tool poisoning, prompt injection indirecta, privilegios excesivos y abuso de conectores. La respuesta no es prohibir agentes, sino tratarlos como software operativo con identidad, politicas, logs, sandboxing y revisiones de seguridad.',
            'image' => 'research-4.jpg',
            'type' => 'Seguridad IA',
            'research_type' => 'analysis',
            'author' => 'Juan Pablo Basualdo',
            'views' => 0,
            'comments_count' => 0,
            'citations' => 1400,
            'featured' => true,
            'is_published' => true,
            'status' => 'published',
            'category_id' => $this->categoryId('Seguridad IA', $now),
            'institution' => 'ConocIA Research Desk',
            'references' => implode(PHP_EOL, [
                'https://modelcontextprotocol.io/docs/getting-started/intro',
                'https://arxiv.org/abs/2603.22489',
                'https://arxiv.org/abs/2602.01129',
                'https://arxiv.org/abs/2604.24118',
            ]),
            'additional_authors' => 'ConocIA Research Desk',
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $authorId = $this->juanPabloId();
        if ($authorId !== null && Schema::hasColumn('research', 'user_id')) {
            $payload['user_id'] = $authorId;
        }

        DB::table('research')->updateOrInsert(['slug' => $this->newResearchSlug], $payload);
    }

    private function publishPaper(Carbon $now): void
    {
        $content = $this->paperContent();
        DB::table('conocia_papers')->updateOrInsert(
            ['arxiv_id' => $this->newPaperArxivId],
            [
                'arxiv_id' => $this->newPaperArxivId,
                'arxiv_url' => 'https://arxiv.org/abs/2604.24118',
                'original_title' => 'AgentVisor: Defending LLM Agents Against Prompt Injection via Semantic Virtualization',
                'original_abstract' => 'Large Language Model agents are increasingly used to automate complex workflows, but integrating untrusted external data with privileged execution exposes them to severe security risks, particularly direct and indirect prompt injection. AgentVisor enforces semantic privilege separation by treating the target agent as an untrusted guest and intercepting tool calls through a trusted semantic visor.',
                'authors' => $this->json([
                    'Zonghao Ying',
                    'Haozheng Wang',
                    'Jiangfan Liu',
                    'Quanchen Zou',
                    'Aishan Liu',
                    'Jian Yang',
                    'Yaodong Yang',
                    'Xianglong Liu',
                ]),
                'arxiv_published_date' => '2026-04-27',
                'arxiv_category' => 'cs.CR',
                'title' => 'AgentVisor: separar semanticamente permisos para defender agentes de IA',
                'slug' => $this->newPaperSlug,
                'excerpt' => 'AgentVisor propone una defensa inspirada en virtualizacion de sistemas operativos: tratar al agente como invitado no confiable, interceptar tool calls y aplicar separacion semantica de privilegios para reducir prompt injection directa e indirecta.',
                'content' => $content,
                'key_contributions' => $this->json([
                    'Formula la defensa de agentes como un problema de separacion semantica de privilegios.',
                    'Trata al agente objetivo como un invitado no confiable y ubica un visor confiable entre el agente y las herramientas.',
                    'Interviene tool calls antes de que una instruccion maliciosa pueda convertirse en accion privilegiada.',
                    'Agrega un mecanismo de autocorreccion para que las violaciones de seguridad se transformen en feedback util.',
                    'Reporta una reduccion fuerte de attack success rate con baja perdida de utilidad en los experimentos publicados.',
                ]),
                'practical_implications' => $this->json([
                    'Los agentes empresariales no deberian ejecutar herramientas directamente sin una capa de control confiable.',
                    'La defensa debe mirar acciones y permisos, no solo texto de entrada.',
                    'Separar datos no confiables, instrucciones y tool calls reduce el riesgo de prompt injection indirecta.',
                    'Los equipos de seguridad pueden adaptar ideas de sistemas operativos: sandboxing, privilegios minimos, auditoria y monitores confiables.',
                    'MCP, copilotos de codigo y agentes de workflow necesitan evaluaciones continuas antes de conectar datos sensibles.',
                ]),
                'difficulty_level' => 'avanzado',
                'image' => null,
                'featured' => true,
                'status' => 'published',
                'views' => 0,
                'reading_time' => $this->readingTime($content),
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    private function categoryId(string $name, Carbon $now): ?int
    {
        if (!Schema::hasTable('categories')) {
            return null;
        }

        $slug = Str::slug($name);
        $category = DB::table('categories')->where('slug', $slug)->first();
        $payload = [
            'name' => $name,
            'description' => 'Seguridad, gobernanza, prompt injection, agentes, herramientas y riesgos operacionales de inteligencia artificial.',
            'color' => '#dc2626',
            'icon' => 'fa-shield-alt',
            'is_active' => true,
            'updated_at' => $now,
        ];

        if ($category) {
            DB::table('categories')->where('id', $category->id)->update($payload);

            return (int) $category->id;
        }

        $payload['slug'] = $slug;
        $payload['created_at'] = $now;

        return (int) DB::table('categories')->insertGetId($payload);
    }

    private function juanPabloId(): ?int
    {
        if (!Schema::hasTable('users')) {
            return null;
        }

        $authorId = DB::table('users')
            ->whereRaw('LOWER(name) = ?', ['juan pablo basualdo'])
            ->value('id');

        if ($authorId) {
            return (int) $authorId;
        }

        $fallbackId = DB::table('users')
            ->whereRaw('LOWER(name) LIKE ?', ['%juan%basualdo%'])
            ->orderBy('id')
            ->value('id');

        return $fallbackId ? (int) $fallbackId : null;
    }

    private function readingTime(string $content): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($content)) / 220));
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
            'research_page_data',
            'research_articles',
            'featured_research',
            'most_commented_research',
            "research_article_{$this->oldResearchSlug}",
            "research_article_{$this->newResearchSlug}",
            'home_latest_papers',
            'home_featured_paper',
            'papers_featured',
            'papers_arxiv_cats',
            'all_categories',
            'featured_categories',
        ] as $key) {
            Cache::forget($key);
        }
    }

    private function researchContent(): string
    {
        return <<<'HTML'
<p>La adopcion de agentes de IA esta cambiando la conversacion de seguridad. Un chatbot responde. Un agente observa contexto, decide pasos, llama herramientas, consulta APIs, lee documentos y puede ejecutar acciones sobre sistemas reales. Esa diferencia convierte a la IA en una nueva superficie operacional.</p>

<p>El Model Context Protocol, conocido como MCP, acelera esta transicion porque estandariza la forma en que asistentes y agentes se conectan con herramientas y fuentes de datos. Esa estandarizacion es poderosa: reduce friccion, evita integraciones a medida y permite componer ecosistemas de agentes. Pero tambien introduce una pregunta incomoda: que pasa cuando el modelo ya no solo lee informacion, sino que puede actuar sobre ella?</p>

<h2>La hipotesis</h2>
<p>La hipotesis de esta investigacion es simple: las organizaciones no deberian evaluar agentes de IA como si fueran interfaces conversacionales. Deben evaluarlos como software con permisos, identidad, dependencias, conectores, logs, controles de acceso y riesgo de cadena de suministro.</p>

<p>El prompt sigue importando, pero ya no alcanza. Cuando hay herramientas conectadas, el riesgo se desplaza hacia metadatos, permisos, procedencia de datos, tool calls, resultados no confiables y decisiones automatizadas que pueden parecer legitimas.</p>

<h2>Tool poisoning: cuando la herramienta miente antes de ejecutarse</h2>
<p>Una investigacion de marzo de 2026 sobre MCP aplica threat modeling con STRIDE y DREAD a componentes como host, cliente, LLM, servidores MCP, data stores externos y servidores de autorizacion. Su hallazgo mas relevante es que el tool poisoning aparece como una vulnerabilidad prevalente e impactante del lado cliente.</p>

<p>El problema es elegante y peligroso: una herramienta puede describirse a si misma con metadatos que contienen instrucciones maliciosas o ambiguas. El agente lee esos metadatos para decidir como usar la herramienta. Si no existe validacion suficiente, una descripcion envenenada puede influir en el razonamiento del modelo antes de que el usuario vea el riesgo.</p>

<p>En seguridad tradicional, esto se parece a confiar demasiado en un paquete, plugin o dependencia. En agentes de IA, la dependencia no solo se instala: tambien conversa con el modelo.</p>

<h2>Prompt injection indirecta: el dato se disfraza de instruccion</h2>
<p>La prompt injection indirecta aparece cuando el agente procesa contenido externo que contiene instrucciones hostiles: una pagina web, un ticket, un correo, un documento compartido o una respuesta de una API. El usuario no escribe el ataque. El agente lo encuentra mientras trabaja.</p>

<p>Esto rompe una intuicion comun: no basta con entrenar al usuario para que no escriba prompts peligrosos. El agente puede traer el peligro desde fuera. Si ese contenido se mezcla en el mismo contexto que las instrucciones del sistema, el modelo puede confundir datos no confiables con ordenes.</p>

<h2>MCP como oportunidad y como riesgo</h2>
<p>MCP no es "el problema". El problema es desplegar ecosistemas de herramientas sin una arquitectura de seguridad proporcional. MCP puede ordenar integraciones, pero tambien puede facilitar que una mala herramienta, un servidor mal configurado o un cliente poco transparente amplifique el riesgo.</p>

<p>Por eso la respuesta no deberia ser bloquear toda adopcion. La respuesta deberia ser madurar controles: identidad de agentes, autorizacion granular, permisos minimos, auditoria, validacion de metadatos, procedencia de herramientas, aislamiento de contexto y aprobacion humana para acciones sensibles.</p>

<h2>La propuesta SMCP</h2>
<p>El paper SMCP: Secure Model Context Protocol propone una extension de seguridad a nivel de protocolo. Sus piezas centrales son identidad unificada, autenticacion mutua, propagacion continua de contexto de seguridad, politicas finas y logging integral. Esa direccion es correcta porque mueve la seguridad desde recomendaciones sueltas hacia una capa sistemica.</p>

<p>En una empresa, esto se traduce en preguntas concretas: que identidad tiene el agente, bajo que usuario actua, que herramientas puede descubrir, que acciones puede ejecutar, que datos puede leer, que permisos hereda y que queda registrado para auditoria.</p>

<h2>Matriz de evaluacion para agentes conectados</h2>
<ul>
<li><strong>Identidad:</strong> cada agente debe tener identidad propia, no operar como usuario generico.</li>
<li><strong>Permisos:</strong> aplicar minimo privilegio por herramienta, accion y contexto.</li>
<li><strong>Procedencia:</strong> validar de donde vienen herramientas, prompts, documentos y resultados.</li>
<li><strong>Separacion:</strong> no mezclar instrucciones confiables con contenido externo no confiable.</li>
<li><strong>Auditoria:</strong> registrar decisiones, tool calls, parametros, respuestas y aprobaciones.</li>
<li><strong>Transparencia:</strong> mostrar al usuario que herramienta se va a usar y con que parametros.</li>
<li><strong>Sandboxing:</strong> ejecutar acciones riesgosas en entornos limitados o reversibles.</li>
<li><strong>Supervision:</strong> pedir confirmacion humana para pagos, borrados, envios, cambios de permisos o acciones legales.</li>
</ul>

<h2>Lectura para empresas chilenas y latinoamericanas</h2>
<p>El riesgo regional no es que falte entusiasmo por la IA. El riesgo es conectar agentes a sistemas reales con la misma liviandad con que se prueba un chatbot. Muchas organizaciones todavia tienen cuentas compartidas, permisos amplios, baja observabilidad, APIs internas sin buena segmentacion y documentacion dispersa. Un agente encima de esa base puede aumentar productividad, pero tambien amplificar errores.</p>

<p>La adopcion responsable no significa frenar innovacion. Significa decidir donde un agente puede leer, donde puede sugerir, donde puede ejecutar y donde debe pedir autorizacion. Esa diferencia entre asistir y actuar es la nueva frontera de la gobernanza de IA.</p>

<h2>Conclusion</h2>
<p>La seguridad de agentes no se resuelve con prompts mas duros. Se resuelve con arquitectura. MCP y los agentes conectados obligan a traer al mundo de la IA conceptos clasicos de seguridad: identidad, permisos, aislamiento, logging, monitoreo y defensa en profundidad.</p>

<p>El agente puede razonar, pero la organizacion debe decidir que puede tocar.</p>

<h2>Fuentes principales</h2>
<ul>
<li><a href="https://modelcontextprotocol.io/docs/getting-started/intro" target="_blank" rel="noopener noreferrer">Model Context Protocol: introduccion oficial</a></li>
<li><a href="https://arxiv.org/abs/2603.22489" target="_blank" rel="noopener noreferrer">Model Context Protocol Threat Modeling and Analyzing Vulnerabilities to Prompt Injection with Tool Poisoning</a></li>
<li><a href="https://arxiv.org/abs/2602.01129" target="_blank" rel="noopener noreferrer">SMCP: Secure Model Context Protocol</a></li>
<li><a href="https://arxiv.org/abs/2604.24118" target="_blank" rel="noopener noreferrer">AgentVisor: Defending LLM Agents Against Prompt Injection via Semantic Virtualization</a></li>
</ul>
HTML;
    }

    private function paperContent(): string
    {
        return <<<'HTML'
<h2>El problema que intenta resolver</h2>
<p>Los agentes de IA son utiles porque pueden conectar razonamiento con accion. Pueden leer informacion externa, llamar herramientas, operar APIs, escribir codigo, consultar bases de conocimiento y continuar una tarea en varios pasos. Esa misma capacidad los vuelve peligrosos cuando mezclan datos no confiables con ejecucion privilegiada.</p>

<p>AgentVisor parte de ese punto. El paper sostiene que las defensas habituales contra prompt injection tienen una tension dificil: si son muy estrictas, bloquean tareas legitimas; si son flexibles, dejan pasar instrucciones maliciosas sutiles. El aporte del trabajo es cambiar la pregunta: en vez de confiar en que el agente siempre distinguira dato de instruccion, propone poner una capa confiable entre el agente y las herramientas.</p>

<h2>La intuicion: virtualizacion semantica</h2>
<p>La idea viene inspirada por sistemas operativos. En un sistema tradicional, una aplicacion no deberia tener acceso directo e ilimitado al hardware o a recursos sensibles. Opera como invitada bajo control de una capa que aplica permisos, aislamiento y auditoria. AgentVisor lleva esa intuicion al plano semantico de los agentes.</p>

<p>El agente objetivo se trata como un invitado no confiable. Sus tool calls pasan por un visor semantico confiable que intercepta acciones, evalua si respetan la politica y evita que una instruccion inyectada se convierta en operacion peligrosa.</p>

<h2>Por que es distinto a filtrar prompts</h2>
<p>Filtrar texto de entrada ayuda, pero no es suficiente. Un ataque puede llegar como resultado de una busqueda, comentario en un documento, descripcion de herramienta, respuesta de una API o fragmento aparentemente inocente. Ademas, un agente puede transformar una instruccion maliciosa en una accion que parece razonable si se mira solo el texto final.</p>

<p>AgentVisor mira la frontera accion-permiso. Eso es mas cercano a como se protege software real: no basta con preguntar si una frase parece peligrosa; hay que controlar que operaciones puede ejecutar el sistema, con que parametros y bajo que contexto.</p>

<h2>Autocorreccion en vez de bloqueo ciego</h2>
<p>Un punto interesante del paper es el mecanismo de autocorreccion. Cuando el visor detecta una violacion, no se limita a cortar la tarea. Convierte el problema en feedback para que el agente intente una alternativa segura. Esta idea busca reducir el costo de utilidad: proteger sin convertir al agente en una herramienta inutil o excesivamente paranoica.</p>

<p>Segun el resumen del paper, AgentVisor reduce el attack success rate a 0,65% en sus experimentos, con una disminucion promedio de utilidad de 1,45% frente a un escenario sin defensa. Esos resultados deben leerse como evidencia experimental, no como garantia universal, pero muestran una direccion prometedora.</p>

<h2>Implicancias para MCP y agentes empresariales</h2>
<p>El trabajo encaja muy bien con la discusion sobre MCP. A medida que los agentes descubren herramientas y operan conectores, la seguridad no puede quedar en manos del prompt del sistema. Hace falta una capa que entienda herramientas, permisos, parametros, procedencia y consecuencias.</p>

<p>Para una empresa, eso significa que un agente no deberia poder usar correo, CRM, repositorios, calendarios, terminales o bases de datos sin controles intermedios. Cada tool call debe ser observable y gobernable.</p>

<h2>Que puede adoptar un equipo hoy</h2>
<p>Primero, separar claramente datos externos de instrucciones confiables. Si todo termina aplastado en un unico prompt, la defensa nace debil.</p>

<p>Segundo, aplicar minimo privilegio a herramientas. El agente que resume tickets no necesita permiso para borrar clientes. El agente que lee documentacion no necesita credenciales de produccion.</p>

<p>Tercero, registrar tool calls con parametros y resultados. Sin trazabilidad, no hay investigacion posterior ni mejora de politicas.</p>

<p>Cuarto, exigir confirmacion humana para acciones irreversibles o sensibles: pagos, envios masivos, cambios de permisos, eliminaciones, despliegues y comunicaciones externas.</p>

<p>Quinto, evaluar agentes con ataques indirectos, no solo prompts directos. Hay que probar documentos maliciosos, metadatos contaminados, paginas con instrucciones ocultas y respuestas de herramientas manipuladas.</p>

<h2>Lectura final</h2>
<p>AgentVisor es relevante porque desplaza la defensa desde "hacer que el modelo obedezca mejor" hacia "disenar una arquitectura donde no pueda tocar cualquier cosa". Esa diferencia es enorme.</p>

<p>La seguridad de agentes probablemente no se resolvera con un unico metodo. Pero la virtualizacion semantica apunta a una idea madura: cuando una IA puede actuar, debe operar bajo una capa confiable que controle sus permisos.</p>
HTML;
    }
};
