<?php

namespace App\Http\Controllers;

use App\Models\Regulation;

class RegulationController extends Controller
{
    public function index()
    {
        $all           = Regulation::orderBy('scope')->orderByDesc('date_introduced')->get();
        $featured      = $all->where('slug', 'proyecto-de-ley-de-sistemas-de-inteligencia-artificial-boletin-16821-19')->first()
                         ?? $all->where('scope', 'chile')->where('status', 'en_tramitacion')->first();
        $chile         = $all->where('scope', 'chile')->where('id', '!=', optional($featured)->id)->values();
        $internacional = $all->where('scope', 'internacional')->values();
        $updatedAt     = $all->max('updated_at');
        $voices        = $this->voicesData();

        return view('regulacion.index', compact('all', 'featured', 'chile', 'internacional', 'updatedAt', 'voices'));
    }

    public function show(string $slug)
    {
        $regulation = Regulation::where('slug', $slug)->firstOrFail();
        $others     = Regulation::where('id', '!=', $regulation->id)
            ->orderByDesc('date_introduced')
            ->limit(3)
            ->get();

        return view('regulacion.show', compact('regulation', 'others'));
    }

    public function voces()
    {
        $voices = $this->voicesData();
        return view('regulacion.voces', compact('voices'));
    }

    private function voicesData(): array
    {
        return [
            [
                'id'            => 'gobierno',
                'name'          => 'Ministerio de Ciencia',
                'person'        => 'Ministra Aisén Etcheverry',
                'role'          => 'Ministra de Ciencia, Tecnología, Conocimiento e Innovación',
                'institution'   => 'Gobierno de Chile',
                'icon'          => 'fas fa-landmark',
                'postura'       => 'impulsor',
                'postura_label' => 'Impulsor',
                'postura_color' => '#3b82f6',
                'spectrum_pos'  => 12,
                'summary'       => 'Chile debe liderar la regulación de IA en Latinoamérica. La regulación no frena la innovación, la ordena.',
                'context'       => 'El gobierno ha impulsado el proyecto con urgencia suma, señalando que Chile tiene la oportunidad de posicionarse como referente latinoamericano en gobernanza de IA. El Ministerio de Ciencia lidera el proceso y ha coordinado la participación de Chile en foros internacionales, incluyendo el International AI Safety Report. También impulsa iniciativas complementarias como Chile PotencIA (premio nacional de IA), la certificación ChileValora en IA, y el Plan Nacional de Data Centers. La visión del gobierno es que un marco claro de reglas da certeza jurídica a los actores y protege a los ciudadanos, lo que a largo plazo genera más confianza y más inversión que un entorno sin reglas.',
                'punto_clave'   => 'Para el gobierno, no regular no es una opción. La pregunta no es si hay que regular, sino cómo hacerlo bien. Chile quiere ser el primero en LATAM en tener una ley integral de IA.',
            ],
            [
                'id'            => 'alvaro-soto',
                'name'          => 'Álvaro Soto',
                'person'        => 'Álvaro Soto',
                'role'          => 'Director de CENIA',
                'institution'   => 'CENIA / PUC Chile',
                'icon'          => 'fas fa-university',
                'postura'       => 'a_favor_matices',
                'postura_label' => 'A favor con matices',
                'postura_color' => '#10b981',
                'spectrum_pos'  => 30,
                'summary'       => 'A favor de la regulación, con énfasis en que debe impulsar la innovación, no frenarla.',
                'context'       => 'Soto ha sido una voz central en el debate de IA en Chile. Participó en la redacción del International AI Safety Report junto a la profesora Raquel Pezoa de la UTFSM, siendo Chile uno de solo tres países latinoamericanos que participaron en ese informe mundial. Desde CENIA impulsa una visión de IA "al servicio de las personas" y proyectos como LATAM-GPT, un modelo colaborativo desarrollado por más de 50 instituciones latinoamericanas, demuestran que es posible desarrollar IA propia sin depender de las big tech.',
                'punto_clave'   => 'La academia apoya regular, pero insiste en que debe acompañarse de inversión en investigación y talento. Regular sin capacidades propias solo haría a Chile dependiente de tecnología extranjera.',
            ],
            [
                'id'            => 'landerretche',
                'name'          => 'Andrés Landerretche',
                'person'        => 'Andrés Landerretche',
                'role'          => 'Director de Regulatory Analysis and Design',
                'institution'   => 'Sovos',
                'icon'          => 'fas fa-balance-scale',
                'postura'       => 'a_favor_matices',
                'postura_label' => 'A favor con matices',
                'postura_color' => '#10b981',
                'spectrum_pos'  => 48,
                'summary'       => 'Chile puede ser referente regional, pero la regulación no debe ahogar a los actores pequeños.',
                'context'       => 'Landerretche ha advertido que una regulación excesivamente rígida puede ahogar a quienes emprendiendo, investigando o experimentando con IA en Chile. Startups, pymes y centros académicos no tienen la musculatura de grandes corporaciones, y establecer barreras normativas demasiado altas podría consolidar la asimetría entre gigantes globales y actores locales. Al mismo tiempo reconoce que proyectos como LATAM-GPT y la plataforma CopuChat del Ministerio de Ciencia muestran que es posible regular e innovar a la vez.',
                'punto_clave'   => 'La regulación debe ser proporcional. No puede exigir lo mismo a una startup de 3 personas que a Google. Si lo hace, termina beneficiando a los grandes y aplastando a los chicos.',
            ],
            [
                'id'            => 'juridico',
                'name'          => 'Análisis jurídico',
                'person'        => 'Diario Constitucional',
                'role'          => 'Constitucionalistas y abogados especializados',
                'institution'   => 'Diario Constitucional / academia jurídica',
                'icon'          => 'fas fa-gavel',
                'postura'       => 'critico',
                'postura_label' => 'Crítico',
                'postura_color' => '#ef4444',
                'spectrum_pos'  => 55,
                'summary'       => 'El proyecto tiene problemas serios de diseño procesal e institucional que deben corregirse antes de aprobarse.',
                'context'       => 'Desde el ámbito jurídico se señala que el mecanismo de resolución de incidentes deja al propio operador de IA como investigador, juez y parte. Si un sistema de IA te causa un daño, es la misma empresa la que investiga, determina causalidad y decide medidas correctivas, sin regulación procesal, sin recursos administrativos o judiciales y sin participación de la víctima. Adicionalmente, se designa como fiscalizadora a la futura Agencia de Protección de Datos Personales, que aún no existe operativamente.',
                'punto_clave'   => 'La ley puede tener buenas intenciones, pero si los mecanismos de protección no funcionan en la práctica, se convierte en letra muerta. El proyecto necesita mejoras procesales significativas.',
            ],
            [
                'id'            => 'bigtech',
                'name'          => 'Amazon y Google',
                'person'        => 'Amazon y Google',
                'role'          => 'Empresas tecnológicas globales con presencia en Chile',
                'institution'   => 'Big Tech',
                'icon'          => 'fas fa-building',
                'postura'       => 'cautela',
                'postura_label' => 'Cautela',
                'postura_color' => '#f59e0b',
                'spectrum_pos'  => 70,
                'summary'       => 'Reparos sobre el impacto en la velocidad de innovación y despliegue de servicios.',
                'context'       => 'Empresas como Amazon y Google han manifestado reparos respecto del proyecto. Su inquietud apunta al riesgo de que ciertas obligaciones limiten la velocidad de despliegue e innovación en Chile. Esta resistencia recuerda que regular la IA significa navegar equilibrios entre soberanía nacional, competencia de mercado y desarrollo económico. Su presencia en Chile con inversiones en infraestructura de datos y servicios cloud les otorga influencia en el debate.',
                'punto_clave'   => 'Las grandes tecnológicas temen que Chile se convierta en un mercado con más trabas que sus vecinos, desviando inversión. El contraargumento: regulación clara también genera certeza jurídica, lo cual atrae inversión de calidad.',
            ],
            [
                'id'            => 'lyd',
                'name'          => 'Libertad y Desarrollo',
                'person'        => 'Libertad y Desarrollo',
                'role'          => 'Centro de estudios de política pública',
                'institution'   => 'Libertad y Desarrollo',
                'icon'          => 'fas fa-chart-line',
                'postura'       => 'critico',
                'postura_label' => 'Crítico',
                'postura_color' => '#ef4444',
                'spectrum_pos'  => 85,
                'summary'       => 'Chile está importando un modelo europeo sin considerar que su realidad económica es radicalmente distinta.',
                'context'       => 'Libertad y Desarrollo ha cuestionado si la regulación podría generar cargas excesivas para las empresas. Su preocupación principal es que Chile está importando el EU AI Act sin considerar que la realidad económica chilena es radicalmente distinta a la de Europa. El tejido empresarial chileno está compuesto principalmente por pymes que no tienen departamentos de compliance ni equipos legales para cumplir con regulaciones complejas.',
                'punto_clave'   => 'Hay una tensión real entre proteger derechos y no asfixiar la innovación. Chile no puede darse el lujo de alejar inversión con burocracia regulatoria. El desafío es encontrar el punto medio.',
            ],
        ];
    }
}
