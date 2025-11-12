<?php
namespace App\Modules\Hvac;

use App\Modules\BaseProvider;

class HvacServiceProvider extends BaseProvider
{
    public function register($router): void
    {
        // Register HVAC module health endpoint
        $router->add('GET', '/api/v1/hvac/health', 'Api\\Hvac\\StatusController@health');
    }
}
