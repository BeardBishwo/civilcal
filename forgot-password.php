<?php
/**
 * Direct Forgot Password Page - Bypasses routing issues
 */

define('BISHWO_CALCULATOR', true);

// Include the existing forgot password view directly
include __DIR__ . '/themes/default/views/auth/forgot.php';
?>
