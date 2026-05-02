@php
    $analyticsEnabled = (bool) config('services.analytics.enabled');
    $gtmContainerId = trim((string) config('services.analytics.gtm_container_id'));
@endphp

@if($analyticsEnabled && $gtmContainerId !== '')
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmContainerId }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
