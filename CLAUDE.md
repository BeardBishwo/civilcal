# Bishwo Calculator - Claude Instructions

## Project Overview

**Bishwo Calculator** is a comprehensive MVC-based engineering calculator web application designed for AEC (Architecture, Engineering, Construction) professionals. The application provides specialized calculators for civil, electrical, structural, HVAC, plumbing, and other engineering disciplines.

### Tech Stack
- **Backend**: PHP 7.4+ with custom MVC framework
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript (Vanilla)
- **Dependency Management**: Composer
- **Server**: Apache with mod_rewrite (.htaccess)

## Project Structure

```
Bishwo_Calculator/
├── app/                    # Core application
│   ├── Controllers/        # MVC Controllers
│   ├── Models/            # Database models
│   ├── Services/          # Business logic services
│   ├── Middleware/        # Authentication & security
│   ├── Calculators/       # Calculator implementations
│   ├── Core/              # Framework core (Router, Controller, etc.)
│   ├── bootstrap.php      # App initialization
│   └── routes.php         # Route definitions
├── api/                   # API endpoints
├── config/                # Configuration files
├── database/              # Database schema & migrations
├── modules/               # Modular calculator implementations
├── public/                # Public assets & entry point
├── storage/               # Logs, cache, uploads
├── themes/                # Frontend themes & views
├── tests/                 # Test files
└── vendor/                # Composer dependencies
```

## Key Features

### 1. Authentication & Authorization
- Session-based authentication with CSRF protection
- Two-Factor Authentication (2FA) support
- Role-based access control (Admin/User)
- Secure password hashing

### 2. Admin Panel
**Recent Focus Area** - The admin panel has been a major development focus:
- **Dashboard**: Analytics and system overview
- **User Management**: CRUD operations for users
- **Settings Module**: General, Email (SMTP), Security settings
- **Debug Tools**: Error logs, system tests, live error monitoring
- **Content Management**: Pages, menus, calculators

**Key Admin URLs:**
- `/admin` - Dashboard
- `/admin/users` - User management
- `/admin/settings` - Settings (General, Email, Security)
- `/admin/debug` - Debug dashboard with system diagnostics
- `/admin/debug/errors` - Error logs viewer
- `/admin/debug/tests` - System tests runner

### 3. Calculator System
- Modular calculator architecture
- History tracking and favorites
- Export functionality
- Wide range of engineering calculators

### 4. API System
- RESTful API endpoints (`/api/`)
- Multiple authentication methods
- Rate limiting and CORS support

## Development Conventions

### Controllers
- Location: `app/Controllers/`
- Naming: `{Name}Controller.php`
- Base class: `App\Core\Controller`
- Admin controllers in: `app/Controllers/Admin/`

### Models
- Location: `app/Models/`
- Handle database interactions
- Use prepared statements for security

### Views
- Location: `themes/default/views/` or `app/views/`
- Admin views: `app/views/admin/`
- Use PHP templating
- Layouts in: `app/views/layouts/`

### Routing
- All routes defined in: `app/routes.php`
- Router class: `app/Core/Router.php`
- Supports middleware, route parameters, and HTTP methods

### Middleware
- Location: `app/Middleware/`
- Key middleware:
  - `AuthMiddleware` - User authentication
  - `AdminMiddleware` - Admin-only access
  - `CsrfMiddleware` - CSRF protection

## Environment Configuration

### Environment Files
- `.env` - Development configuration
- `.env.production` - Production settings
- `.env.example` - Example/template

### Key Settings
```env
DB_HOST=localhost
DB_NAME=bishwo_calculator
DB_USER=root
DB_PASS=

APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/Bishwo_Calculator
```

## Common Tasks

### Running the Application
```bash
# Development (using Laragon or similar)
# Access via: http://localhost/Bishwo_Calculator

# Or use PHP built-in server:
php -S localhost:8080 -t public/
```

### Installing Dependencies
```bash
composer install
```

### Database Setup
1. Create database: `bishwo_calculator`
2. Import schema from `database/schema.sql` (if exists)
3. Configure `.env` with database credentials

### Adding a New Route
1. Open `app/routes.php`
2. Add route definition:
   ```php
   $router->get('/your-route', 'ControllerName@method', ['middleware' => 'auth']);
   ```
3. Create controller method if needed
4. Create view template

