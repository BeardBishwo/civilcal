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
        // Get categories for filtering
        $categories = $this->wordModel->getCategories();

        // View for the game container
        $this->view->render('quiz/games/guess_word', [
            'page_title' => 'Guess The Word',
            'user' => $_SESSION['user'],
            'categories' => $categories
        ], 'layouts/quiz_focus');
    }

    /**
     * API: Get a new word puzzle
     */
    public function getWord()
    {
        // Validate and sanitize difficulty (1-3)
        $difficulty = (int)($_GET['difficulty'] ?? 1);
        if ($difficulty < 1 || $difficulty > 3) {
            $difficulty = 1;
        }

        // Validate and sanitize category_id
        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        if ($categoryId !== null) {
            if ($categoryId <= 0) {
                $this->json(['error' => 'Invalid category ID']);
                return;
            }
            // Check if category exists and is active
            $categoryExists = $this->db->query("SELECT id FROM syllabus_nodes WHERE id = ? AND type = 'category' AND is_active = 1", [$categoryId])->fetch();
            if (!$categoryExists) {
                $this->json(['error' => 'Category not found']);
                return;
            }
        }

        // Get terms based on difficulty and category
        $terms = $this->wordModel->getRandomTerms(1, $difficulty, $categoryId);

        if (empty($terms)) {
            $this->json([
                'success' => false,
                'error' => 'No terms found for the selected criteria',
                'reason' => $categoryId ? 'empty_category' : 'no_data'
            ]);
            return;
        }

        $term = $terms[0];

        // Track usage (Priority 3)
        $this->wordModel->incrementUsage($term['id']);
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

        // Store answer in session for server-side validation (SECURE)
        $_SESSION['current_puzzle'] = [
            'answer' => $answer,
            'term_id' => $term['id'],
            'difficulty' => $difficulty,
            'category_id' => $categoryId,
            'created_at' => time()
        ];

        // Return Puzzle WITHOUT answer
        $this->json([
            'success' => true,
            'definition' => $definition,
            'length' => strlen($answer),
            'scrambled' => $chars,
            'puzzle_id' => md5($term['id'] . time()),
            'category' => $term['category_name'] ?? 'General'
        ]);
    }

    /**
     * API: Verify user's answer (Server-side validation)
     */
    public function checkAnswer()
    {
        $userAnswer = strtoupper(trim($_POST['answer'] ?? ''));

        // Get stored answer from session
        if (empty($_SESSION['current_puzzle'])) {
            $this->json(['success' => false, 'error' => 'No active puzzle']);
            return;
        }

        $storedAnswer = $_SESSION['current_puzzle']['answer'];
        $termId = $_SESSION['current_puzzle']['term_id'];
        $difficulty = $_SESSION['current_puzzle']['difficulty'];
        $userId = $_SESSION['user_id'] ?? null;

        // VALIDATE
        $isCorrect = ($userAnswer === $storedAnswer);

        if ($isCorrect) {
            // Calculate points based on difficulty
            $points = $difficulty * 10; // 10, 20, 30 for levels 1-3

            // Save progress
            if ($userId) {
                $this->wordModel->recordProgress($userId, $termId, true, $points);
            }

            // Clear session
            unset($_SESSION['current_puzzle']);

            $this->json([
                'success' => true,
                'correct' => true,
                'points' => $points,
                'answer' => $storedAnswer,
                'message' => 'Excellent! You got it right!'
            ]);
        } else {
            $this->json([
                'success' => true,
                'correct' => false,
                'answer' => $storedAnswer,
                'message' => 'Try again! That\'s not quite right.'
            ]);
        }
    }

    /**
     * API: Use a hint
     */
    public function useHint()
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (empty($_SESSION['current_puzzle'])) {
            $this->json(['success' => false, 'error' => 'No active puzzle']);
            return;
        }

        // Check if user has coins (assuming user model has coins)
        // For now, just return hint

        $answer = $_SESSION['current_puzzle']['answer'];

        // Hint: Reveal 50% of letters
        $chars = str_split($answer);
        $half = ceil(count($chars) / 2);
        $revealed = array_slice($chars, 0, $half);
        $hidden = array_fill(0, count($chars) - $half, '_');
        $hint = implode('', array_merge($revealed, $hidden));

        $this->json([
            'success' => true,
            'hint' => $hint,
            'cost' => 2,
            'message' => 'Hint used! First 50% of letters revealed.'
        ]);
    }

    /**
     * API: Get user progress/stats
     */
    public function getProgress()
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $stats = $this->wordModel->getUserStats($userId);

        $this->json([
            'success' => true,
            'stats' => [ // Wrapped in stats key to match frontend expectation
                'correct' => $stats['correct'] ?? 0,
                'wrong' => ($stats['total'] ?? 0) - ($stats['correct'] ?? 0),
                'total' => $stats['total'] ?? 0,
                'points' => $stats['points'] ?? 0,
                'accuracy' => $stats['total'] > 0 ? round(($stats['correct'] / $stats['total']) * 100, 1) : 0
            ],
            'coins' => $_SESSION['user']['coins'] ?? 10 // Assuming coins are in user session
        ]);
    }

    /**
     * API: Get all active categories
     */
    public function getCategories()
    {
        try {
            $categories = $this->wordModel->getCategories();
            $this->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Failed to fetch categories']);
        }
    }
}
