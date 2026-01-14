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
}
