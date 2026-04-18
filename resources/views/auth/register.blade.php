@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-brain fa-2x mb-2" style="color:var(--primary-color);"></i>
                        <h4 class="fw-bold mb-0">Crear cuenta</h4>
                        <p class="text-muted" style="font-size:.85rem;">Únete a la comunidad ConocIA</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold" style="font-size:.85rem;">Nombre</label>
                            <input id="name" type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}"
                                   required autocomplete="name" autofocus>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold" style="font-size:.85rem;">Correo electrónico</label>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   required autocomplete="email">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold" style="font-size:.85rem;">Contraseña</label>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="new-password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-semibold" style="font-size:.85rem;">Confirmar contraseña</label>
                            <input id="password-confirm" type="password"
                                   class="form-control"
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="fas fa-user-plus me-1"></i>Crear cuenta
                        </button>
                    </form>

                    <hr class="my-4">

                    <p class="text-center text-muted mb-0" style="font-size:.85rem;">
                        ¿Ya tienes cuenta?
                        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Iniciar sesión</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
