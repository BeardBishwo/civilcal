-- Calculator Management System Database Schema
-- This schema supports admin-configurable calculators with zero hardcoding

-- Main calculators table
CREATE TABLE IF NOT EXISTS `calculators` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `calculator_id` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Slug: concrete-volume, rebar-calculation',
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `category` VARCHAR(50) NOT NULL COMMENT 'civil, electrical, plumbing, etc.',
  `subcategory` VARCHAR(50) DEFAULT NULL,
  `version` VARCHAR(20) DEFAULT '1.0',
  `icon` VARCHAR(100) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `is_premium` TINYINT(1) DEFAULT 0,
  `order_index` INT DEFAULT 0,
  `config_json` LONGTEXT NOT NULL COMMENT 'Full calculator configuration as JSON',
  `created_by` INT DEFAULT NULL,
  `updated_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_category` (`category`),
  INDEX `idx_active` (`is_active`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Calculator inputs schema
CREATE TABLE IF NOT EXISTS `calculator_inputs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `calculator_id` INT NOT NULL,
  `field_name` VARCHAR(100) NOT NULL,
  `field_label` VARCHAR(255) NOT NULL,
  `field_type` ENUM('number', 'integer', 'string', 'boolean', 'select', 'text', 'date') DEFAULT 'number',
  `unit` VARCHAR(20) DEFAULT NULL,
  `unit_type` ENUM('length', 'area', 'volume', 'weight', 'temperature', 'pressure', 'time') DEFAULT NULL,
  `is_required` TINYINT(1) DEFAULT 1,
  `min_value` DECIMAL(20,6) DEFAULT NULL,
  `max_value` DECIMAL(20,6) DEFAULT NULL,
  `default_value` VARCHAR(255) DEFAULT NULL,
  `placeholder` VARCHAR(255) DEFAULT NULL,
  `help_text` TEXT,
  `validation_pattern` VARCHAR(255) DEFAULT NULL,
  `options_json` TEXT COMMENT 'For select/radio fields',
  `order_index` INT DEFAULT 0,
  FOREIGN KEY (`calculator_id`) REFERENCES `calculators`(`id`) ON DELETE CASCADE,
  INDEX `idx_calculator` (`calculator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Calculator outputs schema
CREATE TABLE IF NOT EXISTS `calculator_outputs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `calculator_id` INT NOT NULL,
  `output_name` VARCHAR(100) NOT NULL,
  `output_label` VARCHAR(255) NOT NULL,
  `unit` VARCHAR(20) DEFAULT NULL,
  `output_type` ENUM('number', 'currency', 'percentage', 'integer', 'scientific') DEFAULT 'number',
  `precision` INT DEFAULT 2,
  `is_visible` TINYINT(1) DEFAULT 1,
  `order_index` INT DEFAULT 0,
  FOREIGN KEY (`calculator_id`) REFERENCES `calculators`(`id`) ON DELETE CASCADE,
  INDEX `idx_calculator` (`calculator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Calculator formulas
CREATE TABLE IF NOT EXISTS `calculator_formulas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `calculator_id` INT NOT NULL,
  `result_name` VARCHAR(100) NOT NULL,
  `formula` TEXT NOT NULL COMMENT 'Mathematical expression or function name',
  `formula_type` ENUM('expression', 'function', 'lookup', 'api') DEFAULT 'expression',
  `description` TEXT,
  `dependencies` TEXT COMMENT 'Comma-separated list of required inputs/results',
  `order_index` INT DEFAULT 0,
  FOREIGN KEY (`calculator_id`) REFERENCES `calculators`(`id`) ON DELETE CASCADE,
  INDEX `idx_calculator` (`calculator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Calculator versions (for formula tracking)
CREATE TABLE IF NOT EXISTS `calculator_versions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `calculator_id` INT NOT NULL,
  `version_number` VARCHAR(20) NOT NULL,
  `config_snapshot` LONGTEXT NOT NULL,
  `change_notes` TEXT,
  `is_active` TINYINT(1) DEFAULT 0,
  `created_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`calculator_id`) REFERENCES `calculators`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `unique_version` (`calculator_id`, `version_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Calculator connections (for data flow between calculators)
CREATE TABLE IF NOT EXISTS `calculator_connections` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `source_calculator_id` INT NOT NULL,
  `target_calculator_id` INT NOT NULL,
  `mapping_json` TEXT NOT NULL COMMENT 'JSON mapping of source outputs to target inputs',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`source_calculator_id`) REFERENCES `calculators`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`target_calculator_id`) REFERENCES `calculators`(`id`) ON DELETE CASCADE,
  INDEX `idx_source` (`source_calculator_id`),
  INDEX `idx_target` (`target_calculator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Calculator workflows (combine multiple calculators)
CREATE TABLE IF NOT EXISTS `calculator_workflows` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `category` VARCHAR(50),
  `workflow_json` LONGTEXT NOT NULL COMMENT 'Workflow definition with calculator sequence',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User calculation history (already exists, adding index)
ALTER TABLE `calculation_history` ADD INDEX IF NOT EXISTS `idx_calculator_type` (`calculator_type`);
ALTER TABLE `calculation_history` ADD INDEX IF NOT EXISTS `idx_user_date` (`user_id`, `calculated_at`);
