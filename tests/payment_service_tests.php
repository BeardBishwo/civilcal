<?php
/**
 * Payment Gateway Service Tests
 *
 * Tests for payment gateway service classes
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load minimal bootstrap for env function
if (file_exists(__DIR__ . '/../app/Helpers/functions.php')) {
    require_once __DIR__ . '/../app/Helpers/functions.php';
}

// Load .env file manually if it exists
if (file_exists(__DIR__ . '/../.env')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    } catch (\Exception $e) {
        // Silently fail if dotenv cannot be loaded
    }
}

class PaymentServiceTests
{
    private $results = [];
    private $errors = [];

    public function __construct()
    {
        echo "🔧 Payment Gateway Service Tests\n";
        echo "=================================\n\n";
    }

    /**
     * Run all service tests
     */
    public function runAllTests()
    {
        $this->testServiceClasses();
        $this->testServiceMethods();
        $this->testServiceDependencies();
        $this->testErrorHandling();

        $this->displayResults();
    }

    /**
     * Test service class loading
     */
    private function testServiceClasses()
    {
        echo "1. Testing Service Class Loading...\n";

        $services = [
            'App\Services\Gateways\StripeService',
            'App\Services\Gateways\PayPalService',
            'App\Services\Gateways\MollieService',
            'App\Services\Gateways\PayStackService',
            'App\Services\Gateways\BankTransferService',
            'App\Services\PayPalService',
            'App\Services\StripeService',
        ];

        foreach ($services as $service) {
            try {
                if (class_exists($service)) {
                    $this->results[] = "✅ {$service} loaded successfully";

                    // Test instantiation
                    try {
                        $instance = new $service();
                        $this->results[] = "✅ {$service} instantiated successfully";
                    } catch (Exception $e) {
                        $this->errors[] = "❌ {$service} instantiation failed: " . $e->getMessage();
                    }
                } else {
                    $this->errors[] = "❌ {$service} not found";
                }
            } catch (Exception $e) {
                $this->errors[] = "❌ {$service} error: " . $e->getMessage();
            }
        }

        echo "\n";
    }

    /**
     * Test service methods
     */
    private function testServiceMethods()
    {
        echo "2. Testing Service Methods...\n";

        // Test Stripe Service
        try {
            $stripeService = new \App\Services\Gateways\StripeService();
            $reflection = new ReflectionClass($stripeService);

            $expectedMethods = ['getConfig', 'checkout', 'webhook', 'refund'];
            foreach ($expectedMethods as $method) {
                if ($reflection->hasMethod($method)) {
                    $this->results[] = "✅ StripeService::{$method}() exists";
                } else {
                    $this->errors[] = "❌ StripeService::{$method}() missing";
                }
            }
        } catch (Exception $e) {
            $this->errors[] = "❌ StripeService method test failed: " . $e->getMessage();
        }

        // Test PayPal Service
        try {
            $paypalService = new \App\Services\Gateways\PayPalService();
            $reflection = new ReflectionClass($paypalService);

            $expectedMethods = ['getContext', 'checkout', 'webhook', 'refund'];
            foreach ($expectedMethods as $method) {
                if ($reflection->hasMethod($method)) {
                    $this->results[] = "✅ PayPalService::{$method}() exists";
                } else {
                    $this->errors[] = "❌ PayPalService::{$method}() missing";
                }
            }
        } catch (Exception $e) {
            $this->errors[] = "❌ PayPalService method test failed: " . $e->getMessage();
        }

        echo "\n";
    }

    /**
     * Test service dependencies
     */
    private function testServiceDependencies()
    {
        echo "3. Testing Service Dependencies...\n";

        $dependencies = [
            'App\Models\User' => 'User model',
            'App\Models\Payment' => 'Payment model',
            'App\Models\Subscription' => 'Subscription model',
            'App\Services\SettingsService' => 'Settings service',
            'App\Core\Database' => 'Database core',
        ];

        foreach ($dependencies as $class => $name) {
            try {
                if (class_exists($class)) {
                    $this->results[] = "✅ {$name} dependency available";
                } else {
                    $this->errors[] = "❌ {$name} dependency missing";
                }
            } catch (Exception $e) {
                $this->errors[] = "❌ {$name} dependency error: " . $e->getMessage();
            }
        }

        echo "\n";
    }

    /**
     * Test error handling
     */
    private function testErrorHandling()
    {
        echo "4. Testing Error Handling...\n";

        // Test Stripe service with invalid config
        try {
            $stripeService = new \App\Services\Gateways\StripeService();

            // This should throw an exception with invalid config
            try {
                $result = $stripeService->checkout([], 'invalid_plan', 'monthly');
                $this->errors[] = "❌ StripeService should throw exception for invalid config";
            } catch (Exception $e) {
                $this->results[] = "✅ StripeService properly handles invalid config: " . $e->getMessage();
            }
        } catch (Exception $e) {
            $this->errors[] = "❌ StripeService error handling test failed: " . $e->getMessage();
        }

        // Test PayPal service with invalid config
        try {
            $paypalService = new \App\Services\Gateways\PayPalService();

            try {
                $result = $paypalService->checkout([], 'invalid_plan', 'monthly');
                $this->errors[] = "❌ PayPalService should throw exception for invalid config";
            } catch (Exception $e) {
                $this->results[] = "✅ PayPalService properly handles invalid config: " . $e->getMessage();
            }
        } catch (Exception $e) {
            $this->errors[] = "❌ PayPalService error handling test failed: " . $e->getMessage();
        }

        echo "\n";
    }

    /**
     * Display test results
     */
    private function displayResults()
    {
        echo "📊 Service Test Results Summary\n";
        echo "===============================\n\n";

        echo "✅ PASSED (" . count($this->results) . "):\n";
        foreach ($this->results as $result) {
            echo "   {$result}\n";
        }

        echo "\n";

        if (!empty($this->errors)) {
            echo "❌ FAILED (" . count($this->errors) . "):\n";
            foreach ($this->errors as $error) {
                echo "   {$error}\n";
            }
        } else {
            echo "🎉 All service tests passed! Payment services are properly implemented.\n";
        }

        echo "\n📝 Service Testing Recommendations:\n";
        echo "   1. Test with real payment gateway sandboxes\n";
        echo "   2. Implement comprehensive webhook testing\n";
        echo "   3. Test subscription lifecycle management\n";
        echo "   4. Validate refund and cancellation flows\n";
        echo "   5. Test multi-currency support\n";
    }
}

// Run the service tests
$serviceTests = new PaymentServiceTests();
$serviceTests->runAllTests();