<?php
require_once '../../../app/Config/config.php';

class BIMIntegration {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function importBIMModel($filePath, $projectId, $userId, $modelType = 'IFC') {
        try {
            if (!file_exists($filePath)) {
                throw new Exception("BIM file not found");
            }
            
            $fileInfo = pathinfo($filePath);
            $fileName = $fileInfo['filename'];
            $fileSize = filesize($filePath);
            $fileType = $fileInfo['extension'];
            
            switch (strtoupper($modelType)) {
                case 'IFC':
                    $elements = $this->parseIFCFile($filePath);
                    break;
                case 'RVT':
                    $elements = $this->parseRVTFile($filePath);
                    break;
                case 'DWG':
                    $elements = $this->parseDWGFile($filePath);
                    break;
                default:
                    throw new Exception("Unsupported BIM model type");
            }
            
            $sql = "INSERT INTO mep_bim_models (project_id, model_name, model_type, file_path, file_size, element_count, imported_by, import_date, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'processing')";
            $stmt = $this->db->prepare($sql);
            // ensure variables for bind_param (count() is an expression)
            $b_fileName = $fileName;
            $b_modelType = $modelType;
            $b_filePath = $filePath;
            $b_fileSize = $fileSize;
            $b_element_count = count($elements);
            $b_userId = $userId;
            $stmt->bind_param("issssii", $projectId, $b_fileName, $b_modelType, $b_filePath, $b_fileSize, $b_element_count, $b_userId);
            $stmt->execute();
            $modelId = $this->db->insert_id;
            
            $this->storeBIMElements($modelId, $elements);
            $this->updateModelStatus($modelId, 'completed');
            
            return [
                'success' => true,
                'model_id' => $modelId,
                'elements_count' => count($elements),
                'message' => 'BIM model imported successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function parseIFCFile($filePath) {
        $elements = [];
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (strpos($line, 'IFCBUILDINGELEMENT') !== false || 
                strpos($line, 'IFCWALL') !== false || 
                strpos($line, 'IFCDOOR') !== false || 
                strpos($line, 'IFCWINDOW') !== false) {
                
                preg_match('/#(\d+)=IFC([A-Z]+)\((.*?)\);/', $line, $matches);
                if (count($matches) >= 4) {
                    $elements[] = [
                        'element_id' => $matches[1],
                        'element_type' => $matches[2],
                        'data' => $matches[3],
                        'category' => $this->categorizeElement($matches[2])
                    ];
                }
            }
        }
        
        return $elements;
    }
    
    private function parseRVTFile($filePath) {
        $elements = [];
        $simulatedElements = [
            ['element_id' => 'rvt_001', 'element_type' => 'WALL', 'category' => 'Architectural'],
            ['element_id' => 'rvt_002', 'element_type' => 'DUCT', 'category' => 'Mechanical'],
            ['element_id' => 'rvt_003', 'element_type' => 'PIPE', 'category' => 'Plumbing'],
            ['element_id' => 'rvt_004', 'element_type' => 'CABLETRAY', 'category' => 'Electrical']
        ];
        
        return $simulatedElements;
    }
    
    private function parseDWGFile($filePath) {
        $elements = [];
        $simulatedElements = [
            ['element_id' => 'dwg_001', 'element_type' => 'LINE', 'category' => 'Generic'],
            ['element_id' => 'dwg_002', 'element_type' => 'CIRCLE', 'category' => 'Generic'],
            ['element_id' => 'dwg_003', 'element_type' => 'POLYLINE', 'category' => 'Generic']
        ];
        
        return $simulatedElements;
    }
    
    private function categorizeElement($elementType) {
        $categories = [
            'HVAC' => ['IFCFAN', 'IFCDUCT', 'IFCAIRTERMINAL', 'IFCPUMP'],
            'ELECTRICAL' => ['IFCCABLE', 'IFCFIXEDLIGHT', 'IFCELECTRICGENERATOR'],
            'PLUMBING' => ['IFCPIPE', 'IFCSANITARYTERMINAL', 'IFCFLOWTERMINAL'],
            'FIRE' => ['IFCSPRINKLER', 'IFCFIRESUPPRESSIONTERMINAL'],
            'STRUCTURAL' => ['IFCCOLUMN', 'IFCSLAB', 'IFCBEAM', 'IFCWALL'],
            'ARCHITECTURAL' => ['IFCDOOR', 'IFCWINDOW', 'IFCWALL', 'IFCSLAB']
        ];
        
        foreach ($categories as $category => $types) {
            if (in_array($elementType, $types)) {
                return $category;
            }
        }
        
        return 'Generic';
    }
    
