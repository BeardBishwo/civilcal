<?php
// Test if the image file exists and is readable
$imagePath = __DIR__ . '/public/uploads/settings/logo.png';
echo "Image path: " . $imagePath . "\n";
echo "File exists: " . (file_exists($imagePath) ? 'YES' : 'NO') . "\n";
echo "File readable: " . (is_readable($imagePath) ? 'YES' : 'NO') . "\n";

if (file_exists($imagePath)) {
    echo "File size: " . filesize($imagePath) . " bytes\n";
    echo "File permissions: " . substr(sprintf('%o', fileperms($imagePath)), -4) . "\n";
}

// Test the URL
$url = 'http://localhost/Bishwo_Calculator/uploads/settings/logo.png';
echo "Testing URL: " . $url . "\n";

// Try to get headers
$headers = @get_headers($url);
if ($headers) {
    echo "HTTP Headers:\n";
    foreach ($headers as $header) {
        echo "  " . $header . "\n";
    }
} else {
    echo "Could not get headers - URL might be inaccessible\n";
}