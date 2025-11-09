<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Services\CalculationService;
use App\Calculators\CalculatorFactory;

class CalculatorController extends Controller
{
    private $calculationService;
    
    public function __construct()
    {
        $this->calculationService = new CalculationService();
    }
    
    public function index()
    {
        $categories = $this->getCalculatorCategories();
        $featuredCalculators = $this->getFeaturedCalculators();
        
        return view('calculators/index', [
            'categories' => $categories,
            'featuredCalculators' => $featuredCalculators,
            'user' => Auth::user()
        ]);
    }
    
    public function category($category)
    {
        $calculators = $this->getCalculatorsByCategory($category);
        $categoryInfo = $this->getCategoryInfo($category);
        
        if (!$categoryInfo) {
            return $this->notFound();
        }
        
        return view('calculators/category', [
            'category' => $categoryInfo,
            'calculators' => $calculators,
            'user' => Auth::user()
        ]);
    }
    
    public function tool($category, $tool)
    {
        $calculator = CalculatorFactory::create($category, $tool);
        
        if (!$calculator) {
            return $this->notFound();
        }
        
        $calculatorInfo = $this->getCalculatorInfo($category, $tool);
        
        return view('calculators/tool', [
            'calculator' => $calculator,
            'calculatorInfo' => $calculatorInfo,
            'category' => $category,
            'tool' => $tool,
            'user' => Auth::user()
        ]);
    }
    
    public function calculate($category, $tool)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        
        $result = $this->calculationService->performCalculation(
            $category, 
            $tool, 
            $_POST, 
            $userId
        );
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    public function apiCalculate()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['category']) || !isset($input['tool']) || !isset($input['data'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Missing required parameters'
            ]);
            return;
        }
        
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        
        $result = $this->calculationService->performCalculation(
            $input['category'],
            $input['tool'],
            $input['data'],
            $userId
        );
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    private function getCalculatorCategories()
    {
        return [
            'civil' => [
                'name' => 'Civil Engineering',
                'description' => 'Structural, concrete, masonry, and construction calculators',
                'icon' => 'bi-building',
                'color' => 'primary',
                'count' => 15
            ],
            'electrical' => [
                'name' => 'Electrical Engineering',
                'description' => 'Electrical load, circuit design, and power distribution calculators',
                'icon' => 'bi-lightning-charge',
                'color' => 'warning',
                'count' => 12
            ],
            'structural' => [
                'name' => 'Structural Engineering',
                'description' => 'Beam, column, foundation, and load analysis calculators',
                'icon' => 'bi-bricks',
                'color' => 'danger',
                'count' => 10
            ],
            'hvac' => [
                'name' => 'HVAC',
                'description' => 'Heating, ventilation, and air conditioning calculators',
                'icon' => 'bi-thermometer-sun',
                'color' => 'info',
                'count' => 8
            ],
            'plumbing' => [
                'name' => 'Plumbing',
                'description' => 'Pipe sizing, drainage, and water supply calculators',
                'icon' => 'bi-droplet',
                'color' => 'primary',
                'count' => 6
            ],
            'estimation' => [
                'name' => 'Estimation',
                'description' => 'Cost estimation, material takeoff, and project budgeting',
                'icon' => 'bi-calculator',
                'color' => 'success',
                'count' => 5
            ]
        ];
    }
    
    private function getFeaturedCalculators()
    {
        return [
            [
                'category' => 'civil',
                'tool' => 'concrete-volume',
                'name' => 'Concrete Volume',
                'description' => 'Calculate concrete volume for slabs, beams, columns, and footings',
                'icon' => 'bi-cone',
                'usage' => 1250
            ],
            [
                'category' => 'electrical',
                'tool' => 'load-calculation',
                'name' => 'Electrical Load',
                'description' => 'Calculate electrical load for residential and commercial buildings',
                'icon' => 'bi-lightning-charge',
                'usage' => 980
            ],
            [
                'category' => 'structural',
                'tool' => 'beam-design',
                'name' => 'Beam Design',
                'description' => 'Design and analyze beams for various loading conditions',
                'icon' => 'bi-bricks',
                'usage' => 756
            ]
        ];
    }
    
    private function getCalculatorsByCategory($category)
    {
        $allCalculators = [
            'civil' => [
                [
                    'slug' => 'concrete-volume',
                    'name' => 'Concrete Volume Calculator',
                    'description' => 'Calculate volume of concrete required for construction elements',
                    'icon' => 'bi-cone',
                    'formula' => 'Volume = Length × Width × Height'
                ],
                [
                    'slug' => 'rebar-calculation',
                    'name' => 'Rebar Calculation',
                    'description' => 'Calculate reinforcement steel quantity and weight',
                    'icon' => 'bi-bricks',
                    'formula' => 'Weight = Length × Number × Unit Weight'
                ],
                [
                    'slug' => 'brickwork-quantity',
                    'name' => 'Brickwork Quantity',
                    'description' => 'Calculate number of bricks and mortar required',
                    'icon' => 'bi-square',
                    'formula' => 'Bricks = Wall Area / (Brick Length × Brick Height)'
                ]
            ],
            'electrical' => [
                [
                    'slug' => 'load-calculation',
                    'name' => 'Load Calculation',
                    'description' => 'Calculate electrical load for buildings',
                    'icon' => 'bi-lightning-charge',
                    'formula' => 'Total Load = Σ(Connected Load × Demand Factor)'
                ],
                [
                    'slug' => 'voltage-drop',
                    'name' => 'Voltage Drop Calculator',
                    'description' => 'Calculate voltage drop in electrical circuits',
                    'icon' => 'bi-graph-down',
                    'formula' => 'VD = (2 × L × I × R) / 1000'
                ],
                [
                    'slug' => 'wire-sizing',
                    'name' => 'Wire Sizing',
                    'description' => 'Determine appropriate wire size for circuits',
                    'icon' => 'bi-plug',
                    'formula' => 'Based on current, distance, and voltage drop'
                ]
            ]
        ];
        
        return $allCalculators[$category] ?? [];
    }
    
    private function getCategoryInfo($category)
    {
        $categories = $this->getCalculatorCategories();
        return $categories[$category] ?? null;
    }
    
    private function getCalculatorInfo($category, $tool)
    {
        $calculators = $this->getCalculatorsByCategory($category);
        
        foreach ($calculators as $calc) {
            if ($calc['slug'] === $tool) {
                return $calc;
            }
        }
        
        return null;
    }
    
    private function notFound()
    {
        http_response_code(404);
        return view('errors/404');
    }
}
