# Root Test Files - Moved to Tests Directory

## Summary

All test, debug, and diagnostic files have been moved from the root directory to the `tests/` folder for better organization.

## Files Moved and Their Purpose

### Test Files (tests/manual/)

| File | Purpose |
|------|---------|
| `test_admin_dashboard.php` | Tests admin dashboard functionality |
| `test_admin_fix.php` | Tests admin-related fixes |
| `test_css_js_and_tests.php` | Tests CSS and JavaScript loading |
| `test_css_url.php` | Tests CSS URL generation and ThemeManager |
| `test_fixes.php` | Tests various bug fixes |
| `test_logo_favicon.php` | Tests logo and favicon loading |
| `test_routing.php` | Tests application routing |
| `test_theme_images.php` | Tests theme image loading |
| `test_website_errors.php` | Tests website error handling |

### Debug Files (tests/debug/)

| File | Purpose |
|------|---------|
| `debug_test.php` | General debugging test utilities |
| `debug_ide_runtime.php` | IDE runtime debugging |
| `debug_pages.php` | Debug page rendering |
| `auto_debug_demo.php` | Automatic debugging demonstration |
| `generate_debug_report.php` | Generates comprehensive debug reports |
| `install_xdebug_auto.php` | Automatic XDebug installation |
| `setup_xdebug.php` | XDebug setup and configuration |

### Diagnostic Files (tests/diagnostics/)

| File | Purpose |
|------|---------|
| `check_db.php` | Checks database connection and tables |
| `check_errors.php` | Checks for PHP errors and warnings |
| `verify_pages.php` | Verifies page accessibility |
| `diagnose_css_loading.php` | Diagnoses CSS loading issues |
| `comprehensive_verification.php` | Comprehensive system verification |

### API Test Files (tests/api/)

| File | Purpose |
|------|---------|
| `direct_check_username.php` | Direct API test for username availability |
| `direct_forgot_password.php` | Direct API test for password reset |
| `direct_login.php` | Direct API test for login functionality |

## Directory Structure

```
tests/
├── manual/
│   ├── test_admin_dashboard.php
│   ├── test_admin_fix.php
│   ├── test_css_js_and_tests.php
│   ├── test_css_url.php
│   ├── test_fixes.php
│   ├── test_logo_favicon.php
│   ├── test_routing.php
│   ├── test_theme_images.php
│   └── test_website_errors.php
├── debug/
│   ├── debug_test.php
│   ├── debug_ide_runtime.php
│   ├── debug_pages.php
│   ├── auto_debug_demo.php
│   ├── generate_debug_report.php
│   ├── install_xdebug_auto.php
│   └── setup_xdebug.php
├── diagnostics/
│   ├── check_db.php
│   ├── check_errors.php
│   ├── verify_pages.php
│   ├── diagnose_css_loading.php
│   └── comprehensive_verification.php
└── api/
    ├── direct_check_username.php
    ├── direct_forgot_password.php
    └── direct_login.php
```

## How to Use

### Run Manual Tests
```bash
php tests/manual/test_css_url.php
php tests/manual/test_routing.php
```

### Run Debug Tools
```bash
php tests/debug/generate_debug_report.php
php tests/debug/setup_xdebug.php
```

### Run Diagnostics
```bash
php tests/diagnostics/check_db.php
php tests/diagnostics/verify_pages.php
php tests/diagnostics/comprehensive_verification.php
```

### Run API Tests
```bash
php tests/api/direct_check_username.php
php tests/api/direct_login.php
```

## Benefits

✅ **Organized** - All test files in one place
✅ **Categorized** - Grouped by purpose
✅ **Cleaner Root** - Root directory is cleaner
✅ **Easier Maintenance** - Easy to find and update tests
✅ **Better Structure** - Follows project conventions

## Notes

- All files maintain their original functionality
- No code changes were made during the move
- Update any hardcoded paths if needed
- Use relative paths when running tests from tests/ directory
