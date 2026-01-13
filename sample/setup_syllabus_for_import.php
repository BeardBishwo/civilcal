<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    if (!$db) {
        die("Database connection failed\n");
    }

    function createSlug($text)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
        return $slug;
    }

    function getOrInsert($db, $title, $type, $parentId = null)
    {
        $sql = "SELECT id FROM syllabus_nodes WHERE title = ? AND type = ? AND (parent_id = ? OR (parent_id IS NULL AND ? IS NULL))";
        $params = [$title, $type, $parentId, $parentId];
        $stmt = $db->query($sql, $params);
        $existing = $stmt->fetch();
        if ($existing) return $existing['id'];

        $slug = createSlug($title);
        // Check for duplicate slug under same parent
        $checkSlug = $db->query("SELECT id FROM syllabus_nodes WHERE slug = ? AND (parent_id = ? OR (parent_id IS NULL AND ? IS NULL))", [$slug, $parentId, $parentId])->fetch();
        if ($checkSlug) {
            $slug .= '-' . rand(100, 999);
        }

        $sqlInsert = "INSERT INTO syllabus_nodes (title, slug, type, parent_id, is_active, order_index) VALUES (?, ?, ?, ?, 1, 0)";
        $db->query($sqlInsert, [$title, $slug, $type, $parentId]);
        return $db->lastInsertId();
    }

    // 1. Ensure PSC Course
    $pscCourseId = getOrInsert($db, 'Public Service Commission (PSC)', 'course', null);
    echo "PSC Course ID: $pscCourseId\n";

    // 2. Map existing root categories to PSC if they exist
    $categoriesToMap = ['General Awareness', 'Public Management', 'General Aptitude Test'];
    foreach ($categoriesToMap as $catTitle) {
        $sqlFind = "SELECT id FROM syllabus_nodes WHERE title = ? AND type = 'category' AND parent_id IS NULL";
        $existing = $db->query($sqlFind, [$catTitle])->fetch();
        if ($existing) {
            $db->query("UPDATE syllabus_nodes SET parent_id = ? WHERE id = ?", [$pscCourseId, $existing['id']]);
            echo "Mapped $catTitle to PSC.\n";
        } else {
            getOrInsert($db, $catTitle, 'category', $pscCourseId);
            echo "Created $catTitle under PSC.\n";
        }
    }

    getOrInsert($db, 'General Knowledge', 'category', $pscCourseId);

    // 3. Civil Engineering Subjects (Course ID 1)
    // Using 'category' as it is a valid Enum value
    $civilSubjects = [
        'Structural Design',
        'Soil Mechanics',
        'Irrigation Eng.',
        'Const. Management',
        'Surveying',
        'Construction Materials',
        'Building Construction',
        'Hydraulics',
        'Concrete Technology',
        'Water Supply',
        'Sanitary Engineering',
        'Highway Engineering',
        'Estimating & Costing'
    ];

    foreach ($civilSubjects as $sub) {
        $id = getOrInsert($db, $sub, 'category', 1);
        echo "Civil Category (Subject): $sub ID: $id\n";
    }

    echo "Syllabus Setup Complete.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
