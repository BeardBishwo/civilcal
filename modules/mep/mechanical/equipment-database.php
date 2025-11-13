<?php
/**
 * HVAC Equipment Database
 * Store AHU, FCU, chiller, pump data for reuse
 * 
 * @package MEP
 * @subpackage Mechanical
 */
$base = defined('APP_BASE') ? rtrim(APP_BASE, '/') : '/aec-calculator';
require_once rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . $base . '/modules/mep/bootstrap.php';
// header is included later in the file — use AEC_ROOT for consistent path

// Equipment database array
$equipment_database = [
    'ahu' => [
        ['model' => 'TRANE Voyager 20-50 Ton', 'capacity' => 50, 'cooling_eer' => 12.5, 'heating_eer' => 11.8, 'airflow' => 20000],
        ['model' => 'CARRIER 30XA 30-150 Ton', 'capacity' => 75, 'cooling_eer' => 13.2, 'heating_eer' => 12.0, 'airflow' => 30000],
        ['model' => 'YORK YVAA 20-400 Ton', 'capacity' => 100, 'cooling_eer' => 14.1, 'heating_eer' => 12.5, 'airflow' => 40000],
        ['model' => 'DAIKIN Pathfinder 20-60 Ton', 'capacity' => 60, 'cooling_eer' => 13.8, 'heating_eer' => 12.3, 'airflow' => 24000],
        ['model' => 'LENNOX CRAV 20-50 Ton', 'capacity' => 40, 'cooling_eer' => 12.0, 'heating_eer' => 11.5, 'airflow' => 16000]
    ],
    'fcu' => [
        ['model' => 'CARRIER 42CN 1-5 Ton', 'capacity' => 3, 'cooling_eer' => 11.2, 'heating_eer' => 10.8, 'airflow' => 1200],
        ['model' => 'TRANE UCEC 2-8 Ton', 'capacity' => 5, 'cooling_eer' => 10.8, 'heating_eer' => 10.5, 'airflow' => 2000],
        ['model' => 'YORK ZF 1-6 Ton', 'capacity' => 4, 'cooling_eer' => 11.5, 'heating_eer' => 11.0, 'airflow' => 1600],
        ['model' => 'DAIKIN FDX 1-4 Ton', 'capacity' => 2.5, 'cooling_eer' => 10.5, 'heating_eer' => 10.2, 'airflow' => 1000],
        ['model' => 'LENNOX FCV 2-5 Ton', 'capacity' => 3.5, 'cooling_eer' => 11.8, 'heating_eer' => 11.2, 'airflow' => 1400]
    ],
    'chiller' => [
        ['model' => 'CARRIER 19XRV 100-500 Ton', 'capacity' => 200, 'cooling_eer' => 16.2, 'ipmv' => 0.6, 'water_flow' => 400],
        ['model' => 'TRANE RTAC 100-1500 Ton', 'capacity' => 300, 'cooling_eer' => 17.1, 'ipmv' => 0.58, 'water_flow' => 600],
        ['model' => 'YORK YK 100-3000 Ton', 'capacity' => 500, 'cooling_eer' => 18.5, 'ipmv' => 0.55, 'water_flow' => 1000],
        ['model' => 'DAIKIN EWAQ 100-800 Ton', 'capacity' => 150, 'cooling_eer' => 15.8, 'ipmv' => 0.62, 'water_flow' => 300],
        ['model' => 'JOHNSON CONTROLS PUV 100-600 Ton', 'capacity' => 250, 'cooling_eer' => 16.8, 'ipmv' => 0.59, 'water_flow' => 500]
    ],
    'pump' => [
        ['model' => 'BELL & GOSSETET 1510', 'capacity' => 10, 'head' => 100, 'efficiency' => 0.85, 'power' => 5],
        ['model' => 'GRUNDFOS CR 45-2', 'capacity' => 15, 'head' => 120, 'efficiency' => 0.87, 'power' => 7.5],
        ['model' => 'WILO CR 90-1', 'capacity' => 25, 'head' => 150, 'efficiency' => 0.89, 'power' => 12.5],
        ['model' => 'KSB Etanorm 080', 'capacity' => 40, 'head' => 200, 'efficiency' => 0.88, 'power' => 20],
        ['model' => 'EBARA CDX 120/36', 'capacity' => 8, 'head' => 80, 'efficiency' => 0.82, 'power' => 3]
    ]
];

// Handle search and filtering
$search_term = $_POST['search_term'] ?? '';
$category = $_POST['category'] ?? 'all';
$capacity_range = $_POST['capacity_range'] ?? '';

