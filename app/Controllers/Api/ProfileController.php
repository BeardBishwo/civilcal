<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\User;
use Exception;

/**
 * API Profile Controller
 * Handles user profile API operations with JSON responses
 */
class ProfileController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Get user profile (JSON)
     */
    public function index()
    {
        header('Content-Type: application/json');
        
        try {
            $userId = $this->getCurrentUserId();
            $user = $this->userModel->find($userId);
            
            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }
            
            $stats = $this->userModel->getStatistics($userId);
            $profileCompletion = $this->userModel->getProfileCompletion($userId);
            
            echo json_encode([
                'username' => $user['username'] ?? '',
                'email' => $user['email'] ?? '',
                'first_name' => $user['first_name'] ?? '',
                'last_name' => $user['last_name'] ?? '',
                'phone' => $user['phone'] ?? '',
                'bio' => $user['bio'] ?? '',
                'location' => $user['location'] ?? '',
                'company' => $user['company'] ?? '',
                'avatar' => $user['avatar'] ?? null,
                'statistics' => $stats,
                'profile_completion' => $profileCompletion
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update user profile (JSON)
     */
    public function update()
    {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                return;
            }

            $userId = $this->getCurrentUserId();
            
            // Get JSON input
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON']);
                return;
            }
            
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['error' => 'No data provided']);
                return;
            }
            
            // Validate allowed fields and types
            $allowedFields = ['first_name', 'last_name', 'company', 'phone', 'bio', 'location'];
            foreach ($data as $field => $value) {
                if (!in_array($field, $allowedFields)) {
                    continue;
                }
                // Validate type - must be string or null
                if (!is_string($value) && $value !== null) {
                    http_response_code(400);
                    echo json_encode(['error' => "Field '$field' must be a string"]);
                    return;
                }
            }
            
            // Update profile
            $success = $this->userModel->updateProfile($userId, $data);
            
            if ($success) {
                // Return updated profile data
                $user = $this->userModel->find($userId);
                $response = [];
                foreach (['first_name', 'last_name', 'company', 'phone'] as $field) {
                    if (isset($user[$field])) {
                        $response[$field] = $user[$field];
                    }
                }
                
                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update profile']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get current user ID
     */
    protected function getCurrentUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }
}
?>
