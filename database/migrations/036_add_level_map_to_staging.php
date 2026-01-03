<?php

if (file_exists(dirname(__DIR__, 3) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
} else {
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

class Migration_Add_Level_Map_To_Staging {
    
    public function up() {
        $db = Database::getInstance();
        $pdo = $db->getPdo();
        
        try {
            echo "🚀 Adding 'level_map' to 'question_import_staging'...\n";
            
            // Check if column exists
            $columns = $db->query("SHOW COLUMNS FROM question_import_staging LIKE 'level_map'")->fetchAll();
            
            if (empty($columns)) {
                $sql = "ALTER TABLE question_import_staging ADD COLUMN level_map VARCHAR(255) NULL COMMENT 'Stores Level Map Syntax (L4:Hard|L7:Easy)' AFTER explanation";
                $pdo->exec($sql);
                echo "✅ Added 'level_map' column.\n";
            } else {
                echo "ℹ️  Column 'level_map' already exists.\n";
            }

        } catch (Exception $e) {
            echo "⚠️  Error adding 'level_map': " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

// Execute Migration
if (php_sapi_name() === 'cli') {
    (new Migration_Add_Level_Map_To_Staging())->up();
}
