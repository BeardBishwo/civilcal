<?php
echo "PHP is working!";
echo "\nPHP Version: " . phpversion();
echo "\nServer: " . $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
echo "\nRequest Method: " . $_SERVER['REQUEST_METHOD'];
?>
