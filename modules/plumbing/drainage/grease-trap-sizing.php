<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grease Trap Sizing - AEC Calculator</title>
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

        .calculator-wrapper {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .result-area {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            display: none;
        }

        .recent-calculations {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 10px;
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
            width: 100%;
        }

        .btn-calculate:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .back-button {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50px;
            color: var(--light);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            margin-top: 2rem;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: white;
            text-decoration: none;
        }

        .info-table {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .section-toggle {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            cursor: pointer;
        }

        .section-content {
            display: none;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-filter me-2"></i>Grease Trap Sizing
            </h2>
            
            <form id="grease-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('kitchen')">
                            <h5><i class="fas fa-utensils me-2"></i>Kitchen Details</h5>
                        </div>
                        <div id="kitchen-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="mealCount">Number of Meals per Day</label>
                                <input type="number" class="form-control" id="mealCount" min="0" step="1">
                            </div>

                            <div class="form-group">
                                <label for="kitchenType">Kitchen Type</label>
                                <select class="form-control" id="kitchenType">
                                    <option value="restaurant">Restaurant</option>
                                    <option value="cafe">Café/Coffee Shop</option>
                                    <option value="hospital">Hospital/Institution</option>
                                    <option value="hotel">Hotel/Motel</option>
                                    <option value="takeaway">Fast Food/Takeaway</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="operatingHours">Operating Hours per Day</label>
                                <input type="number" class="form-control" id="operatingHours" min="0" max="24" step="0.5" value="12">
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('fixtures')">
                            <h5><i class="fas fa-sink me-2"></i>Fixtures & Flow</h5>
                        </div>
                        <div id="fixtures-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="sinkCount">Number of Kitchen Sinks</label>
                                <input type="number" class="form-control" id="sinkCount" min="0" step="1">
                            </div>

                            <div class="form-group">
                                <label for="dishwasherCount">Number of Dishwashers</label>
                                <input type="number" class="form-control" id="dishwasherCount" min="0" step="1">
                            </div>

                            <div class="form-group">
                                <label for="wasteFoodDisposal">Waste Food Disposal Units</label>
                                <select class="form-control" id="wasteFoodDisposal">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Grease Trap Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Sizing Results</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Sizing Guidelines</h6>
                            <small>
                                Restaurant: 12-15L per meal<br>
                                Café: 8-10L per meal<br>
                                Hospital: 10-12L per meal<br>
                                Hotel: 10-12L per meal<br>
                                Fast Food: 6-8L per meal<br>
                                Min. Retention: 30 mins<br>
                                Safety Factor: 1.3
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentGreaseCalculations"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <a href="<?php echo function_exists('app_base_url') ? app_base_url('modules/plumbing/index.php') : '../modules/plumbing/index.php'; ?>" class="back-button">
            <i class="fas fa-arrow-left me-2"></i>Back to Plumbing
        </a>
    </div>

    <script>
        // Flow rates per fixture (L/min)
        const fixtureFlows = {
            sink: 15,
            dishwasher: 25
        };

        // Grease production per meal type (L/meal)
        const mealFactors = {
            restaurant: 13,
            cafe: 9,
            hospital: 11,
            hotel: 11,
            takeaway: 7
        };

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('grease-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const mealCount = parseInt(document.getElementById('mealCount').value) || 0;
            const kitchenType = document.getElementById('kitchenType').value;
            const hours = parseFloat(document.getElementById('operatingHours').value) || 0;
            const sinks = parseInt(document.getElementById('sinkCount').value) || 0;
            const dishwashers = parseInt(document.getElementById('dishwasherCount').value) || 0;
            const hasDisposal = document.getElementById('wasteFoodDisposal').value === 'yes';
            
            if (!mealCount || !hours || (!sinks && !dishwashers)) {
                showNotification('Please fill in all required fields', 'info');
                return;
            }
            
            // Calculate requirements
            const results = calculateGreaseTrap(
                mealCount,
                kitchenType,
                hours,
                sinks,
                dishwashers,
                hasDisposal
            );
            
            let resultText = `<strong>Flow Analysis:</strong><br>`;
            resultText += `Peak Flow Rate: ${results.peakFlow.toFixed(1)} L/min<br>`;
            resultText += `Daily Flow: ${results.dailyFlow.toFixed(1)} L/day<br><br>`;
            
            resultText += `<strong>Grease Trap Sizing:</strong><br>`;
            resultText += `Required Capacity: ${results.capacity.toFixed(1)} L<br>`;
            resultText += `Recommended Size: ${results.recommendedSize} L<br>`;
            resultText += `Retention Time: ${results.retentionTime.toFixed(1)} mins<br>`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${mealCount} meals/day → ${results.recommendedSize}L trap`);
        });

        function calculateGreaseTrap(meals, type, hours, sinks, dishwashers, hasDisposal) {
            const warnings = [];
            
            // Calculate peak flow rate
            const totalFlow = (sinks * fixtureFlows.sink) + 
                            (dishwashers * fixtureFlows.dishwasher);
            
            const peakFlow = totalFlow * 1.2; // 20% surge factor
            const dailyFlow = totalFlow * hours * 60;
            
            // Calculate grease capacity
            let baseCapacity = meals * mealFactors[type];
            
            // Adjustments
            if (hasDisposal) {
                baseCapacity *= 1.3;
                warnings.push('Waste disposal unit increases required capacity by 30%');
            }
            
            // Apply retention time requirements
            const flowBasedCapacity = peakFlow * 30; // 30 mins retention
            const capacity = Math.max(baseCapacity, flowBasedCapacity);
            
            // Standard sizes (L)
            const standardSizes = [500, 1000, 1500, 2000, 2500, 3000, 4000, 5000];
            const recommendedSize = standardSizes.find(s => s >= capacity) || 
                                 Math.ceil(capacity/500) * 500;
            
            // Calculate actual retention
            const retentionTime = (recommendedSize / peakFlow);
            
            if (retentionTime < 30) {
                warnings.push('Warning: Retention time below 30 minutes minimum');
            }
            
            if (dailyFlow > recommendedSize * 3) {
                warnings.push('Warning: Daily flow exceeds recommended turnover rate');
            }
            
            return {
                peakFlow,
                dailyFlow,
                capacity,
                recommendedSize,
                retentionTime,
                warnings
            };
        }

        function saveRecent(calculation) {
            const key = 'recentGreaseCalculations';
            let recent = JSON.parse(localStorage.getItem(key) || '[]');
            recent.unshift({
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            recent = recent.slice(0, 5); // Keep last 5
            localStorage.setItem(key, JSON.stringify(recent));
            displayRecent();
        }

        function displayRecent() {
            const key = 'recentGreaseCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentGreaseCalculations');
            
            if (recent.length === 0) {
                container.innerHTML = '<p class="text-muted">No recent calculations</p>';
                return;
            }
            
            container.innerHTML = recent.map(item => `
                <div class="card bg-dark mb-2">
                    <div class="card-body">
                        <div class="small">${item.calculation}</div>
                        <div class="small text-muted">${item.timestamp}</div>
                    </div>
                </div>
            `).join('');
        }

        // Show kitchen section by default and load recent calculations
        document.getElementById('kitchen-section').style.display = 'block';
        displayRecent();
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>
