<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\MathEngine;

use App\Services\GamificationService;
use App\Services\RankService;
use App\Services\QuestService;
use App\Services\Quiz\DailyQuizService;
use App\Services\Quiz\StreakService;

class CalculatorController extends Controller
{
    private $engine;
    private $campaignModel;
    private $rankService;
    private $questService;
    private $dailyQuizService;
    private $streakService;

    public function __construct()
    {
        parent::__construct();
        $this->engine = new MathEngine();
        $this->campaignModel = new \App\Models\Campaign();
        $this->rankService = new RankService();
        $this->questService = new QuestService();
        $this->dailyQuizService = new DailyQuizService();
        $this->streakService = new StreakService();
    }

    private function getCategories()
    {
        return $this->db->find('calc_unit_categories', [], 'display_order ASC');
    }
    
    /**
     * Calculator Platform Landing Page
     */
    public function index()
    {
        $this->view->render('calculator/index', [
            'title' => 'Universal Calculator Platform',
            'categories' => $this->getCategories()
        ]);
    }

    public function converter($categorySlug = null)
    {
        if (!$categorySlug) {
            header('Location: ' . app_base_url('/calculator'));
            exit;
        }

        $allCategories = $this->getCategories();
        $category = $this->db->findOne('calc_unit_categories', ['slug' => $categorySlug]);
        
        if (!$category) {
            header('Location: ' . app_base_url('/calculator'));
            exit;
        }

        $units = $this->db->find('calc_units', ['category_id' => $category['id']], 'display_order ASC');
        
        // B2B: Fetch Campaign
        $campaign = $this->campaignModel->getActiveForCalculator($categorySlug);
        if ($campaign) {
            $this->campaignModel->recordImpression(
                $campaign['id'], 
                $_SESSION['user_id'] ?? null, 
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            );
        }

        $this->view->render('calculator/converter', [
            'title' => $category['name'] . ' Converter',
            'category' => $category,
            'units' => $units,
            'categories' => $allCategories,
            'campaign' => $campaign // Pass to view
        ]);
    }
    
    // ... convert, calculate ...

    // ... show ...
    public function show($categorySlug, $calculatorSlug)
    {
        $calculator = $this->db->findOne('calculators', [
            'category' => $categorySlug, 
            'calculator_id' => $calculatorSlug,
            'is_active' => 1
        ]);

        if (!$calculator) {
            header('Location: ' . app_base_url('/calculators'));
            exit;
        }
        
        // B2B: Fetch Campaign
        $campaign = $this->campaignModel->getActiveForCalculator($calculatorSlug);
        if ($campaign) {
            $this->campaignModel->recordImpression(
                $campaign['id'], 
                $_SESSION['user_id'] ?? null, 
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            );
        }

        $inputs = $this->db->find('calculator_inputs', ['calculator_id' => $calculator['id']], 'order_index ASC');
        $outputs = $this->db->find('calculator_outputs', ['calculator_id' => $calculator['id']], 'order_index ASC');

        $this->view->render('calculator/show', [
            'title' => $calculator['name'],
            'calculator' => $calculator,
            'inputs' => $inputs,
            'outputs' => $outputs,
            'config' => json_decode($calculator['config_json'], true),
            'campaign' => $campaign // Pass to view
        ]);
    }
    public function dashboard()
    {
        $userId = $_SESSION['user_id'];
        $user = $this->db->find('users', $userId);
        
        // 1. Rank Data
        $stats = $this->db->findOne('user_stats', ['user_id' => $userId]) ?: [];
        $wallet = $this->db->findOne('user_wallets', ['user_id' => $userId]) ?: [];
        $rankData = $this->rankService->getUserRankData($stats, $wallet);
        
        // 2. Old Quest (Tool of the Day)
        $tod = $this->questService->getToolOfTheDay();
        $todCompleted = $this->questService->isCompleted($userId);
        $questData = [
            'tool' => $tod,
            'completed' => $todCompleted
        ];

        // 3. NEW: Daily Quest Info
        $date = date('Y-m-d');
        $streakInfo = $this->streakService->getStreakInfo($userId);
        $dailyAttempt = $this->dailyQuizService->checkAttempt($userId, $date);
        $dailyQuiz = $this->dailyQuizService->getQuizForUser($date, $user['stream_id'] ?? null); // stream_id might not exist in $user array if not selected?
        
        // Pass everything to view
        $this->view->render('dashboard', [
            'user' => $user, // Ensure view gets user array
            'rank' => $rankData,
            'quest' => $questData,
            'daily_quest' => [
                'available' => (bool)$dailyQuiz,
                'completed' => (bool)$dailyAttempt,
                'streak' => $streakInfo['current_streak'],
                'multiplier' => min(1 + (($streakInfo['current_streak'] - 1) * 0.05), 2.0),
                'streak_freeze' => $streakInfo['streak_freeze_left'] ?? 0
            ]
        ]);
    }
}
