<?php

namespace App\Services;

class ImageOptimizer
{
    /**
     * optimize an image (compress and strip metadata)
     */
    public function optimize($sourcePath, $quality = 80)
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $info = getimagesize($sourcePath);
        $mime = $info['mime'];
        $fileSizeBefore = filesize($sourcePath);

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                // Save formatted with quality
                imagejpeg($image, $sourcePath, $quality);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                // Preserve transparency
                imagealphablending($image, false);
                imagesavealpha($image, true);
                // PNG quality is 0-9 (inverted scaling)
                // 80/100 -> ~ 2/9 or 6-8 compression level
                $pngQuality = round((100 - $quality) / 10);
                imagepng($image, $sourcePath, $pngQuality);
                break;
            case 'image/gif':
                // GIFs are hard to compress without losing animation or transparency with GD
                return false;
            default:
                return false;
        }

        if (isset($image)) {
            imagedestroy($image);
        }
        
        clearstatcache();
        $fileSizeAfter = filesize($sourcePath);
        
        return [
            'original_size' => $fileSizeBefore,
            'optimized_size' => $fileSizeAfter,
            'savings' => $fileSizeBefore - $fileSizeAfter
        ];
    }

    /**
     * Create a thumbnail or resized version
     */
    public function resize($sourcePath, $destPath, $maxWidth, $maxHeight = null)
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        list($width, $height) = getimagesize($sourcePath);
        
        if ($maxHeight === null) {
            // Calculate height to maintain aspect ratio
            $ratio = $width / $height;
            $maxHeight = $maxWidth / $ratio;
        }

        // Calculate new dimensions
        $newWidth = $maxWidth;
        $newHeight = $maxHeight;
        
        // If original is smaller, don't upscale
        if ($width < $newWidth && $height < $newHeight) {
            return copy($sourcePath, $destPath);
        }

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        
        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($sourcePath);
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }

        // Resize
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($thumb, $destPath, 85);
                break;
            case 'image/png':
                imagepng($thumb, $destPath, 8);
                break;
            case 'image/gif':
                imagegif($thumb, $destPath);
                break;
        }
        
        imagedestroy($thumb);
        imagedestroy($source);
        
        return true;
    }

    /**
     * Convert image to WebP
     */
    public function convertToWebP($sourcePath, $destPath, $quality = 80)
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                imagepalettetotruecolor($image);
                break;
            default:
                return false;
        }

        if ($image) {
            $result = imagewebp($image, $destPath, $quality);
            imagedestroy($image);
            return $result;
        }
        
        return false;
    }
}
