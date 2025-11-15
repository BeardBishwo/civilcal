# Tests Directory Index

## Overview

All test files have been organized into categorized subdirectories for better maintainability and clarity.

## Directory Structure

```
tests/
├── manual/              # Manual test files
├── debug/               # Debugging utilities
├── diagnostics/         # System diagnostics
├── api/                 # API test files
├── theme/               # Theme-related tests
├── frontend/            # Frontend tests
├── database/            # Database tests
├── legacy/              # Legacy tests
├── installation/        # Installation tests
├── routing/             # Routing tests
├── registration/        # Registration tests
├── email/               # Email tests
├── payment/             # Payment tests
├── search/              # Search tests
├── server/              # Server tests
├── location/            # Location tests
├── username/            # Username tests
├── test_runner.php      # Test runner script
└── organize_tests.php   # Test organization script
```

## New Subdirectories

### manual/ (9 files)
Manual test files for various features:
- `test_admin_dashboard.php` - Admin dashboard tests
- `test_admin_fix.php` - Admin fix tests
- `test_css_js_and_tests.php` - CSS/JS loading tests
- `test_css_url.php` - CSS URL generation tests
- `test_fixes.php` - Bug fix tests
- `test_logo_favicon.php` - Logo/favicon tests
- `test_routing.php` - Routing tests
- `test_theme_images.php` - Theme image tests
- `test_website_errors.php` - Error handling tests

### debug/ (7 files)
Debugging and development utilities:
- `debug_test.php` - General debugging
- `debug_ide_runtime.php` - IDE runtime debugging
- `debug_pages.php` - Page rendering debugging
- `auto_debug_demo.php` - Auto debug demonstration
- `generate_debug_report.php` - Debug report generation
- `install_xdebug_auto.php` - XDebug auto-installation
- `setup_xdebug.php` - XDebug setup

### diagnostics/ (5 files)
System diagnostics and verification:
- `check_db.php` - Database verification
- `check_errors.php` - Error checking
- `verify_pages.php` - Page verification
- `diagnose_css_loading.php` - CSS diagnostics
- `comprehensive_verification.php` - Full system check

### api/ (3 files)
API test files:
- `direct_check_username.php` - Username API tests
- `direct_forgot_password.php` - Password reset API tests
- `direct_login.php` - Login API tests

## Existing Subdirectories

- **theme/** - Theme-related tests
- **frontend/** - Frontend tests
- **database/** - Database tests
- **legacy/** - Legacy tests
- **installation/** - Installation tests
- **routing/** - Routing tests
- **registration/** - Registration tests
- **email/** - Email tests
- **payment/** - Payment tests
- **search/** - Search tests
- **server/** - Server tests
- **location/** - Location tests
- **username/** - Username tests

## Quick Commands

### Run Manual Tests
```bash
php tests/manual/test_css_url.php
php tests/manual/test_routing.php
php tests/manual/test_theme_images.php
```

### Run Diagnostics
```bash
php tests/diagnostics/check_db.php
php tests/diagnostics/verify_pages.php
php tests/diagnostics/comprehensive_verification.php
```

### Run Debug Tools
```bash
php tests/debug/generate_debug_report.php
php tests/debug/setup_xdebug.php
```

### Run API Tests
```bash
php tests/api/direct_login.php
php tests/api/direct_check_username.php
```

## Documentation

- **ROOT_FILES_MOVED.md** - Details about moved files
- **TESTS_ORGANIZATION_SUMMARY.txt** - Complete organization summary
- **TESTS_QUICK_REFERENCE.txt** - Quick reference guide

## Statistics

- **Total Test Files**: 24 (moved from root)
- **Manual Tests**: 9
- **Debug Files**: 7
- **Diagnostic Files**: 5
- **API Test Files**: 3

## Benefits

✅ **Organized** - All tests in logical categories
✅ **Maintainable** - Easy to find and update tests
✅ **Professional** - Better project structure
✅ **Scalable** - Easy to add new tests
✅ **Clean Root** - Root directory is cleaner

## Notes

- All files maintain their original functionality
- No code changes were made during organization
- Use relative paths when running tests from tests/ directory
- See individual test files for specific usage instructions
