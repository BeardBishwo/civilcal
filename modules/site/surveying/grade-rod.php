<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Rod Calculator - Site Tools</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --glass-bg: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            line-height: 1.6;
        }

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .py-4 {
            padding: 2rem 0;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .me-3 {
            margin-right: 1rem;
        }

        .me-2 {
            margin-right: 0.5rem;
        }

        .text-white {
            color: white;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.5);
        }

        .text-muted {
            color: rgba(255, 255, 255, 0.6);
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        h5 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        h6 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        p {
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-light {
            background: rgba(255, 255, 255, 0.9);
            color: var(--dark-color);
        }

        .btn-light:hover {
            background: white;
        }

        .btn-outline-light {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-outline-primary {
            background: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .w-100 {
            width: 100%;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .col-lg-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
            padding: 0 15px;
        }

        .col-lg-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 0 15px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .p-4 {
            padding: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 2rem;
        }

        .calculator-form {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .calculator-form h5 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 0.5rem;
        }

        .info-panel {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .info-panel h5, .info-panel h6 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .info-panel ul {
            margin-bottom: 1rem;
        }

        .formula-box {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            text-align: center;
        }

        .results-section {
            margin-top: 2rem;
        }

        .result-card {
            background: var(--success-color);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: none;
        }

        .result-card h5 {
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }

        .quick-reference .btn {
            text-align: left;
        }

        .recent-item {
            background: rgba(255, 255, 255, 0.05);
            transition: background 0.3s;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        .recent-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .input-group {
            display: flex;
            margin-bottom: 1rem;
        }

        .input-group-text {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--dark-color);
            padding: 0.5rem;
            border-radius: 5px 0 0 5px;
            font-weight: 500;
            min-width: 160px;
            display: flex;
            align-items: center;
        }

        .form-control {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0 5px 5px 0;
            background: rgba(255, 255, 255, 0.9);
            color: var(--dark-color);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .border {
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .rounded {
            border-radius: 5px;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        ol, ul {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        li {
            margin-bottom: 0.5rem;
        }

        strong {
            font-weight: 600;
        }

        .small {
            font-size: 0.875rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .col-lg-8, .col-lg-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .row {
                margin: 0 -10px;
            }
            
            .col-lg-8, .col-lg-4, .col-md-6 {
                padding: 0 10px;
            }
            
            .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
            
            .btn {
                width: 100%;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-ruler-vertical me-3"></i>Grade Rod Calculator</h1>
                <p class="text-white-50">Calculate ground elevations using grade rod readings</p>
            </div>
            <div>
                <button class="btn btn-light me-2" onclick="toggleFavorite('grade-rod')" id="favoriteBtn">
                    <i class="far fa-star"></i> Add to Favorites
                </button>
                <a href="../../index.php" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left"></i> Back to Site Tools
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="glass-card p-4 mb-4">
                    <h4><i class="fas fa-ruler-vertical me-2"></i>Grade Rod Elevation Calculator</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="calculator-form">
                                <h5>Survey Parameters</h5>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Instrument Height</span>
                                    <input type="number" class="form-control" id="instrumentHt" placeholder="feet" step="0.01">
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Rod Reading</span>
                                    <input type="number" class="form-control" id="rodReading" placeholder="feet" step="0.01">
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Benchmark Elevation</span>
                                    <input type="number" class="form-control" id="benchmarkElev" placeholder="feet" step="0.01">
                                </div>
                                <button class="btn btn-primary w-100" onclick="calculateGradeElevation()">
                                    Calculate Ground Elevation
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-panel">
                                <h5><i class="fas fa-info-circle me-2"></i>Instructions</h5>
                                <ul>
                                    <li><strong>Instrument Height:</strong> Height of total station/theodolite</li>
                                    <li><strong>Rod Reading:</strong> Reading on grade rod through telescope</li>
                                    <li><strong>Benchmark Elevation:</strong> Known elevation reference point</li>
                                    <li><strong>HI:</strong> Height of Instrument = BM + HH</li>
                                </ul>
                                
                                <h6 class="mt-4">Formula:</h6>
                                <div class="formula-box">
                                    <p><strong>Ground Elevation = HI - Rod Reading</strong></p>
                                    <p>Where: <strong>HI = BM + Instrument Height</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results -->
                    <div class="results-section mt-4">
                        <div class="result-card" id="gradeRodResult">
                            <h5><i class="fas fa-ruler-vertical me-2"></i>Grade Rod Calculation</h5>
                            <div id="gradeRodOutput"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Reference -->
                <div class="glass-card p-4 mb-4">
                    <h4><i class="fas fa-book me-2"></i>Quick Reference</h4>
                    <div class="quick-reference">
                        <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('fieldProcedure')">
                            Field Procedure
                        </button>
                        <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('errorSources')">
                            Common Errors
                        </button>
                        <button class="btn btn-outline-primary w-100 mb-2" onclick="showReference('equipment')">
                            Equipment Requirements
                        </button>
                    </div>
                </div>

                <!-- Recent Calculations -->
                <div class="glass-card p-4">
                    <h4><i class="fas fa-history me-2"></i>Recent Calculations</h4>
                    <div id="recentCalculations">
                        <!-- Recent calculations will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateGradeElevation() {
            const instrumentHt = parseFloat(document.getElementById('instrumentHt').value);
            const rodReading = parseFloat(document.getElementById('rodReading').value);
            const benchmarkElev = parseFloat(document.getElementById('benchmarkElev').value);
            
            if (!instrumentHt || !rodReading || !benchmarkElev) {
                alert('Please enter all values');
                return;
            }
            
            const groundElevation = benchmarkElev + instrumentHt - rodReading;
            const hi = benchmarkElev + instrumentHt; // Height of Instrument
            
            const resultHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Benchmark Elevation:</strong> ${benchmarkElev} feet</p>
                        <p><strong>Instrument Height:</strong> ${instrumentHt} feet</p>
                        <p><strong>Rod Reading:</strong> ${rodReading} feet</p>
                        <p><strong>Height of Instrument (HI):</strong> ${hi.toFixed(3)} feet</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Ground Elevation:</strong> ${groundElevation.toFixed(3)} feet</p>
                        <p><strong>Elevation Difference:</strong> ${Math.abs(groundElevation - benchmarkElev).toFixed(3)} feet</p>
                        <p><strong>Status:</strong> ${groundElevation > benchmarkElev ? 'Above Benchmark' : 'Below Benchmark'}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <h6>Field Procedure:</h6>
                    <ol>
                        <li>Set up instrument over benchmark</li>
                        <li>Measure instrument height: ${instrumentHt} ft</li>
                        <li>Read rod on benchmark: ${rodReading} ft</li>
                        <li>Calculate HI: BM + HH = ${hi.toFixed(3)} ft</li>
                        <li>Move to point, read rod, subtract from HI</li>
                        <li>Result: ${groundElevation.toFixed(3)} ft elevation</li>
                    </ol>
                </div>
                <div class="mt-3">
                    <h6>Quality Check:</h6>
                    <ul>
                        <li>Verify benchmark elevation is correct</li>
                        <li>Check instrument height measurement</li>
                        <li>Ensure rod is held vertically</li>
                        <li>Read rod to nearest 0.01 ft</li>
                        <li>Repeat measurements for verification</li>
                    </ul>
                </div>
            `;
            
            document.getElementById('gradeRodOutput').innerHTML = resultHTML;
            document.getElementById('gradeRodResult').style.display = 'block';
            saveCalculation('Grade Rod', `Elevation: ${groundElevation.toFixed(3)} ft (HI: ${hi.toFixed(3)} ft)`);
        }

        function toggleFavorite(calculator) {
            let favorites = JSON.parse(localStorage.getItem('favoriteCalculators') || '[]');
            const btn = document.getElementById('favoriteBtn');
            
            if (favorites.includes(calculator)) {
                favorites = favorites.filter(fav => fav !== calculator);
                btn.innerHTML = '<i class="far fa-star"></i> Add to Favorites';
            } else {
                favorites.push(calculator);
                btn.innerHTML = '<i class="fas fa-star"></i> Remove from Favorites';
            }
            
            localStorage.setItem('favoriteCalculators', JSON.stringify(favorites));
        }

        function showReference(type) {
            let message = '';
            
            switch(type) {
                case 'fieldProcedure':
                    message = 'Standard Field Procedure:\n\n' +
                             '1. Set up tripod over benchmark\n' +
                             '2. Level instrument carefully\n' +
                             '3. Measure and record instrument height\n' +
                             '4. Take backsight on benchmark\n' +
                             '5. Calculate Height of Instrument (HI)\n' +
                             '6. Move to survey point\n' +
                             '7. Hold rod vertically on point\n' +
                             '8. Take foresight reading\n' +
                             '9. Calculate elevation (HI - Rod Reading)\n' +
                             '10. Record all measurements';
                    break;
                case 'errorSources':
                    message = 'Common Sources of Error:\n\n' +
                             'Instrument Height:\n' +
                             '- Incorrect measurement of HH\n' +
                             '- Not measuring to correct point\n' +
                             '- Rod not held vertically\n\n' +
                             'Reading Errors:\n' +
                             '- Parallax in telescope\n' +
                             '- Rod not held plumb\n' +
                             '- Reading wrong number on rod\n' +
                             '- Temperature effects on rod\n\n' +
                             'Setup Errors:\n' +
                             '- Instrument not level\n' +
                             '- Benchmark elevation wrong\n' +
                             '- Instrument not over point\n\n' +
                             'Environmental:\n' +
                             '- Wind affecting rod\n' +
                             '- Heat shimmer\n' +
                             '- Poor visibility';
                    break;
                case 'equipment':
                    message = 'Required Equipment:\n\n' +
                             'Survey Instruments:\n' +
                             '- Total station or theodolite\n' +
                             '- Tripod with adjustable legs\n' +
                             '- Grade rod (5-25 ft sections)\n\n' +
                             'Measuring Tools:\n' +
                             '- Steel measuring tape\n' +
                             '- Plumb bob for centering\n' +
                             '- Hand level for rod check\n\n' +
                             'Accessories:\n' +
                             '- Target or prism for long distances\n' +
                             '- Data collector or field book\n' +
                             '- Calculator\n\n' +
                             'Safety Equipment:\n' +
                             '- High-visibility clothing\n' +
                             '- Hard hat in construction areas\n' +
                             '- Communication devices';
                    break;
            }
            
            alert(message);
        }

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentSiteCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            // Keep only last 10 calculations
            recent = recent.slice(0, 10);
            localStorage.setItem('recentSiteCalculations', JSON.stringify(recent));
            loadRecentCalculations();
        }

        function loadRecentCalculations() {
            const recent = JSON.parse(localStorage.getItem('recentSiteCalculations') || '[]');
            const container = document.getElementById('recentCalculations');
            
            if (recent.length === 0) {
                container.innerHTML = '<p class="text-muted">No recent calculations</p>';
                return;
            }
            
            container.innerHTML = recent.map(calc => `
                <div class="recent-item mb-2 p-2 border rounded">
                    <div class="small"><strong>${calc.type}</strong></div>
                    <div class="small text-muted">${calc.calculation}</div>
                    <div class="small text-muted">${calc.timestamp}</div>
                </div>
            `).join('');
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentCalculations();
            
            // Check if this calculator is in favorites
            const favorites = JSON.parse(localStorage.getItem('favoriteCalculators') || '[]');
            if (favorites.includes('grade-rod')) {
                document.getElementById('favoriteBtn').innerHTML = '<i class="fas fa-star"></i> Remove from Favorites';
            }
        });
    </script>
</body>
</html>
