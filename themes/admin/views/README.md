# MVC Views Directory

This directory contains **Controller-rendered views** for the MVC framework of the Bishwo Calculator application. These views are PHP templates that render HTML content for different parts of the application, processed by Controllers before being sent to the client.

## Directory Structure Overview

```
app/Views/
├── admin/                  # Admin panel views (rendered by AdminController)
├── auth/                   # Authentication views (rendered by AuthController) 
├── user/                   # User dashboard views (rendered by UserController)
├── payment/                # Payment system views (rendered by PaymentController)
├── calculators/            # Calculator-specific views
├── errors/                 # Error handling views
├── help/                   # Help and documentation views
├── share/                  # Public sharing views
├── developer/              # Developer tools and API documentation
├── layouts/                # Layout templates for MVC views
├── partials/               # Reusable partial views for MVC
└── README.md              # This documentation file
```

## Layout System

The layout system provides consistent HTML structure across the application:

### `/layouts/main.php`
- **Purpose**: Main layout for most application pages
- **Features**: 
  - Theme detection and switching (checks for active theme in session)
  - Fallback to default theme if theme files don't exist
  - Includes responsive design with Tailwind CSS
  - Automatic inclusion of header/footer from theme or app
  - Base URL configuration for JavaScript
- **Usage**: Used by most public-facing pages
- **Key Variables**: `$title`, `$content`, `$_SESSION['active_theme']`

### `/layouts/admin.php`
- **Purpose**: Dedicated layout for admin panel
- **Features**:
  - Bootstrap 5 based responsive design
  - Custom CSS variables for theming
  - Fixed sidebar navigation
  - Chart.js integration for analytics
  - Collapsible sidebar with cookie persistence
  - Multi-level submenu support
- **Usage**: Used by all admin panel pages
- **Key Components**: Header, sidebar navigation, main content area

### `/layouts/auth.php`
- **Purpose**: Layout for authentication pages (login, register, etc.)
- **Status**: Currently empty (uses minimal layout)

### Layout Selection Logic
Controllers determine which layout to use based on the context:
- Admin pages → `admin.php`
- Auth pages → `auth.php`
- All other pages → `main.php`

## Partials System

Partials are reusable view components that can be included across multiple views:

### `/partials/navigation.php`
- **Purpose**: Main navigation bar for the application
- **Features**:
  - Authentication-aware menu (different items for logged-in vs. guests)
  - Admin dropdown for administrators
  - User profile dropdown with avatar
  - Mobile-responsive design
  - Active state detection for current page
- **Dependencies**: Bootstrap 5, Font Awesome icons

### Admin Partials (`/admin/partials/`)

#### `/admin/partials/sidebar.php`
- **Purpose**: Left sidebar navigation for admin panel
- **Features**:
  - Hierarchical menu structure with dividers
  - Active state detection based on current URL
  - Expandable submenus with chevron indicators
  - User information display in footer
  - Mobile-responsive toggle functionality
- **Menu Sections**:
  - Dashboard, Users, Calculations
  - Analytics (with submenus)
  - Content, Activity, Audit Logs
  - Email Manager, Subscriptions
  - Settings (with submenus)
  - System tools (Debug, Backup, Status)
  - Help and Support

#### `/admin/partials/topbar.php`
- **Purpose**: Top navigation bar for admin panel
- **Features**:
  - Mobile sidebar toggle
  - Global search functionality
  - Quick access to view site
  - Notification system with badge counter
  - User profile dropdown with quick actions
- **Components**: Search bar, notification dropdown, profile dropdown

## Admin Views (`/admin/`)

The admin views provide a comprehensive backend management interface:

### Core Admin Files
- **`dashboard.php`**: Main admin dashboard with statistics, charts, and quick actions
- **`layout.php`**: Admin-specific layout wrapper
- **`system-status.php`**: System health monitoring and diagnostics

### Admin Subdirectories

