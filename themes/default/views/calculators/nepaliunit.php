<?php // Layout is handled automatically by App\Core\View::render ?>

<div class="precision-pro-wrapper">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-deep: #09090b;
            --bg-surface: #18181b;
            --bg-card: #27272a;
            --accent: #6366f1;
            --accent-glow: rgba(99, 102, 241, 0.2);
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

        .precision-pro-wrapper {
            font-family: var(--font-main);
            min-height: 100vh;
            display: flex;
            background-color: var(--bg-deep);
        }

        /* --- Workspace Layout --- */
        .workspace {
            display: grid;
            grid-template-columns: 350px 1fr;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        /* --- Sidebar (Inputs) --- */
        .sidebar {
            background: var(--bg-surface);
            border-right: 1px solid var(--border-subtle);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            z-index: 10;
        }

        .brand-sec {
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 1.5rem;
        }

        .brand-sec h1 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .brand-sec span {
            color: var(--accent);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .label-mono {
            font-family: var(--font-mono);
            font-size: 0.7rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-field {
            background: var(--bg-deep);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            color: #fff;
            padding: 0.8rem 1rem;
            font-family: var(--font-mono);
            font-size: 1.1rem;
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
            border-radius: 8px;
            color: #fff;
            padding: 0.8rem 1rem;
            font-family: var(--font-main);
            font-size: 0.95rem;
            width: 100%;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2371717a'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
        }

        .btn-action {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 1rem;
        }

        .btn-action:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        /* --- Main Console (Results) --- */
        .main-console {
            background: var(--bg-deep);
            padding: 3rem;
            overflow-y: auto;
            position: relative;
        }

        .hero-result {
            margin-bottom: 3rem;
            opacity: 0;
            transform: translateY(20px);
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
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 1rem;
            display: block;
        }

        .hero-result .value {
            font-family: var(--font-mono);
            font-size: 5.5rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.04em;
            transition: color 0.2s ease;
        }

        .hero-result .unit {
            font-size: 1.5rem;
            color: var(--text-secondary);
            margin-left: 1rem;
            font-weight: 400;
        }

        /* --- Matrix Sections --- */
        .matrix-section {
            margin-bottom: 2.5rem;
        }

        .matrix-header {
            font-size: 0.75rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 600;
            margin-bottom: 1.25rem;
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
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .unit-node {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
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
            box-shadow: inset 0 0 20px rgba(99, 102, 241, 0.05);
        }

        .unit-node .val {
            font-family: var(--font-mono);
            font-size: 1.25rem;
            font-weight: 700;
        }

        .unit-node .sys {
            font-size: 0.65rem;
            color: var(--accent);
            background: rgba(99, 102, 241, 0.1);
            padding: 2px 8px;
            border-radius: 4px;
            width: fit-content;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* --- Information Cards --- */
        .info-strip {
            margin-top: 4rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            border-top: 1px solid var(--border-subtle);
            padding-top: 2rem;
        }

        .info-card {
            text-align: center;
            padding: 1rem;
        }

        .info-card h4 {
            color: var(--accent);
            font-size: 1rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .info-card p {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin: 0;
            line-height: 1.6;
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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
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

        /* Responsive */
        @media (max-width: 1024px) {
            .workspace {
                grid-template-columns: 1fr;
                height: auto;
                overflow: visible;
            }
            .sidebar {
                border-right: none;
                border-bottom: 1px solid var(--border-subtle);
                height: auto;
            }
            .main-console {
                height: auto;
            }
            .info-strip {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="workspace">
        <aside class="sidebar">
            <div class="brand-sec">
                <span>Precision Engineering</span>
                <h1>Nepali Unit</h1>
            </div>

            <form id="pro-calc-form" class="control-group">
                <div>
                    <span class="label-mono">01. Input Analysis Value</span>
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
                            <option value="sq_feet">Square Feet (sq.ft)</option>
                            <option value="sq_meter" selected>Square Meters (m²)</option>
                            <option value="acre">Acres</option>
                            <option value="hectare">Hectares</option>
                        </optgroup>
                        <optgroup label="Traditional Native">
                            <option value="ropani">Ropani</option>
                            <option value="bigha">Bigha</option>
                            <option value="aana">Aana</option>
                        </optgroup>
                    </select>
                </div>

                <button type="submit" class="btn-action">
                    Run Computation
                </button>
            </form>

            <div class="footer-hint mt-auto pt-4 border-top border-secondary opacity-25" style="border-top: 1px solid var(--border-subtle) !important;">
                <p class="small text-muted mb-0" style="font-size: 0.7rem; line-height: 1.4;">
                    Precision Pro v2.3<br>
                    Nepali Land Measurement Standards<br>
                    Engineering Toolkit
                </p>
            </div>
        </aside>

        <main class="main-console">
            <div id="initial-view" class="empty-state">
                <i class="fas fa-microchip"></i>
                <h3>Ready for Analysis</h3>
                <p>Enter a value and select units to begin the multi-pane computation.</p>
            </div>

            <div id="analysis-view" class="d-none">
                <div id="hero-output" class="hero-result" title="Click to copy value">
                    <span class="label">Primary Computation</span>
                    <div>
                        <span id="result-val" class="value text-white">0.00</span>
                        <span id="result-unit" class="unit">Units</span>
                    </div>
                </div>

                <div id="hilly-section" class="matrix-section">
                    <div class="matrix-header">Hilly Region (Ropani System)</div>
                    <div id="hilly-grid" class="unit-grid"></div>
                </div>

                <div id="terai-section" class="matrix-section">
                    <div class="matrix-header">Terai Region (Bigha System)</div>
                    <div id="terai-grid" class="unit-grid"></div>
                </div>

                <div id="metric-section" class="matrix-section">
                    <div class="matrix-header">Metric Standards</div>
                    <div id="metric-grid" class="unit-grid"></div>
                </div>

                <div class="info-strip">
                    <div class="info-card">
                        <h4>1 Ropani</h4>
                        <p>equals to 74 feet x 74 feet</p>
                        <p>or 16 Aana</p>
                    </div>
                    <div class="info-card">
                        <h4>1 Bigha</h4>
                        <p>equals to 13.31 Ropani</p>
                        <p>or 20 Kattha</p>
                    </div>
                    <div class="info-card">
                        <h4>1 Kattha</h4>
                        <p>equals to 442 sq. yards</p>
                        <p>or 338.63 sq. meters</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Copy Notification -->
    <div id="copy-toast" class="copy-toast">
        <i class="fas fa-check-circle"></i>
        <span>COPIED TO CLIPBOARD</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pro-calc-form');
    const initialView = document.getElementById('initial-view');
    const analysisView = document.getElementById('analysis-view');
    const heroOutput = document.getElementById('hero-output');
    const resultVal = document.getElementById('result-val');
    const resultUnit = document.getElementById('result-unit');
    const hillyGrid = document.getElementById('hilly-grid');
    const teraiGrid = document.getElementById('terai-grid');
    const metricGrid = document.getElementById('metric-grid');
    const copyToast = document.getElementById('copy-toast');

    // Clipboard Logic
    function copyToClipboard(text) {
        const cleanText = text.replace(/,/g, '');
        navigator.clipboard.writeText(cleanText).then(() => {
            showToast();
        });
    }

    function showToast() {
        copyToast.classList.add('active');
        setTimeout(() => {
            copyToast.classList.remove('active');
        }, 2000);
    }

    heroOutput.addEventListener('click', () => {
        copyToClipboard(resultVal.innerText);
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const value = parseFloat(document.getElementById('value').value);
        const fromUnit = document.getElementById('from_unit').value;
        const toUnit = document.getElementById('to_unit').value;

        if (isNaN(value)) return;

        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
        submitBtn.disabled = true;

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
                
                heroOutput.classList.remove('active');
                setTimeout(() => {
                    resultVal.innerText = formatNumber(outVal);
                    resultUnit.innerText = outUnit;
                    heroOutput.classList.add('active');
                }, 50);

                // Reset Grids
                hillyGrid.innerHTML = '';
                teraiGrid.innerHTML = '';
                metricGrid.innerHTML = '';
                
                if (data.conversions.traditional) {
                    Object.entries(data.conversions.traditional).forEach(([key, info]) => {
                        const system = info.system || 'hilly';
                        const grid = system === 'hilly' ? hillyGrid : teraiGrid;
                        addMatrixNode(grid, info.output_unit_name, info.output_value, key === fromUnit);
                    });
                }
                
                if (data.conversions.metric) {
                    // Inject basic metric units manually for multi-pane view
                    addMatrixNode(metricGrid, 'Square Feet', data.conversions.metric.sq_feet_value, false);
                    addMatrixNode(metricGrid, 'Square Meters', data.conversions.metric.sq_feet_value * 0.092903, false);
                }

            } else {
                alert('Analysis failed: ' + (data.error || 'Server processing error'));
            }
        } catch (error) {
            console.error('Computation error:', error);
            alert('A critical system error occurred during computation.');
        } finally {
            submitBtn.innerHTML = 'Run Computation';
            submitBtn.disabled = false;
        }
    });

    function addMatrixNode(container, name, val, isSource) {
        const node = document.createElement('div');
        node.className = `unit-node ${isSource ? 'highlight' : ''}`;
        node.setAttribute('title', 'Click to copy value');
        node.innerHTML = `
            <span class="sys">${name}</span>
            <span class="val">${formatNumber(val)}</span>
        `;
        
        node.addEventListener('click', () => {
            copyToClipboard(val.toString());
        });
        
        container.appendChild(node);
    }

    function formatNumber(num) {
        const n = parseFloat(num);
        if (n === 0) return '0.00';
        if (n < 0.0001) return n.toExponential(4);
        return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 6 });
    }

    if (document.getElementById('value').value) {
        form.dispatchEvent(new Event('submit'));
    }
});
</script>
