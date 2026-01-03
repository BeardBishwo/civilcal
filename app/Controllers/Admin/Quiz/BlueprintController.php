<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Services\ExamBlueprintService;
use App\Services\SyllabusService;
use App\Services\ExamGeneratorService;
use Exception;

class BlueprintController extends Controller
{
    private $blueprintService;
    private $syllabusService;
    private $generatorService;

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            header('Location: ' . app_base_url('login'));
            exit;
        }

        $this->blueprintService = new ExamBlueprintService();
        $this->syllabusService = new SyllabusService();
        $this->generatorService = new ExamGeneratorService();
    }

    /**
     * List all blueprints
     */
    public function index()
    {
        $blueprints = $this->blueprintService->getAllBlueprints();

        $this->view->render('admin/quiz/blueprints/index', [
            'page_title' => 'Exam Blueprints',
            'blueprints' => $blueprints
        ]);
    }

    /**
     * Create new blueprint form
     */
    public function create()
    {
        $this->view->render('admin/quiz/blueprints/form', [
            'page_title' => 'Create Exam Blueprint',
            'blueprint' => null,
            'action' => app_base_url('admin/quiz/blueprints/store')
        ]);
    }

    /**
     * Store new blueprint
     */
    public function store()
    {
        try {
            $data = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'level' => $_POST['level'] ?? '',
                'total_questions' => (int)($_POST['total_questions'] ?? 50),
                'total_marks' => (int)($_POST['total_marks'] ?? 100),
                'duration_minutes' => (int)($_POST['duration_minutes'] ?? 60),
                'negative_marking_rate' => (float)($_POST['negative_marking_rate'] ?? 0),
                'wildcard_percentage' => (float)($_POST['wildcard_percentage'] ?? 10),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'created_by' => $this->auth->user()['id'] ?? null
            ];

            if (empty($data['title'])) {
                throw new Exception("Title is required");
            }

            $blueprintId = $this->blueprintService->createBlueprint($data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Blueprint created successfully',
                'redirect' => app_base_url('admin/quiz/blueprints/edit/' . $blueprintId)
            ]);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Edit blueprint and its rules
     */
    public function edit($id)
    {
        try {
            $blueprint = $this->blueprintService->getBlueprintWithRules($id);
            $syllabusTree = $this->syllabusService->getTree($blueprint['level']);

            $this->view->render('admin/quiz/blueprints/editor', [
                'page_title' => 'Edit Blueprint: ' . $blueprint['title'],
                'blueprint' => $blueprint,
                'syllabus_tree' => $syllabusTree
            ]);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . app_base_url('admin/quiz/blueprints'));
            exit;
        }
    }

    /**
     * Update blueprint
     */
    public function update($id)
    {
        try {
            $data = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'level' => $_POST['level'] ?? '',
                'total_questions' => (int)($_POST['total_questions'] ?? 50),
                'total_marks' => (int)($_POST['total_marks'] ?? 100),
                'duration_minutes' => (int)($_POST['duration_minutes'] ?? 60),
                'negative_marking_rate' => (float)($_POST['negative_marking_rate'] ?? 0),
                'wildcard_percentage' => (float)($_POST['wildcard_percentage'] ?? 10),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $this->blueprintService->updateBlueprint($id, $data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Blueprint updated successfully'
            ]);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Delete blueprint
     */
    public function delete($id)
    {
        try {
            $this->blueprintService->deleteBlueprint($id);
            $this->jsonResponse(['success' => true, 'message' => 'Blueprint deleted']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Add rule to blueprint
     */
    public function addRule($blueprintId)
    {
        try {
            $ruleData = [
                'syllabus_node_id' => (int)($_POST['syllabus_node_id'] ?? 0),
                'questions_required' => (int)($_POST['questions_required'] ?? 10),
                'difficulty_distribution' => $_POST['difficulty_distribution'] ?? null
            ];

            // Parse difficulty distribution if provided as JSON string
            if (is_string($ruleData['difficulty_distribution'])) {
                $ruleData['difficulty_distribution'] = json_decode($ruleData['difficulty_distribution'], true);
            }

            $ruleId = $this->blueprintService->addRule($blueprintId, $ruleData);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Rule added successfully',
                'rule_id' => $ruleId
            ]);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Update rule
     */
    public function updateRule($ruleId)
    {
        try {
            $ruleData = [
                'questions_required' => (int)($_POST['questions_required'] ?? 10),
                'difficulty_distribution' => $_POST['difficulty_distribution'] ?? null
            ];

            if (is_string($ruleData['difficulty_distribution'])) {
                $ruleData['difficulty_distribution'] = json_decode($ruleData['difficulty_distribution'], true);
            }

            $this->blueprintService->updateRule($ruleId, $ruleData);

            $this->jsonResponse(['success' => true, 'message' => 'Rule updated']);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Delete rule
     */
    public function deleteRule($ruleId)
    {
        try {
            $this->blueprintService->deleteRule($ruleId);
            $this->jsonResponse(['success' => true, 'message' => 'Rule deleted']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Validate blueprint
     */
    public function validate($blueprintId)
    {
        try {
            $validation = $this->blueprintService->validateBlueprint($blueprintId);
            $this->jsonResponse($validation);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Preview blueprint (show summary)
     */
    public function preview($blueprintId)
    {
        try {
            $summary = $this->blueprintService->getBlueprintSummary($blueprintId);
            $this->jsonResponse(['success' => true, 'summary' => $summary]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Generate exam from blueprint
     */
    public function generate($blueprintId)
    {
        try {
            $options = [
                'shuffle' => isset($_POST['shuffle']) ? (bool)$_POST['shuffle'] : true,
                'include_wildcard' => isset($_POST['include_wildcard']) ? (bool)$_POST['include_wildcard'] : true
            ];

            $generatedExam = $this->generatorService->generateFromBlueprint($blueprintId, $options);

            // Optionally save to database
            if (isset($_POST['save']) && $_POST['save']) {
                $examId = $this->generatorService->saveGeneratedExam($generatedExam, [
                    'title' => $_POST['exam_title'] ?? $generatedExam['blueprint_title']
                ]);

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Exam generated and saved',
                    'exam_id' => $examId,
                    'redirect' => app_base_url('admin/quiz/exams/edit/' . $examId)
                ]);
            } else {
                // Just return preview
                $this->jsonResponse([
                    'success' => true,
                    'exam' => $generatedExam
                ]);
            }

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Helper: JSON response
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
