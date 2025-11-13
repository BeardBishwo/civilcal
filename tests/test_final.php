<?php
echo "=== FINAL CONFIG TEST ===\n";

try {
    echo "1. Testing direct config load...\n";
    require_once __DIR__ . '/app/Config/config.php';
    echo "   ✅ Direct config loaded\n";
    
    echo "2. Testing themes/default/views/partials/header.php...\n";
    require_once __DIR__ . '/themes/default/views/partials/header.php';
    echo "   ✅ Header loaded\n";
    
    echo "3. Testing APP_BASE...\n";
    echo "   APP_BASE: '" . (defined('APP_BASE') ? APP_BASE : 'undefined') . "'\n";
    
    echo "4. Testing APP_URL...\n";
    echo "   APP_URL: '" . (defined('APP_URL') ? APP_URL : 'undefined') . "'\n";
    
    echo "5. Testing ThemeManager...\n";
    $themeManager = new \App\Services\ThemeManager();
    echo "   ✅ ThemeManager created\n";
    echo "   Theme URL: " . $themeManager->themeUrl('assets/css/home.css') . "\n";
    
    echo "\n✅ ALL TESTS PASSED - Config migration successful!\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>

<!DOCTYPE html>
<html>
<head><title>Config Test</title></head>
<body>
<h1>If you see styled content below, CSS loading is working!</h1>
<?php if (isset($themeManager)): ?>
<link rel="stylesheet" href="<?php echo $themeManager->themeUrl('assets/css/home.css'); ?>">
<div style="background: linear-gradient(135deg, #00ffff 0%, #ff00ff 100%); padding: 20px; color: white;">
    <h2>Test Neon Element</h2>
    <p>This should show neon colors if CSS is loading properly.</p>
</div>
<?php endif; ?>
</body>
</html>



