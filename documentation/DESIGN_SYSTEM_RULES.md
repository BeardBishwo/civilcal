# Bishwo Calculator Design System Rules

## Overview
This document defines the design system architecture for the Bishwo Calculator application, a PHP MVC-based engineering calculator platform with dynamic theming capabilities.

## Design System Structure

### 1. Token Definitions

**Location**: `themes/default/assets/css/theme.css`

**Format**: CSS Custom Properties (CSS Variables)

```css
:root {
    /* Primary Brand Colors - Enhanced with better contrast */
    --primary: #6366f1;
    --primary-light: #8b5cf6;
    --primary-dark: #4338ca;
    --primary-50: #eef2ff;
    --primary-100: #e0e7ff;
    --primary-500: #6366f1;
    --primary-600: #5b21b6;
    --primary-900: #312e81;
    
    /* Spacing Scale */
    --space-xs: 0.25rem;
    --space-sm: 0.5rem;
    --space-md: 1rem;
    --space-lg: 1.5rem;
    --space-xl: 2rem;
    --space-2xl: 3rem;
    --space-3xl: 4rem;
    
    /* Border Radius Scale */
    --radius-xs: 0.125rem;
    --radius-sm: 0.25rem;
    --radius-md: 0.375rem;
    --radius-lg: 0.5rem;
    --radius-xl: 0.75rem;
    --radius-2xl: 1rem;
    --radius-full: 9999px;
}
```

**Dynamic Token System**: Theme settings are dynamically injected via PHP in `themes/default/views/partials/header.php`:

```php
<?php
try {
    $tm = new \App\Services\ThemeManager();
    $meta = $tm->getThemeMetadata();
    $settings = $meta['settings'] ?? [];
    $primary = $settings['primary'] ?? '#2563eb';
    // ... other dynamic tokens
} catch (\Throwable $e) {
    // Fallback values
}
?>
<style>
:root {
    --primary-color: <?= htmlspecialchars($primary) ?>;
    --secondary-color: <?= htmlspecialchars($secondary) ?>;
    /* ... other dynamic variables */
}
</style>
```

### 2. Component Library

**Architecture**: PHP-based MVC with theme-aware components

**Component Locations**:
- Theme Views: `themes/default/views/`
- Admin Components: `app/Views/admin/`
- Partials: `themes/default/views/partials/`

**Component Structure**:
```php
// themes/default/views/partials/header.php
<header class="header">
    <div class="header-container">
        <a href="/" class="logo">
            <img src="<?= $base_url ?>assets/images/logo.png" alt="Logo">
            Bishwo Calculator
        </a>
        <nav class="nav">
            <!-- Dynamic navigation from CalculatorFactory -->
        </nav>
    </div>
</header>
```

**Component Classes**:
```css
/* Card Component */
.card {
    background: var(--glass-bg);
    backdrop-filter: var(--backdrop-blur) var(--backdrop-saturate);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    box-shadow: var(--glass-shadow);
    transition: var(--transition-normal);
}

/* Button Component */
.btn {
    display: inline-flex;
    align-items: center;
    gap: var(--space-sm);
    padding: var(--space-md) var(--space-lg);
    border-radius: var(--radius-lg);
    font-weight: 600;
    transition: var(--transition-fast);
}
```

### 3. Frameworks & Libraries

**Backend Framework**: Custom PHP MVC
- Router: `app/Core/Router.php`
- Controllers: `app/Controllers/`
- Models: `app/Models/`
- Services: `app/Services/`

**Frontend Libraries**:
- **Bootstrap 5**: Core UI framework
- **Font Awesome 6**: Icon system
- **Inter Font**: Primary typography
- **Custom CSS**: Theme-specific styling

**Build System**: No build system - direct file serving
- Assets served from: `/themes/{theme}/assets/`
- Public assets: `/assets/themes/{theme}/`

### 4. Asset Management

**Asset Structure**:
```
themes/default/assets/
├── css/
│   ├── theme.css          # Main theme styles
│   ├── header.css         # Header-specific styles
│   ├── responsive.css     # Responsive design
│   └── [category].css     # Category-specific styles
├── js/
│   └── main.js           # Theme JavaScript
└── images/
    └── [theme-images]    # Theme-specific images
```

