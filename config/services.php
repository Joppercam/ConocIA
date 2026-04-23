<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    
    'newsapi' => [
        'key' => env('NEWSAPI_KEY'),
    ],

    'pexels' => [
        'api_key' => env('PEXELS_API_KEY'),
    ],

    'guardian' => [
        'api_key' => env('GUARDIAN_API_KEY'),
    ],

    'openai' => [
        'api_key'         => env('OPENAI_API_KEY'),
        'organization'    => env('OPENAI_ORGANIZATION'),
        'model'           => env('OPENAI_MODEL_NAME', 'gpt-4.1'),
        'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 60),
    ],
    
    // Google Gemini — fetch noticias (Search Grounding) + tareas batch
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model'   => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    ],

    // Anthropic Claude — contenido editorial Profundiza (Análisis, Conceptos, Estado del Arte)
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model'   => env('ANTHROPIC_MODEL', 'claude-sonnet-4-5'),
    ],

    
    'text_analysis' => [
        'api_key' => env('TEXT_ANALYSIS_API_KEY'),
        'api_url' => env('TEXT_ANALYSIS_API_URL'),
    ],

    'twitter' => [
        'consumer_key' => env('TWITTER_CONSUMER_KEY'),
        'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
        'access_token' => env('TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
        'bearer_token' => env('TWITTER_BEARER_TOKEN'),
    ],

    'youtube' => [
        'key' => env('YOUTUBE_API_KEY'),
    ],

    'vimeo' => [
        'client_id' => env('VIMEO_CLIENT_ID'),
        'client_secret' => env('VIMEO_CLIENT_SECRET'),
        'redirect' => env('VIMEO_REDIRECT_URI'),
        'access_token' => env('VIMEO_ACCESS_TOKEN'),
    ],

    'dailymotion' => [
        'key' => env('DAILYMOTION_API_KEY'),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

];
