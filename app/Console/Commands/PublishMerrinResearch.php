<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Research;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PublishMerrinResearch extends Command
{
    protected $signature = 'content:publish-merrin-research
                            {--user-id= : ID del usuario autor}
                            {--force : Sobrescribe el contenido si ya existe}';

    protected $description = 'Publica o actualiza la investigación editorial basada en el paper MERRIN';

    public function handle(): int
    {
        $slug = 'agentes-ia-fallan-web-real-benchmark-merrin';
        $existing = Research::where('slug', $slug)->first();

        if ($existing && !$this->option('force')) {
            $this->warn("La investigación ya existe con slug {$slug}. Usa --force para actualizarla.");
            return self::SUCCESS;
        }

        $author = $this->resolveAuthor();

        if (!$author) {
            $this->error('No encontré un usuario autor. Usa --user-id=<id> o crea un usuario primero.');
            return self::FAILURE;
        }

        $category = Category::firstOrCreate(
            ['slug' => 'investigacion'],
            [
                'name' => 'Investigación',
                'description' => 'Investigaciones, estudios y papers clave sobre IA.',
                'is_active' => true,
            ]
        );

        $summary = 'Un nuevo benchmark académico muestra que los agentes de IA siguen lejos de navegar, filtrar y razonar bien en entornos web ruidosos y multimodales. Incluso los sistemas más avanzados gastan más pasos y herramientas que los humanos, pero consiguen menos precisión.';

        $content = <<<'HTML'
<p>La narrativa dominante alrededor de los agentes de IA sugiere que estamos entrando en una etapa en la que los modelos ya no solo responden preguntas, sino que también pueden buscar información, contrastarla, navegar herramientas y resolver tareas complejas casi como un analista digital. Pero un nuevo paper presentado en arXiv pone bastante paños fríos sobre esa expectativa.</p>

<p>El trabajo, titulado <strong>MERRIN: A Benchmark for Multimodal Evidence Retrieval and Reasoning in Noisy Web Environments</strong>, propone una idea incómoda pero muy necesaria: evaluar a los agentes no en entornos limpios, artificiales y excesivamente guiados, sino en la web real, donde los resultados son ambiguos, multimodales, a veces contradictorios y, muchas veces, directamente ruidosos.</p>

<h2>Qué intenta medir MERRIN</h2>

<p>MERRIN fue diseñado para evaluar agentes con búsqueda web aumentada. No se enfoca solo en si un modelo “encuentra una respuesta”, sino en algo mucho más difícil: si es capaz de identificar qué tipo de evidencia necesita, recuperar información relevante desde distintas modalidades y luego razonar correctamente sobre ella.</p>

<p>Ese punto es clave. Muchas evaluaciones anteriores se concentran en texto o en tareas donde la modalidad relevante ya está implícita. MERRIN eleva la exigencia en tres dimensiones:</p>

<ul>
  <li>usa consultas en lenguaje natural sin pistas explícitas sobre la modalidad necesaria,</li>
  <li>incorpora modalidades menos exploradas, como audio y video,</li>
  <li>obliga a razonar sobre evidencia compleja, incompleta o incluso conflictiva durante la búsqueda.</li>
</ul>

<p>En otras palabras, no evalúa a un chatbot encerrado en su contexto, sino a un agente que debe salir al mundo, buscar, discriminar y pensar.</p>

<h2>El resultado más importante: los agentes todavía rinden mal</h2>

<p>El hallazgo central del paper es directo: <strong>el benchmark es muy difícil incluso para agentes avanzados</strong>. Según los autores, la precisión promedio entre todos los agentes evaluados fue de <strong>22,3%</strong>, y el mejor sistema apenas alcanzó <strong>40,1%</strong>.</p>

<p>Eso importa por dos razones. Primero, porque muestra que el salto desde “responder bien benchmarks” hasta “resolver tareas abiertas en la web” sigue siendo enorme. Segundo, porque expone una debilidad menos visible en la discusión pública: los agentes no fracasan solo por falta de conocimiento, sino por mala selección de fuentes, exceso de pasos y una tendencia a dejarse arrastrar por evidencia parcialmente relevante.</p>

<h2>Más herramientas no significa mejor razonamiento</h2>

<p>Uno de los puntos más interesantes del paper es que los agentes más potentes no necesariamente fallan por hacer poco, sino por hacer demasiado. Los autores observan que sistemas más fuertes, como los de tipo deep research, pueden obtener mejores resultados, pero con mejoras modestas y a costa de una exploración más extensa.</p>

<p>El problema es que esa sobreexploración no siempre agrega claridad. En contextos web reales, más pasos pueden significar más distracción. Cuantas más páginas, fuentes, formatos y señales parciales aparecen, más fácil es desviarse hacia pistas secundarias o contradictorias.</p>

<p>Eso transforma un problema clásico de recuperación de información en un problema mucho más profundo de <strong>criterio</strong>: no basta con acceder a muchos datos; hay que saber cuáles merecen atención, cuáles pueden ignorarse y cómo integrarlos sin romper la cadena de razonamiento.</p>

<h2>El sesgo silencioso hacia el texto</h2>

<p>Otro hallazgo fuerte es que los agentes tienden a depender demasiado del texto, incluso en tareas donde la evidencia relevante puede estar en video, audio u otras modalidades. Esta sobredependencia revela una limitación estructural del estado actual de muchos sistemas “multimodales”: pueden procesar varios tipos de input, sí, pero no necesariamente deciden bien cuándo una modalidad no textual es la clave del problema.</p>

<p>Y ese detalle cambia mucho la interpretación del progreso reciente en IA. Una cosa es que un modelo pueda “ver” imágenes o “escuchar” audio. Otra muy distinta es que sepa, en un entorno abierto, cuándo debe cambiar de modalidad para responder mejor. MERRIN sugiere que esa capacidad todavía está lejos de consolidarse.</p>

<h2>Lo más incómodo: los humanos siguen siendo más eficientes</h2>

<p>Quizás el mensaje más duro del paper no está solo en la baja precisión, sino en la comparación con humanos. Los autores sostienen que los agentes consumen más recursos y aun así logran menor exactitud, en buena parte por selección ineficiente de fuentes y por un uso pobre de la evidencia disponible.</p>

<p>Esto no significa que los agentes no sirvan. Significa algo más matizado: <strong>todavía no son confiables como investigadores autónomos en entornos web complejos</strong>. Pueden ayudar, acelerar tareas, resumir, explorar caminos iniciales y ampliar cobertura. Pero cuando la búsqueda exige criterio multimodal, filtrado fino y razonamiento robusto ante ruido, el margen de error sigue siendo demasiado alto.</p>

<h2>Por qué este paper importa ahora</h2>

<p>MERRIN llega en un momento especialmente relevante. La industria está empujando con fuerza la idea de agentes capaces de hacer research, operar interfaces, consultar fuentes y ejecutar tareas con mínima intervención humana. En ese contexto, benchmarks como este cumplen una función esencial: separar demos impresionantes de capacidades realmente estables.</p>

<p>El paper no dice que los agentes estén estancados. Dice algo más útil: que el próximo cuello de botella ya no es solo “más modelo”, sino <strong>mejor coordinación entre búsqueda, selección de evidencia y razonamiento multimodal</strong>.</p>

<p>Si ese diagnóstico es correcto, la próxima fase de progreso en agentes no dependerá únicamente de modelos más grandes o más rápidos, sino de sistemas que aprendan a explorar menos, seleccionar mejor y razonar con mayor disciplina en contextos abiertos.</p>

<h2>La lectura de fondo</h2>

<p>Durante meses, buena parte de la conversación sobre agentes de IA se apoyó en una intuición optimista: si los modelos ya son buenos respondiendo preguntas, pronto también serán buenos investigando por su cuenta. MERRIN muestra que esa transición no es automática.</p>

<p>Buscar no es entender. Recuperar no es discriminar. Y combinar modalidades en un entorno ruidoso no es un detalle técnico: es uno de los problemas centrales que todavía separan a los agentes prometedores de los agentes realmente confiables.</p>

<p>Ese es, probablemente, el valor más importante del paper: no exagera ni minimiza el progreso. Simplemente nos recuerda que la inteligencia útil en el mundo real sigue necesitando algo que los agentes aún no dominan del todo: buen juicio.</p>

<p><strong>Fuente:</strong> Han Wang et al., <em>MERRIN: A Benchmark for Multimodal Evidence Retrieval and Reasoning in Noisy Web Environments</em>, arXiv, 15 de abril de 2026. Disponible en <a href="https://arxiv.org/abs/2604.13418" target="_blank" rel="noopener noreferrer">arxiv.org/abs/2604.13418</a>.</p>
HTML;

        $payload = [
            'title' => 'Los agentes de IA todavía fallan en la web real: un nuevo benchmark expone sus límites',
            'slug' => $slug,
            'summary' => $summary,
            'excerpt' => $summary,
            'abstract' => $summary,
            'content' => $content,
            'research_type' => 'study',
            'type' => 'Paper Analysis',
            'status' => 'published',
            'is_published' => true,
            'published_at' => now(),
            'category_id' => $category->id,
            'user_id' => $author->id,
            'featured' => true,
            'views' => 0,
            'comments_count' => 0,
            'citations' => 0,
            'institution' => 'arXiv / investigación académica',
            'references' => 'https://arxiv.org/abs/2604.13418',
            'additional_authors' => 'Han Wang, David Wan, Hyunji Lee, Thinh Pham, Mikaela Cankosyan, Weiyuan Chen, Elias Stengel-Eskin, Tu Vu, Mohit Bansal',
        ];

        if ($existing) {
            $existing->update($payload);
            $research = $existing->fresh();
            $this->info("Investigación actualizada: {$research->title}");
        } else {
            $research = Research::create($payload);
            $this->info("Investigación publicada: {$research->title}");
        }

        $tagIds = collect([
            'IA',
            'Agentes',
            'Benchmark',
            'Multimodal',
            'Razonamiento',
            'Investigación',
        ])->map(function (string $name) {
            return Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            )->id;
        })->all();

        if (method_exists($research, 'tags')) {
            $research->tags()->sync($tagIds);
        }

        $this->line("Slug: {$research->slug}");
        $this->line("Categoría: {$category->name}");
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
            ->with('role')
            ->get()
            ->first(fn (User $user) => $user->isAdmin()) ?? User::query()->first();
    }
}
