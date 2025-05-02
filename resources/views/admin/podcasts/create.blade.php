@extends('admin.layouts.app')

@section('title', 'Crear Podcast')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Crear Podcast</h1>
        <a href="{{ route('admin.podcasts.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Generar Podcast de Noticia</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.podcasts.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="title">Título del Resumen:</label>
                    <input type="text" name="title" id="title" class="form-control" required 
                        value="Resumen de noticias {{ now()->format('d/m/Y') }}" />
                </div>
                
                <div class="form-group">
                    <label for="voice">Seleccionar Voz:</label>
                    <select name="voice" id="voice" class="form-control" required>
                        <option value="alloy">Alloy (Neutral)</option>
                        <option value="echo">Echo (Formal)</option>
                        <option value="fable">Fable (Narrativa)</option>
                        <option value="onyx">Onyx (Profunda)</option>
                        <option value="nova">Nova (Femenina)</option>
                        <option value="shimmer">Shimmer (Optimista)</option>
                    </select>
                </div>
                
                <div class="alert alert-info">
                    <strong>Nota:</strong> El proceso de generación del resumen diario puede tomar varios segundos.
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-microphone"></i> Generar Resumen Diario
                </button>
            </form>
        </div>
    </div>
</div>
@endsection