<?php
/**
 * API endpoint for user profile operations
 * Handles GET (retrieve) and PUT (update) profile data
 */

require_once __DIR__ . '/../app/bootstrap.php';

use App\Models\User;

header('Content-Type: application/json');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Check authentication (session already started in bootstrap)
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$userModel = new User();

try {
    switch ($method) {
        case 'GET':
            // Get user profile data
            $user = $userModel->find($userId);
            
            if (!$user) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found']);
                exit;
            }
            
            // Remove sensitive data
            unset($user['password']);
            
            // Add additional profile data
            $user['statistics'] = $userModel->getStatistics($userId);
            $user['profile_completion'] = $userModel->getProfileCompletion($userId);
            
            http_response_code(200);
            echo json_encode($user);
            break;
            
        case 'PUT':
            // Update user profile
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
                exit;
            }
            
            // Validate data (basic validation)
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No data provided']);
                exit;
            }
            
            // Filter allowed fields (only fields that exist in users table)
            $allowedFields = ['first_name', 'last_name', 'company', 'phone'];
            $updateData = [];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    // Validate data types and lengths
                    if (is_string($data[$field])) {
                        $value = trim($data[$field]);
                        
                        // Reject empty strings for name fields (but allow for optional fields)
                        if ($value === '' && in_array($field, ['first_name', 'last_name'])) {
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => "Field '$field' cannot be empty"]);
                            exit;
                        }
                        
                        // Check max length (prevent overly long strings)
                        if (strlen($value) > 255) {
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => "Field '$field' exceeds maximum length"]);
                            exit;
                        }
                        
                        $updateData[$field] = $value;
                    } elseif ($data[$field] === null) {
                        // Null values are not allowed for required fields
                        if (in_array($field, ['first_name', 'last_name'])) {
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => "Field '$field' cannot be null"]);
                            exit;
                        }
                        $updateData[$field] = null;
                    } else {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => "Invalid type for field '$field'"]);
                        exit;
                    }
                }
            }
            
            // Update profile - build SQL directly since User model's updateProfile doesn't support these fields
            if (empty($updateData)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No valid data provided for update']);
                exit;
            }
            
            $db = \App\Core\Database::getInstance();
            $setParts = [];
            $values = [];
            
            foreach ($updateData as $field => $value) {
                $setParts[] = "$field = ?";
                $values[] = $value;
            }
            
            $values[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $setParts) . ", updated_at = NOW() WHERE id = ?";
            $stmt = $db->prepare($sql);
            $success = $stmt->execute($values);
            
            if ($success) {
                // Get updated user data
                $user = $userModel->find($userId);
                unset($user['password']);
                
                // Return the updated fields
                $response = [];
                foreach ($allowedFields as $field) {
                    if (isset($user[$field])) {
                        $response[$field] = $user[$field];
                    }
                }
                
                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
            }
            break;
            
        case 'POST':
            // Also support POST for compatibility
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Use PUT method for profile updates']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
