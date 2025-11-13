<?php
require_once __DIR__ . '/app/bootstrap.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>CSS Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; }
        .working { background: #d4edda; }
        .broken { background: #f8d7da; }
    </style>
</head>
<body>
    <h1>CSS Loading Test</h1>
    
    <div class="debug">
        <strong>Domain:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'unknown'; ?><br>
        <strong>APP_BASE:</strong> "<?php echo defined('APP_BASE') ? APP_BASE : 'not defined'; ?>"<br>
        <strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'unknown'; ?>
    </div>

    <?php
    try {
        $themeManager = new \App\Services\ThemeManager();
        $cssUrl = $themeManager->themeUrl('assets/css/home.css');
        echo "<div class='debug'>";
        echo "<strong>Generated CSS URL:</strong> <a href='$cssUrl' target='_blank'>$cssUrl</a><br>";
        
        // Test if CSS file is accessible
        $cssPath = BASE_PATH . '/themes/default/assets/css/home.css';
        if (file_exists($cssPath)) {
            echo "<span class='working'>✅ CSS file exists on server</span><br>";
            echo "<strong>File size:</strong> " . number_format(filesize($cssPath)) . " bytes<br>";
        } else {
            echo "<span class='broken'>❌ CSS file not found on server</span><br>";
        }
        echo "</div>";
        
        // Try to load the CSS
        echo "<link rel='stylesheet' href='$cssUrl'>";
        echo "<div class='debug'>CSS link tag added to page</div>";
        
    } catch (Exception $e) {
        echo "<div class='debug broken'>Error: " . $e->getMessage() . "</div>";
    }
    ?>

    <div style="background: linear-gradient(135deg, #00ffff 0%, #ff00ff 100%); padding: 20px; color: white; margin-top: 20px;">
        <h2>Test Element</h2>
        <p>If you see this with neon colors, CSS is working!</p>
    </div>

</body>
</html>
