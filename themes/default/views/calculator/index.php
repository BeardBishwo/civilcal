<?php $page_title = $title ?? 'Universal Calculator Platform'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --secondary: #a855f7;
            --bg-dark: #0f172a;
            --sidebar-bg: #1e293b;
            --card-bg: rgba(30, 41, 59, 0.7);
            --border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            overflow: hidden; /* Sidebar/Main balance */
            height: 100vh;
            margin: 0;
        }

        .layout-wrapper {
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 300px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 30px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .sidebar-search {
            padding: 20px;
        }

        .sidebar-search .form-control {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border);
            color: white;
            border-radius: 12px;
            padding: 10px 15px;
        }

        .sidebar-search .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.2);
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .nav-category {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .nav-category:hover {
            color: white;
            background: rgba(99, 102, 241, 0.1);
            border-left-color: var(--primary);
        }

        .nav-category.active {
            color: white;
            background: rgba(99, 102, 241, 0.2);
            border-left-color: var(--primary);
            font-weight: 600;
        }

        .nav-category i {
            font-size: 1.25rem;
            margin-right: 15px;
            width: 24px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 40px;
        }

        .dashboard-hero {
            background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.1), transparent),
                        radial-gradient(circle at bottom left, rgba(168, 85, 247, 0.1), transparent);
            border-radius: 30px;
            padding: 50px;
            margin-bottom: 40px;
            border: 1px solid var(--border);
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .db-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 25px;
            transition: all 0.3s;
            text-decoration: none;
            color: white;
            display: block;
        }

        .db-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            color: white;
        }

        .db-card i {
            padding: 15px;
            background: rgba(99, 102, 241, 0.2);
            color: var(--primary-light);
            border-radius: 12px;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: inline-block;
        }

        .db-card h4 {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .db-card p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Scientific Calculator Mini (Shared Component Logic) */
        .scientific-preview {
            background: #141b2d;
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.05);
            max-width: 800px;
            margin: 0 auto;
        }

        .text-gradient {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            .sidebar.show {
                width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?php echo app_base_url('/calculator'); ?>" class="sidebar-brand">
                    <i class="bi bi-grid-fill me-2"></i>Bishwo Calc
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
                <h1 class="display-4 fw-bold mb-3">Welcome to <span class="text-gradient">Universal</span> Platform</h1>
                <p class="lead text-muted mb-5">Select a category from the sidebar or start calculating immediately.</p>
                
                <!-- Centerpiece: Professional Scientific Calculator -->
                <div class="scientific-preview shadow-lg">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="m-0"><i class="bi bi-cpu me-2 text-primary"></i>Scientific Dashboard</h3>
                        <a href="<?php echo app_base_url('/calculator/scientific'); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4">Full Page Mode</a>
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
        .section-title {
            position: relative;
            padding-bottom: 15px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 4px;
            background: #6366f1;
            border-radius: 2px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
