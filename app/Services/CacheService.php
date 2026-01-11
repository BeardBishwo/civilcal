<?php

namespace App\Services;

/**
 * Cache Service
 * 
 * @deprecated Use App\Services\AdvancedCache instead
 * Unified caching interface supporting multiple drivers
 * Currently supports file-based caching, Redis-ready for future
 * 
 * @package App\Services
 */
class CacheService
{
    private static $instance = null;
    private $advancedCache;

    private function __construct()
    {
        $this->advancedCache = AdvancedCache::getInstance();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get cached value
     * 
     * @param string $key Cache key
     * @param mixed $default Default value if not found
     * @return mixed Cached value or default
     */
    public function get($key, $default = null)
    {
        return $this->advancedCache->get($key, $default);
    }

    /**
     * Set cache value
     * 
     * @param string $key Cache key
     * @param mixed $value Value to cache
     * @param int $ttl Time to live in seconds
     * @return bool Success status
     */
    public function set($key, $value, $ttl = null)
    {
        // AdvancedCache expects TTL, handle null default there or here
        return $this->advancedCache->set($key, $value, $ttl);
    }

    /**
     * Delete cached value
     * 
     * @param string $key Cache key
     * @return bool Success status
     */
    public function delete($key)
    {
        return $this->advancedCache->forget($key);
    }

    /**
     * Clear all cache
     * 
     * @return bool Success status
     */
    public function flush()
    {
        return $this->advancedCache->flush();
    }

    /**
     * Remember: Get from cache or execute callback and cache result
     * 
     * @param string $key Cache key
     * @param int $ttl Time to live
     * @param callable $callback Callback to execute if cache miss
     * @return mixed Cached or computed value
     */
    public function remember($key, $ttl, callable $callback)
    {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }

    /**
     * Check if key exists in cache
     * 
     * @param string $key Cache key
     * @return bool True if exists
     */
    public function has($key)
    {
        return $this->advancedCache->has($key);
    }

    // ============ File Driver Methods ============

    /**
     * Get from file cache
     */
    private function fileGet($key, $default = null)
    {
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        // Check if expired
        if ($data['expires_at'] < time()) {
            unlink($filename);
            return $default;
        }
        
        return $data['value'];
    }

    /**
     * Set file cache
     */
    private function fileSet($key, $value, $ttl)
    {
        $filename = $this->getCacheFilename($key);
        
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl
        ];
        
        return file_put_contents($filename, serialize($data)) !== false;
    }

    /**
     * Delete from file cache
     */
    private function fileDelete($key)
    {
        $filename = $this->getCacheFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return false;
    }

    /**
     * Flush file cache
     */
    private function fileFlush()
    {
        $files = glob($this->cacheDir . '*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }

    /**
     * Get cache filename for key
     */
    private function getCacheFilename($key)
    {
        $hash = md5($key);
        return $this->cacheDir . $hash . '.cache';
    }

    /**
     * Clean expired cache files
     */
    public function cleanExpired()
    {
        $files = glob($this->cacheDir . '*.cache');
        $cleaned = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $data = unserialize(file_get_contents($file));
                if ($data['expires_at'] < time()) {
                    unlink($file);
                    $cleaned++;
                }
            }
        }
        
        return $cleaned;
    }

    // ============ Redis Driver Methods ============

    /**
     * Get from Redis cache
     */
    private function redisGet($key, $default = null)
    {
        try {
            $value = $this->redis->get($key);
            return ($value === false) ? $default : unserialize($value);
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set Redis cache
     */
    private function redisSet($key, $value, $ttl)
    {
        try {
            return $this->redis->setex($key, $ttl, serialize($value));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete from Redis cache
     */
    private function redisDelete($key)
    {
        try {
            return $this->redis->del($key) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Flush Redis cache
     */
    private function redisFlush()
    {
        try {
            return $this->redis->flushDB();
        } catch (\Exception $e) {
            return false;
        }
    }

    // ============ File Driver Methods ============

    /**
     * Get current cache driver
     * 
     * @return string Current driver ('redis' or 'file')
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Get cache statistics
     */
    public function getStats()
    {
        $files = glob($this->cacheDir . '*.cache');
        $totalSize = 0;
        $expired = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $data = unserialize(file_get_contents($file));
                if ($data['expires_at'] < time()) {
                    $expired++;
                }
            }
        }
        
        return [
            'total_items' => count($files),
            'expired_items' => $expired,
            'total_size_bytes' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2)
        ];
    }
}
