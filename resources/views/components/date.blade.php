{{-- Guardar como resources/views/components/date.blade.php --}}
@props(['date', 'format' => 'full'])

@php
    if (!($date instanceof \Carbon\Carbon)) {
        $date = \Carbon\Carbon::parse($date);
    }
    
    switch ($format) {
        case 'short':
            $formatted = $date->format('d/m/Y');
            break;
        case 'day-month':
            $formatted = $date->locale('es')->isoFormat('D [de] MMMM');
            break;
        case 'time':
            $formatted = $date->format('H:i');
            break;
        case 'datetime':
            $formatted = $date->locale('es')->isoFormat('D [de] MMMM, YYYY HH:mm');
            break;
        case 'human':
            $formatted = $date->locale('es')->diffForHumans();
            break;
        case 'full':
        default:
            $formatted = $date->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            break;
    }
@endphp

<span {{ $attributes }}>{{ $formatted }}</span>