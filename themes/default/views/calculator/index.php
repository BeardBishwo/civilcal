<?php 
$site_meta = get_site_meta();
$site_title = defined('APP_NAME') ? APP_NAME : $site_meta['title'];
$page_title = $title ?? $site_title; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/theme.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/calculator-platform.css'); ?>?v=<?php echo time(); ?>">
</head>
<body>
    <div class="layout-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?php echo app_base_url('/calculator'); ?>" class="sidebar-brand">
                    <?php if (!empty($site_meta['logo'])): ?>
                        <img src="<?php echo htmlspecialchars($site_meta['logo']); ?>" alt="<?php echo htmlspecialchars($site_title); ?>" style="max-height: 40px; width: auto;">
                    <?php else: ?>
                        <i class="bi bi-grid-fill me-2 text-primary"></i><?php echo htmlspecialchars($site_title); ?>
                    <?php endif; ?>
                </a>
            </div>

            <div class="sidebar-search">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="sidebarSearch" class="form-control ps-5" placeholder="Search converters..." oninput="filterSidebar()">
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php foreach ($categories as $cat): ?>
                <a href="<?php echo app_base_url('/calculator/converter/' . $cat['slug']); ?>" 
                   class="nav-category" 
                   data-name="<?php echo htmlspecialchars($cat['name']); ?>">
                    <i class="<?php echo $cat['icon']; ?>"></i>
                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="dashboard-hero text-center">
                <h1 class="display-4 fw-bold mb-3">Welcome to <span class="text-gradient"><?php echo htmlspecialchars($site_title); ?></span> Platform</h1>
                <p class="lead text-muted mb-5">Select a category from the sidebar or start calculating immediately.</p>
                
                <!-- Centerpiece: Professional Scientific Calculator -->
                <div class="scientific-preview shadow-lg">
                    <div class="scientific-preview-header">
                        <h3 class="m-0"><i class="bi bi-cpu me-2 text-primary"></i>Scientific Dashboard</h3>
                        <a href="<?php echo app_base_url('/calculator/scientific'); ?>" class="scientific-expand-btn" title="Full Page Mode">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </a>
                    </div>
                    
                    <?php 
                    // Inline a simplified version of scientific calc for the dashboard
                    include __DIR__ . '/dashboard-scientific.php'; 
                    ?>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <h3 class="fw-bold mb-4">Popular Tools</h3>
                    <div class="dashboard-grid">
                        <a href="<?php echo app_base_url('/calculator/scientific'); ?>" class="db-card">
                            <i class="bi bi-calculator-fill"></i>
                            <h4>Scientific</h4>
                            <p>Advanced math & history</p>
                        </a>
                        <a href="<?php echo app_base_url('/calculator/converter/length'); ?>" class="db-card">
                            <i class="bi bi-rulers"></i>
                            <h4>Length</h4>
                            <p>Meters, Feet, Miles, etc.</p>
                        </a>
                        <a href="<?php echo app_base_url('/calculator/converter/mass-weight'); ?>" class="db-card">
                            <i class="bi bi-scales"></i>
                            <h4>Weight</h4>
                            <p>KG, Pounds, Grams, etc.</p>
                        </a>
                        <a href="<?php echo app_base_url('/calculator/converter/area'); ?>" class="db-card">
                            <i class="bi bi-bounding-box-circles"></i>
                            <h4>Area</h4>
                            <p>SQM, Acres, Hectares, etc.</p>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function filterSidebar() {
            const query = document.getElementById('sidebarSearch').value.toLowerCase();
            const items = document.querySelectorAll('.nav-category');
            
            items.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                item.style.display = name.includes(query) ? 'flex' : 'none';
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
