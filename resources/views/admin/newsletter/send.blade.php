@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Enviar Newsletter</h5>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Hay {{ $subscribersCount }} suscriptores activos que recibirán este newsletter.
                    </div>
                    
                    <form action="{{ route('admin.newsletter.send.post') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Asunto del correo</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject', 'Últimas noticias de ConocIA') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="news_count" class="form-label">Número de noticias a incluir</label>
                            <select class="form-select @error('news_count') is-invalid @enderror" 
                                    id="news_count" name="news_count" required>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('news_count') == $i || (!old('news_count') && $i == 5) ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'noticia' : 'noticias' }}
                                    </option>
                                @endfor
                            </select>
                            @error('news_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Se enviarán las noticias más recientes según la cantidad seleccionada.
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i> Enviar Newsletter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection