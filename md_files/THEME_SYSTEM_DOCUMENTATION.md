# Bishwo Calculator Theme System Documentation

## Table of Contents
1. [How the Theme System Works](#how-the-theme-system-works)
2. [What Was Fixed](#what-was-fixed)
3. [Theme Detection Process](#theme-detection-process)
4. [Theme Management](#theme-management)
5. [File Structure](#file-structure)

## How the Theme System Works

The Bishwo Calculator theme system operates on two layers:

### 1. Database Layer
- Themes are stored in the `themes` database table
- Each theme record contains metadata like name, status, premium status, and configuration
- The active theme is determined by the `status = 'active'` field

### 2. Filesystem Layer
- Theme files are stored in the `themes/` directory
- Each theme has its own directory with assets, views, and configuration
- Premium themes may have assets in `public/assets/themes/`

### Theme Activation Process
1. When a theme is activated through the admin panel:
   - The database record's `status` is set to "active"
   - The `ThemeManager` loads the active theme on next page load
   - Session variables are updated to track the active theme

2. When pages are rendered:
   - The View system checks for theme files in the active theme directory
   - Falls back to default views if theme-specific views don't exist
   - Loads theme-specific CSS/JS assets

## What Was Fixed

### 1. Theme Detection Issue
**Problem**: Themes that didn't exist in the filesystem were still showing in the admin panel

**Solution**: Modified the ThemeManager to filter out themes that don't exist in the filesystem

**Code Changes**:
```php
public function getAllThemes($status = null, $isPremium = null, $limit = null, $offset = 0)
{
    $themes = $this->themeModel->getAll($status, $isPremium, $limit, $offset);
    
    // Filter out themes that don't exist in the filesystem
    $validThemes = [];
    foreach ($themes as $theme) {
        $themePath = $this->themesPath . $theme['name'];
        // Check if theme directory exists or if it's a premium theme with assets
        if (is_dir($themePath) || $this->hasPremiumAssets($theme['name'])) {
            $validThemes[] = $theme;
        }
    }
    
    return $validThemes;
}
```

### 2. Theme Statistics
**Problem**: Theme stats were counting all database records, including non-existent themes

**Solution**: Updated the getThemeStats() method to only count valid themes

**Code Changes**:
```php
public function getThemeStats()
{
    $allThemes = $this->getAllThemes();
    
    $stats = [
        'total' => 0,
        'active' => 0,
        'inactive' => 0,
        'deleted' => 0,
        'premium' => 0,
        'updates' => 0
    ];
    
    foreach ($allThemes as $theme) {
        $stats['total']++;
        
        if ($theme['is_premium']) {
            $stats['premium']++;
        }
        
        switch ($theme['status']) {
            case 'active':
                $stats['active']++;
                break;
            case 'inactive':
                $stats['inactive']++;
                break;
            case 'deleted':
                $stats['deleted']++;
                break;
        }
    }
    
    return $stats;
}
```

### 3. Premium Theme Support
**Problem**: Premium themes that only have assets in `public/assets/themes/` were being filtered out

**Solution**: Added a check for premium theme assets in the validation

**Code Changes**:
```php
private function hasPremiumAssets($themeName)
{
    $premiumAssetPath = BASE_PATH . '/public/assets/themes/' . $themeName;
    return is_dir($premiumAssetPath);
}
```

## Theme Detection Process

### 1. Theme Discovery
Themes are discovered through the following process:
1. Query the `themes` database table for all theme records
2. For each theme, check if the corresponding directory exists in `themes/[theme_name]/`
3. For premium themes, also check if assets exist in `public/assets/themes/[theme_name]/`
4. Only themes that pass these checks are returned to the admin panel

### 2. Theme Validation
Each theme is validated by:
- Checking for the existence of the theme directory
- Verifying theme.json configuration file (if present)
- Ensuring required asset files are available

### 3. Theme Display
In the admin panel:
- Only valid themes are displayed
- Themes show their current status (active/inactive/deleted)
- Premium themes are marked appropriately
- Themes without filesystem presence are automatically filtered out

## Theme Management

### Admin Interface
The theme management interface provides:
- Grid view of all valid themes
- Theme statistics (total, active, inactive, premium counts)
- Search and filter functionality
- Theme actions (activate, deactivate, delete, preview)
- Upload functionality for new themes

### Theme Actions
1. **Activate**: Sets theme status to 'active' in database
2. **Deactivate**: Sets theme status to 'inactive' in database
3. **Delete**: Soft deletes theme (sets status to 'deleted')
4. **Restore**: Restores deleted theme
5. **Hard Delete**: Permanently removes theme from database
6. **Upload**: Installs new theme from ZIP file

### Theme Installation
New themes can be installed by:
1. Uploading a ZIP file containing theme files
2. The system validates the ZIP and extracts it
3. Theme metadata is read from theme.json
4. Database record is created for the new theme
5. Theme becomes available in the admin panel

## File Structure

### Theme Directories
```
themes/
├── admin/                 # Admin theme files
│   ├── views/             # Admin view templates
│   │   ├── layouts/       # Admin layout files
│   │   └── partials/      # Admin partial templates
│   └── assets/            # Admin CSS/JS/Image assets
│
├── default/               # Default frontend theme
│   ├── views/             # Frontend view templates
│   │   ├── layouts/       # Layout files
│   │   ├── partials/      # Partial templates
│   │   └── [module]/      # Module-specific views
│   ├── assets/            # CSS/JS/Image assets
│   └── theme.json         # Theme configuration
```

### Premium Theme Assets
```
public/assets/themes/
├── procalculator/         # Premium theme assets
│   ├── css/               # Theme CSS files
│   └── js/                # Theme JavaScript files
```

### Database Schema
The `themes` table contains:
- `id`: Unique identifier
- `name`: Theme directory name
- `display_name`: Human-readable name
- `version`: Theme version
- `author`: Theme author
- `description`: Theme description
- `status`: active/inactive/deleted
- `is_premium`: Boolean flag
- `price`: Price for premium themes
- `config_json`: JSON configuration
- `file_size`: Size of theme files
- `checksum`: Theme integrity checksum
- `screenshot_path`: Path to theme screenshot
- `settings_json`: Theme settings
- `created_at`: Creation timestamp
- `updated_at`: Last update timestamp

This documentation provides a comprehensive overview of how the theme system works and the improvements that were made to ensure only valid themes are displayed in the admin panel.