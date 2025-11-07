<?php
// modules/civil/concrete/concrete-mix.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concrete Mix Design - AEC Toolkit</title>
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
            font-size: 1.5rem;
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
            <h1>Concrete Mix Design</h1>
            <form id="concrete-mix-form">
                <div class="form-group">
                    <label for="volume">Volume (m³)</label>
                    <input type="number" id="volume" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="grade">Grade</label>
                    <select id="grade" class="form-control">
                        <option value="M20">M20 (1:1.5:3)</option>
                        <option value="M25">M25 (1:1:2)</option>
                        <option value="M30">M30 (1:0.75:1.5)</option>
                    </select>
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
        document.getElementById('concrete-mix-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const volume = parseFloat(document.getElementById('volume').value);
            const grade = document.getElementById('grade').value;

            if (isNaN(volume)) {
                alert('Please enter a valid volume.');
                return;
            }
            
            const ratios = {
                'M20': { cement: 1, sand: 1.5, aggregate: 3 },
                'M25': { cement: 1, sand: 1, aggregate: 2 },
                'M30': { cement: 1, sand: 0.75, aggregate: 1.5 },
            };
            
            const ratio = ratios[grade];
            const totalParts = ratio.cement + ratio.sand + ratio.aggregate;
            const dryVolume = volume * 1.54;
            const cementVolume = (dryVolume / totalParts) * ratio.cement;
            const sandVolume = (dryVolume / totalParts) * ratio.sand;
            const aggregateVolume = (dryVolume / totalParts) * ratio.aggregate;
            const cementBags = cementVolume / 0.035;
            
            document.getElementById('result').innerHTML = `Cement: ${cementBags.toFixed(1)} bags<br>Sand: ${sandVolume.toFixed(3)} m³<br>Aggregate: ${aggregateVolume.toFixed(3)} m³`;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
</body>
</html>