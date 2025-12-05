<?php

namespace App\Controllers\Api;

use App\Core\Controller;
<<<<<<< HEAD
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
=======
use App\Core\Database;
use App\Models\User;
use Exception;

class ProfileController extends Controller
{
    /**
     * Get user profile (API endpoint)
     */
    public function getProfile()
>>>>>>> temp-branch
    {
        header('Content-Type: application/json');
        
        try {
<<<<<<< HEAD
            $userId = $this->getCurrentUserId();
            $user = $this->userModel->find($userId);
=======
            // Check authentication
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                return;
            }
            
            $userId = $_SESSION['user_id'];
            $userModel = new User();
            $user = $userModel->find($userId);
>>>>>>> temp-branch
            
            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }
            
<<<<<<< HEAD
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
=======
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
>>>>>>> temp-branch
    {
        header('Content-Type: application/json');
        
        try {
<<<<<<< HEAD
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
=======
            // Check authentication via session or HTTP Basic Auth
            $userId = null;
            
            // Check session first
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            }
            
            // Check HTTP Basic Auth
            if (!$userId && isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                $username = $_SERVER['PHP_AUTH_USER'];
                $password = $_SERVER['PHP_AUTH_PW'];
                
                $userModel = new User();
                $user = $userModel->findByUsername($username);
                if ($user) {
                    $userArray = is_array($user) ? $user : (array) $user;
                    if (password_verify($password, $userArray['password'])) {
                        $userId = $userArray['id'];
                    }
                }
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
>>>>>>> temp-branch
