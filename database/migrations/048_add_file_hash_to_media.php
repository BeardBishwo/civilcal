<?php
/**
 * Migration: Add file_hash to media table
 */
return [
    'up' => function($db) {
        $db->exec("ALTER TABLE media ADD COLUMN file_hash CHAR(32) NULL AFTER id");
        $db->exec("CREATE INDEX idx_media_file_hash ON media(file_hash)");
        
        // Backfill existing hashes
        $stmt = $db->query("SELECT id, file_path FROM media");
        $files = $stmt->fetchAll();
        $basePath = __DIR__ . '/../../public/storage/';
        
        foreach ($files as $file) {
            $path = $basePath . $file['file_path'];
            if (file_exists($path)) {
                $hash = md5_file($path);
                $stmtUpdate = $db->prepare("UPDATE media SET file_hash = ? WHERE id = ?");
                $stmtUpdate->execute([$hash, $file['id']]);
            }
        }
    },
    'down' => function($db) {
        $db->exec("ALTER TABLE media DROP COLUMN file_hash");
    }
];
