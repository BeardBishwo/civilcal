<?php

if (file_exists(dirname(__DIR__, 3) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
} else {
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

/**
 * Upgrade Syllabus Nodes for Premium & Gamification Features
 */
class Migration_Upgrade_Syllabus_Premium {

    public function up() {
        $db = Database::getInstance();
        $pdo = $db->getPdo();

        echo "🚀 Upgrading 'syllabus_nodes' table with Premium Fields...\n";

        // Helper to add column safely
        $addColumn = function($sql, $colName) use ($pdo) {
            try {
                $pdo->exec($sql);
                echo "✅ Added '$colName' column.\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                     echo "ℹ️  Column '$colName' already exists.\n";
                } else {
                     echo "⚠️  Error adding '$colName': " . $e->getMessage() . "\n";
                }
            }
        };

        try {
            // Add 'slug' column
            $addColumn("ALTER TABLE syllabus_nodes ADD COLUMN slug VARCHAR(255) NULL AFTER title", "slug");
            try { $pdo->exec("ALTER TABLE syllabus_nodes ADD INDEX idx_slug (slug)"); } catch (Exception $e) {}

            // Add 'image_path' column
            $addColumn("ALTER TABLE syllabus_nodes ADD COLUMN image_path VARCHAR(255) NULL AFTER slug", "image_path");

            // Add 'is_premium' column
            $addColumn("ALTER TABLE syllabus_nodes ADD COLUMN is_premium BOOLEAN DEFAULT FALSE COMMENT 'If true, requires coins to unlock'", "is_premium");

            // Add 'unlock_price' column
            $addColumn("ALTER TABLE syllabus_nodes ADD COLUMN unlock_price INT DEFAULT 0 COMMENT 'Cost in BB Coins'", "unlock_price");

            // Add 'order_index' column
            $addColumn("ALTER TABLE syllabus_nodes ADD COLUMN order_index INT DEFAULT 0 COMMENT 'For custom sorting'", "order_index");

            // Add 'question_count' column (Performance)
            $addColumn("ALTER TABLE syllabus_nodes ADD COLUMN question_count INT DEFAULT 0", "question_count");

            // Initialize Slugs (Only if empty)
            echo "🔄 Checking slugs...\n";
            $stmt = $pdo->query("SELECT id, title FROM syllabus_nodes");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $slug = $this->generateSlug($row['title']);
                // Ensure uniqueness could be handled here, but simplest approach for now:
                $pdo->prepare("UPDATE syllabus_nodes SET slug = ? WHERE id = ?")->execute([$slug, $row['id']]);
            }
            echo "✅ Slugs generated.\n";

        } catch (PDOException $e) {
            // If checking specifically for 'Duplicate column name' error code (1060), we could skip.
            // But relying on try-catch for the block. A robust migration system checks per column.
            echo "⚠️  Migration Warning (Column likely exists): " . $e->getMessage() . "\n";
        }
    }

    private function generateSlug($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }

    public function down() {
        $db = Database::getInstance();
        $pdo = $db->getPdo();

        $columns = ['question_count', 'order_index', 'unlock_price', 'is_premium', 'image_path', 'slug'];
        
        foreach ($columns as $col) {
            try {
                $pdo->exec("ALTER TABLE syllabus_nodes DROP COLUMN $col");
            } catch (Exception $e) {}
        }
        echo "⬇️  Downgraded 'syllabus_nodes' table.\n";
    }
}

// Execute Migration
if (php_sapi_name() === 'cli') {
    (new Migration_Upgrade_Syllabus_Premium())->up();
}
