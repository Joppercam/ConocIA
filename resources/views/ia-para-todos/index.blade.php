@extends('layouts.app')

@section('title', 'IA para Todos — Curso gratuito de alfabetización en IA | ConocIA')
@section('meta_description', 'Aprende qué es la inteligencia artificial, cómo te afecta y qué derechos tienes frente a decisiones automatizadas. Curso gratuito en español, sin jerga técnica.')

@section('reading_progress', true)

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(0,200,150,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(0,200,150,.2);color:#00c896;font-size:.78rem;letter-spacing:.06em;">APRENDE</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">IA para Todos</h1>
                <p class="text-white fw-semibold mb-2" style="font-size:1.05rem;">Curso gratuito de alfabetización en inteligencia artificial</p>
                <p style="color:#94a3b8;font-size:.97rem;line-height:1.7;max-width:580px;">
                    Un programa educativo para ciudadanos sin formación técnica. Aprende qué es la IA, cómo cambia tu trabajo, qué derechos tienes y cómo usarla de forma inteligente.
                </p>
            </div>
            <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:1.75rem;" class="text-center">
                    <i class="fas fa-graduation-cap" style="font-size:2.2rem;color:#00c896;display:block;margin-bottom:.75rem;"></i>
                    <div class="fw-bold text-white" style="font-size:1.1rem;">5 módulos</div>
                    <div style="color:#64748b;font-size:.85rem;margin-top:.3rem;">20 lecciones · Gratis</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5">

        {{-- Sidebar: índice de lecciones --}}
        <div class="col-lg-3">
            <nav class="ipa-sidebar" style="position:sticky;top:88px;z-index:10;max-height:calc(100vh - 110px);overflow-y:auto;padding-right:.25rem;">
                <p class="text-uppercase fw-semibold mb-3" style="color:#94a3b8;font-size:.7rem;letter-spacing:.1em;">Contenido del curso</p>

                {{-- Módulo 1 --}}
                <div class="ipa-module mb-1">
                    <div class="ipa-module-header">
                        <span class="ipa-module-num" style="background:#00c89622;color:#00c896;">1</span>
                        <span>¿Qué es la IA?</span>
                    </div>
                    <div class="ipa-lessons">
                        <button class="ipa-lesson-btn active" data-target="lesson-1-1">1.1 IA en tu vida diaria</button>
                        <button class="ipa-lesson-btn" data-target="lesson-1-2">1.2 ¿Qué es y qué no es?</button>
                        <button class="ipa-lesson-btn" data-target="lesson-1-3">1.3 Tipos de IA</button>
                        <button class="ipa-lesson-btn" data-target="lesson-1-4">1.4 ¿Cómo aprende?</button>
                    </div>
                </div>

                {{-- Módulo 2 --}}
                <div class="ipa-module mb-1">
                    <div class="ipa-module-header">
                        <span class="ipa-module-num" style="background:#38b6ff22;color:#38b6ff;">2</span>
                        <span>IA y tu trabajo</span>
                    </div>
                    <div class="ipa-lessons">
                        <button class="ipa-lesson-btn" data-target="lesson-2-1">2.1 ¿Qué trabajos cambian?</button>
                        <button class="ipa-lesson-btn" data-target="lesson-2-2">2.2 Habilidades difíciles de automatizar</button>
                        <button class="ipa-lesson-btn" data-target="lesson-2-3">2.3 Usa la IA a tu favor</button>
                        <button class="ipa-lesson-btn" data-target="lesson-2-4">2.4 Derechos laborales</button>
                    </div>
                </div>

                {{-- Módulo 3 --}}
                <div class="ipa-module mb-1">
                    <div class="ipa-module-header">
                        <span class="ipa-module-num" style="background:#a78bfa22;color:#a78bfa;">3</span>
                        <span>Tus derechos</span>
                    </div>
                    <div class="ipa-lessons">
                        <button class="ipa-lesson-btn" data-target="lesson-3-1">3.1 Cuando la IA decide sobre ti</button>
                        <button class="ipa-lesson-btn" data-target="lesson-3-2">3.2 Tus datos personales</button>
                        <button class="ipa-lesson-btn" data-target="lesson-3-3">3.3 Derecho a explicación</button>
                        <button class="ipa-lesson-btn" data-target="lesson-3-4">3.4 Cómo reclamar en Chile</button>
                    </div>
                </div>

                {{-- Módulo 4 --}}
                <div class="ipa-module mb-1">
                    <div class="ipa-module-header">
                        <span class="ipa-module-num" style="background:#f59e0b22;color:#f59e0b;">4</span>
                        <span>Usar IA con criterio</span>
                    </div>
                    <div class="ipa-lessons">
                        <button class="ipa-lesson-btn" data-target="lesson-4-1">4.1 Guía de herramientas IA</button>
                        <button class="ipa-lesson-btn" data-target="lesson-4-2">4.2 Qué no compartir con la IA</button>
                        <button class="ipa-lesson-btn" data-target="lesson-4-3">4.3 Desinformación generada por IA</button>
                        <button class="ipa-lesson-btn" data-target="lesson-4-4">4.4 IA y productividad</button>
                    </div>
                </div>

                {{-- Módulo 5 --}}
                <div class="ipa-module">
                    <div class="ipa-module-header">
                        <span class="ipa-module-num" style="background:#f472b622;color:#f472b6;">5</span>
                        <span>IA y sociedad</span>
                    </div>
                    <div class="ipa-lessons">
                        <button class="ipa-lesson-btn" data-target="lesson-5-1">5.1 Beneficios reales</button>
                        <button class="ipa-lesson-btn" data-target="lesson-5-2">5.2 Sesgos algorítmicos</button>
                        <button class="ipa-lesson-btn" data-target="lesson-5-3">5.3 El debate del control</button>
                        <button class="ipa-lesson-btn" data-target="lesson-5-4">5.4 Chile y la IA</button>
                    </div>
                </div>

                <div class="mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
                    <p style="color:#94a3b8;font-size:.75rem;line-height:1.5;margin-bottom:.75rem;">¿Quieres avisos de nuevas lecciones?</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-sm">
                            <input type="email" name="email" class="form-control" placeholder="tu@email.cl" required style="font-size:.8rem;">
                            <button type="submit" class="btn btn-primary" style="font-size:.78rem;">Suscribir</button>
                        </div>
                    </form>
                </div>
            </nav>
        </div>

        {{-- Paneles de lecciones --}}
        <div class="col-lg-9">

            {{-- ============================================================
                 MÓDULO 1
            ============================================================ --}}

            {{-- Lección 1.1 --}}
            <div data-panel="lesson-1-1">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#00c89622;color:#00c896;border-color:#00c89644;">Módulo 1</span>
                    <span class="ipa-lesson-num" style="color:#00c896;">Lección 1.1</span>
                </div>
                <h2 class="ipa-lesson-title">IA en tu vida diaria</h2>

                <p>Probablemente ya usas inteligencia artificial varias veces al día sin saberlo. Cuando Netflix te recomienda una serie que termina gustándote, hay un algoritmo de IA que analizó tu historial y el de millones de usuarios para hacer esa sugerencia. No fue un humano quien eligió para ti.</p>
                <p>Cuando tu banco detecta un cargo sospechoso en tu tarjeta a las 3 de la mañana y la bloquea automáticamente, también es IA. El sistema aprendió qué patrones de gasto son normales para ti y cuáles se desvían de ese patrón. Lo mismo ocurre cuando el GPS te propone una ruta alternativa por un accidente que no sabías que existía, cuando tu teléfono reconoce tu cara para desbloquearse, o cuando un correo de phishing termina en tu carpeta de spam.</p>
                <p>En todos esos casos hay un sistema de IA trabajando en segundo plano. No es ciencia ficción ni cosa del futuro. Es tecnología que ya usas todos los días, a menudo sin notarlo.</p>

                <div class="ipa-callout" style="border-left-color:#00c896;background:#f0fdf4;border-color:#bbf7d0;">
                    <p class="fw-bold mb-2" style="color:#166534;">¿En cuántos sistemas de IA participas hoy?</p>
                    <p style="color:#166534;margin:0;font-size:.92rem;">Spotify, YouTube, Gmail, WhatsApp (spam), Google Maps, Instagram (feed), tu banco (fraude), el buscador de Google, el autocorrector del teclado... probablemente más de 10 antes del mediodía.</p>
                </div>

                <p>Lo interesante no es solo que la IA está en todas partes, sino que toma decisiones que antes tomaban personas. Eso tiene consecuencias que vale la pena entender: quién diseñó esos sistemas, qué datos usaron, qué errores pueden cometer y qué pasa cuando se equivocan con algo que te importa.</p>

                <div class="ipa-nav-bottom">
                    <span></span>
                    <button class="ipa-nav-btn" data-target="lesson-1-2">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 1.2 --}}
            <div data-panel="lesson-1-2" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#00c89622;color:#00c896;border-color:#00c89644;">Módulo 1</span>
                    <span class="ipa-lesson-num" style="color:#00c896;">Lección 1.2</span>
                </div>
                <h2 class="ipa-lesson-title">¿Qué es y qué NO es la IA?</h2>

                <p>En términos simples, la inteligencia artificial es software que aprende de datos y encuentra patrones. En lugar de seguir reglas escritas por un programador ("si pasa X, haz Y"), un sistema de IA aprende esas reglas por sí mismo al procesar miles o millones de ejemplos.</p>

                <div class="ipa-callout" style="border-left-color:#00c896;background:#f0fdf4;border-color:#bbf7d0;">
                    <p class="fw-bold mb-2" style="color:#166534;">Lo que SÍ es la IA</p>
                    <p style="color:#166534;margin:0;font-size:.92rem;">Software que aprende patrones a partir de datos para tomar decisiones o hacer predicciones en tareas específicas.</p>
                </div>

                <div class="ipa-callout" style="border-left-color:#f43f5e;background:#fff1f2;border-color:#fecdd3;">
                    <p class="fw-bold mb-2" style="color:#9f1239;">Lo que NO es la IA</p>
                    <ul style="color:#9f1239;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>No es consciente.</strong> No "sabe" que existe.</li>
                        <li><strong>No piensa.</strong> Procesa información según patrones aprendidos.</li>
                        <li><strong>No siente.</strong> No tiene emociones, motivaciones ni intenciones.</li>
                        <li><strong>No tiene objetivos propios.</strong> Hace lo que fue diseñada para hacer.</li>
                    </ul>
                </div>

                <p>Cuando ChatGPT te responde con algo que parece reflexivo o empático, no está pensando ni sintiendo. Está generando texto que estadísticamente tiene sentido dado lo que escribiste, basado en miles de millones de ejemplos de texto humano que procesó durante su entrenamiento. El resultado puede ser impresionante, pero el mecanismo es muy diferente al pensamiento humano.</p>
                <p>Esta distinción importa porque cuando creemos que la IA "piensa" o "entiende", tendemos a confiar demasiado en sus respuestas. Y los sistemas de IA cometen errores —a veces absurdos— que un humano nunca cometería.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-1-1"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-1-3">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 1.3 --}}
            <div data-panel="lesson-1-3" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#00c89622;color:#00c896;border-color:#00c89644;">Módulo 1</span>
                    <span class="ipa-lesson-num" style="color:#00c896;">Lección 1.3</span>
                </div>
                <h2 class="ipa-lesson-title">Tipos de IA que existen</h2>

                <p>Se habla mucho de IA como si fuera una sola cosa, pero en realidad hay distintos niveles de lo que la IA puede hacer. Es importante distinguirlos porque confundirlos genera expectativas o miedos equivocados.</p>

                <div class="row g-3 my-3">
                    <div class="col-md-4">
                        <div style="border:1px solid #e2e8f0;border-radius:.75rem;padding:1.25rem;height:100%;text-align:center;">
                            <div style="font-size:1.8rem;margin-bottom:.5rem;">🎯</div>
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.95rem;">IA Estrecha</div>
                            <div style="color:#475569;font-size:.85rem;line-height:1.6;">Hace una tarea específica muy bien. Es <strong>toda la IA que existe hoy</strong>: reconocer imágenes, traducir texto, recomendar contenido.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="border:1px solid #e2e8f0;border-radius:.75rem;padding:1.25rem;height:100%;text-align:center;">
                            <div style="font-size:1.8rem;margin-bottom:.5rem;">🧠</div>
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.95rem;">IA General</div>
                            <div style="color:#475569;font-size:.85rem;line-height:1.6;">Hipotética. Podría hacer cualquier tarea cognitiva que hace un humano. <strong>No existe todavía.</strong> Es tema de investigación y debate.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="border:1px solid #e2e8f0;border-radius:.75rem;padding:1.25rem;height:100%;text-align:center;">
                            <div style="font-size:1.8rem;margin-bottom:.5rem;">🚀</div>
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.95rem;">Superinteligencia</div>
                            <div style="color:#475569;font-size:.85rem;line-height:1.6;">Especulación. Superaría la inteligencia humana en todo. <strong>No existe.</strong> Tema de filosofía y ciencia ficción.</div>
                        </div>
                    </div>
                </div>

                <p>La clave: <strong>toda la IA que usas hoy es estrecha.</strong> GPT-4 es muy bueno generando texto, pero no puede conducir un auto. Un sistema de visión por computadora puede detectar tumores en una radiografía, pero no puede escribir un email. Cada sistema de IA es muy bueno en una cosa, y completamente inútil fuera de esa cosa.</p>

                <div class="ipa-callout" style="border-left-color:#64748b;background:#f8fafc;border-color:#e2e8f0;">
                    <p class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">📌 Para recordar</p>
                    <p style="color:#475569;margin:0;font-size:.9rem;">Cuando los medios hablan de "la IA" como si fuera una entidad que va a "conquistar el mundo", están confundiendo categorías. La IA que existe hoy es poderosa, pero estrecha. Los debates sobre superinteligencia son importantes, pero son sobre el futuro, no el presente.</p>
                </div>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-1-2"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-1-4">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 1.4 --}}
            <div data-panel="lesson-1-4" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#00c89622;color:#00c896;border-color:#00c89644;">Módulo 1</span>
                    <span class="ipa-lesson-num" style="color:#00c896;">Lección 1.4</span>
                </div>
                <h2 class="ipa-lesson-title">¿Cómo aprende una IA?</h2>

                <p>Imagina un niño que nunca ha visto un gato. La primera vez que le muestras uno y dices "esto es un gato", aprende algo. Después de ver cien gatos de distintos colores, tamaños y poses, empieza a reconocer qué características tienen en común. Después de ver miles, puede identificar un gato aunque sea una foto borrosa o un dibujo que nunca ha visto.</p>
                <p>Una IA aprende de la misma forma, pero con datos en lugar de experiencias. Para que un sistema aprenda a reconocer gatos en fotos, se le muestran millones de imágenes etiquetadas: "esto es un gato", "esto no es un gato". El sistema ajusta sus parámetros internos —millones o miles de millones de números— hasta que puede distinguirlos correctamente.</p>
                <p>Este proceso se llama <strong>entrenamiento</strong>. Y la calidad del resultado depende directamente de la calidad y cantidad de los datos de entrenamiento. Si los datos tienen errores o sesgos, el modelo los aprende también.</p>

                <div class="ipa-callout" style="border-left-color:#64748b;background:#f8fafc;border-color:#e2e8f0;">
                    <p class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">📌 Para recordar</p>
                    <p style="color:#475569;margin:0;font-size:.9rem;">La IA no "entiende" lo que aprende en el sentido humano. Encuentra patrones matemáticos en los datos. Si hay suficientes ejemplos buenos, los patrones son muy útiles. Pero si los datos son malos —incompletos, sesgados o incorrectos— el modelo también será malo, aunque parezca muy seguro de sí mismo.</p>
                </div>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-1-3"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-2-1">Siguiente módulo <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>


            {{-- ============================================================
                 MÓDULO 2 · IA y tu trabajo
            ============================================================ --}}

            {{-- Lección 2.1 --}}
            <div data-panel="lesson-2-1" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#38b6ff22;color:#38b6ff;border-color:#38b6ff44;">Módulo 2</span>
                    <span class="ipa-lesson-num" style="color:#38b6ff;">Lección 2.1</span>
                </div>
                <h2 class="ipa-lesson-title">¿Qué trabajos están cambiando con la IA?</h2>

                <p>La pregunta que más escuchamos es "¿la IA va a quitarme el trabajo?". La respuesta honesta es: depende, y nadie lo sabe con certeza. Lo que sí podemos decir es que la IA está transformando casi todos los sectores, pero de formas muy distintas según qué tipo de tareas realizas.</p>
                <p>Los trabajos más expuestos a la automatización son aquellos que involucran tareas repetitivas, predecibles y basadas en reglas: procesamiento de documentos, atención al cliente rutinaria, clasificación de datos, traducción básica, generación de reportes estándar. No es que desaparezcan de golpe, sino que cada vez requieren menos horas humanas.</p>

                <div class="ipa-callout" style="border-left-color:#38b6ff;background:#f0f9ff;border-color:#bae6fd;">
                    <p class="fw-bold mb-2" style="color:#0369a1;">Tareas más automatizables</p>
                    <ul style="color:#0369a1;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li>Ingreso y clasificación de datos</li>
                        <li>Revisión básica de documentos y contratos</li>
                        <li>Atención al cliente con preguntas frecuentes</li>
                        <li>Generación de informes y reportes rutinarios</li>
                        <li>Traducción de textos simples</li>
                        <li>Contabilidad básica y conciliación bancaria</li>
                    </ul>
                </div>

                <p>Pero automatizar una tarea no equivale a eliminar un trabajo. Muchos roles evolucionan: el contador que antes revisaba miles de facturas manualmente ahora supervisa el sistema que lo hace automáticamente y se enfoca en el análisis estratégico. El médico que antes leía cada radiografía ahora trabaja con IA que le señala las zonas sospechosas, y él toma la decisión final. La transformación suele ser gradual y los roles se adaptan.</p>
                <p>También hay trabajos que crecen con la IA: especialistas en datos, personas que saben usar y supervisar sistemas de IA, expertos en ética tecnológica, y profesionales que combinan conocimiento técnico con habilidades humanas que la IA no puede replicar.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-1-4"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-2-2">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 2.2 --}}
            <div data-panel="lesson-2-2" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#38b6ff22;color:#38b6ff;border-color:#38b6ff44;">Módulo 2</span>
                    <span class="ipa-lesson-num" style="color:#38b6ff;">Lección 2.2</span>
                </div>
                <h2 class="ipa-lesson-title">Habilidades difíciles de automatizar</h2>

                <p>Si bien la IA avanza rápido, hay un conjunto de habilidades humanas que resultan notablemente difíciles de replicar. Entender cuáles son es clave para orientar tu desarrollo profesional.</p>

                <div class="row g-3 my-3">
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #38b6ff;border-radius:.5rem;padding:1.1rem;height:100%;">
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.92rem;"><i class="fas fa-heart me-2" style="color:#38b6ff;"></i>Empatía y cuidado humano</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.65;">Enfermería, terapia, trabajo social, docencia personalizada. La IA puede asistir, pero la conexión humana genuina es irreemplazable.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #38b6ff;border-radius:.5rem;padding:1.1rem;height:100%;">
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.92rem;"><i class="fas fa-lightbulb me-2" style="color:#38b6ff;"></i>Creatividad original</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.65;">La IA recombina lo existente, pero la creatividad que surge de experiencias vividas, emociones e intuición sigue siendo profundamente humana.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #38b6ff;border-radius:.5rem;padding:1.1rem;height:100%;">
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.92rem;"><i class="fas fa-balance-scale me-2" style="color:#38b6ff;"></i>Juicio ético y contextual</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.65;">Tomar decisiones en situaciones ambiguas, donde los valores y el contexto importan. La IA no puede asumir responsabilidad moral.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #38b6ff;border-radius:.5rem;padding:1.1rem;height:100%;">
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.92rem;"><i class="fas fa-hands me-2" style="color:#38b6ff;"></i>Destreza física adaptativa</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.65;">Plomeros, electricistas, cirujanos en contextos complejos. La robótica avanza, pero adaptarse a entornos físicos impredecibles sigue siendo muy difícil.</p>
                        </div>
                    </div>
                </div>

                <p>El consejo práctico: no evites la tecnología, pero tampoco reduzcas tu identidad profesional a las tareas que la IA puede hacer más rápido. Invierte en las habilidades que te hacen irreemplazable: comunicación clara, pensamiento crítico, liderazgo, especialización profunda en tu área, y la capacidad de usar IA como herramienta para ampliar lo que puedes hacer.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-2-1"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-2-3">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 2.3 --}}
            <div data-panel="lesson-2-3" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#38b6ff22;color:#38b6ff;border-color:#38b6ff44;">Módulo 2</span>
                    <span class="ipa-lesson-num" style="color:#38b6ff;">Lección 2.3</span>
                </div>
                <h2 class="ipa-lesson-title">Usa la IA a tu favor</h2>

                <p>Más que preguntarse si la IA va a quitarte el trabajo, la pregunta útil es: ¿cómo puedo usar la IA para hacer mejor trabajo? Las personas que aprendan a colaborar con IA van a tener ventaja sobre quienes la ignoren, y también sobre quienes confíen en ella ciegamente.</p>

                <div class="ipa-callout" style="border-left-color:#38b6ff;background:#f0f9ff;border-color:#bae6fd;">
                    <p class="fw-bold mb-2" style="color:#0369a1;">Herramientas útiles según tu área</p>
                    <ul style="color:#0369a1;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>Escritura y comunicación:</strong> ChatGPT, Claude — borradores, resúmenes, corrección de estilo</li>
                        <li><strong>Análisis de datos:</strong> ChatGPT con Code Interpreter, Copilot en Excel</li>
                        <li><strong>Diseño:</strong> Midjourney, DALL-E, Canva IA — genera ideas visuales rápidamente</li>
                        <li><strong>Investigación:</strong> Perplexity, NotebookLM — resume documentos largos</li>
                        <li><strong>Productividad general:</strong> Notion IA, Microsoft Copilot, Google Duet IA</li>
                    </ul>
                </div>

                <p>Lo más importante al usar estas herramientas es mantener tu criterio. La IA comete errores que parecen convincentes: puede inventar citas, confundir hechos, o darte respuestas que suenan bien pero son incorrectas. Siempre verifica la información relevante antes de usarla, especialmente si tiene consecuencias para otras personas.</p>
                <p>Un tip práctico: usa la IA para hacer el primer borrador, no el final. Pídele que te ayude a estructurar un documento, generar ideas, o simplificar un texto complejo. Luego tú revisas, corriges y aportas el contexto que la IA no tiene.</p>

                <div class="ipa-callout" style="border-left-color:#64748b;background:#f8fafc;border-color:#e2e8f0;">
                    <p class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">📌 Regla básica</p>
                    <p style="color:#475569;margin:0;font-size:.9rem;">Si firmas algo o lo presentas como tuyo, debes poder responder por ello. Eso significa que tú lo revisaste y verificaste, no solo que la IA lo generó.</p>
                </div>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-2-2"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-2-4">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 2.4 --}}
            <div data-panel="lesson-2-4" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#38b6ff22;color:#38b6ff;border-color:#38b6ff44;">Módulo 2</span>
                    <span class="ipa-lesson-num" style="color:#38b6ff;">Lección 2.4</span>
                </div>
                <h2 class="ipa-lesson-title">Derechos laborales frente a la IA</h2>

                <p>Cada vez más empresas usan IA en sus procesos de recursos humanos: para filtrar currículums, predecir el rendimiento de empleados, monitorear la productividad, decidir horarios o incluso recomendar despidos. Tienes derecho a saber cuándo esto te afecta.</p>
                <p>En países con regulación avanzada (como la Unión Europea bajo el AI Act), los trabajadores tienen derecho a que no se tomen decisiones laborales significativas basadas únicamente en sistemas automatizados, sin revisión humana. En Chile, esta protección está todavía en construcción, pero el principio es claro: una IA no debería decidir sola si te contratan, ascienden o despiden.</p>

                <div class="ipa-callout" style="border-left-color:#f59e0b;background:#fffbeb;border-color:#fde68a;">
                    <p class="fw-bold mb-2" style="color:#92400e;">Señales de alerta en el trabajo</p>
                    <ul style="color:#92400e;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li>Te dicen que "el sistema" rechazó tu postulación sin más explicación</li>
                        <li>Tu rendimiento se evalúa mediante software de monitoreo constante</li>
                        <li>Recibes advertencias automáticas por métricas sin supervisión humana</li>
                        <li>Se usan análisis de redes sociales o tests con IA en procesos de selección</li>
                    </ul>
                </div>

                <p>Qué puedes hacer: preguntar explícitamente si hay sistemas automatizados en tu evaluación o selección. Pedir explicación sobre las razones de una decisión. Consultar a un abogado laboral si crees que una decisión automatizada fue injusta. Y en Chile, puedes dirigirte a la Dirección del Trabajo si hay indicios de que se vulneran tus derechos.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-2-3"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-3-1">Siguiente módulo <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>


            {{-- ============================================================
                 MÓDULO 3 · Tus derechos
            ============================================================ --}}

            {{-- Lección 3.1 --}}
            <div data-panel="lesson-3-1" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#a78bfa22;color:#a78bfa;border-color:#a78bfa44;">Módulo 3</span>
                    <span class="ipa-lesson-num" style="color:#a78bfa;">Lección 3.1</span>
                </div>
                <h2 class="ipa-lesson-title">Cuando una IA decide sobre ti</h2>

                <p>La IA no solo hace tu vida más conveniente. También toma decisiones que pueden cambiarte la vida: si te aprueban un crédito, si tu CV llega a la segunda etapa de un proceso de selección, qué publicidad ves, qué noticias te muestran, o incluso si un sistema médico te clasifica como paciente de riesgo.</p>
                <p>Estas decisiones se toman a gran escala y de forma automatizada. Un algoritmo de crédito puede revisar 10.000 solicitudes en el tiempo en que un analista humano revisa 10. La eficiencia es real, pero también el riesgo: si el algoritmo tiene un sesgo, ese sesgo se aplica a todos por igual.</p>

                <div class="ipa-callout" style="border-left-color:#a78bfa;background:#faf5ff;border-color:#e9d5ff;">
                    <p class="fw-bold mb-2" style="color:#6d28d9;">Ejemplos concretos en tu vida</p>
                    <ul style="color:#6d28d9;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>Banca:</strong> score crediticio calculado por algoritmo — determina si te prestan dinero y a qué tasa</li>
                        <li><strong>Seguros:</strong> modelos predictivos de riesgo que afectan tu prima</li>
                        <li><strong>Salud:</strong> triaje automatizado en urgencias, priorización de listas de espera</li>
                        <li><strong>Justicia:</strong> en algunos países, algoritmos que sugieren sentencias o libertad condicional</li>
                        <li><strong>Educación:</strong> plataformas que adaptan el currículo según tu "perfil"</li>
                    </ul>
                </div>

                <p>El problema del "sistema caja negra" es que muchas veces ni la empresa que usa el algoritmo sabe exactamente por qué tomó una decisión en particular. El modelo de IA aprende patrones complejos que no son fáciles de explicar en lenguaje humano. Eso dificulta detectar errores y aún más impugnarlos.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-2-4"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-3-2">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 3.2 --}}
            <div data-panel="lesson-3-2" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#a78bfa22;color:#a78bfa;border-color:#a78bfa44;">Módulo 3</span>
                    <span class="ipa-lesson-num" style="color:#a78bfa;">Lección 3.2</span>
                </div>
                <h2 class="ipa-lesson-title">Tus datos personales</h2>

                <p>Cada servicio digital que usas recopila datos sobre ti. Tu historial de búsqueda, tus clics, tus compras, tu ubicación, el tiempo que pasas en cada página, tus contactos, tus mensajes. Todos esos datos alimentan sistemas de IA que aprenden de ti para predecir tu comportamiento, y eventualmente influir en él.</p>

                <div class="ipa-callout" style="border-left-color:#a78bfa;background:#faf5ff;border-color:#e9d5ff;">
                    <p class="fw-bold mb-2" style="color:#6d28d9;">Lo que tienes derecho a saber y hacer</p>
                    <ul style="color:#6d28d9;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>Derecho de acceso:</strong> saber qué datos tiene una empresa sobre ti</li>
                        <li><strong>Derecho de rectificación:</strong> corregir datos incorrectos</li>
                        <li><strong>Derecho al olvido:</strong> pedir que borren tus datos</li>
                        <li><strong>Portabilidad:</strong> obtener tus datos en un formato que puedas usar en otro servicio</li>
                        <li><strong>Oposición:</strong> negarte a que tus datos se usen para ciertos fines</li>
                    </ul>
                </div>

                <p>En Chile, la Ley N° 19.628 de Protección de la Vida Privada regula el tratamiento de datos personales. Sin embargo, data de 1999 y está desactualizada para el contexto de la IA moderna. Se está trabajando en una nueva ley que incorpore estas protecciones de forma más robusta, con una Agencia de Protección de Datos Personales que tendrá capacidad sancionatoria.</p>
                <p>Mientras tanto, la práctica más poderosa que puedes ejercer hoy: revisa los permisos de las apps que usas, limita el rastreo donde puedas, y lee qué dicen las políticas de privacidad de los servicios que más usas.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-3-1"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-3-3">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 3.3 --}}
            <div data-panel="lesson-3-3" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#a78bfa22;color:#a78bfa;border-color:#a78bfa44;">Módulo 3</span>
                    <span class="ipa-lesson-num" style="color:#a78bfa;">Lección 3.3</span>
                </div>
                <h2 class="ipa-lesson-title">El derecho a una explicación</h2>

                <p>Si una IA tomó una decisión que te perjudicó —te rechazó un crédito, filtró tu CV, te clasificó como riesgo alto en un seguro— tienes derecho a saber por qué. No a un resumen técnico de cómo funciona el modelo, sino a una explicación clara de qué factores influyeron en esa decisión.</p>
                <p>En la Unión Europea, el GDPR lo establece explícitamente: las personas tienen derecho a no ser sujeto de decisiones basadas exclusivamente en tratamiento automatizado que les produzcan efectos jurídicos significativos. Tienen derecho a intervención humana, a expresar su punto de vista y a impugnar la decisión.</p>

                <div class="ipa-callout" style="border-left-color:#a78bfa;background:#faf5ff;border-color:#e9d5ff;">
                    <p class="fw-bold mb-2" style="color:#6d28d9;">Cómo ejercer este derecho</p>
                    <ol style="color:#6d28d9;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li>Solicita por escrito una explicación de la decisión que te afectó</li>
                        <li>Pregunta explícitamente si hubo un sistema automatizado involucrado</li>
                        <li>Pide que un humano revise la decisión</li>
                        <li>Si no obtienes respuesta satisfactoria, escala a organismos reguladores</li>
                    </ol>
                </div>

                <p>En Chile, este derecho está menos desarrollado normativamente, pero la nueva ley de protección de datos en tramitación incluye garantías en esta dirección. Lo más importante es que no asumas que una decisión de "el sistema" es final e inapelable. Siempre hay humanos detrás de esos sistemas, y son ellos los responsables.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-3-2"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-3-4">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 3.4 --}}
            <div data-panel="lesson-3-4" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#a78bfa22;color:#a78bfa;border-color:#a78bfa44;">Módulo 3</span>
                    <span class="ipa-lesson-num" style="color:#a78bfa;">Lección 3.4</span>
                </div>
                <h2 class="ipa-lesson-title">Cómo reclamar en Chile</h2>

                <p>Si crees que un sistema de IA vulneró tus derechos en Chile, tienes varias vías disponibles dependiendo del contexto.</p>

                <div class="row g-3 my-2">
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #a78bfa;border-radius:.5rem;padding:1.1rem;">
                            <div class="fw-bold mb-1" style="color:#0f172a;font-size:.9rem;">SERNAC</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.6;">Para problemas de consumo: bancos, seguros, retail, telecomunicaciones. Si un algoritmo te discriminó en un servicio, SERNAC puede mediar o iniciar una investigación.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #a78bfa;border-radius:.5rem;padding:1.1rem;">
                            <div class="fw-bold mb-1" style="color:#0f172a;font-size:.9rem;">Dirección del Trabajo</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.6;">Para problemas laborales: vigilancia indebida, despidos por métricas automatizadas, discriminación en selección. dt.gob.cl tiene formularios de denuncia online.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #a78bfa;border-radius:.5rem;padding:1.1rem;">
                            <div class="fw-bold mb-1" style="color:#0f172a;font-size:.9rem;">CMF (Comisión para el Mercado Financiero)</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.6;">Para problemas con bancos, cajas de compensación o seguros: si un sistema automatizado tomó decisiones financieras que te perjudicaron.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #a78bfa;border-radius:.5rem;padding:1.1rem;">
                            <div class="fw-bold mb-1" style="color:#0f172a;font-size:.9rem;">Vía judicial civil</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.6;">Si la decisión automatizada causó un daño económico concreto, puedes recurrir a la justicia civil alegando vulneración de derechos fundamentales.</p>
                        </div>
                    </div>
                </div>

                <div class="ipa-callout" style="border-left-color:#64748b;background:#f8fafc;border-color:#e2e8f0;">
                    <p class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">📌 Consejo práctico</p>
                    <p style="color:#475569;margin:0;font-size:.9rem;">Guarda siempre evidencia: capturas de pantalla de la comunicación que recibiste, el texto exacto del rechazo o la decisión, fechas. Eso será fundamental si decides reclamar formalmente.</p>
                </div>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-3-3"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-4-1">Siguiente módulo <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>


            {{-- ============================================================
                 MÓDULO 4 · Usar IA con criterio
            ============================================================ --}}

            {{-- Lección 4.1 --}}
            <div data-panel="lesson-4-1" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#f59e0b22;color:#f59e0b;border-color:#f59e0b44;">Módulo 4</span>
                    <span class="ipa-lesson-num" style="color:#f59e0b;">Lección 4.1</span>
                </div>
                <h2 class="ipa-lesson-title">Guía práctica de herramientas de IA</h2>

                <p>ChatGPT, Claude, Gemini, Copilot, Midjourney... El mercado de herramientas de IA crece tan rápido que es difícil saber por dónde empezar. Acá te damos una guía básica para orientarte.</p>

                <div class="ipa-callout" style="border-left-color:#f59e0b;background:#fffbeb;border-color:#fde68a;">
                    <p class="fw-bold mb-2" style="color:#92400e;">Principales herramientas de texto (modelos de lenguaje)</p>
                    <ul style="color:#92400e;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>ChatGPT (OpenAI):</strong> el más conocido. Versión gratuita disponible. Útil para redactar, explicar, resumir, programar.</li>
                        <li><strong>Claude (Anthropic):</strong> bueno para textos largos y análisis. Disponible en claude.ai, con plan gratuito.</li>
                        <li><strong>Gemini (Google):</strong> integrado con Google Docs y Gmail. Útil si ya usas el ecosistema Google.</li>
                        <li><strong>Copilot (Microsoft):</strong> integrado en Word, Excel, Outlook. Si tu empresa usa Microsoft 365, ya lo tienes.</li>
                    </ul>
                </div>

                <p><strong>Cómo escribir buenos prompts:</strong> un prompt es la instrucción que le das a la IA. Cuanto más específico seas, mejor resultado obtendrás. En lugar de "escríbeme un email", prueba "escríbeme un email formal a mi proveedor de 3 párrafos para reclamar una entrega con retraso de 5 días, en tono profesional pero directo". La diferencia en el resultado es notable.</p>
                <p>Tres principios: (1) da contexto sobre quién eres y para qué es el texto, (2) especifica el formato que necesitas, y (3) indica el tono o audiencia. Y si el primer resultado no es lo que querías, pídele que lo ajuste — el diálogo funciona mejor que un solo intento.</p>

                <div class="ipa-callout" style="border-left-color:#64748b;background:#f8fafc;border-color:#e2e8f0;">
                    <p class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">⚠️ Siempre verifica</p>
                    <p style="color:#475569;margin:0;font-size:.9rem;">Los modelos de lenguaje pueden "alucinar": inventar hechos, citar fuentes que no existen, o dar datos incorrectos con total confianza. Nunca uses información factual generada por IA sin verificarla en fuentes confiables.</p>
                </div>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-3-4"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-4-2">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 4.2 --}}
            <div data-panel="lesson-4-2" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#f59e0b22;color:#f59e0b;border-color:#f59e0b44;">Módulo 4</span>
                    <span class="ipa-lesson-num" style="color:#f59e0b;">Lección 4.2</span>
                </div>
                <h2 class="ipa-lesson-title">Lo que no debes compartir con la IA</h2>

                <p>Cuando usas ChatGPT u otra herramienta similar, todo lo que escribes puede ser usado para entrenar modelos futuros (a menos que lo desactives en la configuración), es revisado por operadores del servicio en casos de posibles infracciones, y queda almacenado en servidores fuera de Chile. Eso no significa que debas dejar de usar estas herramientas, pero sí que debes ser cuidadoso.</p>

                <div class="ipa-callout" style="border-left-color:#f43f5e;background:#fff1f2;border-color:#fecdd3;">
                    <p class="fw-bold mb-2" style="color:#9f1239;">Nunca compartas con una IA pública:</p>
                    <ul style="color:#9f1239;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>Contraseñas o tokens de acceso</strong></li>
                        <li><strong>Documentos confidenciales de tu empresa</strong> — contratos, estrategias, datos de clientes</li>
                        <li><strong>Datos de salud propios o de terceros</strong> — fichas médicas, diagnósticos</li>
                        <li><strong>Información de menores</strong> — datos, fotos, rutinas de niños</li>
                        <li><strong>Datos de tarjetas o cuentas bancarias</strong></li>
                    </ul>
                </div>

                <p>Si necesitas que la IA te ayude con documentos sensibles, usa versiones empresariales que ofrezcan garantías contractuales de privacidad (como ChatGPT Enterprise o Azure OpenAI), o herramientas que se ejecuten localmente en tu equipo. En el contexto laboral, consulta siempre la política de tu empresa antes de usar herramientas de IA con información corporativa.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-4-1"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-4-3">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 4.3 --}}
            <div data-panel="lesson-4-3" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#f59e0b22;color:#f59e0b;border-color:#f59e0b44;">Módulo 4</span>
                    <span class="ipa-lesson-num" style="color:#f59e0b;">Lección 4.3</span>
                </div>
                <h2 class="ipa-lesson-title">Detectar desinformación generada por IA</h2>

                <p>La IA ha reducido dramáticamente el costo de producir contenido falso convincente: noticias inventadas, imágenes trucadas, videos de personas diciendo cosas que nunca dijeron, voces clonadas. No es que esto sea nuevo —la desinformación existe desde siempre— pero la escala y el realismo han aumentado enormemente.</p>

                <div class="ipa-callout" style="border-left-color:#f59e0b;background:#fffbeb;border-color:#fde68a;">
                    <p class="fw-bold mb-2" style="color:#92400e;">Señales de alerta en imágenes y videos</p>
                    <ul style="color:#92400e;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li>Manos con dedos mal formados o en número incorrecto</li>
                        <li>Texto dentro de la imagen con errores raros o incoherentes</li>
                        <li>Fondo con detalles inconsistentes o borrosos</li>
                        <li>Movimientos de labios que no calzan exactamente con el audio</li>
                        <li>Iluminación o sombras que no concuerdan con el resto de la escena</li>
                    </ul>
                </div>

                <p><strong>Herramientas para verificar:</strong> Google Images y TinEye para búsqueda inversa de imágenes. InVID y WeVerify para verificar videos. AI or Not (aiornot.com) para detectar si una imagen fue generada por IA. Y siempre, AFP Factual para verificar noticias que circulan en Chile y América Latina.</p>
                <p>El hábito más valioso es simple: antes de compartir algo que te genera una reacción emocional fuerte —indignación, miedo, sorpresa— tómate 60 segundos para buscar si otros medios confiables lo están reportando. La desinformación generada por IA suele circular solo en redes sociales y no aparece en medios verificados.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-4-2"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-4-4">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 4.4 --}}
            <div data-panel="lesson-4-4" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#f59e0b22;color:#f59e0b;border-color:#f59e0b44;">Módulo 4</span>
                    <span class="ipa-lesson-num" style="color:#f59e0b;">Lección 4.4</span>
                </div>
                <h2 class="ipa-lesson-title">IA y productividad laboral</h2>

                <p>Cuando se usa bien, la IA puede multiplicar lo que produces en un día. No porque haga tu trabajo, sino porque elimina las partes más lentas y mecánicas, dejándote tiempo para las que requieren juicio, creatividad y relaciones humanas.</p>

                <div class="ipa-callout" style="border-left-color:#f59e0b;background:#fffbeb;border-color:#fde68a;">
                    <p class="fw-bold mb-2" style="color:#92400e;">Usos concretos por tarea</p>
                    <ul style="color:#92400e;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>Reuniones:</strong> Otter.ai o Fireflies transcriben y resumen reuniones automáticamente</li>
                        <li><strong>Emails:</strong> borradores iniciales, clasificación, respuestas a consultas frecuentes</li>
                        <li><strong>Investigación:</strong> NotebookLM resume documentos largos y responde preguntas sobre ellos</li>
                        <li><strong>Presentaciones:</strong> Gamma.app genera estructuras completas desde un tema</li>
                        <li><strong>Datos:</strong> ChatGPT con Code Interpreter analiza archivos Excel sin saber programar</li>
                    </ul>
                </div>

                <p>El principio clave es mantener al humano en el circuito ("human in the loop"). La IA genera, tú decides. La IA redacta, tú revisas. La IA analiza, tú interpretas. Cuando se invierte ese orden —cuando confías en el output de la IA sin revisarlo— es cuando aparecen los problemas.</p>

                <div class="ipa-callout" style="border-left-color:#64748b;background:#f8fafc;border-color:#e2e8f0;">
                    <p class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">📌 Regla de oro</p>
                    <p style="color:#475569;margin:0;font-size:.9rem;">Usa la IA para hacer más rápido lo que ya sabes hacer bien. No para reemplazar el conocimiento que aún no tienes. Si no sabes evaluar si el resultado es correcto, no estás en condiciones de delegar esa tarea a la IA.</p>
                </div>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-4-3"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-5-1">Siguiente módulo <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>


            {{-- ============================================================
                 MÓDULO 5 · IA y sociedad
            ============================================================ --}}

            {{-- Lección 5.1 --}}
            <div data-panel="lesson-5-1" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#f472b622;color:#f472b6;border-color:#f472b644;">Módulo 5</span>
                    <span class="ipa-lesson-num" style="color:#f472b6;">Lección 5.1</span>
                </div>
                <h2 class="ipa-lesson-title">Beneficios reales de la IA para la sociedad</h2>

                <p>Más allá de la productividad individual, la IA está generando avances significativos en áreas donde el impacto social es enorme. No todos los beneficios son hype: hay resultados concretos y verificables que vale la pena conocer.</p>

                <div class="row g-3 my-3">
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #f472b6;border-radius:.5rem;padding:1.1rem;height:100%;">
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.9rem;"><i class="fas fa-heartbeat me-2" style="color:#f472b6;"></i>Medicina</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.65;">AlphaFold (DeepMind) resolvió el problema del plegamiento de proteínas, acelerando el desarrollo de medicamentos. Sistemas de IA detectan cánceres en imágenes médicas con precisión comparable a especialistas.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #f472b6;border-radius:.5rem;padding:1.1rem;height:100%;">
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.9rem;"><i class="fas fa-leaf me-2" style="color:#f472b6;"></i>Clima y energía</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.65;">Google usa IA para optimizar el consumo energético de sus centros de datos, reduciendo el 40% del uso de refrigeración. Los modelos de IA mejoran la predicción del clima y optimizan redes eléctricas con energía renovable.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #f472b6;border-radius:.5rem;padding:1.1rem;height:100%;">
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.9rem;"><i class="fas fa-universal-access me-2" style="color:#f472b6;"></i>Accesibilidad</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.65;">Reconocimiento de voz permite que personas con discapacidades motoras controlen computadores. Subtítulos en tiempo real democratizan contenido para personas con sordera.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #e2e8f0;border-left:3px solid #f472b6;border-radius:.5rem;padding:1.1rem;height:100%;">
                            <div class="fw-bold mb-2" style="color:#0f172a;font-size:.9rem;"><i class="fas fa-seedling me-2" style="color:#f472b6;"></i>Agricultura</div>
                            <p style="color:#475569;font-size:.85rem;margin:0;line-height:1.65;">Drones con visión computacional detectan enfermedades en cultivos antes de ser visibles al ojo humano. Modelos predictivos optimizan el riego. En Chile, startups agtech ya aplican esto en valles agrícolas.</p>
                        </div>
                    </div>
                </div>

                <p>Estos beneficios son reales, pero no son automáticos. Requieren inversión, voluntad política y gobernanza adecuada para que lleguen a quienes más los necesitan.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-4-4"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-5-2">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 5.2 --}}
            <div data-panel="lesson-5-2" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#f472b622;color:#f472b6;border-color:#f472b644;">Módulo 5</span>
                    <span class="ipa-lesson-num" style="color:#f472b6;">Lección 5.2</span>
                </div>
                <h2 class="ipa-lesson-title">Sesgos algorítmicos: cuando la IA discrimina</h2>

                <p>Un sistema de IA aprende de los datos del pasado. Si esos datos reflejan discriminaciones históricas —y casi siempre lo hacen— el sistema las aprenderá y perpetuará. Esto no es un problema teórico: hay casos documentados con consecuencias reales.</p>

                <div class="ipa-callout" style="border-left-color:#f43f5e;background:#fff1f2;border-color:#fecdd3;">
                    <p class="fw-bold mb-2" style="color:#9f1239;">Casos documentados de sesgos algorítmicos</p>
                    <ul style="color:#9f1239;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>COMPAS (EE.UU.):</strong> algoritmo de predicción de reincidencia criminal que clasificaba a personas negras como de mayor riesgo con el mismo perfil delictivo.</li>
                        <li><strong>Amazon (2018):</strong> descartó un sistema de selección de CVs con IA porque aprendió a penalizar currículums que incluían la palabra "mujeres".</li>
                        <li><strong>Reconocimiento facial:</strong> sistemas líderes identifican incorrectamente a personas de piel oscura hasta un 35% más que a personas blancas.</li>
                        <li><strong>Modelos de crédito:</strong> algunos algoritmos discriminan por código postal, que correlaciona con etnia.</li>
                    </ul>
                </div>

                <p>El sesgo no surge de mala intención: surge de que los datos históricos reflejan un mundo injusto. Si históricamente se prestó menos a ciertos grupos, el algoritmo aprende que esos grupos son "peores pagadores". No porque lo sean, sino porque el sistema anterior los trató peor.</p>
                <p>¿Qué puedes hacer? Exigir transparencia sobre qué datos usa el sistema que toma decisiones sobre ti. Apoyar regulaciones que requieran auditorías de sesgo. Y reportar cuando sospechas que un sistema automatizado te discriminó.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-5-1"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-5-3">Siguiente lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 5.3 --}}
            <div data-panel="lesson-5-3" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#f472b622;color:#f472b6;border-color:#f472b644;">Módulo 5</span>
                    <span class="ipa-lesson-num" style="color:#f472b6;">Lección 5.3</span>
                </div>
                <h2 class="ipa-lesson-title">El debate sobre el control de la IA</h2>

                <p>A medida que los sistemas de IA se vuelven más poderosos, crece una pregunta fundamental: ¿quién los controla y a quién rinden cuentas? Este debate involucra a gobiernos, empresas tecnológicas, investigadores y la sociedad civil.</p>
                <p>Por un lado están quienes argumentan que regular demasiado frena la innovación y deja a los países rezagados. Por otro, quienes señalan que sin regulación adecuada, los sistemas más poderosos quedan en manos de pocas empresas privadas, con casi ninguna rendición de cuentas.</p>

                <div class="ipa-callout" style="border-left-color:#f472b6;background:#fdf2f8;border-color:#fbcfe8;">
                    <p class="fw-bold mb-2" style="color:#9d174d;">Los principales ejes del debate</p>
                    <ul style="color:#9d174d;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>Concentración de poder:</strong> pocos países (EE.UU., China) y pocas empresas concentran la IA más poderosa. ¿Cómo evitar ventajas geopolíticas permanentes?</li>
                        <li><strong>Riesgos de largo plazo:</strong> algunos investigadores serios argumentan que IA muy avanzada mal alineada podría ser peligrosa.</li>
                        <li><strong>IA abierta vs. cerrada:</strong> los modelos abiertos permiten experimentar, pero también que actores maliciosos los usen.</li>
                        <li><strong>Regulación nacional vs. global:</strong> la IA no respeta fronteras, pero la regulación sí.</li>
                    </ul>
                </div>

                <p>En 2024, la Unión Europea aprobó el AI Act, la primera regulación integral de IA del mundo, que clasifica los sistemas por nivel de riesgo y establece obligaciones proporcionales. En Chile se debate una política nacional de IA y una nueva ley de datos personales. Tu participación en esas discusiones —como ciudadano y votante— importa.</p>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-5-2"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <button class="ipa-nav-btn" data-target="lesson-5-4">Última lección <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Lección 5.4 --}}
            <div data-panel="lesson-5-4" style="display:none;">
                <div class="ipa-lesson-header">
                    <span class="ipa-mod-badge" style="background:#f472b622;color:#f472b6;border-color:#f472b644;">Módulo 5</span>
                    <span class="ipa-lesson-num" style="color:#f472b6;">Lección 5.4</span>
                </div>
                <h2 class="ipa-lesson-title">Chile y la IA: dónde estamos</h2>

                <p>Chile tiene condiciones interesantes para el desarrollo de IA: infraestructura digital relativamente buena para la región, capital humano calificado en ingeniería y ciencias, y una institucionalidad que le permite actuar con rapidez cuando hay voluntad política. Pero también enfrenta desafíos reales.</p>

                <div class="ipa-callout" style="border-left-color:#f472b6;background:#fdf2f8;border-color:#fbcfe8;">
                    <p class="fw-bold mb-2" style="color:#9d174d;">Estado actual del ecosistema IA en Chile</p>
                    <ul style="color:#9d174d;margin:0;font-size:.92rem;padding-left:1.2rem;">
                        <li><strong>Política Nacional de IA:</strong> publicada en 2021, en proceso de actualización. Establece lineamientos para el desarrollo ético y productivo de la IA.</li>
                        <li><strong>Ecosistema académico:</strong> centros de excelencia en U. de Chile, PUC, USACH. El Centro Nacional de IA (CENIA) coordina la investigación a nivel país.</li>
                        <li><strong>Startups:</strong> crecimiento en agtech, salud, fintech y educación. Ecosistema en etapa temprana pero con buenos casos.</li>
                        <li><strong>Infraestructura:</strong> Chile es sede de centros de datos de Google, Microsoft y Amazon gracias a estabilidad sísmica y energía renovable barata.</li>
                    </ul>
                </div>

                <p>El principal desafío es la brecha de adopción: mientras algunas empresas e instituciones líderes incorporan IA activamente, la mayoría de las pymes y el sector público van muy por detrás. Eso genera un riesgo de polarización: quienes tienen acceso a IA ganan productividad, y quienes no se quedan más atrás.</p>
                <p>Como ciudadano, puedes participar en este proceso: informándote (como estás haciendo en este curso), participando en consultas públicas sobre regulación, exigiendo a autoridades que la alfabetización digital sea parte de la agenda educativa, y usando herramientas de IA en tu trabajo y vida para no quedarte fuera del cambio.</p>

                <div style="background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);border:1px solid #bbf7d0;border-radius:.75rem;padding:1.75rem;margin-top:2rem;text-align:center;">
                    <div style="font-size:2rem;margin-bottom:.75rem;">🎓</div>
                    <h3 class="fw-bold" style="color:#166534;font-size:1.1rem;margin-bottom:.5rem;">¡Completaste el curso!</h3>
                    <p style="color:#166534;font-size:.92rem;margin:0 0 1.25rem;">Has recorrido los 5 módulos de IA para Todos. Ahora tienes una base sólida para entender, usar y cuestionar la IA que te rodea.</p>
                    <button class="btn btn-sm" style="background:#00c896;color:white;font-size:.85rem;border:none;" data-target="lesson-1-1">
                        <i class="fas fa-redo me-1"></i>Volver al inicio
                    </button>
                </div>

                <div class="ipa-nav-bottom">
                    <button class="ipa-nav-btn ipa-nav-prev" data-target="lesson-5-3"><i class="fas fa-arrow-left me-1"></i> Anterior</button>
                    <span></span>
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
.ipa-sidebar::-webkit-scrollbar { width: 4px; }
.ipa-sidebar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }
.ipa-module-header {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .5rem .4rem;
    font-size: .82rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: .15rem;
}
.ipa-module-num {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .72rem;
    font-weight: 800;
    flex-shrink: 0;
}
.ipa-lessons {
    display: flex;
    flex-direction: column;
    gap: .1rem;
    padding-left: .75rem;
    margin-bottom: .5rem;
}
.ipa-lesson-btn {
    display: block;
    width: 100%;
    text-align: left;
    padding: .45rem .75rem;
    background: transparent;
    border: 1px solid transparent;
    border-radius: .4rem;
    cursor: pointer;
    font-size: .81rem;
    color: #64748b;
    transition: all .15s;
    line-height: 1.35;
}
.ipa-lesson-btn:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #0f172a;
}
.ipa-lesson-btn.active {
    background: rgba(0,200,150,.08);
    border-color: rgba(0,200,150,.3);
    color: #0f172a;
    font-weight: 600;
}
.ipa-lesson-header {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: .75rem;
}
.ipa-mod-badge {
    display: inline-block;
    padding: .25rem .75rem;
    border: 1px solid;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: .04em;
}
.ipa-lesson-num {
    font-size: .82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
}
.ipa-lesson-title {
    font-size: 1.55rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: 1.5rem;
}
[data-panel] > p {
    color: #475569;
    font-size: .97rem;
    line-height: 1.85;
    margin-bottom: 1rem;
}
.ipa-callout {
    border: 1px solid;
    border-left-width: 4px;
    border-radius: .5rem;
    padding: 1.25rem;
    margin: 1.5rem 0;
}
.ipa-nav-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid #f1f5f9;
}
.ipa-nav-btn {
    padding: .55rem 1.1rem;
    border: 1px solid #e2e8f0;
    border-radius: .5rem;
    background: white;
    cursor: pointer;
    font-size: .85rem;
    font-weight: 600;
    color: #0f172a;
    transition: all .15s;
}
.ipa-nav-btn:hover {
    background: #f8fafc;
    border-color: #38b6ff;
    color: var(--primary-color);
}
.ipa-nav-prev { color: #64748b; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const panels = document.querySelectorAll('[data-panel]');
    const btns   = document.querySelectorAll('[data-target]');

    function show(target) {
        panels.forEach(p => p.style.display = p.dataset.panel === target ? '' : 'none');
        btns.forEach(b => b.classList.toggle('active', b.dataset.target === target));
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    btns.forEach(btn => btn.addEventListener('click', () => show(btn.dataset.target)));
})();
</script>
@endpush

@endsection
