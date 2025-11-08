<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SettingsService;

/**
 * Payment Controller - Handles PayPal, eSewa, and Khalti payments
 * Supports location-based payment methods for Nepali and international users
 */
class PaymentController extends Controller
{
    private $settingsService;
    
    public function __construct()
    {
        parent::__construct();
        $this->settingsService = new SettingsService();
    }
    
    /**
     * Show payment checkout page
     */
    public function showPaymentOptions()
    {
        $amount = $_GET['amount'] ?? 0;
        $feature = $_GET['feature'] ?? 'premium_feature';
        $userCountry = $this->getUserCountry();
        
        $data = [
            'amount' => $amount,
            'feature' => $feature,
            'user_country' => $userCountry,
            'is_nepali_user' => $userCountry === 'NP',
            'payment_methods' => $this->getAvailablePaymentMethods($userCountry)
        ];
        
        $this->view('payment/checkout', $data);
    }
    
    /**
     * Process PayPal payment
     */
    public function processPayPal()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/payment/checkout');
        }
        
        $amount = $_POST['amount'] ?? 0;
        $feature = $_POST['feature'] ?? 'premium_feature';
        
        $paypalSettings = $this->getPayPalSettings();
        
        if (!$paypalSettings['email']) {
            $_SESSION['flash_error'] = 'PayPal is not configured. Please contact administrator.';
            return $this->redirect('/payment/checkout?amount=' . $amount);
        }
        
        // Create PayPal payment URL
        $paypalUrl = $this->generatePayPalUrl($amount, $feature, $paypalSettings);
        
        // Log payment attempt
        $this->logPaymentAttempt('paypal', $amount, $feature, 'initiated');
        
        $this->redirect($paypalUrl);
    }
    
    /**
     * Process eSewa payment
     */
    public function processEsewa()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/payment/checkout');
        }
        
        $amount = $_POST['amount'] ?? 0;
        $feature = $_POST['feature'] ?? 'premium_feature';
        
        $esewaSettings = $this->getEsewaSettings();
        
        if (!$esewaSettings['merchant_code']) {
            $_SESSION['flash_error'] = 'eSewa is not configured. Please contact administrator.';
            return $this->redirect('/payment/checkout?amount=' . $amount);
        }
        
        $transactionId = 'txn_' . uniqid();
        $signature = $this->generateEsewaSignature($amount, $transactionId, $esewaSettings['merchant_code']);
        
        $data = [
            'amount' => $amount,
            'tax_amount' => 0,
            'total_amount' => $amount,
            'transaction_uuid' => $transactionId,
            'product_code' => $esewaSettings['merchant_code'],
            'product_service_charge' => 0,
            'product_delivery_charge' => 0,
            'success_url' => $this->baseUrl() . '/payment/success?gateway=esewa&txn=' . $transactionId,
            'failure_url' => $this->baseUrl() . '/payment/failed?gateway=esewa',
            'signed_field_names' => 'total_amount,transaction_uuid,product_code',
            'signature' => $signature
        ];
        
        // Log payment attempt
        $this->logPaymentAttempt('esewa', $amount, $feature, 'initiated');
        
        $this->view('payment/esewa-form', $data);
    }
    
    /**
     * Process Khalti payment
     */
    public function processKhalti()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }
        
        $amount = $_POST['amount'] ?? 0;
        $feature = $_POST['feature'] ?? 'premium_feature';
        $token = $_POST['khalti_token'] ?? '';
        
        $khaltiSettings = $this->getKhaltiSettings();
        
        if (!$khaltiSettings['public_key']) {
            return $this->json(['error' => 'Khalti is not configured']);
        }
        
        // Verify payment with Khalti
        $verificationResult = $this->verifyKhaltiPayment($token, $amount, $khaltiSettings);
        
        if ($verificationResult['success']) {
            // Payment successful
            $this->completePayment('khalti', $amount, $feature, $verificationResult['data']);
            return $this->json(['success' => true, 'message' => 'Payment successful']);
        } else {
            return $this->json(['error' => 'Payment verification failed']);
        }
    }
    
    /**
     * Handle payment success
     */
    public function paymentSuccess()
    {
        $gateway = $_GET['gateway'] ?? 'unknown';
        $transactionId = $_GET['txn'] ?? '';
        
        $data = [
            'gateway' => $gateway,
            'transaction_id' => $transactionId,
            'amount' => $_GET['amount'] ?? 0,
            'feature' => $_GET['feature'] ?? ''
        ];
        
        // Mark payment as completed
        $this->completePayment($gateway, $data['amount'], $data['feature'], $data);
        
        $this->view('payment/success', $data);
    }
    
    /**
     * Handle payment failure
     */
    public function paymentFailed()
    {
        $gateway = $_GET['gateway'] ?? 'unknown';
        
        $data = [
            'gateway' => $gateway,
            'error' => $_GET['error'] ?? 'Payment was not completed'
        ];
        
        $this->view('payment/failed', $data);
    }
    
    /**
     * Get user country for payment method selection
     */
    private function getUserCountry()
    {
        // Check for geolocation service
        if (class_exists('App\\Services\\GeolocationService')) {
            try {
                $geoService = new \App\Services\GeolocationService();
                return $geoService->getUserCountry()['country_code'] ?? 'US';
            } catch (\Exception $e) {
                return 'US';
            }
        }
        
        return 'US';
    }
    
    /**
     * Get available payment methods for user
     */
    private function getAvailablePaymentMethods($country)
    {
        $methods = ['paypal']; // PayPal is always available
        
        // Add local payment methods for Nepali users
        if ($country === 'NP') {
            $methods = array_merge($methods, ['esewa', 'khalti']);
        }
        
        return $methods;
    }
    
    /**
     * Get PayPal settings
     */
    private function getPayPalSettings()
    {
        return [
            'email' => $this->settingsService->get('paypal_email'),
            'client_id' => $this->settingsService->get('paypal_client_id'),
            'secret' => $this->settingsService->get('paypal_secret'),
            'sandbox' => $this->settingsService->get('paypal_sandbox', false)
        ];
    }
    
    /**
     * Get eSewa settings
     */
    private function getEsewaSettings()
    {
        return [
            'merchant_code' => $this->settingsService->get('esewa_merchant_code', 'EPAYTEST'),
            'secret_key' => $this->settingsService->get('esewa_secret_key'),
            'sandbox' => $this->settingsService->get('esewa_sandbox', true)
        ];
    }
    
    /**
     * Get Khalti settings
     */
    private function getKhaltiSettings()
    {
        return [
            'public_key' => $this->settingsService->get('khalti_public_key'),
            'secret_key' => $this->settingsService->get('khalti_secret_key'),
            'sandbox' => $this->settingsService->get('khalti_sandbox', true)
        ];
    }
    
    /**
     * Generate PayPal payment URL
     */
    private function generatePayPalUrl($amount, $feature, $settings)
    {
        $baseUrl = $settings['sandbox'] ? 
            'https://www.sandbox.paypal.com/cgi-bin/webscr' : 
            'https://www.paypal.com/cgi-bin/webscr';
            
        $params = [
            'cmd' => '_xclick',
            'business' => $settings['email'],
            'amount' => $amount,
            'currency_code' => 'USD',
            'item_name' => 'Bishwo Calculator - ' . ucfirst($feature),
            'return' => $this->baseUrl() . '/payment/success?gateway=paypal',
            'cancel_return' => $this->baseUrl() . '/payment/failed?gateway=paypal',
            'notify_url' => $this->baseUrl() . '/payment/webhook/paypal'
        ];
        
        return $baseUrl . '?' . http_build_query($params);
    }
    
    /**
     * Generate eSewa signature
     */
    private function generateEsewaSignature($amount, $transactionId, $merchantCode)
    {
        $message = "total_amount=$amount,transaction_uuid=$transactionId,product_code=$merchantCode";
        $secret = $this->getEsewaSettings()['secret_key'];
        
        return base64_encode(hash_hmac('sha256', $message, $secret, true));
    }
    
    /**
     * Verify Khalti payment
     */
    private function verifyKhaltiPayment($token, $amount, $settings)
    {
        $url = $settings['sandbox'] ? 
            'https://khalti.com/api/v2/epayment/verify/' : 
            'https://khalti.com/api/v2/epayment/verify/';
            
        $data = [
            'token' => $token,
            'amount' => $amount * 100 // Convert to paisa
        ];
        
        $headers = [
            'Authorization: Key ' . $settings['secret_key'],
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return ['success' => true, 'data' => $result];
        }
        
        return ['success' => false, 'error' => 'Verification failed'];
    }
    
    /**
     * Log payment attempt
     */
    private function logPaymentAttempt($gateway, $amount, $feature, $status)
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO payment_logs (user_id, gateway, amount, feature, status, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $gateway, $amount, $feature, $status]);
        } catch (\Exception $e) {
            error_log("Payment logging error: " . $e->getMessage());
        }
    }
    
    /**
     * Complete payment and grant access
     */
    private function completePayment($gateway, $amount, $feature, $transactionData)
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        try {
            // Log successful payment
            $this->logPaymentAttempt($gateway, $amount, $feature, 'completed');
            
            // Update user subscription or grant feature access
            if ($userId) {
                $this->grantFeatureAccess($userId, $feature);
            }
            
            // Send confirmation email
            $this->sendPaymentConfirmation($userId, $gateway, $amount, $feature);
            
        } catch (\Exception $e) {
            error_log("Payment completion error: " . $e->getMessage());
        }
    }
    
    /**
     * Grant feature access to user
     */
    private function grantFeatureAccess($userId, $feature)
    {
        // Implementation depends on your feature access system
        // This could update user permissions, subscription status, etc.
    }
    
    /**
     * Send payment confirmation email
     */
    private function sendPaymentConfirmation($userId, $gateway, $amount, $feature)
    {
        // Implementation for sending payment confirmation email
    }
    
    /**
     * Get base URL
     */
    private function baseUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'];
    }
}
?>
