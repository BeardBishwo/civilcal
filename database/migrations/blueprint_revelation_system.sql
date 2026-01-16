-- Blueprint System Database Schema
-- Real blueprint revelation system with SVG layers and educational content

-- Create blueprints table
CREATE TABLE IF NOT EXISTS blueprints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category ENUM('structural', 'electrical', 'civil', 'mechanical', 'architectural') NOT NULL,

    -- Difficulty and progression
    difficulty_level INT DEFAULT 1,
    prerequisite_blueprint_id INT NULL,
    estimated_completion_time INT DEFAULT 10, -- minutes

    -- Blueprint content
    full_svg_content LONGTEXT NOT NULL, -- Complete SVG blueprint
    layer_definitions JSON NOT NULL, -- Layer IDs and reveal order
    preview_image VARCHAR(255),

    -- Educational content
    learning_objectives JSON, -- What users should learn
    key_terms JSON, -- Important terminology covered
    syllabus_topic_ids JSON, -- Connected syllabus topics

    -- Game mechanics
    total_sections INT DEFAULT 5,
    base_reward_coins INT DEFAULT 50,
    hint_penalty_coins INT DEFAULT 5,

    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_premium BOOLEAN DEFAULT FALSE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (prerequisite_blueprint_id) REFERENCES blueprints(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),

    INDEX idx_category (category),
    INDEX idx_difficulty (difficulty_level),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create blueprint_sections table for individual revealable sections
CREATE TABLE IF NOT EXISTS blueprint_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blueprint_id INT NOT NULL,
    section_order INT NOT NULL,
    section_name VARCHAR(100) NOT NULL,
    svg_layer_ids JSON NOT NULL, -- Which SVG layers this section controls
    required_terms JSON NOT NULL, -- Terms that must be matched to unlock
    hint_text TEXT,
    explanation_text TEXT, -- Educational explanation when revealed

    FOREIGN KEY (blueprint_id) REFERENCES blueprints(id) ON DELETE CASCADE,
    UNIQUE KEY unique_section_order (blueprint_id, section_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Update blueprint_reveals table to track section-level progress
ALTER TABLE blueprint_reveals
ADD COLUMN revealed_sections JSON DEFAULT ('[]'),
ADD COLUMN completed_at TIMESTAMP NULL,
ADD COLUMN total_attempts INT DEFAULT 0,
ADD COLUMN best_score INT DEFAULT 0,
ADD COLUMN hints_used INT DEFAULT 0;

-- Create blueprint_attempts table for detailed tracking
CREATE TABLE IF NOT EXISTS blueprint_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    blueprint_id INT NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    sections_revealed INT DEFAULT 0,
    correct_matches INT DEFAULT 0,
    total_matches INT DEFAULT 0,
    hints_used INT DEFAULT 0,
    time_taken_seconds INT DEFAULT 0,
    final_score INT DEFAULT 0,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (blueprint_id) REFERENCES blueprints(id) ON DELETE CASCADE,

    INDEX idx_user_blueprint (user_id, blueprint_id),
    INDEX idx_started (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample blueprint data
INSERT INTO blueprints (
    slug, title, description, category, difficulty_level,
    full_svg_content, layer_definitions, total_sections, base_reward_coins
) VALUES (
    'structural-beam-layout',
    'Structural Beam Layout',
    'Master the fundamental components of structural beam systems including load distribution, support conditions, and reinforcement patterns.',
    'structural',
    1,
    '<svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg"><!-- Full blueprint SVG content --></svg>',
    '["foundation", "beams", "columns", "reinforcement", "dimensions"]',
    5,
    50
), (
    'reinforced-concrete-slab',
    'Reinforced Concrete Slab Design',
    'Learn advanced reinforced concrete slab design including rebar spacing, concrete cover, and load calculations.',
    'civil',
    2,
    '<svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg"><!-- Full slab blueprint --></svg>',
    '["concrete", "reinforcement", "formwork", "loads", "details"]',
    5,
    100
);

-- Insert sample sections for first blueprint
INSERT INTO blueprint_sections (
    blueprint_id, section_order, section_name, svg_layer_ids,
    required_terms, hint_text, explanation_text
) VALUES
(1, 1, 'Foundation Layout', '["foundation"]',
 '["footing", "foundation", "bearing"]',
 'Look for the base support elements',
 'Foundation footings provide the bearing surface for structural loads and must be designed for soil bearing capacity.'),

(1, 2, 'Column Placement', '["columns"]',
 '["column", "axial", "compression"]',
 'Vertical load-bearing members',
 'Columns transfer loads from beams to foundations and are designed for axial compression and buckling resistance.'),

(1, 3, 'Beam Configuration', '["beams"]',
 '["beam", "bending", "moment"]',
 'Horizontal load-carrying members',
 'Beams span between supports and resist bending moments from applied loads.'),

(1, 4, 'Reinforcement Details', '["reinforcement"]',
 '["rebar", "reinforcement", "concrete"]',
 'Steel reinforcement within concrete',
 'Reinforcement provides tensile strength that concrete lacks, working together in composite action.'),

(1, 5, 'Dimensions & Annotations', '["dimensions"]',
 '["dimension", "scale", "annotation"]',
 'Measurements and technical notes',
 'Proper dimensioning ensures accurate construction and compliance with engineering specifications.');</content>
<parameter name="filePath">c:\laragon\www\Bishwo_Calculator\database\migrations\blueprint_revelation_system.sql