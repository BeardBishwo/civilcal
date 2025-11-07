<?php
/**
 * Dev logger helper
 * Writes simple debug logs to db/debug_<channel>.log when not in production or request is localhost.
 */
require_once __DIR__ . '/config.php';

function dev_log_allowed(): bool {
    // Allow logging on non-production environments or when request comes from localhost
    $remote = $_SERVER['REMOTE_ADDR'] ?? 'cli';
    if (ENVIRONMENT !== 'production') return true;
    return in_array($remote, ['127.0.0.1', '::1']);
}

function dev_log(string $channel, $message, array $context = []): void {
    try {
        if (!dev_log_allowed()) return;
        $dir = __DIR__ . '/../db';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $file = $dir . '/debug_' . preg_replace('/[^a-z0-9_\-]/i', '_', $channel) . '.log';

        $entry = '[' . date('Y-m-d H:i:s') . ']';
        $entry .= ' REMOTE=' . ($_SERVER['REMOTE_ADDR'] ?? 'cli');
        $entry .= ' URI=' . ($_SERVER['REQUEST_URI'] ?? '');
        $entry .= ' PID=' . getmypid();
        $entry .= ' MESSAGE=' . (is_string($message) ? $message : json_encode($message));
        if (!empty($context)) {
            $entry .= ' CONTEXT=' . json_encode($context);
        }
        $entry .= PHP_EOL;

        // Rotate if bigger than 2MB
        if (file_exists($file) && filesize($file) > 2 * 1024 * 1024) {
            @rename($file, $file . '.' . time());
        }

        @file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
    } catch (Throwable $e) {
        // swallow errors - dev logging must not break the app
    }
}

?>
