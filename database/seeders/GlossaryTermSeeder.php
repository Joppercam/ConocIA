<?php

namespace Database\Seeders;

use App\Models\GlossaryTerm;
use Illuminate\Database\Seeder;

class GlossaryTermSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            ['term' => 'Agente de IA',          'difficulty_level' => 'intermedio', 'definition' => 'Sistema de IA capaz de actuar de forma autónoma para cumplir objetivos, tomando decisiones y ejecutando acciones sin intervención humana constante.'],
            ['term' => 'Alucinación',            'difficulty_level' => 'basico',    'definition' => 'Cuando un modelo de IA genera información falsa o inventada presentándola como si fuera real.'],
            ['term' => 'Algoritmo',              'difficulty_level' => 'basico',    'definition' => 'Conjunto de instrucciones paso a paso que una computadora sigue para resolver un problema o completar una tarea.'],
            ['term' => 'API',                    'difficulty_level' => 'intermedio', 'definition' => 'Interfaz que permite a diferentes programas comunicarse entre sí. En IA, las APIs permiten integrar modelos en aplicaciones.'],
            ['term' => 'Aprendizaje automático', 'difficulty_level' => 'basico',    'definition' => 'Rama de la IA donde los sistemas aprenden patrones a partir de datos en lugar de ser programados explícitamente.', 'explanation' => 'También conocido como Machine Learning, es la base de la mayoría de las aplicaciones de IA actuales. En lugar de escribir reglas explícitas, se le muestran al sistema miles o millones de ejemplos y éste aprende a generalizar.'],
            ['term' => 'Aprendizaje por refuerzo', 'difficulty_level' => 'intermedio', 'definition' => 'Método donde un agente de IA aprende a tomar decisiones probando acciones y recibiendo recompensas o penalizaciones.'],
            ['term' => 'Aprendizaje profundo',   'difficulty_level' => 'intermedio', 'definition' => 'Tipo de aprendizaje automático que usa redes neuronales con muchas capas para encontrar patrones complejos en grandes volúmenes de datos.', 'explanation' => 'También llamado Deep Learning, es la tecnología detrás de reconocimiento de imágenes, voz y los modelos de lenguaje actuales.'],
            ['term' => 'Atención',               'difficulty_level' => 'avanzado',  'definition' => 'Técnica que permite a un modelo de IA enfocarse en las partes más relevantes de los datos de entrada al generar una respuesta.'],
            ['term' => 'Chatbot',                'difficulty_level' => 'basico',    'definition' => 'Programa que simula una conversación humana usando procesamiento de lenguaje natural o reglas predefinidas.'],
            ['term' => 'Clasificación',          'difficulty_level' => 'basico',    'definition' => 'Tarea de IA que consiste en asignar una categoría o etiqueta a un dato de entrada, como identificar si un email es spam o no.'],
            ['term' => 'Conjunto de datos',      'difficulty_level' => 'basico',    'definition' => 'Colección organizada de datos usada para entrenar, validar o probar un modelo de IA.', 'explanation' => 'También llamado Dataset. La calidad y cantidad de datos de entrenamiento es uno de los factores más determinantes en el rendimiento de un modelo de IA.'],
            ['term' => 'Deepfake',               'difficulty_level' => 'basico',    'definition' => 'Contenido multimedia (video, audio, imagen) generado o manipulado por IA para simular personas reales de forma convincente.'],
            ['term' => 'Embeddings',             'difficulty_level' => 'avanzado',  'definition' => 'Representaciones numéricas de palabras, frases o documentos que capturan su significado semántico en un espacio matemático.'],
            ['term' => 'Entrenamiento',          'difficulty_level' => 'basico',    'definition' => 'Proceso mediante el cual un modelo de IA ajusta sus parámetros internos usando datos para aprender a realizar una tarea.'],
            ['term' => 'Fine-tuning',            'difficulty_level' => 'intermedio', 'definition' => 'Proceso de ajustar un modelo de IA previamente entrenado con datos específicos para especializarlo en una tarea particular.'],
            ['term' => 'GPT',                    'difficulty_level' => 'basico',    'definition' => 'Generative Pre-trained Transformer. Familia de modelos de lenguaje desarrollados por OpenAI que generan texto a partir de instrucciones.'],
            ['term' => 'IA Generativa',          'difficulty_level' => 'basico',    'definition' => 'Sistemas de IA capaces de crear contenido nuevo como texto, imágenes, audio o video a partir de instrucciones o datos de entrada.'],
            ['term' => 'Inferencia',             'difficulty_level' => 'intermedio', 'definition' => 'Momento en que un modelo de IA ya entrenado procesa datos nuevos para generar predicciones o respuestas.'],
            ['term' => 'LLM',                    'difficulty_level' => 'intermedio', 'definition' => 'Large Language Model. Modelo de lenguaje de gran escala entrenado con enormes cantidades de texto para comprender y generar lenguaje humano.'],
            ['term' => 'Modelo de difusión',     'difficulty_level' => 'avanzado',  'definition' => 'Tipo de modelo generativo que crea imágenes añadiendo y removiendo ruido progresivamente, usado en herramientas como Stable Diffusion.'],
            ['term' => 'NLP',                    'difficulty_level' => 'basico',    'definition' => 'Procesamiento de Lenguaje Natural. Campo de la IA dedicado a que las máquinas entiendan, interpreten y generen lenguaje humano.'],
            ['term' => 'Parámetros',             'difficulty_level' => 'intermedio', 'definition' => 'Valores numéricos internos de un modelo de IA que se ajustan durante el entrenamiento. Más parámetros generalmente implican mayor capacidad.'],
            ['term' => 'Prompt',                 'difficulty_level' => 'basico',    'definition' => 'Instrucción o texto de entrada que se le da a un modelo de IA para obtener una respuesta o resultado específico.'],
            ['term' => 'Prompt Engineering',     'difficulty_level' => 'intermedio', 'definition' => 'Técnica de diseñar y optimizar las instrucciones que se le dan a un modelo de IA para obtener mejores resultados.'],
            ['term' => 'RAG',                    'difficulty_level' => 'avanzado',  'definition' => 'Retrieval-Augmented Generation. Técnica que combina búsqueda de información en bases de datos con generación de texto por IA para dar respuestas más precisas y actualizadas.'],
            ['term' => 'Red neuronal',           'difficulty_level' => 'intermedio', 'definition' => 'Modelo computacional inspirado en el cerebro humano, compuesto por capas de nodos interconectados que procesan información.'],
            ['term' => 'Sesgo algorítmico',      'difficulty_level' => 'basico',    'definition' => 'Tendencia de un sistema de IA a producir resultados sistemáticamente injustos debido a datos de entrenamiento desequilibrados o diseño inadecuado.'],
            ['term' => 'Token',                  'difficulty_level' => 'intermedio', 'definition' => 'Unidad mínima de texto que procesa un modelo de lenguaje. Puede ser una palabra, parte de una palabra o un carácter de puntuación.'],
            ['term' => 'Transformer',            'difficulty_level' => 'avanzado',  'definition' => 'Arquitectura de red neuronal que revolucionó el procesamiento de lenguaje natural. Es la base de modelos como GPT, BERT y Claude.'],
            ['term' => 'Visión por computadora', 'difficulty_level' => 'basico',    'definition' => 'Campo de la IA que permite a las máquinas interpretar y entender imágenes y videos del mundo real.'],
        ];

        foreach ($terms as $data) {
            $slug   = \Illuminate\Support\Str::slug($data['term']);
            $letter = strtoupper(mb_substr($data['term'], 0, 1));

            GlossaryTerm::firstOrCreate(['slug' => $slug], array_merge($data, [
                'slug'   => $slug,
                'letter' => $letter,
            ]));
        }
    }
}
