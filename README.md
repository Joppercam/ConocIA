# ConocIA

Aplicación Laravel 12 para gestión y publicación de contenidos (noticias, videos, newsletter, redes sociales y automatizaciones con IA).

> Referencia rápida permanente: [COMANDOS_RAPIDOS.md](COMANDOS_RAPIDOS.md)
>
> Si pausan y retoman más adelante: [GUIA_RETORNO_RELANZAMIENTO.md](GUIA_RETORNO_RELANZAMIENTO.md)
>
> Hoja de ruta del relanzamiento: [PLAN_EJECUTIVO_RELANZAMIENTO.md](PLAN_EJECUTIVO_RELANZAMIENTO.md)

## Requisitos

- PHP 8.2+
- Composer 2+
- Node.js 20+
- NPM 10+
- Docker Desktop (opcional, recomendado para infraestructura local)

## Inicio rápido recomendado (Docker)

```bash
make up
make dev
```

Con eso levantas infraestructura + app completa. Para detener infraestructura:

```bash
make docker-down
```

## Flujo A: rápido (SQLite, sin Docker)

1. Instalar dependencias:

```bash
composer install
npm install
```

2. Inicializar entorno:

```bash
composer local:init
```

3. Levantar desarrollo completo (server + queue + scheduler + logs + vite):

```bash
composer dev
```

4. Abrir aplicación:

- App: http://127.0.0.1:8000

## Flujo B: infraestructura local con Docker (MySQL + Redis + Mailpit)

1. Copiar variables Docker:

```bash
cp .env.docker.example .env
```

2. Levantar servicios de infraestructura:

```bash
docker compose up -d
```

3. Instalar dependencias y ejecutar migraciones:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
```

4. Levantar aplicación y workers:

```bash
composer dev
```

5. Verificación visual:

- App: http://127.0.0.1:8000
- Mailpit (bandeja SMTP local): http://127.0.0.1:8025

## Comandos útiles

- Flujo diario simplificado:

```bash
make up
make dev
make check
make logs
```

- Validar estado general:

```bash
composer local:check
```

- Detener infraestructura Docker:

```bash
docker compose down
```

- Ver logs de infraestructura:

```bash
docker compose logs -f
```

## Variables externas (opcionales según features)

En `.env` podés completar según lo que quieras probar localmente:

- OpenAI: `OPENAI_API_KEY`, `OPENAI_ORGANIZATION`
- News API: `NEWSAPI_KEY`
- Análisis de texto: `TEXT_ANALYSIS_API_KEY`, `TEXT_ANALYSIS_API_URL`
- Twitter/X: `TWITTER_CONSUMER_KEY`, `TWITTER_CONSUMER_SECRET`, `TWITTER_ACCESS_TOKEN`, `TWITTER_ACCESS_TOKEN_SECRET`, `TWITTER_BEARER_TOKEN`
- YouTube: `YOUTUBE_API_KEY`
- Vimeo: `VIMEO_CLIENT_ID`, `VIMEO_CLIENT_SECRET`, `VIMEO_REDIRECT_URI`, `VIMEO_ACCESS_TOKEN`
- Dailymotion: `DAILYMOTION_API_KEY`
- Captcha: `NOCAPTCHA_SITEKEY`, `NOCAPTCHA_SECRET`

## Checklist de revisión local

1. `php artisan about` responde sin errores.
2. `php artisan migrate:status` muestra migraciones aplicadas.
3. `composer dev` deja activos: server, queue, scheduler, logs y vite.
4. Se puede abrir la app en navegador.
5. Si usás mail SMTP local, llega correo a Mailpit.

## Notas

- El scheduler es importante en este proyecto porque ejecuta tareas automáticas (newsletters, publicaciones, procesos programados).
- Si no necesitás integraciones externas, podés dejar sus variables vacías para desarrollo de interfaz/flujo base.
