<?php
namespace App\Models;

use App\Core\Model;

class Settings extends Model 
{
    protected $table = 'settings';
    
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        // General Settings
        'site_name',
        'site_url', 
        'site_description',
        'admin_email',
        'timezone',
        'date_format',
        'items_per_page',
        
        // Email Settings
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'from_name',
        'from_email',
        
        // Security Settings
        'login_attempts',
        'lockout_time',
        'password_min_length',
        'require_strong_password',
        'enable_2fa',
        
        // PayPal Configuration
        'paypal_email',
        'paypal_sandbox',
        
        // PayTM/UPI Configuration (India)
        'paytm_merchant_id',
        'paytm_merchant_key',
        'paytm_website',
        'paytm_industry_type',
        
        // eSewa Configuration (Nepal)
        'esewa_merchant_code',
        'esewa_secret_key',
        
        // Khalti Configuration (Nepal) 
        'khalti_public_key',
        'khalti_secret_key',
        
        // Country-Specific Pricing
        'pricing_usd',
        'pricing_india',
        'pricing_nepal'
    ];
    
    /**
     * Get a setting value by key
     */
    public function get($key, $default = null)
    {
        $setting = $this->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
    
    /**
     * Set a setting value
     */
    public function set($key, $value)
    {
        $setting = $this->where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            $this->create(['key' => $key, 'value' => $value]);
        }
        
        return true;
    }
    
    /**
     * Get all settings as key-value array
     */
    public function getAll()
    {
        $settings = $this->all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        
        return $result;
    }
    
    /**
     * Get payment gateway settings
     */
    public function getPaymentSettings()
    {
        return [
            'paypal' => [
                'email' => $this->get('paypal_email'),
                'sandbox' => $this->get('paypal_sandbox', false)
            ],
            'paytm' => [
                'merchant_id' => $this->get('paytm_merchant_id'),
                'merchant_key' => $this->get('paytm_merchant_key'),
                'website' => $this->get('paytm_website', 'WEBSTAGING'),
                'industry_type' => $this->get('paytm_industry_type', 'Retail')
            ],
            'esewa' => [
                'merchant_code' => $this->get('esewa_merchant_code'),
                'secret_key' => $this->get('esewa_secret_key')
            ],
            'khalti' => [
                'public_key' => $this->get('khalti_public_key'),
                'secret_key' => $this->get('khalti_secret_key')
            ],
            'pricing' => [
                'usd' => $this->get('pricing_usd', 15),
                'india' => $this->get('pricing_india', 499),
                'nepal' => $this->get('pricing_nepal', 799)
            ]
        ];
    }
}
?>
