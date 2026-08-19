# NACO Portal — System Documentation

## 1. Overview

NACO is a Laravel 12 web platform combining a public organizational website with a personnel management portal. Production runs on **Railway** with **PostgreSQL**.

## 2. Technology architecture

```text
Browser
   │
   ├── Public Blade pages
   └── Authenticated Portal
          │
          ▼
     Laravel 12
          │
    ┌─────┴─────┐
    ▼           ▼
PostgreSQL   Mail/Notifications
          │
          ▼
       Railway
```

The application uses Blade templates, Laravel routing/controllers, policies and middleware, PostgreSQL persistence, session authentication and email/web notifications.

## 3. Canonical personnel identity

Every Cadet has a unique **Service Number**. The Service Number is the canonical Cadet identifier throughout the application and is also the identifier used by the public verification feature.

No public registration is provided.

## 4. Organizational hierarchy

```text
National
  └── State
       └── LGA / Chairman Self-Reliance
            └── Ward / HCS
                 └── Cadets + Instructors
```

A State Controller can view information throughout the assigned state, including its LGAs and wards. A Chairman Self-Reliance can view information within the assigned LGA. An HCS/Ward Commander can view cadets and instructors linked to the assigned ward.

## 5. Roles

### Super Admin

Global access. Can make or revoke Admin roles. Super Admin accounts cannot be modified by ordinary Admins.

### Admin

Full operational management across the application, except Super Admin role administration.

### State Controller

Read and manage resources permitted within the assigned state and its subordinate LGAs and wards.

### Chairman Self-Reliance

Read and manage resources permitted within the assigned LGA and its subordinate wards.

### HCS / Ward Commander

Read and manage resources permitted within the assigned ward, including cadets and instructors.

### Unit Commander

Manage resources associated with the assigned unit.

### Instructor

Access instructor, course and warrant resources and other cadet resources permitted by policy.

### Cadet

Access personal profile, applications, documents, course participation, warrants, transfers and other resources permitted to the cadet.

## 6. Core domain entities

### Cadet

Central personnel record. Each cadet has one Service Number, one current unit and one current rank.

### Instructor

A cadet who has an active qualifying warrant. Instructor status is therefore tied to warrant validity.

### Course

All courses in the portal are instructor courses. Example courses include Drill, BF, DS, Islamic and Admin.

### Warrant

Evidence of instructor authority obtained through successful qualifying course training. A cadet may have multiple warrants for multiple courses.

### Unit

Cadets belong to Unit A, B, C, D, E or F.

### Promotion

Moves a cadet to a higher rank. Approved promotions generate an official document.

### Demotion

Moves a cadet to a lower rank while retaining historical rank records.

### Post

An organizational appointment at National, State, LGA or Ward level. Post assignment is independent of rank.

## 7. Ranks

### Other ranks

1. Private
2. Corporal
3. Sergeant
4. Staff Sergeant
5. Senior Staff Sergeant
6. Warrant Officer 2
7. Warrant Officer 1

### Junior Officers

8. Second Lieutenant
9. Lieutenant
10. Captain

### Senior Officers

11. Master
12. Senior Master
13. Right Comrade

### Superior Officers

14. Engineer
15. Chief Engineer
16. Rear Marshal
17. Cadet Marshal

Promotion and demotion decisions use rank ordering.

## 8. Leadership posts

### National

- General Officer
- Chief Instructor

### State

- State Controller
- Deputy State Controller
- National Medical Director
- Auditor
- Secretary
- National Parade Commander
- National Intelligent Director
- National Provost Marshal
- Unit Sergeant Major

### LGA

- Chairman Self-Reliance

### Ward

- HCS

## 9. Unit transfer workflow

A unit transfer is not immediate.

```text
Cadet submits application
        ↓
Current Unit Commander releases
        ↓
Destination Unit Commander accepts
        ↓
Payment is made
        ↓
Payment is verified
        ↓
Cadet's unit is changed
```

