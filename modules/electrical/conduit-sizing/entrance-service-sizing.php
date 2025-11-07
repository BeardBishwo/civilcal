<?php
// modules/electrical/service/entrance-service-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Entrance Sizing Calculator - AEC Toolkit</title>
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
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
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

        .service-note {
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
            <h1><i class="fas fa-plug me-2"></i>Service Entrance Sizing</h1>
            <form id="service-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="dwelling-type"><i class="fas fa-home me-2"></i>Dwelling Type</label>
                            <select id="dwelling-type" class="form-control" required>
                                <option value="single-family">Single Family</option>
                                <option value="multi-family">Multi-Family</option>
                                <option value="commercial">Commercial</option>
                                <option value="industrial">Industrial</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="living-area"><i class="fas fa-ruler-combined me-2"></i>Living Area (sq ft)</label>
                            <input type="number" id="living-area" class="form-control" step="1" min="100" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="appliance-count"><i class="fas fa-blender me-2"></i>Major Appliances</label>
                            <input type="number" id="appliance-count" class="form-control" step="1" min="0" value="3" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="cooking-equipment"><i class="fas fa-fire me-2"></i>Cooking Equipment kW</label>
                            <input type="number" id="cooking-equipment" class="form-control" step="0.1" min="0" value="12" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="hvac-load"><i class="fas fa-snowflake me-2"></i>HVAC Load (kW)</label>
                            <input type="number" id="hvac-load" class="form-control" step="0.1" min="0" value="5" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="additional-load"><i class="fas fa-plus me-2"></i>Additional Load (kW)</label>
                            <input type="number" id="additional-load" class="form-control" step="0.1" min="0" value="0" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="service-voltage"><i class="fas fa-plug me-2"></i>Service Voltage</label>
                            <select id="service-voltage" class="form-control" required>
                                <option value="120/240">120/240V Single Phase</option>
                                <option value="208/120">208/120V Three Phase</option>
                                <option value="480/277">480/277V Three Phase</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="service-capacity"><i class="fas fa-tachometer-alt me-2"></i>Service Capacity (A)</label>
                            <input type="number" id="service-capacity" class="form-control" step="1" min="30" value="200" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Service Size</button>
            </form>
            
            <div class="service-note">
                <i class="fas fa-info-circle me-2"></i>
                Service entrance sizing per NEC Article 220. Includes demand factors for dwelling units and proper ampacity calculations.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Service Entrance Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateServiceEntrance(dwellingType, livingArea, appliances, cookingKW, hvacKW, additionalKW, serviceVoltage, serviceCapacity) {
            // Calculate general lighting load (3 VA/sq ft)
            const generalLoad = livingArea * 3; // VA
            
            // Calculate minimum service load
            let calculatedLoad = generalLoad;
            
            // Apply dwelling-specific calculations
            if (dwellingType === 'single-family') {
                // First 3 kW at 100%, next 117 kW at 35%
                const first3kW = Math.min(3000, calculatedLoad);
                const remainingLoad = Math.max(0, calculatedLoad - 3000);
                const next117kW = Math.min(117000, remainingLoad);
                const beyond117kW = Math.max(0, remainingLoad - 117000);
                
                calculatedLoad = first3kW + (next117kW * 0.35) + (beyond117kW * 0.25);
                
                // Add appliance loads
                const applianceLoad = appliances * 1500; // 1.5 kW per appliance
                calculatedLoad += applianceLoad;
                
                // Add fixed appliance loads
                calculatedLoad += (cookingKW * 1000) + (hvacKW * 1000) + (additionalKW * 1000);
            }
            
            // Convert to amps
            let current = 0;
            if (serviceVoltage === '120/240') {
                current = calculatedLoad / 240;
            } else if (serviceVoltage === '208/120') {
                current = calculatedLoad / (1.732 * 208);
            } else if (serviceVoltage === '480/277') {
                current = calculatedLoad / (1.732 * 480);
            }
            
            // Determine recommended service size
            const standardServices = [30, 60, 100, 200, 400, 600, 800, 1000, 1200, 1600, 2000];
            const recommendedService = standardServices.find(size => size >= current) || 2000;
            
            // Calculate conductor sizes
            const copperConductor = current * 1.25; // 125% for continuous load
            const aluminumConductor = current * 1.25;
            
            return {
                generalLoad: generalLoad,
                calculatedLoad: calculatedLoad,
                current: current,
                recommendedService: recommendedService,
                copperConductor: copperConductor,
                aluminumConductor: aluminumConductor,
                dwellingType: dwellingType,
                appliances: appliances,
                serviceVoltage: serviceVoltage
            };
        }

        document.getElementById('service-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const dwellingType = document.getElementById('dwelling-type').value;
            const livingArea = parseFloat(document.getElementById('living-area').value);
            const appliances = parseInt(document.getElementById('appliance-count').value);
            const cookingKW = parseFloat(document.getElementById('cooking-equipment').value);
            const hvacKW = parseFloat(document.getElementById('hvac-load').value);
            const additionalKW = parseFloat(document.getElementById('additional-load').value);
            const serviceVoltage = document.getElementById('service-voltage').value;
            const serviceCapacity = parseFloat(document.getElementById('service-capacity').value);

            if (isNaN(livingArea) || isNaN(appliances) || isNaN(cookingKW) || 
                isNaN(hvacKW) || isNaN(additionalKW) || isNaN(serviceCapacity)) {
                alert('Please enter valid numbers.');
                return;
            }
            
            const result = calculateServiceEntrance(dwellingType, livingArea, appliances, cookingKW, hvacKW, additionalKW, serviceVoltage, serviceCapacity);
            
            const dwellingText = {
                'single-family': 'Single Family Dwelling',
                'multi-family': 'Multi-Family Dwelling',
                'commercial': 'Commercial',
                'industrial': 'Industrial'
            };
            
            const voltageText = {
                '120/240': '120/240V Single Phase',
                '208/120': '208/120V Three Phase',
                '480/277': '480/277V Three Phase'
            };
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Dwelling Type:</strong><br>${dwellingText[dwellingType]}
                </div>
                <div class="result-item">
                    <strong>Living Area:</strong><br>${livingArea.toLocaleString()} sq ft
                </div>
                <div class="result-item">
                    <strong>General Load:</strong><br>${(result.generalLoad / 1000).toFixed(1)} kVA
                </div>
                <div class="result-item">
                    <strong>Major Appliances:</strong><br>${appliances}
                </div>
                <div class="result-item">
                    <strong>Cooking Load:</strong><br>${cookingKW} kW
                </div>
                <div class="result-item">
                    <strong>HVAC Load:</strong><br>${hvacKW} kW
                </div>
                <div class="result-item">
                    <strong>Total Load:</strong><br>${(result.calculatedLoad / 1000).toFixed(1)} kVA
                </div>
                <div class="result-item">
                    <strong>Service Current:</strong><br>${result.current.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Recommended Service:</strong><br>${result.recommendedService} A
                </div>
                <div class="result-item">
                    <strong>Cu Conductor (125%):</strong><br>${result.copperConductor.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Al Conductor (125%):</strong><br>${result.aluminumConductor.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Service Voltage:</strong><br>${voltageText[serviceVoltage]}
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Service Entrance', `${dwellingType} ${livingArea}sqft → ${result.recommendedService}A service`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentServiceCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentServiceCalculations', JSON.stringify(recent));
        }
    </script>
</body>
</html>
