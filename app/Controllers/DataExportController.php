<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\DataExportService;
use Exception;

/**
 * Data Export Controller (GDPR Compliance)
 */
class DataExportController extends Controller
{
    private $exportService;
    
    public function __construct()
    {
        parent::__construct();
        $this->exportService = new DataExportService();
    }
    
    /**
     * Request data export
     */
    public function requestExport()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                $this->json(['error' => 'Not authenticated'], 401);
                return;
            }
            
            $requestId = $this->exportService->requestExport($userId);
            
            $this->json([
                'success' => true,
                'message' => 'Your data export has been requested. You will be able to download it once processing is complete.',
                'request_id' => $requestId
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Get export requests
     */
    public function getRequests()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                $this->json(['error' => 'Not authenticated'], 401);
                return;
            }
            
            $requests = $this->exportService->getExportRequests($userId);
            
            $this->json([
                'success' => true,
                'requests' => $requests
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Download export file
     */
    public function download($requestId)
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                $_SESSION['flash_messages']['error'] = 'Not authenticated';
                $this->redirect('/login');
                return;
            }
            
            $filePath = $this->exportService->downloadExport($requestId, $userId);
            
            // Send file to browser
            $filename = basename($filePath);
            
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: public');
            
            readfile($filePath);
            exit;
            
        } catch (Exception $e) {
            $_SESSION['flash_messages']['error'] = $e->getMessage();
            $this->redirect('/user/profile');
        }
    }
}
