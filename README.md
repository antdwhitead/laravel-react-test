# Laravel React Technical Test

## Introduction
This technical test is designed to evaluate your coding skills and is your chance to show off your ability to write clear, maintainable, 
well-tested code.

## 📋 Environment Requirements

- **PHP** >= 8.2
- **Node.js** >= 18
- **Composer** >= 2.0
- **SQLite**

## 🛠 Setup instructions Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel-react-test
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   php artisan db:seed
   ```

## 🏃‍♂️ Development

- **Start full development environment** (Laravel server, queue worker, logs, Vite)
  ```bash
  composer dev
  ```

## 🧪 Testing

- **Run PHP tests**
  ```bash
  composer test
  ```

- **Run Pest tests directly**
  ```bash
  php artisan test
  vendor/bin/pest
  ```

## 🎨 Code Quality

- **Run ESLint and auto-fix issues**
  ```bash
  npm run lint
  ```

- **Run TypeScript type checking**
  ```bash
  npm run types
  ```

- **Format code with Prettier**
  ```bash
  npm run format
  npm run format:check
  ```

- **Format PHP code (Laravel Pint)**
  ```bash
  vendor/bin/pint
  ```

## 🗄 Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `email_verified_at` - Email verification timestamp
- `password` - Hashed password
- `remember_token` - Remember me token
- `created_at`, `updated_at` - Timestamps

### Posts Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `name` - Post title
- `content` - Post content (text)
- `category` - Post category (nullable)
- `created_at`, `updated_at` - Timestamps

### Comments Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `post_id` - Foreign key to posts table
- `content` - Comment content (text)
- `created_at`, `updated_at` - Timestamps

### System Tables
- `password_reset_tokens` - Password reset functionality
- `sessions` - User session management
- `cache` - Application cache storage
- `jobs` - Queue job storage

## 🏗 Architecture

### Frontend Structure
```
resources/js/
├── components/          # Reusable UI components
│   ├── ui/             # Radix UI + shadcn/ui components
│   └── ...             # Custom components
├── hooks/              # Custom React hooks
├── layouts/            # Page layouts (auth, app, settings)
├── lib/                # Utilities and helpers
├── pages/              # Inertia.js pages
│   ├── auth/          # Authentication pages
│   ├── posts/         # Blog post pages
│   └── settings/      # User settings pages
└── types/              # TypeScript type definitions
```

### Backend Structure
```
app/
├── Http/
│   ├── Controllers/    # Request handlers
│   ├── Middleware/     # HTTP middleware
│   └── Requests/       # Form request validation
├── Models/             # Eloquent models
└── Providers/          # Service providers
```

### Key Files
- `resources/js/app.tsx` - React application entry point
- `app/Http/Middleware/HandleInertiaRequests.php` - Shared data provider
- `routes/web.php` - Main route definitions
- `routes/auth.php` - Authentication routes
- `routes/settings.php` - User settings routes
- `vite.config.ts` - Frontend build configuration

## 🎨 Styling System

- **Tailwind CSS v4** with CSS-in-JS support
- **Component variants** using `class-variance-authority`
- **Custom UI components** following Radix + shadcn/ui patterns
- **Appearance system** with light/dark mode support
- **Responsive design** with mobile-first approach

## 📝 Available Routes

### Authentication
- `/login` - User login
- `/register` - User registration
- `/forgot-password` - Password reset request
- `/reset-password` - Password reset form
- `/verify-email` - Email verification

### Application
- `/dashboard` - Main dashboard
- `/posts` - Blog posts listing
- `/posts/create` - Create new post
- `/posts/{id}` - View individual post
- `/posts/{id}/edit` - Edit post

### Settings
- `/settings/profile` - User profile settings
- `/settings/password` - Password change
- `/settings/appearance` - Theme preferences
