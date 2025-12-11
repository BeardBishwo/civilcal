<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\WidgetManager;
use App\Widgets\BaseWidget;

/**
 * WidgetController - Admin interface for widget management
 * 
 * This controller provides admin functionality for managing widgets in the Bishwo Calculator system.
 */
class WidgetController extends Controller
{
    private $widgetManager;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->widgetManager = new WidgetManager();
    }
    
    /**
     * Global widget settings
     */
    public function globalSettings()
    {
        $data = [
            'page_title' => 'Widget Settings',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => '/admin'],
                ['title' => 'Widgets', 'url' => '/admin/widgets'],
                ['title' => 'Settings', 'url' => '']
            ]
        ];
        
        $this->view->render('admin/widgets/global_settings', $data);
    }

    /**
     * Admin dashboard for widget management
     */
    public function index()
    {
        try {
            $widgets = $this->widgetManager->getAllWidgets();
            $availableClasses = $this->widgetManager->getAvailableWidgetClasses();
            $status = $this->widgetManager->getStatus();
            
            $data = [
                'widgets' => $widgets,
                'available_classes' => $availableClasses,
                'status' => $status,
                'page_title' => 'Widget Management',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/admin'],
                    ['title' => 'Widgets', 'url' => '/admin/widgets']
                ]
            ];
            
            // Use the View class's render method to properly use themes/admin layout
            $this->view->render('admin/widgets/index', $data);
        } catch (\Exception $e) {
            flash('error', 'Failed to load widget management: ' . $e->getMessage());
            $this->redirect('/admin/widgets');
        }
    }
    
    /**
     * Create a new widget
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleCreate();
        }
        
        $availableClasses = $this->widgetManager->getAvailableWidgetClasses();
        $widgetClassInfo = [];
        
        foreach ($availableClasses as $className) {
            $info = $this->widgetManager->getWidgetClassInfo($className);
            if ($info) {
                $widgetClassInfo[$className] = $info;
            }
        }
        
        $data = [
            'available_classes' => $availableClasses,
            'widget_class_info' => $widgetClassInfo,
            'page_title' => 'Create Widget',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => '/admin'],
                ['title' => 'Widgets', 'url' => '/admin/widgets'],
                ['title' => 'Create Widget', 'url' => '']
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/widgets/create', $data);
    }
    
    /**
     * Handle widget creation
     */
    private function handleCreate()
    {
        try {
            $className = $_POST['class_name'] ?? '';
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $isEnabled = isset($_POST['is_enabled']);
            $isVisible = isset($_POST['is_visible']);
            $position = (int)($_POST['position'] ?? 0);
            
            if (empty($className)) {
                flash('error', 'Widget class is required');
                $this->redirect('/admin/widgets/create');
                return;
            }
            
            // Create widget instance
            $config = [
                'title' => $title,
                'description' => $description,
                'enabled' => $isEnabled,
                'visible' => $isVisible,
                'position' => $position
            ];
            
            $widget = $this->widgetManager->createWidget($className, $config);
            
            if (!$widget) {
                flash('error', 'Failed to create widget');
                $this->redirect('/admin/widgets/create');
                return;
            }
            
            // Save widget
            if ($this->widgetManager->saveWidget($widget)) {
                flash('success', 'Widget created successfully');
                $this->redirect('/admin/widgets');
            } else {
                flash('error', 'Failed to save widget');
                $this->redirect('/admin/widgets/create');
            }
        } catch (\Exception $e) {
            flash('error', 'Error creating widget: ' . $e->getMessage());
            $this->redirect('/admin/widgets/create');
        }
    }
    
    /**
     * Edit widget
     * 
     * @param string $id
     */
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleEdit($id);
        }
        
        $widget = $this->widgetManager->loadWidget($id);
        
        if (!$widget) {
            flash('error', 'Widget not found');
            $this->redirect('/admin/widgets');
            return;
        }
        
        $data = [
            'widget' => $widget,
            'page_title' => 'Edit Widget: ' . $widget->getTitle(),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => '/admin'],
                ['title' => 'Widgets', 'url' => '/admin/widgets'],
                ['title' => 'Edit Widget', 'url' => '']
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/widgets/edit', $data);
    }
    
    /**
     * Handle widget editing
     * 
     * @param string $id
     */
    private function handleEdit($id)
    {
        try {
            $widget = $this->widgetManager->loadWidget($id);
            
            if (!$widget) {
                flash('error', 'Widget not found');
                $this->redirect('/admin/widgets');
                return;
            }
            
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $isEnabled = isset($_POST['is_enabled']);
            $isVisible = isset($_POST['is_visible']);
            $position = (int)($_POST['position'] ?? 0);
            
            // Update widget properties
            $widget->setTitle($title);
            $widget->setDescription($description);
            $widget->setPosition($position);
            
            if ($isEnabled) {
                $widget->enable();
            } else {
                $widget->disable();
            }
            
            $widget->setVisible($isVisible);
            
            // Save widget
            if ($this->widgetManager->saveWidget($widget)) {
                flash('success', 'Widget updated successfully');
                $this->redirect('/admin/widgets');
            } else {
                flash('error', 'Failed to save widget');
                $this->redirect('/admin/widgets/edit/' . $id);
            }
        } catch (\Exception $e) {
            flash('error', 'Error updating widget: ' . $e->getMessage());
            $this->redirect('/admin/widgets/edit/' . $id);
        }
    }
    
    /**
     * Delete widget
     * 
     * @param string $id
     */
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            flash('error', 'Invalid request method');
            $this->redirect('/admin/widgets');
            return;
        }
        
        try {
            if ($this->widgetManager->deleteWidget($id)) {
                flash('success', 'Widget deleted successfully');
            } else {
                flash('error', 'Failed to delete widget');
            }
        } catch (\Exception $e) {
            flash('error', 'Error deleting widget: ' . $e->getMessage());
        }
        
        $this->redirect('/admin/widgets');
    }
    
    /**
     * Toggle widget enabled/disabled
     * 
     * @param string $id
     */
    public function toggle($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            flash('error', 'Invalid request method');
            $this->redirect('/admin/widgets');
            return;
        }
        
        try {
            $widget = $this->widgetManager->loadWidget($id);
            
            if (!$widget) {
                flash('error', 'Widget not found');
                $this->redirect('/admin/widgets');
                return;
            }
            
            $enabled = $widget->isEnabled();
            $action = $enabled ? 'disabled' : 'enabled';
            
            if ($this->widgetManager->setWidgetEnabled($id, !$enabled)) {
                flash('success', "Widget {$action} successfully");
            } else {
                flash('error', "Failed to {$action} widget");
            }
        } catch (\Exception $e) {
            flash('error', 'Error toggling widget: ' . $e->getMessage());
        }
        
        $this->redirect('/admin/widgets');
    }
    
    /**
     * Toggle widget visibility
     * 
     * @param string $id
     */
    public function toggleVisibility($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            flash('error', 'Invalid request method');
            $this->redirect('/admin/widgets');
            return;
        }
        
        try {
            $widget = $this->widgetManager->loadWidget($id);
            
            if (!$widget) {
                flash('error', 'Widget not found');
                $this->redirect('/admin/widgets');
                return;
            }
            
            $visible = $widget->isVisible();
            $action = $visible ? 'hidden' : 'visible';
            
            if ($this->widgetManager->setWidgetVisible($id, !$visible)) {
                flash('success', "Widget set to {$action}");
            } else {
                flash('error', "Failed to change widget visibility");
            }
        } catch (\Exception $e) {
            flash('error', 'Error changing widget visibility: ' . $e->getMessage());
        }
        
        $this->redirect('/admin/widgets');
    }
    
    /**
     * Reorder widgets
     */
    public function reorder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            flash('error', 'Invalid request method');
            $this->redirect('/admin/widgets');
            return;
        }
        
        try {
            $order = $_POST['order'] ?? [];
            
            foreach ($order as $position => $widgetId) {
                $this->widgetManager->setWidgetPosition($widgetId, (int)$position);
            }
            
            flash('success', 'Widget order updated successfully');
        } catch (\Exception $e) {
            flash('error', 'Error reordering widgets: ' . $e->getMessage());
        }
        
        $this->redirect('/admin/widgets');
    }
    
    /**
     * Preview widget
     * 
     * @param string $id
     */
    public function preview($id)
    {
        try {
            $widget = $this->widgetManager->loadWidget($id);
            
            if (!$widget) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Widget not found'
                ]);
                return;
            }
            
            $html = $widget->render();
            
            // Return JSON response for AJAX preview
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'html' => $html,
                'metadata' => $widget->getMetadata()
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get widget settings
     * 
     * @param string $id
     */
    public function settings($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleUpdateSettings($id);
        }
        
        try {
            $widget = $this->widgetManager->loadWidget($id);
            
            if (!$widget) {
                flash('error', 'Widget not found');
                $this->redirect('/admin/widgets');
                return;
            }
            
            $data = [
                'widget' => $widget,
                'page_title' => 'Widget Settings: ' . $widget->getTitle(),
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/admin'],
                    ['title' => 'Widgets', 'url' => '/admin/widgets'],
                    ['title' => 'Settings', 'url' => '']
                ]
            ];
            
            // Use the View class's render method to properly use themes/admin layout
            $this->view->render('admin/widgets/settings', $data);
        } catch (\Exception $e) {
            flash('error', 'Error loading widget settings: ' . $e->getMessage());
            $this->redirect('/admin/widgets');
        }
    }
    
    /**
     * Handle widget settings update
     * 
     * @param string $id
     */
    private function handleUpdateSettings($id)
    {
        try {
            $widget = $this->widgetManager->loadWidget($id);
            
            if (!$widget) {
                flash('error', 'Widget not found');
                $this->redirect('/admin/widgets');
                return;
            }
            
            // Update widget settings based on the widget type
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                $widget->setSetting($key, $value);
            }
            
            // Save widget with updated settings
            if ($this->widgetManager->saveWidget($widget)) {
                flash('success', 'Widget settings updated successfully');
                $this->redirect('/admin/widgets');
            } else {
                flash('error', 'Failed to save widget settings');
                $this->redirect('/admin/widgets/settings/' . $id);
            }
        } catch (\Exception $e) {
            flash('error', 'Error updating widget settings: ' . $e->getMessage());
            $this->redirect('/admin/widgets/settings/' . $id);
        }
    }
    
    /**
     * Create widget database tables
     */
    public function setup()
    {
        try {
            if ($this->widgetManager->createWidgetTables()) {
                flash('success', 'Widget database tables created successfully');
            } else {
                flash('error', 'Failed to create widget database tables');
            }
        } catch (\Exception $e) {
            flash('error', 'Error creating widget tables: ' . $e->getMessage());
        }
        
        $this->redirect('/admin/widgets');
    }
}