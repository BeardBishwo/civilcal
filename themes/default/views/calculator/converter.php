<?php 
/**
 * Calculator Platform - Converter View
 * 
 * Handles layout and logic for all unit conversions.
 * Features:
 * - Dynamic Sidebar & Branding
 * - Linear Layout (From -> Swap -> To)
 * - Scientific Shortcuts
 * - Result Logging
 */
$site_meta = get_site_meta();
$site_title = defined('APP_NAME') ? APP_NAME : $site_meta['title'];
$page_title = $title ?? ($category['name'] . ' Converter - ' . $site_title); 

// Helper to format unit symbols with superscripts
function formatUnitSymbol($symbol) {
    // Map common power representations to HTML superscripts
    $map = [
        '2' => '<sup>2</sup>',
        '3' => '<sup>3</sup>',
        '²' => '<sup>2</sup>',
        '³' => '<sup>3</sup>',
        '┬▓' => '<sup>2</sup>', // Handle potential encoding corruption
        '┬│' => '<sup>3</sup>',
    ];
    // Targeted replacement: only numbers at the end or following non-digits
    return preg_replace_callback('/([a-zA-Z\/])([23²³])|([┬][▓│])/', function($m) use ($map) {
        $char = $m[2] ?? $m[0];
        return ($m[1] ?? '') . ($map[$char] ?? $char);
    }, $symbol);
}
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
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/calculator_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="converter-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold m-0"><i class="<?php echo $category['icon']; ?> me-3 text-primary"></i><?php echo htmlspecialchars($category['name']); ?> Converter</h2>
                    <span class="badge bg-primary rounded-pill px-3 py-2">Premium Tool</span>
                </div>

                <div class="converter-card glass-card shadow-lg">
                    <!-- From Section -->
                    <div class="row align-items-center mb-0">
                        <div class="col-md-5">
                            <label class="unit-label-sm mb-2 d-block text-secondary small text-uppercase fw-bold ls-1">From</label>
                            <div class="unit-input-wrapper mb-3">
                                <input type="number" id="fromValue" class="unit-value-input form-control form-control-lg bg-dark text-white border-glass" value="1" step="any" oninput="convertUnits()">
                            </div>
                             <div class="custom-dropdown-container" id="fromDropdown">
                                <div class="custom-dropdown-btn" onclick="toggleDropdown('fromDropdown')">
                                    <span class="selected-text">Select Unit</span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="custom-dropdown-menu">
                                    <div class="custom-dropdown-search">
                                        <input type="text" placeholder="Search units..." oninput="filterCustomDropdown('fromDropdown', this.value)">
                                    </div>
                                    <div class="custom-dropdown-list">
                                        <?php foreach ($units as $unit): ?>
                                         <div class="custom-dropdown-item <?php echo $unit['base_unit'] ? 'selected' : ''; ?>" 
                                              data-value="<?php echo $unit['symbol']; ?>" 
                                              onclick="selectDropdownItem('fromDropdown', '<?php echo $unit['symbol']; ?>', '<?php echo htmlspecialchars($unit['name']); ?>')">
                                            <?php echo htmlspecialchars($unit['name']); ?> <span class="unit-symbol text-muted ms-1">(<?php echo formatUnitSymbol($unit['symbol']); ?>)</span>
                                         </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <input type="hidden" id="fromUnit" value="<?php echo array_filter($units, fn($u) => $u['base_unit'])[0]['symbol'] ?? $units[0]['symbol']; ?>" onchange="convertUnits()">
                            </div>
                        </div>

                        <div class="col-md-2 text-center py-3 py-md-0">
                            <div class="swap-circle mx-auto" onclick="swapUnits()">
                                <i class="bi bi-arrow-left-right fs-4"></i>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="unit-label-sm mb-2 d-block text-secondary small text-uppercase fw-bold ls-1">To</label>
                            <div class="unit-input-wrapper mb-3">
                                <input type="number" id="toValue" class="unit-value-input form-control form-control-lg bg-dark text-white border-glass" readonly>
                            </div>
                            <div class="custom-dropdown-container" id="toDropdown">
                                <div class="custom-dropdown-btn" onclick="toggleDropdown('toDropdown')">
                                    <span class="selected-text">Select Unit</span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="custom-dropdown-menu">
                                    <div class="custom-dropdown-search">
                                        <input type="text" placeholder="Search units..." oninput="filterCustomDropdown('toDropdown', this.value)">
                                    </div>
                                    <div class="custom-dropdown-list">
                                        <?php 
                                        $firstNonBase = true;
                                        foreach ($units as $unit): 
                                            $isSelected = false;
                                            if (!$unit['base_unit'] && $firstNonBase) {
                                                $firstNonBase = false; $isSelected = true;
                                            }
                                        ?>
                                         <div class="custom-dropdown-item <?php echo $isSelected ? 'selected' : ''; ?>" 
                                              data-value="<?php echo $unit['symbol']; ?>" 
                                              onclick="selectDropdownItem('toDropdown', '<?php echo $unit['symbol']; ?>', '<?php echo htmlspecialchars($unit['name']); ?>')">
                                            <?php echo htmlspecialchars($unit['name']); ?> <span class="unit-symbol text-muted ms-1">(<?php echo formatUnitSymbol($unit['symbol']); ?>)</span>
                                         </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <input type="hidden" id="toUnit" value="" onchange="convertUnits()">
                            </div>
                        </div>
                    </div>

                    <hr class="glass-divider my-4">

                    <!-- Shortcuts & Actions Bottom Row -->
                    <div class="d-flex flex-column gap-3">
                         <!-- Shortcuts -->
                         <div class="shortcut-rows justify-content-center">
                            <div class="shortcut-line justify-content-center">
                                <button class="shortcut-btn" onclick="applyShortcut('base', 2)">x2</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 3)">x3</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 4)">x4</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 5)">x5</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 10)">x10</button>
                                <button class="shortcut-btn" onclick="applyShortcut('inv', 1)">1/x</button>
                            </div>
                            <div class="shortcut-line justify-content-center">
                                <button class="shortcut-btn" onclick="applyShortcut('base', 0.5)">/2</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 1/3)">/3</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 0.25)">/4</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 0.2)">/5</button>
                                <button class="shortcut-btn" onclick="applyShortcut('base', 0.1)">/10</button>
                            </div>
                        </div>

                        <!-- Add to Log Button -->
                        <div class="text-center mt-2">
                             <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg rounded-pill fw-bold shadow-primary transition-hover d-flex align-items-center justify-content-center gap-2" onclick="addToLog()">
                                    <i class="bi bi-journal-plus fs-5"></i>
                                    <span>Add to Results Log</span>
                                </button>
                            </div>
                        </div>
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
        
        // Custom Dropdown Logic
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const menu = dropdown.querySelector('.custom-dropdown-menu');
            const isOpen = menu.style.display === 'block';
            
            // Close others
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.style.display = 'none');
            
            menu.style.display = isOpen ? 'none' : 'block';
            if (!isOpen) {
                dropdown.querySelector('.custom-dropdown-search input').focus();
            }
        }

        function selectDropdownItem(dropdownId, value, text) {
            const container = document.getElementById(dropdownId);
            const hiddenInput = container.querySelector('input[type="hidden"]');
            const selectedSpan = container.querySelector('.selected-text');
            
            hiddenInput.value = value;
            selectedSpan.innerText = text;
            
            // Update UI
            container.querySelectorAll('.custom-dropdown-item').forEach(item => {
                item.classList.toggle('selected', item.getAttribute('data-value') === value);
            });
            
            container.querySelector('.custom-dropdown-menu').style.display = 'none';
            
            // Trigger conversion
            convertUnits();
        }

        function filterCustomDropdown(dropdownId, query) {
            const container = document.getElementById(dropdownId);
            const items = container.querySelectorAll('.custom-dropdown-item');
            const lowerQuery = query.toLowerCase();
            
            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                item.style.display = text.includes(lowerQuery) ? 'block' : 'none';
            });
        }

        // Close dropdowns on outside click
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-dropdown-container')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.style.display = 'none');
            }
        });

        // Conversion Logic
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
            const fromInput = document.getElementById('fromUnit');
            const toInput = document.getElementById('toUnit');
            const tempVal = fromInput.value;
            
            // Use custom selection logic to update visuals too
            const fromItem = document.querySelector(`#fromDropdown .custom-dropdown-item[data-value="${toInput.value}"]`);
            const toItem = document.querySelector(`#toDropdown .custom-dropdown-item[data-value="${tempVal}"]`);
            
            if (fromItem && toItem) {
                selectDropdownItem('fromDropdown', toInput.value, fromItem.innerText);
                selectDropdownItem('toDropdown', tempVal, toItem.innerText);
            }
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

        // Sidebar Persistence & Auto-scroll
        window.onload = function() {
            const activeItem = document.querySelector('.nav-category.active');
            if (activeItem) {
                activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Initialize Dropdown Texts
            const fromVal = document.getElementById('fromUnit').value;
            const fromItem = document.querySelector(`#fromDropdown .custom-dropdown-item[data-value="${fromVal}"]`);
            if (fromItem) document.querySelector('#fromDropdown .selected-text').innerText = fromItem.innerText;
            
            // Initialize 'To' unit (first non-base or second item)
            const toItems = document.querySelectorAll('#toDropdown .custom-dropdown-item');
            let toItem = Array.from(toItems).find(i => i.classList.contains('selected'));
            if (!toItem) toItem = toItems[1] || toItems[0];
            if (toItem) selectDropdownItem('toDropdown', toItem.getAttribute('data-value'), toItem.innerText);
            
            convertUnits();
        };
    </script>
</body>
</html>
