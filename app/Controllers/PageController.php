<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ContentService;

class PageController extends Controller
{
    public function show($slug)
    {
        $svc = new ContentService();
        $page = $svc->getPage($slug);
        if (!$page) {
            http_response_code(404);
            $this->view->render('errors/404', ['title' => 'Not Found']);
            return;
        }

        // Fix: Draft Visibility Logic
        if (($page['status'] ?? 'published') !== 'published') {
            $user = \App\Core\Auth::user();
            if (!$user || !$user->is_admin) {
                http_response_code(404); // Hide existence of drafts
                $this->view->render('errors/404', ['title' => 'Not Found']);
                return;
            }
        }
        $this->view->render('pages/page', ['page' => $page, 'title' => $page['title']]);
    }
}
