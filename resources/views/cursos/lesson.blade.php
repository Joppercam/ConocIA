@extends('layouts.app')

@section('title', $lessonTitle . ' — ' . $course['title'] . ' | ConocIA')
@section('meta_description', $course['subtitle'])

@section('reading_progress', true)

@section('content')

{{-- Hero compacto --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:2px solid {{ $course['badge_color'] }}33;" class="py-3">
    <div class="container py-1">
        <nav style="font-size:.8rem;color:#64748b;margin-bottom:.5rem;">
            <a href="{{ route('cursos.index') }}" style="color:#7dd3f0;text-decoration:none;">Cursos</a>
            <span class="mx-1">›</span>
            <a href="{{ route('cursos.show', $course['slug']) }}" style="color:#7dd3f0;text-decoration:none;">{{ $course['title'] }}</a>
            <span class="mx-1">›</span>
            <span style="color:#94a3b8;">Módulo {{ $module }}</span>
        </nav>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge" style="background:{{ $course['badge_color'] }}22;color:{{ $course['badge_color'] }};border:1px solid {{ $course['badge_color'] }}44;font-size:.72rem;">
                <i class="fas {{ $course['icon'] }} me-1"></i>{{ $course['badge'] }}
            </span>
            <span style="color:#94a3b8;font-size:.8rem;">Módulo {{ $module }} · Lección {{ $lesson }}</span>
        </div>
    </div>
</section>

<div class="container py-4">
    <div class="row g-4">

        {{-- Sidebar: índice del curso --}}
        <div class="col-lg-3">
            <nav style="position:sticky;top:88px;z-index:10;max-height:calc(100vh - 110px);overflow-y:auto;padding-right:.25rem;">
                <a href="{{ route('cursos.show', $course['slug']) }}"
                   style="display:flex;align-items:center;gap:.5rem;color:#64748b;font-size:.78rem;text-decoration:none;margin-bottom:1rem;padding:.4rem .6rem;border-radius:.4rem;border:1px solid #e2e8f0;">
                    <i class="fas fa-arrow-left" style="font-size:.7rem;"></i> Volver al curso
                </a>

                @foreach($course['modules'] as $mi => $mod)
                <div class="mb-2">
                    <div style="display:flex;align-items:center;gap:.5rem;padding:.4rem .4rem;font-size:.8rem;font-weight:700;color:{{ ($mi + 1) === $module ? $course['badge_color'] : '#0f172a' }};">
                        <span style="width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:800;background:{{ ($mi + 1) === $module ? $course['badge_color'] . '22' : '#f1f5f9' }};color:{{ ($mi + 1) === $module ? $course['badge_color'] : '#64748b' }};flex-shrink:0;">{{ $mi + 1 }}</span>
                        {{ $mod['title'] }}
                    </div>
                    <div style="padding-left:.75rem;">
                        @foreach($mod['lessons'] as $li => $lt)
                        @php
                            $isActive = ($mi + 1) === $module && ($li + 1) === $lesson;
                            $hasContent = !is_null(\App\Http\Controllers\CursosController::lessonHasContent($course['slug'], $mi + 1, $li + 1));
                        @endphp
                        @if($hasContent)
                            <a href="{{ route('cursos.lesson', [$course['slug'], $mi + 1, $li + 1]) }}"
                               style="display:block;padding:.35rem .65rem;font-size:.78rem;text-decoration:none;border-radius:.35rem;border:1px solid {{ $isActive ? $course['badge_color'] . '44' : 'transparent' }};background:{{ $isActive ? $course['badge_color'] . '0d' : 'transparent' }};color:{{ $isActive ? '#0f172a' : '#64748b' }};font-weight:{{ $isActive ? '600' : '400' }};margin-bottom:.1rem;line-height:1.35;transition:all .15s;"
                               @if(!$isActive) onmouseover="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'" onmouseout="this.style.background='transparent';this.style.borderColor='transparent'" @endif>
                                {{ $mi + 1 }}.{{ $li + 1 }} {{ $lt }}
                            </a>
                        @else
                            <div style="display:flex;align-items:center;gap:.4rem;padding:.35rem .65rem;font-size:.78rem;color:#94a3b8;margin-bottom:.1rem;line-height:1.35;">
                                <i class="fas fa-lock" style="font-size:.65rem;flex-shrink:0;"></i>
                                <span>{{ $mi + 1 }}.{{ $li + 1 }} {{ $lt }}</span>
                            </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </nav>
        </div>

        {{-- Contenido de la lección --}}
        <div class="col-lg-9">
            <div class="profundiza-card p-4 p-md-5">

                {{-- Cabecera --}}
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:{{ $course['badge_color'] }};">
                        Módulo {{ $module }} · Lección {{ $lesson }}
                    </span>
                </div>
                <h1 style="font-size:1.65rem;font-weight:800;color:#0f172a;line-height:1.2;margin-bottom:2rem;">
                    {{ $lessonTitle }}
                </h1>

                {{-- Contenido --}}
                @if($content)
                <div class="lesson-content">
                    {!! $content !!}
                </div>
                @else
                <div class="text-center py-5">
                    <div style="font-size:2.5rem;margin-bottom:1rem;">🔒</div>
                    <h2 style="color:#0f172a;font-size:1.2rem;font-weight:700;margin-bottom:.5rem;">Lección en preparación</h2>
                    <p style="color:#64748b;font-size:.92rem;max-width:400px;margin:0 auto 1.5rem;">
                        Esta lección estará disponible pronto. Suscríbete para recibir aviso cuando se publique.
                    </p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex gap-2 justify-content-center">
                        @csrf
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="tu@email.cl" required style="max-width:240px;font-size:.85rem;">
                        <button type="submit" class="btn btn-primary btn-sm" style="font-size:.85rem;white-space:nowrap;">Avisarme</button>
                    </form>
                </div>
                @endif

                {{-- Navegación entre lecciones --}}
                <div class="d-flex justify-content-between align-items-center mt-5 pt-4" style="border-top:1px solid #f1f5f9;">
                    @if($prevLesson)
                    <a href="{{ route('cursos.lesson', [$course['slug'], $prevLesson['module'], $prevLesson['lesson']]) }}"
                       class="btn btn-outline-secondary btn-sm" style="font-size:.85rem;">
                        <i class="fas fa-arrow-left me-1"></i> Lección anterior
                    </a>
                    @else
                    <span></span>
                    @endif

                    @if($nextLesson)
                    @php
                        $nextHasContent = !is_null(\App\Http\Controllers\CursosController::lessonHasContent($course['slug'], $nextLesson['module'], $nextLesson['lesson']));
                    @endphp
                    @if($nextHasContent)
                    <a href="{{ route('cursos.lesson', [$course['slug'], $nextLesson['module'], $nextLesson['lesson']]) }}"
                       class="btn btn-primary btn-sm" style="font-size:.85rem;">
                        Lección siguiente <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                    @else
                    <span style="color:#94a3b8;font-size:.82rem;"><i class="fas fa-lock me-1"></i>Próxima lección en preparación</span>
                    @endif
                    @else
                    <a href="{{ route('cursos.show', $course['slug']) }}" class="btn btn-primary btn-sm" style="font-size:.85rem;">
                        <i class="fas fa-graduation-cap me-1"></i> Ver otros cursos
                    </a>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
.lesson-content h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 2rem;
    margin-bottom: .75rem;
    padding-bottom: .5rem;
    border-bottom: 2px solid rgba(56,182,255,.12);
}
.lesson-content h2:first-child { margin-top: 0; }
.lesson-content h3 {
    font-size: .97rem;
    font-weight: 700;
    color: #1e40af;
    margin-top: 1.5rem;
    margin-bottom: .5rem;
}
.lesson-content p {
    color: #475569;
    font-size: .97rem;
    line-height: 1.85;
    margin-bottom: 1rem;
}
.lesson-content ul, .lesson-content ol {
    color: #475569;
    font-size: .97rem;
    line-height: 1.85;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}
.lesson-content li { margin-bottom: .35rem; }
.lesson-content strong { color: #1e293b; font-weight: 600; }
</style>
@endpush

@endsection
