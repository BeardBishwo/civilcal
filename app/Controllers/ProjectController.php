<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;

class ProjectController extends Controller
{
    protected $projectModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->projectModel = new Project();
    }

    public function index()
    {
        $userId = $_SESSION['user']['id'];
        $projects = $this->projectModel->getAll($userId);

        $this->view('projects/index', [
            'projects' => $projects,
            'title' => 'My Projects'
        ]);
    }

    public function show($id)
    {
        $userId = $_SESSION['user']['id'];
        $project = $this->projectModel->find($id, $userId);

        if (!$project) {
            $this->redirect('/projects?error=not_found');
        }

        $calculations = $this->projectModel->getCalculations($id, $userId);

        $this->view('projects/view', [
            'project' => $project,
            'calculations' => $calculations,
            'title' => $project['name']
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // CSRF Check
            if (!\App\Services\Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token');
            }

            $data = [
                'user_id' => $_SESSION['user']['id'],
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? '')
            ];

            if (empty($data['name'])) {
                $this->redirect('/projects?error=name_required');
            }

            if ($this->projectModel->create($data)) {
                $this->redirect('/projects?success=created');
            } else {
                $this->redirect('/projects?error=failed');
            }
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // CSRF Check
            if (!\App\Services\Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token');
            }

            $userId = $_SESSION['user']['id'];
            if ($this->projectModel->delete($id, $userId)) {
                $this->redirect('/projects?success=deleted');
            } else {
                $this->redirect('/projects?error=failed');
            }
        }
    }
}
