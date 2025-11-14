<?php
/**
 * Front Controller - Routes requests internally to public/index.php
 * This allows clean URLs without /public in the browser
 */

// Internal routing - include public front controller without redirect
require_once __DIR__ . '/public/index.php';
exit;
?>

