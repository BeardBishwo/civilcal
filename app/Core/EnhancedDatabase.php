<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Enhanced Database class with connection pooling, error handling, and performance optimization
 * Fixes path issues and provides robust database connectivity
 */
class EnhancedDatabase
{
    private static ?EnhancedDatabase $instance = null;
    private ?PDO $pdo = null;
    private int $connectionCount = 0;
    private array $config = [];

    private function __construct()
    {
        $this->loadConfiguration();
        $this->connect();
    }

    /**
     * Load database configuration from multiple sources
     */
    private function loadConfiguration(): void
    {
        // Try multiple configuration sources
        $configPaths = [
            __DIR__ . '/../../config/database.php',
            BASE_PATH . '/config/database.php',
            __DIR__ . '/../Config/config.php',
            BASE_PATH . '/app/Config/db.php'
        ];

        foreach ($configPaths as $configPath) {
            if (file_exists($configPath)) {
                $this->config = $this->parseConfiguration($configPath);
                if (!empty($this->config)) {
                    return;
                }
            }
        }

        // Fallback to environment variables
        $this->config = [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'database' => getenv('DB_DATABASE') ?: 'bishwo_calculator',
            'username' => getenv('DB_USERNAME') ?: 'root',
            'password' => getenv('DB_PASSWORD') ?: '',
            'charset' => 'utf8mb4',
            'port' => 3306
        ];
    }

    /**
     * Parse configuration file
     */
    private function parseConfiguration(string $configPath): array
    {
        try {
            if (strpos($configPath, 'database.php') !== false) {
                $config = include $configPath;
                if (is_array($config)) {
                    return [
                        'host' => $config['host'] ?? 'localhost',
                        'database' => $config['database'] ?? 'bishwo_calculator',
                        'username' => $config['username'] ?? $config['user'] ?? 'root',
                        'password' => $config['password'] ?? '',
                        'charset' => $config['charset'] ?? 'utf8mb4',
                        'port' => $config['port'] ?? 3306
                    ];
                }
            } elseif (strpos($configPath, 'db.php') !== false || strpos($configPath, 'config.php') !== false) {
                // Legacy db.php or config.php format using constants
                // Ensure config.php is included if not already
                if (strpos($configPath, 'config.php') !== false && !defined('DB_HOST')) {
                    include_once $configPath;
                }

                return [
                    'host' => defined('DB_HOST') ? DB_HOST : 'localhost',
                    'database' => defined('DB_NAME') ? DB_NAME : 'bishwo_calculator',
                    'username' => defined('DB_USER') ? DB_USER : 'root',
                    'password' => defined('DB_PASS') ? DB_PASS : '',
                    'charset' => 'utf8mb4',
                    'port' => 3306
                ];
            }
        } catch (Exception $e) {
            error_log("Failed to parse config file {$configPath}: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Establish database connection with retry logic
     */
    private function connect(): void
    {
        $maxRetries = 3;
        $retryDelay = 1; // seconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $this->pdo = $this->createConnection();
                $this->connectionCount++;
                $this->validateConnection();
                return;
            } catch (PDOException $e) {
                error_log("Database connection attempt {$attempt} failed: " . $e->getMessage());

                if ($attempt === $maxRetries) {
                    $this->handleConnectionFailure($e);
                }

                sleep($retryDelay);
                $retryDelay *= 2; // Exponential backoff
            }
        }
    }

    /**
     * Create PDO connection with optimized settings
     */
    private function createConnection(): PDO
    {
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            $this->config['host'],
            $this->config['database'],
            $this->config['charset']
        );

        // Add port if not default
        if ($this->config['port'] != 3306) {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $this->config['host'],
                $this->config['port'],
                $this->config['database'],
                $this->config['charset']
            );
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 10,
            PDO::ATTR_PERSISTENT => true, // Connection pooling
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_AUTOCOMMIT => true
        ];

        return new PDO($dsn, $this->config['username'], $this->config['password'], $options);
    }

    /**
     * Validate database connection
     */
    private function validateConnection(): void
    {
        try {
            $this->pdo->query('SELECT 1');
        } catch (PDOException $e) {
            throw new \Exception("Database validation failed: " . $e->getMessage());
        }
    }

    /**
     * Handle connection failure
     */
    private function handleConnectionFailure(PDOException $e): void
    {
        $errorMessage = "Database connection failed after {$this->connectionCount} attempts: " . $e->getMessage();

        // Log detailed error information
        error_log($errorMessage);
        error_log("Database config: " . json_encode([
            'host' => $this->config['host'],
            'database' => $this->config['database'],
            'username' => $this->config['username'],
            'charset' => $this->config['charset']
        ]));

        // Create detailed error log file
        $errorLog = BASE_PATH . '/debug/logs/database_errors.log';
        if (!file_exists(dirname($errorLog))) {
            mkdir(dirname($errorLog), 0755, true);
        }

        $errorDetails = [
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => $errorMessage,
            'config' => $this->config,
            'trace' => $e->getTraceAsString()
        ];

        file_put_contents($errorLog, json_encode($errorDetails, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

        throw new \Exception("Unable to connect to database. Check configuration and database server.");
    }

    /**
     * Get database instance (singleton)
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     */
    public function getConnection(): PDO
    {
        if (!$this->pdo || !$this->isConnectionAlive()) {
            $this->reconnect();
        }
        return $this->pdo;
    }

    /**
     * Check if connection is still alive
     */
    private function isConnectionAlive(): bool
    {
        try {
            $this->pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Reconnect to database
     */
    private function reconnect(): void
    {
        try {
            $this->pdo = null;
            $this->connect();
        } catch (\Exception $e) {
            error_log("Database reconnection failed: " . $e->getMessage());
            throw new \Exception("Failed to reconnect to database");
        }
    }

    /**
     * Execute query with error handling
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query execution failed: " . $e->getMessage());
            error_log("SQL: {$sql}, Params: " . json_encode($params));
            throw new \Exception("Database query failed");
        }
    }

    /**
     * Execute select query and return results
     */
    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute select query and return single result
     */
    public function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Execute insert query
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * Execute update query
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): bool
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $set);

        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);

        $stmt = $this->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    /**
     * Execute delete query
     */
    public function delete(string $table, string $where, array $whereParams = []): bool
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount() > 0;
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId(): string
    {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->getConnection()->rollBack();
    }

    /**
     * Get connection statistics
     */
    public function getConnectionStats(): array
    {
        return [
            'connection_count' => $this->connectionCount,
            'is_alive' => $this->isConnectionAlive(),
            'config' => [
                'host' => $this->config['host'],
                'database' => $this->config['database'],
                'charset' => $this->config['charset']
            ]
        ];
    }

    /**
     * Test database connection
     */
    public static function testConnection(): array
    {
        try {
            $db = self::getInstance();
            $stats = $db->getConnectionStats();

            return [
                'success' => true,
                'message' => 'Database connection successful',
                'stats' => $stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
