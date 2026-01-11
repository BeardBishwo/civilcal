<?php

namespace App\Core;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Logger Wrapper
 * 
 * Centralized logging service using Monolog.
 * Provides static methods for logging at various levels to different channels.
 */
class Logger
{
    private static $loggers = [];

    /**
     * Get a logger instance for a specific channel
     * 
     * @param string $channel Channel name (app, security, payment, etc.)
     * @return MonologLogger
     */
    public static function get($channel = 'app')
    {
        if (!isset(self::$loggers[$channel])) {
            self::$loggers[$channel] = self::createLogger($channel);
        }
        return self::$loggers[$channel];
    }

    /**
     * Create a new logger instance
     */
    private static function createLogger($channel)
    {
        $logger = new MonologLogger($channel);
        
        $logPath = STORAGE_PATH . '/logs/' . $channel . '.log';
        $today = date('Y-m-d');
        
        // Main log file (daily rotation logic could be added here or via RotatingFileHandler)
        // For simplicity, we use a single file per channel or could suffix with date
        // Ideally: new \Monolog\Handler\RotatingFileHandler($logPath, 30, MonologLogger::DEBUG)
        
        $handler = new StreamHandler($logPath, MonologLogger::DEBUG);
        
        // Custom format
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $handler->setFormatter($formatter);
        
        $logger->pushHandler($handler);

        // Security channel might want immediate email alerts in production
        // if ($channel === 'security' && APP_ENV === 'production') { ... }

        return $logger;
    }

    // Static facades for the 'app' channel (default)
    
    public static function info($message, array $context = [])
    {
        self::get('app')->info($message, $context);
    }

    public static function error($message, array $context = [])
    {
        self::get('app')->error($message, $context);
    }

    public static function warning($message, array $context = [])
    {
        self::get('app')->warning($message, $context);
    }

    public static function debug($message, array $context = [])
    {
        self::get('app')->debug($message, $context);
    }

    // Facades for 'security' channel
    public static function security($message, array $context = [])
    {
        self::get('security')->warning($message, $context);
    }
}
