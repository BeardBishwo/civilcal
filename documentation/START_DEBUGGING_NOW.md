# 🚀 START DEBUGGING NOW - Step by Step Tutorial
## VS Code Debug Panel (Ctrl+Shift+D) - Hands-On Guide

**Status:** ✅ Xdebug is installed and ready!

---

## 📋 Step-by-Step Instructions

### Step 1: Open VS Code
```
1. Open Windows Start Menu
2. Type: "VS Code" or "Visual Studio Code"
3. Press Enter
```

OR from Command Line:
```bash
cd C:\laragon\www\Bishwo_Calculator
code .
```

---

### Step 2: Install PHP Debug Extension (If Not Already)

1. **Open Extensions Panel:**
   - Press `Ctrl+Shift+X`
   - OR click Extensions icon in left sidebar (4 squares icon)

2. **Search for Extension:**
   - Type: `PHP Debug`
   - Look for extension by: **Xdebug**

3. **Install:**
   - Click the blue "Install" button
   - Wait for installation to complete

4. **Verify:**
   - Extension should show "Installed" badge

---

### Step 3: Open Debug Panel

**Press:** `Ctrl+Shift+D`

**OR:**
- Click the "Run and Debug" icon (play button with bug) in left sidebar

**What You Should See:**
- Debug panel opens on left side
- Dropdown at top shows debug configurations
- Play button (green triangle) next to dropdown

---

### Step 4: Create a Test File (Let's Debug Something!)

1. **Create new file:**
   - Press `Ctrl+N` (New File)
   - Save as: `debug_test.php` in project root

2. **Copy this code:**

```php
<?php
require_once __DIR__ . '/app/bootstrap.php';

echo "Starting debug test...\n\n";

// Test 1: Variables
$name = "Bishwo Calculator";
$version = "1.0";
$isActive = true;

echo "App Name: $name\n";
echo "Version: $version\n";
echo "Active: " . ($isActive ? "Yes" : "No") . "\n\n";

// Test 2: Array
$config = [
    'debug' => true,
    'database' => 'mysql',
    'port' => 3306
];

echo "Configuration:\n";
print_r($config);
echo "\n";

// Test 3: Function
function calculateSum($a, $b) {
    $result = $a + $b;
    return $result;
}

$sum = calculateSum(10, 20);
echo "Sum of 10 + 20 = $sum\n\n";

// Test 4: Helper function
if (function_exists('app_base_url')) {
    $baseUrl = app_base_url('/test');
    echo "Base URL: $baseUrl\n";
} else {
    echo "app_base_url() not found!\n";
}

// Test 5: Database connection
try {
    $db = App\Core\Database::getInstance();
    echo "\n✓ Database connection successful!\n";
} catch (Exception $e) {
    echo "\n✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Test Complete ===\n";
```

3. **Save the file:** `Ctrl+S`

---

### Step 5: Set Breakpoints

**What is a Breakpoint?**
A breakpoint pauses code execution so you can inspect variables.

**How to Set Breakpoint:**
1. Open `debug_test.php`
2. Find line 8: `$name = "Bishwo Calculator";`
3. **Click in the LEFT MARGIN** next to line number 8
4. A **red dot** appears ← This is a breakpoint!

**Set Multiple Breakpoints:**
- Line 15: `$config = [...]` (click left margin)
- Line 26: `function calculateSum(...)` (click left margin)
- Line 32: `$sum = calculateSum(10, 20);` (click left margin)

**You should now have 4 red dots visible!**

---

### Step 6: Select Debug Configuration

1. **Look at Debug Panel** (left side, opened with Ctrl+Shift+D)
2. **Find dropdown at top** (says "RUN AND DEBUG" or shows config name)
3. **Click dropdown arrow**
4. **Select:** "Launch currently open script"

---

### Step 7: Start Debugging! 🎯

1. **Press F5** (or click green play button)

**What Happens:**
- Status bar at bottom turns **ORANGE** ← Debugging active!
- Code executes and pauses at first breakpoint (line 8)
- Current line highlighted in **YELLOW**
- Debug toolbar appears at top (with play, pause, stop buttons)

**You are now in DEBUG MODE! 🐛**

---

### Step 8: Inspect Variables

**When paused at breakpoint, look at LEFT PANEL:**

**Variables Panel (collapsed/expanded):**
- Click "Variables" to expand
- You'll see:
  - **Superglobals** ($_GET, $_POST, $_SESSION, etc.)
  - **Local variables** (variables in current function)
  - Expand any variable to see its contents

**Watch Panel:**
- Click "Watch" section
- Click "+" button
- Type variable name: `$name`
- Press Enter
- Variable value appears!

**Hover Over Variables:**
- In the code editor, hover mouse over `$name`
- A tooltip shows current value

---

