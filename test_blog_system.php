<?php
/**
 * Blog System Test Script
 * Run this to verify database and basic functionality
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/database/bootstrap.php';

use App\Core\Database;

echo "=== Blog System Verification ===\n\n";

$db = Database::getInstance();

// Test 1: Check blog_posts table
echo "1. Checking blog_posts table...\n";
try {
    $result = $db->query("SHOW TABLES LIKE 'blog_posts'")->fetch();
    if ($result) {
        echo "   ✓ blog_posts table exists\n";
    } else {
        echo "   ✗ blog_posts table NOT found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Check quiz_questions blog columns
echo "\n2. Checking quiz_questions blog columns...\n";
try {
    $columns = $db->query("SHOW COLUMNS FROM quiz_questions WHERE Field IN ('slug', 'is_published_as_blog', 'blog_published_at', 'view_count', 'share_count')")->fetchAll();
    foreach ($columns as $col) {
        echo "   ✓ {$col['Field']} ({$col['Type']})\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check blog_posts structure
echo "\n3. Checking blog_posts structure...\n";
try {
    $columns = $db->query("DESCRIBE blog_posts")->fetchAll();
    echo "   Columns: " . count($columns) . "\n";
    foreach ($columns as $col) {
        echo "   - {$col['Field']} ({$col['Type']})\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: Check if controllers exist
echo "\n4. Checking controller files...\n";
$controllers = [
    'app/Controllers/BlogController.php',
    'app/Controllers/Admin/Blog/BlogPostController.php',
    'app/Helpers/BlogUrlHelper.php'
];
foreach ($controllers as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✓ $file\n";
    } else {
        echo "   ✗ $file NOT found\n";
    }
}

// Test 5: Check if views exist
echo "\n5. Checking view files...\n";
$views = [
    'themes/admin/views/blog/posts/create.php',
    'themes/admin/views/blog/posts/index.php',
    'themes/admin/views/blog/posts/preview.php',
    'themes/public/views/blog/question-post.php',
    'themes/public/views/blog/collection-post.php'
];
foreach ($views as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✓ $file\n";
    } else {
        echo "   ✗ $file NOT found\n";
    }
}

// Test 6: Count existing data
echo "\n6. Checking existing data...\n";
try {
    $postCount = $db->query("SELECT COUNT(*) as count FROM blog_posts")->fetch();
    echo "   Blog posts: {$postCount['count']}\n";
    
    $questionCount = $db->query("SELECT COUNT(*) as count FROM quiz_questions WHERE is_published_as_blog = 1")->fetch();
    echo "   Published questions: {$questionCount['count']}\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Verification Complete ===\n";
echo "\nNext steps:\n";
echo "1. Visit: http://localhost/Bishwo_Calculator/admin/blog/posts\n";
echo "2. Click 'Create New Post'\n";
echo "3. Test generating a blog post\n";
