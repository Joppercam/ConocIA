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

    'search_console' => [
        'site_url' => env('GOOGLE_SEARCH_CONSOLE_SITE_URL'),
        'oauth_client_id' => env('GOOGLE_SEARCH_CONSOLE_CLIENT_ID'),
        'oauth_client_secret' => env('GOOGLE_SEARCH_CONSOLE_CLIENT_SECRET'),
        'oauth_refresh_token' => env('GOOGLE_SEARCH_CONSOLE_REFRESH_TOKEN'),
        'client_email' => env('GOOGLE_SEARCH_CONSOLE_CLIENT_EMAIL'),
        'private_key' => env('GOOGLE_SEARCH_CONSOLE_PRIVATE_KEY'),
        'token_uri' => env('GOOGLE_SEARCH_CONSOLE_TOKEN_URI', 'https://oauth2.googleapis.com/token'),
        'scope' => env('GOOGLE_SEARCH_CONSOLE_SCOPE', 'https://www.googleapis.com/auth/webmasters.readonly'),
    ],

    'editorial_agent' => [
        'auto_news_enabled' => env('EDITORIAL_AGENT_AUTO_NEWS_ENABLED', true),
        'auto_news_daily_limit' => env('EDITORIAL_AGENT_AUTO_NEWS_DAILY_LIMIT', 3),
        'auto_news_max_pending' => env('EDITORIAL_AGENT_AUTO_NEWS_MAX_PENDING', 6),
        'auto_news_days' => env('EDITORIAL_AGENT_AUTO_NEWS_DAYS', 2),
        'auto_publish' => env('EDITORIAL_AGENT_AUTO_PUBLISH', false),
        'auto_publish_sensitive' => env('EDITORIAL_AGENT_AUTO_PUBLISH_SENSITIVE', false),
        'sensitive_terms' => [
            'salud',
            'medicina',
            'hospital',
            'farmaco',
            'fármaco',
            'regulacion',
            'regulación',
            'ley',
            'politica publica',
            'política pública',
            'gobierno',
            'propiedad intelectual',
            'derechos de autor',
            'privacidad',
            'seguridad',
            'ciberseguridad',
            'demanda',
            'acusacion',
            'acusación',
            'fraude',
        ],
        'auto_news_topics' => [
            ['topic' => 'noticias recientes de inteligencia artificial con impacto empresarial', 'category' => 'inteligencia-artificial', 'priority' => 'high'],
            ['topic' => 'inteligencia artificial en Chile, regulación, educación, empresas o sector público', 'category' => 'regulacion-de-ia', 'priority' => 'high'],
            ['topic' => 'nuevos modelos de IA, agentes, asistentes o avances en IA generativa', 'category' => 'ia-generativa', 'priority' => 'high'],
            ['topic' => 'papers o investigaciones universitarias recientes sobre inteligencia artificial aplicada', 'category' => 'investigacion', 'priority' => 'medium'],
            ['topic' => 'startups de inteligencia artificial con financiamiento, producto o adopción relevante', 'category' => 'startups-de-ia', 'priority' => 'medium'],
            ['topic' => 'inteligencia artificial en salud, medicina, hospitales o descubrimiento de fármacos', 'category' => 'ia-en-salud', 'priority' => 'medium'],
        ],
    ],

];
