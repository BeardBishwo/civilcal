<?php
namespace App\Controllers;

use App\Services\SecurityValidator;
use App\Services\SecurityMonitor;

class HoneypotController extends Controller
{
    /**
     * Honeypot: Fake free coins endpoint
     */
    public function freeCoins()
    {
        $ip = SecurityValidator::getClientIp();
        $userId = $_SESSION['user_id'] ?? null;
        
        // Log honeypot access
        SecurityMonitor::log($userId, 'honeypot_accessed', '/api/shop/free-coins', [
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ], 'critical');
        
        // Ban IP immediately
        SecurityValidator::banIp($ip, 'Accessed honeypot endpoint: free-coins', 86400 * 7); // 7 days
        
        // Return fake success to not alert the bot
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Processing request...',
            'coins' => 0
        ]);
        exit;
    }

    /**
     * Honeypot: Fake admin grant resources endpoint
     */
    public function grantResources()
    {
        $ip = SecurityValidator::getClientIp();
        $userId = $_SESSION['user_id'] ?? null;
        
        // Log honeypot access
        SecurityMonitor::log($userId, 'honeypot_accessed', '/api/admin/grant-resources', [
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'post_data' => $_POST
        ], 'critical');
        
        // Ban IP permanently
        SecurityValidator::banIp($ip, 'Accessed honeypot endpoint: admin grant-resources');
        
        // Return fake success
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Resources granted successfully'
        ]);
        exit;
    }

    /**
     * Honeypot: Fake unlimited coins endpoint
     */
    public function unlimitedCoins()
    {
        $ip = SecurityValidator::getClientIp();
        $userId = $_SESSION['user_id'] ?? null;
        
        SecurityMonitor::log($userId, 'honeypot_accessed', '/api/shop/unlimited-coins', [
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ], 'critical');
        
        SecurityValidator::banIp($ip, 'Accessed honeypot endpoint: unlimited-coins', 86400 * 30); // 30 days
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'coins' => 999999]);
        exit;
    }
}