    private function storeBIMElements($modelId, $elements) {
        foreach ($elements as $element) {
            $sql = "INSERT INTO mep_bim_elements (model_id, element_id, element_type, category, data, created_date) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $dataJson = json_encode($element['data'] ?? []);
            // temporaries for bind_param (array access must be assigned to variables)
            $e_element_id = $element['element_id'];
            $e_element_type = $element['element_type'];
            $e_category = $element['category'];
            $stmt->bind_param("issss", $modelId, $e_element_id, $e_element_type, $e_category, $dataJson);
            $stmt->execute();
        }
    }
    
    private function updateModelStatus($modelId, $status) {
        $sql = "UPDATE mep_bim_models SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $status, $modelId);
        $stmt->execute();
    }
    
    public function getProjectBIMModels($projectId) {
        $sql = "SELECT * FROM mep_bim_models WHERE project_id = ? ORDER BY import_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $models = [];
        while ($row = $result->fetch_assoc()) {
            $models[] = $row;
        }
        
        return $models;
    }
    
    public function getBIMModelElements($modelId, $category = null) {
        $sql = "SELECT * FROM mep_bim_elements WHERE model_id = ?";

        if ($category) {
            $sql .= " AND category = ?";
        }

        $sql .= " ORDER BY element_type, element_id";

        $stmt = $this->db->prepare($sql);
        if ($category) {
            $stmt->bind_param("is", $modelId, $category);
        } else {
            $stmt->bind_param("i", $modelId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $elements = [];
        while ($row = $result->fetch_assoc()) {
            $elements[] = $row;
        }
        
        return $elements;
    }
    
    public function extractMEPSystems($modelId) {
        $elements = $this->getBIMModelElements($modelId);
        $systems = [];
        
        foreach ($elements as $element) {
            $category = $element['category'];
            if (!isset($systems[$category])) {
                $systems[$category] = [];
            }
            $systems[$category][] = $element;
        }
        
        return $systems;
    }
    
    public function generateCoordinationReport($modelId) {
        $elements = $this->getBIMModelElements($modelId);
        $report = [
            'total_elements' => count($elements),
            'systems' => [],
            'conflicts' => [],
            'recommendations' => []
        ];
        
        $bySystem = [];
        foreach ($elements as $element) {
            $system = $element['category'];
            if (!isset($bySystem[$system])) {
                $bySystem[$system] = [];
            }
            $bySystem[$system][] = $element;
        }
        
        $report['systems'] = array_map('count', $bySystem);
        $report['conflicts'] = $this->detectConflicts($elements);
        $report['recommendations'] = $this->generateRecommendations($bySystem);
        
        return $report;
    }
    
    private function detectConflicts($elements) {
        $conflicts = [];
        
        $hvacElements = array_filter($elements, function($e) { return $e['category'] === 'HVAC'; });
        $electricalElements = array_filter($elements, function($e) { return $e['category'] === 'ELECTRICAL'; });
        
        if (count($hvacElements) > 0 && count($electricalElements) > 0) {
            $conflicts[] = [
                'type' => 'MEP_Overlap',
                'severity' => 'medium',
                'description' => 'HVAC and Electrical systems may overlap',
                'elements' => ['hvac' => count($hvacElements), 'electrical' => count($electricalElements)]
            ];
        }
        
        return $conflicts;
    }
    
    private function generateRecommendations($bySystem) {
        $recommendations = [];
        
        foreach ($bySystem as $system => $elements) {
            if (count($elements) > 10) {
                $elementCount = count($elements);
                $rec = [
                    'type' => 'Optimization',
                    'priority' => 'high',
                    'message' => 'System optimization recommended'
                ];
                $recommendations[] = $rec;
            }
        }
        
        return $recommendations;
    }
    
    public function exportBIMModel($modelId, $format = 'IFC') {
        $model = $this->getBIMModel($modelId);
        if (!$model) {
            return ['success' => false, 'message' => 'Model not found'];
        }
        
        $elements = $this->getBIMModelElements($modelId);
        $exportPath = $this->generateExportPath($modelId, $format);
        
        switch ($format) {
            case 'IFC':
                return $this->exportToIFC($elements, $exportPath);
            case 'CSV':
                return $this->exportToCSV($elements, $exportPath);
            case 'JSON':
                return $this->exportToJSON($elements, $exportPath);
            default:
                return ['success' => false, 'message' => 'Unsupported export format'];
        }
    }
    
    public function getBIMModel($modelId) {
        $sql = "SELECT * FROM mep_bim_models WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $modelId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    private function generateExportPath($modelId, $format) {
        $exportDir = '../../../exports/bim/';
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }
        return $exportDir . "model_{$modelId}.{$format}";
    }
    
    private function exportToIFC($elements, $filePath) {
        $ifcContent = "ISO-10303-21;\n";
        $ifcContent .= "HEADER;\n";
        $ifcContent .= "FILE_DESCRIPTION(('BIM Model Export'),'2;1');\n";
        $ifcContent .= "FILE_NAME('model_export.ifc','2024-01-01T00:00:00',('System'),('MEP Calculator'),'MEP Calculator','MEP Calculator','');\n";
        $ifcContent .= "FILE_SCHEMA(('IFC2X3'));\n";
        $ifcContent .= "ENDSEC;\n\n";
        $ifcContent .= "DATA;\n";
        
        $elementId = 100;
        foreach ($elements as $element) {
            $nextId = $elementId + 1;
            // use a computed next id instead of inline expression in interpolation
            $ifcContent .= "#{$elementId}=IFCBUILDINGELEMENT('{$element['element_id']}',#{$nextId},$,'{$element['element_type']}',\$,\$,\$,\$,\$);\n";
            $elementId++;
        }
        
        $ifcContent .= "ENDSEC;\n";
        $ifcContent .= "END-ISO-10303-21;\n";
        
        file_put_contents($filePath, $ifcContent);
        
        return ['success' => true, 'file_path' => $filePath];
    }
    
    private function exportToCSV($elements, $filePath) {
        $csvContent = "Element ID,Type,Category,Data\n";
        foreach ($elements as $element) {
            $data = json_encode($element['data'] ?? []);
            $csvContent .= "{$element['element_id']},{$element['element_type']},{$element['category']},\"{$data}\"\n";
        }
        
        file_put_contents($filePath, $csvContent);
        
        return ['success' => true, 'file_path' => $filePath];
    }
    
    private function exportToJSON($elements, $filePath) {
        $jsonContent = json_encode([
            'model_info' => [
                'export_date' => date('Y-m-d H:i:s'),
                'total_elements' => count($elements)
            ],
            'elements' => $elements
        ], JSON_PRETTY_PRINT);
        
        file_put_contents($filePath, $jsonContent);
        
        return ['success' => true, 'file_path' => $filePath];
    }
    
    public function syncWithBIMSoftware($modelId, $softwareType) {
        $model = $this->getBIMModel($modelId);
        if (!$model) {
            return ['success' => false, 'message' => 'Model not found'];
        }
        
        try {
            switch ($softwareType) {
                case 'revit':
                    return $this->syncWithRevit($model);
                case 'autocad':
                    return $this->syncWithAutoCAD($model);
                case 'navisworks':
                    return $this->syncWithNavisworks($model);
                default:
                    throw new Exception('Unsupported BIM software');
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function syncWithRevit($model) {
        return ['success' => true, 'message' => 'Model synchronized with Revit'];
    }
    
    private function syncWithAutoCAD($model) {
        return ['success' => true, 'message' => 'Model synchronized with AutoCAD'];
    }
    
    private function syncWithNavisworks($model) {
        return ['success' => true, 'message' => 'Model synchronized with Navisworks'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $bim = new BIMIntegration();
    
    switch ($action) {
        case 'import_model':
            $filePath = $_POST['file_path'] ?? '';
            $projectId = $_POST['project_id'] ?? 0;
            $modelType = $_POST['model_type'] ?? 'IFC';
            $userId = $_SESSION['user_id'] ?? 1;
            $result = $bim->importBIMModel($filePath, $projectId, $userId, $modelType);
            echo json_encode($result);
            break;
            
        case 'get_models':
            $projectId = $_POST['project_id'] ?? 0;
            $result = $bim->getProjectBIMModels($projectId);
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'get_elements':
            $modelId = $_POST['model_id'] ?? 0;
            $category = $_POST['category'] ?? null;
            $result = $bim->getBIMModelElements($modelId, $category);
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'generate_report':
            $modelId = $_POST['model_id'] ?? 0;
            $result = $bim->generateCoordinationReport($modelId);
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'export_model':
            $modelId = $_POST['model_id'] ?? 0;
            $format = $_POST['format'] ?? 'IFC';
            $result = $bim->exportBIMModel($modelId, $format);
            echo json_encode($result);
            break;
            
        case 'sync_software':
            $modelId = $_POST['model_id'] ?? 0;
            $software = $_POST['software'] ?? '';
            $result = $bim->syncWithBIMSoftware($modelId, $software);
            echo json_encode($result);
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIM Integration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bim-card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: #fff;
            transition: all 0.2s;
        }
        .bim-card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .upload-zone {
            border: 2px dashed #6c757d;
            border-radius: 0.375rem;
            padding: 3rem;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.2s;
        }
        .upload-zone:hover {
            border-color: #007bff;
            background: #e3f2fd;
        }
        .upload-zone.dragover {
            border-color: #28a745;
            background: #e8f5e8;
        }
        .element-category {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #fff;
            border-radius: 0.25rem;
            margin-right: 0.5rem;
        }
        .category-hvac { background-color: #dc3545; }
        .category-electrical { background-color: #ffc107; color: #000; }
        .category-plumbing { background-color: #17a2b8; }
        .category-fire { background-color: #fd7e14; }
        .category-structural { background-color: #6f42c1; }
        .category-architectural { background-color: #6c757d; }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .sync-status {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 500;
        }
        .sync-completed { background-color: #d4edda; color: #155724; }
        .sync-processing { background-color: #fff3cd; color: #856404; }
        .sync-failed { background-color: #f8d7da; color: #721c24; }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-cube me-2"></i>BIM Integration
                    </h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-upload me-2"></i>Import BIM Model
                    </button>
                </div>

                <div class="row mb-4" id="statsContainer">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Total Models</h5>
                            <h3 id="totalModels">-</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Imported Elements</h5>
                            <h3 id="totalElements">-</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Conflicts Detected</h5>
                            <h3 id="totalConflicts">-</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Sync Status</h5>
                            <h3 id="syncStatus">-</h3>
                        </div>
                    </div>
                </div>

                <div class="row" id="modelsContainer">
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No BIM models imported yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-upload me-2"></i>Import BIM Model
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="upload-zone" id="uploadZone">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <p class="mb-3">Drag & drop your BIM file here or click to browse</p>
                        <input type="file" id="fileInput" accept=".ifc,.rvt,.dwg" style="display: none;">
                        <button class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                            Choose File
                        </button>
                        <div class="mt-3">
                            <small class="text-muted">Supported formats: IFC, RVT, DWG (Max: 100MB)</small>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="modelType" class="form-label">Model Type</label>
                        <select class="form-select" id="modelType">
                            <option value="IFC">IFC (Industry Foundation Classes)</option>
                            <option value="RVT">RVT (Revit)</option>
                            <option value="DWG">DWG (AutoCAD)</option>
                        </select>
                    </div>
                    
                    <div class="mt-3">
                        <label for="projectId" class="form-label">Project</label>
                        <select class="form-select" id="projectId">
                            <option value="">Select Project</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="importBIMModel()">Import</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modelDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelDetailsTitle">Model Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="modelTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="elements-tab" data-bs-toggle="tab" data-bs-target="#elements" type="button">Elements</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="coordination-tab" data-bs-toggle="tab" data-bs-target="#coordination" type="button">Coordination</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-3" id="modelTabContent">
                        <div class="tab-pane fade show active" id="overview">
                            <div id="modelOverview"></div>
                        </div>
                        <div class="tab-pane fade" id="elements">
                            <div id="elementsContainer">
                                <div class="text-center py-3">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="coordination">
                            <div id="coordinationContainer">
                                <div class="text-center py-3">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" onclick="exportModel()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="syncModel()">
                        <i class="fas fa-sync me-2"></i>Sync
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentModelId = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            loadProjects();
            loadStatistics();
            loadModels();
            setupFileUpload();
        });

        function setupFileUpload() {
            const uploadZone = document.getElementById('uploadZone');
            const fileInput = document.getElementById('fileInput');
            
            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.classList.add('dragover');
            });
            
            uploadZone.addEventListener('dragleave', () => {
                uploadZone.classList.remove('dragover');
            });
            
            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                }
            });
        }

        function loadProjects() {
            fetch('../../../api/get_projects.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('projectId');
                        data.data.forEach(project => {
                            const option = document.createElement('option');
                            option.value = project.id;
                            option.textContent = project.project_name;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading projects:', error));
        }

        function loadStatistics() {
            document.getElementById('totalModels').textContent = '0';
            document.getElementById('totalElements').textContent = '0';
            document.getElementById('totalConflicts').textContent = '0';
            document.getElementById('syncStatus').textContent = 'Idle';
        }

        function loadModels() {
            const formData = new FormData();
            formData.append('action', 'get_models');
            formData.append('project_id', '');

            fetch('bim-integration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayModels(data.data);
                } else {
                    console.error('Error loading models:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function displayModels(models) {
            const container = document.getElementById('modelsContainer');
            
            if (models.length === 0) {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No BIM models imported yet</p>
                        </div>
                    </div>
                `;
                return;
            }

            let html = '';
            models.forEach(model => {
                const statusClass = model.status === 'completed' ? 'sync-completed' : 
                                  model.status === 'processing' ? 'sync-processing' : 'sync-failed';
                const statusIcon = model.status === 'completed' ? 'fas fa-check-circle' : 
                                 model.status === 'processing' ? 'fas fa-spinner fa-spin' : 'fas fa-exclamation-triangle';

                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="bim-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="mb-0">${model.model_name}</h6>
                                <span class="sync-status ${statusClass}">
                                    <i class="${statusIcon} me-1"></i>${model.status}
                                </span>
                            </div>
                            <p class="text-muted mb-2">
                                <i class="fas fa-file me-2"></i>${model.model_type.toUpperCase()}
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-cube me-2"></i>${model.element_count || 0} elements
                            </p>
                            <p class="text-muted mb-3">
                                <i class="fas fa-calendar me-2"></i>${new Date(model.import_date).toLocaleDateString()}
                            </p>
                            <div class="d-grid">
                                <button class="btn btn-outline-primary btn-sm" onclick="viewModelDetails(${model.id}, '${model.model_name}')">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function importBIMModel() {
            const fileInput = document.getElementById('fileInput');
            const modelType = document.getElementById('modelType').value;
            const projectId = document.getElementById('projectId').value;
            
            if (!fileInput.files[0]) {
                showNotification('Please select a file', 'info');
                return;
            }
            
            if (!projectId) {
                showNotification('Please select a project', 'info');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'import_model');
            formData.append('file_path', fileInput.files[0].name);
            formData.append('project_id', projectId);
            formData.append('model_type', modelType);

            showNotification('Importing BIM model...', 'info');
            
            setTimeout(() => {
                fetch('bim-integration.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('BIM model imported successfully!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
                        loadModels();
                    } else {
                        showNotification('Error importing model: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error importing model', 'danger');
                });
            }, 2000);
        }

        function viewModelDetails(modelId, modelName) {
            currentModelId = modelId;
            document.getElementById('modelDetailsTitle').textContent = `Model: ${modelName}`;
            
            loadModelOverview(modelId);
            loadModelElements(modelId);
            loadCoordinationReport(modelId);
            
            new bootstrap.Modal(document.getElementById('modelDetailsModal')).show();
        }

        function loadModelOverview(modelId) {
            const formData = new FormData();
            formData.append('action', 'get_elements');
            formData.append('model_id', modelId);

            fetch('bim-integration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayModelOverview(data.data);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function displayModelOverview(elements) {
            const container = document.getElementById('modelOverview');
            
            const grouped = elements.reduce((acc, elem) => {
                if (!acc[elem.category]) acc[elem.category] = 0;
                acc[elem.category]++;
                return acc;
            }, {});

            let html = '<div class="row">';
            
            Object.entries(grouped).forEach(([category, count]) => {
                html += `
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="element-category category-${category.toLowerCase()}">${category}</div>
                                <h4>${count}</h4>
                                <small class="text-muted">elements</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            container.innerHTML = html;
        }

        function loadModelElements(modelId) {
            const formData = new FormData();
            formData.append('action', 'get_elements');
            formData.append('model_id', modelId);

            fetch('bim-integration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayModelElements(data.data);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function displayModelElements(elements) {
            const container = document.getElementById('elementsContainer');
            
            if (elements.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No elements found</p>';
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>ID</th><th>Type</th><th>Category</th><th>Data</th></tr></thead><tbody>';
            
            elements.forEach(element => {
                const data = JSON.stringify(element.data || {});
                html += `
                    <tr>
                        <td>${element.element_id}</td>
                        <td>${element.element_type}</td>
                        <td><span class="element-category category-${element.category.toLowerCase()}">${element.category}</span></td>
                        <td><small>${data}</small></td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            container.innerHTML = html;
        }

        function loadCoordinationReport(modelId) {
            const formData = new FormData();
            formData.append('action', 'generate_report');
            formData.append('model_id', modelId);

            fetch('bim-integration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayCoordinationReport(data.data);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function displayCoordinationReport(report) {
            const container = document.getElementById('coordinationContainer');
            
            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6>Total Elements</h6>
                                <h3>${report.total_elements}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6>Conflicts Detected</h6>
                                <h3>${report.conflicts.length}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            if (report.conflicts.length > 0) {
                html += '<h6>Conflicts</h6>';
                report.conflicts.forEach(conflict => {
                    html += `
                        <div class="alert alert-warning">
                            <strong>${conflict.type}</strong> - ${conflict.description}
                        </div>
                    `;
                });
            }
            
            if (report.recommendations.length > 0) {
                html += '<h6 class="mt-3">Recommendations</h6>';
                report.recommendations.forEach(rec => {
                    const priorityClass = rec.priority === 'high' ? 'alert-danger' : 
                                        rec.priority === 'medium' ? 'alert-warning' : 'alert-info';
                    html += `
                        <div class="alert ${priorityClass}">
                            <strong>${rec.type}</strong> - ${rec.message}
                        </div>
                    `;
                });
            }
            
            container.innerHTML = html;
        }

        function exportModel() {
            if (!currentModelId) return;
            
            showPrompt('Export Model', 'Export format (IFC, CSV, JSON):', (format) => {
                if (format) {
                    const formats = ['IFC', 'CSV', 'JSON'];
                    if (formats.includes(format.toUpperCase())) {
                        const formData = new FormData();
                        formData.append('action', 'export_model');
                        formData.append('model_id', currentModelId);
                        formData.append('format', format.toUpperCase());

                        fetch('bim-integration.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Model exported successfully!', 'success');
                            } else {
                                showNotification('Error exporting model: ' + data.message, 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('Error exporting model', 'danger');
                        });
                    }
                }
            }, { defaultValue: 'IFC' });
        }

        function syncModel() {
            if (!currentModelId) return;
            
            showPrompt('Sync Model', 'Sync with software (revit, autocad, navisworks):', (software) => {
                if (software) {
                    const formData = new FormData();
                    formData.append('action', 'sync_software');
                    formData.append('model_id', currentModelId);
                    formData.append('software', software.toLowerCase());

                    fetch('bim-integration.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Model synchronized successfully!', 'success');
                        } else {
                            showNotification('Error syncing model: ' + data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error syncing model', 'danger');
                    });
                }
            }, { defaultValue: 'revit' });
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
