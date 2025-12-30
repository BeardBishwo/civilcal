<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Analytics
{
    private $db;
    private $pdo;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPdo();
    }

    /**
     * Track a new event
     */
    public function track($data)
    {
        $sql = "INSERT INTO analytics_events 
                (event_type, event_category, event_data, user_id, session_id, ip_address, user_agent, referrer, page_url) 
                VALUES 
                (:event_type, :event_category, :event_data, :user_id, :session_id, :ip_address, :user_agent, :referrer, :page_url)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':event_type' => $data['event_type'],
            ':event_category' => $data['event_category'] ?? null,
            ':event_data' => isset($data['event_data']) ? json_encode($data['event_data']) : null,
            ':user_id' => $data['user_id'] ?? null,
            ':session_id' => $data['session_id'] ?? null,
            ':ip_address' => $data['ip_address'] ?? null,
            ':user_agent' => $data['user_agent'] ?? null,
            ':referrer' => $data['referrer'] ?? null,
            ':page_url' => $data['page_url'] ?? null
        ]);
    }

    /**
     * Get aggregate stats for a date range
     */
    public function getStats($metricType, $startDate, $endDate)
    {
        // First look in summary table
        $sql = "SELECT date, metric_value 
                FROM analytics_summary 
                WHERE metric_type = :type 
                AND date BETWEEN :start AND :end 
                ORDER BY date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':type' => $metricType,
            ':start' => $startDate,
            ':end' => $endDate
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent events
     */
    public function getRecentEvents($limit = 10, $type = null)
    {
        $sql = "SELECT * FROM analytics_events";
        $params = [];
        
        if ($type) {
            $sql .= " WHERE event_type = :type";
            $params[':type'] = $type;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get popular pages/calculators
     */
    public function getPopularContent($type = 'page_view', $limit = 5, $days = 30)
    {
        $sql = "SELECT 
                    event_category,
                    page_url,
                    COUNT(*) as count
                FROM analytics_events 
                WHERE event_type = :type 
                AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY page_url
                ORDER BY count DESC
                LIMIT " . (int)$limit;
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':type', $type);
        $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total counts for dashboard cards
     */
    public function getDashboardCounts()
    {
        $today = date('Y-m-d');
        
        // Visitors today (unique IP)
        $visitorsStmt = $this->pdo->prepare("SELECT COUNT(DISTINCT ip_address) FROM analytics_events WHERE DATE(created_at) = ?");
        $visitorsStmt->execute([$today]);
        $visitors = $visitorsStmt->fetchColumn();
        
        // Page views today
        $viewsStmt = $this->pdo->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type='page_view' AND DATE(created_at) = ?");
        $viewsStmt->execute([$today]);
        $views = $viewsStmt->fetchColumn();
        
        // Calculator uses today
        $calcStmt = $this->pdo->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type='calculator_use' AND DATE(created_at) = ?");
        $calcStmt->execute([$today]);
        $calc = $calcStmt->fetchColumn();
        
        return [
            'visitors_today' => $visitors,
            'views_today' => $views,
            'calcs_today' => $calc
        ];
    }
}
