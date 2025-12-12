<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = $this->getSubscriptionPlans();
        $transactions = $this->getRecentTransactions();
        $stats = $this->getBillingStats();
        
        // Load the subscriptions management view
        // Load the subscriptions management view
        $this->view->render('admin/subscriptions/index', [
            'plans' => $plans,
            'transactions' => $transactions,
            'stats' => $stats,
            'title' => 'Subscription Management'
        ]);
    }

    public function createPlanPage()
    {
        // Render the create plan page
        $this->view->render('admin/subscriptions/create-plan', [
            'title' => 'Create Subscription Plan'
        ]);
    }

    public function createPlan()
    {
        if ($_POST) {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price_monthly' => $_POST['price_monthly'],
                'price_yearly' => $_POST['price_yearly'],
                'features' => json_encode($_POST['features']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Save to database logic would go here
            $result = $this->savePlan($data);
            
            echo json_encode($result);
            return;
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }

    private function getSubscriptionPlans()
    {
        // Mock data for subscription plans
        return [
            [
                'id' => 1,
                'name' => 'Free',
                'description' => 'Basic calculator access',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'features' => ['5 calculations per day', 'Basic calculators', 'Email support'],
                'is_active' => true,
                'subscribers' => 850
            ],
            [
                'id' => 2,
                'name' => 'Professional',
                'description' => 'For individual engineers',
                'price_monthly' => 9.99,
                'price_yearly' => 99.99,
                'features' => ['Unlimited calculations', 'All calculators', 'Priority support', 'Export features'],
                'is_active' => true,
                'subscribers' => 320
            ],
            [
                'id' => 3,
                'name' => 'Enterprise',
                'description' => 'For teams and companies',
                'price_monthly' => 29.99,
                'price_yearly' => 299.99,
                'features' => ['Everything in Professional', 'Team management', 'API access', 'Custom calculators'],
                'is_active' => true,
                'subscribers' => 45
            ]
        ];
    }

    private function getRecentTransactions()
    {
        // Mock data for recent transactions
        return [
            [
                'id' => 'TXN_001',
                'user' => 'John Doe',
                'plan' => 'Professional',
                'amount' => 9.99,
                'status' => 'completed',
                'date' => '2024-01-15 14:30:00'
            ],
            [
                'id' => 'TXN_002', 
                'user' => 'Jane Smith',
                'plan' => 'Enterprise',
                'amount' => 29.99,
                'status' => 'completed',
                'date' => '2024-01-15 12:15:00'
            ],
            [
                'id' => 'TXN_003',
                'user' => 'Mike Johnson',
                'plan' => 'Professional',
                'amount' => 9.99,
                'status' => 'pending',
                'date' => '2024-01-15 10:45:00'
            ]
        ];
    }

    private function getBillingStats()
    {
        return [
            'total_revenue' => 4850.50,
            'monthly_recurring' => 1250.75,
            'active_subscribers' => 1215,
            'conversion_rate' => 3.2
        ];
    }

    private function savePlan($data)
    {
        // Database save logic would go here
        return ['success' => true, 'message' => 'Plan created successfully'];
    }

    public function edit($id)
    {
        // Get the plan data
        $plans = $this->getSubscriptionPlans();
        $plan = null;
        
        foreach ($plans as $p) {
            if ($p['id'] == $id) {
                $plan = $p;
                break;
            }
        }
        
        if (!$plan) {
            // Plan not found, redirect to index
            header('Location: ' . app_base_url('/admin/subscriptions'));
            exit;
        }
        
        // Render the edit plan page
        $this->view->render('admin/subscriptions/edit-plan', [
            'plan' => $plan,
            'title' => 'Edit Subscription Plan'
        ]);
    }

    public function update($id)
    {
        if ($_POST) {
            $data = [
                'id' => $id,
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price_monthly' => $_POST['price_monthly'],
                'price_yearly' => $_POST['price_yearly'],
                'features' => $_POST['features'], // Already JSON from frontend
                'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update database logic would go here
            $result = $this->updatePlan($data);
            
            echo json_encode($result);
            return;
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }

    private function updatePlan($data)
    {
        // Database update logic would go here
        return ['success' => true, 'message' => 'Plan updated successfully'];
    }
}
?>
