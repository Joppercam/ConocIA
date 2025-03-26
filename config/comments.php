<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de validación de comentarios
    |--------------------------------------------------------------------------
    |
    | Esta configuración permite personalizar las reglas para la validación
    | automática de comentarios en el portal de noticias.
    |
    */

    // Palabras prohibidas adicionales
    'banned_words' => [
        // Puedes agregar palabras específicas para tu contexto aquí
    ],

    // Dominios prohibidos adicionales
    'banned_domains' => [
        // Puedes agregar dominios específicos para bloquear aquí
    ],

    // Límites de caracteres
    'min_length' => 5,
    'max_length' => 1000,

    // Porcentaje máximo de mayúsculas permitido (0.0 - 1.0)
    'max_uppercase_ratio' => 0.7,

    // Número máximo de enlaces permitidos
    'max_links' => 2,

    // Número máximo de signos de exclamación o interrogación
    'max_punctuation' => 5,

    // Si es true, rechaza automáticamente los comentarios que no cumplen con las normas
    // Si es false, los deja en estado pendiente para revisión manual
    'auto_reject' => false,

    // Habilitar análisis avanzado de texto
    'enable_advanced_analysis' => env('COMMENTS_ENABLE_ADVANCED_ANALYSIS', false),

    // Umbral de toxicidad (0.0 - 1.0)
    'toxicity_threshold' => 0.7,

    // Umbral de spam (0.0 - 1.0)
    'spam_threshold' => 0.7,
];