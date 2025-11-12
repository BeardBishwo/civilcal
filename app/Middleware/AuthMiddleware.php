<?php
namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware {
    public function handle($request, $next) {
        $user = Auth::check();
        if (!$user) {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $scriptDir = dirname($scriptName);
            if (substr($scriptDir, -7) === '/public') {
                $scriptDir = substr($scriptDir, 0, -7);
            }
            $basePath = ($scriptDir === '/' || $scriptDir === '') ? '' : $scriptDir;
            header('Location: ' . $basePath . '/login');
            exit;
        }
        return $next($request);
    }
}