#### `/admin/activity/`
- **`index.php`**: Activity log viewer showing recent system actions

#### `/admin/analytics/`
- **`overview.php`**: General analytics dashboard
- **`users.php`**: User-specific analytics and metrics
- **`calculators.php`**: Calculator usage statistics
- **`performance.php`**: System performance metrics
- **`reports.php`**: Generated reports and analytics

#### `/admin/audit/`
- **`index.php`**: Security audit trail viewer

#### `/admin/backup/`
- **`index.php`**: Database backup management interface

#### `/admin/calculations/`
- **`index.php`**: User calculation history and management

#### `/admin/calculators/`
- **`index.php`**: Calculator tool management
- **`list.php`**: Available calculators listing

#### `/admin/content/`
- **`index.php`**: Content management system
- **`media.php`**: Media file management
- **`menus.php`**: Navigation menu editor
- **`pages.php`**: Static page management

#### `/admin/email-manager/`
- **`dashboard.php`**: Email campaign overview
- **`templates.php`**: Email template management
- **`threads.php`**: Email conversation threads
- **`thread-detail.php`**: Individual email thread view
- **`template-form.php`**: Email template creation/editing
- **`settings.php`**: Email configuration settings
- **`error.php`**: Email error handling display

#### `/admin/help/`
- **`index.php`**: Admin help documentation

#### `/admin/logs/`
- **`index.php`**: System log viewer
- **`view.php`**: Detailed log entry inspection

#### `/admin/modules/`
- **`index.php`**: Module management interface

#### `/admin/plugins/`
- **`index.php`**: Plugin management system

#### `/admin/settings/`
- **`index.php`**: General settings management
- **`general.php`**: Basic application settings
- **`email.php`**: Email configuration
- **`security.php`**: Security settings
- **`advanced.php`**: Advanced system options
- **`api.php`**: API configuration
- **`application.php`**: Application-specific settings
- **`users.php`**: User management settings

#### `/admin/setup/`
- **`checklist.php`**: Initial setup checklist

#### `/admin/system/`
- **`status.php`**: System status monitoring

#### `/admin/themes/`
- **`index.php`**: Theme management interface
- **`customize.php`**: Theme customization tools
- **`preview.php`**: Theme preview functionality

#### `/admin/users/`
- **`index.php`**: User listing and management
- **`create.php`**: New user creation form
- **`edit.php`**: User editing interface
- **`bulk.php`**: Bulk user operations
- **`permissions.php`**: User permission management
- **`roles.php`**: Role definition and management

#### `/admin/widgets/`
- **`index.php`**: Widget management interface

## User Views (`/user/`)

User-facing account management interfaces:

### Core User Files
- **`profile.php`**: Comprehensive user profile management with:
  - Personal information editing
  - Professional details
  - Social media links
  - Privacy settings
  - Two-factor authentication management
  - Data export (GDPR compliance)
  - Account deletion options
- **`history.php`**: User calculation history
- **`exports.php`**: Data export management
- **`2fa-setup.php`**: Two-factor authentication setup

### User Modals (`/user/modals/`)
- **`profile-modals.php`**: Modal dialogs for profile management including:
  - Notification preferences
  - Privacy settings
  - Password change
  - Account deletion confirmation

## Payment Views (`/payment/`)

Payment processing and subscription management:

### Core Payment Files
- **`checkout.php`**: Payment checkout interface with:
  - Multiple payment method support (PayPal, PayTM, eSewa, Khalti)
  - Country-specific payment options
  - Order summary display
  - Security indicators and trust badges
- **`success.php`**: Payment success confirmation
- **`failed.php`**: Payment failure handling
- **`esewa-form.php`**: eSewa payment integration form

## Calculator Views (`/calculators/`)

Specialized calculator interfaces:

### Core Calculator Files
- **`traditional-units.php`**: Traditional Nepali units converter with:
  - Widget integration support
  - Geolocation detection
  - Comprehensive unit information
  - Interactive conversion interface
  - Fallback calculator for basic functionality

