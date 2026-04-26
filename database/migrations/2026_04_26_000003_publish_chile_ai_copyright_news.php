<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private string $slug = 'chile-debate-ia-derechos-autor-mineria-datos';

    public function up(): void
    {
        $now = Carbon::now();
        $editorId = $this->editorId();
        $categoryId = $this->categoryId($now);

        $payload = [
            'title' => 'Chile reabre el debate por IA y derechos de autor: la minería de datos entra al centro de la agenda',
            'excerpt' => 'El Gobierno incluyó una excepción a la Ley de Propiedad Intelectual para habilitar minería de textos y datos. La medida busca atraer inversión y acelerar el desarrollo de IA, pero medios, gremios y especialistas advierten riesgos para autores y productores de contenido.',
            'summary' => 'Chile volvió a poner la inteligencia artificial en el centro del debate regulatorio. Una norma incluida en la megarreforma económica del Gobierno propone permitir el uso de obras lícitamente publicadas para procesos de extracción, análisis y minería de datos vinculados a IA. Sus defensores la presentan como una condición para desarrollar innovación, ciencia e inversión en el país. Sus críticos advierten que, si queda mal delimitada, puede debilitar derechos de autor, medios de comunicación y creadores.',
            'keywords' => 'inteligencia artificial Chile, propiedad intelectual IA, minería de datos, derechos de autor, regulación IA Chile, CENIA, medios chilenos',
            'content' => $this->content(),
            'image' => null,
            'category_id' => $categoryId,
            'author' => 'Editor',
            'views' => 0,
            'tags' => 'Chile, inteligencia artificial, propiedad intelectual, minería de datos, derechos de autor, medios, regulación IA',
            'featured' => false,
            'is_published' => true,
            'status' => 'published',
            'source' => 'El País Chile',
            'source_url' => 'https://elpais.com/chile/2026-04-24/kast-impulsa-una-norma-que-abre-la-puerta-al-uso-sin-control-de-contenidos-de-la-ia.html',
            'published_at' => $now,
            'reading_time' => $this->readingTime($this->content()),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if ($editorId !== null && Schema::hasColumn('news', 'author_id')) {
            $payload['author_id'] = $editorId;
        }

        if ($editorId !== null && Schema::hasColumn('news', 'user_id')) {
            $payload['user_id'] = $editorId;
        }

        DB::table('news')->updateOrInsert(
            ['slug' => $this->slug],
            $payload
        );

        $this->clearNewsCache();
    }

    public function down(): void
    {
        DB::table('news')->where('slug', $this->slug)->delete();

        $this->clearNewsCache();
    }

    private function categoryId(Carbon $now): int
    {
        $name = 'Regulación de IA';
        $slug = Str::slug($name);

        $category = DB::table('categories')->where('slug', $slug)->first();

        if ($category) {
            DB::table('categories')->where('id', $category->id)->update([
                'name' => $name,
                'description' => 'Leyes, políticas públicas, derechos digitales y gobernanza de sistemas de inteligencia artificial.',
                'color' => '#2196F3',
                'icon' => 'fa-gavel',
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $category->id;
        }

        return (int) DB::table('categories')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'description' => 'Leyes, políticas públicas, derechos digitales y gobernanza de sistemas de inteligencia artificial.',
            'color' => '#2196F3',
            'icon' => 'fa-gavel',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function editorId(): ?int
    {
        $roleIds = DB::table('roles')
            ->whereIn('slug', ['editor', 'admin'])
            ->pluck('id');

        $editorId = DB::table('users')
            ->whereIn('role_id', $roleIds)
            ->where(function ($query) {
                $query->where('email', 'editor@conocia.com')
                    ->orWhere('username', 'editor')
                    ->orWhere('name', 'Editor');
            })
            ->orderByRaw("CASE WHEN email = 'editor@conocia.com' THEN 0 ELSE 1 END")
            ->value('id');

        if ($editorId) {
            return (int) $editorId;
        }

        $fallbackId = DB::table('users')
            ->whereIn('role_id', $roleIds)
            ->orderBy('id')
            ->value('id');

        return $fallbackId ? (int) $fallbackId : null;
    }

    private function readingTime(string $content): int
    {
        $words = str_word_count(strip_tags($content));

        return max(1, (int) ceil($words / 220));
    }

    private function clearNewsCache(): void
    {
        foreach ([
            'home_page_data',
            'home_page_data_v2',
            'all_published_news',
            'all_published_news_v2',
            'popular_news',
            'secondary_news',
            'trending_ids',
            'news_index_list',
            'most_read_articles',
            'popular_tags',
            'all_categories',
        ] as $key) {
            Cache::forget($key);
        }
    }

    private function content(): string
    {
        return <<<'TEXT'
Chile volvió a abrir una de las discusiones más complejas de la inteligencia artificial: qué puede usar un sistema de IA para aprender, analizar o generar conocimiento, y bajo qué condiciones debe hacerlo.

La controversia surgió después de que el Gobierno incluyera en su megarreforma económica una disposición que modifica la Ley 17.336 de Propiedad Intelectual. Según reportó El País Chile, la norma permitiría usar obras lícitamente publicadas para procesos de extracción, clasificación, análisis o minería de datos, sin autorización ni remuneración al titular, siempre que no exista una explotación encubierta de la obra.

El punto técnico puede sonar estrecho, pero su impacto es amplio. En la práctica, la minería de textos y datos es una de las bases sobre las que se entrenan, evalúan o alimentan sistemas de inteligencia artificial. Permite procesar grandes volúmenes de documentos, noticias, imágenes, sonidos, código o bases de conocimiento para encontrar patrones, extraer información o construir nuevos servicios.

Para el Gobierno y parte del ecosistema tecnológico, habilitar este tipo de procesamiento puede ser clave para que Chile no quede rezagado. La Tercera reportó que desde el Ministerio de Ciencia se ha defendido el espíritu pro innovación de la norma, apuntando a actualizar la legislación para abrir una "carretera de datos" que permita desarrollar capacidades locales en IA.

Esa mirada también tiene respaldo en estudios recientes. CENIA presentó en abril un informe, apoyado por Microsoft, ACTI y CEPAL, que estima que habilitar la minería de textos y datos podría tener efectos relevantes sobre productividad, innovación y ciencia. El argumento económico es directo: sin acceso legalmente claro a grandes volúmenes de información, desarrollar IA local se vuelve más difícil, caro e incierto.

Pero la otra cara del debate es igual de importante. Medios de comunicación, gremios audiovisuales, autores y especialistas en propiedad intelectual advierten que una excepción demasiado amplia podría permitir que sistemas de IA usen contenido chileno sin licencias, sin compensación y sin reglas claras de trazabilidad.

La Asociación Nacional de la Prensa y Anatel han cuestionado la propuesta, según reportes de El País y La Tercera. Su preocupación no es solo jurídica. También es económica y democrática: si noticias, reportajes, imágenes, programas o contenido editorial pueden ser absorbidos por sistemas de IA sin acuerdos, se debilita la sostenibilidad de quienes producen información profesional.

El centro del problema está en el equilibrio. Chile necesita condiciones para que universidades, startups, centros de investigación, empresas y organismos públicos puedan usar datos para desarrollar IA. Pero también necesita proteger a quienes producen conocimiento, cultura, periodismo y obras creativas.

La tensión no es única de Chile. Estados Unidos, la Unión Europea, Reino Unido y otros mercados están enfrentando versiones similares del mismo dilema: cómo permitir innovación con IA sin transformar la propiedad intelectual en una zona gris donde solo ganen quienes tienen escala, cómputo y capacidad legal para litigar.

Una mala regulación puede producir dos efectos opuestos, ambos indeseables. Si es demasiado restrictiva, puede frenar investigación, emprendimiento e inversión. Si es demasiado amplia, puede transferir valor desde creadores locales hacia plataformas de IA sin mecanismos razonables de licencia, opt-out, compensación o transparencia.

Por eso la pregunta relevante no es si Chile debe permitir minería de datos para IA. Probablemente sí debe hacerlo, al menos en ciertos casos. La pregunta de fondo es bajo qué condiciones.

Un marco más robusto podría distinguir entre investigación pública, uso educativo, desarrollo comercial, entrenamiento de modelos generativos, análisis interno, uso periodístico, obras protegidas con reserva expresa y contenidos cuya explotación normal pueda verse afectada. También podría considerar mecanismos de oposición, registros de datasets, trazabilidad, acuerdos de licencia y excepciones diferenciadas para pymes, academia y grandes plataformas.

La discusión llega, además, mientras el proyecto de ley que regula sistemas de IA sigue su propio trámite. El Ministerio de Ciencia mantiene información pública sobre esa iniciativa, que busca ordenar el uso de sistemas de IA con criterios de derechos, innovación y capacidad estatal. La nueva controversia muestra que la gobernanza de IA no puede separarse de otras capas regulatorias: datos, propiedad intelectual, competencia, derechos fundamentales y desarrollo productivo.

Para Chile, el desafío es estratégico. El país puede intentar ser solo consumidor de modelos desarrollados afuera, o puede construir capacidades propias en datos, infraestructura, talento y aplicaciones. Pero esa ambición requiere reglas claras.

La minería de datos puede ser una herramienta para acelerar ciencia, salud, educación, minería, servicios públicos y productividad. También puede convertirse en una puerta abierta al uso indiscriminado de contenido si no se define con precisión.

El debate que se abre no es menor. Chile está decidiendo qué tipo de ecosistema de IA quiere construir: uno que habilite innovación con responsabilidad, o uno que avance rápido dejando zonas grises para resolver después en tribunales.

Y en inteligencia artificial, las zonas grises suelen crecer más rápido que las leyes.
TEXT;
    }
};
