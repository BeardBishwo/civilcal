<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class BlogController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * Show single blog post by slug (individual question or collection)
     */
    public function show($slug)
    {
        // Check if it's a collection post first
        $collection = $this->db->query("
            SELECT * FROM blog_posts 
            WHERE slug = :slug 
            AND is_published = 1
        ", ['slug' => $slug])->fetch();
        
        if ($collection) {
            return $this->showCollection($slug);
        }
        
        // Otherwise, show individual question
        $question = $this->db->query("
            SELECT q.*, 
                   c.title as category_name,
                   c.slug as category_slug
            FROM quiz_questions q
            LEFT JOIN syllabus_nodes c ON q.category_id = c.id
            WHERE q.slug = :slug 
            AND q.is_published_as_blog = 1
            AND q.is_active = 1
        ", ['slug' => $slug])->fetch();
        
        if (!$question) {
            http_response_code(404);
            echo "Question not found";
            return;
        }
        
        // Increment view count
        $this->db->query("
            UPDATE quiz_questions 
            SET view_count = view_count + 1 
            WHERE id = :id
        ", ['id' => $question['id']]);
        
        // Get related questions (same category)
        $related = $this->db->query("
            SELECT id, slug, content, default_marks, theory_type
            FROM quiz_questions
            WHERE category_id = :cat_id
            AND type = 'THEORY'
            AND is_published_as_blog = 1
            AND id != :current_id
            ORDER BY view_count DESC
            LIMIT 10
        ", [
            'cat_id' => $question['category_id'],
            'current_id' => $question['id']
        ])->fetchAll();
        
        // Parse content
        $content = json_decode($question['content'], true);
        $question['question_text'] = $content['text'] ?? '';
        $question['options'] = $content['options'] ?? [];
        $question['correct'] = $content['correct'] ?? null;
        
        // Calculate reading time (200 words per minute)
        $wordCount = str_word_count($question['question_text'] . ' ' . ($question['answer_explanation'] ?? ''));
        $readingTime = max(1, ceil($wordCount / 200));
        
        // Render view
        $this->view('blog/question-post', [
            'question' => $question,
            'related' => $related,
            'readingTime' => $readingTime,
            'page_title' => substr($question['question_text'], 0, 60) . ' - PSC Question',
            'meta_description' => substr($question['question_text'], 0, 155)
        ]);
    }
    
    /**
     * Show collection blog post
     */
    public function showCollection($slug)
    {
        $post = $this->db->query("
            SELECT * FROM blog_posts 
            WHERE slug = :slug 
            AND is_published = 1
        ", ['slug' => $slug])->fetch();
        
        if (!$post) {
            http_response_code(404);
            echo "Post not found";
            return;
        }
        
        // Get questions
        $questionIds = json_decode($post['question_ids'], true);
        
        if (empty($questionIds)) {
            $questions = [];
        } else {
            $placeholders = implode(',', array_fill(0, count($questionIds), '?'));
            $questions = $this->db->query("
                SELECT * FROM quiz_questions 
                WHERE id IN ($placeholders)
            ", $questionIds)->fetchAll();
        }
        
        // Increment view count
        $this->db->query("
            UPDATE blog_posts 
            SET view_count = view_count + 1 
            WHERE id = :id
        ", ['id' => $post['id']]);
        
        // Render view
        $this->view('blog/collection-post', [
            'post' => $post,
            'questions' => $questions,
            'page_title' => $post['title'],
            'meta_description' => $post['meta_description'] ?? substr($post['title'], 0, 155)
        ]);
    }
}
