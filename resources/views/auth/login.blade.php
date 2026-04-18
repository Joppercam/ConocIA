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
