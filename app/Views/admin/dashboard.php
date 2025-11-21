<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-layout">
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <div class="admin-container">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-content">
                <div class="dashboard-header">
                    <h1>Dashboard Overview</h1>
                    <p>Monitor your system's performance and activity.</p>
                </div>

                <div class="dashboard-widgets">
                    <!-- Recent Activity Widget -->
                    <div class="widget-card">
                        <div class="widget-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="widget-content">
                            <h3>Recent Activity</h3>
                            <ul class="activity-list">
                                <li>User John registered</li>
                                <li>Calculation completed</li>
                                <li>New backup created</li>
                        </div>
                    </div>

                    <!-- System Status Widget -->
                    <div class="widget-card">
                        <div class="widget-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="widget-content">
                            <h3>System Status</h3>
                            <div class="status-grid">
                                <div class="status-item">
                                    <span class="status-label">Users Online</span>
                                    <span class="status-value">24</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </main>
    </div>
</body>

</html>