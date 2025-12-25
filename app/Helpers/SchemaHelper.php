<?php

namespace App\Helpers;

class SchemaHelper
{
    /**
     * Generate WebApplication Schema for Homepage
     */
    public static function getHomepageSchema()
    {
        $url = app_base_url('/');
        $siteName = \App\Services\SettingsService::get('site_name', 'Civil Cal');
        $description = \App\Services\SettingsService::get('site_description', 'Professional Engineering Calculators Suite');
        $logo = app_base_url('public/assets/images/logo.png'); // Default fallback

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => $siteName,
            'url' => $url,
            'description' => $description,
            'applicationCategory' => 'DesignApplication',
            'operatingSystem' => 'All',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD'
            ]
        ];

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate SoftwareApplication Schema for Calculator Pages
     */
    public static function getCalculatorSchema($title, $description, $category = 'Engineering')
    {
        // Get current full URL
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $currentUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        $siteName = \App\Services\SettingsService::get('site_name', 'Civil Cal');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $title,
            'url' => $currentUrl,
            'description' => $description,
            'applicationCategory' => 'DesignApplication', // or 'BusinessApplication' / 'UtilitiesApplication'
            'operatingSystem' => 'Web',
            'browserRequirements' => 'Requires Javascript',
            'softwareVersion' => '1.0',
            'author' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => app_base_url('/')
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock'
            ]
        ];

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate BreadcrumbList Schema
     */
    public static function getBreadcrumbSchema($items)
    {
        $itemListElement = [];
        $position = 1;

        // Always add Home first
        $itemListElement[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Home',
            'item' => app_base_url('/')
        ];

        foreach ($items as $name => $url) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => strip_tags($name), // Ensure no HTML in name
                'item' => strpos($url, 'http') === 0 ? $url : app_base_url($url)
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        ];

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
