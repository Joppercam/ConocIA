@extends('admin.layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0">Nuevo usuario</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="">Seleccioná un rol</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }} — {{ $role->description }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                           value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Usuario activo</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                                <h6 class="text-muted mb-3">Perfil del columnista (opcional)</h6>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Biografía</label>
                                <textarea name="bio" class="form-control @error('bio') is-invalid @enderror"
                                          rows="3" placeholder="Breve descripción del autor...">{{ old('bio') }}</textarea>
                                @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Twitter / X</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                    <input type="text" name="twitter" class="form-control"
                                           placeholder="@usuario" value="{{ old('twitter') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">LinkedIn</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                    <input type="text" name="linkedin" class="form-control"
                                           placeholder="URL del perfil" value="{{ old('linkedin') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Crear usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Roles disponibles</h6>
                    @foreach($roles as $role)
                    <div class="mb-3">
                        <span class="fw-semibold" style="font-size:.88rem;">{{ $role->name }}</span>
                        <p class="text-muted mb-0" style="font-size:.8rem;">{{ $role->description }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
