<?php
namespace App\Services;

class Logger
{
    private static function logDir(): string
    {
        $base = defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 2) . '/storage';
        return $base . '/logs';
    }

    private static function ensureDir(): void
    {
        $dir = self::logDir();
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    private static function logPath(): string
    {
        self::ensureDir();
        return self::logDir() . '/' . date('Y-m-d') . '.log';
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => strtolower($level),
            'message' => $message,
            'context' => $context
        ];
        @file_put_contents(self::logPath(), json_encode($entry, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('info', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('warning', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('error', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log('debug', $message, $context);
    }

    public static function exception(\Throwable $e, array $context = []): void
    {
        $ctx = array_merge($context, [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
            'trace' => $e->getTrace()
        ]);
        self::error($e->getMessage(), $ctx);
    }
}
