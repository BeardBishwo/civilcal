<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Traditional Nepali Units Calculator') ?></title>
    <link rel="stylesheet" href="/assets/css/widgets.css">
    <link rel="stylesheet" href="/assets/css/calculators.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <!-- Header Section -->
            <div class="calculator-header">
                <h1>🇳🇵 <?= htmlspecialchars($title ?? 'Traditional Nepali Units Calculator') ?></h1>
                <p class="description"><?= htmlspecialchars($description ?? 'Convert between traditional Nepali measurement units') ?></p>
            </div>

            <!-- Widget Integration Section -->
            <?php if (($widget_enabled ?? false) && ($geolocation_available ?? false)): ?>
                <div class="widget-integration">
                    <?php
                    // Create and render the TraditionalUnitsWidget
                    require_once __DIR__ . '/../../Services/WidgetManager.php';
                    require_once __DIR__ . '/../../Widgets/TraditionalUnitsWidget.php';
                    
                    $widgetManager = new \App\Services\WidgetManager();
                    $traditionalUnitsWidget = $widgetManager->createWidget('TraditionalUnitsWidget', [
                        'show_nepali_names' => true,
                        'auto_detect_location' => true,
                        'default_from_unit' => 'daam',
                        'default_to_unit' => 'sq_feet',
                        'compact_mode' => false
                    ]);
                    
                    if ($traditionalUnitsWidget) {
                        echo $traditionalUnitsWidget->render();
                    } else {
                        // Fallback to basic calculator if widget fails
                        include 'traditional-units-fallback.php';
                    }
                    ?>
                </div>
            <?php else: ?>
                <!-- Basic Calculator Fallback -->
                <div class="traditional-units-calculator">
                    <div class="calculator-form">
                        <div class="input-section">
                            <div class="form-group">
                                <label for="value">Value:</label>
                                <input type="number" id="value" step="any" placeholder="Enter value" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="from-unit">From Unit:</label>
                                <select id="from-unit" class="form-control" required>
                                    <option value="">Select unit</option>
                                    <?php foreach (($supported_units ?? []) as $key => $label): ?>
                                        <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="to-unit">To Unit:</label>
                                <select id="to-unit" class="form-control" required>
                                    <option value="">Select unit</option>
                                    <?php foreach (($supported_units ?? []) as $key => $label): ?>
                                        <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                    <?php foreach (($metric_units ?? []) as $key => $label): ?>
                                        <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" onclick="convertUnits()" class="btn btn-primary">Convert</button>
                            <button type="button" onclick="getAllConversions()" class="btn btn-secondary">Show All Conversions</button>
                        </div>
                    </div>
                    
                    <div id="results-section" class="results-section" style="display: none;">
                        <h3>Conversion Result</h3>
                        <div id="conversion-result" class="result-display"></div>
                    </div>
                    
                    <div id="all-conversions-section" class="all-conversions-section" style="display: none;">
                        <h3>All Conversions</h3>
                        <div id="conversions-list" class="conversions-grid"></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Unit Information Section -->
            <div class="unit-info">
                <h3>Traditional Nepali Units Information</h3>
                <div class="unit-details">
                    <div class="unit-item">
                        <h4>Dhur (धुर)</h4>
                        <p>Smallest unit. Base unit for all conversions.</p>
                    </div>
                    <div class="unit-item">
                        <h4>Daam (दाम)</h4>
                        <p>1 Dhur = 1 Daam (synonymous)</p>
                    </div>
                    <div class="unit-item">
                        <h4>Paisa (पैसा)</h4>
                        <p>4 Dhur = 1 Paisa</p>
                    </div>
                    <div class="unit-item">
                        <h4>Aana (आना)</h4>
                        <p>4 Paisa = 16 Dhur = 1 Aana</p>
                    </div>
                    <div class="unit-item">
                        <h4>Kattha (कठ्ठा)</h4>
                        <p>5 Paisa = 20 Dhur = 1 Kattha</p>
                    </div>
                    <div class="unit-item">
                        <h4>Bigha (बिघा)</h4>
                        <p>20 Kattha = 400 Dhur = 1 Bigha</p>
                    </div>
                    <div class="unit-item">
                        <h4>Ropani (रोपनी)</h4>
                        <p>256 Dhur = 1 Ropani (varies by region)</p>
                    </div>
                </div>
            </div>

            <!-- Usage Instructions -->
            <div class="instructions">
                <h3>How to Use</h3>
                <ol>
                    <li>Enter the value you want to convert</li>
                    <li>Select the source unit from the "From Unit" dropdown</li>
                    <li>Select the target unit from the "To Unit" dropdown</li>
                    <li>Click "Convert" to see the result</li>
                    <li>Click "Show All Conversions" to see conversions to all available units</li>
                </ol>
            </div>

            <!-- Notes Section -->
            <div class="notes">
                <h3>Important Notes</h3>
                <ul>
                    <li>These units are traditionally used in Nepal for land measurement</li>
                    <li>Exact conversions may vary slightly by region and historical context</li>
                    <li>All conversions are approximate and based on standard values</li>
                    <li>The calculator automatically detects if you are in Nepal and shows Nepali names</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // JavaScript functionality for the traditional units calculator
        
        function convertUnits() {
            const value = parseFloat(document.getElementById('value').value);
            const fromUnit = document.getElementById('from-unit').value;
            const toUnit = document.getElementById('to-unit').value;
            
            if (!value || !fromUnit || !toUnit) {
                alert('Please fill in all fields');
                return;
            }
            
            // Make AJAX request to convert units
            fetch('/api/traditional-units/convert', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    value: value,
                    from_unit: fromUnit,
                    to_unit: toUnit
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('conversion-result').innerHTML = 
                        `<div class="result-item">
                            <strong>${data.input_value} ${getUnitName(data.input_unit, true)} = ${data.output_value} ${getUnitName(data.output_unit, true)}</strong>
                        </div>`;
                    document.getElementById('results-section').style.display = 'block';
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during conversion');
            });
        }
        
        function getAllConversions() {
            const value = parseFloat(document.getElementById('value').value);
            const fromUnit = document.getElementById('from-unit').value;
            
            if (!value || !fromUnit) {
                alert('Please enter a value and select a unit');
                return;
            }
            
            // Make AJAX request to get all conversions
            fetch('/api/traditional-units/all-conversions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    value: value,
                    from_unit: fromUnit
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let html = '';
                    // Show all traditional unit conversions
                    if (data.conversions.traditional) {
                        for (const [unit, result] of Object.entries(data.conversions.traditional)) {
                            html += `
                                <div class="conversion-item">
                                    <div class="unit-name">${getUnitName(unit, true)}</div>
                                    <div class="unit-value">${result.output_value}</div>
                                </div>
                            `;
                        }
                    }
                    
                    // Show metric conversion
                    if (data.conversions.metric) {
                        const metric = data.conversions.metric;
                        html += `
                            <div class="conversion-item">
                                <div class="unit-name">${metric.output_unit_name}</div>
                                <div class="unit-value">${metric.output_value}</div>
                            </div>
                        `;
                    }
                    
                    document.getElementById('conversions-list').innerHTML = html;
                    document.getElementById('all-conversions-section').style.display = 'block';
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while getting conversions');
            });
        }
        
        function getUnitName(unit, showNepali = false) {
            const units = {
                'dhur': 'Dhur (धुर)',
                'daam': 'Daam (दाम)',
                'paisa': 'Paisa (पैसा)',
                'aana': 'Aana (आना)',
                'kattha': 'Kattha (कठ्ठा)',
                'bigha': 'Bigha (बिघा)',
                'ropani': 'Ropani (रोपनी)',
                'sq_feet': 'Square Feet',
                'sq_meter': 'Square Meters',
                'sq_yard': 'Square Yards'
            };
            
            return units[unit] || unit;
        }
        
        // Auto-detect user's location and adjust UI
        function detectLocation() {
            // This would typically make a request to the geolocation service
            // For now, we'll just check if the browser supports geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Could implement location-based features here
                    console.log('User location detected');
                });
            }
        }
        
        // Initialize calculator
        document.addEventListener('DOMContentLoaded', function() {
            detectLocation();
            
            // Add enter key support for form submission
            const form = document.querySelector('.traditional-units-calculator .calculator-form');
            if (form) {
                form.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        convertUnits();
                    }
                });
            }
        });
    </script>
</body>
</html>
