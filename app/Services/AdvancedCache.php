<?php
namespace App\Services;

/**
 * Advanced caching service
 * Provides multi-tier caching with Redis support, cache warming, and intelligent invalidation
 */
class AdvancedCache {
    private array $adapters = [];
    private array $fallbackAdapters = [];
    private array $cacheConfig = [];
    private $logger;
    private bool $enabled = true;
    
    public function __construct(array $config = [], $logger = null) {
        $this->logger = $logger;
        $this->enabled = getenv('ADVANCED_CACHE_ENABLED') !== 'false';
        $this->cacheConfig = $config;
        $this->initializeAdapters();
    }
    
    /**
     * Initialize cache adapters
     */
    private function initializeAdapters(): void {
        // Redis adapter (if available)
        if (extension_loaded('redis') && $this->cacheConfig['redis']['enabled'] ?? false) {
            try {
                $redis = new \Redis();
                $redisConfig = $this->cacheConfig['redis'] ?? [];
                $redis->connect(
                    $redisConfig['host'] ?? '127.0.0.1',
                    $redisConfig['port'] ?? 6379,
                    $redisConfig['timeout'] ?? 2.5
                );
                
                if (!empty($redisConfig['password'])) {
                    $redis->auth($redisConfig['password']);
                }
                
                $this->adapters['redis'] = new RedisAdapter($redis);
                $this->logCacheEvent('Redis adapter initialized');
                
            } catch (\Exception $e) {
                $this->logCacheEvent("Redis adapter failed: " . $e->getMessage(), 'warning');
            }
        }
        
        // Memcached adapter (if available)
        if (extension_loaded('memcached') && $this->cacheConfig['memcached']['enabled'] ?? false) {
            try {
                $memcached = new \Memcached();
                $memcachedConfig = $this->cacheConfig['memcached'] ?? [];
                
                if (!$memcached->getServerList()) {
                    $memcached->addServer(
                        $memcachedConfig['host'] ?? '127.0.0.1',
                        $memcachedConfig['port'] ?? 11211
                    );
                }
                
                $this->adapters['memcached'] = new MemcachedAdapter($memcached);
                $this->logCacheEvent('Memcached adapter initialized');
                
            } catch (\Exception $e) {
                $this->logCacheEvent("Memcached adapter failed: " . $e->getMessage(), 'warning');
            }
        }
        
        // File-based adapter (fallback)
        $this->adapters['file'] = new FileAdapter($this->cacheConfig['file']['path'] ?? BASE_PATH . '/storage/cache/advanced');
        $this->logCacheEvent('File adapter initialized');
        
        // Memory adapter (fastest fallback)
        $this->adapters['memory'] = new MemoryAdapter();
        $this->logCacheEvent('Memory adapter initialized');
        
        // Set fallback chain
        $this->fallbackAdapters = ['memory', 'file'];
        if (isset($this->adapters['memcached'])) {
            array_unshift($this->fallbackAdapters, 'memcached');
        }
        if (isset($this->adapters['redis'])) {
            array_unshift($this->fallbackAdapters, 'redis');
        }
    }
    
    /**
     * Get an item from cache
     */
    public function get(string $key, mixed $default = null, string $adapter = null): mixed {
        if (!$this->enabled) {
            return $default;
        }
        
        $adapter = $adapter ?? $this->getBestAdapter($key);
        
        try {
            if (isset($this->adapters[$adapter])) {
                $value = $this->adapters[$adapter]->get($this->normalizeKey($key));
                
                if ($value !== null) {
                    $this->logCacheEvent("Cache HIT: {$key} ({$adapter})");
                    return $value;
                }
            }
            
            // Try fallback adapters
            foreach ($this->fallbackAdapters as $fallbackAdapter) {
                if ($fallbackAdapter !== $adapter && isset($this->adapters[$fallbackAdapter])) {
                    $value = $this->adapters[$fallbackAdapter]->get($this->normalizeKey($key));
                    if ($value !== null) {
                        // Promote to primary adapter
                        $this->adapters[$adapter]->set($this->normalizeKey($key), $value, 3600);
                        $this->logCacheEvent("Cache HIT (fallback): {$key} ({$fallbackAdapter})");
                        return $value;
                    }
                }
            }
            
            $this->logCacheEvent("Cache MISS: {$key}");
            return $default;
            
        } catch (\Exception $e) {
            $this->logCacheEvent("Cache GET error for {$key}: " . $e->getMessage(), 'error');
            return $default;
        }
    }
    
