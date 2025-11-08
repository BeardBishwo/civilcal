<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = $this->getAllModules();
        $categories = $this->getModuleCategories();
        
        // Prepare data for the view
        $this->data['currentPage'] = 'modules';
        $this->data['modules'] = $modules;
        $this->data['categories'] = $categories;
        $this->data['title'] = 'Modules Management - Admin Panel';
        
        // Load the view
        $this->loadView('admin/modules/index', $this->data);
    }

    private function getAllModules()
    {
        // Mock data for now
        return [
            [
                'id' => 1,
                'name' => 'Civil Engineering',
                'category' => 'engineering',
                'description' => 'Civil engineering calculations and tools',
                'status' => 'active',
                'calculators_count' => 15,
                'version' => '1.2.0'
            ],
            [
                'id' => 2,
                'name' => 'Electrical Engineering',
                'category' => 'engineering',
                'description' => 'Electrical calculations and circuit design',
                'status' => 'active',
                'calculators_count' => 12,
                'version' => '1.1.5'
            ],
            [
                'id' => 3,
                'name' => 'Project Estimation',
                'category' => 'management',
                'description' => 'Cost estimation and project budgeting',
                'status' => 'active',
                'calculators_count' => 8,
                'version' => '1.0.3'
            ],
            [
                'id' => 4,
                'name' => 'Structural Analysis',
                'category' => 'engineering',
                'description' => 'Structural analysis and load calculations',
                'status' => 'inactive',
                'calculators_count' => 6,
                'version' => '1.0.0'
            ]
        ];
    }

    private function getModuleCategories()
    {
        return [
            'engineering' => 'Engineering',
            'management' => 'Project Management',
            'analysis' => 'Analysis Tools',
            'custom' => 'Custom Modules'
        ];
    }
}
?>
