<?php
// Start session if needed
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grille & Diffuser Sizing - HVAC Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/theme.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .calculator-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .calculator-form {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .calculator-form h5 {
            color: #fff;
            margin-bottom: 1rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }
        
        .result-card {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: none;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .result-card h5 {
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }
        
        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            font-weight: 500;
        }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.5);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 0.5rem;
        }
        
        .breadcrumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }
        
        .breadcrumb a {
            color: #fff;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .velocity-indicator {
            padding: 0.5rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        
        .velocity-good {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #d4edda;
        }
        
        .velocity-warning {
            background: rgba(255, 193, 7, 0.2);
            border: 1px solid #ffc107;
            color: #fff3cd;
        }
        
        .velocity-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #f8d7da;
        }
        
        .calculation-history {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        
        .history-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            border-left: 3px solid #007bff;
        }
        
        .history-item:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="calculator-container">
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-wind me-2"></i>Grille & Diffuser Sizing</h1>
            <p class="text-white-50">Calculate optimal grille and diffuser sizes based on air flow requirements</p>
        </div>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../index.php">HVAC Module</a></li>
                <li class="breadcrumb-item"><a href="index.php">Duct Sizing</a></li>
                <li class="breadcrumb-item active">Grille & Diffuser Sizing</li>
            </ol>
        </nav>
        
        <div class="glass-card">
            <!-- Calculator Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="calculator-form">
                        <h5><i class="fas fa-calculator me-2"></i>Grille Sizing Calculation</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-wind me-1"></i>Air Flow
                                    </span>
                                    <input type="number" class="form-control" id="grilleAirFlow" 
                                           placeholder="Enter CFM" step="1" min="0">
                                    <span class="input-group-text">CFM</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-tachometer-alt me-1"></i>Face Velocity
                                    </span>
                                    <input type="number" class="form-control" id="faceVelocity" 
                                           placeholder="FPM" step="10" value="500" min="0">
                                    <span class="input-group-text">FPM</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-building me-1"></i>Grille Type
                                    </span>
                                    <select class="form-control" id="grilleType">
                                        <option value="supply">Supply Grille</option>
                                        <option value="return">Return Grille</option>
                                        <option value="exhaust">Exhaust Grille</option>
                                        <option value="linear">Linear Grille</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-percentage me-1"></i>Free Area
                                    </span>
                                    <input type="number" class="form-control" id="freeAreaPercent" 
                                           placeholder="%" step="5" value="70" min="40" max="90">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button class="btn btn-primary" onclick="calculateGrilleSize()">
                                <i class="fas fa-calculator me-2"></i>Calculate Grille Size
                            </button>
                        </div>
                    </div>
                    
                    <!-- Results Section -->
                    <div class="result-card" id="grilleSizeResult">
                        <h5><i class="fas fa-chart-bar me-2"></i>Calculation Results</h5>
                        <div id="grilleSizeOutput"></div>
                        <div id="velocityIndicator" class="velocity-indicator"></div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="calculator-form">
                        <h5><i class="fas fa-info-circle me-2"></i>Quick Reference</h5>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Recommended Face Velocities</h6>
                            <small class="text-white-50">
                                <strong>Supply Grilles:</strong> 300-800 FPM<br>
                                <strong>Return Grilles:</strong> 400-600 FPM<br>
                                <strong>Exhaust Grilles:</strong> 500-1000 FPM<br>
                                <strong>Linear Grilles:</strong> 200-600 FPM
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Free Area Percentages</h6>
                            <small class="text-white-50">
                                <strong>Standard Grilles:</strong> 60-75%<br>
                                <strong>Custom Grilles:</strong> 50-80%<br>
                                <strong>Egg Crate:</strong> 70-85%<br>
                                <strong>Linear Slats:</strong> 80-90%
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Common Grille Sizes</h6>
                            <small class="text-white-50">
                                4"×4", 4"×6", 4"×8", 4"×10", 4"×12"<br>
                                6"×6", 6"×8", 6"×10", 6"×12", 6"×14"<br>
                                8"×8", 8"×10", 8"×12", 8"×14", 8"×16"<br>
                                10"×10", 10"×12", 10"×14", 10"×16"<br>
                                12"×12", 12"×14", 12"×16", 12"×18"
                            </small>
                        </div>
                    </div>
                    
                    <!-- Calculation History -->
                    <div class="calculation-history">
                        <h6 class="text-white mb-3">
                            <i class="fas fa-history me-2"></i>Recent Calculations
                        </h6>
                        <div id="calculationHistory">
                            <small class="text-white-50">No calculations yet</small>
                        </div>
                        <button class="btn btn-outline-light btn-sm mt-2" onclick="clearHistory()">
                            <i class="fas fa-trash me-1"></i>Clear History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateGrilleSize() {
            const airFlow = parseFloat(document.getElementById('grilleAirFlow').value);
            const faceVelocity = parseFloat(document.getElementById('faceVelocity').value);
            const grilleType = document.getElementById('grilleType').value;
            const freeAreaPercent = parseFloat(document.getElementById('freeAreaPercent').value);
            
            if (!airFlow || !faceVelocity) {
                alert('Please enter air flow and face velocity');
                return;
            }
            
            // Check face velocity based on grille type
            let velocityRange = { min: 300, max: 800 };
            if (grilleType === 'return') velocityRange = { min: 400, max: 600 };
            else if (grilleType === 'exhaust') velocityRange = { min: 500, max: 1000 };
            else if (grilleType === 'linear') velocityRange = { min: 200, max: 600 };
            
            // Calculate required area: A = Q / V
            const area = airFlow / faceVelocity; // ft²
            const areaIn2 = area * 144; // in²
            const actualRequiredArea = areaIn2 / (freeAreaPercent / 100); // Account for free area
            
            // Common grille sizes
            const commonSizes = [
                [4, 4], [4, 6], [4, 8], [4, 10], [4, 12],
                [6, 6], [6, 8], [6, 10], [6, 12], [6, 14],
                [8, 8], [8, 10], [8, 12], [8, 14], [8, 16],
                [10, 10], [10, 12], [10, 14], [10, 16], [10, 18],
                [12, 12], [12, 14], [12, 16], [12, 18], [12, 20],
                [14, 14], [14, 16], [14, 18], [14, 20], [14, 24],
                [16, 16], [16, 18], [16, 20], [16, 24], [16, 30]
            ];
            
            // Find closest standard size
            let recommendedSize = [12, 12];
            let minDiff = Infinity;
            
            commonSizes.forEach(size => {
                const sizeArea = size[0] * size[1];
                const diff = Math.abs(sizeArea - actualRequiredArea);
                if (diff < minDiff) {
                    minDiff = diff;
                    recommendedSize = size;
                }
            });
            
            const actualVelocity = airFlow / ((recommendedSize[0] * recommendedSize[1]) / 144);
            const actualFreeArea = actualRequiredArea / (recommendedSize[0] * recommendedSize[1]) * 100;
            
            // Determine velocity status
            const velocityStatus = getVelocityStatus(actualVelocity, grilleType);
            const velocityClass = actualVelocity < velocityRange.min || actualVelocity > velocityRange.max ? 'velocity-warning' : 'velocity-good';
            
            const resultHTML = `
                <p><strong>Air Flow:</strong> ${airFlow.toLocaleString()} CFM</p>
                <p><strong>Target Face Velocity:</strong> ${faceVelocity} FPM</p>
                <p><strong>Grille Type:</strong> ${grilleType.charAt(0).toUpperCase() + grilleType.slice(1)} Grille</p>
                <p><strong>Free Area Percentage:</strong> ${freeAreaPercent}%</p>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <p><strong>Required Net Area:</strong> ${area.toFixed(2)} ft² (${areaIn2.toFixed(0)} in²)</p>
                <p><strong>Adjusted for Free Area:</strong> ${actualRequiredArea.toFixed(0)} in²</p>
                <p><strong>Recommended Grille:</strong> ${recommendedSize[0]}" × ${recommendedSize[1]}"</p>
                <p><strong>Actual Face Velocity:</strong> ${actualVelocity.toFixed(0)} FPM</p>
                <p><strong>Actual Free Area:</strong> ${actualFreeArea.toFixed(1)}%</p>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <p><strong>Net Free Area:</strong> ${(recommendedSize[0] * recommendedSize[1] * freeAreaPercent / 100).toFixed(0)} in²</p>
            `;
            
            document.getElementById('grilleSizeOutput').innerHTML = resultHTML;
            document.getElementById('velocityIndicator').innerHTML = `
                <strong>Velocity Assessment:</strong> ${velocityStatus}<br>
                <strong>Recommended Range:</strong> ${velocityRange.min}-${velocityRange.max} FPM for ${grilleType} grilles
            `;
            document.getElementById('velocityIndicator').className = `velocity-indicator ${velocityClass}`;
            document.getElementById('grilleSizeResult').style.display = 'block';
            
            // Save to history
            saveCalculation('Grille Sizing', {
                airFlow: airFlow,
                faceVelocity: faceVelocity,
                grilleType: grilleType,
                recommendedSize: recommendedSize,
                actualVelocity: actualVelocity
            });
        }
        
        function getVelocityStatus(velocity, grilleType) {
            if (grilleType === 'supply') {
                if (velocity < 300) return 'Too low - may cause poor air distribution';
                if (velocity > 800) return 'Too high - may cause noise and discomfort';
                return 'Excellent - within optimal range';
            } else if (grilleType === 'return') {
                if (velocity < 400) return 'Low return velocity';
                if (velocity > 600) return 'High return velocity - noise possible';
                return 'Good return velocity';
            } else if (grilleType === 'exhaust') {
                if (velocity < 500) return 'Low exhaust velocity';
                if (velocity > 1000) return 'Very high - excessive noise likely';
                return 'Good exhaust velocity';
            } else {
                if (velocity < 200) return 'Low velocity for linear grille';
                if (velocity > 600) return 'High velocity for linear grille';
                return 'Good linear grille velocity';
            }
        }
        
        function saveCalculation(type, data) {
            let history = JSON.parse(localStorage.getItem('hvacGrilleSizingHistory') || '[]');
            history.unshift({
                type: type,
                data: data,
                timestamp: new Date().toLocaleString()
            });
            
            // Keep only last 10 calculations
            history = history.slice(0, 10);
            localStorage.setItem('hvacGrilleSizingHistory', JSON.stringify(history));
            loadCalculationHistory();
        }
        
        function loadCalculationHistory() {
            const history = JSON.parse(localStorage.getItem('hvacGrilleSizingHistory') || '[]');
            const container = document.getElementById('calculationHistory');
            
            if (history.length === 0) {
                container.innerHTML = '<small class="text-white-50">No calculations yet</small>';
                return;
            }
            
            container.innerHTML = history.map(calc => `
                <div class="history-item">
                    <div class="small">
                        <strong>${calc.data.airFlow} CFM</strong> → 
                        ${calc.data.recommendedSize[0]}×${calc.data.recommendedSize[1]}"
                    </div>
                    <div class="small text-white-50">
                        ${calc.data.faceVelocity} FPM • ${calc.data.grilleType}
                    </div>
                    <div class="small text-white-50">
                        ${calc.timestamp}
                    </div>
                </div>
            `).join('');
        }
        
        function clearHistory() {
            if (confirm('Clear all calculation history?')) {
                localStorage.removeItem('hvacGrilleSizingHistory');
                loadCalculationHistory();
            }
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadCalculationHistory();
        });
    </script>
</body>
</html>
