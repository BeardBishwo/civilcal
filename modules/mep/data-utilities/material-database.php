i<?php
/**
 * Material Database Module
 * Comprehensive MEP materials database with specifications, pricing, and vendor information
 * Supports equipment catalogs, pipe and fitting databases, electrical components, and more
 */

require_once '../../../app/Config/config.php';
require_once '../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Helpers/functions.php';

// Initialize database connection
$db = new Database();

// Get parameters
$category = $_GET['category'] ?? 'all';
$search = $_GET['search'] ?? '';
$manufacturer = $_GET['manufacturer'] ?? '';

// Handle form submissions
$message = '';
$message_type = '';

if ($_POST) {
    try {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add_material':
                $result = addMaterialToDatabase($_POST);
                if ($result) {
                    $message = 'Material added to database successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error adding material to database.';
                    $message_type = 'error';
                }
                break;
                
            case 'update_material':
                $result = updateMaterialInDatabase($_POST);
                if ($result) {
                    $message = 'Material updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating material.';
                    $message_type = 'error';
                }
                break;
                
            case 'delete_material':
                $result = deleteMaterialFromDatabase($_POST['material_id']);
                if ($result) {
                    $message = 'Material deleted successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error deleting material.';
                    $message_type = 'error';
                }
                break;
                
            case 'import_materials':
                $result = importMaterialsFromFile($_POST);
                if ($result['success']) {
                    $message = 'Materials imported successfully! ' . $result['count'] . ' items added.';
                    $message_type = 'success';
                } else {
                    $message = 'Error importing materials: ' . $result['error'];
                    $message_type = 'error';
                }
                break;
                
            case 'export_materials':
                $result = exportMaterialsToFile($_POST);
                if ($result['success']) {
                    $message = 'Materials exported successfully! File: ' . $result['filename'];
                    $message_type = 'success';
                } else {
                    $message = 'Error exporting materials: ' . $result['error'];
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get materials from database
$materials = getMaterialsFromDatabase($category, $search, $manufacturer);
$categories = getMaterialCategories();
$manufacturers = getManufacturersList();

/**
 * Get materials from database with filtering
 */
function getMaterialsFromDatabase($category, $search, $manufacturer) {
    global $db;
    
    try {
        $where_conditions = [];
        $params = [];
        
        if ($category !== 'all') {
            $where_conditions[] = "category = ?";
            $params[] = $category;
        }
        
        if (!empty($search)) {
            $where_conditions[] = "(name LIKE ? OR model LIKE ? OR description LIKE ?)";
            $search_param = '%' . $search . '%';
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        if (!empty($manufacturer)) {
            $where_conditions[] = "manufacturer = ?";
            $params[] = $manufacturer;
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $query = "SELECT * FROM material_database $where_clause ORDER BY category, manufacturer, name LIMIT 500";
        $stmt = $db->executeQuery($query, $params);
        
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (Exception $e) {
        error_log("Error fetching materials: " . $e->getMessage());
        return [];
    }
}

/**
 * Add new material to database
 */
function addMaterialToDatabase($data) {
    global $db;
    
    try {
        $material_data = [
            'name' => trim($data['name'] ?? ''),
            'model' => trim($data['model'] ?? ''),
            'category' => $data['category'] ?? '',
            'manufacturer' => trim($data['manufacturer'] ?? ''),
            'specifications' => json_encode([
                'dimensions' => $data['dimensions'] ?? '',
                'weight' => $data['weight'] ?? '',
                'material' => $data['material'] ?? '',
                'color' => $data['color'] ?? '',
                'voltage_rating' => $data['voltage_rating'] ?? '',
                'current_rating' => $data['current_rating'] ?? '',
                'pressure_rating' => $data['pressure_rating'] ?? '',
                'temperature_range' => $data['temperature_range'] ?? ''
            ]),
            'price' => floatval($data['price'] ?? 0),
            'unit' => $data['unit'] ?? '',
            'supplier' => trim($data['supplier'] ?? ''),
            'supplier_contact' => trim($data['supplier_contact'] ?? ''),
            'availability' => $data['availability'] ?? 'in_stock',
            'lead_time' => intval($data['lead_time'] ?? 0),
            'warranty' => trim($data['warranty'] ?? ''),
            'certifications' => trim($data['certifications'] ?? ''),
            'notes' => trim($data['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $query = "INSERT INTO material_database (name, model, category, manufacturer, specifications, price, unit, supplier, supplier_contact, availability, lead_time, warranty, certifications, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->executeQuery($query, array_values($material_data));
        
        return $stmt !== false;
    } catch (Exception $e) {
        error_log("Error adding material: " . $e->getMessage());
        return false;
    }
}

/**
 * Update existing material in database
 */
function updateMaterialInDatabase($data) {
    global $db;
    
    try {
        $material_id = intval($data['material_id']);
        
        $specifications = json_encode([
            'dimensions' => $data['dimensions'] ?? '',
            'weight' => $data['weight'] ?? '',
            'material' => $data['material'] ?? '',
            'color' => $data['color'] ?? '',
            'voltage_rating' => $data['voltage_rating'] ?? '',
            'current_rating' => $data['current_rating'] ?? '',
            'pressure_rating' => $data['pressure_rating'] ?? '',
            'temperature_range' => $data['temperature_range'] ?? ''
        ]);
        
        $query = "UPDATE material_database SET name = ?, model = ?, category = ?, manufacturer = ?, specifications = ?, price = ?, unit = ?, supplier = ?, supplier_contact = ?, availability = ?, lead_time = ?, warranty = ?, certifications = ?, notes = ?, updated_at = NOW() WHERE id = ?";
        
        $stmt = $db->executeQuery($query, [
            trim($data['name']),
            trim($data['model']),
            $data['category'],
            trim($data['manufacturer']),
            $specifications,
            floatval($data['price']),
            $data['unit'],
            trim($data['supplier']),
            trim($data['supplier_contact']),
            $data['availability'],
            intval($data['lead_time']),
            trim($data['warranty']),
            trim($data['certifications']),
            trim($data['notes']),
            $material_id
        ]);
        
        return $stmt !== false;
    } catch (Exception $e) {
        error_log("Error updating material: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete material from database
 */
function deleteMaterialFromDatabase($material_id) {
    global $db;
    
    try {
        $query = "DELETE FROM material_database WHERE id = ?";
        $stmt = $db->executeQuery($query, [$material_id]);
        
        return $stmt !== false;
    } catch (Exception $e) {
        error_log("Error deleting material: " . $e->getMessage());
        return false;
    }
}

/**
 * Import materials from file
 */
function importMaterialsFromFile($data) {
    try {
        $file_content = $data['import_data'] ?? '';
        
        if (empty($file_content)) {
            return ['success' => false, 'error' => 'No data provided'];
        }
        
        // Parse CSV or JSON data
        $materials = parseImportData($file_content);
        $imported_count = 0;
        
        foreach ($materials as $material) {
            if (addMaterialToDatabase($material)) {
                $imported_count++;
            }
        }
        
        return ['success' => true, 'count' => $imported_count];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Parse import data (CSV or JSON)
 */
function parseImportData($data) {
    // Try JSON first
    $json_data = json_decode($data, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        return $json_data;
    }
    
    // Parse CSV
    $lines = explode("\n", trim($data));
    $headers = str_getcsv(array_shift($lines));
    $materials = [];
    
    foreach ($lines as $line) {
        if (empty(trim($line))) continue;
        
        $values = str_getcsv($line);
        if (count($values) === count($headers)) {
            $material = [];
            foreach ($headers as $i => $header) {
                $material[$header] = $values[$i] ?? '';
            }
            $materials[] = $material;
        }
    }
    
    return $materials;
}

/**
 * Export materials to file
 */
function exportMaterialsToFile($data) {
    try {
        $category_filter = $data['export_category'] ?? 'all';
        $format = $data['export_format'] ?? 'csv';
        
        $materials = getMaterialsFromDatabase($category_filter, '', '');
        
        if ($format === 'json') {
            $export_data = json_encode($materials, JSON_PRETTY_PRINT);
            $filename = 'materials_export_' . date('Y-m-d_H-i-s') . '.json';
        } else {
            // CSV format
            $export_data = "Name,Model,Category,Manufacturer,Price,Unit,Supplier,Availability\n";
            foreach ($materials as $material) {
                $export_data .= sprintf(
                    '"%s","%s","%s","%s","%.2f","%s","%s","%s"' . "\n",
                    addslashes($material['name']),
                    addslashes($material['model']),
                    addslashes($material['category']),
                    addslashes($material['manufacturer']),
                    $material['price'],
                    addslashes($material['unit']),
                    addslashes($material['supplier']),
                    addslashes($material['availability'])
                );
            }
            $filename = 'materials_export_' . date('Y-m-d_H-i-s') . '.csv';
        }
        
        // Save to file
        $filepath = '../../exports/' . $filename;
        if (!is_dir('../../exports')) {
            mkdir('../../exports', 0755, true);
        }
        
        file_put_contents($filepath, $export_data);
        
        return ['success' => true, 'filename' => $filename, 'count' => count($materials)];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get material categories
 */
function getMaterialCategories() {
    return [
        'hvac_equipment' => 'HVAC Equipment',
        'ductwork' => 'Ductwork & Accessories',
        'pipes_fittings' => 'Pipes & Fittings',
        'valves_controls' => 'Valves & Controls',
        'pumps_fans' => 'Pumps & Fans',
        'electrical_panels' => 'Electrical Panels',
        'wiring_conduits' => 'Wiring & Conduits',
        'lighting' => 'Lighting',
        'fire_protection' => 'Fire Protection',
        'insulation' => 'Insulation',
        'instruments' => 'Instruments & Controls',
        'tools_supplies' => 'Tools & Supplies'
    ];
}

/**
 * Get manufacturers list
 */
function getManufacturersList() {
    return [
        'Carrier' => 'Carrier Corporation',
        'Trane' => 'Trane Technologies',
        'York' => 'Johnson Controls (York)',
        'Daikin' => 'Daikin Industries',
        'Lennox' => 'Lennox International',
        'Honeywell' => 'Honeywell International',
        'Siemens' => 'Siemens AG',
        'Schneider Electric' => 'Schneider Electric',
        'ABB' => 'ABB Ltd.',
        'Eaton' => 'Eaton Corporation',
        'Philips' => 'Signify (Philips)',
        'General Electric' => 'General Electric',
        'Johnson Controls' => 'Johnson Controls',
        'Johnson Controls' => 'Johnson Controls (Tyco)',
        'Viega' => 'Viega LLC',
        'Uponor' => 'Uponor Corporation',
        'Wirsbo' => 'Wirsbo (Uponor)',
        'American Standard' => 'American Standard Brands',
        'Kohler' => 'Kohler Co.',
        'Delta' => 'Delta Faucet Company'
    ];
}

/**
 * Get material specifications template
 */
function getSpecificationTemplate($category) {
    $templates = [
        'hvac_equipment' => [
            'dimensions' => '',
            'weight' => '',
            'cooling_capacity' => '',
            'heating_capacity' => '',
            'efficiency_rating' => '',
            'voltage' => '',
            'refrigerant_type' => '',
            'airflow_rate' => ''
        ],
        'pipes_fittings' => [
            'diameter' => '',
            'wall_thickness' => '',
            'material' => '',
            'pressure_rating' => '',
            'temperature_rating' => '',
            'connection_type' => '',
            'length' => '',
            'finish' => ''
        ],
        'electrical_panels' => [
            'voltage_rating' => '',
            'amperage' => '',
            'circuit_count' => '',
            'enclosure_type' => '',
            'mounting_type' => '',
            'ip_rating' => '',
            'compliance_standards' => '',
            'door_configuration' => ''
        ]
    ];
    
    return $templates[$category] ?? [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Database - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .database-container {
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
        
        .database-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
        
        .btn-danger {
            background: #f44336;
        }
        
        .btn-danger:hover {
            background: #d32f2f;
        }
        
        .material-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .material-card:hover {
            border-color: #2E7D32;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.1);
        }
        
        .material-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .material-title {
            font-size: 18px;
            font-weight: 600;
            color: #2E7D32;
            margin: 0;
        }
        
        .material-model {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .material-price {
            font-size: 20px;
            font-weight: 600;
            color: #2E7D32;
        }
        
        .material-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        
        .meta-item {
            font-size: 14px;
            color: #666;
        }
        
        .meta-label {
            font-weight: 500;
            color: #333;
        }
        
        .specifications-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .spec-category {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }
        
        .spec-title {
            font-weight: 600;
            color: #2E7D32;
            margin-bottom: 10px;
        }
        
        .spec-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .spec-item:last-child {
            border-bottom: none;
        }
        
        .availability-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .availability-in-stock {
            background: #d4edda;
            color: #155724;
        }
        
        .availability-limited {
            background: #fff3cd;
            color: #856404;
        }
        
        .availability-out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }
        
        .availability-discontinued {
            background: #e2e3e5;
            color: #6c757d;
        }
        
        .search-filters {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .import-export-panel {
            background: #fff3e0;
            border: 1px solid #ffcc02;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .tab-container {
            display: flex;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab.active {
            color: #2E7D32;
            border-bottom-color: #2E7D32;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .close-modal {
            font-size: 24px;
            cursor: pointer;
            color: #666;
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
            .database-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .material-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .material-meta {
                grid-template-columns: 1fr;
            }
            
            .specifications-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../../../themes/default/views/partials/header.php'; ?>
    
    <div class="database-container">
        <div class="page-header">
            <h1>MEP Material Database</h1>
            <p>Comprehensive materials database with specifications, pricing, and vendor information</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="database-grid">
            <!-- Materials List -->
            <div class="card">
                <div class="card-header">Materials Database</div>
                
                <!-- Search and Filters -->
                <div class="search-filters">
                    <form method="GET" id="search-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="search">Search Materials</label>
                                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, model, or description">
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" name="category">
                                    <option value="all">All Categories</option>
                                    <?php foreach ($categories as $cat_id => $cat_name): ?>
                                        <option value="<?php echo $cat_id; ?>" <?php echo $category === $cat_id ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="manufacturer">Manufacturer</label>
                            <select id="manufacturer" name="manufacturer">
                                <option value="">All Manufacturers</option>
                                <?php foreach ($manufacturers as $manuf_id => $manuf_name): ?>
                                    <option value="<?php echo $manuf_id; ?>" <?php echo $manufacturer === $manuf_id ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($manuf_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn">Search</button>
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                    </form>
                </div>
                
                <!-- Materials List -->
                <div id="materials-list">
                    <?php if (empty($materials)): ?>
                        <p style="color: #666; text-align: center; padding: 50px 20px;">
                            No materials found matching your criteria.
                        </p>
                    <?php else: ?>
                        <?php foreach ($materials as $material): ?>
                            <div class="material-card">
                                <div class="material-header">
                                    <div>
                                        <h3 class="material-title"><?php echo htmlspecialchars($material['name']); ?></h3>
                                        <div class="material-model">
                                            Model: <?php echo htmlspecialchars($material['model']); ?> | 
                                            Manufacturer: <?php echo htmlspecialchars($material['manufacturer']); ?>
                                        </div>
                                    </div>
                                    <div class="material-price">
                                        $<?php echo number_format($material['price'], 2); ?>
                                    </div>
                                </div>
                                
                                <div class="material-meta">
                                    <div class="meta-item">
                                        <span class="meta-label">Category:</span> 
                                        <?php echo htmlspecialchars($categories[$material['category']] ?? $material['category']); ?>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Unit:</span> 
                                        <?php echo htmlspecialchars($material['unit']); ?>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Supplier:</span> 
                                        <?php echo htmlspecialchars($material['supplier']); ?>
                                    </div>
                                    <div class="meta-item">
                                        <span class="availability-badge availability-<?php echo str_replace('_', '-', $material['availability']); ?>">
                                            <?php echo str_replace('_', ' ', ucfirst($material['availability'])); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <?php
                                $specifications = json_decode($material['specifications'], true);
                                if ($specifications && !empty($specifications)):
                                ?>
                                    <div class="specifications-grid">
                                        <div class="spec-category">
                                            <div class="spec-title">Specifications</div>
                                            <?php foreach ($specifications as $key => $value): ?>
                                                <?php if (!empty($value)): ?>
                                                    <div class="spec-item">
                                                        <span><?php echo ucwords(str_replace('_', ' ', $key)); ?>:</span>
                                                        <span><?php echo htmlspecialchars($value); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div style="display: flex; gap: 10px; margin-top: 15px;">
                                    <button type="button" class="btn btn-secondary" onclick="editMaterial(<?php echo $material['id']; ?>)">Edit</button>
                                    <button type="button" class="btn btn-danger" onclick="deleteMaterial(<?php echo $material['id']; ?>)">Delete</button>
                                    <button type="button" class="btn" onclick="viewDetails(<?php echo $material['id']; ?>)">Details</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actions Panel -->
            <div class="card">
                <div class="card-header">Actions</div>
                
                <div class="form-group">
                    <button type="button" class="btn btn-success" onclick="openAddModal()">Add New Material</button>
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn" onclick="openImportModal()">Import Materials</button>
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn btn-secondary" onclick="openExportModal()">Export Materials</button>
                </div>
                
                <hr style="margin: 20px 0;">
                
                <h4>Statistics</h4>
                <div class="form-group">
                    <strong>Total Materials:</strong> <?php echo count($materials); ?>
                </div>
                
                <div class="form-group">
                    <strong>Categories:</strong> <?php echo count($categories); ?>
                </div>
                
                <div class="form-group">
                    <strong>Manufacturers:</strong> <?php echo count($manufacturers); ?>
                </div>
                
                <hr style="margin: 20px 0;">
                
                <h4>Quick Actions</h4>
                <div class="form-group">
                    <button type="button" class="btn btn-secondary" onclick="generateBOQ()">Generate BOQ</button>
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn btn-secondary" onclick="comparePrices()">Price Comparison</button>
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn btn-secondary" onclick="checkAvailability()">Availability Check</button>
                </div>
            </div>
        </div>
        
        <!-- Import/Export Panel -->
        <div class="card">
            <div class="card-header">Import/Export Tools</div>
            
            <div class="import-export-panel">
                <h4>Bulk Import Materials</h4>
                <p>Import materials from CSV or JSON format. Expected columns: name, model, category, manufacturer, price, unit, supplier</p>
                
                <textarea class="batch-input" id="import-data" placeholder="Paste CSV or JSON data here..."></textarea>
                
                <button type="button" class="btn btn-success" onclick="importMaterials()">Import Materials</button>
            </div>
            
            <div class="import-export-panel">
                <h4>Export Materials</h4>
                <form id="export-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="export_category">Category Filter</label>
                            <select id="export_category" name="export_category">
                                <option value="all">All Categories</option>
                                <?php foreach ($categories as $cat_id => $cat_name): ?>
                                    <option value="<?php echo $cat_id; ?>"><?php echo htmlspecialchars($cat_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="export_format">Format</label>
                            <select id="export_format" name="export_format">
                                <option value="csv">CSV</option>
                                <option value="json">JSON</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="button" class="btn" onclick="exportMaterials()">Export Materials</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Material Modal -->
    <div id="material-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Add New Material</h2>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            
            <form id="material-form">
                <input type="hidden" id="material_id" name="material_id">
                <input type="hidden" id="form_action" name="action">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="material_name">Material Name *</label>
                        <input type="text" id="material_name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="material_model">Model</label>
                        <input type="text" id="material_model" name="model">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="material_category">Category *</label>
                        <select id="material_category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat_id => $cat_name): ?>
                                <option value="<?php echo $cat_id; ?>"><?php echo htmlspecialchars($cat_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="material_manufacturer">Manufacturer *</label>
                        <select id="material_manufacturer" name="manufacturer" required>
                            <option value="">Select Manufacturer</option>
                            <?php foreach ($manufacturers as $manuf_id => $manuf_name): ?>
                                <option value="<?php echo $manuf_id; ?>"><?php echo htmlspecialchars($manuf_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="material_price">Price *</label>
                        <input type="number" id="material_price" name="price" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="material_unit">Unit *</label>
                        <select id="material_unit" name="unit" required>
                            <option value="">Select Unit</option>
                            <option value="piece">Piece</option>
                            <option value="meter">Meter</option>
                            <option value="kilogram">Kilogram</option>
                            <option value="liter">Liter</option>
                            <option value="square_meter">Square Meter</option>
                            <option value="cubic_meter">Cubic Meter</option>
                            <option value="set">Set</option>
                            <option value="package">Package</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="material_supplier">Supplier</label>
                        <input type="text" id="material_supplier" name="supplier">
                    </div>
                    
                    <div class="form-group">
                        <label for="material_supplier_contact">Supplier Contact</label>
                        <input type="text" id="material_supplier_contact" name="supplier_contact">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="material_availability">Availability</label>
                        <select id="material_availability" name="availability">
                            <option value="in_stock">In Stock</option>
                            <option value="limited">Limited</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="discontinued">Discontinued</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="material_lead_time">Lead Time (days)</label>
                        <input type="number" id="material_lead_time" name="lead_time" min="0">
                    </div>
                </div>
                
                <h4>Specifications</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="material_dimensions">Dimensions</label>
                        <input type="text" id="material_dimensions" name="dimensions" placeholder="L x W x H">
                    </div>
                    
                    <div class="form-group">
                        <label for="material_weight">Weight</label>
                        <input type="text" id="material_weight" name="weight" placeholder="kg">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="material_material">Material</label>
                        <input type="text" id="material_material" name="material" placeholder="e.g., Steel, Copper, PVC">
                    </div>
                    
                    <div class="form-group">
                        <label for="material_color">Color</label>
                        <input type="text" id="material_color" name="color">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="material_voltage_rating">Voltage Rating</label>
                        <input type="text" id="material_voltage_rating" name="voltage_rating" placeholder="e.g., 230V, 480V">
                    </div>
                    
                    <div class="form-group">
                        <label for="material_current_rating">Current Rating</label>
                        <input type="text" id="material_current_rating" name="current_rating" placeholder="e.g., 16A, 32A">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="material_pressure_rating">Pressure Rating</label>
                        <input type="text" id="material_pressure_rating" name="pressure_rating" placeholder="e.g., 10 bar, 150 PSI">
                    </div>
                    
                    <div class="form-group">
                        <label for="material_temperature_range">Temperature Range</label>
                        <input type="text" id="material_temperature_range" name="temperature_range" placeholder="e.g., -20°C to 80°C">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="material_warranty">Warranty</label>
                    <input type="text" id="material_warranty" name="warranty" placeholder="e.g., 2 years, 5 years">
                </div>
                
                <div class="form-group">
                    <label for="material_certifications">Certifications</label>
                    <input type="text" id="material_certifications" name="certifications" placeholder="e.g., UL Listed, CE Marked">
                </div>
                
                <div class="form-group">
                    <label for="material_notes">Notes</label>
                    <textarea id="material_notes" name="notes" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn">Save Material</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <?php include '../../../themes/default/views/partials/footer.php'; ?>
    
    <script>
        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add New Material';
            document.getElementById('form_action').value = 'add_material';
            document.getElementById('material-form').reset();
            document.getElementById('material-modal').style.display = 'block';
        }
        
        function editMaterial(materialId) {
            // Fetch material data and populate form
            fetch(`material-database.php?action=get_material&id=${materialId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const material = data.material;
                        document.getElementById('modal-title').textContent = 'Edit Material';
                        document.getElementById('form_action').value = 'update_material';
                        document.getElementById('material_id').value = material.id;
                        
                        // Populate form fields
                        Object.keys(material).forEach(key => {
                            const element = document.getElementById(`material_${key}`);
                            if (element) {
                                if (key === 'specifications' && typeof material[key] === 'object') {
                                    Object.keys(material[key]).forEach(specKey => {
                                        const specElement = document.getElementById(`material_${specKey}`);
                                        if (specElement) {
                                            specElement.value = material[key][specKey] || '';
                                        }
                                    });
                                } else {
                                    element.value = material[key] || '';
                                }
                            }
                        });
                        
                        document.getElementById('material-modal').style.display = 'block';
                    }
                });
        }
        
        function deleteMaterial(materialId) {
            if (confirm('Are you sure you want to delete this material?')) {
                const formData = new FormData();
                formData.append('action', 'delete_material');
                formData.append('material_id', materialId);
                
                fetch('material-database.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting material: ' + data.error);
                    }
                });
            }
        }
        
        function viewDetails(materialId) {
            window.open(`material-database.php?action=view_details&id=${materialId}`, '_blank');
        }
        
        function closeModal() {
            document.getElementById('material-modal').style.display = 'none';
        }
        
        function resetFilters() {
            window.location.href = 'material-database.php';
        }
        
        function importMaterials() {
            const importData = document.getElementById('import-data').value;
            
            if (!importData.trim()) {
                alert('Please enter data to import');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'import_materials');
            formData.append('import_data', importData);
            
            fetch('material-database.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Successfully imported ${data.count} materials`);
                    location.reload();
                } else {
                    alert('Error importing materials: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error importing materials');
            });
        }
        
        function exportMaterials() {
            const formData = new FormData(document.getElementById('export-form'));
            formData.append('action', 'export_materials');
            
            fetch('material-database.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Exported ${data.count} materials. File: ${data.filename}`);
                    // Download link would be generated here
                } else {
                    alert('Error exporting materials: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error exporting materials');
            });
        }
        
        // Form submission
        document.getElementById('material-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('material-database.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Material saved successfully!');
                    closeModal();
                    location.reload();
                } else {
                    alert('Error saving material: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving material');
            });
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('material-modal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>



