<?php

namespace App\Services;

class DifficultyClassifierService
{
    // Términos técnicos/investigación → avanzado
    private const ADVANCED = [
        'transformer', 'backpropagation', 'gradient', 'fine-tuning', 'finetuning',
        'rlhf', 'embedding', 'tokenizer', 'perplexity', 'benchmark', 'benchmarks',
        'attention mechanism', 'hiperparámetro', 'hiperparametro', 'cuantización',
        'cuantizacion', 'lora', 'rag', 'retrieval', 'latent space', 'vector database',
        'parámetros del modelo', 'parametros del modelo', 'arquitectura del modelo',
        'entrenamiento del modelo', 'datos de entrenamiento', 'conjunto de datos',
        'dataset', 'paper', 'arxiv', 'preprint', 'publicación científica',
        'publicacion cientifica', 'modelo de lenguaje grande', 'large language model',
        'multimodal', 'inferencia distribuida', 'gpu cluster', 'tpu', 'vram',
        'tokens por segundo', 'context window', 'ventana de contexto',
        'función de pérdida', 'funcion de perdida', 'red neuronal profunda',
        'deep learning técnico', 'reinforcement learning', 'aprendizaje por refuerzo',
        'difusión estable', 'stable diffusion', 'gan', 'variational autoencoder',
    ];

    // Términos divulgativos/accesibles → básico
    private const BASIC = [
        'qué es la inteligencia artificial', 'qué es la ia', 'que es la ia',
        'cómo funciona', 'como funciona', 'para todos', 'sin tecnicismos',
        'en palabras simples', 'explicado fácil', 'explicado facil',
        'introducción a', 'introduccion a', 'guía para principiantes',
        'guia para principiantes', 'primer paso', 'primeros pasos',
        'uso cotidiano', 'vida diaria', 'día a día', 'dia a dia',
        'herramienta para', 'aplicación práctica', 'aplicacion practica',
        'asistente virtual', 'chatbot para', 'inteligencia artificial en el hogar',
        'ia en tu celular', 'ia en tu teléfono', 'ia en tu telefono',
    ];

    public static function classify(string $title, string $content = ''): string
    {
        $haystack = mb_strtolower($title . ' ' . strip_tags($content));
        $score    = 0;

        foreach (self::ADVANCED as $term) {
            if (str_contains($haystack, $term)) {
                $score += 2;
            }
        }

        foreach (self::BASIC as $term) {
            if (str_contains($haystack, $term)) {
                $score -= 2;
            }
        }

        if ($score >= 4) {
            return 'avanzado';
        }

        if ($score <= -2) {
            return 'basico';
        }

        return 'intermedio';
    }
}
