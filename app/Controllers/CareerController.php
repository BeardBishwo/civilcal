<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;
use Exception;

class CareerController extends BaseController
{
    private $ranks = [
        'Intern' => 0,
        'Surveyor' => 500,
        'Site Supervisor' => 2000,
        'Assistant Engineer' => 5000,
        'Senior Engineer' => 15000,
        'Project Manager' => 50000,
        'Chief Engineer' => 100000
    ];

    public function switchMode()
    {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $input = json_decode(file_get_contents('php://input'), true);
            $mode = $input['mode'] ?? 'psc';

            $userModel = new User();
            if ($userModel->setStudyMode($user['id'], $mode)) {
                $this->json(['success' => true, 'mode' => $mode]);
            } else {
                throw new Exception('Failed to update mode');
            }

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function checkPromotion()
    {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);

            $currentXp = $user['xp'] ?? 0;
            $currentRank = $user['rank_title'] ?? 'Intern';
            
            $nextRank = $this->getNextRank($currentRank);
            if (!$nextRank) {
                $this->json(['success' => false, 'message' => 'Max rank reached!']);
                return;
            }

            $requiredXp = $this->ranks[$nextRank];
            
            if ($currentXp >= $requiredXp) {
                $this->json([
                    'success' => true, 
                    'eligible' => true, 
                    'next_rank' => $nextRank,
                    'cost' => 100 // Exam fee
                ]);
            } else {
                $this->json([
                    'success' => true, 
                    'eligible' => false, 
                    'current_xp' => $currentXp,
                    'required_xp' => $requiredXp
                ]);
            }

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function promote()
    {
        try {
            $user = Auth::user();
            if (!$user) throw new Exception('Unauthorized', 401);
            
            // In a real scenario, this would check if they passed the exam.
            // For now, we simulate the promotion if they meet XP requirements and pay coins.
            
            $userModel = new User();
            $currentXp = $user['xp'] ?? 0;
            $currentRank = $user['rank_title'] ?? 'Intern';
            $nextRank = $this->getNextRank($currentRank);
            
            if (!$nextRank) throw new Exception('Max rank.');
            if ($currentXp < $this->ranks[$nextRank]) throw new Exception('Not enough XP.');
            
            // Deduct cost
            if (!$userModel->deductCoins($user['id'], 100, 'Promotion Exam Fee')) {
                 throw new Exception('Insufficient Coins (100 required).');
            }
            
            // Promote
            $userModel->updateRank($user['id'], $nextRank);
            
            $_SESSION['just_promoted'] = true;
            
            $this->json(['success' => true, 'new_rank' => $nextRank, 'message' => "Promoted to $nextRank!"]);

        } catch (Exception $e) {
             $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function getNextRank($current)
    {
        $keys = array_keys($this->ranks);
        $search = array_search($current, $keys);
        if ($search !== false && isset($keys[$search + 1])) {
            return $keys[$search + 1];
        }
        return null;
    }
}
