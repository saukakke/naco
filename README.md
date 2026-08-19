# NACO — Normal Apprenticeship Company

![NACO](https://img.shields.io/badge/NACO-Personnel%20Portal-0B3D2E?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/Database-PostgreSQL-4169E1?style=flat-square&logo=postgresql&logoColor=white)
![Railway](https://img.shields.io/badge/Deploy-Railway-111111?style=flat-square&logo=railway&logoColor=white)
![Blade](https://img.shields.io/badge/UI-Blade%20%2B%20CSS-0B3D2E?style=flat-square)

NACO is a Laravel 12 organizational website and personnel management portal for the Normal Apprenticeship Company. It combines the public website with authenticated personnel management for cadets, instructors, unit commanders, HCS/ward leadership, LGA leadership, state controllers and administrators.

## Status

The project is being developed as a complete Laravel application. The deployment target is **Railway**, with **PostgreSQL** as the production database. Render and SQLite are no longer the deployment/database targets.

## Core capabilities

- Public organization website
- Leadership, ICT team, gallery, blog and contact pages
- Public Service Number verification at `/verify`
- Cadet and instructor management
- Units A–F
- Courses and course results
- Warrants and instructor status
- Promotions and demotions
- Official promotion documents
- Posts and organizational appointments
- Unit transfers
- Ward transfers with hierarchical approval
- ID-card renewal
- Four-monthly reports
- Notifications and email notifications
- Personnel documents
- Audit logs
- Role-based and organizational-scope authorization

## Cadet identity

The **Service Number is the canonical unique identifier for every Cadet** and is used wherever a cadet identifier is required.

```text
Cadet
  └── service_number
        ├── Courses
        ├── Warrants
        ├── Promotions
        ├── Demotions
        ├── Transfers
        ├── ID Cards
        ├── Documents
        └── Personnel records
```

## Organizational hierarchy

```text
National
   ↓
State
   ↓
Chairman Self-Reliance (LGA)
   ↓
HCS / Ward Commander
   ↓
Cadets and Instructors
```

Access is scoped to the organizational jurisdiction. Ward commanders can view personnel in their ward; LGA chairmen can view information in their LGA; state controllers can view their state, its LGAs and wards; authorized national/admin roles can operate across jurisdictions.

## Roles

| Role | Scope |
|---|---|
| Super Admin | Full system access; grant/revoke Admin |
| Admin | Full operational access except Super Admin management |
| State Controller | Own state, LGAs, wards and personnel |
| Chairman Self-Reliance | Own LGA, wards and personnel |
| HCS / Ward Commander | Own ward, cadets and instructors |
| Unit Commander | Assigned unit resources |
| Instructor | Instructor/course/warrant resources and permitted cadets |
| Cadet | Own resources |

Authorization must be enforced server-side through middleware, policies and scoped queries.

## Instructor and warrant rules

- A cadet can also be an instructor.
- An instructor must have a valid warrant.
- A warrant is obtained through instructor course training and successful completion.
- All courses are instructor courses.
- A cadet can have multiple warrants for multiple courses.
- When a warrant expires, the cadet is no longer an active instructor until another valid warrant is obtained.
- Course training has a payment requirement before the warrant process can complete.

## Units and transfers

Every cadet belongs to Unit A, B, C, D, E or F.

Unit transfer:

```text
Cadet applies
 → Current Unit Commander releases
 → Destination Unit Commander accepts
 → Payment
 → Payment verified
 → Unit changes
```

Ward transfer requires source HCS release, LGA/state acknowledgements, destination HCS acceptance and final National approval. If the source and destination wards are in different LGAs, both relevant LGA chairmen and state controllers participate.

## ID-card renewal

A cadet can apply for renewal only when the existing ID card is two months from its due date.

## Ranks

### Other ranks
Private, Corporal, Sergeant, Staff Sergeant, Senior Staff Sergeant, Warrant Officer 2, Warrant Officer 1.

### Junior officers
Second Lieutenant, Lieutenant, Captain.

### Senior officers
Master, Senior Master, Right Comrade.

### Superior officers
Engineer, Chief Engineer, Rear Marshal, Cadet Marshal.

Promotion moves to a higher rank and generates an official document. Demotion moves to a lower rank while preserving history.

## Leadership posts

### National
General Officer; Chief Instructor.

### State
State Controller; Deputy State Controller; National Medical Director; Auditor; Secretary; National Parade Commander; National Intelligent Director; National Provost Marshal; Unit Sergeant Major.

### LGA
Chairman Self-Reliance.

### Ward
HCS.

## Four-monthly reporting

```text
Ward HCS
   ↓
Chairman Self-Reliance
   ↓
State Controller
   ↓
National
```

## Authentication

There is **no public registration**. Authorized personnel sign in through the portal. Supported roles include Cadet, Instructor, Unit Commander, HCS, Chairman Self-Reliance, State Controller, Admin and Super Admin.

## Public verification

`/verify` is available to authenticated and unauthenticated visitors. Verification uses the Cadet's Service Number and returns only controlled public verification information.

## Technology

- Laravel 12
- PHP 8.3
- PostgreSQL
- Laravel Blade
- Vite/CSS/JavaScript
- Laravel session authentication
- Policies and role middleware
- Railway deployment

## Railway + PostgreSQL deployment

The repository contains `railway.json` and a PostgreSQL-ready `backend/Dockerfile`.

The Railway project should contain:

1. A Laravel application service connected to this repository.
2. A Railway PostgreSQL service.
3. The PostgreSQL service variables exposed to the application, preferably through Railway's `DATABASE_URL`.
4. `APP_KEY` and production application settings configured as Railway variables.
5. SMTP variables configured for email notifications.

Required production database configuration can be represented as:

```env
DB_CONNECTION=pgsql
DATABASE_URL=${{Postgres.DATABASE_URL}}
```

Alternatively configure the standard variables:

```env
DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

The application startup runs:

```bash
php artisan migrate --force
php artisan optimize
php artisan serve --host=0.0.0.0 --port=$PORT
```

Do not commit production credentials.

## Local PostgreSQL setup

```bash
git clone https://github.com/saukakke/naco.git
cd naco/backend
composer install
cp .env.example .env
php artisan key:generate
```

Create a PostgreSQL database named `naco`, then configure:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=naco
DB_USERNAME=postgres
DB_PASSWORD=your-password
```

Run:

```bash
php artisan migrate
php artisan serve
```

Frontend assets:

```bash
npm install
npm run dev
```

## Environment variables

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://your-railway-domain.example

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

## Design system

The UI follows a premium institutional design while preserving NACO's brand identity.

| Role | Value |
|---|---|
| Dark Green | `#0B3D2E` |
| Deep Green | `#071A16` |
| Yellow | `#D9AA3D` |
| Black | `#050806` |
| Surface | `#F5F7F4` |
| White | `#FFFFFF` |

## Repository structure

```text
naco/
├── backend/
│   ├── app/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── Dockerfile
│   └── README.md
├── docs/
│   ├── PORTAL-DOMAIN.md
│   └── NACO-PORTAL-DOCUMENTATION.md
├── railway.json
└── README.md
```

## Security

- No public registration.
- Protected portal resources require authentication.
- Authorization is server-side.
- Organizational scope is enforced for personnel access.
- Service Number is the canonical Cadet identifier.
- Public verification exposes controlled information only.
- Secrets remain in Railway environment variables.
- PostgreSQL is the production persistence layer.

## Development convention

For a new feature, implement all required migrations, models, controllers, policies, validation, routes, notifications, views and tests together; verify relationships and authorization; then push the completed implementation to `main`.

## Documentation

- Backend architecture: `backend/README.md`
- Domain specification: `docs/PORTAL-DOMAIN.md`
- Full project documentation: `docs/NACO-PORTAL-DOCUMENTATION.md`

## Repository

urlNACO GitHub Repositoryhttps://github.com/saukakke/naco
