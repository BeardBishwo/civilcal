<?php
/**
 * Add blog functionality to quiz_questions
 */

// Bootstrap
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

try {
    echo "Adding blog columns to quiz_questions...\n";
    
    // Check if columns exist before adding
    $columns = $pdo->query("SHOW COLUMNS FROM quiz_questions")->fetchAll(PDO::FETCH_ASSOC);
    $existingColumns = array_column($columns, 'Field');
    
    // Add slug column
    if (!in_array('slug', $existingColumns)) {
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN slug VARCHAR(255) UNIQUE AFTER unique_code");
        echo "✓ Added slug column\n";
    } else {
        echo "⊙ slug column already exists\n";
    }
    
    // Add is_published_as_blog column
    if (!in_array('is_published_as_blog', $existingColumns)) {
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN is_published_as_blog TINYINT(1) DEFAULT 0 AFTER is_active");
        echo "✓ Added is_published_as_blog column\n";
    } else {
        echo "⊙ is_published_as_blog column already exists\n";
    }
    
    // Add blog_published_at column
    if (!in_array('blog_published_at', $existingColumns)) {
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN blog_published_at DATETIME AFTER is_published_as_blog");
        echo "✓ Added blog_published_at column\n";
    } else {
        echo "⊙ blog_published_at column already exists\n";
    }
    
    // Add view_count column
    if (!in_array('view_count', $existingColumns)) {
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN view_count INT DEFAULT 0 AFTER blog_published_at");
        echo "✓ Added view_count column\n";
    } else {
        echo "⊙ view_count column already exists\n";
    }
    
    // Add share_count column
    if (!in_array('share_count', $existingColumns)) {
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN share_count INT DEFAULT 0 AFTER view_count");
        echo "✓ Added share_count column\n";
    } else {
        echo "⊙ share_count column already exists\n";
    }
    
    // Create indexes
    echo "\nCreating indexes...\n";
    
    try {
        $pdo->exec("CREATE INDEX idx_blog_published ON quiz_questions(is_published_as_blog, blog_published_at)");
        echo "✓ Created idx_blog_published\n";
    } catch (Exception $e) {
        echo "⊙ idx_blog_published already exists\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX idx_slug ON quiz_questions(slug)");
        echo "✓ Created idx_slug\n";
    } catch (Exception $e) {
        echo "⊙ idx_slug already exists\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX idx_view_count ON quiz_questions(view_count DESC)");
        echo "✓ Created idx_view_count\n";
    } catch (Exception $e) {
        echo "⊙ idx_view_count already exists\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
