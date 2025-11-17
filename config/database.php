<?php
// Database Configuration
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
?>
