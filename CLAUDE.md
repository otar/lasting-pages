# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application using modern PHP 8.2+ with Vite for frontend asset building and Bootstrap 5.3 for styling. The project uses Pest for testing and Laravel Pint for code formatting.

## Prerequisites

This project uses [Volta](https://volta.sh/) for Node.js version management. Install Volta before running any npm/npx commands:

```bash
# Install Volta (macOS/Linux)
curl https://get.volta.sh | bash

# Or install with Homebrew (macOS)
brew install volta
```

Volta will automatically use the correct Node.js version (22.17.1) specified in package.json.

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
# Run all tests (parallel execution - ~29% faster)
composer test

# Run tests with Pest directly (parallel execution)
./vendor/bin/pest --parallel

# Run specific test file
./vendor/bin/pest tests/Endpoints/AuthenticationTest.php

# Run tests with coverage (parallel execution)
./vendor/bin/pest --parallel --coverage

# Run tests without parallel execution (slower)
php artisan test
```

**Performance**: Tests run in parallel using 8 processes with in-memory SQLite database for maximum speed.

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Run Pint in dry-run mode to see what would change
./vendor/bin/pint --test

# Run PHPStan static analysis (max level)
./vendor/bin/phpstan analyse --memory-limit=256M

# Run PHPStan with verbose output
./vendor/bin/phpstan analyse --verbose --memory-limit=256M

# Run PHPStan on specific path
./vendor/bin/phpstan analyse app/ --memory-limit=256M

# Run PHP Mess Detector
./vendor/bin/phpmd app text phpmd.xml

# Run PHP Magic Number Detector
./vendor/bin/phpmnd app --progress

# Run all code quality checks (includes tests)
composer qa

# Generate coverage report
composer coverage

# Open coverage report (after running composer coverage)
open storage/coverage-report/index.html
```

### Git Hooks
A pre-commit hook automatically runs `composer qa` before each commit to ensure code quality. The hook will:
- Run all QA checks (Pint, PHPStan, PHPMD, PHPMND)
- Prevent commits if any checks fail
- Show helpful messages for fixing issues

**Installation:**
```bash
# Install git hooks (run once after cloning)
./hooks/install.sh
```

The hook source is in `hooks/pre-commit` and gets copied to `.git/hooks/pre-commit` when installed.

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
- Endpoint tests: `tests/Endpoints/`
- Base test case: `tests/TestCase.php`

## Key Technologies
- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vite 7, Bootstrap 5.3 (local)
- **UI Theme**: Sepia color scheme (GetPocket.com inspired)
- **Testing**: Pest PHP, PHPUnit
- **Database**: SQLite (development)
- **Queue**: Laravel Queues
- **Code Style**: Laravel Pint
- **Static Analysis**: PHPStan (max level) with Larastan
- **Code Quality**: PHPMD (PHP Mess Detector), PHPMND (PHP Magic Number Detector)

## Code Architecture Best Practices

- **Service Layer Guidance**:
  - Never write business logic or use models directly in the controllers
  - Move all business logic to the service layer

## Workflow and Best Practices
- Always test with `composer qa` before committing code

## Development Principles

- Always use soft deletes for the models if not told otherwise

## Development Guidance

- Don't use model scopes if not told otherwise
- Dont use controller resources, directly create methods
- Never, ever write PHP code in the views. Pass data from the controller to the views and use it. Views are only for rendering, not writing any kind of logic.

## Migration Guidelines

- When creating a migration, only generate it with up() method, down() is not needed

## Coding Standards
When working with Laravel/PHP projects, first read the coding guidelines at @laravel-php-guidelines.md