    /**
     * Store an item in cache
     */
    public function set(string $key, mixed $value, int $ttl = null, string $adapter = null): bool {
        if (!$this->enabled) {
            return false;
        }
        
        $adapter = $adapter ?? $this->getBestAdapter($key);
        $normalizedKey = $this->normalizeKey($key);
        $ttl = $ttl ?? $this->cacheConfig['default_ttl'] ?? 3600;
        
        try {
            $success = false;
            
            // Try to set in primary adapter
            if (isset($this->adapters[$adapter])) {
                $success = $this->adapters[$adapter]->set($normalizedKey, $value, $ttl);
            }
            
            // Also set in fallback adapters for redundancy
            foreach ($this->fallbackAdapters as $fallbackAdapter) {
                if ($fallbackAdapter !== $adapter && isset($this->adapters[$fallbackAdapter])) {
                    try {
                        $this->adapters[$fallbackAdapter]->set($normalizedKey, $value, $ttl);
                    } catch (\Exception $e) {
                        $this->logCacheEvent("Fallback cache SET error for {$key}: " . $e->getMessage(), 'warning');
                    }
                }
            }
            
            $this->logCacheEvent("Cache SET: {$key} ({$adapter})");
            return $success;
            
        } catch (\Exception $e) {
            $this->logCacheEvent("Cache SET error for {$key}: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Store an item permanently
     */
    public function forever(string $key, mixed $value, string $adapter = null): bool {
        return $this->set($key, $value, 0, $adapter);
    }
    
    /**
     * Remove an item from cache
     */
    public function forget(string $key, string $adapter = null): bool {
        if (!$this->enabled) {
            return false;
        }
        
        $adapter = $adapter ?? $this->getBestAdapter($key);
        $normalizedKey = $this->normalizeKey($key);
        
        try {
            $success = false;
            
            // Try to remove from all adapters
            foreach ($this->adapters as $adapterName => $adapterObj) {
                try {
                    if ($adapterObj->has($normalizedKey)) {
                        $adapterObj->forget($normalizedKey);
                        $success = true;
                        $this->logCacheEvent("Cache FORGET: {$key} ({$adapterName})");
                    }
                } catch (\Exception $e) {
                    $this->logCacheEvent("Cache FORGET error for {$key} ({$adapterName}): " . $e->getMessage(), 'warning');
                }
            }
            
            return $success;
            
        } catch (\Exception $e) {
            $this->logCacheEvent("Cache FORGET error for {$key}: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Check if an item exists in cache
     */
    public function has(string $key, string $adapter = null): bool {
        if (!$this->enabled) {
            return false;
        }
        
        $adapter = $adapter ?? $this->getBestAdapter($key);
        
        try {
            return $this->adapters[$adapter]->has($this->normalizeKey($key));
        } catch (\Exception $e) {
            $this->logCacheEvent("Cache HAS error for {$key}: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Clear all cache items
     */
    public function flush(string $adapter = null): bool {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            $success = true;
            
            $adaptersToFlush = $adapter ? [$adapter] : array_keys($this->adapters);
            
            foreach ($adaptersToFlush as $adapterName) {
                if (isset($this->adapters[$adapterName])) {
                    try {
                        $this->adapters[$adapterName]->flush();
                        $this->logCacheEvent("Cache FLUSH: {$adapterName}");
                    } catch (\Exception $e) {
                        $this->logCacheEvent("Cache FLUSH error for {$adapterName}: " . $e->getMessage(), 'error');
                        $success = false;
                    }
                }
            }
            
            return $success;
            
        } catch (\Exception $e) {
            $this->logCacheEvent("Cache FLUSH error: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Get cache statistics
     */
    public function getStats(): array {
        $stats = [];
        
        foreach ($this->adapters as $adapterName => $adapter) {
            try {
                $adapterStats = $adapter->getStats();
                $stats[$adapterName] = array_merge([
                    'adapter' => $adapterName,
                    'available' => true
                ], $adapterStats);
            } catch (\Exception $e) {
                $stats[$adapterName] = [
                    'adapter' => $adapterName,
                    'available' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return [
            'enabled' => $this->enabled,
            'adapters' => $stats,
            'fallback_chain' => $this->fallbackAdapters,
            'total_adapters' => count($this->adapters)
        ];
    }
    
    /**
     * Cache warming - pre-populate cache with frequently accessed data
     */
    public function warmCache(array $items): int {
        $warmed = 0;
        
        foreach ($items as $key => $value) {
            $ttl = $value['ttl'] ?? 3600;
            $data = $value['data'] ?? $value;
            
            if ($this->set($key, $data, $ttl)) {
                $warmed++;
            }
        }
        
        $this->logCacheEvent("Cache warming completed: {$warmed} items warmed");
        return $warmed;
    }
    
    /**
     * Get the best adapter for a key
     */
    private function getBestAdapter(string $key): string {
        // For large items, prefer Redis or Memcached
        if (strlen($key) > 1000) {
            if (isset($this->adapters['redis'])) {
                return 'redis';
            }
            if (isset($this->adapters['memcached'])) {
                return 'memcached';
            }
        }
        
        // For session data, prefer Redis
        if (str_starts_with($key, 'session:')) {
            if (isset($this->adapters['redis'])) {
                return 'redis';
            }
        }
        
        // Default to first available adapter
        return $this->fallbackAdapters[0] ?? 'memory';
    }
    
    /**
     * Normalize cache key
     */
    private function normalizeKey(string $key): string {
        // Replace invalid characters
        $key = preg_replace('/[^a-zA-Z0-9:_\-\.]/', '_', $key);
        
        // Ensure key length is reasonable
        if (strlen($key) > 250) {
            $key = substr($key, 0, 200) . '_' . md5($key);
        }
        
        return $key;
    }
    
    /**
     * Log cache events
     */
    private function logCacheEvent(string $message, string $level = 'info'): void {
        if ($this->logger) {
            switch ($level) {
                case 'error':
                    $this->logger->error("AdvancedCache: {$message}");
                    break;
                case 'warning':
                    $this->logger->warning("AdvancedCache: {$message}");
                    break;
                default:
                    $this->logger->info("AdvancedCache: {$message}");
            }
        }
    }
    
    /**
     * Get cache adapter instance
     */
    public function getAdapter(string $name): ?object {
        return $this->adapters[$name] ?? null;
    }
    
    /**
     * Add custom cache adapter
     */
    public function addAdapter(string $name, object $adapter): void {
        $this->adapters[$name] = $adapter;
        $this->logCacheEvent("Custom adapter added: {$name}");
    }
}

/**
 * Redis Cache Adapter
 */
class RedisAdapter {
    private $redis;
    
    public function __construct($redis) {
        $this->redis = $redis;
    }
    
    public function get(string $key): mixed {
        $value = $this->redis->get($key);
        return $value !== false ? unserialize($value) : null;
    }
    
    public function set(string $key, mixed $value, int $ttl): bool {
        $serialized = serialize($value);
        return $ttl > 0 ? 
            $this->redis->setex($key, $ttl, $serialized) : 
            $this->redis->set($key, $serialized);
    }
    
    public function forget(string $key): bool {
        return $this->redis->del($key) > 0;
    }
    
    public function has(string $key): bool {
        return $this->redis->exists($key);
    }
    
    public function flush(): bool {
        return $this->redis->flushDB();
    }
    
    public function getStats(): array {
        $info = $this->redis->info();
        return [
            'total_keys' => array_sum(array_column($info, 'keys', 'db')),
            'used_memory' => $info['used_memory'] ?? 0,
            'hits' => $info['keyspace_hits'] ?? 0,
            'misses' => $info['keyspace_misses'] ?? 0
        ];
    }
}

/**
 * Memcached Adapter
 */
class MemcachedAdapter {
    private $memcached;
    
    public function __construct($memcached) {
        $this->memcached = $memcached;
    }
    
    public function get(string $key): mixed {
        $value = $this->memcached->get($key);
        return $value !== false ? $value : null;
    }
    
    public function set(string $key, mixed $value, int $ttl): bool {
        return $this->memcached->set($key, $value, time() + $ttl);
    }
    
    public function forget(string $key): bool {
        return $this->memcached->delete($key);
    }
    
    public function has(string $key): bool {
        $this->memcached->get($key);
        return $this->memcached->getResultCode() === \Memcached::RES_SUCCESS;
    }
    
    public function flush(): bool {
        return $this->memcached->flushBuffers();
    }
    
    public function getStats(): array {
        $stats = $this->memcached->getStats();
        return [
            'total_items' => $stats[$stats['pid']]['items'] ?? 0,
            'total_connections' => $stats[$stats['pid']]['curr_connections'] ?? 0,
            'bytes_read' => $stats[$stats['pid']]['bytes_read'] ?? 0,
            'bytes_written' => $stats[$stats['pid']]['bytes_written'] ?? 0
        ];
    }
}

/**
 * File Adapter
 */
class FileAdapter {
    private string $cacheDir;
    
    public function __construct(string $cacheDir) {
        $this->cacheDir = rtrim($cacheDir, '/') . '/';
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function get(string $key): mixed {
        $file = $this->getFilePath($key);
        if (!file_exists($file)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($file));
        if ($data['expires_at'] !== 0 && time() > $data['expires_at']) {
            unlink($file);
            return null;
        }
        
        return $data['data'];
    }
    
    public function set(string $key, mixed $value, int $ttl): bool {
        $file = $this->getFilePath($key);
        $data = [
            'data' => $value,
            'expires_at' => $ttl > 0 ? time() + $ttl : 0
        ];
        
        return file_put_contents($file, serialize($data)) !== false;
    }
    
    public function forget(string $key): bool {
        $file = $this->getFilePath($key);
        return !file_exists($file) || unlink($file);
    }
    
    public function has(string $key): bool {
        return $this->get($key) !== null;
    }
    
    public function flush(): bool {
        $files = glob($this->cacheDir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }
    
    public function getStats(): array {
        $files = glob($this->cacheDir . '*');
        $totalSize = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
            }
        }
        
        return [
            'total_files' => count($files),
            'total_size' => $totalSize,
            'cache_dir' => $this->cacheDir
        ];
    }
    
    private function getFilePath(string $key): string {
        $hash = md5($key);
        return $this->cacheDir . substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . $hash . '.cache';
    }
}

/**
 * Memory Adapter
 */
class MemoryAdapter {
    private array $cache = [];
    private array $expires = [];
    
    public function get(string $key): mixed {
        if (isset($this->expires[$key]) && time() > $this->expires[$key]) {
            $this->forget($key);
            return null;
        }
        
        return $this->cache[$key] ?? null;
    }
    
    public function set(string $key, mixed $value, int $ttl): bool {
        $this->cache[$key] = $value;
        $this->expires[$key] = $ttl > 0 ? time() + $ttl : 0;
        return true;
    }
    
    public function forget(string $key): bool {
        unset($this->cache[$key], $this->expires[$key]);
        return true;
    }
    
    public function has(string $key): bool {
        return isset($this->cache[$key]) && ($this->expires[$key] === 0 || time() <= $this->expires[$key]);
    }
    
    public function flush(): bool {
        $this->cache = [];
        $this->expires = [];
        return true;
    }
    
    public function getStats(): array {
        return [
            'total_items' => count($this->cache),
            'memory_usage' => strlen(serialize($this->cache))
        ];
    }
}
