#!/usr/bin/env bash
set -o errexit

php artisan migrate --force
php artisan config:clear
php artisan config:cache
php artisan serve --host=0.0.0.0 --port=$PORT