<?php

namespace App\Helpers;

use App\Models\Advertisement;

/**
 * AdHelper
 * Handles fetching and rendering of advertisement slots
 */
class AdHelper
{
    private static $adModel = null;
    private static $cache = [];

    /**
     * Get Ad Model instance
     */
    private static function getModel()
    {
        if (self::$adModel === null) {
            self::$adModel = new Advertisement();
        }
        return self::$adModel;
    }

    /**
     * Fetch active ads for a specific location
     * 
     * @param string $location Example: 'header_top', 'sidebar_top', 'result_bottom'
     * @return array List of active ads
     */
    public static function getAds($location)
    {
        if (isset(self::$cache[$location])) {
            return self::$cache[$location];
        }

        $ads = self::getModel()->getActiveByLocation($location);
        self::$cache[$location] = $ads;
        return $ads;
    }

    /**
     * Render a specific ad slot
     * 
     * @param string $location The slot name
     * @param string $wrapper Optional wrapper class
     * @return string HTML content of the ad
     */
    public static function show($location, $wrapper = 'ad-slot-wrapper')
    {
        $ads = self::getAds($location);
        
        if (empty($ads)) {
            return '';
        }

        // Randomly pick one ad if multiple exist for the same slot
        $ad = $ads[array_rand($ads)];

        $html = "<!-- Ad Slot: {$location} -->\n";
        $html .= "<div id='ad-{$location}' class='{$wrapper} ad-unit' data-ad-id='{$ad['id']}'>\n";
        $html .= $ad['code'] . "\n";
        $html .= "</div>\n";

        return $html;
    }
}
