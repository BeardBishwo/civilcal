<?php
// Check footer visibility
$site_settings = get_site_settings();
$header_footer_visibility = $site_settings['header_footer_visibility'] ?? 'both';
$show_footer = in_array($header_footer_visibility, ['both', 'footer_only']);

if ($show_footer): ?>
    <hr class="footer-separator">
    <footer class="site-footer">
        <?php 
        // Inject Footer Ad
        if (class_exists('App\Helpers\AdHelper')) {
            echo \App\Helpers\AdHelper::show('footer_top');
        }
        ?>
        <?php include __DIR__ . '/project-selector.php'; ?>
        
        <div class="footer-content-wrapper">
            <div class="container">
                <?php 
                $menuService = new \App\Services\MenuService();
                $activeCols = [];
                for ($i = 1; $i <= 4; $i++) {
                    $menu = $menuService->getMenu('footer_' . $i);
                    if ($menu && !empty($menu['items'])) {
                        $activeCols[] = $menu;
                    }
                }
                
                $colCount = count($activeCols);
                ?>
                <div class="footer-grid col-count-<?php echo $colCount; ?>" 
                     style="<?php echo $colCount > 0 ? "grid-template-columns: repeat($colCount, 1fr);" : "display:none;"; ?>">
                    <?php foreach ($activeCols as $menu): ?>
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
                    <?php endforeach; ?>
                </div>
                
                <div class="footer-bottom">
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
    
    
    <!-- Back to Top Script -->
    <script src="<?php echo $tm->themeUrl('assets/js/back-to-top.js'); ?>" 
            defer
            onerror="console.error('Error loading back-to-top.js:', event)"
            onload="console.log('Back to top script loaded successfully')">
    </script>
    <script src="<?php echo app_base_url('public/assets/js/global-notifications.js'); ?>"></script>

    <?php include __DIR__ . '/floating-calculator.php'; ?>

    </body>
</html>