## Error Views (`/errors/`)

Error handling and user-friendly error pages:

- **`404.php`**: Page not found error page
- **`500.php`**: Internal server error page

## Help Views (`/help/`)

Help system and documentation:

### Core Help Files
- **`index.php`**: Main help center with:
  - Search functionality
  - Categorized help articles
  - Popular articles section
  - Contact support options
- **`index_complex.php`**: Advanced help interface
- **`index_simple.php`**: Simplified help view
- **`search.php`**: Help search results
- **`article.php`**: Individual help article display

## Share Views (`/share/`)

Public sharing functionality:

- **`public-view.php`**: Public calculation sharing with:
  - Social media integration
  - Embed functionality
  - Comment system
  - View tracking
  - Open Graph and Twitter Card meta tags

## Developer Views (`/developer/`)

Developer tools and documentation:

- **`index.php`**: Developer documentation index
- **`playground.php`**: Code testing and experimentation area

## View Rendering System

### Controller Integration
Views are rendered by Controllers using the View class:

```php
// In a Controller:
$this->view->render('admin/dashboard', $data);
```

### Data Passing
Controllers pass data to views through the `$data` array, which becomes available as local variables in the view template.

### Theme Integration
The Views system integrates with the theme system:

```php
// Theme detection logic in main.php
$activeTheme = $_SESSION['active_theme'] ?? 'default';
$themeHeader = BASE_PATH . '/themes/' . $activeTheme . '/views/partials/header.php';
```

### Asset Management
Views use helper functions for asset URLs:
- `asset_url('css/app.css')` → `/assets/css/app.css`
- `app_base_url('/admin')` → Full URL to admin section

## Security Considerations

### CSRF Protection
Admin layout includes CSRF tokens:
```php
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
```

### Input Sanitization
Views consistently use `htmlspecialchars()` for output escaping:
```php
<?= htmlspecialchars($user['first_name'] ?? ''); ?>
```

### Authentication Checks
Navigation and admin partials include authentication state checks:
```php
$auth = new \App\Core\Auth();
$isLoggedIn = $auth->check();
$isAdmin = $isLoggedIn && $auth->isAdmin();
```

## Relationship with Theme System

### app/Views vs themes/default/views

- **app/Views/** = MVC Controller views (framework)
  - Processed by Controllers
  - Include business logic
  - Handle form submissions
  - Manage authentication states

- **themes/default/views/** = Theme direct-access pages (public-facing)
  - Static pages accessible directly
  - Theme-specific styling
  - Public content pages
  - Marketing and informational content

### Integration Points
The two systems integrate through:
1. Layout inheritance (app/Views can include theme partials)
2. Asset sharing (both use the same CSS/JS assets)
3. Session data (authentication state shared between systems)

## Best Practices for View Development

1. **Separation of Concerns**: Keep business logic in Controllers, presentation in Views
2. **Security**: Always escape user input with `htmlspecialchars()`
3. **Consistency**: Use layout templates for consistent structure
4. **Modularity**: Use partials for reusable components
5. **Responsiveness**: Ensure mobile-friendly design
6. **Accessibility**: Include proper ARIA labels and semantic HTML
7. **Performance**: Minimize inline CSS/JS, use external files
8. **Internationalization**: Use language strings for user-facing text

## JavaScript Integration

Views include JavaScript for:
- Form validation and submission
- AJAX requests for dynamic content
- Interactive UI components
- Chart rendering (Chart.js)
- Modal management
- Real-time updates

## CSS Framework Usage

- **Bootstrap 5**: Admin panel and form components
- **Tailwind CSS**: Main application styling
- **Font Awesome**: Icon system
- **Custom CSS**: Application-specific styling

This Views directory provides a comprehensive, secure, and maintainable foundation for the Bishwo Calculator application's user interface, supporting both MVC patterns and theme-based customization.
