<?php
namespace App\Controllers\Api\Hvac;

class StatusController
{
    public function health()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'module' => 'hvac',
            'status' => 'ok',
            'timestamp' => date('c')
        ]);
        return;
    }
}
