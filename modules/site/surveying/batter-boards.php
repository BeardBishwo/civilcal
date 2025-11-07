<?php
// modules/site/surveying/batter-boards.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batter Board Setup Calculator - AEC Toolkit</title>
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
            <h1><i class="fas fa-square"></i> Batter Board Setup Calculator</h1>
            <form id="batter-board-form">
                <div class="form-group">
                    <label for="building-width">Building Width (feet)</label>
                    <input type="number" id="building-width" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="building-length">Building Length (feet)</label>
                    <input type="number" id="building-length" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="offset-distance">Offset Distance (feet)</label>
                    <input type="number" id="offset-distance" class="form-control" step="0.01" value="2" required>
                </div>
                <div class="form-group">
                    <label for="board-height">Board Height Above Grade (feet)</label>
                    <input type="number" id="board-height" class="form-control" step="0.01" value="3" required>
                </div>
                <button type="submit" class="btn-calculate">Calculate</button>
            </form>
            <div class="result-area" id="result-area">
                <h3>Batter Board Setup</h3>
                <div id="result"></div>
            </div>
        </div>
        <a href="../../../index.php" class="back-link">Back to Site Tools</a>
    </div>

    <script>
        document.getElementById('batter-board-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const buildingWidth = parseFloat(document.getElementById('building-width').value);
            const buildingLength = parseFloat(document.getElementById('building-length').value);
            const offsetDistance = parseFloat(document.getElementById('offset-distance').value);
            const boardHeight = parseFloat(document.getElementById('board-height').value);

            if (isNaN(buildingWidth) || isNaN(buildingLength) || isNaN(offsetDistance) || isNaN(boardHeight)) {
                alert('Please enter valid numbers.');
                return;
            }
            
            const totalWidth = buildingWidth + (2 * offsetDistance);
            const totalLength = buildingLength + (2 * offsetDistance);
            const boardCount = 4;
            
            const result = `
                <p><strong>Building Width:</strong> ${buildingWidth} feet</p>
                <p><strong>Building Length:</strong> ${buildingLength} feet</p>
                <p><strong>Offset Distance:</strong> ${offsetDistance} feet</p>
                <p><strong>Board Height:</strong> ${boardHeight} feet</p>
                <p><strong>Total Setup Width:</strong> ${totalWidth} feet</p>
                <p><strong>Total Setup Length:</strong> ${totalLength} feet</p>
                <p><strong>Number of Boards Needed:</strong> ${boardCount}</p>
                <p><strong>Board Spacing:</strong> Set boards ${offsetDistance} feet from building corners</p>
            `;
            
            document.getElementById('result').innerHTML = result;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
</body>
</html>
