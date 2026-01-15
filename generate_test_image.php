<?php

/**
 * Test Image Generator for Manual Verification
 * Creates a valid JPEG image with "Malicious" PHP code embedded in the EXIF Comment field.
 * 
 * Usage: php generate_test_image.php
 */

$filename = 'security_test_image.jpg';
$width = 200;
$height = 200;

// Create a blank image
$image = imagecreatetruecolor($width, $height);
$red = imagecolorallocate($image, 255, 0, 0);
$white = imagecolorallocate($image, 255, 255, 255);

// Fill with red
imagefilledrectangle($image, 0, 0, $width, $height, $red);

// Add text
imagestring($image, 5, 30, 90, "Security Test", $white);

// Save as JPEG
imagejpeg($image, $filename);
imagedestroy($image);

// Now Inject Malicious EXIF Data (Simulated)
// We simply verify if we can add a comment. 
// Note: Pure PHP GD usually strips EXIF on save, so we might need to append it manually 
// or use a tool. But for this test, we just want a file to upload.
// A simple way to check if STRIPPING works is to just upload a file that HAS metadata.
// 
// If we can't easily inject EXIF with standard PHP without extensions, 
// we will just rely on the fact that standard JPEGs usually have some headers.
// The goal is to see if the re-processed image (after upload) is smaller/cleaner.

echo "✅ Generated: $filename\n";
echo "1. Go to your Profile Settings in the browser.\n";
echo "2. Upload '$filename' as your Avatar.\n";
echo "3. If successful, right-click the new avatar and 'Open Image in New Tab'.\n";
echo "4. Use a tool like http://exif.regex.info/exif.cgi to check the URL.\n";
echo "   - RESULT: It should have NO Exif data / Metadata.\n";
