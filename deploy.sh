#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════
#  ConocIA — Script de despliegue a producción
#  Ejecutar desde la raíz del proyecto: bash deploy.sh
# ══════════════════════════════════════════════════════════════
set -e

echo "▶ [1/8] Poniendo sitio en mantenimiento..."
php artisan down --render="errors.503" --secret="deploy-$(date +%s)" || true

echo "▶ [2/8] Actualizando dependencias Composer (sin dev)..."
composer install --optimize-autoloader --no-dev --no-interaction

echo "▶ [3/8] Migraciones de base de datos..."
php artisan migrate --force

echo "▶ [4/8] Limpiando caches anteriores..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "▶ [5/8] Compilando caches de producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "▶ [6/8] Enlace de storage..."
php artisan storage:link --force 2>/dev/null || true

echo "▶ [7/8] Optimizando autoloader..."
php artisan optimize

echo "▶ [8/8] Levantando sitio..."
php artisan up

echo ""
echo "✓ Despliegue completado en $(date '+%Y-%m-%d %H:%M:%S')"
echo ""
echo "═══ Próximos pasos manuales en el servidor: ═══"
echo "  • Configurar cron: * * * * * php /ruta/artisan schedule:run >> /dev/null 2>&1"
echo "  • Configurar Supervisor para queue worker:"
echo "      [program:conocia-worker]"
echo "      command=php /ruta/artisan queue:work --sleep=3 --tries=3 --max-time=3600"
echo "  • Verificar permisos: chmod -R 775 storage bootstrap/cache"
echo "  • Reiniciar queue workers: php artisan queue:restart"
