-- ================================================
-- PayPal Subscription Integration - Database Schema
-- ================================================
-- Created: 2025-12-12
-- Purpose: Support PayPal subscription payments for SaaS platform
-- ================================================

-- Table 1: User Subscriptions
-- Tracks active and historical subscriptions for each user
CREATE TABLE IF NOT EXISTS `user_subscriptions` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `plan_id` INT(11) UNSIGNED NOT NULL,
    `paypal_subscription_id` VARCHAR(255) DEFAULT NULL COMMENT 'PayPal subscription ID',
    `paypal_plan_id` VARCHAR(255) DEFAULT NULL COMMENT 'PayPal billing plan ID',
    
    -- Subscription Status
    `status` ENUM('active', 'cancelled', 'expired', 'suspended', 'pending') NOT NULL DEFAULT 'pending',
    
    -- Billing Information
    `billing_cycle` ENUM('monthly', 'yearly') NOT NULL DEFAULT 'monthly',
    `amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    
    -- Period Tracking
    `current_period_start` DATETIME DEFAULT NULL,
    `current_period_end` DATETIME DEFAULT NULL,
    `next_billing_date` DATETIME DEFAULT NULL,
    
    -- Cancellation
    `cancel_at_period_end` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if subscription will cancel at end of period',
    `cancelled_at` DATETIME DEFAULT NULL,
    `cancellation_reason` TEXT DEFAULT NULL,
    
    -- Trial Period
    `trial_start` DATETIME DEFAULT NULL,
    `trial_end` DATETIME DEFAULT NULL,
    `is_trial` TINYINT(1) NOT NULL DEFAULT 0,
    
    -- Timestamps
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_plan_id` (`plan_id`),
    KEY `idx_paypal_subscription_id` (`paypal_subscription_id`),
    KEY `idx_status` (`status`),
    KEY `idx_next_billing_date` (`next_billing_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User subscription records';

-- Table 2: Payments
-- Records all payment transactions
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `subscription_id` INT(11) UNSIGNED DEFAULT NULL,
    `invoice_id` INT(11) UNSIGNED DEFAULT NULL,
    
    -- PayPal Information
    `paypal_payment_id` VARCHAR(255) DEFAULT NULL COMMENT 'PayPal payment/capture ID',
    `paypal_order_id` VARCHAR(255) DEFAULT NULL COMMENT 'PayPal order ID',
    `paypal_payer_id` VARCHAR(255) DEFAULT NULL COMMENT 'PayPal payer ID',
    
    -- Payment Details
    `amount` DECIMAL(10, 2) NOT NULL,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `payment_method` VARCHAR(50) DEFAULT 'paypal',
    `status` ENUM('pending', 'completed', 'failed', 'refunded', 'partially_refunded') NOT NULL DEFAULT 'pending',
    
    -- Transaction Details
    `transaction_type` ENUM('subscription', 'one_time', 'refund', 'adjustment') NOT NULL DEFAULT 'subscription',
    `description` VARCHAR(255) DEFAULT NULL,
    
    -- Refund Information
    `refunded_amount` DECIMAL(10, 2) DEFAULT 0.00,
    `refunded_at` DATETIME DEFAULT NULL,
    `refund_reason` TEXT DEFAULT NULL,
    
    -- Metadata
    `metadata` TEXT DEFAULT NULL COMMENT 'JSON metadata',
    
    -- Timestamps
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_subscription_id` (`subscription_id`),
    KEY `idx_invoice_id` (`invoice_id`),
    KEY `idx_paypal_payment_id` (`paypal_payment_id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payment transaction records';

-- Table 3: Invoices
-- Stores invoice records for payments
CREATE TABLE IF NOT EXISTS `invoices` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `subscription_id` INT(11) UNSIGNED DEFAULT NULL,
    `payment_id` INT(11) UNSIGNED DEFAULT NULL,
    
    -- Invoice Details
    `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
    `invoice_date` DATE NOT NULL,
    `due_date` DATE DEFAULT NULL,
    
    -- Amounts
    `subtotal` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `tax` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `discount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    
    -- Status
    `status` ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled', 'refunded') NOT NULL DEFAULT 'draft',
    `paid_at` DATETIME DEFAULT NULL,
    
    -- Files
    `pdf_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL to generated PDF invoice',
    
    -- Billing Information
    `billing_name` VARCHAR(255) DEFAULT NULL,
    `billing_email` VARCHAR(255) DEFAULT NULL,
    `billing_address` TEXT DEFAULT NULL,
    
    -- Line Items (JSON)
    `line_items` TEXT DEFAULT NULL COMMENT 'JSON array of invoice line items',
    
    -- Notes
    `notes` TEXT DEFAULT NULL,
    
    -- Timestamps
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_invoice_number` (`invoice_number`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_subscription_id` (`subscription_id`),
    KEY `idx_payment_id` (`payment_id`),
    KEY `idx_status` (`status`),
    KEY `idx_invoice_date` (`invoice_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Invoice records';

-- Table 4: Payment Methods
-- Stores user payment method information
CREATE TABLE IF NOT EXISTS `payment_methods` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    
    -- PayPal Information
    `paypal_payer_id` VARCHAR(255) DEFAULT NULL,
    `paypal_email` VARCHAR(255) DEFAULT NULL,
    
    -- Payment Method Details
    `type` VARCHAR(50) NOT NULL DEFAULT 'paypal' COMMENT 'paypal, credit_card, etc.',
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    
    -- Card Information (if applicable, encrypted)
    `last4` VARCHAR(4) DEFAULT NULL COMMENT 'Last 4 digits of card',
    `brand` VARCHAR(50) DEFAULT NULL COMMENT 'Visa, Mastercard, etc.',
    `exp_month` INT(2) DEFAULT NULL,
    `exp_year` INT(4) DEFAULT NULL,
    
    -- Status
    `status` ENUM('active', 'expired', 'removed') NOT NULL DEFAULT 'active',
    
    -- Timestamps
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_paypal_payer_id` (`paypal_payer_id`),
    KEY `idx_is_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User payment methods';

-- Table 5: Webhook Events
-- Logs all webhook events from PayPal for debugging and auditing
CREATE TABLE IF NOT EXISTS `webhook_events` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_id` VARCHAR(255) NOT NULL UNIQUE COMMENT 'PayPal event ID',
    `event_type` VARCHAR(100) NOT NULL,
    
    -- Event Data
    `resource_type` VARCHAR(100) DEFAULT NULL,
    `resource_id` VARCHAR(255) DEFAULT NULL,
    `payload` LONGTEXT NOT NULL COMMENT 'Full JSON payload',
    
    -- Processing Status
    `processed` TINYINT(1) NOT NULL DEFAULT 0,
    `processed_at` DATETIME DEFAULT NULL,
    `processing_error` TEXT DEFAULT NULL,
    
    -- Related Records
    `user_id` INT(11) UNSIGNED DEFAULT NULL,
    `subscription_id` INT(11) UNSIGNED DEFAULT NULL,
    `payment_id` INT(11) UNSIGNED DEFAULT NULL,
    
    -- Timestamps
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_event_id` (`event_id`),
    KEY `idx_event_type` (`event_type`),
    KEY `idx_processed` (`processed`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='PayPal webhook event log';

-- Table 6: Update existing subscription_plans table
-- Add PayPal-specific fields to existing plans
ALTER TABLE `subscription_plans` 
ADD COLUMN IF NOT EXISTS `paypal_product_id` VARCHAR(255) DEFAULT NULL COMMENT 'PayPal product ID',
ADD COLUMN IF NOT EXISTS `paypal_plan_id_monthly` VARCHAR(255) DEFAULT NULL COMMENT 'PayPal billing plan ID for monthly',
ADD COLUMN IF NOT EXISTS `paypal_plan_id_yearly` VARCHAR(255) DEFAULT NULL COMMENT 'PayPal billing plan ID for yearly',
ADD COLUMN IF NOT EXISTS `trial_days` INT(11) DEFAULT 0 COMMENT 'Number of trial days',
ADD COLUMN IF NOT EXISTS `setup_fee` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'One-time setup fee';

-- Indexes for subscription_plans
ALTER TABLE `subscription_plans`
ADD INDEX IF NOT EXISTS `idx_paypal_product_id` (`paypal_product_id`),
ADD INDEX IF NOT EXISTS `idx_paypal_plan_monthly` (`paypal_plan_id_monthly`),
ADD INDEX IF NOT EXISTS `idx_paypal_plan_yearly` (`paypal_plan_id_yearly`);

-- ================================================
-- Sample Data for Testing
-- ================================================

-- Note: Uncomment these if you want sample data for testing
/*
-- Sample subscription plans (if not already exists)
INSERT INTO `subscription_plans` (`name`, `description`, `price_monthly`, `price_yearly`, `features`, `is_active`, `trial_days`) VALUES
('Free', 'Basic calculator access', 0.00, 0.00, '["5 calculations per day","Basic calculators","Email support"]', 1, 0),
('Professional', 'For individual engineers', 9.99, 99.99, '["Unlimited calculations","All calculators","Priority support","Export features"]', 1, 7),
('Enterprise', 'For teams and companies', 29.99, 299.99, '["Everything in Professional","Team management","API access","Custom calculators","Dedicated support"]', 1, 14)
ON DUPLICATE KEY UPDATE name=name;
*/

-- ================================================
-- Views for Reporting
-- ================================================

-- View: Active Subscriptions Summary
CREATE OR REPLACE VIEW `v_active_subscriptions` AS
SELECT 
    us.id,
    us.user_id,
    u.username,
    u.email,
    sp.name AS plan_name,
    us.billing_cycle,
    us.amount,
    us.currency,
    us.status,
    us.current_period_start,
    us.current_period_end,
    us.next_billing_date,
    us.created_at
FROM user_subscriptions us
INNER JOIN users u ON us.user_id = u.id
INNER JOIN subscription_plans sp ON us.plan_id = sp.id
WHERE us.status = 'active';

-- View: Monthly Revenue Report
CREATE OR REPLACE VIEW `v_monthly_revenue` AS
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') AS month,
    COUNT(*) AS payment_count,
    SUM(amount) AS total_revenue,
    AVG(amount) AS avg_payment,
    currency
FROM payments
WHERE status = 'completed'
GROUP BY DATE_FORMAT(created_at, '%Y-%m'), currency
ORDER BY month DESC;

-- View: Subscription Status Summary
CREATE OR REPLACE VIEW `v_subscription_stats` AS
SELECT 
    status,
    COUNT(*) AS count,
    SUM(amount) AS total_value
FROM user_subscriptions
GROUP BY status;

-- ================================================
-- Stored Procedures (Optional but Recommended)
-- ================================================

DELIMITER $$

-- Procedure: Cancel Subscription
CREATE PROCEDURE IF NOT EXISTS `sp_cancel_subscription`(
    IN p_subscription_id INT,
    IN p_cancel_immediately BOOLEAN,
    IN p_reason TEXT
)
BEGIN
    IF p_cancel_immediately THEN
        UPDATE user_subscriptions 
        SET 
            status = 'cancelled',
            cancelled_at = NOW(),
            cancellation_reason = p_reason,
            updated_at = NOW()
        WHERE id = p_subscription_id;
    ELSE
        UPDATE user_subscriptions 
        SET 
            cancel_at_period_end = 1,
            cancellation_reason = p_reason,
            updated_at = NOW()
        WHERE id = p_subscription_id;
    END IF;
END$$

-- Procedure: Generate Invoice Number
CREATE FUNCTION IF NOT EXISTS `fn_generate_invoice_number`() 
RETURNS VARCHAR(50)
DETERMINISTIC
BEGIN
    DECLARE next_number INT;
    DECLARE invoice_num VARCHAR(50);
    
    SELECT COALESCE(MAX(CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED)), 0) + 1 
    INTO next_number 
    FROM invoices 
    WHERE invoice_number LIKE 'INV-%';
    
    SET invoice_num = CONCAT('INV-', LPAD(next_number, 6, '0'));
    
    RETURN invoice_num;
END$$

DELIMITER ;

-- ================================================
-- Triggers
-- ================================================

DELIMITER $$

-- Trigger: Auto-generate invoice number
CREATE TRIGGER IF NOT EXISTS `trg_invoice_before_insert`
BEFORE INSERT ON `invoices`
FOR EACH ROW
BEGIN
    IF NEW.invoice_number IS NULL OR NEW.invoice_number = '' THEN
        SET NEW.invoice_number = fn_generate_invoice_number();
    END IF;
END$$

DELIMITER ;

-- ================================================
-- Grants (Adjust as needed for your setup)
-- ================================================

-- GRANT SELECT, INSERT, UPDATE ON user_subscriptions TO 'your_app_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE ON payments TO 'your_app_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE ON invoices TO 'your_app_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE ON payment_methods TO 'your_app_user'@'localhost';
-- GRANT SELECT, INSERT ON webhook_events TO 'your_app_user'@'localhost';

-- ================================================
-- End of Schema
-- ================================================

-- Success message
SELECT 'PayPal subscription database schema created successfully!' AS message;
