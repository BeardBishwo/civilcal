<?php
// modules/electrical/grounding/ground-conductor-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ground Conductor Sizing Calculator - AEC Toolkit</title>
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
            background: linear-gradient(135deg, #000000, #000000, #000000);
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

        .ground-note {
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
            <h1><i class="fas fa-anchor me-2"></i>Ground Conductor Sizing</h1>
            <form id="ground-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="wire-size"><i class="fas fa-wire me-2"></i>Circuit Wire Size (AWG)</label>
                            <select id="wire-size" class="form-control" required>
                                <option value="14">14 AWG</option>
                                <option value="12">12 AWG</option>
                                <option value="10">10 AWG</option>
                                <option value="8">8 AWG</option>
                                <option value="6">6 AWG</option>
                                <option value="4">4 AWG</option>
                                <option value="2">2 AWG</option>
                                <option value="1">1 AWG</option>
                                <option value="1/0">1/0 AWG</option>
                                <option value="2/0">2/0 AWG</option>
                                <option value="3/0">3/0 AWG</option>
                                <option value="4/0">4/0 AWG</option>
                                <option value="250">250 kcmil</option>
                                <option value="300">300 kcmil</option>
                                <option value="350">350 kcmil</option>
                                <option value="400">400 kcmil</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="circuit-current"><i class="fas fa-bolt me-2"></i>Circuit Current (A)</label>
                            <input type="number" id="circuit-current" class="form-control" step="1" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="protection-device"><i class="fas fa-shield-alt me-2"></i>Protection Device</label>
                            <select id="protection-device" class="form-control" required>
                                <option value="circuit-breaker">Circuit Breaker</option>
                                <option value="fuse">Fuse</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="ground-material"><i class="fas fa-cog me-2"></i>Conductor Material</label>
                            <select id="ground-material" class="form-control" required>
                                <option value="copper">Copper</option>
                                <option value="aluminum">Aluminum</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="application"><i class="fas fa-sitemap me-2"></i>Application</label>
                            <select id="application" class="form-control" required>
                                <option value="branch-circuit">Branch Circuit</option>
                                <option value="feeder">Feeder</option>
                                <option value="service">Service</option>
                                <option value="motor-circuit">Motor Circuit</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="insulation-type"><i class="fas fa-layer-group me-2"></i>Insulation Type</label>
                            <select id="insulation-type" class="form-control">
                                <option value="thhn-thwn">THHN/THWN</option>
                                <option value="xhhw">XHHW</option>
                                <option value="thw">THW</option>
                                <option value="bare">Bare</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Ground Wire</button>
            </form>
            
            <div class="ground-note">
                <i class="fas fa-info-circle me-2"></i>
                Per NEC 250.122, equipment grounding conductor sizing is based on overcurrent device rating using the percentage factors in Table 250.122.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Ground Conductor Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function getGroundWireSize(current, application, material) {
            // NEC Table 250.122 sizing factors
            let factor = 1.0; // Default for circuits ≤ 60A
            if (current > 60 && current <= 100) factor = 0.75;
            else if (current > 100 && current <= 200) factor = 0.5;
            else if (current > 200 && current <= 400) factor = 0.375;
            else if (current > 400 && current <= 800) factor = 0.3;
            else if (current > 800) factor = 0.25;

            // Calculate required ground wire size
            let requiredCurrent = current * factor;
            
            // Equipment grounding conductor sizes (NEC Table 250.122)
            const copperSizes = [
                { awg: 16, current: 15 },
                { awg: 14, current: 20 },
                { awg: 12, current: 30 },
                { awg: 10, current: 40 },
                { awg: 8, current: 55 },
                { awg: 6, current: 80 },
                { awg: 4, current: 100 },
                { awg: 3, current: 110 },
                { awg: 2, current: 130 },
                { awg: 1, current: 150 },
                { awg: 1/0, current: 175 },
                { awg: 2/0, current: 200 },
                { awg: 3/0, current: 225 },
                { awg: 4/0, current: 260 },
                { awg: 250, current: 300 },
                { awg: 300, current: 320 },
                { awg: 350, current: 350 },
                { awg: 400, current: 385 },
                { awg: 500, current: 455 },
                { awg: 600, current: 495 },
                { awg: 700, current: 525 },
                { awg: 800, current: 555 },
                { awg: 900, current: 585 },
                { awg: 1000, current: 615 }
            ];
            
            const aluminumSizes = [
                { awg: 12, current: 25 },
                { awg: 10, current: 35 },
                { awg: 8, current: 50 },
                { awg: 6, current: 65 },
                { awg: 4, current: 85 },
                { awg: 3, current: 100 },
                { awg: 2, current: 115 },
                { awg: 1, current: 130 },
                { awg: 1/0, current: 150 },
                { awg: 2/0, current: 175 },
                { awg: 3/0, current: 200 },
                { awg: 4/0, current: 225 },
                { awg: 250, current: 255 },
                { awg: 300, current: 280 },
                { awg: 350, current: 310 },
                { awg: 400, current: 335 },
                { awg: 500, current: 380 },
                { awg: 600, current: 420 },
                { awg: 700, current: 460 },
                { awg: 800, current: 500 },
                { awg: 900, current: 545 },
                { awg: 1000, current: 590 }
            ];
            
            const sizes = material === 'aluminum' ? aluminumSizes : copperSizes;
            
            let groundSize = 'No wire available';
            let factorText = (factor * 100).toFixed(0) + '%';
            
            for (let i = 0; i < sizes.length; i++) {
                if (sizes[i].current >= requiredCurrent) {
                    groundSize = sizes[i].awg + ' AWG';
                    break;
                }
            }
            
            return {
                groundSize: groundSize,
                requiredCurrent: requiredCurrent,
                factor: factor,
                factorText: factorText
            };
        }

        document.getElementById('ground-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const wireSize = document.getElementById('wire-size').value;
            const current = parseFloat(document.getElementById('circuit-current').value);
            const protection = document.getElementById('protection-device').value;
            const material = document.getElementById('ground-material').value;
            const application = document.getElementById('application').value;
            const insulation = document.getElementById('insulation-type').value;

            if (isNaN(current)) {
                showNotification('Please enter a valid current.', 'info');
                return;
            }
            
            const result = getGroundWireSize(current, application, material);
            
            const applicationText = {
                'branch-circuit': 'Branch Circuit',
                'feeder': 'Feeder',
                'service': 'Service',
                'motor-circuit': 'Motor Circuit'
            };
            
            const protectionText = protection === 'circuit-breaker' ? 'Circuit Breaker' : 'Fuse';
            const insulationText = {
                'thhn-thwn': 'THHN/THWN',
                'xhhw': 'XHHW',
                'thw': 'THW',
                'bare': 'Bare'
            };
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Circuit Wire:</strong><br>${wireSize} AWG
                </div>
                <div class="result-item">
                    <strong>Circuit Current:</strong><br>${current} A
                </div>
                <div class="result-item">
                    <strong>Application:</strong><br>${applicationText[application]}
                </div>
                <div class="result-item">
                    <strong>Protection Device:</strong><br>${protectionText}
                </div>
                <div class="result-item">
                    <strong>Material:</strong><br>${material === 'copper' ? 'Copper' : 'Aluminum'}
                </div>
                <div class="result-item">
                    <strong>Insulation:</strong><br>${insulationText[insulation]}
                </div>
                <div class="result-item">
                    <strong>Multiplier Factor:</strong><br>${result.factorText}
                </div>
                <div class="result-item">
                    <strong>Required Current:</strong><br>${result.requiredCurrent.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Equipment Ground:</strong><br>${result.groundSize}
                </div>
                <div class="result-item">
                    <strong>Formula Used:</strong><br>Min Current = OCPD × ${result.factorText}
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Ground Conductor', `${wireSize}AWG circuit → ${result.groundSize} equipment ground`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentGroundConductorCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentGroundConductorCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
