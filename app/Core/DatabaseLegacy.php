<?php
/**
 * Database class for EngiCal Pro authentication system
 * Provides a secure connection to the MySQL database using PDO
 */
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;
    public $conn;
    /**
     * Singleton instance
     * @var Database|null
     */
    private static $instance = null;

    /**
     * Constructor - initialize database connection parameters
     */
    public function __construct() {
        // Load .env file if it exists
        $this->loadEnv();
        
        // Use environment variables with fallbacks to config.php constants
        $this->host = getenv('DB_HOST') ?: (defined('DB_HOST') ? DB_HOST : '127.0.0.1');
        $this->db_name = getenv('DB_DATABASE') ?: (defined('DB_NAME') ? DB_NAME : 'aec_calculator');
        $this->username = getenv('DB_USERNAME') ?: (defined('DB_USER') ? DB_USER : 'root');
        $this->password = getenv('DB_PASSWORD') ?: (defined('DB_PASS') ? DB_PASS : '');
        $this->charset = 'utf8mb4';
    }
    
    /**
     * Load environment variables from .env file
     */
    private function loadEnv() {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue; // Skip comments
                }
                if (strpos($line, '=') === false) {
                    continue; // Skip invalid lines
                }
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                if (!empty($name)) {
                    putenv("$name=$value");
                }
            }
        }
    }

    /**
     * Get database connection
     * @return PDO|null Database connection object or null on failure
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            // Log error but don't expose details to user
            error_log('Database connection error: ' . $e->getMessage());
            $this->conn = null;
        }
        
        return $this->conn;
    }

    /**
     * Return singleton instance of Database
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Backwards-compatible alias used across the codebase
     * @return PDO|null
     */
    public function getPdo()
    {
        return $this->getConnectionObject();
    }

    /**
     * Test database connection
     * @return bool True if connection successful, false otherwise
     */
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                $stmt = $conn->query('SELECT 1');
                return $stmt !== false;
            }
        } catch (Exception $e) {
            error_log('Database test connection error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Get the current connection
     * @return PDO|null
     */
    public function getConnectionObject() {
        if ($this->conn === null) {
            return $this->getConnection();
        }
        return $this->conn;
    }

    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }

    /**
     * Execute a prepared statement with error handling
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return PDOStatement|false Statement object or false on failure
     */
    public function executeQuery($sql, $params = []) {
        try {
            $conn = $this->getConnectionObject();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
            
        } catch (PDOException $e) {
            error_log('Query execution error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            return false;
        } catch (Exception $e) {
            error_log('General query error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Begin a database transaction
     * @return bool True if transaction started successfully, false otherwise
     */
    public function beginTransaction() {
        try {
            $conn = $this->getConnectionObject();
            if ($conn) {
                return $conn->beginTransaction();
            }
        } catch (Exception $e) {
            error_log('Begin transaction error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Commit a database transaction
     * @return bool True if transaction committed successfully, false otherwise
     */
    public function commit() {
        try {
            $conn = $this->getConnectionObject();
            if ($conn) {
                return $conn->commit();
            }
        } catch (Exception $e) {
            error_log('Commit transaction error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Rollback a database transaction
     * @return bool True if transaction rolled back successfully, false otherwise
     */
    public function rollback() {
        try {
            $conn = $this->getConnectionObject();
            if ($conn) {
                return $conn->rollback();
            }
        } catch (Exception $e) {
            error_log('Rollback transaction error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Get the last inserted ID
     * @return string Last inserted ID
     */
    public function lastInsertId() {
        try {
            $conn = $this->getConnectionObject();
            if ($conn) {
                return $conn->lastInsertId();
            }
        } catch (Exception $e) {
            error_log('Last insert ID error: ' . $e->getMessage());
        }
        return 0;
    }

    /**
     * Compatibility: provide a mysqli-like prepare() wrapper that returns
     * a lightweight statement object with bind_param and get_result methods.
     * This helps legacy modules that expect mysqli-style API.
     * @param string $sql
     * @return PDOPreparedStatementWrapper|false
     */
    public function prepare($sql)
    {
        $conn = $this->getConnectionObject();
        if (!$conn) {
            return false;
        }
        // Load compatibility class if available
        if (!class_exists('PDOPreparedStatementWrapper')) {
            $compatFile = __DIR__ . '/pdo_mysqli_compat.php';
            if (file_exists($compatFile)) {
                require_once $compatFile;
            }
        }
        if (class_exists('PDOPreparedStatementWrapper')) {
            return new PDOPreparedStatementWrapper($conn, $sql);
        }
        // Fallback to native PDO prepare
        return $conn->prepare($sql);
    }

    /**
     * Magic getter to support legacy $db->insert_id usage
     */
    public function __get($name)
    {
        if ($name === 'insert_id') {
            return $this->lastInsertId();
        }
        return null;
    }
}
?>
