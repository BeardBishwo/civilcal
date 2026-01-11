<?php

namespace App\Services;

class ShortcodeService
{
    /**
     * Parse content and replace valid shortcodes
     *
     * @param string $content
     * @return string
     */
    public static function parse($content)
    {
        if (empty($content)) {
            return '';
        }

        // Define available shortcodes and their replacement logic
        $replacements = [
            '{site_name}' => SettingsService::get('site_name', 'Bishwo Calculator'),
            '{year}' => date('Y'),
            '{site_url}' => app_base_url('/'),
            '{admin_url}' => app_base_url('/admin'),
            '{login_url}' => app_base_url('/login'),
        ];

        // Advanced Shortcodes (Tags that produce HTML)
        
        // {site_logo}
        $logoUrl = SettingsService::get('site_logo');
        if ($logoUrl) {
            $logoUrl = htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8');
            $replacements['{site_logo}'] = '<img src="' . app_base_url($logoUrl) . '" alt="' . htmlspecialchars($replacements['{site_name}']) . '" class="site-logo img-fluid" style="max-height: 50px;">';
        } else {
            $replacements['{site_logo}'] = '';
        }

        // {favicon}
        $faviconUrl = SettingsService::get('favicon');
        if ($faviconUrl) {
            $faviconUrl = htmlspecialchars($faviconUrl, ENT_QUOTES, 'UTF-8');
            $replacements['{favicon}'] = '<img src="' . app_base_url($faviconUrl) . '" alt="Favicon" class="site-favicon" style="width: 32px; height: 32px;">';
        } else {
            $replacements['{favicon}'] = '';
        }

        // Perform simple string replacements
        $content = strtr($content, $replacements);

        // Handle specific pattern-based shortcodes if needed (e.g. {media:123}) using regex
        // For now, we stick to the requested simple ones.

        return $content;
    }
}
