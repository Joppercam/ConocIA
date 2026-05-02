@php
    $analyticsEnabled = (bool) config('services.analytics.enabled');
    $ga4MeasurementId = trim((string) config('services.analytics.ga4_measurement_id'));
    $gtmContainerId = trim((string) config('services.analytics.gtm_container_id'));
@endphp

@if($analyticsEnabled && $gtmContainerId !== '')
<!-- Google Tag Manager -->
<script>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmContainerId }}');
</script>
<!-- End Google Tag Manager -->
@endif

@if($analyticsEnabled && $ga4MeasurementId !== '')
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4MeasurementId }}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '{{ $ga4MeasurementId }}');
</script>
<!-- End Google Analytics 4 -->
@endif
