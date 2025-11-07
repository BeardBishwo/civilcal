<!DOCTYPE html>
<html lang="en" data-theme="professional">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Bishwo Calculator' ?></title>
    
    <!-- Theme CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Theme Custom CSS -->
    <link href="/themes/professional/assets/css/theme.css" rel="stylesheet">
    
    <!-- Page-specific CSS -->
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link href="<?= htmlspecialchars($css) ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        :root {
            --bs-primary: #2c3e50;
            --bs-secondary: #34495e;
            --bs-success: #27ae60;
            --bs-info: #3498db;
            --bs-warning: #f39c12;
            --bs-danger: #e74c3c;
            --bs-light: #ecf0f1;
            --bs-dark: #2c3e50;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
        }
        
        .main-wrapper {
            background: #34495e;
            min-height: 100vh;
            border-radius: 0;
            box-shadow: none;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
        }
        
        .sidebar {
            background: #2c3e50;
            min-height: calc(100vh - 76px);
            padding: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.3);
        }
        
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            border-radius: 0;
            margin: 0;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #34495e;
            color: #3498db;
            border-left-color: #3498db;
            transform: none;
        }
        
        .content-area {
            padding: 20px;
            background: #ecf0f1;
            min-height: calc(100vh - 76px);
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: #2c3e50;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #34495e;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(44, 62, 80, 0.3);
        }
        
        .breadcrumb {
            background: rgba(255,255,255,0.9);
            border-radius: 6px;
            padding: 8px 12px;
        }
        
        .page-header {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #2c3e50 !important;">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-calculator me-2"></i>
                Bishwo Calculator
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/calculators">
                            <i class="fas fa-calculator me-1"></i>Calculators
                        </a>
                    </li>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>Admin
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/admin"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="/admin/users"><i class="fas fa-users me-2"></i>Users</a></li>
                                <li><a class="dropdown-item" href="/admin/plugins"><i class="fas fa-puzzle-piece me-2"></i>Plugins</a></li>
                                <li><a class="dropdown-item" href="/admin/themes"><i class="fas fa-palette me-2"></i>Themes</a></li>
                                <li><a class="dropdown-item" href="/admin/settings"><i class="fas fa-sliders-h me-2"></i>Settings</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['user']['name']) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="/logout" class="d-inline">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <?php if (isset($_SESSION['user'])): ?>
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 sidebar d-md-block">
                    <div class="p-3">
                        <h6 class="text-light mb-3">Navigation</h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?= basename($_SERVER['REQUEST_URI']) === '/' ? 'active' : '' ?>" href="/">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/calculators">
                                    <i class="fas fa-calculator me-2"></i>Calculators
                                </a>
                            </li>
                            
                            <!-- Calculator Categories -->
                            <li class="nav-item mt-3">
                                <h6 class="text-light mb-2">Categories</h6>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/calculators/civil">
                                    <i class="fas fa-building me-2"></i>Civil Engineering
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/calculators/electrical">
                                    <i class="fas fa-bolt me-2"></i>Electrical
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/calculators/structural">
                                    <i class="fas fa-hard-hat me-2"></i>Structural
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/calculators/plumbing">
                                    <i class="fas fa-faucet me-2"></i>Plumbing
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/calculators/hvac">
                                    <i class="fas fa-snowflake me-2"></i>HVAC
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/calculators/fire">
                                    <i class="fas fa-fire me-2"></i>Fire Protection
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/calculators/estimation">
                                    <i class="fas fa-chart-line me-2"></i>Estimation
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/calculators/management">
                                    <i class="fas fa-tasks me-2"></i>Project Management
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="col-md-9 col-lg-10">
                    <div class="content-area">
            <?php else: ?>
                <!-- Main Content for guests -->
                <div class="col-12">
                    <div class="content-area">
            <?php endif; ?>
                        
                        <!-- Breadcrumb -->
                        <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
                            <nav aria-label="breadcrumb" class="mb-4">
                                <ol class="breadcrumb">
                                    <?php foreach ($breadcrumbs as $breadcrumb): ?>
                                        <?php if (isset($breadcrumb['url'])): ?>
                                            <li class="breadcrumb-item">
                                                <a href="<?= htmlspecialchars($breadcrumb['url']) ?>">
                                                    <?php if (isset($breadcrumb['icon'])): ?>
                                                        <i class="<?= htmlspecialchars($breadcrumb['icon']) ?> me-1"></i>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($breadcrumb['title']) ?>
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="breadcrumb-item active">
                                                <?php if (isset($breadcrumb['icon'])): ?>
                                                    <i class="<?= htmlspecialchars($breadcrumb['icon']) ?> me-1"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($breadcrumb['title']) ?>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ol>
                            </nav>
                        <?php endif; ?>
                        
                        <!-- Page Header -->
                        <?php if (isset($pageHeader) && !empty($pageHeader)): ?>
                            <div class="page-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if (isset($pageHeader['icon'])): ?>
                                            <i class="<?= htmlspecialchars($pageHeader['icon']) ?> me-2"></i>
                                        <?php endif; ?>
                                        <h1 class="h3 mb-0"><?= htmlspecialchars($pageHeader['title']) ?></h1>
                                        <?php if (isset($pageHeader['subtitle'])): ?>
                                            <p class="text-muted mb-0"><?= htmlspecialchars($pageHeader['subtitle']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (isset($pageHeader['actions']) && !empty($pageHeader['actions'])): ?>
                                        <div class="btn-group" role="group">
                                            <?php foreach ($pageHeader['actions'] as $action): ?>
                                                <a href="<?= htmlspecialchars($action['url']) ?>" 
                                                   class="btn <?= htmlspecialchars($action['class'] ?? 'btn-primary') ?>">
                                                    <?php if (isset($action['icon'])): ?>
                                                        <i class="<?= htmlspecialchars($action['icon']) ?> me-1"></i>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($action['title']) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Flash Messages -->
                        <?php if (isset($_SESSION['flash'])): ?>
                            <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['flash']); ?>
                        <?php endif; ?>
                        
                        <!-- Main Content -->
                        <?= $content ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-light py-4" style="background: #2c3e50;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; <?= date('Y') ?> Bishwo Calculator. 
                        <span class="ms-2">Version <?= file_exists('version.json') ? json_decode(file_get_contents('version.json'), true)['version'] ?? '1.0.0' : '1.0.0' ?></span>
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">
                        Professional Engineering Solutions
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme JavaScript -->
    <script src="/themes/professional/assets/js/theme.js"></script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= htmlspecialchars($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline JavaScript -->
    <?php if (isset($inlineJS)): ?>
        <script>
            <?= $inlineJS ?>
        </script>
    <?php endif; ?>
</body>
</html>
