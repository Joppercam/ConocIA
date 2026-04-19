@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-brain fa-2x mb-2" style="color:var(--primary-color);"></i>
                        <h4 class="fw-bold mb-0">Iniciar sesión</h4>
                        <p class="text-muted" style="font-size:.85rem;">Bienvenido de vuelta a ConocIA</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold" style="font-size:.85rem;">Correo electrónico</label>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   required autocomplete="email" autofocus>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold" style="font-size:.85rem;">Contraseña</label>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="remember" style="font-size:.82rem;">Recordarme</label>
                            </div>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size:.82rem;">¿Olvidaste tu contraseña?</a>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="fas fa-sign-in-alt me-1"></i>Entrar
                        </button>
                    </form>

                    <div class="d-flex align-items-center my-4 gap-2">
                        <hr class="flex-grow-1 m-0"><span class="text-muted" style="font-size:.8rem;">o</span><hr class="flex-grow-1 m-0">
                    </div>

                    <a href="{{ route('login.google') }}" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                        Continuar con Google
                    </a>

                    <hr class="my-4">

                    <p class="text-center text-muted mb-0" style="font-size:.85rem;">
                        ¿No tienes cuenta?
                        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Registrarse</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
