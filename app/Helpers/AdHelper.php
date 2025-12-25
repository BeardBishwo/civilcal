<?php

namespace App\Helpers;

use App\Models\Advertisement;

class AdHelper
{
    /**
     * Render advertisements for a specific location
     * 
     * @param string $location Location identifier (e.g., 'header_top')
     * @return string HTML output of ads
     */
    public static function render($location)
    {
        try {
            $adModel = new Advertisement();
            $ads = $adModel->getActiveByLocation($location);
            
            if (empty($ads)) {
                return '';
            }
            
            $html = '';
            foreach ($ads as $ad) {
                $html .= "<!-- Ad: {$ad['name']} -->\n";
                $html .= "<div class=\"ad-container ad-{$location}\">\n";
                $html .= $ad['code'] . "\n";
                $html .= "</div>\n";
            }
            
            return $html;
            
        } catch (\Exception $e) {
            // Silently fail to not break the site
            error_log("AdHelper Error: " . $e->getMessage());
            return '';
        }
    }
}
