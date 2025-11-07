<?php

class ComplianceConfig {
    // Feature Flags
    public const GDPR_ENABLED = false;          // Enable GDPR-specific features
    public const CCPA_ENABLED = false;          // Enable CCPA-specific features
    public const SUBSCRIPTION_ENABLED = false;   // Enable subscription/payment features
    
    // Retention Periods (in days)
    public const RETENTION_PERIOD_HISTORY = 365;     // Calculation history
    public const RETENTION_PERIOD_LOGS = 90;         // System logs
    public const RETENTION_PERIOD_CONTACTS = 180;    // Contact form submissions
    
    // Email Settings
    public const PRIVACY_EMAIL = 'privacy@aeccalculator.com';
    public const LEGAL_EMAIL = 'legal@aeccalculator.com';
    
    // Organization Details
    private static $organizationDetails = [
        'name' => 'AEC Calculator',
        'address' => '[Your business address]',
        'jurisdiction' => '[Your jurisdiction]'
    ];
    
    /**
     * Get organization details
     * @param string $key The detail key to retrieve
     * @return string|null The requested detail or null if not found
     */
    public static function getOrgDetail(string $key): ?string {
        return self::$organizationDetails[$key] ?? null;
    }

    /**
     * Set organization details
     * @param array $details Array of organization details to update
     * @return void
     */
    public static function setOrgDetails(array $details): void {
        self::$organizationDetails = array_merge(self::$organizationDetails, $details);
    }

    /**
     * Get all retention periods
     * @return array Associative array of retention periods
     */
    public static function getRetentionPeriods(): array {
        return [
            'history' => self::RETENTION_PERIOD_HISTORY,
            'logs' => self::RETENTION_PERIOD_LOGS,
            'contacts' => self::RETENTION_PERIOD_CONTACTS
        ];
    }
    
    /**
     * Check if a feature is enabled
     * @param string $feature The feature to check
     * @return bool Whether the feature is enabled
     */
    public static function isFeatureEnabled(string $feature): bool {
        $constName = strtoupper($feature) . '_ENABLED';
        return defined("self::$constName") && constant("self::$constName");
    }
    
    /**
     * Get all feature flags
     * @return array Associative array of feature flags and their status
     */
    public static function getFeatureFlags(): array {
        return [
            'gdpr' => self::GDPR_ENABLED,
            'ccpa' => self::CCPA_ENABLED,
            'subscription' => self::SUBSCRIPTION_ENABLED
        ];
    }
}