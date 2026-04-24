<?php

namespace App\Console\Commands;

use App\Models\Column;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublishProjectLeadColumn extends Command
{
    protected $signature = 'content:publish-project-lead-column
                            {--user-id= : ID del autor}
                            {--force : Sobrescribe la columna si ya existe}';

    protected $description = 'Publica o actualiza la columna sobre IA y desarrollo de software firmada por Juan Pablo Basualdo';

    public function handle(): int
    {
        $author = $this->resolveAuthor();

        if (!$author) {
            $this->error('No encontré al autor. Usa --user-id=<id> o verifica que exista Juan Pablo Basualdo.');
            return self::FAILURE;
        }

        $slug = 'dirigir-proyectos-con-ia-no-es-trabajar-menos';
        $existing = Column::where('slug', $slug)->first();

        if ($existing && !$this->option('force')) {
            $this->warn("La columna ya existe con slug {$slug}. Usa --force para actualizarla.");
            return self::SUCCESS;
        }

        $content = <<<'HTML'
<p>Hace tiempo que vengo pensando algo cada vez que aparece una nueva herramienta de inteligencia artificial para desarrollo de software: la conversación casi siempre arranca en el lugar equivocado.</p>

<p>Muy rápido aparecen las mismas preguntas de siempre. Si la IA va a reemplazar programadores. Si los equipos van a ser más chicos. Si ahora todo se va a construir en una fracción del tiempo. Si el futuro del software va a quedar en manos de agentes que programan solos.</p>

<p>Entiendo por qué pasa. Son preguntas que generan impacto, ansiedad y titulares. Pero, sinceramente, desde mi lugar como jefe de proyecto, la sensación que tengo es otra. Yo no veo a la IA como una tecnología que nos libera de pensar. La veo como una tecnología que nos obliga a pensar mejor.</p>

<p>Y en ese sentido, creo que el cambio para quienes dirigimos proyectos puede ser incluso más profundo de lo que parece.</p>

<h2>La ejecución se acelera, pero el problema de fondo sigue ahí</h2>

<p>Lo primero que uno siente al trabajar bien con estas herramientas es bastante obvio: impresionan. Una idea se convierte en componente, una necesidad se convierte en endpoint, una mejora se convierte en propuesta concreta mucho más rápido que antes. Eso es real. Y sería ridículo negarlo.</p>

<p>Pero también hay una segunda verdad que aparece apenas baja un poco el entusiasmo inicial: la velocidad no resuelve por sí sola lo más difícil de un proyecto.</p>

<p>Porque, en mi experiencia, lo más difícil casi nunca fue solo construir. Lo más difícil es entender qué vale la pena construir, qué conviene postergar, qué decisión puede comprometer al sistema más adelante, qué apuro del negocio está bien leído y cuál todavía viene mal definido.</p>

<p>La IA ayuda muchísimo con la ejecución. Pero no puede cargar sola con la responsabilidad del criterio. Y ahí es donde, para mí, empieza la parte más importante de esta discusión.</p>

<h2>Hay proyectos que producen mucho y avanzan poco</h2>

<p>Esto lo aprendí hace tiempo, mucho antes de que explotara la IA. En software, producir cosas y avanzar de verdad no son lo mismo.</p>

<p>Uno puede llenar semanas enteras de tareas cerradas, pantallas nuevas, integraciones, automatizaciones y entregables. Y sin embargo no haber movido nada verdaderamente importante. También puede pasar lo contrario: tomar menos decisiones, hacer menos ruido, pero resolver justo lo que destraba al equipo o le da dirección real al producto.</p>

<p>Con IA, este riesgo se vuelve más evidente. Porque ahora producir va a ser cada vez más fácil. Y justamente por eso, dirigir va a exigir más disciplina. Si todo se puede hacer más rápido, entonces el verdadero desafío pasa a ser no perder dirección.</p>

<p>Cuando la capacidad de ejecución aumenta, también aumenta la tentación de hacer de más. Más funciones, más pruebas, más ideas, más prototipos, más caminos abiertos al mismo tiempo. Todo parece posible. Y ahí aparece un problema muy humano: confundir movimiento con avance.</p>

<h2>El rol del jefe de proyecto cambia, y bastante</h2>

<p>Yo creo que una de las cosas más interesantes que está dejando al descubierto la IA es cuál era el valor real de algunos roles, incluso antes de que lo nombráramos así.</p>

<p>Durante mucho tiempo, al jefe de proyecto se lo vio como alguien que coordina tareas, sigue fechas, ordena prioridades y alinea áreas. Todo eso sigue siendo parte del trabajo, claro. Pero hoy siento que aparece una capa mucho más visible: la de ordenar contexto y hacerse cargo de la responsabilidad de lo que el equipo pone en marcha.</p>

<p>Porque si un equipo puede ejecutar más rápido, entonces alguien tiene que cuidar con más claridad el marco de esa ejecución. Qué problema estamos resolviendo. Qué restricciones tenemos. Qué nivel de calidad necesitamos. Qué riesgo podemos aceptar. Qué decisión es reversible y cuál no.</p>

<p>La IA no elimina esa necesidad. La intensifica.</p>

<h2>Ya no alcanza con coordinar: también hay que filtrar</h2>

<p>Si tuviera que resumirlo de una manera simple, diría que dirigir proyectos con IA obliga a transformarse un poco en filtro.</p>

<p>Filtro de prioridades. Filtro de ideas. Filtro de urgencias. Filtro de soluciones que parecen buenas, pero no encajan con el momento del producto. Filtro de complejidad innecesaria. Filtro de entusiasmo mal administrado.</p>

<p>Y esa parte no siempre es cómoda. Porque cuando una herramienta te demuestra que algo efectivamente “se puede hacer”, hace falta bastante madurez para responder: sí, pero eso no significa que convenga hacerlo ahora, ni así, ni con este costo, ni con este impacto sobre el resto del sistema.</p>

<p>En ese sentido, la IA no reduce la necesidad de liderazgo. La expone más.</p>

<h2>El desarrollador fuerte no pierde valor</h2>

<p>Tampoco me convence demasiado esa idea de que todo esto nos lleva a un escenario donde el desarrollador pierde protagonismo. Lo que veo es otra cosa: cambia el tipo de valor que más va a pesar.</p>

<p>Va a importar todavía más la capacidad de leer contexto, revisar con criterio, detectar inconsistencias, defender una arquitectura sana, anticipar problemas y entender cuándo una solución aparente en realidad introduce una deuda peor.</p>

<p>Cuanto más fácil sea producir, más importante va a ser juzgar bien lo producido.</p>

<p>Y eso corre tanto para desarrollo como para liderazgo.</p>

<h2>Quizás el futuro no sea menos trabajo, sino trabajo más expuesto</h2>

<p>Hay una fantasía bastante instalada de que la IA va a simplificarlo todo. Yo no lo veo tan lineal. Sí creo que va a simplificar ciertas partes del trabajo. Pero también creo que va a dejar mucho más expuestas otras partes que antes se podían disimular.</p>

<p>Va a quedar más expuesto si una prioridad estaba mal pensada. Si una definición era ambigua. Si la conversación entre negocio y tecnología era floja. Si el equipo estaba construyendo sin suficiente claridad. Si se tomaban decisiones por inercia y no por criterio.</p>

<p>La IA no inventa esos problemas. Lo que hace es volverlos más visibles, porque ya no quedan tan tapados por el esfuerzo de ejecución.</p>

<h2>Hacia dónde creo que nos lleva esto</h2>

<p>Si me preguntan hacia dónde nos lleva la IA en el desarrollo de software, yo no respondería “hacia menos equipos” ni “hacia el fin de los programadores”. Mi respuesta sería otra: nos lleva a una etapa donde producir será más fácil, pero dirigir bien será más importante.</p>

<p>Nos lleva a equipos donde el contexto importa más. Donde las decisiones pesan más. Donde la calidad de la conversación entre producto, negocio y tecnología se vuelve todavía más crítica. Donde la responsabilidad no desaparece, sino que cambia de lugar.</p>

<p>Y, honestamente, eso no me parece una mala noticia. Me parece una oportunidad para trabajar mejor. Para sacar ruido. Para ordenar más. Para dejar de confundir velocidad con claridad.</p>

<p>Pero también me parece una advertencia. Si no fortalecemos criterio, liderazgo y capacidad de priorización, la IA no nos va a ordenar el desarrollo. Lo único que va a hacer es acelerar nuestro desorden.</p>

<p>Por eso, al menos desde mi lugar, no veo el futuro del software como un problema de reemplazo. Lo veo como un desafío de conducción.</p>

<p>Y, para mí, ahí está la conversación que realmente vale la pena dar.</p>

<p><strong>Lectura relacionada:</strong> esta columna dialoga con la investigación <a href="/investigacion/proxima-revolucion-cientifica-agentes-supervisados-humanos">La próxima revolución científica podría no venir de un modelo, sino de agentes supervisados por humanos</a>, donde se analiza cómo los sistemas multiagente empiezan a cambiar no solo la automatización, sino también la forma en que organizamos trabajo complejo y toma de decisiones.</p>
HTML;

        $payload = [
            'title' => 'Dirigir proyectos con IA no es trabajar menos: es hacerse cargo de cosas más importantes',
            'slug' => $slug,
            'content' => $content,
            'excerpt' => 'Como jefe de proyecto, no veo a la inteligencia artificial como una amenaza al desarrollo de software, sino como una presión directa para gestionar mejor el contexto, las prioridades y la responsabilidad de lo que construimos.',
            'author_id' => $author->id,
            'category_id' => null,
            'featured' => true,
            'published_at' => now(),
            'views' => 0,
        ];

        if ($existing) {
            $existing->update($payload);
            $column = $existing->fresh();
            $this->info("Columna actualizada: {$column->title}");
        } else {
            $column = Column::create($payload);
            $this->info("Columna publicada: {$column->title}");
        }

        $this->clearColumnCaches($column);

        $this->line("Slug: {$column->slug}");
        $this->line("Autor asignado: {$author->name} <{$author->email}>");

        return self::SUCCESS;
    }

    private function resolveAuthor(): ?User
    {
        $userId = $this->option('user-id');

        if ($userId) {
            return User::find($userId);
        }

        return User::query()
            ->whereRaw('LOWER(name) = ?', ['juan pablo basualdo'])
            ->first();
    }

    private function clearColumnCaches(Column $column): void
    {
        foreach ([
            'home_page_data',
            'latest_columns',
            'latest_columns_section_featured',
            'latest_columns_section',
            'columns_page_side_data',
            "column_{$column->slug}",
        ] as $key) {
            Cache::forget($key);
        }
    }
}
