# NACO — Normal Apprenticeship Company

![NACO](https://img.shields.io/badge/NACO-Personnel%20Portal-0B3D2E?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/Database-SQLite-003B57?style=flat-square&logo=sqlite&logoColor=white)
![Blade](https://img.shields.io/badge/UI-Blade%20%2B%20CSS-0B3D2E?style=flat-square)
![License](https://img.shields.io/badge/License-To%20be%20defined-lightgrey?style=flat-square)

NACO is a full organizational website and personnel management portal for the **Normal Apprenticeship Company**. The platform combines the public-facing organization website with a Laravel 12 personnel portal for cadets, instructors, unit commanders, HCS/ward commanders, LGA leadership, state controllers and administrators.

> **Design principle:** premium, disciplined and institutional. The interface retains NACO's **dark green, yellow and black** identity throughout the public website and authenticated portal.

## Project status

The project is being developed as a complete Laravel application. The public website, authentication foundation, personnel workflows, role-based access model and SQLite/Render deployment configuration are being implemented incrementally.

## Core capabilities

### Public website

- Home / organization overview
- About NACO
- Programs and activities
- Leadership structure
- ICT/team section
- Gallery
- Blog
- Impact
- Contact
- Public personnel verification by **Service Number**

### Personnel portal

The portal is designed around these core entities:

- Cadet
- Instructor
- Course
- Unit
- Warrant
- Promotion
- Demotion
- Post

Additional operational resources include ID-card renewals, unit transfers, ward transfers, reports, notifications, personnel documents and audit records.

## Cadet identity

The **Service Number is the canonical identity of a Cadet**. It is unique to every cadet and is used throughout the application wherever a cadet identifier is required.

```text
Cadet
  └── service_number (unique primary identifier)
        ├── Warrants
        ├── Courses
        ├── Promotions
        ├── Demotions
        ├── Transfers
        ├── ID Cards
        ├── Documents
        └── Personnel records
```

The public verification page accepts a Service Number and can be used without authentication.

## Organizational hierarchy

NACO's operational hierarchy is:

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

Access is scoped to this hierarchy. A ward commander manages personnel in the commander's ward; an LGA chairman manages records within the LGA; and a state controller can view records across the state, its LGAs and wards.

## Roles and access

| Role | Scope / privileges |
|---|---|
| **Super Admin** | Full system access; can grant and revoke Admin privileges |
| **Admin** | Full operational privileges except Super Admin management |
| **State Controller** | State, LGAs, wards and personnel within the state |
| **Chairman Self-Reliance** | Own LGA, wards and personnel within the LGA |
| **HCS / Ward Commander** | Own ward, including cadets and instructors |
| **Unit Commander** | Resources belonging to the assigned unit |
| **Instructor** | Instructor, course, warrant and permitted cadet resources |
| **Cadet** | Own profile and cadet-authorized resources |

Authorization is intended to be enforced server-side at route/controller/policy/query level, not merely by hiding navigation links.

## Instructor and warrant rules

- A cadet may also be an instructor.
- An instructor must have a valid warrant.
- Warrants are obtained through instructor course training.
- All NACO courses are instructor courses.
- A cadet may hold multiple warrants for multiple courses.
- A cadet ceases to be an active instructor when the relevant warrant expires until another valid warrant is obtained.
- Course training requires payment and successful completion before a warrant can be issued.

## Units

Every cadet belongs to one of the six operational units:

- Unit A
- Unit B
- Unit C
- Unit D
- Unit E
- Unit F

### Unit transfer

A cadet changing units follows an approval workflow:

```text
Cadet applies
   ↓
Current Unit Commander releases
   ↓
Destination Unit Commander accepts
   ↓
Payment
   ↓
Payment verification
   ↓
Unit changed
```

## Ward transfer

Ward transfers require hierarchical approval. The current HCS must release the cadet, the relevant LGA and state authorities must acknowledge the transfer, the destination HCS must accept the cadet, and **National approval is final**.

When source and destination wards belong to different LGAs, both relevant LGA chairmen and state controllers participate in the approval process.

Without National approval, the transfer does not take effect.

## ID-card renewal

A cadet can apply for ID-card renewal only when the existing card is **two months from its due date**. The portal handles the application and subsequent administrative/payment workflow.

## Ranks

### Other ranks

- Private
- Corporal
- Sergeant
- Staff Sergeant
- Senior Staff Sergeant
- Warrant Officer 2
- Warrant Officer 1

### Junior officers

- Second Lieutenant
- Lieutenant
- Captain

### Senior officers

- Master
- Senior Master
- Right Comrade

### Superior officers

- Engineer
- Chief Engineer
- Rear Marshal
- Cadet Marshal

A promotion moves a cadet to a higher rank and a demotion moves the cadet to a lower rank. A promotion produces an official document for the new rank.

## Leadership posts

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

## Four-monthly reporting

Ward HCS officers submit a four-month report to the Chairman Self-Reliance. The report progresses upward through the organization:

```text
Ward HCS
   ↓
Chairman Self-Reliance
   ↓
State Controller
   ↓
National
```

The portal supports report submission, review and notification workflows.

## Authentication

The portal uses authenticated access without public self-registration.

Supported account roles include:

- Cadet
- Instructor
- Unit Commander
- HCS / Ward Commander
- Chairman Self-Reliance
- State Controller
- Admin
- Super Admin

Cadet authentication is based on the unique Service Number, while authorized administrative accounts may use their configured account credentials.

## Public Service Number verification

The verification page is intentionally available to both authenticated and unauthenticated visitors:

```text
/verify
```

A visitor enters a Service Number and receives a controlled verification result. Private personnel information is not intended to be exposed through public verification.

## Technology stack

### Backend

- Laravel 12
- PHP
- Laravel Blade
- Laravel authentication/session infrastructure
- Policies and role middleware
- SQLite

### Frontend

- Blade templates
- Semantic HTML
- Modern CSS
- CSS Grid / Flexbox
- Responsive layouts
- Vanilla JavaScript where required
- Premium UI/UX design system

### Deployment

- Render
- SQLite with a persistent disk
- Database migrations executed during deployment/startup
- Laravel queue support for notifications

## SQLite on Render

Production SQLite is stored on the Render persistent disk:

```text
/var/data/database.sqlite
```

The application is configured to create the database file when necessary and run migrations with:

```bash
php artisan migrate --force
```

A Render persistent disk is required because SQLite is file-based and the database must survive service recreation/deployment.

## Email notifications

The portal supports web notifications and an email notification layer. Production SMTP credentials should be supplied through Render environment variables and must never be committed to the repository.

## Local development

Clone the repository and enter the Laravel backend:

```bash
git clone https://github.com/saukakke/naco.git
cd naco/backend
composer install
cp .env.example .env
php artisan key:generate
```

Create the SQLite database:

```bash
mkdir -p database
touch database/database.sqlite
```

Run migrations:

```bash
php artisan migrate
```

Start the application:

```bash
php artisan serve
```

For frontend assets, install the Node dependencies and run the Vite development server when required by the project configuration:

```bash
npm install
npm run dev
```

## Environment variables

At minimum, production should define:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://your-domain.example

DB_CONNECTION=sqlite
DB_DATABASE=/var/data/database.sqlite
DB_FOREIGN_KEYS=true

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

Never commit real credentials, API keys, SMTP passwords or production `.env` files.

## Design system

The UI uses a premium institutional visual language based on the NACO identity rather than replacing it with generic SaaS colors.

### Brand colors

| Role | Value |
|---|---|
| Dark Green | `#0B3D2E` |
| Deep Green | `#071A16` |
| Yellow | `#D9AA3D` |
| Black | `#050806` |
| Surface | `#F5F7F4` |
| White | `#FFFFFF` |

Design priorities include:

- Strong hierarchy
- Restrained use of color
- High-contrast CTAs
- Consistent spacing
- Responsive navigation
- Accessible focus states
- Clear role-oriented portal navigation
- Dense but readable administrative tables
- Premium cards and command-centre layouts

## Repository structure

```text
naco/
├── backend/
│   ├── app/
│   │   ├── Http/
│   │   ├── Models/
│   │   └── Policies/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── resources/
│   │   ├── css/
│   │   ├── js/
│   │   └── views/
│   ├── routes/
│   └── scripts/
├── render.yaml
└── README.md
```

## Security principles

- No public registration.
- Authentication is required for protected portal resources.
- Authorization must be enforced server-side.
- Personnel access follows organizational scope.
- Super Admin is protected from Admin role management.
- Public Service Number verification exposes only controlled verification information.
- Production credentials remain in environment variables.
- SQLite production data is stored on persistent infrastructure rather than committed to Git.

## Development conventions

When implementing new NACO features:

1. Create all required backend files together.
2. Add migrations for persistent data changes.
3. Add policies/middleware for authorization.
4. Enforce organizational scope in queries/controllers.
5. Add validation and appropriate error handling.
6. Add notification/audit behavior where required.
7. Update Blade UI using the existing NACO design system.
8. Verify routes, migrations and relationships before deployment.
9. Push completed implementation work to `main` when the feature is complete.

## Deployment

The repository contains Render configuration for the Laravel backend. Before production deployment, configure:

- Render persistent disk mounted at `/var/data`
- `APP_KEY`
- `APP_URL`
- Production mail credentials
- Any required third-party service credentials

Then deploy the `main` branch and confirm migrations complete successfully.

## Contact

The original project content contains the following contact information, which should be verified before production launch:

- Phone: `+234 813 014 4920`
- Email: `hello@naco.org.ng`
- Location: Kaduna, Nigeria

## License

No license has been formally specified for the repository. Add the appropriate license before redistribution.

## Repository

urlNACO GitHub Repositoryhttps://github.com/saukakke/naco
