<?php

namespace App\Core;

/**
 * Simple Cache Manager for Database Query Results
 */
class CacheManager
{
    private static $cache = [];
    private static $ttl = 300; // 5 minutes default

    /**
     * Get cached value
     */
    public static function get($key)
    {
        if (!isset(self::$cache[$key])) {
            return null;
        }

        $item = self::$cache[$key];
        
        // Check if expired
        if (time() > $item['expires']) {
            unset(self::$cache[$key]);
            return null;
        }

        return $item['value'];
    }

    /**
     * Set cache value
     */
    public static function set($key, $value, $ttl = null)
    {
        $ttl = $ttl ?? self::$ttl;
        
        self::$cache[$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
    }

    /**
     * Check if key exists and is valid
     */
    public static function has($key)
    {
        return self::get($key) !== null;
    }

    /**
     * Delete cache key
     */
    public static function delete($key)
    {
        unset(self::$cache[$key]);
    }

    /**
     * Clear all cache
     */
    public static function clear()
    {
        self::$cache = [];
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
