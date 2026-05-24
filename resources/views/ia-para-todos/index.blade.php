@extends('layouts.app')

@section('title', 'IA para Todos — Curso gratuito de alfabetización en IA | ConocIA')
@section('meta_description', 'Aprende qué es la inteligencia artificial, cómo te afecta y qué derechos tienes frente a decisiones automatizadas. Curso gratuito en español, sin jerga técnica.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(0,200,150,.2);color:#00c896;font-size:.78rem;letter-spacing:.06em;">APRENDE</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">IA para Todos</h1>
                <p class="text-white fw-semibold mb-2" style="font-size:1.1rem;">Curso gratuito de alfabetización en inteligencia artificial</p>
                <p style="color:#94a3b8;font-size:1rem;line-height:1.7;max-width:580px;">
                    Un programa educativo diseñado para ciudadanos sin formación técnica. Aprende qué es la IA, cómo te afecta y qué derechos tienes frente a decisiones automatizadas.
                </p>
            </div>
            <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:2rem;" class="text-center">
                    <i class="fas fa-graduation-cap" style="font-size:2.5rem;color:#00c896;display:block;margin-bottom:.75rem;"></i>
                    <div class="text-white fw-bold">Módulo 1 disponible</div>
                    <div style="color:#64748b;font-size:.85rem;margin-top:.3rem;">Más módulos próximamente</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    <div class="row g-5">
        {{-- Contenido principal --}}
        <div class="col-lg-8">

            {{-- Badge módulo --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="badge px-3 py-2" style="background:#00c89622;color:#00c896;border:1px solid #00c89644;font-size:.82rem;">
                    MÓDULO 1
                </span>
                <span class="fw-bold" style="color:#0f172a;font-size:1.3rem;">¿Qué es la Inteligencia Artificial?</span>
            </div>

            {{-- Lección 1.1 --}}
            <div class="mb-5">
                <h2 class="fw-bold mb-3" style="color:#0f172a;font-size:1.15rem;">
                    <span style="color:#00c896;font-size:.9rem;display:block;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.05em;">Lección 1.1</span>
                    IA en tu vida diaria
                </h2>
                <div style="color:#475569;font-size:.97rem;line-height:1.85;">
                    <p>Probablemente ya usas inteligencia artificial varias veces al día sin saberlo. Cuando Netflix te recomienda una serie que termina gustándote, hay un algoritmo de IA que analizó tu historial y el de millones de usuarios para hacer esa sugerencia. No fue un humano quien eligió para ti.</p>
                    <p>Cuando tu banco detecta un cargo sospechoso en tu tarjeta a las 3 de la mañana y la bloquea automáticamente, también es IA. El sistema aprendió qué patrones de gasto son normales para ti y cuáles se desvían de ese patrón.</p>
                    <p>Lo mismo ocurre cuando el GPS te propone una ruta alternativa por un accidente que no sabías que existía, cuando tu teléfono reconoce tu cara para desbloquearse, cuando un correo de phishing termina en tu carpeta de spam o cuando el autocorrector cambia lo que escribiste. En todos esos casos, hay un sistema de IA trabajando en segundo plano.</p>
                    <p>La IA no es ciencia ficción ni cosa del futuro. Es tecnología que ya usas todos los días, a menudo sin notarlo.</p>
                </div>
            </div>

            {{-- Separador --}}
            <hr style="border-color:#e2e8f0;margin:2.5rem 0;">

            {{-- Lección 1.2 --}}
            <div class="mb-5">
                <h2 class="fw-bold mb-3" style="color:#0f172a;font-size:1.15rem;">
                    <span style="color:#00c896;font-size:.9rem;display:block;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.05em;">Lección 1.2</span>
                    ¿Qué es y qué NO es la IA?
                </h2>
                <div style="color:#475569;font-size:.97rem;line-height:1.85;">
                    <p>En términos simples, la inteligencia artificial es software que aprende de datos y encuentra patrones. En lugar de seguir reglas escritas por un programador ("si pasa X, haz Y"), un sistema de IA aprende esas reglas por sí mismo al procesar miles o millones de ejemplos.</p>

                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #00c896;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
                        <p class="fw-bold mb-2" style="color:#166534;">Lo que SÍ es la IA</p>
                        <p style="color:#166534;margin:0;font-size:.93rem;">Software que aprende patrones a partir de datos para tomar decisiones o hacer predicciones en tareas específicas.</p>
                    </div>

                    <div style="background:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #f43f5e;border-radius:.5rem;padding:1.25rem;margin:1.5rem 0;">
                        <p class="fw-bold mb-2" style="color:#9f1239;">Lo que NO es la IA</p>
                        <ul style="color:#9f1239;margin:0;font-size:.93rem;padding-left:1.2rem;">
                            <li>No es consciente. No "sabe" que existe.</li>
                            <li>No piensa. Procesa información según patrones aprendidos.</li>
                            <li>No siente. No tiene emociones, motivaciones ni intenciones.</li>
                            <li>No tiene objetivos propios. Hace lo que fue diseñada para hacer.</li>
                        </ul>
                    </div>

                    <p>Cuando ChatGPT te responde con algo que parece reflexivo o empático, no está pensando ni sintiendo. Está generando texto que estadísticamente tiene sentido dado lo que escribiste, basado en miles de millones de ejemplos de texto humano que procesó durante su entrenamiento. El resultado puede ser impresionante, pero el mecanismo es muy diferente al pensamiento humano.</p>
                </div>
            </div>

            <hr style="border-color:#e2e8f0;margin:2.5rem 0;">

            {{-- Lección 1.3 --}}
            <div class="mb-5">
                <h2 class="fw-bold mb-3" style="color:#0f172a;font-size:1.15rem;">
                    <span style="color:#00c896;font-size:.9rem;display:block;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.05em;">Lección 1.3</span>
                    Tipos de IA que existen
                </h2>
                <div style="color:#475569;font-size:.97rem;line-height:1.85;">
                    <p>Se habla mucho de IA como si fuera una sola cosa, pero en realidad hay distintos niveles de lo que la IA puede hacer (o podría hacer en el futuro). Es importante distinguirlos porque confundirlos genera expectativas o miedos equivocados.</p>

                    <div class="row g-3 my-3">
                        <div class="col-md-4">
                            <div class="profundiza-card p-3 h-100 text-center">
                                <div style="font-size:1.8rem;margin-bottom:.5rem;">🎯</div>
                                <div class="fw-bold mb-2" style="color:#0f172a;font-size:.95rem;">IA Estrecha</div>
                                <div style="color:#475569;font-size:.85rem;line-height:1.6;">Hace una tarea específica muy bien. Es <strong>toda la IA que existe hoy</strong>: reconocer imágenes, traducir texto, recomendar contenido.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="profundiza-card p-3 h-100 text-center">
                                <div style="font-size:1.8rem;margin-bottom:.5rem;">🧠</div>
                                <div class="fw-bold mb-2" style="color:#0f172a;font-size:.95rem;">IA General</div>
                                <div style="color:#475569;font-size:.85rem;line-height:1.6;">Hipotética. Podría hacer cualquier tarea cognitiva que hace un humano. <strong>No existe todavía.</strong> Es tema de investigación y debate.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="profundiza-card p-3 h-100 text-center">
                                <div style="font-size:1.8rem;margin-bottom:.5rem;">🚀</div>
                                <div class="fw-bold mb-2" style="color:#0f172a;font-size:.95rem;">IA Superinteligente</div>
                                <div style="color:#475569;font-size:.85rem;line-height:1.6;">Especulación. Superaría la inteligencia humana en todo. <strong>No existe y nadie sabe si es posible.</strong> Tema de ciencia ficción y filosofía.</div>
                            </div>
                        </div>
                    </div>

                    <p>La clave: <strong>toda la IA que usas hoy es estrecha.</strong> GPT-4 es muy bueno generando texto, pero no puede conducir un auto. Un sistema de visión por computadora puede detectar tumores en una radiografía, pero no puede escribir un email. Cada sistema de IA es muy bueno en una cosa, y completamente inútil fuera de esa cosa.</p>
                </div>
            </div>

            <hr style="border-color:#e2e8f0;margin:2.5rem 0;">

            {{-- Lección 1.4 --}}
            <div class="mb-5">
                <h2 class="fw-bold mb-3" style="color:#0f172a;font-size:1.15rem;">
                    <span style="color:#00c896;font-size:.9rem;display:block;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.05em;">Lección 1.4</span>
                    ¿Cómo aprende una IA?
                </h2>
                <div style="color:#475569;font-size:.97rem;line-height:1.85;">
                    <p>Imagina un niño que nunca ha visto un gato. La primera vez que le muestras uno y dices "esto es un gato", aprende algo. Después de ver cien gatos de distintos colores, tamaños y poses, empieza a reconocer qué características tienen en común. Después de ver miles, puede identificar un gato aunque sea una foto borrosa o un dibujo que nunca ha visto.</p>
                    <p>Una IA aprende de la misma forma, pero con datos en lugar de experiencias. Para que un sistema de IA aprenda a reconocer gatos en fotos, se le muestran millones de imágenes etiquetadas: "esto es un gato", "esto no es un gato". El sistema ajusta sus parámetros internos hasta que puede distinguirlos correctamente.</p>
                    <p>Este proceso se llama <strong>entrenamiento</strong>. Y la calidad del resultado depende directamente de la calidad y cantidad de los datos de entrenamiento. Si los datos tienen errores o sesgos, el modelo los aprende también.</p>

                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.75rem;padding:1.5rem;margin-top:1.5rem;">
                        <p class="fw-bold mb-2" style="color:#0f172a;font-size:.93rem;">📌 Para recordar</p>
                        <p style="color:#475569;font-size:.9rem;margin:0;">La IA no "entiende" lo que aprende en el sentido humano. Encuentra patrones matemáticos en los datos. Si hay suficientes ejemplos buenos, los patrones que encuentra son muy útiles. Pero si los datos son malos, el modelo también será malo — aunque parezca seguro de sí mismo.</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Progreso --}}
            <div class="profundiza-card p-4 mb-4 sticky-top" style="top:80px;">
                <h4 class="fw-bold mb-3" style="color:#0f172a;font-size:.97rem;">Contenido del curso</h4>

                {{-- Módulo 1 activo --}}
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.5rem;padding:1rem;margin-bottom:.75rem;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span style="width:24px;height:24px;background:#00c896;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-check" style="color:white;font-size:.7rem;"></i>
                        </span>
                        <span class="fw-bold" style="color:#166534;font-size:.88rem;">Módulo 1</span>
                    </div>
                    <p class="mb-0" style="color:#166534;font-size:.82rem;padding-left:2rem;">¿Qué es la Inteligencia Artificial?</p>
                    <div style="padding-left:2rem;margin-top:.5rem;">
                        <div style="color:#166534;font-size:.78rem;">✓ 1.1 IA en tu vida diaria</div>
                        <div style="color:#166534;font-size:.78rem;">✓ 1.2 ¿Qué es y qué no es?</div>
                        <div style="color:#166534;font-size:.78rem;">✓ 1.3 Tipos de IA</div>
                        <div style="color:#166534;font-size:.78rem;">✓ 1.4 ¿Cómo aprende?</div>
                    </div>
                </div>

                {{-- Módulos futuros --}}
                @php
                    $upcoming = [
                        'Módulo 2' => '¿Cómo me afecta la IA en mi trabajo?',
                        'Módulo 3' => '¿Qué derechos tengo frente a decisiones de una IA?',
                        'Módulo 4' => '¿Cómo usar herramientas de IA de forma segura?',
                        'Módulo 5' => 'IA y sociedad: beneficios, riesgos y debates abiertos',
                    ];
                @endphp
                @foreach($upcoming as $num => $title)
                <div style="border:1px solid #e2e8f0;border-radius:.5rem;padding:.75rem 1rem;margin-bottom:.5rem;opacity:.6;">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width:24px;height:24px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-lock" style="color:#94a3b8;font-size:.65rem;"></i>
                        </span>
                        <div>
                            <span class="fw-bold" style="color:#64748b;font-size:.82rem;">{{ $num }}</span>
                            <span class="badge ms-1" style="background:#fef3c7;color:#b45309;font-size:.65rem;">Próximamente</span>
                        </div>
                    </div>
                    <p class="mb-0 mt-1" style="color:#94a3b8;font-size:.78rem;padding-left:2rem;">{{ $title }}</p>
                </div>
                @endforeach

                {{-- CTA notificación --}}
                <div class="mt-4 pt-3" style="border-top:1px solid #e2e8f0;">
                    <p style="color:#475569;font-size:.82rem;margin-bottom:.75rem;">¿Quieres que te avisemos cuando salgan nuevos módulos?</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-sm">
                            <input type="email" name="email" class="form-control" placeholder="tu@email.cl" required>
                            <button type="submit" class="btn btn-primary" style="font-size:.8rem;">Avisar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
