#!/usr/bin/env sh
set -eu
cd /var/www/html
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/app/private storage/logs public/storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public/storage
su-exec www-data php artisan package:discover --ansi
su-exec www-data php artisan config:cache
su-exec www-data php artisan route:cache
su-exec www-data php artisan view:cache
if [ "$1" = php-fpm ]; then exec "$@"; fi
exec su-exec www-data "$@"
