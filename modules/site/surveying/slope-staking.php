<?php
// modules/site/surveying/slope-staking.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slope Staking Calculator - AEC Toolkit</title>
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
        }

        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            color: var(--light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .container {
            max-width: 800px;
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
            color: #feca57;
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
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            display: none; /* Hidden by default */
        }

        .result-area h3 {
            font-size: 1.5rem;
            color: #feca57;
            margin-bottom: 1rem;
        }

        #result {
            font-size: 2rem;
            font-weight: 700;
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
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-chart-line"></i> Slope Staking Calculator</h1>
            <form id="slope-staking-form">
                <div class="form-group">
                    <label for="cut-fill">Cut/Fill</label>
                    <select id="cut-fill" class="form-control" required>
                        <option value="cut">Cut</option>
                        <option value="fill">Fill</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="slope-ratio">Slope Ratio (H:V)</label>
                    <input type="number" id="slope-ratio" class="form-control" step="0.1" value="2" required>
                </div>
                <div class="form-group">
                    <label for="grade-elev">Grade Elevation (feet)</label>
                    <input type="number" id="grade-elev" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="ground-elev">Ground Elevation (feet)</label>
                    <input type="number" id="ground-elev" class="form-control" step="0.01" required>
                </div>
                <button type="submit" class="btn-calculate">Calculate</button>
            </form>
            <div class="result-area" id="result-area">
                <h3>Slope Stake Calculation</h3>
                <div id="result"></div>
            </div>
        </div>
        <a href="../../../index.php" class="back-link">Back to Site Tools</a>
    </div>

    <script>
        document.getElementById('slope-staking-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const cutFill = document.getElementById('cut-fill').value;
            const slopeRatio = parseFloat(document.getElementById('slope-ratio').value);
            const gradeElev = parseFloat(document.getElementById('grade-elev').value);
            const groundElev = parseFloat(document.getElementById('ground-elev').value);

            if (isNaN(slopeRatio) || isNaN(gradeElev) || isNaN(groundElev)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const elevDifference = Math.abs(gradeElev - groundElev);
            const horizontalDistance = elevDifference * slopeRatio;
            const stakeType = cutFill === 'cut' ? 'Cut Slope Stake' : 'Fill Slope Stake';
            
            const result = `
                <p><strong>Operation:</strong> ${cutFill === 'cut' ? 'Cut' : 'Fill'} Slope</p>
                <p><strong>Slope Ratio:</strong> ${slopeRatio}:1 (H:V)</p>
                <p><strong>Grade Elevation:</strong> ${gradeElev} feet</p>
                <p><strong>Ground Elevation:</strong> ${groundElev} feet</p>
                <p><strong>Elevation Difference:</strong> ${elevDifference.toFixed(2)} feet</p>
                <p><strong>Horizontal Distance:</strong> ${horizontalDistance.toFixed(1)} feet</p>
                <p><strong>Stake Type:</strong> ${stakeType}</p>
                <p><strong>Field Instructions:</strong> Set stake ${horizontalDistance.toFixed(1)} feet from centerline</p>
            `;
            
            document.getElementById('result').innerHTML = result;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
