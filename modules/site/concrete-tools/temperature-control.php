<?php
// modules/site/concrete-tools/temperature-control.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concrete Temperature Control Calculator - AEC Toolkit</title>
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
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-thermometer-half"></i> Concrete Temperature Control</h1>
            <form id="temperature-control-form">
                <div class="form-group">
                    <label for="ambient-temp">Ambient Temperature (°F)</label>
                    <input type="number" id="ambient-temp" class="form-control" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="concrete-temp">Concrete Temperature (°F)</label>
                    <input type="number" id="concrete-temp" class="form-control" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="section-thickness">Section Thickness (inches)</label>
                    <input type="number" id="section-thickness" class="form-control" step="0.1" value="12" required>
                </div>
                <div class="form-group">
                    <label for="target-temp">Target Temperature (°F)</label>
                    <input type="number" id="target-temp" class="form-control" step="0.1" value="70" required>
                </div>
                <button type="submit" class="btn-calculate">Calculate</button>
            </form>
            <div class="result-area" id="result-area">
                <h3>Temperature Control Requirements</h3>
                <div id="result"></div>
            </div>
        </div>
        <a href="../../../index.php" class="back-link">Back to Site Tools</a>
    </div>

    <script>
        document.getElementById('temperature-control-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const ambientTemp = parseFloat(document.getElementById('ambient-temp').value);
            const concreteTemp = parseFloat(document.getElementById('concrete-temp').value);
            const sectionThickness = parseFloat(document.getElementById('section-thickness').value);
            const targetTemp = parseFloat(document.getElementById('target-temp').value);

            if (isNaN(ambientTemp) || isNaN(concreteTemp) || isNaN(sectionThickness) || isNaN(targetTemp)) {
                alert('Please enter valid numbers.');
                return;
            }
            
            const tempDifference = concreteTemp - ambientTemp;
            const targetDifference = targetTemp - ambientTemp;
            const heatingRequired = tempDifference > targetDifference;
            const coolingRequired = tempDifference < targetDifference;
            
            let recommendations = '';
            if (heatingRequired) {
                recommendations = 'Heating required - Use heated water, pre-warm aggregates, or heating blankets';
            } else if (coolingRequired) {
                recommendations = 'Cooling required - Use chilled water, ice, or shaded storage';
            } else {
                recommendations = 'Temperature within acceptable range';
            }
            
            const result = `
                <p><strong>Ambient Temperature:</strong> ${ambientTemp}°F</p>
                <p><strong>Concrete Temperature:</strong> ${concreteTemp}°F</p>
                <p><strong>Target Temperature:</strong> ${targetTemp}°F</p>
                <p><strong>Section Thickness:</strong> ${sectionThickness} inches</p>
                <p><strong>Temperature Difference:</strong> ${tempDifference}°F</p>
                <p><strong>Action Required:</strong> ${heatingRequired ? 'Heating' : coolingRequired ? 'Cooling' : 'No action needed'}</p>
                <p><strong>Recommendations:</strong> ${recommendations}</p>
                <p><strong>Monitor:</strong> Check temperature every 4 hours for first 24 hours</p>
            `;
            
            document.getElementById('result').innerHTML = result;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
</body>
</html>
