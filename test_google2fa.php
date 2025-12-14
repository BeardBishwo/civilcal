<?php
require_once __DIR__ . '/vendor/autoload.php';

echo "Testing Google2FA Library...\n\n";

try {
    // Check if class exists
    if (!class_exists('\\PragmaRX\\Google2FA\\Google2FA')) {
        die("ERROR: Google2FA class not found!\n");
    }
    echo "✓ Google2FA class found\n";
    
    // Create instance
    $google2fa = new \PragmaRX\Google2FA\Google2FA();
    echo "✓ Google2FA instance created\n";
    
    // Generate secret
    $secret = $google2fa->generateSecretKey();
    echo "✓ Secret generated: " . $secret . "\n";
    
    // Generate QR URL
    $qrUrl = $google2fa->getQRCodeUrl('Test App', 'test@example.com', $secret);
    echo "✓ QR URL generated: " . substr($qrUrl, 0, 50) . "...\n";
    
    echo "\n✅ All tests passed! Google2FA is working correctly.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
