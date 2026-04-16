# Tareas programadas — ConocIA

Referencia completa de todos los comandos artisan y su programación.  
Para que el scheduler funcione se necesita **un solo cron** activo en el servidor.

---

## 1. Activar el scheduler de Laravel (requisito único)

En el servidor de producción, agregar este cron **una sola vez**:

```bash
crontab -e
```

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

En desarrollo local, usar:

```bash
php artisan schedule:work
```

---

## 2. Resumen de tareas programadas

| Comando | Frecuencia | Hora | Descripción |
|---|---|---|---|
| `news:fetch-all --count=2` | 2× al día | 08:00 y 20:00 | Busca 2 noticias por categoría usando Gemini + NewsAPI |
| `briefing:generate` | 1× al día | 07:30 | Genera el AI Briefing diario (requiere Gemini) |
| `newsletter:send` | Semanal | Lunes 08:00 | Envía newsletter a suscriptores verificados |
| `comments:auto-approve` | Cada 3 min | — | Aprueba comentarios que pasan el filtro automático |
| `news:archive --days=4` | 1× al día | 02:00 | Mueve noticias +4 días a la tabla histórica |
| `tiktok:generate-scripts --count=5` | 2× al día | 09:00 y 16:00 | Genera guiones de TikTok para noticias relevantes |
| `tiktok:notify-pending-scripts` | 1× al día (días hábiles) | 10:00 | Notifica admins si hay guiones pendientes de revisión |
| `videos:generate-summaries --limit=5` | 1× al día | 08:00 | Genera resúmenes IA y keywords para videos nuevos |
| `news:publish-twitter` | Desactivado | — | Publica noticias en Twitter (ver sección 5) |

---

## 3. Credenciales necesarias por tarea

### 3.1 Fetch de noticias con IA — `news:fetch-all`

**Variables `.env` requeridas:**
```env
GEMINI_API_KEY=        # https://aistudio.google.com/app/apikey (free tier disponible)
GEMINI_MODEL=gemini-2.0-flash
NEWSAPI_KEY=           # https://newsapi.org (free tier: 100 req/día)
```

**Notas:**
- El free tier de Gemini tiene cuota diaria. Si se agota, los comandos fallan con error 429 y reintentan al día siguiente.
- Usa `--count=2` para no agotar la cuota con pocas categorías. Ajustar si se paga plan pago.

---

### 3.2 AI Briefing diario — `briefing:generate`

**Variables `.env` requeridas:**
```env
GEMINI_API_KEY=        # mismo que fetch de noticias
GEMINI_MODEL=gemini-2.0-flash
```

**Uso manual:**
```bash
php artisan briefing:generate           # genera el de hoy (si no existe)
php artisan briefing:generate --force   # regenera aunque ya exista
```

**Notas:**
- Se guarda en la tabla `daily_briefings` (fecha única por día).
- Si falla por cuota, correr manualmente una vez que la cuota se resetee.
- La UI en el home consume el endpoint `GET /api/briefing/today`.

---

### 3.3 Newsletter semanal — `newsletter:send`

**Variables `.env` requeridas:**
```env
MAIL_MAILER=smtp          # o 'resend', 'mailgun', etc.
MAIL_HOST=smtp.ejemplo.com
MAIL_PORT=587
MAIL_USERNAME=tu@email.com
MAIL_PASSWORD=password
MAIL_FROM_ADDRESS=noticias@conocia.com
MAIL_FROM_NAME="ConocIA"
```

**Uso manual:**
```bash
php artisan newsletter:send --news=5 --include-research --include-columns
```