### Step 9: Use Debug Controls

**Debug Toolbar (at top center):**

| Button | Hotkey | Action | When to Use |
|--------|--------|--------|-------------|
| ▶️ Continue | F5 | Run to next breakpoint | Skip ahead |
| ⤵️ Step Over | F10 | Execute current line | Move line by line |
| ⬇️ Step Into | F11 | Enter function | Debug inside functions |
| ⬆️ Step Out | Shift+F11 | Exit function | Leave function |
| 🔄 Restart | Ctrl+Shift+F5 | Restart debugging | Start over |
| ⏹️ Stop | Shift+F5 | Stop debugging | End session |

**Try This:**
1. You're paused at line 8 (`$name = "Bishwo Calculator";`)
2. Press **F10** (Step Over)
3. Line 9 is now highlighted
4. Check Variables panel - `$name` now has a value!
5. Press **F10** again
6. Now `$version` has a value
7. Press **F10** again
8. Now `$isActive` has a value

Keep pressing F10 and watch variables populate!

---

### Step 10: Continue to Next Breakpoint

1. Press **F5** (Continue)
2. Execution jumps to next breakpoint (line 15)
3. Inspect the `$config` array in Variables panel
4. Expand it to see all values

Press **F5** again:
- Jumps to line 26 (function definition)

Press **F5** again:
- Jumps to line 32 (function call)

Press **F11** (Step Into):
- Enters inside `calculateSum` function
- You can now see `$a` and `$b` parameters in Variables panel

---

### Step 11: Debug Console (Advanced)

**At bottom of VS Code:**
- Click "Debug Console" tab

**Execute PHP code while debugging:**
```php
// Type these in Debug Console (one at a time):
$name
$version
$config
print_r($config)
$sum = 100 + 200
echo $sum
```

The console executes code in the **current context**!

---

### Step 12: Stop Debugging

When you're done:
1. Press **Shift+F5** (Stop)
2. OR click red square in debug toolbar
3. Status bar returns to normal color
4. Debugging session ends

---

## 🎯 Quick Practice Exercise

### Exercise 1: Debug the Homepage

1. **Open file:** `public/index.php`
2. **Set breakpoint:** Line 11 (where bootstrap is loaded)
3. **Debug configuration:** Select "Debug Index (Homepage)"
4. **Press F5**
5. **Open browser:** http://localhost/Bishwo_Calculator/
6. **VS Code pauses!** ← Magic! 🎩✨

Now you can:
- See all `$_SERVER` variables
- Check `BASE_PATH` constant
- Inspect the router
- Step through the entire page load!

### Exercise 2: Debug Helper Functions

1. **Open:** `app/Helpers/functions.php`
2. **Find function:** `app_base_url` (around line 99)
3. **Set breakpoint:** First line inside function
4. **Run:** `debug_test.php` (from Step 7)
5. **When it calls `app_base_url()`** → Breakpoint hits!
6. **Inspect:** See the `$path` parameter

### Exercise 3: Debug a Web Request

1. **Open:** `app/Core/Router.php`
2. **Set breakpoint:** Line 45 (in `dispatch()` method)
3. **Select:** "Listen for Xdebug"
4. **Press F5** (status bar turns orange)
5. **Open browser:** Visit any page
6. **VS Code pauses** and you can see:
   - What route was matched
   - What controller is being called
   - All request data

---

## 🎨 Understanding the Debug Interface

### Left Panel Sections:

**1. VARIABLES**
- All variables in current scope
- Superglobals ($_GET, $_POST, etc.)
- Local variables
- Constants

**2. WATCH**
- Custom expressions you want to monitor
- Add variables to track across breakpoints

**3. CALL STACK**
- Shows function call hierarchy
- Click entries to jump to that point
- See which function called which

**4. BREAKPOINTS**
- List of all breakpoints
- Toggle on/off with checkbox
- Right-click for conditional breakpoints

**5. LOADED SCRIPTS**
- All PHP files loaded
- Click to open file

---

## 💡 Pro Tips

### Tip 1: Conditional Breakpoints
1. Right-click on breakpoint (red dot)
2. Select "Edit Breakpoint"
3. Choose "Expression"
4. Enter condition: `$sum > 100`
5. Breakpoint only triggers when condition is true!

### Tip 2: Logpoints (Print Without Stopping)
1. Right-click line number
2. Select "Add Logpoint"
3. Enter message: `Value is {$name}`
4. Logs to Debug Console without stopping!

### Tip 3: Watch Expressions
Instead of just variables, watch:
```
count($array)
$user->getName()
isset($_SESSION['user_id'])
```

### Tip 4: Inspect Stack Trace
When paused:
1. Look at CALL STACK panel
2. See entire function call chain
3. Click any frame to see local variables at that point

