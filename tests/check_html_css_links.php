<?php
/**
 * Check HTML CSS Links
 */

// Suppress output buffering issues
if (ob_get_level()) ob_end_clean();

echo "=== CHECKING HTML CSS LINKS ===\n\n";

// Set minimal environment
$_SERVER['REQUEST_URI'] = '/bishwo_calculator/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/bishwo_calculator/index.php';

// Get the homepage HTML
$url = 'http://localhost/bishwo_calculator/';
$html = @file_get_contents($url);

if (!$html) {
    echo "Cannot fetch homepage. Trying via CLI simulation...\n\n";
    
    // Try direct include
    $_GET = [];
    $_POST = [];
    $_COOKIE = [];
    
    ob_start();
    try {
        require __DIR__ . '/../public/index.php';
        $html = ob_get_clean();
    } catch (Exception $e) {
        ob_end_clean();
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "HTML fetched: " . strlen($html) . " bytes\n\n";

// Extract CSS links
preg_match_all('/<link[^>]*rel="stylesheet"[^>]*href="([^"]*)"/', $html, $matches);

echo "CSS LINKS FOUND: " . count($matches[1]) . "\n";
echo str_repeat("-", 80) . "\n";

foreach ($matches[1] as $i => $url) {
    echo ($i + 1) . ". " . $url . "\n";
}

echo "\n";
echo "CHECKING IF CSS FILES ARE ACCESSIBLE:\n";
echo str_repeat("-", 80) . "\n";

foreach ($matches[1] as $url) {
    // Convert relative URL to absolute if needed
    if (strpos($url, 'http') !== 0) {
        $fullUrl = 'http://localhost' . $url;
    } else {
        $fullUrl = $url;
    }
    
    $content = @file_get_contents($fullUrl);
    if ($content !== false) {
        echo "✓ " . $url . " (" . strlen($content) . " bytes)\n";
    } else {
        echo "✗ " . $url . " - NOT ACCESSIBLE\n";
    }
}

echo "\n";
echo "CHECKING HTML BODY CLASS:\n";
echo str_repeat("-", 80) . "\n";

if (preg_match('/<body[^>]*class="([^"]*)"/', $html, $match)) {
    echo "Body class: " . $match[1] . "\n";
} else {
    echo "No body class found\n";
}

echo "\n";
echo "CHECKING FOR HERO SECTION:\n";
echo str_repeat("-", 80) . "\n";

if (preg_match('/<div[^>]*class="[^"]*hero[^"]*"[^>]*>/i', $html, $match)) {
    echo "✓ Hero div found: " . $match[0] . "\n";
} else {
    echo "✗ Hero div not found\n";
}

?>
