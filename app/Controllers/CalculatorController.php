<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\MathEngine;

class CalculatorController extends Controller
{
    private $engine;

    private $campaignModel;

    public function __construct()
    {
        parent::__construct();
        $this->engine = new MathEngine();
        $this->campaignModel = new \App\Models\Campaign();
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
}
