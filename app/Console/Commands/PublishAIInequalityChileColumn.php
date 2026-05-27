<?php

namespace App\Console\Commands;

use App\Models\Column;
use App\Models\User;
use App\Services\ColumnAudioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PublishAIInequalityChileColumn extends Command
{
    protected $signature = 'content:publish-ai-inequality-chile-column
                            {--user-id= : ID del autor/editor}
                            {--force : Sobrescribe la columna si ya existe}';

    protected $description = 'Publica la columna sobre IA y desigualdad en sectores vulnerables de Chile';

    public function handle(): int
    {
        $author = $this->resolveAuthor();

        if (!$author) {
            $this->error('No encontré un usuario editor. Usa --user-id=<id> o verifica editor@conocia.com.');
            return self::FAILURE;
        }

        $slug = 'ia-y-desigualdad-en-chile-la-nueva-brecha-que-nadie-esta-midiendo';
        $existing = Column::where('slug', $slug)->first();

        if ($existing && !$this->option('force')) {
            $this->warn("La columna ya existe con slug {$slug}. Usa --force para actualizarla.");
            return self::SUCCESS;
        }

        $content = <<<'HTML'
<p>Chile tiene uno de los índices de cobertura de internet más altos de América Latina. Según la undécima encuesta de SUBTEL, el 96,5% de los hogares estaba conectado en 2024, y el quintil más pobre del país —el que en 2011 apenas llegaba al 33,9% de cobertura— alcanzó el 88,8% en 2025.<sup><a href="#fn1">1</a></sup> Es un avance real, sostenido y documentado. Y es, también, la razón por la que la discusión sobre IA y desigualdad en Chile corre el riesgo de quedar mal planteada desde el principio: la brecha que importa ya no es si los sectores más vulnerables tienen conexión. Es si la inteligencia artificial va a funcionar para ellos, o va a funcionar contra ellos.</p>

<p>Solo el 45% de los chilenos utiliza herramientas de inteligencia artificial en su trabajo o lugar de estudio, según un estudio de la Universidad de los Andes publicado en 2025.<sup><a href="#fn2">2</a></sup> La encuesta muestra brechas por género —51% en hombres, 40% en mujeres— pero los datos por grupo socioeconómico son más esquivos: el uso de IA en contextos laborales y académicos está concentrado en quienes ya tienen acceso a educación superior y empleos de oficina. No es solo una inferencia. Es el perfil que emerge cuando se cruza el dato de que el 81% de los estudiantes de primer año de la Universidad de Chile declara usar IA regularmente,<sup><a href="#fn3">3</a></sup> con el hecho de que la tasa de acceso a la educación superior sigue siendo profundamente desigual por nivel de ingreso. La IA llega primero —y llega mejor— a quienes ya estaban mejor posicionados. Eso no es una anomalía. Es el patrón histórico de toda tecnología que no tiene política pública activa detrás.</p>

<p>El problema de fondo es estructural. Un estudio de la OCDE ubica a Chile en el quinto lugar entre 32 países con mayor proporción de empleos en riesgo de automatización: el 55% de los trabajadores chilenos realiza tareas con alto riesgo de ser desplazadas por máquinas o algoritmos.<sup><a href="#fn4">4</a></sup> Ese número no se distribuye de forma pareja en la estructura laboral. Los trabajos más expuestos —manufactura de línea, servicios administrativos repetitivos, comercio minorista, logística, ciertas labores agrícolas— son precisamente los que concentran a los trabajadores del segmento más bajo de ingresos. Y el mismo estudio de la OCDE señala algo que merece más atención de la que ha recibido en el debate chileno: Chile lidera entre los países de la organización en disparidad salarial según habilidades cognitivas. Los trabajadores con baja competencia numérica ganan significativamente menos, y la probabilidad de que sus empleadores los reentrenan es también significativamente más baja. La IA no crea esta desigualdad. La hereda, y la amplifica.</p>

<p>El Fondo Monetario Internacional publicó en 2025 un documento de trabajo que analiza la relación entre adopción de IA y desigualdad en distintas economías.<sup><a href="#fn5">5</a></sup> Sus conclusiones son incómodas: la IA tiende a ampliar la desigualdad de riqueza más que la de salarios, porque los beneficios de la automatización fluyen predominantemente hacia los propietarios del capital —las empresas, los accionistas, quienes pueden invertir en la tecnología— y no hacia los trabajadores desplazados. En economías con mercados laborales más rígidos o con menor capacidad estatal de redistribución, ese efecto es más pronunciado. Chile, con sus niveles históricos de concentración de ingresos, no está en una posición cómoda frente a ese escenario.</p>

<p>Frente a esto, el Estado chileno tiene programas. SENCE lanzó en 2024 el programa Talento Digital con 2.400 becas en formación tecnológica.<sup><a href="#fn6">6</a></sup> FOSIS financia microemprendimiento e innovación. ChileAtiende centraliza el acceso a la oferta pública. Pero ninguno de estos programas responde específicamente a la pregunta de cómo preparar a los trabajadores más vulnerables para un mercado laboral que ya está siendo reconfigurado por la inteligencia artificial. Talento Digital existe, pero sus cupos son insuficientes frente a la magnitud del problema. Los programas de capacitación laboral del SENCE están orientados mayoritariamente a quienes ya tienen Clave Única y navegación digital fluida —es decir, quienes ya cruzaron la primera brecha digital. La segunda brecha, la de la IA, no tiene todavía una política pública equivalente.</p>

<p>Lo que se está configurando en Chile no es la segregación tecnológica que imaginaban las distopías del siglo pasado —robots reemplazando a obreros de forma visible y dramática. Es algo más gradual y, por eso, más difícil de ver y de combatir. Es una economía en la que quienes pueden usar la IA como multiplicador de productividad —el abogado que procesa expedientes en minutos, el analista que automatiza reportes, el diseñador que produce en horas lo que antes tomaba días— acumulan ventajas que se traducen en ingresos, en empleabilidad, en capacidad de negociación. Y quienes no pueden acceder a esa capa de productividad —porque no tienen la formación, porque sus trabajos no lo permiten, porque nadie les ha enseñado a usar estas herramientas de forma efectiva— quedan expuestos a la presión del mercado sin las herramientas para responderle.</p>

<p>La segregación que viene no es binaria. No es "los que tienen internet" versus "los que no". Es más fina y más persistente: es la diferencia entre quienes pueden usar la IA para generar valor y quienes son desplazados por ella sin ninguna red de contención. Y esa diferencia, en Chile, se va a superponer casi exactamente sobre las líneas de clase que ya existen. El trabajador del comercio minorista que pierde su turno porque la empresa implementa autoatención inteligente no es el mismo que el ejecutivo cuya productividad se triplica con las mismas herramientas. Ambos están en el mismo país, bajo la misma ola tecnológica, pero en lados completamente distintos de ella.</p>

<p>Chile aprobó en octubre de 2025 un proyecto de ley que regula los sistemas de inteligencia artificial, clasificando como alto riesgo los usos en contextos laborales y de servicios esenciales.<sup><a href="#fn7">7</a></sup> Es una señal legislativa importante. Pero regular el riesgo de los sistemas ya desplegados no es lo mismo que tener una política activa de inclusión en la IA. La pregunta que el país todavía no ha respondido con claridad es quién se hace cargo de los trabajadores cuyos empleos se automatizan antes de que existan alternativas reales. La respuesta no puede ser solo conectividad —ya está casi resuelta— ni solo regulación. Requiere inversión masiva en reconversión laboral, acceso diferenciado a herramientas de IA para sectores vulnerables, y una discusión honesta sobre cómo distribuir los beneficios de una tecnología que, si no se interviene, va a concentrarlos donde ya está concentrada la riqueza.</p>

<p>La pregunta de si Chile va a tener una segregación por IA no es una pregunta del futuro. Es una pregunta del presente, y la respuesta que estamos construyendo —o no construyendo— ahora va a determinar si la brecha que se cierra en conectividad se reabre, más amplia, en capacidad productiva.</p>

<hr>

<p><strong>Fuentes</strong></p>
<ol class="columna-fuentes">
    <li id="fn1">SUBTEL. <em>Undécima Encuesta de Acceso y Uso de Internet</em>. Febrero 2025. Disponible en <a href="https://www.subtel.gob.cl/wp-content/uploads/2025/02/Informe-Final-Subtel-Acceso-y-Uso-Internet-2024.pdf" target="_blank" rel="noopener">subtel.gob.cl</a>.</li>
    <li id="fn2">Universidad de los Andes / ISCI. "Casi la mitad de los chilenos usa herramientas con IA en su trabajo o lugar de estudio." 2025. <a href="https://www.uandes.cl/noticias/casi-la-mitad-de-los-chilenos-usa-herramientas-con-ia-en-su-trabajo-o-lugar-de-estudio/" target="_blank" rel="noopener">uandes.cl</a>.</li>
    <li id="fn3">Universidad de Chile. "81% de estudiantes de primer año de la U. de Chile usa inteligencia artificial." 2024. <a href="https://uchile.cl/noticias/227983/81-de-estudiantes-de-primer-ano-uchile-usa-inteligencia-artificial" target="_blank" rel="noopener">uchile.cl</a>.</li>
    <li id="fn4">OCDE, citado en La Tercera. "Estudio OCDE ubica a Chile entre los países con más riesgos de automatización de empleos." <a href="https://www.latercera.com/pulso/estudio-ocde-ubica-chile-los-paises-mas-riesgos-automatizacion-empleos/" target="_blank" rel="noopener">latercera.com</a>. Ver también: <a href="https://g5noticias.cl/2025/07/16/mas-del-50-de-los-trabajadores-chilenos-realiza-tareas-con-alto-riesgo-de-automatizacion-chile-lidera-brecha-salarial-segun-habilidades-cognitivas-en-la-ocde/" target="_blank" rel="noopener">g5noticias.cl</a>.</li>
    <li id="fn5">Fondo Monetario Internacional. <em>AI Adoption and Inequality</em>. Working Paper WP/2025/068. Abril 2025. <a href="https://www.imf.org/en/publications/wp/issues/2025/04/04/ai-adoption-and-inequality-565729" target="_blank" rel="noopener">imf.org</a>.</li>
    <li id="fn6">SENCE. "Talento Digital 2024: 2.400 nuevas becas para fortalecer la formación tecnológica en Chile." <a href="https://sence.gob.cl/personas/noticias/talento-digital-2024-2400-nuevas-becas-para-fortalecer-la-formacion-tecnologica-en-chile" target="_blank" rel="noopener">sence.gob.cl</a>.</li>
    <li id="fn7">Proyecto de ley Boletín 16821-19, aprobado por la Cámara de Diputados el 13 de octubre de 2025 y enviado al Senado. <a href="https://www.biobiochile.cl/noticias/nacional/chile/2025/10/13/chile-avanza-hacia-la-primera-ley-de-inteligencia-artificial-ia-con-foco-en-ddhh-y-desarrollo-etico.html" target="_blank" rel="noopener">BioBioChile</a>.</li>
</ol>
HTML;

        $excerpt = 'El 96,5% de los hogares chilenos tiene internet. Pero solo el 45% de los chilenos usa IA, y el 55% de los trabajadores está en empleos con alto riesgo de automatización. La brecha que viene no es de conectividad: es de quién puede usar la IA como herramienta y quién será desplazado por ella.';

        $payload = [
            'title'       => 'IA y desigualdad en Chile: la nueva brecha que nadie está midiendo',
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

        $this->info('Generando audio…');
        $audioService = app(ColumnAudioService::class);
        $audioResult  = $audioService->generate($column->fresh());

        if ($audioResult === true) {
            $this->info('Audio generado y subido a R2.');
        } else {
            $this->warn("Audio no generado: {$audioResult}");
        }

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
