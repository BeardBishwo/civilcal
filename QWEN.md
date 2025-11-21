# Bishwo Calculator - QWEN.md

## Project Overview

Bishwo Calculator is a comprehensive, MVC-based engineering calculator web application designed primarily for AEC (Architecture, Engineering, Construction) professionals. The application provides a wide range of specialized calculators for civil, electrical, structural, HVAC, plumbing, and other engineering disciplines. It features a modern PHP MVC framework with authentication, admin panels, calculator management, and extensive API support.

The application follows a modular architecture with support for plugins, themes, and a comprehensive admin panel for managing users, calculators, content, and system settings. It includes advanced features like two-factor authentication, history management, export functionality, and payment integration.

## Architecture & Structure

The application is organized with a traditional MVC pattern:

- `app/` - Core application code (Controllers, Models, Services, Calculators, Middleware)
- `api/` - API endpoints
- `config/` - Configuration files
- `database/` - Database schema and migrations
- `includes/` - Helper functions
- `modules/` - Modular calculator implementations
- `public/` - Public assets and entry point
- `themes/` - Frontend themes and templates
- `vendor/` - Composer dependencies

## Key Technologies

- PHP 7.4+ (MVC framework)
- Composer for dependency management
- MySQL for data storage
- Modern CSS with responsive design
- JavaScript for enhanced UI functionality
- Third-party libraries like Google2FA, PHPMailer, and math libraries

## Building and Running

### Prerequisites
- PHP 7.4 or higher
- MySQL database
- Composer
- Web server (Apache/Nginx)

### Setup
1. Clone or download the repository
2. Run `composer install` to install dependencies
3. Set up your database and configure database credentials in `.env` or config files
4. Run database migrations if any exist
5. Set up a virtual host or use PHP's built-in server for development (`php -S localhost:8080 -t public/`)

### Configuration
- Copy `.env.example` to `.env` and configure database credentials
- Update configuration in `config/app.php` if needed

## Key Features

### Calculator System
- Specialized calculators for various engineering disciplines
- Modular design supporting custom calculators
- Calculation history and favorites
- Export functionality for reports

### Authentication & Authorization
- User registration and login system
- Session-based authentication
- HTTP Basic Authentication support
- Two-factor authentication (2FA)
- Role-based access control (admin/user)

### Admin Panel
- Comprehensive administrative dashboard
- User management system
- Calculator management
- Content management (pages, menus, etc.)
- Theme and plugin management
- Analytics and reporting
- System settings and configuration

### API System
- RESTful API endpoints
- Multiple authentication methods
- Rate limiting
- CORS support
- Versioned API endpoints

### Frontend & UI
- Responsive design
- Theme customization
- Dark/light mode support
- Modern UI with CSS frameworks
- Interactive calculator interfaces

## Development Conventions

### Controllers
- Located in `app/Controllers/`
- Follow naming convention: `{Name}Controller.php`
- Extend `App\Core\Controller`
- Use middleware for authentication and authorization

### Models
- Located in `app/Models/`
- Follow naming convention: `{Name}.php`
- Handle database interactions and business logic

### Views
- Located in `themes/default/views/` or theme-specific directories
- Use PHP templating with HTML
- Organized by controller/function

### Middleware
- Located in `app/Middleware/`
- Handle authentication, authorization, security
- Applied in routes.php

### Routing
- Configured in `app/routes.php`
- RESTful conventions (GET, POST, PUT, DELETE)
- Support for route parameters and middleware

## Key Files & Components

- `index.php` - Application entry point
- `public/index.php` - Main entry point for web requests
- `app/bootstrap.php` - Application initialization
- `app/routes.php` - All application routes
- `app/Core/Router.php` - Custom routing system
- `composer.json` - Dependencies and project metadata
- `config/app.php` - Main application configuration

## Environment Variables

The application uses environment variables defined in `.env`, `.env.production` files to manage configuration across different environments.

## Testing & Debugging

- Debug mode can be enabled in config
- Error logging to storage/logs/
- Unit test files in tests/ directory
- Debug tools available in admin panel

## Security Features

- CSRF protection
- Session management
- Input validation and sanitization
- SQL injection prevention
- Rate limiting
- Authentication middleware
- Secure password hashing

## API Endpoints

The application provides a comprehensive API for calculators, user management, and system functions accessible via `/api/` endpoints with various authentication methods.

## Deployment

1. Prepare production environment
2. Update `.env.production`
3. Run `composer install --no-dev` 
4. Configure web server
5. Set proper file permissions
6. Update database as needed

## Common Tasks

### Adding a new calculator:
1. Create calculator class in `modules/{category}/{calculator-type}/`
2. Register in the appropriate location
3. Add routes if needed
4. Create UI templates

### Adding a new route:
1. Add route definition in `app/routes.php`
2. Create controller method if needed
3. Create view if needed

### Adding middleware:
1. Create middleware class in `app/Middleware/`
2. Add to middleware map in Router
3. Apply to routes as needed