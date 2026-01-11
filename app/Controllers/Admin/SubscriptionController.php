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
                'description' => $_POST['description'] ?? '',
                'price_monthly' => (float)$_POST['price_monthly'],
                'price_yearly' => (float)$_POST['price_yearly'],
                'features' => is_array($_POST['features']) ? json_encode($_POST['features']) : $_POST['features'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->savePlan($data);
            
            echo json_encode($result);
            return;
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }

    private function getSubscriptionPlans()
    {
        return $this->subscriptionModel->getAll();
    }

    private function getRecentTransactions()
    {
        return $this->subscriptionModel->getTransactions(10);
    }

    private function getBillingStats()
    {
        return $this->subscriptionModel->getStats();
    }

    private function savePlan($data)
    {
        $success = $this->subscriptionModel->create($data);
        if ($success) {
            return ['success' => true, 'message' => 'Plan created successfully'];
        }
        return ['success' => false, 'message' => 'Failed to create plan'];
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
        $id = $data['id'];
        unset($data['id']);
        unset($data['updated_at']); // handled by MySQL trigger usually, but we can keep it if needed
        $data['updated_at'] = date('Y-m-d H:i:s');

        $success = $this->subscriptionModel->update($id, $data);
        if ($success) {
            return ['success' => true, 'message' => 'Plan updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update plan'];
    }
}
?>
