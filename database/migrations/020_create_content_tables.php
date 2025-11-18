<?php

class CreateContentTables {
    
    public function up($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        // Pages table for CMS
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS pages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(255) UNIQUE NOT NULL,
                title VARCHAR(255) NOT NULL,
                content LONGTEXT,
                excerpt TEXT,
                meta_title VARCHAR(255),
                meta_description TEXT,
                meta_keywords VARCHAR(255),
                status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
                template VARCHAR(100) DEFAULT 'default',
                author_id INT,
                published_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_status (status),
                INDEX idx_author (author_id),
                FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Menus table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS menus (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                location VARCHAR(50) NOT NULL,
                items JSON,
                is_active BOOLEAN DEFAULT TRUE,
                display_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_location (location),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Translations table (i18n)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS translations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                translation_key VARCHAR(255) NOT NULL,
                locale VARCHAR(10) NOT NULL,
                translation_value TEXT NOT NULL,
                translation_group VARCHAR(100) DEFAULT 'general',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_translation (translation_key, locale),
                INDEX idx_locale (locale),
                INDEX idx_group (translation_group)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Media library table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS media (
                id INT AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL,
                original_filename VARCHAR(255) NOT NULL,
                file_path VARCHAR(500) NOT NULL,
                file_type VARCHAR(100),
                file_size INT,
                mime_type VARCHAR(100),
                width INT NULL,
                height INT NULL,
                alt_text VARCHAR(255),
                caption TEXT,
                uploaded_by INT,
                folder VARCHAR(255) DEFAULT '/',
                is_public BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_type (file_type),
                INDEX idx_folder (folder),
                INDEX idx_uploader (uploaded_by),
                FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Insert default menus
        $this->insertDefaultMenus($pdo);
        
        // Insert default pages
        $this->insertDefaultPages($pdo);
        
        // Insert default translations
        $this->insertDefaultTranslations($pdo);
        
        echo "✓ Content tables created successfully\n";
    }
    
    private function insertDefaultMenus($pdo) {
        $defaultMenus = [
            [
                'name' => 'Main Menu',
                'location' => 'header',
                'items' => json_encode([
                    ['title' => 'Home', 'url' => '/', 'target' => '_self', 'icon' => 'fas fa-home'],
                    ['title' => 'Calculators', 'url' => '/calculators', 'target' => '_self', 'icon' => 'fas fa-calculator'],
                    ['title' => 'Help', 'url' => '/help', 'target' => '_self', 'icon' => 'fas fa-question-circle'],
                    ['title' => 'Contact', 'url' => '/contact', 'target' => '_self', 'icon' => 'fas fa-envelope']
                ]),
                'is_active' => 1,
                'display_order' => 1
            ],
            [
                'name' => 'Footer Menu',
                'location' => 'footer',
                'items' => json_encode([
                    ['title' => 'About Us', 'url' => '/about', 'target' => '_self'],
                    ['title' => 'Privacy Policy', 'url' => '/privacy-policy', 'target' => '_self'],
                    ['title' => 'Terms of Service', 'url' => '/terms-of-service', 'target' => '_self'],
                    ['title' => 'Contact', 'url' => '/contact', 'target' => '_self']
                ]),
                'is_active' => 1,
                'display_order' => 2
            ]
        ];
        
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO menus (name, location, items, is_active, display_order) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($defaultMenus as $menu) {
            $stmt->execute([
                $menu['name'],
                $menu['location'],
                $menu['items'],
                $menu['is_active'],
                $menu['display_order']
            ]);
        }
    }
    
    private function insertDefaultPages($pdo) {
        $defaultPages = [
            [
                'slug' => 'about',
                'title' => 'About Us',
                'content' => '<h1>About Bishwo Calculator</h1><p>Professional engineering calculation tools for modern engineers.</p>',
                'excerpt' => 'Learn more about Bishwo Calculator',
                'meta_title' => 'About Us - Bishwo Calculator',
                'meta_description' => 'Professional engineering calculation tools',
                'status' => 'published',
                'template' => 'default'
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us.</p>',
                'excerpt' => 'Our privacy policy',
                'meta_title' => 'Privacy Policy - Bishwo Calculator',
                'meta_description' => 'Read our privacy policy',
                'status' => 'published',
                'template' => 'default'
            ],
            [
                'slug' => 'terms-of-service',
                'title' => 'Terms of Service',
                'content' => '<h1>Terms of Service</h1><p>Terms and conditions for using our service.</p>',
                'excerpt' => 'Terms and conditions',
                'meta_title' => 'Terms of Service - Bishwo Calculator',
                'meta_description' => 'Terms and conditions',
                'status' => 'published',
                'template' => 'default'
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'content' => '<h1>Contact Us</h1><p>Get in touch with our team.</p>',
                'excerpt' => 'Contact information',
                'meta_title' => 'Contact Us - Bishwo Calculator',
                'meta_description' => 'Contact our support team',
                'status' => 'published',
                'template' => 'contact'
            ]
        ];
        
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO pages 
            (slug, title, content, excerpt, meta_title, meta_description, status, template, published_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        foreach ($defaultPages as $page) {
            $stmt->execute([
                $page['slug'],
                $page['title'],
                $page['content'],
                $page['excerpt'],
                $page['meta_title'],
                $page['meta_description'],
                $page['status'],
                $page['template']
            ]);
        }
    }
    
    private function insertDefaultTranslations($pdo) {
        $defaultTranslations = [
            // English
            ['welcome', 'en', 'Welcome', 'general'],
            ['login', 'en', 'Login', 'general'],
            ['register', 'en', 'Register', 'general'],
            ['logout', 'en', 'Logout', 'general'],
            ['dashboard', 'en', 'Dashboard', 'general'],
            ['settings', 'en', 'Settings', 'general'],
            ['profile', 'en', 'Profile', 'general'],
            
            // Nepali
            ['welcome', 'ne', 'स्वागत छ', 'general'],
            ['login', 'ne', 'लग - इन', 'general'],
            ['register', 'ne', 'दर्ता', 'general'],
            ['logout', 'ne', 'लग आउट', 'general'],
            ['dashboard', 'ne', 'ड्यासबोर्ड', 'general'],
            ['settings', 'ne', 'सेटिङहरू', 'general'],
            ['profile', 'ne', 'प्रोफाइल', 'general'],
        ];
        
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO translations 
            (translation_key, locale, translation_value, translation_group) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($defaultTranslations as $translation) {
            $stmt->execute($translation);
        }
    }
    
    public function down($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        $pdo->exec("DROP TABLE IF EXISTS media");
        $pdo->exec("DROP TABLE IF EXISTS translations");
        $pdo->exec("DROP TABLE IF EXISTS menus");
        $pdo->exec("DROP TABLE IF EXISTS pages");
    }
}
