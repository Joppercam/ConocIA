<!-- resources/views/components/newsletter-subscription.blade.php -->
<div class="newsletter-subscription py-3 border-bottom bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-envelope-open-text text-primary me-2"></i>Suscríbete a nuestro newsletter
                        </h5>
                        
                        @if(session('subscription_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('subscription_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                        
                        @if(session('subscription_info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i> {{ session('subscription_info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                        
                        <form id="newsletterForm" action="{{ route('newsletter.subscribe') }}" method="POST" class="row g-3">
                            @csrf
                            
                            <div class="col-md-5">
                                <div class="form-floating">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                        id="newsletterEmail" name="email" placeholder="Tu correo electrónico" 
                                        value="{{ old('email') }}" required>
                                    <label for="newsletterEmail">Correo electrónico</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                        id="newsletterName" name="name" placeholder="Tu nombre (opcional)" 
                                        value="{{ old('name') }}">
                                    <label for="newsletterName">Nombre (opcional)</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary h-100 w-100" 
                                        data-bs-toggle="collapse" data-bs-target="#categorySelection">
                                    <i class="fas fa-tags me-1"></i> Seleccionar categorías
                                </button>
                            </div>
                            
                            <div class="col-12 collapse" id="categorySelection">
                                <div class="card card-body border-0 bg-light">
                                    <h6 class="mb-2">Elige las categorías que te interesan:</h6>
                                    <div class="row row-cols-2 row-cols-md-4 g-2">
                                        @foreach(App\Models\Category::all() as $category)
                                        <div class="col">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="categories[]" value="{{ $category->id }}" 
                                                    id="category{{ $category->id }}"
                                                    {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="category{{ $category->id }}">
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted mt-2">Si no seleccionas ninguna categoría, recibirás noticias de todas las categorías.</small>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privacy_consent" 
                                        id="privacyConsent" {{ old('privacy_consent') ? 'checked' : '' }} required>
                                    <label class="form-check-label small" for="privacyConsent">
                                        Acepto recibir comunicaciones y he leído la 
                                        <a href="{{ route('pages.privacy') }}" target="_blank">política de privacidad</a>
                                    </label>
                                    @error('privacy_consent')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Suscribirme ahora
                                </button>
                                <small class="text-muted ms-2">
                                    <i class="fas fa-shield-alt me-1"></i> Sin spam, puedes cancelar cuando quieras
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>