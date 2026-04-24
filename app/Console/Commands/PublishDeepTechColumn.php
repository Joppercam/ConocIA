<?php

namespace App\Console\Commands;

use App\Models\Column;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PublishDeepTechColumn extends Command
{
    protected $signature = 'content:publish-deep-tech-column
                            {--user-id= : ID del autor/editor}
                            {--force : Sobrescribe la columna si ya existe}';

    protected $description = 'Publica o actualiza una columna tecnológica profunda firmada por el editor';

    public function handle(): int
    {
        $author = $this->resolveAuthor();

        if (!$author) {
            $this->error('No encontré un usuario editor. Usa --user-id=<id> o verifica editor@conocia.com.');
            return self::FAILURE;
        }

        $slug = 'la-nueva-complejidad-tecnologica-no-esta-en-construir-sistemas-sino-en-gobernarlos';
        $existing = Column::where('slug', $slug)->first();

        if ($existing && !$this->option('force')) {
            $this->warn("La columna ya existe con slug {$slug}. Usa --force para actualizarla.");
            return self::SUCCESS;
        }

        $content = <<<'HTML'
<p>Durante mucho tiempo, una parte importante del prestigio en tecnología estuvo asociada a una idea bastante clara: construir sistemas complejos y hacerlos escalar. Resolver infraestructura, tolerancia a fallos, performance, bases de datos distribuidas, observabilidad, despliegues, colas, arquitectura modular. En ese mundo, la dificultad era visible. Había límites técnicos concretos, cuellos de botella reconocibles y una sensación compartida de que lo más difícil era, efectivamente, construir.</p>

<p>Pero da la impresión de que estamos entrando en una etapa distinta. No porque esa complejidad haya desaparecido, sino porque empieza a convivir con otra más sutil y, en muchos casos, más difícil de administrar. La nueva pregunta ya no es solo cómo construir sistemas potentes. La pregunta empieza a ser <strong>cómo gobernarlos</strong>.</p>

<h2>La tecnología se volvió más accesible, pero no necesariamente más legible</h2>

<p>Una de las grandes paradojas de esta etapa es que construir se está volviendo más accesible. La nube redujo barreras de infraestructura, las plataformas simplificaron operaciones que antes eran especializadas, los servicios administrados abstrajeron capas enteras de complejidad, y la inteligencia artificial está empezando a reducir todavía más el costo de producción técnica.</p>

<p>Eso es una buena noticia. Permite que equipos más pequeños hagan más cosas, más rápido, con menos fricción. Pero también produce un efecto secundario que no siempre se discute lo suficiente: a medida que la capacidad de construir aumenta, también aumenta la posibilidad de crear sistemas que nadie entiende del todo.</p>

<p>Y ese es un problema serio.</p>

<p>Porque una cosa es tener una arquitectura poderosa. Otra muy distinta es tener una arquitectura que el equipo realmente pueda comprender, sostener, corregir y gobernar en el tiempo.</p>

<h2>Los sistemas modernos ya no fallan solo por limitaciones técnicas</h2>

<p>En etapas anteriores, muchos sistemas fallaban por problemas bastante directos: infraestructura insuficiente, diseños poco escalables, malas decisiones de almacenamiento, latencias, saturación de recursos, falta de redundancia. Todo eso sigue existiendo. Pero cada vez más aparecen fallas de otro tipo.</p>

<p>Fallas de entendimiento. Fallas de coordinación. Fallas de trazabilidad. Fallas donde nadie sabe exactamente por qué una parte del sistema terminó comportándose de determinada manera. Fallas donde el problema no es que falte capacidad técnica, sino que sobran capas, dependencias, automatizaciones y decisiones heredadas que nadie termina de mapear del todo.</p>

<p>En otras palabras, estamos entrando en una etapa donde muchos sistemas no son difíciles solo porque son grandes. Son difíciles porque son <strong>opacos</strong>.</p>

<h2>Automatizar no siempre ordena</h2>

<p>La automatización fue, con razón, una de las grandes promesas de la ingeniería moderna. Automatizar despliegues, tests, observabilidad, infraestructura, integraciones, seguridad, operaciones repetitivas. Todo eso genera valor real. Reduce errores manuales y mejora consistencia.</p>

<p>Pero automatizar no necesariamente ordena un sistema. A veces simplemente acelera su complejidad.</p>

<p>Un pipeline puede ser impecable y al mismo tiempo incomprensible. Un sistema puede estar lleno de buenas prácticas y seguir siendo inmanejable para un equipo que perdió visibilidad de sus dependencias. Una organización puede tener más tooling, más dashboards y más flujos automáticos, pero menos claridad real sobre cómo se comporta su stack bajo presión.</p>

<p>Esa es una de las tensiones más interesantes del momento actual: cuantas más capas agregamos para administrar complejidad, más riesgo hay de producir una complejidad nueva.</p>

<h2>La gobernanza tecnológica pasa a primer plano</h2>

<p>Durante bastante tiempo, la palabra “gobernanza” sonó más a compliance, gestión corporativa o burocracia que a ingeniería. Sin embargo, cada vez parece más claro que la tecnología necesita una conversación más seria sobre gobernanza.</p>

<p>No como freno. No como ritual de control vacío. Sino como capacidad real de entender qué estamos operando, cómo se toman decisiones, dónde están los riesgos, qué partes del sistema son críticas, qué automatizaciones son auditables y qué dependencias ya dejaron de estar bajo control real del equipo.</p>

<p>Gobernar tecnología no es simplemente documentarla. Es poder responder preguntas básicas con suficiente claridad: qué hace este sistema, por qué está diseñado así, qué puede romperse, qué supuestos lo sostienen y quién puede intervenir con criterio cuando algo sale mal.</p>

<p>Ese tipo de claridad, paradójicamente, empieza a volverse más escasa justo cuando la potencia técnica disponible es mayor.</p>

<h2>La IA intensifica este problema</h2>

<p>La inteligencia artificial no inventa esta tendencia, pero sí la acelera. Si producir software, automatizaciones, flujos y componentes se vuelve más rápido, entonces también se vuelve más fácil aumentar volumen, superficie y dependencia sin el mismo nivel de comprensión estructural.</p>

<p>La IA puede ayudarnos a construir más. Puede ayudarnos a documentar, sugerir, integrar, detectar patrones y reducir tiempos. Pero también puede empujar a una situación peligrosa si no hay suficiente criterio: sistemas creciendo más rápido que la capacidad del equipo para gobernarlos.</p>

<p>Y ahí aparece una tensión central del futuro tecnológico. La productividad ya no puede medirse solo por cuánto somos capaces de hacer. También tiene que medirse por cuánto somos capaces de entender y sostener de manera responsable.</p>

<h2>La sofisticación real no es sumar capas, sino conservar claridad</h2>

<p>Hay una idea que me parece especialmente importante en esta etapa: la sofisticación tecnológica real no consiste únicamente en sumar herramientas, automatizaciones y componentes. La verdadera sofisticación está en construir sistemas potentes sin perder claridad sobre ellos.</p>

<p>Eso requiere disciplina. Requiere liderazgo técnico. Requiere equipos capaces de decir que no a complejidades innecesarias. Requiere criterio arquitectónico. Y requiere algo que a veces parece menos glamoroso que lanzar una nueva solución: mantener legibilidad operativa.</p>

<p>En el fondo, la pregunta deja de ser si podemos construir algo. La pregunta más difícil pasa a ser si vamos a poder vivir con eso después.</p>

<h2>El nuevo prestigio tecnológico quizá esté en hacer sistemas gobernables</h2>

<p>Tal vez una de las señales más interesantes de madurez tecnológica en los próximos años no sea quién construye más rápido ni quién lanza más funcionalidades. Tal vez el verdadero diferencial empiece a estar en quién logra construir sistemas más gobernables.</p>

<p>Sistemas que escalen sin volverse inentendibles. Sistemas que automaticen sin borrar responsabilidad. Sistemas que integren inteligencia sin sacrificar trazabilidad. Sistemas donde el conocimiento no quede atrapado en unas pocas personas, ni en herramientas que nadie sabe interpretar del todo.</p>

<p>Ese tipo de diseño probablemente será menos vistoso hacia afuera, pero mucho más importante hacia adentro.</p>

<p>Porque cuando la complejidad tecnológica deja de ser solo un problema de construcción y pasa a ser un problema de gobierno, la ingeniería ya no puede medirse únicamente por potencia. Tiene que medirse también por lucidez.</p>

<p>Y quizás ahí esté una de las conversaciones más profundas que la tecnología necesita tener en esta década.</p>
HTML;

        $payload = [
            'title' => 'La nueva complejidad tecnológica no está en construir sistemas, sino en gobernarlos',
            'slug' => $slug,
            'content' => $content,
            'excerpt' => 'Durante años, el desafío de la tecnología fue escalar infraestructura y construir sistemas robustos. Hoy el problema empieza a cambiar: con herramientas más poderosas, automatización e IA, el reto ya no es solo crear sistemas complejos, sino mantener control, trazabilidad y criterio sobre ellos.',
            'author_id' => $author->id,
            'category_id' => null,
            'featured' => false,
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
        $this->line('Destacada: no');

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
            ?? User::query()->with('role')->get()->first(fn (User $user) => $user->role?->slug === 'editor');
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
