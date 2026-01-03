<?php

namespace App\Models;

use App\Core\Model;

class BlueprintReveal extends Model
{
    protected $table = 'blueprint_reveals';

    public function getUserProgress($userId)
    {
        return $this->where(['user_id' => $userId]);
    }

    public function updateProgress($userId, $blueprintId, $percent)
    {
        $sql = "INSERT INTO {$this->table} (user_id, blueprint_id, revealed_percentage) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE revealed_percentage = GREATEST(revealed_percentage, VALUES(revealed_percentage))";
        return $this->db->query($sql, [$userId, $blueprintId, $percent]);
    }
}
