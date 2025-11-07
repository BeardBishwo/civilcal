<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CalculationHistory;

class CalculatorController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    // Add this method to save calculation history
    private function saveToHistory($calculatorType, $inputs, $results, $title = null) {
        // Check if user is logged in
        $auth = new \App\Core\Auth();
        if (!$auth->check()) {
            return; // Don't save if user not logged in
        }

        $user = $auth->user();
        $historyModel = new CalculationHistory();
        
        // Auto-generate title if not provided
        if (!$title) {
            $title = $calculatorType . ' Calculation - ' . date('M j, Y g:i A');
        }

        $historyModel->saveCalculation(
            $user->id, 
            $calculatorType, 
            $inputs, 
            $results, 
            $title
        );
    }
    
    // Add this method to handle calculation results and save to history
    public function saveCalculation() {
        // This method would be called via AJAX when a calculation is completed
        $auth = new \App\Core\Auth();
        if (!$auth->check()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $calculatorType = $input['calculator_type'] ?? 'Unknown';
        $inputs = $input['inputs'] ?? [];
        $results = $input['results'] ?? [];
        $title = $input['title'] ?? null;

        $this->saveToHistory($calculatorType, $inputs, $results, $title);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Calculation saved to history']);
    }
    
    // Add this method to calculate and save history
    public function calculate($calculatorType, $inputs) {
        // Get calculation results based on calculator type
        $results = $this->processCalculation($calculatorType, $inputs);
        
        // Save to history
        $this->saveToHistory($calculatorType, $inputs, $results);
        
        return $results;
    }
    
    // Placeholder method for actual calculation processing
    private function processCalculation($calculatorType, $inputs) {
        // This would contain the actual calculation logic for each calculator type
        // For now, returning a placeholder result
        return [
            'input_received' => $inputs,
            'calculator_type' => $calculatorType,
            'calculated_at' => date('Y-m-d H:i:s'),
            'status' => 'processed'
        ];
    }
    
    // Add dashboard method
    public function dashboard() {
        $this->setCategory('dashboard');
        $this->setTitle('Dashboard - Bishwo Calculator');
        
        // Get recent calculations if user is logged in
        $recentCalculations = [];
        $auth = new \App\Core\Auth();
        if ($auth->check()) {
            $user = $auth->user();
            $historyModel = new CalculationHistory();
            $recentCalculations = $historyModel->getRecentCalculations($user->id, 5);
        }
        
        $data = [
            'title' => 'Dashboard',
            'recent_calculations' => $recentCalculations,
            'total_calculators' => 200, // Example number
            'categories' => [
                'civil' => 'Civil Engineering',
                'electrical' => 'Electrical Engineering', 
                'structural' => 'Structural Engineering',
                'plumbing' => 'Plumbing Engineering',
                'hvac' => 'HVAC Engineering',
                'fire' => 'Fire Protection',
                'mep' => 'MEP Integration',
                'estimation' => 'Cost Estimation',
                'management' => 'Project Management',
                'site' => 'Site Engineering'
            ]
        ];
        
        $this->view('dashboard', $data);
    }
    
    // Add category method
    public function category($category) {
        $this->setCategory($category);
        $this->setTitle(ucfirst($category) . ' Engineering Calculator');
        
        $data = [
            'category' => $category,
            'title' => ucfirst($category) . ' Engineering Tools',
            'description' => 'Professional ' . $category . ' engineering calculation tools'
        ];
        
        $this->view($category . '/index', $data);
    }
    
    // Add show method
    public function show($category, $calculator) {
        $this->setCategory($category);
        $this->setTitle(ucfirst(str_replace('-', ' ', $calculator)) . ' Calculator');
        
        $data = [
            'category' => $category,
            'calculator' => $calculator,
            'title' => ucfirst(str_replace('-', ' ', $calculator)) . ' Calculator',
            'description' => 'Calculate ' . str_replace('-', ' ', $calculator) . ' with our professional tool'
        ];
        
        $this->view('calculators/' . $category . '/' . $calculator, $data);
    }
    
    /**
     * Home page - Main dashboard
     */
    public function index() {
        $this->setCategory('home');
        $this->setTitle('Bishwo Calculator - Professional Engineering Calculations');
        $this->setDescription('Professional engineering calculation tools for civil, electrical, structural, and MEP design');
        $this->setKeywords('engineering calculator, civil engineering, electrical design, structural analysis, MEP calculations');
        
        $data = [
            'title' => 'Welcome to Bishwo Calculator',
            'subtitle' => 'Professional Engineering Calculation Tools',
            'description' => 'Comprehensive suite of engineering calculation tools for design professionals'
        ];
        
        $this->view('index', $data);
    }
    
    /**
     * Civil Engineering calculations
     */
    public function civil() {
        $this->setCategory('civil');
        $this->setTitle('Civil Engineering Calculator');
        $this->setDescription('Civil engineering calculation tools including concrete, structural, and earthwork calculations');
        
        $data = [
            'title' => 'Civil Engineering Tools',
            'calculators' => [
                'concrete' => 'Concrete Mix Design & Volume',
                'structural' => 'Structural Analysis & Design',
                'earthwork' => 'Earthwork & Excavation',
                'brickwork' => 'Masonry & Brickwork',
                'plastering' => 'Plastering & Finishing'
            ]
        ];
        
        $this->view('civil/index', $data);
    }
    
    /**
     * Electrical Engineering calculations
     */
    public function electrical() {
        $this->setCategory('electrical');
        $this->setTitle('Electrical Engineering Calculator');
        $this->setDescription('Electrical engineering calculation tools including load calculations, wire sizing, and electrical design');
        
        $data = [
            'title' => 'Electrical Engineering Tools',
            'calculators' => [
                'load-calculation' => 'Load Calculations',
                'wire-sizing' => 'Wire & Cable Sizing',
                'voltage-drop' => 'Voltage Drop Analysis',
                'conduit-sizing' => 'Conduit Sizing',
                'short-circuit' => 'Short Circuit Analysis'
            ]
        ];
        
        $this->view('electrical/index', $data);
    }
    
    /**
     * Structural Engineering calculations
     */
    public function structural() {
        $this->setCategory('structural');
        $this->setTitle('Structural Engineering Calculator');
        $this->setDescription('Structural engineering analysis and design tools for beams, columns, slabs, and foundations');
        
        $data = [
            'title' => 'Structural Engineering Tools',
            'calculators' => [
                'beam-analysis' => 'Beam Analysis & Design',
                'column-design' => 'Column Design',
                'slab-design' => 'Slab Design',
                'foundation-design' => 'Foundation Design',
                'load-analysis' => 'Load Analysis'
            ]
        ];
        
        $this->view('structural/index', $data);
    }
    
    /**
     * Plumbing Engineering calculations
     */
    public function plumbing() {
        $this->setCategory('plumbing');
        $this->setTitle('Plumbing Engineering Calculator');
        $this->setDescription('Plumbing system design and calculation tools for water supply, drainage, and fixtures');
        
        $data = [
            'title' => 'Plumbing Engineering Tools',
            'calculators' => [
                'water-supply' => 'Water Supply Design',
                'drainage' => 'Drainage System Design',
                'stormwater' => 'Stormwater Management',
                'fixtures' => 'Fixture Count & Sizing',
                'pipe-sizing' => 'Pipe Sizing'
            ]
        ];
        
        $this->view('plumbing/index', $data);
    }
    
    /**
     * HVAC Engineering calculations
     */
    public function hvac() {
        $this->setCategory('hvac');
        $this->setTitle('HVAC Engineering Calculator');
        $this->setDescription('HVAC system design and analysis tools for load calculations and equipment sizing');
        
        $data = [
            'title' => 'HVAC Engineering Tools',
            'calculators' => [
                'load-calculation' => 'HVAC Load Calculations',
                'equipment-sizing' => 'Equipment Sizing',
                'duct-sizing' => 'Duct Sizing',
                'psychrometrics' => 'Psychrometric Analysis',
                'energy-analysis' => 'Energy Analysis'
            ]
        ];
        
        $this->view('hvac/index', $data);
    }
    
    /**
     * Fire Protection Engineering calculations
     */
    public function fire() {
        $this->setCategory('fire');
        $this->setTitle('Fire Protection Engineering Calculator');
        $this->setDescription('Fire protection system design and hydraulic calculations for sprinkler systems');
        
        $data = [
            'title' => 'Fire Protection Engineering Tools',
            'calculators' => [
                'sprinklers' => 'Sprinkler System Design',
                'hydraulics' => 'Hydraulic Calculations',
                'standpipes' => 'Standpipe Systems',
                'fire-pumps' => 'Fire Pump Sizing',
                'hazard-classification' => 'Hazard Classification'
            ]
        ];
        
        $this->view('fire/index', $data);
    }
    
    /**
     * MEP (Mechanical, Electrical, Plumbing) Integration
     */
    public function mep() {
        $this->setCategory('mep');
        $this->setTitle('MEP Integration Calculator');
        $this->setDescription('MEP system integration and coordination tools for building design');
        
        $data = [
            'title' => 'MEP Integration Tools',
            'calculators' => [
                'coordination' => 'System Coordination',
                'cost-management' => 'Cost Management',
                'energy-efficiency' => 'Energy Efficiency',
                'integration' => 'System Integration',
                'reports-documentation' => 'Reports & Documentation'
            ]
        ];
        
        $this->view('mep/index', $data);
    }
    
    /**
     * Estimation and Costing
     */
    public function estimation() {
        $this->setCategory('estimation');
        $this->setTitle('Project Estimation Calculator');
        $this->setDescription('Project cost estimation, quantity takeoff, and financial analysis tools');
        
        $data = [
            'title' => 'Project Estimation Tools',
            'calculators' => [
                'cost-estimation' => 'Cost Estimation',
                'quantity-takeoff' => 'Quantity Takeoff',
                'labor-estimation' => 'Labor Estimation',
                'equipment-estimation' => 'Equipment Estimation',
                'project-financials' => 'Project Financials'
            ]
        ];
        
        $this->view('estimation/index', $data);
    }
    
    /**
     * Project Management
     */
    public function management() {
        $this->setCategory('management');
        $this->setTitle('Project Management Tools');
        $this->setDescription('Project management tools for scheduling, resource allocation, and quality control');
        
        $data = [
            'title' => 'Project Management Tools',
            'calculators' => [
                'scheduling' => 'Project Scheduling',
                'resource-allocation' => 'Resource Allocation',
                'quality-control' => 'Quality Control',
                'procurement' => 'Procurement Management',
                'analytics' => 'Project Analytics'
            ]
        ];
        
        $this->view('management/index', $data);
    }
    
    /**
     * Site Engineering
     */
    public function site() {
        $this->setCategory('site');
        $this->setTitle('Site Engineering Tools');
        $this->setDescription('Site engineering tools for surveying, earthwork, and construction site management');
        
        $data = [
            'title' => 'Site Engineering Tools',
            'calculators' => [
                'surveying' => 'Surveying Calculations',
                'earthwork' => 'Earthwork Operations',
                'safety' => 'Construction Safety',
                'productivity' => 'Productivity Analysis',
                'concrete-tools' => 'Concrete Field Tools'
            ]
        ];
        
        $this->view('site/index', $data);
    }
}
?>
