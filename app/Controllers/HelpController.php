<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Help Controller
 * Handles help center functionality - articles, FAQs, search
 */
class HelpController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show help center main page
     */
    public function index()
    {
        $this->setTitle('Help Center - Engineering Calculator Support');
        $this->setDescription('Get help with engineering calculations, tutorials, and frequently asked questions');
        $this->setCategory('help');

        $data = [
            'page_title' => 'Help Center',
            'featured_articles' => $this->getFeaturedArticles(),
            'categories' => $this->getHelpCategories(),
            'common_questions' => $this->getCommonQuestions(),
            'recent_articles' => $this->getRecentArticles()
        ];

        $this->view('help/index', $data);
    }

    /**
     * Show help article by slug
     */
    public function article($slug)
    {
        $article = $this->getArticleBySlug($slug);
        
        if (!$article) {
            $this->redirect('/help');
            return;
        }

        $this->setTitle($article['title'] . ' - Help Center');
        $this->setDescription($article['excerpt']);
        $this->setCategory('help');

        $data = [
            'page_title' => $article['title'],
            'article' => $article,
            'related_articles' => $this->getRelatedArticles($article['category'], $article['id'])
        ];

        $this->view('help/article', $data);
    }

    /**
     * Show help category
     */
    public function category($category)
    {
        $categoryInfo = $this->getCategoryInfo($category);
        
        if (!$categoryInfo) {
            $this->redirect('/help');
            return;
        }

        $this->setTitle($categoryInfo['name'] . ' - Help Center');
        $this->setDescription('Help articles about ' . $categoryInfo['name']);
        $this->setCategory('help');

        $data = [
            'page_title' => $categoryInfo['name'],
            'category' => $categoryInfo,
            'articles' => $this->getArticlesByCategory($category)
        ];

        $this->view('help/category', $data);
    }

    /**
     * Search help articles
     */
    public function search()
    {
        $query = $_GET['q'] ?? '';
        $results = [];
        
        if (!empty($query)) {
            $results = $this->searchArticles($query);
        }

        $this->setTitle('Search Results - Help Center');
        $this->setCategory('help');

        $data = [
            'page_title' => 'Search Results',
            'query' => $query,
            'results' => $results,
            'total_results' => count($results)
        ];

        $this->view('help/search', $data);
    }

    /**
     * Get featured articles
     */
    private function getFeaturedArticles()
    {
        return [
            [
                'id' => 1,
                'title' => 'Getting Started with Engineering Calculators',
                'excerpt' => 'Learn the basics of using our engineering calculation tools effectively.',
                'slug' => 'getting-started',
                'category' => 'getting-started',
                'icon' => 'fas fa-play-circle',
                'color' => '#10b981'
            ],
            [
                'id' => 2,
                'title' => 'Understanding Civil Engineering Calculations',
                'excerpt' => 'Comprehensive guide to civil engineering formulas and calculations.',
                'slug' => 'civil-engineering-guide',
                'category' => 'civil-engineering',
                'icon' => 'fas fa-hard-hat',
                'color' => '#3b82f6'
            ],
            [
                'id' => 3,
                'title' => 'Electrical Load Calculations Made Easy',
                'excerpt' => 'Step-by-step guide to electrical load calculations and safety factors.',
                'slug' => 'electrical-load-calculations',
                'category' => 'electrical-engineering',
                'icon' => 'fas fa-bolt',
                'color' => '#f59e0b'
            ]
        ];
    }

    /**
     * Get help categories
     */
    private function getHelpCategories()
    {
        return [
            [
                'slug' => 'getting-started',
                'name' => 'Getting Started',
                'description' => 'New to our platform? Start here for basic tutorials and setup guides.',
                'icon' => 'fas fa-play-circle',
                'color' => '#10b981',
                'article_count' => 8
            ],
            [
                'slug' => 'civil-engineering',
                'name' => 'Civil Engineering',
                'description' => 'Concrete, steel, foundation, and structural calculation guides.',
                'icon' => 'fas fa-hard-hat',
                'color' => '#3b82f6',
                'article_count' => 15
            ],
            [
                'slug' => 'electrical-engineering',
                'name' => 'Electrical Engineering',
                'description' => 'Load calculations, wire sizing, and electrical system design.',
                'icon' => 'fas fa-bolt',
                'color' => '#f59e0b',
                'article_count' => 12
            ],
            [
                'slug' => 'mechanical-engineering',
                'name' => 'Mechanical Engineering',
                'description' => 'HVAC, plumbing, and mechanical system calculations.',
                'icon' => 'fas fa-cogs',
                'color' => '#8b5cf6',
                'article_count' => 10
            ],
            [
                'slug' => 'account-settings',
                'name' => 'Account & Settings',
                'description' => 'Manage your profile, preferences, and account settings.',
                'icon' => 'fas fa-user-cog',
                'color' => '#ef4444',
                'article_count' => 6
            ],
            [
                'slug' => 'troubleshooting',
                'name' => 'Troubleshooting',
                'description' => 'Common issues and their solutions.',
                'icon' => 'fas fa-tools',
                'color' => '#6b7280',
                'article_count' => 9
            ]
        ];
    }

    /**
     * Get common questions
     */
    private function getCommonQuestions()
    {
        return [
            [
                'question' => 'How do I create an account?',
                'answer' => 'Click the "Login" button in the top right corner, then select "Create Account". Fill in your details and verify your email address.',
                'category' => 'getting-started'
            ],
            [
                'question' => 'Are the calculation results accurate?',
                'answer' => 'Yes, all our calculators are based on industry-standard formulas and codes. However, always verify critical calculations with a licensed professional.',
                'category' => 'general'
            ],
            [
                'question' => 'Can I save my calculations?',
                'answer' => 'Yes! Create an account to save your calculations, access calculation history, and bookmark your favorite tools.',
                'category' => 'account-settings'
            ],
            [
                'question' => 'What units are supported?',
                'answer' => 'We support both metric (SI) and imperial (US) units. You can switch between them in your profile settings.',
                'category' => 'general'
            ],
            [
                'question' => 'How do I report a bug or issue?',
                'answer' => 'Use the feedback form in your profile menu or contact our support team directly through the contact page.',
                'category' => 'troubleshooting'
            ],
            [
                'question' => 'Is there a mobile app?',
                'answer' => 'Our web application is fully responsive and works great on mobile devices. A dedicated mobile app is in development.',
                'category' => 'general'
            ]
        ];
    }

    /**
     * Get recent articles
     */
    private function getRecentArticles()
    {
        return [
            [
                'title' => 'New Steel Design Calculator Released',
                'slug' => 'steel-design-calculator',
                'date' => '2025-11-10',
                'category' => 'civil-engineering'
            ],
            [
                'title' => 'Updated Electrical Load Calculation Methods',
                'slug' => 'electrical-load-updates',
                'date' => '2025-11-08',
                'category' => 'electrical-engineering'
            ],
            [
                'title' => 'How to Export Calculation Results',
                'slug' => 'export-calculations',
                'date' => '2025-11-05',
                'category' => 'getting-started'
            ]
        ];
    }

    /**
     * Get article by slug
     */
    private function getArticleBySlug($slug)
    {
        $articles = [
            'getting-started' => [
                'id' => 1,
                'title' => 'Getting Started with Engineering Calculators',
                'slug' => 'getting-started',
                'category' => 'getting-started',
                'excerpt' => 'Learn the basics of using our engineering calculation tools effectively.',
                'content' => $this->getGettingStartedContent(),
                'author' => 'Engineering Team',
                'date' => '2025-11-01',
                'updated' => '2025-11-10',
                'tags' => ['tutorial', 'basics', 'getting-started']
            ],
            'civil-engineering-guide' => [
                'id' => 2,
                'title' => 'Understanding Civil Engineering Calculations',
                'slug' => 'civil-engineering-guide',
                'category' => 'civil-engineering',
                'excerpt' => 'Comprehensive guide to civil engineering formulas and calculations.',
                'content' => $this->getCivilEngineeringContent(),
                'author' => 'Civil Engineering Team',
                'date' => '2025-10-28',
                'updated' => '2025-11-08',
                'tags' => ['civil', 'concrete', 'steel', 'structural']
            ]
        ];

        return $articles[$slug] ?? null;
    }

    /**
     * Get getting started content
     */
    private function getGettingStartedContent()
    {
        return '
        <h2>Welcome to Engineering Calculator Pro</h2>
        <p>Our platform provides professional-grade engineering calculation tools for civil, electrical, mechanical, and structural engineers. This guide will help you get started quickly.</p>
        
        <h3>Creating Your Account</h3>
        <p>To access advanced features like calculation history and favorites, create a free account:</p>
        <ol>
            <li>Click the "Login" button in the top navigation</li>
            <li>Select "Create Account"</li>
            <li>Fill in your professional details</li>
            <li>Verify your email address</li>
        </ol>
        
        <h3>Using Calculators</h3>
        <p>Our calculators are organized by engineering discipline:</p>
        <ul>
            <li><strong>Civil Engineering:</strong> Concrete, steel, foundation calculations</li>
            <li><strong>Electrical Engineering:</strong> Load calculations, wire sizing</li>
            <li><strong>Mechanical Engineering:</strong> HVAC, plumbing systems</li>
            <li><strong>Structural Engineering:</strong> Beam analysis, load calculations</li>
        </ul>
        
        <h3>Tips for Accurate Results</h3>
        <p>Follow these best practices:</p>
        <ul>
            <li>Always verify units before calculating</li>
            <li>Double-check input values</li>
            <li>Review safety factors and code requirements</li>
            <li>Save important calculations for future reference</li>
        </ul>
        ';
    }

    /**
     * Get civil engineering content
     */
    private function getCivilEngineeringContent()
    {
        return '
        <h2>Civil Engineering Calculations</h2>
        <p>This comprehensive guide covers the most common civil engineering calculations used in construction and design projects.</p>
        
        <h3>Concrete Calculations</h3>
        <p>Key concrete calculation types include:</p>
        <ul>
            <li><strong>Volume Calculations:</strong> Slabs, beams, columns, footings</li>
            <li><strong>Mix Design:</strong> Cement, sand, aggregate ratios</li>
            <li><strong>Reinforcement:</strong> Rebar spacing and quantities</li>
        </ul>
        
        <h3>Steel Calculations</h3>
        <p>Common steel calculations:</p>
        <ul>
            <li><strong>Beam Design:</strong> Moment capacity, deflection</li>
            <li><strong>Column Design:</strong> Axial load capacity</li>
            <li><strong>Connection Design:</strong> Bolted and welded connections</li>
        </ul>
        
        <h3>Foundation Design</h3>
        <p>Foundation calculation essentials:</p>
        <ul>
            <li><strong>Bearing Capacity:</strong> Soil bearing pressure</li>
            <li><strong>Settlement Analysis:</strong> Immediate and consolidation settlement</li>
            <li><strong>Pile Design:</strong> Capacity and spacing calculations</li>
        </ul>
        ';
    }

    /**
     * Search articles
     */
    private function searchArticles($query)
    {
        // In a real application, this would search a database
        $allArticles = [
            [
                'title' => 'Getting Started with Engineering Calculators',
                'excerpt' => 'Learn the basics of using our engineering calculation tools effectively.',
                'slug' => 'getting-started',
                'category' => 'Getting Started'
            ],
            [
                'title' => 'Understanding Civil Engineering Calculations',
                'excerpt' => 'Comprehensive guide to civil engineering formulas and calculations.',
                'slug' => 'civil-engineering-guide',
                'category' => 'Civil Engineering'
            ],
            [
                'title' => 'Electrical Load Calculations Made Easy',
                'excerpt' => 'Step-by-step guide to electrical load calculations and safety factors.',
                'slug' => 'electrical-load-calculations',
                'category' => 'Electrical Engineering'
            ]
        ];

        $results = [];
        $query = strtolower($query);
        
        foreach ($allArticles as $article) {
            if (strpos(strtolower($article['title']), $query) !== false || 
                strpos(strtolower($article['excerpt']), $query) !== false) {
                $results[] = $article;
            }
        }

        return $results;
    }

    /**
     * Get category info
     */
    private function getCategoryInfo($slug)
    {
        $categories = $this->getHelpCategories();
        
        foreach ($categories as $category) {
            if ($category['slug'] === $slug) {
                return $category;
            }
        }
        
        return null;
    }

    /**
     * Get articles by category
     */
    private function getArticlesByCategory($category)
    {
        // Mock data - in real app, query database
        return [
            [
                'title' => 'Getting Started with Engineering Calculators',
                'excerpt' => 'Learn the basics of using our engineering calculation tools effectively.',
                'slug' => 'getting-started',
                'date' => '2025-11-01'
            ],
            [
                'title' => 'Creating Your First Calculation',
                'excerpt' => 'Step-by-step guide to performing your first engineering calculation.',
                'slug' => 'first-calculation',
                'date' => '2025-10-28'
            ]
        ];
    }

    /**
     * Get related articles
     */
    private function getRelatedArticles($category, $excludeId)
    {
        return [
            [
                'title' => 'Advanced Calculator Features',
                'slug' => 'advanced-features',
                'excerpt' => 'Discover advanced features to enhance your calculations.'
            ],
            [
                'title' => 'Saving and Organizing Calculations',
                'slug' => 'organizing-calculations',
                'excerpt' => 'Learn how to save and organize your calculation history.'
            ]
        ];
    }
}
