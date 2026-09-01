#!/bin/sh
set -eu

cd /var/www/html

# Use Railway's PORT when provided; otherwise listen on 10000.
PORT="${PORT:-10000}"

if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY is not configured."
    exit 1
fi

if [ "${APP_ENV:-}" != "production" ]; then
    echo "ERROR: APP_ENV must be production in this deployment."
    exit 1
fi

if [ "${APP_DEBUG:-false}" != "false" ]; then
    echo "ERROR: APP_DEBUG must be false in this deployment."
    exit 1
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="$PORT"
