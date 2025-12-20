<?php
$page_title = 'Dashboard - ' . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');
?>

<div class="container">
    <div class="hero bg-gradient">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</h1>
        <p>Access your tools and profile settings below.</p>
    </div>

    <div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
        <!-- Profile Card -->
        <div class="category-card">
            <div class="category-header">
                <h3>My Profile</h3>
                <div class="separator"></div>
            </div>
            <div class="card-content" style="padding: 1.5rem;">
                <p>Manage your account settings and preferences.</p>
                <a href="<?php echo app_base_url('profile'); ?>" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">Go to Profile</a>
            </div>
        </div>

        <!-- Calculators Card -->
        <div class="category-card">
            <div class="category-header">
                <h3>Calculators</h3>
                <div class="separator"></div>
            </div>
            <div class="card-content" style="padding: 1.5rem;">
                <p>Explore our collection of engineering calculators.</p>
                <a href="<?php echo app_base_url('calculators'); ?>" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">Browse Calculators</a>
            </div>
        </div>

        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'superadmin')): ?>
        <!-- Admin Card -->
        <div class="category-card">
            <div class="category-header">
                <h3>Admin Panel</h3>
                <div class="separator"></div>
            </div>
            <div class="card-content" style="padding: 1.5rem;">
                <p>Manage users, content, and system settings.</p>
                <a href="<?php echo app_base_url('admin/dashboard'); ?>" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">Go to Admin Panel</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
