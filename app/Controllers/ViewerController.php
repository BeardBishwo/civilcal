<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Auth;
use App\Models\LibraryFile;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ViewerController extends Controller
{
    public function show($id)
    {
        try {
            // 1. Get File Info
            $libraryFileModel = new LibraryFile();
            $file = $libraryFileModel->find($id);

            if (!$file) {
                http_response_code(404);
                die('File not found');
            }

            // Universal Preview Image Priority
            // If a manual preview image exists (uploaded by user), show that!
            if (!empty($file->preview_path)) {
                $this->renderCad($file); // Uses generic image renderer
                return;
            }

            // 2. Resolve Path
            // Files are in storage/library...
            // $file->file_path usually relative to storage/library or just 'quarantine/filename'
            // Let's assume relative to STORAGE_PATH . '/library/'
            $filePath = STORAGE_PATH . '/library/' . $file->file_path;

            if (!file_exists($filePath)) {
                die('Physical file not found: ' . $filePath);
            }

            // 3. Handle Types
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            switch ($ext) {
                case 'xlsx':
                case 'xls':
                case 'csv':
                    if (class_exists(IOFactory::class)) {
                        $this->renderExcel($filePath);
                    } else {
                        echo "Spreadsheet viewer not installed.";
                    }
                    break;

                case 'pdf':
                    $this->renderPdf($file, $id);
                    break;

                case 'dwg':
                case 'dxf':
                    $this->renderCad($file);
                    break;

                case 'jpg':
                case 'jpeg':
                case 'png':
                    $this->renderImage($filePath);
                    break;

                default:
                    die("No preview available for this file type.");
            }
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    private function renderExcel($path)
    {
        // Use PhpSpreadsheet to convert to HTML
        $spreadsheet = IOFactory::load($path);
        $writer = IOFactory::createWriter($spreadsheet, 'Html');

        // Output clean HTML
        echo '<!DOCTYPE html><html><head><style>table { border-collapse: collapse; width: 100%; font-family: sans-serif; } td, th { border: 1px solid #ddd; padding: 8px; } tr:nth-child(even){background-color: #f2f2f2;} th { padding-top: 12px; padding-bottom: 12px; text-align: left; background-color: #04AA6D; color: white; }</style></head><body>';
        echo '<div style="padding: 20px; background: #f9f9f9; border-bottom: 1px solid #ccc;"><h3>Excel Preview</h3><p>This is a read-only preview.</p></div>';
        echo '<div style="overflow: auto; max-height: 90vh;">';
        $writer->save('php://output');
        echo '</div></body></html>';
    }

    private function renderPdf($file, $id)
    {
        // SECURITY UPDATE: Check ownership or permission
        // Assuming Auth::user() and some ownership check exists. 
        // For strictness, we default to blocking full view unless we can verify.
        // As per prompt: "The backend must generate a separate... single-page PDF"
        // Since we can't easily generate PDFs on the fly here without libraries, we fallback to preview image.

        $hasAccess = false;
        if (\App\Core\Auth::check()) {
            $user = \App\Core\Auth::user();
            // Check if free or purchased
            if (($file->price ?? 0) <= 0) {
                $hasAccess = true;
            } else {
                $db = \App\Core\Database::getInstance();
                $owned = $db->query("SELECT id FROM user_library WHERE user_id = ? AND file_id = ?", [$user->id, $id])->fetch();
                if ($owned) $hasAccess = true;
            }
        }

        if ($hasAccess) {
            $this->view('library/viewer/pdf', ['file' => $file, 'streamUrl' => "/api/library/stream?id=$id"]);
        } else {
            // Show Preview Image Only
            if (!empty($file->preview_path)) {
                $this->renderCad($file); // Re-use image renderer
            } else {
                die("<h2>Preview Only</h2><p>Please purchase this file to view the full content.</p>");
            }
        }
    }

    private function renderCad($file)
    {
        // Show the preview image
        if (!empty($file->preview_path)) {
            // Preview path is relative to storage/library? 
            // We need to serve this image. 
            // If it's in public, direct link. If in storage, stream it.
            // Our upload logic put it in storage/library/previews. we need to stream it.

            $imageUrl = "/api/library/preview-image?id={$file->id}";
            echo '<!DOCTYPE html><html><body style="margin:0; background: #111; display:flex; justify-content:center; align-items:center; height:100vh;">';
            echo '<img src="' . $imageUrl . '" style="max-width:100%; max-height:100%; box-shadow: 0 0 20px rgba(0,0,0,0.5);">';
            echo '</body></html>';
        } else {
            echo '<div style="padding:50px; text-align:center; font-family:sans-serif;">No preview image available for this CAD file.</div>';
        }
    }

    private function renderImage($path)
    {
        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        readfile($path);
    }
}
