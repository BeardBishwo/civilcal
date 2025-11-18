<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Panel'; ?> - Bishwo Calculator</title>
    
    <!-- Base URL for JavaScript -->
    <script>
        window.APP_BASE_URL = <?php echo json_encode(app_base_url('/')); ?>;
    </script>
    <script src="<?php echo asset_url('js/app-utils.js'); ?>"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Admin CSS -->
    <link href="<?php echo asset_url('css/admin.css'); ?>" rel="stylesheet">
    
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link href="<?php echo $css; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="admin-body">
    
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    
    <!-- MAIN CONTENT WRAPPER -->
    <div class="admin-main">
        
        <!-- TOP NAVBAR -->
        <?php include __DIR__ . '/partials/topbar.php'; ?>
        
        <!-- PAGE CONTENT -->
        <div class="admin-content">
            <!-- Flash Messages -->
            <?php
            $flashMessages = $_SESSION['flash_messages'] ?? [];
            unset($_SESSION['flash_messages']);
            ?>
            <?php if (!empty($flashMessages)): ?>
                <?php foreach ($flashMessages as $type => $message): ?>
                    <div class="alert alert-<?php echo $type === 'error' ? 'danger' : $type; ?>">
                        <i class="fas fa-<?php echo $type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <!-- Page Content -->
            <?php echo $content ?? ''; ?>
        </div>
        
    </div>
    
    <!-- Admin JS -->
    <script src="<?php echo asset_url('js/admin.js'); ?>"></script>
    
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
