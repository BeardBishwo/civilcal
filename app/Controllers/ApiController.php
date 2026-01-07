<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Services\CalculationService;
use App\Calculators\CalculatorFactory;

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
        
        $projectId = $input['project_id'] ?? null;

        $result = $this->calculationService->performCalculation(
            $input['category'],
            $input['tool'],
            $input['data'],
            $userId,
            $projectId
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
        $flat = CalculatorFactory::getAvailableCalculators();
        $grouped = [];
        foreach ($flat as $c) {
            $cat = $c['category'] ?? 'general';
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [ 'category' => $cat, 'tools' => [] ];
            }
            $grouped[$cat]['tools'][] = [
                'slug' => $c['slug'] ?? '',
                'name' => $c['name'] ?? ($c['slug'] ?? ''),
                'subcategory' => $c['subcategory'] ?? null,
                'path' => $c['path'] ?? null
            ];
        }
        return array_values($grouped);
    }
    
    private function getCalculatorInfo($category, $tool)
    {
        foreach ($this->getAvailableCalculators() as $cat) {
            if (($cat['category'] ?? '') === $category) {
                foreach ($cat['tools'] as $calculator) {
                    if (($calculator['slug'] ?? '') === $tool) {
                        return $calculator;
                    }
                }
            }
        }
        return null;
    }
    
    public function traditionalUnitsConvert()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['value']) || !isset($input['from_unit']) || !isset($input['to_unit'])) {
            $this->jsonError('Missing required parameters: value, from_unit, to_unit');
            return;
        }
        
        $calculator = new \App\Calculators\TraditionalUnitsCalculator();
        $metricUnits = $calculator->getMetricUnits();
        
        if (isset($metricUnits[$input['to_unit']])) {
            $result = $calculator->convertToMetric((float)$input['value'], $input['from_unit'], $input['to_unit']);
        } else {
            $result = $calculator->convertBetweenUnits((float)$input['value'], $input['from_unit'], $input['to_unit']);
        }
        
        echo json_encode($result);
    }
    
    public function traditionalUnitsAllConversions()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['value']) || !isset($input['from_unit'])) {
            $this->jsonError('Missing required parameters: value, from_unit');
            return;
        }
        
        $calculator = new \App\Calculators\TraditionalUnitsCalculator();
        $result = $calculator->getAllConversions(
            (float)$input['value'], 
            $input['from_unit'], 
            $input['metric_unit'] ?? 'sq_feet'
        );
        
        echo json_encode($result);
    }

    public function converterData($slug)
    {
        $category = $this->db->findOne('calc_unit_categories', ['slug' => $slug]);
        
        if (!$category) {
            $this->jsonError('Category not found', 404);
            return;
        }

        $units = $this->db->find('calc_units', ['category_id' => $category['id']], 'display_order ASC');
        
        // B2B: Fetch Campaign
        $campaignModel = new \App\Models\Campaign();
        $campaign = $campaignModel->getActiveForCalculator($slug);
        
        if ($campaign) {
            $campaignModel->recordImpression(
                $campaign['id'], 
                $_SESSION['user_id'] ?? null, 
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            );
        }

        echo json_encode([
            'success' => true,
            'category' => $category,
            'units' => $units,
            'campaign' => $campaign
        ]);
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
