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
        $exists = DB::table('conocia_papers')->where('arxiv_id', '2510.02823')->exists();

        $payload = [
            'arxiv_url' => 'https://arxiv.org/abs/2510.02823',
            'original_title' => 'The Curious Case of In-Training Compression of State Space Models',
            'original_abstract' => 'State Space Models (SSMs), developed to tackle long sequence modeling tasks efficiently, offer both parallelizable training and fast inference. At their core are recurrent dynamical systems that maintain a hidden state, with update costs scaling with the state dimension. A key design challenge is striking the right balance between maximizing expressivity and limiting this computational burden. Control theory, and more specifically Hankel singular value analysis, provides a potent framework for the measure of energy for each state, as well as the balanced truncation of the original system down to a smaller representation with performance guarantees. Leveraging the eigenvalue stability properties of Hankel matrices, we apply this lens to SSMs during training, where only dimensions of high influence are identified and preserved. Our approach, CompreSSM, applies to Linear Time-Invariant SSMs such as Linear Recurrent Units, but is also extendable to selective models. Experiments show that in-training reduction significantly accelerates optimization while preserving expressivity, with compressed models retaining task-critical structure lost by models trained directly at smaller dimension.',
            'authors' => $this->json([
                'Makram Chahine',
                'Philipp Nazari',
                'Daniela Rus',
                'T. Konstantin Rusch',
            ]),
            'arxiv_published_date' => '2025-10-03',
            'arxiv_category' => 'cs.LG',
            'title' => 'CompreSSM: cuando un modelo de IA aprende a volverse mas liviano mientras entrena',
            'slug' => 'modelos-ia-comprimen-durante-entrenamiento-compressm',
            'excerpt' => 'Investigadores de MIT CSAIL y colaboradores presentan CompreSSM, una tecnica que comprime modelos de espacio de estados durante el entrenamiento usando herramientas de teoria de control. El resultado apunta a modelos mas eficientes sin pagar primero todo el costo de entrenar una version grande completa.',
            'content' => $this->content(),
            'key_contributions' => $this->json([
                'Propone una forma de compresion durante el entrenamiento para modelos de espacio de estados.',
                'Conecta teoria de control con optimizacion moderna de modelos de IA.',
                'Muestra que la importancia relativa de ciertos estados internos puede estabilizarse temprano.',
                'Ofrece evidencia experimental de mejoras de eficiencia con perdida controlada de rendimiento.',
                'Abre una ruta para investigar compresion temprana en arquitecturas relacionadas con Mamba y modelos de secuencia.',
            ]),
            'practical_implications' => $this->json([
                'Puede reducir el costo de experimentar con modelos de secuencias largas.',
                'Ayuda a pensar la eficiencia como parte del entrenamiento, no solo como una optimizacion posterior.',
                'Es relevante para equipos universitarios, startups y ecosistemas con acceso limitado a grandes clusters.',
                'Apunta a modelos mas sostenibles en infraestructura, energia y tiempo de entrenamiento.',
            ]),
            'difficulty_level' => 'avanzado',
            'image' => null,
            'featured' => true,
            'status' => 'published',
            'views' => 0,
            'reading_time' => 6,
            'published_at' => $now,
            'updated_at' => $now,
        ];

        if (!$exists) {
            $payload['arxiv_id'] = '2510.02823';
            $payload['created_at'] = $now;
        }

        DB::table('conocia_papers')->updateOrInsert(['arxiv_id' => '2510.02823'], $payload);

        $this->clearCaches();
    }

    public function down(): void
    {
        DB::table('conocia_papers')->where('arxiv_id', '2510.02823')->delete();

        $this->clearCaches();
    }

    private function content(): string
    {
        return <<<'HTML'
<h2>La eficiencia ya no tiene que esperar al final</h2>
<p>Una parte importante del costo de la inteligencia artificial moderna aparece antes de que el modelo llegue a produccion: durante el entrenamiento. Tradicionalmente, si un equipo quiere un modelo mas pequeno, debe entrenar uno grande y luego comprimirlo, podarlo o destilarlo. El problema es que ese camino sigue pagando gran parte del costo inicial.</p>
<p>El paper <em>The Curious Case of In-Training Compression of State Space Models</em>, desarrollado por investigadores de MIT CSAIL y colaboradores, propone una alternativa: comprimir ciertos modelos mientras aprenden. La tecnica se llama CompreSSM y se enfoca en modelos de espacio de estados, una familia relevante para tareas de secuencias largas y arquitecturas asociadas a Mamba.</p>

<h2>Enlaces originales</h2>
<ul>
<li><a href="https://arxiv.org/abs/2510.02823" target="_blank" rel="noopener noreferrer">Paper en arXiv</a></li>
<li><a href="https://openreview.net/forum?id=LtzmeSMBTW" target="_blank" rel="noopener noreferrer">Ficha del paper en OpenReview / ICLR 2026</a></li>
<li><a href="https://iclr.cc/virtual/2026/poster/10009997" target="_blank" rel="noopener noreferrer">Poster oficial de ICLR 2026</a></li>
<li><a href="https://news.mit.edu/2026/new-technique-makes-ai-models-leaner-faster-while-still-learning-0409" target="_blank" rel="noopener noreferrer">Nota de MIT News</a></li>
<li><a href="https://github.com/camail-official/compressm" target="_blank" rel="noopener noreferrer">Codigo del proyecto en GitHub</a></li>
</ul>

<h2>La idea central</h2>
<p>Los modelos de espacio de estados mantienen una representacion interna que evoluciona a medida que procesan una secuencia. Esa dimension interna influye directamente en el costo computacional. Si se reduce demasiado, el modelo pierde capacidad; si se mantiene grande, el entrenamiento se vuelve mas caro.</p>
<p>CompreSSM usa herramientas de teoria de control para estimar que partes de esa representacion interna realmente aportan al comportamiento del modelo. La clave es que, segun los autores, la importancia relativa de esas dimensiones puede estabilizarse temprano. Eso permite eliminar componentes menos utiles durante el entrenamiento y continuar con una version mas compacta.</p>

<h2>Que encontraron</h2>
<p>La nota de MIT reporta que, en benchmarks de clasificacion de imagenes, los modelos comprimidos mantuvieron una precision cercana a sus versiones completas y entrenaron hasta 1,5 veces mas rapido. En una configuracion asociada a Mamba, se observaron aceleraciones cercanas a 4x al reducir fuertemente la dimension del modelo.</p>
<p>El punto mas interesante no es solo la mejora de velocidad, sino el mecanismo: los modelos que comienzan grandes y se reducen durante el entrenamiento parecen conservar estructuras relevantes que un modelo pequeno entrenado desde cero no logra aprender con la misma calidad.</p>

<h2>Por que esto importa</h2>
<p>Si esta linea de trabajo escala a mas arquitecturas, podria ayudar a reducir una barrera central de la IA: el costo de experimentar. Menos costo de entrenamiento significa mas capacidad para que laboratorios universitarios, startups y equipos fuera de las grandes tecnologicas prueben modelos propios o adapten arquitecturas a problemas especificos.</p>
<p>Para America Latina, esto es especialmente relevante. El acceso a computo avanzado sigue siendo desigual, y las tecnicas que hacen mas eficiente el entrenamiento pueden ampliar quien participa en la investigacion y desarrollo de IA.</p>

<h2>Limites de la investigacion</h2>
<p>CompreSSM no es una receta universal para todos los modelos. La tecnica esta pensada principalmente para modelos de espacio de estados y depende de propiedades matematicas que no siempre aparecen igual en otras arquitecturas. Los autores tambien reconocen que algunas extensiones hacia modelos mas modernos requieren mas trabajo.</p>
<p>Aun asi, el paper deja una pregunta poderosa para el futuro: que pasaria si los modelos no solo aprendieran la tarea, sino tambien la forma mas eficiente de representarla?</p>
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
