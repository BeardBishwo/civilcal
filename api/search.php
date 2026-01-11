<?php
/**
 * Enhanced Search API for Civil Calculator
 * Provides beautiful, fast search results for calculators and tools
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Prevent any output before JSON
ob_start();

try {
    // Define constant to prevent bootstrap issues
    if (!defined('BISHWO_CALCULATOR')) {
        define('BISHWO_CALCULATOR', true);
    }
    
    // Include necessary files
    require_once __DIR__ . '/../app/bootstrap.php';
    
    // Ensure app_base_url function is available
    if (!function_exists('app_base_url')) {
        function app_base_url($path = '') {
            $base = '';
            if (isset($_SERVER['HTTP_HOST'])) {
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $base = $protocol . '://' . $_SERVER['HTTP_HOST'];
            }
            return $base . '/' . ltrim($path, '/');
        }
    }
    
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $qLower = function_exists('mb_strtolower') ? mb_strtolower($q) : strtolower($q);
    
    // Auto-discover all calculators from modules/ folder structure
    function discover_calculators($modules_dir) {
        $calculators = [];
        
        if (!is_dir($modules_dir)) {
            return $calculators;
        }
        
        $categories = scandir($modules_dir);
        
        foreach ($categories as $category) {
            if ($category === '.' || $category === '..' || !is_dir($modules_dir . '/' . $category)) continue;
            
            // Scan subcategories (e.g., concrete, brickwork, etc.)
            $subcategory_path = $modules_dir . '/' . $category;
            if (!is_dir($subcategory_path)) continue;
            
            $subcategories = scandir($subcategory_path);
            foreach ($subcategories as $subcategory) {
                if ($subcategory === '.' || $subcategory === '..' || !is_dir($subcategory_path . '/' . $subcategory)) continue;
                
                // Scan calculator files
                $files_path = $subcategory_path . '/' . $subcategory;
                if (!is_dir($files_path)) continue;
                
                $files = scandir($files_path);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
                    
                    $filepath = $files_path . '/' . $file;
                    if (!file_exists($filepath)) continue;
                    
                    $content = file_get_contents($filepath);
                    
                    // Extract title from page_title or use filename
                    $title = null;
                    if (preg_match("/\\\$page_title\s*=\s*['\"](.+?)['\"]/", $content, $matches)) {
                        $title = $matches[1];
                    } else {
                        // Fallback: convert filename to readable name
                        $title = ucfirst(str_replace(['-', '_'], ' ', pathinfo($file, PATHINFO_FILENAME)));
                    }
                    
                    // Extract description from content or create one
                    $description = ucfirst($subcategory) . ' calculations and engineering tools';
                    if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']+)["\']/i', $content, $matches)) {
                        $description = $matches[1];
                    }
                    
                    $calculators[] = [
                        'type' => 'calculator',
                        'id' => pathinfo($file, PATHINFO_FILENAME),
                        'name' => $title,
                        'description' => $description,
                        'url' => app_base_url('modules/' . $category . '/' . $subcategory . '/' . $file),
                        'category' => ucfirst(str_replace('-', ' ', $category)),
                        'subcategory' => ucfirst(str_replace('-', ' ', $subcategory)),
                        'icon' => getCategoryIcon($category),
                        'color' => getCategoryColor($category)
                    ];
                }
            }
        }
        
        return $calculators;
    }
    
    // Get category-specific icons
    function getCategoryIcon($category) {
        $icons = [
            'civil' => 'fas fa-hard-hat',
            'structural' => 'fas fa-building',
            'electrical' => 'fas fa-bolt',
            'mechanical' => 'fas fa-cogs',
            'plumbing' => 'fas fa-tint',
            'hvac' => 'fas fa-wind',
            'fire' => 'fas fa-fire-extinguisher',
            'default' => 'fas fa-calculator'
        ];
        
        return $icons[$category] ?? $icons['default'];
    }
    
    // Get category-specific colors
    function getCategoryColor($category) {
        $colors = [
            'civil' => '#667eea',
            'structural' => '#ff6b6b',
            'electrical' => '#feca57',
            'mechanical' => '#48cae4',
            'plumbing' => '#4facfe',
            'hvac' => '#00f2fe',
            'fire' => '#ff9a9e',
            'default' => '#6c757d'
        ];
        
        return $colors[$category] ?? $colors['default'];
    }
    
    $modules_dir = __DIR__ . '/../modules';
    $cache_file = __DIR__ . '/../storage/cache/calculators_index.json';
    $cache_lifetime = 3600; // 1 hour

    $calculators = [];
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_lifetime)) {
        $calculators = json_decode(file_get_contents($cache_file), true);
    }

    if (empty($calculators)) {
        $calculators = discover_calculators($modules_dir);
        // Ensure cache directory exists
        if (!is_dir(dirname($cache_file))) {
            @mkdir(dirname($cache_file), 0755, true);
        }
        file_put_contents($cache_file, json_encode($calculators));
    }
    
    $results = [];
    
    // If no search query, show popular/recent items
    if ($qLower === '') {
        // Show first 8 calculators as popular items
        foreach (array_slice($calculators, 0, 8) as $c) {
            $results[] = $c;
        }
        
        // Add recent history items from database if available
        try {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
            
            if ($pdo) {
                $stmt = $pdo->prepare('SELECT id, tool_name, result, date_time FROM history ORDER BY date_time DESC LIMIT 3');
                $stmt->execute();
                $rows = $stmt->fetchAll();
                
                foreach ($rows as $r) {
                    $results[] = [
                        'type' => 'history',
                        'id' => 'history_'.$r['id'],
                        'name' => $r['tool_name'],
                        'description' => 'Recent calculation: ' . substr(strip_tags($r['result']), 0, 100) . '...',
                        'url' => '#',
                        'icon' => 'fas fa-history',
                        'color' => '#059669',
                        'category' => 'Recent',
                        'subcategory' => ''
                    ];
                }
            }
        } catch (Exception $e) {
            // Ignore database errors for history
        }
        
        // Clean output buffer and return results
        ob_clean();
        echo json_encode($results);
        exit;
    }
    
    // Search calculators by name, category, or subcategory
    $searchResults = [];
    
    foreach ($calculators as $c) {
        $score = 0;
        
        // Exact name match gets highest score
        if (stripos($c['name'], $q) !== false) {
            $score += 100;
        }
        
        // Category match
        if (stripos($c['category'], $q) !== false) {
            $score += 50;
        }
        
        // Subcategory match
        if (stripos($c['subcategory'], $q) !== false) {
            $score += 30;
        }
        
        // Description match
        if (stripos($c['description'], $q) !== false) {
            $score += 20;
        }
        
        // ID match (filename)
        if (stripos($c['id'], $q) !== false) {
            $score += 10;
        }
        
        if ($score > 0) {
            $c['score'] = $score;
            $searchResults[] = $c;
        }
    }
    
    // Sort by score (highest first)
    usort($searchResults, function($a, $b) {
        return $b['score'] - $a['score'];
    });
    
    // If no results, try fuzzy matching
    if (empty($searchResults)) {
        $words = preg_split('/\s+/', $q);
        
        foreach ($calculators as $c) {
            $score = 0;
            
            foreach ($words as $word) {
                if (strlen($word) < 3) continue;
                
                // Check each word against name parts
                $nameParts = preg_split('/\s+/', $c['name']);
                foreach ($nameParts as $part) {
                    if (stripos($part, $word) !== false) {
                        $score += 5;
                    }
                }
                
                // Check against category and subcategory
                if (stripos($c['category'], $word) !== false) {
                    $score += 3;
                }
                if (stripos($c['subcategory'], $word) !== false) {
                    $score += 2;
                }
            }
            
            if ($score > 0) {
                $c['score'] = $score;
                $searchResults[] = $c;
            }
        }
        
        // Sort by score again
        usort($searchResults, function($a, $b) {
            return $b['score'] - $a['score'];
        });
    }
    
    // Return up to 10 results
    $results = array_slice($searchResults, 0, 10);
    
    // Remove score from final results
    foreach ($results as &$result) {
        unset($result['score']);
    }
    
    // Clean output buffer and return results
    ob_clean();
    echo json_encode($results);
    
} catch (Exception $e) {
    // Clean output buffer and return error
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'Search failed',
        'message' => $e->getMessage()
    ]);
}
?>
