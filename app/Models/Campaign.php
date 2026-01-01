<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Campaign
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO campaigns 
            (sponsor_id, calculator_slug, title, banner_image, ad_text, start_date, end_date, priority, max_impressions) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $data['sponsor_id'],
            $data['calculator_slug'],
            $data['title'],
            $data['banner_image'] ?? null,
            $data['ad_text'] ?? null,
            $data['start_date'],
            $data['end_date'],
            $data['priority'] ?? 0,
            $data['max_impressions'] ?? 0
        ]);
    }

    public function getActiveForCalculator($slug)
    {
        // Logic: 
        // 1. Matches Slug OR 'global' (if we imply global ads, but sticking to specific for now)
        // 2. Status 'active'
        // 3. Current Date is between start and end
        // 4. Impressions < Max Impressions (if Max > 0)
        // 5. Ordered by Priority DESC, then Random
        
        $sql = "SELECT c.*, s.name as sponsor_name, s.logo_path, s.website_url 
                FROM campaigns c
                JOIN sponsors s ON c.sponsor_id = s.id
                WHERE c.calculator_slug = ? 
                AND c.status = 'active'
                AND s.status = 'active'
                AND NOW() BETWEEN c.start_date AND c.end_date
                AND (c.max_impressions = 0 OR c.current_impressions < c.max_impressions)
                ORDER BY c.priority DESC, RAND() 
                LIMIT 1";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function recordImpression($campaignId, $userId, $ip, $device)
    {
        // 1. Insert record
        $stmt = $this->pdo->prepare("INSERT INTO ad_impressions (campaign_id, user_id, ip_hash, user_agent, action_type) VALUES (?, ?, ?, ?, 'view')");
        $stmt->execute([$campaignId, $userId, md5($ip), substr($device, 0, 250)]);
        
        // 2. Increment counter
        $update = $this->pdo->prepare("UPDATE campaigns SET current_impressions = current_impressions + 1 WHERE id = ?");
        $update->execute([$campaignId]);
    }
    
    public function recordClick($campaignId, $userId, $ip, $device)
    {
        $stmt = $this->pdo->prepare("INSERT INTO ad_impressions (campaign_id, user_id, ip_hash, user_agent, action_type) VALUES (?, ?, ?, ?, 'click')");
        $stmt->execute([$campaignId, $userId, md5($ip), substr($device, 0, 250)]);
        
        $update = $this->pdo->prepare("UPDATE campaigns SET current_clicks = current_clicks + 1 WHERE id = ?");
        $update->execute([$campaignId]);
    }
}
