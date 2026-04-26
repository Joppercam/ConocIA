# Documentacion del software ConocIA

Fecha de referencia: 2026-04-25

Este documento resume el panorama tecnico y funcional del aplicativo ConocIA segun el codigo actual del repositorio. Complementa, no reemplaza, los documentos existentes:

- `README.md`: arranque local y comandos generales.
- `TAREAS_PROGRAMADAS.md`: scheduler, comandos Artisan y credenciales por tarea.
- `PLAN_EJECUTIVO_RELANZAMIENTO.md`: roadmap ejecutivo.
- `GUIA_RETORNO_RELANZAMIENTO.md`: bitacora historica de retorno.

## 1. Vision general

ConocIA es una aplicacion Laravel 12 para publicar, administrar y automatizar contenido editorial sobre inteligencia artificial.

El sistema combina:

- Portal publico de contenidos.
- Panel administrativo privado.
- Automatizaciones de generacion, ingesta y distribucion.
- Integraciones externas de IA, noticias, imagenes, video, email, SEO y redes.
- Scheduler y queue workers para procesos recurrentes y trabajos asincronos.

El aplicativo esta en produccion en Laravel Cloud, por lo que cualquier cambio debe tratarse como cambio productivo: revisar rutas, cache, migraciones, scheduler, colas y pruebas antes de desplegar.

## 2. Stack tecnico

- Framework: Laravel 12.
- PHP: compatible con PHP 8.2+, entorno local detectado con PHP 8.4.
- Frontend: Blade, Vite, Bootstrap, Tailwind y CSS propio.
- Base de datos: MySQL en produccion; SQLite posible para flujo local simple.
- Queue: driver database segun plantillas actuales.
- Cache y sesiones: database por defecto; Redis recomendado si esta disponible.
- Assets: Vite con build en `public/build`.
- Tests: PHPUnit con pruebas feature y unit enfocadas en SEO, admin, noticias, TikTok, videos y comandos editoriales.

## 3. Estructura principal

Directorios clave:

- `app/Http/Controllers`: controladores publicos.
- `app/Http/Controllers/Admin`: panel administrativo.
- `app/Models`: modelos Eloquent principales.
- `app/Console/Commands`: automatizaciones y comandos operativos.
- `app/Services`: servicios de IA, SEO, videos, comentarios, imagenes y redes.
- `resources/views`: vistas Blade publicas y administrativas.
- `routes/web.php`: rutas web publicas y admin.
- `bootstrap/app.php`: bootstrap moderno de Laravel 12 y scheduler activo.
- `database/migrations`: evolucion de esquema.
- `tests`: pruebas automatizadas.
- `docs`: documentacion viva del proyecto.

## 4. Modulos publicos

El sitio publico expone, entre otras, estas areas:

- Home: `HomeController@index`.
- Noticias: `/news`, categorias, tags, archivo e IA en Chile.
- Investigacion: `/investigacion`.
- Columnas: `/columnas`.
- Colaboraciones: `/colaboraciones`.
- Videos / ConocIA TV: `/videos`.
- Radio / briefing: `/radio`.
- Conceptos IA: `/conceptos-ia`.
- Analisis de fondo: `/analisis`.
- Papers: `/papers`.
- Estado del arte: `/estado-del-arte`.
- Modelos IA: `/modelos`.
- Agenda: `/agenda`.
- Startups: `/startups`.
- Agentes IA: vistas existentes en `resources/views/agents`.
- Busqueda: `/buscar` y `/api/buscar`.
- Newsletter, contacto, paginas legales y perfiles.

## 5. Panel administrativo

El panel privado esta bajo:

- `/cp-conocia`

Alias heredados:

- `/admin` redirige a `/cp-conocia`.
- `/admin/login` redirige a `/cp-conocia/login`.

Areas administrativas detectadas:

- Dashboard y analitica de noticias.
- Noticias CRUD, bulk actions, export, preview y carga de imagen.
- Investigaciones CRUD, invitados pendientes y aprobaciones.
- Categorias.
- Comentarios y respuestas.
- Usuarios.
- Newsletter y envio manual.
- API de noticias desde panel.
- Columnas.
- ConocIA Papers.
- Modelos IA.
- Agenda.
- Startups.
- Agentes IA.
- Estado del Arte.
- Cola de redes sociales.
- Videos, categorias y plataformas.
- TikTok scripts, recomendaciones, metricas, kits y ayuda.
- Search Console.

El acceso esta protegido con autenticacion y `AdminMiddleware`.

## 6. Modelos principales

Modelos editoriales:

- `News`
- `NewsHistoric`
- `Research`
- `Column`
- `GuestPost`
- `Comment`
- `Category`
- `Tag`
- `Newsletter`

Modelos Profundiza / IA:

- `ConceptoIa`
- `AnalisisFondo`
- `ConocIaPaper`
- `EstadoArte`
- `DailyBriefing`

Modelos ecosistema IA:

- `AiModel`
- `Event`
- `Startup`
- `AiAgent`

Modelos video:

- `Video`
- `VideoCategory`
- `VideoPlatform`
- `VideoKeyword`
- `VideoTag`
- `VideoView`

Modelos distribucion y medicion:

- `SocialMediaQueue`
- `SocialMediaPost`
- `TikTokScript`
- `TikTokMetric`
- `SearchConsoleMetric`

Usuarios y permisos:

- `User`
- `Role`

## 7. Automatizaciones y comandos

Los comandos Artisan son parte central del producto. Los mas relevantes:

Contenido y noticias:

- `news:fetch-gemini`
- `news:fetch-all`
- `news:fetch-rss`
- `news:fetch-guardian`
- `news:fetch-missing-images`
- `news:clean-content`
- `news:validate-quality`
- `news:reprocess-short`
- `news:archive`

Profundiza:

- `conceptos:generate`
- `analisis:generate`
- `papers:fetch-arxiv`
- `digest:generate`

Briefing, newsletter y comentarios:

- `briefing:generate`
- `newsletter:send`
- `comments:auto-approve`

Video:

- `videos:fetch-youtube`
- `videos:fetch-vimeo`
- `videos:generate-summaries`
- `videos:seed`

Ecosistema IA:

- `models:sync`
- `events:fetch`
- `startups:fetch`
- `startups:feature-weekly`
- `agents:fetch`

SEO:

- `seo:sync-search-console`
- `seo:audit-search-console`
- `content:optimize-priority-news-seo`
- `sitemap:generate`

Social / TikTok:

- `news:publish-social`
- `news:publish-twitter`
- `tiktok:generate-scripts`
- `tiktok:notify-pending-scripts`

Setup:

- `admin:create`
- `categories:create-tech`
- `images:create-defaults-images`

## 8. Scheduler

En Laravel 12, el scheduler activo del proyecto esta en `bootstrap/app.php`, dentro de `withSchedule(...)`.

Importante:

- `app/Console/Kernel.php` tambien contiene programacion, pero en esta estructura moderna el punto critico a revisar es `bootstrap/app.php`.
- `TAREAS_PROGRAMADAS.md` documenta el schedule y debe mantenerse sincronizado con `bootstrap/app.php`.
- En produccion debe existir una unica forma de ejecutar el scheduler: Laravel Cloud scheduler, cron `schedule:run` o `schedule:work` gestionado. No duplicar.

Tareas activas destacadas:

- Gemini hourly para noticias.
- RSS cada 30 minutos.
- NewsAPI diario.
- Guardian diario.
- Imagenes faltantes dos veces al dia.
- Newsletter semanal.
- Comentarios cada 3 minutos.
- Archivo diario.
- Profundiza semanal.
- Videos YouTube y resumenes IA.
- Briefing diario.
- Search Console sync y auditoria si esta configurado.

## 9. Integraciones externas

IA:

- OpenAI: generacion editorial y reescritura.
- Gemini: grounding, noticias, briefing, videos y tareas batch.
- Anthropic Claude: fallback o contenido editorial especializado.

Contenido:

- NewsAPI.
- The Guardian API.
- RSS curados.
- arXiv.
- Pexels para imagenes.

