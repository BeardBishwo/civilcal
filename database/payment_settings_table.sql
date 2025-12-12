-- Payment Gateway Settings Table
-- Stores configuration for all payment providers

CREATE TABLE IF NOT EXISTS `payment_settings` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `gateway_name` VARCHAR(50) NOT NULL UNIQUE COMMENT 'paypal_basic, paypal_api, stripe, mollie, etc.',
    `display_name` VARCHAR(100) NOT NULL,
    `is_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `settings` TEXT DEFAULT NULL COMMENT 'JSON encoded settings',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_gateway` (`gateway_name`),
    KEY `idx_enabled` (`is_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payment gateway configurations';

-- Insert default payment gateways
INSERT INTO `payment_settings` (`gateway_name`, `display_name`, `is_enabled`, `settings`) VALUES
('paypal_basic', 'PayPal Basic Checkout', 0, '{"email":"","ipn_url":""}'),
('paypal_api', 'PayPal API Payments', 0, '{"mode":"sandbox","client_id":"","client_secret":"","currency":"USD"}'),
('stripe', 'Stripe Payments', 0, '{"checkout_type":"built-in","publishable_key":"","secret_key":"","webhook_secret":""}'),
('mollie', 'Mollie Payments', 0, '{"api_key":""}'),
('paddle_billing', 'Paddle Billing', 0, '{"client_token":"","api_key":"","webhook_secret":""}'),
('paddle_classic', 'Paddle Classic', 0, '{"vendor_id":"","api_key":"","public_key":"","monthly_plan_id":"","yearly_plan_id":""}'),
('paystack', 'PayStack Payments', 0, '{"secret_key":"","public_key":""}'),
('bank_transfer', 'Bank Transfer', 0, '{"bank_info":""}')
ON DUPLICATE KEY UPDATE display_name=VALUES(display_name);
