@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Videos Populares</h1>
    
    <div class="row">
        @forelse($videos as $video)
            <div class="col-md-4 mb-4">
                <div class="card">
                    @if($video->thumbnail)
                        <img src="{{ $video->thumbnail }}" class="card-img-top" alt="{{ $video->title }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $video->title }}</h5>
                        <p class="card-text">{{ Str::limit($video->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('videos.show', $video) }}" class="btn btn-primary">Ver video</a>
                            <small class="text-muted">{{ $video->views ?? 0 }} visualizaciones</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>No hay videos populares disponibles.</p>
            </div>
        @endforelse
    </div>
    
    {{ $videos->links() }}
</div>
@endsection