<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\BountySubmission;

class BountyController extends Controller
{
    public function index()
    {
        // For Admin View
        // Typically Admin views are separate. I'll load 'bounty/requests' and assume the admin theme wrapper handles it or is applied.
        // In this framework, it seems I just call view() and the router/middleware ensures admin access.
        
        $this->view('bounty/requests'); 
    }
}
