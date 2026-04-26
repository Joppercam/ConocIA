@extends('admin.layouts.app')

@section('title', 'Solicitar noticia')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.editorial-agent.index') }}" class="text-decoration-none small">&larr; Volver al agente</a>
        <h1 class="h3 mt-2 mb-0">Solicitar noticia al agente</h1>
        <p class="text-muted mb-0">Crea una solicitud editorial pendiente. Para generar el borrador con búsqueda externa, ejecuta el comando sugerido.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.editorial-agent.store-news-request') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="topic" class="form-label">Tema o instrucción</label>
                    <input type="text" name="topic" id="topic" class="form-control" value="{{ old('topic') }}" placeholder="Ej: IA en Chile y educación escolar" required>
                    @error('topic')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="category_slug" class="form-label">Categoría sugerida</label>
                        <select name="category_slug" id="category_slug" class="form-select">
                            <option value="inteligencia-artificial">Inteligencia Artificial</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" @selected(old('category_slug') === $category->slug)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Prioridad</label>
                        <select name="priority" id="priority" class="form-select">
                            <option value="high" @selected(old('priority') === 'high')>Alta</option>
                            <option value="medium" @selected(old('priority') === 'medium')>Media</option>
                            <option value="low" @selected(old('priority') === 'low')>Baja</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="notes" class="form-label">Notas editoriales</label>
                    <textarea name="notes" id="notes" rows="4" class="form-control" placeholder="Ángulo, fuentes preferidas o restricciones">{{ old('notes') }}</textarea>
                </div>

                <div class="alert alert-light border mt-4 mb-3">
                    El comando para crear el borrador con fuentes externas será guardado dentro de la solicitud.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.editorial-agent.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button class="btn btn-primary">Crear solicitud</button>
                </div>
            </form>
        </div>
    </div>
@endsection
