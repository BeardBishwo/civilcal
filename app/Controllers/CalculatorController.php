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
        parent::__construct();
        $this->calculationService = new CalculationService();
    }

    public function index()
    {
        $categories = $this->getCalculatorCategories();
        $featuredCalculators = $this->getFeaturedCalculators();

        $this->view->render('home/index', [
            'categories' => $categories,
            'featuredCalculators' => $featuredCalculators,
            'stats' => ['calculators' => 56, 'users' => 1234, 'calculations' => 15420, 'countries' => 25],
            'testimonials' => [],
            'user' => null,
            'viewHelper' => $this->view
        ]);
    }

    public function dashboard()
    {
        $this->view->render('dashboard');
    }

    public function category($category)
    {
        $all = CalculatorFactory::getAvailableCalculators();
        $list = array_values(array_filter($all, function ($c) use ($category) {
            return isset($c['category']) && strtolower($c['category']) === strtolower($category);
        }));

        if (empty($list)) {
            http_response_code(404);
            echo '404 - Category Not Found';
            return;
        }

        $title = ucwords(str_replace(['-', '_'], ' ', $category)) . ' Calculators';
        $this->view->render('calculators/category', [
            'title' => $title,
            'category' => $category,
            'calculators' => $list,
        ]);
    }

    public function tool($category, $tool)
    {
        $all = CalculatorFactory::getAvailableCalculators();
        $match = null;
        foreach ($all as $c) {
            if ((isset($c['category']) && strtolower($c['category']) === strtolower($category))
                && (isset($c['slug']) && strtolower($c['slug']) === strtolower($tool))
            ) {
                $match = $c;
                break;
            }
        }

        if (!$match) {
            http_response_code(404);
            echo '404 - Calculator Not Found';
            return;
        }

        $title = ($match['name'] ?? ucwords(str_replace(['-', '_'], ' ', $tool)));
        $this->view->render('calculators/tool', [
            'title' => $title,
            'category' => $category,
            'tool' => $tool,
            'calculator' => $match,
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

    /**
     * Execute calculator calculation (API endpoint)
     * Supports HTTP Basic Auth for testing
     */
    public function execute($module, $function)
    {
        header('Content-Type: application/json');

        try {
            // Support HTTP Basic Auth for API testing
            $userId = null;
            if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                $user = \App\Models\User::findByUsername($_SERVER['PHP_AUTH_USER']);
                if ($user) {
                    $userArray = is_array($user) ? $user : (array) $user;
                    if (password_verify($_SERVER['PHP_AUTH_PW'], $userArray['password'])) {
                        $userId = $userArray['id'];
                    }
                }
            } else {
                // Fall back to session auth
                $user = Auth::user();
                $userId = $user ? $user->id : null;
            }

            // Get input from JSON body
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON input']);
                return;
            }

            $inputValues = $input['input_values'] ?? [];

            // Validate input
            if (empty($inputValues)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing input_values']);
                return;
            }

            // Try to create the calculator to check if it exists
            $testCalculator = \App\Calculators\CalculatorFactory::create($module, $function);

            if (!$testCalculator) {
                // If calculator class doesn't exist, check in available calculators list
                $all = \App\Calculators\CalculatorFactory::getAvailableCalculators();
                $found = false;
                foreach ($all as $calc) {
                    if ((isset($calc['category']) && strtolower($calc['category']) === strtolower($module))
                        && (isset($calc['slug']) && strtolower($calc['slug']) === strtolower($function))
                    ) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Calculator not found']);
                    return;
                }
            }

            // Perform calculation
            $result = $this->calculationService->performCalculation(
                $module,
                $function,
                $inputValues,
                $userId
            );

            // Return result
            if (isset($result['success']) && $result['success']) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Calculation failed',
                'message' => $e->getMessage()
            ]);
        }
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

    public function traditionalUnits()
    {
        $this->view->render('calculators/nepali', [
            'title' => 'Nepali Unit Calculator',
            'description' => 'Professional conversion between traditional Nepali land measurement units.',
            'page_title' => 'Nepali Unit Calculator - Engineering Toolkit Pro'
        ]);
    }

    private function notFound()
    {
        http_response_code(404);
        echo '404 - Page Not Found';
    }
}
