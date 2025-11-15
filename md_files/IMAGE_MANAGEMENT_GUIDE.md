# Image Management System - Complete Guide

## Overview

A modular, secure image management system for Bishwo Calculator that handles:
- **Admin images** (logo, favicon, banner)
- **User profile images**
- **Default theme images**
- **Automatic optimization and resizing**
- **Database tracking**
- **Security validation**

---

## Architecture

### Directory Structure

```
storage/
├── uploads/
│   ├── admin/              # Admin uploaded images
│   │   ├── logo_*.jpg
│   │   ├── favicon_*.png
│   │   └── banner_*.jpg
│   └── users/              # User profile images
│       ├── 1/
│       │   └── profile_*.jpg
│       ├── 2/
│       │   └── profile_*.jpg
│       └── ...

themes/default/assets/images/
├── logo.png                # Default logo
├── favicon.png             # Default favicon
├── banner.jpg              # Default banner
└── profile.png             # Default profile image
```

### Components

1. **ImageManager Service** (`app/Services/ImageManager.php`)
   - Handles uploads, validation, optimization
   - Manages file storage
   - Provides image URLs

2. **Image Model** (`app/Models/Image.php`)
   - Database records for uploaded images
   - Tracks image metadata
   - Supports soft deletes

3. **Admin Controller** (`app/Controllers/Admin/ImageController.php`)
   - Admin image upload endpoints
   - Image management interface

4. **Profile Controller** (`app/Controllers/ProfileImageController.php`)
   - User profile image uploads
   - Personal image management

5. **Image Helper** (`app/Helpers/ImageHelper.php`)
   - Easy access functions in views
   - HTML tag generation

---

## Security Features

### File Validation
- ✅ Extension whitelist (jpg, jpeg, png, gif, webp, svg)
- ✅ MIME type verification
- ✅ File size limits
  - Admin images: 5MB
  - Profile images: 2MB
- ✅ Secure filename generation (timestamp + random hash)

### Storage Security
- ✅ Files stored outside web root
- ✅ `.htaccess` prevents PHP execution
- ✅ Proper file permissions (0644)
- ✅ Path traversal prevention

### Database Security
- ✅ Soft deletes (no permanent loss)
- ✅ User ID validation
- ✅ Admin flag tracking
- ✅ Audit trail (timestamps)

---

## Usage

### Admin Image Upload

#### Upload Logo
```php
// In admin controller
$result = ImageManager::uploadAdminImage($_FILES['logo'], ImageManager::TYPE_LOGO);

if ($result['success']) {
    // Save to database
    Image::create([
        'image_type' => ImageManager::TYPE_LOGO,
        'original_name' => $_FILES['logo']['name'],
        'filename' => $result['filename'],
        'path' => $result['path'],
        'file_size' => $result['size'],
        'mime_type' => $_FILES['logo']['type'],
        'is_admin' => true,
    ]);
}
```

#### Upload Favicon
```php
$result = ImageManager::uploadAdminImage($_FILES['favicon'], ImageManager::TYPE_FAVICON);
```

#### Upload Banner
```php
$result = ImageManager::uploadAdminImage($_FILES['banner'], ImageManager::TYPE_BANNER);
```

### User Profile Image Upload

```php
// In profile controller
$result = ImageManager::uploadUserImage($_FILES['profile_image'], $userId);

if ($result['success']) {
    Image::create([
        'user_id' => $userId,
        'image_type' => ImageManager::TYPE_PROFILE,
        'original_name' => $_FILES['profile_image']['name'],
        'filename' => $result['filename'],
        'path' => $result['path'],
        'file_size' => $result['size'],
        'mime_type' => $_FILES['profile_image']['type'],
        'is_admin' => false,
    ]);
}
```

### Get Images in Views

#### Using Helper Functions
```php
// Get logo
<img src="<?php echo get_logo(); ?>" alt="Logo" />

// Get favicon
<link rel="icon" href="<?php echo get_favicon(); ?>" />

// Get banner
<img src="<?php echo get_banner(); ?>" alt="Banner" />

// Get user profile image
<img src="<?php echo get_user_image($userId); ?>" alt="Profile" />

// Get current user profile image
<img src="<?php echo get_current_user_profile_image(); ?>" alt="My Profile" />
```

#### Using Image Tags
```php
// Generate complete img tag
<?php echo image_tag(ImageManager::TYPE_LOGO, ['class' => 'logo', 'id' => 'site-logo']); ?>

// Generate profile image tag
<?php echo profile_image_tag($userId, ['class' => 'profile-pic', 'alt' => 'User Profile']); ?>
```

#### Direct ImageManager Access
```php
// Get admin image or default
$logoUrl = ImageManager::getImageUrl(ImageManager::getAdminImage(ImageManager::TYPE_LOGO));

// Get user image or default
$profileUrl = ImageManager::getImageUrl(ImageManager::getUserImage($userId));

// Get default image
$defaultUrl = ImageManager::getImageUrl(ImageManager::getDefaultImage(ImageManager::TYPE_PROFILE));
```

---

## Database Schema

### images Table

```sql
CREATE TABLE images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    image_type VARCHAR(50) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(50) NOT NULL,
    width INT NULL,
    height INT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_image_type (image_type),
    INDEX idx_is_admin (is_admin),
    INDEX idx_uploaded_at (uploaded_at)
);
```

