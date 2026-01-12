<?php
/**
 * PayPal Service
 * 
 * Handles all PayPal API interactions for subscription management
 */

namespace App\Services;

use App\Config\PayPal as PayPalConfig;
use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Common\PayPalModel;
use PayPal\Exception\PayPalConnectionException;

class PayPalService
{
    /**
     * Create a PayPal Product (Billing Plan)
     * 
     * @param array $planData Plan data from database
     * @param string $billingCycle 'monthly' or 'yearly'
     * @return array ['success' => bool, 'plan_id' => string, 'message' => string]
     */
    public function createBillingPlan($planData, $billingCycle = 'monthly')
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            
            // Determine price and frequency based on billing cycle
            $price = $billingCycle === 'yearly' ? $planData['price_yearly'] : $planData['price_monthly'];
            $frequency = $billingCycle === 'yearly' ? 'YEAR' : 'MONTH';
            $interval = 1;
            
            // Create the billing plan
            $plan = new Plan();
            $plan->setName($planData['name'] . ' - ' . ucfirst($billingCycle))
                ->setDescription($planData['description'])
                ->setType('INFINITE'); // or 'FIXED' for limited duration
            
            // Set up payment definition
            $paymentDefinition = new PaymentDefinition();
            $paymentDefinition->setName('Regular Payment')
                ->setType('REGULAR')
                ->setFrequency($frequency)
                ->setFrequencyInterval((string)$interval)
                ->setCycles('0') // 0 = infinite
                ->setAmount(new Currency([
                    'value' => number_format($price, 2, '.', ''),
                    'currency' => PayPalConfig::getCurrency()
                ]));
            
            // Set up merchant preferences
            $merchantPreferences = new MerchantPreferences();
            $merchantPreferences->setReturnUrl(PayPalConfig::getReturnUrl())
                ->setCancelUrl(PayPalConfig::getCancelUrl())
                ->setAutoBillAmount('YES')
                ->setInitialFailAmountAction('CONTINUE')
                ->setMaxFailAttempts('3');
            
            // Add setup fee if exists
            if (isset($planData['setup_fee']) && $planData['setup_fee'] > 0) {
                $merchantPreferences->setSetupFee(new Currency([
                    'value' => number_format($planData['setup_fee'], 2, '.', ''),
                    'currency' => PayPalConfig::getCurrency()
                ]));
            }
            
            $plan->setPaymentDefinitions([$paymentDefinition]);
            $plan->setMerchantPreferences($merchantPreferences);
            
            // Create the plan
            $createdPlan = $plan->create($apiContext);
            
            // Activate the plan
            $patch = new Patch();
            $patch->setOp('replace')
                ->setPath('/')
                ->setValue(new PayPalModel(json_encode([
                    'state' => 'ACTIVE'
                ])));
            
            $patchRequest = new PatchRequest();
            $patchRequest->addPatch($patch);
            
            $createdPlan->update($patchRequest, $apiContext);
            
