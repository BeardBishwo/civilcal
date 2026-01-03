<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Models\WordBank;
use App\Models\BlueprintReveal;
use Exception;

class TerminologyController extends Controller
{
    private $wordModel;
    private $revealModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->wordModel = new WordBank();
        $this->revealModel = new BlueprintReveal();
    }

    /**
     * Entry point for Blueprint Builder
     */
    public function index()
    {
        $blueprints = [
            [
                'id' => 'beam_structure',
                'title' => 'Structural Beam Layout',
                'difficulty' => 1,
                'reward' => 50,
                'image' => '/themes/basic/assets/img/blueprints/beam_preview.svg'
            ],
            [
                'id' => 'concrete_slab',
                'title' => 'Reinforced Concrete Slab',
                'difficulty' => 2,
                'reward' => 100,
                'image' => '/themes/basic/assets/img/blueprints/slab_preview.svg'
            ]
        ];

        $progress = $this->revealModel->getUserProgress($_SESSION['user_id']);
        $progressMap = [];
        foreach($progress as $p) $progressMap[$p['blueprint_id']] = $p['revealed_percentage'];

        $this->view->render('quiz/games/blueprint_list', [
            'page_title' => 'Architect' . "'" . 's Studio',
            'blueprints' => $blueprints,
            'progress' => $progressMap
        ]);
    }

    /**
     * Start the terminology matching game
     */
    public function arena($id)
    {
        $difficulty = ($id == 'beam_structure') ? 1 : 2;
        $terms = $this->wordModel->getRandomTerms(5, $difficulty);

        if (count($terms) < 5) {
            $_SESSION['flash_error'] = "Not enough terms in the bank for this blueprint.";
            return $this->redirect('/blueprint');
        }

        $this->view->render('quiz/games/blueprint_arena', [
            'blueprint_id' => $id,
            'terms' => $terms
        ], 'layouts/quiz_focus');
    }

    /**
     * Submit progress
     */
    public function submit()
    {
        $blueprintId = $_POST['blueprint_id'];
        $correctMatches = (int)$_POST['correct_matches'];
        $totalTerms = (int)$_POST['total_terms'];

        $percent = floor(($correctMatches / $totalTerms) * 100);
        
        $this->revealModel->updateProgress($_SESSION['user_id'], $blueprintId, $percent);

        return $this->jsonResponse(['success' => true, 'percent' => $percent]);
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
