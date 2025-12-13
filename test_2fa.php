<?php
require __DIR__ . '/vendor/autoload.php';

use PragmaRX\Google2FA\Google2FA;

try {
    $google2fa = new Google2FA();
    echo "Google2FA is available. Secret: " . $google2fa->generateSecretKey();
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
