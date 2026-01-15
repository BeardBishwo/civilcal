<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class QuizZoneController extends Controller
{
    public function index()
    {
        $this->requireAuth();
        $user = Auth::user();

        // 1. Check if user has selected a course
        $db = Database::getInstance();
        $userData = $db->find('users', ['id' => $user->id]);

        if (empty($userData['selected_course_id'])) {
            $this->redirect('/quiz/setup');
            return;
        }

        // 2. Fetch Course and Education Level info
        $course = $db->find('syllabus_nodes', ['id' => $userData['selected_course_id']]);
        $eduLevel = $userData['selected_edu_level_id'] 
            ? $db->find('syllabus_nodes', ['id' => $userData['selected_edu_level_id']]) 
            : null;

        // 3. Fetch Syllabus Hierarchy
        // Get all nodes for this course (Papers, Categories, Units)
        $pdo = $db->getPdo();
        
        // Fetch Papers (top-level subjects under the course)
        $stmt = $pdo->prepare("
            SELECT * FROM syllabus_nodes 
            WHERE parent_id = ? AND type = 'paper' AND is_active = 1
            ORDER BY `order` ASC
        ");
        $stmt->execute([$userData['selected_course_id']]);
        $papers = $stmt->fetchAll();

        // For each paper, fetch categories and units
        foreach ($papers as &$paper) {
            // Fetch categories under this paper
            $stmt = $pdo->prepare("
                SELECT * FROM syllabus_nodes 
                WHERE parent_id = ? AND type = 'category' AND is_active = 1
                ORDER BY `order` ASC
            ");
            $stmt->execute([$paper['id']]);
            $paper['categories'] = $stmt->fetchAll();

            // For each category, fetch units
            foreach ($paper['categories'] as &$category) {
                $stmt = $pdo->prepare("
                    SELECT * FROM syllabus_nodes 
                    WHERE parent_id = ? AND type = 'unit' AND is_active = 1
                    ORDER BY `order` ASC
                ");
                $stmt->execute([$category['id']]);
                $category['units'] = $stmt->fetchAll();

                // For each unit, check progress and quiz availability
                foreach ($category['units'] as &$unit) {
                    // Check if user has completed this unit
                    $stmt = $pdo->prepare("
                        SELECT * FROM user_syllabus_progress 
                        WHERE user_id = ? AND syllabus_node_id = ?
                    ");
                    $stmt->execute([$user->id, $unit['id']]);
                    $progress = $stmt->fetch();

                    $unit['progress'] = $progress;
                    $unit['is_completed'] = $progress && $progress['is_completed'];
                    $unit['completion_percentage'] = $progress ? $progress['completion_percentage'] : 0;

                    // Check if there are quizzes for this unit
                    $stmt = $pdo->prepare("
                        SELECT COUNT(*) as quiz_count FROM quiz_exams 
                        WHERE syllabus_node_id = ? AND status = 'published'
                    ");
                    $stmt->execute([$unit['id']]);
                    $quizData = $stmt->fetch();
                    $unit['quiz_count'] = $quizData['quiz_count'];
                }
            }
        }

        // 4. Calculate overall progress
        $totalUnits = 0;
        $completedUnits = 0;
        foreach ($papers as $paper) {
            foreach ($paper['categories'] as $category) {
                foreach ($category['units'] as $unit) {
                    $totalUnits++;
                    if ($unit['is_completed']) {
                        $completedUnits++;
                    }
                }
            }
        }

        $overallProgress = $totalUnits > 0 ? round(($completedUnits / $totalUnits) * 100) : 0;

        $this->view('quiz/zone_list', [
            'course' => $course,
            'eduLevel' => $eduLevel,
            'papers' => $papers,
            'overallProgress' => $overallProgress,
            'totalUnits' => $totalUnits,
            'completedUnits' => $completedUnits,
            'title' => 'My Quiz Zone - ' . ($course['title'] ?? 'Learning Path')
        ]);
    }

    public function setup()
    {
        $this->requireAuth();
        $this->view('quiz/setup', [
            'title' => 'Setup Your Learning Path'
        ]);
    }

    public function saveSetup()
    {
        $this->requireAuth();
        $user = Auth::user();

        $courseId = $_POST['course_id'] ?? null;
        $eduLevelId = $_POST['edu_level_id'] ?? null;

        if (!$courseId || !$eduLevelId) {
            // Error handling
            $this->redirect('/quiz/setup');
        }

        $db = Database::getInstance();
        $db->update(
            'users',
            ['id' => $user->id],
            [
                'selected_course_id' => $courseId,
                'selected_edu_level_id' => $eduLevelId
            ]
        );

        $this->redirect('/quiz/zone');
    }
}
