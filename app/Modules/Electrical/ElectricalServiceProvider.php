<?php
namespace App\Modules\Electrical;

use App\Modules\BaseProvider;

class ElectricalServiceProvider extends BaseProvider
{
    public function register($router): void
    {
        // Register Electrical module health endpoint
        $router->add('GET', '/api/v1/electrical/health', 'Api\\Electrical\\StatusController@health');
    }
}
