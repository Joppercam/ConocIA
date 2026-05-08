<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_do_not_load_google_tags_before_cookie_consent(): void
    {
        config([
            'services.analytics.enabled' => true,
            'services.analytics.ga4_measurement_id' => 'G-W2M9Q5P8MQ',
            'services.analytics.google_tag_id' => 'GT-TNP9X5ZP',
            'services.analytics.gtm_container_id' => '',
        ]);

        $response = $this->get(route('search', ['query' => 'chatgpt']));

        $response->assertOk();
        $response->assertSee('window.conociaAnalyticsConfig', false);
        $response->assertSee('"ga4MeasurementId":"G-W2M9Q5P8MQ"', false);
        $response->assertSee('cookie-consent-banner', false);
        $response->assertDontSee('https://www.googletagmanager.com/gtag/js', false);
        $response->assertDontSee("gtag('config'", false);
        $response->assertDontSee('googletagmanager.com/ns.html', false);
    }

    public function test_analytics_config_is_omitted_when_disabled(): void
    {
        config(['services.analytics.enabled' => false]);

        $response = $this->get(route('search', ['query' => 'chatgpt']));

        $response->assertOk();
        $response->assertDontSee('window.conociaAnalyticsConfig', false);
        $response->assertDontSee('https://www.googletagmanager.com/gtag/js', false);
    }
}
