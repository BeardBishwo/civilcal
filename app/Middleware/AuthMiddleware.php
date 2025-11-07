<?php
namespace App\Middleware;

use App\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface {
    public function handle(): bool {
        // Authentication logic
        return true;
    }
}
