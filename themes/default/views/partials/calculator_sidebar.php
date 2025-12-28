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
        <?php echo \App\Helpers\AdHelper::show('sidebar_top', 'sidebar-ad-wrapper mt-3 px-3'); ?>
        <div class="position-relative mt-3">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" id="sidebarSearch" class="form-control ps-5" placeholder="Search tools..." oninput="filterSidebar()">
        </div>
    </div>

    <nav class="sidebar-nav">
        <!-- Favorites Widget (Hidden by default, shown by JS) -->
        <div id="favorites-widget" class="mb-3 px-3" style="display: none;">
            <h6 class="text-uppercase text-secondary fw-bold small ls-1 mb-2">My Favorites</h6>
            <div id="favorites-widget-content">
                <!-- Populated by favorites.js -->
            </div>
            <hr class="border-secondary opacity-25 my-3">
        </div>

        <!-- Main Tools -->
        <div class="nav-label">Core</div>
        <a href="<?php echo app_base_url('/calculator/scientific'); ?>" class="nav-category <?php echo (strpos($current_uri ?? '', '/scientific') !== false) ? 'active' : ''; ?>" data-name="Scientific Calculator">
            <i class="bi bi-cpu"></i>
            <span>Scientific</span>
        </a>

        <!-- Scientific Modules (Hardcoded for now as they are custom controllers) -->
        <div class="nav-label mt-3">Modules</div>
        <a href="<?php echo app_base_url('/calculator/math/trigonometry'); ?>" class="nav-category <?php echo (strpos($current_uri ?? '', '/math/') !== false) ? 'active' : ''; ?>" data-name="Mathematics">
            <i class="bi bi-calculator"></i>
            <span>Mathematics</span>
        </a>
        <a href="<?php echo app_base_url('/calculator/datetime/duration'); ?>" class="nav-category <?php echo (strpos($current_uri ?? '', '/datetime/') !== false) ? 'active' : ''; ?>" data-name="Date & Time">
            <i class="bi bi-calendar-range"></i>
            <span>Date & Time</span>
        </a>
        <div class="ps-4 mb-2 <?php echo (strpos($current_uri ?? '', '/datetime/') !== false) ? 'd-block' : 'd-none'; ?>">
            <a href="<?php echo app_base_url('/calculator/datetime/duration'); ?>" class="d-block text-muted py-1 small text-decoration-none">Duration</a>
            <a href="<?php echo app_base_url('/calculator/datetime/adder'); ?>" class="d-block text-muted py-1 small text-decoration-none">Date Adder</a>
            <a href="<?php echo app_base_url('/calculator/datetime/workdays'); ?>" class="d-block text-muted py-1 small text-decoration-none">Work Days</a>
            <a href="<?php echo app_base_url('/calculator/datetime/time'); ?>" class="d-block text-muted py-1 small text-decoration-none">Time Calc</a>
            <a href="<?php echo app_base_url('/calculator/datetime/nepali'); ?>" class="d-block text-muted py-1 small text-decoration-none text-primary">Nepali Date</a>
        </div>
         <a href="<?php echo app_base_url('/calculator/finance/loan'); ?>" class="nav-category <?php echo (strpos($current_uri ?? '', '/finance/') !== false) ? 'active' : ''; ?>" data-name="Finance">
            <i class="bi bi-cash-coin"></i>
            <span>Finance</span>
        </a>
        <a href="<?php echo app_base_url('/calculator/health/bmi'); ?>" class="nav-category <?php echo (strpos($current_uri ?? '', '/health/') !== false) ? 'active' : ''; ?>" data-name="Health & Fitness">
            <i class="bi bi-activity"></i>
            <span>Health</span>
        </a>
        <a href="<?php echo app_base_url('/calculator/physics/velocity'); ?>" class="nav-category <?php echo (strpos($current_uri ?? '', '/physics/') !== false) ? 'active' : ''; ?>" data-name="Physics">
            <i class="bi bi-lightning-charge"></i>
            <span>Physics</span>
        </a>
        <a href="<?php echo app_base_url('/calculator/chemistry/molar-mass'); ?>" class="nav-category <?php echo (strpos($current_uri ?? '', '/chemistry/') !== false) ? 'active' : ''; ?>" data-name="Chemistry">
            <i class="bi bi-radioactive"></i>
            <span>Chemistry</span>
        </a>
        <a href="<?php echo app_base_url('/calculator/statistics/basic'); ?>" class="nav-category <?php echo (strpos($current_uri ?? '', '/statistics/') !== false) ? 'active' : ''; ?>" data-name="Statistics">
            <i class="bi bi-bar-chart-steps"></i>
            <span>Statistics</span>
        </a>

        <!-- Unit Converters -->
        <div class="nav-label mt-3">Converters</div>
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo app_base_url('/calculator/converter/' . $cat['slug']); ?>" 
               class="nav-category <?php echo (isset($category) && $category['slug'] == $cat['slug']) ? 'active' : ''; ?>" 
               data-name="<?php echo htmlspecialchars($cat['name']); ?>">
                <i class="<?php echo $cat['icon']; ?>"></i>
                <span><?php echo htmlspecialchars($cat['name']); ?></span>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php echo \App\Helpers\AdHelper::show('sidebar_bottom', 'sidebar-ad-wrapper mt-4 px-3 text-center'); ?>
    </nav>
</aside>

<script>
    function filterSidebar() {
        const query = document.getElementById('sidebarSearch').value.toLowerCase();
        const items = document.querySelectorAll('.nav-category');
        
        items.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const parent = item.previousElementSibling; 
            // Simple display toggle
            item.style.display = name.includes(query) ? 'flex' : 'none';
        });
        
        // Optional: Hide headers if no children visible (advanced, skipping for simple MVP)
    }
</script>
