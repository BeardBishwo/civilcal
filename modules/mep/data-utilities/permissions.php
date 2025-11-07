<?php
require_once '../../../../includes/config.php';
require_once '../../../includes/Security.php';

class MEPPermissions {
    private $db;
    private $security;
    
    public function __construct() {
        global $db;
        $this->db = $db;
        $this->security = new Security();
    }
    
    /**
     * Get all permissions
     */
    public function getAllPermissions() {
        $sql = "SELECT * FROM mep_permissions ORDER BY category, permission_name";
        $result = $this->db->executeQuery($sql);
        $permissions = [];
        
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }
        
        return $permissions;
    }
    
    /**
     * Get permissions by category
     */
    public function getPermissionsByCategory($category) {
        $sql = "SELECT * FROM mep_permissions WHERE category = ? ORDER BY permission_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }
        
        return $permissions;
    }
    
    /**
     * Get user permissions
     */
    public function getUserPermissions($userId) {
        $sql = "SELECT p.*, up.granted, up.granted_date, up.granted_by 
                FROM mep_permissions p 
                LEFT JOIN mep_user_permissions up ON p.id = up.permission_id AND up.user_id = ?
                ORDER BY p.category, p.permission_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }
        
        return $permissions;
    }
    
    /**
     * Grant permission to user
     */
    public function grantPermission($userId, $permissionId, $grantedBy) {
        // Check if permission already exists
        $checkSql = "SELECT id FROM mep_user_permissions WHERE user_id = ? AND permission_id = ?";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->bind_param("ii", $userId, $permissionId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            // Update existing permission
            $sql = "UPDATE mep_user_permissions SET granted = 1, granted_date = NOW(), granted_by = ? 
                    WHERE user_id = ? AND permission_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iii", $grantedBy, $userId, $permissionId);
        } else {
            // Insert new permission
            $sql = "INSERT INTO mep_user_permissions (user_id, permission_id, granted, granted_date, granted_by) 
                    VALUES (?, ?, 1, NOW(), ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iii", $userId, $permissionId, $grantedBy);
        }
        
        $result = $stmt->execute();
        
        if ($result) {
            // Log the permission grant
            $this->logPermissionChange($userId, $permissionId, 'GRANTED', $grantedBy);
        }
        
        return $result;
    }
    
    /**
     * Revoke permission from user
     */
    public function revokePermission($userId, $permissionId, $revokedBy) {
        $sql = "UPDATE mep_user_permissions SET granted = 0, revoked_date = NOW(), revoked_by = ? 
                WHERE user_id = ? AND permission_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iii", $revokedBy, $userId, $permissionId);
        $result = $stmt->execute();
        
        if ($result) {
            // Log the permission revocation
            $this->logPermissionChange($userId, $permissionId, 'REVOKED', $revokedBy);
        }
        
        return $result;
    }
    
    /**
     * Check if user has specific permission
     */
    public function hasPermission($userId, $permissionName) {
        $sql = "SELECT up.granted 
                FROM mep_user_permissions up 
                JOIN mep_permissions p ON up.permission_id = p.id 
                WHERE up.user_id = ? AND p.permission_name = ? AND up.granted = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $userId, $permissionName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    /**
     * Create new permission
     */
    public function createPermission($name, $description, $category, $createdBy) {
        $sql = "INSERT INTO mep_permissions (permission_name, description, category, created_by, created_date) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $name, $description, $category, $createdBy);
        $result = $stmt->execute();
        
        return $result ? $this->db->insert_id : false;
    }
    
    /**
     * Update permission
     */
    public function updatePermission($id, $name, $description, $category) {
        $sql = "UPDATE mep_permissions SET permission_name = ?, description = ?, category = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $name, $description, $category, $id);
        return $stmt->execute();
    }
    
    /**
     * Delete permission
     */
    public function deletePermission($id) {
        // First revoke from all users
        $sql = "DELETE FROM mep_user_permissions WHERE permission_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Then delete the permission
        $sql = "DELETE FROM mep_permissions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Get role permissions
     */
    public function getRolePermissions($roleId) {
        $sql = "SELECT p.*, rp.granted 
                FROM mep_permissions p 
                JOIN mep_role_permissions rp ON p.id = rp.permission_id 
                WHERE rp.role_id = ? 
                ORDER BY p.category, p.permission_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $roleId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }
        
        return $permissions;
    }
    
    /**
     * Grant permission to role
     */
    public function grantRolePermission($roleId, $permissionId, $grantedBy) {
        $sql = "INSERT INTO mep_role_permissions (role_id, permission_id, granted, granted_date, granted_by) 
                VALUES (?, ?, 1, NOW(), ?) 
                ON DUPLICATE KEY UPDATE granted = 1, granted_date = NOW(), granted_by = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiii", $roleId, $permissionId, $grantedBy, $grantedBy);
        return $stmt->execute();
    }
    
    /**
     * Revoke permission from role
     */
    public function revokeRolePermission($roleId, $permissionId) {
        $sql = "UPDATE mep_role_permissions SET granted = 0, revoked_date = NOW() 
                WHERE role_id = ? AND permission_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $roleId, $permissionId);
        return $stmt->execute();
    }
    
    /**
     * Log permission changes
     */
    private function logPermissionChange($userId, $permissionId, $action, $changedBy) {
        $sql = "INSERT INTO mep_permission_logs (user_id, permission_id, action, changed_by, change_date) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iisi", $userId, $permissionId, $action, $changedBy);
        $stmt->execute();
    }
    
    /**
     * Get permission audit log
     */
    public function getPermissionLogs($userId = null, $limit = 100) {
        $sql = "SELECT l.*, u.username as user_name, p.permission_name, ch.username as changed_by_name 
                FROM mep_permission_logs l 
                LEFT JOIN users u ON l.user_id = u.id 
                LEFT JOIN mep_permissions p ON l.permission_id = p.id 
                LEFT JOIN users ch ON l.changed_by = ch.id";
        
        if ($userId) {
            $sql .= " WHERE l.user_id = ?";
            $stmt = $this->db->prepare($sql . " ORDER BY l.change_date DESC LIMIT ?");
            $stmt->bind_param("ii", $userId, $limit);
        } else {
            $stmt = $this->db->prepare($sql . " ORDER BY l.change_date DESC LIMIT ?");
            $stmt->bind_param("i", $limit);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
        
        return $logs;
    }
    
    /**
     * Initialize default permissions
     */
    public function initializeDefaultPermissions() {
        $defaultPermissions = [
            // HVAC Permissions
            ['hvac_view', 'View HVAC calculations', 'HVAC'],
            ['hvac_edit', 'Edit HVAC calculations', 'HVAC'],
            ['hvac_delete', 'Delete HVAC calculations', 'HVAC'],
            ['hvac_admin', 'HVAC system administration', 'HVAC'],
            
            // Electrical Permissions
            ['electrical_view', 'View electrical calculations', 'Electrical'],
            ['electrical_edit', 'Edit electrical calculations', 'Electrical'],
            ['electrical_delete', 'Delete electrical calculations', 'Electrical'],
            ['electrical_admin', 'Electrical system administration', 'Electrical'],
            
            // Plumbing Permissions
            ['plumbing_view', 'View plumbing calculations', 'Plumbing'],
            ['plumbing_edit', 'Edit plumbing calculations', 'Plumbing'],
            ['plumbing_delete', 'Delete plumbing calculations', 'Plumbing'],
            ['plumbing_admin', 'Plumbing system administration', 'Plumbing'],
            
            // Fire Protection Permissions
            ['fire_view', 'View fire protection calculations', 'Fire Protection'],
            ['fire_edit', 'Edit fire protection calculations', 'Fire Protection'],
            ['fire_delete', 'Delete fire protection calculations', 'Fire Protection'],
            ['fire_admin', 'Fire protection system administration', 'Fire Protection'],
            
            // Coordination Permissions
            ['coordination_view', 'View coordination analysis', 'Coordination'],
            ['coordination_edit', 'Edit coordination analysis', 'Coordination'],
            ['coordination_clash', 'Perform clash detection', 'Coordination'],
            ['coordination_admin', 'Coordination system administration', 'Coordination'],
            
            // Energy & Sustainability Permissions
            ['energy_view', 'View energy analysis', 'Energy & Sustainability'],
            ['energy_edit', 'Edit energy analysis', 'Energy & Sustainability'],
            ['energy_optimize', 'Optimize energy efficiency', 'Energy & Sustainability'],
            ['energy_admin', 'Energy system administration', 'Energy & Sustainability'],
            
            // Cost Management Permissions
            ['cost_view', 'View cost analysis', 'Cost Management'],
            ['cost_edit', 'Edit cost analysis', 'Cost Management'],
            ['cost_approve', 'Approve cost estimates', 'Cost Management'],
            ['cost_admin', 'Cost management administration', 'Cost Management'],
            
            // Reports Permissions
            ['reports_view', 'View reports', 'Reports & Documentation'],
            ['reports_generate', 'Generate reports', 'Reports & Documentation'],
            ['reports_export', 'Export reports', 'Reports & Documentation'],
            ['reports_admin', 'Reports administration', 'Reports & Documentation'],
            
            // Administration Permissions
            ['admin_users', 'User management', 'Administration'],
            ['admin_permissions', 'Permission management', 'Administration'],
            ['admin_system', 'System configuration', 'Administration'],
            ['admin_logs', 'View system logs', 'Administration'],
            
            // Data & Utilities Permissions
            ['data_view', 'View data utilities', 'Data & Utilities'],
            ['data_manage', 'Manage data utilities', 'Data & Utilities'],
            ['data_import', 'Import data', 'Data & Utilities'],
            ['data_export', 'Export data', 'Data & Utilities'],
        ];
        
        foreach ($defaultPermissions as $perm) {
            // Check if permission already exists
            $checkSql = "SELECT id FROM mep_permissions WHERE permission_name = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->bind_param("s", $perm[0]);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows == 0) {
                $this->createPermission($perm[0], $perm[1], $perm[2], 1); // Created by admin (user ID 1)
            }
        }
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $permissions = new MEPPermissions();
    
    switch ($action) {
        case 'get_permissions':
            $userId = $_POST['user_id'] ?? null;
            if ($userId) {
                $result = $permissions->getUserPermissions($userId);
            } else {
                $result = $permissions->getAllPermissions();
            }
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'grant_permission':
            $userId = $_POST['user_id'];
            $permissionId = $_POST['permission_id'];
            $grantedBy = $_SESSION['user_id'] ?? 1;
            $result = $permissions->grantPermission($userId, $permissionId, $grantedBy);
            echo json_encode(['success' => $result]);
            break;
            
        case 'revoke_permission':
            $userId = $_POST['user_id'];
            $permissionId = $_POST['permission_id'];
            $revokedBy = $_SESSION['user_id'] ?? 1;
            $result = $permissions->revokePermission($userId, $permissionId, $revokedBy);
            echo json_encode(['success' => $result]);
            break;
            
        case 'check_permission':
            $userId = $_POST['user_id'];
            $permissionName = $_POST['permission_name'];
            $result = $permissions->hasPermission($userId, $permissionName);
            echo json_encode(['success' => true, 'has_permission' => $result]);
            break;
            
        case 'get_logs':
            $userId = $_POST['user_id'] ?? null;
            $limit = $_POST['limit'] ?? 100;
            $result = $permissions->getPermissionLogs($userId, $limit);
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'initialize_permissions':
            $permissions->initializeDefaultPermissions();
            echo json_encode(['success' => true, 'message' => 'Default permissions initialized']);
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
    <title>MEP Permissions Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .permission-card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #fff;
            transition: all 0.2s;
        }
        .permission-card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .permission-granted {
            border-left: 4px solid #28a745;
            background-color: #f8fff9;
        }
        .permission-denied {
            border-left: 4px solid #dc3545;
            background-color: #fff8f8;
        }
        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            margin: 2rem 0 1rem 0;
            border-radius: 0.375rem;
        }
        .log-entry {
            border-left: 3px solid #6c757d;
            padding-left: 1rem;
            margin-bottom: 0.75rem;
        }
        .log-granted { border-left-color: #28a745; }
        .log-revoked { border-left-color: #dc3545; }
        .filter-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-shield-alt me-2"></i>MEP Permissions Management
                    </h1>
                    <button class="btn btn-primary" onclick="initializePermissions()">
                        <i class="fas fa-magic me-2"></i>Initialize Default Permissions
                    </button>
                </div>

                <!-- Statistics -->
                <div class="row mb-4" id="statsContainer">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Total Permissions</h5>
                            <h3 id="totalPermissions">-</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Categories</h5>
                            <h3 id="totalCategories">-</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Granted to User</h5>
                            <h3 id="grantedPermissions">-</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Recent Changes</h5>
                            <h3 id="recentChanges">-</h3>
                        </div>
                    </div>
                </div>

                <!-- User Selection -->
                <div class="filter-section">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="userSelect" class="form-label">Select User</label>
                            <select class="form-select" id="userSelect" onchange="loadUserPermissions()">
                                <option value="">Choose a user...</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button class="btn btn-outline-secondary me-2" onclick="loadAllPermissions()">
                                <i class="fas fa-list me-2"></i>View All Permissions
                            </button>
                            <button class="btn btn-outline-info" onclick="loadPermissionLogs()">
                                <i class="fas fa-history me-2"></i>View Logs
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Permissions Display -->
                <div id="permissionsContainer">
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Select a user to view their permissions</p>
                    </div>
                </div>

                <!-- Logs Modal -->
                <div class="modal fade" id="logsModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-history me-2"></i>Permission Audit Log
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div id="logsContainer">
                                    <div class="text-center py-3">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
            loadStatistics();
        });

        function loadUsers() {
            fetch('../../../api/get_users.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('userSelect');
                        select.innerHTML = '<option value="">Choose a user...</option>';
                        data.data.forEach(user => {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = `${user.username} (${user.email})`;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading users:', error));
        }

        function loadStatistics() {
            fetch('api-endpoints.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=get_permissions'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const permissions = data.data;
                    const categories = [...new Set(permissions.map(p => p.category))];
                    
                    document.getElementById('totalPermissions').textContent = permissions.length;
                    document.getElementById('totalCategories').textContent = categories.length;
                }
            })
            .catch(error => console.error('Error loading statistics:', error));
        }

        function loadUserPermissions() {
            const userId = document.getElementById('userSelect').value;
            if (!userId) {
                document.getElementById('permissionsContainer').innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Select a user to view their permissions</p>
                    </div>
                `;
                return;
            }

            const formData = new FormData();
            formData.append('action', 'get_permissions');
            formData.append('user_id', userId);

            fetch('permissions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPermissions(data.data);
                    updateGrantedCount(data.data);
                } else {
                    alert('Error loading permissions: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function loadAllPermissions() {
            document.getElementById('userSelect').value = '';
            const formData = new FormData();
            formData.append('action', 'get_permissions');

            fetch('permissions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayAllPermissions(data.data);
                } else {
                    alert('Error loading permissions: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function displayPermissions(permissions) {
            const container = document.getElementById('permissionsContainer');
            const userId = document.getElementById('userSelect').value;
            
            // Group by category
            const grouped = permissions.reduce((acc, perm) => {
                if (!acc[perm.category]) acc[perm.category] = [];
                acc[perm.category].push(perm);
                return acc;
            }, {});

            let html = '';
            Object.keys(grouped).forEach(category => {
                html += `
                    <div class="category-header">
                        <h4 class="mb-0">
                            <i class="fas fa-folder me-2"></i>${category}
                        </h4>
                    </div>
                `;
                
                grouped[category].forEach(permission => {
                    const isGranted = permission.granted == 1;
                    const statusClass = isGranted ? 'permission-granted' : 'permission-denied';
                    const statusIcon = isGranted ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger';
                    const actionButton = isGranted ? 
                        `<button class="btn btn-sm btn-outline-danger" onclick="revokePermission(${userId}, ${permission.id})">
                            <i class="fas fa-ban me-1"></i>Revoke
                        </button>` :
                        `<button class="btn btn-sm btn-outline-success" onclick="grantPermission(${userId}, ${permission.id})">
                            <i class="fas fa-check me-1"></i>Grant
                        </button>`;

                    html += `
                        <div class="permission-card ${statusClass}">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-1">${permission.permission_name}</h6>
                                    <p class="text-muted mb-2">${permission.description}</p>
                                    ${permission.granted_date ? `
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            ${new Date(permission.granted_date).toLocaleDateString()}
                                            ${permission.granted_by ? ` by User #${permission.granted_by}` : ''}
                                        </small>
                                    ` : ''}
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="mb-2">
                                        <i class="${statusIcon} me-2"></i>
                                        <span class="fw-bold">${isGranted ? 'GRANTED' : 'DENIED'}</span>
                                    </div>
                                    ${actionButton}
                                </div>
                            </div>
                        </div>
                    `;
                });
            });

            container.innerHTML = html;
        }

        function displayAllPermissions(permissions) {
            const container = document.getElementById('permissionsContainer');
            
            // Group by category
            const grouped = permissions.reduce((acc, perm) => {
                if (!acc[perm.category]) acc[perm.category] = [];
                acc[perm.category].push(perm);
                return acc;
            }, {});

            let html = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>All system permissions (not assigned to users)</div>';
            
            Object.keys(grouped).forEach(category => {
                html += `
                    <div class="category-header">
                        <h4 class="mb-0">
                            <i class="fas fa-folder me-2"></i>${category}
                        </h4>
                    </div>
                `;
                
                grouped[category].forEach(permission => {
                    html += `
                        <div class="permission-card">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-1">${permission.permission_name}</h6>
                                    <p class="text-muted mb-2">${permission.description}</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editPermission(${permission.id})">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            });

            container.innerHTML = html;
        }

        function updateGrantedCount(permissions) {
            const granted = permissions.filter(p => p.granted == 1).length;
            document.getElementById('grantedPermissions').textContent = granted;
        }

        function grantPermission(userId, permissionId) {
            const formData = new FormData();
            formData.append('action', 'grant_permission');
            formData.append('user_id', userId);
            formData.append('permission_id', permissionId);

            fetch('permissions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadUserPermissions();
                    showAlert('Permission granted successfully', 'success');
                } else {
                    showAlert('Error granting permission: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error granting permission', 'danger');
            });
        }

        function revokePermission(userId, permissionId) {
            if (!confirm('Are you sure you want to revoke this permission?')) return;

            const formData = new FormData();
            formData.append('action', 'revoke_permission');
            formData.append('user_id', userId);
            formData.append('permission_id', permissionId);

            fetch('permissions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadUserPermissions();
                    showAlert('Permission revoked successfully', 'success');
                } else {
                    showAlert('Error revoking permission: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error revoking permission', 'danger');
            });
        }

        function loadPermissionLogs() {
            const formData = new FormData();
            formData.append('action', 'get_logs');
            formData.append('limit', 50);

            fetch('permissions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayLogs(data.data);
                    new bootstrap.Modal(document.getElementById('logsModal')).show();
                } else {
                    showAlert('Error loading logs: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error loading logs', 'danger');
            });
        }

        function displayLogs(logs) {
            const container = document.getElementById('logsContainer');
            
            if (logs.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No permission changes recorded</p>';
                return;
            }

            let html = '';
            logs.forEach(log => {
                const actionClass = log.action === 'GRANTED' ? 'log-granted' : 'log-revoked';
                const actionIcon = log.action === 'GRANTED' ? 'fas fa-plus-circle text-success' : 'fas fa-minus-circle text-danger';
                
                html += `
                    <div class="log-entry ${actionClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong><i class="${actionIcon} me-2"></i>${log.action}</strong>
                                <span class="ms-2">${log.permission_name || 'Unknown Permission'}</span>
                                <br>
                                <small class="text-muted">
                                    User: ${log.user_name || 'Unknown'} | 
                                    By: ${log.changed_by_name || 'System'} | 
                                    ${new Date(log.change_date).toLocaleString()}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function initializePermissions() {
            if (!confirm('This will create default system permissions. Continue?')) return;

            const formData = new FormData();
            formData.append('action', 'initialize_permissions');

            fetch('permissions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Default permissions initialized successfully', 'success');
                    loadStatistics();
                    if (document.getElementById('userSelect').value) {
                        loadUserPermissions();
                    }
                } else {
                    showAlert('Error initializing permissions: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error initializing permissions', 'danger');
            });
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
    </script>
</body>
</html>
