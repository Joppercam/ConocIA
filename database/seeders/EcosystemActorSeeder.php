<?php

namespace Database\Seeders;

use App\Models\EcosystemActor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EcosystemActorSeeder extends Seeder
{
    public function run(): void
    {
        $actors = [
            // Centros de investigación
            [
                'name'        => 'CENIA — Centro Nacional de Inteligencia Artificial',
                'type'        => 'centro_investigacion',
                'description' => 'Centro Basal de Excelencia financiado por el Ministerio de Ciencia. Liderado por la PUC, reúne investigadores de múltiples universidades chilenas. Su objetivo es convertir a Chile en polo de desarrollo de IA para Latinoamérica.',
                'url'         => 'https://cenia.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['NLP', 'Visión por computadora', 'IA Ética', 'Robótica'],
            ],
            [
                'name'        => 'Instituto de Ingeniería Matemática y Computacional (IMC) — PUC',
                'type'        => 'centro_investigacion',
                'description' => 'Centro interdisciplinario de la PUC que combina matemáticas, computación e ingeniería para abordar problemas complejos con herramientas de IA y modelamiento computacional.',
                'url'         => 'https://imc.uc.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Modelamiento matemático', 'Machine Learning', 'Ciencias físicas + IA'],
            ],
            [
                'name'        => 'NLHPC — Laboratorio Nacional de Computación de Alto Rendimiento',
                'type'        => 'centro_investigacion',
                'description' => 'Infraestructura nacional de supercomputación alojada en la Universidad de Chile. Provee capacidad de procesamiento de alto rendimiento para investigación en IA, ciencias y simulación.',
                'url'         => 'https://www.nlhpc.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Supercomputación', 'HPC', 'Procesamiento de datos masivos'],
            ],
            [
                'name'        => 'CSIAA — Centro Chileno de Supercómputo para IA Aplicada',
                'type'        => 'centro_investigacion',
                'description' => 'Iniciativa que ofrece infraestructura avanzada de IA accesible y segura para empresas, universidades e instituciones públicas de todo Chile. Financiado por CORFO con 7 millones de dólares.',
                'url'         => 'https://csiaa.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Supercómputo', 'IA aplicada', 'Infraestructura cloud'],
            ],
            [
                'name'        => 'IMFD — Instituto Milenio Fundamentos de los Datos',
                'type'        => 'centro_investigacion',
                'description' => 'Centro de investigación de excelencia dedicado a los fundamentos científicos del manejo de datos, incluyendo ética, transparencia y seguridad en sistemas de IA.',
                'url'         => 'https://imfd.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Fundamentos de datos', 'Transparencia algorítmica', 'IA ética', 'Desinformación'],
            ],
            // Universidades
            [
                'name'        => 'Instituto de Datos e Inteligencia Artificial (ID&IA) — Universidad de Chile',
                'type'        => 'universidad',
                'description' => 'Instituto de la Facultad de Ciencias Físicas y Matemáticas de la U. de Chile. Se proyecta como centro referente a nivel nacional e internacional en datos e IA, con especial liderazgo en Latinoamérica.',
                'url'         => 'https://uchile.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Machine Learning', 'Data Science', 'Computación de alto rendimiento'],
            ],
            [
                'name'        => 'Vicerrectoría de Inteligencia Digital — PUC',
                'type'        => 'universidad',
                'description' => 'Creada en 2025, es una unidad institucional de la Pontificia Universidad Católica de Chile dedicada a integrar la inteligencia artificial y digital en la docencia, investigación y extensión universitaria.',
                'url'         => 'https://www.uc.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['IA en educación', 'Transformación digital', 'Investigación interdisciplinaria'],
            ],
            [
                'name'        => 'Universidad Técnica Federico Santa María',
                'type'        => 'universidad',
                'description' => 'Investigación activa en IA aplicada a minería, procesamiento de datos y NLP. Participante del consorcio CENIA y del IMFD con foco en transparencia y seguridad.',
                'url'         => 'https://www.usm.cl',
                'location'    => 'Valparaíso',
                'region'      => 'Valparaíso',
                'focus_areas' => ['NLP', 'Minería de datos', 'Seguridad IA'],
            ],
            [
                'name'        => 'Universidad de Santiago de Chile (USACH)',
                'type'        => 'universidad',
                'description' => 'Investigación en IA aplicada a minería, formación planetaria y educación. Alianza con MIT para desarrollo de IA en investigación científica.',
                'url'         => 'https://portal.usach.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['IA en minería', 'Machine Learning', 'Colaboración MIT'],
            ],
            [
                'name'        => 'Universidad Autónoma de Chile',
                'type'        => 'universidad',
                'description' => 'Implementa un ecosistema integral de innovación educativa con IA, incluyendo infraestructura tecnológica avanzada y alianzas internacionales. Desarrolla proyectos como IA-Design.',
                'url'         => 'https://www.uautonoma.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['IA en educación', 'IA-Design', 'Innovación educativa'],
            ],
            [
                'name'        => 'Universidad de Talca',
                'type'        => 'universidad',
                'description' => 'Integrante del consorcio SCAI-Lab para supercómputo en IA. Investiga aplicaciones de IA en salud y agricultura en la zona central de Chile.',
                'url'         => 'https://www.utalca.cl',
                'location'    => 'Talca',
                'region'      => 'Maule',
                'focus_areas' => ['IA en salud', 'IA en agricultura', 'Supercómputo'],
            ],
            // Startups
            [
                'name'        => 'Ednova',
                'type'        => 'startup',
                'description' => 'Startup chilena pionera en educación personalizada basada en inteligencia artificial. Liderada por Valeria Silva, desarrolla soluciones edtech que adaptan el aprendizaje a cada estudiante.',
                'url'         => 'https://ednova.com',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Edtech', 'IA en educación', 'Aprendizaje personalizado'],
            ],
            [
                'name'        => 'Eclectic',
                'type'        => 'startup',
                'description' => 'Startup chilena que apuesta por crecer con equipos pequeños y fuerte apalancamiento en inteligencia artificial para maximizar eficiencia operativa.',
                'url'         => 'https://www.eclectic.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Automatización', 'Productividad con IA'],
            ],
            // Gobierno
            [
                'name'        => 'Ministerio de Ciencia, Tecnología, Conocimiento e Innovación',
                'type'        => 'gobierno',
                'description' => 'Ministerio responsable de la política de IA en Chile. Impulsa el proyecto de ley de regulación de IA (Boletín 16821-19) y el programa Chile PotencIA.',
                'url'         => 'https://www.minciencia.gob.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Política pública IA', 'Regulación', 'Fomento CTCI'],
            ],
            [
                'name'        => 'CORFO — Corporación de Fomento de la Producción',
                'type'        => 'gobierno',
                'description' => 'Agencia del Estado que financia iniciativas de IA como el programa de supercómputo SCAI-Lab y fondos para startups tecnológicas. Destinó 7 millones de dólares para infraestructura de IA.',
                'url'         => 'https://www.corfo.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Financiamiento', 'Innovación', 'Supercómputo'],
            ],
            // Organizaciones
            [
                'name'        => 'Fundación País Digital',
                'type'        => 'organizacion',
                'description' => 'Organización que impulsa la transformación digital de Chile. Co-organiza el Premio Nacional de Inteligencia Artificial Chile PotencIA junto al Ministerio de Ciencia.',
                'url'         => 'https://paisdigital.org',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Transformación digital', 'Premio Chile PotencIA', 'Divulgación'],
            ],
            [
                'name'        => 'AI Adoption Initiative (AIAI)',
                'type'        => 'organizacion',
                'description' => 'Iniciativa de acompañamiento experto para la adopción de IA en organizaciones chilenas. Participa en la evaluación del Premio Chile PotencIA.',
                'url'         => 'https://aiai.cl',
                'location'    => 'Santiago',
                'region'      => 'Metropolitana',
                'focus_areas' => ['Adopción de IA', 'Consultoría', 'Evaluación'],
            ],
        ];

        foreach ($actors as $data) {
            $slug = Str::slug($data['name']);
            EcosystemActor::firstOrCreate(['slug' => $slug], array_merge($data, ['slug' => $slug]));
        }
    }
}
