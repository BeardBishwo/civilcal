<?php

namespace App\Services;

use App\Core\Database;

/**
 * Translation Service for Multi-language Support
 */
class TranslationService
{
    private static $cache = [];
    private static $currentLocale = 'en';
    private static $fallbackLocale = 'en';
    
    /**
     * Set current locale
     */
    public static function setLocale($locale)
    {
        self::$currentLocale = $locale;
    }
    
    /**
     * Get current locale
     */
    public static function getLocale()
    {
        return self::$currentLocale;
    }
    
    /**
     * Translate a key
     */
    public static function trans($key, $locale = null, $default = null)
    {
        $locale = $locale ?? self::$currentLocale;
        $cacheKey = $locale . '.' . $key;
        
        // Check cache
        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }
        
        // Get from database
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT translation_value 
            FROM translations 
            WHERE translation_key = ? AND locale = ?
            LIMIT 1
        ");
        $stmt->execute([$key, $locale]);
        $result = $stmt->fetch();
        
        if ($result) {
            self::$cache[$cacheKey] = $result['translation_value'];
            return $result['translation_value'];
        }
        
        // Try fallback locale
        if ($locale !== self::$fallbackLocale) {
            return self::trans($key, self::$fallbackLocale, $default);
        }
        
        // Return default or key
        return $default ?? $key;
    }
    
    /**
     * Get all translations for a locale
     */
    public static function getAllTranslations($locale = null, $group = null)
    {
        $locale = $locale ?? self::$currentLocale;
        $db = Database::getInstance();
        
        if ($group) {
            $stmt = $db->prepare("
                SELECT translation_key, translation_value 
                FROM translations 
                WHERE locale = ? AND translation_group = ?
            ");
            $stmt->execute([$locale, $group]);
        } else {
            $stmt = $db->prepare("
                SELECT translation_key, translation_value 
                FROM translations 
                WHERE locale = ?
            ");
            $stmt->execute([$locale]);
        }
        
        $translations = [];
        while ($row = $stmt->fetch()) {
            $translations[$row['translation_key']] = $row['translation_value'];
        }
        
        return $translations;
    }
    
    /**
     * Add or update translation
     */
    public static function addTranslation($key, $value, $locale, $group = 'general')
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            INSERT INTO translations (translation_key, locale, translation_value, translation_group)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE translation_value = VALUES(translation_value), translation_group = VALUES(translation_group)
        ");
        
        $result = $stmt->execute([$key, $locale, $value, $group]);
        
        // Clear cache
        unset(self::$cache[$locale . '.' . $key]);
        
        return $result;
    }
    
    /**
     * Delete translation
     */
    public static function deleteTranslation($key, $locale)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM translations WHERE translation_key = ? AND locale = ?");
        $result = $stmt->execute([$key, $locale]);
        
        // Clear cache
        unset(self::$cache[$locale . '.' . $key]);
        
        return $result;
    }
    
    /**
     * Get available locales
     */
    public static function getAvailableLocales()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT DISTINCT locale FROM translations ORDER BY locale");
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    /**
     * Export translations for a locale
     */
    public static function exportTranslations($locale)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT translation_key, translation_value, translation_group
            FROM translations 
            WHERE locale = ?
            ORDER BY translation_group, translation_key
        ");
        $stmt->execute([$locale]);
        
        $translations = [];
        while ($row = $stmt->fetch()) {
            $group = $row['translation_group'];
            if (!isset($translations[$group])) {
                $translations[$group] = [];
            }
            $translations[$group][$row['translation_key']] = $row['translation_value'];
        }
        
        return $translations;
    }
    
    /**
     * Import translations from array
     */
    public static function importTranslations($locale, $translations, $overwrite = false)
    {
        $db = Database::getInstance();
        $count = 0;
        
        foreach ($translations as $group => $items) {
            foreach ($items as $key => $value) {
                if ($overwrite) {
                    self::addTranslation($key, $value, $locale, $group);
                } else {
                    // Only add if doesn't exist
                    $stmt = $db->prepare("
                        SELECT COUNT(*) FROM translations 
                        WHERE translation_key = ? AND locale = ?
                    ");
                    $stmt->execute([$key, $locale]);
                    
                    if ($stmt->fetchColumn() == 0) {
                        self::addTranslation($key, $value, $locale, $group);
                    }
                }
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Clear translation cache
     */
    public static function clearCache()
    {
        self::$cache = [];
    }
}
