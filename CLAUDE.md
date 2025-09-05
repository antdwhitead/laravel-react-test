# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Primary Development
- `composer dev` - Start full development environment (Laravel server, queue worker, logs, Vite)
- `composer dev:ssr` - Start SSR development environment 
- `npm run dev` - Start Vite development server only
- `npm run build` - Build frontend assets for production
- `npm run build:ssr` - Build frontend assets with SSR support

### Code Quality
- `npm run lint` - Run ESLint and auto-fix issues
- `npm run types` - Run TypeScript type checking
- `npm run format` - Format code with Prettier
- `npm run format:check` - Check code formatting
- `vendor/bin/pint` - Format PHP code (Laravel Pint)

### Testing
- `composer test` - Run PHP tests (clears config and runs Pest)
- `php artisan test` - Run Pest tests directly
- `vendor/bin/pest` - Run specific test files

### Database
- `php artisan migrate` - Run database migrations
- `php artisan db:seed` - Seed database

## Architecture

This is a Laravel + React application using Inertia.js for seamless SPA-like experience.

### Frontend Architecture
- **Framework**: React 19 with TypeScript
- **Styling**: Tailwind CSS v4 with custom UI components
- **Component Library**: Radix UI primitives with custom wrappers in `resources/js/components/ui/`
- **State Management**: Inertia.js shared data and React state
- **Build Tool**: Vite with Laravel integration
- **SSR**: Enabled by default (configurable in `config/inertia.php`)

### Backend Architecture  
- **Framework**: Laravel 12 with PHP 8.2+
- **Frontend Integration**: Inertia.js middleware handles React page rendering
- **Authentication**: Laravel Breeze-style auth with Inertia
- **Database**: SQLite (development), configured in `config/database.php`
- **Testing**: Pest PHP testing framework

### Key Files
- `resources/js/app.tsx` - React application entry point
- `app/Http/Middleware/HandleInertiaRequests.php` - Shared data provider (auth, ziggy routes, app config)
- `routes/web.php` - Main route definitions
- `routes/auth.php` - Authentication routes  
- `routes/settings.php` - User settings routes
- `vite.config.ts` - Frontend build configuration

### Page Structure
- Pages: `resources/js/pages/` (auto-discovered by Inertia)
- Layouts: `resources/js/layouts/` (auth, app, settings)
- Components: `resources/js/components/` (reusable UI components)
- Hooks: `resources/js/hooks/` (custom React hooks)

### Styling System
- Tailwind CSS v4 with CSS-in-JS support
- Component variants using `class-variance-authority`
- Custom UI components follow Radix + shadcn/ui patterns
- Appearance system with light/dark mode support

### Development process

## Backend
- Read .claude/laravel-php-guidelines.md and use this as reference for any laravel code
- All backend code must be feature/unit tested
- Your task is only complete once you have run all tests and they pass