---

## Image Types and Dimensions

| Type | Width | Height | Max Size | Use Case |
|------|-------|--------|----------|----------|
| logo | 200px | 50px | 5MB | Site logo |
| favicon | 32px | 32px | 5MB | Browser tab icon |
| banner | 1920px | 400px | 5MB | Page banner |
| profile | 200px | 200px | 2MB | User profile picture |

---

## API Endpoints

### Admin Endpoints

#### Upload Logo
```
POST /admin/images/upload-logo
Content-Type: multipart/form-data

file: logo.png
```

Response:
```json
{
  "success": true,
  "path": "/storage/uploads/admin/logo_1731654000_a1b2c3d4.png",
  "filename": "logo_1731654000_a1b2c3d4.png",
  "type": "logo",
  "size": 45234
}
```

#### Upload Favicon
```
POST /admin/images/upload-favicon
Content-Type: multipart/form-data

file: favicon.png
```

#### Upload Banner
```
POST /admin/images/upload-banner
Content-Type: multipart/form-data

file: banner.jpg
```

#### Get Current Admin Image
```
GET /admin/images/current?type=logo
```

Response:
```json
{
  "success": true,
  "image": {
    "id": 1,
    "path": "/storage/uploads/admin/logo_1731654000_a1b2c3d4.png",
    "url": "http://localhost/Bishwo_Calculator/storage/uploads/admin/logo_1731654000_a1b2c3d4.png"
  }
}
```

#### Delete Image
```
POST /admin/images/delete
Content-Type: application/x-www-form-urlencoded

image_id=1
```

### User Endpoints

#### Upload Profile Image
```
POST /profile/upload-image
Content-Type: multipart/form-data

profile_image: profile.jpg
```

#### Get Profile Image
```
GET /profile/image
```

Response:
```json
{
  "success": true,
  "image": {
    "path": "/storage/uploads/users/1/profile_1731654000_a1b2c3d4.jpg",
    "url": "http://localhost/Bishwo_Calculator/storage/uploads/users/1/profile_1731654000_a1b2c3d4.jpg"
  }
}
```

#### Delete Profile Image
```
POST /profile/delete-image
```

---

## Initialization

### Setup Storage Directories

```php
// In bootstrap or setup script
use App\Services\ImageManager;

ImageManager::initializeStorage();
```

This creates:
- `/storage/uploads/admin/`
- `/storage/uploads/users/`
- `.htaccess` files in each directory

---

## Error Handling

### Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| No file uploaded | Missing file in request | Ensure file is in correct form field |
| File size exceeds maximum | File too large | Compress image or increase limit |
| Invalid file type | Wrong extension | Use jpg, png, gif, webp, or svg |
| Invalid MIME type | File corrupted or misnamed | Verify file is valid image |
| Failed to move uploaded file | Permission issue | Check directory permissions |

### Error Response Example

```json
{
  "success": false,
  "error": "File size exceeds maximum (5MB)"
}
```

---

## Best Practices

### For Admins
1. ✅ Use high-quality images
2. ✅ Optimize images before upload
3. ✅ Use appropriate formats:
   - Logo: PNG (transparent background)
   - Favicon: PNG or ICO
   - Banner: JPG (compressed)
4. ✅ Keep file sizes small
5. ✅ Test on different devices

### For Users
1. ✅ Use clear, recognizable profile pictures
2. ✅ Ensure face is visible
3. ✅ Use appropriate dimensions
4. ✅ Keep file size under 2MB
5. ✅ Use PNG or JPG format

### For Developers
1. ✅ Always validate file uploads
2. ✅ Use helper functions in views
3. ✅ Check database for image records
4. ✅ Implement proper error handling
5. ✅ Test with various file types
6. ✅ Monitor storage usage

---

## Troubleshooting

### Images Not Displaying
1. Check if file exists in storage directory
2. Verify path in database
3. Check file permissions (should be 0644)
4. Verify web server can read files
5. Check browser console for 404 errors

### Upload Fails
1. Check file size limit
2. Verify file type is allowed
3. Check storage directory permissions
4. Ensure `/storage/uploads/` exists
5. Check PHP upload limits in php.ini

### Performance Issues
1. Images are automatically optimized
2. Consider CDN for high traffic
3. Implement image caching
4. Use WebP format for modern browsers
5. Lazy load images in views

---

## Security Checklist

- ✅ Files stored outside web root
- ✅ `.htaccess` prevents execution
- ✅ Filename randomization
- ✅ MIME type validation
- ✅ File size limits
- ✅ Extension whitelist
- ✅ Path traversal prevention
- ✅ Soft deletes (audit trail)
- ✅ User ID validation
- ✅ Admin flag tracking

---

## Future Enhancements

- [ ] Image cropping tool
- [ ] Batch upload
- [ ] Image compression queue
- [ ] CDN integration
- [ ] Image versioning
- [ ] Watermarking
- [ ] Advanced permissions
- [ ] Image analytics

---

## Support

For issues or questions:
1. Check error messages in response
2. Review database records
3. Verify file permissions
4. Check storage directory
5. Review security settings

---

**Status:** ✅ **Image Management System Ready**

All components are in place and ready to use!
