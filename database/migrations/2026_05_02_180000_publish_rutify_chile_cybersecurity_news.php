<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private string $slug = 'rutify-chile-anci-presunta-filtracion-datos-servicios-publicos';

    public function up(): void
    {
        $now = Carbon::now();
        $editorId = $this->editorId();
        $categoryId = $this->categoryId($now);
        $content = $this->content();

        $payload = [
            'title' => 'Caso Rutify: ANCI descarta ciberataque reciente, pero alerta por exposición de datos públicos en Chile',
            'excerpt' => 'La Agencia Nacional de Ciberseguridad investigó reportes de actividad maliciosa asociados a servicios públicos, telecomunicaciones y datos personales. Al 2 de mayo de 2026, ANCI sostiene que no hay infraestructuras comprometidas y que gran parte de la información correspondería a filtraciones antiguas reordenadas.',
            'summary' => 'Chile enfrenta una alerta de ciberseguridad por el caso Rutify, un sitio y actor digital asociado a la publicación de datos personales. ANCI informó el 1 de mayo que analizaba reportes de actividad maliciosa contra servicios públicos y operadores de telecomunicaciones. El 2 de mayo, su directora subrogante descartó un ciberataque reciente y explicó que la información publicada parece provenir mayoritariamente de filtraciones anteriores, aunque reconoció una porción menor de datos nuevos posiblemente obtenida mediante credenciales válidas. Gobierno Digital, TGR y Registro Civil han descartado afectaciones confirmadas en sus sistemas.',
            'keywords' => 'Rutify Chile, ANCI, ciberseguridad Chile, filtración de datos, ClaveÚnica, Tesorería General de la República, Registro Civil, servicios públicos, telecomunicaciones, datos personales',
            'content' => $content,
            'image' => null,
            'category_id' => $categoryId,
            'author' => 'Editor',
            'views' => 0,
            'tags' => 'Chile, ciberseguridad, ANCI, Rutify, datos personales, ClaveUnica, TGR, Registro Civil, servicios públicos',
            'featured' => true,
            'is_published' => true,
            'status' => 'published',
            'source' => 'ANCI, BioBioChile, 24 Horas, ADN, Emol y T13',
            'source_url' => implode("\n", [
                'https://www.biobiochile.cl/noticias/nacional/chile/2026/05/02/desde-anci-explican-que-paso-con-la-filtracion-de-datos-y-descartan-reciente-ciberataque.shtml',
                'https://www.24horas.cl/amp/actualidad/nacional/anci-confirma-que-esta-investigando-incidentes-de-ciberseguridad-afectaria-instituciones-publicas',
                'https://www.adnradio.cl/2026/05/01/agencia-nacional-de-ciberseguridad-investiga-presunta-filtracion-de-datos-en-servicios-publicos-y-telecomunicaciones/?outputType=amp',
                'https://www.emol.com/noticias/Nacional/2026/05/02/1198879/gobierno-digital-datos-clave-unica.html',
                'https://www.t13.cl/noticia/nacional/tesoreria-general-descarta-vulneraciones-tras-presunta-filtracion-datos-afirman-2-5-2026',
            ]),
            'published_at' => $now,
            'reading_time' => $this->readingTime($content),
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
        $name = 'Ciberseguridad';
        $slug = Str::slug($name);

        $category = DB::table('categories')->where('slug', $slug)->first();

        if ($category) {
            DB::table('categories')->where('id', $category->id)->update([
                'name' => $name,
                'description' => 'Amenazas digitales, protección de datos, seguridad pública y resiliencia tecnológica.',
                'color' => '#d32f2f',
                'icon' => 'fa-shield-alt',
                'is_active' => true,
                'updated_at' => $now,
            ]);

            return (int) $category->id;
        }

        return (int) DB::table('categories')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'description' => 'Amenazas digitales, protección de datos, seguridad pública y resiliencia tecnológica.',
            'color' => '#d32f2f',
            'icon' => 'fa-shield-alt',
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
Chile volvió a mirar de frente una de sus vulnerabilidades más sensibles: la exposición de datos personales asociados a servicios públicos, telecomunicaciones y plataformas de identidad digital.

El caso explotó entre el 1 y el 2 de mayo de 2026, después de que reportes especializados y publicaciones en redes sociales atribuyeran al actor o sitio conocido como Rutify una presunta filtración de datos vinculada a instituciones del Estado y empresas de telecomunicaciones. En las primeras versiones aparecieron menciones a la Tesorería General de la República, el Registro Civil, Fonasa, ClaveÚnica y operadores privados.

La información disponible exige precisión. Hasta ahora, no existe confirmación oficial de una intrusión masiva reciente contra infraestructura crítica del Estado. Lo que sí existe es una investigación abierta, una alerta pública de ANCI y una exposición de datos que volvió accesible para usuarios comunes información que, según la autoridad, en buena parte ya circulaba desde antes en entornos menos visibles.

El 1 de mayo, la Agencia Nacional de Ciberseguridad (ANCI) informó que estaba analizando reportes de fuentes de inteligencia en ciberseguridad sobre presunta actividad maliciosa detectada en las 48 horas previas. Según reportaron ADN y 24 Horas, la agencia señaló que el caso podía involucrar a operadores de telecomunicaciones y servicios públicos, pero enfatizó que no había podido corroborar la autenticidad ni el alcance de la información supuestamente comprometida.

La agencia también indicó que estaba coordinada con instituciones públicas y privadas para verificar los antecedentes y, si correspondía, adoptar medidas. En paralelo, las entidades contactadas aplicaron medidas preventivas adicionales mientras seguía la revisión.

La actualización más relevante llegó el sábado 2 de mayo. En conversación con Radio Bío-Bío, Michelle Bordachar, directora subrogante de ANCI, descartó un ciberataque reciente en los términos en que circulaba en redes: hasta ese momento no había infraestructuras comprometidas. Su explicación fue que el caso apuntaba principalmente a datos filtrados con anterioridad, luego correlacionados, ordenados y publicados de forma más accesible.

Esa distinción importa. Una base de datos rearmada con filtraciones antiguas no es inocua: puede facilitar fraude, suplantación, doxing, llamadas engañosas y campañas de phishing. Pero no equivale automáticamente a una brecha nueva en sistemas como ClaveÚnica, Registro Civil o TGR. En ciberseguridad, confundir ambos escenarios puede producir pánico, desinformación y malas decisiones.

Bordachar también reconoció un matiz: ANCI detectó una porción pequeña de información que sí parecía nueva. La hipótesis entregada por la autoridad es que esa información pudo obtenerse mediante accesos con credenciales válidas, no necesariamente por una vulneración técnica directa de una gran base estatal. En términos simples, si una persona reutiliza la misma contraseña en varios servicios y una clave antigua ya estaba filtrada, un atacante puede probarla contra cuentas de funcionarios o usuarios y lograr acceso.

Gobierno Digital salió a contener una de las principales preocupaciones ciudadanas: ClaveÚnica. Según informó Emol, la Secretaría de Gobierno Digital del Ministerio de Hacienda aseguró el 2 de mayo que no hay evidencia de que la infraestructura o la base de datos de ClaveÚnica haya sido afectada por los reportes. También afirmó que la plataforma seguía operativa, segura y funcionando con normalidad.

La Tesorería General de la República entregó un mensaje similar. De acuerdo con T13, la TGR informó durante la tarde del 2 de mayo que sus servicios y plataformas tecnológicas operaban con normalidad, sin evidencias de vulneraciones ni afectaciones asociadas a incidentes de ciberseguridad. La institución añadió que la información tributaria se mantenía resguardada y que sus equipos especializados realizaban monitoreo permanente.

ADN reportó, además, que el Registro Civil descartó que las publicaciones atribuidas a la cuenta identificada como Vercet correspondieran a datos o parámetros utilizados en sus bases registrales o de identificación.

El cuadro, entonces, es este: hubo una alerta real, una investigación en curso y exposición pública de datos sensibles o personales; pero, al 2 de mayo de 2026, las autoridades no han confirmado un ciberataque reciente de gran escala contra las plataformas mencionadas. ANCI, Gobierno Digital, TGR y Registro Civil han insistido en que no hay evidencia pública de afectación directa a sus infraestructuras principales.

Eso no vuelve menor el episodio. Al contrario, revela un problema estructural. Cuando bases antiguas, datos de telcos, registros públicos, credenciales reutilizadas y plataformas de consulta se cruzan, el resultado puede parecer una filtración nueva aunque sus piezas provengan de distintos años y fuentes. Para la ciudadanía, el daño práctico puede ser parecido: más exposición, más intentos de engaño y más riesgo de suplantación.

El caso Rutify también expone una tensión chilena conocida: durante años, datos personales como RUT, direcciones, teléfonos, correos, afiliaciones o antecedentes de trámites han circulado en bases parcialmente públicas, filtradas o comercializadas. La novedad no siempre está en el dato, sino en la facilidad de acceso, el cruce automatizado y la capacidad de convertir piezas dispersas en perfiles utilizables.

Por eso las recomendaciones no son cosméticas. ANCI llamó a extremar precauciones frente a correos, mensajes, llamadas o enlaces sospechosos que pidan claves o datos personales. Bordachar insistió en cambiar contraseñas periódicamente, no reutilizarlas entre servicios y activar doble factor de autenticación donde esté disponible. Para instituciones públicas y privadas, el mensaje es aún más fuerte: implementar doble factor, revisar accesos, monitorear credenciales expuestas y reducir la dependencia de contraseñas como único control.

Para usuarios, la prioridad inmediata es práctica: cambiar contraseñas críticas si se reutilizan, usar claves únicas por servicio, activar autenticación multifactor, desconfiar de mensajes que simulen provenir de TGR, Registro Civil, Gobierno Digital, bancos o telcos, y entrar siempre escribiendo manualmente la dirección oficial del servicio. También conviene revisar correos o RUT en herramientas oficiales como CiberLupa de ANCI cuando corresponda.

Para el Estado, la lección es más amplia. Chile ya cuenta con una Agencia Nacional de Ciberseguridad y una Ley Marco de Ciberseguridad, pero incidentes como este muestran que la resiliencia no depende solo de reaccionar ante ataques. También depende de higiene de credenciales, interoperabilidad segura, trazabilidad de accesos, minimización de datos, respuesta pública rápida y comunicación clara en crisis.

La comunicación, de hecho, fue clave. En pocas horas circularon versiones que hablaban de hackeo masivo, filtración de ClaveÚnica y compromiso de instituciones estatales. La evidencia pública disponible no permite sostener esas afirmaciones como hechos confirmados. La cobertura responsable debe separar tres niveles: reportes no verificados, datos efectivamente expuestos y afectación comprobada de sistemas.

El caso seguirá evolucionando. Si aparecen nuevos antecedentes técnicos, muestras verificadas o confirmaciones institucionales, el diagnóstico puede cambiar. Pero con la información disponible al sábado 2 de mayo de 2026, la conclusión prudente es esta: Chile no enfrenta, según ANCI, un ciberataque reciente confirmado contra la infraestructura de ClaveÚnica, TGR o Registro Civil; sí enfrenta una alerta seria por exposición y reutilización de datos, con impacto ciudadano real y con obligaciones urgentes para usuarios, instituciones y proveedores digitales.

Fuentes consultadas: BioBioChile, 24 Horas, ADN Radio, Emol y T13, con reportes publicados entre el 1 y el 2 de mayo de 2026 sobre la investigación de ANCI, la actualización de su directora subrogante, el estado de ClaveÚnica y los comunicados de TGR y Registro Civil.
TEXT;
    }
};
