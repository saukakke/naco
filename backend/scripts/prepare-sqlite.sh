#!/usr/bin/env bash
set -euo pipefail
DB_PATH="${DB_DATABASE:-database/database.sqlite}"
mkdir -p "$(dirname "$DB_PATH")"
if [ ! -f "$DB_PATH" ]; then touch "$DB_PATH"; fi
php artisan config:clear
php artisan migrate --force
