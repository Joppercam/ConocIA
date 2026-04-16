# Guía de retorno (pausa temporal)

Fecha de corte: 21-02-2026
Proyecto: ConocIA
Objetivo: dejar trazabilidad para retomar en 2 semanas sin perder contexto.

## 1) Estado actual del proyecto

### Infraestructura local
- Docker instalado y operativo.
- Servicios Docker funcionando correctamente: MySQL, Redis y Mailpit.
- MySQL expuesto por puerto 3307 (no 3306) por conflicto local detectado.

### Aplicación
- Sitio levantado en local y respondiendo OK.
- URL local: http://127.0.0.1:8000
- Mailpit local: http://127.0.0.1:8025

### Base de datos y migraciones
- Migraciones ajustadas para que corran en entorno MySQL limpio.
- Migrate fresh ejecutado con éxito en MySQL.

### Testing
- Test de archivado validado en verde:
  - Tests\Feature\NewsArchivingTest

## 2) Documentación operativa ya creada

- README operativo en español con flujo local y Docker.
- Chuleta diaria de comandos:
  - COMANDOS_RAPIDOS.md
- Makefile con comandos de 1 paso:
  - make up
  - make dev
  - make check
  - make logs
  - make docker-down

## 3) Motivo de pausa y bloqueadores actuales

Se pausa por costos externos (APIs/servicios de pago) necesarios para validar funcionalidades completas del relanzamiento.

Bloqueadores funcionales probables al retomar:
- OpenAI (generación/análisis IA)
- NewsAPI y/u otras APIs de ingestión
- APIs sociales (Twitter/X, Facebook, LinkedIn)
- Vimeo/YouTube/Dailymotion según flujo habilitado

Nota: el proyecto puede correr localmente sin estas credenciales, pero varias automatizaciones quedarán en modo warning o sin datos reales.

## 4) Checklist para retomar en 2 semanas

### Paso A: actualizar credenciales
1. Completar variables en .env (o regenerarlo desde .env.docker.example).
2. Verificar claves activas y límites de cuota de APIs.

### Paso B: levantar entorno
1. make up
2. make dev
3. Abrir http://127.0.0.1:8000

### Paso C: validar estado técnico
1. make check
2. Revisar logs: make logs
3. Confirmar que no haya errores de conexión a APIs pagadas.

### Paso D: validar negocio (pre-relanzamiento)
1. Flujo de noticias (ingesta, publicación, archivo).
2. Flujo social (cola, publicación, tracking).
3. Flujo TikTok scripts y notificaciones.
4. Flujo newsletter.

## 5) Variables críticas a revisar al retomar

- OPENAI_API_KEY
- OPENAI_ORGANIZATION
- NEWSAPI_KEY
- TEXT_ANALYSIS_API_KEY
- TEXT_ANALYSIS_API_URL
- TWITTER_CONSUMER_KEY
- TWITTER_CONSUMER_SECRET
- TWITTER_ACCESS_TOKEN
- TWITTER_ACCESS_TOKEN_SECRET
- TWITTER_BEARER_TOKEN
- YOUTUBE_API_KEY
- VIMEO_CLIENT_ID
- VIMEO_CLIENT_SECRET
- VIMEO_REDIRECT_URI
- VIMEO_ACCESS_TOKEN
- DAILYMOTION_API_KEY
- NOCAPTCHA_SITEKEY
- NOCAPTCHA_SECRET

## 6) Comandos de referencia rápida

### Levantar todo
make up
make dev

### Verificar estado
make check

### Logs
make logs

### Bajar infraestructura
make docker-down

### Reset de base (si necesitas reiniciar pruebas)
make fresh

## 7) Recomendación para el reinicio

Al volver, arrancar por una mini fase de estabilización (1 día):
- Verificar credenciales
- Ejecutar smoke test completo
- Definir backlog funcional priorizado de relanzamiento

Después recién iniciar mejoras funcionales.

---
Último estado confirmado: entorno local funcional con Docker + app accesible.