### Tip 5: Hot Reload
After fixing code while debugging:
1. Save the file (Ctrl+S)
2. Press Ctrl+Shift+F5 (Restart)
3. Debug session restarts with new code

---

## 🐛 Common Issues & Solutions

### Issue: "Cannot connect to runtime process"
**Solution:**
1. Verify Xdebug installed: `php -m | grep xdebug`
2. Check status bar is orange (debugging active)
3. Restart VS Code
4. Restart Laragon

### Issue: Breakpoints are grey circles (not red)
**Solution:**
1. File not executed yet
2. Code might be unreachable
3. Try running the script first

### Issue: Variables panel empty
**Solution:**
1. Click "▶" to expand sections
2. Some variables only available in certain scopes
3. Use Watch panel to track specific variables

### Issue: Browser request doesn't trigger breakpoint
**Solution:**
1. Ensure "Listen for Xdebug" is selected
2. Press F5 to start listening (orange status bar)
3. Check firewall not blocking port 9003
4. Verify browser visited correct URL

---

## 📚 Debug Configurations Explained

### 1. Listen for Xdebug (Browser)
**Use for:** Debugging web pages
**How:** Start listening (F5), then visit page in browser

### 2. Launch currently open script
**Use for:** Debug the PHP file you have open
**How:** Open PHP file, press F5

### 3. Debug Bootstrap
**Use for:** Debug app initialization
**How:** Tests bootstrap loading

### 4. Debug Index (Homepage)
**Use for:** Debug homepage rendering
**How:** Simulates visiting homepage

### 5. Debug Test Script
**Use for:** Run test_fixes.php with debugging
**How:** Step through test suite

### 6-9. Other Configurations
Specialized configs for routes, helpers, database, etc.

---

## 🎓 Learning Path

### Beginner (Day 1)
- ✅ Install Xdebug
- ✅ Set simple breakpoints
- ✅ Use F10 (Step Over)
- ✅ Inspect variables

### Intermediate (Day 2-3)
- Use F11 (Step Into functions)
- Add Watch expressions
- Use Debug Console
- Debug web requests

### Advanced (Week 2)
- Conditional breakpoints
- Logpoints
- Multi-file debugging
- Performance profiling

---

## ✅ Debugging Checklist

Before starting a debug session:
- [ ] Xdebug installed and configured ✓
- [ ] VS Code open in project folder
- [ ] PHP Debug extension installed
- [ ] Debug panel open (Ctrl+Shift+D)
- [ ] Breakpoints set (red dots visible)
- [ ] Configuration selected from dropdown
- [ ] Ready to press F5!

---

## 🎯 Success Metrics

**You've mastered debugging when you can:**
- [ ] Set and remove breakpoints confidently
- [ ] Use F10/F11 to navigate code
- [ ] Inspect variables and arrays
- [ ] Debug web page requests
- [ ] Use Debug Console to test expressions
- [ ] Understand call stack
- [ ] Fix bugs faster than before! 🚀

---

## 🎉 Congratulations!

You are now ready to debug like a pro!

**Key Takeaways:**
- Press `Ctrl+Shift+D` to open debug panel
- Click line numbers to set breakpoints (red dots)
- Press `F5` to start debugging
- Use `F10` to step through code line by line
- Variables panel shows all data
- Debug Console lets you execute code

**Happy Debugging! 🐛🔍**

---

## 📞 Quick Reference Card

```
┌─────────────────────────────────────────────────────────┐
│          VS CODE DEBUG QUICK REFERENCE                  │
├─────────────────────────────────────────────────────────┤
│ Ctrl+Shift+D         Open Debug Panel                  │
│ F9                   Toggle Breakpoint                  │
│ F5                   Start/Continue                     │
│ F10                  Step Over (next line)              │
│ F11                  Step Into (enter function)         │
│ Shift+F11            Step Out (exit function)           │
│ Shift+F5             Stop Debugging                     │
│ Ctrl+Shift+F5        Restart                            │
├─────────────────────────────────────────────────────────┤
│ LEFT PANEL                                              │
│ • Variables          See all variables                  │
│ • Watch              Track custom expressions           │
│ • Call Stack         Function call hierarchy            │
│ • Breakpoints        Manage all breakpoints             │
├─────────────────────────────────────────────────────────┤
│ STATUS BAR                                              │
│ • Orange = Debugging Active                             │
│ • Blue = Normal Mode                                    │
└─────────────────────────────────────────────────────────┘
```

---

**Next Steps:**
1. Try the debug_test.php exercise above
2. Practice with breakpoints
3. Debug a real page request
4. Explore advanced features

**You're all set! Start debugging now! 🚀**

---

*Generated for Bishwo Calculator Project*
*Xdebug Version: 3.3.2*
*Status: Ready for Debugging ✓*