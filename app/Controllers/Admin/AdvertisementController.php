<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Advertisement;

class AdvertisementController extends Controller
{
    protected $adModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin(); // Function from parent Controller to ensure auth
        $this->adModel = new Advertisement();
    }

    public function index()
    {
        $ads = $this->adModel->getAll();
        $this->view('admin/advertisements/index', ['ads' => $ads]);
    }

    public function create()
    {
        $this->view('admin/advertisements/form');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'location' => $_POST['location'] ?? '',
                'code' => $_POST['code'] ?? '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->adModel->create($data)) {
                $this->redirect('/admin/advertisements?success=created');
            } else {
                $this->redirect('/admin/advertisements/create?error=failed');
            }
        }
    }

    public function edit($id)
    {
        $ad = $this->adModel->find($id);
        if (!$ad) {
            $this->redirect('/admin/advertisements');
        }
        $this->view('admin/advertisements/form', ['ad' => $ad]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'location' => $_POST['location'] ?? '',
                'code' => $_POST['code'] ?? '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->adModel->update($id, $data)) {
                $this->redirect('/admin/advertisements?success=updated');
            } else {
                $this->redirect("/admin/advertisements/edit/$id?error=failed");
            }
        }
    }

    public function delete($id)
    {
        if ($this->adModel->delete($id)) {
            $this->redirect('/admin/advertisements?success=deleted');
        } else {
            $this->redirect('/admin/advertisements?error=delete_failed');
        }
    }

    public function toggle($id)
    {
        $this->adModel->toggleStatus($id);
        $this->redirect('/admin/advertisements');
    }
}
