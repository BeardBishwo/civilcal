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

        // Get categories for dropdown
        $categories = $this->wordModel->getCategories();

        $this->view->render('admin/quiz/word-bank/index', [
            'page_title' => 'Terminology Manager',
            'words' => $words,
            'categories' => $categories
        ]);
    }

    public function store()
    {
        try {
            // Sanitize and validate inputs
            $term = trim($_POST['term'] ?? '');
            if (empty($term)) throw new Exception("Term is required");
            $term = htmlspecialchars($term, ENT_QUOTES, 'UTF-8'); // Prevent XSS

            $definition = trim($_POST['definition'] ?? '');
            if (empty($definition)) throw new Exception("Definition is required");
            $definition = htmlspecialchars($definition, ENT_QUOTES, 'UTF-8');

            $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            if ($categoryId !== null) {
                if ($categoryId <= 0) throw new Exception("Invalid category ID");
                // Check if category exists and is active
                $categoryExists = $this->db->query("SELECT id FROM syllabus_nodes WHERE id = ? AND type = 'category' AND is_active = 1", [$categoryId])->fetch();
                if (!$categoryExists) throw new Exception("Category not found");
            }

            $difficultyLevel = (int)($_POST['difficulty_level'] ?? 3);
            if ($difficultyLevel < 1 || $difficultyLevel > 5) {
                $difficultyLevel = 3;
            }

            $language = trim($_POST['language'] ?? 'en');
            $language = htmlspecialchars($language, ENT_QUOTES, 'UTF-8');

            $synonyms = trim($_POST['synonyms'] ?? '');
            $synonyms = htmlspecialchars($synonyms, ENT_QUOTES, 'UTF-8');

            $usageExample = trim($_POST['usage_example'] ?? '');
            $usageExample = htmlspecialchars($usageExample, ENT_QUOTES, 'UTF-8');

            $data = [
                'term' => $term,
                'definition' => $definition,
                'category_id' => $categoryId,
                'difficulty_level' => $difficultyLevel,
                'language' => $language,
                'synonyms' => $synonyms,
                'usage_example' => $usageExample
            ];

            $this->wordModel->create($data);
            $this->jsonResponse(['success' => true, 'message' => 'Term added to Word Bank']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        try {
            $id = (int)$id;
            if ($id <= 0) throw new Exception("Invalid term ID");

            // Sanitize and validate inputs
            $term = trim($_POST['term'] ?? '');
            if (empty($term)) throw new Exception("Term is required");
            $term = htmlspecialchars($term, ENT_QUOTES, 'UTF-8');

            $definition = trim($_POST['definition'] ?? '');
            if (empty($definition)) throw new Exception("Definition is required");
            $definition = htmlspecialchars($definition, ENT_QUOTES, 'UTF-8');

            $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            if ($categoryId !== null) {
                if ($categoryId <= 0) $categoryId = null; // Map 0/empty to null
            }

            $difficultyLevel = (int)($_POST['difficulty_level'] ?? 3);
            if ($difficultyLevel < 1 || $difficultyLevel > 5) {
                $difficultyLevel = 3;
            }

            $language = trim($_POST['language'] ?? 'en');
            $language = htmlspecialchars($language, ENT_QUOTES, 'UTF-8');

            $synonyms = trim($_POST['synonyms'] ?? '');
            $synonyms = htmlspecialchars($synonyms, ENT_QUOTES, 'UTF-8');

            $usageExample = trim($_POST['usage_example'] ?? '');
            $usageExample = htmlspecialchars($usageExample, ENT_QUOTES, 'UTF-8');

            $data = [
                'term' => $term,
                'definition' => $definition,
                'category_id' => $categoryId,
                'difficulty_level' => $difficultyLevel,
                'language' => $language,
                'synonyms' => $synonyms,
                'usage_example' => $usageExample
            ];

            $this->wordModel->update($id, $data);
            $this->jsonResponse(['success' => true, 'message' => 'Term updated successfully']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $id = (int)$id;
            if ($id <= 0) throw new Exception("Invalid term ID");

            $this->wordModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => 'Term deleted']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function bulkImport()
    {
        try {
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("No file uploaded or upload error");
            }

            $file = $_FILES['csv_file'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                throw new Exception("File size exceeds 5MB limit");
            }

            // Validate MIME type
            $allowedMimes = ['text/csv', 'text/plain', 'application/csv'];
            if (!in_array($file['type'], $allowedMimes)) {
                throw new Exception("Invalid file type. Only CSV allowed");
            }

            $handle = fopen($file['tmp_name'], 'r');
            if (!$handle) throw new Exception("Cannot open file");

            $imported = 0;
            $errors = [];
            $row = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                if ($row === 1) continue; // Skip header

                if (count($data) < 3) {
                    $errors[] = "Row $row: Invalid format (need: term, definition, difficulty_level, category_id)";
                    continue;
                }

                try {
                    $term = trim($data[0] ?? '');
                    if (empty($term)) throw new Exception("Term is empty");
                    $term = htmlspecialchars($term, ENT_QUOTES, 'UTF-8');

                    $definition = trim($data[1] ?? '');
                    if (empty($definition)) throw new Exception("Definition is empty");
                    $definition = htmlspecialchars($definition, ENT_QUOTES, 'UTF-8');

                    $difficultyLevel = (int)($data[2] ?? 3);
                    if ($difficultyLevel < 1 || $difficultyLevel > 5) {
                        $difficultyLevel = 3;
                    }

                    $categoryId = isset($data[3]) ? (int)trim($data[3]) : null;
                    if ($categoryId !== null && $categoryId > 0) {
                        // Validate category exists
                        $categoryExists = $this->db->query("SELECT id FROM syllabus_nodes WHERE id = ? AND type = 'category' AND is_active = 1", [$categoryId])->fetch();
                        if (!$categoryExists) {
                            $categoryId = null; // Set to null if invalid
                        }
                    }

                    // Check for duplicates
                    $exists = $this->db->query("SELECT id FROM word_bank WHERE term = ? AND category_id IS NOT DISTINCT FROM ?", [$term, $categoryId])->fetch();
                    if ($exists) {
                        $errors[] = "Row $row: Term '$term' already exists in this category";
                        continue;
                    }

                    $this->wordModel->create([
                        'term' => $term,
                        'definition' => $definition,
                        'difficulty_level' => $difficultyLevel,
                        'category_id' => $categoryId
                    ]);
                    $imported++;
                } catch (Exception $e) {
                    $errors[] = "Row $row: " . $e->getMessage();
                }
            }

            fclose($handle);

            $this->jsonResponse([
                'success' => true,
                'imported' => $imported,
                'errors' => $errors,
                'message' => "Imported $imported terms. " . (count($errors) > 0 ? count($errors) . " errors." : "")
            ]);
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
