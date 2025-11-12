<?php
class VersionChecker {
    private const VERSION_URL = 'https://raw.githubusercontent.com/yourusername/aec-calculator/main/version.json';
    private const CACHE_TIME = 3600; // 1 hour
    private const CACHE_FILE = __DIR__ . '/../db/version_cache.json';
    private const CURRENT_VERSION = '1.0.0'; // Update this when releasing new versions
    
    /**
     * Check if a new version is available
     * @return array|null Version info if update available, null if current
     */
    public static function checkForUpdates(): ?array {
        // Check cache first
        $cached = self::getCachedVersion();
        if ($cached !== null) {
            return self::compareVersion($cached);
        }

        // Fetch from remote
        $remote = self::fetchRemoteVersion();
        if ($remote) {
            self::cacheVersion($remote);
            return self::compareVersion($remote);
        }

        return null;
    }

    /**
     * Get the current installed version
     * @return string Current version
     */
    public static function getCurrentVersion(): string {
        return self::CURRENT_VERSION;
    }

    /**
     * Compare versions and return update info if available
     * @param array $versionInfo Version info from remote/cache
     * @return array|null Update info if available, null if current
     */
    private static function compareVersion(array $versionInfo): ?array {
        if (version_compare($versionInfo['version'], self::CURRENT_VERSION, '>')) {
            return [
                'version' => $versionInfo['version'],
                'releaseDate' => $versionInfo['releaseDate'],
                'description' => $versionInfo['description'] ?? '',
                'downloadUrl' => $versionInfo['downloadUrl'] ?? '',
                'current' => self::CURRENT_VERSION
            ];
        }
        return null;
    }

    /**
     * Get cached version info if not expired
     * @return array|null Cached version info or null if expired/missing
     */
    private static function getCachedVersion(): ?array {
        if (!file_exists(self::CACHE_FILE)) {
            return null;
        }

        $cache = json_decode(file_get_contents(self::CACHE_FILE), true);
        if (!$cache || !isset($cache['timestamp']) || !isset($cache['data'])) {
            return null;
        }

        if (time() - $cache['timestamp'] > self::CACHE_TIME) {
            return null;
        }

        return $cache['data'];
    }

    /**
     * Cache version info
     * @param array $versionInfo Version info to cache
     * @return void
     */
    private static function cacheVersion(array $versionInfo): void {
        $cache = [
            'timestamp' => time(),
            'data' => $versionInfo
        ];

        if (!is_dir(dirname(self::CACHE_FILE))) {
            mkdir(dirname(self::CACHE_FILE), 0777, true);
        }

        file_put_contents(self::CACHE_FILE, json_encode($cache, JSON_PRETTY_PRINT));
    }

    /**
     * Fetch version info from remote
     * @return array|null Version info or null on failure
     */
    private static function fetchRemoteVersion(): ?array {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        $response = @file_get_contents(self::VERSION_URL, false, $ctx);
        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['version'])) {
            return null;
        }

        return $data;
    }
}