<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Core\View;

class QuizHomeController extends Controller
{
    /**
     * Display the Quiz Dashboard
     */
    public function index()
    {
        // 1. Fetch all quiz mode toggles from site_settings
        $db = Database::getInstance();
        $settings = $db->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_group = 'quiz_modes'")->fetchAll();

        $modes = [];
        foreach ($settings as $s) {
            $modes[$s['setting_key']] = $s['setting_value'];
        }

        // Defaults if settings missing
        $defaults = [
            'quiz_mode_daily' => 1,
            'quiz_mode_zone' => 1,
            'quiz_mode_contest' => 1,
            'quiz_mode_battle_1v1' => 0,
        ];

        $modes = array_merge($defaults, $modes);

        // 2. Render View
        $this->view('quiz/home', [
            'modes' => $modes,
            'title' => 'Quiz Dashboard | Bishwo Calculator'
        ]);
    }
}
