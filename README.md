<div align="center">
  <img src="public/images/doctor-booking-api-banner.svg" alt="Doctor Booking API Banner" width="100%">
</div>

<h1 align="center">Doctor Booking API</h1>

<p align="center">
  A clean and organized Laravel 13 API for doctor appointment booking, payments, reviews, translations, and admin management.
</p>

<p align="center">
  <strong>Stack:</strong> PHP 8.4 · Laravel 13 · MySQL · Vite · Tailwind CSS 4 · Spatie Permission
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#architecture">Architecture</a> •
  <a href="#database-models">Database Models</a> •
  <a href="#local-setup">Local Setup</a> •
  <a href="#testing">Testing</a>
</p>

---

## Overview

Doctor Booking API is a Laravel 13 backend built around a versioned API structure. The project focuses on clear separation of concerns, reusable services, repository abstractions, and role-based access control.

## Features

- Authentication flow: register, login, logout, forgot password, reset password, and change password
- Social authentication entry point
- Doctor and specialty management
- Schedule and time slot handling
- Appointment booking and status tracking
- Payment processing support
- Review creation and approval workflow
- Translation, locale, and settings management
- Spatie roles and permissions integration

## Architecture

The project is organized for maintainability and long-term growth.

<table>
  <tr>
    <td><strong>Layer</strong></td>
    <td><strong>Purpose</strong></td>
  </tr>
  <tr>
    <td><code>app/Http/Controllers/Api/V1</code></td>
    <td>Versioned API controllers for auth, admin, doctor, patient, and social flows.</td>
  </tr>
  <tr>
    <td><code>app/Http/Requests/Api/V1</code></td>
    <td>Dedicated form requests for store/update validation.</td>
  </tr>
  <tr>
    <td><code>app/Models</code></td>
    <td>Domain models for users, doctors, appointments, payments, reviews, and settings.</td>
  </tr>
  <tr>
    <td><code>app/Repositories</code></td>
    <td>Repository implementations for persistence logic.</td>
  </tr>
  <tr>
    <td><code>app/Repositories/Interfaces</code></td>
    <td>Repository contracts and payment gateway abstractions.</td>
  </tr>
  <tr>
    <td><code>app/Services</code></td>
    <td>Business logic for auth, appointments, payments, and translations.</td>
  </tr>
  <tr>
    <td><code>app/Events</code> / <code>app/Listeners</code></td>
    <td>Event-driven notifications and side effects.</td>
  </tr>
  <tr>
    <td><code>app/Jobs</code></td>
    <td>Queued background tasks such as reminders and payment webhooks.</td>
  </tr>
  <tr>
    <td><code>app/Policies</code></td>
    <td>Authorization policies for protected actions.</td>
  </tr>
</table>

## Database Models

The main database structure includes:

| Table | Purpose |
| --- | --- |
| `users` | Patients, doctors, and admin users |
| `roles`, `permissions`, `model_has_roles`, `role_has_permissions` | Spatie permissions tables |
| `specialties` | Doctor specialties |
| `doctors` | Doctor profiles and professional data |
| `time_slots` | Availability windows |
| `appointments` | Booking records |
| `payments` | Payment transactions |
| `reviews` | Doctor reviews and ratings |
| `translations` | Polymorphic translation records |
| `locales` | Supported locales |
| `settings` | Application settings |

## Directory Structure

```text
app/
├── Events
├── Http
│   ├── Controllers/Api/V1
│   ├── Middleware
│   ├── Requests/Api/V1
│   └── Resources/Api/V1
├── Jobs
├── Listeners
├── Models
├── Policies
├── Providers
├── Repositories
│   └── Interfaces
└── Services
```

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
npm install
npm run build
php artisan serve
```

For frontend development:

```bash
npm run dev
```

## Testing

```bash
php artisan test
```

## CI Workflow

The repository includes a GitHub Actions workflow in `.github/workflows/test.yml` that:

- runs on pushes and pull requests targeting `dev` and `main`
- installs PHP 8.4 dependencies
- runs Laravel Pint
- refreshes the database
- executes the test suite

## Notes

- The root route currently returns the default Laravel welcome page.
- The banner image used in this README is stored in `public/images/doctor-booking-api-banner.svg`.
- The project is intentionally structured to stay clear, modular, and easy to extend.

