<?php
// Check footer visibility
$site_settings = get_site_settings();
$header_footer_visibility = $site_settings['header_footer_visibility'] ?? 'both';
$show_footer = in_array($header_footer_visibility, ['both', 'footer_only']);

if ($show_footer): ?>
    <hr class="footer-separator">
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Bishwo. All Rights Reserved.</p>
            <p>Made with <i class="fas fa-heart"></i> in 🇳🇵 Nepal by BeardBishwo</p>
        </div>
    </footer>
<?php endif; ?>

    </main>
    
    <!-- Back to Top Script -->
    <script src="<?php echo app_base_url('assets/js/back-to-top.js'); ?>" 
            defer
            onerror="console.error('Error loading back-to-top.js:', event)"
            onload="console.log('Back to top script loaded successfully')">
    </script>

    </body>
</html>
