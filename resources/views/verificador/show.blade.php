<!-- resources/views/verificador/show.blade.php -->
@extends('layouts.app')

@section('title', 'Verificación: ' . Str::limit($verification->claim->statement, 50))

@section('meta')
<meta name="description" content="{{ $verification->summary }}">
<meta property="og:title" content="Verificación: {{ Str::limit($verification->claim->statement, 50) }}">
<meta property="og:description" content="{{ $verification->summary }}">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ route('verificador.show', $verification->id) }}">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('verificador.index') }}" class="text-blue-600 hover:underline flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver a todas las verificaciones
            </a>
        </div>

        <article class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="verification-badge {{ $verification->verdict_class }} px-3 py-1 rounded-full text-sm font-bold">
                        {{ $verification->verdict_label }}
                    </span>
                    <span class="text-sm text-gray-500">
                        {{ $verification->created_at->format('d/m/Y H:i') }}
                    </span>
                    <span class="text-sm text-gray-500">
                        Categoría: {{ $verification->claim->category->name }}
                    </span>
                </div>

                <h1 class="text-2xl lg:text-3xl font-bold mb-4">{{ $verification->claim->statement }}</h1>
                
                <div class="text-gray-600 mb-6">
                    <p><strong>Fuente original:</strong> {{ $verification->claim->source }}</p>
                    <p><strong>Fecha de la afirmación:</strong> {{ $verification->claim->statement_date->format('d/m/Y') }}</p>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-3">Resumen de la verificación</h2>
                    <div class="text-gray-800 mb-4">
                        {{ $verification->summary }}
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-3">Análisis detallado</h2>
                    <div class="prose max-w-none">
                        {!! $verification->analysis !!}
                    </div>
                </div>

                @if($verification->evidence && count($verification->evidence) > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-3">Evidencia consultada</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        @foreach($verification->evidence as $evidence)
                        <li>
                            <a href="{{ $evidence['url'] }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">
                                {{ $evidence['title'] }}
                            </a>
                            <p class="text-sm text-gray-600">{{ $evidence['description'] }}</p>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="bg-gray-100 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold mb-2">Metodología</h2>
                    <p class="text-gray-700 text-sm">
                        Esta verificación ha sido realizada de forma automática por nuestro sistema de IA. El sistema analiza la afirmación, consulta 
                        fuentes confiables y evalúa su veracidad basándose en la evidencia disponible. El veredicto y el análisis son generados sin 
                        intervención humana, aplicando criterios objetivos y transparentes.
                    </p>
                </div>
            </div>

            <div class="p-6 bg-gray-50 border-t">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>{{ $verification->views_count }} vistas</span>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('verificador.show', $verification->id)) }}&text={{ urlencode('Verificación: ' . $verification->claim->statement) }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center text-gray-700 hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path>
                            </svg>
                            Compartir
                        </a>
                        
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('verificador.show', $verification->id)) }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center text-gray-700 hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"></path>
                            </svg>
                            Compartir
                        </a>
                        
                        <a href="whatsapp://send?text={{ urlencode('Verificación: ' . $verification->claim->statement . ' ' . route('verificador.show', $verification->id)) }}" 
                           class="flex items-center text-gray-700 hover:text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"></path>
                            </svg>
                            Compartir
                        </a>
                    </div>
                </div>
            </div>
        </article>

        @if(count($relatedVerifications) > 0)
        <div class="mt-10">
            <h2 class="text-2xl font-bold mb-6">Verificaciones relacionadas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($relatedVerifications as $relatedVerification)
                    <x-verification-card :verification="$relatedVerification" />
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .verification-badge.true {
        background-color: #d1fae5;
        color: #047857;
    }
    
    .verification-badge.partially_true {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .verification-badge.false {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    .verification-badge.unverifiable {
        background-color: #e5e7eb;
        color: #4b5563;
    }
</style>
@endpush