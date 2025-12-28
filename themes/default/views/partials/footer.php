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
                <div class="footer-grid">
                    <?php 
                    $menuService = new \App\Services\MenuService();
                    // Loop through 4 columns
                    for ($i = 1; $i <= 4; $i++) {
                        $colItems = $menuService->get('footer_' . $i);
                        // If empty, show a fallback title for Admin visualization or empty placeholder
                        echo '<div class="footer-col">';
                        if (!empty($colItems) && is_array($colItems)) {
                            // Extract title from first item if it's a "heading" type, otherwise just list
                            // For simplicity, we assume the menu name in Admin is the title, BUT
                            // MenuService doesn't return the menu Name, only items.
                            // So we will just render the list. The user can add a "Heading" item if needed or we style the first item bold.
                            echo '<ul class="footer-links">';
                            foreach ($colItems as $index => $item) {
                                if (isset($item['is_active']) && $item['is_active'] === false) continue;

                                $url = $item['url'] ?? '#';
                                $label = $item['name'] ?? ($item['title'] ?? 'Link');
                                $isHeading = $index === 0; // Optional: Treat first item as heading if we want, but Udemy has distinct headers.
                                // Better approach: Just render links. User creates "About" as first link or we find a way to pass Menu Name.
                                // Current System: Just links.
                                echo '<li><a href="' . ((strpos($url, 'http') === 0) ? $url : app_base_url($url)) . '">' . htmlspecialchars($label) . '</a></li>';
                            }
                            echo '</ul>';
                        } else {
                            // Empty column placeholder (so grid remains intact)
                            echo '&nbsp;';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
                
                <div class="footer-bottom">
                    <div class="footer-logo">
                        <!-- Optional: SVG Logo or Site Name -->
                        <span class="udemy-style-logo"><?php echo htmlspecialchars(\App\Services\SettingsService::get('site_name', 'Civil Cal')); ?></span>
                    </div>
                    <div class="copyright-text">
                        <?php 
                        $footer_text = \App\Services\SettingsService::get('footer_text');
                        if (!empty($footer_text)) {
                            echo str_replace('{year}', date('Y'), $footer_text);
                        } else {
                            echo '&copy; ' . date('Y') . ' ' . htmlspecialchars(\App\Services\SettingsService::get('site_name', 'Civil Cal')) . '. All Rights Reserved.';
                        } 
                        ?>
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

