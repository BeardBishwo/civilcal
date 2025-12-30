<?php

namespace App\Services;

use App\Models\Search;
use App\Models\Page;
use App\Models\Module;

class SearchIndexer
{
    private $search;

    public function __construct()
    {
        $this->search = new Search();
    }

    public function indexAll()
    {
        $this->search->clearIndex();
        
        $count = 0;
        $count += $this->indexPages();
        $count += $this->indexModules();
        $count += $this->indexAdminSettings();
        
        return $count;
    }

    private function indexPages()
    {
        $db = \App\Core\Database::getInstance();
        $stmt = $db->query("SELECT id, title, content, slug, status FROM pages");
        $pages = $stmt->fetchAll();
        $count = 0;

        foreach ($pages as $page) {
            // Check visibility logic if needed (e.g. status='published')
            if (isset($page['status']) && $page['status'] !== 'published') continue;
            
            $this->search->updateIndex(
                'page',
                $page['id'],
                $page['title'],
                $page['content'] ?? '',
                '/page/' . $page['slug']
            );
            $count++;
        }
        return $count;
    }

    private function indexModules()
    {
        $moduleModel = new Module();
        // Assuming getAll() or similar method exists. 
        // If getAll doesn't exist, we might need a direct DB call or check the model.
        // For safety, let's try-catch or assume standard model method.
        try {
            $modules = $moduleModel->getAll(); 
            $count = 0;

            foreach ($modules as $module) {
                if ($module['status'] !== 'active') continue;

                $this->search->updateIndex(
                    'module',
                    $module['id'],
                    $module['name'],
                    $module['description'] ?? '',
                    '/' . $module['slug']
                );
                $count++;
            }
            return $count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function indexAdminSettings()
    {
        $settings = [
            ['title' => 'General Settings', 'content' => 'Site title, logo, description, social links', 'url' => '/admin/settings/general'],
            ['title' => 'Email Settings', 'content' => 'SMTP configuration, email templates, sender info', 'url' => '/admin/email-manager/settings'],
            ['title' => 'User Management', 'content' => 'Manage users, roles, permissions', 'url' => '/admin/users'],
            ['title' => 'Media Library', 'content' => 'Upload images, manage files, gallery', 'url' => '/admin/content/media'],
            ['title' => 'Menu Customization', 'content' => 'Navigation menus, header, footer links', 'url' => '/admin/settings/menus'],
            ['title' => 'Backup & Restore', 'content' => 'Database backup, system restore', 'url' => '/admin/backup'],
            ['title' => 'System Health', 'content' => 'Error logs, system status, performance', 'url' => '/admin/system-status'],
            ['title' => 'Dashboard', 'content' => 'Analytics, overview, stats', 'url' => '/admin/dashboard'],
            ['title' => 'Calculators', 'content' => 'Manage calculators, modules', 'url' => '/admin/calculators']
        ];

        $count = 0;
        foreach ($settings as $idx => $setting) {
            $this->search->updateIndex(
                'setting',
                $idx + 1000, // Offset ID to avoid conflict or just usage strictly as types
                $setting['title'],
                $setting['content'],
                $setting['url']
            );
            $count++;
        }
        return $count;
    }
}
