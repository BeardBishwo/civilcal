<?php
namespace App\Controllers\Api\Electrical;

class StatusController
{
    public function health()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'module' => 'electrical',
            'status' => 'ok',
            'timestamp' => date('c')
        ]);
        return;
    }
}
