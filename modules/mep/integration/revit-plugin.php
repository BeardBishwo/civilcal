<?php
require_once '../../../app/Config/config.php';

class RevitPluginIntegration {
    /**
     * @var \App\Core\Database|\PDO|\mysqli|null
     * Database connection. Could be the app Database wrapper (PDO), a raw PDO
     * instance, or a legacy mysqli connection. PHPDoc helps static analysis.
     */
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function registerRevitPlugin($pluginData) {
        try {
            $sql = "INSERT INTO mep_revit_plugins (plugin_name, plugin_version, api_key, plugin_path, 
                    features, is_active, created_date) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            // mysqli_stmt::bind_param requires variables (passed by reference).
            $p_name = $pluginData['name'];
            $p_version = $pluginData['version'];
            $p_api_key = $pluginData['api_key'];
            $p_path = $pluginData['path'];
            $p_features = json_encode($pluginData['features']);
            $p_active = $pluginData['active'] ?? 1;

            $stmt->bind_param("sssssi",
                $p_name,
                $p_version,
                $p_api_key,
                $p_path,
                $p_features,
                $p_active
            );
            
            $stmt->execute();
            $pluginId = $this->db->insert_id;
            
            return [
                'success' => true,
                'plugin_id' => $pluginId,
                'message' => 'Revit plugin registered successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function syncWithRevit($projectId, $revitModelPath, $userId) {
        try {
            // Create sync record
            $syncId = $this->createSyncRecord($projectId, 'revit', 'sync_started');
            
            // Process Revit model
            $syncResult = $this->processRevitModel($revitModelPath, $projectId, $userId);
            
            // Update sync status
            $this->updateSyncStatus($syncId, 'completed', $syncResult);
            
            // Notify Revit plugin
            $this->notifyRevitPlugin($syncResult);
            
            return [
                'success' => true,
                'sync_id' => $syncId,
                'message' => 'Revit sync completed successfully',
                'details' => $syncResult
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function createSyncRecord($projectId, $softwareType, $status) {
        $sql = "INSERT INTO mep_sync_records (project_id, software_type, sync_status, sync_date) 
                VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iss", $projectId, $softwareType, $status);
        $stmt->execute();
        
        return $this->db->insert_id;
    }
    
    private function processRevitModel($modelPath, $projectId, $userId) {
        // Simulate Revit model processing
        $elements = $this->extractRevitElements($modelPath);
        
        // Store elements in database
        $storedCount = $this->storeRevitElements($projectId, $elements);
        
        // Generate coordination analysis
        $analysis = $this->generateCoordinationAnalysis($elements);
        
        return [
            'elements_processed' => count($elements),
            'elements_stored' => $storedCount,
            'conflicts_found' => count($analysis['conflicts']),
            'recommendations' => $analysis['recommendations']
        ];
    }
    
    private function extractRevitElements($modelPath) {
        // Simulate Revit element extraction
        $elements = [
            [
                'element_id' => 'rev_001',
                'family_name' => 'MECHANICAL_EQUIPMENT',
                'type_name' => 'AIR_HANDLER',
                'category' => 'Mechanical',
                'parameters' => [
                    'capacity' => '5000 CFM',
                    'power' => '25 kW',
                    'location' => 'Roof'
                ]
            ],
            [
                'element_id' => 'rev_002',
                'family_name' => 'ELECTRICAL_EQUIPMENT',
                'type_name' => 'PANEL_BOARD',
                'category' => 'Electrical',
                'parameters' => [
                    'amperage' => '200A',
                    'voltage' => '480V',
                    'circuits' => '42'
                ]
            ],
            [
                'element_id' => 'rev_003',
                'family_name' => 'PLUMBING_FIXTURES',
                'type_name' => 'WATER_HEATER',
                'category' => 'Plumbing',
                'parameters' => [
                    'capacity' => '80 gallons',
                    'fuel_type' => 'Gas',
                    'efficiency' => '0.82'
                ]
            ]
        ];
        
        return $elements;
    }
    
    private function storeRevitElements($projectId, $elements) {
        $stored = 0;
        
        foreach ($elements as $element) {
            $sql = "INSERT INTO mep_rev_elements 
                   (project_id, element_id, family_name, type_name, category, parameters, created_date) 
                   VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            // bind_param needs variables; extract element fields to temporary variables
            $e_element_id = $element['element_id'];
            $e_family_name = $element['family_name'];
            $e_type_name = $element['type_name'];
            $e_category = $element['category'];
            $e_parameters = json_encode($element['parameters']);

            $stmt->bind_param("isssss",
                $projectId,
                $e_element_id,
                $e_family_name,
                $e_type_name,
                $e_category,
                $e_parameters
            );
            
            if ($stmt->execute()) {
                $stored++;
            }
        }
        
        return $stored;
    }
    
    private function generateCoordinationAnalysis($elements) {
        $conflicts = [];
        $recommendations = [];
        
        // Check for equipment conflicts
        $mechanical = array_filter($elements, function($e) { return $e['category'] === 'Mechanical'; });
        $electrical = array_filter($elements, function($e) { return $e['category'] === 'Electrical'; });
        
        if (count($mechanical) > 0 && count($electrical) > 0) {
            $conflicts[] = [
                'type' => 'Equipment_Conflict',
                'severity' => 'Medium',
                'description' => 'MEP equipment may interfere with each other',
                'location' => 'Equipment room coordination'
            ];
            
            $recommendations[] = [
                'priority' => 'High',
                'title' => 'Equipment Spacing Review',
                'description' => 'Review spacing between MEP equipment in Revit model'
            ];
        }
        
        // Check for load conflicts
        $totalLoad = 0;
        foreach ($mechanical as $equipment) {
            if (isset($equipment['parameters']['power'])) {
                $power = floatval(str_replace(' kW', '', $equipment['parameters']['power']));
                $totalLoad += $power;
            }
        }
        
        if ($totalLoad > 50) {
            $recommendations[] = [
                'priority' => 'Medium',
                'title' => 'Electrical Load Analysis',
                'description' => 'Consider electrical load distribution for total load of ' . $totalLoad . 'kW'
            ];
        }
        
        return [
            'conflicts' => $conflicts,
            'recommendations' => $recommendations,
            'total_load' => $totalLoad
        ];
    }
    
    private function updateSyncStatus($syncId, $status, $result) {
        $sql = "UPDATE mep_sync_records 
                SET sync_status = ?, sync_result = ?, completion_date = NOW() 
                WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    // json_encode returns a string; bind_param requires variables (references)
    $result_json = json_encode($result);
    $stmt->bind_param("ssi", $status, $result_json, $syncId);
        $stmt->execute();
    }
    
    private function notifyRevitPlugin($syncResult) {
        // Simulate plugin notification
        return [
            'notification_sent' => true,
            'message' => 'Sync completion notification sent to Revit plugin'
        ];
    }
    
    public function getRevitSyncHistory($projectId, $limit = 10) {
        $sql = "SELECT * FROM mep_sync_records 
                WHERE project_id = ? AND software_type = 'revit' 
                ORDER BY sync_date DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $projectId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        
        return $history;
    }
    
    public function getRevitElements($projectId, $category = null) {
        $sql = "SELECT * FROM mep_rev_elements WHERE project_id = ?";
        $params = [$projectId];
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY family_name, type_name";
        
        $stmt = $this->db->prepare($sql);
        // bind_param requires a type string plus variables. Build types and call bind_param accordingly.
        if ($category) {
            // types: projectId (int), category (string)
            $stmt->bind_param('is', $projectId, $category);
        } else {
            // only projectId
            $stmt->bind_param('i', $projectId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $elements = [];
        while ($row = $result->fetch_assoc()) {
            $row['parameters'] = json_decode($row['parameters'], true);
            $elements[] = $row;
        }
        
        return $elements;
    }
    
    public function exportToRevit($projectId, $elements, $exportPath) {
        try {
            $revitData = [
                'project_info' => [
                    'name' => 'MEP Coordination Export',
                    'export_date' => date('Y-m-d H:i:s'),
                    'total_elements' => count($elements)
                ],
                'elements' => $elements
            ];
            
            // Create export directory if it doesn't exist
            $exportDir = dirname($exportPath);
            if (!is_dir($exportDir)) {
                mkdir($exportDir, 0755, true);
            }
            
            file_put_contents($exportPath, json_encode($revitData, JSON_PRETTY_PRINT));
            
            return [
                'success' => true,
                'file_path' => $exportPath,
                'elements_exported' => count($elements)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function getPluginStatus() {
        $sql = "SELECT COUNT(*) as active_plugins FROM mep_revit_plugins WHERE is_active = 1";
        $active = 0;

        // Support multiple DB wrappers: App\Core\Database (PDO wrapper), raw PDO, and mysqli
        // 1) If we have our Database wrapper, use its PDO instance
        if (is_object($this->db) && method_exists($this->db, 'getPdo')) {
            try {
                // Annotate $pdo for static analyzers: getPdo() should return a \PDO instance from the app Database wrapper.
                /** @var \PDO $pdo */
                $pdo = $this->db->getPdo();
                $stmt = $pdo->query($sql);
                $row = $stmt ? $stmt->fetch(\PDO::FETCH_ASSOC) : false;
                $active = isset($row['active_plugins']) ? (int)$row['active_plugins'] : 0;
            } catch (Exception $e) {
                $active = 0;
            }

        // 2) If $this->db is a raw PDO instance
        } elseif ($this->db instanceof \PDO) {
            $stmt = $this->db->query($sql);
            $row = $stmt ? $stmt->fetch(\PDO::FETCH_ASSOC) : false;
            $active = isset($row['active_plugins']) ? (int)$row['active_plugins'] : 0;

        // 3) Assume mysqli-style connection for legacy code
        } else {
            $result = $this->db->query($sql);
            if ($result) {
                $row = method_exists($result, 'fetch_assoc') ? $result->fetch_assoc() : null;
                $active = isset($row['active_plugins']) ? (int)$row['active_plugins'] : 0;
            }
        }

        return [
            'active_plugins' => $active,
            'status' => $active > 0 ? 'connected' : 'disconnected',
            'last_check' => date('Y-m-d H:i:s')
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $revit = new RevitPluginIntegration();
    
    switch ($action) {
        case 'register_plugin':
            $pluginData = [
                'name' => $_POST['plugin_name'] ?? '',
                'version' => $_POST['plugin_version'] ?? '1.0',
                'api_key' => $_POST['api_key'] ?? '',
                'path' => $_POST['plugin_path'] ?? '',
                'features' => explode(',', $_POST['features'] ?? ''),
                'active' => $_POST['active'] ?? 1
            ];
            $result = $revit->registerRevitPlugin($pluginData);
            echo json_encode($result);
            break;
            
        case 'sync_revit':
            $projectId = $_POST['project_id'] ?? 0;
            $modelPath = $_POST['model_path'] ?? '';
            $userId = $_SESSION['user_id'] ?? 1;
            $result = $revit->syncWithRevit($projectId, $modelPath, $userId);
            echo json_encode($result);
            break;
            
        case 'get_sync_history':
            $projectId = $_POST['project_id'] ?? 0;
            $limit = $_POST['limit'] ?? 10;
            $result = $revit->getRevitSyncHistory($projectId, $limit);
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'get_elements':
            $projectId = $_POST['project_id'] ?? 0;
            $category = $_POST['category'] ?? null;
            $result = $revit->getRevitElements($projectId, $category);
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'export_to_revit':
            $projectId = $_POST['project_id'] ?? 0;
            $elements = json_decode($_POST['elements'] ?? '[]', true);
            $exportPath = $_POST['export_path'] ?? '../../../exports/revit/';
            $result = $revit->exportToRevit($projectId, $elements, $exportPath);
            echo json_encode($result);
            break;
            
        case 'get_plugin_status':
            $result = $revit->getPluginStatus();
            echo json_encode(['success' => true, 'data' => $result]);
            break;
    }
    exit;
}
?>
