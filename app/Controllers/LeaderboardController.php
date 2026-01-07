<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class LeaderboardController extends Controller
{
    public function index()
    {
        $limit = 50;
        
        // Fetch Top 50 Users by XP
        $topUsers = $this->db->query("
            SELECT id, username, first_name, last_name, avatar, xp, 
                   rank_title, quiz_solved_count, created_at
            FROM users 
            WHERE is_active = 1 
            ORDER BY xp DESC 
            LIMIT $limit
        ")->fetchAll();

        // Get Current User Rank if logged in
        $currentUserRank = null;
        $currentUserData = null;
        
        if ($userId = ($this->auth->id() ?? $_SESSION['user_id'] ?? null)) {
            // Calculate rank efficiently
            // Count users with more XP than current user
            $user = $this->db->findOne('users', ['id' => $userId]);
            if ($user) {
                $rankStmt = $this->db->query("SELECT COUNT(*) as rank FROM users WHERE xp > ? AND is_active = 1", [$user['xp']]);
                $rank = $rankStmt->fetchColumn() + 1;
                
                $currentUserRank = $rank;
                $currentUserData = $user;
            }
        }

        $this->view('leaderboard/index', [
            'topUsers' => $topUsers,
            'currentUserRank' => $currentUserRank,
            'currentUserData' => $currentUserData,
            'page_title' => 'Global Leaderboard'
        ]);
    }
}
