@extends('layouts.app')

@section('title', $course['title'] . ' — Curso especializado de IA | ConocIA')
@section('meta_description', $course['subtitle'])

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid {{ $course['badge_color'] }}33;" class="py-4">
    <div class="container py-2">
        <nav style="font-size:.82rem;color:#64748b;margin-bottom:1.25rem;">
            <a href="{{ route('cursos.index') }}" style="color:#7dd3f0;text-decoration:none;">
                <i class="fas fa-graduation-cap me-1"></i>Cursos especializados
            </a>
            <span class="mx-2">›</span>
            <span style="color:#94a3b8;">{{ $course['badge'] }}</span>
        </nav>

        <div class="d-flex flex-wrap align-items-start gap-3">
            <div class="flex-grow-1">
                <div class="d-flex gap-2 mb-2 flex-wrap">
                    <span class="badge px-3 py-2" style="background:{{ $course['badge_color'] }}22;color:{{ $course['badge_color'] }};border:1px solid {{ $course['badge_color'] }}44;font-size:.78rem;">
                        <i class="fas {{ $course['icon'] }} me-1"></i>{{ $course['badge'] }}
                    </span>
                    @if($course['status'] === 'disponible')
                    <span class="badge px-3 py-2" style="background:rgba(0,200,150,.15);color:#00c896;font-size:.78rem;">
                        <i class="fas fa-lock-open me-1"></i>Gratis con registro
                    </span>
                    @else
                    <span class="badge px-3 py-2" style="background:rgba(245,158,11,.15);color:#f59e0b;font-size:.78rem;">Próximamente</span>
                    @endif
                </div>
                <h1 class="fw-bold text-white mb-2" style="font-size:1.8rem;line-height:1.2;max-width:680px;">{{ $course['title'] }}</h1>
                <p style="color:#94a3b8;font-size:.93rem;margin:0;">{{ $course['subtitle'] }}</p>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5">

        {{-- Contenido principal --}}
        <div class="col-lg-8">

            {{-- Para quién es --}}
            <div class="mb-4 p-4" style="background:{{ $course['badge_color'] }}0d;border-left:4px solid {{ $course['badge_color'] }};border-radius:.75rem;">
                <p class="mb-0" style="font-size:.92rem;line-height:1.7;">
                    <strong style="color:#0f172a;">Para quién es:</strong>
                    <span style="color:#475569;"> {{ $course['audience'] }}</span>
                </p>
            </div>

            {{-- Descripción --}}
            <h2 class="fw-bold mb-3" style="color:#0f172a;font-size:1.1rem;padding-bottom:.5rem;border-bottom:2px solid rgba(56,182,255,.12);">
                ¿De qué trata este curso?
            </h2>
            <p style="color:#475569;font-size:.97rem;line-height:1.85;margin-bottom:1.5rem;">{{ $course['description'] }}</p>

            {{-- Por qué ahora --}}
            <div class="mb-5 p-4" style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:.75rem;">
                <p class="fw-semibold mb-2" style="color:#92400e;font-size:.88rem;text-transform:uppercase;letter-spacing:.05em;">Por qué es urgente ahora</p>
                <p class="mb-0" style="color:#78350f;font-size:.93rem;line-height:1.75;">{{ $course['why_now'] }}</p>
            </div>

            {{-- Qué vas a aprender --}}
            <h2 class="fw-bold mb-3" style="color:#0f172a;font-size:1.1rem;padding-bottom:.5rem;border-bottom:2px solid rgba(56,182,255,.12);">
                Qué vas a aprender
            </h2>
            <div class="mb-5">
                @foreach($course['outcomes'] as $outcome)
                <div class="d-flex align-items-start gap-3 mb-2">
                    <div style="width:22px;height:22px;background:{{ $course['badge_color'] }}18;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:.15rem;">
                        <i class="fas fa-check" style="color:{{ $course['badge_color'] }};font-size:.65rem;"></i>
                    </div>
                    <p style="color:#334155;font-size:.92rem;line-height:1.65;margin:0;">{{ $outcome }}</p>
                </div>
                @endforeach
            </div>

            {{-- Contenido del curso --}}
            <h2 class="fw-bold mb-4" style="color:#0f172a;font-size:1.1rem;padding-bottom:.5rem;border-bottom:2px solid rgba(56,182,255,.12);">
                Contenido del curso
            </h2>

            <div class="d-flex flex-column gap-3">
                @foreach($course['modules'] as $module)
                <div style="border:1px solid #e2e8f0;border-radius:.75rem;overflow:hidden;">
                    {{-- Cabecera módulo --}}
                    <div class="d-flex align-items-center gap-3 p-3" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                        <div style="width:32px;height:32px;background:{{ $course['badge_color'] }}18;border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="color:{{ $course['badge_color'] }};font-size:.75rem;font-weight:800;">{{ $module['number'] }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <p class="fw-bold mb-0" style="color:#0f172a;font-size:.93rem;">{{ $module['title'] }}</p>
                            <p class="mb-0" style="color:#64748b;font-size:.8rem;">{{ $module['description'] }}</p>
                        </div>
                        <span style="color:#94a3b8;font-size:.75rem;white-space:nowrap;">{{ count($module['lessons']) }} lecciones</span>
                    </div>
                    {{-- Lista de lecciones --}}
                    @foreach($module['lessons'] as $i => $lesson)
                    @php
                        $mNum = $loop->parent->index + 1;
                        $lNum = $i + 1;
                        $hasContent = \App\Http\Controllers\CursosController::lessonHasContent($course['slug'], $mNum, $lNum);
                    @endphp
                    <div class="d-flex align-items-center gap-3 px-4 py-2" style="{{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }}">
                        <div style="width:20px;height:20px;background:{{ $hasContent ? $course['badge_color'] . '15' : '#f1f5f9' }};border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-{{ $hasContent ? 'play' : 'lock' }}" style="color:{{ $hasContent ? $course['badge_color'] : '#94a3b8' }};font-size:.6rem;"></i>
                        </div>
                        @if($hasContent)
                            <a href="{{ route('cursos.lesson', [$course['slug'], $mNum, $lNum]) }}"
                               style="color:#334155;font-size:.85rem;flex-grow:1;text-decoration:none;font-weight:500;">
                                {{ $lesson }}
                            </a>
                            @if($lNum === 1 && $mNum === 1)
                            <span style="font-size:.7rem;color:#00c896;font-weight:600;white-space:nowrap;">
                                <i class="fas fa-lock-open me-1" style="font-size:.65rem;"></i>Disponible
                            </span>
                            @endif
                        @else
                            <span style="color:#94a3b8;font-size:.85rem;flex-grow:1;">{{ $lesson }}</span>
                            <span style="font-size:.7rem;color:#94a3b8;white-space:nowrap;">Próximamente</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>

            {{-- Nota --}}
            <div class="mt-5 p-3" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.5rem;">
                <p class="mb-0" style="color:#94a3b8;font-size:.8rem;line-height:1.6;">
                    <i class="fas fa-info-circle me-1"></i>
                    El contenido de este curso es desarrollado por el equipo editorial de ConocIA. Las lecciones se actualizan periódicamente para reflejar los cambios regulatorios y tecnológicos más recientes.
                </p>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div style="position:sticky;top:88px;z-index:10;">

                {{-- CTA principal --}}
                <div class="profundiza-card p-4 mb-4">
                    <div class="text-center mb-3">
                        <div style="width:56px;height:56px;background:{{ $course['badge_color'] }}18;border-radius:1rem;display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;">
                            <i class="fas {{ $course['icon'] }}" style="color:{{ $course['badge_color'] }};font-size:1.4rem;"></i>
                        </div>
                        <div class="d-flex justify-content-center gap-3 mb-3" style="font-size:.8rem;color:#64748b;">
                            <span><i class="fas fa-layer-group me-1" style="color:{{ $course['badge_color'] }};"></i>{{ $course['modules_count'] }} módulos</span>
                            <span><i class="fas fa-book me-1" style="color:{{ $course['badge_color'] }};"></i>{{ $course['lessons_count'] }} lecciones</span>
                        </div>
                    </div>

                    @auth
                        <a href="#" class="btn btn-primary w-100 mb-2" style="font-size:.9rem;">
                            <i class="fas fa-play me-1"></i>Comenzar curso
                        </a>
                        <p class="mb-0 text-center" style="color:#94a3b8;font-size:.78rem;">Tu progreso se guarda automáticamente</p>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary w-100 mb-2" style="font-size:.9rem;">
                            <i class="fas fa-lock-open me-1"></i>Registrarme gratis para acceder
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 btn-sm mb-3" style="font-size:.82rem;">
                            Ya tengo cuenta — Iniciar sesión
                        </a>
                        <p class="mb-0 text-center" style="color:#94a3b8;font-size:.77rem;">
                            Sin costo. Sin tarjeta de crédito.
                        </p>
                    @endauth
                </div>

                {{-- Ficha del curso --}}
                <div class="profundiza-card p-4 mb-4">
                    <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:.95rem;border-bottom:2px solid rgba(56,182,255,.15);padding-bottom:.75rem;">
                        Ficha del curso
                    </h3>
                    <dl style="font-size:.85rem;margin:0;">
                        <dt style="color:#64748b;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.2rem;">Área</dt>
                        <dd class="mb-3" style="color:#334155;">{{ $course['badge'] }}</dd>

                        <dt style="color:#64748b;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.2rem;">Para quién</dt>
                        <dd class="mb-3" style="color:#334155;">{{ $course['audience'] }}</dd>

                        <dt style="color:#64748b;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.2rem;">Nivel</dt>
                        <dd class="mb-3" style="color:#334155;">{{ $course['level'] }}</dd>

                        <dt style="color:#64748b;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.2rem;">Contenido</dt>
                        <dd class="mb-0" style="color:#334155;">{{ $course['modules_count'] }} módulos · {{ $course['lessons_count'] }} lecciones</dd>
                    </dl>
                </div>

                {{-- Otros cursos --}}
                @if(count($others) > 0)
                <div class="profundiza-card p-4">
                    <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:.92rem;">Otros cursos</h3>
                    <div class="d-flex flex-column gap-2">
                        @foreach($others as $other)
                        <a href="{{ route('cursos.show', $other['slug']) }}"
                           style="text-decoration:none;border:1px solid #e2e8f0;border-radius:.5rem;padding:.75rem;display:block;transition:border-color .2s;"
                           onmouseover="this.style.borderColor='{{ $other['badge_color'] }}'" onmouseout="this.style.borderColor='#e2e8f0'">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;background:{{ $other['badge_color'] }}18;border-radius:.4rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas {{ $other['icon'] }}" style="color:{{ $other['badge_color'] }};font-size:.75rem;"></i>
                                </div>
                                <div>
                                    <p class="fw-semibold mb-0" style="color:#0f172a;font-size:.82rem;line-height:1.3;">{{ $other['title'] }}</p>
                                    <span class="badge mt-1" style="background:{{ $other['badge_color'] }}15;color:{{ $other['badge_color'] }};font-size:.65rem;">{{ $other['badge'] }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <div class="mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
                        <a href="{{ route('cursos.index') }}" class="btn btn-sm btn-outline-secondary w-100" style="font-size:.8rem;">
                            Ver todos los cursos
                        </a>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>

@endsection
