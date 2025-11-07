<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) . ' - ' : ''; ?>Bishwo Calculator - Engineering Tools</title>
    
    <!-- Meta Tags -->
    <meta name="description" content="<?php echo isset($description) ? htmlspecialchars($description) : 'Advanced engineering calculators for professionals'; ?>">
    <meta name="keywords" content="<?php echo isset($keywords) ? htmlspecialchars($keywords) : 'engineering calculator, civil engineering, electrical engineering, structural analysis'; ?>">
    <meta name="author" content="Bishwo Calculator">
    
    <!-- Theme Styles -->
    <?php 
    if (isset($theme)) {
        $theme->loadThemeStyles();
    }
    ?>
    
    <!-- Category Specific Styles -->
    <?php if (isset($category_css) && isset($theme)): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($theme->getThemeAsset("css/{$category_css}.css")); ?>">
    <?php endif; ?>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo isset($theme) ? $theme->getThemeAsset('images/favicon.png') : '/images/favicon.png'; ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo isset($title) ? htmlspecialchars($title) : 'Bishwo Calculator'; ?>">
    <meta property="og:description" content="<?php echo isset($description) ? htmlspecialchars($description) : 'Advanced engineering calculators for professionals'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo isset($theme) ? $theme->getThemeAsset('images/banner.jpg') : '/images/banner.jpg'; ?>">
    
    <!-- Page Specific CSS -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?php echo isset($body_class) ? htmlspecialchars($body_class) : ''; ?>">
    
    <!-- Header -->
    <?php 
    if (isset($theme)) {
        $theme->renderPartial('header', isset($header_data) ? $header_data : []);
    }
    ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <?php 
    if (isset($theme)) {
        $theme->renderPartial('footer', isset($footer_data) ? $footer_data : []);
    }
    ?>

    <!-- Back to Top Button -->
    <div id="back-to-top-btn" class="back-to-top-btn" style="display: none;">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Theme Scripts -->
    <?php 
    if (isset($theme)) {
        $theme->loadThemeScripts();
    }
    ?>
    
    <!-- Page Specific Scripts -->
    <?php if (isset($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo isset($theme) ? htmlspecialchars($theme->getThemeAsset("js/{$script}.js")) : htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Additional JavaScript -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo htmlspecialchars($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline JavaScript -->
    <?php if (isset($inline_js)): ?>
        <script>
        <?php echo $inline_js; ?>
        </script>
    <?php endif; ?>
    
</body>
</html>
