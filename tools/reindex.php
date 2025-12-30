<?php
define('BISHWO_CALCULATOR', true);
require __DIR__ . '/../app/bootstrap.php';
use App\Services\SearchIndexer;

echo "Indexing...\n";
$indexer = new SearchIndexer();
try {
    $count = $indexer->indexAll();
    echo "Indexed $count items.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
