<?php
$appHeader = APP_PATH . '/Views/partials/header.php';
$activeTheme = $_SESSION['active_theme'] ?? 'default';
$themeHeader = BASE_PATH . '/themes/' . $activeTheme . '/views/partials/header.php';
$appFooter = APP_PATH . '/Views/partials/footer.php';
$themeFooter = BASE_PATH . '/themes/' . $activeTheme . '/views/partials/footer.php';

$usingThemeChrome = file_exists($themeHeader) && file_exists($themeFooter) && !file_exists($appHeader);

if ($usingThemeChrome) {
    include $themeHeader;
    echo $content ?? '';
    include $themeFooter;
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?? 'Bishwo Calculator' ?></title>
        
        <!-- Base URL for JavaScript -->
        <script>
            window.APP_BASE_URL = <?php echo json_encode(app_base_url('/')); ?>;
        </script>
        <script src="<?php echo asset_url('js/app-utils.js'); ?>"></script>
        
        <link rel="stylesheet" href="/assets/css/app.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            html, body { height: 100%; }
            body { display: flex; min-height: 100vh; flex-direction: column; }
            main { flex: 1 0 auto; }
        </style>
    </head>
    <body class="bg-gray-50">
        <?php
        if (file_exists($appHeader)) {
            include $appHeader;
        } elseif (file_exists($themeHeader)) {
            include $themeHeader;
        }
        ?>
        <main class="container mx-auto px-4 py-8">
            <?= $content ?? '' ?>
        </main>
        <?php
        if (file_exists($appFooter)) {
            include $appFooter;
        } elseif (file_exists($themeFooter)) {
            include $themeFooter;
        } else {
            echo '<footer class="site-footer"><div class="container">&copy; ' . date('Y') . ' Bishwo Calculator</div></footer>';
        }
        ?>
        <script src="/assets/js/app.js"></script>
    </body>
    </html>
    <?php
}
?>
