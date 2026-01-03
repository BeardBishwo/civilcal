<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SyllabusService;

class SyllabusController extends Controller
{
    protected $db;
    protected $syllabusService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->db = Database::getInstance();
        $this->syllabusService = new SyllabusService();
    }

    /**
     * Syllabus Master Tree View
     */
    public function index()
    {
        // Get complete tree
        // SyllabusService::getTree() returns hierarchical array
        $tree = $this->syllabusService->getTree(null, false); // activeOnly=false to see drafts

        // We might want statistics
        $totalNodes = $this->db->query("SELECT count(*) as c FROM syllabus_nodes")->fetch();
        $totalQuestions = $this->db->query("SELECT count(*) as c FROM quiz_questions")->fetch(); // Approximation or use map

        return $this->view('admin/quiz/syllabus/index', [
            'page_title' => 'Syllabus Master',
            'tree' => $tree,
            'stats' => [
                'nodes' => $totalNodes['c'] ?? 0,
                'questions' => $totalQuestions['c'] ?? 0
            ]
        ]);
    }

    /**
     * Quick Add Node (AJAX) - Optional helper if Tree View allows direct add
     */
    public function store() {
        // ... handled by Category/SubCategory controllers mainly, 
        // but simple add logic could exist here.
    }
}
