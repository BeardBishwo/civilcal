<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Search;
use App\Services\SearchIndexer;

class SearchController extends Controller
{
    private $searchModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->searchModel = new Search();
    }

    /**
     * API Endpoint for Search
     * GET /admin/api/search?q=query
     */
    public function search()
    {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            return $this->json([]);
        }

        $results = $this->searchModel->search($query, 10);
        
        // Format results for UI
        $formatted = array_map(function($item) {
            $icon = 'fa-file';
            switch($item['type']) {
                case 'page': $icon = 'fa-file-alt'; break;
                case 'module': $icon = 'fa-calculator'; break;
                case 'setting': $icon = 'fa-cog'; break;
                case 'user': $icon = 'fa-user'; break;
            }

            return [
                'title' => $item['title'],
                'url' => app_base_url($item['url']),
                'type' => ucfirst($item['type']),
                'icon' => $icon
            ];
        }, $results);

        // If no results locally, checking if we need to call reindex? No, that's heavy.
        
        return $this->json($formatted);
    }

    /**
     * Manual trigger to rebuild index
     * POST /admin/api/search/reindex
     */
    public function reindex()
    {
        // Validate CSRF
        if (empty($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            return $this->json(['success' => false, 'message' => 'Invalid CSRF token']);
        }

        // Increase time limit for heavy indexing
        set_time_limit(300);

        $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : null;
        $offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;

        $indexer = new SearchIndexer();
        
        // If batching requested
        if ($limit !== null) {
            $count = $indexer->indexBatch($limit, $offset);
            return $this->json([
                'success' => true, 
                'message' => "Batch indexed: {$count} items from offset {$offset}.",
                'batch_count' => $count
            ]);
        }

        $count = $indexer->indexAll();
        
        return $this->json([
            'success' => true, 
            'message' => "Index rebuilt successfully. {$count} items indexed."
        ]);
    }
}
