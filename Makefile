SHELL := /bin/bash

.PHONY: help env docker-up docker-down up dev logs check migrate fresh

help:
	@echo "Comandos disponibles:"
	@echo "  make up         -> Prepara entorno Docker + instala dependencias + migra"
	@echo "  make dev        -> Levanta app (server, queue, scheduler, logs, vite)"
	@echo "  make logs       -> Logs de infraestructura Docker"
	@echo "  make check      -> Health check rápido del proyecto"
	@echo "  make migrate    -> Ejecuta migraciones"
	@echo "  make fresh      -> Reinstala esquema (migrate:fresh)"
	@echo "  make docker-up  -> Solo infraestructura Docker"
	@echo "  make docker-down-> Baja infraestructura Docker"

env:
	@if [ ! -f .env ]; then \
		cp .env.docker.example .env; \
		echo "Archivo .env creado desde .env.docker.example"; \
	fi

docker-up: env
	docker compose up -d

docker-down:
	docker compose down

up: docker-up
	composer install
	npm install
	php artisan key:generate --ansi
	php artisan migrate --ansi

dev:
	composer dev

logs:
	docker compose logs -f

check:
	php artisan about --ansi
	php artisan migrate:status --ansi

migrate:
	php artisan migrate --ansi

fresh:
	php artisan migrate:fresh --ansi