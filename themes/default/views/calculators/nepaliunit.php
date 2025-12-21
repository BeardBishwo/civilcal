<?php // Layout is handled automatically by App\Core\View::render ?>

<div class="pink-precision-wrapper">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-deep: #09090b;
            --bg-surface: #18181b;
            --bg-card: #27272a;
            --accent: #ec4899; /* Pink Accent */
            --accent-glow: rgba(236, 72, 153, 0.2);
            --text-primary: #fafafa;
            --text-secondary: #a1a1aa;
            --text-dim: #71717a;
            --border-subtle: #3f3f46;
            --font-main: 'Outfit', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body {
            background-color: var(--bg-deep) !important;
            color: var(--text-primary);
        }

        .pink-precision-wrapper {
            font-family: var(--font-main);
            height: 100vh;
            display: flex;
            background-color: var(--bg-deep);
            overflow: hidden;
        }

        /* --- Workspace Layout --- */
        .workspace {
            display: grid;
            grid-template-columns: 320px 1fr;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        /* --- Sidebar (Inputs) --- */
        .sidebar {
            background: var(--bg-surface);
            border-right: 1px solid var(--border-subtle);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            z-index: 10;
        }

        .brand-sec {
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 1rem;
        }

        .brand-sec h1 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .brand-sec span {
            color: var(--accent);
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .label-mono {
            font-family: var(--font-mono);
            font-size: 0.65rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.4rem;
            display: block;
        }

        .input-field {
            background: var(--bg-deep);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #fff;
            padding: 0.65rem 0.85rem;
            font-family: var(--font-mono);
            font-size: 1rem;
            width: 100%;
            transition: all 0.2s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px var(--accent-glow);
        }

        .select-field {
            background: var(--bg-deep);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            color: #fff;
            padding: 0.65rem 0.85rem;
            font-family: var(--font-main);
            font-size: 0.9rem;
            width: 100%;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2371717a'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.85rem center;
            background-size: 0.9rem;
        }

        .btn-action {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.85rem;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 0.5rem;
        }

        .btn-action:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);
        }

        /* --- Main Console --- */
        .main-console {
            background: var(--bg-deep);
            padding: 2rem 2.5rem;
            overflow-y: auto;
            position: relative;
        }

        .hero-result {
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(15px);
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            cursor: pointer;
        }

        .hero-result.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        .hero-result:hover .value {
            color: var(--accent);
        }

        .hero-result .label {
            color: var(--accent);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 0.5rem;
            display: block;
        }

        .hero-result .value {
            font-family: var(--font-mono);
            font-size: 4rem; /* Compact Hero */
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.04em;
            transition: color 0.2s ease;
        }

        .hero-result .unit {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-left: 0.75rem;
            font-weight: 400;
        }

        /* --- Matrix Sections --- */
        .matrix-section {
            margin-bottom: 1.5rem; /* Compact sections */
        }

        .matrix-header {
            font-size: 0.7rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .matrix-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-subtle);
        }

        .unit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.75rem;
        }

        .unit-node {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .unit-node:hover {
            border-color: var(--accent);
            background: var(--bg-card);
            transform: translateY(-2px);
        }

        .unit-node.highlight {
            border-color: var(--accent);
            box-shadow: inset 0 0 15px rgba(236, 72, 153, 0.05);
        }

        .unit-node .val {
            font-family: var(--font-mono);
            font-size: 1.15rem;
            font-weight: 700;
        }

        .unit-node .sys {
            font-size: 0.6rem;
            color: var(--accent);
            background: rgba(236, 72, 153, 0.1);
            padding: 1px 6px;
            border-radius: 4px;
            width: fit-content;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* --- Information Strip --- */
        .info-strip {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            border-top: 1px solid var(--border-subtle);
            padding-top: 1.5rem;
        }

        .info-card {
            text-align: center;
            padding: 0.5rem;
        }

        .info-card h4 {
            color: var(--accent);
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
            font-weight: 700;
        }

        .info-card p {
            color: var(--text-secondary);
            font-size: 0.8rem;
            margin: 0;
            line-height: 1.5;
        }

        /* --- Toast Notification --- */
        .copy-toast {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: var(--accent);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.4);
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .copy-toast.active {
            transform: translateX(-50%) translateY(0);
        }

        /* --- Empty State --- */
        .empty-state {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            color: var(--text-dim);
        }

        .empty-state.d-none {
            display: none !important;
        }

        /* Scrollbar */
        .main-console::-webkit-scrollbar { width: 6px; }
        .main-console::-webkit-scrollbar-track { background: var(--bg-deep); }
        .main-console::-webkit-scrollbar-thumb { background: var(--border-subtle); border-radius: 10px; }
        .main-console::-webkit-scrollbar-thumb:hover { background: var(--text-dim); }

        @media (max-width: 1024px) {
            .workspace { 
                display: flex; /* Using flex column for better stack control */
                flex-direction: column;
                height: auto; 
                overflow: visible; 
            }
            .sidebar { 
                border-right: none; 
                border-bottom: 1px solid var(--border-subtle); 
                height: auto; 
                padding: 1.25rem;
                position: -webkit-sticky;
                position: sticky;
                top: 0;
                background: var(--bg-surface);
                z-index: 1000;
                width: 100%;
                box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            }
            .main-console { 
                height: auto; 
                overflow: visible; 
                padding: 1.5rem 1.25rem;
                flex: 1;
            }
            .info-strip { grid-template-columns: 1fr; gap: 1rem; }
            .pink-precision-wrapper { height: auto; overflow: visible; }
            .hero-result .value { font-size: 2.2rem; }
            .unit-grid { 
                grid-template-columns: 1fr !important; /* Force 1 column strictly */
                gap: 0.75rem;
            }
            .matrix-header { font-size: 0.65rem; margin-top: 1.25rem; }
            .hero-result .unit { font-size: 1rem; }
        }

        @media (max-width: 480px) {
            .hero-result .value { font-size: 1.8rem; }
            .brand-sec { display: none; } /* Hide for compact sticky */
            .sidebar { padding: 0.75rem 1rem; }
            .input-field, .select-field { font-size: 0.8rem; height: 36px; padding: 0 0.5rem; }
            .label-mono { font-size: 0.6rem; margin-bottom: 0.2rem; }
            .control-group { gap: 0.5rem; }
        }
    </style>

    <div class="workspace">
        <aside class="sidebar">
            <div class="brand-sec">
                <span>Pink Precision Toolkit</span>
                <h1>Nepali Unit</h1>
            </div>

            <form id="pro-calc-form" class="control-group">
                <div>
                    <span class="label-mono">01. Analysis Value</span>
                    <input type="number" id="value" name="value" class="input-field" step="any" value="1" required autofocus>
                </div>

                <div>
                    <span class="label-mono">02. Reference Unit</span>
                    <select id="from_unit" name="from_unit" class="select-field" required>
                        <optgroup label="Hilly Area (Ropani System)">
                            <option value="ropani" selected>Ropani</option>
                            <option value="aana">Aana</option>
                            <option value="paisa">Paisa</option>
                            <option value="daam">Daam</option>
                        </optgroup>
                        <optgroup label="Terai Area (Bigha System)">
                            <option value="bigha">Bigha</option>
                            <option value="kattha">Kattha</option>
                            <option value="dhur">Dhur</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <span class="label-mono">03. Target Objective</span>
                    <select id="to_unit" name="to_unit" class="select-field" required>
                        <optgroup label="Metric Standard">
                            <option value="sq_meter" selected>Square Meters (m²)</option>
                            <option value="sq_mm">Square Millimeters (mm²)</option>
                            <option value="hectare">Hectares</option>
                        </optgroup>
                        <optgroup label="Imperial Standard">
                            <option value="sq_feet">Square Feet (sq.ft)</option>
                            <option value="sq_in">Square Inches (sq.in)</option>
                            <option value="acre">Acres</option>
                        </optgroup>
                        <optgroup label="Traditional Native">
                            <option value="ropani">Ropani</option>
                            <option value="bigha">Bigha</option>
                        </optgroup>
                    </select>
                </div>

                <div class="d-md-none">
                    <button type="submit" class="btn-action w-100">
                        Run Computation
                    </button>
                </div>
            </form>

            <!-- Removed footer hint -->
        </aside>

        <main class="main-console">
            <div id="initial-view" class="empty-state">
                <i class="fas fa-bolt"></i>
                <h3>Pink Precision Ready</h3>
                <p>Execute analysis to view multi-pane results.</p>
            </div>

            <div id="analysis-view" class="d-none">
                <div id="hero-output" class="hero-result" title="Click to copy value">
                    <span class="label">Live Computation</span>
                    <div>
                        <span id="result-val" class="value text-white">0.00</span>
                        <span id="result-unit" class="unit">Units</span>
                    </div>
                </div>

                <div id="hilly-section" class="matrix-section">
                    <div class="matrix-header">Hilly Region (Ropani)</div>
                    <div id="hilly-grid" class="unit-grid"></div>
                </div>

                <div id="terai-section" class="matrix-section">
                    <div class="matrix-header">Terai Region (Bigha)</div>
                    <div id="terai-grid" class="unit-grid"></div>
                </div>

                <div id="metric-section" class="matrix-section">
                    <div class="matrix-header">Metric Standards</div>
                    <div id="metric-grid" class="unit-grid"></div>
                </div>

                <div id="imperial-section" class="matrix-section">
                    <div class="matrix-header">Imperial Standards</div>
                    <div id="imperial-grid" class="unit-grid"></div>
                </div>

                <div class="info-strip">
                    <div class="info-card">
                        <h4>1 Ropani</h4>
                        <p>74ft x 74ft</p>
                        <p>16 Aana</p>
                    </div>
                    <div class="info-card">
                        <h4>1 Bigha</h4>
                        <p>13.31 Ropani</p>
                        <p>20 Kattha</p>
                    </div>
                    <div class="info-card">
                        <h4>1 Kattha</h4>
                        <p>442 sq. yards</p>
                        <p>338.63 sq. m</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Copy Notification -->
    <div id="copy-toast" class="copy-toast">
        <i class="fas fa-check"></i>
        <span>COPIED TO CLIPBOARD</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pro-calc-form');
    const inputVal = document.getElementById('value');
    const fromUnitSelect = document.getElementById('from_unit');
    const toUnitSelect = document.getElementById('to_unit');
    
    const initialView = document.getElementById('initial-view');
    const analysisView = document.getElementById('analysis-view');
    const heroOutput = document.getElementById('hero-output');
    const resultVal = document.getElementById('result-val');
    const resultUnit = document.getElementById('result-unit');
    const hillyGrid = document.getElementById('hilly-grid');
    const teraiGrid = document.getElementById('terai-grid');
    const metricGrid = document.getElementById('metric-grid');
    const imperialGrid = document.getElementById('imperial-grid');
    const copyToast = document.getElementById('copy-toast');

    let debounceTimer;

    function copyToClipboard(text) {
        const cleanText = text.replace(/,/g, '');
        navigator.clipboard.writeText(cleanText).then(() => {
            copyToast.classList.add('active');
            setTimeout(() => copyToast.classList.remove('active'), 1500);
        });
    }

    function debounceCompute() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            performAnalysis();
        }, 150); // Blazing fast 150ms debounce
    }

    // Auto-compute Listeners
    inputVal.addEventListener('input', debounceCompute);
    fromUnitSelect.addEventListener('change', performAnalysis);
    toUnitSelect.addEventListener('change', performAnalysis);
    heroOutput.addEventListener('click', () => copyToClipboard(resultVal.innerText));

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        performAnalysis();
    });

    async function performAnalysis() {
        const value = parseFloat(inputVal.value);
        const fromUnit = fromUnitSelect.value;
        const toUnit = toUnitSelect.value;

        if (isNaN(value)) {
            analysisView.classList.add('d-none');
            initialView.classList.remove('d-none');
            return;
        }

        try {
            const response = await fetch('<?php echo app_base_url('/api/nepali-unit/all-conversions'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ value, from_unit: fromUnit, metric_unit: toUnit })
            });

            const data = await response.json();

            if (data.success) {
                let outVal = '0';
                let outUnit = toUnit;

                if (data.conversions.traditional && data.conversions.traditional[toUnit]) {
                    outVal = data.conversions.traditional[toUnit].output_value;
                    outUnit = data.conversions.traditional[toUnit].output_unit_name;
                } else {
                    const directRes = await fetch('<?php echo app_base_url('/api/nepali-unit/convert'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ value, from_unit: fromUnit, to_unit: toUnit })
                    });
                    const directData = await directRes.json();
                    if(directData.success) {
                        outVal = directData.output_value;
                        outUnit = directData.output_unit_name;
                    }
                }

                initialView.classList.add('d-none');
                analysisView.classList.remove('d-none');
                
                resultVal.innerText = formatNumber(outVal);
                resultUnit.innerText = outUnit;
                heroOutput.classList.add('active');

                // Clear
                hillyGrid.innerHTML = '';
                teraiGrid.innerHTML = '';
                metricGrid.innerHTML = '';
                imperialGrid.innerHTML = '';
                
                if (data.conversions.traditional) {
                    Object.entries(data.conversions.traditional).forEach(([key, info]) => {
                        const system = info.system || 'hilly';
                        const grid = system === 'hilly' ? hillyGrid : teraiGrid;
                        if(key !== 'sq_feet') addMatrixNode(grid, info.output_unit_name, info.output_value, key === fromUnit);
                    });
                }
                
                if (data.conversions.metric) {
                    const baseSqFt = data.conversions.metric.sq_feet_value;
                    // Metric Standards
                    addMatrixNode(metricGrid, 'Sq. Meters', baseSqFt * 0.092903, false);
                    addMatrixNode(metricGrid, 'Sq. mm', baseSqFt * 92903.04, false);
                    addMatrixNode(metricGrid, 'Hectares', baseSqFt / 107639, false);
                    
                    // Imperial Standards
                    addMatrixNode(imperialGrid, 'Sq. Feet', baseSqFt, false);
                    addMatrixNode(imperialGrid, 'Sq. Inches', baseSqFt * 144, false);
                    addMatrixNode(imperialGrid, 'Acres', baseSqFt / 43560, false);
                }
            }
        } catch (error) {
            console.error('Computation error:', error);
        }
    }

    function addMatrixNode(container, name, val, isSource) {
        const node = document.createElement('div');
        node.className = `unit-node ${isSource ? 'highlight' : ''}`;
        node.innerHTML = `<span class="sys">${name}</span><span class="val">${formatNumber(val)}</span>`;
        node.addEventListener('click', () => copyToClipboard(val.toString()));
        container.appendChild(node);
    }

    function formatNumber(num) {
        const n = parseFloat(num);
        if (n === 0) return '0.00';
        if (n < 0.0001) return n.toExponential(4);
        return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 6 });
    }

    // Initial compute
    performAnalysis();
});
</script>
