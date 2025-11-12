<?php
namespace App\Middleware;

use App\Core\Auth;

class GuestMiddleware {
    public function handle($request, $next) {
        if (Auth::check()) {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $scriptDir = dirname($scriptName);
            if (substr($scriptDir, -7) === '/public') {
                $scriptDir = substr($scriptDir, 0, -7);
            }
            $basePath = ($scriptDir === '/' || $scriptDir === '') ? '' : $scriptDir;
            header('Location: ' . $basePath . '/dashboard');
            exit;
        }
        return $next($request);
    }
}
