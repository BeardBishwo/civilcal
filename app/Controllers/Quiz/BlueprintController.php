<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Models\Blueprint;
use App\Models\BlueprintReveal;
use Exception;

class BlueprintController extends Controller
{
    private $blueprintModel;
    private $revealModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->blueprintModel = new Blueprint();
        $this->revealModel = new BlueprintReveal();
    }

    /**
     * Entry point for Blueprint Builder - Show all available blueprints
     */
    public function index()
    {
        $blueprints = $this->blueprintModel->getAllBlueprints();
        $progress = $this->revealModel->getUserProgress($_SESSION['user_id']);

        // Create progress map for easy lookup
        $progressMap = [];
        foreach($progress as $p) {
            $progressMap[$p['blueprint_id']] = $p['revealed_percentage'];
        }

        $this->view->render('quiz/games/blueprint_list', [
            'page_title' => 'Architect\'s Studio',
            'blueprints' => $blueprints,
            'progress' => $progressMap
        ]);
    }

    /**
     * Start a blueprint learning session
     * This redirects to the terminology game with blueprint context
     */
    public function start($blueprintId)
    {
        $blueprint = $this->blueprintModel->getBlueprintById($blueprintId);

        if (!$blueprint) {
            $_SESSION['flash_error'] = "Blueprint not found.";
            return $this->redirect('/blueprint');
        }

        // Redirect to terminology arena with blueprint context
        return $this->redirect('/blueprint/arena/' . $blueprintId);
    }

    /**
     * Submit blueprint progress
     * This handles the completion of a blueprint learning session
     */
    public function submit()
    {
        $blueprintId = $_POST['blueprint_id'] ?? '';
        $correctMatches = (int)($_POST['correct_matches'] ?? 0);
        $totalTerms = (int)($_POST['total_terms'] ?? 0);

        if (empty($blueprintId)) {
            return $this->jsonResponse(['success' => false, 'error' => 'Blueprint ID required']);
        }

        $percent = $totalTerms > 0 ? floor(($correctMatches / $totalTerms) * 100) : 0;

        $this->revealModel->updateProgress($_SESSION['user_id'], $blueprintId, $percent);

        return $this->jsonResponse([
            'success' => true,
            'percent' => $percent,
            'blueprint_id' => $blueprintId
        ]);
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}