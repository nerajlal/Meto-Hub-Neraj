#!/bin/bash
set -e

# Ensure storage subdirectories exist with proper permissions
mkdir -p \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/app/private \
    /var/www/html/storage/logs \
    /var/www/html/public/storage \
    /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage

# If running as app container, optionally optimize caches in production
if [ "$1" = 'php-fpm' ] && [ "$APP_ENV" = "production" ]; then
    echo "Caching routes and configuration for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

exec "$@"