$filtered_equipment = $equipment_database;

if (!empty($search_term)) {
    foreach ($filtered_equipment as $cat => &$equipment_list) {
        $equipment_list = array_filter($equipment_list, function($item) use ($search_term) {
            return stripos($item['model'], $search_term) !== false;
        });
    }
}

if ($category !== 'all') {
    $filtered_equipment = [$category => $filtered_equipment[$category] ?? []];
}

if (!empty($capacity_range)) {
    $range_parts = explode('-', $capacity_range);
    $min_capacity = floatval($range_parts[0]);
    $max_capacity = floatval($range_parts[1] ?? PHP_FLOAT_MAX);
    
    foreach ($filtered_equipment as $cat => &$equipment_list) {
        $equipment_list = array_filter($equipment_list, function($item) use ($min_capacity, $max_capacity) {
            return $item['capacity'] >= $min_capacity && $item['capacity'] <= $max_capacity;
        });
    }
}

// Equipment selection and comparison
if (isset($_POST['action']) && $_POST['action'] === 'compare') {
    $selected_items = $_POST['selected_equipment'] ?? [];
    
    $comparison_data = [];
    foreach ($selected_items as $item_id) {
        $item_parts = explode('_', $item_id);
        $cat = $item_parts[0];
        $idx = intval($item_parts[1]);
        
        if (isset($equipment_database[$cat][$idx])) {
            $comparison_data[] = [
                'category' => $cat,
                'data' => $equipment_database[$cat][$idx]
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HVAC Equipment Database - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <style>
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .search-section { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px; }
        .equipment-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .equipment-card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .equipment-card h4 { color: #007bff; margin-top: 0; }
        .spec-item { margin-bottom: 8px; padding: 5px 0; border-bottom: 1px solid #f0f0f0; }
        .spec-label { font-weight: bold; color: #555; }
        .category-tabs { display: flex; margin-bottom: 20px; background: #f8f9fa; border-radius: 8px; padding: 5px; }
        .category-tab { padding: 10px 20px; border-radius: 6px; cursor: pointer; transition: all 0.3s; }
        .category-tab.active { background: #007bff; color: white; }
        .category-tab:hover { background: #0056b3; color: white; }
        .results { background: #f8f9fa; padding: 25px; border-radius: 8px; border-left: 5px solid #28a745; }
        .results h3 { color: #28a745; margin-top: 0; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .btn:hover { background: #0056b3; }
        .checkbox-container { margin: 10px 0; }
        .comparison-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .comparison-table th, .comparison-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .comparison-table th { background: #007bff; color: white; }
    </style>
</head>
<body>
    <?php include AEC_ROOT . '/themes/default/views/partials/header.php'; ?>
    
    <div class="container">
        <h1>HVAC Equipment Database</h1>
        
        <form method="POST" class="search-section">
            <div class="category-tabs">
                <div class="category-tab <?= $category === 'all' ? 'active' : '' ?>" onclick="filterCategory('all')">All Equipment</div>
                <div class="category-tab <?= $category === 'ahu' ? 'active' : '' ?>" onclick="filterCategory('ahu')">Air Handling Units</div>
                <div class="category-tab <?= $category === 'fcu' ? 'active' : '' ?>" onclick="filterCategory('fcu')">Fan Coil Units</div>
                <div class="category-tab <?= $category === 'chiller' ? 'active' : '' ?>" onclick="filterCategory('chiller')">Chillers</div>
                <div class="category-tab <?= $category === 'pump' ? 'active' : '' ?>" onclick="filterCategory('pump')">Pumps</div>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 20px; align-items: end;">
                <div class="input-group">
                    <label>Search Equipment:</label>
                    <input type="text" name="search_term" value="<?= htmlspecialchars($search_term) ?>" placeholder="e.g., Carrier, Voyager, CR...">
                </div>
                
                <div class="input-group">
                    <label>Capacity Range:</label>
                    <select name="capacity_range">
                        <option value="">All Capacities</option>
                        <option value="0-10" <?= $capacity_range === '0-10' ? 'selected' : '' ?>>0-10</option>
                        <option value="10-50" <?= $capacity_range === '10-50' ? 'selected' : '' ?>>10-50</option>
                        <option value="50-100" <?= $capacity_range === '50-100' ? 'selected' : '' ?>>50-100</option>
                        <option value="100-500" <?= $capacity_range === '100-500' ? 'selected' : '' ?>>100-500</option>
                        <option value="500-1000" <?= $capacity_range === '500-1000' ? 'selected' : '' ?>>500-1000</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn">Search Equipment</button>
                </div>
            </div>
            
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>" id="category-input">
        </form>
        
        <form method="POST">
            <input type="hidden" name="search_term" value="<?= htmlspecialchars($search_term) ?>">
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
            <input type="hidden" name="capacity_range" value="<?= htmlspecialchars($capacity_range) ?>">
            <input type="hidden" name="action" value="compare">
            
            <?php foreach ($filtered_equipment as $cat => $equipment_list): ?>
                <?php if (!empty($equipment_list)): ?>
                    <h3><?= strtoupper($cat) ?> Equipment</h3>
                    <div class="equipment-grid">
                        <?php foreach ($equipment_list as $idx => $item): ?>
                            <?php 
                            $item_id = $cat . '_' . $idx;
                            $is_selected = in_array($item_id, $selected_items ?? []);
                            ?>
                            <div class="equipment-card">
                                <div class="checkbox-container">
                                    <input type="checkbox" name="selected_equipment[]" value="<?= $item_id ?>" <?= $is_selected ? 'checked' : '' ?>>
                                    <strong>Select for Comparison</strong>
                                </div>
                                
                                <h4><?= htmlspecialchars($item['model']) ?></h4>
                                
                                <div class="spec-item">
                                    <span class="spec-label">Capacity:</span> 
                                    <?= $item['capacity'] ?> Tons/HP
                                </div>
                                
                                <?php if (isset($item['cooling_eer'])): ?>
                                    <div class="spec-item">
                                        <span class="spec-label">Cooling EER:</span> 
                                        <?= $item['cooling_eer'] ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($item['heating_eer'])): ?>
                                    <div class="spec-item">
                                        <span class="spec-label">Heating EER:</span> 
                                        <?= $item['heating_eer'] ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($item['ipmv'])): ?>
                                    <div class="spec-item">
                                        <span class="spec-label">IPMV:</span> 
                                        <?= $item['ipmv'] ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($item['airflow'])): ?>
                                    <div class="spec-item">
                                        <span class="spec-label">Airflow:</span> 
                                        <?= $item['airflow'] ?> CFM
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($item['water_flow'])): ?>
                                    <div class="spec-item">
                                        <span class="spec-label">Water Flow:</span> 
                                        <?= $item['water_flow'] ?> GPM
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($item['head'])): ?>
                                    <div class="spec-item">
                                        <span class="spec-label">Head:</span> 
                                        <?= $item['head'] ?> ft
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($item['efficiency'])): ?>
                                    <div class="spec-item">
                                        <span class="spec-label">Efficiency:</span> 
                                        <?= ($item['efficiency'] * 100) ?>%
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($item['power'])): ?>
                                    <div class="spec-item">
                                        <span class="spec-label">Power:</span> 
                                        <?= $item['power'] ?> HP
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <div style="margin-top: 30px; text-align: center;">
                <button type="submit" class="btn" style="padding: 15px 30px; font-size: 16px;">
                    Compare Selected Equipment
                </button>
            </div>
        </form>
        
        <?php if (isset($comparison_data) && !empty($comparison_data)): ?>
            <div class="results">
                <h3>Equipment Comparison</h3>
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Model</th>
                            <th>Capacity</th>
                            <th>Efficiency</th>
                            <th>Power</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comparison_data as $item): ?>
                            <tr>
                                <td><?= strtoupper($item['category']) ?></td>
                                <td><?= htmlspecialchars($item['data']['model']) ?></td>
                                <td><?= $item['data']['capacity'] ?></td>
                                <td>
                                    <?php 
                                    if (isset($item['data']['cooling_eer'])) {
                                        echo "EER: " . $item['data']['cooling_eer'];
                                    } elseif (isset($item['data']['efficiency'])) {
                                        echo ($item['data']['efficiency'] * 100) . "%";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?= isset($item['data']['power']) ? $item['data']['power'] . ' HP' : 'N/A' ?>
                                </td>
                                <td>
                                    <?php
                                    if ($item['data']['capacity'] > 100) {
                                        echo "Industrial grade";
                                    } elseif ($item['data']['capacity'] < 5) {
                                        echo "Residential/Light commercial";
                                    } else {
                                        echo "Commercial grade";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function filterCategory(category) {
            document.getElementById('category-input').value = category;
            document.querySelector('.search-section form').submit();
        }
    </script>
</body>
</html>

