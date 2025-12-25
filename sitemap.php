<?php
// Dynamic Sitemap Generator
require_once 'app/bootstrap.php';

use App\Core\Database;
use App\Helpers\UrlHelper;

// Set header
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    // Get current domain with protocol
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $domain = $protocol . '://' . $host;
    
    // Homepage
    ?>
    <url>
        <loc><?php echo $domain . app_base_url('/'); ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <?php
    try {
        $db = Database::getInstance()->getPdo();
        
        // Fetch all calculators
        $stmt = $db->query("SELECT calculator_id, updated_at FROM calculator_urls ORDER BY category, calculator_id");
        $calculators = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($calculators as $calc) {
            // UrlHelper returns path starting with / (including APP_BASE)
            $url = UrlHelper::calculator($calc['calculator_id']);
            $fullUrl = $domain . $url;
            
            // Format updated_at or default to now
            $lastMod = !empty($calc['updated_at']) 
                ? date('Y-m-d', strtotime($calc['updated_at'])) 
                : date('Y-m-d');
            ?>
    <url>
        <loc><?php echo htmlspecialchars($fullUrl); ?></loc>
        <lastmod><?php echo $lastMod; ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
            <?php
        }
    } catch (Exception $e) {
        // Silently fail or log error, but don't break XML structure if possible
        error_log("Sitemap Error: " . $e->getMessage());
    }
    ?>
</urlset>
