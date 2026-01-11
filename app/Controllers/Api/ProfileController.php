<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use Exception;

class ProfileController extends Controller
{
    /**
     * Get user profile (API endpoint)
     */
    public function getProfile()
    {
        header('Content-Type: application/json');
        
        try {
            // Check authentication
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                return;
            }
            
            $userId = $_SESSION['user_id'];
            $userModel = new User();
            $user = $userModel->find($userId);
            
            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }
            
            // Convert to array and remove sensitive data
            $userData = (array) $user;
            unset($userData['password']);
            
            echo json_encode($userData);
            
        } catch (Exception $e) {
            error_log('Get profile error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error retrieving profile']);
        }
    }
    
    /**
     * Update user profile (API endpoint)
     */
    public function updateProfile()
    {
        header('Content-Type: application/json');
        
        try {
            // Check session
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            }
            
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                return;
            }
            
            // Get input data
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validate input data types
            if (isset($input['first_name']) && !is_string($input['first_name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid first_name type']);
                return;
            }
            
            if (isset($input['last_name']) && !is_string($input['last_name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid last_name type']);
                return;
            }
            
            // Build update data
            $updateData = [];
            $allowedFields = ['first_name', 'last_name', 'company', 'bio', 'phone', 'location'];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $updateData[$field] = $input[$field];
                }
            }
            
            if (empty($updateData)) {
                http_response_code(400);
                echo json_encode(['error' => 'No valid fields to update']);
                return;
            }
            
            // Update user
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            
            $fields = [];
            $values = [];
            foreach ($updateData as $key => $value) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
            $values[] = $userId;
            
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            
            // Get updated user
            $userModel = new User();
            $user = $userModel->find($userId);
            $userData = (array) $user;
            unset($userData['password']);
            
            echo json_encode($userData);
            
        } catch (Exception $e) {
            error_log('Update profile error: ' . $e->getMessage());
            error_log('Update profile trace: ' . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode([
                'error' => 'Error updating profile',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}
