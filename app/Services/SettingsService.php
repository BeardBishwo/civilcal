<?php

namespace App\Services;

use App\Core\Database;

class SettingsService
{
    private static $cache = [];

    public static function get($key, $default = null)
    {
        // Check cache first
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT setting_value, setting_type FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();

        if ($result) {
            $value = self::castValue($result['setting_value'], $result['setting_type']);
            self::$cache[$key] = $value;
            return $value;
        }

        return $default;
    }

    public static function set($key, $value, $type = 'string', $group = 'general', $description = '')
    {
        try {
            $db = Database::getInstance();

            // Prepare value for storage
            $storageValue = self::prepareValueForStorage($value, $type);

            // Check if the value actually changed
            $currentValue = self::get($key);
            $valueChanged = ($currentValue !== $value);

            $stmt = $db->prepare("
                INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, description)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                setting_type = VALUES(setting_type),
                setting_group = VALUES(setting_group),
                description = VALUES(description),
                updated_at = NOW()
            ");

            $result = $stmt->execute([$key, $storageValue, $type, $group, $description]);

            // Update cache
            if ($result) {
                self::$cache[$key] = $value;
            }

            // Return true only if value actually changed or if it's a new setting
            return $result && ($valueChanged || $currentValue === null);
        } catch (\Exception $e) {
            // Log the error but don't throw to maintain compatibility
            error_log("SettingsService::set() failed for key '$key': " . $e->getMessage());
            return false;
        }
    }

    public static function getAll($group = null)
    {
        $db = Database::getInstance();

        if ($group) {
            $stmt = $db->prepare("SELECT * FROM settings WHERE setting_group = ? ORDER BY setting_key");
            $stmt->execute([$group]);
        } else {
            $stmt = $db->prepare("SELECT * FROM settings ORDER BY setting_group, setting_key");
            $stmt->execute();
        }

        $settings = $stmt->fetchAll();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = self::castValue($setting['setting_value'], $setting['setting_type']);
        }

        return $result;
    }

    /**
     * Get settings by group (alias for getAll with group filter)
     */
    public static function getByGroup($group)
    {
        return self::getAll($group);
    }

    public static function getGroups()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT DISTINCT setting_group FROM settings ORDER BY setting_group");
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool)$value;
            case 'integer':
                return (int)$value;
            case 'json':
                return json_decode($value, true) ?? $value;
            default:
                return $value;
        }
    }

    private static function prepareValueForStorage($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'json':
                return json_encode($value);
            default:
                return (string)$value;
        }
    }

    public static function clearCache()
    {
        self::$cache = [];
    }

    public static function bulkSet($settings)
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            foreach ($settings as $key => $value) {
                // Determine type based on value
                $type = 'string';
                if (is_bool($value)) {
                    $type = 'boolean';
                } elseif (is_int($value)) {
                    $type = 'integer';
                } elseif (is_array($value)) {
                    $type = 'json';
                }

                self::set($key, $value, $type);
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}
