<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;

/**
 * QuestionImportController - Bulk Import with Nepali Support
 */
class QuestionImportController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Show the import form
     */
    public function index()
    {
        $this->view('admin/quiz/import', [
            'title' => 'Bulk Import Questions | Admin'
        ]);
    }

    /**
     * Handle the CSV upload
     */
    public function upload()
    {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Please select a valid CSV file.";
            return $this->redirect('/admin/quiz/import');
        }

        $fileName = $_FILES['file']['tmp_name'];
        $file = fopen($fileName, "r");
        
        // Remove UTF-8 BOM if present
        $bom = fread($file, 3);
        if ($bom != "\xEF\xBB\xBF") {
            rewind($file);
        }

        // Skip the header row
        $header = fgetcsv($file);
        
        $successCount = 0;
        $errorCount = 0;
        $rowNumber = 1;

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            $rowNumber++;

            // Skip empty rows
            if (empty($column[0])) continue;

            // Validate column count (expecting 8-9 columns)
            if (count($column) < 8) {
                $errorCount++;
                continue;
            }

            try {
                $this->db->insert('quiz_questions', [
                    'question_text'   => $column[0],
                    'option_a'        => $column[1],
                    'option_b'        => $column[2],
                    'option_c'        => $column[3],
                    'option_d'        => $column[4],
                    'correct_option'  => strtoupper(trim($column[5])),
                    'difficulty'      => strtolower(trim($column[6])),
                    'explanation'     => $column[7] ?? '',
                    'related_tool_link' => $column[8] ?? null,
                    'status'          => 1
                ]);
                $successCount++;
            } catch (\Exception $e) {
                error_log("Import Error at row $rowNumber: " . $e->getMessage());
                $errorCount++;
            }
        }

        fclose($file);

        if ($errorCount > 0) {
            $_SESSION['warning'] = "Import completed with $successCount successes and $errorCount errors.";
        } else {
            $_SESSION['success'] = "Successfully imported $successCount questions!";
        }

        return $this->redirect('/admin/quiz/import');
    }
}
