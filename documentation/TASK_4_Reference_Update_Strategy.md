# Task 4: Reference Update Strategy

## Objective
Decide the precise strategy to update all view references to use theme-based paths and deprecate `app/Views`.

## Chosen Strategy

### Core Principle
**Do not hardcode theme paths in controllers.** Instead, ensure the theme resolution system works correctly and all view files are in the right theme directories.

### Implementation Approach

#### 1. Keep Controller Calls Unchanged
- Controllers continue to use `$this->view->render('path/name', $data)`
- No changes to controller view paths needed
- Let `View::render()` and `ThemeManager` handle path resolution

#### 2. View Path Resolution Rules

**Admin Views**
- Controller calls: `$this->view->render('admin/dashboard', $data)`
- Resolves to: `themes/admin/views/dashboard.php`
- No `admin/` prefix in filesystem path

**Frontend Views**
- Controller calls: `$this->view->render('user/profile', $data)`
- Resolves to: `themes/<active-theme>/views/user/profile.php`
- Uses active theme from `ThemeManager`

#### 3. Layout Resolution

**Admin Layouts**
- Primary: `themes/admin/layouts/main.php`
- Remove fallback to `app/Views/layouts/admin.php`

**Frontend Layouts**
- Primary: `themes/<active-theme>/views/layouts/main.php`
- Remove fallback to `app/Views/layouts/main.php`
- Special layouts (auth, landing) in theme directories

#### 4. Fallback Removal Strategy

**Phase 1: Remove app/Views fallbacks**
- Update `View::render()` to remove `app/Views` fallbacks
- Update `ThemeManager` to remove `app/Views` fallbacks
- Add clear error messages for missing views

**Phase 2: Ensure theme completeness**
- Verify all required views exist in theme directories
- Move any missing views from `app/Views` to appropriate theme location
- Ensure all required layouts exist in theme directories

## Path Mappings

### Admin Views
| Controller Call | Filesystem Location |
|-----------------|---------------------|
| `admin/dashboard` | `themes/admin/views/dashboard.php` |
| `admin/users/index` | `themes/admin/views/users/index.php` |
| `admin/settings/index` | `themes/admin/views/settings/index.php` |
| `admin/calculators/list` | `themes/admin/views/calculators/list.php` |

### Frontend Views
| Controller Call | Filesystem Location |
|-----------------|---------------------|
| `user/profile` | `themes/<active-theme>/views/user/profile.php` |
| `help/index` | `themes/<active-theme>/views/help/index.php` |
| `calculators/tool` | `themes/<active-theme>/views/calculators/tool.php` |
| `payment/checkout` | `themes/<active-theme>/views/payment/checkout.php` |

### Layouts
| Type | Filesystem Location |
|------|---------------------|
| Admin main | `themes/admin/layouts/main.php` |
| Frontend main | `themes/<active-theme>/views/layouts/main.php` |
| Auth | `themes/<active-theme>/views/layouts/auth.php` |
| Landing | `themes/<active-theme>/views/layouts/landing.php` |

## Implementation Steps

### Step 1: Verify View File Locations
- Check all controller view paths have corresponding files in theme directories
- Move any missing files from `app/Views` to appropriate theme location

### Step 2: Update View::render()
- Remove fallback to `app/Views` for admin views
- Ensure clear error handling for missing views

### Step 3: Update ThemeManager
- Remove any fallbacks to `app/Views`
- Ensure theme resolution works correctly

### Step 4: Update Layout Resolution
- Remove fallbacks to `app/Views/layouts/`
- Ensure all required layouts exist in theme directories

### Step 5: Remove Controller::view()
- Deprecate or remove the `Controller::view()` method
- Ensure no code is using this method

## Benefits of This Strategy

1. **Clean Controllers**: No theme path hardcoding in controllers
2. **Theme Flexibility**: Easy to switch themes without controller changes
3. **Clear Separation**: Admin and frontend views clearly separated
4. **Maintainability**: Single point of view resolution logic
5. **Future-Proof**: Easy to add new themes or modify existing ones

## Risk Mitigation

1. **Comprehensive Testing**: Test all view paths after changes
2. **Clear Error Messages**: Helpful errors for missing views
3. **Gradual Migration**: Can be done incrementally
4. **Backup Strategy**: Keep `app/Views` backup until validation complete

## Success Criteria

1. All views resolve to theme directories
2. No fallbacks to `app/Views` remain
3. All pages function correctly
4. Clear error handling for missing views
5. `app/Views` can be safely deleted