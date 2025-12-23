<?php
/**
 * Create Database Tables
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=bishwo_calculator', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to database\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

$sql = "
CREATE TABLE IF NOT EXISTS `calculators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calculator_id` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `subcategory` varchar(50) DEFAULT NULL,
  `version` varchar(20) DEFAULT '1.0',
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `is_active` tinyint(1) DEFAULT 1,
  `config_json` json DEFAULT NULL,
  `formula` text DEFAULT NULL,
  `inputs` json DEFAULT NULL,
  `outputs` json DEFAULT NULL,
  `usage_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `calculator_id` (`calculator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `calculator_inputs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calculator_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_type` varchar(50) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `min_value` float DEFAULT NULL,
  `max_value` float DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `help_text` text DEFAULT NULL,
  `validation_pattern` varchar(255) DEFAULT NULL,
  `options_json` json DEFAULT NULL,
  `order_index` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `calculator_id` (`calculator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `calculator_outputs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calculator_id` int(11) NOT NULL,
  `output_name` varchar(100) NOT NULL,
  `output_label` varchar(255) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `output_type` varchar(50) DEFAULT 'number',
  `precision` int(11) DEFAULT 2,
  `is_visible` tinyint(1) DEFAULT 1,
  `order_index` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `calculator_id` (`calculator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `calculator_formulas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calculator_id` int(11) NOT NULL,
  `result_name` varchar(100) NOT NULL,
  `formula` text NOT NULL,
  `formula_type` varchar(50) DEFAULT 'expression',
  `order_index` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `calculator_id` (`calculator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `calculator_workflows` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text,
    `steps_json` json NOT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    $pdo->exec($sql);
    echo "✅ Tables created successfully!\n";
} catch (PDOException $e) {
    echo "❌ Execution failed: " . $e->getMessage() . "\n";
}
