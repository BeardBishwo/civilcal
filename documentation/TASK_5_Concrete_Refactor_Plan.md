# Task 5: Concrete Refactor Plan

## Objective
Design a concrete refactor plan including mapping of old view paths to new themed paths and handling of admin vs frontend views.

## View Path Mapping

### Admin Views Mapping

| Original Path | Controller Call | New Theme Path |
|---------------|-----------------|----------------|
| `app/Views/admin/dashboard.php` | `admin/dashboard` | `themes/admin/views/dashboard.php` |
| `app/Views/admin/users/index.php` | `admin/users/index` | `themes/admin/views/users/index.php` |
| `app/Views/admin/users/create.php` | `admin/users/create` | `themes/admin/views/users/create.php` |
| `app/Views/admin/users/edit.php` | `admin/users/edit` | `themes/admin/views/users/edit.php` |
| `app/Views/admin/settings/index.php` | `admin/settings/index` | `themes/admin/views/settings/index.php` |
| `app/Views/admin/calculators/index.php` | `admin/calculators/index` | `themes/admin/views/calculators/index.php` |
| `app/Views/admin/calculators/list.php` | `admin/calculators/list` | `themes/admin/views/calculators/list.php` |
| `app/Views/admin/themes/index.php` | `admin/themes/index` | `themes/admin/views/themes/index.php` |
| `app/Views/admin/logs/index.php` | `admin/logs/index` | `themes/admin/views/logs/index.php` |
| `app/Views/admin/email-manager/index.php` | `admin/email-manager/index` | `themes/admin/views/email-manager/index.php` |

### Frontend Views Mapping

| Original Path | Controller Call | New Theme Path |
|---------------|-----------------|----------------|
| `app/Views/user/profile.php` | `user/profile` | `themes/default/views/user/profile.php` |
| `app/Views/user/edit-profile.php` | `user/edit-profile` | `themes/default/views/user/edit-profile.php` |
| `app/Views/user/change-password.php` | `user/change-password` | `themes/default/views/user/change-password.php` |
| `app/Views/help/index.php` | `help/index` | `themes/default/views/help/index.php` |
| `app/Views/help/article.php` | `help/article` | `themes/default/views/help/article.php` |
| `app/Views/help/category.php` | `help/category` | `themes/default/views/help/category.php` |
| `app/Views/calculators/tool.php` | `calculators/tool` | `themes/default/views/calculators/tool.php` |
| `app/Views/calculators/category.php` | `calculators/category` | `themes/default/views/calculators/category.php` |
| `app/Views/payment/checkout.php` | `payment/checkout` | `themes/default/views/payment/checkout.php` |
| `app/Views/payment/success.php` | `payment/success` | `themes/default/views/payment/success.php` |
| `app/Views/payment/failed.php` | `payment/failed` | `themes/default/views/payment/failed.php` |
| `app/Views/auth/login.php` | `auth/login` | `themes/default/views/auth/login.php` |
| `app/Views/auth/register.php` | `auth/register` | `themes/default/views/auth/register.php` |
| `app/Views/auth/forgot.php` | `auth/forgot` | `themes/default/views/auth/forgot.php` |
| `app/Views/errors/404.php` | `errors/404` | `themes/default/views/errors/404.php` |
| `app/Views/errors/500.php` | `errors/500` | `themes/default/views/errors/500.php` |

### Layouts Mapping

| Original Path | Usage | New Theme Path |
|---------------|-------|----------------|
| `app/Views/layouts/main.php` | Frontend main | `themes/default/views/layouts/main.php` |
| `app/Views/layouts/admin.php` | Admin main | `themes/admin/layouts/main.php` |
| `app/Views/layouts/auth.php` | Auth pages | `themes/default/views/layouts/auth.php` |
| `app/Views/layouts/landing.php` | Landing pages | `themes/default/views/layouts/landing.php` |

## Refactor Steps

### Phase 1: Verify Current State

1. **Check existing theme directories**
   - Verify `themes/admin/views/` exists and contains admin views
   - Verify `themes/default/views/` exists (or identify active theme)
   - Check for required layout files

