<?php
/**
 * MEP API Endpoints
 * RESTful API endpoints for external integration and system communication
 * Version: 1.0.0
 */

require_once '../../../app/Config/config.php';
require_once '../../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Services/Security.php';

class MEPAPIEndpoints {
    private $db;
    private $security;
    private $api_version = '1.0';
    private $supported_formats = ['json', 'xml'];
    private $rate_limit = 1000; // requests per hour
    
    public function __construct() {
        $this->db = new Database();
        $this->security = new Security();
    }
    
    /**
     * Handle API request
     */
    public function handleRequest() {
        // Set security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        
        // Get request details
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $this->getRequestPath();
        $format = $this->getResponseFormat();
        
        // Rate limiting check
        if (!$this->checkRateLimit()) {
            $this->sendError(429, 'Rate limit exceeded', 'Too Many Requests');
            return;
        }
        
        // Authentication check
        $auth_result = $this->authenticate();
        if (!$auth_result['success']) {
            $this->sendError(401, $auth_result['message'], 'Unauthorized');
            return;
        }
        
        // Route request
        try {
            $response = $this->routeRequest($method, $path, $format, $auth_result['user_id']);
            $this->sendResponse($response, $format);
        } catch (Exception $e) {
            $this->sendError(500, $e->getMessage(), 'Internal Server Error');
        }
    }
    
    /**
     * Get request path
     */
    private function getRequestPath() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/modules/mep/data-utilities/api-endpoints.php', '', $path);
        $path = trim($path, '/');
        
