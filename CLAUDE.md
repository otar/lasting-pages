# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application using modern PHP 8.2+ with Vite for frontend asset building and TailwindCSS 4.0 for styling. The project uses Pest for testing and Laravel Pint for code formatting.

## Development Commands

### Starting Development Environment
```bash
composer dev
```
This starts a full development environment with:
- Laravel development server
- Queue worker
- Laravel Pail for real-time logs
- Vite for frontend asset compilation

### Individual Services
```bash
# Laravel development server
php artisan serve

# Frontend asset compilation (watch mode)
npm run dev

# Build frontend assets for production
npm run build

# Queue worker
php artisan queue:work

# Real-time application logs
php artisan pail
```

### Testing
```bash
# Run all tests
composer test
# or
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run tests with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Run Pint in dry-run mode to see what would change
./vendor/bin/pint --test

# Run PHPStan static analysis (max level)
./vendor/bin/phpstan analyse

# Run PHPStan with verbose output
./vendor/bin/phpstan analyse --verbose

# Run PHPStan on specific path
./vendor/bin/phpstan analyse app/
```

### Database
```bash
# Run migrations
php artisan migrate

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name

# Create new model with migration and factory
php artisan make:model ModelName -mf
```

## Architecture

### Backend Structure
- **Controllers**: `app/Http/Controllers/` - HTTP request handling
- **Models**: `app/Models/` - Eloquent models and business logic
- **Providers**: `app/Providers/` - Service providers for dependency injection
- **Routes**: `routes/web.php` - Web routes, `routes/console.php` - Artisan commands
- **Config**: `config/` - Application configuration files
- **Database**: 
  - Migrations: `database/migrations/`
  - Factories: `database/factories/`
  - Seeders: `database/seeders/`
  - SQLite database: `database/database.sqlite`

### Frontend Structure
- **Assets**: `resources/css/app.css`, `resources/js/app.js` - Main asset entry points
- **Views**: `resources/views/` - Blade templates
- **Public**: `public/` - Web accessible files, Vite builds assets to `public/build/`

### Testing
- Uses **Pest PHP** testing framework
- Test configuration in `tests/Pest.php`
- Feature tests: `tests/Feature/`
- Unit tests: `tests/Unit/`
- Base test case: `tests/TestCase.php`

## Key Technologies
- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vite 7, TailwindCSS 4.0, Axios
- **Testing**: Pest PHP, PHPUnit
- **Database**: SQLite (development)
- **Queue**: Laravel Queues
- **Code Style**: Laravel Pint
- **Static Analysis**: PHPStan (max level) with Larastan