<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Services\CalculationService;

class ApiController extends Controller
{
    private $calculationService;
    
    public function __construct()
    {
        $this->calculationService = new CalculationService();
        header('Content-Type: application/json');
    }
    
    public function calculate()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->jsonError('Invalid JSON input');
            return;
        }
        
        if (!isset($input['category']) || !isset($input['tool']) || !isset($input['data'])) {
            $this->jsonError('Missing required parameters: category, tool, data');
            return;
        }
        
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        
        // Validate API key if no user session
        if (!$user && $apiKey) {
            $userId = $this->validateApiKey($apiKey);
            if (!$userId) {
                $this->jsonError('Invalid API key', 401);
                return;
            }
        }
        
        if (!$user && !$apiKey) {
            $this->jsonError('Authentication required', 401);
            return;
        }
        
        $result = $this->calculationService->performCalculation(
            $input['category'],
            $input['tool'],
            $input['data'],
            $userId
        );
        
        echo json_encode($result);
    }
    
    public function getCalculators()
    {
        $calculators = $this->getAvailableCalculators();
        
        echo json_encode([
            'success' => true,
            'data' => $calculators,
            'count' => count($calculators)
        ]);
    }
    
    public function getCalculator($category, $tool)
    {
        $calculator = $this->getCalculatorInfo($category, $tool);
        
        if (!$calculator) {
            $this->jsonError('Calculator not found', 404);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $calculator
        ]);
    }
    
    public function getUserCalculations()
    {
        $user = Auth::user();
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        
        if (!$user && $apiKey) {
            $userId = $this->validateApiKey($apiKey);
            if (!$userId) {
                $this->jsonError('Invalid API key', 401);
                return;
            }
        } else if (!$user) {
            $this->jsonError('Authentication required', 401);
            return;
        } else {
            $userId = $user->id;
        }
        
        $page = $_GET['page'] ?? 1;
        $limit = min($_GET['limit'] ?? 50, 100); // Max 100 per request
        $offset = ($page - 1) * $limit;
        
        $calculations = $this->calculationService->getUserHistory($userId, $limit, $offset);
        
        echo json_encode([
            'success' => true,
            'data' => $calculations,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => count($calculations)
            ]
        ]);
    }
    
    public function getCalculation($id)
    {
        $user = Auth::user();
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        $userId = null;
        
        if ($user) {
            $userId = $user->id;
        } elseif ($apiKey) {
            $userId = $this->validateApiKey($apiKey);
        }
        
        if (!$userId) {
            $this->jsonError('Authentication required', 401);
            return;
        }
        
        $calculation = $this->calculationService->getCalculationById($id, $userId);
        
        if (!$calculation) {
            $this->jsonError('Calculation not found', 404);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $calculation
        ]);
    }
    
    private function validateApiKey($apiKey)
    {
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT user_id FROM api_keys 
            WHERE api_key = ? AND is_active = 1 AND expires_at > NOW()
        ");
        $stmt->execute([$apiKey]);
        $result = $stmt->fetch();
        
        return $result ? $result['user_id'] : null;
    }
    
    private function getAvailableCalculators()
    {
        // This would typically come from database
        return [
            [
                'category' => 'civil',
                'tools' => [
                    [
                        'slug' => 'concrete-volume',
                        'name' => 'Concrete Volume Calculator',
                        'description' => 'Calculate volume of concrete required',
                        'inputs' => [
                            ['name' => 'length', 'type' => 'number', 'required' => true, 'label' => 'Length (m)'],
                            ['name' => 'width', 'type' => 'number', 'required' => true, 'label' => 'Width (m)'],
                            ['name' => 'height', 'type' => 'number', 'required' => true, 'label' => 'Height (m)']
                        ]
                    ]
                ]
            ]
        ];
    }
    
    private function getCalculatorInfo($category, $tool)
    {
        $calculators = $this->getAvailableCalculators();
        
        foreach ($calculators as $cat) {
            if ($cat['category'] === $category) {
                foreach ($cat['tools'] as $calculator) {
                    if ($calculator['slug'] === $tool) {
                        return $calculator;
                    }
                }
            }
        }
        
        return null;
    }
    
    private function jsonError($message, $code = 400)
    {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
    }
}
