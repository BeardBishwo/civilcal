# Image Management System - Quick Start

## ✅ What's Included

A complete, production-ready image management system with:

- **Admin image uploads** (logo, favicon, banner)
- **User profile image uploads**
- **Automatic image optimization and resizing**
- **Secure file storage** (outside web root)
- **Database tracking** of all images
- **Default fallback images** (theme images)
- **Helper functions** for easy access in views
- **Security validation** (MIME types, file sizes, extensions)

---

## 📁 Files Created

### Core Services
- `app/Services/ImageManager.php` - Main image handling service
- `app/Models/Image.php` - Database model for images
- `app/Helpers/ImageHelper.php` - Helper functions for views

### Controllers
- `app/Controllers/Admin/ImageController.php` - Admin image management
- `app/Controllers/ProfileImageController.php` - User profile images

### Database
- `database/migrations/create_images_table.php` - Database schema

### Documentation
- `IMAGE_MANAGEMENT_GUIDE.md` - Complete documentation
- `IMAGE_SETUP_QUICK_START.md` - This file

---

## 🚀 Setup Steps

### Step 1: Initialize Storage Directories

```php
// Add to your bootstrap or setup script
use App\Services\ImageManager;

ImageManager::initializeStorage();
```

This creates:
- `/storage/uploads/admin/`
- `/storage/uploads/users/`
- `.htaccess` files (prevents PHP execution)

### Step 2: Run Database Migration

```bash
# Run migration to create images table
php artisan migrate:fresh
# Or manually execute the SQL from database/migrations/create_images_table.php
```

### Step 3: Add Routes (if not auto-routed)

```php
// In app/routes.php

// Admin image routes
Route::post('/admin/images/upload-logo', 'Admin\ImageController@uploadLogo');
Route::post('/admin/images/upload-favicon', 'Admin\ImageController@uploadFavicon');
Route::post('/admin/images/upload-banner', 'Admin\ImageController@uploadBanner');
Route::get('/admin/images/current', 'Admin\ImageController@getCurrent');
Route::post('/admin/images/delete', 'Admin\ImageController@deleteImage');

// User profile image routes
Route::post('/profile/upload-image', 'ProfileImageController@upload');
Route::get('/profile/image', 'ProfileImageController@get');
Route::post('/profile/delete-image', 'ProfileImageController@delete');
```

### Step 4: Use in Views

#### Simple Usage (Recommended)
```php
<!-- Display logo -->
<img src="<?php echo get_logo(); ?>" alt="Logo" />

<!-- Display favicon -->
<link rel="icon" href="<?php echo get_favicon(); ?>" />

<!-- Display banner -->
<img src="<?php echo get_banner(); ?>" alt="Banner" />

<!-- Display user profile image -->
<img src="<?php echo get_user_image($userId); ?>" alt="Profile" />
```

#### Using Helper Tags
```php
<!-- Generate complete img tag -->
<?php echo image_tag('logo', ['class' => 'site-logo']); ?>

<!-- Generate profile image tag -->
<?php echo profile_image_tag($userId, ['class' => 'profile-pic']); ?>
```

---

## 📤 Upload Images

### Admin Upload (Logo)

```html
<form method="POST" action="/admin/images/upload-logo" enctype="multipart/form-data">
    <input type="file" name="logo" accept="image/*" required />
    <button type="submit">Upload Logo</button>
</form>
```

### User Profile Upload

```html
<form method="POST" action="/profile/upload-image" enctype="multipart/form-data">
    <input type="file" name="profile_image" accept="image/*" required />
    <button type="submit">Upload Profile Picture</button>
</form>
```

---

## 🔒 Security Features

✅ **File Validation**
- Extension whitelist: jpg, jpeg, png, gif, webp, svg
- MIME type verification
- File size limits (5MB admin, 2MB user)

✅ **Storage Security**
- Files stored outside web root (`/storage/uploads/`)
- `.htaccess` prevents PHP execution
- Secure filename generation (timestamp + random hash)
- Proper file permissions (0644)

✅ **Database Security**
- Soft deletes (audit trail)
- User ID validation
- Admin flag tracking
- Timestamps for all operations

---

## 📊 Image Dimensions

| Type | Size | Max File |
|------|------|----------|
| Logo | 200×50px | 5MB |
| Favicon | 32×32px | 5MB |
| Banner | 1920×400px | 5MB |
| Profile | 200×200px | 2MB |

---

## 🎯 Common Tasks

### Get Admin Logo
```php
$logoUrl = get_logo();
```

### Get User Profile Image
```php
$profileUrl = get_user_image($userId);
```

### Get Current User Profile
```php
$myProfileUrl = get_current_user_profile_image();
```

### Upload Admin Image (in controller)
```php
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

### Delete Image
```php
ImageManager::deleteImage($imagePath);
```

---

## 🐛 Troubleshooting

### Images Not Displaying
1. Check if `/storage/uploads/` directory exists
2. Verify file permissions (should be 0644)
3. Check browser console for 404 errors
4. Verify database has image records

### Upload Fails
1. Check file size (max 5MB for admin, 2MB for user)
2. Verify file type is allowed (jpg, png, gif, webp, svg)
3. Check `/storage/uploads/` permissions (should be 0755)
4. Ensure PHP can write to storage directory

### Permission Issues
```bash
# Fix directory permissions
chmod -R 0755 storage/uploads/

# Fix file permissions
chmod 0644 storage/uploads/admin/*
chmod 0644 storage/uploads/users/*/*
```

---

## 📚 Full Documentation

See `IMAGE_MANAGEMENT_GUIDE.md` for:
- Complete API documentation
- Database schema details
- Advanced usage examples
- Security checklist
- Best practices
- Performance tips

---

## ✨ Features

✅ Modular design - easy to extend
✅ Automatic image optimization
✅ Secure file storage
✅ Database tracking
✅ Default fallback images
✅ Helper functions
✅ Admin and user uploads
✅ Soft deletes
✅ MIME type validation
✅ File size limits
✅ Secure filenames
✅ Easy integration

---

## 🎉 You're Ready!

The image management system is fully set up and ready to use. Start uploading images!

For detailed information, see `IMAGE_MANAGEMENT_GUIDE.md`
