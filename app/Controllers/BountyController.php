<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\BountyRequest;
use App\Models\BountySubmission;
use App\Models\User;

class BountyController extends Controller
{
    public function index()
    {
        $this->view('bounty/index', [
            'title' => 'Bounty Board - Civil City Freelance',
            'user' => Auth::user(),
            'coins' => (new User())->getCoins(Auth::id())
        ]);
    }

    public function create()
    {
        $this->view('bounty/create', [
            'title' => 'Post a Bounty',
            'user' => Auth::user(),
            'coins' => (new User())->getCoins(Auth::id())
        ]);
    }
    
    public function show($id)
    {
        $bountyModel = new BountyRequest();
        $bounty = $bountyModel->find($id);
        
        if (!$bounty) {
            http_response_code(404);
            echo "Bounty not found";
            return;
        }

        $userId = Auth::id();
        $isOwner = ($bounty->requester_id == $userId);
        
        // If owner, show submissions
        $submissions = [];
        if ($isOwner) {
            $submissionModel = new BountySubmission();
            $submissions = $submissionModel->getByBountyId($id);
        }

        $this->view('bounty/show', [
            'title' => $bounty->title,
            'bounty' => $bounty,
            'user' => Auth::user(),
            'isOwner' => $isOwner,
            'submissions' => $submissions
        ]);
    }

    public function dashboard()
    {
        $bountyModel = new BountyRequest();
        $myBounties = $bountyModel->getByUser(Auth::id());

        $this->view('bounty/dashboard', [
            'title' => 'My Bounties',
            'bounties' => $myBounties,
            'user' => Auth::user()
        ]);
    }
}
