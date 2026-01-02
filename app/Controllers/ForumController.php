<?php

namespace App\Controllers;

use App\Core\Controller;

class ForumController extends Controller
{
    public function index()
    {
        $this->view('shared/coming_soon', [
            'title' => 'Civil City Forum',
            'module_name' => 'Community Forum',
            'expected_date' => 'Q2 2026'
        ]);
    }
}
