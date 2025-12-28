<?php 
/**
 * Calculator Platform - Landing Page
 */
$site_meta = get_site_meta();
$site_title = defined('APP_NAME') ? APP_NAME : $site_meta['title'];
$page_title = $title ?? $site_title; 
$current_uri = $_SERVER['REQUEST_URI'];
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
        <?php include __DIR__ . '/../partials/calculator_sidebar.php'; ?>

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

            <div class="row g-4 mb-5">
                <div class="col-12">
                     <h3 class="fw-bold mb-4">Scientific Modules</h3>
                     <div class="dashboard-grid">
                        <div class="db-card position-relative">
                            <span class="badge-overlay badge-essential">ESSENTIAL</span>
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="math/trigonometry" data-name="Mathematics"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/math/trigonometry'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-calculator"></i>
                                <h4>Mathematics</h4>
                                <p>Algebra, Geometry, Trig</p>
                            </a>
                        </div>
                        <div class="db-card position-relative">
                            <span class="badge-overlay badge-editor">EDITOR'S CHOICE</span>
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="finance/loan" data-name="Finance"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/finance/loan'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-cash-coin"></i>
                                <h4>Finance</h4>
                                <p>Loans, Investment, Salary</p>
                            </a>
                        </div>
                        <div class="db-card position-relative">
                            <span class="badge-overlay badge-new">NEW</span>
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="datetime/nepali" data-name="Date & Time"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/datetime/duration'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-calendar-date"></i>
                                <h4>Date & Time</h4>
                                <p>Diff, Adder, Nepali Date</p>
                            </a>
                        </div>
                        <div class="db-card position-relative">
                            <span class="badge-overlay badge-popular">POPULAR</span>
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="health/bmi" data-name="Health"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/health/bmi'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-activity"></i>
                                <h4>Health</h4>
                                <p>BMI, BMR, Calories</p>
                            </a>
                        </div>
                        <div class="db-card position-relative">
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="physics/velocity" data-name="Physics"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/physics/velocity'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-lightning-charge"></i>
                                <h4>Physics</h4>
                                <p>Force, Energy, Ohms</p>
                            </a>
                        </div>
                        <div class="db-card position-relative">
                            <span class="badge-overlay badge-new">NEW</span>
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="chemistry/molar-mass" data-name="Chemistry"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/chemistry/molar-mass'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-radioactive"></i>
                                <h4>Chemistry</h4>
                                <p>Molar Mass, pH, Gas Laws</p>
                            </a>
                        </div>
                        <div class="db-card position-relative">
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="statistics/basic" data-name="Statistics"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/statistics/basic'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-bar-chart-steps"></i>
                                <h4>Statistics</h4>
                                <p>Mean, Median, Probability</p>
                            </a>
                        </div>
                     </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <h3 class="fw-bold mb-4">Popular Converters</h3>
                    <div class="dashboard-grid">
                        <div class="db-card position-relative">
                            <span class="badge-overlay badge-popular">POPULAR</span>
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="converter/length" data-name="Length Converter" data-category="Converters"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/converter/length'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-rulers"></i>
                                <h4>Length</h4>
                                <p>Meters, Feet, Miles</p>
                            </a>
                        </div>
                        <div class="db-card position-relative">
                            <span class="badge-overlay badge-essential">ESSENTIAL</span>
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="converter/mass-weight" data-name="Weight Converter" data-category="Converters"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/converter/mass-weight'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-scales"></i>
                                <h4>Weight</h4>
                                <p>KG, Pounds, Grams</p>
                            </a>
                        </div>
                        <div class="db-card position-relative">
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="converter/area" data-name="Area Converter" data-category="Converters"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/converter/area'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-bounding-box-circles"></i>
                                <h4>Area</h4>
                                <p>SQM, Acres, Hectares</p>
                            </a>
                        </div>
                        <div class="db-card position-relative">
                            <button class="card-favorite-btn position-absolute top-0 end-0 m-2 btn btn-sm text-danger border-0 bg-transparent" data-slug="converter/volume" data-name="Volume Converter" data-category="Converters"><i class="bi bi-heart"></i></button>
                            <a href="<?php echo app_base_url('/calculator/converter/volume'); ?>" class="text-decoration-none text-white d-block">
                                <i class="bi bi-box"></i>
                                <h4>Volume</h4>
                                <p>Liters, Gallons, Cups</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
