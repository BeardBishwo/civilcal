# VS Code Debug Panel Setup Guide (Ctrl+Shift+D)
## Bishwo Calculator - Complete Debugging Configuration

---

## 📋 Table of Contents
1. [Quick Start](#quick-start)
2. [Xdebug Installation](#xdebug-installation)
3. [VS Code Configuration](#vs-code-configuration)
4. [Debugging Methods](#debugging-methods)
5. [Breakpoint Usage](#breakpoint-usage)
6. [Troubleshooting](#troubleshooting)

---

## 🚀 Quick Start

### Step 1: Install Required Extension
1. Open VS Code
2. Press `Ctrl+Shift+X` (Extensions)
3. Search for "PHP Debug"
4. Install the extension by Xdebug (formerly by Felix Becker)

### Step 2: Install Xdebug
Since you're using Laragon, follow these steps:

#### For Laragon (Windows):
```bash
# 1. Check PHP version
php -v
# Output: PHP 8.3.16

# 2. Download Xdebug for PHP 8.3 (Thread Safe for Windows)
# Visit: https://xdebug.org/download
# Download: php_xdebug-3.3.2-8.3-vs16-x86_64.dll

# 3. Copy to PHP extensions directory
# Location: C:\laragon\bin\php\php-8.3.16\ext\
# Rename to: php_xdebug.dll
```

#### For Linux/Mac:
```bash
# Install via PECL
sudo pecl install xdebug

# Or via package manager
# Ubuntu/Debian:
sudo apt-get install php-xdebug

# Mac (Homebrew):
brew install php@8.3-xdebug
```

### Step 3: Configure PHP
Add to `php.ini` (Laragon: `C:\laragon\bin\php\php-8.3.16\php.ini`):

```ini
[Xdebug]
zend_extension=php_xdebug.dll

; Xdebug 3 configuration
xdebug.mode=debug,develop
xdebug.start_with_request=yes
xdebug.client_port=9003
xdebug.client_host=127.0.0.1
xdebug.idekey=VSCODE

; Optional: Better error display
xdebug.var_display_max_depth=10
xdebug.var_display_max_children=256
xdebug.var_display_max_data=1024

; Performance profiling (optional)
xdebug.mode=debug,develop,profile
xdebug.output_dir="C:\laragon\www\Bishwo_Calculator\storage\logs"
```

### Step 4: Restart Web Server
```bash
# In Laragon, click "Stop All" then "Start All"
# Or restart Apache/Nginx
```

### Step 5: Verify Installation
```bash
php -m | grep xdebug
# Should output: xdebug

php -r "var_dump(extension_loaded('xdebug'));"
# Should output: bool(true)
```

---

## 🔧 VS Code Configuration

### Launch Configuration
The `.vscode/launch.json` file has been created with multiple debug configurations:

#### Available Debug Configurations:

1. **Listen for Xdebug** (Browser debugging)
   - Use this for debugging web requests
   - Set breakpoints and visit pages in browser
   - Port: 9003

2. **Launch currently open script**
   - Debug the PHP file you currently have open
   - Quick way to test individual files

3. **Debug Bootstrap**
   - Debug the application bootstrap process
   - Tests: `app/bootstrap.php`

4. **Debug Index (Homepage)**
   - Debug homepage rendering
   - Tests: `public/index.php`

5. **Debug Test Script**
   - Debug the test verification script
   - Tests: `test_fixes.php`

6. **Debug IDE Runtime Test**
   - Debug the comprehensive runtime test
   - Tests: `debug_ide_runtime.php`

7. **Debug Routes**
   - Debug route configuration
   - Tests: `app/routes.php`

8. **Debug Helper Functions**
   - Debug helper function execution
   - Tests: `app/Helpers/functions.php`

9. **Debug Database Connection**
   - Debug database connectivity
   - Tests: `app/Core/Database.php`

---

## 🐛 How to Use Debug Panel (Ctrl+Shift+D)

### Method 1: Browser Debugging (Recommended)

1. **Open Debug Panel:**
   ```
   Press: Ctrl+Shift+D
   ```

2. **Select Configuration:**
   - At the top of Debug panel, select: "Listen for Xdebug"

3. **Set Breakpoints:**
   - Open any PHP file (e.g., `public/index.php`)
   - Click left of line number to set red breakpoint dot
   - Example locations:
     ```php
     // public/index.php
     require_once dirname(__DIR__) . '/app/bootstrap.php'; // ← Set breakpoint here
     
     // app/bootstrap.php
     define("BASE_PATH", dirname(__DIR__)); // ← Set breakpoint here
     
     // app/Helpers/functions.php
     function app_base_url($path='') { // ← Set breakpoint here
     ```

4. **Start Debugging:**
   - Press `F5` or click green play button
   - Status bar turns orange
   - Debug Console opens

5. **Trigger Breakpoint:**
   - Open browser
   - Visit: `http://localhost/Bishwo_Calculator/`
   - VS Code will pause at breakpoint

6. **Debug Controls:**
   - `F5` - Continue
   - `F10` - Step Over
   - `F11` - Step Into
   - `Shift+F11` - Step Out
   - `Ctrl+Shift+F5` - Restart
   - `Shift+F5` - Stop

### Method 2: CLI Script Debugging

1. **Open Debug Panel:**
   ```
   Press: Ctrl+Shift+D
   ```

2. **Select Configuration:**
   - Choose: "Debug Test Script" (or any script-based config)

3. **Set Breakpoints:**
   - Open `test_fixes.php`
   - Set breakpoints on lines you want to inspect

4. **Start Debugging:**
   - Press `F5`
   - Script executes and pauses at breakpoints

5. **Inspect Variables:**
   - Hover over variables to see values
   - View Variables panel on left
   - Check Call Stack
   - Examine Debug Console output

---

## 📍 Effective Breakpoint Locations

### Critical Breakpoints for Bishwo Calculator:

#### 1. Bootstrap Loading
```php
// app/bootstrap.php - Line 6
define("BASE_PATH", dirname(__DIR__)); // ← Check if BASE_PATH is set correctly

// Line 29
$appConfig = require_once CONFIG_PATH . "/app.php"; // ← Verify config loading

// Line 95
require_once APP_PATH . "/Helpers/functions.php"; // ← Confirm helpers loaded
```

#### 2. Helper Functions
```php
// app/Helpers/functions.php - Line 99
function app_base_url($path='') { // ← Test URL generation
    $base = defined('APP_BASE') ? rtrim(APP_BASE, '/') : '';
    return $base . ($path ? '/' . ltrim($path, '/') : '');
}

// Line 236
function asset_url(string $path = ""): string { // ← Check asset URL generation

// Line 248
function is_logged_in(): bool { // ← Verify authentication check
```

#### 3. Routing
```php
// app/Core/Router.php - Line 23
public function add(string $method, string $route, $handler, array $middleware = []) {
    // ← Verify routes being registered

// Line 45
public function dispatch() { // ← Debug request dispatching
```

#### 4. View Rendering
```php
// app/Core/View.php - Line 52
public function render(string $view, array $data = [], ?string $layout = 'main') {
    // ← Debug view rendering

// themes/default/views/partials/header.php - Line 148
foreach ($cssFiles as $css) { // ← Check CSS loading
```

#### 5. Database
```php
// app/Core/Database.php - Line 15
public static function getInstance() { // ← Verify DB connection

// Line 45
public function query($sql, $params = []) { // ← Monitor queries
```

---

## 🎯 Debug Panel Features

### Variables Panel
- **Local Variables:** Current scope variables
- **Superglobals:** $_GET, $_POST, $_SESSION, etc.
- **Constants:** All defined constants
- **Watch:** Add custom expressions to monitor

### Call Stack
- Shows function call hierarchy
- Click entries to jump to that point in code
- See parameter values passed to functions

### Breakpoints Panel
- Manage all breakpoints
- Enable/disable without removing
- Conditional breakpoints: Right-click → Edit Breakpoint

### Debug Console
- Execute PHP code in current context
- Example:
  ```php
  // Type in Debug Console while paused:
  var_dump($appConfig);
  print_r($_SESSION);
  echo BASE_PATH;
  ```

---

## 🔍 Advanced Debugging Techniques

### Conditional Breakpoints
Right-click on breakpoint → Edit Breakpoint → Expression:
```php
// Only break when user is admin
$_SESSION['role'] === 'admin'

// Only break for specific route
$route === '/login'

// Only break when variable has value
!empty($user)
```

### Logpoints
Right-click line number → Add Logpoint:
```php
// Instead of breakpoint, logs to Debug Console
Value of $base: {$base}
User session: {$_SESSION}
```

### Watch Expressions
Add to Watch panel:
```php
BASE_PATH
$_SESSION['user_id']
is_logged_in()
count($cssFiles)
```

### Step Filtering
Skip stepping into certain functions:
```json
// In settings.json
"php.debug.stepFilters": [
    "vendor/**"
]
```

---

## 🧪 Testing Scenarios with Debugger

### Scenario 1: Debug CSS Loading Issue
1. Set breakpoint: `themes/default/views/partials/header.php:148`
2. Start "Listen for Xdebug"
3. Visit homepage in browser
4. When paused:
   - Check `$cssFiles` array
   - Verify `$cssPath` values
   - Inspect `$url` generated
   - Step through loop with F10

### Scenario 2: Debug Login Process
1. Set breakpoints:
   - `app/Controllers/AuthController.php:30` (login method)
   - `app/Services/Auth.php` (if exists)
2. Start debugging
3. Submit login form
4. Inspect:
   - `$_POST` data
   - `$identity` and `$password` variables
   - Authentication result
   - Session setting

### Scenario 3: Debug Helper Function
1. Open `app/Helpers/functions.php`
2. Set breakpoint on `app_base_url()` function
3. Use "Debug Test Script"
4. When paused:
   - Check `APP_BASE` constant value
   - Verify `$path` parameter
   - Step through logic
   - See return value

### Scenario 4: Debug Bootstrap Sequence
1. Set breakpoints at:
   - `app/bootstrap.php:6` (BASE_PATH)
   - `app/bootstrap.php:29` (Config loading)
   - `app/bootstrap.php:95` (Helpers loading)
2. Use "Debug Bootstrap" config
3. Step through entire bootstrap process
4. Verify each constant and include

---

## 🛠️ Troubleshooting

### Issue 1: Debug Panel Shows No Configurations
**Solution:**
- Ensure `.vscode/launch.json` exists
- Reload VS Code window: `Ctrl+Shift+P` → "Reload Window"

### Issue 2: Breakpoints Not Hit
**Checklist:**
- ✓ Xdebug installed: `php -m | grep xdebug`
- ✓ Extension enabled in VS Code
- ✓ Correct port (9003) in both php.ini and launch.json
- ✓ Web server restarted after php.ini changes
- ✓ Firewall not blocking port 9003

**Test Connection:**
```bash
# Create test file: xdebug-test.php
<?php
phpinfo();
?>

# Visit in browser, search for "xdebug"
# Should show Xdebug section
```

### Issue 3: "Cannot connect to runtime process"
**Solutions:**
1. Check php.ini has correct path to xdebug.dll
2. Verify xdebug.mode includes "debug"
3. Restart PHP service
4. Check if xdebug.client_port matches launch.json port

### Issue 4: Path Mapping Issues
**Fix:**
Update `launch.json` pathMappings:
```json
"pathMappings": {
    "/var/www/html": "${workspaceFolder}",
    "C:\\laragon\\www\\Bishwo_Calculator": "${workspaceFolder}",
    "/actual/server/path": "${workspaceFolder}"
}
```

### Issue 5: Debug Console Shows Nothing
**Solutions:**
- Set `"log": true` in launch.json
- Check Output panel → "Xdebug" channel
- Increase xdebug.log_level in php.ini

---

## 📊 Performance Profiling with Xdebug

### Enable Profiling
Add to php.ini:
```ini
xdebug.mode=debug,develop,profile
xdebug.output_dir="C:\laragon\www\Bishwo_Calculator\storage\logs"
xdebug.profiler_output_name=cachegrind.out.%t
```

### Analyze with Tools
- **Webgrind:** Web-based profiling viewer
- **QCacheGrind:** Desktop profiling analyzer
- **VS Code Extension:** PHP Profiler

### View Profile
```bash
# Profiles saved to: storage/logs/cachegrind.out.*
# Use tool to visualize:
# - Function call times
# - Memory usage
# - Bottlenecks
```

---

## 🎓 Best Practices

### 1. Strategic Breakpoint Placement
```php
// At function entry
function myFunction($param) { // ← Breakpoint here
    // Check parameters

// Before critical operations
$result = $database->query($sql); // ← Breakpoint before
// Check $sql value

// After operations
$data = processData($input);
// ← Breakpoint after to check $data

// In error conditions
if (!$valid) {
    // ← Breakpoint to debug validation failures
}
```

### 2. Use Step Controls Effectively
- **F10 (Step Over):** Skip function internals
- **F11 (Step Into):** Enter function to debug
- **Shift+F11 (Step Out):** Exit current function
- **F5 (Continue):** Jump to next breakpoint

### 3. Monitor Key Variables
Add to Watch panel:
```php
$_SESSION
$_GET
$_POST
$_SERVER['REQUEST_URI']
BASE_PATH
APP_BASE
```

### 4. Debug Console Commands
```php
// While paused, try:
var_dump(get_defined_vars());
print_r(debug_backtrace());
echo memory_get_usage();
echo get_class($object);
```

---

## 🚀 Quick Commands Reference

| Action | Shortcut | Description |
|--------|----------|-------------|
| Open Debug Panel | `Ctrl+Shift+D` | Main debug interface |
| Start Debugging | `F5` | Run selected configuration |
| Stop Debugging | `Shift+F5` | End debug session |
| Restart | `Ctrl+Shift+F5` | Restart debugging |
| Continue | `F5` | Run to next breakpoint |
| Step Over | `F10` | Execute current line |
| Step Into | `F11` | Enter function |
| Step Out | `Shift+F11` | Exit function |
| Toggle Breakpoint | `F9` | Add/remove breakpoint |
| Disable All Breakpoints | `Ctrl+Shift+F9` | Temporarily disable |

---

## 📝 Example Debug Session

### Complete Workflow:

1. **Open VS Code in project folder**
   ```bash
   cd C:\laragon\www\Bishwo_Calculator
   code .
   ```

2. **Install PHP Debug Extension**
   - Extensions (Ctrl+Shift+X)
   - Search: "PHP Debug"
   - Install

3. **Set Breakpoints**
   - Open: `public/index.php`
   - Click line 11 (bootstrap require)
   - Click line 76 (router dispatch)

4. **Open Debug Panel**
   - Press `Ctrl+Shift+D`
   - Select: "Listen for Xdebug"

5. **Start Debugging**
   - Press `F5`
   - Status bar turns orange

6. **Trigger Breakpoint**
   - Open browser
   - Visit: `http://localhost/Bishwo_Calculator/`

7. **Debug Session**
   - VS Code pauses at first breakpoint
   - Inspect variables in left panel
   - Check Call Stack
   - Press F10 to step through code
   - Hover over variables to see values

8. **Continue or Stop**
   - Press F5 to continue to next breakpoint
   - Press Shift+F5 to stop debugging

---

## 🎯 Current System Status

Based on our testing:

### ✅ Ready for Debugging
- Bootstrap loading: Working
- Helper functions: All loaded
- Core classes: All available
- Routes: Valid syntax
- Database: Connected
- CSS/JS: Loading correctly

### 🎯 Recommended Debug Points

**Test CSS Loading:**
```
File: themes/default/views/partials/header.php
Line: 148-159 (CSS loading loop)
Config: "Listen for Xdebug"
```

**Test Bootstrap:**
```
File: app/bootstrap.php
Line: 6, 29, 95
Config: "Debug Bootstrap"
```

**Test Helpers:**
```
File: app/Helpers/functions.php
Line: 99 (app_base_url)
Line: 236 (asset_url)
Config: "Debug Helper Functions"
```

---

## 📚 Additional Resources

### Official Documentation
- [Xdebug Documentation](https://xdebug.org/docs/)
- [VS Code PHP Debugging](https://code.visualstudio.com/docs/languages/php)
- [PHP Debug Extension](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug)

### Video Tutorials
- Search YouTube: "VS Code PHP Xdebug tutorial"
- Look for PHP 8.3 specific guides

### Laragon Specific
- Laragon already includes PHP
- Just need to add Xdebug extension
- Configuration in: `C:\laragon\bin\php\php-8.3.16\php.ini`

---

## ✅ Verification Checklist

Before debugging:
- [ ] Xdebug installed: `php -m | grep xdebug`
- [ ] PHP Debug extension installed in VS Code
- [ ] `.vscode/launch.json` exists
- [ ] Web server restarted after php.ini changes
- [ ] Port 9003 not blocked by firewall
- [ ] Breakpoints set in desired files
- [ ] Debug configuration selected
- [ ] Status bar turns orange when debugging starts

---

## 🎊 You're Ready to Debug!

1. Press `Ctrl+Shift+D` to open Debug Panel
2. Select a configuration from dropdown
3. Set breakpoints by clicking line numbers
4. Press `F5` to start debugging
5. Trigger the code path (browser or CLI)
6. Use debug controls to step through code

**Happy Debugging! 🐛🔍**

---

For questions or issues, refer to:
- ERROR_RESOLUTION_REPORT.md
- FINAL_CSS_AND_TESTS_REPORT.md
- IDE_DEBUGGER_SUMMARY.txt