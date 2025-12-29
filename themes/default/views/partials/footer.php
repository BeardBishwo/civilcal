<?php
// Check footer visibility
$site_settings = get_site_settings();
$header_footer_visibility = $site_settings['header_footer_visibility'] ?? 'both';
$show_footer = in_array($header_footer_visibility, ['both', 'footer_only']);

if ($show_footer): ?>
    <footer class="site-footer">
        <hr class="footer-separator">
        <?php 
        // Inject Footer Ad
        if (class_exists('App\Helpers\AdHelper')) {
            echo \App\Helpers\AdHelper::show('footer_top');
        }
        ?>
        <?php include __DIR__ . '/project-selector.php'; ?>
        
        <div class="footer-top">
            <div class="container">
                <!-- Brand Section: Logo, Tagline, Socials, Apps -->
                <div class="footer-brand-container">
                    <?php 
                    $site_name = \App\Services\SettingsService::get('site_name', 'Civil Cal');
                    $site_logo = \App\Services\SettingsService::get('site_logo');
                    ?>
                    <div class="footer-logo-wrapper">
                        <?php if ($site_logo): ?>
                            <a href="<?= app_base_url('/') ?>">
                                <img src="<?php echo app_base_url($site_logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" class="footer-logo-img">
                            </a>
                        <?php else: ?>
                            <a href="<?= app_base_url('/') ?>" class="footer-logo-text"><?php echo htmlspecialchars($site_name); ?></a>
                        <?php endif; ?>
                    </div>
                    
                    <p class="footer-tagline">
                        <?php echo htmlspecialchars(\App\Services\SettingsService::get('site_description', 'Advanced precision tools for engineers and professionals.')); ?>
                    </p>

                    <div class="footer-social-icons">
                        <?php 
                        $socialLinks = \App\Services\SettingsService::get('social_links', []);
                        if (is_string($socialLinks)) {
                            $socialLinks = json_decode($socialLinks, true) ?? [];
                        }
                        
                        $platformIcons = [
                            'facebook' => 'fab fa-facebook-f', 'twitter' => 'fab fa-twitter',
                            'instagram' => 'fab fa-instagram', 'linkedin' => 'fab fa-linkedin-in',
                            'youtube' => 'fab fa-youtube', 'telegram' => 'fab fa-telegram-plane',
                            'whatsapp' => 'fab fa-whatsapp', 'tiktok' => 'fab fa-tiktok',
                            'pinterest' => 'fab fa-pinterest-p', 'github' => 'fab fa-github'
                        ];

                        if (!empty($socialLinks)):
                            foreach ($socialLinks as $link):
                                $platform = $link['platform'] ?? '';
                                $url = $link['url'] ?? '#';
                                $icon = $platformIcons[$platform] ?? 'fas fa-link';
                        ?>
                            <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="social-link" title="<?php echo ucfirst($platform); ?>">
                                <i class="<?php echo $icon; ?>"></i>
                            </a>
                        <?php endforeach; endif; ?>
                    </div>

                    <div class="footer-app-stores">
                        <?php 
                        $playStore = \App\Services\SettingsService::get('play_store_url');
                        $appStore = \App\Services\SettingsService::get('app_store_url');
                        if ($playStore): ?>
                            <a href="<?= htmlspecialchars($playStore) ?>" target="_blank" class="store-btn">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" style="height: 40px;">
                            </a>
                        <?php endif; 
                        if ($appStore): ?>
                            <a href="<?= htmlspecialchars($appStore) ?>" target="_blank" class="store-btn">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge_US-UK_RGB_blk_092917.svg" alt="App Store" style="height: 40px;">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Links Grid: 4 Columns -->
                <div class="footer-links-grid">
                    <?php 
                    $menuService = new \App\Services\MenuService();
                    for ($i = 1; $i <= 4; $i++) {
                        $menu = $menuService->getMenu('footer_' . $i);
                        if ($menu && !empty($menu['items'])): ?>
                            <div class="footer-col">
                                <h4 class="footer-col-title"><?php echo htmlspecialchars($menu['name']); ?></h4>
                                <ul class="footer-links">
                                    <?php foreach ($menu['items'] as $item): 
                                        if (isset($item['is_active']) && $item['is_active'] === false) continue;
                                        $url = $item['url'] ?? '#';
                                        $label = $item['name'] ?? ($item['title'] ?? 'Link');
                                    ?>
                                        <li><a href="<?php echo (strpos($url, 'http') === 0) ? $url : app_base_url($url); ?>"><?php echo htmlspecialchars($label); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                    <?php endif; } ?>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <?php
                $site_name = \App\Services\SettingsService::get('site_name', 'Civil Cal');
                $site_meta = \App\Services\SettingsService::get('site_meta', []);
                ?>
                <div class="copyright-text">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. All Rights Reserved.</p>
                    <p>Made with <i class="fas fa-heart" style="color: #ff5e5e;"></i> in <?php echo htmlspecialchars($site_meta['country'] ?? 'Nepal'); ?> by BeardBishwo</p>
                </div>
            </div>
        </div>
    </footer>
<?php endif; ?>

    </main>
    
    <!-- Header Script -->
    <?php
    $tm = new \App\Services\ThemeManager();
    ?>
    <script src="<?php echo $tm->themeUrl('assets/js/header.js'); ?>" 
            defer
            onerror="console.error('Error loading header.js:', event)"
            onload="console.log('Header script loaded successfully')">
    </script>
    <script src="<?php echo $tm->themeUrl('assets/js/favorites.js'); ?>" defer></script>
    <script src="<?php echo $tm->themeUrl('assets/js/calculator-export.js'); ?>" defer></script>
    
    
    <script src="<?php echo $tm->themeUrl('assets/js/back-to-top.js'); ?>" 
            defer
            onerror="console.error('Error loading back-to-top.js:', event)"
            onload="console.log('Back to top script loaded successfully')">
    </script>
    <script src="<?php echo $tm->themeUrl('assets/js/quest-tracker.js'); ?>" defer></script>
    <script src="<?php echo app_base_url('public/assets/js/global-notifications.js'); ?>"></script>

    <?php include __DIR__ . '/floating-calculator.php'; ?>

    </body>
</html>
