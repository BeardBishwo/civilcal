<?php

namespace App\Controllers;

use App\Core\Controller;

class LegalController extends Controller
{
    public function privacy()
    {
        $this->view('legal/privacy', ['title' => 'Privacy Policy']);
    }

    public function terms()
    {
        $this->view('legal/terms', ['title' => 'Terms of Service']);
    }

    public function refund()
    {
        $this->view('legal/refund', ['title' => 'Refund Policy']);
    }
}
