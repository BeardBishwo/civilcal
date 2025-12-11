<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\NotificationPreference;

class NotificationPreferencesController extends Controller
{
    private $preferenceModel;

    public function __construct()
    {
        parent::__construct();
        $this->preferenceModel = new NotificationPreference();
    }

    /**
     * Show preferences page
     */
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            redirect('/login');
            return;
        }

        $preferences = $this->preferenceModel->getUserPreferences($userId);
        
        // Render view
        $title = 'Notification Preferences';
        require __DIR__ . '/../../themes/admin/views/notifications/preferences.php';
    }

    /**
     * Get preferences (AJAX)
     */
    public function get()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $preferences = $this->preferenceModel->getUserPreferences($userId);
        
        echo json_encode([
            'success' => true,
            'preferences' => $preferences
        ]);
    }

    /**
     * Update preferences (AJAX)
     */
    public function update()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $result = $this->preferenceModel->updatePreferences($userId, $input);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Preferences updated successfully' : 'Failed to update preferences'
        ]);
    }
}
