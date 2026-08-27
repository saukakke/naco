# Railway deployment

## Required service variables

Set these in the Railway service environment:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=<generated Laravel application key>`
- `APP_URL=<public Railway URL or custom API URL>`
- `LOG_CHANNEL=stderr`
- `LOG_LEVEL=info`
- Database variables appropriate to the Railway PostgreSQL service (`DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- `SESSION_DRIVER=database` or another explicitly configured production driver
- `CACHE_STORE=database` or another explicitly configured production store

Do not commit secrets to the repository.

## Deployment behavior

Railway builds `backend/Dockerfile`. The container entrypoint validates `APP_KEY`, builds Laravel production caches, runs `php artisan migrate --force`, and starts Laravel on Railway's `$PORT`.

Railway health checks use Laravel's `/up` endpoint.

## Important

The Railway PostgreSQL service must be provisioned and its connection variables must be available to the backend service before the first deployment. If the project is intentionally deployed with SQLite instead, configure `DB_CONNECTION=sqlite` and provide a persistent mounted volume for the SQLite database; an ephemeral container filesystem must not be used for production data.
