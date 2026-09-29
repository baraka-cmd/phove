#!/usr/bin/env bash
set -o errexit

composer install --no-dev --optimize-autoloader --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true