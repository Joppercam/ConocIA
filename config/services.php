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
    
    // Añadir configuración para NewsAPI
    'newsapi' => [
        'key' => env('NEWSAPI_KEY'),
    ],
    
    // Añadir configuración para OpenAI
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model_name' => env('OPENAI_MODEL_NAME', 'gpt-4-turbo'),
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

];
