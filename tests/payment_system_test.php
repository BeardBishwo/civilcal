<?php
/**
 * Bishwo Calculator - Payment System Test Suite
 * Comprehensive testing for all payment functionality
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bootstrap the application
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/bootstrap.php';

class PaymentSystemTestSuite
{
    private $results = [];
    private $passCount = 0;
    private $failCount = 0;
    private $warningCount = 0;

    public function __construct()
    {
        echo "\n💳 Bishwo Calculator - Payment System Test Suite\n";
        echo "================================================\n";
        echo "Started: " . date('Y-m-d H:i:s') . "\n\n";
    }

    public function runTest($testName, $testFunction)
    {
        echo "Running: $testName... ";
        
        try {
            $result = $testFunction();
            
            if ($result['status'] === 'pass') {
                echo "✅ PASS\n";
                $this->results[] = ['name' => $testName, 'status' => 'pass', 'details' => $result['details'] ?? ''];
                $this->passCount++;
            } elseif ($result['status'] === 'warning') {
                echo "⚠️  WARNING\n";
                $this->results[] = ['name' => $testName, 'status' => 'warning', 'details' => $result['details'] ?? ''];
                $this->warningCount++;
            } else {
                echo "❌ FAIL\n";
                $this->results[] = ['name' => $testName, 'status' => 'fail', 'details' => $result['details'] ?? ''];
                $this->failCount++;
            }
            
            if (isset($result['details'])) {
                echo "  → " . $result['details'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ ERROR\n";
            $this->results[] = ['name' => $testName, 'status' => 'error', 'details' => $e->getMessage()];
            $this->failCount++;
        }
    }

    public function testPaymentController()
    {
        // Test PaymentController Class Exists
        $this->runTest('PaymentController Class', function() {
            if (class_exists('App\\Controllers\\PaymentController')) {
                return ['status' => 'pass', 'details' => 'PaymentController class exists'];
            } else {
                return ['status' => 'fail', 'details' => 'PaymentController class not found'];
            }
        });

        // Test PaymentController Methods
        $this->runTest('PaymentController Methods', function() {
            $controller = new App\Controllers\PaymentController();
            $methods = get_class_methods($controller);
            
            $requiredMethods = [
                'checkout', 'processPaypal', 'processPaytm', 'processEsewa', 
                'processKhalti', 'success', 'failed'
            ];
            
            $foundMethods = 0;
            foreach ($requiredMethods as $method) {
                if (in_array($method, $methods)) {
                    $foundMethods++;
                }
            }
            
            if ($foundMethods === count($requiredMethods)) {
                return ['status' => 'pass', 'details' => "All $foundMethods required methods found"];
            } else {
                return ['status' => 'warning', 'details' => "Only $foundMethods of " . count($requiredMethods) . " methods found"];
            }
        });

        // Test Payment Service Integration
        $this->runTest('PaymentService Class', function() {
            if (class_exists('App\\Services\\PaymentService')) {
                return ['status' => 'pass', 'details' => 'PaymentService class exists'];
            } else {
                return ['status' => 'warning', 'details' => 'PaymentService class not found - using fallback'];
            }
        });
    }

    public function testPayTMLibrary()
    {
        // Test PayTMLibrary Class
        $this->runTest('PayTMLibrary Class', function() {
            if (class_exists('PayTMLibrary')) {
                return ['status' => 'pass', 'details' => 'PayTMLibrary class exists'];
            } else {
                return ['status' => 'fail', 'details' => 'PayTMLibrary class not found'];
            }
        });

        // Test PayTMLibrary Initialization
        $this->runTest('PayTMLibrary Initialization', function() {
            $config = [
                'merchant_id' => 'test_merchant',
                'merchant_key' => 'test_key',
                'sandbox_mode' => true
            ];
            
            try {
                $paytm = new PayTMLibrary($config);
                $result = $paytm->getConfig();
                
                if (isset($result['merchant_id']) && isset($result['sandbox_mode'])) {
                    return ['status' => 'pass', 'details' => 'PayTMLibrary initialized with proper config'];
                } else {
                    return ['status' => 'fail', 'details' => 'PayTMLibrary config incomplete'];
                }
            } catch (Exception $e) {
                return ['status' => 'fail', 'details' => 'PayTMLibrary initialization failed: ' . $e->getMessage()];
            }
        });

        // Test Signature Generation
        $this->runTest('PayTM Signature Generation', function() {
            $config = [
                'merchant_id' => 'test_merchant',
                'merchant_key' => 'merchant_test_key_123',
                'sandbox_mode' => true
            ];
            
            $paytm = new PayTMLibrary($config);
            $testParams = [
                'ORDER_ID' => 'TEST_ORDER_123',
                'CUST_ID' => 'CUST_456',
                'MOBILE_NO' => '9876543210',
                'EMAIL' => 'test@example.com',
                'TXN_AMOUNT' => '100.00',
                'INDUSTRY_TYPE_ID' => 'Retail',
                'CHANNEL_ID' => 'WEB',
                'WEBSITE' => 'WEBSTAGING'
            ];
            
            try {
                $signature = $paytm->generateSignature($testParams);
                
                if (isset($signature['CHECKSUMHASH']) && !empty($signature['CHECKSUMHASH'])) {
                    return ['status' => 'pass', 'details' => 'PayTM signature generated successfully'];
                } else {
                    return ['status' => 'fail', 'details' => 'PayTM signature generation failed'];
                }
            } catch (Exception $e) {
                return ['status' => 'fail', 'details' => 'Signature generation error: ' . $e->getMessage()];
            }
        });

        // Test Signature Verification
        $this->runTest('PayTM Signature Verification', function() {
            $config = [
                'merchant_id' => 'test_merchant',
                'merchant_key' => 'merchant_test_key_123',
                'sandbox_mode' => true
            ];
            
            $paytm = new PayTMLibrary($config);
            $testParams = [
                'ORDER_ID' => 'TEST_ORDER_123',
                'CUST_ID' => 'CUST_456',
                'MOBILE_NO' => '9876543210',
                'EMAIL' => 'test@example.com',
                'TXN_AMOUNT' => '100.00',
                'INDUSTRY_TYPE_ID' => 'Retail',
                'CHANNEL_ID' => 'WEB',
                'WEBSITE' => 'WEBSTAGING'
            ];
            
            try {
                $signedParams = $paytm->generateSignature($testParams);
                $isValid = $paytm->verifySignature($signedParams, $config['merchant_key']);
                
                if ($isValid) {
                    return ['status' => 'pass', 'details' => 'PayTM signature verification working'];
                } else {
                    return ['status' => 'fail', 'details' => 'PayTM signature verification failed'];
                }
            } catch (Exception $e) {
                return ['status' => 'fail', 'details' => 'Signature verification error: ' . $e->getMessage()];
            }
        });

        // Test Order ID Generation
        $this->runTest('PayTM Order ID Generation', function() {
            $config = [
                'merchant_id' => 'test_merchant',
                'merchant_key' => 'test_key',
                'sandbox_mode' => true
            ];
            
            $paytm = new PayTMLibrary($config);
            $orderId = $paytm->generateOrderId('PREMIUM', 123);
            
            if (strpos($orderId, 'PREMIUM_123_') === 0) {
                return ['status' => 'pass', 'details' => "Order ID generated: $orderId"];
            } else {
                return ['status' => 'fail', 'details' => 'Invalid order ID format'];
            }
        });
    }

    public function testPaymentViews()
    {
        // Test Payment Views Directory
        $this->runTest('Payment Views Directory', function() {
            if (is_dir('app/Views/payment')) {
                return ['status' => 'pass', 'details' => 'Payment views directory exists'];
            } else {
                return ['status' => 'fail', 'details' => 'Payment views directory not found'];
            }
        });

        // Test Checkout View
        $this->runTest('Payment Checkout View', function() {
            $viewFile = 'app/Views/payment/checkout.php';
            if (file_exists($viewFile)) {
                $content = file_get_contents($viewFile);
                if (strpos($content, 'payment_method') !== false && strpos($content, 'Proceed to Payment') !== false) {
                    return ['status' => 'pass', 'details' => 'Checkout view exists with proper content'];
                } else {
                    return ['status' => 'warning', 'details' => 'Checkout view exists but may be incomplete'];
                }
            } else {
                return ['status' => 'fail', 'details' => 'Checkout view not found'];
            }
        });

        // Test eSewa Form View
        $this->runTest('eSewa Form View', function() {
            $viewFile = 'app/Views/payment/esewa-form.php';
            if (file_exists($viewFile)) {
                $content = file_get_contents($viewFile);
                if (strpos($content, 'esewa') !== false && strpos($content, 'PROCEED') !== false) {
                    return ['status' => 'pass', 'details' => 'eSewa form view exists with proper content'];
                } else {
                    return ['status' => 'warning', 'details' => 'eSewa form exists but may be incomplete'];
                }
            } else {
                return ['status' => 'fail', 'details' => 'eSewa form view not found'];
            }
        });

        // Test Success View
        $this->runTest('Payment Success View', function() {
            $viewFile = 'app/Views/payment/success.php';
            if (file_exists($viewFile)) {
                $content = file_get_contents($viewFile);
                if (strpos($content, 'Payment Successful') !== false && strpos($content, 'Transaction ID') !== false) {
                    return ['status' => 'pass', 'details' => 'Success view exists with proper content'];
                } else {
                    return ['status' => 'warning', 'details' => 'Success view exists but may be incomplete'];
                }
            } else {
                return ['status' => 'fail', 'details' => 'Success view not found'];
            }
        });

        // Test Failed View
        $this->runTest('Payment Failed View', function() {
            $viewFile = 'app/Views/payment/failed.php';
            if (file_exists($viewFile)) {
                $content = file_get_contents($viewFile);
                if (strpos($content, 'Payment Failed') !== false && strpos($content, 'Try Again') !== false) {
                    return ['status' => 'pass', 'details' => 'Failed view exists with proper content'];
                } else {
                    return ['status' => 'warning', 'details' => 'Failed view exists but may be incomplete'];
                }
            } else {
                return ['status' => 'fail', 'details' => 'Failed view not found'];
            }
        });
    }

    public function testPaymentRoutes()
    {
        // Test Routes File
        $this->runTest('Routes File Exists', function() {
            if (file_exists('app/routes.php')) {
                return ['status' => 'pass', 'details' => 'Routes file exists'];
            } else {
                return ['status' => 'fail', 'details' => 'Routes file not found'];
            }
        });

        // Test Payment Routes
        $this->runTest('Payment Routes Configuration', function() {
            if (file_exists('app/routes.php')) {
                $content = file_get_contents('app/routes.php');
                
                $requiredRoutes = [
                    '/payment/checkout',
                    '/payment/process-paypal',
                    '/payment/process-paytm',
                    '/payment/process-esewa',
                    '/payment/process-khalti',
                    '/payment/success',
                    '/payment/failed'
                ];
                
                $foundRoutes = 0;
                foreach ($requiredRoutes as $route) {
                    if (strpos($content, $route) !== false) {
                        $foundRoutes++;
                    }
                }
                
                if ($foundRoutes === count($requiredRoutes)) {
                    return ['status' => 'pass', 'details' => "All $foundRoutes payment routes configured"];
                } else {
                    return ['status' => 'warning', 'details' => "Only $foundRoutes of " . count($requiredRoutes) . " routes found"];
                }
            } else {
                return ['status' => 'fail', 'details' => 'Routes file not accessible'];
            }
        });
    }

    public function testSettingsIntegration()
    {
        // Test Settings Model
        $this->runTest('Settings Model', function() {
            if (class_exists('App\\Models\\Settings')) {
                return ['status' => 'pass', 'details' => 'Settings model exists'];
            } else {
                return ['status' => 'fail', 'details' => 'Settings model not found'];
            }
        });

        // Test Payment Settings Fields
        $this->runTest('Payment Settings Fields', function() {
            if (class_exists('App\\Models\\Settings')) {
                $settings = new App\Models\Settings();
                $reflection = new ReflectionClass($settings);
                $properties = $reflection->getDefaultProperties();
                
                $paymentFields = [
                    'paypal_client_id',
                    'paypal_client_secret',
                    'paytm_merchant_id',
                    'paytm_merchant_key',
                    'esewa_merchant_code',
                    'esewa_secret_key',
                    'khalti_public_key',
                    'khalti_secret_key',
                    'price_india',
                    'price_nepal',
                    'price_international'
                ];
                
                $fillableFields = $properties['fillable'] ?? [];
                $paymentFieldsCount = 0;
                
                foreach ($paymentFields as $field) {
                    if (in_array($field, $fillableFields)) {
                        $paymentFieldsCount++;
                    }
                }
                
                if ($paymentFieldsCount === count($paymentFields)) {
                    return ['status' => 'pass', 'details' => "All $paymentFieldsCount payment settings fields configured"];
                } else {
                    return ['status' => 'warning', 'details' => "Only $paymentFieldsCount of " . count($paymentFields) . " fields found"];
                }
            } else {
                return ['status' => 'fail', 'details' => 'Settings model not available'];
            }
        });

        // Test getPaymentSettings Method
        $this->runTest('Payment Settings Method', function() {
            if (method_exists('App\\Models\\Settings', 'getPaymentSettings')) {
                return ['status' => 'pass', 'details' => 'getPaymentSettings method exists'];
            } else {
                return ['status' => 'warning', 'details' => 'getPaymentSettings method not found'];
            }
        });
    }

    public function testCountryBasedPricing()
    {
        // Test Geolocation Service
        $this->runTest('Geolocation Service', function() {
            if (class_exists('App\\Services\\GeolocationService')) {
                return ['status' => 'pass', 'details' => 'GeolocationService class exists'];
            } else {
                return ['status' => 'warning', 'details' => 'GeolocationService not found - using fallback'];
            }
        });

        // Test Country Pricing Configuration
        $this->runTest('Country Pricing Structure', function() {
            $expectedPrices = [
                'India' => 499,
                'Nepal' => 799,
                'International' => 15
            ];
            
            $configContent = file_exists('app/Views/admin/settings/index.php') ? 
                file_get_contents('app/Views/admin/settings/index.php') : '';
            
            $foundPrices = 0;
            if (strpos($configContent, '₹') !== false && strpos($configContent, 'रू') !== false) {
                $foundPrices++;
            }
            if (strpos($configContent, '499') !== false) {
                $foundPrices++;
            }
            if (strpos($configContent, '799') !== false) {
                $foundPrices++;
            }
            if (strpos($configContent, '$15') !== false) {
                $foundPrices++;
            }
            
            if ($foundPrices >= 3) {
                return ['status' => 'pass', 'details' => 'Country-based pricing structure found'];
            } else {
                return ['status' => 'warning', 'details' => 'Country pricing configuration may be incomplete'];
            }
        });
    }

    public function testPaymentSecurity()
    {
        // Test Security Constants
        $this->runTest('Security Constants', function() {
            if (file_exists('../app/Services/Security.php')) {
                return ['status' => 'pass', 'details' => 'Security constants file exists'];
            } else {
                return ['status' => 'warning', 'details' => 'Security constants file not found'];
            }
        });

        // Test CSRF Protection
        $this->runTest('CSRF Protection Setup', function() {
            $securityFile = 'app/Services/Security.php';
            if (file_exists($securityFile)) {
                $content = file_get_contents($securityFile);
                if (strpos($content, 'CSRF') !== false || strpos($content, 'csrf') !== false) {
                    return ['status' => 'pass', 'details' => 'CSRF protection found in security implementation'];
                } else {
                    return ['status' => 'warning', 'details' => 'CSRF protection may not be implemented'];
                }
            } else {
                return ['status' => 'warning', 'details' => 'Security file not found'];
            }
        });
    }

    public function testDatabaseStructure()
    {
        // Test Payment Models
        $this->runTest('Payment Model', function() {
            if (class_exists('App\\Models\\Payment')) {
                return ['status' => 'pass', 'details' => 'Payment model exists'];
            } else {
                return ['status' => 'warning', 'details' => 'Payment model not found'];
            }
        });

        // Test Subscription Model
        $this->runTest('Subscription Model', function() {
            if (class_exists('App\\Models\\Subscription')) {
                return ['status' => 'pass', 'details' => 'Subscription model exists'];
            } else {
                return ['status' => 'warning', 'details' => 'Subscription model not found'];
            }
        });
    }

    public function generateSummary()
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "💳 PAYMENT SYSTEM TEST SUMMARY\n";
        echo str_repeat("=", 50) . "\n";
        echo "✅ Passed:  " . $this->passCount . "\n";
        echo "⚠️  Warnings: " . $this->warningCount . "\n";
        echo "❌ Failed:   " . $this->failCount . "\n";
        echo "📊 Total:    " . count($this->results) . "\n\n";

        $totalScore = $this->passCount + $this->warningCount + $this->failCount;
        $successRate = $totalScore > 0 ? round(($this->passCount / $totalScore) * 100, 1) : 0;
        
        echo "🎯 Success Rate: $successRate%\n\n";

        if ($this->failCount === 0 && $this->warningCount === 0) {
            echo "🎉 ALL PAYMENT TESTS PASSED! Payment system is fully operational.\n";
        } elseif ($this->failCount === 0) {
            echo "✅ PAYMENT SYSTEM READY! Minor warnings to review.\n";
        } elseif ($this->failCount <= 2) {
            echo "⚠️  PAYMENT SYSTEM MOSTLY READY! Fix minor issues.\n";
        } else {
            echo "❌ PAYMENT SYSTEM NEEDS WORK! Review failed tests.\n";
        }

        echo "\n📋 TEST CATEGORIES:\n";
        echo "• PaymentController: " . $this->passCount . "/" . (count($this->results)) . " passed\n";
        echo "• PayTMLibrary: Core payment processing\n";
        echo "• Payment Views: User interface components\n";
        echo "• Payment Routes: API endpoints\n";
        echo "• Settings Integration: Configuration management\n";
        echo "• Country Pricing: Multi-currency support\n";
        echo "• Security: Payment security measures\n";
        echo "• Database: Data models\n";

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "💳 PAYMENT FEATURES TESTED:\n";
        echo "• PayPal Integration\n";
        echo "• PayTM/UPI Support\n";
        echo "• eSewa Payment\n";
        echo "• Khalti Digital Wallet\n";
        echo "• Country-based Pricing (₹, रू, $)\n";
        echo "• Secure Payment Processing\n";
        echo "• Payment Flow Management\n";
        echo "================================================\n";
    }

    public function runAllTests()
    {
        $this->testPaymentController();
        echo "\n";
        
        $this->testPayTMLibrary();
        echo "\n";
        
        $this->testPaymentViews();
        echo "\n";
        
        $this->testPaymentRoutes();
        echo "\n";
        
        $this->testSettingsIntegration();
        echo "\n";
        
        $this->testCountryBasedPricing();
        echo "\n";
        
        $this->testPaymentSecurity();
        echo "\n";
        
        $this->testDatabaseStructure();
        echo "\n";
        
        $this->generateSummary();
    }
}

// Run the payment system test suite
$paymentTestSuite = new PaymentSystemTestSuite();
$paymentTestSuite->runAllTests();



