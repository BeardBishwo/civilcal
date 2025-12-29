<?php
// Check footer visibility
$site_settings = get_site_settings();
$header_footer_visibility = $site_settings['header_footer_visibility'] ?? 'both';
$show_footer = in_array($header_footer_visibility, ['both', 'footer_only']);

if ($show_footer): ?>
    <footer class="site-footer">
        <?php 
        // Inject Footer Ad
        if (class_exists('App\Helpers\AdHelper')) {
            echo \App\Helpers\AdHelper::show('footer_top');
        }
        ?>
        <?php include __DIR__ . '/project-selector.php'; ?>
        
        <hr class="footer-separator">

        <div class="footer-top">
            <div class="container">
                <!-- Brand Section: Logo, Tagline -->
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
                        <?php endif; ?>
                    </div>
                    
                    <p class="footer-about">
                        <?php 
                        $footer_about = \App\Services\SettingsService::get('footer_about_text', 'Civil Cal is a comprehensive engineering calculator platform designed to help civil engineers, students, and professionals solve complex calculations with precision and ease. From structural analysis to material estimation, we provide the tools you need to build better.');
                        echo nl2br(htmlspecialchars($footer_about)); 
                        ?>
                    </p>
                </div>

                <!-- Links Grid: 3 Columns -->
                <div class="footer-links-grid">
                    <?php 
                    $menuService = new \App\Services\MenuService();
                    for ($i = 1; $i <= 3; $i++) {
                        $menu = $menuService->getMenu('footer_' . $i);
                        if ($menu && !empty($menu['items'])): ?>
                            <div class="footer-col" id="footer-col-<?= $i ?>">
                                <h4 class="footer-col-title"><?php echo htmlspecialchars($menu['name']); ?></h4>
                                <ul class="footer-links">
                                    <?php 
                                    $consecutiveSocials = [];
                                    $renderSocials = function(&$socials) {
                                        if (empty($socials)) return;
                                        echo '<li class="social-wrapper"><div class="footer-social-links">';
                                        foreach ($socials as $s) {
                                            $platform = strtolower($s['name']);
                                            if ($platform === 'twitter') $platform = 'x';
                                            
                                            $svg_path = 'themes/default/assets/images/' . $platform . '.svg';
                                            
                                            echo '<a href="' . htmlspecialchars($s['url']) . '" target="_blank" class="social-link" title="' . htmlspecialchars($s['name']) . '">';
                                            if (file_exists(BASE_PATH . '/' . $svg_path)) {
                                                echo '<img src="' . app_base_url($svg_path) . '" alt="' . htmlspecialchars($s['name']) . '" style="width: 20px; height: 20px; filter: brightness(0) invert(1);">';
                                            } else {
                                                echo '<i class="' . htmlspecialchars($s['icon']) . '"></i>';
                                            }
                                            echo '</a>';
                                        }
                                        echo '</div></li>';
                                        $socials = [];
                                    };

                                    foreach ($menu['items'] as $item): 
                                        if (isset($item['is_active']) && $item['is_active'] === false) continue;
                                        $url = $item['url'] ?? '#';
                                        if (strpos($url, 'http') !== 0) {
                                            $url = app_base_url($url);
                                        }
                                        $label = $item['name'] ?? ($item['title'] ?? 'Link');
                                        $icon = $item['icon'] ?? '';

                                        // 1. Social Icons (if icon field is filled with fa-icon)
                                        if (!empty($icon) && (strpos($icon, 'fa-') !== false || strpos($icon, 'fab') !== false || strpos($icon, 'fas') !== false)):
                                            $consecutiveSocials[] = ['url' => $url, 'name' => $label, 'icon' => $icon];
                                            continue;
                                        else:
                                            // Render any pending socials before next item
                                            $renderSocials($consecutiveSocials);
                                        endif;

                                        // 2. App Store Buttons (by name)
                                        if (strtolower($label) === 'google play' || strtolower($label) === 'play store'): ?>
                                            <li class="store-wrapper">
                                                <div class="footer-app-stores">
                                                    <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="store-btn">
                                                        <img src="<?= app_base_url('themes/default/assets/images/playstore.svg') ?>" alt="Get it on Google Play" style="height: 30px;">
                                                    </a>
                                                </div>
                                            </li>
                                        <?php elseif (strtolower($label) === 'app store' || strtolower($label) === 'apple store'): ?>
                                            <li class="store-wrapper">
                                                <div class="footer-app-stores">
                                                    <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="store-btn">
                                                        <img src="<?= app_base_url('themes/default/assets/images/appstore.svg') ?>" alt="Download on the App Store" style="height: 30px;">
                                                    </a>
                                                </div>
                                            </li>
                                        <?php else: ?>
                                            <!-- 3. Regular Link -->
                                            <li><a href="<?php echo htmlspecialchars($url); ?>"><?php echo htmlspecialchars($label); ?></a></li>
                                        <?php endif; ?>
                                    <?php endforeach; 
                                    // Final flush of socials
                                    $renderSocials($consecutiveSocials);
                                    ?>
                                </ul>
                            </div>
                    <?php endif; } ?>
                </div>
            </div>
        </div>

        <hr class="footer-separator">

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
