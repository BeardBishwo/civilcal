<?php
namespace App\Services;

class AuditLogger
{
    private static function logPath(): string
    {
        $base = defined('STORAGE_PATH') ? STORAGE_PATH : (defined('BASE_PATH') ? BASE_PATH . '/storage' : __DIR__ . '/../../storage');
        $dir = rtrim($base, '/\\') . '/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir . '/audit-' . date('Y-m-d') . '.log';
    }

    private static function context(array $extra = []): array
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        return array_filter([
            'ip' => $ip,
            'ua' => $ua,
            'user_id' => $userId,
        ]) + $extra;
    }

    private static function write(string $level, string $action, array $details = []): void
    {
        $record = [
            'ts' => date('c'),
            'level' => strtoupper($level),
            'action' => $action,
            'details' => self::context($details),
        ];
        $line = json_encode($record, JSON_UNESCAPED_SLASHES) . PHP_EOL;
        @file_put_contents(self::logPath(), $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $action, array $details = []): void { self::write('info', $action, $details); }
    public static function warning(string $action, array $details = []): void { self::write('warning', $action, $details); }
    public static function error(string $action, array $details = []): void { self::write('error', $action, $details); }
}
