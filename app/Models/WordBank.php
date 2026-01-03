<?php

namespace App\Models;

use App\Core\Model;

class WordBank extends Model
{
    protected $table = 'word_bank';

    public function getRandomTerms($count = 5, $difficulty = null)
    {
        $params = [];
        $where = "1=1";
        
        if ($difficulty) {
            $where .= " AND difficulty_level = ?";
            $params[] = $difficulty;
        }

        return $this->db->query("SELECT * FROM {$this->table} WHERE {$where} ORDER BY RAND() LIMIT " . (int)$count, $params)->fetchAll();
    }
}
