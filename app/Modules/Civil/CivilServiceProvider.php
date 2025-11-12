<?php
namespace App\Modules\Civil;

use App\Modules\BaseProvider;

class CivilServiceProvider extends BaseProvider
{
    public function register($router): void
    {
        // Register Civil module specific routes here in future
        // Example (when controller exists):
        // $router->add('GET', '/api/v1/civil/health', 'Api\\Civil\\StatusController@health');
    }
}
