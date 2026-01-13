<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\Quiz\DailyQuizService;

class DailyQuizController extends Controller
{

    protected $service;
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->service = new DailyQuizService();
        $this->db = Database::getInstance();
    }

    /**
     * View Calendar of Scheduled Quizzes
     */
    public function index()
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));

        // Fetch Schedule
        $sql = "SELECT s.*, n.title as stream_title, e.title as edu_level_title 
                FROM daily_quiz_schedule s 
                LEFT JOIN syllabus_nodes n ON s.target_stream_id = n.id 
                LEFT JOIN syllabus_nodes e ON s.target_edu_level_id = e.id 
                WHERE s.date BETWEEN ? AND ? 
                ORDER BY s.date ASC, s.target_stream_id ASC, s.target_edu_level_id ASC";

        $schedule = $this->db->query($sql, [$startDate, $endDate])->fetchAll();

        // Group by Date for Calendar View
        $calendar = [];
        foreach ($schedule as $item) {
            $calendar[$item['date']][] = $item;
        }

        $this->view->render('admin/quiz/daily/index', [
            'page_title' => 'Daily Quest Scheduler',
            'calendar' => $calendar,
            'start_date' => $startDate
        ]);
    }

    /**
     * Manually Trigger Auto-Generation
     */
    public function generate()
    {
        try {
            $this->service->autoGenerateWeek();
            echo json_encode(['status' => 'success', 'message' => 'Successfully generated quizzes for the next 7 days.']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
