<?php

namespace App\Models;

use App\Core\Model;

class QuestionReport extends Model
{
    protected static $table = 'question_reports';

    protected static $fillable = [
        'question_id',
        'user_id',
        'issue_type',
        'description',
        'status', // pending, resolved, ignored
        'reply_message',
        'screenshot',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
