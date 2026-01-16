<?php
/**
 * Blueprint Revelation System Migration
 * Creates tables for real blueprint progressive revelation with SVG layers
 */

use App\Core\Database;

class BlueprintRevelationSystem
{
    private $pdo;

    public function __construct()
    {
        $db = Database::getInstance();
        $this->pdo = $db->getPdo();
    }

    public function up()
    {
        echo "Creating blueprint revelation system tables...\n";

        // Create blueprints table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS blueprints (
                id INT AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(100) NOT NULL UNIQUE,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                category ENUM('structural', 'electrical', 'civil', 'mechanical', 'architectural') NOT NULL,

                -- Difficulty and progression
                difficulty_level INT DEFAULT 1,
                prerequisite_blueprint_id INT NULL,
                estimated_completion_time INT DEFAULT 10,

                -- Blueprint content
                full_svg_content LONGTEXT NOT NULL,
                layer_definitions JSON NOT NULL,
                preview_image VARCHAR(255),

                -- Educational content
                learning_objectives JSON,
                key_terms JSON,
                syllabus_topic_ids JSON,

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
        ");

        // Create blueprint_sections table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS blueprint_sections (
                id INT AUTO_INCREMENT PRIMARY KEY,
                blueprint_id INT NOT NULL,
                section_order INT NOT NULL,
                section_name VARCHAR(100) NOT NULL,
                svg_layer_ids JSON NOT NULL,
                required_terms JSON NOT NULL,
                hint_text TEXT,
                explanation_text TEXT,

                FOREIGN KEY (blueprint_id) REFERENCES blueprints(id) ON DELETE CASCADE,
                UNIQUE KEY unique_section_order (blueprint_id, section_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Update blueprint_reveals table - check if columns exist before adding
        $this->addColumnIfNotExists('blueprint_reveals', 'revealed_sections', 'JSON DEFAULT (\'[]\')');
        $this->addColumnIfNotExists('blueprint_reveals', 'completed_at', 'TIMESTAMP NULL');
        $this->addColumnIfNotExists('blueprint_reveals', 'total_attempts', 'INT DEFAULT 0');
        $this->addColumnIfNotExists('blueprint_reveals', 'best_score', 'INT DEFAULT 0');
        $this->addColumnIfNotExists('blueprint_reveals', 'hints_used', 'INT DEFAULT 0');

        // Create blueprint_attempts table
        $this->pdo->exec("
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
        ");

        // Create blueprint_education table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS blueprint_education (
                id INT AUTO_INCREMENT PRIMARY KEY,
                blueprint_id INT NOT NULL,
                section_order INT NOT NULL,
                wrong_answer_type VARCHAR(100) NOT NULL,
                educational_content TEXT NOT NULL,
                reinforcement_hint TEXT,
                key_learning_points JSON,

                FOREIGN KEY (blueprint_id) REFERENCES blueprints(id) ON DELETE CASCADE,
                UNIQUE KEY unique_wrong_answer (blueprint_id, section_order, wrong_answer_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Insert sample blueprint data
        $this->insertSampleData();

        echo "Blueprint revelation system tables created successfully!\n";
    }

    private function addColumnIfNotExists($table, $column, $definition)
    {
        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
            $stmt->execute([$column]);
            $exists = $stmt->fetch();

            if (!$exists) {
                $this->pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
            }
        } catch (Exception $e) {
            // Table might not exist yet, skip
        }
    }

    private function insertSampleData()
    {
        // Insert sample blueprints
        $stmt = $this->pdo->prepare("
            INSERT INTO blueprints (
                slug, title, description, category, difficulty_level,
                full_svg_content, layer_definitions, total_sections, base_reward_coins, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE title = VALUES(title)
        ");

        // Structural Beam Layout
        $stmt->execute([
            'structural-beam-layout',
            'Structural Beam Layout',
            'Master the fundamental components of structural beam systems including load distribution, support conditions, and reinforcement patterns.',
            'structural',
            1,
            $this->getSampleBeamBlueprintSVG(),
            '["foundation", "beams", "columns", "reinforcement", "dimensions"]',
            5,
            50,
            1 // Assuming admin user ID
        ]);

        // Reinforced Concrete Slab
        $stmt->execute([
            'reinforced-concrete-slab',
            'Reinforced Concrete Slab Design',
            'Learn advanced reinforced concrete slab design including rebar spacing, concrete cover, and load calculations.',
            'civil',
            2,
            $this->getSampleSlabBlueprintSVG(),
            '["concrete", "reinforcement", "formwork", "loads", "details"]',
            5,
            100,
            1
        ]);

        // Insert sample sections
        $this->insertSampleSections();
    }

    private function insertSampleSections()
    {
        $sections = [
            [1, 1, 'Foundation Layout', '["foundation"]', '["footing", "foundation", "bearing"]', 'Look for the base support elements', 'Foundation footings provide the bearing surface for structural loads and must be designed for soil bearing capacity.'],
            [1, 2, 'Column Placement', '["columns"]', '["column", "axial", "compression"]', 'Vertical load-bearing members', 'Columns transfer loads from beams to foundations and are designed for axial compression and buckling resistance.'],
            [1, 3, 'Beam Configuration', '["beams"]', '["beam", "bending", "moment"]', 'Horizontal load-carrying members', 'Beams span between supports and resist bending moments from applied loads.'],
            [1, 4, 'Reinforcement Details', '["reinforcement"]', '["rebar", "reinforcement", "concrete"]', 'Steel reinforcement within concrete', 'Reinforcement provides tensile strength that concrete lacks, working together in composite action.'],
            [1, 5, 'Dimensions & Annotations', '["dimensions"]', '["dimension", "scale", "annotation"]', 'Measurements and technical notes', 'Proper dimensioning ensures accurate construction and compliance with engineering specifications.']
        ];

        $stmt = $this->pdo->prepare("
            INSERT INTO blueprint_sections (
                blueprint_id, section_order, section_name, svg_layer_ids,
                required_terms, hint_text, explanation_text
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE section_name = VALUES(section_name)
        ");

        foreach ($sections as $section) {
            $stmt->execute($section);
        }
    }

    private function getSampleBeamBlueprintSVG()
    {
        return '<svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
            <!-- Foundation Layer -->
            <g id="foundation" opacity="0">
                <rect x="100" y="500" width="600" height="50" fill="#8B4513" stroke="#654321" stroke-width="2"/>
                <text x="400" y="530" text-anchor="middle" fill="#654321" font-size="12">Foundation</text>
            </g>

            <!-- Columns Layer -->
            <g id="columns" opacity="0">
                <rect x="150" y="200" width="40" height="300" fill="#708090" stroke="#2F4F4F" stroke-width="2"/>
                <rect x="350" y="200" width="40" height="300" fill="#708090" stroke="#2F4F4F" stroke-width="2"/>
                <rect x="550" y="200" width="40" height="300" fill="#708090" stroke="#2F4F4F" stroke-width="2"/>
                <text x="170" y="180" fill="#2F4F4F" font-size="10">Columns</text>
            </g>

            <!-- Beams Layer -->
            <g id="beams" opacity="0">
                <rect x="150" y="180" width="240" height="30" fill="#D2691E" stroke="#8B4513" stroke-width="2"/>
                <rect x="350" y="180" width="240" height="30" fill="#D2691E" stroke="#8B4513" stroke-width="2"/>
                <text x="270" y="160" fill="#8B4513" font-size="10">Beams</text>
            </g>

            <!-- Reinforcement Layer -->
            <g id="reinforcement" opacity="0">
                <circle cx="200" cy="195" r="3" fill="#FF6347"/>
                <circle cx="250" cy="195" r="3" fill="#FF6347"/>
                <circle cx="300" cy="195" r="3" fill="#FF6347"/>
                <circle cx="400" cy="195" r="3" fill="#FF6347"/>
                <circle cx="450" cy="195" r="3" fill="#FF6347"/>
                <circle cx="500" cy="195" r="3" fill="#FF6347"/>
                <text x="350" y="220" text-anchor="middle" fill="#FF6347" font-size="10">Rebar</text>
            </g>

            <!-- Dimensions Layer -->
            <g id="dimensions" opacity="0">
                <line x1="150" y1="160" x2="390" y2="160" stroke="#000" stroke-width="1" marker-start="url(#arrow)" marker-end="url(#arrow)"/>
                <text x="270" y="150" text-anchor="middle" fill="#000" font-size="12">6.0m</text>
                <line x1="130" y1="200" x2="130" y2="500" stroke="#000" stroke-width="1" marker-start="url(#arrow)" marker-end="url(#arrow)"/>
                <text x="120" y="350" text-anchor="middle" fill="#000" font-size="12" transform="rotate(-90 120 350)">3.0m</text>
            </g>

            <defs>
                <marker id="arrow" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
                    <path d="M0,0 L0,6 L9,3 z" fill="#000"/>
                </marker>
            </defs>
        </svg>';
    }

    private function getSampleSlabBlueprintSVG()
    {
        return '<svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
            <!-- Concrete Layer -->
            <g id="concrete" opacity="0">
                <rect x="50" y="100" width="700" height="400" fill="#C0C0C0" stroke="#808080" stroke-width="2"/>
                <text x="400" y="320" text-anchor="middle" fill="#808080" font-size="14">Concrete Slab</text>
            </g>

            <!-- Reinforcement Layer -->
            <g id="reinforcement" opacity="0">
                <line x1="100" y1="150" x2="700" y2="150" stroke="#FF6347" stroke-width="3"/>
                <line x1="100" y1="200" x2="700" y2="200" stroke="#FF6347" stroke-width="3"/>
                <line x1="100" y1="250" x2="700" y2="250" stroke="#FF6347" stroke-width="3"/>
                <line x1="100" y1="300" x2="700" y2="300" stroke="#FF6347" stroke-width="3"/>
                <line x1="100" y1="350" x2="700" y2="350" stroke="#FF6347" stroke-width="3"/>
                <line x1="100" y1="400" x2="700" y2="400" stroke="#FF6347" stroke-width="3"/>
                <text x="400" y="430" text-anchor="middle" fill="#FF6347" font-size="12">Reinforcement Grid</text>
            </g>

            <!-- Formwork Layer -->
            <g id="formwork" opacity="0">
                <rect x="40" y="90" width="720" height="420" fill="none" stroke="#8B4513" stroke-width="4" stroke-dasharray="10,5"/>
                <text x="400" y="70" text-anchor="middle" fill="#8B4513" font-size="12">Formwork</text>
            </g>

            <!-- Loads Layer -->
            <g id="loads" opacity="0">
                <polygon points="200,80 190,100 210,100" fill="#DC143C"/>
                <polygon points="400,80 390,100 410,100" fill="#DC143C"/>
                <polygon points="600,80 590,100 610,100" fill="#DC143C"/>
                <text x="400" y="60" text-anchor="middle" fill="#DC143C" font-size="12">Applied Loads</text>
            </g>

            <!-- Details Layer -->
            <g id="details" opacity="0">
                <circle cx="150" cy="150" r="8" fill="none" stroke="#000" stroke-width="2"/>
                <text x="150" y="155" text-anchor="middle" fill="#000" font-size="8">Ø12</text>
                <circle cx="250" cy="200" r="8" fill="none" stroke="#000" stroke-width="2"/>
                <text x="250" y="205" text-anchor="middle" fill="#000" font-size="8">Ø12</text>
                <text x="400" y="520" text-anchor="middle" fill="#000" font-size="10">Rebar Details & Spacing</text>
            </g>
        </svg>';
    }

    public function down()
    {
        echo "Removing blueprint revelation system tables...\n";

        $this->pdo->exec("DROP TABLE IF EXISTS blueprint_attempts");
        $this->pdo->exec("DROP TABLE IF EXISTS blueprint_sections");
        $this->pdo->exec("DROP TABLE IF EXISTS blueprints");

        // Remove added columns from blueprint_reveals
        try {
            $this->pdo->exec("ALTER TABLE blueprint_reveals DROP COLUMN revealed_sections");
            $this->pdo->exec("ALTER TABLE blueprint_reveals DROP COLUMN completed_at");
            $this->pdo->exec("ALTER TABLE blueprint_reveals DROP COLUMN total_attempts");
            $this->pdo->exec("ALTER TABLE blueprint_reveals DROP COLUMN best_score");
            $this->pdo->exec("ALTER TABLE blueprint_reveals DROP COLUMN hints_used");
        } catch (Exception $e) {
            // Columns might not exist, ignore
        }

        echo "Blueprint revelation system tables removed.\n";
    }
}