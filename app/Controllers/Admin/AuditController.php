<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class AuditController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Check if user is admin
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        // Get audit logs
        $logs = $this->getAuditLogs();
        
        // Render the audit view with admin layout
        $this->view->render('admin/audit/index', [
            'currentPage' => 'audit',
            'logs' => $logs,
            'title' => 'Audit Logs - Admin Panel'
        ]);
    }

    private function getAuditLogs()
    {
        // This would typically come from your database or log files
        // For now, we'll return mock data
        return [
            [
                'id' => 1,
                'user' => 'admin',
                'action' => 'User Login',
                'details' => 'Successful login',
                'ip_address' => '127.0.0.1',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-5 minutes'))
            ],
            [
                'id' => 2,
                'user' => 'john_doe',
                'action' => 'Calculator Used',
                'details' => 'Used Concrete Volume calculator',
                'ip_address' => '192.168.1.100',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-15 minutes'))
            ],
            [
                'id' => 3,
                'user' => 'jane_smith',
                'action' => 'Settings Updated',
                'details' => 'Updated email settings',
                'ip_address' => '192.168.1.101',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
            ]
        ];
    }
}