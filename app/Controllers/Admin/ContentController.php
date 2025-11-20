<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ContentService;
use App\Services\MenuService;

class ContentController extends Controller {
    private $svc;

    public function __construct() {
        parent::__construct();
        $this->svc = new ContentService();
        if (!$this->auth->check() || !$this->auth->isAdmin()) { $this->redirect('/login'); }
    }

    public function index() {
        $pages = $this->svc->allPages();
        $this->view->render('admin/content/index', ['currentPage'=>'content','pages'=>$pages,'title'=>'Content Management']);
    }

    public function pages() { $this->index(); }

    public function edit($slug) {
        $page = $this->svc->getPage($slug);
        $this->view->render('admin/content/pages', ['currentPage'=>'content','page'=>$page,'mode'=>'edit','title'=>'Edit Page']);
    }

    public function create() {
        $this->view->render('admin/content/pages', ['currentPage'=>'content','page'=>null,'mode'=>'create','title'=>'Create Page']);
    }

    public function save() {
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $body = $_POST['body'] ?? '';
        $status = $_POST['status'] ?? 'draft';
        if ($slug === '') { $slug = strtolower(preg_replace('/[^a-z0-9-]+/','-', $title)); }
        $page = ['title'=>$title,'slug'=>$slug,'body'=>$body,'status'=>$status,'updated_at'=>date('c')];
        $this->svc->upsertPage($page);
        $this->redirect('/admin/content');
    }

    public function publish() {
        $slug = $_POST['slug'] ?? '';
        $page = $this->svc->getPage($slug);
        if ($page) { $page['status'] = 'published'; $page['updated_at'] = date('c'); $this->svc->upsertPage($page); }
        $this->redirect('/admin/content');
    }

    public function preview($slug) {
        $page = $this->svc->getPage($slug);
        if (!$page) { $this->redirect('/admin/content'); return; }
        $this->view->render('pages/page', ['page'=>$page,'title'=>$page['title']]);
    }

    public function menus() {
        $ms = new MenuService();
        $items = $ms->get('primary');
        $this->view->render('admin/content/menus', ['currentPage'=>'content','items'=>$items,'title'=>'Menus']);
    }

    public function saveMenus() {
        $payload = $_POST['items'] ?? '[]';
        $items = json_decode($payload, true);
        if (!is_array($items)) { $items = []; }
        $ms = new MenuService();
        $ms->set('primary', $items);
        $this->redirect('/admin/content/menus');
    }
}