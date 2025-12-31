<?php

namespace App\Services;

use Exception;

class WatermarkService
{
    /**
     * Creates a duplicate "dirty" preview of the image.
     * 
     * @param string $sourcePath Absolute path to source image
     * @param string $targetPath Absolute path to save preview
     * @return bool
     */
    public function createDirtyPreview($sourcePath, $targetPath)
    {
        // Ensure processed directory exists
        $dir = dirname($targetPath);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        if (extension_loaded('imagick')) {
            return $this->processWithImagick($sourcePath, $targetPath);
        } elseif (extension_loaded('gd')) {
            return $this->processWithGD($sourcePath, $targetPath);
        } else {
            throw new Exception("No image processing library found (Imagick or GD is required).");
        }
    }

    private function processWithImagick($source, $target)
    {
        try {
            $image = new \Imagick($source);
            
            // Flatten if PDF (take first page)
            if (strtolower(pathinfo($source, PATHINFO_EXTENSION)) === 'pdf') {
                $image->setIteratorIndex(0);
                $image = $image->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
                $image->setImageFormat('jpg');
            }

            // 1. Resize (Low Res)
            $image->resizeImage(800, 800, \Imagick::FILTER_LANCZOS, 1, true);

            // 2. Tile Watermark (Simulated with repeated text for independence from assets)
            $draw = new \ImagickDraw();
            $draw->setFillColor('rgba(150, 150, 150, 0.2)'); // Faint grey
            $draw->setFontSize(20);
            $draw->setGravity(\Imagick::GRAVITY_NORTHWEST);
            
            $w = $image->getImageWidth();
            $h = $image->getImageHeight();
            
            // Repeat logo text pattern
            for ($x = 0; $x < $w; $x += 150) {
                for ($y = 0; $y < $h; $y += 100) {
                    $image->annotateImage($draw, $x, $y, -30, "CivilCity.com");
                }
            }

            // 3. Diagonal "PREVIEW ONLY" Banner
            $bannerDraw = new \ImagickDraw();
            $bannerDraw->setFillColor('rgba(255, 0, 0, 0.3)'); // Red Semi-transparent
            $bannerDraw->setFontSize(50);
            $bannerDraw->setGravity(\Imagick::GRAVITY_CENTER);
            // Annotate allows rotation in newer versions, but gravity center + text works well. 
            // Rotation needs more complex logic or specific font metrics.
            // Simple approach: Center huge text.
            $image->annotateImage($bannerDraw, 0, 0, -45, "PREVIEW ONLY");
            $image->annotateImage($bannerDraw, 0, 0, -45, "\nUNPAID ASSET");

            $image->writeImage($target);
            $image->clear();
            $image->destroy();
            return true;

        } catch (Exception $e) {
            error_log("Imagick Watermark Failed: " . $e->getMessage());
            // Fallback to GD if Imagick fails (e.g. PDF issue)
            if (extension_loaded('gd') && strtolower(pathinfo($source, PATHINFO_EXTENSION)) !== 'pdf') {
                return $this->processWithGD($source, $target);
            }
            throw $e;
        }
    }

    private function processWithGD($source, $target)
    {
        $info = getimagesize($source);
        if ($info === false) return false;

        $type = $info[2];
        if ($type == IMAGETYPE_JPEG) $img = imagecreatefromjpeg($source);
        elseif ($type == IMAGETYPE_PNG) $img = imagecreatefrompng($source);
        else return false;

        $w = imagesx($img);
        $h = imagesy($img);

        // 1. Resize if too big
        if ($w > 800) {
            $newW = 800;
            $newH = ($h / $w) * 800;
            $newImg = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
            imagedestroy($img);
            $img = $newImg;
            $w = $newW;
            $h = $newH;
        }

        // 2. Add Watermark Pattern
        $textColor = imagecolorallocatealpha($img, 200, 200, 200, 90); // Very transparent
        $font = 4; // Built-in font
        
        for ($x = 0; $x < $w; $x += 150) {
            for ($y = 0; $y < $h; $y += 100) {
                imagestring($img, $font, $x, $y, "CivilCity", $textColor);
            }
        }

        // 3. Big Center Text (Poor man's diagonal - just multiple lines)
        $bannerColor = imagecolorallocatealpha($img, 255, 0, 0, 100); // Red transparent
        $cx = $w / 2 - 100;
        $cy = $h / 2 - 20;
        imagestring($img, 5, $cx, $cy, "PREVIEW ONLY - UNPAID", $bannerColor);
        imagestring($img, 5, $cx, $cy + 20, "PREVIEW ONLY - UNPAID", $bannerColor);

        // Save
        imagejpeg($img, $target, 60); // Low quality
        imagedestroy($img);
        return true;
    }
}
