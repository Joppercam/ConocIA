<?php

return [
    'feeds' => [
        'news' => [
            'items' => [\App\Models\News::class, 'getAllFeedItems'],
            'url' => '/feed',
            'title' => 'ConocIA — Noticias de Inteligencia Artificial',
            'description' => 'Las últimas noticias sobre IA, Machine Learning y tecnología en español.',
            'language' => 'es-ES',
            'image' => '',
            'format' => 'rss',
            'view' => 'feed::rss',
            'type' => 'application/rss+xml',
            'contentType' => '',
        ],
    ],
];
