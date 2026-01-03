<?php

namespace App\Models;

use App\Core\Model;

class SyllabusNode extends Model {
    protected static $table = 'syllabus_nodes';

    protected static $fillable = [
        'parent_id',
        'title',
        'slug',
        'type', // domain, part, section, subject, topic
        'description',
        'order_index',
        'is_active',
        'created_at',
        'updated_at'
    ];

    public function parent() {
        return $this->belongsTo(SyllabusNode::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(SyllabusNode::class, 'parent_id');
    }

    public function questions() {
        return $this->hasMany(Question::class, 'syllabus_node_id');
    }
}
