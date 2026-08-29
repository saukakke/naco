#!/usr/bin/env bash
set -euo pipefail

if [ "${APP_ENV:-}" != "production" ]; then
    echo "ERROR: APP_ENV must be production in this deployment."
    exit 1
fi

if [ "${APP_DEBUG:-false}" != "false" ]; then
    echo "ERROR: APP_DEBUG must be false in this deployment."
    exit 1
fi

DB_PATH="${DB_DATABASE:-/var/data/database.sqlite}"
DB_DIR="$(dirname "$DB_PATH")"
mkdir -p "$DB_DIR"

if [ ! -f "$DB_PATH" ]; then
    touch "$DB_PATH"
fi

php artisan config:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
