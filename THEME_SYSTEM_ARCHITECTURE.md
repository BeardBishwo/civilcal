# Bishwo Calculator Theme System Architecture 2025/11/27

## Table of Contents
1. [Overview](#overview)
2. [System Components](#system-components)
3. [MVC Architecture](#mvc-architecture)
4. [Theme Lifecycle](#theme-lifecycle)
5. [Data Flow](#data-flow)
6. [Storage Mechanisms](#storage-mechanisms)
7. [Theme Validation](#theme-validation)
8. [Premium Theme Handling](#premium-theme-handling)
9. [Security Considerations](#security-considerations)
10. [Performance Optimization](#performance-optimization)

## Overview

The Bishwo Calculator theme system follows a Model-View-Controller (MVC) architectural pattern with a dual storage approach combining database records and filesystem assets. This design ensures flexibility, scalability, and proper separation of concerns.

## System Components

### 1. Models
- **ThemeModel**: Handles database operations for theme records
- **ThemeManager**: Business logic layer for theme management

### 2. Views
- **Admin Theme Views**: Templates for theme management interface
- **Frontend Theme Views**: Templates for displaying content with themes

### 3. Controllers
- **ThemeController**: Handles HTTP requests for theme management
- **ViewController**: Renders themed views for frontend

### 4. Services
- **ThemeService**: Core theme processing and validation
- **AssetService**: Manages theme asset loading and caching

## MVC Architecture

### Model Layer
```php
// ThemeModel responsibilities:
- CRUD operations on themes table
- Theme search and filtering
- Status management (active/inactive/deleted)
- Statistics generation
```

### View Layer
```php
// Theme views responsibilities:
- Display theme management interface
- Render theme previews
- Show theme details and metadata
- Provide theme action controls
```

### Controller Layer
```php
// ThemeController responsibilities:
- Handle HTTP requests (GET/POST)
- Validate user permissions
- Coordinate between models and views
- Return JSON responses for AJAX
- Manage redirects and error handling
```

## Theme Lifecycle

### 1. Theme Creation
1. **Upload**: User uploads theme ZIP file
2. **Validation**: System validates theme structure and metadata
3. **Extraction**: Theme files extracted to appropriate directories
4. **Database Record**: Theme metadata stored in database
5. **Activation**: Theme becomes available for activation

### 2. Theme Activation
1. **Request**: User clicks "Activate" for a theme
2. **Validation**: System checks theme validity
3. **Database Update**: Theme status set to "active"
4. **Cache Clear**: Clear theme-related caches
5. **Session Update**: Update user session with active theme

### 3. Theme Usage
1. **Request Processing**: Each page request checks for active theme
2. **Asset Loading**: Theme CSS/JS assets loaded
3. **Template Rendering**: Theme templates used for view rendering
4. **Fallback**: Default theme used if theme files missing

### 4. Theme Deletion
1. **Soft Delete**: Theme status set to "deleted" (retained in DB)
2. **Hard Delete**: Complete removal from database and filesystem
3. **Asset Cleanup**: Removal of theme files and directories

## Data Flow

### Theme Upload Process
```
User -> ThemeController -> ThemeService -> ThemeModel
                                      -> Filesystem
                                      -> Validation Service
                                      <- Success/Failure
                         <- Response
User <- JSON Response
```

### Theme Activation Process
```
User -> ThemeController -> ThemeService -> ThemeModel
                                      <- Theme Data
                         <- Validation Result
User <- Redirect/Response
```

### Theme Rendering Process
```
Request -> Router -> Controller -> View Service
                              -> ThemeManager
                              <- Active Theme
                   <- Themed View
Client <- HTML Response
```

## Storage Mechanisms

### Database Storage
**Table: themes**
- `id`: Primary key
- `name`: Theme identifier (directory name)
- `display_name`: Human-readable name
- `version`: Theme version
- `author`: Theme creator
- `description`: Theme description
- `status`: active|inactive|deleted
- `is_premium`: Boolean flag
- `config_json`: JSON configuration
- `file_size`: Size in bytes
- `checksum`: Integrity verification
- `created_at`: Timestamp
- `updated_at`: Timestamp

### Filesystem Storage
```
themes/
├── {theme_name}/           # Theme directory
│   ├── views/              # Theme view templates
│   │   ├── layouts/        # Layout templates
│   │   └── partials/       # Partial templates
│   ├── assets/             # Theme-specific assets
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── theme.json          # Theme configuration
```

### Premium Theme Assets
```
public/assets/themes/
└── {theme_name}/           # Premium theme assets
    ├── css/
    └── js/
```

## Theme Validation

### Database Validation
- Theme name uniqueness
- Required metadata fields
- Status value validation
- Premium flag consistency

### Filesystem Validation
- Directory existence
- Required files (theme.json, layouts, assets)
- File permission checks
- Size limitations

### Content Validation
- JSON schema validation for theme.json
- Asset file integrity
- Template syntax validation
- Security scanning

## Premium Theme Handling

### Identification
Premium themes are identified by:
1. `is_premium = 1` in database
2. Presence of assets in `public/assets/themes/{theme_name}/`

### Advantages
- Separates premium assets from core theme files
- Allows distribution of premium assets separately
- Enables marketplace integration

### Validation Process
```php
function isValidTheme($theme) {
    // Check database record
    if (!$theme) return false;
    
    // Check filesystem presence
    $themePath = THEMES_PATH . $theme['name'];
    $assetPath = PUBLIC_ASSETS_PATH . $theme['name'];
    
    // Valid if either theme directory exists OR premium assets exist
    return is_dir($themePath) || (is_dir($assetPath) && $theme['is_premium']);
}
```

## Security Considerations

### File Upload Security
- ZIP file validation
- Path traversal prevention
- File type restrictions
- Size limitations
- Malware scanning

### Database Security
- Prepared statements
- Input validation
- Permission checks
- Audit logging

### Asset Security
- File permission controls
- Access restriction
- Cache busting
- CDN integration

## Performance Optimization

### Caching Strategy
- Theme metadata caching
- Asset path caching
- Compiled template caching
- Database query caching

### Lazy Loading
- Assets loaded only when needed
- Templates compiled on first use
- Database connections pooled

### Asset Optimization
- CSS/JS minification
- Image compression
- CDN delivery
- Browser caching headers

## Implementation Guidelines

### Adding New Themes
1. Create theme directory structure
2. Add theme.json configuration
3. Insert database record
4. Validate theme files
5. Test theme functionality

### Theme Updates
1. Version comparison
2. Backup existing files
3. Apply updates
4. Validate updated files
5. Update database record

### Theme Removal
1. Soft delete database record
2. Archive theme files
3. Update user associations
4. Clear caches
5. Optional: Hard delete

This architecture ensures a robust, scalable, and maintainable theme system that follows proper MVC principles without hardcoding any values.