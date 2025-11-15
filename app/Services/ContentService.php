<?php
namespace App\Services;

class ContentService {
    private $storePath;

    public function __construct() {
        $this->storePath = (defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__,2) . '/storage') . '/content.json';
        if (!file_exists(dirname($this->storePath))) { @mkdir(dirname($this->storePath), 0755, true); }
        if (!file_exists($this->storePath)) { file_put_contents($this->storePath, json_encode(['pages'=>[]])); }
    }

    public function load() {
        $raw = file_get_contents($this->storePath);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : ['pages'=>[]];
    }

    public function save($data) {
        file_put_contents($this->storePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }

    public function allPages() {
        $data = $this->load();
        return $data['pages'] ?? [];
    }

    public function getPage($slug) {
        $pages = $this->allPages();
        foreach ($pages as $p) { if (($p['slug'] ?? '') === $slug) return $p; }
        return null;
    }

    public function upsertPage($page) {
        $data = $this->load();
        $pages = $data['pages'] ?? [];
        $found = false;
        for ($i=0; $i<count($pages); $i++) {
            if (($pages[$i]['slug'] ?? '') === ($page['slug'] ?? '')) { $pages[$i] = $page; $found = true; break; }
        }
        if (!$found) { $pages[] = $page; }
        $data['pages'] = $pages;
        $this->save($data);
        return $page;
    }
}
?>