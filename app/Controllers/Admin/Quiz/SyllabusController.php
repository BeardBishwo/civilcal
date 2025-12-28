<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use Exception;

class SyllabusController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Ensure admin access
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            header('Location: ' . app_base_url('login'));
            exit;
        }
    }

    /**
     * Main Syllabus View (Tree Structure)
     */
    public function index()
    {
        $categories = $this->db->find('quiz_categories', [], '`order` ASC');
        
        // Eager load subjects and topics for the tree view? 
        // For performance, we might want to fetch flat and build tree or fetch on demand.
        // Let's fetch all active structured data for the main view.
        
        // Fetch all data for the tree
        $sql = "
            SELECT 
                c.id as cat_id, c.name as cat_name, c.slug as cat_slug, 
                s.id as sub_id, s.name as sub_name, s.slug as sub_slug,
                t.id as topic_id, t.name as topic_name, t.slug as topic_slug
            FROM quiz_categories c
            LEFT JOIN quiz_subjects s ON c.id = s.category_id
            LEFT JOIN quiz_topics t ON s.id = t.subject_id
            ORDER BY c.`order` ASC, s.`order` ASC, t.`order` ASC
        ";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute();
        $flatData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Process into hierarchical array
        $syllabus = [];
        foreach ($flatData as $row) {
            $catId = $row['cat_id'];
            if (!isset($syllabus[$catId])) {
                $syllabus[$catId] = [
                    'id' => $catId,
                    'name' => $row['cat_name'],
                    'slug' => $row['cat_slug'],
                    'subjects' => []
                ];
            }
            
            if ($row['sub_id']) {
                $subId = $row['sub_id'];
                if (!isset($syllabus[$catId]['subjects'][$subId])) {
                    $syllabus[$catId]['subjects'][$subId] = [
                        'id' => $subId,
                        'name' => $row['sub_name'],
                        'slug' => $row['sub_slug'],
                        'topics' => []
                    ];
                }
                
                if ($row['topic_id']) {
                    $syllabus[$catId]['subjects'][$subId]['topics'][] = [
                        'id' => $row['topic_id'],
                        'name' => $row['topic_name'],
                        'slug' => $row['topic_slug']
                    ];
                }
            }
        }
        
        $this->view->render('admin/quiz/syllabus/index', [
            'page_title' => 'Quiz Syllabus Manager',
            'syllabus' => $syllabus,
            'categories' => $categories // For creating new subjects
        ]);
    }

    // --- Category Methods ---

    public function storeCategory()
    {
        try {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? $this->slugify($name);
            
            if (empty($name)) throw new Exception("Category Name is required");
            
            $this->db->insert('quiz_categories', [
                'name' => $name,
                'slug' => $slug,
                'description' => $_POST['description'] ?? '',
                'order' => (int)($_POST['order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ]);
            
            $this->jsonResponse(['success' => true, 'message' => 'Category Created']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updateCategory($id)
    {
        try {
            $name = $_POST['name'] ?? '';
            
            if (empty($name)) throw new Exception("Category Name is required");
            
            $data = [
                'name' => $name,
                'description' => $_POST['description'] ?? '',
                'order' => (int)($_POST['order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if (!empty($_POST['slug'])) {
                $data['slug'] = $_POST['slug'];
            }
            
            $this->db->update('quiz_categories', $data, "id = :id", ['id' => $id]);
            
            $this->jsonResponse(['success' => true, 'message' => 'Category Updated']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function deleteCategory($id)
    {
        try {
            $this->db->delete('quiz_categories', "id = :id", ['id' => $id]);
            $this->jsonResponse(['success' => true, 'message' => 'Category Deleted']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // --- Subject Methods ---

    public function storeSubject()
    {
        try {
            $categoryId = $_POST['category_id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? $this->slugify($name);
            
            if (empty($name) || empty($categoryId)) throw new Exception("Category and Subject Name required");
            
            $this->db->insert('quiz_subjects', [
                'category_id' => $categoryId,
                'name' => $name,
                'slug' => $slug,
                'order' => (int)($_POST['order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ]);
            
            $this->jsonResponse(['success' => true, 'message' => 'Subject Created']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updateSubject($id)
    {
        try {
            $name = $_POST['name'] ?? '';
            if (empty($name)) throw new Exception("Subject Name required");

            $data = [
                'name' => $name,
                'order' => (int)($_POST['order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            // Only update category if provided
            if (!empty($_POST['category_id'])) {
                $data['category_id'] = $_POST['category_id'];
            }
             
            if (!empty($_POST['slug'])) {
                $data['slug'] = $_POST['slug'];
            }

            $this->db->update('quiz_subjects', $data, "id = :id", ['id' => $id]);
            $this->jsonResponse(['success' => true, 'message' => 'Subject Updated']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function deleteSubject($id)
    {
        try {
            $this->db->delete('quiz_subjects', "id = :id", ['id' => $id]);
            $this->jsonResponse(['success' => true, 'message' => 'Subject Deleted']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // --- Topic Methods ---

    public function storeTopic()
    {
        try {
            $subjectId = $_POST['subject_id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? $this->slugify($name);
            
            if (empty($name) || empty($subjectId)) throw new Exception("Subject and Topic Name required");
            
            $this->db->insert('quiz_topics', [
                'subject_id' => $subjectId,
                'name' => $name,
                'slug' => $slug,
                'order' => (int)($_POST['order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ]);
            
            $this->jsonResponse(['success' => true, 'message' => 'Topic Created']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function updateTopic($id)
    {
        try {
            $name = $_POST['name'] ?? '';
            if (empty($name)) throw new Exception("Topic Name required");

            $data = [
                'name' => $name,
                'order' => (int)($_POST['order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if (!empty($_POST['subject_id'])) {
                $data['subject_id'] = $_POST['subject_id'];
            }

            if (!empty($_POST['slug'])) {
                $data['slug'] = $_POST['slug'];
            }

            $this->db->update('quiz_topics', $data, "id = :id", ['id' => $id]);
            $this->jsonResponse(['success' => true, 'message' => 'Topic Updated']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function deleteTopic($id)
    {
        try {
            $this->db->delete('quiz_topics', "id = :id", ['id' => $id]);
            $this->jsonResponse(['success' => true, 'message' => 'Topic Deleted']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // --- AJAX Helpers ---
    
    public function getSubjects($categoryId)
    {
        $subjects = $this->db->find('quiz_subjects', ['category_id' => $categoryId, 'is_active' => 1], '`order` ASC');
        $this->jsonResponse($subjects);
    }
    
    public function getTopics($subjectId)
    {
        $topics = $this->db->find('quiz_topics', ['subject_id' => $subjectId, 'is_active' => 1], '`order` ASC');
        $this->jsonResponse($topics);
    }

    // Helper
    private function slugify($text)
    {
        // Simple slugify - in production use a more robust library
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) return 'n-a';
        return $text;
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
