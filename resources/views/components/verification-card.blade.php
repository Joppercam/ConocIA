<!-- resources/views/components/verification-card.blade.php -->
@props(['verification', 'featured' => false])

<div class="verification-card {{ $featured ? 'verification-card-featured' : '' }} rounded-lg shadow-md overflow-hidden mb-4">
    <div class="p-4">
        <div class="flex items-center mb-2">
            <span class="verification-badge {{ $verification->verdict_class }} px-2 py-1 rounded-full text-xs font-bold">
                {{ $verification->verdict_label }}
            </span>
            <span class="ml-2 text-sm text-gray-500">{{ $verification->claim->category->name }}</span>
        </div>
        
        <h3 class="text-xl font-bold mb-2 hover:text-blue-600">
            <a href="{{ route('verificador.show', $verification->id) }}">
                {{ $verification->claim->statement }}
            </a>
        </h3>
        
        <div class="mb-3 text-gray-700">
            {{ Str::limit($verification->summary, 150) }}
        </div>
        
        <div class="flex justify-between items-center text-sm text-gray-500">
            <div>
                <span>Fuente: {{ $verification->claim->source }}</span>
            </div>
            <div>
                {{ $verification->created_at->format('d/m/Y') }}
            </div>
        </div>
    </div>
    
    @if($featured)
    <div class="p-4 bg-gray-50 border-t">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>{{ $verification->views_count }} vistas</span>
            </div>
            <a href="{{ route('verificador.show', $verification->id) }}" class="text-blue-600 hover:underline">
                Ver verificación completa
            </a>
        </div>
    </div>
    @endif
</div>