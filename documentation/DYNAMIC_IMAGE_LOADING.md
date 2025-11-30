# Dynamic Image Loading System

## Overview

The image management system now uses **dynamic image loading** from the theme folder. No hardcoding of image paths - images are automatically detected and loaded from `themes/default/assets/images/`.

## How It Works

### 1. **Theme Image Loader Service**

`app/Services/ThemeImageLoader.php` - Automatically scans the theme folder and loads images dynamically.

**Key Features:**
- ✅ Scans `themes/default/assets/images/` folder
- ✅ Auto-detects image types by filename
- ✅ No hardcoding of paths
- ✅ Caches results for performance
- ✅ Provides image info (size, dimensions, MIME type)

### 2. **Image Manager Integration**

`app/Services/ImageManager.php` - Uses ThemeImageLoader for default images.

**Flow:**
```
Admin/User Upload
    ↓
ImageManager validates & stores
    ↓
If no custom image → ThemeImageLoader gets default from theme folder
```

### 3. **Helper Functions**

Easy access in views:

```php
// Get default images (auto-detected from theme folder)
get_logo()              // Returns logo URL
get_favicon()           // Returns favicon URL
get_banner()            // Returns banner URL

// Get user images (with fallback to defaults)
get_user_image($userId) // Returns user profile or default

// Direct theme image access
get_theme_image('logo')           // Get specific theme image
get_all_theme_images()            // Get all theme images
has_theme_image('logo')           // Check if image exists
get_theme_image_info('logo')      // Get image details
```

## File Structure

```
themes/default/assets/images/
├── logo.png              ← Auto-detected as 'logo'
├── favicon.png           ← Auto-detected as 'favicon'
├── banner.jpg            ← Auto-detected as 'banner'
└── profile.png           ← Auto-detected as 'profile'

storage/uploads/
├── admin/                ← Custom admin uploads
│   ├── logo_*.jpg
│   ├── favicon_*.png
│   └── banner_*.jpg
└── users/                ← User profile uploads
    ├── 1/profile_*.jpg
    └── 2/profile_*.jpg
```

## Usage Examples

### In Views

```php
<!-- Display logo (auto-detected from theme folder) -->
<img src="<?php echo get_logo(); ?>" alt="Logo" />

<!-- Display favicon -->
<link rel="icon" href="<?php echo get_favicon(); ?>" />

<!-- Display banner -->
<img src="<?php echo get_banner(); ?>" alt="Banner" />

<!-- Display user profile (or default if not uploaded) -->
<img src="<?php echo get_user_image($userId); ?>" alt="Profile" />

<!-- Direct theme image access -->
<img src="<?php echo get_theme_image('logo'); ?>" alt="Logo" />
```

### In Controllers

```php
// Get all available theme images
$images = get_all_theme_images();
// Returns: ['logo' => '/themes/default/assets/images/logo.png', ...]

// Check if image exists
if (has_theme_image('logo')) {
    $url = get_theme_image('logo');
}

// Get image details
$info = get_theme_image_info('logo');
// Returns: [
//     'type' => 'logo',
//     'filename' => 'logo.png',
//     'path' => '/path/to/logo.png',
//     'url' => '/themes/default/assets/images/logo.png',
//     'size' => 12345,
//     'width' => 200,
//     'height' => 50,
//     'mime' => 'image/png'
// ]
```

## Adding New Default Images

Simply add images to `themes/default/assets/images/` folder:

```
themes/default/assets/images/
├── logo.png              ← Automatically detected
├── favicon.png           ← Automatically detected
├── banner.jpg            ← Automatically detected
├── profile.png           ← Automatically detected
└── custom_image.png      ← Automatically detected as 'custom_image'
```

Then use in views:
```php
<img src="<?php echo get_theme_image('custom_image'); ?>" />
```

## How Images Are Detected

### Filename Matching

Images are detected by their filename (without extension):

