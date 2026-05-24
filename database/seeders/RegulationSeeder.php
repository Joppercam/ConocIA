<?php

namespace Database\Seeders;

use App\Models\Regulation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RegulationSeeder extends Seeder
{
    public function run(): void
    {
        $regulations = [
            [
                'title'            => 'Proyecto de Ley de Sistemas de Inteligencia Artificial (Boletín 16821-19)',
                'scope'            => 'chile',
                'status'           => 'en_tramitacion',
                'summary'          => 'Proyecto de ley que regula los sistemas de IA en Chile. Establece principios de transparencia, clasificación por niveles de riesgo (inaceptable, alto, limitado, sin riesgo), obligaciones para proveedores e implementadores, y crea un Consejo Asesor Técnico de IA dependiente del Ministerio de Ciencia. Fue aprobado por la Cámara de Diputados en octubre 2025 y se encuentra en segundo trámite en el Senado, en la Comisión de Desafíos del Futuro, Ciencia, Tecnología e Innovación. Tiene urgencia suma.',
                'source_url'       => 'https://www.minciencia.gob.cl/areas/inteligencia-artificial/Inteligencia-Artificial/Proyecto-Ley-regula-sistemas-IA/',
                'institution'      => 'Ministerio de Ciencia / Gobierno de Chile',
                'date_introduced'  => '2024-05-07',
            ],
            [
                'title'            => 'Política Nacional de Inteligencia Artificial de Chile',
                'scope'            => 'chile',
                'status'           => 'vigente',
                'summary'          => 'Política pública que establece la hoja de ruta de Chile para el desarrollo y adopción de IA. Define ejes estratégicos en formación de talento, infraestructura, investigación, adopción productiva y marco ético. Fue actualizada en 2024.',
                'source_url'       => 'https://www.minciencia.gob.cl',
                'institution'      => 'Ministerio de Ciencia / CTCI',
                'date_introduced'  => '2021-11-01',
            ],
            [
                'title'            => 'Certificación ChileValora en IA',
                'scope'            => 'chile',
                'status'           => 'vigente',
                'summary'          => 'Iniciativa del Ministerio de Ciencia para que ChileValora certifique la formación de especialistas en inteligencia artificial, creando un estándar nacional de competencias en IA.',
                'source_url'       => 'https://www.minciencia.gob.cl',
                'institution'      => 'ChileValora / Ministerio de Ciencia',
                'date_introduced'  => '2025-01-01',
            ],
            [
                'title'            => 'Plan Nacional de Data Centers',
                'scope'            => 'chile',
                'status'           => 'propuesta',
                'summary'          => 'Hoja de ruta coordinada por el Ministerio de Ciencia para el desarrollo sostenible de centros de datos en Chile. El país alberga 22 data centers operativos y se proyectan 28 más.',
                'source_url'       => 'https://www.minciencia.gob.cl',
                'institution'      => 'Ministerio de Ciencia',
                'date_introduced'  => '2025-06-01',
            ],
            [
                'title'            => 'Reglamento de Inteligencia Artificial de la UE (EU AI Act)',
                'scope'            => 'internacional',
                'status'           => 'vigente',
                'summary'          => 'Primera ley integral sobre IA en el mundo. Establece un marco regulatorio basado en niveles de riesgo para sistemas de IA comercializados o utilizados en la Unión Europea. Entró en vigencia progresiva desde 2024. El proyecto de ley chileno se inspira parcialmente en su enfoque de clasificación por riesgo.',
                'source_url'       => null,
                'institution'      => 'Unión Europea',
                'date_introduced'  => '2024-03-13',
            ],
            [
                'title'            => 'Orden Ejecutiva sobre IA Segura (EE.UU.)',
                'scope'            => 'internacional',
                'status'           => 'vigente',
                'summary'          => 'Orden ejecutiva firmada por el presidente de Estados Unidos que establece estándares de seguridad para sistemas de IA, requisitos de transparencia y directrices para el uso gubernamental de inteligencia artificial.',
                'source_url'       => null,
                'institution'      => 'Gobierno de Estados Unidos',
                'date_introduced'  => '2023-10-30',
            ],
        ];

        foreach ($regulations as $data) {
            $slug = Str::slug($data['title']);
            Regulation::firstOrCreate(['slug' => $slug], array_merge($data, ['slug' => $slug]));
        }
    }
}
