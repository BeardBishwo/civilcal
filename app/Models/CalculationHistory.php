<?php

namespace App\Models;

use App\Core\Database;

class CalculationHistory {
    protected $table = 'calculation_history';
    protected $fillable = [
        'user_id', 'calculator_type', 'calculation_title', 
        'input_data', 'result_data', 'is_favorite', 'tags'
    ];
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Helper method to decode JSON data
    public static function decodeJsonField($value) {
        return json_decode($value, true);
    }
    
    // Helper method to encode JSON data
    public static function encodeJsonField($value) {
        return json_encode($value);
    }
    
    // Get user's calculation history
    public static function getUserHistory($userId, $limit = 50) {
        $db = Database::getInstance();
        $stmt = $db->getPdo()->prepare("
            SELECT * FROM calculation_history 
            WHERE user_id = ? 
            ORDER BY calculation_date DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        $results = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($results as &$result) {
            $result['input_data'] = json_decode($result['input_data'], true);
            $result['result_data'] = json_decode($result['result_data'], true);
        }
        
        return $results;
    }
    
    // Search history
    public static function searchHistory($userId, $searchTerm) {
        $db = Database::getInstance();
        $stmt = $db->getPdo()->prepare("
            SELECT * FROM calculation_history 
            WHERE user_id = ? 
            AND (calculation_title LIKE ? OR calculator_type LIKE ? OR tags LIKE ?)
            ORDER BY calculation_date DESC
        ");
        
        $searchPattern = "%$searchTerm%";
        $stmt->execute([$userId, $searchPattern, $searchPattern, $searchPattern]);
        $results = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($results as &$result) {
            $result['input_data'] = json_decode($result['input_data'], true);
            $result['result_data'] = json_decode($result['result_data'], true);
        }
        
        return $results;
    }
    
    // Save calculation to history
    public function saveCalculation($userId, $calculatorType, $inputs, $results, $title = null) {
        $db = Database::getInstance();
        
        // Auto-generate title if not provided
        if (!$title) {
            $title = $calculatorType . ' Calculation - ' . date('M j, Y g:i A');
        }
        
        // Generate tags from inputs for better search
        $tags = [];
        foreach ($inputs as $key => $value) {
            if (is_string($value) && strlen($value) < 50 && !empty(trim($value))) {
                $tags[] = $key . ':' . trim($value);
            }
        }
        $tagsString = implode(',', $tags);
        
        $stmt = $db->getPdo()->prepare("
            INSERT INTO calculation_history 
            (user_id, calculator_type, calculation_title, input_data, result_data, tags) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $userId,
            $calculatorType,
            $title,
            json_encode($inputs),
            json_encode($results),
            $tagsString
        ]);
    }
    
    // Toggle favorite status
    public function toggleFavorite($id, $userId) {
        $db = Database::getInstance();
        $stmt = $db->getPdo()->prepare("
            UPDATE calculation_history 
            SET is_favorite = !is_favorite 
            WHERE id = ? AND user_id = ?
        ");
        
        if ($stmt->execute([$id, $userId])) {
            // Get the new favorite status
            $stmt = $db->getPdo()->prepare("
                SELECT is_favorite FROM calculation_history 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$id, $userId]);
            $result = $stmt->fetch();
            return $result['is_favorite'] ? true : false;
        }
        
        return false;
    }
    
    // Delete calculation
    public function deleteCalculation($id, $userId) {
        $db = Database::getInstance();
        $stmt = $db->getPdo()->prepare("
            DELETE FROM calculation_history 
            WHERE id = ? AND user_id = ?
        ");
        
        return $stmt->execute([$id, $userId]);
    }
    
    // Get calculation by ID
    public function getById($id, $userId) {
        $db = Database::getInstance();
        $stmt = $db->getPdo()->prepare("
            SELECT * FROM calculation_history 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$id, $userId]);
        $result = $stmt->fetch();
        
        if ($result) {
            $result['input_data'] = json_decode($result['input_data'], true);
            $result['result_data'] = json_decode($result['result_data'], true);
        }
        
        return $result;
    }
    
    // Get calculation statistics for user
    public function getUserStats($userId) {
        $db = Database::getInstance();
        
        // Total calculations
        $stmt = $db->getPdo()->prepare("
            SELECT COUNT(*) as total FROM calculation_history WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $total = $stmt->fetch()['total'];
        
        // Favorite calculations
        $stmt = $db->getPdo()->prepare("
            SELECT COUNT(*) as favorites FROM calculation_history 
            WHERE user_id = ? AND is_favorite = 1
        ");
        $stmt->execute([$userId]);
        $favorites = $stmt->fetch()['favorites'];
        
        // Most used calculator types
        $stmt = $db->getPdo()->prepare("
            SELECT calculator_type, COUNT(*) as count 
            FROM calculation_history 
            WHERE user_id = ? 
            GROUP BY calculator_type 
            ORDER BY count DESC 
            LIMIT 5
        ");
        $stmt->execute([$userId]);
        $topCalculators = $stmt->fetchAll();
        
        return [
            'total_calculations' => $total,
            'favorite_calculations' => $favorites,
            'top_calculators' => $topCalculators
        ];
    }
    
    // Get recent calculations
    public function getRecentCalculations($userId, $limit = 10) {
        return self::getUserHistory($userId, $limit);
    }
    
    // Get calculations by calculator type
    public function getByCalculatorType($userId, $calculatorType, $limit = 50) {
        $db = Database::getInstance();
        $stmt = $db->getPdo()->prepare("
            SELECT * FROM calculation_history 
            WHERE user_id = ? AND calculator_type = ?
            ORDER BY calculation_date DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $calculatorType, $limit]);
        $results = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($results as &$result) {
            $result['input_data'] = json_decode($result['input_data'], true);
            $result['result_data'] = json_decode($result['result_data'], true);
        }
        
        return $results;
    }
}