The backend should reject attempts to bypass an approval or payment state.

## 10. Ward transfer workflow

```text
Cadet applies
   ↓
Current HCS releases
   ↓
LGA acknowledgement
   ↓
State acknowledgement
   ↓
Destination HCS accepts
   ↓
Destination LGA/State approvals where applicable
   ↓
National final approval
   ↓
Transfer takes effect
```

For transfers between wards in different LGAs, both LGAs and both relevant state authorities participate. National approval is mandatory and final.

## 11. Instructor and course workflow

```text
Cadet selects course
   ↓
Course payment
   ↓
Course training
   ↓
Assessment / pass
   ↓
Warrant issued
   ↓
Instructor status active
```

When a warrant expires, instructor status becomes inactive until a new valid warrant exists.

## 12. ID-card renewal

The application window opens when the cadet's current card is two months from its due date. Applications outside the allowed window must be rejected by backend validation.

## 13. Four-monthly reports

Every Ward HCS submits a four-month report to the Chairman Self-Reliance. The Chairman submits it to State, and State submits it to National.

```text
HCS → Chairman Self-Reliance → State Controller → National
```

Each transition should be authorized by the appropriate organizational role.

## 14. Public Service Number verification

The verification page is public and does not require login.

```text
/verify
```

Input: Cadet Service Number.

The result must contain only controlled verification fields. Private contact information, credentials and internal administrative data must never be returned by the public endpoint.

## 15. Authentication

Authentication is exclusive of public registration.

Login supports the configured portal roles and establishes a Laravel authenticated session. Successful authentication regenerates the session. Logout invalidates the session and regenerates the CSRF token.

## 16. Authorization model

Every protected controller action should evaluate both:

```text
role permission + organizational scope
```

Example:

```text
HCS of Ward A
  ├── Cadet in Ward A → allowed
  └── Cadet in Ward B → forbidden
```

The same rule applies when a resource is accessed directly through a URL or API request.

## 17. Notifications

The portal supports in-app notifications and email notifications for workflow events. Production email configuration is provided through Railway environment variables.

## 18. Database architecture

PostgreSQL is the production and local development target.

Required production configuration can use Railway's PostgreSQL `DATABASE_URL`:

```env
DB_CONNECTION=pgsql
DATABASE_URL=...
```

The Laravel PostgreSQL driver is enabled by `pdo_pgsql` in the backend Docker image.

## 19. Railway deployment

The repository contains `railway.json` at the project root and a Dockerfile in `backend/`.

### Railway services

Create:

1. Laravel application service.
2. PostgreSQL database service.

Connect the PostgreSQL service variables to the Laravel service. Configure `APP_KEY`, `APP_URL`, mail credentials and any additional secrets.

### Deployment lifecycle

The application starts with:

```bash
php artisan migrate --force
php artisan optimize
php artisan serve --host=0.0.0.0 --port=$PORT
```

### Production environment

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=...
APP_URL=...
DB_CONNECTION=pgsql
DATABASE_URL=...
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

## 20. Local development

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Configure a local PostgreSQL database before running migrations.

## 21. Verification checklist

Before deploying a feature:

- Run migrations.
- Check route registration.
- Verify authorization boundaries.
- Test the Service Number relationship.
- Test valid and expired warrants.
- Test workflow approval transitions.
- Test invalid direct URL access.
- Test notification delivery.
- Test public verification without authentication.
- Run the automated test suite.

## 22. Security requirements

- Never commit `.env` or credentials.
- Never trust client-side role selectors as authorization.
- Enforce scope on database queries and controller actions.
- Validate all workflow transitions server-side.
- Protect CSRF-enabled browser forms.
- Use hashed passwords.
- Limit public verification data.
- Record sensitive administrative actions in the audit trail.

## 23. Related files

- `../README.md` — project overview
- `../backend/README.md` — backend architecture and deployment
- `PORTAL-DOMAIN.md` — core domain specification
