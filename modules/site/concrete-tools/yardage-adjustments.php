<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yardage Adjustments Calculator - Site Tools</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
            --light-bg: rgba(255, 255, 255, 0.95);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --border-color: rgba(255, 255, 255, 0.2);
        }

        body {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: var(--light-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        .header h1 {
            color: var(--secondary-color);
            margin: 0 0 10px 0;
            font-size: 2.2rem;
            font-weight: 600;
        }

        .breadcrumb {
            color: #666;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .card {
            background: var(--light-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 25px;
            border-bottom: none;
        }

        .card-title {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 600;
        }

        .card-text {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }

        .card-body {
            padding: 25px;
        }

        .form-label {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn {
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 20px;
        }

        .alert-info {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: #2d3436;
        }

        .border-primary {
            border: 2px solid var(--primary-color) !important;
        }

        .border-warning {
            border: 2px solid var(--warning-color) !important;
        }

        .border-success {
            border: 2px solid var(--success-color) !important;
        }

        .small {
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .card-body {
                padding: 20px;
            }
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container-fluid">
        <div class="header">
            <h1><i class="fas fa-tools me-3"></i>Site Tools</h1>
            <div class="breadcrumb">
                <a href="../../index.php">Home</a> / 
                <a href="../../modules/site/index.php">Site Tools</a> / 
                <span>Yardage Adjustments</span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-adjust"></i>
                    Yardage Adjustments Calculator
                </h2>
                <p class="card-text">Calculate concrete yardage adjustments for forms, reinforcement, and waste factors</p>
            </div>
            <div class="card-body">
                <form id="yardageForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="structuralElement" class="form-label">Structural Element</label>
                                <select class="form-control" id="structuralElement" required>
                                    <option value="">Select Element</option>
                                    <option value="footings">Footings</option>
                                    <option value="grade-beams">Grade Beams</option>
                                    <option value="columns">Columns</option>
                                    <option value="beams">Beams</option>
                                    <option value="slabs">Slabs</option>
                                    <option value="walls">Walls</option>
                                    <option value="piers">Piers</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dimensions" class="form-label">Dimensions (L×W×H)</label>
                                <input type="text" class="form-control" id="dimensions" placeholder="20×3×2" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" step="1" value="1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rebarPercent" class="form-label">Rebar Displacement (%)</label>
                                <input type="number" class="form-control" id="rebarPercent" step="0.1" value="1.5" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pourLoss" class="form-label">Pour Loss Factor (%)</label>
                                <input type="number" class="form-control" id="pourLoss" step="0.1" value="5" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="settlement" class="form-label">Settlement Factor (%)</label>
                                <input type="number" class="form-control" id="settlement" step="0.1" value="2" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="formworkThickness" class="form-label">Formwork Thickness (in)</label>
                                <input type="number" class="form-control" id="formworkThickness" step="0.5" placeholder="1.5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="underbreak" class="form-label">Underbreak Allowance (in)</label>
                                <input type="number" class="form-control" id="underbreak" step="0.5" value="2">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-calculator"></i> Calculate Yardage
                    </button>
                </form>

                <div id="results" class="mt-4" style="display: none;">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-chart-line"></i> Yardage Adjustment Results</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Theoretical Volume:</strong> <span id="theoreticalVolume">0</span> CY</p>
                                <p><strong>Rebar Displacement:</strong> <span id="rebarDisplacement">0</span> CY</p>
                                <p><strong>Pour Loss:</strong> <span id="pourLossVolume">0</span> CY</p>
                                <p><strong>Settlement Loss:</strong> <span id="settlementLoss">0</span> CY</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Formwork Adjustment:</strong> <span id="formworkAdj">0</span> CY</p>
                                <p><strong>Total Adjusted Volume:</strong> <span id="totalVolume">0</span> CY</p>
                                <p><strong>Adjustment Percentage:</strong> <span id="adjustmentPercent">0</span>%</p>
                                <p><strong>Total Project Impact:</strong> <span id="projectImpact">0</span> CY</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Yardage Adjustment Guidelines</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Typical Adjustment Factors</h6>
                        <ul class="list-unstyled">
                            <li>• Rebar displacement: 1.0-2.5%</li>
                            <li>• Pour loss (splatter/overspray): 3-8%</li>
                            <li>• Settlement during placement: 1-3%</li>
                            <li>• Formwork deflection: 0.5-2%</li>
                            <li>• Excavation underbreak: 2-6 inches</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Element-Specific Considerations</h6>
                        <ul class="list-unstyled">
                            <li>• Footings: High underbreak allowance</li>
                            <li>• Columns: Minimal settlement loss</li>
                            <li>• Beams: Formwork deflection critical</li>
                            <li>• Slabs: Pour loss from pumping</li>
                            <li>• Walls: Reinforcement displacement</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-tools"></i> Adjustment Calculations</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6><i class="fas fa-balance-scale"></i> Rebar Displacement</h6>
                                <ul class="list-unstyled small">
                                    <li>• #4 bars: 0.5-1.0%</li>
                                    <li>• #5-#7 bars: 1.0-1.5%</li>
                                    <li>• #8-#11 bars: 1.5-2.5%</li>
                                    <li>• Dense reinforcement: 2.5%+</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h6><i class="fas fa-tint"></i> Pour Loss Factors</h6>
                                <ul class="list-unstyled small">
                                    <li>• Hand placement: 3-5%</li>
                                    <li>• Chute placement: 4-6%</li>
                                    <li>• Pump placement: 5-8%</li>
                                    <li>• Tremie placement: 6-10%</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6><i class="fas fa-arrow-down"></i> Settlement Loss</h6>
                                <ul class="list-unstyled small">
                                    <li>• High slump concrete: 2-3%</li>
                                    <li>• Normal slump: 1-2%</li>
                                    <li>• Low slump: 0.5-1%</li>
                                    <li>• Vibrated concrete: 0.5%</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-exclamation-triangle"></i> Critical Adjustment Notes</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-clipboard-list"></i> Project-Specific Factors</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>High-Risk Conditions:</strong>
                            <ul>
                                <li>• Irregular forms or complex geometry</li>
                                <li>• Heavy reinforcement congestion</li>
                                <li>• Extreme weather conditions</li>
                                <li>• Inexperienced placement crews</li>
                                <li>• Long pumping distances</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Documentation Required:</strong>
                            <ul>
                                <li>• As-built dimension verification</li>
                                <li>• Reinforcement shop drawing review</li>
                                <li>• Formwork deflection calculations</li>
                                <li>• Placement method specifications</li>
                                <li>• Historical project data</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function saveToLocalStorage(key, data) {
            try {
                localStorage.setItem(key, JSON.stringify(data));
            } catch (e) {
                console.log('Error saving to localStorage:', e);
            }
        }

        function loadFromLocalStorage(key) {
            try {
                const data = localStorage.getItem(key);
                return data ? JSON.parse(data) : null;
            } catch (e) {
                console.log('Error loading from localStorage:', e);
                return null;
            }
        }

        document.getElementById('yardageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const structuralElement = document.getElementById('structuralElement').value;
            const dimensions = document.getElementById('dimensions').value;
            const quantity = parseFloat(document.getElementById('quantity').value);
            const rebarPercent = parseFloat(document.getElementById('rebarPercent').value) / 100;
            const pourLoss = parseFloat(document.getElementById('pourLoss').value) / 100;
            const settlement = parseFloat(document.getElementById('settlement').value) / 100;
            const formworkThickness = parseFloat(document.getElementById('formworkThickness').value) || 0;
            const underbreak = parseFloat(document.getElementById('underbreak').value) || 0;
            
            // Parse dimensions (L×W×H)
            const dimParts = dimensions.split('×');
            const length = parseFloat(dimParts[0]);
            const width = parseFloat(dimParts[1]);
            const height = parseFloat(dimParts[2]);
            
            // Calculate theoretical volume
            const theoreticalVolume = (length * width * height * quantity) / 27; // Convert to CY
            
            // Calculate adjustments
            const rebarDisplacement = theoreticalVolume * rebarPercent;
            const pourLossVolume = theoreticalVolume * pourLoss;
            const settlementLoss = theoreticalVolume * settlement;
            
            // Formwork adjustments based on element type
            let formworkAdj = 0;
            if (structuralElement === 'footings' && underbreak > 0) {
                // Footings: Add volume for underbreak around perimeter
                const perimeter = 2 * (length + width);
                const underbreakVolume = (perimeter * underbreak * height * quantity) / 144; // Convert in to ft
                formworkAdj = underbreakVolume / 27; // Convert to CY
            } else if (formworkThickness > 0) {
                // Add formwork deflection allowance
                const surfaceArea = 2 * (length * width + length * height + width * height) * quantity;
                const deflectionVolume = surfaceArea * (formworkThickness / 12) * 0.01; // 1% of surface area
                formworkAdj = deflectionVolume / 27;
            }
            
            // Calculate total adjusted volume
            const totalAdjustments = rebarDisplacement + pourLossVolume + settlementLoss + formworkAdj;
            const totalVolume = theoreticalVolume + totalAdjustments;
            const adjustmentPercent = (totalAdjustments / theoreticalVolume) * 100;
            const projectImpact = totalVolume;
            
            // Display results
            document.getElementById('theoreticalVolume').textContent = theoreticalVolume.toFixed(2);
            document.getElementById('rebarDisplacement').textContent = rebarDisplacement.toFixed(2);
            document.getElementById('pourLossVolume').textContent = pourLossVolume.toFixed(2);
            document.getElementById('settlementLoss').textContent = settlementLoss.toFixed(2);
            document.getElementById('formworkAdj').textContent = formworkAdj.toFixed(2);
            document.getElementById('totalVolume').textContent = totalVolume.toFixed(2);
            document.getElementById('adjustmentPercent').textContent = adjustmentPercent.toFixed(1);
            document.getElementById('projectImpact').textContent = projectImpact.toFixed(2);
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            const calculation = {
                type: 'Yardage Adjustments',
                timestamp: new Date().toISOString(),
                inputs: {
                    structuralElement, dimensions, quantity, rebarPercent, 
                    pourLoss, settlement, formworkThickness, underbreak
                },
                results: {
                    theoreticalVolume, rebarDisplacement, pourLossVolume, 
                    settlementLoss, formworkAdj, totalVolume, adjustmentPercent, projectImpact
                }
            };
            
            saveCalculation(calculation);
        });

        function saveCalculation(calculation) {
            const history = JSON.parse(localStorage.getItem('calculationHistory') || '[]');
            const entry = {
                type: calculation.type,
                timestamp: calculation.timestamp,
                data: calculation
            };
            history.unshift(entry);
            if (history.length > 10) history.pop();
            localStorage.setItem('calculationHistory', JSON.stringify(history));
        }

        // Load saved calculations
        function loadCalculations(type) {
            const history = JSON.parse(localStorage.getItem('calculationHistory') || '[]');
            const calculations = history.filter(entry => entry.data.type === type);
            console.log('Loaded ' + calculations.length + ' calculations for ' + type);
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
