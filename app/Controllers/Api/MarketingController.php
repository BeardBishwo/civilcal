<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\User;
use Exception;

class MarketingController extends Controller
{
    /**
     * Get marketing opt-in statistics
     */
    public function getStats()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        try {
            $userModel = new User();
            
            // Get total users
            $totalUsers = $this->getTotalUsers();
            
            // Get marketing opt-in users
            $optInUsers = $userModel->getMarketingOptInUsers();
            $optInCount = count($optInUsers);
            
            // Calculate percentage
            $optInPercentage = $totalUsers > 0 ? round(($optInCount / $totalUsers) * 100, 1) : 0;
            
            echo json_encode([
                'success' => true,
                'stats' => [
                    'total_users' => $totalUsers,
                    'marketing_opt_in' => $optInCount,
                    'marketing_opt_out' => $totalUsers - $optInCount,
                    'opt_in_percentage' => $optInPercentage,
                    'opt_out_percentage' => round(100 - $optInPercentage, 1)
                ]
            ]);
            
        } catch (Exception $e) {
            error_log('Marketing stats error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Unable to get marketing statistics']);
        }
    }
    
    /**
     * Get users who opted in for marketing emails
     */
    public function getOptInUsers()
    {
        header('Content-Type: application/json');
        
        try {
            $limit = $_GET['limit'] ?? null;
            
            $userModel = new User();
            $users = $userModel->getMarketingOptInUsers($limit);
            
            echo json_encode([
                'success' => true,
                'users' => $users,
                'count' => count($users)
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to get opt-in users']);
        }
    }
    
    /**
     * Update user marketing preferences
     */
    public function updatePreferences()
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['user_id'] ?? null;
            $allowMarketing = $input['allow_marketing'] ?? false;
            
            if (!$userId) {
                echo json_encode(['error' => 'User ID required']);
                return;
            }
            
            $userModel = new User();
            $result = $userModel->updateMarketingPreferences($userId, $allowMarketing);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Marketing preferences updated successfully'
                ]);
            } else {
                echo json_encode(['error' => 'Failed to update preferences']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to update marketing preferences']);
        }
    }
    
    /**
     * Get total user count
     */
    private function getTotalUsers()
    {
        $userModel = new User();
        $users = $userModel->getAll();
        return count($users);
    }
}
?>
