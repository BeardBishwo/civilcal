<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Models\WordBank;
use Exception;

class WordBankController extends Controller
{
    private $wordModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->wordModel = new WordBank();
    }

    public function index()
    {
        $words = $this->wordModel->findAll();
        $this->view->render('admin/quiz/word-bank/index', [
            'page_title' => 'Terminology Manager',
            'words' => $words
        ]);
    }

    public function store()
    {
        try {
            if (empty($_POST['term'])) throw new Exception("Term is required");
            if (empty($_POST['definition'])) throw new Exception("Definition is required");

            $data = [
                'term' => $_POST['term'],
                'definition' => $_POST['definition'],
                'difficulty_level' => (int)($_POST['difficulty_level'] ?? 3),
                'language' => $_POST['language'] ?? 'en',
                'synonyms' => $_POST['synonyms'] ?? '',
                'usage_example' => $_POST['usage_example'] ?? ''
            ];

            $this->wordModel->create($data);
            $this->jsonResponse(['success' => true, 'message' => 'Term added to Word Bank']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $this->wordModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => 'Term deleted']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
