<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

// Mocking the BulkSave Logic from SyllabusController
try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    $level = "Test Simulation Level";
    $nodes = [
        ['title' => 'Unit 1', 'depth' => 0, 'type' => 'unit'],
        ['title' => 'Chapter A', 'depth' => 1, 'type' => 'chapter'],
        ['title' => 'Ref Unit', 'depth' => 0, 'type' => 'unit']
    ];
    $settings = ['active' => true];

    echo "--- Starting BulkSave Simulation ---\n";
    $db->beginTransaction();
    
    try {
        echo "1. Deleting existing nodes for level '$level'...\n";
        // DELETE logic
        $db->delete('syllabus_nodes', "level = :level", ['level' => $level]);
        echo "   Deleted.\n";

        echo "2. Reconstructing Hierarchy...\n";
        $parentStack = [null]; 
        
        foreach ($nodes as $index => $node) {
            $currentDepth = (int)$node['depth'];
            
            $parentId = ($currentDepth > 0 && isset($parentStack[$currentDepth - 1])) 
                        ? $parentStack[$currentDepth - 1] 
                        : null;

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $node['title']), '-'));
            
            $data = [
                'level' => $level,
                'title' => $node['title'],
                'slug' => $slug . '-' . uniqid(), // Ensure unique slug for test
                'type' => $node['type'] ?? 'unit',
                'parent_id' => $parentId,
                'questions_weight' => $node['weight'] ?? 0,
                'time_minutes' => $node['time'] ?? 0,
                'question_count' => $node['qCount'] ?? 0,
                'order' => $index,
                'is_active' => 1
            ];
            
            echo "   Inserting Node: {$node['title']} (Parent: " . var_export($parentId, true) . ")...\n";
            $db->insert('syllabus_nodes', $data);
            $newId = $db->lastInsertId();
            echo "   Inserted ID: $newId\n";
            
            $parentStack[$currentDepth] = $newId;
            
            for($i = $currentDepth + 1; $i < 10; $i++) unset($parentStack[$i]);
        }

        echo "3. Saving Settings...\n";
        if (!empty($settings)) {
            // Map settings to actual table columns: total_time, full_marks, pass_marks, negative_rate
            $settingsData = [
                'level' => $level,
                'total_time' => $settings['total_time'] ?? 0,
                'full_marks' => $settings['full_marks'] ?? 0,
                'pass_marks' => $settings['pass_marks'] ?? 0,
                'negative_rate' => $settings['negative_rate'] ?? 0.00
            ];

            $existing = $db->findOne('syllabus_settings', ['level' => $level]);
            if ($existing) {
                echo "   Updating Settings...\n";
                $updateData = $settingsData;
                unset($updateData['level']);
                $db->update('syllabus_settings', $updateData, "level = :level", ['level' => $level]);
            } else {
                echo "   Inserting Settings...\n";
                $db->insert('syllabus_settings', $settingsData);
            }
        }

        $db->commit();
        echo "--- Success! Transaction Committed. ---\n";

    } catch (\Exception $e) {
        $db->rollBack();
        echo "!!! CRASH (500) !!!: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }

} catch (Exception $e) {
    echo "General Setup Error: " . $e->getMessage() . "\n";
}
