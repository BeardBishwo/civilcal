<?php

/**
 * Import Syllabus Script
 * 
 * Usage: php scripts/import_syllabus.php
 */

// Define base path for bootstrap
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/app/bootstrap.php';

use App\Services\SyllabusService;

$service = new SyllabusService();

function createNode($service, $title, $type, $parentId = null, $order = 0, $description = '')
{
    $data = [
        'parent_id' => $parentId,
        'title' => $title,
        'type' => $type,
        'description' => $description,
        'order' => $order,
        'is_active' => 1
    ];

    try {
        $id = $service->createNode($data);
        return $id;
    } catch (Exception $e) {
        // If parent not found or other error
        echo "Error creating node '$title': " . $e->getMessage() . "\n";
        return null;
    }
}

// Read the file
$filePath = ROOT_PATH . '/001_Sub Engineer Syllabus.md';
if (!file_exists($filePath)) {
    die("File not found: $filePath\n");
}

$lines = file($filePath);

echo "Starting import...\n";

// State tracking
$courseId = null;
$paperId = null;
$partId = null;
$sectionId = null;
$subjectId = null; // Level 1 (e.g. 1. General Awareness)
$topicId = null;   // Level 2 (e.g. 1.1)

// Root Node
// "Sub Engineer (Civil)"
// Type: course
$courseId = createNode($service, "Sub Engineer (Civil)", "course", null, 0);
echo "Created Course: Sub Engineer (Civil) [ID: $courseId]\n";

foreach ($lines as $lineIndex => $line) {
    $line = trim($line);
    if (empty($line)) continue;

    // Detect Levels

    // Paper: **First Paper...**
    if (preg_match('/^\*\*(.*Paper.*?)\*\*$/i', $line, $matches) || preg_match('/^\*\*(Paper.*?)\*\*$/i', $line, $matches)) {
        $title = $matches[1]; // Remove **
        $paperId = createNode($service, $title, 'paper', $courseId, $lineIndex);
        $partId = null;
        $sectionId = null;
        $subjectId = null;
        $topicId = null;
        echo "  Created Paper: $title\n";
        continue;
    }

    // Part: **Part I...**
    if (preg_match('/^\*\*Part [IVX]+: (.*?)\*\*/', $line, $matches)) {
        $title = "Part " . $matches[1];
        // Clean trailing ** if inside capture
        $title = trim($line, '* ');

        $partId = createNode($service, $title, 'part', $paperId ?? $courseId, $lineIndex);
        $sectionId = null;
        $subjectId = null;
        $topicId = null;
        echo "    Created Part: $title\n";
        continue;
    }

    // Section: **Section - A...**
    if (preg_match('/^\*\*Section - ([A-Z])(.*?)\*\*/', $line, $matches)) {
        $title = trim($line, '* ');
        $parentId = $partId ?? $paperId ?? $courseId;
        $sectionId = createNode($service, $title, 'section', $parentId, $lineIndex);
        $subjectId = null;
        $topicId = null;
        echo "      Created Section: $title\n";
        continue;
    }

    // Subject (Level 1): 1. General Awareness
    // Matches "1. Text" or "1\. Text"
    if (preg_match('/^(\d+)(\\|\.) (.*)/', $line, $matches)) {
        $title = $matches[3];
        // Clean marks "(16 Marks)"
        $title = preg_replace('/\(.*Marks.*\)/', '', $title);
        $title = trim($title);

        $parentId = $sectionId ?? $partId ?? $paperId ?? $courseId;
        $subjectId = createNode($service, $title, 'unit', $parentId, $lineIndex); // Type: unit (matches SyllabusService.php defaults better than subject)
        $topicId = null;
        echo "        Created Unit: $title\n";
        continue;
    }

    // Topic (Level 2): * 1.1. Text
    // Uses Indent checks
    if (preg_match('/^\s*\*\s+(\d+\.\d+)\.?\s+(.*)/', $line, $matches) && !preg_match('/^\s*\*\s+(\d+\.\d+\.\d+)/', $line)) {
        $number = $matches[1];
        $title = $matches[2];
        $parentId = $subjectId ?? $sectionId ?? $partId; // Fallback

        $topicId = createNode($service, $title, 'topic', $parentId, $lineIndex);
        echo "          Created Topic: $title\n";
        continue;
    }

    // Sub-topic (Level 3): * 1.1.1. Text
    if (preg_match('/^\s*\*\s+(\d+\.\d+\.\d+)\.?\s+(.*)/', $line, $matches)) {
        $number = $matches[1];
        $title = $matches[2];
        $parentId = $topicId;

        createNode($service, $title, 'sub-topic', $parentId, $lineIndex);
        echo "            Created Sub-Topic: $title\n";
        continue;
    }
}

echo "Import Completed.\n";
