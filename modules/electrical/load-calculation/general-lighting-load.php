<?php
// modules/electrical/load-calculation/general-lighting-load.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Lighting Load Calculator - AEC Toolkit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #f093fb;
            --dark: #1a202c;
            --light: #f7fafc;
            --glass: rgba(255, 255, 255, 0.05);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            --yellow: #feca57;
        }

        body {
            /* Removed inline background - using theme.css instead */
            min-height: 100vh;
            color: var(--light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }

        .calculator-wrapper {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
            margin-top: 3rem;
        }

        .calculator-wrapper h1 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: var(--yellow);
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-col {
            flex: 1;
        }

        .form-group label {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--light);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #f093fb;
            box-shadow: 0 0 15px rgba(240, 147, 251, 0.3);
        }

        .btn-calculate {
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, #f093fb, #f5576c);
            border: none;
            border-radius: 50px;
            color: var(--light);
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-calculate:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .result-area {
            margin-top: 2rem;
            background: linear-gradient(45deg, #4ecdc4, #44a08d);
            padding: 2rem;
            border-radius: 10px;
            display: none;
            text-align: left;
        }

        .result-area h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }

        .result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .result-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
        }

        .result-item strong {
            color: var(--yellow);
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #f093fb;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .lighting-note {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-lightbulb me-2"></i>General Lighting Load</h1>
            <form id="lighting-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="area"><i class="fas fa-ruler-combined me-2"></i>Area (sq ft)</label>
                            <input type="number" id="area" class="form-control" step="1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="load-density"><i class="fas fa-bolt me-2"></i>Load Density (VA/sq ft)</label>
                            <select id="load-density" class="form-control" required>
                                <option value="3">General (3 VA/sq ft)</option>
                                <option value="2">Storage (2 VA/sq ft)</option>
                                <option value="0.5">Corridors (0.5 VA/sq ft)</option>
                                <option value="5">Office (5 VA/sq ft)</option>
                                <option value="10">Retail (10 VA/sq ft)</option>
                                <option value="2.5">Schools (2.5 VA/sq ft)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="demand-factor"><i class="fas fa-percentage me-2"></i>Demand Factor (%)</label>
                            <input type="number" id="demand-factor" class="form-control" value="100" step="5" min="50" max="100" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="continuous-load"><i class="fas fa-clock me-2"></i>Continuous Load</label>
                            <select id="continuous-load" class="form-control">
                                <option value="no">No</option>
                                <option value="yes">Yes (125% multiplier)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Lighting Load</button>
            </form>
            
            <div class="lighting-note">
                <i class="fas fa-info-circle me-2"></i>
                NEC 220.12 requires lighting load calculation based on volt-amperes per square foot. General use areas require 3 VA/sq ft minimum.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Lighting Load Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateLightingLoad(area, loadDensity, demandFactor, continuousLoad) {
            // Basic lighting load calculation
            const basicLoad = area * loadDensity;
            
            // Apply demand factor
            const demandLoad = basicLoad * (demandFactor / 100);
            
            // Apply continuous load factor (125%) if applicable
            const finalLoad = continuousLoad === 'yes' ? demandLoad * 1.25 : demandLoad;
            
            // Calculate required circuits (15A circuits at 80% = 12A usable, 120V)
            const circuits15A = Math.ceil(finalLoad / 1440);
            const circuits20A = Math.ceil(finalLoad / 1920);
            
            return {
                basicLoad: basicLoad,
                demandLoad: demandLoad,
                finalLoad: finalLoad,
                circuits15A: circuits15A,
                circuits20A: circuits20A,
                area: area,
                loadDensity: loadDensity,
                demandFactor: demandFactor,
                continuousLoad: continuousLoad
            };
        }

        document.getElementById('lighting-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const area = parseFloat(document.getElementById('area').value);
            const loadDensity = parseFloat(document.getElementById('load-density').value);
            const demandFactor = parseFloat(document.getElementById('demand-factor').value);
            const continuousLoad = document.getElementById('continuous-load').value;

            if (isNaN(area) || isNaN(loadDensity) || isNaN(demandFactor)) {
                alert('Please enter valid numbers.');
                return;
            }
            
            const result = calculateLightingLoad(area, loadDensity, demandFactor, continuousLoad);
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Area:</strong><br>${result.area.toLocaleString()} sq ft
                </div>
                <div class="result-item">
                    <strong>Load Density:</strong><br>${result.loadDensity} VA/sq ft
                </div>
                <div class="result-item">
                    <strong>Basic Load:</strong><br>${result.basicLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Demand Factor:</strong><br>${result.demandFactor}%
                </div>
                <div class="result-item">
                    <strong>Demand Load:</strong><br>${result.demandLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Continuous Load:</strong><br>${result.continuousLoad === 'yes' ? 'Yes (125%)' : 'No'}
                </div>
                <div class="result-item">
                    <strong>Final Load:</strong><br>${result.finalLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Required 15A Circuits:</strong><br>${result.circuits15A} circuits
                </div>
                <div class="result-item">
                    <strong>Required 20A Circuits:</strong><br>${result.circuits20A} circuits
                </div>
                <div class="result-item">
                    <strong>Current @ 120V:</strong><br>${(result.finalLoad / 120).toFixed(1)} A
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Lighting Load', `${area} sq ft → ${result.finalLoad.toLocaleString()}VA`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentLightingLoadCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentLightingLoadCalculations', JSON.stringify(recent));
        }
    </script>
</body>
</html>
