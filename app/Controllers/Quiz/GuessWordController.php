<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Models\WordBank;

class GuessWordController extends Controller
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
        // View for the game container
        $this->view->render('quiz/games/guess_word', [
            'page_title' => 'Guess The Word',
            'user' => $_SESSION['user']
        ], 'layouts/quiz_focus');
    }

    /**
     * API: Get a new word puzzle
     */
    public function getWord()
    {
        // Difficulty 1-3. Can be passed via GET
        $difficulty = (int)($_GET['difficulty'] ?? 1);

        // Fetch 1 term
        $terms = $this->wordModel->getRandomTerms(1, $difficulty);

        if (empty($terms)) {
            $this->json(['error' => 'No terms found']);
            return;
        }

        $term = $terms[0];
        $answer = strtoupper($term['term']);
        $definition = $term['definition'];

        // SCRAMBLE LOGIC
        // 1. Get answer chars
        $chars = str_split($answer);

        // 2. Add distractor chars (random A-Z)
        $distractorsNeeded = 12 - count($chars); // Target 12 buttons
        if ($distractorsNeeded < 0) $distractorsNeeded = 0;

        $alphabet = range('A', 'Z');
        for ($i = 0; $i < $distractorsNeeded; $i++) {
            $chars[] = $alphabet[array_rand($alphabet)];
        }

        // 3. Shuffle
        shuffle($chars);

        // Return Puzzle
        // HIDE Answer, but send hash/id for validation
        // OR send encrypted answer? Ideally server verifies.
        // For simplicity in this game loop, we can send the answer if we trust client (Not secure for high stakes)
        // Better: Store answer in Session or return simple hash.

        // For this "Educational" mode, client-side validation is acceptable for responsiveness.
        // We will send specific structure.

        $this->json([
            'success' => true,
            'definition' => $definition,
            'length' => strlen($answer),
            'scrambled' => $chars,
            'hash' => md5($answer . 'SALT'), // Update this if needed
            'answer_debug' => $answer // Remove in production if sensitive
        ]);
    }
}