            return [
                'success' => true,
                'plan_id' => $createdPlan->getId(),
                'message' => 'Billing plan created successfully',
                'plan' => $createdPlan
            ];
            
        } catch (PayPalConnectionException $e) {
            return [
                'success' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
                'error' => json_decode($e->getData())
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating billing plan: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Create a subscription agreement
     * 
     * @param string $planId PayPal plan ID
     * @param array $userData User data
     * @param \DateTime $startDate When subscription starts
     * @return array ['success' => bool, 'approval_url' => string, 'token' => string]
     */
    public function createSubscription($planId, $userData, $startDate = null)
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            
            // Default start date to tomorrow if not provided
            if ($startDate === null) {
                $startDate = new \DateTime('tomorrow');
            }
            
            // Create agreement
            $agreement = new Agreement();
            $agreement->setName($userData['plan_name'] . ' Subscription')
                ->setDescription('Subscription to ' . $userData['plan_name'])
                ->setStartDate($startDate->format('c'));
            
            // Set the plan
            $plan = new Plan();
            $plan->setId($planId);
            $agreement->setPlan($plan);
            
            // Set payer
            $payer = new \PayPal\Api\Payer();
            $payer->setPaymentMethod('paypal');
            $agreement->setPayer($payer);
            
            // Create the agreement
            $createdAgreement = $agreement->create($apiContext);
            
            // Get approval URL
            $approvalUrl = $createdAgreement->getApprovalLink();
            
            // Extract token from approval URL
            parse_str(parse_url($approvalUrl, PHP_URL_QUERY), $params);
            $token = $params['token'] ?? '';
            
            return [
                'success' => true,
                'approval_url' => $approvalUrl,
                'token' => $token,
                'agreement' => $createdAgreement
            ];
            
        } catch (PayPalConnectionException $e) {
            return [
                'success' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
                'error' => json_decode($e->getData())
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating subscription: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Execute (activate) a subscription agreement after user approval
     * 
     * @param string $token Payment token from return URL
     * @return array ['success' => bool, 'subscription_id' => string]
     */
    public function executeSubscription($token)
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            
            $agreement = new Agreement();
            $result = $agreement->execute($token, $apiContext);
            
            return [
                'success' => true,
                'subscription_id' => $result->getId(),
                'state' => $result->getState(),
                'agreement' => $result
            ];
            
        } catch (PayPalConnectionException $e) {
            return [
                'success' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
                'error' => json_decode($e->getData())
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error executing subscription: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get subscription details
     * 
     * @param string $subscriptionId PayPal subscription ID
     * @return array ['success' => bool, 'subscription' => object]
     */
    public function getSubscription($subscriptionId)
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            
            $agreement = Agreement::get($subscriptionId, $apiContext);
            
            return [
                'success' => true,
                'subscription' => $agreement,
                'state' => $agreement->getState(),
                'start_date' => $agreement->getStartDate(),
                'payer' => $agreement->getPayer()
            ];
            
        } catch (PayPalConnectionException $e) {
            return [
                'success' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
                'error' => json_decode($e->getData())
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error getting subscription: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cancel a subscription
     * 
     * @param string $subscriptionId PayPal subscription ID
     * @param string $note Cancellation note
     * @return array ['success' => bool, 'message' => string]
     */
    public function cancelSubscription($subscriptionId, $note = 'Customer requested cancellation')
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            
            $agreement = Agreement::get($subscriptionId, $apiContext);
            
            $agreementStateDescriptor = new AgreementStateDescriptor();
            $agreementStateDescriptor->setNote($note);
            
            $agreement->cancel($agreementStateDescriptor, $apiContext);
            
            return [
                'success' => true,
                'message' => 'Subscription cancelled successfully'
            ];
            
        } catch (PayPalConnectionException $e) {
            return [
                'success' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
                'error' => json_decode($e->getData())
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error cancelling subscription: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Suspend a subscription
     * 
     * @param string $subscriptionId PayPal subscription ID
     * @param string $note Suspension note
     * @return array ['success' => bool, 'message' => string]
     */
    public function suspendSubscription($subscriptionId, $note = 'Subscription suspended')
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            
            $agreement = Agreement::get($subscriptionId, $apiContext);
            
            $agreementStateDescriptor = new AgreementStateDescriptor();
            $agreementStateDescriptor->setNote($note);
            
            $agreement->suspend($agreementStateDescriptor, $apiContext);
            
            return [
                'success' => true,
                'message' => 'Subscription suspended successfully'
            ];
            
        } catch (PayPalConnectionException $e) {
            return [
                'success' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
                'error' => json_decode($e->getData())
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error suspending subscription: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Reactivate a suspended subscription
     * 
     * @param string $subscriptionId PayPal subscription ID
     * @param string $note Reactivation note
     * @return array ['success' => bool, 'message' => string]
     */
    public function reactivateSubscription($subscriptionId, $note = 'Subscription reactivated')
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            
            $agreement = Agreement::get($subscriptionId, $apiContext);
            
            $agreementStateDescriptor = new AgreementStateDescriptor();
            $agreementStateDescriptor->setNote($note);
            
            $agreement->reActivate($agreementStateDescriptor, $apiContext);
            
            return [
                'success' => true,
                'message' => 'Subscription reactivated successfully'
            ];
            
        } catch (PayPalConnectionException $e) {
            return [
                'success' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
                'error' => json_decode($e->getData())
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error reactivating subscription: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get billing plan details
     * 
     * @param string $planId PayPal plan ID
     * @return array ['success' => bool, 'plan' => object]
     */
    public function getBillingPlan($planId)
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            
            $plan = Plan::get($planId, $apiContext);
            
            return [
                'success' => true,
                'plan' => $plan,
                'state' => $plan->getState(),
                'name' => $plan->getName()
            ];
            
        } catch (PayPalConnectionException $e) {
            return [
                'success' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
                'error' => json_decode($e->getData())
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error getting billing plan: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * List all billing plans
     * 
     * @return array ['success' => bool, 'plans' => array]
     */
    public function listBillingPlans()
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            
            $params = ['page_size' => '20'];
            $planList = Plan::all($params, $apiContext);
            
            return [
                'success' => true,
                'plans' => $planList->getPlans(),
                'total' => count($planList->getPlans())
            ];
            
        } catch (PayPalConnectionException $e) {
            return [
                'success' => false,
                'message' => 'PayPal connection error: ' . $e->getMessage(),
                'error' => json_decode($e->getData())
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error listing billing plans: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Verify webhook signature
     * 
     * @param array $headers Request headers
     * @param string $body Request body
     * @return bool True if signature is valid
     */
    public function verifyWebhookSignature($headers, $body)
    {
        try {
            $apiContext = PayPalConfig::getApiContext();
            $webhookId = PayPalConfig::getWebhookId();
            
            if (empty($webhookId)) {
                error_log('PayPal Webhook ID not configured. Verification REJECTED.');
                return false;
            }

            // Prepare verification request
            $signatureVerification = new \PayPal\Api\VerifyWebhookSignature();
            $signatureVerification->setAuthAlgo($headers['PAYPAL-AUTH-ALGO'] ?? $headers['Paypal-Auth-Algo'] ?? null);
            $signatureVerification->setTransmissionId($headers['PAYPAL-TRANSMISSION-ID'] ?? $headers['Paypal-Transmission-Id'] ?? null);
            $signatureVerification->setCertUrl($headers['PAYPAL-CERT-URL'] ?? $headers['Paypal-Cert-Url'] ?? null);
            $signatureVerification->setTransmissionSig($headers['PAYPAL-TRANSMISSION-SIG'] ?? $headers['Paypal-Transmission-Sig'] ?? null);
            $signatureVerification->setTransmissionTime($headers['PAYPAL-TRANSMISSION-TIME'] ?? $headers['Paypal-Transmission-Time'] ?? null);
            $signatureVerification->setWebhookId($webhookId);
            $signatureVerification->setRequestBody($body);

            $output = $signatureVerification->post($apiContext);
            $verificationStatus = json_decode($output, true);

            // SECURITY: Strictly enforce SUCCESS status. Rejects if PayPal returns anything else.
            return isset($verificationStatus['verification_status']) && $verificationStatus['verification_status'] === 'SUCCESS';
            
        } catch (\Exception $e) {
            error_log('PayPal Webhook Verification Error: ' . $e->getMessage());
            return false;
        }
    }
}
