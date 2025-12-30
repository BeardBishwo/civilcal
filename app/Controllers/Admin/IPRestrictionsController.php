<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\IPRestrictionService;

class IPRestrictionsController extends Controller
{
    private $service;

    public function __construct()
    {
        parent::__construct();
        if (!$this->auth->isAdmin()) {
            redirect('/login');
            exit;
        }
        $this->service = new IPRestrictionService();
    }

    public function index()
    {
        $whitelist = $this->service->getRestrictions('whitelist');
        $blacklist = $this->service->getRestrictions('blacklist');
        
        $this->view->render('admin/security/ip_restrictions', [
            'page_title' => 'IP Access Restrictions',
            'whitelist' => $whitelist,
            'blacklist' => $blacklist
        ]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ip = $_POST['ip_address'] ?? '';
            $type = $_POST['restriction_type'] ?? 'blacklist';
            $reason = $_POST['reason'] ?? '';
            $expiresAt = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
            
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $this->service->addRestriction($ip, $type, $reason, $expiresAt);
                $_SESSION['success_message'] = 'IP restriction added successfully.';
            } else {
                $_SESSION['error_message'] = 'Invalid IP address.';
            }
        }
        redirect('/admin/security/ip-restrictions');
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            if ($id) {
                $this->service->removeRestriction($id);
                $_SESSION['success_message'] = 'IP restriction removed successfully.';
            }
        }
        redirect('/admin/security/ip-restrictions');
    }
}