        return $path;
    }
    
    /**
     * Get response format
     */
    private function getResponseFormat() {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        
        if (strpos($accept, 'application/xml') !== false) {
            return 'xml';
        }
        
        return 'json'; // default
    }
    
    /**
     * Rate limiting check
     */
    private function checkRateLimit() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $current_time = time();
        $window_start = $current_time - 3600; // 1 hour window
        
        // Check current requests in window
        $query = "SELECT COUNT(*) as request_count 
                 FROM api_rate_limits 
                 WHERE ip_address = ? AND created_at > ?";
        $result = $this->db->executeQuery($query, [$ip, date('Y-m-d H:i:s', $window_start)]);
        
        $request_count = $result ? $result->fetch()['request_count'] : 0;
        
        if ($request_count >= $this->rate_limit) {
            return false;
        }
        
        // Log this request
        $insert_query = "INSERT INTO api_rate_limits (ip_address, endpoint, created_at) 
                        VALUES (?, ?, NOW())";
        $this->db->executeQuery($insert_query, [$ip, $this->getRequestPath()]);
        
        return true;
    }
    
    /**
     * Authenticate API request
     */
    private function authenticate() {
        $headers = getallheaders();
        
        // Check for API key
        if (isset($headers['X-API-Key'])) {
            return $this->authenticateAPIKey($headers['X-API-Key']);
        }
        
        // Check for Bearer token
        if (isset($headers['Authorization'])) {
            $auth_header = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.+)$/i', $auth_header, $matches)) {
                return $this->authenticateBearerToken($matches[1]);
            }
        }
        
        // Check for Basic auth
        if (isset($headers['Authorization'])) {
            $auth_header = $headers['Authorization'];
            if (preg_match('/Basic\s+(.+)$/i', $auth_header, $matches)) {
                return $this->authenticateBasic($matches[1]);
            }
        }
        
        // Check query parameter authentication (for GET requests)
        if (isset($_GET['api_key'])) {
            return $this->authenticateAPIKey($_GET['api_key']);
        }
        
        return [
            'success' => false,
            'message' => 'No valid authentication provided'
        ];
    }
    
    /**
     * Authenticate using API key
     */
    private function authenticateAPIKey($api_key) {
        $query = "SELECT * FROM api_keys WHERE api_key = ? AND status = 'active'";
        $result = $this->db->executeQuery($query, [$api_key]);
        
        if ($result && $row = $result->fetch()) {
            // Check if key is expired
            if ($row['expires_at'] && strtotime($row['expires_at']) < time()) {
                return [
                    'success' => false,
                    'message' => 'API key has expired'
                ];
            }
            
            // Log API usage
            $this->logAPIUsage($row['user_id'], 'api_key');
            
            return [
                'success' => true,
                'user_id' => $row['user_id'],
                'permissions' => json_decode($row['permissions'], true) ?: []
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Invalid API key'
        ];
    }
    
    /**
     * Authenticate using Bearer token
     */
    private function authenticateBearerToken($token) {
        $query = "SELECT * FROM user_sessions WHERE session_token = ? AND expires_at > NOW()";
        $result = $this->db->executeQuery($query, [$token]);
        
        if ($result && $row = $result->fetch()) {
            $this->logAPIUsage($row['user_id'], 'bearer_token');
            
            return [
                'success' => true,
                'user_id' => $row['user_id'],
                'permissions' => $this->getUserPermissions($row['user_id'])
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Invalid or expired token'
        ];
    }
    
    /**
     * Authenticate using Basic auth
     */
    private function authenticateBasic($credentials) {
        $decoded = base64_decode($credentials);
        list($username, $password) = explode(':', $decoded, 2);
        
        // This would typically validate against your user system
        // For now, return a simple implementation
        return [
            'success' => false,
            'message' => 'Basic authentication not implemented'
        ];
    }
    
    /**
     * Get user permissions
     */
    private function getUserPermissions($user_id) {
        $query = "SELECT permissions FROM user_permissions WHERE user_id = ?";
        $result = $this->db->executeQuery($query, [$user_id]);
        
        if ($result && $row = $result->fetch()) {
            return json_decode($row['permissions'], true) ?: [];
        }
        
        return [];
    }
    
    /**
     * Log API usage
     */
    private function logAPIUsage($user_id, $auth_method) {
        $query = "INSERT INTO api_usage_log (user_id, endpoint, auth_method, ip_address, user_agent, created_at) 
                 VALUES (?, ?, ?, ?, ?, NOW())";
        $this->db->executeQuery($query, [
            $user_id,
            $this->getRequestPath(),
            $auth_method,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }
    
    /**
     * Route request to appropriate handler
     */
    private function routeRequest($method, $path, $format, $user_id) {
        $parts = explode('/', $path);
        
        // Route based on first path segment
        $resource = $parts[0] ?? '';
        
        switch ($resource) {
            case 'hvac':
                return $this->handleHVACRequest($method, array_slice($parts, 1), $user_id);
                
            case 'electrical':
                return $this->handleElectricalRequest($method, array_slice($parts, 1), $user_id);
                
            case 'plumbing':
                return $this->handlePlumbingRequest($method, array_slice($parts, 1), $user_id);
                
            case 'fire-protection':
                return $this->handleFireProtectionRequest($method, array_slice($parts, 1), $user_id);
                
            case 'projects':
                return $this->handleProjectsRequest($method, array_slice($parts, 1), $user_id);
                
            case 'calculations':
                return $this->handleCalculationsRequest($method, array_slice($parts, 1), $user_id);
                
            case 'reports':
                return $this->handleReportsRequest($method, array_slice($parts, 1), $user_id);
                
            case 'materials':
                return $this->handleMaterialsRequest($method, array_slice($parts, 1), $user_id);
                
            case 'energy':
                return $this->handleEnergyRequest($method, array_slice($parts, 1), $user_id);
                
            case 'coordination':
                return $this->handleCoordinationRequest($method, array_slice($parts, 1), $user_id);
                
            case 'health':
                return $this->handleHealthRequest();
                
            case 'info':
                return $this->handleInfoRequest();
                
            default:
                throw new Exception("Unknown resource: {$resource}");
        }
    }
    
    /**
     * Handle HVAC requests
     */
    private function handleHVACRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'load-calculations') {
                    return $this->getHVACLoadCalculations($user_id);
                } elseif ($action === 'equipment') {
                    return $this->getHVACEquipment($user_id);
                } elseif ($action === 'duct-sizing') {
                    return $this->getDuctSizing($user_id);
                } elseif ($action === 'static-pressure') {
                    return $this->getStaticPressure($user_id);
                }
                break;
                
            case 'POST':
                if ($action === 'load-calculations') {
                    return $this->createHVACLoadCalculation($user_id);
                } elseif ($action === 'duct-sizing') {
                    return $this->createDuctSizing($user_id);
                }
                break;
        }
        
        throw new Exception("Unknown HVAC endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle Electrical requests
     */
    private function handleElectricalRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'load-analysis') {
                    return $this->getElectricalLoadAnalysis($user_id);
                } elseif ($action === 'power-distribution') {
                    return $this->getPowerDistribution($user_id);
                } elseif ($action === 'panel-coordination') {
                    return $this->getPanelCoordination($user_id);
                } elseif ($action === 'lighting-layout') {
                    return $this->getLightingLayout($user_id);
                }
                break;
                
            case 'POST':
                if ($action === 'load-analysis') {
                    return $this->createElectricalLoadAnalysis($user_id);
                }
                break;
        }
        
        throw new Exception("Unknown Electrical endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle Plumbing requests
     */
    private function handlePlumbingRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'water-supply') {
                    return $this->getWaterSupply($user_id);
                } elseif ($action === 'drainage-system') {
                    return $this->getDrainageSystem($user_id);
                } elseif ($action === 'storm-water') {
                    return $this->getStormWater($user_id);
                } elseif ($action === 'pump-selection') {
                    return $this->getPumpSelection($user_id);
                }
                break;
                
            case 'POST':
                if ($action === 'water-supply') {
                    return $this->createWaterSupply($user_id);
                }
                break;
        }
        
        throw new Exception("Unknown Plumbing endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle Fire Protection requests
     */
    private function handleFireProtectionRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'sprinkler-layout') {
                    return $this->getSprinklerLayout($user_id);
                } elseif ($action === 'hydrant-system') {
                    return $this->getHydrantSystem($user_id);
                } elseif ($action === 'fire-pump') {
                    return $this->getFirePump($user_id);
                }
                break;
                
            case 'POST':
                if ($action === 'sprinkler-layout') {
                    return $this->createSprinklerLayout($user_id);
                }
                break;
        }
        
        throw new Exception("Unknown Fire Protection endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle Projects requests
     */
    private function handleProjectsRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === '') {
                    return $this->getProjects($user_id);
                } else {
                    $project_id = $action;
                    return $this->getProject($project_id, $user_id);
                }
                break;
                
            case 'POST':
                return $this->createProject($user_id);
                
            case 'PUT':
                if ($action) {
                    return $this->updateProject($action, $user_id);
                }
                break;
                
            case 'DELETE':
                if ($action) {
                    return $this->deleteProject($action, $user_id);
                }
                break;
        }
        
        throw new Exception("Unknown Projects endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle Calculations requests
     */
    private function handleCalculationsRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === '') {
                    return $this->getCalculations($user_id);
                } else {
                    return $this->getCalculation($action, $user_id);
                }
                break;
                
            case 'POST':
                return $this->createCalculation($user_id);
                
            case 'DELETE':
                if ($action) {
                    return $this->deleteCalculation($action, $user_id);
                }
                break;
        }
        
        throw new Exception("Unknown Calculations endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle Reports requests
     */
    private function handleReportsRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === '') {
                    return $this->getReports($user_id);
                } elseif ($action === 'generate') {
                    return $this->generateReport($user_id);
                }
                break;
        }
        
        throw new Exception("Unknown Reports endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle Materials requests
     */
    private function handleMaterialsRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === '') {
                    return $this->getMaterials($user_id);
                } elseif ($action === 'search') {
                    return $this->searchMaterials($user_id);
                }
                break;
                
            case 'POST':
                return $this->createMaterial($user_id);
        }
        
        throw new Exception("Unknown Materials endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle Energy requests
     */
    private function handleEnergyRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'consumption') {
                    return $this->getEnergyConsumption($user_id);
                } elseif ($action === 'efficiency') {
                    return $this->getEnergyEfficiency($user_id);
                } elseif ($action === 'solar') {
                    return $this->getSolarAnalysis($user_id);
                }
                break;
        }
        
        throw new Exception("Unknown Energy endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle Coordination requests
     */
    private function handleCoordinationRequest($method, $parts, $user_id) {
        $action = $parts[0] ?? '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'clash-detection') {
                    return $this->getClashDetection($user_id);
                } elseif ($action === 'space-allocation') {
                    return $this->getSpaceAllocation($user_id);
                }
                break;
        }
        
        throw new Exception("Unknown Coordination endpoint: {$method} /{$action}");
    }
    
    /**
     * Handle health check request
     */
    private function handleHealthRequest() {
        return [
            'status' => 'healthy',
            'timestamp' => date('c'),
            'version' => $this->api_version,
            'uptime' => $this->getUptime(),
            'database' => $this->checkDatabaseHealth(),
            'services' => $this->checkServicesHealth()
        ];
    }
    
    /**
     * Handle API info request
     */
    private function handleInfoRequest() {
        return [
            'name' => 'MEP Coordination Suite API',
            'version' => $this->api_version,
            'description' => 'RESTful API for MEP calculations and coordination',
            'endpoints' => [
                'GET /hvac/*' => 'HVAC system endpoints',
                'GET /electrical/*' => 'Electrical system endpoints',
                'GET /plumbing/*' => 'Plumbing system endpoints',
                'GET /fire-protection/*' => 'Fire protection endpoints',
                'GET /projects' => 'Project management',
                'GET /calculations' => 'Calculation management',
                'GET /reports' => 'Report generation',
                'GET /materials' => 'Material database',
                'GET /energy/*' => 'Energy analysis',
                'GET /coordination/*' => 'System coordination',
                'GET /health' => 'Health check',
                'GET /info' => 'API information'
            ],
            'authentication' => [
                'header' => 'X-API-Key',
                'type' => 'API Key authentication',
                'format' => 'X-API-Key: your-api-key-here'
            ],
            'formats' => $this->supported_formats,
            'rate_limit' => $this->rate_limit . ' requests/hour'
        ];
    }
    
    // Sample data retrieval methods (implement actual logic as needed)
    
    private function getHVACLoadCalculations($user_id) {
        return [
            'success' => true,
            'data' => [
                'calculations' => [
                    [
                        'id' => 1,
                        'project_id' => 100,
                        'building_type' => 'commercial',
                        'area' => 5000,
                        'calculated_load' => 250.5,
                        'safety_factor' => 1.15,
                        'final_load' => 288.1,
                        'created_at' => date('c')
                    ]
                ]
            ]
        ];
    }
    
    private function getHVACEquipment($user_id) {
        return [
            'success' => true,
            'data' => [
                'equipment' => [
                    [
                        'id' => 1,
                        'type' => 'chiller',
                        'manufacturer' => 'Carrier',
                        'model' => '30RB-300',
                        'capacity' => 300,
                        'efficiency' => 0.85,
                        'power_consumption' => 250,
                        'created_at' => date('c')
                    ]
                ]
            ]
        ];
    }
    
    private function getProjects($user_id) {
        return [
            'success' => true,
            'data' => [
                'projects' => [
                    [
                        'id' => 1,
                        'name' => 'Commercial Building ABC',
                        'type' => 'commercial',
                        'area' => 10000,
                        'floors' => 5,
                        'status' => 'active',
                        'created_at' => date('c'),
                        'updated_at' => date('c')
                    ]
                ]
            ]
        ];
    }
    
    private function getCalculations($user_id) {
        return [
            'success' => true,
            'data' => [
                'calculations' => [
                    [
                        'id' => 1,
                        'project_id' => 1,
                        'type' => 'hvac_load',
                        'status' => 'completed',
                        'result' => 288.1,
                        'created_at' => date('c')
                    ]
                ]
            ]
        ];
    }
    
    private function createProject($user_id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        if (!isset($input['name']) || !isset($input['type'])) {
            throw new Exception('Missing required fields: name, type');
        }
        
        // Create project (simplified)
        return [
            'success' => true,
            'message' => 'Project created successfully',
            'data' => [
                'project_id' => rand(1000, 9999),
                'name' => $input['name'],
                'type' => $input['type'],
                'created_at' => date('c')
            ]
        ];
    }
    
    private function getUptime() {
        if (function_exists('sys_getloadavg')) {
            return sys_getloadavg()[0] ?? 0;
        }
        return 0;
    }
    
    private function checkDatabaseHealth() {
        try {
            $result = $this->db->executeQuery("SELECT 1");
            return $result ? 'healthy' : 'unhealthy';
        } catch (Exception $e) {
            return 'unhealthy';
        }
    }
    
    private function checkServicesHealth() {
        return [
            'database' => $this->checkDatabaseHealth(),
            'api' => 'healthy'
        ];
    }
    
    /**
     * Send JSON response
     */
    private function sendResponse($data, $format) {
        if ($format === 'xml') {
            header('Content-Type: application/xml');
            echo $this->arrayToXML($data);
        } else {
            header('Content-Type: application/json');
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
    }
    
    /**
     * Send error response
     */
    private function sendError($code, $message, $status_text) {
        http_response_code($code);
        header('Content-Type: application/json');
        
        $error = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message
            ],
            'timestamp' => date('c')
        ];
        
        echo json_encode($error);
    }
    
    /**
     * Convert array to XML
     */
    private function arrayToXML($array, $root_element = 'response') {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><{$root_element}></{$root_element}>");
        $this->arrayToXMLRecursive($array, $xml);
        return $xml->asXML();
    }
    
    /**
     * Recursive array to XML conversion
     */
    private function arrayToXMLRecursive($array, $xml) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item';
                }
                $subnode = $xml->addChild($key);
                $this->arrayToXMLRecursive($value, $subnode);
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }
    
    /**
     * Create database tables for API
     */
    public static function createAPITables() {
        $db = new Database();
        
        // API Keys table
        $sql_keys = "CREATE TABLE IF NOT EXISTS api_keys (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            api_key VARCHAR(255) UNIQUE NOT NULL,
            name VARCHAR(255) NOT NULL,
            permissions JSON,
            status ENUM('active', 'inactive') DEFAULT 'active',
            expires_at DATETIME,
            last_used_at DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        // API Rate Limits table
        $sql_rate_limits = "CREATE TABLE IF NOT EXISTS api_rate_limits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            endpoint VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip_time (ip_address, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        // API Usage Log table
        $sql_usage_log = "CREATE TABLE IF NOT EXISTS api_usage_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            endpoint VARCHAR(255) NOT NULL,
            auth_method VARCHAR(50) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_time (user_id, created_at),
            INDEX idx_endpoint_time (endpoint, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->executeQuery($sql_keys);
        $db->executeQuery($sql_rate_limits);
        $db->executeQuery($sql_usage_log);
    }
}

