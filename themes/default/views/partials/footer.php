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
            echo \App\Helpers\AdHelper::render('footer_top');
        }
        ?>
        <?php include __DIR__ . '/project-selector.php'; ?>
        <div class="container">
            <?php 
            $footer_text = \App\Services\SettingsService::get('footer_text');
            if (!empty($footer_text)) {
                $footer_text = str_replace('{year}', date('Y'), $footer_text);
                echo $footer_text; 
            } else {
            ?>
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(\App\Services\SettingsService::get('site_name', 'Civil Cal')); ?>. All Rights Reserved.</p>
                <p>Professional Tools for Modern Engineering</p>
            <?php } ?>
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

