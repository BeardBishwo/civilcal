<?php
/**
 * MEP Unit Converter
 * Comprehensive unit conversion utility for MEP engineering calculations
 * Supports multiple unit systems and conversion types
 */
$base = defined('APP_BASE') ? rtrim(APP_BASE, '/') : '/aec-calculator';
require_once rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . $base . '/modules/mep/bootstrap.php';

// Initialize database connection
$db = new Database();

$message = '';
$message_type = '';

if ($_POST) {
    try {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'convert':
                $result = performUnitConversion($_POST);
                $converted_value = $result['converted_value'];
                $conversion_details = $result['conversion_details'];
                break;
                
            case 'batch_convert':
                $result = performBatchConversion($_POST);
                $batch_results = $result['results'];
                $conversion_count = $result['count'];
                break;
                
            case 'save_conversion':
                $result = saveConversionHistory($_POST);
                if ($result) {
                    $message = 'Conversion saved to history successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error saving conversion.';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get conversion history
$conversion_history = [];
try {
    $query = "SELECT * FROM unit_conversions ORDER BY created_at DESC LIMIT 20";
    $stmt = $db->executeQuery($query);
    $conversion_history = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Exception $e) {
    error_log("Error fetching conversion history: " . $e->getMessage());
}

/**
 * Perform unit conversion
 */
function performUnitConversion($data) {
    $from_unit = $data['from_unit'] ?? '';
    $to_unit = $data['to_unit'] ?? '';
    $value = floatval($data['value'] ?? 0);
    $category = $data['category'] ?? '';
    
    if (empty($from_unit) || empty($to_unit) || $value <= 0) {
        throw new Exception('Please provide valid conversion parameters');
    }
    
    $converted_value = convertUnit($value, $from_unit, $to_unit, $category);
    
    $conversion_details = [
        'original_value' => $value,
        'from_unit' => $from_unit,
        'to_unit' => $to_unit,
        'converted_value' => $converted_value,
        'conversion_factor' => $converted_value / $value,
        'category' => $category,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    return [
        'converted_value' => $converted_value,
        'conversion_details' => $conversion_details
    ];
}

/**
 * Core unit conversion function
 */
function convertUnit($value, $from_unit, $to_unit, $category) {
    // Define conversion factors for different categories
    $conversions = getUnitConversions();
    
    if (!isset($conversions[$category][$from_unit]) || !isset($conversions[$category][$to_unit])) {
        throw new Exception('Unsupported conversion: ' . $from_unit . ' to ' . $to_unit);
    }
    
    $from_factor = $conversions[$category][$from_unit];
    $to_factor = $conversions[$category][$to_unit];
    
    // Convert to base unit first, then to target unit
    $base_value = $value * $from_factor;
    $converted_value = $base_value / $to_factor;
    
    return round($converted_value, 6);
}

/**
 * Get all unit conversion factors
 */
function getUnitConversions() {
    return [
        'length' => [
            'mm' => 1,
            'cm' => 10,
            'm' => 1000,
            'km' => 1000000,
            'in' => 25.4,
            'ft' => 304.8,
            'yd' => 914.4,
            'mi' => 1609344
        ],
        'area' => [
            'mm2' => 1,
            'cm2' => 100,
            'm2' => 1000000,
            'km2' => 1000000000000,
            'in2' => 645.16,
            'ft2' => 92903.04,
            'yd2' => 836127.36,
            'acre' => 4046860000,
            'hectare' => 10000000000
        ],
        'volume' => [
            'mm3' => 1,
            'cm3' => 1000,
            'm3' => 1000000000,
            'liter' => 1000000,
            'ml' => 1,
            'gal_us' => 3785411.784,
            'gal_imp' => 4546090,
            'ft3' => 28316846.592,
            'in3' => 16387.064
        ],
        'mass' => [
            'mg' => 1,
            'g' => 1000,
            'kg' => 1000000,
            'tonne' => 1000000000,
            'lb' => 453592.37,
            'oz' => 28349.523,
            'ton_us' => 907184740,
            'ton_imp' => 1016046908.8
        ],
        'pressure' => [
            'pa' => 1,
            'kpa' => 1000,
            'mpa' => 1000000,
            'bar' => 100000,
            'psi' => 6894.757,
            'atm' => 101325,
            'mmhg' => 133.322,
            'torr' => 133.322,
            'inh2o' => 249.082
        ],
        'temperature' => [
            'c' => 'special',
            'f' => 'special',
            'k' => 'special'
        ],
        'energy' => [
            'j' => 1,
            'kj' => 1000,
            'mj' => 1000000,
            'wh' => 3600,
            'kwh' => 3600000,
            'mwh' => 3600000000,
            'btu' => 1055.056,
            'cal' => 4.184,
            'kcal' => 4184
        ],
        'power' => [
            'w' => 1,
            'kw' => 1000,
            'mw' => 1000000,
            'hp' => 745.7,
            'btu_hr' => 0.293071
        ],
        'flow_rate' => [
            'm3_s' => 1,
            'm3_hr' => 3600,
            'm3_day' => 86400,
            'l_s' => 0.001,
            'l_hr' => 3.6,
            'l_day' => 86.4,
            'gal_us_s' => 0.003785,
            'gal_us_min' => 0.227124,
            'ft3_s' => 0.028317,
            'ft3_min' => 1.699
        ],
        'velocity' => [
            'm_s' => 1,
            'm_min' => 60,
            'km_hr' => 3.6,
            'ft_s' => 0.3048,
            'ft_min' => 18.288,
            'mph' => 1.609344,
            'knots' => 1.852
        ],
        'density' => [
            'kg_m3' => 1,
            'g_cm3' => 1000,
            'lb_ft3' => 16.018463,
            'lb_gal_us' => 119.826
        ],
        'thermal_conductivity' => [
            'w_mk' => 1,
            'btu_ft_hr_f' => 1.73073,
            'cal_cm_s_c' => 418.4
        ]
    ];
}

/**
 * Handle temperature conversions (special case)
 */
function convertTemperature($value, $from_unit, $to_unit) {
    switch ($from_unit) {
        case 'c':
            if ($to_unit === 'f') {
                return ($value * 9/5) + 32;
            } elseif ($to_unit === 'k') {
                return $value + 273.15;
            }
            break;
        case 'f':
            if ($to_unit === 'c') {
                return ($value - 32) * 5/9;
            } elseif ($to_unit === 'k') {
                return ($value - 32) * 5/9 + 273.15;
            }
            break;
        case 'k':
            if ($to_unit === 'c') {
                return $value - 273.15;
            } elseif ($to_unit === 'f') {
                return ($value - 273.15) * 9/5 + 32;
            }
            break;
    }
    throw new Exception('Invalid temperature conversion');
}

/**
 * Perform batch conversions
 */
function performBatchConversion($data) {
    $batch_data = json_decode($data['batch_data'] ?? '[]', true);
    $results = [];
    
    foreach ($batch_data as $conversion) {
        try {
            $result = performUnitConversion($conversion);
            $result['input'] = $conversion;
            $result['success'] = true;
            $results[] = $result;
        } catch (Exception $e) {
            $results[] = [
                'success' => false,
                'error' => $e->getMessage(),
                'input' => $conversion
            ];
        }
    }
    
    return [
        'results' => $results,
        'count' => count($results)
    ];
}

/**
 * Save conversion to database
 */
function saveConversionHistory($data) {
    global $db;
    
    try {
        $query = "INSERT INTO unit_conversions (value, from_unit, to_unit, category, converted_value, conversion_factor, user_ip, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $db->executeQuery($query, [
            floatval($data['value']),
            $data['from_unit'],
            $data['to_unit'],
            $data['category'],
            floatval($data['converted_value']),
            floatval($data['conversion_factor']),
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        
        return $stmt !== false;
    } catch (Exception $e) {
        error_log("Error saving conversion: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all available units for a category
 */
function getUnitsByCategory($category) {
    $conversions = getUnitConversions();
    return isset($conversions[$category]) ? array_keys($conversions[$category]) : [];
}

/**
 * Get all available categories
 */
function getCategories() {
    return [
        'length' => 'Length',
        'area' => 'Area',
        'volume' => 'Volume',
        'mass' => 'Mass',
        'pressure' => 'Pressure',
        'temperature' => 'Temperature',
        'energy' => 'Energy',
        'power' => 'Power',
        'flow_rate' => 'Flow Rate',
        'velocity' => 'Velocity',
        'density' => 'Density',
        'thermal_conductivity' => 'Thermal Conductivity'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP Unit Converter - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .converter-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .converter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #2E7D32;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 15px;
            align-items: end;
        }
        
        .convert-icon {
            font-size: 24px;
            color: #2E7D32;
            text-align: center;
            padding: 10px;
        }
        
        .btn {
            background: #2E7D32;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 10px 10px 0;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #1B5E20;
        }
        
        .btn-secondary {
            background: #666;
        }
        
        .btn-secondary:hover {
            background: #555;
        }
        
        .btn-success {
            background: #4CAF50;
        }
        
        .btn-success:hover {
            background: #388E3C;
        }
        
        .result-display {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .result-value {
            font-size: 28px;
            font-weight: 600;
            color: #2E7D32;
            margin-bottom: 10px;
        }
        
        .result-units {
            font-size: 16px;
            color: #666;
        }
        
        .conversion-details {
            background: #e8f5e8;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 14px;
        }
        
        .batch-converter {
            background: #fff3e0;
            border: 1px solid #ffcc02;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .batch-input {
            width: 100%;
            min-height: 120px;
            font-family: monospace;
            font-size: 12px;
        }
        
        .history-item {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .history-conversion {
            flex: 1;
            font-family: monospace;
        }
        
        .history-time {
            font-size: 12px;
            color: #666;
        }
        
        .category-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .category-tab {
            padding: 8px 16px;
            background: #f0f0f0;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .category-tab:hover,
        .category-tab.active {
            background: #2E7D32;
            color: white;
        }
        
        .quick-conversions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .quick-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .quick-card:hover {
            border-color: #2E7D32;
            background: #f8f9fa;
        }
        
        .quick-card h4 {
            margin: 0 0 10px 0;
            color: #2E7D32;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .converter-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .convert-icon {
                transform: rotate(90deg);
            }
            
            .category-tabs {
                justify-content: center;
            }
            
            .quick-conversions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include AEC_ROOT . '/includes/header.php'; ?>
    
    <div class="converter-container">
        <div class="page-header">
            <h1>MEP Unit Converter</h1>
            <p>Comprehensive unit conversion utility for MEP engineering calculations</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="converter-grid">
            <!-- Single Unit Converter -->
            <div class="card">
                <div class="card-header">Unit Converter</div>
                
                <form method="POST" id="converter-form">
                    <input type="hidden" name="action" value="convert">
                    
                    <div class="form-group">
                        <label>Conversion Category</label>
                        <div class="category-tabs">
                            <?php foreach (getCategories() as $cat_id => $cat_name): ?>
                                <button type="button" class="category-tab" data-category="<?php echo $cat_id; ?>">
                                    <?php echo htmlspecialchars($cat_name); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="selected_category" name="category" value="length">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="from_value">Value</label>
                            <input type="number" id="from_value" name="value" step="any" required>
                        </div>
                        
                        <div class="convert-icon">→</div>
                        
                        <div class="form-group">
                            <label for="result_value">Result</label>
                            <input type="text" id="result_value" readonly>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="from_unit">From Unit</label>
                            <select id="from_unit" name="from_unit" required>
                                <option value="">Select unit</option>
                            </select>
                        </div>
                        
                        <div class="convert-icon">⇄</div>
                        
                        <div class="form-group">
                            <label for="to_unit">To Unit</label>
                            <select id="to_unit" name="to_unit" required>
                                <option value="">Select unit</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Convert</button>
                    <button type="button" class="btn btn-secondary" onclick="swapUnits()">Swap Units</button>
                    <button type="button" class="btn btn-success" onclick="saveConversion()">Save to History</button>
                </form>
                
                <?php if (isset($converted_value)): ?>
                    <div class="result-display">
                        <div class="result-value"><?php echo number_format($converted_value, 6); ?></div>
                        <div class="result-units">
                            <?php echo htmlspecialchars($_POST['from_value'] . ' ' . $_POST['from_unit']); ?> 
                            = 
                            <?php echo number_format($converted_value, 6) . ' ' . htmlspecialchars($_POST['to_unit']); ?>
                        </div>
                    </div>
                    
                    <div class="conversion-details">
                        <strong>Conversion Details:</strong>
                        <div class="details-grid">
                            <div><strong>Conversion Factor:</strong> <?php echo number_format($conversion_details['conversion_factor'], 8); ?></div>
                            <div><strong>Category:</strong> <?php echo htmlspecialchars(ucfirst($conversion_details['category'])); ?></div>
                            <div><strong>Timestamp:</strong> <?php echo $conversion_details['timestamp']; ?></div>
                            <div><strong>Base Units:</strong> <?php echo htmlspecialchars($conversion_details['category']); ?> base</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Batch Converter & Quick Conversions -->
            <div class="card">
                <div class="card-header">Batch & Quick Conversions</div>
                
                <div class="batch-converter">
                    <h4>Batch Converter</h4>
                    <p>Enter conversions in JSON format:</p>
                    <textarea class="batch-input" id="batch-input" placeholder='[
  {"value": 100, "from_unit": "mm", "to_unit": "in", "category": "length"},
  {"value": 50, "from_unit": "c", "to_unit": "f", "category": "temperature"}
]'></textarea>
                    
                    <button type="button" class="btn btn-success" onclick="performBatchConversion()">Batch Convert</button>
                    
                    <div id="batch-results"></div>
                </div>
                
                <h4>Quick Conversions</h4>
                <div class="quick-conversions">
                    <div class="quick-card" onclick="quickConvert(25.4, 'mm', 'in', 'length')">
                        <h4>mm to inches</h4>
                        <p>25.4 mm = 1 inch</p>
                    </div>
                    <div class="quick-card" onclick="quickConvert(1000, 'mm', 'm', 'length')">
                        <h4>mm to meters</h4>
                        <p>1000 mm = 1 meter</p>
                    </div>
                    <div class="quick-card" onclick="quickConvert(0, 'c', 'f', 'temperature')">
                        <h4>°C to °F</h4>
                        <p>°C × 9/5 + 32</p>
                    </div>
                    <div class="quick-card" onclick="quickConvert(100, 'kpa', 'psi', 'pressure')">
                        <h4>kPa to PSI</h4>
                        <p>100 kPa ≈ 14.5 PSI</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Conversion History -->
        <div class="card">
            <div class="card-header">Recent Conversions</div>
            
            <?php if (empty($conversion_history)): ?>
                <p style="color: #666; text-align: center; padding: 50px 20px;">
                    No conversion history found. Perform some conversions to see them here.
                </p>
            <?php else: ?>
                <?php foreach ($conversion_history as $conversion): ?>
                    <div class="history-item">
                        <div class="history-conversion">
                            <strong><?php echo number_format($conversion['value'], 4); ?></strong> 
                            <?php echo htmlspecialchars($conversion['from_unit']); ?> 
                            → 
                            <strong><?php echo number_format($conversion['converted_value'], 4); ?></strong> 
                            <?php echo htmlspecialchars($conversion['to_unit']); ?>
                            (<?php echo ucfirst($conversion['category']); ?>)
                        </div>
                        <div class="history-time">
                            <?php echo date('M j, Y g:i A', strtotime($conversion['created_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include AEC_ROOT . '/includes/footer.php'; ?>
    
    <script>
        let currentCategory = 'length';
        let lastConversion = null;
        
        // Initialize units for the selected category
        function updateUnits(category) {
            const units = getUnitsForCategory(category);
            const fromSelect = document.getElementById('from_unit');
            const toSelect = document.getElementById('to_unit');
            
            // Clear existing options
            fromSelect.innerHTML = '<option value="">Select unit</option>';
            toSelect.innerHTML = '<option value="">Select unit</option>';
            
            // Add unit options
            units.forEach(unit => {
                fromSelect.innerHTML += `<option value="${unit}">${unit}</option>`;
                toSelect.innerHTML += `<option value="${unit}">${unit}</option>`;
            });
            
            currentCategory = category;
        }
        
        function getUnitsForCategory(category) {
            const unitData = {
                length: ['mm', 'cm', 'm', 'km', 'in', 'ft', 'yd', 'mi'],
                area: ['mm2', 'cm2', 'm2', 'km2', 'in2', 'ft2', 'yd2', 'acre', 'hectare'],
                volume: ['mm3', 'cm3', 'm3', 'liter', 'ml', 'gal_us', 'gal_imp', 'ft3', 'in3'],
                mass: ['mg', 'g', 'kg', 'tonne', 'lb', 'oz', 'ton_us', 'ton_imp'],
                pressure: ['pa', 'kpa', 'mpa', 'bar', 'psi', 'atm', 'mmhg', 'torr', 'inh2o'],
                temperature: ['c', 'f', 'k'],
                energy: ['j', 'kj', 'mj', 'wh', 'kwh', 'mwh', 'btu', 'cal', 'kcal'],
                power: ['w', 'kw', 'mw', 'hp', 'btu_hr'],
                flow_rate: ['m3_s', 'm3_hr', 'm3_day', 'l_s', 'l_hr', 'l_day', 'gal_us_s', 'gal_us_min', 'ft3_s', 'ft3_min'],
                velocity: ['m_s', 'm_min', 'km_hr', 'ft_s', 'ft_min', 'mph', 'knots'],
                density: ['kg_m3', 'g_cm3', 'lb_ft3', 'lb_gal_us'],
                thermal_conductivity: ['w_mk', 'btu_ft_hr_f', 'cal_cm_s_c']
            };
            return unitData[category] || [];
        }
        
        // Category tab selection
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('selected_category').value = this.dataset.category;
                updateUnits(this.dataset.category);
            });
        });
        
        // Default select first category
        document.querySelector('.category-tab').classList.add('active');
        updateUnits('length');
        
        function swapUnits() {
            const fromUnit = document.getElementById('from_unit');
            const toUnit = document.getElementById('to_unit');
            const temp = fromUnit.value;
            fromUnit.value = toUnit.value;
            toUnit.value = temp;
            
            if (fromUnit.value && toUnit.value) {
                performConversion();
            }
        }
        
        function quickConvert(value, fromUnit, toUnit, category) {
            document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`[data-category="${category}"]`).classList.add('active');
            document.getElementById('selected_category').value = category;
            updateUnits(category);
            
            document.getElementById('from_value').value = value;
            document.getElementById('from_unit').value = fromUnit;
            document.getElementById('to_unit').value = toUnit;
            
            performConversion();
        }
        
        function performConversion() {
            const formData = new FormData(document.getElementById('converter-form'));
            formData.append('action', 'convert');
            
            fetch('unit-converter.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.converted_value !== undefined) {
                    document.getElementById('result_value').value = data.converted_value.toFixed(6);
                    lastConversion = {
                        ...formData,
                        converted_value: data.converted_value,
                        conversion_details: data.conversion_details
                    };
                } else {
                    alert('Conversion error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error performing conversion');
            });
        }
        
        function saveConversion() {
            if (!lastConversion) {
                alert('Please perform a conversion first');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'save_conversion');
            formData.append('value', document.getElementById('from_value').value);
            formData.append('from_unit', document.getElementById('from_unit').value);
            formData.append('to_unit', document.getElementById('to_unit').value);
            formData.append('category', currentCategory);
            formData.append('converted_value', lastConversion.converted_value);
            formData.append('conversion_factor', lastConversion.conversion_details.conversion_factor);
            
            fetch('unit-converter.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error saving conversion');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving conversion');
            });
        }
        
        function performBatchConversion() {
            const batchData = document.getElementById('batch-input').value;
            
            if (!batchData.trim()) {
                alert('Please enter batch conversion data');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'batch_convert');
            formData.append('batch_data', batchData);
            
            fetch('unit-converter.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.results) {
                    displayBatchResults(data.results);
                } else {
                    alert('Batch conversion error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error performing batch conversion');
            });
        }
        
        function displayBatchResults(results) {
            const resultsDiv = document.getElementById('batch-results');
            let html = '<h5>Batch Results:</h5>';
            
            results.forEach((result, index) => {
                if (result.success) {
                    html += `
                        <div style="background: #d4edda; padding: 10px; margin: 5px 0; border-radius: 5px;">
                            ${result.converted_value.toFixed(6)} ${result.conversion_details.to_unit} ✓
                        </div>
                    `;
                } else {
                    html += `
                        <div style="background: #f8d7da; padding: 10px; margin: 5px 0; border-radius: 5px;">
                            Error: ${result.error} ❌
                        </div>
                    `;
                }
            });
            
            resultsDiv.innerHTML = html;
        }
        
        // Auto-convert on form change
        document.getElementById('converter-form').addEventListener('change', function() {
            if (document.getElementById('from_value').value && 
                document.getElementById('from_unit').value && 
                document.getElementById('to_unit').value) {
                performConversion();
            }
        });
    </script>
</body>
</html>
