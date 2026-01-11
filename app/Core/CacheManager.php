<?php

namespace App\Core;

use App\Services\AdvancedCache;

/**
 * Simple Cache Manager for Database Query Results
 * @deprecated Use App\Services\AdvancedCache instead
 */
class CacheManager
{
    // Keeping minimal structure for compatibility but proxying logic

    /**
     * Get cached value
     */
    public static function get($key)
    {
        return AdvancedCache::getInstance()->get($key);
    }

    /**
     * Set cache value
     */
    public static function set($key, $value, $ttl = null)
    {
        AdvancedCache::getInstance()->set($key, $value, $ttl);
    }

    /**
     * Check if key exists and is valid
     */
    public static function has($key)
    {
        return AdvancedCache::getInstance()->has($key);
    }

    /**
     * Delete cache key
     */
    public static function delete($key)
    {
        AdvancedCache::getInstance()->forget($key);
    }

    /**
     * Clear all cache
     */
    public static function clear()
    {
        AdvancedCache::getInstance()->flush();
    }

    /**
     * Remember pattern: Get from cache or execute callback
     */
    public static function remember($key, $callback, $ttl = null)
    {
        if (self::has($key)) {
            return self::get($key);
        }

        $value = $callback();
        self::set($key, $value, $ttl);
        
        return $value;
    }
}
