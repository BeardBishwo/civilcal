<?php
/**
 * PayTM Payment Gateway Library
 * Handles PayTM payment processing, signature generation, and verification
 * 
 * @package Bishwo_Calculator
 * @version 1.0.0
 */

class PayTMLibrary
{
    private $merchant_id;
    private $merchant_key;
    private $website;
    private $industry_type;
    private $channel_id;
    private $sandbox_mode;
    private $paytm_url;
    
    public function __construct($config = [])
    {
        $this->merchant_id = $config['merchant_id'] ?? '';
        $this->merchant_key = $config['merchant_key'] ?? '';
        $this->website = $config['website'] ?? 'WEBSTAGING';
        $this->industry_type = $config['industry_type'] ?? 'Retail';
        $this->channel_id = $config['channel_id'] ?? 'WEB';
        $this->sandbox_mode = $config['sandbox_mode'] ?? true;
        $this->paytm_url = $this->sandbox_mode ? 
            'https://securegw-stage.paytm.in' : 
            'https://securegw.paytm.in';
    }
    
    /**
     * Generate PayTM checksum hash
     * 
     * @param array $param_list Array of parameters
     * @return string Generated checksum
     */
    public function generateSignature($param_list)
    {
        if (empty($this->merchant_key)) {
            throw new Exception('Merchant key is required');
        }
        
        $param_list = $this->filterParams($param_list);
        ksort($param_list);
        
        $param_list['CHECKSUMHASH'] = $this->getChecksumFromArray($param_list, $this->merchant_key);
        return $param_list;
    }
    
    /**
     * Verify PayTM checksum
     * 
     * @param array $param_list Array of parameters
     * @param string $merchant_key Merchant key
     * @return bool Verification status
     */
    public function verifySignature($param_list, $merchant_key = null)
    {
        $merchant_key = $merchant_key ?? $this->merchant_key;
        
        if (empty($merchant_key)) {
            throw new Exception('Merchant key is required');
        }
        
        if (empty($param_list['CHECKSUMHASH'])) {
            return false;
        }
        
        $return_array = $this->filterParams($param_list);
        unset($return_array['CHECKSUMHASH']);
        
        $checksum = $this->getChecksumFromArray($return_array, $merchant_key);
        return $param_list['CHECKSUMHASH'] === $checksum;
    }
    
    /**
     * Create PayTM transaction form
     * 
     * @param array $param_list Transaction parameters
     * @return string HTML form
     */
    public function createTransactionForm($param_list)
    {
        $form_data = $this->generateSignature($param_list);
        
        $html = '<form id="paytm_form" method="post" action="' . $this->paytm_url . '/order/submitForm" target="_blank">' . "\n";
        foreach ($form_data as $key => $value) {
            if (!empty($value)) {
                $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . "\n";
            }
        }
        $html .= '<input type="submit" value="Proceed to PayTM" style="display:none;">' . "\n";
        $html .= '</form>' . "\n";
        
        return $html;
    }
    
    /**
     * Get PayTM transaction status
     * 
     * @param string $order_id Order ID
     * @param string $txn_id Transaction ID
     * @return array Transaction status
     */
    public function getTransactionStatus($order_id, $txn_id = null)
    {
        $api_endpoint = $this->paytm_url . '/theia/api/v1/OrderStatus';
        
        $data = [
            'mid' => $this->merchant_id,
            'orderId' => $order_id
        ];
        
        if ($txn_id) {
            $data['txnid'] = $txn_id;
        }
        
        $checksum = $this->getChecksumFromArray($data, $this->merchant_key);
        $data['CHECKSUMHASH'] = $checksum;
        
        $response = $this->makeApiCall($api_endpoint, $data);
        
        if ($response && $this->verifySignature($response, $this->merchant_key)) {
            return $response;
        }
        
        return ['status' => 'ERROR', 'message' => 'Invalid response or verification failed'];
    }
    
    /**
     * Filter parameters to remove empty values
     * 
     * @param array $param_list Parameters
     * @return array Filtered parameters
     */
    private function filterParams($param_list)
    {
        $filtered = [];
        foreach ($param_list as $key => $value) {
            if (empty($value) && $value !== '0') {
                continue;
            }
            $filtered[$key] = $value;
        }
        return $filtered;
    }
    
    /**
     * Generate checksum from parameters
     * 
     * @param array $param_list Parameters
     * @param string $key Merchant key
     * @return string Checksum hash
     */
    private function getChecksumFromArray($param_list, $key)
    {
        ksort($param_list);
        $hash_sequence = '';
        
        foreach ($param_list as $value) {
            $hash_sequence .= $value;
        }
        
        return strtoupper(hash("sha256", $hash_sequence . $key));
    }
    
    /**
     * Make API call to PayTM
     * 
     * @param string $url API endpoint
     * @param array $data Request data
     * @return array Response
     */
    private function makeApiCall($url, $data)
    {
        $ch = curl_init();
        $data_string = json_encode($data);
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        
        curl_close($ch);
        
        if ($curl_error) {
            error_log("PayTM cURL Error: " . $curl_error);
            return false;
        }
        
        if ($http_code !== 200) {
            error_log("PayTM HTTP Error: " . $http_code);
            return false;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Get PayTM website configuration
     * 
     * @return array PayTM configuration
     */
    public function getConfig()
    {
        return [
            'merchant_id' => $this->merchant_id,
            'merchant_key' => $this->merchant_key,
            'website' => $this->website,
            'industry_type' => $this->industry_type,
            'channel_id' => $this->channel_id,
            'sandbox_mode' => $this->sandbox_mode,
            'paytm_url' => $this->paytm_url
        ];
    }
    
    /**
     * Validate required parameters for PayTM
     * 
     * @param array $param_list Parameters to validate
     * @return bool Validation status
     */
    public function validateRequiredParams($param_list)
    {
        $required_fields = [
            'ORDER_ID',
            'CUST_ID',
            'MOBILE_NO',
            'EMAIL',
            'TXN_AMOUNT'
        ];
        
        foreach ($required_fields as $field) {
            if (empty($param_list[$field])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Format PayTM response
     * 
     * @param array $response Raw PayTM response
     * @return array Formatted response
     */
    public function formatResponse($response)
    {
        if (!$response) {
            return [
                'status' => 'ERROR',
                'message' => 'No response received',
                'success' => false
            ];
        }
        
        $formatted = [
            'status' => $response['RESPCODE'] === '01' ? 'SUCCESS' : 'FAILED',
            'message' => $response['RESPMSG'] ?? 'Unknown error',
            'order_id' => $response['ORDERID'] ?? '',
            'transaction_id' => $response['TXNID'] ?? '',
            'amount' => $response['TXNAMOUNT'] ?? 0,
            'currency' => 'INR',
            'bank_transaction_id' => $response['BANKTXNID'] ?? '',
            'payment_mode' => $response['PAYMENTMODE'] ?? '',
            'gateway_name' => $response['GATEWAYNAME'] ?? 'PayTM',
            'response_data' => $response,
            'success' => $response['RESPCODE'] === '01'
        ];
        
        return $formatted;
    }
    
    /**
     * Generate order ID for PayTM
     * 
     * @param string $prefix Order ID prefix
     * @param int $user_id User ID
     * @return string Generated order ID
     */
    public function generateOrderId($prefix = 'ORDER', $user_id = 0)
    {
        return $prefix . '_' . $user_id . '_' . time();
    }
}
