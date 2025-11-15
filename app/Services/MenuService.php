<?php
namespace App\Services;

class MenuService {
    private $path;
    public function __construct() {
        $base = defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__,2) . '/storage';
        $this->path = $base . '/menus.json';
        if (!is_dir($base)) { @mkdir($base, 0755, true); }
        if (!file_exists($this->path)) { file_put_contents($this->path, json_encode(['menus'=>[]])); }
    }
    private function load() {
        $raw = file_get_contents($this->path);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : ['menus'=>[]];
    }
    private function save($data) { file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); }
    public function get($location) {
        $data = $this->load();
        foreach ($data['menus'] as $m) { if (($m['location'] ?? '') === $location) return $m['items'] ?? []; }
        return [];
    }
    public function set($location, $items) {
        $data = $this->load();
        $found = false;
        for ($i=0; $i<count($data['menus']); $i++) {
            if (($data['menus'][$i]['location'] ?? '') === $location) { $data['menus'][$i]['items'] = $items; $found = true; break; }
        }
        if (!$found) { $data['menus'][] = ['location'=>$location, 'items'=>$items]; }
        $this->save($data);
        return true;
    }
}
?>