**Asset URL Generation**:
```php
// Via ThemeManager
$assetUrl = $themeManager->themeUrl('assets/css/theme.css');

// Via View helper
$assetUrl = $this->view->asset('css/theme.css');
```

**CDN Assets**:
- Bootstrap 5 CSS/JS from CDN
- Font Awesome from CDN
- Google Fonts (Inter, JetBrains Mono)

### 5. Icon System

**Primary Icons**: Font Awesome 6
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
```

**Usage Pattern**:
```html
<i class="fas fa-calculator"></i>
<i class="bi bi-gear"></i> <!-- Bootstrap Icons for admin -->
```

**Icon Categories**:
- `fas fa-*`: Solid icons
- `far fa-*`: Regular icons  
- `bi bi-*`: Bootstrap icons (admin interface)

### 6. Styling Approach

**Methodology**: CSS Custom Properties + Utility Classes

**Global Styles**: Defined in `theme.css` with CSS variables

**Glass Morphism Design**:
```css
.glass-effect {
    background: var(--glass-bg);
    backdrop-filter: var(--backdrop-blur);
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
}
```

**Responsive Design**:
```css
@media (max-width: 768px) {
    .header-content {
        padding: 0 1rem;
        height: 70px;
    }
    .main-nav {
        display: none;
    }
}
```

**Dark Theme Support**:
```css
body[data-theme="dark"],
body.dark-theme {
    background: var(--bg-dark);
    color: var(--text-light);
}
```

### 7. Project Structure

```
Bishwo_Calculator/
├── app/                          # Application core
│   ├── Controllers/             # MVC Controllers
│   ├── Models/                  # Data models
│   ├── Services/               # Business logic
│   │   └── ThemeManager.php    # Theme management
│   └── Views/                  # Admin views
├── themes/                     # Theme system
│   └── default/               # Default theme
│       ├── views/             # Theme templates
│       │   ├── layouts/       # Layout templates
│       │   ├── partials/      # Reusable components
│       │   └── calculators/   # Calculator pages
│       └── assets/            # Theme assets
├── modules/                   # Calculator modules
│   ├── civil/                # Civil engineering
│   ├── electrical/           # Electrical engineering
│   └── [other-modules]/      # Other categories
└── public/                   # Public web root
```

## Design Integration Guidelines

### 1. Adding New Components

1. **Create component CSS** in `themes/default/assets/css/`
2. **Use design tokens** from CSS variables
3. **Follow naming convention**: `.component-name`
4. **Add responsive variants** with media queries
5. **Include dark theme styles**

### 2. Color System Integration

```css
/* Use semantic color tokens */
.primary-button {
    background: var(--primary-color);
    color: var(--text-inverse);
}

/* Support theme customization */
.custom-element {
    color: var(--text-primary);
    background: var(--bg-primary);
}
```

### 3. Typography Scale

```css
/* Use consistent typography hierarchy */
h1 { font-size: 2.5rem; }
h2 { font-size: 2rem; }
h3 { font-size: 1.75rem; }
h4 { font-size: 1.5rem; }
h5 { font-size: 1.25rem; }
h6 { font-size: 1rem; }
```

### 4. Animation Guidelines

```css
/* Use consistent timing functions */
.animated-element {
    transition: var(--transition-normal);
}

/* Micro-interactions */
.interactive:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}
```

### 5. Admin Theme Integration

Admin components automatically inherit theme settings:

```php
// themes/default/views/layouts/admin.php
$admin_primary = $settings['primary'] ?? '#4f46e5';
?>
<style>
:root {
    --admin-primary: <?= htmlspecialchars($admin_primary) ?>;
}
</style>
```

## Theme Customization API

### Admin Interface
- **Location**: `/admin/themes`
- **Customize Modal**: Color pickers, typography, dark mode
- **API Endpoint**: `POST /admin/themes/{id}/settings`

### Supported Settings
- `primary`: Primary brand color
- `secondary`: Secondary color
- `accent`: Accent color
- `background`: Background color
- `text`: Primary text color
- `text_secondary`: Secondary text color
- `dark_mode_enabled`: Boolean for default dark mode
- `typography_style`: Typography variant

### Implementation
Settings are stored in database and dynamically applied via CSS variables, ensuring real-time theme customization without file modifications.
