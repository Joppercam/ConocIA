<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Illuminate\Support\Str;
use App\Models\Category;

class NewsSeeder extends Seeder
{
    public function run()
    {

        // Primero, asegúrate de que las categorías existen
        $iaCategory = Category::firstOrCreate(['name' => 'Inteligencia Artificial', 'slug' => 'inteligencia-artificial']);
        $researchCategory = Category::firstOrCreate(['name' => 'Investigación', 'slug' => 'investigacion']);
        $techCategory = Category::firstOrCreate(['name' => 'Tecnología', 'slug' => 'tecnologia']);
        $roboticsCategory = Category::firstOrCreate(['name' => 'Robótica', 'slug' => 'robotica']);
        $cyberCategory = Category::firstOrCreate(['name' => 'Ciberseguridad', 'slug' => 'ciberseguridad']);
        $innovationCategory = Category::firstOrCreate(['name' => 'Innovación', 'slug' => 'innovacion']);
        $educationCategory = Category::firstOrCreate(['name' => 'Educación', 'slug' => 'educacion']);
        
                
        // Noticia 1 - Hero
        News::create([
            'title' => 'La revolución de los modelos multimodales: Integrando visión, lenguaje y audio',
            'slug' => Str::slug('La revolución de los modelos multimodales: Integrando visión, lenguaje y audio'),
            'excerpt' => 'Los nuevos modelos de IA están rompiendo barreras entre diferentes formas de percepción, acercándonos a la comprensión genuinamente humana.',
            'content' => '<p>Los modelos multimodales representan el siguiente gran salto en la evolución de la inteligencia artificial, permitiendo a los sistemas procesar e integrar información de múltiples formas de percepción simultáneamente.</p><p>A diferencia de los modelos tradicionales que se especializan en un solo tipo de datos, como texto o imágenes, los sistemas multimodales pueden entender la relación entre palabras, imágenes y sonidos, similar a cómo los humanos percibimos el mundo.</p><h3>Beneficios de la multimodalidad</h3><p>Esta capacidad para entender múltiples modalidades ofrece ventajas significativas:</p><ul><li>Comprensión más profunda del contexto</li><li>Interpretación más precisa de situaciones complejas</li><li>Interacciones más naturales con los usuarios</li><li>Mayor accesibilidad para personas con diferentes capacidades</li></ul><p>Los investigadores están observando que estos modelos pueden realizar tareas que serían imposibles con enfoques unimodales, como describir detalladamente el contenido de una imagen, responder preguntas basadas en información visual, o incluso entender emociones combinando señales visuales y auditivas.</p>',
            'image' => 'hero-news-1.jpg',
            'category_id' => $iaCategory->id,
            'author' => 'María Rodríguez',
            'views' => 2500,
            'tags' => 'multimodal,visión artificial,procesamiento de lenguaje,audio AI',
            'is_published' => true,
            'featured' => true
        ]);

        // Noticia 2 - Hero
        News::create([
            'title' => 'Investigadores crean IA que predice compuestos farmacéuticos con precisión del 94%',
            'slug' => Str::slug('Investigadores crean IA que predice compuestos farmacéuticos con precisión del 94%'),
            'excerpt' => 'Un nuevo algoritmo desarrollado por científicos españoles podría revolucionar el descubrimiento de medicamentos, reduciendo años de investigación.',
            'content' => '<p>Un equipo de investigadores españoles ha logrado un avance significativo en la aplicación de la inteligencia artificial al descubrimiento de fármacos, desarrollando un algoritmo capaz de predecir compuestos farmacéuticos efectivos con una precisión sin precedentes del 94%.</p><p>Este avance podría transformar radicalmente el proceso de desarrollo de medicamentos, que tradicionalmente requiere décadas de investigación y miles de millones de euros en inversión.</p><h3>Metodología innovadora</h3><p>El sistema utiliza una combinación de aprendizaje profundo y modelado molecular para analizar la estructura química de posibles compuestos y predecir su efectividad contra enfermedades específicas. La IA evalúa millones de combinaciones posibles en cuestión de días, una tarea que llevaría años a los métodos tradicionales.</p><p>El Dr. Carlos Fernández, líder del equipo de investigación, explica: "Nuestro algoritmo no solo identifica compuestos prometedores, sino que también puede predecir posibles efectos secundarios y sugerir modificaciones para mejorar la eficacia y reducir la toxicidad".</p>',
            'image' => 'hero-news-2.jpg',
            'category_id' => $researchCategory->id,
            'author' => 'Juan Pérez',
            'views' => 4700,
            'tags' => 'farmacología,descubrimiento de fármacos,medicina,algoritmos',
            'is_published' => true,
            'featured' => true
        ]);

        // Noticia 3 - Hero
        News::create([
            'title' => 'Computación cuántica alcanza hito histórico: Error de cálculo inferior al 0.01%',
            'slug' => Str::slug('Computación cuántica alcanza hito histórico: Error de cálculo inferior al 0.01%'),
            'excerpt' => 'IBM ha anunciado un avance monumental en la corrección de errores cuánticos, abriendo la puerta a aplicaciones comerciales prácticas.',
            'content' => '<p>IBM ha logrado un avance revolucionario en el campo de la computación cuántica al desarrollar un sistema de corrección de errores que reduce la tasa de error a menos del 0.01%, un logro que marca un antes y un después en esta tecnología emergente.</p><p>Este hito, considerado durante mucho tiempo como uno de los mayores obstáculos para la adopción práctica de la computación cuántica, acerca esta tecnología a aplicaciones comerciales que podrían transformar sectores como la farmacéutica, la criptografía y el diseño de materiales.</p><h3>Superando la fragilidad cuántica</h3><p>Los sistemas cuánticos son inherentemente frágiles y susceptibles a errores debido a la interferencia del entorno, un fenómeno conocido como "decoherencia". El nuevo sistema de IBM emplea un enfoque innovador utilizando "qubits lógicos" que distribuyen la información a través de múltiples qubits físicos, permitiendo detectar y corregir errores sin perturbar el estado cuántico.</p><p>"Este avance es comparable al momento en que los primeros transistores comenzaron a funcionar de manera confiable," afirma la Dra. Elena Gómez, investigadora principal de IBM. "Estamos al borde de una nueva era en la computación".</p>',
            'image' => 'hero-news-3.jpg',
            'category_id' => $techCategory->id,
            'author' => 'Carlos Martínez',
            'views' => 5800,
            'tags' => 'computación cuántica,IBM,qubits,corrección de errores',
            'is_published' => true,
            'featured' => true
        ]);

        // Noticias secundarias
        News::create([
            'title' => 'Robots humanoides comienzan pruebas en entornos hospitalarios',
            'slug' => Str::slug('Robots humanoides comienzan pruebas en entornos hospitalarios'),
            'excerpt' => 'Nuevos robots asistenciales muestran resultados prometedores en pruebas piloto realizadas en cinco hospitales europeos.',
            'content' => '<p>Una nueva generación de robots humanoides ha comenzado a operar en cinco hospitales europeos como parte de un programa piloto diseñado para evaluar su efectividad en entornos sanitarios reales. Estos robots, equipados con inteligencia artificial avanzada, están diseñados para asistir al personal médico en tareas rutinarias, permitiéndoles concentrarse en aspectos más complejos del cuidado del paciente.</p><p>Los robots pueden transportar suministros, ayudar a pacientes con movilidad reducida, y monitorear signos vitales. También están programados para interactuar socialmente con pacientes, proporcionando compañía y estimulación cognitiva particularmente valiosa para pacientes de edad avanzada.</p><p>Los resultados preliminares muestran una recepción positiva tanto por parte del personal médico como de los pacientes. "Los robots complementan perfectamente a nuestro equipo humano", explica la Dra. Sofia Berger, coordinadora del proyecto. "No estamos reemplazando el contacto humano, sino liberando tiempo para que nuestros profesionales puedan ofrecer una atención más personalizada".</p>',
            'image' => 'news-secondary-1.jpg',
            'category_id' => $roboticsCategory->id,
            'author' => 'Lucía González',
            'views' => 1800,
            'tags' => 'robótica,salud,hospitales,asistencia',
            'is_published' => true,
            'featured' => false
        ]);

        News::create([
            'title' => 'Nuevo ransomware utiliza IA para evadir sistemas de detección',
            'slug' => Str::slug('Nuevo ransomware utiliza IA para evadir sistemas de detección'),
            'excerpt' => 'Expertos advierten sobre una nueva generación de amenazas que utilizan algoritmos avanzados para adaptarse a defensas.',
            'content' => '<p>Un sofisticado ransomware que emplea inteligencia artificial para evadir sistemas de seguridad ha sido detectado atacando organizaciones en múltiples sectores. Apodado "Chameleon" por su capacidad de adaptación, este malware representa una preocupante evolución en el panorama de amenazas cibernéticas.</p><p>A diferencia del ransomware tradicional, Chameleon utiliza algoritmos de aprendizaje automático para analizar los sistemas de defensa y alterar su comportamiento en tiempo real, haciendo extremadamente difícil su detección mediante métodos convencionales. El malware puede permanecer inactivo durante largos períodos, estudiando patrones de tráfico y comportamiento del sistema antes de cifrar archivos.</p><p>"Estamos presenciando el nacimiento de una nueva generación de amenazas inteligentes", advierte Miguel Sánchez, investigador de ciberseguridad. "Este no es solo un programa malicioso, sino un adversario que aprende y se adapta".</p><p>Los expertos recomiendan un enfoque multicapa para la protección, incluyendo sistemas de detección de anomalías basados en IA, copias de seguridad aisladas, y capacitación continua del personal sobre nuevas técnicas de phishing utilizadas para la distribución inicial del malware.</p>',
            'image' => 'news-secondary-2.jpg',
            'category_id' => $cyberCategory->id,
            'author' => 'Alejandro Ramos',
            'views' => 2100,
            'tags' => 'ransomware,ciberseguridad,malware,amenazas,IA',
            'is_published' => true,
            'featured' => false
        ]);

        // Noticias recientes
        News::create([
            'title' => 'Las 10 aplicaciones más innovadoras de IA en 2025',
            'slug' => Str::slug('Las 10 aplicaciones más innovadoras de IA en 2025'),
            'excerpt' => 'Un análisis detallado de las aplicaciones de inteligencia artificial que están revolucionando diferentes industrias este año.',
            'content' => '<p>El 2025 está siendo testigo de avances significativos en aplicaciones de IA que están transformando radicalmente diversos sectores industriales y aspectos de nuestra vida cotidiana. Estas son las diez aplicaciones más innovadoras que están liderando esta revolución:</p><h3>1. Asistentes médicos de diagnóstico</h3><p>Sistemas capaces de analizar imágenes médicas con una precisión superior a la de especialistas humanos, reduciendo drásticamente los tiempos de diagnóstico y mejorando la detección temprana de enfermedades.</p><h3>2. Traductores neurales en tiempo real</h3><p>Dispositivos de traducción instantánea que permiten conversaciones fluidas entre personas que hablan diferentes idiomas, eliminando barreras lingüísticas en contextos empresariales y turísticos.</p><h3>3. Asesores financieros personalizados</h3><p>Aplicaciones que analizan patrones de gasto individuales, ofreciendo asesoramiento financiero personalizado y optimizando inversiones según objetivos específicos.</p><h3>4. Sistemas de enseñanza adaptativa</h3><p>Plataformas educativas que se adaptan dinámicamente al ritmo de aprendizaje y necesidades específicas de cada estudiante, revolucionando la educación personalizada.</p><h3>5. Compañeros virtuales para personas mayores</h3><p>Asistentes que combinan monitorización de salud con interacción social, ayudando a combatir la soledad y proporcionando asistencia práctica para personas de edad avanzada.</p><h3>6. Simuladores climáticos ultra-precisos</h3><p>Modelos que predicen cambios climáticos localizados con precisión sin precedentes, permitiendo la planificación urbana y agrícola con meses de antelación.</p><h3>7. Diseñadores creativos IA</h3><p>Herramientas que colaboran con diseñadores humanos en la creación de productos, edificios y espacios urbanos optimizados para sostenibilidad y experiencia de usuario.</p><h3>8. Sistemas de transporte predictivo</h3><p>Redes de transporte público que ajustan rutas y frecuencias en tiempo real basándose en predicciones de demanda, reduciendo tiempos de espera y congestión.</p><h3>9. Asistentes legales especializados</h3><p>Plataformas que democratizan el acceso a asesoramiento legal, analizando documentos complejos y ofreciendo orientación personalizada a costes accesibles.</p><h3>10. Gemelos digitales personales</h3><p>Representaciones virtuales que aprenden continuamente de nuestros hábitos y preferencias, simplificando interacciones con servicios digitales y protegiendo nuestra privacidad online.</p>',
            'image' => 'trending-1.jpg',
            'category_id' => $techCategory->id,
            'author' => 'Roberto Vázquez',
            'views' => 4500,
            'tags' => 'inteligencia artificial,innovación,tecnología,aplicaciones',
            'is_published' => true,
            'featured' => false
        ]);
        
        // Agrega más noticias recientes y populares según necesites...
        
        // Elon Musk chip
        News::create([
            'title' => 'Elon Musk anuncia nuevo chip cerebral con capacidades mejoradas',
            'slug' => Str::slug('Elon Musk anuncia nuevo chip cerebral con capacidades mejoradas'),
            'excerpt' => 'Neuralink presenta la nueva generación de interfaces cerebro-computadora con funcionalidades que prometen revolucionar el tratamiento de trastornos neurológicos.',
            'content' => '<p>Elon Musk ha presentado la última versión del implante cerebral de Neuralink, una interfaz cerebro-computadora significativamente más avanzada que sus predecesoras y que promete expandir dramáticamente las posibilidades de tratamiento para diversas condiciones neurológicas.</p><p>El nuevo dispositivo, denominado "Neuralink N2", es aproximadamente un 40% más pequeño que la versión anterior, mientras que contiene el doble de electrodos, permitiendo una comunicación mucho más precisa con el tejido cerebral. El chip también incorpora un nuevo sistema de inserción minimamente invasivo que reduce significativamente el trauma durante la implantación.</p><h3>Aplicaciones médicas revolucionarias</h3><p>"Esta tecnología tiene el potencial de restaurar la movilidad en personas con parálisis, devolver la visión a pacientes con ceguera adquirida, y ofrecer nuevos tratamientos para condiciones como Parkinson, epilepsia y lesiones cerebrales traumáticas", explicó Musk durante la presentación.</p><p>Los primeros ensayos clínicos en humanos con el dispositivo N2 están programados para comenzar en los próximos meses, centrándose inicialmente en pacientes con lesiones medulares severas. La tecnología permitiría a estos pacientes controlar dispositivos externos como teléfonos, computadoras e incluso prótesis robóticas directamente con su actividad cerebral.</p><p>Musk enfatizó que el enfoque actual de Neuralink está firmemente en aplicaciones médicas, aunque no ocultó su visión a largo plazo de crear una simbiosis humano-IA que permita a las personas mantenerse relevantes en una era de inteligencia artificial avanzada.</p>',
            'image' => 'trending-2.jpg',
            'category_id' => $innovationCategory->id, 
            'author' => 'Elena Martínez',
            'views' => 3800,
            'tags' => 'Neuralink,cerebro,implantes,Elon Musk,neurociencia',
            'is_published' => true,
            'featured' => false
        ]);
        
        // Educación
        News::create([
            'title' => 'Cómo la IA está revolucionando la educación en Latinoamérica',
            'slug' => Str::slug('Cómo la IA está revolucionando la educación en Latinoamérica'),
            'excerpt' => 'Nuevas plataformas educativas impulsadas por inteligencia artificial están transformando la forma en que se aprende en países de América Latina.',
            'content' => '<p>Una nueva generación de plataformas educativas impulsadas por inteligencia artificial está transformando radicalmente los sistemas educativos en América Latina, democratizando el acceso a educación de calidad y adaptándose a las necesidades específicas de cada estudiante.</p><p>Estas iniciativas, muchas desarrolladas por emprendedores locales que entienden los desafíos únicos de la región, están ayudando a superar limitaciones históricas como la escasez de docentes especializados en áreas rurales, la falta de recursos educativos actualizados, y la necesidad de adaptarse a diversos contextos culturales y lingüísticos.</p><h3>Personalización a escala</h3><p>La plataforma colombiana "AprendeIA", implementada ya en más de 500 escuelas, utiliza algoritmos avanzados para evaluar continuamente el progreso de cada estudiante, identificando áreas de dificultad y ajustando el contenido en tiempo real para optimizar el aprendizaje.</p><p>"Hemos observado mejoras del 35% en comprensión matemática y 28% en lectura crítica en escuelas que han implementado el sistema por al menos un año", explica Sofía Restrepo, cofundadora de la startup. "La verdadera innovación es poder ofrecer educación personalizada a escala, algo que simplemente no sería posible con métodos tradicionales".</p><h3>Superando la brecha digital</h3><p>Un aspecto crucial de estas iniciativas es su capacidad para funcionar con infraestructura tecnológica limitada. La plataforma mexicana "EduconectaMX" ha desarrollado un sistema que puede operar offline con actualizaciones periódicas, permitiendo su implementación en comunidades con conectividad intermitente.</p><p>Estas tecnologías también están transformando la formación docente. "Los profesores reciben retroalimentación detallada sobre el desempeño del grupo y recomendaciones específicas para intervenciones pedagógicas", explica Luis Ramírez, coordinador de tecnología educativa del Ministerio de Educación de Uruguay, país que ha sido pionero en la adopción de estas tecnologías.</p><p>Los expertos coinciden en que estas plataformas no buscan reemplazar a los docentes sino potenciar su labor, permitiéndoles concentrarse en los aspectos más humanos y creativos de la enseñanza mientras la IA se encarga de la personalización y el análisis de datos.</p>',
            'image' => 'trending-3.jpg',
            'category_id' => $educationCategory->id, 
            'author' => 'Gabriela Hernández',
            'views' => 3200,
            'tags' => 'educación,Latinoamérica,aprendizaje,tecnología educativa',
            'is_published' => true,
            'featured' => false
        ]);
    }
}