<?php
/**
 * Legacy Logout Redirect
 * This file redirects old logout.php links to the proper logout route
 */

// Redirect to the proper logout route
header('Location: /logout');
exit;
?>
