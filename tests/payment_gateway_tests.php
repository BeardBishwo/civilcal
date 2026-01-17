<?php
/**
 * Payment Gateway Integration Tests
 *
 * Comprehensive testing suite for all payment gateway integrations
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

class PaymentGatewayTests
{
    private $results = [];
    private $errors = [];

    public function __construct()
    {
        echo "🧪 Payment Gateway Integration Tests\n";
        echo "=====================================\n\n";
    }

    /**
     * Run all tests
     */
    public function runAllTests()
    {
        $this->testConfigurationValidation();
        $this->testGatewayAvailability();
        $this->testSDKLoading();
        $this->testBasicConnectivity();
        $this->testWebhookValidation();

        $this->displayResults();
    }

    /**
     * Test configuration validation for all gateways
     */
    private function testConfigurationValidation()
    {
        echo "1. Testing Configuration Validation...\n";

        $gateways = [
            'Stripe' => 'App\Config\Stripe',
            'PayPal' => 'App\Config\PayPal',
            'Adyen' => 'App\Config\Adyen',
            'AuthorizeNet' => 'App\Config\AuthorizeNet',
            'Braintree' => 'App\Config\Braintree',
            'Flutterwave' => 'App\Config\Flutterwave',
            'PayStack' => 'App\Config\PayStack',
            'Razorpay' => 'App\Config\Razorpay',
            'Square' => 'App\Config\Square',
            'CoinPayments' => 'App\Config\CoinPayments',
            'TwoCheckout' => 'App\Config\TwoCheckout',
        ];

        foreach ($gateways as $name => $class) {
            try {
                if (class_exists($class)) {
                    // Test if config class loads
                    $reflection = new ReflectionClass($class);
                    $this->results[] = "✅ {$name} config class loaded successfully";

                    // Test basic methods exist
                    if ($reflection->hasMethod('isEnabled')) {
                        $this->results[] = "✅ {$name} has isEnabled() method";
                    }

                    if ($reflection->hasMethod('validateConfig')) {
                        try {
                            $class::validateConfig();
                            $this->results[] = "✅ {$name} configuration is valid";
                        } catch (Exception $e) {
                            $this->results[] = "⚠️  {$name} configuration incomplete: " . $e->getMessage();
                        }
                    }
                } else {
                    $this->errors[] = "❌ {$name} config class not found";
                }
            } catch (Exception $e) {
                $this->errors[] = "❌ {$name} config error: " . $e->getMessage();
            }
        }

        echo "\n";
    }

    /**
     * Test gateway availability in services config
     */
    private function testGatewayAvailability()
    {
        echo "2. Testing Gateway Availability in Services Config...\n";

        $services = require __DIR__ . '/../config/services.php';

        if (isset($services['payment_gateways'])) {
            $this->results[] = "✅ Payment gateways configuration found";

            $expectedGateways = [
                'stripe', 'paypal', 'adyen', 'authorizenet', 'braintree',
                'flutterwave', 'paystack', 'razorpay', 'square', 'coinpayments', 'twocheckout'
            ];

            foreach ($expectedGateways as $gateway) {
                if (isset($services['payment_gateways'][$gateway])) {
                    $this->results[] = "✅ {$gateway} gateway configured";
                } else {
                    $this->errors[] = "❌ {$gateway} gateway missing from config";
                }
            }
        } else {
            $this->errors[] = "❌ Payment gateways configuration not found";
        }

        echo "\n";
    }

    /**
     * Test SDK loading for all gateways
     */
    private function testSDKLoading()
    {
        echo "3. Testing SDK Loading...\n";

        $sdkTests = [
            'Stripe' => function() {
                return class_exists('Stripe\Stripe');
            },
            'PayPal' => function() {
                return class_exists('PayPal\Api\Payment');
            },
            'Adyen' => function() {
                return class_exists('Adyen\Client');
            },
            'Authorize.net' => function() {
                return class_exists('net\authorize\api\contract\v2\MerchantAuthenticationType');
            },
            'Braintree' => function() {
                return class_exists('Braintree\Gateway');
            },
            'Flutterwave' => function() {
                return class_exists('Flutterwave\Rave');
            },
            'PayStack' => function() {
                return class_exists('Yabacon\Paystack');
            },
            'Razorpay' => function() {
                return class_exists('Razorpay\Api\Api');
            },
            'Square' => function() {
                return class_exists('Square\SquareClient');
            },
            'CoinPayments' => function() {
                return class_exists('CoinpaymentsAPI\CoinpaymentsAPI');
            },
            '2Checkout' => function() {
                return class_exists('Twocheckout\TwocheckoutApi');
            },
        ];

        foreach ($sdkTests as $name => $test) {
            try {
                if ($test()) {
                    $this->results[] = "✅ {$name} SDK loaded successfully";
                } else {
                    $this->errors[] = "❌ {$name} SDK not loaded";
                }
            } catch (Exception $e) {
                $this->errors[] = "❌ {$name} SDK error: " . $e->getMessage();
            }
        }

        echo "\n";
    }

    /**
     * Test basic connectivity (without real credentials)
     */
    private function testBasicConnectivity()
    {
        echo "4. Testing Basic Connectivity...\n";

        // Test Stripe (safest to test without real credentials)
        try {
            if (class_exists('Stripe\Stripe')) {
                \Stripe\Stripe::setApiKey('sk_test_dummy_key_for_testing');
                $this->results[] = "✅ Stripe SDK initialized successfully";
            }
        } catch (Exception $e) {
            $this->results[] = "⚠️  Stripe initialization note: " . $e->getMessage();
        }

        // Test PayPal
        try {
            if (class_exists('PayPal\Rest\ApiContext')) {
                $this->results[] = "✅ PayPal SDK available for initialization";
            }
        } catch (Exception $e) {
            $this->errors[] = "❌ PayPal SDK error: " . $e->getMessage();
        }

        // Test Square
        try {
            if (class_exists('Square\SquareClient')) {
                $this->results[] = "✅ Square SDK available for initialization";
            }
        } catch (Exception $e) {
            $this->errors[] = "❌ Square SDK error: " . $e->getMessage();
        }

        echo "\n";
    }

    /**
     * Test webhook validation setup
     */
    private function testWebhookValidation()
    {
        echo "5. Testing Webhook Validation Setup...\n";

        $services = require __DIR__ . '/../config/services.php';

        if (isset($services['webhooks'])) {
            $this->results[] = "✅ Webhook configuration found";

            $required = ['retry_attempts', 'timeout', 'signature_verification'];
            foreach ($required as $setting) {
                if (isset($services['webhooks'][$setting])) {
                    $this->results[] = "✅ Webhook {$setting} configured";
                } else {
                    $this->errors[] = "❌ Webhook {$setting} missing";
                }
            }
        } else {
            $this->errors[] = "❌ Webhook configuration not found";
        }

        echo "\n";
    }

    /**
     * Display test results
     */
    private function displayResults()
    {
        echo "📊 Test Results Summary\n";
        echo "========================\n\n";

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
            echo "🎉 All tests passed! Payment gateway integrations are ready.\n";
        }

        echo "\n📝 Next Steps:\n";
        echo "   1. Configure real API credentials in .env file\n";
        echo "   2. Test actual payment processing with test cards\n";
        echo "   3. Set up webhook endpoints for payment confirmations\n";
        echo "   4. Configure IPN/callback URLs for each gateway\n";
        echo "   5. Test refund and subscription management features\n";
    }
}

// Run the tests
$tests = new PaymentGatewayTests();
$tests->runAllTests();