# 🚀 Image System - Quick Reference Guide

## ⚡ 30-Second Setup

### 1. Initialize Storage (One-Time)
```
http://localhost/Bishwo_Calculator/image_system_diagnostic?init=1
```

### 2. Check System Status
```
http://localhost/Bishwo_Calculator/image_system_diagnostic
```

### 3. Upload Images
```
http://localhost/Bishwo_Calculator/admin/logo-settings
```

---

## 📁 Where Files Are Stored

| Image Type | Upload Location | URL Path |
|------------|----------------|----------|
| **Logo** | `storage/uploads/admin/logos/` | `/storage/uploads/admin/logos/logo_*.png` |
| **Favicon** | `public/assets/icons/` | `/assets/icons/favicon_*.ico` |
| **Banner** | `storage/uploads/admin/banners/` | `/storage/uploads/admin/banners/banner_*.jpg` |
| **Profile** | `storage/uploads/users/{id}/` | `/storage/uploads/users/{id}/profile_*.jpg` |

---

## 💻 Quick Code Examples

### Upload Logo
```php
use App\Services\ImageUploadService;

$result = ImageUploadService::uploadAdminImage($_FILES['logo'], 'logo');

if ($result['success']) {
    echo "Uploaded: " . $result['url'];
}
```

### Get Logo (with fallback)
```php
use App\Services\ImageRetrievalService;

// Automatically uses uploaded or falls back to default
$logoUrl = ImageRetrievalService::getLogo();
```

### Get User Profile Image
```php
use App\Services\ImageRetrievalService;

$profileUrl = ImageRetrievalService::getUserProfile($userId);
```

### Get All Admin Images
```php
use App\Services\ImageRetrievalService;

$images = ImageRetrievalService::getAllAdminImages();
// Returns: ['logo' => '...', 'favicon' => '...', 'banner' => '...']
```

### Clear Cache
```php
use App\Services\ImageRetrievalService;

// Clear specific type
ImageRetrievalService::clearCache('logo');

// Clear all
ImageRetrievalService::clearCache();
```

---

## 🎯 Fallback System

Images automatically fall back in this order:

```
1. Custom Uploaded Image
   ↓ (if not found)
2. site_meta.json Configuration
   ↓ (if not found)
3. Theme Default Image
```

**Example:**
```
Logo Search Order:
1. storage/uploads/admin/logos/logo_*.png
2. app/db/site_meta.json → "logo": "/path/to/logo.png"
3. themes/default/assets/images/logo.png
```

---

## 📏 Image Limits

| Type | Max Size | Max Dimensions | Formats |
|------|----------|----------------|---------|
| **Logo** | 5MB | 500x200px | PNG, JPG, SVG, WEBP |
| **Favicon** | 1MB | 512x512px | ICO, PNG, JPG |
| **Banner** | 10MB | 2560x800px | PNG, JPG, WEBP |
| **Profile** | 2MB | 400x400px | PNG, JPG, WEBP |

---

## 🔧 Common Tasks

### Replace Logo
1. Go to `/admin/logo-settings`
2. Upload new logo file
3. Old logo automatically deleted

### Replace Favicon
1. Go to `/admin/logo-settings`
2. Upload new favicon (.ico or .png)
3. Hard refresh browser: `Ctrl + F5`

### Change Default Images
Edit: `app/db/site_meta.json`
```json
{
    "logo": "/assets/icons/icon-192.png",
    "favicon": "/assets/icons/favicon.ico"
}
```

---

## 🐛 Troubleshooting

### Logo Not Showing?
```bash
# Check diagnostic
http://localhost/Bishwo_Calculator/image_system_diagnostic

# Clear cache
php -r "require 'app/Services/ImageRetrievalService.php'; \App\Services\ImageRetrievalService::clearCache('logo');"
```

### Favicon Not Appearing?
```
1. Hard refresh: Ctrl + F5
2. Clear browser cache
3. Check: public/assets/icons/favicon.ico exists
```

### Upload Failing?
```
1. Check PHP limits: upload_max_filesize, post_max_size
2. Verify GD extension: php -m | grep gd
3. Check permissions: chmod 755 storage/uploads/
```

### Directory Errors?
```
# Re-initialize storage
http://localhost/Bishwo_Calculator/image_system_diagnostic?init=1
```

---

