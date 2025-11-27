<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Calculation;

class CalculationsController extends Controller
{
    private $calculationModel;

    public function __construct()
    {
        parent::__construct();
        $this->checkAdminAccess();
        $this->calculationModel = new Calculation();
    }

    public function index()
    {
        // Get recent calculations using model
        $calculationsResult = $this->calculationModel->getAll([], 1, 100);
        $calculations = $calculationsResult['calculations'];

        // Enhance calculations data with user info
        foreach ($calculations as &$calc) {
            if ($calc['user_id']) {
                $user = $this->getUserInfo($calc['user_id']);
                $calc['username'] = $user['username'] ?? null;
                $calc['email'] = $user['email'] ?? null;
            } else {
                $calc['username'] = 'Guest';
                $calc['email'] = 'guest@example.com';
            }
        }

        // Get statistics using model
        $stats = $this->getCalculationStats();

        $data = [
            'page_title' => 'Calculations Management',
            'calculations' => $calculations,
            'stats' => $stats
        ];

        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/calculations/index', $data);
    }

    /**
     * Get calculation statistics using model method
     */
    private function getCalculationStats()
    {
        // Get statistics using the model method
        return $this->calculationModel->getCalculationStats();
    }

    /**
     * Get user info by ID
     */
    private function getUserInfo($userId)
    {
        try {
            $userStmt = $this->db->prepare("SELECT username, email FROM users WHERE id = ?");
            $userStmt->execute([$userId]);
            return $userStmt->fetch(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            redirect('/login');
            exit;
        }
    }
}