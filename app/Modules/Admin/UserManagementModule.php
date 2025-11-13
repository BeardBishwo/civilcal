<?php

namespace App\Modules\Admin;

use App\Core\AdminModule;
use App\Models\User;

/**
 * User Management Module - Complete user administration
 */
class UserManagementModule extends AdminModule
{
    protected function init()
    {
        $this->name = 'User Management';
        $this->version = '1.0.0';
        $this->description = 'Comprehensive user management system';
        $this->author = 'Bishwo Calculator Team';
        $this->icon = 'fas fa-users-cog';
        $this->permissions = ['admin', 'super_admin'];
    }
    
    public function registerMenu()
    {
        return [
            'title' => 'Users',
            'url' => '/admin/users',
            'icon' => $this->icon,
            'position' => 2,
            'submenu' => [
                [
                    'title' => 'All Users',
                    'url' => '/admin/users',
                    'icon' => 'fas fa-list'
                ],
                [
                    'title' => 'Add New User',
                    'url' => '/admin/users/create',
                    'icon' => 'fas fa-user-plus'
                ],
                [
                    'title' => 'User Roles',
                    'url' => '/admin/users/roles',
                    'icon' => 'fas fa-user-tag'
                ],
                [
                    'title' => 'Permissions',
                    'url' => '/admin/users/permissions',
                    'icon' => 'fas fa-key'
                ],
                [
                    'title' => 'Bulk Actions',
                    'url' => '/admin/users/bulk',
                    'icon' => 'fas fa-tasks'
                ]
            ]
        ];
    }
    
    public function renderWidget()
    {
        $userModel = new User();
        $users = $userModel->getAll();
        
        $stats = [
            'total' => count($users),
            'active' => count(array_filter($users, function($u) { return $u['is_active']; })),
            'admins' => count(array_filter($users, function($u) { return $u['role'] === 'admin'; })),
            'engineers' => count(array_filter($users, function($u) { return $u['role'] === 'engineer'; }))
        ];
        
        return [
            'title' => 'User Management',
            'content' => $this->getUserManagementWidget($stats),
            'size' => 'medium'
        ];
    }
    
    private function getUserManagementWidget($stats)
    {
        ob_start();
        ?>
        <div class="dashboard-widget user-management">
            <div class="widget-header">
                <h3><i class="fas fa-users-cog"></i> User Management</h3>
                <a href="/admin/users" class="widget-link">View All</a>
            </div>
            <div class="widget-content">
                <div class="user-stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-details">
                            <div class="stat-number"><?php echo $stats['total']; ?></div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                        <div class="stat-details">
                            <div class="stat-number"><?php echo $stats['active']; ?></div>
                            <div class="stat-label">Active Users</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                        <div class="stat-details">
                            <div class="stat-number"><?php echo $stats['admins']; ?></div>
                            <div class="stat-label">Administrators</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-hard-hat"></i></div>
                        <div class="stat-details">
                            <div class="stat-number"><?php echo $stats['engineers']; ?></div>
                            <div class="stat-label">Engineers</div>
                        </div>
                    </div>
                </div>
                
                <div class="quick-actions">
                    <a href="/admin/users/create" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add User
                    </a>
                    <a href="/admin/users/bulk" class="btn btn-secondary btn-sm">
                        <i class="fas fa-tasks"></i> Bulk Actions
                    </a>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function getSettingsSchema()
    {
        return [
            'user_registration' => [
                'type' => 'checkbox',
                'label' => 'Allow Public Registration',
                'default' => true
            ],
            'default_role' => [
                'type' => 'select',
                'label' => 'Default User Role',
                'options' => [
                    'user' => 'User',
                    'engineer' => 'Engineer',
                    'admin' => 'Administrator'
                ],
                'default' => 'user'
            ],
            'email_verification_required' => [
                'type' => 'checkbox',
                'label' => 'Require Email Verification',
                'default' => true
            ],
            'password_policy' => [
                'type' => 'group',
                'label' => 'Password Policy',
                'fields' => [
                    'min_length' => [
                        'type' => 'number',
                        'label' => 'Minimum Length',
                        'default' => 8
                    ],
                    'require_uppercase' => [
                        'type' => 'checkbox',
                        'label' => 'Require Uppercase Letter',
                        'default' => true
                    ],
                    'require_numbers' => [
                        'type' => 'checkbox',
                        'label' => 'Require Numbers',
                        'default' => true
                    ]
                ]
            ]
        ];
    }
}
?>
