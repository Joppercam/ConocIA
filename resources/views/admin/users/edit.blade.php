@extends('admin.layouts.app')

@section('title', 'Editar usuario')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0">Editar usuario</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="user-edit-form" action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nueva contraseña</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Dejar en blanco para no cambiar">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       placeholder="Repetir nueva contraseña">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                           value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Usuario activo</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                                <h6 class="text-muted mb-3">Perfil del columnista</h6>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Foto de perfil</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $user->photo_url }}" id="admin-photo-preview"
                                         class="rounded-circle flex-shrink-0"
                                         width="64" height="64"
                                         style="object-fit:cover;border:3px solid #e9ecef;">
                                    <div>
                                        <input type="file" id="admin_profile_photo" name="profile_photo"
                                               accept="image/*" class="form-control form-control-sm"
                                               onchange="document.getElementById('admin-photo-preview').src = URL.createObjectURL(this.files[0])">
                                        <div class="form-text">JPG, PNG o WEBP. Máx 2MB.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Biografía</label>
                                <textarea name="bio" class="form-control @error('bio') is-invalid @enderror"
                                          rows="3">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Twitter / X</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                    <input type="text" name="twitter" class="form-control"
                                           value="{{ old('twitter', $user->twitter) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">LinkedIn</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                    <input type="text" name="linkedin" class="form-control"
                                           value="{{ old('linkedin', $user->linkedin) }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center">
                    <img src="{{ $user->photo_url }}" id="admin-photo-preview"
                         class="rounded-circle mb-3"
                         width="80" height="80"
                         style="object-fit:cover;border:3px solid #e9ecef;">
                    <h6 class="fw-bold mb-0">{{ $user->name }}</h6>
                    <div class="text-muted small">{{ $user->email }}</div>
                    <div class="mt-2">
                        <span class="badge bg-secondary">{{ $user->role?->name ?? 'Sin rol' }}</span>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Actividad</h6>
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Columnas publicadas</span>
                        <strong class="text-dark">{{ $user->columns()->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Registrado</span>
                        <strong class="text-dark">{{ $user->created_at->format('d/m/Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
