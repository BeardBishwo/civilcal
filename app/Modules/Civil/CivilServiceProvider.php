<?php
namespace App\Modules\Civil;

use App\Modules\BaseProvider;

class CivilServiceProvider extends BaseProvider
{
    public function register($router): void
    {
        // Register Civil module health endpoint
        $router->add('GET', '/api/v1/civil/health', 'Api\\Civil\\StatusController@health');
    }
}
