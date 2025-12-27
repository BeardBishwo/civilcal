<?php $page_title = $title ?? 'Scientific Calculator'; ?>
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
            --card-bg: #141b2d;
            --border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            height: 100vh;
            margin: 0;
        }

        .layout-wrapper {
            display: flex;
            height: 100vh;
        }

        /* Sidebar (Same as index) */
        .sidebar {
            width: 300px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
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

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
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

        .nav-category i {
            font-size: 1.25rem;
            margin-right: 15px;
            width: 24px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .calc-standalone-card {
            background: var(--card-bg);
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border);
            overflow: hidden;
            display: flex;
            width: 100%;
            max-width: 1000px;
            height: 700px;
        }

        .calc-main-panel {
            flex: 2;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }

        .calc-history-panel {
            flex: 1;
            background: rgba(15, 23, 42, 0.3);
            border-left: 1px solid var(--border);
            padding: 30px;
            display: flex;
            flex-direction: column;
        }

        .history-title {
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .history-list {
            flex: 1;
            overflow-y: auto;
        }

        .history-item {
            padding: 15px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
        }

        .history-item:hover { background: rgba(255, 255, 255, 0.05); }

        /* Display */
        .calc-display-section {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: right;
            border: 1px solid var(--border);
        }

        #calc-main-display {
            font-size: 3.5rem;
            font-weight: 700;
            color: #fff;
        }

        /* Keypad */
        .calc-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            flex-grow: 1;
        }

        .scientific-btn {
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.2s;
            background: rgba(255, 255, 255, 0.03);
            color: white;
        }

        .scientific-btn:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-op { color: #fb923c; }
        .btn-func { color: #38bdf8; }
        .btn-equal {
            grid-column: span 2;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?php echo app_base_url('/calculator'); ?>" class="sidebar-brand">
                    <i class="bi bi-grid-fill me-2"></i>Bishwo Calc
                </a>
            </div>
            <nav class="sidebar-nav">
                <a href="<?php echo app_base_url('/calculator/scientific'); ?>" class="nav-category active">
                    <i class="bi bi-cpu"></i>
                    <span>Scientific</span>
                </a>
                <?php foreach ($categories as $cat): ?>
                <a href="<?php echo app_base_url('/calculator/converter/' . $cat['slug']); ?>" class="nav-category">
                    <i class="<?php echo $cat['icon']; ?>"></i>
                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
        </aside>

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
        </main>
    </div>

    <script>
        window.appConfig = { baseUrl: "<?php echo rtrim(app_base_url(), '/'); ?>", csrfToken: "<?php echo csrf_token(); ?>" };
    </script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/scientific-calculator.js'); ?>"></script>
</body>
</html>
