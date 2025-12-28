<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class FavoritesController extends Controller
{
    /**
     * Get all favorites for the logged-in user
     */
    public function index()
    {
        $this->requireAuth();
        $db = Database::getInstance();
        $user_id = $_SESSION['user_id'];
        
        $favorites = $db->find('calc_favorites', ['user_id' => $user_id], 'created_at DESC');
        
        $this->json(['success' => true, 'favorites' => $favorites]);
    }

    /**
     * Toggle a favorite (Add/Remove)
     */
    public function toggle()
    {
        $this->requireAuth();
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['calculator_slug']) || !isset($input['calculator_name'])) {
            $this->json(['success' => false, 'message' => 'Missing required fields'], 400);
            return;
        }

        $db = Database::getInstance();
        $user_id = $_SESSION['user_id'];
        $slug = $input['calculator_slug'];
        $name = $input['calculator_name'];
        $category = $input['category'] ?? 'General';

        // Check if exists
        $existing = $db->findOne('calc_favorites', [
            'user_id' => $user_id,
            'calculator_slug' => $slug
        ]);

        if ($existing) {
            // Remove
            $db->delete('calc_favorites', "id = :id", ['id' => $existing['id']]);
            $this->json(['success' => true, 'action' => 'removed', 'message' => 'Removed from favorites']);
        } else {
            // Add
            $db->insert('calc_favorites', [
                'user_id' => $user_id,
                'calculator_slug' => $slug,
                'calculator_name' => $name,
                'category' => $category
            ]);
            $this->json(['success' => true, 'action' => 'added', 'message' => 'Added to favorites']);
        }
    }
    
    private function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            exit;
        }
    }
}
