<?php

namespace Tests\Unit;

use App\Models\News;
use PHPUnit\Framework\TestCase;

class NewsSeoTest extends TestCase
{
    public function test_seo_title_adds_brand_suffix_when_title_is_short(): void
    {
        $news = new News([
            'title' => 'Guia completa para integrar ChatGPT con Apple',
        ]);

        $this->assertSame(
            'Guia completa para integrar ChatGPT con Apple | ConocIA',
            $news->seoTitle()
        );
    }

    public function test_seo_title_is_trimmed_when_title_is_too_long(): void
    {
        $news = new News([
            'title' => 'Esta es una noticia extremadamente larga sobre inteligencia artificial aplicada a empresas y desarrollo de software',
        ]);

        $this->assertLessThanOrEqual(60, mb_strlen($news->seoTitle()));
    }

    public function test_seo_description_prefers_clean_summary_and_limits_length(): void
    {
        $news = new News([
            'summary' => '  Una guia practica para entender diferencias, precios, ventajas y casos de uso reales de herramientas de inteligencia artificial en empresas modernas.  ',
            'content' => '<p>Contenido largo de respaldo.</p>',
        ]);

        $description = $news->seoDescription();

        $this->assertStringStartsWith('Una guia practica para entender diferencias', $description);
        $this->assertLessThanOrEqual(155, mb_strlen($description));
    }
}