2. **Identify missing view files**
   - Compare controller view calls with files in theme directories
   - List any views that need to be moved from `app/Views`

### Phase 2: Move View Files

1. **Move missing admin views**
   ```bash
   # Example commands (adjust as needed)
   mv app/Views/admin/dashboard.php themes/admin/views/dashboard.php
   mv app/Views/admin/users/* themes/admin/views/users/
   ```

2. **Move missing frontend views**
   ```bash
   # Example commands
   mv app/Views/user/* themes/default/views/user/
   mv app/Views/help/* themes/default/views/help/
   ```

3. **Move layout files**
   ```bash
   mv app/Views/layouts/main.php themes/default/views/layouts/main.php
   mv app/Views/layouts/admin.php themes/admin/layouts/main.php
   ```

### Phase 3: Update Code

1. **Update View::render() in `app/Core/View.php`**
   - Remove fallback to `app/Views` for admin views
   - Remove fallback to `app/Views` for non-admin views
   - Add clear error handling

2. **Update ThemeManager**
   - Remove fallbacks to `app/Views`
   - Ensure theme resolution works correctly

3. **Update layout resolution**
   - Remove fallbacks to `app/Views/layouts/`
   - Ensure theme layouts are used

4. **Deprecate Controller::view()**
   - Add deprecation warning
   - Or remove method entirely if unused

### Phase 4: Code Changes Details

#### View::render() Changes

**Before:**
```php
if (strpos($view, "admin/") === 0) {
    $adminThemeViewPath = BASE_PATH . "/themes/admin/views/" . substr($view, 6) . ".php";
    if (file_exists($adminThemeViewPath)) {
        include $adminThemeViewPath;
    } else {
        // Fallback to app/Views
        $altPath = BASE_PATH . "/app/Views/" . $view . ".php";
        if (file_exists($altPath)) {
            include $altPath;
        }
    }
}
```

**After:**
```php
if (strpos($view, "admin/") === 0) {
    $adminThemeViewPath = BASE_PATH . "/themes/admin/views/" . substr($view, 6) . ".php";
    if (file_exists($adminThemeViewPath)) {
        include $adminThemeViewPath;
    } else {
        throw new \RuntimeException("Admin view not found: " . $view . " at " . $adminThemeViewPath);
    }
}
```

#### Layout Resolution Changes

**Before:**
```php
if (strpos($view, "admin/") === 0) {
    $layoutPath = BASE_PATH . "/themes/admin/layouts/main.php";
    if (!file_exists($layoutPath)) {
        $layoutPath = BASE_PATH . "/app/Views/layouts/admin.php";
    }
}
```

**After:**
```php
if (strpos($view, "admin/") === 0) {
    $layoutPath = BASE_PATH . "/themes/admin/layouts/main.php";
    if (!file_exists($layoutPath)) {
        throw new \RuntimeException("Admin layout not found: " . $layoutPath);
    }
}
```

### Phase 5: Validation

1. **Static analysis**
   - Search for any remaining references to `app/Views`
   - Verify all view paths have corresponding files

2. **Runtime testing**
   - Test all controller actions
   - Verify layouts are applied correctly
   - Check error handling

3. **Temporary rename test**
   - Rename `app/Views` to `app/Views_backup`
   - Run full test suite
   - Delete backup if successful

## Implementation Order

1. Verify current theme directory structure
2. Move any missing view files to theme directories
3. Update `View::render()` to remove fallbacks
4. Update `ThemeManager` to remove fallbacks
5. Update layout resolution
6. Add comprehensive error handling
7. Test all functionality
8. Delete `app/Views` directory

## Risk Mitigation

1. **Backup Strategy**: Create `app/Views_backup` before deletion
2. **Incremental Testing**: Test after each phase
3. **Rollback Plan**: Keep backup until full validation
4. **Error Handling**: Clear error messages for debugging

## Success Criteria

1. All views resolve to theme directories
2. No fallbacks to `app/Views` remain
3. All pages function correctly
4. Clear error handling for missing views
5. `app/Views` directory successfully deleted