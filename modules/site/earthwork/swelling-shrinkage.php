<?php
// modules/site/earthwork/swelling-shrinkage.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swell & Shrink Calculator - AEC Toolkit</title>
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
            <h1><i class="fas fa-compress-arrows-alt"></i> Swell & Shrink Calculator</h1>
            <form id="swell-shrink-form">
                <div class="form-group">
                    <label for="material-type">Material Type</label>
                    <select id="material-type" class="form-control" required>
                        <option value="clay">Clay</option>
                        <option value="sand">Sand</option>
                        <option value="gravel">Gravel</option>
                        <option value="rock">Rock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="original-volume">Original Volume (cubic yards)</label>
                    <input type="number" id="original-volume" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="density-factor">Density Factor</label>
                    <input type="number" id="density-factor" class="form-control" step="0.01" value="1.2" required>
                </div>
                <button type="submit" class="btn-calculate">Calculate</button>
            </form>
            <div class="result-area" id="result-area">
                <h3>Swell & Shrink Calculation</h3>
                <div id="result"></div>
            </div>
        </div>
        <a href="../../../index.php" class="back-link">Back to Site Tools</a>
    </div>

    <script>
        document.getElementById('swell-shrink-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const materialType = document.getElementById('material-type').value;
            const originalVolume = parseFloat(document.getElementById('original-volume').value);
            const densityFactor = parseFloat(document.getElementById('density-factor').value);

            if (isNaN(originalVolume) || isNaN(densityFactor)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const swellFactor = densityFactor;
            const shrinkFactor = 1 / densityFactor;
            const swelledVolume = originalVolume * swellFactor;
            const shrunkVolume = originalVolume * shrinkFactor;
            
            const result = `
                <p><strong>Material Type:</strong> ${materialType}</p>
                <p><strong>Original Volume:</strong> ${originalVolume} cubic yards</p>
                <p><strong>Density Factor:</strong> ${densityFactor}</p>
                <p><strong>Swell Factor:</strong> ${swellFactor}</p>
                <p><strong>Swelled Volume:</strong> ${swelledVolume.toFixed(2)} cubic yards</p>
                <p><strong>Shrunk Volume:</strong> ${shrunkVolume.toFixed(2)} cubic yards</p>
                <p><strong>Volume Change:</strong> ${((swelledVolume - originalVolume)).toFixed(2)} cubic yards increase (swelled)</p>
            `;
            
            document.getElementById('result').innerHTML = result;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
