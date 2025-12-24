<?php

namespace App\Services;

use App\Core\Database;

/**
 * Permalink Service
 * Handles URL redirects, mappings, and permalink management
 */
class PermalinkService
{
    private $db;
    private $settingsService;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
        $this->settingsService = new SettingsService();
    }

    /**
     * Create a 301 redirect mapping
     */
    public function createRedirect($oldUrl, $newUrl, $type = '301')
    {
        try {
            // Check if redirect already exists
            $stmt = $this->db->prepare("SELECT id FROM permalink_mappings WHERE old_url = ? LIMIT 1");
            $stmt->execute([$oldUrl]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Update existing redirect
                $stmt = $this->db->prepare("
                    UPDATE permalink_mappings 
                    SET new_url = ?, redirect_type = ?, updated_at = NOW() 
                    WHERE old_url = ?
                ");
                return $stmt->execute([$newUrl, $type, $oldUrl]);
            } else {
                // Create new redirect
                $stmt = $this->db->prepare("
                    INSERT INTO permalink_mappings (old_url, new_url, redirect_type, created_at, updated_at) 
                    VALUES (?, ?, ?, NOW(), NOW())
                ");
                return $stmt->execute([$oldUrl, $newUrl, $type]);
            }
        } catch (\Exception $e) {
            Logger::error("Failed to create redirect: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get redirect for old URL
     */
    public function getRedirect($oldUrl)
    {
        $stmt = $this->db->prepare("SELECT new_url, redirect_type FROM permalink_mappings WHERE old_url = ? LIMIT 1");
        $stmt->execute([$oldUrl]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Bulk create redirects for permalink structure change
     */
    public function createBulkRedirects($oldStructure, $newStructure)
    {
        if (!$this->settingsService->get('permalink_redirect_old_urls', true)) {
            return true; // Redirects disabled
        }

        try {
            $db = Database::getInstance()->getPdo();
            
            // Get all calculators
            $stmt = $db->prepare("SELECT calculator_id, category, subcategory, slug FROM calculator_urls");
            $stmt->execute();
            $calculators = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $redirectsCreated = 0;

            foreach ($calculators as $calculator) {
                // Generate old URL based on old structure
                $oldUrl = $this->generateUrlForStructure($calculator, $oldStructure);
                
                // Generate new URL based on new structure
                $newUrl = $this->generateUrlForStructure($calculator, $newStructure);

                if ($oldUrl !== $newUrl) {
                    if ($this->createRedirect($oldUrl, $newUrl, '301')) {
                        $redirectsCreated++;
                    }
                }
            }

            Logger::info("Created $redirectsCreated redirects for permalink structure change from $oldStructure to $newStructure");
            return true;
        } catch (\Exception $e) {
            Logger::error("Failed to create bulk redirects: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate URL for specific structure
     */
    private function generateUrlForStructure($calculator, $structure)
    {
        $baseUrl = defined('APP_BASE') ? '/' . APP_BASE : '';
        $slug = $calculator['slug'];
        $category = $calculator['category'];
        $subcategory = $calculator['subcategory'];
        $calculatorId = $calculator['calculator_id'];

        switch ($structure) {
            case 'full-path':
                return "{$baseUrl}/modules/{$category}/{$subcategory}/{$calculatorId}.php";
            case 'category-calculator':
                return "{$baseUrl}/{$category}/{$calculatorId}";
            case 'subcategory-calculator':
                return "{$baseUrl}/{$subcategory}/{$calculatorId}";
            case 'calculator-only':
                return "{$baseUrl}/{$slug}";
            case 'php-extension':
                return "{$baseUrl}/{$slug}.php";
            case 'base-path':
                $basePath = $this->settingsService->get('permalink_base_path', 'tools');
                return "{$baseUrl}/{$basePath}/{$slug}";
            case 'custom':
                $pattern = $this->settingsService->get('permalink_custom_pattern', '');
                if (!empty($pattern)) {
                    $pattern = str_replace('{category}', $category, $pattern);
                    $pattern = str_replace('{subcategory}', $subcategory, $pattern);
                    $pattern = str_replace('{slug}', $slug, $pattern);
                    return "{$baseUrl}{$pattern}";
                }
                return "{$baseUrl}/{$slug}";
            default:
                return "{$baseUrl}/{$slug}";
        }
    }

    /**
     * Get redirect statistics
     */
    public function getRedirectStats()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_redirects,
                    COUNT(CASE WHEN redirect_type = '301' THEN 1 END) as permanent_redirects,
                    COUNT(CASE WHEN redirect_type = '302' THEN 1 END) as temporary_redirects
                FROM permalink_mappings
            ");
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            Logger::error("Failed to get redirect stats: " . $e->getMessage());
            return ['total_redirects' => 0, 'permanent_redirects' => 0, 'temporary_redirects' => 0];
        }
    }

    /**
     * Clean up old redirects (keep only last 3 structure changes)
     */
    public function cleanupOldRedirects()
    {
        try {
            // This is a basic cleanup - in production, you might want more sophisticated logic
            $stmt = $this->db->prepare("
                DELETE FROM permalink_mappings 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)
            ");
            $stmt->execute();
            
            $deleted = $stmt->rowCount();
            Logger::info("Cleaned up $deleted old redirects");
            return $deleted;
        } catch (\Exception $e) {
            Logger::error("Failed to cleanup old redirects: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Test URL generation for a calculator
     */
    public function testUrlGeneration($calculatorId)
    {
        try {
            $stmt = $this->db->prepare("SELECT calculator_id, category, subcategory, slug FROM calculator_urls WHERE calculator_id = ? LIMIT 1");
            $stmt->execute([$calculatorId]);
            $calculator = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$calculator) {
                return ['error' => 'Calculator not found'];
            }

            $baseUrl = defined('APP_BASE') ? '/' . APP_BASE : '';
            $results = [];

            $structures = [
                'full-path',
                'category-calculator', 
                'subcategory-calculator',
                'calculator-only',
                'php-extension',
                'base-path',
                'custom'
            ];

            foreach ($structures as $structure) {
                $url = $this->generateUrlForStructure($calculator, $structure);
                $results[$structure] = $url;
            }

            return [
                'calculator' => $calculator,
                'urls' => $results,
                'base_url' => $baseUrl
            ];
        } catch (\Exception $e) {
            return ['error' => 'Failed to test URL generation: ' . $e->getMessage()];
        }
    }
}