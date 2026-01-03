<?php

namespace App\Models;

use App\Core\Model;

class Question extends Model {
    protected static $table = 'quiz_questions';

    protected static $fillable = [
        'unique_code',
        'topic_id',
        'syllabus_node_id',
        'type',
        'content',
        'options',
        'correct_answer',
        'correct_answer_json',
        'explanation',
        'difficulty_level',
        'default_marks',
        'default_negative_marks',
        'tags',
        'is_active',
        'created_by',
        'content_hash'
    ];

    public function reports() {
        return $this->hasMany(QuestionReport::class, 'question_id');
    }

    public function topic() {
        return $this->belongsTo(SyllabusNode::class, 'topic_id'); // Assuming topic_id links to a SyllabusNode
    }
}
