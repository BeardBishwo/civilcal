<?php 
/**
 * Calculator Platform - Scientific Calculator
 * 
 * Full-screen scientific calculator interface.
 * Features:
 * - Advanced Math Functions (Trig, Log, Pow)
 * - Calculation History
 * - Keyboard Support
 */
$site_meta = get_site_meta();
$site_title = defined('APP_NAME') ? APP_NAME : $site_meta['title'];
$page_title = $title ?? ('Scientific Calculator - ' . $site_title); 
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
        <?php include __DIR__ . '/../partials/calculator_sidebar.php'; ?>

        <main class="main-content">
            <div class="calc-standalone-card shadow-2xl">
                <div class="calc-main-panel">
                    <div class="calc-display-section">
                        <div id="calc-expr" class="text-muted small"></div>
                        <div id="calc-main-display">0</div>
                    </div>

                    <div class="calc-grid">
                        <!-- Keys -->
                        <button class="scientific-btn text-danger" onclick="clearAll()">AC</button>
                        <button class="scientific-btn btn-func" onclick="appendFunc('sin')">sin</button>
                        <button class="scientific-btn btn-func" onclick="appendFunc('cos')">cos</button>
                        <button class="scientific-btn btn-func" onclick="appendFunc('tan')">tan</button>
                        <button class="scientific-btn btn-op" onclick="deleteLast()"><i class="bi bi-backspace"></i></button>

                        <button class="scientific-btn" onclick="appendNum('7')">7</button>
                        <button class="scientific-btn" onclick="appendNum('8')">8</button>
                        <button class="scientific-btn" onclick="appendNum('9')">9</button>
                        <button class="scientific-btn btn-func" onclick="appendOp('(')">(</button>
                        <button class="scientific-btn btn-op" onclick="appendOp('/')">÷</button>

                        <button class="scientific-btn" onclick="appendNum('4')">4</button>
                        <button class="scientific-btn" onclick="appendNum('5')">5</button>
                        <button class="scientific-btn" onclick="appendNum('6')">6</button>
                        <button class="scientific-btn btn-func" onclick="appendOp(')')">)</button>
                        <button class="scientific-btn btn-op" onclick="appendOp('*')">×</button>

                        <button class="scientific-btn" onclick="appendNum('1')">1</button>
                        <button class="scientific-btn" onclick="appendNum('2')">2</button>
                        <button class="scientific-btn" onclick="appendNum('3')">3</button>
                        <button class="scientific-btn btn-func" onclick="appendOp('pow')">xʸ</button>
                        <button class="scientific-btn btn-op" onclick="appendOp('-')">-</button>

                        <button class="scientific-btn" onclick="appendNum('0')">0</button>
                        <button class="scientific-btn" onclick="appendNum('.')">.</button>
                        <button class="scientific-btn btn-func" onclick="appendIcon('π')">π</button>
                        <button class="scientific-btn btn-equal" onclick="performCalculate()">=</button>
                        <button class="scientific-btn btn-op" onclick="appendOp('+')">+</button>
                    </div>
                </div>

                <div class="calc-history-panel">
                    <div class="history-title">History <button class="btn btn-sm btn-link text-muted" onclick="clearHistory()">Clear</button></div>
                    <div id="historyList" class="history-list"></div>
                </div>
            </div>

            <?php echo \App\Helpers\AdHelper::show('result_bottom', 'mt-4 text-center'); ?>
        </main>
    </div>

    <script>
        window.appConfig = { baseUrl: "<?php echo rtrim(app_base_url(), '/'); ?>", csrfToken: "<?php echo csrf_token(); ?>" };
    </script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/scientific-calculator.js'); ?>"></script>
</body>
</html>
