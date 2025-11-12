<?php
namespace App\Controllers\Api\Civil;

class StatusController
{
    public function health()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'module' => 'civil',
            'status' => 'ok',
            'timestamp' => date('c')
        ]);
        return;
    }
}
