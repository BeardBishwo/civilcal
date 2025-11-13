<?php
// Test the router to see what URI is being detected
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/bishwo_calculator/public/';
$_SERVER['SCRIPT_NAME'] = '/bishwo_calculator/public/index.php';

require_once 'app/bootstrap.php';
require_once 'app/Core/Router.php';

$router = new \App\Core\Router();
require_once 'app/routes.php';

// Debug: show detected base path
$basePath = $router->getBasePath();
echo "Detected base path: " . ($basePath ? "'$basePath'" : "null") . "\n";

// Debug: show URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "Request URI: '$uri'\n";

// Check if base path exists in URI
if ($basePath && strpos($uri, $basePath) === 0) {
    $cleanUri = substr($uri, strlen($basePath));
    echo "Cleaned URI: '" . ($cleanUri ?: '/') . "'\n";
} else {
    echo "No base path found in URI\n";
}

// Test direct home route
echo "\nTesting direct home route: \n";
foreach ($router->routes as $route) {
    if ($route['uri'] === '/') {
        echo "Found home route: " . $route['controller'] . "\n";
        break;
    }
}
?>


