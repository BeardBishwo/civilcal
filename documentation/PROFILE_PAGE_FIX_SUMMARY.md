# Profile Page Error Fix Summary

## Issue
The user profile page at `http://localhost/Bishwo_Calculator/user/profile` was showing PHP warnings about missing include files.

## Errors Found
1. **Line 765 (Footer)**: `include('themes/default/views/partials/footer.php')` - Failed to open stream
2. **Line 428 (Header)**: `include('themes/default/views/partials/header.php')` - Failed to open stream

## Root Cause
The include statements were using relative paths that don't work correctly when the view file is executed from within the MVC framework. The working directory is the project root, not the view file's directory.

## Solution Applied
Fixed both include statements in `app/Views/user/profile.php` to use absolute paths with proper file existence checks:

### Header Fix (Line 427)
```php
<?php 
$headerPath = __DIR__ . '/../../../themes/default/views/partials/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
}
?>
```

### Footer Fix (Line 765)
```php
<?php 
$footerPath = __DIR__ . '/../../../themes/default/views/partials/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
}
?>
```

## Result
✅ Profile page now loads successfully without any PHP warnings or errors
✅ Both header and footer are properly included
✅ Page displays "User Profile" title correctly

## Testing
- Verified page loads at: `http://localhost/Bishwo_Calculator/user/profile`
- No xdebug errors or warnings present
- Page renders correctly with all content

## Date
Fixed: <?php echo date('Y-m-d H:i:s'); ?>
