<?php
// Load environment variables from .env file if it exists
function loadConfigEnv() {
    $envFile = __DIR__ . '/../.env';
    if (!file_exists($envFile)) {
        return false;
    }
    
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
    return true;
}

// Load environment variables
loadConfigEnv();

return [
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost',
    'database' => $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? 'bishwo_calculator',
    'username' => $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
];
