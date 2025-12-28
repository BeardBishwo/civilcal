<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new LeaderboardService();
    }

    public function index()
    {
        $period = $_GET['period'] ?? 'weekly';
        
        // Simple security/validation on period
        if (!in_array($period, ['weekly', 'monthly', 'yearly'])) {
            $period = 'weekly';
        }
        
        $value = $_GET['value'] ?? match($period) {
            'weekly' => date('Y-W'),
            'monthly' => date('Y-m'),
            'yearly' => date('Y') // Fixed syntax
        };
        
        $rankings = $this->service->getLeaderboard($period, $value, 0, 50); // Top 50 public
        
        $this->view('quiz/leaderboard/index', [
            'title' => 'Public Leaderboard',
            'rankings' => $rankings,
            'current_period' => $period,
            'current_value' => $value
        ]);
    }
}
