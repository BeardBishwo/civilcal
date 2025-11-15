<?php
namespace App\Services;

use App\Core\Database;
use App\Services\Logger;

class PluginManager
{
    private $db;
    private $pluginsDir;
    private $coreModulesDir;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->pluginsDir = BASE_PATH . "/plugins/calculator-plugins/";
        $this->coreModulesDir = BASE_PATH . "/modules/";
    }

    /**
     * Scan and register all plugins
     */
    public function scanPlugins()
    {
        $plugins = [];

        // Scan plugin directories
        if (is_dir($this->pluginsDir)) {
            $pluginDirs = array_filter(glob($this->pluginsDir . "*"), "is_dir");

            foreach ($pluginDirs as $pluginDir) {
                $pluginConfig = $this->loadPluginConfig($pluginDir);
                if ($pluginConfig) {
                    $plugins[] = $pluginConfig;
                }
            }
        }

        return $plugins;
    }

    /**
     * Load plugin configuration
     */
    private function loadPluginConfig($pluginDir)
    {
        $configFile = $pluginDir . "/plugin.json";

        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            $config["plugin_path"] = $pluginDir;
            $config["slug"] = basename($pluginDir);
            $config["is_core"] = false;

            // Check if plugin is active in database
            $stmt = $this->db->prepare(
                "SELECT is_active FROM plugins WHERE slug = ?",
            );
            $stmt->execute([$config["slug"]]);
            $dbPlugin = $stmt->fetch(\PDO::FETCH_ASSOC);
            $config["is_active"] = $dbPlugin
                ? (bool) $dbPlugin["is_active"]
                : false;

            return $config;
        }

        return null;
    }

    /**
     * Install a plugin (uploaded via admin)
     */
    public function installPlugin($zipFile)
    {
        if (!file_exists($zipFile)) {
            return false;
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFile) === true) {
            $pluginName = pathinfo($zipFile, PATHINFO_FILENAME);
            $extractPath = $this->pluginsDir . $pluginName;

            // Create directory
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            // Extract files
            $zip->extractTo($extractPath);
            $zip->close();

            // Validate manifest
            if (!$this->validatePluginManifest($extractPath)) {
                // cleanup on invalid manifest
                $this->removeDirectory($extractPath);
                Logger::warning("plugin_manifest_invalid", [
                    "zip" => basename($zipFile),
                ]);
                return false;
            }

            // Zip bomb guard: enforce limits
            [$totalBytes, $fileCount] = $this->dirStats($extractPath);
            $maxBytes = 50 * 1024 * 1024; // 50 MB
            $maxFiles = 5000;
            if ($totalBytes > $maxBytes || $fileCount > $maxFiles) {
                $this->removeDirectory($extractPath);
                Logger::warning("plugin_zip_limits_exceeded", [
                    "bytes" => $totalBytes,
                    "files" => $fileCount,
                ]);
                return false;
            }

            // Register in database
            return $this->registerPlugin($extractPath);
        }

        return false;
    }

    /**
     * Register plugin in database
     */
    private function registerPlugin($pluginDir)
    {
        $pluginConfig = $this->loadPluginConfig($pluginDir);

        if (!$pluginConfig) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO plugins (name, slug, type, description, version, author, author_url, plugin_path, main_file, is_active, is_core, settings, requirements)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                name = VALUES(name), description = VALUES(description), version = VALUES(version), updated_at = CURRENT_TIMESTAMP
            ");

            $mainFile =
                $pluginConfig["entrypoint"] ??
                ($pluginConfig["main_file"] ?? "");
            return $stmt->execute([
                $pluginConfig["name"],
                $pluginConfig["slug"],
                $pluginConfig["type"],
                $pluginConfig["description"],
                $pluginConfig["version"],
                $pluginConfig["author"] ?? "",
                $pluginConfig["author_url"] ?? "",
                $pluginConfig["plugin_path"],
                $mainFile,
                0, // Not active by default
                0, // Not core plugin
                json_encode($pluginConfig["settings"] ?? []),
                json_encode($pluginConfig["requirements"] ?? []),
            ]);
        } catch (\Exception $e) {
            error_log("Plugin registration error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Activate a plugin
     */
    public function activatePlugin($pluginSlug)
    {
        $plugin = $this->getPlugin($pluginSlug);

        if ($plugin && $this->checkRequirements($plugin)) {
            try {
                // Update database
                $stmt = $this->db->prepare(
                    "UPDATE plugins SET is_active = 1 WHERE slug = ?",
                );
                $stmt->execute([$pluginSlug]);

                // Run activation hooks
                $this->runActivationHook($plugin);

                return true;
            } catch (\Exception $e) {
                error_log("Plugin activation error: " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    /**
     * Deactivate a plugin
     */
    public function deactivatePlugin($pluginSlug)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE plugins SET is_active = 0 WHERE slug = ?",
            );
            $stmt->execute([$pluginSlug]);

            // Run deactivation hooks
            $plugin = $this->getPlugin($pluginSlug);
            if ($plugin) {
                $this->runDeactivationHook($plugin);
            }

            return true;
        } catch (\Exception $e) {
            error_log("Plugin deactivation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a plugin
     */
    public function deletePlugin($pluginSlug)
    {
        try {
            // First deactivate if active
            $this->deactivatePlugin($pluginSlug);

            // Remove from database
            $stmt = $this->db->prepare("DELETE FROM plugins WHERE slug = ?");
            $stmt->execute([$pluginSlug]);

            // Remove files
            $plugin = $this->getPlugin($pluginSlug);
            if (
                $plugin &&
                !$plugin["is_core"] &&
                isset($plugin["plugin_path"])
            ) {
                $this->removeDirectory($plugin["plugin_path"]);
            }

            return true;
        } catch (\Exception $e) {
            error_log("Plugin deletion error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all active calculators (including plugins)
     */
    public function getActiveCalculators()
    {
        $calculators = [];

        // 1. Get core calculators (your existing modules)
        $calculators = array_merge($calculators, $this->getCoreCalculators());

        // 2. Get plugin calculators
        $calculators = array_merge($calculators, $this->getPluginCalculators());

        return $calculators;
    }

    /**
     * Get your existing modules as core calculators
     */
    private function getCoreCalculators()
    {
        $coreCalculators = [];
        $disciplines = [
            "civil",
            "electrical",
            "plumbing",
            "hvac",
            "fire",
            "structural",
            "estimation",
            "mep",
            "project-management",
            "site",
        ];

        foreach ($disciplines as $discipline) {
            $disciplinePath = $this->coreModulesDir . $discipline;
            if (is_dir($disciplinePath)) {
                $categories = array_filter(
                    glob($disciplinePath . "/*"),
                    "is_dir",
                );

                foreach ($categories as $category) {
                    $calculators = array_filter(
                        glob($category . "/*.php"),
                        "is_file",
                    );

                    foreach ($calculators as $calculator) {
                        $coreCalculators[] = [
                            "type" => "core",
                            "discipline" => $discipline,
                            "category" => basename($category),
                            "calculator" => pathinfo(
                                $calculator,
                                PATHINFO_FILENAME,
                            ),
                            "file_path" => $calculator,
                            "name" => $this->getCalculatorName($calculator),
                        ];
                    }
                }
            }
        }

        return $coreCalculators;
    }

    /**
     * Get active plugin calculators
     */
    private function getPluginCalculators()
    {
        $pluginCalculators = [];
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM plugins WHERE is_active = 1",
            );
            $stmt->execute();
            $activePlugins = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($activePlugins as $plugin) {
                $pluginConfig = $this->getPlugin($plugin["slug"]);
                if (
                    !$pluginConfig ||
                    empty($pluginConfig["calculators"]) ||
                    !is_array($pluginConfig["calculators"])
                ) {
                    continue;
                }
                $base = rtrim($plugin["plugin_path"] ?? "", "/\\");
                foreach (
                    $pluginConfig["calculators"]
                    as $calcSlug => $calcConfig
                ) {
                    $cat =
                        $calcConfig["category"] ??
                        ($calcConfig["discipline"] ?? "general");
                    $fileRel =
                        $calcConfig["file"] ?? ($calcConfig["file_path"] ?? "");
                    if (!$fileRel || !$base) {
                        continue;
                    }
                    $full = $base . "/" . ltrim($fileRel, "/\\");
                    if (!is_file($full)) {
                        continue;
                    }
                    $pluginCalculators[] = [
                        "type" => "plugin",
                        "discipline" => $cat,
                        "category" => $cat,
                        "calculator" => is_string($calcSlug)
                            ? $calcSlug
                            : (pathinfo($fileRel, PATHINFO_FILENAME) ?:
                            ""),
                        "file_path" => $full,
                        "name" =>
                            $calcConfig["name"] ??
                            (is_string($calcSlug)
                                ? ucwords(
                                    str_replace(["-", "_"], " ", $calcSlug),
                                )
                                : ""),
                        "plugin_slug" => $plugin["slug"] ?? null,
                        "plugin_name" => $plugin["name"] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            error_log("Error getting plugin calculators: " . $e->getMessage());
        }
        return $pluginCalculators;
    }

    /**
     * Validate plugin.json manifest and required files
     */
    private function validatePluginManifest(string $pluginDir): bool
    {
        $manifest = rtrim($pluginDir, "/\\") . "/plugin.json";
        if (!is_file($manifest)) {
            return false;
        }
        $data = json_decode(file_get_contents($manifest), true);
        if (!is_array($data)) {
            return false;
        }
        // Required fields
        $name = $data["name"] ?? ($data["id"] ?? null);
        $version = $data["version"] ?? null;
        $entry = $data["entrypoint"] ?? ($data["main_file"] ?? null);
        if (!$name || !$version) {
            return false;
        }
        // If entry defined, ensure file exists
        if ($entry) {
            $entryPath = rtrim($pluginDir, "/\\") . "/" . ltrim($entry, "/\\");
            if (!is_file($entryPath)) {
                return false;
            }
        }
        // calculators mapping optional; if present, ensure array and validate entries
        if (isset($data["calculators"])) {
            if (!is_array($data["calculators"])) {
                return false;
            }
            $baseReal = realpath($pluginDir) ?: $pluginDir;
            foreach ($data["calculators"] as $slug => $cfg) {
                if (!is_array($cfg)) {
                    return false;
                }
                $nameOk =
                    isset($cfg["name"]) &&
                    is_string($cfg["name"]) &&
                    $cfg["name"] !== "";
                $catOk =
                    isset($cfg["category"]) &&
                    is_string($cfg["category"]) &&
                    $cfg["category"] !== "";
                $fileRel = $cfg["file"] ?? ($cfg["file_path"] ?? null);
                if (!$nameOk || !$catOk || !$fileRel) {
                    return false;
                }
                $full = rtrim($pluginDir, "/\\") . "/" . ltrim($fileRel, "/\\");
                $fullReal = realpath($full);
                if ($fullReal === false || !is_file($fullReal)) {
                    return false;
                }
                // Prevent path traversal: ensure file is inside plugin dir
                if (strpos($fullReal, $baseReal) !== 0) {
                    return false;
                }
            }
        }
        return true;
    }

    private function dirStats(string $dir): array
    {
        $bytes = 0;
        $count = 0;
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $dir,
                \RecursiveDirectoryIterator::SKIP_DOTS,
            ),
        );
        foreach ($it as $file) {
            if ($file->isFile()) {
                $bytes += $file->getSize();
                $count++;
            }
        }
        return [$bytes, $count];
    }

    /**
     * Get plugin by slug
     */
    public function getPlugin($slug)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM plugins WHERE slug = ?");
            $stmt->execute([$slug]);
            $plugin = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($plugin) {
                // Load plugin configuration
                if ($plugin["plugin_path"]) {
                    $configFile = $plugin["plugin_path"] . "/plugin.json";
                    if (file_exists($configFile)) {
                        $pluginConfig = json_decode(
                            file_get_contents($configFile),
                            true,
                        );
                        $plugin = array_merge($plugin, $pluginConfig);
                    }
                }
            }

            return $plugin;
        } catch (\Exception $e) {
            error_log("Error getting plugin: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check plugin requirements
     */
    private function checkRequirements($plugin)
    {
        $requirements = $plugin["requirements"] ?? [];

        // Check PHP version
        if (isset($requirements["php_version"])) {
            if (
                version_compare(PHP_VERSION, $requirements["php_version"], "<")
            ) {
                return false;
            }
        }

        // Check required plugins
        if (isset($requirements["required_plugins"])) {
            foreach ($requirements["required_plugins"] as $requiredPlugin) {
                $stmt = $this->db->prepare(
                    "SELECT is_active FROM plugins WHERE slug = ?",
                );
                $stmt->execute([$requiredPlugin]);
                $required = $stmt->fetch(\PDO::FETCH_ASSOC);
                if (!$required || !$required["is_active"]) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Run activation hook
     */
    private function runActivationHook($plugin)
    {
        $hookFile = $plugin["plugin_path"] . "/activate.php";
        if (file_exists($hookFile)) {
            include $hookFile;
        }
    }

    /**
     * Run deactivation hook
     */
    private function runDeactivationHook($plugin)
    {
        $hookFile = $plugin["plugin_path"] . "/deactivate.php";
        if (file_exists($hookFile)) {
            include $hookFile;
        }
    }

    /**
     * Extract calculator name from file
     */
    private function getCalculatorName($filePath)
    {
        $content = file_get_contents($filePath);
        if (
            preg_match("/<h[1-6][^>]*>([^<]+)<\/h[1-6]>/i", $content, $matches)
        ) {
            return trim($matches[1]);
        }

        // Fallback to filename
        return ucwords(
            str_replace(
                ["-", "_"],
                " ",
                pathinfo($filePath, PATHINFO_FILENAME),
            ),
        );
    }

    /**
     * Remove directory and its contents
     */
    private function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), [".", ".."]);
            foreach ($files as $file) {
                $path = $dir . "/" . $file;
                is_dir($path) ? $this->removeDirectory($path) : unlink($path);
            }
            rmdir($dir);
        }
    }

    /**
     * Boot all active plugins by requiring their entrypoints
     */
    public function bootAll(): void
    {
        try {
            // Check if plugins table exists before attempting to query it
            $tablesStmt = $this->db->query("SHOW TABLES LIKE 'plugins'");
            if (!$tablesStmt || $tablesStmt->rowCount() === 0) {
                Logger::info("plugins_table_not_found", [
                    "message" =>
                        "Plugins table does not exist, skipping plugin boot",
                ]);
                return;
            }

            $stmt = $this->db->prepare(
                "SELECT slug, plugin_path, main_file FROM plugins WHERE is_active = 1",
            );
            $stmt->execute();
            $active = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($active as $plugin) {
                $entry = $plugin["main_file"] ?? "";
                if (!$entry) {
                    // Fallbacks
                    $candidates = [
                        "entrypoint.php",
                        "bootstrap.php",
                        "index.php",
                    ];
                    foreach ($candidates as $cand) {
                        if (
                            is_file(
                                rtrim($plugin["plugin_path"], "/\\") .
                                    "/" .
                                    $cand,
                            )
                        ) {
                            $entry = $cand;
                            break;
                        }
                    }
                }
                if ($entry) {
                    $path =
                        rtrim($plugin["plugin_path"], "/\\") .
                        "/" .
                        ltrim($entry, "/\\");
                    if (is_file($path)) {
                        try {
                            require_once $path;
                            Logger::info("plugin_booted", [
                                "slug" => $plugin["slug"],
                                "entry" => $entry,
                            ]);
                        } catch (\Throwable $e) {
                            Logger::exception($e, [
                                "when" => "boot_plugin",
                                "slug" => $plugin["slug"],
                            ]);
                        }
                    } else {
                        Logger::warning("plugin_entry_missing", [
                            "slug" => $plugin["slug"],
                            "path" => $path,
                        ]);
                    }
                } else {
                    Logger::warning("plugin_entry_undefined", [
                        "slug" => $plugin["slug"],
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Logger::exception($e, ["when" => "boot_all_plugins"]);
        }
    }
}
?>
