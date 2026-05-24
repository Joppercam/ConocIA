<?php

namespace Database\Seeders;

use App\Models\EcosystemActor;
use Illuminate\Database\Seeder;

class UpdateEcosystemActorContentSeeder extends Seeder
{
    public function run(): void
    {
        $updates = [
            'cenia-centro-nacional-de-inteligencia-artificial' => [
                'name'        => 'CENIA — Centro Nacional de Inteligencia Artificial',
                'director'    => 'Álvaro Soto',
                'founded'     => 'Noviembre 2021',
                'url'         => 'https://cenia.cl',
                'focus_areas' => ['Aprendizaje profundo', 'NLP', 'Visión por computadora', 'IA ética', 'Neurociencia computacional'],
                'key_facts'   => [
                    '200+ personas en el equipo',
                    '15+ universidades participantes',
                    '4 universidades fundadoras: PUC, U. de Chile, UAI, UTFSM',
                    '5 líneas de investigación',
                    'Publicaciones en NeurIPS, CVPR, EMNLP, ICLR',
                    '9 alianzas con empresas e instituciones públicas',
                ],
                'description' => 'El Centro Nacional de Inteligencia Artificial (CENIA) es la principal institución de investigación en IA de Chile. Es una corporación público-privada sin fines de lucro, financiada por el concurso de centros basales de ANID. Fue fundado en noviembre de 2021 por cuatro universidades: Pontificia Universidad Católica de Chile, Universidad de Chile, Universidad Adolfo Ibáñez y Universidad Técnica Federico Santa María.

Hoy integra a más de 200 personas e investigadores de más de 15 universidades chilenas. Su misión es ser un pilar para el desarrollo de la IA en Chile y el mundo, promoviendo investigación de vanguardia, transferencia tecnológica, colaboración multidisciplinaria y un progreso tecnológico sustentable y ético.

CENIA trabaja en 5 líneas de investigación que van desde modelos de aprendizaje profundo hasta el cruce de la IA con la física, la neurociencia y su impacto en la sociedad. En 2026, logró 4 publicaciones en ICLR (International Conference on Learning Representations), una de las conferencias más prestigiosas de IA a nivel mundial. También ha presentado investigaciones en NeurIPS, CVPR y EMNLP.',
            ],

            'instituto-de-datos-e-inteligencia-artificial-idia-universidad-de-chile' => [
                'name'        => 'Instituto de Datos e Inteligencia Artificial (ID&IA) — Universidad de Chile',
                'url'         => 'https://ingenieria.uchile.cl',
                'focus_areas' => ['Machine Learning', 'Data Science', 'Computación de alto rendimiento', 'IA ética'],
                'key_facts'   => [
                    'Universidad pública más antigua de Chile',
                    'Investigadores en CENIA, IMFD y otros centros',
                    'Aloja el NLHPC (supercomputación)',
                    'Académicos destacados: Bárbara Poblete, Andrés Abeliuk',
                ],
                'description' => 'El Instituto de Datos e Inteligencia Artificial (ID&IA) fue creado en la Facultad de Ciencias Físicas y Matemáticas de la Universidad de Chile como centro referente a nivel nacional e internacional, con especial liderazgo en Latinoamérica. Nace de la convicción de que el círculo virtuoso entre disponibilidad masiva de datos y herramientas de IA abre nuevos espacios para la investigación científica y la tecnología.

A través del Departamento de Ciencias de la Computación (DCC), la facultad aporta investigadores clave al ecosistema de IA chileno, incluyendo a la profesora Bárbara Poblete (IA centrada en el ser humano) y el profesor Andrés Abeliuk (computación social). Varios de sus académicos participan activamente en CENIA e IMFD.',
            ],

            'vicerrectoria-de-inteligencia-digital-puc' => [
                'name'        => 'Vicerrectoría de Inteligencia Digital — PUC',
                'founded'     => '2025',
                'url'         => 'https://www.uc.cl',
                'focus_areas' => ['IA en educación', 'Transformación digital', 'Investigación interdisciplinaria'],
                'key_facts'   => [
                    'Primera Vicerrectoría de Inteligencia Digital en Chile',
                    'Una de las primeras en Latinoamérica',
                    'Lidera CENIA (a través del prof. Álvaro Soto)',
                    'Aloja el IMC',
                ],
                'description' => 'En 2025, la Pontificia Universidad Católica de Chile dio un paso institucional significativo al crear la Vicerrectoría de Inteligencia Digital, una unidad dedicada a integrar la inteligencia artificial y las tecnologías digitales en la docencia, investigación y extensión universitaria. Es una de las primeras vicerrectorías de este tipo en Latinoamérica.

La PUC es además la universidad que lidera CENIA a través del profesor Álvaro Soto del Departamento de Ciencia de la Computación, y aloja el Instituto de Ingeniería Matemática y Computacional (IMC). Con esta estructura institucional, la PUC se posiciona como la universidad con mayor presencia institucional en el ecosistema de IA chileno.',
            ],

            'instituto-de-ingenieria-matematica-y-computacional-imc-puc' => [
                'name'        => 'Instituto de Ingeniería Matemática y Computacional (IMC) — PUC',
                'url'         => 'https://imc.uc.cl',
                'focus_areas' => ['Modelamiento matemático', 'Machine Learning', 'Ciencias físicas + IA'],
                'key_facts'   => [
                    'Centro interdisciplinario de la PUC',
                    'Participación activa en CENIA',
                    'Investiga causalidad en modelos de IA',
                ],
                'description' => 'El IMC es un centro interdisciplinario de la Pontificia Universidad Católica de Chile que combina matemáticas, computación e ingeniería para abordar problemas complejos. Investiga la intersección entre ciencias físicas e IA, buscando desarrollar modelos de aprendizaje automático basados en relaciones causales, no solo correlaciones estadísticas.

Participa activamente en las líneas de investigación de CENIA, específicamente en el cruce entre IA y modelamiento físico-matemático. Su enfoque en la causalidad es relevante para desarrollar sistemas de IA más interpretables y confiables.',
            ],

            'nlhpc-laboratorio-nacional-de-computacion-de-alto-rendimiento' => [
                'name'        => 'NLHPC — Laboratorio Nacional de Computación de Alto Rendimiento',
                'url'         => 'https://www.nlhpc.cl',
                'focus_areas' => ['Supercomputación', 'HPC', 'Procesamiento de datos masivos'],
                'key_facts'   => [
                    'Infraestructura nacional de supercomputación',
                    'Alojado en la Universidad de Chile',
                    'Abierto a toda la comunidad académica chilena',
                    'Base para entrenamiento de modelos de IA',
                ],
                'description' => 'El NLHPC es la infraestructura nacional de supercomputación de Chile, alojada en la Universidad de Chile. Provee capacidad de procesamiento de alto rendimiento para investigación en IA, ciencias, simulación y procesamiento de datos masivos a toda la comunidad académica del país.

Es un recurso fundamental para que investigadores chilenos puedan entrenar modelos de IA y procesar grandes volúmenes de datos sin depender de infraestructura extranjera. Su existencia permite a universidades de regiones acceder a capacidad computacional de clase mundial.',
            ],

            'csiaa-centro-chileno-de-supercomputo-para-ia-aplicada' => [
                'name'        => 'CSIAA — Centro Chileno de Supercómputo para IA Aplicada',
                'url'         => 'https://csiaa.cl',
                'founded'     => '2026',
                'focus_areas' => ['Supercómputo', 'IA aplicada', 'Infraestructura cloud', 'Democratización tecnológica'],
                'key_facts'   => [
                    'US$7 millones de financiamiento CORFO',
                    '65 instituciones en el consorcio',
                    'Operativo desde 2026',
                    'Accesible para pymes, startups y academia',
                    'Incluye acompañamiento técnico y créditos de uso',
                ],
                'description' => 'El CSIAA es una iniciativa financiada por CORFO con 7 millones de dólares a través de la convocatoria "Desafíos de I+D: Desarrollo y Gestión de una Infraestructura Nacional de Supercómputo Especializada en Inteligencia Artificial". Su misión es ofrecer infraestructura avanzada de IA accesible, segura y adaptable para empresas de todos los tamaños, universidades e instituciones públicas a lo largo de Chile.

No es solo tecnología: incluye acompañamiento técnico, redes colaborativas, créditos de uso, infraestructura dedicada y espacios de aprendizaje. El consorcio integra 65 instituciones del ámbito público, privado y académico. La infraestructura comenzó a operar en 2026.',
            ],

            'imfd-instituto-milenio-fundamentos-de-los-datos' => [
                'name'        => 'IMFD — Instituto Milenio Fundamentos de los Datos',
                'url'         => 'https://imfd.cl',
                'focus_areas' => ['Fundamentos de datos', 'Transparencia algorítmica', 'IA ética', 'Desinformación', 'Seguridad'],
                'key_facts'   => [
                    'Centro Milenio de excelencia',
                    'Investigación sobre desinformación y IA',
                    'Investigadores: Bárbara Poblete, Marcelo Mendoza',
                ],
                'description' => 'El IMFD es un Centro de Investigación de Excelencia dedicado a los fundamentos científicos del manejo de datos. Investiga temas críticos como transparencia algorítmica, desinformación, seguridad en sistemas de IA y el uso justo de tecnologías.

Investigadores como Marcelo Mendoza (UTFSM) y Bárbara Poblete (U. de Chile) participan activamente en sus líneas de investigación sobre transparencia, seguridad y uso justo de tecnologías de IA. Sus resultados son relevantes para informar la política pública y el marco regulatorio de la IA en Chile.',
            ],

            'universidad-tecnica-federico-santa-maria' => [
                'name'        => 'Universidad Técnica Federico Santa María',
                'url'         => 'https://www.usm.cl',
                'location'    => 'Valparaíso',
                'region'      => 'Valparaíso',
                'focus_areas' => ['NLP', 'Minería de datos', 'Seguridad IA', 'Transparencia algorítmica'],
                'key_facts'   => [
                    'Universidad fundadora de CENIA',
                    'Participación en IMFD',
                    'Polo de IA regional fuera de Santiago',
                    'Investigador destacado: Marcelo Mendoza',
                ],
                'description' => 'La Universidad Técnica Federico Santa María es una de las cuatro universidades fundadoras de CENIA y participa activamente en el IMFD. Sus investigadores trabajan en NLP, minería de datos, transparencia algorítmica y seguridad en IA. Destaca la participación del profesor Marcelo Mendoza en investigaciones sobre transparencia y uso responsable de tecnologías de IA.

La UTFSM aporta una perspectiva regional importante al ecosistema de IA desde Valparaíso, siendo uno de los polos académicos más activos fuera de la Región Metropolitana.',
            ],

            'universidad-de-santiago-de-chile-usach' => [
                'name'        => 'Universidad de Santiago de Chile (USACH)',
                'url'         => 'https://portal.usach.cl',
                'focus_areas' => ['IA en minería', 'Machine Learning', 'Colaboración MIT', 'Ciencias planetarias'],
                'key_facts'   => [
                    'Alianza con MIT en IA aplicada',
                    'Investigación en IA para minería y ciencias planetarias',
                    'Publicaciones indexadas internacionales',
                    'Investigador destacado: Alberto Fernández (Minas)',
                ],
                'description' => 'La USACH investiga activamente en IA aplicada a minería, ciencias planetarias y educación. Mantiene una alianza con el MIT para desarrollo de IA en investigación de formación planetaria. El académico Alberto Fernández del Departamento de Ingeniería en Minas ha publicado investigaciones sobre el uso de técnicas de Machine Learning para procesar datos de perforación minera, construyendo modelos de predicción de propiedades del macizo rocoso.

Su enfoque en aplicaciones industriales y científicas de la IA complementa el trabajo más teórico de otras instituciones del ecosistema.',
            ],

            'universidad-autonoma-de-chile' => [
                'name'        => 'Universidad Autónoma de Chile',
                'url'         => 'https://www.uautonoma.cl',
                'focus_areas' => ['IA en educación', 'IA-Design', 'Innovación educativa', 'Blockchain'],
                'key_facts'   => [
                    'Proyecto IA-Design activo',
                    'Blockchain para certificaciones académicas',
                    'Alianzas internacionales en educación + IA',
                    'Ecosistema integral de innovación educativa',
                ],
                'description' => 'La Universidad Autónoma está implementando un ecosistema integral de innovación educativa sustentado en tres dimensiones: infraestructura tecnológica avanzada, desarrollo de capacidades humanas y alianzas estratégicas internacionales. Desarrolla proyectos como IA-Design y plataformas con blockchain para certificaciones.

Su enfoque está en integrar la IA como herramienta transversal en la experiencia educativa universitaria, combinando tecnologías emergentes con modelos pedagógicos innovadores.',
            ],

            'universidad-de-talca' => [
                'name'        => 'Universidad de Talca',
                'url'         => 'https://www.utalca.cl',
                'location'    => 'Talca',
                'region'      => 'Maule',
                'focus_areas' => ['IA en salud', 'IA en agricultura', 'Supercómputo', 'Investigación regional'],
                'key_facts'   => [
                    'Integrante del consorcio SCAI-Lab',
                    'Investigación en IA para salud y agricultura regional',
                    'Polo regional de IA en la Región del Maule',
                    'Formación de estudiantes en tecnologías emergentes',
                ],
                'description' => 'La Universidad de Talca integra el consorcio SCAI-Lab para supercómputo en IA, un proyecto adjudicado con recursos de CORFO. Investiga aplicaciones de IA en salud y agricultura en la zona central de Chile, aportando una perspectiva regional fuera del eje Santiago-Valparaíso.

Sus investigadores proyectan utilizar las capacidades del SCAI-Lab para tesis y formación de estudiantes en tecnologías emergentes, contribuyendo a descentralizar el desarrollo de capacidades en IA a lo largo del país.',
            ],

            'ednova' => [
                'name'        => 'Ednova',
                'director'    => 'Valeria Silva',
                'focus_areas' => ['Edtech', 'IA en educación', 'Aprendizaje personalizado'],
                'key_facts'   => [
                    'Referente LATAM en edtech + IA',
                    'Fundada por Valeria Silva',
                    'Algoritmos de adaptación del aprendizaje',
                    'Aparece en rankings de startups IA en LATAM 2026',
                ],
                'description' => 'Ednova es una startup chilena pionera en educación personalizada basada en inteligencia artificial. Liderada por Valeria Silva, desarrolla soluciones edtech que adaptan el aprendizaje a cada estudiante usando algoritmos de IA.

Ha sido destacada a nivel regional como uno de los referentes latinoamericanos en la intersección de IA y educación, apareciendo en múltiples rankings de startups IA en LATAM 2026.',
            ],

            'eclectic' => [
                'name'        => 'Eclectic',
                'url'         => 'https://www.eclectic.cl',
                'focus_areas' => ['Automatización', 'Productividad con IA', 'Operaciones lean'],
                'key_facts'   => [
                    'Modelo de crecimiento lean + IA',
                    'Automatización de procesos operativos',
                    'Equipo pequeño con alto apalancamiento en IA',
                ],
                'description' => 'Eclectic es una startup chilena que apuesta por un modelo de crecimiento basado en equipos pequeños con fuerte apalancamiento en inteligencia artificial. Su enfoque está en maximizar la eficiencia operativa usando IA para automatizar procesos y potenciar la productividad.

Demuestra que es posible escalar operaciones tecnológicas sin necesidad de grandes equipos, usando la IA como multiplicador de capacidades.',
            ],

            'ministerio-de-ciencia-tecnologia-conocimiento-e-innovacion' => [
                'name'        => 'Ministerio de Ciencia, Tecnología, Conocimiento e Innovación',
                'url'         => 'https://www.minciencia.gob.cl',
                'focus_areas' => ['Política pública IA', 'Regulación', 'Fomento CTCI', 'Data Centers'],
                'key_facts'   => [
                    'Impulsa proyecto de ley de IA (Boletín 16821-19, en el Senado)',
                    'Chile PotencIA: 600+ postulaciones, 162 proyectos seleccionados',
                    'Plan Nacional de Data Centers',
                    '22 data centers operativos + 28 proyectados',
                    'Certificación ChileValora en IA',
                ],
                'description' => 'El Ministerio de Ciencia es el organismo rector de la política de IA en Chile. Impulsa el proyecto de ley que regula los sistemas de inteligencia artificial (Boletín 16821-19), que fue aprobado por la Cámara de Diputados en octubre 2025 y se encuentra en segundo trámite en el Senado con urgencia suma.

También coordina el programa Chile PotencIA (Premio Nacional de IA) junto a Fundación País Digital, el Plan Nacional de Data Centers, y la certificación ChileValora en IA. Chile alberga 22 data centers operativos y se proyectan 28 más, posicionando al país como hub regional de infraestructura digital.',
            ],

            'corfo-corporacion-de-fomento-de-la-produccion' => [
                'name'        => 'CORFO — Corporación de Fomento de la Producción',
                'url'         => 'https://www.corfo.cl',
                'focus_areas' => ['Financiamiento', 'Innovación', 'Supercómputo', 'Startups'],
                'key_facts'   => [
                    'US$7 millones para infraestructura SCAI-Lab/CSIAA',
                    'Fondos para startups de IA',
                    'Programas de innovación tecnológica productiva',
                    'Proyecta mejoras +10% en productividad minera y agroindustrial',
                ],
                'description' => 'CORFO es la agencia del Estado chileno que financia la innovación y el desarrollo productivo. En IA, ha sido clave al financiar el programa de supercómputo SCAI-Lab con US$7 millones y fondos para startups tecnológicas.

Su vicepresidente ejecutivo José Miguel Benavente ha señalado que países con capacidades avanzadas de supercomputación han demostrado mejoras superiores al 10% en la productividad de sectores como minería y agroindustria, justificando la inversión pública en infraestructura de IA.',
            ],

            'fundacion-pais-digital' => [
                'name'        => 'Fundación País Digital',
                'url'         => 'https://paisdigital.org',
                'focus_areas' => ['Transformación digital', 'Premio Chile PotencIA', 'Divulgación', 'Inclusión digital'],
                'key_facts'   => [
                    'Co-organiza Premio Chile PotencIA con el Ministerio de Ciencia',
                    '600+ postulaciones recibidas en 2025',
                    '4 categorías: Startups, Pymes, Grandes Empresas, Organizaciones',
                    'Jurado mixto: sector público, academia y expertos internacionales',
                ],
                'description' => 'Fundación País Digital impulsa la transformación digital de Chile. Co-organiza junto al Ministerio de Ciencia el Premio Nacional de Inteligencia Artificial "Chile PotencIA", que en 2025 recibió más de 600 postulaciones en cuatro categorías: Startups, Pymes, Grandes Empresas y Organizaciones.

El proceso incluye evaluación técnica, ética y de impacto a cargo de un jurado mixto con representantes del sector público, academia y expertos internacionales, consolidándose como el principal reconocimiento a la innovación en IA del país.',
            ],

            'ai-adoption-initiative-aiai' => [
                'name'        => 'AI Adoption Initiative (AIAI)',
                'focus_areas' => ['Adopción de IA', 'Consultoría', 'Evaluación técnica'],
                'key_facts'   => [
                    'Evaluador experto del Premio Chile PotencIA',
                    'Acompañamiento para adopción de IA en organizaciones',
                    'Criterios técnicos y de impacto en evaluaciones',
                ],
                'description' => 'La AI Adoption Initiative (AIAI) es una iniciativa de acompañamiento experto para la adopción de inteligencia artificial en organizaciones chilenas. Participa como organismo experto en la evaluación del Premio Nacional de IA Chile PotencIA, aportando criterios técnicos y de impacto.

Su foco está en ayudar a organizaciones de distintos tamaños a implementar IA de forma práctica, ética y efectiva, reduciendo la brecha entre el potencial tecnológico y su aplicación concreta en el mundo real.',
            ],
        ];

        foreach ($updates as $slug => $data) {
            EcosystemActor::where('slug', $slug)->update($data);
        }

        $this->command->info('✓ ' . count($updates) . ' actores del ecosistema actualizados.');
    }
}
