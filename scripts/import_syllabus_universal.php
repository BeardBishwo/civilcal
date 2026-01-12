<?php

/**
 * Universal & Civil Syllabus Import Script - FINAL VERSION with In-Memory Cache
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/app/bootstrap.php';

use App\Services\SyllabusService;
use App\Core\Database;

$service = new SyllabusService();
$db = Database::getInstance();

echo "Starting Split Import (FINAL - with Cache)...\n";

$db->getPdo()->exec("SET FOREIGN_KEY_CHECKS = 0");
$db->getPdo()->exec("TRUNCATE TABLE syllabus_nodes");
$db->getPdo()->exec("SET FOREIGN_KEY_CHECKS = 1");

// In-memory cache: "parentId-slug" => nodeId
$nodeCache = [];

function createNode($service, $title, $type, $parentId = null, $order = 0)
{
    global $nodeCache, $db;

    if (empty($title)) return null;

    // Ensure parentId is int or null
    if ($parentId !== null) $parentId = (int)$parentId;

    $slug = $service->slugify($title);
    $cacheKey = ($parentId ?? 'NULL') . '-' . $slug;

    // Check cache first
    if (isset($nodeCache[$cacheKey])) {
        return $nodeCache[$cacheKey];
    }

    // Try to create
    $data = [
        'parent_id' => $parentId,
        'title' => $title,
        'type' => $type,
        'description' => '',
        'order' => $order,
        'is_active' => 1,
        'slug' => $slug
    ];

    try {
        $id = $service->createNode($data);
        $nodeCache[$cacheKey] = $id;
        return $id;
    } catch (Exception $e) {
        // Duplicate - fetch from DB and cache it
        $db = Database::getInstance();
        $sql = "SELECT id FROM syllabus_nodes WHERE slug = :slug AND " .
            ($parentId !== null ? "parent_id = :p" : "parent_id IS NULL");
        $params = ['slug' => $slug];
        if ($parentId !== null) $params['p'] = $parentId;

        $stmt = $db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $id = $stmt->fetchColumn();

        if ($id) {
            $nodeCache[$cacheKey] = $id;
            return $id;
        }

        echo "    [ERROR] Failed to create/find: $title (Parent: $parentId, Slug: $slug)\n";
        return null;
    }
}

// Setup Roots
$courseId = createNode($service, "Civil Engineering", "course", null, 1);
$educationId = createNode($service, "Diploma in Civil Engineering", "education_level", $courseId, 1);
$positionId = createNode($service, "Sub Engineer", "position", $educationId, 1);

echo "Civil Branch: $courseId -> $educationId -> $positionId\n";

// Parse
$filePath = ROOT_PATH . '/001_Sub Engineer Syllabus.md.processed';
if (!file_exists($filePath)) $filePath = ROOT_PATH . '/001_Sub Engineer Syllabus.md';
$lines = file($filePath);

$context = 'universal';
$currentParentId = null;
$categoryId = null;
$subCategoryId = null;

foreach ($lines as $lineIndex => $line) {
    $line = trim($line);
    if (empty($line)) continue;

    // Context Switchers
    if (strpos($line, 'Part I:') !== false) {
        $context = 'universal';
        $currentParentId = null;
        continue;
    }
    if (strpos($line, 'Part II:') !== false) {
        $context = 'civil';
        $currentParentId = $positionId;
        continue;
    }

    // Ignore Headers
    if (preg_match('/^\*\*(Paper|Section).*?\*\*/i', $line)) continue;

    // Main Category: "1. Title" or "1\. Title"
    if (preg_match('/^\s*(\d+)[\.\\\)]+\s+(.*)/', $line, $matches)) {
        $title = trim($matches[2]);
        $title = preg_replace('/\(.*Marks.*\)/', '', $title);
        $title = trim($title);

        $categoryId = createNode($service, $title, 'category', $currentParentId, $lineIndex);
        $subCategoryId = null;

        if ($categoryId) {
            $loc = $currentParentId ? "CIVIL" : "ROOT";
            echo "  [CAT] $title -> $categoryId ($loc)\n";
        }
        continue;
    }

    // Sub Category: "1.1. Title"
    if (
        preg_match('/^\s*\*?\s*(\d+\.\d+)\.?\s+(.*)/', $line, $matches) &&
        !preg_match('/^\s*\*?\s*(\d+\.\d+\.\d+)/', $line)
    ) {
        $title = trim($matches[2]);

        if (!$categoryId) {
            // echo "  [SKIP] Sub '$title' (No Category)\n";
            continue;
        }

        $subCategoryId = createNode($service, $title, 'sub_category', $categoryId, $lineIndex);
        continue;
    }

    // Topic: "1.1.1. Title"
    if (preg_match('/^\s*\*?\s*(\d+\.\d+\.\d+)\.?\s+(.*)/', $line, $matches)) {
        $title = trim($matches[2]);
        $parent = $subCategoryId ?? $categoryId;

        if ($parent) {
            createNode($service, $title, 'topic', $parent, $lineIndex);
        }
        continue;
    }
}

echo "\nImport Completed Successfully!\n";
echo "Total nodes cached: " . count($nodeCache) . "\n";