| Filename | Type | URL |
|----------|------|-----|
| `logo.png` | `logo` | `/themes/default/assets/images/logo.png` |
| `favicon.ico` | `favicon` | `/themes/default/assets/images/favicon.ico` |
| `banner.jpg` | `banner` | `/themes/default/assets/images/banner.jpg` |
| `profile.png` | `profile` | `/themes/default/assets/images/profile.png` |
| `custom.png` | `custom` | `/themes/default/assets/images/custom.png` |

### Supported Formats

- ✅ JPG / JPEG
- ✅ PNG
- ✅ GIF
- ✅ WebP
- ✅ SVG

## Priority Order

When displaying images, the system follows this priority:

1. **Custom Admin Upload** (if exists)
   - Path: `/storage/uploads/admin/logo_*.jpg`
   
2. **Custom User Upload** (if exists, for profiles)
   - Path: `/storage/uploads/users/{userId}/profile_*.jpg`
   
3. **Theme Default** (auto-detected)
   - Path: `/themes/default/assets/images/logo.png`

## Caching

ThemeImageLoader caches image list for performance:

```php
// Cache is automatically populated on first call
$images = ThemeImageLoader::getAvailableImages();

// Clear cache if needed (e.g., after adding new images)
ThemeImageLoader::clearCache();

// Next call will re-scan folder
$images = ThemeImageLoader::getAvailableImages();
```

## API Reference

### ThemeImageLoader Methods

```php
// Get all available images
ThemeImageLoader::getAvailableImages()
// Returns: ['logo' => 'logo.png', 'favicon' => 'favicon.ico', ...]

// Get specific image
ThemeImageLoader::getImage('logo')
// Returns: 'logo.png' or null

// Get image path
ThemeImageLoader::getImagePath('logo')
// Returns: '/full/path/to/logo.png'

// Get image URL
ThemeImageLoader::getImageUrl('logo')
// Returns: '/themes/default/assets/images/logo.png'

// Check if image exists
ThemeImageLoader::hasImage('logo')
// Returns: true or false

// Get all images with URLs
ThemeImageLoader::getAllImagesWithUrls()
// Returns: ['logo' => '/themes/default/assets/images/logo.png', ...]

// Get all images with paths
ThemeImageLoader::getAllImagesWithPaths()
// Returns: ['logo' => '/full/path/to/logo.png', ...]

// Get image info
ThemeImageLoader::getImageInfo('logo')
// Returns: ['type' => 'logo', 'filename' => 'logo.png', 'size' => 12345, ...]

// Get all images info
ThemeImageLoader::getAllImagesInfo()
// Returns: ['logo' => [...], 'favicon' => [...], ...]

// Clear cache
ThemeImageLoader::clearCache()
```

## Benefits

✅ **No Hardcoding** - Paths are dynamically detected
✅ **Easy to Extend** - Add new images by adding files
✅ **Flexible** - Works with any filename pattern
✅ **Performant** - Caches results
✅ **Modular** - Separate concerns
✅ **Maintainable** - Easy to understand and modify
✅ **Fallback Support** - Graceful degradation
✅ **Image Info** - Get dimensions, size, MIME type

## Configuration

Image types and settings are defined in `app/Config/images.php`:

```php
'types' => [
    'logo' => [
        'name' => 'Logo',
        'dimensions' => ['width' => 200, 'height' => 50],
        'max_size' => 5242880, // 5MB
    ],
    // ... more types
],
```

## Troubleshooting

### Images Not Showing

1. Check if images exist in `themes/default/assets/images/`
2. Verify filename matches expected type
3. Check file permissions (should be readable)
4. Clear cache: `ThemeImageLoader::clearCache()`

### Wrong Image Displayed

1. Check priority order (custom uploads override defaults)
2. Verify filename in theme folder
3. Check if custom upload exists in `/storage/uploads/`

### Performance Issues

1. Cache is automatically managed
2. Clear cache only if needed: `ThemeImageLoader::clearCache()`
3. Consider using CDN for high traffic

## Summary

The dynamic image loading system provides:

- ✅ Automatic image detection from theme folder
- ✅ No hardcoded paths
- ✅ Easy extension with new images
- ✅ Fallback to defaults
- ✅ Support for custom uploads
- ✅ Performance caching
- ✅ Comprehensive image information

**Start using it now!**
