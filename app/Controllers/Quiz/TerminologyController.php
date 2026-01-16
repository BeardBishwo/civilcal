<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Models\WordBank;
use App\Models\Blueprint;
use Exception;

class TerminologyController extends Controller
{
    private $wordModel;
    private $blueprintModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->wordModel = new WordBank();
        $this->blueprintModel = new Blueprint();
    }

    /**
     * Start the terminology matching game for a specific blueprint with progressive revelation
     */
    public function arena($blueprintId)
    {
        $blueprint = $this->blueprintModel->getBlueprintById($blueprintId);

        if (!$blueprint) {
            $_SESSION['flash_error'] = "Blueprint not found.";
            return $this->redirect('/blueprint');
        }

        // Get user's current progress
        $userProgress = $this->blueprintModel->getUserRevealedLayers($_SESSION['user_id'], $blueprintId);
        $totalSections = $blueprint['total_sections'];

        // Determine next section to unlock
        $nextSection = count($userProgress) + 1;

        if ($nextSection > $totalSections) {
            $_SESSION['flash_success'] = "You've completed this blueprint!";
            return $this->redirect('/blueprint');
        }

        // Get terms for the next section based on difficulty
        $difficulty = $blueprint['difficulty_level'];
        $terms = $this->wordModel->getRandomTerms(5, $difficulty);

        if (count($terms) < 5) {
            $_SESSION['flash_error'] = "Not enough terms available for this difficulty level.";
            return $this->redirect('/blueprint');
        }

        // Get educational content for this section
        $educationContent = $this->blueprintModel->getEducationalContent($blueprintId, $nextSection);

        $this->view->render('quiz/games/blueprint_reveal', [
            'blueprint' => $blueprint,
            'terms' => $terms,
            'currentSection' => $nextSection,
            'totalSections' => $totalSections,
            'revealedLayers' => $userProgress,
            'educationContent' => $educationContent,
            'completionPercentage' => $this->blueprintModel->calculateCompletionPercentage($blueprintId, $userProgress)
        ], 'layouts/quiz_focus');
    }

    /**
     * Submit terminology game progress and handle blueprint revelation
     */
    public function submit()
    {
        $blueprintId = $_POST['blueprint_id'] ?? '';
        $sectionId = (int)($_POST['section_id'] ?? 0);
        $correctMatches = (int)($_POST['correct_matches'] ?? 0);
        $totalTerms = (int)($_POST['total_terms'] ?? 0);
        $timeSpent = (int)($_POST['time_spent'] ?? 0);

        if (empty($blueprintId) || $sectionId < 1) {
            return $this->jsonResponse(['success' => false, 'error' => 'Invalid submission data']);
        }

        $blueprint = $this->blueprintModel->getBlueprintById($blueprintId);
        if (!$blueprint) {
            return $this->jsonResponse(['success' => false, 'error' => 'Blueprint not found']);
        }

        // Calculate section success (need 80% correct to pass)
        $sectionScore = $totalTerms > 0 ? ($correctMatches / $totalTerms) * 100 : 0;
        $sectionPassed = $sectionScore >= 80;

        if ($sectionPassed) {
            // Get current revealed layers
            $revealedLayers = $this->blueprintModel->getUserRevealedLayers($_SESSION['user_id'], $blueprintId);

            // Add this section to revealed layers if not already there
            if (!in_array($sectionId, $revealedLayers)) {
                $revealedLayers[] = $sectionId;
                sort($revealedLayers); // Keep sorted
            }

            // Update progress
            $completionPercentage = $this->blueprintModel->calculateCompletionPercentage($blueprintId, $revealedLayers);
            $this->blueprintModel->updateUserProgress($_SESSION['user_id'], $blueprintId, $revealedLayers, $completionPercentage);

            // Award coins if this is the first time completing this section
            $coinsAwarded = 0;
            if ($completionPercentage >= 100 && !in_array($blueprintId, $_SESSION['completed_blueprints'] ?? [])) {
                $this->blueprintModel->awardBlueprintCoins($_SESSION['user_id'], $blueprintId);
                $coinsAwarded = $blueprint['base_reward_coins'];

                // Mark as completed in session
                if (!isset($_SESSION['completed_blueprints'])) {
                    $_SESSION['completed_blueprints'] = [];
                }
                $_SESSION['completed_blueprints'][] = $blueprintId;
            }

            return $this->jsonResponse([
                'success' => true,
                'sectionPassed' => true,
                'sectionScore' => round($sectionScore, 1),
                'revealedLayers' => $revealedLayers,
                'completionPercentage' => $completionPercentage,
                'coinsAwarded' => $coinsAwarded,
                'nextSection' => count($revealedLayers) + 1,
                'totalSections' => $blueprint['total_sections'],
                'message' => $completionPercentage >= 100 ?
                    "Blueprint completed! +{$coinsAwarded} coins earned." :
                    "Section {$sectionId} unlocked! Continue to reveal more of the blueprint."
            ]);
        } else {
            // Section failed - provide educational feedback
            $educationContent = $this->blueprintModel->getEducationalContent($blueprintId, $sectionId);

            return $this->jsonResponse([
                'success' => true,
                'sectionPassed' => false,
                'sectionScore' => round($sectionScore, 1),
                'message' => "Section not passed (need 80% correct). Try again to unlock this blueprint section.",
                'educationalContent' => $educationContent,
                'canRetry' => true
            ]);
        }
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
