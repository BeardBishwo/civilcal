-- =====================================================
-- CALCULATOR PLATFORM DATABASE SCHEMA
-- =====================================================

-- Calculator definitions
CREATE TABLE IF NOT EXISTS calc_definitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    formula TEXT,
    input_fields JSON,
    output_format VARCHAR(50) DEFAULT 'number',
    icon VARCHAR(50),
    gradient VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Unit categories
CREATE TABLE IF NOT EXISTS calc_unit_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Units for conversion
CREATE TABLE IF NOT EXISTS calc_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(20) NOT NULL,
    to_base_multiplier DECIMAL(30,15) NOT NULL,
    base_unit BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES calc_unit_categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_symbol (symbol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Calculation history
CREATE TABLE IF NOT EXISTS calc_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    calculator_slug VARCHAR(100) NOT NULL,
    inputs JSON NOT NULL,
    result DECIMAL(30,10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_calc (user_id, calculator_slug),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User favorites
CREATE TABLE IF NOT EXISTS calc_favorites (
    user_id INT NOT NULL,
    calculator_slug VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, calculator_slug),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- SEED DATA: Unit Categories
-- =====================================================

INSERT INTO calc_unit_categories (name, slug, icon, display_order) VALUES
('Acceleration', 'acceleration', 'bi-speedometer2', 1),
('Angles', 'angles', 'bi-triangle', 2),
('Area', 'area', 'bi-square', 3),
('Circular Measure', 'circular-measure', 'bi-circle', 4),
('Density', 'density', 'bi-box', 5),
('Energy', 'energy', 'bi-lightning', 6),
('Flow Rate (Volume)', 'flow-rate-volume', 'bi-water', 7),
('Flow Rate (Mass)', 'flow-rate-mass', 'bi-droplet', 8),
('Force', 'force', 'bi-arrow-right', 9),
('Frequency', 'frequency', 'bi-broadcast', 10),
('Fuel Consumption', 'fuel-consumption', 'bi-fuel-pump', 11),
('Length', 'length', 'bi-rulers', 12),
('Lighting', 'lighting', 'bi-lightbulb', 13),
('Liquid Measure', 'liquid-measure', 'bi-cup', 14),
('Mass / Weight', 'mass-weight', 'bi-box-seam', 15),
('Power', 'power', 'bi-plug', 16),
('Pressure', 'pressure', 'bi-speedometer', 17),
('Temperature', 'temperature', 'bi-thermometer', 18),
('Time', 'time', 'bi-clock', 19),
('Torque', 'torque', 'bi-gear', 20),
('Velocity', 'velocity', 'bi-arrow-right-circle', 21),
('Viscosity Dynamic', 'viscosity-dynamic', 'bi-droplet-half', 22),
('Viscosity Kinematic', 'viscosity-kinematic', 'bi-droplet', 23),
('Volume / Capacity', 'volume-capacity', 'bi-box', 24)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- =====================================================
-- SEED DATA: Sample Units (Length category)
-- =====================================================

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
(12, 'Meter', 'm', 1.000000000000000, TRUE, 1),
(12, 'Kilometer', 'km', 1000.000000000000000, FALSE, 2),
(12, 'Centimeter', 'cm', 0.010000000000000, FALSE, 3),
(12, 'Millimeter', 'mm', 0.001000000000000, FALSE, 4),
(12, 'Micrometer', 'µm', 0.000001000000000, FALSE, 5),
(12, 'Nanometer', 'nm', 0.000000001000000, FALSE, 6),
(12, 'Mile', 'mi', 1609.344000000000000, FALSE, 7),
(12, 'Yard', 'yd', 0.914400000000000, FALSE, 8),
(12, 'Foot', 'ft', 0.304800000000000, FALSE, 9),
(12, 'Inch', 'in', 0.025400000000000, FALSE, 10),
(12, 'Nautical Mile', 'nmi', 1852.000000000000000, FALSE, 11)
ON DUPLICATE KEY UPDATE name=VALUES(name);
