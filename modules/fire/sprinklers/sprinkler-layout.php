<?php
// modules/fire/sprinklers/sprinkler-layout.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sprinkler Layout Calculator - Fire Protection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e74c3c;
            --secondary: #c0392b;
            --accent: #f39c12;
            --dark: #1a202c;
            --light: #f7fafc;
            --glass: rgba(255, 255, 255, 0.05);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        body {
            background: linear-gradient(135deg, #2c1810, #4a2c2a, #6b3410);
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
            color: var(--accent);
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
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
            border-color: var(--accent);
            box-shadow: 0 0 15px rgba(243, 156, 18, 0.3);
        }

        .btn-calculate {
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
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
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            display: none; /* Hidden by default */
        }

        .result-area h3 {
            font-size: 1.5rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        #result {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.6;
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: var(--accent);
            text-decoration: none;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .info-box {
            background: rgba(52, 73, 94, 0.3);
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            border-left: 4px solid var(--accent);
        }

        .hazard-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .hazard-option {
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
        }

        .hazard-option:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .hazard-option.selected {
            background: var(--accent);
            color: var(--dark);
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-shower me-3"></i>Sprinkler Layout Calculator</h1>
            <form id="sprinkler-layout-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="room-length">Room Length (feet)</label>
                            <input type="number" id="room-length" class="form-control" step="0.1" required>
                        </div>
                        <div class="form-group">
                            <label for="room-width">Room Width (feet)</label>
                            <input type="number" id="room-width" class="form-control" step="0.1" required>
                        </div>
                        <div class="form-group">
                            <label for="ceiling-height">Ceiling Height (feet)</label>
                            <input type="number" id="ceiling-height" class="form-control" step="0.1" value="12">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Hazard Classification</label>
                            <div class="hazard-selector">
                                <div class="hazard-option" data-value="light">Light</div>
                                <div class="hazard-option" data-value="ordinary1">Ordinary 1</div>
                                <div class="hazard-option" data-value="ordinary2">Ordinary 2</div>
                                <div class="hazard-option" data-value="extra1">Extra 1</div>
                                <div class="hazard-option" data-value="extra2">Extra 2</div>
                            </div>
                            <input type="hidden" id="hazard-class" value="light">
                        </div>
                        <div class="form-group">
                            <label for="max-spacing">Maximum Spacing (feet)</label>
                            <input type="number" id="max-spacing" class="form-control" step="0.1" value="15">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-calculate">Calculate Layout</button>
            </form>
            
            <div class="info-box">
                <h5><i class="fas fa-info-circle me-2"></i>NFPA 13 Maximum Coverage Areas</h5>
                <p>Light Hazard: 225 ft² | Ordinary Hazard 1 & 2: 130 ft² | Extra Hazard 1 & 2: 100 ft²</p>
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Layout Results</h3>
                <div id="result"></div>
            </div>
        </div>
        <a href="../../../index.php" class="back-link">Back to Fire Protection Toolkit</a>
    </div>

    <script>
        // Hazard classification selection
        const hazardOptions = document.querySelectorAll('.hazard-option');
        const hazardInput = document.getElementById('hazard-class');
        
        hazardOptions.forEach(option => {
            option.addEventListener('click', function() {
                hazardOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                hazardInput.value = this.dataset.value;
            });
        });
        
        // Select Light Hazard by default
        document.querySelector('.hazard-option[data-value="light"]').classList.add('selected');

        document.getElementById('sprinkler-layout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const length = parseFloat(document.getElementById('room-length').value);
            const width = parseFloat(document.getElementById('room-width').value);
            const ceilingHeight = parseFloat(document.getElementById('ceiling-height').value);
            const hazardClass = document.getElementById('hazard-class').value;
            const maxSpacing = parseFloat(document.getElementById('max-spacing').value);

            if (isNaN(length) || isNaN(width)) {
                showNotification('Please enter valid room dimensions.', 'info');
                return;
            }
            
            const area = length * width;
            
            // Maximum coverage areas per sprinkler (NFPA 13)
            const coverageAreas = {
                'light': 225,        // ft²
                'ordinary1': 130,    // ft²
                'ordinary2': 130,    // ft²
                'extra1': 100,       // ft²
                'extra2': 100        // ft²
            };
            
            const maxCoverage = coverageAreas[hazardClass] || 225;
            const minSprinklers = Math.ceil(area / maxCoverage);
            
            // Maximum spacing between sprinklers
            const spacing = maxSpacing || 15;
            
            // Calculate optimal layout
            const sprinklersLength = Math.ceil(length / spacing);
            const sprinklersWidth = Math.ceil(width / spacing);
            const totalSprinklers = sprinklersLength * sprinklersWidth;
            
            const actualSpacingLength = length / sprinklersLength;
            const actualSpacingWidth = width / sprinklersWidth;
            const actualCoverage = area / totalSprinklers;
            
            // Check if layout meets requirements
            const meetsCoverage = actualCoverage <= maxCoverage;
            const meetsSpacing = Math.max(actualSpacingLength, actualSpacingWidth) <= spacing;
            
            let statusClass = meetsCoverage && meetsSpacing ? 'color: #2ecc71;' : 'color: #e74c3c;';
            let statusText = meetsCoverage && meetsSpacing ? 'COMPLIANT' : 'NON-COMPLIANT';
            
            // Calculate pipe requirements (simplified)
            const pipeRuns = sprinklersLength + sprinklersWidth;
            const totalPipeLength = pipeRuns * Math.max(length, width);
            
            const resultHTML = `
                <div style="text-align: left;">
                    <p><strong>Room Area:</strong> ${area.toFixed(0)} ft²</p>
                    <p><strong>Hazard Classification:</strong> ${hazardClass.replace(/([A-Z])/g, ' $1').trim()}</p>
                    <p><strong>Maximum Coverage per Sprinkler:</strong> ${maxCoverage} ft²</p>
                    <p><strong>Minimum Sprinklers Required:</strong> ${minSprinklers}</p>
                    <hr>
                    <p><strong>Recommended Layout:</strong> ${sprinklersLength} × ${sprinklersWidth} = ${totalSprinklers} sprinklers</p>
                    <p><strong>Actual Spacing:</strong> ${actualSpacingLength.toFixed(1)} ft × ${actualSpacingWidth.toFixed(1)} ft</p>
                    <p><strong>Coverage per Sprinkler:</strong> ${actualCoverage.toFixed(1)} ft²</p>
                    <p><strong>Pipe Runs Required:</strong> ${pipeRuns}</p>
                    <p><strong>Estimated Pipe Length:</strong> ${totalPipeLength.toFixed(0)} ft</p>
                    <hr>
                    <p style="${statusClass} font-size: 1.2em;"><strong>Status: ${statusText}</strong></p>
                    ${!meetsCoverage ? `<p style="color: #f39c12;">Coverage exceeds maximum allowable area</p>` : ''}
                    ${!meetsSpacing ? `<p style="color: #f39c12;">Sprinkler spacing exceeds maximum allowable</p>` : ''}
                </div>
            `;
            
            document.getElementById('result').innerHTML = resultHTML;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
