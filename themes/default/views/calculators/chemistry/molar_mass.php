<?php $page_title = $title ?? 'Molar Mass Calculator'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/theme.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/calculator-platform.css'); ?>?v=<?php echo time(); ?>">
    <style>
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 800px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #1cb5e0 0%, #000851 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #1cb5e0 0%, #000851 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #e3f2fd; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; border: 1px solid #bbdefb; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #0277bd; margin: 10px 0; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: var(--text-primary); text-decoration: none; font-weight: 600; }
        .element-pill { display: inline-block; padding: 5px 10px; border-radius: 20px; background: #e0e0e0; margin: 2px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include __DIR__ . '/../../partials/calculator_sidebar.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid">
                <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Dashboard</a>
                <div class="calc-card">
                    <div class="calc-header">
                        <i class="bi bi-radioactive" style="font-size: 2.5rem;"></i>
                        <h2>Molar Mass Calculator</h2>
                        <p class="mb-0 mt-2">Calculate Molar Mass of Compounds</p>
                    </div>
                    <div class="calc-body">
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Chemical Formula</label>
                                <div class="input-group">
                                    <input type="text" id="formula" class="form-control calc-input" placeholder="e.g. H2O, C6H12O6, NaCl" value="H2SO4">
                                    <button class="btn btn-outline-secondary" type="button" onclick="clearInput()">Clear</button>
                                </div>
                                <div class="text-muted small mt-2">
                                    Type a formula (case sensitive). Examples: 
                                    <span class="text-primary fw-bold" style="cursor:pointer" onclick="setFormula('H2O')">H2O</span>, 
                                    <span class="text-primary fw-bold" style="cursor:pointer" onclick="setFormula('CO2')">CO2</span>, 
                                    <span class="text-primary fw-bold" style="cursor:pointer" onclick="setFormula('NaCl')">NaCl</span>
                                </div>
                            </div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Mass</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small">Molar Mass</div>
                            <div class="result-val" id="resultValue">0</div>
                            <div class="fw-bold fs-6 text-muted">g/mol</div>
                            
                            <div class="mt-3 text-start">
                                <h6 class="small fw-bold text-muted border-bottom pb-2">Composition</h6>
                                <div id="compositionList"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        // Simplified Periodic Table (Common Elements)
        const atomicWeights = {
            'H': 1.008, 'He': 4.003, 'Li': 6.941, 'Be': 9.012, 'B': 10.811, 'C': 12.011, 'N': 14.007, 'O': 15.999,
            'F': 18.998, 'Ne': 20.180, 'Na': 22.990, 'Mg': 24.305, 'Al': 26.982, 'Si': 28.086, 'P': 30.974, 'S': 32.065,
            'Cl': 35.453, 'K': 39.098, 'Ca': 40.078, 'Fe': 55.845, 'Cu': 63.546, 'Zn': 65.38, 'Ag': 107.87, 'Au': 196.97,
            'Hg': 200.59, 'Pb': 207.2, 'U': 238.03, 'Br': 79.904, 'I': 126.90
            // Add more as needed or integrate full JSON
        };

        function setFormula(f) {
            document.getElementById('formula').value = f;
            calculate();
        }
        
        function clearInput() {
            document.getElementById('formula').value = '';
            document.getElementById('resultBox').style.display = 'none';
        }

        function calculate() {
            const formula = document.getElementById('formula').value.trim();
            if (!formula) return;

            // Simple parser regex: (Element)(Count)?
            // This is a basic parser and won't handle parentheses groups like Ca(OH)2 well without recursion
            // enhancing to handle basic groups if possible or stuck to simple linear formulas for MVP
            
            // First pass: Expand parentheses? (Simple replacement approach for MVP or basic regex)
            // Let's implement a robust regex loop
            
            let mass = 0;
            let breakdown = {};
            let valid = true;
            
            // Regex for Element + Count (e.g., H2, O, Na)
            const regex = /([A-Z][a-z]?)(\d*)/g;
            
            // Check for unsupported characters (parentheses support requires complex parsing logic, skipping for MVP unless required)
            // If user enters Ca(OH)2, this simple regex works if we preprocess or just parse groups.
            // Let's assume strict simple formulas for MVP.
            
            let match;
            // Clean up previous run
            while ((match = regex.exec(formula)) !== null) {
                const element = match[1];
                const count = match[2] ? parseInt(match[2]) : 1;
                
                if (atomicWeights[element]) {
                    const w = atomicWeights[element] * count;
                    mass += w;
                    breakdown[element] = (breakdown[element] || 0) + count;
                } else {
                    valid = false; // Element not found
                }
            }
            
            // Check length to ensure we consumed the whole string (simple validation)
            const consumed = formula.match(regex)?.join('');
            if (consumed !== formula && formula.includes('(')) {
               alert("Complex groups with parentheses () are not supported in this basic version. Please try linear formulas (e.g. C6H12O6).");
               return;
            }

            if (!valid || mass === 0) {
                 alert("Invalid formula or unknown element. Case matters (e.g. CO vs Co).");
                 return;
            }

            document.getElementById('resultValue').textContent = mass.toFixed(3);
            
            // Show composition
            const compList = document.getElementById('compositionList');
            compList.innerHTML = '';
            
            for (const [el, count] of Object.entries(breakdown)) {
                 const elMass = atomicWeights[el] * count;
                 const pct = ((elMass / mass) * 100).toFixed(1);
                 
                 const div = document.createElement('div');
                 div.className = 'd-flex justify-content-between mb-1 small';
                 div.innerHTML = `<span><b>${el}</b> × ${count}</span> <span class="text-muted">${elMass.toFixed(2)} (${pct}%)</span>`;
                 compList.appendChild(div);
            }
            
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
