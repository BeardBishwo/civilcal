<?php

namespace App\Services;

use Sentry\SentrySdk;
use Sentry\State\Scope;

/**
 * Monitoring Service
 * 
 * Handles APM and Error Tracking initialization (Sentry).
 */
class MonitoringService
{
    private static $initialized = false;

    /**
     * Initialize Monitoring (Sentry)
     */
    public static function init()
    {
        if (self::$initialized) {
            return;
        }

        $dsn = $_ENV['SENTRY_DSN'] ?? null;
        $env = $_ENV['APP_ENV'] ?? 'production';

        // Only init if DSN is set and valid
        if ($dsn && filter_var($dsn, FILTER_VALIDATE_URL) && $dsn !== 'https://examplePublicKey@o0.ingest.sentry.io/0') {
            \Sentry\init([
                'dsn' => $dsn,
                'environment' => $env,
                'traces_sample_rate' => 1.0, // Adjust for production volume
                'release' => '1.0.0', // Could be dynamic from git
            ]);
            self::$initialized = true;
        }
    }

    /**
     * Capture an exception manually
     */
    public static function captureException(\Throwable $e)
    {
        if (self::$initialized) {
            \Sentry\captureException($e);
        }
        
        // Also log locally
        \App\Core\Logger::error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    /**
     * Add context to current scope (User info, tags)
     */
    public static function addContext(array $user = null, array $tags = [])
    {
        if (!self::$initialized) {
            return;
        }

        \Sentry\configureScope(function (Scope $scope) use ($user, $tags) {
            if ($user) {
                $scope->setUser([
                    'id' => $user['id'],
                    'email' => $user['email'] ?? null,
                    // Avoid sending sensitive PII if possible
                ]);
            }
            
            foreach ($tags as $key => $value) {
                $scope->setTag($key, $value);
            }
        });
    }
}