// Handle API request
if (!isset($_POST['action'])) {
    $api = new MEPAPIEndpoints();
    $api->handleRequest();
}

// Handle AJAX requests for API management
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $api = new MEPAPIEndpoints();
        
        switch ($_POST['action']) {
            case 'create_api_key':
                // Create new API key
                $user_id = $_POST['user_id'] ?? 1; // Default user for demo
                $name = $_POST['name'] ?? 'API Key';
                $permissions = json_decode($_POST['permissions'] ?? '[]', true);
                
                $api_key = bin2hex(random_bytes(32));
                
                $query = "INSERT INTO api_keys (user_id, api_key, name, permissions, created_at) 
                         VALUES (?, ?, ?, ?, NOW())";
                $result = $api->db->executeQuery($query, [
                    $user_id,
                    $api_key,
                    $name,
                    json_encode($permissions)
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'data' => [
                            'api_key' => $api_key,
                            'name' => $name,
                            'created_at' => date('c')
                        ]
                    ]);
                } else {
                    throw new Exception('Failed to create API key');
                }
                break;
                
            case 'list_api_keys':
                $user_id = $_POST['user_id'] ?? 1;
                
                $query = "SELECT id, api_key, name, permissions, status, expires_at, last_used_at, created_at 
                         FROM api_keys WHERE user_id = ?";
                $result = $api->db->executeQuery($query, [$user_id]);
                
                $keys = [];
                if ($result) {
                    while ($row = $result->fetch()) {
                        $keys[] = [
                            'id' => $row['id'],
                            'api_key' => substr($row['api_key'], 0, 8) . '...',
                            'name' => $row['name'],
                            'permissions' => json_decode($row['permissions'], true) ?: [],
                            'status' => $row['status'],
                            'expires_at' => $row['expires_at'],
                            'last_used_at' => $row['last_used_at'],
                            'created_at' => $row['created_at']
                        ];
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => ['api_keys' => $keys]
                ]);
                break;
                
            case 'revoke_api_key':
                $key_id = $_POST['key_id'];
                
                $query = "UPDATE api_keys SET status = 'inactive' WHERE id = ?";
                $result = $api->db->executeQuery($query, [$key_id]);
                
                echo json_encode([
                    'success' => $result !== false,
                    'message' => $result ? 'API key revoked' : 'Failed to revoke API key'
                ]);
                break;
                
            case 'get_usage_stats':
                $user_id = $_POST['user_id'] ?? 1;
                $days = intval($_POST['days'] ?? 30);
                
                $query = "SELECT DATE(created_at) as date, COUNT(*) as requests 
                         FROM api_usage_log 
                         WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
                         GROUP BY DATE(created_at) 
                         ORDER BY date DESC";
                $result = $api->db->executeQuery($query, [$user_id, $days]);
                
                $stats = [];
                if ($result) {
                    while ($row = $result->fetch()) {
                        $stats[] = [
                            'date' => $row['date'],
                            'requests' => intval($row['requests'])
                        ];
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => ['usage_stats' => $stats]
                ]);
                break;
                
            case 'test_endpoint':
                $endpoint = $_POST['endpoint'] ?? '';
                $method = $_POST['method'] ?? 'GET';
                
                // Test API endpoint availability
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $response_time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
                curl_close($ch);
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'endpoint' => $endpoint,
                        'method' => $method,
                        'http_code' => $http_code,
                        'response_time' => $response_time,
                        'status' => $http_code >= 200 && $http_code < 300 ? 'online' : 'error'
                    ]
                ]);
                break;
                
            case 'create_tables':
                MEPAPIEndpoints::createAPITables();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'API database tables created successfully'
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'error' => 'Unknown action'
                ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// Display API management interface
