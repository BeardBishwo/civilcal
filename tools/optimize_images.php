<?php
// tools/optimize_images.php
// Usage: php tools/optimize_images.php
// Descr: Converts PNG/JPG to WebP in public/assets and themes directories to save bandwidth.

if (php_sapi_name() !== 'cli') die('CLI only');

echo "Starting Image Optimization...\n";

function convertToWebp($source, $destination, $quality = 80) {
    $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
    
    if ($extension === 'jpeg' || $extension === 'jpg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($extension === 'png') {
        $image = imagecreatefrompng($source);
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    } else {
        return false;
    }
    
    if (!$image) return false;
    
    $result = imagewebp($image, $destination, $quality);
    imagedestroy($image);
    
    return $result;
}

$directories = [
    __DIR__ . '/../public/assets',
    __DIR__ . '/../themes/default/assets/images'
];

$count = 0;
$bytesSaved = 0;

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $source = $file->getPathname();
                $dest = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $source);
                
                if (!file_exists($dest)) {
                    echo "Optimizing: " . basename($source) . "... ";
                    if (convertToWebp($source, $dest)) {
                        $origSize = filesize($source);
                        $newSize = filesize($dest);
                        $saved = $origSize - $newSize;
                        if ($saved > 0) $bytesSaved += $saved;
                        
                        echo "Done. Saved " . round($saved / 1024, 2) . "KB\n";
                        $count++;
                    } else {
                        echo "Failed.\n";
                    }
                }
            }
        }
    }
}

echo "\nOptimization Complete.\n";
echo "Converted $count images.\n";
echo "Total Bandwidth Saved: " . round($bytesSaved / 1024 / 1024, 2) . "MB per view.\n";
