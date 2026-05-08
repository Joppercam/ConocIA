@php
    $analyticsEnabled = (bool) config('services.analytics.enabled');
    $ga4MeasurementId = trim((string) config('services.analytics.ga4_measurement_id'));
    $googleTagId = trim((string) config('services.analytics.google_tag_id'));
    $gtmContainerId = trim((string) config('services.analytics.gtm_container_id'));
    $gtagLoaderId = $ga4MeasurementId !== '' ? $ga4MeasurementId : $googleTagId;
    $analyticsConfig = [
        'enabled' => $analyticsEnabled,
        'ga4MeasurementId' => $ga4MeasurementId,
        'googleTagId' => $googleTagId,
        'gtagLoaderId' => $gtagLoaderId,
        'gtmContainerId' => $gtmContainerId,
    ];
@endphp

@if($analyticsEnabled && ($gtagLoaderId !== '' || $gtmContainerId !== ''))
<!-- Analytics configuration. Tags are loaded by cookie-manager.js after consent. -->
<script>
window.conociaAnalyticsConfig = @json($analyticsConfig);
</script>
<!-- End analytics configuration -->
@endif
