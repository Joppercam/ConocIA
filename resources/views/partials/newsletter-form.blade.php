<!-- resources/views/partials/newsletter-form.blade.php -->
<div class="newsletter-container border-0 rounded overflow-hidden">
    <div class="bg-primary text-white p-2 text-center">
        <i class="fas fa-paper-plane me-2"></i>
        <span style="font-size: 0.9rem; font-weight: 600;">NEWSLETTER DE IA</span>
    </div>
    <div class="p-3 bg-white shadow-sm">
        @if(session('newsletter_success'))
            <div class="alert alert-success py-2 mb-2" style="font-size: 0.8rem;">
                {{ session('newsletter_success') }}
            </div>
        @endif
        
        <p class="mb-2" style="font-size: 0.75rem;">Sé el primero en recibir las últimas novedades y análisis sobre inteligencia artificial.</p>
        
        <form action="{{ route('newsletter.subscribe') }}" method="POST">
            @csrf
            <div class="input-group input-group-sm mb-2">
                <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" placeholder="Tu correo electrónico" required>
                <button class="btn btn-primary btn-sm" type="submit">
                    Suscribirse
                </button>
            </div>
            @error('email')
                <div class="invalid-feedback d-block mb-2" style="font-size: 0.7rem;">{{ $message }}</div>
            @enderror
            <div class="text-center mt-2">
                <span class="badge bg-light text-dark rounded-pill" style="font-size: 0.65rem;">
                    <i class="fas fa-lock me-1"></i> Política de privacidad
                </span>
            </div>
        </form>
    </div>
</div>