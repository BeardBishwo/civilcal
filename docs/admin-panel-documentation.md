# Admin Panel Documentation

## Table of Contents
1. [Overview](#overview)
2. [Architecture](#architecture)
3. [File Structure](#file-structure)
4. [View Templates](#view-templates)
5. [Layout System](#layout-system)
6. [Chart.js Integration](#chartjs-integration)
7. [CSS Framework](#css-framework)
8. [JavaScript Components](#javascript-components)
9. [Routing System](#routing-system)
10. [Maintenance Procedures](#maintenance-procedures)
11. [Development Guidelines](#development-guidelines)
12. [Testing Procedures](#testing-procedures)

## Overview

The admin panel is a comprehensive management interface built with modern web technologies. It provides a responsive, feature-rich environment for managing all aspects of the Bishwo Calculator application.

### Key Features
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices
- **Interactive Charts**: Real-time data visualization using Chart.js
- **Modular Architecture**: Component-based design for easy maintenance
- **Role-Based Access**: User permission system
- **Real-Time Updates**: AJAX-powered interface
- **Comprehensive Analytics**: Dashboard with KPIs and metrics
- **System Management**: Complete system administration tools

## Architecture

### MVC Pattern
The admin panel follows the Model-View-Controller (MVC) architectural pattern:

- **Models**: Handle data logic and database interactions
- **Views**: Render the user interface
- **Controllers**: Process user requests and coordinate between models and views

### Component Structure
```
Admin Panel
├── Layout Components
│   ├── Header (navigation, search, user menu)
│   ├── Sidebar (main navigation)
│   └── Footer (system info)
├── View Templates
│   ├── Dashboard views
│   ├── Management views
│   ├── Settings views
│   └── System views
├── Assets
│   ├── CSS (styling, responsive design)
│   ├── JavaScript (interactivity, charts)
│   └── Images (icons, logos)
└── API Endpoints
    ├── Dashboard data
    ├── Settings management
    └── System operations
```

## File Structure

### Directory Organization
```
themes/admin/
├── views/                    # View templates
│   ├── dashboard.php         # Main dashboard
│   ├── dashboard_complex.php   # Complex dashboard
│   ├── configured-dashboard.php # Configurable dashboard
│   ├── performance-dashboard.php # Performance metrics
│   ├── system-status.php      # System monitoring
│   ├── widget-management.php  # Widget administration
│   ├── menu-customization.php # Menu builder
│   └── settings/            # Settings views
│       ├── backup.php         # Backup configuration
│       └── advanced.php       # Advanced settings
├── layouts/                  # Layout components
│   ├── admin.php            # Main admin layout
│   ├── header.php           # Header component
│   └── sidebar.php         # Sidebar navigation
├── assets/                  # Static assets
│   ├── css/
│   │   └── admin.css       # Main stylesheet
│   ├── js/
│   │   └── admin.js       # Main JavaScript file
│   └── images/
│       └── admin-logo.png  # Admin logo
└── partials/               # Reusable components
    ├── alerts.php          # Alert messages
    ├── modals.php          # Modal dialogs
    └── forms.php          # Form components
```

### File Naming Conventions
- **Views**: `kebab-case.php` (e.g., `dashboard-complex.php`)
- **Layouts**: `kebab-case.php` (e.g., `admin-layout.php`)
- **CSS Classes**: `kebab-case` (e.g., `admin-card`)
- **JavaScript Functions**: `camelCase` (e.g., `initializeCharts`)
- **Variables**: `camelCase` (e.g., `currentUser`)

## View Templates

### Standard View Structure
Each view template should follow this structure:

```php
<?php
/**
 * View Documentation
 * @package BishwoCalculator\Admin
 */

// Security check
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

// Get passed variables
$title = $title ?? 'Admin Panel';
$breadcrumbs = $breadcrumbs ?? [];
?>

<!-- Content Header -->
<div class="content-header">
    <h1 class="content-title"><?php echo htmlspecialchars($title); ?></h1>
    <?php if (!empty($breadcrumbs)): ?>
        <nav class="breadcrumb">
            <?php include 'partials/breadcrumb.php'; ?>
        </nav>
    <?php endif; ?>
</div>

<!-- Main Content -->
<div class="content-body">
    <!-- Page content goes here -->
</div>
```

### Available View Templates

#### Dashboard Views
- **`dashboard.php`**: Main admin dashboard with KPIs and quick stats
- **`dashboard_complex.php`**: Advanced analytics dashboard with multiple charts
- **`configured-dashboard.php`**: Customizable dashboard with widget management
- **`performance-dashboard.php`**: System performance monitoring dashboard

#### Management Views
- **`widget-management.php`**: Complete widget CRUD operations
- **`menu-customization.php`**: Drag-and-drop menu builder
- **`system-status.php`**: System health monitoring

#### Settings Views
- **`settings/backup.php`**: Backup configuration and management
- **`settings/advanced.php`**: Advanced system settings

## Layout System

### Main Layout (`layouts/admin.php`)
The main layout file provides:
- HTML5 document structure
- Chart.js CDN integration
- CSS and JavaScript includes
- Global notification system
- Responsive design foundation

### Header Component (`layouts/header.php`)
Features:
- Breadcrumb navigation
- Global search functionality
- Notification system
- User menu with dropdown
- System status indicators
- Mobile-responsive design

### Sidebar Component (`layouts/sidebar.php`)
Features:
- Hierarchical menu structure
- Active state management
- Collapsible design
- Mobile overlay support
- User profile section

### Layout Usage
```php
<?php include 'layouts/header.php'; ?>
<?php include 'layouts/sidebar.php'; ?>

<main class="admin-main">
    <?php include $viewFile; ?>
</main>

<?php include 'layouts/footer.php'; ?>
```

## Chart.js Integration

### Chart Loading System
The admin panel includes a robust Chart.js integration:

1. **Automatic Detection**: Checks if Chart.js is loaded
2. **Dynamic Loading**: Loads Chart.js from CDN if not available
3. **Error Handling**: Graceful fallbacks for chart errors
4. **Responsive Design**: Charts adapt to screen size

### Available Charts
- **Line Charts**: User growth, performance metrics
- **Bar Charts**: Revenue, module usage
- **Doughnut Charts**: Calculator usage distribution
- **Radar Charts**: Resource usage comparison
- **Scatter Charts**: Activity heatmaps

### Chart Initialization
```javascript
// Charts are automatically initialized on page load
// Individual charts can be initialized manually:

AdminApp.initCharts();

// Or specific charts:
AdminApp.initializeServerLoadChart();
AdminApp.initializeUserGrowthChart();
```

### Chart Styling
Charts use consistent styling:
- **Colors**: Admin theme color palette
- **Typography**: Inter font family
- **Responsive**: Automatic sizing
- **Interactive**: Hover states and tooltips

## CSS Framework

### Architecture
The CSS follows a modular architecture:
- **Base Styles**: Typography, colors, layout
- **Components**: Cards, forms, buttons, tables
- **Utilities**: Spacing, positioning, responsive
- **Themes**: Light/dark mode support

### CSS Classes

#### Layout Classes
- `.admin-container`: Main container
- `.admin-sidebar`: Sidebar navigation
- `.admin-main`: Main content area
- `.admin-header`: Top navigation

#### Component Classes
- `.admin-card`: Content card
- `.admin-card-header`: Card header
- `.admin-card-body`: Card content
- `.admin-btn`: Button styling
- `.admin-form`: Form styling
- `.admin-table`: Data table

#### Utility Classes
- `.text-center`: Center alignment
- `.mb-3`: Margin bottom
- `.p-4`: Padding
- `.d-flex`: Flexbox display
- `.w-100`: Full width

### Responsive Breakpoints
```css
/* Mobile */
@media (max-width: 768px) { }

/* Tablet */
@media (min-width: 769px) and (max-width: 1024px) { }

/* Desktop */
@media (min-width: 1025px) { }
```

## JavaScript Components

### Main App Object (`AdminApp`)
Central JavaScript object managing all admin functionality:

#### Core Methods
- `init()`: Initializes all components
- `initCharts()`: Sets up Chart.js integration
- `initSidebar()`: Sidebar functionality
- `initUserMenu()`: User dropdown menu
- `initNotifications()`: Notification system
- `initAjaxForms()`: AJAX form handling

#### Chart Methods
- `initializeServerLoadChart()`: Server performance chart
- `initializeMemoryUsageChart()`: Memory usage tracking
- `initializeResponseTimeChart()`: Response time monitoring
- `initializeDbQueriesChart()`: Database query analysis
- `initializeDashboardCharts()`: Dashboard charts
- `initializeSystemPerformanceChart()`: System metrics
- `initializeRevenueChart()`: Revenue tracking
- `initializeActivityHeatmap()`: User activity visualization
- `initializeResourceUsageChart()`: Resource consumption

#### Utility Methods
- `showNotification()`: Display toast notifications
- `showLoading()`: Loading overlay management
- `animateValue()`: Number animation
- `updateDashboardStats()`: Dashboard data updates

### Event Handling
```javascript
// Form submissions
document.addEventListener('submit', handleFormSubmit);

// Chart interactions
chart.addEventListener('click', handleChartClick);

// Sidebar toggle
sidebarToggle.addEventListener('click', toggleSidebar);
```

## Routing System

### Route Structure
Admin routes follow the pattern: `/admin/{controller}/{action}/{param}`

#### Example Routes
- `/admin` → Dashboard controller, index action
- `/admin/users` → Users controller, index action
- `/admin/users/create` → Users controller, create action
- `/admin/settings/backup` → Settings controller, backup action

### Controller Mapping
```php
// Route to controller mapping
$routes = [
    '/admin' => 'AdminController@index',
    '/admin/users' => 'UserController@index',
    '/admin/settings' => 'SettingsController@index',
    '/admin/modules' => 'ModuleController@index'
];
```

### View Resolution
Views are resolved in this order:
1. `themes/admin/views/{controller}/{action}.php`
2. `themes/admin/views/{controller}.php`
3. `app/Views/admin/{controller}/{action}.php` (fallback)

## Maintenance Procedures

### Regular Maintenance Tasks

#### Daily
- Check system logs for errors
- Monitor disk space usage
- Review backup completion
- Check for security alerts

#### Weekly
- Update system statistics
- Clean temporary files
- Review user activity logs
- Check for module updates

#### Monthly
- Database optimization
- Security audit
- Performance review
- Backup verification

### Update Procedures

#### Adding New Views
1. Create view file in `themes/admin/views/`
2. Follow standard view structure
3. Include necessary CSS/JS
4. Update routing configuration
5. Test functionality

#### Adding New Charts
1. Add chart container to view
2. Create initialization method in `admin.js`
3. Add chart to `initCharts()` method
4. Include responsive styling
5. Test with various data sets

#### Updating CSS
1. Follow existing naming conventions
2. Use modular approach
3. Test responsive behavior
4. Check browser compatibility
5. Minify for production

### Backup Procedures

#### File Backups
```bash
# Backup admin theme files
tar -czf admin-theme-backup.tar.gz themes/admin/

# Backup configuration
cp .env .env.backup
```

#### Database Backups
```bash
# Full database backup
mysqldump -u root -p bishwo_calculator > admin-backup.sql

# Structure only
mysqldump -u root -p --no-data bishwo_calculator > structure.sql
```

## Development Guidelines

### Code Standards

#### PHP
- Follow PSR-12 coding standards
- Use strict typing where possible
- Include proper documentation
- Implement error handling
- Use prepared statements for database queries

#### JavaScript
- Use ES6+ features
- Implement proper error handling
- Use async/await for asynchronous operations
- Include JSDoc comments
- Follow consistent naming conventions

#### CSS
- Use BEM methodology for class names
- Implement mobile-first responsive design
- Use CSS custom properties for theming
- Optimize for performance
- Include vendor prefixes when needed

### Security Considerations

#### Input Validation
```php
// Always validate and sanitize input
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
```

#### CSRF Protection
```php
// Include CSRF token in forms
<input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
```

#### Access Control
```php
// Check user permissions
if (!hasPermission('admin.access')) {
    redirect('/login');
}
```

### Performance Optimization

#### Caching
```php
// Cache expensive operations
$cacheKey = 'admin_stats_' . date('Y-m-d');
$stats = Cache::get($cacheKey);
if (!$stats) {
    $stats = calculateStats();
    Cache::set($cacheKey, $stats, 3600); // 1 hour
}
```

#### Database Optimization
```php
// Use efficient queries
$users = DB::select("
    SELECT id, name, email, created_at 
    FROM users 
    WHERE status = 'active' 
    ORDER BY created_at DESC 
    LIMIT 10
");
```

## Testing Procedures

### Automated Testing

#### Route Testing
Run the automated route testing script:
```bash
php tests/test_admin_routes.php
```

This script tests:
- View file existence
- Layout file availability
- Asset file presence
- Chart.js integration
- Route accessibility

#### Unit Testing
```php
// Example test
class AdminControllerTest extends PHPUnit\Framework\TestCase {
    public function testDashboardAccess() {
        $response = $this->get('/admin');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
```

### Manual Testing

#### Browser Testing Checklist
- [ ] All pages load without errors
- [ ] Charts render correctly
- [ ] Forms submit properly
- [ ] Navigation works on all devices
- [ ] Responsive design functions
- [ ] AJAX operations complete
- [ ] Error messages display
- [ ] Loading states show

#### Device Testing
- **Desktop**: Chrome, Firefox, Safari, Edge
- **Tablet**: iPad, Android tablets
- **Mobile**: iPhone, Android phones
- **Screen Readers**: Accessibility compliance

#### Performance Testing
- Page load times < 3 seconds
- Chart rendering < 2 seconds
- AJAX responses < 1 second
- Memory usage < 100MB
- No JavaScript errors

### Debugging

#### JavaScript Debugging
```javascript
// Enable debug mode
window.ADMIN_DEBUG = true;

// Check chart initialization
console.log('Charts initialized:', AdminApp.charts);

// Monitor AJAX requests
AdminApp.showNotification('Debug message', 'info');
```

#### PHP Debugging
```php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log debugging information
error_log('Admin debug: ' . print_r($data, true));
```

## Conclusion

This admin panel provides a robust, scalable foundation for managing the Bishwo Calculator application. Following these guidelines ensures consistent, maintainable, and secure code that can evolve with the application's needs.

For questions or support, refer to the development team or consult the additional documentation in the `/docs` directory.