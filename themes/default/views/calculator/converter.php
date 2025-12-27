<?php $page_title = $title ?? 'Unit Converter'; ?>
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
            overflow: hidden;
            height: 100vh;
            margin: 0;
        }

        .layout-wrapper {
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styles (Consistent with index.php) */
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

        .nav-category:hover, .nav-category.active {
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
        }

        .converter-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .converter-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            border: 1px solid var(--border);
            padding: 40px;
            margin-bottom: 30px;
        }

        .unit-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .unit-input-group {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .unit-value-input {
            flex: 1;
            background: transparent;
            border: none;
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            width: 100%;
            outline: none;
        }

        .unit-select-lite {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: white;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }

        /* Scientific Shortcut Buttons */
        .shortcut-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 10px;
            margin-bottom: 30px;
        }

        .shortcut-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: var(--text-main);
            border-radius: 10px;
            padding: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .shortcut-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .swap-circle {
            width: 50px;
            height: 50px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -40px auto 10px;
            position: relative;
            z-index: 2;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4);
            transition: all 0.3s;
        }

        .swap-circle:hover {
            transform: rotate(180deg) scale(1.1);
        }

        /* Results Log */
        .results-log {
            background: rgba(15, 23, 42, 0.4);
            border-radius: 20px;
            border: 1px solid var(--border);
            padding: 30px;
        }

        .log-item {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .log-item:last-child { border-bottom: none; }

        .log-formula { color: var(--text-muted); font-size: 0.9rem; }
        .log-result { font-weight: 700; color: var(--primary-light); }

        .text-gradient {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
            <nav class="sidebar-nav">
                <?php foreach ($categories as $cat): ?>
                <a href="<?php echo app_base_url('/calculator/converter/' . $cat['slug']); ?>" 
                   class="nav-category <?php echo ($cat['slug'] == $category['slug']) ? 'active' : ''; ?>">
                    <i class="<?php echo $cat['icon']; ?>"></i>
                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="converter-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold m-0"><i class="<?php echo $category['icon']; ?> me-3 text-primary"></i><?php echo htmlspecialchars($category['name']); ?> Converter</h2>
                    <span class="badge bg-primary rounded-pill px-3 py-2">Premium Tool</span>
                </div>

                <div class="converter-card shadow-lg">
                    <!-- From Section -->
                    <div class="unit-label">From</div>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="unit-input-group">
                                <input type="number" id="fromValue" class="unit-value-input" value="1" step="any" oninput="convertUnits()">
                            </div>
                            <!-- Shortcuts -->
                            <div class="shortcut-grid">
                                <button class="shortcut-btn" onclick="applyShortcut('base', 2)">x2</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 3)">x3</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 4)">x4</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 5)">x5</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 10)">x10</button>
                                <button class="shortcut-btn" onclick="applyShortcut('inv', 1)">1/x</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 0.5)">/2</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 1/3)">/3</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 0.25)">/4</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 0.2)">/5</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 0.1)">/10</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm mb-2 bg-dark text-white border-secondary" placeholder="Search units..." oninput="filterUnits('fromUnit', this.value)">
                            <select id="fromUnit" class="unit-select-lite w-100" onchange="convertUnits()" size="10" style="height: 200px; padding: 10px;">
                                <?php foreach ($units as $unit): ?>
                                <option value="<?php echo $unit['symbol']; ?>" <?php echo $unit['base_unit'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($unit['name']); ?> (<?php echo $unit['symbol']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="swap-circle" onclick="swapUnits()">
                        <i class="bi bi-arrow-down-up fs-4"></i>
                    </div>

                    <!-- To Section -->
                    <div class="unit-label">To</div>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="unit-input-group">
                                <input type="number" id="toValue" class="unit-value-input" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm mb-2 bg-dark text-white border-secondary" placeholder="Search units..." oninput="filterUnits('toUnit', this.value)">
                            <select id="toUnit" class="unit-select-lite w-100" onchange="convertUnits()" size="10" style="height: 200px; padding: 10px;">
                                <?php 
                                $firstNonBase = true;
                                foreach ($units as $unit): 
                                    if (!$unit['base_unit'] && $firstNonBase) {
                                        $firstNonBase = false; $selected = 'selected';
                                    } else { $selected = ''; }
                                ?>
                                <option value="<?php echo $unit['symbol']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($unit['name']); ?> (<?php echo $unit['symbol']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <button class="btn btn-primary rounded-pill px-5 py-3 fw-bold" onclick="addToLog()">
                            <i class="bi bi-journal-plus me-2"></i>Add to Results Log
                        </button>
                    </div>
                </div>

                <!-- Results Log -->
                <div class="results-log">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="m-0 fw-bold">Results Log</h4>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="clearLog()">Clear Log</button>
                    </div>
                    <div id="logItems">
                        <div class="text-center py-4 text-muted">No entries in the log yet.</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const categoryId = <?php echo $category['id']; ?>;
        const unitsData = <?php echo json_encode($units); ?>;
        
        function convertUnits() {
            const fromValue = parseFloat(document.getElementById('fromValue').value) || 0;
            const fromUnit = document.getElementById('fromUnit').value;
            const toUnit = document.getElementById('toUnit').value;
            
            const fromUnitData = unitsData.find(u => u.symbol === fromUnit);
            const toUnitData = unitsData.find(u => u.symbol === toUnit);
            
            if (!fromUnitData || !toUnitData) return;
            
            let result = 0;

            if (categoryId === 18) { // Temp
                let celsius = 0;
                switch (fromUnit) {
                    case '°C': celsius = fromValue; break;
                    case '°F': celsius = (fromValue - 32) / 1.8; break;
                    case 'K':  celsius = fromValue - 273.15; break;
                    case '°R': celsius = (fromValue / 1.8) - 273.15; break;
                    default:   celsius = fromValue;
                }
                switch (toUnit) {
                    case '°C': result = celsius; break;
                    case '°F': result = (celsius * 1.8) + 32; break;
                    case 'K':  result = celsius + 273.15; break;
                    case '°R': result = (celsius + 273.15) * 1.8; break;
                    default:   result = celsius;
                }
            } else {
                const baseValue = fromValue * parseFloat(fromUnitData.to_base_multiplier);
                result = baseValue / parseFloat(toUnitData.to_base_multiplier);
            }
            
            document.getElementById('toValue').value = (result % 1 === 0) ? result : result.toFixed(6);
        }

        function applyShortcut(type, factor) {
            const input = document.getElementById('fromValue');
            let val = parseFloat(input.value) || 0;
            if (type === 'base') val *= factor;
            else if (type === 'inv') val = 1 / val;
            
            input.value = (val % 1 === 0) ? val : val.toFixed(4);
            convertUnits();
        }

        function swapUnits() {
            const fromUnit = document.getElementById('fromUnit');
            const toUnit = document.getElementById('toUnit');
            const temp = fromUnit.value;
            fromUnit.value = toUnit.value;
            toUnit.value = temp;
            convertUnits();
        }

        function addToLog() {
            const fromValue = document.getElementById('fromValue').value;
            const fromUnit = document.getElementById('fromUnit').value;
            const toValue = document.getElementById('toValue').value;
            const toUnit = document.getElementById('toUnit').value;
            
            const logItems = document.getElementById('logItems');
            if (logItems.innerText.includes('No entries')) logItems.innerHTML = '';
            
            const item = document.createElement('div');
            item.className = 'log-item';
            item.innerHTML = `
                <div class="log-formula">${fromValue} ${fromUnit} →</div>
                <div class="log-result">${toValue} ${toUnit}</div>
            `;
            logItems.prepend(item);
        }

        function clearLog() {
            document.getElementById('logItems').innerHTML = '<div class="text-center py-4 text-muted">No entries in the log yet.</div>';
        }

        function filterUnits(selectId, query) {
            const select = document.getElementById(selectId);
            const options = select.options;
            const lowerQuery = query.toLowerCase();
            
            for (let i = 0; i < options.length; i++) {
                const text = options[i].text.toLowerCase();
                const match = text.includes(lowerQuery);
                options[i].style.display = match ? 'block' : 'none';
            }
        }

        // Initial
        convertUnits();
    </script>
</body>
</html>
