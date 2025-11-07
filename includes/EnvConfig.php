<?php
class EnvConfig {
    private static $initialized = false;
    private static $secrets = [];
    
    /**
     * Initialize environment configuration
     */
    public static function init(): void {
        if (self::$initialized) {
            return;
        }
        
        // Load .env file if it exists
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) continue;
                list($name, $value) = explode('=', $line, 2);
                putenv(trim($name) . '=' . trim($value));
            }
        }
        
        // Load secrets from secure storage in production
        if (ENVIRONMENT === 'production') {
            self::loadProductionSecrets();
        }
        
        self::$initialized = true;
        
        // Validate required secrets
        self::validateRequiredSecrets();
    }
    
    /**
     * Load secrets from secure storage in production
     */
    private static function loadProductionSecrets(): void {
        // Example: Load from AWS Secrets Manager
        if (getenv('AWS_SECRETS_ENABLED')) {
            // Implement AWS Secrets Manager loading here
            // For now, just log that we would load from AWS
            error_log('Would load secrets from AWS Secrets Manager');
        }
        
        // Example: Load from Azure Key Vault
        if (getenv('AZURE_KEYVAULT_ENABLED')) {
            // Implement Azure Key Vault loading here
            error_log('Would load secrets from Azure Key Vault');
        }
    }
    
    /**
     * Get a secret value
     */
    public static function getSecret(string $key, $default = null) {
        // Initialize if not done
        if (!self::$initialized) {
            self::init();
        }
        
        // Try secrets first
        if (isset(self::$secrets[$key])) {
            return self::$secrets[$key];
        }
        
        // Then environment
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        return $default;
    }
    
    /**
     * Validate that all required secrets are present
     */
    private static function validateRequiredSecrets(): void {
        $required = [
            'DB_PASS',
            'MAIL_SMTP_PASS',
            'JWT_SECRET',
            'ADMIN_PASS',
            'ENCRYPTION_KEY'
        ];
        
        $missing = [];
        foreach ($required as $key) {
            if (self::getSecret($key) === null) {
                $missing[] = $key;
            }
        }
        
        if (!empty($missing) && ENVIRONMENT === 'production') {
            throw new Exception('Missing required secrets: ' . implode(', ', $missing));
        }
    }
    
    /**
     * Get database credentials
     */
    public static function getDatabaseConfig(): array {
        return [
            'host' => self::getSecret('DB_HOST', '127.0.0.1'),
            'name' => self::getSecret('DB_NAME', 'aec_calculator'),
            'user' => self::getSecret('DB_USER', 'root'),
            'pass' => self::getSecret('DB_PASS', ''),
            'port' => (int)self::getSecret('DB_PORT', '3306')
        ];
    }
    
    /**
     * Get mail configuration
     */
    public static function getMailConfig(): array {
        return [
            'host' => self::getSecret('MAIL_SMTP_HOST'),
            'port' => (int)self::getSecret('MAIL_SMTP_PORT', '587'),
            'user' => self::getSecret('MAIL_SMTP_USER'),
            'pass' => self::getSecret('MAIL_SMTP_PASS'),
            'from' => self::getSecret('MAIL_FROM'),
            'name' => self::getSecret('MAIL_FROM_NAME'),
            'encryption' => self::getSecret('MAIL_SMTP_SECURE', 'tls')
        ];
    }
    
    /**
     * Get PayPal configuration
     */
    public static function getPayPalConfig(): array {
        return [
            'client_id' => self::getSecret('PAYPAL_CLIENT_ID'),
            'secret' => self::getSecret('PAYPAL_SECRET'),
            'mode' => self::getSecret('PAYPAL_MODE', 'sandbox')
        ];
    }
}