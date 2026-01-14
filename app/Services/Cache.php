<?php
namespace App\Services;

/**
 * Simple file-based caching service
 * Provides basic caching functionality for the application
 */
class Cache {
    private string $cacheDir;
    private int $defaultTtl;
    
    public function __construct() {
        $this->cacheDir = BASE_PATH . '/storage/cache';
        $this->defaultTtl = 3600; // 1 hour default TTL
        
        // Ensure cache directory exists
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Get an item from the cache
     */
    public function get(string $key, mixed $default = null): mixed {
        $filePath = $this->getFilePath($key);
        
        if (!file_exists($filePath)) {
            return $default;
        }
        
        $data = file_get_contents($filePath);
        if ($data === false) {
            return $default;
        }
        
        $cacheData = json_decode($data, true);
        
        // Check if cache has expired
        if ($cacheData['expires_at'] !== 0 && time() > $cacheData['expires_at']) {
            $this->forget($key);
            return $default;
        }
        
        return $cacheData['data'];
    }
    
    /**
     * Store an item in the cache
     */
    public function put(string $key, mixed $data, int $ttl = null): bool {
        $ttl = $ttl ?? $this->defaultTtl;
        
        $cacheData = [
            'data' => $data,
            'expires_at' => $ttl > 0 ? time() + $ttl : 0
        ];
        
        $filePath = $this->getFilePath($key);
        $data = json_encode($cacheData);
        
        return file_put_contents($filePath, $data) !== false;
    }
    
    /**
     * Store an item in the cache permanently
     */
    public function forever(string $key, mixed $data): bool {
        return $this->put($key, $data, 0);
    }
    
    /**
     * Remove an item from the cache
     */
    public function forget(string $key): bool {
        $filePath = $this->getFilePath($key);
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }
    
    /**
     * Clear all items from the cache
     */
    public function flush(): bool {
        $files = glob($this->cacheDir . '/*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    /**
     * Check if an item exists in the cache
     */
    public function has(string $key): bool {
        return $this->get($key) !== null;
    }
    
    /**
     * Get the file path for a cache key
     */
    private function getFilePath(string $key): string {
        $hash = md5($key);
        return $this->cacheDir . '/' . $hash . '.cache';
    }
    
    /**
     * Get cache statistics
     */
    public function getStats(): array {
        $files = glob($this->cacheDir . '/*.cache');
        $totalSize = 0;
        $expiredCount = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            
            $data = json_decode(file_get_contents($file), true);
            if ($data['expires_at'] !== 0 && time() > $data['expires_at']) {
                $expiredCount++;
            }
        }
        
        return [
            'total_files' => count($files),
            'total_size' => $totalSize,
            'expired_files' => $expiredCount,
            'cache_dir' => $this->cacheDir
        ];
    }
    
    /**
     * Clean expired cache files
     */
    public function cleanExpired(): int {
        $files = glob($this->cacheDir . '/*.cache');
        $cleanedCount = 0;
        
        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            
            if ($data['expires_at'] !== 0 && time() > $data['expires_at']) {
                unlink($file);
                $cleanedCount++;
            }
        }
        
        return $cleanedCount;
    }
    
    /**
     * Set default TTL
     */
    public function setDefaultTtl(int $ttl): void {
        $this->defaultTtl = $ttl;
    }
    
    /**
     * Get cache directory size
     */
    public function getDirectorySize(): int {
        $files = glob($this->cacheDir . '/*');
        $size = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $size += filesize($file);
            }
        }
        
        return $size;
    }
}
