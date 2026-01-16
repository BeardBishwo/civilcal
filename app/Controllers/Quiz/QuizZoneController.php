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
        if (!$user) return;

        $db = Database::getInstance();
        $userData = $db->findOne('users', ['id' => $user->id]);

        if (empty($userData['selected_course_id'])) {
            $this->redirect('/quiz/setup');
            return;
        }

        // 1. Fetch Course and Education Level info
        $course = $db->findOne('syllabus_nodes', ['id' => $userData['selected_course_id']]);
        $eduLevel = !empty($userData['selected_edu_level_id'])
            ? $db->findOne('syllabus_nodes', ['id' => $userData['selected_edu_level_id']])
            : null;

        // 3. Fetch Syllabus Hierarchy
        // Primary Hierarchy in DB: Category -> Sub Category -> Topic
        $db_categories = $db->fetchAll("SELECT * FROM syllabus_nodes WHERE parent_id = ? AND type = 'category' AND is_active = 1", [$userData['selected_course_id']]);

        $papers = [];
        if (!empty($db_categories)) {
            foreach ($db_categories as $db_cat) {
                $paper = [
                    'id' => $db_cat['id'],
                    'title' => $db_cat['title'],
                    'categories' => []
                ];

                // Fetch Sub Categories as 'UI Categories'
                $sub_cats = $db->fetchAll("SELECT * FROM syllabus_nodes WHERE parent_id = ? AND type = 'sub_category' AND is_active = 1", [$db_cat['id']]);

                if (empty($sub_cats)) {
                    // Fallback: Check for questions directly under this category
                    $qCount = $db->fetch("SELECT COUNT(*) as total FROM quiz_questions WHERE category_id = ? AND status = 'approved'", [$db_cat['id']])['total'] ?? 0;
                    $paper['categories'][] = [
                        'id' => $db_cat['id'],
                        'title' => 'General',
                        'units' => [[
                            'id' => $db_cat['id'],
                            'title' => 'Introduction to ' . $db_cat['title'],
                            'quiz_count' => $qCount,
                            'is_completed' => false,
                            'completion_percentage' => 0
                        ]]
                    ];
                } else {
                    foreach ($sub_cats as $sc) {
                        $category = [
                            'id' => $sc['id'],
                            'title' => $sc['title'],
                            'units' => []
                        ];

                        // Fetch Topics as 'UI Units'
                        $topics = $db->fetchAll("SELECT * FROM syllabus_nodes WHERE parent_id = ? AND type = 'topic' AND is_active = 1", [$sc['id']]);
                        foreach ($topics as $t) {
                            $progress = $db->findOne('user_syllabus_progress', ['user_id' => $user->id, 'syllabus_node_id' => $t['id']]);
                            // Note: We check both topic_id and sub_category_id as backup
                            $qCount = $db->fetch("SELECT COUNT(*) as total FROM quiz_questions WHERE (topic_id = ? OR sub_category_id = ?) AND status = 'approved'", [$t['id'], $t['id']])['total'] ?? 0;
                            $category['units'][] = [
                                'id' => $t['id'],
                                'title' => $t['title'],
                                'description' => $t['description'] ?? '',
                                'progress' => $progress,
                                'is_completed' => $progress && $progress['is_completed'],
                                'completion_percentage' => $progress ? ($progress['score'] / 100) * 100 : 0,
                                'quiz_count' => $qCount
                            ];
                        }

                        if (empty($category['units'])) {
                            $qCount = $db->fetch("SELECT COUNT(*) as total FROM quiz_questions WHERE sub_category_id = ? AND status = 'approved'", [$sc['id']])['total'] ?? 0;
                            $category['units'][] = [
                                'id' => $sc['id'],
                                'title' => 'Core concepts',
                                'quiz_count' => $qCount,
                                'is_completed' => false,
                                'completion_percentage' => 0
                            ];
                        }
                        $paper['categories'][] = $category;
                    }
                }
                $papers[] = $paper;
            }
        } else {
            // Flatest fallback
            $papers[] = [
                'id' => 0,
                'title' => 'Curriculum Overview',
                'categories' => [[
                    'id' => 0,
                    'title' => 'General Topics',
                    'units' => [[
                        'id' => $userData['selected_course_id'],
                        'title' => 'Subject Fundamentals',
                        'quiz_count' => $db->fetch("SELECT COUNT(*) as total FROM quiz_questions WHERE course_id = ? AND status = 'approved'", [$userData['selected_course_id']])['total'] ?? 0,
                        'is_completed' => false,
                        'completion_percentage' => 0
                    ]]
                ]]
            ];
        }

        // 4. Calculate overall progress
        $totalUnits = 0;
        $completedUnits = 0;
        foreach ($papers as $paper) {
            foreach ($paper['categories'] as $category) {
                foreach ($category['units'] as $unit) {
                    $totalUnits++;
                    if (!empty($unit['is_completed'])) $completedUnits++;
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
        $db = Database::getInstance();

        $courses = $db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course'")->fetchAll();
        $eduLevels = $db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'education_level'")->fetchAll();

        $this->view('quiz/setup', [
            'title' => 'Setup Your Learning Path',
            'courses' => $courses,
            'edu_levels' => $eduLevels
        ]);
    }

    public function saveSetup()
    {
        $this->requireAuth();
        $user = Auth::user();

        $courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;
        $eduLevelId = isset($_POST['edu_level_id']) ? (int)$_POST['edu_level_id'] : null;

        if (!$courseId || !$eduLevelId) {
            $this->redirect('/quiz/setup');
            return;
        }

        try {
            $db = Database::getInstance();
            $course = $db->findOne('syllabus_nodes', ['id' => $courseId]);
            $eduLevel = $db->findOne('syllabus_nodes', ['id' => $eduLevelId]);

            if (!$course || !$eduLevel) {
                $this->redirect('/quiz/setup');
                return;
            }

            // Update user preferences
            $db->update(
                'users',
                [
                    'selected_course_id' => $courseId,
                    'selected_edu_level_id' => $eduLevelId,
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                'id = :id',
                ['id' => $user->id]
            );

            $this->redirect('/quiz/zone');
        } catch (\Exception $e) {
            error_log("Setup Save Error: " . $e->getMessage());
            $this->redirect('/quiz/setup');
        }
    }
}
