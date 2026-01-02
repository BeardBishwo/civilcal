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
                <!-- Dynamic Footer Grid: 4 Columns -->
                <div class="footer-grid">
                    <?php 
                    $menuService = new \App\Services\MenuService();
                    for ($i = 1; $i <= 4; $i++) {
                        $menu = $menuService->getMenu('footer_' . $i);
                        if ($menu && !empty($menu['items'])): ?>
                            <div class="footer-col" id="footer-col-<?= $i ?>">
                                <?php if (($menu['show_name'] ?? 1) && !empty($menu['name'])): ?>
                                    <h4 class="footer-col-title"><?php echo htmlspecialchars($menu['name']); ?></h4>
                                <?php endif; ?>
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
                                            echo '<a href="' . htmlspecialchars($s['url'] ?? '#') . '" target="_blank" class="social-link" title="' . htmlspecialchars($s['name']) . '">';
                                            if (file_exists(BASE_PATH . '/' . $svg_path)) {
                                                echo '<img src="' . app_base_url($svg_path) . '" alt="' . htmlspecialchars($s['name']) . '" style="width: 20px; height: 20px; filter: brightness(0) invert(1);">';
                                            } else {
                                                echo '<i class="' . htmlspecialchars($s['icon'] ?? 'fas fa-link') . '"></i>';
                                            }
                                            echo '</a>';
                                        }
                                        echo '</div></li>';
                                        $socials = [];
                                    };

                                    foreach ($menu['items'] as $item): 
                                        if (isset($item['is_active']) && $item['is_active'] === false) continue;
                                        
                                        // Handle Text Type (Support for raw image paths in text blocks)
                                        if (($item['type'] ?? 'link') === 'text'):
                                            $renderSocials($consecutiveSocials); // Flush socials before text block
                                            $content = $item['content'] ?? '';
                                            $plainContent = trim(strip_tags($content));
                                            
                                            // Check if it's a list of images (single or comma-separated)
                                            $isImageBatch = preg_match('/^[\w\/\.\s,-]+\.(jpg|jpeg|png|gif|svg|webp)(,\s*[\w\/\.\s,-]+\.(jpg|jpeg|png|gif|svg|webp))*$/i', $plainContent);
                                            
                                            if ($isImageBatch):
                                                $parts = array_map('trim', explode(',', $plainContent));
                                                echo '<li class="multi-icon-wrapper"><div class="footer-app-stores side-by-side">';
                                                foreach ($parts as $p):
                                                    if (preg_match('/\.(jpg|jpeg|png|gif|svg|webp)$/i', $p)):
                                                        echo '<a href="#" class="store-btn">';
                                                        echo '<img src="' . ((strpos($p, 'http') === 0) ? htmlspecialchars($p) : app_base_url($p)) . '" alt="icon" style="height: 32px;">';
                                                        echo '</a>';
                                                    endif;
                                                endforeach;
                                                echo '</div></li>';
                                            else:
                                                echo '<li class="footer-text-block">' . \App\Services\ShortcodeService::parse($content) . '</li>';
                                            endif;
                                            continue;
                                        endif;

                                        // Handle Link Type (Updated for Multi-Image/Gamenta Style)
                                        $urlField = $item['url'] ?? '#';
                                        $label = $item['name'] ?? ($item['title'] ?? '');
                                        $icon = $item['icon'] ?? '';
                                        
                                        // Detect if URL contains multiple items (comma separated)
                                        $parts = array_map('trim', explode(',', $urlField));
                                        
                                        if (count($parts) > 1):
                                            $renderSocials($consecutiveSocials); ?>
                                            <li class="multi-icon-wrapper">
                                                <div class="footer-app-stores side-by-side">
                                                    <?php foreach ($parts as $p): 
                                                        $isImg = preg_match('/\.(jpg|jpeg|png|gif|svg|webp)$/i', $p);
                                                        if ($isImg): ?>
                                                            <a href="#" class="store-btn">
                                                                <img src="<?= (strpos($p, 'http') === 0) ? htmlspecialchars($p) : app_base_url($p) ?>" alt="icon" style="height: 32px;">
                                                            </a>
                                                        <?php endif;
                                                    endforeach; ?>
                                                </div>
                                            </li>
                                            <?php continue;
                                        endif;

                                        $url = $parts[0];
                                        if (strpos($url, 'http') !== 0 && $url !== '#' && !empty($url)) {
                                            $url = app_base_url($url);
                                        }

                                        // 1. Social Icons (FontAwesome)
                                        if (!empty($icon) && (strpos($icon, 'fa-') !== false || strpos($icon, 'fab') !== false || strpos($icon, 'fas') !== false)):
                                            $consecutiveSocials[] = ['url' => $url, 'name' => $label, 'icon' => $icon];
                                            continue;
                                        else:
                                            $renderSocials($consecutiveSocials);
                                        endif;

                                        // 2. Direct Image Link (e.g. from Media Manager)
                                        $isImageLink = preg_match('/\.(jpg|jpeg|png|gif|svg|webp)$/i', $url);
                                        if ($isImageLink): ?>
                                            <li class="image-icon-wrapper">
                                                <div class="footer-app-stores">
                                                    <a href="#" class="store-btn">
                                                        <img src="<?= $url ?>" alt="<?= htmlspecialchars($label) ?>" style="height: 32px;">
                                                    </a>
                                                </div>
                                            </li>
                                        <?php elseif (strtolower($label) === 'google play' || strtolower($label) === 'play store'): ?>
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
                                            <li><a href="<?php echo htmlspecialchars($url); ?>"><?php echo htmlspecialchars($label ?: 'Link'); ?></a></li>
                                        <?php endif; ?>
                                    <?php endforeach; 
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
                    <p>Made with <i class="fas fa-heart" style="color: #ff5e5e;"></i> in <?php echo htmlspecialchars($site_meta['country'] ?? 'Nepal'); ?> by <?= APP_NAME ?></p>
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

    <script src="<?php echo app_base_url('public/assets/js/global-notifications.js'); ?>"></script>

    <?php include __DIR__ . '/floating-calculator.php'; ?>

    </body>
</html>
