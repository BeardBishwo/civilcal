# Complete Theme Default Structure

## 📁 Root Theme Files
- **`theme.json`** - Theme configuration, metadata, features, colors, gradients
- **`helpers.php`** - Theme helper functions for MVC integration

## 📁 Assets Structure

### 🎨 CSS Files (`assets/css/`)
- **`theme.css`** - Main theme styles (comprehensive premium design system)
- **`premium.css`** - Premium-specific enhanced visual effects
- **`header.css`** - Header-specific styling
- **`footer.css`** - Footer-specific styling  
- **`home.css`** - Homepage-specific styling
- **`back-to-top.css`** - Back to top button styling
- **`responsive.css`** - Mobile responsive styles
- **`site.css`** - General site-wide styles

### 🔧 Module-Specific CSS (`assets/css/`)
- **`civil.css`** - Civil engineering calculator styling
- **`electrical.css`** - Electrical engineering styling
- **`plumbing.css`** - Plumbing calculator styling
- **`hvac.css`** - HVAC calculator styling
- **`fire.css`** - Fire engineering styling
- **`structural.css`** - Structural engineering styling
- **`estimation.css`** - Cost estimation styling
- **`management.css`** - Project management styling
- **`mep.css`** - MEP (Mechanical, Electrical, Plumbing) styling

### 🖼️ Images (`assets/images/`)
- **`applogo.png`** - Application logo
- **`banner.jpg`** - Hero banner image
- **`favicon.png`** - Site favicon
- **`profile.png`** - Default profile picture

### ⚡ JavaScript Files (`assets/js/`)
- **`main.js`** - Basic theme functionality (theme toggle, mobile nav)
- **`header.js`** - Header-specific interactions
- **`home.js`** - Homepage-specific functionality
- **`back-to-top.js`** - Back to top button functionality
- **`theme.js`** - Theme switching functionality
- **`auth.js`** - Authentication forms and validation

## 🏗️ View Structure

### 🏠 View Files (`views/`)
- **`index.php`** - Main homepage with professional engineering focus
- **`home/index.php`** - Alternative homepage with 3D animated calculator tools

### 📐 Layouts (`views/layouts/`)
- **`main.php`** - Main application layout template
- **`admin.php`** - Admin panel layout template
- **`auth.php`** - Authentication layout with dual-panel design
- **`login.php`** - **DEPRECATED** - Redirects to modular auth system

### 🔐 Auth Pages (`views/pages/auth/`)
- **`login.php`** - Login form content (modular)
- **`register.php`** - Registration form content
- **`reset.php`** - Password reset form content
- **`verify.php`** - Email verification content
- **`logout.php`** - Logout functionality
- **`403.php`** - 403 Forbidden page
- **`404.php`** - 404 Not Found page  
- **`500.php`** - 500 Server Error page

### 🔧 Partials (`views/partials/`)
- **`header.php`** - Site header with navigation
- **`footer.php`** - Site footer with links
- **`index.php`** - Index partial
- **`site.php`** - General site partial
- **`civil.php`** - Civil engineering partial
- **`electrical.php`** - Electrical partial
- **`plumbing.php`** - Plumbing partial
- **`hvac.php`** - HVAC partial
- **`fire.php`** - Fire engineering partial
- **`structural.php`** - Structural partial
- **`estimation.php`** - Estimation partial
- **`management.php`** - Management partial
- **`mep.php`** - MEP partial

## 🎯 File Purpose Summary

### Core Configuration
- **`theme.json`** - Defines theme features, colors, layouts, and asset loading
- **`helpers.php`** - Provides MVC integration functions

### Layout System
- **Layouts** - Page wrappers (main, auth, admin)
- **Partials** - Reusable components (header, footer, modules)
- **Content Pages** - Individual page content

### Asset Organization
- **Modular CSS** - Each calculator module has dedicated styles
- **Modular JS** - Functionality separated by feature area
- **Premium Design** - Glassmorphism, gradients, animations

### MVC Integration
- **Layouts** = Controllers (manage page structure)
- **Partials** = Components (reusable view elements)  
- **Content Pages** = Views (specific page content)

## 🔄 File Flow

1. **Request** → **Controller** → **Layout** (main/auth/admin)
2. **Layout** includes **Partials** (header, footer)
3. **Content** loaded into layout → **Final Page**
4. **Assets** loaded via **theme.json** configuration
