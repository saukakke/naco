# NACO Portal Backend — Laravel 12

The NACO backend is the Laravel 12 application powering the personnel portal and public verification services. Production deployment targets **Railway** with **PostgreSQL**.

## Stack

- PHP 8.3+
- Laravel 12
- PostgreSQL
- Laravel Blade
- Laravel session authentication
- Policies and role middleware
- Vite/CSS/JavaScript
- Railway

## Architecture

```text
National
   ↓
State
   ↓
Chairman Self-Reliance (LGA)
   ↓
HCS / Ward Commander
   ↓
Cadets / Instructors
```

Core entities include Cadets, Instructors, Courses, Course enrolments/results, Units A–F, Warrants, Ranks, Promotions, Demotions, Posts, Official documents, Unit transfers, Ward transfers, ID-card renewals, Four-monthly reports, Notifications, Audit logs, Users and Roles.

## Cadet identifier

`service_number` is the canonical unique identifier for every Cadet. Application relationships and route model binding should use the service number wherever a cadet identifier is required.

## Roles and authorization

Supported roles:

- `super_admin`
- `admin`
- `state_controller`
- `chairman_self_reliance`
- `hcs`
- `unit_commander`
- `instructor`
- `cadet`

Authorization has two dimensions: role permission and organizational scope. A Ward Commander is restricted to the commander's ward; a Chairman Self-Reliance is restricted to the LGA; a State Controller is restricted to the state, its LGAs and wards. Super Admin has global access and alone can grant or revoke Admin privileges.

## Authentication

There is no public registration. Authentication uses Laravel server-side sessions, password hashing, CSRF protection, session regeneration, logout invalidation, role validation and authorization middleware/policies.

## Domain rules

1. Every active cadet belongs to exactly one of Units A–F.
2. A cadet can also be an instructor.
3. An instructor must have a valid warrant obtained through instructor course training.
4. All courses are instructor courses.
5. A cadet can hold multiple warrants for multiple courses.
6. An expired warrant means the cadet is no longer an active instructor until another valid warrant is obtained.
7. Promotion requires a higher rank; demotion requires a lower rank.
8. Rank history is retained and auditable.
9. Approved promotion generates an official promotion document.
10. Posts are organizational appointments independent of rank.
11. Sensitive changes are recorded in the audit trail.

## Transfer workflows

### Unit transfer

```text
Cadet application → Current Unit Commander release → Destination Unit Commander acceptance → Payment → Payment verification → Unit update
```

### Ward transfer

The current HCS releases the cadet, the relevant LGA and State authorities acknowledge the transfer, the destination HCS accepts the cadet and National gives final approval. Cross-LGA transfers require both relevant LGA chairmen and state controllers. Without National approval the transfer does not take effect.

## Reporting

```text
Ward HCS → Chairman Self-Reliance → State Controller → National
```

## ID-card renewal

A cadet may apply only when the current ID card is within two months of its due date.

## Public verification

`GET /verify` accepts a Service Number without authentication and returns controlled verification information only. Private personnel information must not be exposed.

## PostgreSQL

Production uses PostgreSQL; SQLite is no longer the deployment database.

Local configuration:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=naco
DB_USERNAME=postgres
DB_PASSWORD=your-password
```

Railway can provide:

```env
DB_CONNECTION=pgsql
DATABASE_URL=${{Postgres.DATABASE_URL}}
```

## Railway deployment

The repository root contains `railway.json`; the backend contains a PostgreSQL-ready Dockerfile. Configure a Railway application service and PostgreSQL service, expose the PostgreSQL connection to the application, set `APP_KEY`, production application settings and mail credentials, then deploy.

Production startup runs migrations, Laravel optimization and the application on Railway's `$PORT`.

```bash
php artisan migrate --force
php artisan optimize
php artisan serve --host=0.0.0.0 --port=$PORT
```

## Environment

```env
APP_NAME=NACO
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://...
DB_CONNECTION=pgsql
DATABASE_URL=...
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="NACO"
```

Never commit `.env`, database passwords, SMTP credentials, API keys or Railway secrets.

## Local development

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

For frontend assets:

```bash
npm install
npm run dev
```

## Quality checks

```bash
php artisan migrate:status
php artisan route:list
php artisan config:clear
php artisan test
```

For production optimization:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Related documentation

- `../README.md` — project overview and deployment
- `../docs/PORTAL-DOMAIN.md` — domain specification
- `../docs/NACO-PORTAL-DOCUMENTATION.md` — complete system documentation