Video:

- YouTube Data API.
- Vimeo.
- Dailymotion.

SEO y analitica:

- Google Search Console.

Distribucion:

- Email SMTP o proveedor equivalente.
- Twitter/X.
- Facebook.
- LinkedIn.

Auth / otros:

- Google Login.
- NoCaptcha.
- Storage local, S3 o Cloudflare R2 segun variables disponibles.

## 10. Produccion en Laravel Cloud

Checklist conceptual para produccion:

- `APP_ENV=production`.
- `APP_DEBUG=false`.
- `APP_URL` correcto.
- Base de datos productiva configurada.
- `SESSION_DRIVER`, `CACHE_STORE` y `QUEUE_CONNECTION` definidos conscientemente.
- Variables de IA y APIs cargadas en Laravel Cloud, no en archivos versionados.
- Scheduler configurado una sola vez.
- Queue worker activo si hay jobs asincronos.
- Build frontend ejecutado (`npm run build`) antes o durante despliegue.
- Cache de config, rutas y vistas preparada si el flujo de deploy lo contempla.
- Storage publico enlazado si se usa disco local/public.
- Logs y alertas revisables.

## 11. Pruebas existentes

Pruebas detectadas:

- Admin News Controller.
- Admin Search Console Controller.
- Admin TikTok Controller.
- Seccion Chile.
- Archivado de noticias.
- SEO robots.
- SEO de videos.
- SEO de noticias.
- Comandos de validacion y optimizacion.
- Publicacion de pack Profundiza.

Antes de cambios sensibles:

- Ejecutar pruebas enfocadas del modulo afectado.
- Si el cambio toca rutas o controladores, validar `php artisan route:list`.
- Si toca vistas Blade, validar render de paginas clave.
- Si toca scheduler, validar `php artisan schedule:list` en entorno con DB/cache disponible.
- Si toca produccion, revisar migraciones y comandos de cache.

## 12. Riesgos operativos

Riesgos principales:

- Scheduler duplicado o detenido.
- Queue worker detenido.
- Cuotas agotadas de IA o APIs externas.
- Contenido generado sin validacion editorial.
- Imagenes faltantes o URLs rotas.
- Cambios en migraciones sin backup o sin plan de rollback.
- Cache de rutas/config desactualizada.
- Variables de entorno incompletas en Laravel Cloud.
- Documentacion desincronizada con `bootstrap/app.php`.

## 13. Runbook rapido

Si no se publican noticias:

1. Revisar logs de `news:fetch-*`.
2. Verificar claves `GEMINI_API_KEY`, `NEWSAPI_KEY`, `GUARDIAN_API_KEY`.
3. Ejecutar manualmente el comando con bajo volumen.
4. Revisar estado de DB y cuotas.

Si falla el briefing:

1. Revisar `storage/logs/briefing.log`.
2. Confirmar `GEMINI_API_KEY`.
3. Ejecutar `php artisan briefing:generate --force`.

Si no salen emails:

1. Revisar variables `MAIL_*`.
2. Probar envio manual de newsletter con pocos destinatarios o entorno seguro.
3. Revisar logs de mail y proveedor.

Si no corre el scheduler:

1. Confirmar configuracion de Laravel Cloud scheduler o cron.
2. Ejecutar `php artisan schedule:list`.
3. Revisar locks de cache si usa database/redis.

Si el panel admin falla:

1. Revisar login y rol del usuario.
2. Revisar `AdminMiddleware`.
3. Validar rutas `cp-conocia`.
4. Revisar logs Laravel.

## 14. Documentacion pendiente recomendada

Crear o mantener:

- `docs/MAPA_BASE_DATOS.md`: tablas, relaciones y campos criticos.
- `docs/RUNBOOK_PRODUCCION.md`: procedimientos de incidentes en Laravel Cloud.
- `docs/MANUAL_ADMIN.md`: uso del panel administrativo.
- `docs/INTEGRACIONES.md`: credenciales, limites, responsables y pruebas.
- `docs/PROMPTS_CONTENIDO.md`: prompts operativos para pedir creacion de contenido.

