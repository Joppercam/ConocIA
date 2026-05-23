<?php

namespace App\Http\Controllers;

class ComingSoonController extends Controller
{
    private array $pages = [
        'ia-para-todos' => [
            'title'       => 'IA para Todos',
            'subtitle'    => 'Curso gratuito de alfabetización en inteligencia artificial',
            'description' => 'Un programa educativo estructurado y gratuito diseñado para ciudadanos sin formación técnica. Aprende qué es la IA, cómo te afecta y qué derechos tienes frente a decisiones automatizadas.',
            'icon'        => 'fa-graduation-cap',
        ],
        'glosario' => [
            'title'       => 'Glosario de IA',
            'subtitle'    => 'Términos clave explicados de forma simple',
            'description' => 'Un diccionario vivo de inteligencia artificial en español. Cada término con explicación accesible, ejemplo práctico y nivel de complejidad.',
            'icon'        => 'fa-book-open',
        ],
        'ecosistema' => [
            'title'       => 'Mapa del Ecosistema IA en Chile',
            'subtitle'    => 'Universidades, startups, centros de investigación y más',
            'description' => 'Un mapa interactivo de todos los actores del ecosistema de inteligencia artificial en Chile. Encuentra quién investiga, quién desarrolla y quién regula la IA en el país.',
            'icon'        => 'fa-map-marked-alt',
        ],
        'regulacion' => [
            'title'       => 'Observatorio de Regulación IA',
            'subtitle'    => 'Seguimiento de legislación y políticas públicas',
            'description' => 'Monitoreo permanente de proyectos de ley, normativas sectoriales y estándares éticos sobre inteligencia artificial en Chile y el mundo.',
            'icon'        => 'fa-balance-scale',
        ],
    ];

    public function show(string $slug)
    {
        if (!isset($this->pages[$slug])) {
            abort(404);
        }

        return view('coming-soon.index', [
            'page' => $this->pages[$slug],
            'slug' => $slug,
        ]);
    }
}
