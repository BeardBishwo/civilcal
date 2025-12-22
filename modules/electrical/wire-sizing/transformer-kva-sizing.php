<?php
// modules/electrical/transformer/transformer-kva-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transformer KVA Sizing Calculator - AEC Toolkit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ffffff;
            --secondary: #ffffff;
            --accent: #ffffff;
            --dark: #000000;
            --light: #ffffff;
            --glass: rgba(255, 255, 255, 0.05);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            --yellow: #ffffff;
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
            border-color: #ffffff;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        .btn-calculate {
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, #ffffff, #ffffff);
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
            color: #ffffff;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .transformer-note {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-transformer me-2"></i>Transformer KVA Sizing</h1>
            <form id="transformer-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="total-load"><i class="fas fa-bolt me-2"></i>Total Load (kW)</label>
                            <input type="number" id="total-load" class="form-control" step="0.1" min="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="power-factor"><i class="fas fa-percentage me-2"></i>Power Factor</label>
                            <input type="number" id="power-factor" class="form-control" value="0.9" step="0.01" min="0.1" max="1.0" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="demand-factor"><i class="fas fa-calculator me-2"></i>Demand Factor</label>
                            <input type="number" id="demand-factor" class="form-control" value="0.8" step="0.01" min="0.1" max="1.0" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="growth-factor"><i class="fas fa-chart-line me-2"></i>Growth Factor</label>
                            <input type="number" id="growth-factor" class="form-control" value="1.25" step="0.05" min="1.0" max="2.0" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="primary-voltage"><i class="fas fa-plug me-2"></i>Primary Voltage</label>
                            <select id="primary-voltage" class="form-control" required>
                                <option value="480">480V</option>
                                <option value="2400">2400V</option>
                                <option value="4160">4160V</option>
                                <option value="7200">7200V</option>
                                <option value="13200">13.2kV</option>
                                <option value="24000">24kV</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="secondary-voltage"><i class="fas fa-plug me-2"></i>Secondary Voltage</label>
                            <select id="secondary-voltage" class="form-control" required>
                                <option value="120">120V</option>
                                <option value="208">208V</option>
                                <option value="240">240V</option>
                                <option value="480">480V</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate KVA Rating</button>
            </form>
            
            <div class="transformer-note">
                <i class="fas fa-info-circle me-2"></i>
                Transformer sizing includes load demand factors and future growth. Typically sized at 125% of connected load per NEC requirements.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Transformer KVA Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateTransformerKVA(loadKW, powerFactor, demandFactor, growthFactor, primaryVoltage, secondaryVoltage) {
            // Calculate apparent power (kVA)
            const kVA = loadKW / powerFactor;
            
            // Apply demand factor
            const demandKVA = kVA * demandFactor;
            
            // Apply growth factor
            const futureKVA = demandKVA * growthFactor;
            
            // Apply 125% safety factor per NEC
            const requiredKVA = futureKVA * 1.25;
            
            // Standard transformer sizes
            const standardSizes = [15, 25, 37.5, 50, 75, 100, 150, 200, 250, 300, 500, 750, 1000, 1500, 2000, 2500, 5000];
            const selectedKVA = standardSizes.find(size => size >= requiredKVA) || 5000;
            
            // Calculate currents
            const primaryCurrent = (selectedKVA * 1000) / primaryVoltage;
            const secondaryCurrent = (selectedKVA * 1000) / secondaryVoltage;
            
            return {
                kVA: kVA,
                demandKVA: demandKVA,
                futureKVA: futureKVA,
                requiredKVA: requiredKVA,
                selectedKVA: selectedKVA,
                primaryCurrent: primaryCurrent,
                secondaryCurrent: secondaryCurrent,
                loadKW: loadKW,
                powerFactor: powerFactor,
                demandFactor: demandFactor,
                growthFactor: growthFactor,
                primaryVoltage: primaryVoltage,
                secondaryVoltage: secondaryVoltage
            };
        }

        document.getElementById('transformer-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const loadKW = parseFloat(document.getElementById('total-load').value);
            const powerFactor = parseFloat(document.getElementById('power-factor').value);
            const demandFactor = parseFloat(document.getElementById('demand-factor').value);
            const growthFactor = parseFloat(document.getElementById('growth-factor').value);
            const primaryVoltage = parseFloat(document.getElementById('primary-voltage').value);
            const secondaryVoltage = parseFloat(document.getElementById('secondary-voltage').value);

            if (isNaN(loadKW) || isNaN(powerFactor) || isNaN(demandFactor) || 
                isNaN(growthFactor) || isNaN(primaryVoltage) || isNaN(secondaryVoltage)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const result = calculateTransformerKVA(loadKW, powerFactor, demandFactor, growthFactor, primaryVoltage, secondaryVoltage);
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Real Power:</strong><br>${result.loadKW} kW
                </div>
                <div class="result-item">
                    <strong>Power Factor:</strong><br>${(result.powerFactor * 100).toFixed(1)}%
                </div>
                <div class="result-item">
                    <strong>Apparent Power:</strong><br>${result.kVA.toFixed(1)} kVA
                </div>
                <div class="result-item">
                    <strong>Demand Factor:</strong><br>${(result.demandFactor * 100).toFixed(1)}%
                </div>
                <div class="result-item">
                    <strong>Demand Load:</strong><br>${result.demandKVA.toFixed(1)} kVA
                </div>
                <div class="result-item">
                    <strong>Growth Factor:</strong><br>${result.growthFactor.toFixed(2)}
                </div>
                <div class="result-item">
                    <strong>Future Load:</strong><br>${result.futureKVA.toFixed(1)} kVA
                </div>
                <div class="result-item">
                    <strong>Required (125%):</strong><br>${result.requiredKVA.toFixed(1)} kVA
                </div>
                <div class="result-item">
                    <strong>Selected Size:</strong><br>${result.selectedKVA} kVA
                </div>
                <div class="result-item">
                    <strong>Primary Current:</strong><br>${result.primaryCurrent.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Secondary Current:</strong><br>${result.secondaryCurrent.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Voltage Ratio:</strong><br>${result.primaryVoltage}:${result.secondaryVoltage}V
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Transformer KVA', `${loadKW}kW → ${result.selectedKVA}kVA transformer`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentTransformerCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentTransformerCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
