#!/usr/bin/env sh
set -e

mkdir -p storage/framework/cache storage/framework/views storage/framework/sessions storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

php artisan optimize:clear || true

exec php-fpm -F


