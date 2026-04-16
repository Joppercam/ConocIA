<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Key
    |--------------------------------------------------------------------------
    |
    | Obtén tu clave gratuita en: https://aistudio.google.com/app/apikey
    | El tier gratuito incluye: 15 req/min, 1M tokens/día con gemini-2.0-flash
    |
    */

    'api_key' => env('GEMINI_API_KEY'),

    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),

    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 30),

];
