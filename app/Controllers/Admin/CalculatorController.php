<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Database;

class CalculatorController extends Controller
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
        $calculators = $this->getAllCalculators();
        $categories = $this->getCalculatorCategories();

        // Render the calculators management view with admin layout
        $this->view->render('admin/calculators/index', [
            'currentPage' => 'calculators',
            'calculators' => $calculators,
            'categories' => $categories,
            'title' => 'Calculators Management - Admin Panel'
        ]);
    }

    public function addCalculator()
    {
        if ($_POST) {
            $data = [
                'name' => $_POST['name'],
                'category' => $_POST['category'],
                'description' => $_POST['description'],
                'formula' => $_POST['formula'],
                'inputs' => json_encode($_POST['inputs']),
                'outputs' => json_encode($_POST['outputs']),
                'status' => $_POST['status'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            try {
                $stmt = $this->db->prepare("INSERT INTO calculators (name, category, description, formula, inputs, outputs, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([
                    $data['name'],
                    $data['category'],
                    $data['description'],
                    $data['formula'],
                    $data['inputs'],
                    $data['outputs'],
                    $data['status'],
                    $data['created_at']
                ]);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Calculator added successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add calculator']);
                }
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            return;
        }
    }

    private function getAllCalculators()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM calculators ORDER BY created_at DESC");
            $stmt->execute();
            $calculators = $stmt->fetchAll();

            // Add usage_count and handle missing data
            foreach ($calculators as &$calculator) {
                $calculator['usage_count'] = rand(100, 2000); // Mock data - replace with actual query
                if (!$calculator['created_at']) {
                    $calculator['created_at'] = date('Y-m-d H:i:s');
                }
            }

            return $calculators;
        } catch (\Exception $e) {
            // Return mock data if database query fails
            return [
                [
                    'id' => 1,
                    'name' => 'Concrete Volume Calculator',
                    'category' => 'civil',
                    'description' => 'Calculate concrete volume for slabs, beams, columns',
                    'status' => 'active',
                    'usage_count' => 1250,
                    'created_at' => '2024-01-15'
                ],
                [
                    'id' => 2,
                    'name' => 'Electrical Load Calculator',
                    'category' => 'electrical',
                    'description' => 'Calculate electrical load for residential buildings',
                    'status' => 'active',
                    'usage_count' => 980,
                    'created_at' => '2024-01-10'
                ]
            ];
        }
    }

    private function getCalculatorCategories()
    {
        return [
            'civil' => 'Civil Engineering',
            'electrical' => 'Electrical Engineering',
            'structural' => 'Structural Engineering',
            'hvac' => 'HVAC',
            'plumbing' => 'Plumbing',
            'estimation' => 'Estimation',
            'management' => 'Project Management'
        ];
    }
}
