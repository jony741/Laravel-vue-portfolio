# Laravel Vue Portfolio

A portfolio management application built with Laravel, Vue, Inertia, Vite, and Tailwind CSS. The app includes authentication, profile/settings pages, and admin screens for managing portfolio data such as experience, projects, technologies, and tech stack items.

## Tech Stack

- PHP 8.3+
- Laravel 13
- Vue 3
- Inertia.js
- Vite
- Tailwind CSS
- Laravel Fortify
- SQLite by default, with MySQL supported through `.env`

## Requirements

Install these before setting up the project:

- PHP 8.3 or newer
- Composer
- Node.js and npm
- SQLite, or MySQL/MariaDB if you prefer a server database

If you are using XAMPP, make sure PHP is available from your terminal and points to a PHP 8.3+ binary.

## Installation

Clone the project and enter the project directory:

```bash
git clone <repository-url>
cd laravel-vue-portfolio
```

Install PHP dependencies:

```bash
composer install
```

Install JavaScript dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

## Database Setup

The project uses SQLite by default.

Create the SQLite database file:

```bash
touch database/database.sqlite
```

Make sure `.env` contains:

```env
DB_CONNECTION=sqlite
```

Run migrations:

```bash
php artisan migrate
```

Optionally seed a local test user:

```bash
php artisan db:seed
```

Seeded login credentials:

```text
Email: test@example.com
Password: password
```

## MySQL Setup Option

If you prefer MySQL, create a database first, then update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_vue_portfolio
DB_USERNAME=root
DB_PASSWORD=
```

Then run:

```bash
php artisan migrate --seed
```

## Running Locally

Start the Laravel backend:

```bash
php artisan serve
```

In another terminal, start Vite:

```bash
npm run dev
```

Open the app at:

```text
http://127.0.0.1:8000
```

You can also run the combined development command:

```bash
composer run dev
```

That command starts the Laravel server, queue listener, log viewer, and Vite dev server together.

## Production Build

Build frontend assets:

```bash
npm run build
```

For deployment, also cache Laravel configuration and routes:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Useful Commands

Run PHP tests:

```bash
php artisan test
```

Run PHP formatting:

```bash
composer run lint
```

Check PHP formatting:

```bash
composer run lint:check
```

Run frontend linting:

```bash
npm run lint:check
```

Run Vue/TypeScript checks:

```bash
npm run types:check
```

Clear Laravel caches during development:

```bash
php artisan optimize:clear
```

## Main Routes

- `/` - public welcome page
- `/login` - login page
- `/register` - registration page
- `/dashboard` - authenticated dashboard/profile view
- `/admin/profile` - manage profile data
- `/admin/experience` - manage experience data
- `/admin/projects` - manage projects
- `/admin/technologies` - manage technologies
- `/admin/tech-stack` - manage tech stack data
- `/settings/profile` - account profile settings
- `/settings/security` - password, passkeys, and security settings
- `/settings/appearance` - appearance settings

## Troubleshooting

If migrations fail with SQLite, confirm the database file exists:

```bash
touch database/database.sqlite
php artisan migrate
```

If frontend changes do not appear, restart Vite:

```bash
npm run dev
```

If routes, config, or views look stale, clear Laravel caches:

```bash
php artisan optimize:clear
```

If Composer scripts fail because PHP is not found or the wrong PHP version is used, update your shell path so it points to PHP 8.3 or newer.
