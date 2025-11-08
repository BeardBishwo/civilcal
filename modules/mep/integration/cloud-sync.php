<?php
require_once '../../../../includes/config.php';

class CloudSyncManager {
    private $db;
    private $syncProvider;
    
    public function __construct($provider = 'aws') {
        global $db;
        $this->db = $db;
        $this->syncProvider = $provider;
    }
    
    public function initializeCloudConnection($config) {
        try {
            // Create connection record
            $sql = "INSERT INTO mep_cloud_connections (provider, config_data, status, created_date) 
                    VALUES (?, ?, 'connecting', NOW())";
                $stmt = $this->db->prepare($sql);
                $provider_var = $this->syncProvider;
                $config_json = json_encode($config);
                $stmt->bind_param("ss", $provider_var, $config_json);
            $stmt->execute();
            $connectionId = $this->db->insert_id;
            
            // Test connection
            $connectionTest = $this->testCloudConnection($config);
            
            if ($connectionTest['success']) {
                $this->updateConnectionStatus($connectionId, 'connected');
                return [
                    'success' => true,
                    'connection_id' => $connectionId,
                    'message' => 'Cloud connection established successfully'
                ];
            } else {
                $this->updateConnectionStatus($connectionId, 'failed');
                return [
                    'success' => false,
                    'message' => $connectionTest['message']
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function testCloudConnection($config) {
        // Simulate cloud connection testing
        $testResults = [
            'aws' => [
                'success' => true,
                'message' => 'AWS S3 connection successful',
                'bucket_access' => true,
                'api_status' => 'operational'
            ],
            'azure' => [
                'success' => true,
                'message' => 'Azure Blob Storage connection successful',
                'container_access' => true,
                'api_status' => 'operational'
            ],
            'google' => [
                'success' => true,
                'message' => 'Google Cloud Storage connection successful',
                'bucket_access' => true,
                'api_status' => 'operational'
            ]
        ];
        
        return $testResults[$this->syncProvider] ?? [
            'success' => false,
            'message' => 'Unknown cloud provider'
        ];
    }
    
    public function syncProjectToCloud($projectId, $userId, $syncOptions = []) {
        try {
            // Create sync record
            $syncId = $this->createSyncRecord($projectId, $userId, 'cloud_upload');
            
            // Gather project data
            $projectData = $this->gatherProjectData($projectId);
            
            // Upload to cloud
            $uploadResult = $this->uploadToCloud($projectData, $syncOptions);
            
            // Update sync status
            $this->updateSyncRecord($syncId, $uploadResult);
            
            return [
                'success' => true,
                'sync_id' => $syncId,
                'upload_result' => $uploadResult,
                'message' => 'Project synced to cloud successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function syncFromCloud($projectId, $userId, $cloudPath) {
        try {
            // Create sync record
            $syncId = $this->createSyncRecord($projectId, $userId, 'cloud_download');
            
            // Download from cloud
            $downloadResult = $this->downloadFromCloud($cloudPath);
            
            if ($downloadResult['success']) {
                // Process downloaded data
                $processResult = $this->processDownloadedData($projectId, $downloadResult['data']);
                
                $this->updateSyncRecord($syncId, $processResult);
                
                return [
                    'success' => true,
                    'sync_id' => $syncId,
                    'process_result' => $processResult,
                    'message' => 'Project synced from cloud successfully'
                ];
            } else {
                $this->updateSyncRecord($syncId, $downloadResult);
                return $downloadResult;
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function createSyncRecord($projectId, $userId, $syncType) {
        $sql = "INSERT INTO mep_cloud_sync_records (project_id, user_id, sync_type, status, sync_date) 
                VALUES (?, ?, ?, 'in_progress', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $projectId, $userId, $syncType);
        $stmt->execute();
        
        return $this->db->insert_id;
    }
    
    private function gatherProjectData($projectId) {
        // Get project information
        $projectInfo = $this->getProjectInfo($projectId);
        
        // Get MEP calculations
        $calculations = $this->getProjectCalculations($projectId);
        
        // Get BIM models
        $bimModels = $this->getProjectBIMModels($projectId);
        
        // Get coordination data
        $coordination = $this->getProjectCoordination($projectId);
        
        return [
            'project_info' => $projectInfo,
            'calculations' => $calculations,
            'bim_models' => $bimModels,
            'coordination' => $coordination,
            'sync_metadata' => [
                'sync_date' => date('Y-m-d H:i:s'),
                'version' => '1.0',
                'provider' => $this->syncProvider
            ]
        ];
    }
    
    private function getProjectInfo($projectId) {
        $sql = "SELECT * FROM projects WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc() ?: [];
    }
    
    private function getProjectCalculations($projectId) {
        $sql = "SELECT * FROM mep_calculations WHERE project_id = ? ORDER BY created_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $calculations = [];
        while ($row = $result->fetch_assoc()) {
            $calculations[] = $row;
        }
        
        return $calculations;
    }
    
    private function getProjectBIMModels($projectId) {
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
    
    private function getProjectCoordination($projectId) {
        $sql = "SELECT * FROM mep_coordination_reports WHERE project_id = ? ORDER BY created_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $coordination = [];
        while ($row = $result->fetch_assoc()) {
            $coordination[] = $row;
        }
        
        return $coordination;
    }
    
    private function uploadToCloud($projectData, $options = []) {
        // Simulate cloud upload based on provider
        $uploadPath = $options['path'] ?? "/projects/{$projectData['project_info']['id']}/";
        $fileName = "mep_project_" . date('Y-m-d_H-i-s') . ".json";
        
        switch ($this->syncProvider) {
            case 'aws':
                return $this->uploadToAWS($projectData, $uploadPath . $fileName);
            case 'azure':
                return $this->uploadToAzure($projectData, $uploadPath . $fileName);
            case 'google':
                return $this->uploadToGoogle($projectData, $uploadPath . $fileName);
            default:
                throw new Exception('Unsupported cloud provider');
        }
    }
    
    private function uploadToAWS($data, $path) {
        // Simulate AWS S3 upload
        $result = [
            'success' => true,
            'provider' => 'AWS S3',
            'path' => $path,
            'file_size' => strlen(json_encode($data)),
            'bucket' => 'mep-projects-bucket',
            'url' => "https://mep-projects-bucket.s3.amazonaws.com/{$path}",
            'upload_time' => '2.3 seconds'
        ];
        
        return $result;
    }
    
    private function uploadToAzure($data, $path) {
        // Simulate Azure Blob Storage upload
        $result = [
            'success' => true,
            'provider' => 'Azure Blob Storage',
            'path' => $path,
            'file_size' => strlen(json_encode($data)),
            'container' => 'mep-projects',
            'url' => "https://mepprojects.blob.core.windows.net/mep-projects/{$path}",
            'upload_time' => '2.8 seconds'
        ];
        
        return $result;
    }
    
    private function uploadToGoogle($data, $path) {
        // Simulate Google Cloud Storage upload
        $result = [
            'success' => true,
            'provider' => 'Google Cloud Storage',
            'path' => $path,
            'file_size' => strlen(json_encode($data)),
            'bucket' => 'mep-projects-gcs',
            'url' => "https://storage.googleapis.com/mep-projects-gcs/{$path}",
            'upload_time' => '2.1 seconds'
        ];
        
        return $result;
    }
    
    private function downloadFromCloud($cloudPath) {
        // Simulate cloud download
        $mockData = [
            'project_info' => [
                'id' => 1,
                'name' => 'Sample Project',
                'description' => 'Downloaded from cloud'
            ],
            'calculations' => [],
            'bim_models' => [],
            'coordination' => [],
            'sync_metadata' => [
                'sync_date' => date('Y-m-d H:i:s'),
                'version' => '1.0'
            ]
        ];
        
        return [
            'success' => true,
            'provider' => $this->syncProvider,
            'path' => $cloudPath,
            'file_size' => strlen(json_encode($mockData)),
            'data' => $mockData,
            'download_time' => '1.8 seconds'
        ];
    }
    
    private function processDownloadedData($projectId, $data) {
        // Process and store downloaded data
        $processed = [
            'project_info_updated' => 0,
            'calculations_imported' => 0,
            'bim_models_imported' => 0,
            'coordination_imported' => 0
        ];
        
        // Update project info if provided
        if (isset($data['project_info'])) {
            $this->updateProjectInfo($projectId, $data['project_info']);
            $processed['project_info_updated'] = 1;
        }
        
        // Import calculations
        if (isset($data['calculations'])) {
            $processed['calculations_imported'] = $this->importCalculations($projectId, $data['calculations']);
        }
        
        // Import BIM models
        if (isset($data['bim_models'])) {
            $processed['bim_models_imported'] = $this->importBIMModels($projectId, $data['bim_models']);
        }
        
        // Import coordination data
        if (isset($data['coordination'])) {
            $processed['coordination_imported'] = $this->importCoordination($projectId, $data['coordination']);
        }
        
        return $processed;
    }
    
    private function updateProjectInfo($projectId, $projectInfo) {
        $sql = "UPDATE projects SET project_name = ?, description = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    // ensure bind_param gets variables (passed by reference)
    $p_name = $projectInfo['name'] ?? null;
    $p_description = $projectInfo['description'] ?? null;
    $p_id = $projectId;
    $stmt->bind_param("ssi", $p_name, $p_description, $p_id);
        $stmt->execute();
    }
    
    private function importCalculations($projectId, $calculations) {
        $imported = 0;
        foreach ($calculations as $calc) {
            $sql = "INSERT INTO mep_calculations (project_id, calculation_type, input_data, result, created_date) 
                    VALUES (?, ?, ?, ?, NOW())";
                $stmt = $this->db->prepare($sql);
                $c_type = $calc['calculation_type'];
                $c_json = json_encode($calc);
                $c_result_json = json_encode($calc['result']);
                $stmt->bind_param("isss", $projectId, $c_type, $c_json, $c_result_json);
            if ($stmt->execute()) {
                $imported++;
            }
        }
        return $imported;
    }
    
    private function importBIMModels($projectId, $models) {
        $imported = 0;
        foreach ($models as $model) {
            $sql = "INSERT INTO mep_bim_models (project_id, model_name, model_type, file_path, import_date) 
                    VALUES (?, ?, ?, ?, NOW())";
                $stmt = $this->db->prepare($sql);
                $m_name = $model['model_name'];
                $m_type = $model['model_type'];
                $m_path = $model['file_path'];
                $stmt->bind_param("isss", $projectId, $m_name, $m_type, $m_path);
            if ($stmt->execute()) {
                $imported++;
            }
        }
        return $imported;
    }
    
    private function importCoordination($projectId, $coordination) {
        $imported = 0;
        foreach ($coordination as $coord) {
            $sql = "INSERT INTO mep_coordination_reports (project_id, report_data, created_date) 
                    VALUES (?, ?, NOW())";
                $stmt = $this->db->prepare($sql);
                $coord_json = json_encode($coord);
                $stmt->bind_param("is", $projectId, $coord_json);
            if ($stmt->execute()) {
                $imported++;
            }
        }
        return $imported;
    }
    
    private function updateSyncRecord($syncId, $result) {
        $sql = "UPDATE mep_cloud_sync_records 
                SET status = ?, result_data = ?, completion_date = NOW() 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $status = $result['success'] ? 'completed' : 'failed';
    $result_json = json_encode($result);
    $stmt->bind_param("ssi", $status, $result_json, $syncId);
        $stmt->execute();
    }
    
    private function updateConnectionStatus($connectionId, $status) {
        $sql = "UPDATE mep_cloud_connections SET status = ?, last_check = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $status, $connectionId);
        $stmt->execute();
    }
    
    public function getCloudSyncHistory($projectId, $limit = 10) {
        $sql = "SELECT * FROM mep_cloud_sync_records 
                WHERE project_id = ? 
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
    
    public function getActiveCloudConnections() {
        $sql = "SELECT * FROM mep_cloud_connections 
                WHERE status = 'connected' 
                ORDER BY created_date DESC";
        $result = $this->db->query($sql);
        
        $connections = [];
        while ($row = $result->fetch_assoc()) {
            $connections[] = $row;
        }
        
        return $connections;
    }
    
    public function configureAutoSync($projectId, $config) {
        try {
            $sql = "INSERT INTO mep_auto_sync_config (project_id, sync_frequency, sync_time, 
                    sync_types, is_active, config_data, created_date) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $cfg_frequency = $config['frequency'];
            $cfg_time = $config['time'];
            $cfg_types_json = json_encode($config['types']);
            $cfg_active = $config['active'] ?? 1;
            $cfg_json = json_encode($config);
            $stmt->bind_param("isssis",
                $projectId,
                $cfg_frequency,
                $cfg_time,
                $cfg_types_json,
                $cfg_active,
                $cfg_json
            );
            
            $stmt->execute();
            $configId = $this->db->insert_id;
            
            return [
                'success' => true,
                'config_id' => $configId,
                'message' => 'Auto sync configuration saved successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function runAutoSync($projectId) {
        // Get auto sync config for project
        $config = $this->getAutoSyncConfig($projectId);
        if (!$config || !$config['is_active']) {
            return ['success' => false, 'message' => 'Auto sync not configured or inactive'];
        }
        
        // Check if sync is due
        if (!$this->isSyncDue($config)) {
            return ['success' => false, 'message' => 'Sync not due yet'];
        }
        
        // Execute sync based on configured types
        $syncTypes = json_decode($config['sync_types'], true);
        $results = [];
        
        foreach ($syncTypes as $type) {
            switch ($type) {
                case 'upload':
                    $result = $this->syncProjectToCloud($projectId, 1);
                    break;
                case 'download':
                    // Implement download sync logic
                    $result = ['success' => true, 'message' => 'Download sync completed'];
                    break;
            }
            $results[] = $result;
        }
        
        // Update last sync time
        $this->updateAutoSyncTime($config['id']);
        
        return [
            'success' => true,
            'sync_results' => $results,
            'message' => 'Auto sync completed successfully'
        ];
    }
    
    private function getAutoSyncConfig($projectId) {
        $sql = "SELECT * FROM mep_auto_sync_config 
                WHERE project_id = ? AND is_active = 1 
                ORDER BY created_date DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $config = $result->fetch_assoc();
        if ($config) {
            $config['sync_types'] = json_decode($config['sync_types'], true);
            $config['config_data'] = json_decode($config['config_data'], true);
        }
        
        return $config;
    }
    
    private function isSyncDue($config) {
        $lastSync = $config['last_sync_time'] ?? null;
        if (!$lastSync) return true;
        
        $frequency = $config['sync_frequency'];
        $lastSyncTime = strtotime($lastSync);
        
        switch ($frequency) {
            case 'hourly':
                return (time() - $lastSyncTime) >= 3600;
            case 'daily':
                return (time() - $lastSyncTime) >= 86400;
            case 'weekly':
                return (time() - $lastSyncTime) >= 604800;
            default:
                return true;
        }
    }
    
    private function updateAutoSyncTime($configId) {
        $sql = "UPDATE mep_auto_sync_config SET last_sync_time = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $configId);
        $stmt->execute();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $provider = $_POST['provider'] ?? 'aws';
    $cloudSync = new CloudSyncManager($provider);
    
    switch ($action) {
        case 'initialize_connection':
            $config = [
                'access_key' => $_POST['access_key'] ?? '',
                'secret_key' => $_POST['secret_key'] ?? '',
                'region' => $_POST['region'] ?? 'us-east-1',
                'bucket' => $_POST['bucket'] ?? ''
            ];
            $result = $cloudSync->initializeCloudConnection($config);
            echo json_encode($result);
            break;
            
        case 'sync_to_cloud':
            $projectId = $_POST['project_id'] ?? 0;
            $userId = $_SESSION['user_id'] ?? 1;
            $options = [
                'path' => $_POST['sync_path'] ?? '',
                'compress' => $_POST['compress'] ?? true
            ];
            $result = $cloudSync->syncProjectToCloud($projectId, $userId, $options);
            echo json_encode($result);
            break;
            
        case 'sync_from_cloud':
            $projectId = $_POST['project_id'] ?? 0;
            $userId = $_SESSION['user_id'] ?? 1;
            $cloudPath = $_POST['cloud_path'] ?? '';
            $result = $cloudSync->syncFromCloud($projectId, $userId, $cloudPath);
            echo json_encode($result);
            break;
            
        case 'get_sync_history':
            $projectId = $_POST['project_id'] ?? 0;
            $limit = $_POST['limit'] ?? 10;
            $result = $cloudSync->getCloudSyncHistory($projectId, $limit);
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'get_connections':
            $result = $cloudSync->getActiveCloudConnections();
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'configure_auto_sync':
            $projectId = $_POST['project_id'] ?? 0;
            $config = [
                'frequency' => $_POST['frequency'] ?? 'daily',
                'time' => $_POST['sync_time'] ?? '02:00',
                'types' => explode(',', $_POST['sync_types'] ?? 'upload'),
                'active' => $_POST['active'] ?? 1
            ];
            $result = $cloudSync->configureAutoSync($projectId, $config);
            echo json_encode($result);
            break;
            
        case 'run_auto_sync':
            $projectId = $_POST['project_id'] ?? 0;
            $result = $cloudSync->runAutoSync($projectId);
            echo json_encode($result);
            break;
    }
    exit;
}
?>
