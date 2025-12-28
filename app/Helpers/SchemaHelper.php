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
        $logo = \App\Services\SettingsService::get('site_logo') ?: app_base_url('public/assets/images/logo.png');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => $siteName,
            'url' => $url,
            'description' => $description,
            'applicationCategory' => 'DesignApplication',
            'operatingSystem' => 'All',
            'image' => $logo,
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

    /**
     * Generate BlogPosting Schema
     */
    public static function getBlogPostSchema($post)
    {
        if (empty($post)) return '';

        $siteName = \App\Services\SettingsService::get('site_name', 'Civil Cal');
        $logo = \App\Services\SettingsService::get('site_logo') ?: app_base_url('public/assets/images/logo.png');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post['title'] ?? '',
            'description' => $post['seo_description'] ?: ($post['excerpt'] ?? ''),
            'image' => $post['featured_image'] ?? $logo,
            'datePublished' => $post['created_at'] ?? date('c'),
            'dateModified' => $post['updated_at'] ?? ($post['created_at'] ?? date('c')),
            'author' => [
                '@type' => 'Person',
                'name' => $post['author_name'] ?? 'Admin'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logo
                ]
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => app_base_url('blog/' . ($post['slug'] ?? ''))
            ]
        ];

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate Organization Schema
     */
    public static function getOrganizationSchema()
    {
        $siteName = \App\Services\SettingsService::get('site_name', 'Civil Cal');
        $logo = \App\Services\SettingsService::get('site_logo') ?: app_base_url('public/assets/images/logo.png');
        $url = app_base_url('/');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $url,
            'logo' => $logo,
            'sameAs' => [
                // Add social profile URLs if available in settings
                \App\Services\SettingsService::get('facebook_url'),
                \App\Services\SettingsService::get('twitter_url'),
                \App\Services\SettingsService::get('linkedin_url')
            ]
        ];

        // Clean up empty sameAs entries
        $schema['sameAs'] = array_values(array_filter($schema['sameAs']));
        if (empty($schema['sameAs'])) unset($schema['sameAs']);

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
