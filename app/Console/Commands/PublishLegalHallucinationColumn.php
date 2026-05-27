<?php

namespace App\Console\Commands;

use App\Models\Column;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PublishLegalHallucinationColumn extends Command
{
    protected $signature = 'content:publish-legal-hallucination-column
                            {--user-id= : ID del autor/editor}
                            {--force : Sobrescribe la columna si ya existe}';

    protected $description = 'Publica la columna sobre alucinaciones de IA en contextos legales';

    public function handle(): int
    {
        $author = $this->resolveAuthor();

        if (!$author) {
            $this->error('No encontré un usuario editor. Usa --user-id=<id> o verifica editor@conocia.com.');
            return self::FAILURE;
        }

        $slug = 'la-ia-que-inventa-leyes-el-peligro-silencioso-que-llega-a-los-tribunales';
        $existing = Column::where('slug', $slug)->first();

        if ($existing && !$this->option('force')) {
            $this->warn("La columna ya existe con slug {$slug}. Usa --force para actualizarla.");
            return self::SUCCESS;
        }

        $content = <<<'HTML'
<p>El 22 de junio de 2023, el juez Kevin Castel de la Corte Distrital del Distrito Sur de Nueva York sancionó a dos abogados con cinco mil dólares cada uno. Peter LoDuca y Steven Schwartz, del estudio Levidow, Levidow &amp; Oberman, habían presentado un escrito con seis casos jurisprudenciales en el caso <em>Mata v. Avianca</em>. Los casos tenían todo lo que se espera de una cita bien construida: nombres de partes, números de expediente, extractos de sentencias, referencias a tribunales superiores. Ninguno existía. Los había generado ChatGPT, y el abogado que los usó no los verificó antes de firmar el escrito.<sup><a href="#fn1">1</a></sup></p>

<p>Lo que hace notable a <em>Mata v. Avianca</em> no es que haya ocurrido, sino que era predecible. Los modelos de lenguaje no funcionan como motores de búsqueda. No consultan bases de datos en tiempo real ni tienen acceso a LexisNexis o Westlaw. Lo que hacen, con una precisión estadística extraordinaria, es predecir cuál es la siguiente palabra más probable dado el contexto. Cuando se les pide jurisprudencia, producen texto con la estructura exacta de una cita legal —partes, tribunal, año, ratio decidendi— porque esa estructura está abundantemente representada en sus datos de entrenamiento. El formato es impecable. El caso puede no existir.</p>

<p>Ese mecanismo produce dos tipos de error distintos. El primero es la alucinación incorrecta: el modelo describe la ley de forma directamente falsa. El segundo es más sutil y, en la práctica, más peligroso: la alucinación desvinculada, en la que la norma citada existe pero se atribuye a una fuente que no la respalda, o a un fallo que nunca fue dictado. Esta segunda variante puede pasar el primer filtro de revisión precisamente porque el marco jurídico invocado es real. Lo que no existe es la sentencia que supuestamente lo establece.</p>

<p>Un estudio publicado en el <em>Journal of Empirical Legal Studies</em> en 2025, basado en investigación de Stanford HAI, cuantificó el problema con precisión: los modelos generativos alucinan en al menos uno de cada seis casos en contextos de investigación legal.<sup><a href="#fn2">2</a></sup> Más revelador aún es lo que ocurre con las herramientas diseñadas específicamente para uso jurídico: Lexis+ AI registra una tasa de alucinación del 17%; Westlaw AI-Assisted Research, del 33%. Estas plataformas integran bases de datos legales verificadas y aun así fallan en proporciones que en cualquier otro campo profesional serían inaceptables. La diferencia respecto a los modelos genéricos es real, pero no convierte a ninguna herramienta en confiable por sí sola.</p>

<p><em>Mata v. Avianca</em> abrió una grieta que no se ha cerrado. En agosto de 2024, la jueza Alison Bachus del Distrito de Arizona sancionó a la abogada Maren Bam en el caso <em>Mavy v. Commissioner of Social Security Administration</em>: doce de los diecinueve casos citados en su escrito eran fabricados, engañosos o insostenibles. Le revocaron la admisión pro hac vice y le retiraron el escrito.<sup><a href="#fn3">3</a></sup> En abril de 2025, en Colorado, los abogados Christopher Kachouroff y Jennifer DeMaster fueron multados con tres mil dólares cada uno por aproximadamente treinta citas defectuosas en un escrito del caso <em>Coomer v. Lindell</em>.<sup><a href="#fn4">4</a></sup> Al momento de escribir esta columna, se contabilizan globalmente más de 1.400 casos documentados de alucinaciones de IA en procesos judiciales.<sup><a href="#fn5">5</a></sup></p>

<p>El patrón que emerge de estos casos no es el de abogados negligentes o deshonestos. Es el de profesionales que confiaron en una herramienta sin entender sus límites fundamentales. La IA genera texto con una seguridad que no admite matices: no dice "podría ser" ni "verifique esta referencia". Afirma. Y esa confianza aparente —esa ausencia de señales de duda— es exactamente lo que hace peligroso usarla en contextos donde la verificación no es opcional.</p>

<p>Chile no tiene casos públicamente documentados de sanciones por este motivo. Todavía. Pero los abogados chilenos usan ChatGPT y Claude, como los usan en cualquier otro país. Y el sistema judicial chileno no tiene ninguna propiedad especial que lo proteja de este tipo de error. Lo que sí tiene Chile es un proceso legislativo en curso: el proyecto de ley que regula los sistemas de inteligencia artificial fue aprobado por la Cámara de Diputados el 13 de octubre de 2025 y enviado al Senado.<sup><a href="#fn6">6</a></sup> El proyecto clasifica los sistemas de IA aplicados en contextos judiciales como alto riesgo, con exigencias de documentación, supervisión humana y evaluación previa. Es un avance real. Pero regula a quienes desarrollan y despliegan los sistemas, no al abogado que usa ChatGPT en su computador para preparar un escrito. Esa brecha no la cierra ninguna ley por sí sola.</p>

<p>La discusión sobre IA en el derecho tiende a polarizarse entre quienes ven en estas herramientas una amenaza a la profesión y quienes las abrazan sin reservas como una ganancia neta de productividad. Ambas posiciones pasan por alto algo más preciso: la IA es extraordinariamente útil para tareas donde el margen de error tiene consecuencias manejables —resumir contratos extensos, identificar cláusulas relevantes, organizar expedientes, redactar borradores iniciales— y profundamente inadecuada para tareas donde ese margen es cero. Citar jurisprudencia ante un tribunal es una de esas tareas. Un caso inventado no es un dato impreciso en un resumen interno: es un argumento que no existe, presentado bajo firma profesional, ante un juez que tomará decisiones con consecuencias reales para un cliente real.</p>

<p>La responsabilidad profesional no se delega al modelo. El abogado firma el escrito. Cuando el caso citado no existe, la sanción recae sobre quien lo presentó. Eso no cambia con ninguna actualización de ChatGPT ni con ninguna herramienta especializada, por más sofisticada que sea. Lo que cambia es la probabilidad del error, no la titularidad de la responsabilidad.</p>

<p>La inteligencia artificial va a transformar el ejercicio legal. Eso ya está ocurriendo y en muchos aspectos es bienvenido. Pero la velocidad de adopción no puede superar la capacidad de verificación. En el derecho, más que en casi cualquier otro campo, la distancia entre lo que parece verdad y lo que es verdad tiene consecuencias que no se deshacen con una corrección posterior. Mientras los modelos de lenguaje no puedan distinguir entre generar texto plausible y decir la verdad —y hoy no pueden— esa distinción es la que el abogado tiene que seguir haciendo. No como un límite de la tecnología que eventualmente se resolverá, sino como una condición estructural del trabajo profesional que no desaparece porque la herramienta sea más convincente.</p>

<hr>

<p><strong>Fuentes</strong></p>
<ol class="columna-fuentes">
    <li id="fn1">Mata v. Avianca, Inc., No. 22-cv-1461 (S.D.N.Y. jun. 22, 2023). Orden de sanción disponible en <a href="https://law.justia.com/cases/federal/district-courts/new-york/nysdce/1:2022cv01461/575368/54/" target="_blank" rel="noopener">Justia</a>. Ver también <a href="https://en.wikipedia.org/wiki/Mata_v._Avianca,_Inc." target="_blank" rel="noopener">Wikipedia: Mata v. Avianca</a>.</li>
    <li id="fn2">Magesh, A. et al. (2024). "Hallucination-Free? Assessing the Reliability of Leading AI Legal Research Tools". <em>Journal of Empirical Legal Studies</em>, 2025. Resumen en <a href="https://hai.stanford.edu/news/ai-trial-legal-models-hallucinate-1-out-6-or-more-benchmarking-queries" target="_blank" rel="noopener">Stanford HAI</a>. Artículo completo en <a href="https://onlinelibrary.wiley.com/doi/full/10.1111/jels.12413" target="_blank" rel="noopener">Wiley Online Library</a>.</li>
    <li id="fn3">Mavy v. Commissioner of Social Security Administration, No. 2:24-cv-03617 (D. Ariz. ago. 2024). Orden disponible en <a href="https://cases.justia.com/federal/district-courts/arizona/azdce/2:2024cv03617/1418413/22/0.pdf" target="_blank" rel="noopener">Justia</a>.</li>
    <li id="fn4">Coomer v. Lindell (D. Colo. abr. 2025). Cobertura en <a href="https://natlawreview.com/article/court-slams-lawyers-ai-generated-fake-citations" target="_blank" rel="noopener">National Law Review</a>.</li>
    <li id="fn5">Registro actualizado de casos documentados en <a href="https://www.damiencharlotin.com/hallucinations/" target="_blank" rel="noopener">damiencharlotin.com/hallucinations</a>.</li>
    <li id="fn6">Proyecto de ley Boletín 16821-19, aprobado por la Cámara de Diputados el 13 de octubre de 2025. Cobertura en <a href="https://www.biobiochile.cl/noticias/nacional/chile/2025/10/13/chile-avanza-hacia-la-primera-ley-de-inteligencia-artificial-ia-con-foco-en-ddhh-y-desarrollo-etico.html" target="_blank" rel="noopener">BioBioChile</a>.</li>
</ol>
HTML;

        $excerpt = 'En 2023, dos abogados fueron multados en Nueva York por presentar casos judiciales inventados por ChatGPT. No fue un caso aislado: hay más de 200 incidentes documentados en tribunales de EE.UU. ¿Qué está pasando, por qué ocurre y cuál es el riesgo para los abogados chilenos?';

        $payload = [
            'title'       => 'La IA que inventa leyes: el peligro silencioso que llega a los tribunales',
            'slug'        => $slug,
            'content'     => $content,
            'excerpt'     => $excerpt,
            'author_id'   => $author->id,
            'category_id' => null,
            'featured'    => true,
            'published_at'=> now(),
            'views'       => 0,
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
        $this->line("URL:  /columnas/{$column->slug}");
        $this->line("Autor: {$author->name} <{$author->email}>");
        $this->line('Destacada: sí');

        return self::SUCCESS;
    }

    private function resolveAuthor(): ?User
    {
        $userId = $this->option('user-id');

        if ($userId) {
            return User::find($userId);
        }

        return User::query()
            ->where('email', 'editor@conocia.com')
            ->first()
            ?? User::query()->with('role')->get()->first(fn (User $user) => $user->role?->slug === 'editor')
            ?? User::orderBy('id')->first();
    }

    private function clearColumnCaches(Column $column): void
    {
        foreach ([
            'home_page_data_v2',
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