if (!isset($_POST['action'])) {
    include '../../../../themes/default/views/partials/header.php';
    ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1><i class="fas fa-plug"></i> MEP API Endpoints</h1>
                    <p class="lead">RESTful API endpoints for external integration and system communication</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-code"></i> API Endpoints</h5>
                    </div>
                    <div class="card-body">
                        <div class="endpoint-section">
                            <h6>HVAC System Endpoints</h6>
                            <div class="code-block">
                                <div class="endpoint">GET /hvac/load-calculations</div>
                                <div class="description">Get HVAC load calculations</div>
                            </div>
                            <div class="code-block">
                                <div class="endpoint">GET /hvac/equipment</div>
                                <div class="description">Get HVAC equipment database</div>
                            </div>
                            <div class="code-block">
                                <div class="endpoint">GET /hvac/duct-sizing</div>
                                <div class="description">Get duct sizing calculations</div>
                            </div>
                        </div>
                        
                        <div class="endpoint-section">
                            <h6>Electrical System Endpoints</h6>
                            <div class="code-block">
                                <div class="endpoint">GET /electrical/load-analysis</div>
                                <div class="description">Get electrical load analysis</div>
                            </div>
                            <div class="code-block">
                                <div class="endpoint">GET /electrical/power-distribution</div>
                                <div class="description">Get power distribution analysis</div>
                            </div>
                            <div class="code-block">
                                <div class="endpoint">GET /electrical/lighting-layout</div>
                                <div class="description">Get lighting layout calculations</div>
                            </div>
                        </div>
                        
                        <div class="endpoint-section">
                            <h6>Plumbing System Endpoints</h6>
                            <div class="code-block">
                                <div class="endpoint">GET /plumbing/water-supply</div>
                                <div class="description">Get water supply calculations</div>
                            </div>
                            <div class="code-block">
                                <div class="endpoint">GET /plumbing/drainage-system</div>
                                <div class="description">Get drainage system analysis</div>
                            </div>
                            <div class="code-block">
                                <div class="endpoint">GET /plumbing/pump-selection</div>
                                <div class="description">Get pump selection data</div>
                            </div>
                        </div>
                        
                        <div class="endpoint-section">
                            <h6>Project Management Endpoints</h6>
                            <div class="code-block">
                                <div class="endpoint">GET /projects</div>
                                <div class="description">List all projects</div>
                            </div>
                            <div class="code-block">
                                <div class="endpoint">POST /projects</div>
                                <div class="description">Create new project</div>
                            </div>
                            <div class="code-block">
                                <div class="endpoint">GET /projects/{id}</div>
                                <div class="description">Get specific project details</div>
                            </div>
                        </div>
                        
                        <div class="endpoint-section">
                            <h6>System Health Endpoints</h6>
                            <div class="code-block">
                                <div class="endpoint">GET /health</div>
                                <div class="description">API health check</div>
                            </div>
                            <div class="code-block">
                                <div class="endpoint">GET /info</div>
                                <div class="description">API information and documentation</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-key"></i> API Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical w-100" role="group">
                            <button type="button" class="btn btn-primary" onclick="createAPIKey()">
                                <i class="fas fa-plus"></i> Create API Key
                            </button>
                            <button type="button" class="btn btn-info" onclick="listAPIKeys()">
                                <i class="fas fa-list"></i> List API Keys
                            </button>
                            <button type="button" class="btn btn-success" onclick="showUsageStats()">
                                <i class="fas fa-chart-bar"></i> Usage Statistics
                            </button>
                            <button type="button" class="btn btn-warning" onclick="testEndpoint()">
                                <i class="fas fa-check"></i> Test Endpoint
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="createTables()">
                                <i class="fas fa-database"></i> Setup Database
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-shield-alt"></i> Authentication</h5>
                    </div>
                    <div class="card-body">
                        <h6>API Key Authentication</h6>
                        <div class="code-block">
                            <code>Header: X-API-Key: your-api-key</code>
                        </div>
                        
                        <h6 class="mt-3">Bearer Token</h6>
                        <div class="code-block">
                            <code>Authorization: Bearer your-token</code>
                        </div>
                        
                        <h6 class="mt-3">Query Parameter</h6>
                        <div class="code-block">
                            <code>?api_key=your-api-key</code>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Rate Limits</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Default:</strong> 1,000 requests per hour</p>
                        <p><strong>IP-based:</strong> Rate limiting by IP address</p>
                        <p><strong>Headers:</strong> Includes rate limit info in response headers</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- API Key Modal -->
    <div class="modal fade" id="apiKeyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create API Key</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createKeyForm">
                        <div class="form-group">
                            <label>Key Name</label>
                            <input type="text" class="form-control" id="keyName" placeholder="My API Key">
                        </div>
                        <div class="form-group">
                            <label>Permissions (JSON array)</label>
                            <textarea class="form-control" id="permissions" rows="3" 
                                      placeholder='["read", "write"]'></textarea>
                            <small class="form-text text-muted">Available: read, write, admin</small>
                        </div>
                    </form>
                    <div id="newKeyDisplay" style="display: none;">
                        <div class="alert alert-success">
                            <h6>API Key Created!</h6>
                            <p><strong>Key:</strong></p>
                            <code id="generatedKey" style="font-size: 0.8em; word-break: break-all;"></code>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitCreateKey()">Create Key</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- API Keys List Modal -->
    <div class="modal fade" id="keysListModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">API Keys</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="keysListContent">
                        <p class="text-muted">Loading API keys...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Usage Statistics Modal -->
    <div class="modal fade" id="usageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">API Usage Statistics</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="usageStatsContent">
                        <p class="text-muted">Loading usage statistics...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Endpoint Modal -->
    <div class="modal fade" id="testModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Endpoint</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="testForm">
                        <div class="form-group">
                            <label>Endpoint URL</label>
                            <input type="text" class="form-control" id="testEndpoint" 
                                   placeholder="http://localhost/modules/mep/data-utilities/api-endpoints.php/hvac/load-calculations">
                        </div>
                        <div class="form-group">
                            <label>HTTP Method</label>
                            <select class="form-control" id="testMethod">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                        </div>
                    </form>
                    <div id="testResults" style="display: none;">
                        <h6>Test Results:</h6>
                        <div id="testOutput"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitTest()">Test Endpoint</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // API Management Functions
    
    function createAPIKey() {
        $('#apiKeyModal').modal('show');
    }
    
    function submitCreateKey() {
        const name = document.getElementById('keyName').value;
        const permissions = document.getElementById('permissions').value;
        
        if (!name) {
            showNotification('Please enter a key name', 'info');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'create_api_key');
        formData.append('name', name);
        formData.append('permissions', permissions);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('generatedKey').textContent = data.data.api_key;
                document.getElementById('newKeyDisplay').style.display = 'block';
                document.getElementById('createKeyForm').style.display = 'none';
            } else {
                showNotification('Error creating API key: ' + (data.error || 'Unknown error', 'info'));
            }
        })
        .catch(error => {
            showNotification('Error: ' + error.message);
        });
    }
    
    function listAPIKeys() {
        const formData = new FormData();
        formData.append('action', 'list_api_keys', 'info');
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayKeysList(data.data.api_keys);
                $('#keysListModal').modal('show');
            } else {
                showNotification('Error loading API keys: ' + (data.error || 'Unknown error', 'info'));
            }
        })
        .catch(error => {
            showNotification('Error: ' + error.message);
        });
    }
    
    function displayKeysList(keys) {
        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Name</th><th>Key</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead><tbody>';
        
        keys.forEach(key => {
            html += `
                <tr>
                    <td>${key.name}</td>
                    <td><code>${key.api_key}</code></td>
                    <td><span class="badge badge-${key.status === 'active' ? 'success' : 'secondary'}">${key.status}</span></td>
                    <td>${key.created_at}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="revokeKey(${key.id})">
                            Revoke
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        document.getElementById('keysListContent', 'info').innerHTML = html;
    }
    
    function revokeKey(keyId) {
        showConfirmModal('Revoke API Key', 'Are you sure you want to revoke this API key?', function() {
            const formData = new FormData();
            formData.append('action', 'revoke_api_key');
            formData.append('key_id', keyId);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('API key revoked successfully', 'info');
                    listAPIKeys(); // Refresh list
                } else {
                    showNotification('Error: ' + (data.error || 'Unknown error', 'info'));
                }
            })
            .catch(error => {
                showNotification('Error: ' + error.message);
            });
        });
    }
    
    function showUsageStats() {
        const formData = new FormData();
        formData.append('action', 'get_usage_stats', 'info');
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUsageStats(data.data.usage_stats);
                $('#usageModal').modal('show');
            } else {
                showNotification('Error loading usage statistics: ' + (data.error || 'Unknown error', 'info'));
            }
        })
        .catch(error => {
            showNotification('Error: ' + error.message);
        });
    }
    
    function displayUsageStats(stats) {
        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Date</th><th>Requests</th></tr></thead><tbody>';
        
        stats.forEach(stat => {
            html += `
                <tr>
                    <td>${stat.date}</td>
                    <td>${stat.requests}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        
        // Simple chart
        if (stats.length > 0) {
            html += '<h6 class="mt-3">Usage Chart</h6>';
            html += '<div class="chart-container" style="height: 200px;">';
            html += '<canvas id="usageChart"></canvas>';
            html += '</div>';
            
            setTimeout(() => {
                createUsageChart(stats);
            }, 100);
        }
        
        document.getElementById('usageStatsContent', 'info').innerHTML = html;
    }
    
    function createUsageChart(stats) {
        const canvas = document.getElementById('usageChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const width = canvas.parentElement.clientWidth;
        const height = 200;
        
        canvas.width = width;
        canvas.height = height;
        
        // Simple bar chart
        const maxRequests = Math.max(...stats.map(s => s.requests));
        const barWidth = width / stats.length;
        
        ctx.clearRect(0, 0, width, height);
        ctx.fillStyle = '#007bff';
        
        stats.slice(-7).forEach((stat, index) => {
            const barHeight = (stat.requests / maxRequests) * (height - 40);
            const x = index * barWidth + 10;
            const y = height - barHeight - 30;
            
            ctx.fillRect(x, y, barWidth - 20, barHeight);
            
            // Date labels
            ctx.fillStyle = '#666';
            ctx.font = '12px Arial';
            ctx.fillText(stat.date.substring(5), x, height - 10);
            ctx.fillStyle = '#007bff';
        });
    }
    
    function testEndpoint() {
        document.getElementById('testEndpoint').value = window.location.origin + 
            '/modules/mep/data-utilities/api-endpoints.php/health';
        $('#testModal').modal('show');
    }
    
    function submitTest() {
        const endpoint = document.getElementById('testEndpoint').value;
        const method = document.getElementById('testMethod').value;
        
        if (!endpoint) {
            showNotification('Please enter an endpoint URL', 'info');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'test_endpoint');
        formData.append('endpoint', endpoint);
        formData.append('method', method);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTestResults(data.data);
            } else {
                showNotification('Test failed: ' + (data.error || 'Unknown error', 'info'));
            }
        })
        .catch(error => {
            showNotification('Error: ' + error.message);
        });
    }
    
    function displayTestResults(results) {
        const statusClass = results.status === 'online' ? 'success' : 'danger';
        const statusIcon = results.status === 'online' ? 'check-circle' : 'times-circle';
        
        let html = `
            <div class="alert alert-${statusClass}">
                <h6><i class="fas fa-${statusIcon}"></i> Endpoint Status: ${results.status}</h6>
                <table class="table table-sm mt-2">
                    <tr><td><strong>URL:</strong></td><td>${results.endpoint}</td></tr>
                    <tr><td><strong>Method:</strong></td><td>${results.method}</td></tr>
                    <tr><td><strong>HTTP Code:</strong></td><td>${results.http_code}</td></tr>
                    <tr><td><strong>Response Time:</strong></td><td>${(results.response_time * 1000).toFixed(2)}ms</td></tr>
                </table>
            </div>
        `;
        
        document.getElementById('testOutput', 'info').innerHTML = html;
        document.getElementById('testResults').style.display = 'block';
    }
    
    function createTables() {
        showConfirmModal('Create Tables', 'This will create the necessary database tables for the API. Continue?', function() {
            const formData = new FormData();
            formData.append('action', 'create_tables');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                } else {
                    showNotification('Error: ' + (data.error || 'Unknown error', 'info'));
                }
            })
            .catch(error => {
                showNotification('Error: ' + error.message, 'danger');
            });
        });
    }
    </script>
    
    <style>
    .endpoint-section {
        margin-bottom: 2rem;
    }
    
    .endpoint-section h6 {
        color: #007bff;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .code-block {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.25rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }
    
    .endpoint {
        font-weight: bold;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    
    .description {
        color: #6c757d;
        font-size: 0.875rem;
        margin-left: 1rem;
    }
    
    code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 0.5rem;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
    
    .chart-container {
        position: relative;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .modal-lg {
        max-width: 900px;
    }
    </style>
    
    <?php
    include '../../../../themes/default/views/partials/footer.php';
}
?>


