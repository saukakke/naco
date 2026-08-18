# NACO Portal Backend — Laravel 12

This directory contains the planned Laravel 12 backend for the NACO personnel portal.

## Architecture

The backend will provide authentication and role-based access for:

- Administrator
- Instructor
- Unit Commander

Core personnel domains:

- Cadets
- Units A–F
- Rank categories and ranks
- Posts
- Courses
- Course enrolments/results
- Warrants
- Instructors
- Promotions
- Demotions
- Official documents
- Audit logs

## Laravel 12 setup

Create the application in this directory with Laravel 12 and PHP 8.2+:

```bash
composer create-project laravel/laravel . "^12.0"
```

Configure `.env` for PostgreSQL or MySQL. Do not commit `.env` or credentials.

## Authentication

The production implementation should use Laravel's server-side authentication with hashed passwords, sessions, CSRF protection, policies/gates and role-based authorization. The current static portal login is only a frontend prototype and must not be treated as secure authentication.

## Domain rules

1. Every active cadet belongs to exactly one of Unit A–F.
2. An instructor is a cadet with a valid instructor warrant.
3. A warrant must reference the qualifying course/training record.
4. Promotion requires a new rank with a higher rank order than the current rank.
5. Demotion requires a new rank with a lower rank order than the current rank.
6. The same rank cannot be recorded as either a promotion or demotion.
7. Rank history is immutable/auditable; current rank is derived from approved personnel events.
8. Posts are organisational appointments and are independent of rank changes.
9. Approved promotions generate an official promotion document.
10. Sensitive personnel changes must be recorded in an audit trail.

See `../docs/PORTAL-DOMAIN.md` for the complete domain specification.
