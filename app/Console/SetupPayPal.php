<?php
/**
 * PayPal Setup Tool
 * 
 * Automatically creates PayPal products and billing plans for all subscription plans
 * Run this after setting up your PayPal credentials
 * 
 * Usage: php app/Console/SetupPayPal.php
 */

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Config/PayPal.php';
require_once __DIR__ . '/../Services/PayPalService.php';

use App\Core\Database;
use App\Config\PayPal as PayPalConfig;
use App\Services\PayPalService;

echo "==============================================\n";
echo "PayPal Product & Billing Plan Setup\n";
echo "==============================================\n\n";

// Check configuration
echo "Step 1: Validating PayPal configuration...\n";
$errors = PayPalConfig::validateConfig();

if (!empty($errors)) {
    echo "✗ Configuration errors found:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\nPlease configure PayPal credentials in your .env file.\n";
    echo "See config/paypal.env.example for reference.\n\n";
    exit(1);
}

echo "✓ Configuration valid\n";
echo "  Mode: " . PayPalConfig::getMode() . "\n";
echo "  Currency: " . PayPalConfig::getCurrency() . "\n\n";

// Test connection
echo "Step 2: Testing PayPal API connection...\n";
$connectionTest = PayPalConfig::testConnection();

if (!$connectionTest['success']) {
    echo "✗ Connection failed: " . $connectionTest['message'] . "\n\n";
    exit(1);
}

echo "✓ Connected to PayPal API\n\n";

// Get database connection
echo "Step 3: Connecting to database...\n";
try {
    $db = Database::getInstance()->getConnection();
    echo "✓ Database connected\n\n";
} catch (\Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Get all subscription plans
echo "Step 4: Loading subscription plans...\n";
try {
    $stmt = $db->query("SELECT * FROM subscription_plans WHERE is_active = 1 ORDER BY id");
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($plans)) {
        echo "✗ No active subscription plans found in database\n";
        echo "  Please create subscription plans first.\n\n";
        exit(1);
    }
    
    echo "✓ Found " . count($plans) . " active subscription plan(s)\n\n";
    
    foreach ($plans as $plan) {
        echo "  - {$plan['name']} (Monthly: \${$plan['price_monthly']}, Yearly: \${$plan['price_yearly']})\n";
    }
    echo "\n";
    
} catch (\Exception $e) {
    echo "✗ Error loading plans: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Create PayPal billing plans
echo "Step 5: Creating PayPal billing plans...\n\n";
$paypalService = new PayPalService();
$successCount = 0;
$errorCount = 0;

foreach ($plans as $plan) {
    echo "Processing: {$plan['name']}\n";
    echo str_repeat('-', 50) . "\n";
    
    // Check if already has PayPal plan IDs
    if (!empty($plan['paypal_plan_id_monthly']) && !empty($plan['paypal_plan_id_yearly'])) {
        echo "⚠ Plan already has PayPal IDs configured\n";
        echo "  Monthly Plan ID: {$plan['paypal_plan_id_monthly']}\n";
        echo "  Yearly Plan ID: {$plan['paypal_plan_id_yearly']}\n";
        echo "  Skipping...\n\n";
        continue;
    }
    
    // Create monthly billing plan
    echo "Creating monthly billing plan...\n";
    $monthlyResult = $paypalService->createBillingPlan($plan, 'monthly');
    
    if ($monthlyResult['success']) {
        echo "✓ Monthly plan created: {$monthlyResult['plan_id']}\n";
        $monthlyPlanId = $monthlyResult['plan_id'];
        $successCount++;
    } else {
        echo "✗ Failed to create monthly plan: {$monthlyResult['message']}\n";
        $monthlyPlanId = null;
        $errorCount++;
    }
    
    // Create yearly billing plan
    echo "Creating yearly billing plan...\n";
    $yearlyResult = $paypalService->createBillingPlan($plan, 'yearly');
    
    if ($yearlyResult['success']) {
        echo "✓ Yearly plan created: {$yearlyResult['plan_id']}\n";
        $yearlyPlanId = $yearlyResult['plan_id'];
        $successCount++;
    } else {
        echo "✗ Failed to create yearly plan: {$yearlyResult['message']}\n";
        $yearlyPlanId = null;
        $errorCount++;
    }
    
    // Update database with PayPal plan IDs
    if ($monthlyPlanId || $yearlyPlanId) {
        echo "Updating database...\n";
        try {
            $updateSql = "UPDATE subscription_plans SET ";
            $updates = [];
            $params = [];
            
            if ($monthlyPlanId) {
                $updates[] = "paypal_plan_id_monthly = :monthly_id";
                $params[':monthly_id'] = $monthlyPlanId;
            }
            
            if ($yearlyPlanId) {
                $updates[] = "paypal_plan_id_yearly = :yearly_id";
                $params[':yearly_id'] = $yearlyPlanId;
            }
            
            $updateSql .= implode(', ', $updates) . " WHERE id = :plan_id";
            $params[':plan_id'] = $plan['id'];
            
            $stmt = $db->prepare($updateSql);
            $stmt->execute($params);
            
            echo "✓ Database updated\n";
            
        } catch (\Exception $e) {
            echo "✗ Database update failed: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
    
    echo "\n";
}

// Summary
echo "==============================================\n";
echo "Setup Complete!\n";
echo "==============================================\n";
echo "✓ Successful: $successCount billing plan(s) created\n";
if ($errorCount > 0) {
    echo "✗ Errors: $errorCount\n";
}
echo "\n";

// Display final plan configuration
echo "Final Configuration:\n";
echo str_repeat('-', 50) . "\n";

$stmt = $db->query("SELECT * FROM subscription_plans WHERE is_active = 1 ORDER BY id");
$finalPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($finalPlans as $plan) {
    echo "\n{$plan['name']}:\n";
    echo "  Monthly Price: \${$plan['price_monthly']}\n";
    echo "  Yearly Price: \${$plan['price_yearly']}\n";
    
    if (!empty($plan['paypal_plan_id_monthly'])) {
        echo "  ✓ Monthly Plan ID: {$plan['paypal_plan_id_monthly']}\n";
    } else {
        echo "  ✗ Monthly Plan ID: Not configured\n";
    }
    
    if (!empty($plan['paypal_plan_id_yearly'])) {
        echo "  ✓ Yearly Plan ID: {$plan['paypal_plan_id_yearly']}\n";
    } else {
        echo "  ✗ Yearly Plan ID: Not configured\n";
    }
}

echo "\n==============================================\n";
echo "Next Steps:\n";
echo "==============================================\n";
echo "1. Verify plans in PayPal dashboard\n";
echo "2. Set up webhook endpoint\n";
echo "3. Test subscription checkout flow\n";
echo "4. Configure webhook events\n\n";

echo "PayPal Dashboard: ";
if (PayPalConfig::isSandbox()) {
    echo "https://www.sandbox.paypal.com/\n";
} else {
    echo "https://www.paypal.com/\n";
}
echo "\n";