### Adding Admin Features
1. Create controller in `app/Controllers/Admin/`
2. Add routes with `admin` middleware
3. Create views in `app/views/admin/`
4. Update admin navigation in `app/views/layouts/admin.php`

### Testing & Debugging

#### Debug Mode
- Enable in config: `APP_DEBUG=true`
- Error logs: `storage/logs/`
- Admin debug panel: `/admin/debug`

#### Testing Files
- Backend tests: `tests/` directory
- Test documentation:
  - `testsprite_backend_testing_prd.md`
  - `testsprite_frontend_testing_prd.md`

## Recent Development Context

### Completed Work
Based on recent conversation history:

1. **Admin Settings Module** (Latest)
   - Implemented General, Email (SMTP), and Security settings
   - Added test email functionality (`/admin/email/send-test`)
   - Settings persistence and validation

2. **Admin Panel Fixes**
   - Fixed login authentication and CSRF token handling
   - Resolved 500 errors on admin pages
   - Fixed debug page network errors
   - Corrected file permission issues (`storage/cache`)

3. **Debug & Monitoring**
   - Enhanced debug dashboard with auto-refresh
   - Error logs viewer with filtering
   - System health checks
   - Live error monitoring

4. **Backend API Testing**
   - Fixed multiple backend API test failures
   - Improved API authentication handling
   - Enhanced error responses

### Known Issues & Considerations
- Cache directory permissions must be writable (`storage/cache/`)
- CSRF tokens must be properly handled in forms and AJAX requests
- Admin routes require both `auth` and `admin` middleware
- SMTP settings require testing with valid credentials

## Security Best Practices

### CSRF Protection
- All forms must include CSRF token
- Token in session: `$_SESSION['csrf_token']`
- Validate using `CsrfMiddleware` or manual validation

### Authentication
- Always use middleware for protected routes
- Session-based authentication via `AuthMiddleware`
- Admin access requires `AdminMiddleware`

### Input Validation
- Sanitize all user inputs
- Use prepared statements for database queries
- Validate file uploads

## Coding Style

### PHP
- Follow PSR-4 autoloading
- Use namespaces: `App\Controllers\`, `App\Models\`, etc.
- Type hints and return types encouraged
- Document complex functions with PHPDoc

### Database
- Use prepared statements (PDO)
- Parameterized queries to prevent SQL injection
- Connection handling via models

### Frontend
- Vanilla JavaScript (no frameworks currently)
- Responsive CSS design
- Admin panel uses "CodeCyan" theme
- Form validation on client and server side

## Deployment

### Production Checklist
1. Update `.env.production` with production credentials
2. Set `APP_DEBUG=false`
3. Run `composer install --no-dev`
4. Ensure proper file permissions:
   - `storage/` writable
   - `storage/cache/` writable
   - `storage/logs/` writable
5. Configure web server (Apache/Nginx)
6. Set up SSL certificate
7. Review security settings

### File Permissions
```bash
# Linux/Unix
chmod -R 755 storage/
chmod -R 755 storage/cache/
chmod -R 755 storage/logs/
```

## Helpful Commands

### Composer
```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Dump autoload
composer dump-autoload
```

### Testing
```bash
# Run backend tests (if test runner exists)
php tests/run_backend_tests.php

# Access test documentation
# See: testsprite_backend_testing_prd.md
```

## Important Notes for Claude

1. **Always check route definitions** in `app/routes.php` before adding new features
2. **Use existing middleware** rather than creating custom authorization logic
3. **Follow MVC pattern** - keep controllers thin, move logic to services/models
4. **Test in browser** after making changes to verify functionality
5. **Check file permissions** when working with storage directories
6. **CSRF tokens** are required for all POST/PUT/DELETE requests
7. **Admin features** should always use both `auth` and `admin` middleware
8. **Error logging** goes to `storage/logs/error.log`

## Quick Reference

### Create a New Admin Controller
```php
<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

class YourController extends Controller {
    public function index() {
        $this->view('admin/your/index', [
            'title' => 'Your Page'
        ]);
    }
}
```

### Add Admin Route
```php
$router->get('/admin/your-route', 'Admin\\YourController@index', [
    'middleware' => ['auth', 'admin']
]);
```

### Create a View with Admin Layout
```php
<?php $this->layout('layouts/admin', ['title' => 'Page Title']); ?>

<!-- Your content here -->
```

---

**Last Updated**: 2025-11-21  
**Project Status**: Active development, Admin Panel enhancement phase  
**Version**: See `version.json` for current version
