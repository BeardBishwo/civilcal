<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Sponsor;
use App\Models\Campaign;
use App\Core\Auth;
use App\Services\FileService;

class SponsorController extends Controller
{
    private $sponsorModel;
    private $campaignModel;

    public function __construct()
    {
        parent::__construct();

        // Ensure Admin
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            header('Location: /login');
            exit;
        }
        $this->sponsorModel = new Sponsor();
        $this->campaignModel = new Campaign();
    }

    public function index()
    {
        $sponsors = $this->sponsorModel->getAll();
        $this->view('admin/sponsors/index', ['sponsors' => $sponsors]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'website_url' => $_POST['website_url'],
                'contact_person' => $_POST['contact_person'],
                'contact_email' => $_POST['contact_email']
            ];

            // Handle Logo Upload (Paranoid-Grade)
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $upload = FileService::uploadAdminFile($_FILES['logo'], 'logo');
                if ($upload['success']) {
                    $data['logo_path'] = $upload['filename'];
                }
            }

            $this->sponsorModel->create($data);
            header('Location: /admin/sponsors');
            exit;
        }
    }

    public function createCampaign()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'sponsor_id' => $_POST['sponsor_id'],
                'calculator_slug' => $_POST['calculator_slug'],
                'title' => $_POST['title'],
                'ad_text' => $_POST['ad_text'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'priority' => $_POST['priority'] ?? 0,
                'max_impressions' => $_POST['max_impressions'] ?? 0
            ];

            $this->campaignModel->create($data);
            header('Location: /admin/sponsors'); // Redirect back for now
            exit;
        }
    }
}
