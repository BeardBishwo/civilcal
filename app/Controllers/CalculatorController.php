<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\MathEngine;

class CalculatorController extends Controller
{
    private $engine;

    public function __construct()
    {
        parent::__construct();
        $this->engine = new MathEngine();
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

    /**
     * Unit Converter Page
     */
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

        $this->view->render('calculator/converter', [
            'title' => $category['name'] . ' Converter',
            'category' => $category,
            'units' => $units,
            'categories' => $allCategories
        ]);
    }

    /**
     * API: Convert Units
     */
    public function convert()
    {
        header('Content-Type: application/json');
        
        $value = $_POST['value'] ?? 0;
        $fromUnit = $_POST['from_unit'] ?? '';
        $toUnit = $_POST['to_unit'] ?? '';
        $categoryId = $_POST['category_id'] ?? 0;

        $result = $this->engine->convertUnit($value, $fromUnit, $toUnit, $categoryId);

        echo json_encode([
            'success' => !isset($result['error']),
            'result' => $result
        ]);
    }

    /**
     * API: Calculate Expression
     */
    public function calculate()
    {
        try {
            $expression = $_POST['expression'] ?? '';
            
            if (empty($expression)) {
                return $this->json([
                    'success' => false,
                    'result' => 'Empty expression'
                ]);
            }

            $result = $this->engine->evaluate($expression);

            return $this->json([
                'success' => !isset($result['error']),
                'result' => $result['error'] ?? $result
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'result' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dedicated Scientific Calculator Page
     */
    public function scientific()
    {
        $this->view->render('calculator/scientific', [
            'title' => 'Professional Scientific Calculator',
            'categories' => $this->getCategories(),
            'meta_description' => 'Advanced scientific calculator with history and memory functions.'
        ]);
    }

    /**
     * Catch-all permalink handler
     */
    public function permalink($slug)
    {
        // Check if $slug matches a unit category
        $category = $this->db->findOne('calc_unit_categories', ['slug' => $slug]);
        if ($category) {
            return $this->converter($slug);
        }

        // Potential for other calculators based on slug
        header('Location: ' . app_base_url('/calculator'));
        exit;
    }

    /**
     * Legacy tool handler (safety)
     */
    public function tool($category = null, $tool = null)
    {
        if ($category === 'converter') {
            return $this->converter($tool);
        }
        
        header('Location: ' . app_base_url('/calculator'));
        exit;
    }
    /**
     * User Dashboard
     */
    public function dashboard()
    {
        $userId = $_SESSION['user_id'];
        
        $userModel = new \App\Models\User();
        $stats = $userModel->getStatistics($userId);
        
        $questService = new \App\Services\QuestService();
        $rankService = new \App\Services\RankService();
        $gamification = new \App\Services\GamificationService();
        
        $toolOfTheDay = $questService->getToolOfTheDay();
        $isQuestCompleted = $questService->isCompleted($userId);
        
        $wallet = $gamification->getWallet($userId);
        $rankData = $rankService->getUserRankData($stats, $wallet);


        $this->view->render('dashboard', [
            'user_stats' => $stats,
            'quest' => [
                'tool' => $toolOfTheDay,
                'completed' => $isQuestCompleted
            ],
            'rank' => $rankData
        ]);
    }

    /**
     * Category Landing Page for CMS Calculators
     */
    public function category($categorySlug)
    {
        $calculators = $this->db->find('calculators', ['category' => $categorySlug, 'is_active' => 1]);
        
        $this->view->render('calculator/category', [
            'title' => ucfirst($categorySlug) . ' Calculators',
            'category' => $categorySlug,
            'calculators' => $calculators
        ]);
    }

    /**
     * Show a specific CMS Calculator
     */
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

        $inputs = $this->db->find('calculator_inputs', ['calculator_id' => $calculator['id']], 'order_index ASC');
        $outputs = $this->db->find('calculator_outputs', ['calculator_id' => $calculator['id']], 'order_index ASC');

        $this->view->render('calculator/show', [
            'title' => $calculator['name'],
            'calculator' => $calculator,
            'inputs' => $inputs,
            'outputs' => $outputs,
            'config' => json_decode($calculator['config_json'], true)
        ]);
    }
}
