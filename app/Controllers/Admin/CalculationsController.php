<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Calculation;
use App\Models\User;

class CalculationsController extends Controller
{
    private $calculationModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->calculationModel = new Calculation();
        $this->userModel = new User();
    }

    public function index()
    {
        // Check admin access
        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        // Get recent calculations using model
        $calculationsResult = $this->calculationModel->getAll([], 1, 100);
        $calculations = $calculationsResult['calculations'];

        // Enhance calculations data with user info
        foreach ($calculations as &$calc) {
            // Add user info if user_id exists
            if (!empty($calc['user_id'])) {
                $user = $this->userModel->find($calc['user_id']);
                $calc['user_name'] = $user ? ($user['first_name'] . ' ' . $user['last_name']) : 'Unknown User';
            } else {
                $calc['user_name'] = 'Guest';
            }
            
            // Format timestamps
            $calc['created_at_formatted'] = date('M j, Y g:i A', strtotime($calc['created_at']));
            $calc['updated_at_formatted'] = date('M j, Y g:i A', strtotime($calc['updated_at']));
        }

        // Render the view with admin layout
        $this->view->render('admin/calculations/index', [
            'calculations' => $calculations,
            'total' => $calculationsResult['total'],
            'currentPage' => 1,
            'perPage' => 100,
            'page_title' => 'Calculations Management',
            'currentUser' => $currentUser
        ]);
    }

    public function delete($id)
    {
        // Check admin access
        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }

        try {
            $deleted = $this->calculationModel->delete($id);
            
            if ($deleted) {
                echo json_encode(['success' => true, 'message' => 'Calculation deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Failed to delete calculation']);
            }
        } catch (\Exception $e) {
            error_log('Calculation delete error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred while deleting the calculation']);
        }
    }

    public function export()
    {
        // Check admin access
        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        // Get all calculations for export
        $calculationsResult = $this->calculationModel->getAll([], 1, 10000);
        $calculations = $calculationsResult['calculations'];

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="calculations_export.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Create CSV output
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, ['ID', 'User ID', 'Category', 'Tool', 'Inputs', 'Result', 'Created At', 'Updated At']);

        // Add data rows
        foreach ($calculations as $calc) {
            fputcsv($output, [
                $calc['id'],
                $calc['user_id'],
                $calc['category'],
                $calc['tool'],
                json_encode($calc['inputs']),
                json_encode($calc['result']),
                $calc['created_at'],
                $calc['updated_at']
            ]);
        }

        fclose($output);
        exit;
    }
}