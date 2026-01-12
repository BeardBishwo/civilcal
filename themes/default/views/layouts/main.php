<?php
$activeTheme = $_SESSION['active_theme'] ?? 'default';
$themeHeader = BASE_PATH . '/themes/' . $activeTheme . '/views/partials/header.php';
$themeFooter = BASE_PATH . '/themes/' . $activeTheme . '/views/partials/footer.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Civil City' ?></title>

    <!-- Base URL -->
    <script>
        window.APP_BASE_URL = <?php echo json_encode(app_base_url('/')); ?>;
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: '#000000',
                        surface: '#0a0a0a',
                        primary: '#ffffff',
                    }
                }
            }
        }
    </script>

    <!-- Premium Theme CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/theme.css?v=' . time()); ?>">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>

<body class="bg-background text-white flex flex-col min-h-screen">

    <?php
    if (file_exists($themeHeader)) {
        include $themeHeader;
    }
    ?>

    <!-- Main Content with Global Fade In -->
    <main class="flex-grow w-full animate-fade-in-up">
        <?= $content ?? '' ?>
    </main>

    <?php
    if (file_exists($themeFooter)) {
        include $themeFooter;
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>