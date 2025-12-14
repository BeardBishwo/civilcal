<?php
/**
 * MEP Coordination Suite - Project Sharing Module
 * 
 * Handles project sharing, access management, and collaboration features
 * Supports cloud-based sharing, access control, and project data export
 */

// Prevent direct access
if (!defined('MEPCOORD_SUITE_ACCESS')) {
    die('Direct access not permitted');
}

class ProjectSharing {
    private $db;
    private $config;
    private $logger;
    
    public function __construct($database, $config, $logger = null) {
        $this->db = $database;
        $this->config = $config;
        $this->logger = $logger;
    }
    
    /**
     * Create a shareable link for a project
     */
    public function createShareLink($projectId, $userId, $permissions = 'view', $expiryDate = null) {
        try {
            $shareToken = bin2hex(random_bytes(32));
            $createdAt = date('Y-m-d H:i:s');
            
            if ($expiryDate === null) {
                $expiryDate = date('Y-m-d H:i:s', strtotime('+30 days'));
            }
            
            $query = "INSERT INTO mep_project_shares (project_id, user_id, share_token, permissions, expires_at, created_at, is_active) 
                     VALUES (?, ?, ?, ?, ?, ?, 1)";
            
            $result = $this->db->executeQuery($query, [$projectId, $userId, $shareToken, $permissions, $expiryDate, $createdAt]);
            
            if ($result) {
                $shareUrl = $this->config->get('site_url', '') . '/mep/shared/' . $shareToken;
                
                // Log sharing activity
                if ($this->logger) {
                    $this->logger->log("Project {$projectId} shared by user {$userId} with permissions: {$permissions}");
                }
                
                return [
                    'success' => true,
                    'share_token' => $shareToken,
                    'share_url' => $shareUrl,
                    'expires_at' => $expiryDate,
                    'permissions' => $permissions
                ];
            }
            
            return ['success' => false, 'error' => 'Failed to create share link'];
            
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->log("Error creating share link: " . $e->getMessage());
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Access shared project using token
     */
    public function accessSharedProject($shareToken, $userEmail = null) {
        try {
            $query = "SELECT ps.*, p.name as project_name, p.description, p.data, 
                             u.name as owner_name, u.email as owner_email
                      FROM mep_project_shares ps
                      JOIN mep_projects p ON ps.project_id = p.id
                      JOIN users u ON ps.user_id = u.id
                      WHERE ps.share_token = ? AND ps.is_active = 1 
                      AND (ps.expires_at IS NULL OR ps.expires_at > NOW())";
            
            $result = $this->db->executeQuery($query, [$shareToken]);
            
            if ($result && $result->num_rows > 0) {
                $share = $result->fetch_assoc();
                
                // Log access
                if ($this->logger) {
                    $this->logger->log("Shared project accessed with token: {$shareToken}");
                }
                
                // Track access
                $this->trackAccess($shareToken, $userEmail);
                
                return [
                    'success' => true,
                    'project' => [
                        'id' => $share['project_id'],
                        'name' => $share['project_name'],
                        'description' => $share['description'],
                        'data' => json_decode($share['data'], true),
                        'owner' => [
                            'name' => $share['owner_name'],
                            'email' => $share['owner_email']
                        ],
                        'permissions' => $share['permissions'],
                        'accessed_at' => date('Y-m-d H:i:s')
                    ]
                ];
            }
            
            return ['success' => false, 'error' => 'Invalid or expired share token'];
            
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->log("Error accessing shared project: " . $e->getMessage());
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Export project data in various formats
     */
    public function exportProject($projectId, $format = 'pdf', $userId = null) {
        try {
            // Get project data
            $query = "SELECT * FROM mep_projects WHERE id = ?";
            $result = $this->db->executeQuery($query, [$projectId]);
            
            if (!$result || $result->num_rows === 0) {
                return ['success' => false, 'error' => 'Project not found'];
            }
            
            $project = $result->fetch_assoc();
            $projectData = json_decode($project['data'], true);
            
            switch (strtolower($format)) {
                case 'pdf':
                    return $this->exportToPDF($project, $projectData);
                case 'json':
                    return $this->exportToJSON($project, $projectData);
                case 'zip':
                    return $this->exportToZIP($project, $projectData);
                case 'csv':
                    return $this->exportToCSV($projectData);
                default:
                    return ['success' => false, 'error' => 'Unsupported export format'];
            }
            
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->log("Error exporting project: " . $e->getMessage());
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Export project as PDF
     */
    private function exportToPDF($project, $projectData) {
        try {
            $pdfContent = $this->generatePDFContent($project, $projectData);
            
            // Create exports directory if it doesn't exist
            if (!file_exists('exports')) {
                mkdir('exports', 0755, true);
            }
            
            $filename = 'project_' . $project['id'] . '_' . date('Y-m-d_H-i-s') . '.pdf';
            $filepath = 'exports/' . $filename;
            
            // For demo purposes, save as HTML file (in production, use PDF library)
            file_put_contents(str_replace('.pdf', '.html', $filepath), $pdfContent);
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'download_url' => '/exports/' . $filename,
                'format' => 'pdf'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'PDF export failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Export project as JSON
     */
    private function exportToJSON($project, $projectData) {
        try {
            $exportData = [
                'project' => [
                    'id' => $project['id'],
                    'name' => $project['name'],
                    'description' => $project['description'],
                    'created_at' => $project['created_at'],
                    'updated_at' => $project['updated_at']
                ],
                'data' => $projectData,
                'exported_at' => date('Y-m-d H:i:s'),
                'version' => '1.0'
            ];
            
            $filename = 'project_' . $project['id'] . '_' . date('Y-m-d_H-i-s') . '.json';
            $filepath = 'exports/' . $filename;
            
            // Create exports directory if it doesn't exist
            if (!file_exists('exports')) {
                mkdir('exports', 0755, true);
            }
            
            file_put_contents($filepath, json_encode($exportData, JSON_PRETTY_PRINT));
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'download_url' => '/exports/' . $filename,
                'format' => 'json'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'JSON export failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Export project as ZIP
     */
    private function exportToZIP($project, $projectData) {
        try {
            $filename = 'project_' . $project['id'] . '_' . date('Y-m-d_H-i-s') . '.zip';
            $filepath = 'exports/' . $filename;
            
            // Create exports directory if it doesn't exist
            if (!file_exists('exports')) {
                mkdir('exports', 0755, true);
            }
            
            // Create temporary files for ZIP
            $tempDir = 'exports/temp_' . uniqid();
            mkdir($tempDir, 0755, true);
            
            // Save project data as JSON
            file_put_contents($tempDir . '/project_data.json', json_encode($projectData, JSON_PRETTY_PRINT));
            
            // Save project metadata
            $metadata = [
                'name' => $project['name'],
                'description' => $project['description'],
                'created_at' => $project['created_at'],
                'updated_at' => $project['updated_at'],
                'exported_at' => date('Y-m-d H:i:s')
            ];
            file_put_contents($tempDir . '/metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));
            
            // Create ZIP file (simplified - in production use ZipArchive)
            $zipContent = "ZIP file created for project: " . $project['name'];
            file_put_contents($filepath, $zipContent);
            
            // Clean up temp directory
            $this->rrmdir($tempDir);
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'download_url' => '/exports/' . $filename,
                'format' => 'zip'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'ZIP export failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Export project as CSV
     */
    private function exportToCSV($projectData) {
        try {
            $filename = 'project_data_' . date('Y-m-d_H-i-s') . '.csv';
            $filepath = 'exports/' . $filename;
            
            // Create exports directory if it doesn't exist
            if (!file_exists('exports')) {
                mkdir('exports', 0755, true);
            }
            
            $csvContent = "Category,Item,Value,Unit\n";
            
            // Convert project data to CSV format
            foreach ($projectData as $category => $items) {
                if (is_array($items)) {
                    foreach ($items as $item => $value) {
                        $csvContent .= "{$category},{$item}," . (is_array($value) ? json_encode($value) : $value) . "\n";
                    }
                }
            }
            
            file_put_contents($filepath, $csvContent);
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'download_url' => '/exports/' . $filename,
                'format' => 'csv'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'CSV export failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generate PDF content (HTML format for demo)
     */
    private function generatePDFContent($project, $projectData) {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>MEP Project Report - {$project['name']}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                .section { margin-bottom: 20px; }
                .section h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        <link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
        <body>
            <div class='header'>
                <h1>MEP Coordination Project Report</h1>
                <h2>{$project['name']}</h2>
                <p><strong>Description:</strong> {$project['description']}</p>
                <p><strong>Generated:</strong> " . date('Y-m-d H:i:s') . "</p>
            </div>
            
            <div class='section'>
                <h3>Project Overview</h3>
                <table>
                    <tr><th>Property</th><th>Value</th></tr>
                    <tr><td>Project ID</td><td>{$project['id']}</td></tr>
                    <tr><td>Created</td><td>{$project['created_at']}</td></tr>
                    <tr><td>Last Updated</td><td>{$project['updated_at']}</td></tr>
                </table>
            </div>
            
            <div class='section'>
                <h3>MEP Systems Data</h3>
                <pre>" . json_encode($projectData, JSON_PRETTY_PRINT) . "</pre>
            </div>
        <script src="../../../public/assets/js/global-notifications.js"></script>
</body>
        </html>";
        
        return $html;
    }
    
    /**
     * Track project access
     */
    private function trackAccess($shareToken, $userEmail = null) {
        try {
            $query = "INSERT INTO mep_project_access_logs (share_token, user_email, accessed_at, ip_address) 
                     VALUES (?, ?, ?, ?)";
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $this->db->executeQuery($query, [$shareToken, $userEmail, date('Y-m-d H:i:s'), $ipAddress]);
            
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->log("Error tracking access: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Get sharing statistics
     */
    public function getSharingStats($projectId) {
        try {
            $query = "SELECT 
                        COUNT(*) as total_shares,
                        COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_shares,
                        COUNT(CASE WHEN expires_at > NOW() THEN 1 END) as valid_shares,
                        COUNT(CASE WHEN expires_at <= NOW() THEN 1 END) as expired_shares
                      FROM mep_project_shares 
                      WHERE project_id = ?";
            
            $result = $this->db->executeQuery($query, [$projectId]);
            
            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return null;
            
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->log("Error getting sharing stats: " . $e->getMessage());
            }
            return null;
        }
    }
    
    /**
     * Revoke share link
     */
    public function revokeShareLink($shareToken, $userId) {
        try {
            $query = "UPDATE mep_project_shares 
                     SET is_active = 0, revoked_at = NOW() 
                     WHERE share_token = ? AND user_id = ?";
            
            $result = $this->db->executeQuery($query, [$shareToken, $userId]);
            
            if ($result) {
                if ($this->logger) {
                    $this->logger->log("Share link revoked: {$shareToken} by user {$userId}");
                }
                return ['success' => true, 'message' => 'Share link revoked successfully'];
            }
            
            return ['success' => false, 'error' => 'Failed to revoke share link'];
            
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->log("Error revoking share link: " . $e->getMessage());
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Helper function to remove directory recursively
     */
    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}

// AJAX Handler for project sharing
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        // Initialize database connection
        require_once '../../../app/Config/db.php';
        $database = new Database();
        $db = $database->getConnection();
        
        // Initialize config
        require_once '../../../app/Config/config.php';
        $config = new Config();
        
        // Initialize logger
        $logger = null;
        if (class_exists('DevLogger')) {
            $logger = new DevLogger();
        }
        
        $projectSharing = new ProjectSharing($db, $config, $logger);
        
        switch ($_POST['action']) {
            case 'create_share':
                $result = $projectSharing->createShareLink(
                    $_POST['project_id'],
                    $_POST['user_id'],
                    $_POST['permissions'] ?? 'view',
                    $_POST['expiry_date'] ?? null
                );
                echo json_encode($result);
                break;
                
            case 'access_shared':
                $result = $projectSharing->accessSharedProject(
                    $_POST['share_token'],
                    $_POST['user_email'] ?? null
                );
                echo json_encode($result);
                break;
                
            case 'export_project':
                $result = $projectSharing->exportProject(
                    $_POST['project_id'],
                    $_POST['format'] ?? 'pdf',
                    $_POST['user_id'] ?? null
                );
                echo json_encode($result);
                break;
                
            case 'get_sharing_stats':
                $result = $projectSharing->getSharingStats($_POST['project_id']);
                echo json_encode(['success' => true, 'stats' => $result]);
                break;
                
            case 'revoke_share':
                $result = $projectSharing->revokeShareLink(
                    $_POST['share_token'],
                    $_POST['user_id']
                );
                echo json_encode($result);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?>
