# Comandos rápidos de ConocIA

Guárdalo como referencia diaria para no depender de memoria.

## Arranque diario (Docker)

```bash
make up
make dev
```

## Verificación rápida

```bash
make check
```

## Logs e infraestructura

```bash
make logs
make docker-down
docker compose ps
```

## Base de datos

```bash
make migrate
make fresh
```

## Accesos locales

- App: http://127.0.0.1:8000
- Mailpit: http://127.0.0.1:8025
- MySQL Docker host: 127.0.0.1
- MySQL Docker port: 3307

## Comandos equivalentes sin Make

```bash
docker compose up -d
composer install
npm install
php artisan key:generate
php artisan migrate
composer dev
```