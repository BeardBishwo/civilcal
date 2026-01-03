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

    private $importService;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
        $this->importService = new \App\Services\QuestionImportService();
    }

    /**
     * Show the import form
     */
    public function index()
    {
        $this->view('admin/quiz/import', [
            'page_title' => 'Bulk Import Questions',
            'menu_active' => 'quiz-import'
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

        try {
            $result = $this->importService->importCSV($_FILES['file']['tmp_name']);
            
            if ($result['error_count'] > 0) {
                $_SESSION['warning'] = "Import completed with {$result['success_count']} successes and {$result['error_count']} errors.";
                $_SESSION['import_errors'] = $result['errors'];
            } else {
                $_SESSION['success'] = "Successfully imported {$result['success_count']} questions!";
            }
            
        } catch (\Exception $e) {
            $_SESSION['error'] = "Import Failed: " . $e->getMessage();
        }

        return $this->redirect('/admin/quiz/import');
    }
}
