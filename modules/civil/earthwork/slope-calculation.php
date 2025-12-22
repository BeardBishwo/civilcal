<?php
// modules/civil/earthwork/slope-calculation.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slope Calculation - AEC Toolkit</title>
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
        }

        body {
            background: linear-gradient(135deg, #000000, #000000, #000000);
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
            color: #ffffff;
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
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            display: none; /* Hidden by default */
        }

        .result-area h3 {
            font-size: 1.5rem;
            color: #ffffff;
            margin-bottom: 1rem;
        }

        #result {
            font-size: 1.5rem;
            font-weight: 700;
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
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1>Slope Calculation</h1>
            <form id="slope-calculation-form">
                <div class="form-group">
                    <label for="rise">Rise (m)</label>
                    <input type="number" id="rise" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="run">Run (m)</label>
                    <input type="number" id="run" class="form-control" step="0.01" required>
                </div>
                <button type="submit" class="btn-calculate">Calculate</button>
            </form>
            <div class="result-area" id="result-area">
                <h3>Result</h3>
                <p id="result"></p>
            </div>
        </div>
        <a href="../../../index.php" class="back-link">Back to Toolkit</a>
    </div>

    <script>
        document.getElementById('slope-calculation-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rise = parseFloat(document.getElementById('rise').value);
            const run = parseFloat(document.getElementById('run').value);

            if (isNaN(rise) || isNaN(run)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const slopeRatio = rise / run;
            const slopePercent = slopeRatio * 100;
            const angle = Math.atan(slopeRatio) * (180 / Math.PI);
            
            document.getElementById('result').innerHTML = `Slope Ratio: 1:${(1/slopeRatio).toFixed(2)}<br>Slope Percentage: ${slopePercent.toFixed(2)}%<br>Angle: ${angle.toFixed(2)}°`;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
