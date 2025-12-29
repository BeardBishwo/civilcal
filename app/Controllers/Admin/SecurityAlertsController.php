<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\SuspiciousActivityDetector;

class SecurityAlertsController extends Controller
{
    private $detector;
    
    public function __construct()
    {
        parent::__construct();
        \App\Services\Security::startSession();
        $this->detector = new SuspiciousActivityDetector();
    }
    
    /**
     * Display all security alerts
     */
    public function index()
    {
        if (!$this->auth->isAdmin()) {
            header('Location: ' . app_base_url('/login'));
            exit;
        }
        
        $filter = $_GET['filter'] ?? 'all';
        $riskLevel = $_GET['risk'] ?? null;
        
        // Get alerts based on filter
        if ($filter === 'unresolved') {
            $alerts = $this->detector->getUnresolvedAlerts($riskLevel);
        } else {
            $alerts = $this->getAllAlerts($riskLevel);
        }
        
        // Get alert statistics
        $stats = $this->getAlertStatistics();
        
        $this->view->render('admin/security/alerts', [
            'page_title' => 'Security Alerts',
            'alerts' => $alerts,
            'filter' => $filter,
            'risk_level' => $riskLevel,
            'stats' => $stats
        ]);
    }
    
    /**
     * Resolve an alert
     */
    public function resolve()
    {
        header('Content-Type: application/json');
        
        if (!$this->auth->isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
        
        $alertId = $_POST['alert_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$alertId || !$userId) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }
        
        $result = $this->detector->resolveAlert($alertId, $userId);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Alert resolved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to resolve alert']);
        }
        exit;
    }
    
    /**
     * Get all alerts with optional risk level filter
     */
    private function getAllAlerts($riskLevel = null)
    {
        $db = \App\Core\Database::getInstance();
        
        $sql = "
            SELECT sa.*, u.username, u.email
            FROM security_alerts sa
            LEFT JOIN users u ON sa.user_id = u.id
        ";
        
        $params = [];
        
        if ($riskLevel) {
            $sql .= " WHERE sa.risk_level = ?";
            $params[] = $riskLevel;
        }
        
        $sql .= " ORDER BY sa.created_at DESC LIMIT 100";
        
        $stmt = $db->getPdo()->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get alert statistics
     */
    private function getAlertStatistics()
    {
        $db = \App\Core\Database::getInstance();
        
        // Total alerts
        $stmt = $db->getPdo()->query("SELECT COUNT(*) FROM security_alerts");
        $total = $stmt->fetchColumn();
        
        // Unresolved alerts
        $stmt = $db->getPdo()->query("SELECT COUNT(*) FROM security_alerts WHERE is_resolved = 0");
        $unresolved = $stmt->fetchColumn();
        
        // High risk alerts
        $stmt = $db->getPdo()->query("SELECT COUNT(*) FROM security_alerts WHERE risk_level = 'high' AND is_resolved = 0");
        $highRisk = $stmt->fetchColumn();
        
        // Alerts by type
        $stmt = $db->getPdo()->query("
            SELECT alert_type, COUNT(*) as count
            FROM security_alerts
            WHERE is_resolved = 0
            GROUP BY alert_type
        ");
        $byType = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        return [
            'total' => $total,
            'unresolved' => $unresolved,
            'high_risk' => $highRisk,
            'by_type' => $byType
        ];
    }
}
