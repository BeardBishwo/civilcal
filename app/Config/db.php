<?php
require_once __DIR__ . '/config.php';

/**
 * Return a PDO connection using the project's config constants.
 * Usage: $pdo = get_db();
 */
function get_db(){
    static $pdo = null;
    if ($pdo) return $pdo;
    $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        // Fail gracefully for now - API endpoints will return JSON errors
        error_log('DB connection error: ' . $e->getMessage());
        // Write a small developer-friendly log (do NOT expose this in production)
        try {
            $errFile = __DIR__ . '/../db/last_db_error.log';
            $entry = '[' . date('Y-m-d H:i:s') . '] DB connection error: ' . $e->getMessage() . PHP_EOL;
            $entry .= $e->getTraceAsString() . PHP_EOL . PHP_EOL;
            // Append with exclusive lock
            @file_put_contents($errFile, $entry, FILE_APPEND | LOCK_EX);
        } catch (Exception $ex) {
            // ignore logging failure
        }
        // Dev: optionally send an alert email when DB connection fails (dev-only and if mail enabled)
        try {
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production' && defined('MAIL_ENABLED') && MAIL_ENABLED && function_exists('mail')) {
                $to = getenv('MAIL_TO') ?: getenv('MAIL_FROM') ?: 'admin@example.com';
                if ($to) {
                    $subject = 'Dev Alert: DB connection failed on ' . ($_SERVER['HTTP_HOST'] ?? 'local');
                    $body = "A database connection failure occurred:\n\n" . $e->getMessage() . "\n\nTrace:\n" . $e->getTraceAsString();
                    @mail($to, $subject, $body, 'From: ' . (defined('MAIL_FROM') ? MAIL_FROM : 'noreply@example.com'));
                }
            }
        } catch (Exception $mailEx) {
            // ignore
        }
        return null;
    }
    return $pdo;
}

?>
