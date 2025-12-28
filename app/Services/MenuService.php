<?php
namespace App\Services;

use App\Models\Menu;

class MenuService {
    private $menuModel;

    public function __construct() {
        $this->menuModel = new Menu();
    }

    /**
     * Get menu items for a specific location
     */
    public function get($location) {
        $menu = $this->menuModel->findByLocation($location);
        return $menu['items'] ?? [];
    }

    /**
     * Set menu items for a location (Admin usage)
     */
    public function set($location, $items) {
        $menu = $this->menuModel->findByLocation($location);
        
        $data = [
            'location' => $location,
            'items' => is_array($items) ? json_encode($items) : $items,
            'is_active' => 1
        ];

        if ($menu) {
            return $this->menuModel->update($menu['id'], $data);
        } else {
            $data['name'] = ucfirst($location) . ' Menu';
            return $this->menuModel->create($data);
        }
    }
}
?>