#!/bin/sh
set -eu

cd /var/www/html

# Railway supplies the runtime port through PORT.
PORT="${PORT:-8080}"

# Fail fast when the application key is missing rather than starting a broken service.
if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY is not configured."
    exit 1
fi

# Cache only configuration that is safe to cache in a long-running production process.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations once at container startup. Laravel migrations are transactional where
# supported by the database and --force prevents the production confirmation prompt.
php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="$PORT"
