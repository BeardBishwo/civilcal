<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    private $service;

    public function __construct()
    {
        parent::__construct();
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
             header('Location: ' . app_base_url('login'));
             exit;
        }
        $this->service = new LeaderboardService();
    }

    public function index()
    {
        $period = $_GET['period'] ?? 'weekly'; // weekly, monthly, yearly
        $value = $_GET['value'] ?? ''; // e.g. 2024-52
        
        $rankings = $this->service->getLeaderboard($period, $value);
        
        $this->view->render('admin/quiz/leaderboard/index', [
            'page_title' => 'Leaderboard Manager',
            'rankings' => $rankings,
            'current_period' => $period,
            'current_value' => $value ?: date('Y-W')
        ]);
    }
}
