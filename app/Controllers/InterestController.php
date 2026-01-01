<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use Exception;

class InterestController extends BaseController
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index() {
        // Show Onboarding UI
        // In a real app, you might check if they already have interests and redirect
        $this->view('onboarding/index');
    }

    public function getCategories() {
        $stmt = $this->db->getPdo()->query("SELECT id, name, slug, icon FROM quiz_categories ORDER BY name ASC");
        $categories = $stmt->fetchAll();
        $this->json(['success' => true, 'categories' => $categories]);
    }

    public function saveInterests() {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $input = json_decode(file_get_contents('php://input'), true);
            $categoryIds = $input['categories'] ?? [];
            $identity = $input['identity'] ?? 'student'; // student, professional, etc.

            if (empty($categoryIds)) throw new Exception('Please select at least one interest.');

            $pdo = $this->db->getPdo();
            $pdo->beginTransaction();

            // 1. Clear existing interests (or merge? let's clear for now to "Update")
            $stmt = $pdo->prepare("DELETE FROM user_interests WHERE user_id = ?");
            $stmt->execute([$user['id']]);

            // 2. Insert new
            $insert = $pdo->prepare("INSERT INTO user_interests (user_id, category_id) VALUES (?, ?)");
            foreach ($categoryIds as $catId) {
                $insert->execute([$user['id'], $catId]);
            }
            
            // 3. Update User Identity/Rank Title if suitable (using existing 'rank_title' field or 'professional_title')
            // Using 'professional_title' field on user profile for identity
            $updUser = $pdo->prepare("UPDATE users SET professional_title = ? WHERE id = ?");
            $updUser->execute([ucfirst($identity), $user['id']]);

            $pdo->commit();
            $this->json(['success' => true, 'message' => 'Interests saved!']);

        } catch (Exception $e) {
            $this->db->getPdo()->rollBack();
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function getFeed() {
        try {
            $user = Auth::user();
            $pdo = $this->db->getPdo();
            
            // Default: Trending/General if no user or no interests
            $categoryIds = [];
            if ($user) {
                $stmt = $pdo->prepare("SELECT category_id FROM user_interests WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $categoryIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            }

            if (empty($categoryIds)) {
                // Fallback: Get General or Random trending
                $sql = "SELECT * FROM quiz_questions ORDER BY RAND() LIMIT 10"; 
                $questions = $pdo->query($sql)->fetchAll();
                $this->json(['success' => true, 'feed_type' => 'trending', 'data' => $questions]);
                return;
            }

            // Interest-Based Logic
            // 60% Inteests, 20% Trending, 20% Challenge?
            // Simplified: Just Get Questions matching Categories
            $inQuery = implode(',', array_fill(0, count($categoryIds), '?'));
            
            $sql = "
                SELECT q.*, c.name as category_name 
                FROM quiz_questions q
                LEFT JOIN quiz_categories c ON q.category_id = c.id
                WHERE q.category_id IN ($inQuery)
                ORDER BY RAND() 
                LIMIT 20
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($categoryIds);
            $questions = $stmt->fetchAll();

            $this->json(['success' => true, 'feed_type' => 'personalized', 'data' => $questions]);

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
