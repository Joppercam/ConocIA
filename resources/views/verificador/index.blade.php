<!-- resources/views/verificador/index.blade.php -->
@extends('layouts.app')

@section('title', 'Verificador Autónomo de Noticias')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Verificador Autónomo</h1>
        <p class="text-gray-600">Verificaciones de afirmaciones realizadas por nuestro sistema inteligente</p>
    </div>

    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-4 border-b pb-2">Verificaciones Destacadas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($featuredVerifications as $verification)
                <x-verification-card :verification="$verification" :featured="true" />
            @empty
                <div class="col-span-2 p-4 bg-gray-100 rounded-lg">
                    <p class="text-center text-gray-600">No hay verificaciones destacadas por el momento.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold border-b pb-2">Verificaciones Recientes</h2>
            <div class="flex space-x-2">
                <div class="relative">
                    <select id="category-filter" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:ring focus:border-blue-300">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                        </svg>
                    </div>
                </div>
                
                <div class="relative">
                    <select id="verdict-filter" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:ring focus:border-blue-300">
                        <option value="">Todos los veredictos</option>
                        <option value="true" {{ request('verdict') == 'true' ? 'selected' : '' }}>Verdadero</option>
                        <option value="partially_true" {{ request('verdict') == 'partially_true' ? 'selected' : '' }}>Parcialmente verdadero</option>
                        <option value="false" {{ request('verdict') == 'false' ? 'selected' : '' }}>Falso</option>
                        <option value="unverifiable" {{ request('verdict') == 'unverifiable' ? 'selected' : '' }}>No verificable</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @forelse($verifications as $verification)
                <x-verification-card :verification="$verification" />
            @empty
                <div class="p-4 bg-gray-100 rounded-lg">
                    <p class="text-center text-gray-600">No hay verificaciones disponibles con los filtros seleccionados.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $verifications->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('category-filter').addEventListener('change', function() {
        updateFilters();
    });
    
    document.getElementById('verdict-filter').addEventListener('change', function() {
        updateFilters();
    });
    
    function updateFilters() {
        const categoryFilter = document.getElementById('category-filter').value;
        const verdictFilter = document.getElementById('verdict-filter').value;
        
        let url = new URL(window.location.href);
        
        if (categoryFilter) {
            url.searchParams.set('category', categoryFilter);
        } else {
            url.searchParams.delete('category');
        }
        
        if (verdictFilter) {
            url.searchParams.set('verdict', verdictFilter);
        } else {
            url.searchParams.delete('verdict');
        }
        
        window.location.href = url.toString();
    }
</script>
@endpush
@endsection