**Notas:**
- Solo envía a suscriptores con `verified_at` no nulo (doble opt-in).
- En desarrollo usa Mailpit (http://127.0.0.1:8025) — no requiere credenciales reales.
- Para producción: Resend, Mailgun o AWS SES son las opciones más económicas.

---

### 3.4 Aprobación de comentarios — `comments:auto-approve`

**Variables `.env` requeridas:** Ninguna externa. Usa lógica interna.

**Uso manual:**
```bash
php artisan comments:auto-approve
```

---

### 3.5 Archivar noticias — `news:archive`

**Variables `.env` requeridas:** Ninguna externa.

**Uso manual:**
```bash
php artisan news:archive              # default: noticias > 4 días
php artisan news:archive --days=7     # personalizar umbral
php artisan news:archive --batch=50   # ajustar tamaño de lote
```

---

### 3.6 Guiones TikTok — `tiktok:generate-scripts`

**Variables `.env` requeridas:**
```env
GEMINI_API_KEY=        # mismo que fetch de noticias
GEMINI_MODEL=gemini-2.0-flash
```

**Uso manual:**
```bash
php artisan tiktok:generate-scripts --count=5
```

**Notas:**
- Los guiones quedan en estado `pending_review` hasta que un admin los aprueba en `/admin/tiktok`.
- Comparte cuota de API con `news:fetch-all` y `briefing:generate`.

---

### 3.7 Resúmenes IA de videos — `videos:generate-summaries`

**Variables `.env` requeridas:**
```env
GEMINI_API_KEY=        # mismo que fetch de noticias
GEMINI_MODEL=gemini-2.0-flash
```

**Uso manual:**
```bash
php artisan videos:generate-summaries             # procesa solo los que no tienen resumen (límite 10)
php artisan videos:generate-summaries --limit=5   # procesar máximo 5 por ejecución
php artisan videos:generate-summaries --force     # regenerar aunque ya tengan resumen
```

**Qué genera:**
- `ai_summary`: 3 bullets clave del video separados internamente por `|||`
- `ai_keywords`: array JSON con hasta 5 temas o tecnologías mencionadas

**Dónde se muestra:**
- En `ConocIA TV` (`/videos`): badge "Resumen IA" en cada card + keywords al hacer hover
- En el detalle del video (`/videos/{id}`): panel expandido con bullets numerados y chips de keywords

**Notas:**
- El comando espera 2 segundos entre videos para respetar el rate limit del free tier.
- Es idempotente: si se corta a mitad, puede reiniciarse sin duplicar trabajo.
- Comparte cuota de API con `news:fetch-all`, `briefing:generate` y `tiktok:generate-scripts`.

---

### 3.8 Videos de Vimeo — `videos:fetch-vimeo` (manual, sin schedule)

**Variables `.env` requeridas:**
```env
VIMEO_CLIENT_ID=
VIMEO_CLIENT_SECRET=
VIMEO_REDIRECT_URI=
VIMEO_ACCESS_TOKEN=    # el más importante
```

**Obtener credenciales:** https://developer.vimeo.com/apps

**Uso manual:**
```bash
php artisan videos:fetch-vimeo "inteligencia artificial" --limit=10
php artisan videos:fetch-vimeo "machine learning" --limit=5
```

---

## 4. Comandos de setup inicial (correr una sola vez)

```bash
# Crear usuario administrador
php artisan admin:create

# Crear categorías de tecnología
php artisan categories:create-tech

# Crear imágenes placeholder por defecto
php artisan images:create-defaults-images

# Generar sitemap inicial
php artisan sitemap:generate

# Sembrar videos de ejemplo (desarrollo)
php artisan videos:seed 10
```

---

## 5. Twitter / X — activar publicación automática

Actualmente **desactivado** en `Kernel.php`. Para activar:

**Paso 1** — Obtener credenciales en https://developer.twitter.com

**Paso 2** — Agregar al `.env`:
```env
TWITTER_CONSUMER_KEY=
TWITTER_CONSUMER_SECRET=
TWITTER_ACCESS_TOKEN=
TWITTER_ACCESS_TOKEN_SECRET=
TWITTER_BEARER_TOKEN=
```

**Paso 3** — Descomentar en `app/Console/Kernel.php`:
```php
$schedule->command('news:publish-twitter --limit=1')->weekdays()->at('09:00');
$schedule->command('news:publish-twitter --limit=1')->weekdays()->at('13:00');
$schedule->command('news:publish-twitter --limit=1')->weekdays()->at('18:00');
```

**Uso manual:**
```bash
php artisan news:publish-twitter --limit=3
php artisan news:publish-twitter --dry-run   # simula sin publicar
```

---

## 6. YouTube — búsqueda de videos (sin schedule activo)

**Variables `.env` requeridas:**
```env
YOUTUBE_API_KEY=    # https://console.cloud.google.com → YouTube Data API v3
```

---

## 7. Logs de cada tarea

Todos los logs se guardan en `storage/logs/`:

| Tarea | Archivo de log |
|---|---|
| Fetch noticias | `storage/logs/fetch-all-news.log` |
| AI Briefing | `storage/logs/briefing.log` |
| Newsletter | `storage/logs/newsletter-cron.log` |
| Comentarios | `storage/logs/comments-auto-approve.log` |
| TikTok scripts | `storage/logs/tiktok-scripts.log` |
| Resúmenes de video | `storage/logs/video-summaries.log` |
| Laravel general | `storage/logs/laravel.log` |

Ver logs en tiempo real:
```bash
tail -f storage/logs/laravel.log
tail -f storage/logs/briefing.log
```

---

## 8. ConocIA TV — arquitectura de la sección de videos

### Archivos involucrados

| Archivo | Rol |
|---|---|
| `app/Models/Video.php` | Modelo con `ai_summary`, `ai_keywords`, `hasAiSummary()` |
| `app/Services/VideoSummaryService.php` | Llama a Gemini, parsea JSON, guarda en DB |
| `app/Console/Commands/GenerateVideoSummaries.php` | Comando artisan que orquesta el servicio |
| `app/Http/Controllers/VideoController.php` | Pasa `featuredVideo`, `videosByCategory` y colecciones a la vista |
| `resources/views/videos/index.blade.php` | Vista principal rediseñada como ConocIA TV |
| `resources/views/videos/show.blade.php` | Detalle con panel de resumen IA |
| `resources/views/partials/tv-video-card.blade.php` | Card reutilizable para las filas de scroll |

### Estructura de datos

**`videos.ai_summary`** — texto plano con bullets separados por `|||`:
```
Explica cómo funciona GPT-4|||Casos de uso en empresas|||Limitaciones actuales del modelo
```

**`videos.ai_keywords`** — JSON array:
```json
["GPT-4", "Large Language Models", "OpenAI", "Empresas", "NLP"]
```

### Flujo de generación

```
videos:generate-summaries
  └── VideoSummaryService::generate(Video)
        └── Gemini API (prompt estructurado → JSON)
              └── Video::update(['ai_summary', 'ai_keywords'])
```

### Lógica de la vista

1. `VideoController::index()` arma 4 colecciones: `featuredVideo`, `latestVideos`, `popularVideos`, `videosByCategory`
2. El hero muestra el video con `is_featured = true` (fallback: el más visto)
3. La barra de categorías sticky filtra las filas via JS sin recarga de página
4. `tv-video-card.blade.php` muestra keywords al hover y badge "Resumen IA" si `ai_summary` existe

### Agregar más videos

```bash
# YouTube (requiere YOUTUBE_API_KEY)
# Vimeo
php artisan videos:fetch-vimeo "inteligencia artificial" --limit=10

# Seed de desarrollo (videos falsos)
php artisan videos:seed 10

# Luego generar resúmenes
php artisan videos:generate-summaries
```

---

## 9. Verificar que el scheduler corre correctamente

```bash
# Ver próximas ejecuciones
php artisan schedule:list

# Correr manualmente una iteración (como si fuera el cron)
php artisan schedule:run

# Monitoreo continuo (desarrollo)
php artisan schedule:work
```