## 🔒 Security Features

✅ **Automatic Protection:**
- `.htaccess` prevents PHP execution in uploads
- `index.php` blocks directory listing
- MIME type validation on uploads
- Secure filename generation
- File size limits enforced

✅ **User Isolation:**
- Each user has own directory: `storage/uploads/users/{id}/`
- No cross-user file access

✅ **Path Security:**
- No directory traversal allowed
- Files only deletable from authorized paths

---

## 📊 Services Overview

| Service | Purpose |
|---------|---------|
| **ImageUploadService** | Handles uploads, validation, optimization |
| **ImageRetrievalService** | Gets images with smart fallback |
| **ImageManager** | Backward-compatible facade |

---

## 🎨 Frontend Usage

### In Views/Templates
```php
<!-- Logo -->
<img src="<?php echo ImageRetrievalService::getLogo(); ?>" alt="Logo">

<!-- Favicon -->
<link rel="icon" href="<?php echo ImageRetrievalService::getFavicon(); ?>">

<!-- User Profile -->
<img src="<?php echo ImageRetrievalService::getUserProfile($userId); ?>">
```

### With Full URL
```php
$logoUrl = ImageRetrievalService::getLogo();
$fullUrl = ImageRetrievalService::getFullUrl($logoUrl);
// Result: http://localhost/Bishwo_Calculator/storage/uploads/admin/logos/logo_*.png
```

---

## 📱 PWA Support

Manifest automatically includes:
```json
{
  "icons": [
    {
      "src": "/assets/icons/icon-192.png",
      "sizes": "192x192"
    },
    {
      "src": "/assets/icons/icon-512.png",
      "sizes": "512x512"
    }
  ]
}
```

---

## ⚙️ Configuration Files

### Upload Settings
**File:** `app/Services/ImageUploadService.php`
```php
// Modify these constants
const MAX_FILE_SIZE = 5242880; // 5MB
const MAX_PROFILE_SIZE = 2097152; // 2MB
```

### Default Images
**File:** `app/Services/ImageRetrievalService.php`
```php
private const DEFAULT_IMAGES = [
    'logo' => '/themes/default/assets/images/logo.png',
    'favicon' => '/assets/icons/favicon.ico',
    'banner' => '/themes/default/assets/images/banner.jpg',
    'profile' => '/themes/default/assets/images/profile.png',
];
```

### Site Meta
**File:** `app/db/site_meta.json`
```json
{
    "logo": "/assets/icons/icon-192.png",
    "favicon": "/assets/icons/favicon.ico",
    "logo_text": "EngiCal Pro"
}
```

---

## 🎯 Quick Commands

```bash
# Check storage structure
ls -la storage/uploads/

# Verify permissions
ls -la storage/uploads/admin/logos/

# Find uploaded logos
ls storage/uploads/admin/logos/logo_*

# Test PHP GD extension
php -r "echo extension_loaded('gd') ? 'GD OK' : 'GD Missing';"

# Check upload limits
php -r "echo ini_get('upload_max_filesize');"
```

---

## 📞 Quick Links

| Resource | URL |
|----------|-----|
| **System Diagnostic** | `/image_system_diagnostic` |
| **Upload Interface** | `/admin/logo-settings` |
| **Homepage** | `/` |
| **Favicon Check** | `/check_logo_favicon` |

---

## ✅ Checklist

Before going live:

- [ ] Run system diagnostic - all green?
- [ ] Upload test logo - displays correctly?
- [ ] Upload test favicon - shows in browser tab?
- [ ] Check mobile display
- [ ] Verify PWA icons work
- [ ] Test user profile upload
- [ ] Check file permissions (755/644)
- [ ] Verify .htaccess protection
- [ ] Test fallback to defaults
- [ ] Clear browser cache and retest

---

## 💡 Pro Tips

1. **Favicon Cache:** Browsers heavily cache favicons - always hard refresh!
2. **Image Quality:** Use PNG with transparency for logos
3. **File Size:** Compress images before upload for better performance
4. **Testing:** Test on multiple browsers and devices
5. **Backup:** Keep original high-res images before uploading

---

**Status:** ✅ All Systems Ready

**Need Help?** Check `/image_system_diagnostic` for detailed status

**Documentation:** See `IMAGE_SYSTEM_COMPLETE.md` for full details