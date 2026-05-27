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
<p>En junio de 2023, dos abogados presentaron ante la Corte Distrital de Nueva York un escrito repleto de citas jurisprudenciales. Los casos eran impecables: nombres de partes, números de expediente, extractos de sentencias. El problema es que ninguno de esos casos existía. Los había inventado ChatGPT, con la misma seguridad con que un modelo de lenguaje afirma cualquier cosa. Ambos abogados fueron multados con cinco mil dólares. El caso se llama <em>Mata v. Avianca</em> y se convirtió en el punto de inflexión que hizo visible un problema que ya venía creciendo en silencio.</p>

<p>Ese no fue el único. En Arizona, en 2024, una jueza descubrió que doce de los diecinueve casos citados en un escrito eran falsos o insostenibles. En Colorado, en 2025, dos abogados más fueron sancionados por exactamente la misma razón. Al día de hoy, hay más de doscientos casos documentados de alucinaciones de IA en tribunales estadounidenses. Y el problema no tiene fronteras.</p>

<h2>Por qué la IA inventa jurisprudencia</h2>

<p>Para entender el problema, hay que entender cómo funciona un modelo de lenguaje. ChatGPT, Claude o cualquier herramienta similar no consulta bases de datos legales en tiempo real. No tiene acceso a LexisNexis ni a Westlaw. Lo que hace es predecir, con altísima probabilidad estadística, cuál es la siguiente palabra más adecuada dado el contexto. Eso produce textos que <em>suenan</em> autoritarios, bien formateados, con la estructura correcta de una cita jurídica. Pero la autoridad del texto no implica que el contenido sea real.</p>

<p>Los investigadores de Stanford lo midieron: los modelos de IA generativos alucinan en al menos uno de cada seis casos cuando se trata de investigación legal. Y eso usando herramientas genéricas. Las especializadas —Lexis+ AI, Westlaw AI-Assisted Research— reducen el problema, pero no lo eliminan: siguen errando entre el 17 y el 33 por ciento de las veces.</p>

<p>El lenguaje jurídico es especialmente problemático. Tiene una estructura muy reconocible: partes, tribunal, año, ratio decidendi. Eso hace que el modelo sea muy bueno imitando el formato, pero el contenido puede ser completamente fabricado. La IA no distingue entre citar un caso real y generar uno que suena plausible dentro del patrón.</p>

<h2>Dos tipos de error</h2>

<p>Los investigadores identifican dos categorías. La primera son las alucinaciones incorrectas: el modelo describe la ley de forma directamente falsa. La segunda, más sutil y peligrosa, son las alucinaciones desvinculadas: cita correctamente la norma pero la atribuye a una fuente que no la respalda, o a un caso que no existe. Esta segunda es la que más daña, porque puede pasar el primer filtro de revisión si el abogado no verifica la fuente directamente.</p>

<p>Y ahí está el nudo del problema. La IA genera el texto con una confianza que no deja espacio visible para la duda. No dice "quizás" ni "no estoy seguro". Afirma. Y esa seguridad aparente invita a bajar la guardia exactamente donde más se necesita tenerla alta.</p>

<h2>El riesgo en Chile</h2>

<p>Chile no tiene casos documentados de sanciones por este motivo. Todavía. Pero los abogados chilenos usan ChatGPT y Claude. Y el riesgo es idéntico al de cualquier otro sistema judicial.</p>

<p>El país avanza en regulación: el proyecto de ley de sistemas de inteligencia artificial aprobado por la Cámara de Diputados en octubre de 2025 contempla cuatro niveles de riesgo, y los sistemas de IA aplicados en contextos judiciales quedan clasificados como alto riesgo, con requisitos de documentación, supervisión humana y evaluaciones previas. Eso es un paso importante. Pero la norma regula a quienes desarrollan o despliegan los sistemas, no al abogado que usa ChatGPT para hacer investigación legal en su computador.</p>

<p>La regulación llegará. La sanción, cuando llegue el primer caso, también. El problema es que el daño —la causa perdida, la reputación afectada, el cliente perjudicado— ocurre antes.</p>

<h2>Lo que se puede hacer hoy</h2>

<p>La respuesta no es prohibir la IA en el ejercicio legal. Es entender qué puede hacer y qué no puede hacer.</p>

<p>Primero, ninguna cita jurisprudencial generada por IA debería ir a un escrito sin verificación directa en la fuente original. Sin excepción. Tratar el output del modelo como lo que es: un punto de partida para investigar, no una investigación terminada.</p>

<p>Segundo, las herramientas especializadas —aunque imperfectas— ofrecen menor riesgo que las genéricas. Lexis+ AI y Westlaw AI-Assisted Research tienen tasas de error más bajas porque integran bases de datos legales verificadas. No eliminan el problema, pero lo reducen.</p>

<p>Tercero, las firmas y departamentos legales necesitan protocolos claros: qué herramientas se usan, para qué etapas del trabajo, con qué nivel de verificación obligatoria. Eso no es burocracia; es gestión de riesgo profesional.</p>

<p>Y cuarto —quizá lo más importante— entender que la responsabilidad profesional no se delega al modelo. El abogado firma el escrito. Si el caso citado no existe, la sanción recae sobre quien lo presentó, no sobre la herramienta que lo generó.</p>

<h2>El fondo del problema</h2>

<p>Los modelos de lenguaje son herramientas extraordinariamente útiles para muchas cosas. Resumir documentos extensos. Identificar patrones en contratos. Redactar borradores que luego un profesional ajusta. Organizar y estructurar información compleja. En esas tareas, el margen de error tiene consecuencias manejables.</p>

<p>Pero citar jurisprudencia no es una de esas tareas. Ahí el margen es cero. Un caso inventado no es un error menor en un resumen: es un argumento que no existe, presentado ante un juez, firmado por un profesional que pone su credencial en juego. El coste de ese error no lo asume la IA. Lo asume el cliente y el abogado.</p>

<p>La inteligencia artificial está llegando al derecho con fuerza y va a transformar cómo se hace la investigación legal, cómo se redactan contratos, cómo se procesan expedientes. Eso es inevitable y, en muchos sentidos, bienvenido. Pero la velocidad de adopción no puede superar la capacidad de verificación. En derecho, más que en cualquier otro campo, la diferencia entre lo que parece verdad y lo que es verdad tiene consecuencias reales para personas reales.</p>

<p>La IA que inventa jurisprudencia no lo hace con mala intención. Lo hace porque no tiene la capacidad de distinguir entre generar texto plausible y decir la verdad. Esa distinción es la que el abogado todavía tiene que hacer. Mientras eso no cambie, la supervisión humana no es opcional: es la única garantía que existe.</p>
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
