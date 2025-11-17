<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class CalculationsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
            }

    public function index()
    {
        // Get all calculations from database
        $stmt = $this->db->query("
            SELECT c.*, u.username, u.email 
            FROM calculation_history c 
            LEFT JOIN users u ON c.user_id = u.id 
            ORDER BY c.created_at DESC 
            LIMIT 100
        ");
        $calculations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get statistics
        $statsStmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT calculator_type) as calculator_types,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as week_count,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as month_count
            FROM calculation_history
        ");
        $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

        $data = [
            'page_title' => 'Calculations Management',
            'calculations' => $calculations,
            'stats' => $stats
        ];

        $this->view('admin/calculations/index', $data);
    }
}
