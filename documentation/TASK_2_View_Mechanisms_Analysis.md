# Task 2: View Loading Mechanisms Analysis

## Objective
Analyze in detail how `Controller::view()` and `View::render()` work to understand what needs to be changed.

## Controller::view() Analysis

### Location
`app/Core/Controller.php`, lines ~52-65

### Implementation
```php
protected function view($view, $data = []) {
    extract($data);
    $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        echo "<h1>View Error</h1>";
        echo "<p>View file not found: " . htmlspecialchars($view) . "</p>";
        echo "<p>Expected path: " . htmlspecialchars($viewPath) . "</p>";
    }
}
```

### Key Characteristics
- Hardcoded path: `__DIR__ . '/../Views/'`
- No theme awareness
- Direct file inclusion
- No layout wrapping
- Simple error handling

### Layout Method
```php
protected function layout($layoutName, $data = []) {
    extract($data);
    $layoutPath = __DIR__ . '/../Views/layouts/' . $layoutName . '.php';
    if (file_exists($layoutPath)) {
        include $layoutPath;
    } else {
        echo "<h1>Layout Error</h1>";
        echo "<p>Layout file not found: " . htmlspecialchars($layoutName) . "</p>";
    }
}
```

## View::render() Analysis

### Location
`app/Core/View.php`, lines ~44-100

### Implementation Details

#### Admin View Resolution
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

#### Non-Admin View Resolution
```php
else {
    $this->themeManager->renderView($view, $data);
}
```

#### Layout Resolution
- Admin: `themes/admin/layouts/main.php` → `app/Views/layouts/admin.php`
- Non-admin: Multiple fallbacks including `app/Views/layouts/main.php`

### Key Characteristics
- Theme-aware with fallbacks to `app/Views`
- Automatic layout wrapping
- Uses ThemeManager for non-admin views
- Complex fallback chain

## ThemeManager Integration

### Role
- Handles non-admin view resolution
- Manages active theme
- Provides theme-related utilities
- Has its own fallbacks to `app/Views`

## Migration Implications

### Issues Identified
1. `Controller::view()` is completely incompatible with themes
2. `View::render()` has multiple fallbacks to `app/Views`
3. Layout resolution has fallbacks to `app/Views/layouts/`
4. ThemeManager likely has fallbacks to `app/Views`

### Required Changes
1. Eliminate all uses of `Controller::view()`
2. Remove fallbacks from `View::render()`
3. Update layout resolution
4. Update ThemeManager fallbacks
5. Ensure all view files exist in theme directories

## Dependencies
- `app/Core/Controller.php`
- `app/Core/View.php`
- `app/Services/ThemeManager.php`
- All controller files
- Layout files in themes