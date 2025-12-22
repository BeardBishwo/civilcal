<?php
/**
 * Vendor Pricing System for MEP Coordination Suite
 * 
 * Comprehensive vendor pricing management for MEP materials
 * with cost tracking, comparison tools, and purchase order generation
 */

session_start();
require_once '../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Helpers/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$db = new Database();
$user_id = $_SESSION['user_id'];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'get_vendors':
                echo json_encode(getVendors($db));
                break;
            case 'add_vendor':
                echo json_encode(addVendor($_POST, $db, $user_id));
                break;
            case 'update_vendor':
                echo json_encode(updateVendor($_POST, $db, $user_id));
                break;
            case 'delete_vendor':
                echo json_encode(deleteVendor($_POST['vendor_id'], $db));
                break;
            case 'get_materials':
                echo json_encode(getMaterials($_POST['vendor_id'], $db));
                break;
            case 'add_material_price':
                echo json_encode(addMaterialPrice($_POST, $db, $user_id));
                break;
            case 'update_material_price':
                echo json_encode(updateMaterialPrice($_POST, $db, $user_id));
                break;
            case 'delete_material_price':
                echo json_encode(deleteMaterialPrice($_POST['price_id'], $db));
                break;
            case 'compare_prices':
                echo json_encode(comparePrices($_POST, $db));
                break;
            case 'generate_po':
                echo json_encode(generatePurchaseOrder($_POST, $db, $user_id));
                break;
            case 'get_price_history':
                echo json_encode(getPriceHistory($_POST['material_id'], $db));
                break;
            case 'search_materials':
                echo json_encode(searchMaterials($_POST['query'], $db));
                break;
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

function getVendors($db) {
    $sql = "SELECT * FROM vendors WHERE status = 'active' ORDER BY vendor_name";
    $result = $db->executeQuery($sql);
    
    $vendors = [];
    if ($result) {
        while ($row = $result->fetch()) {
            // Get material count for each vendor
            $material_count_sql = "SELECT COUNT(*) as count FROM vendor_prices WHERE vendor_id = ? AND status = 'active'";
            $material_result = $db->executeQuery($material_count_sql, [$row['id']]);
            $material_count = $material_result ? $material_result->fetch()['count'] : 0;
            
            $vendors[] = [
                'id' => $row['id'],
                'vendor_name' => $row['vendor_name'],
                'contact_person' => $row['contact_person'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'address' => $row['address'],
                'specialization' => $row['specialization'],
                'rating' => floatval($row['rating']),
                'material_count' => $material_count,
                'created_at' => $row['created_at']
            ];
        }
    }
    
    return ['success' => true, 'vendors' => $vendors];
}

function addVendor($data, $db, $user_id) {
    $vendor_name = trim($data['vendor_name'] ?? '');
    $contact_person = trim($data['contact_person'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $address = trim($data['address'] ?? '');
    $specialization = trim($data['specialization'] ?? '');
    $rating = floatval($data['rating'] ?? 0);
    
    if (empty($vendor_name)) {
        throw new Exception('Vendor name is required');
    }
    
    $sql = "INSERT INTO vendors (vendor_name, contact_person, email, phone, address, specialization, rating, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $params = [$vendor_name, $contact_person, $email, $phone, $address, $specialization, $rating, $user_id];
    $result = $db->executeQuery($sql, $params);
    
    if ($result) {
        $vendor_id = $db->lastInsertId();
        return ['success' => true, 'vendor_id' => $vendor_id];
    } else {
        throw new Exception('Failed to add vendor');
    }
}

function updateVendor($data, $db, $user_id) {
    $vendor_id = intval($data['vendor_id'] ?? 0);
    $vendor_name = trim($data['vendor_name'] ?? '');
    $contact_person = trim($data['contact_person'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $address = trim($data['address'] ?? '');
    $specialization = trim($data['specialization'] ?? '');
    $rating = floatval($data['rating'] ?? 0);
    
    if (empty($vendor_name) || $vendor_id <= 0) {
        throw new Exception('Valid vendor name and ID are required');
    }
    
    $sql = "UPDATE vendors SET vendor_name = ?, contact_person = ?, email = ?, phone = ?, address = ?, specialization = ?, rating = ?, updated_by = ?, updated_at = NOW() 
            WHERE id = ?";
    
    $params = [$vendor_name, $contact_person, $email, $phone, $address, $specialization, $rating, $user_id, $vendor_id];
    $result = $db->executeQuery($sql, $params);
    
    if ($result) {
        return ['success' => true];
    } else {
        throw new Exception('Failed to update vendor');
    }
}

function deleteVendor($vendor_id, $db) {
    if ($vendor_id <= 0) {
        throw new Exception('Invalid vendor ID');
    }
    
    // Soft delete - update status
    $sql = "UPDATE vendors SET status = 'deleted', updated_at = NOW() WHERE id = ?";
    $result = $db->executeQuery($sql, [$vendor_id]);
    
    if ($result) {
        return ['success' => true];
    } else {
        throw new Exception('Failed to delete vendor');
    }
}

function getMaterials($vendor_id, $db) {
    if ($vendor_id <= 0) {
        throw new Exception('Invalid vendor ID');
    }
    
    $sql = "SELECT vp.*, v.vendor_name, 
                   CASE WHEN vp.price IS NOT NULL THEN vp.price ELSE 0 END as current_price,
                   CASE WHEN vp.last_updated IS NOT NULL THEN vp.last_updated ELSE vp.created_at END as price_updated
            FROM vendor_prices vp 
            JOIN vendors v ON vp.vendor_id = v.id 
            WHERE vp.vendor_id = ? AND vp.status = 'active'
            ORDER BY vp.material_name";
    
    $result = $db->executeQuery($sql, [$vendor_id]);
    
    $materials = [];
    if ($result) {
        while ($row = $result->fetch()) {
            $materials[] = [
                'id' => $row['id'],
                'vendor_id' => $row['vendor_id'],
                'vendor_name' => $row['vendor_name'],
                'material_name' => $row['material_name'],
                'category' => $row['category'],
                'specification' => $row['specification'],
                'unit' => $row['unit'],
                'price' => floatval($row['current_price']),
                'bulk_discount' => $row['bulk_discount'],
                'minimum_order' => $row['minimum_order'],
                'lead_time' => $row['lead_time'],
                'warranty' => $row['warranty'],
                'availability' => $row['availability'],
                'quality_rating' => floatval($row['quality_rating']),
                'price_updated' => $row['price_updated'],
                'remarks' => $row['remarks']
            ];
        }
    }
    
    return ['success' => true, 'materials' => $materials];
}

function addMaterialPrice($data, $db, $user_id) {
    $vendor_id = intval($data['vendor_id'] ?? 0);
    $material_name = trim($data['material_name'] ?? '');
    $category = trim($data['category'] ?? '');
    $specification = trim($data['specification'] ?? '');
    $unit = trim($data['unit'] ?? '');
    $price = floatval($data['price'] ?? 0);
    $bulk_discount = trim($data['bulk_discount'] ?? '');
    $minimum_order = intval($data['minimum_order'] ?? 0);
    $lead_time = trim($data['lead_time'] ?? '');
    $warranty = trim($data['warranty'] ?? '');
    $availability = trim($data['availability'] ?? '');
    $quality_rating = floatval($data['quality_rating'] ?? 0);
    $remarks = trim($data['remarks'] ?? '');
    
    if ($vendor_id <= 0 || empty($material_name)) {
        throw new Exception('Vendor ID and material name are required');
    }
    
    $sql = "INSERT INTO vendor_prices (vendor_id, material_name, category, specification, unit, price, bulk_discount, minimum_order, lead_time, warranty, availability, quality_rating, remarks, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $params = [$vendor_id, $material_name, $category, $specification, $unit, $price, $bulk_discount, $minimum_order, $lead_time, $warranty, $availability, $quality_rating, $remarks, $user_id];
    $result = $db->executeQuery($sql, $params);
    
    if ($result) {
        $price_id = $db->lastInsertId();
        return ['success' => true, 'price_id' => $price_id];
    } else {
        throw new Exception('Failed to add material price');
    }
}

function updateMaterialPrice($data, $db, $user_id) {
    $price_id = intval($data['price_id'] ?? 0);
    $material_name = trim($data['material_name'] ?? '');
    $category = trim($data['category'] ?? '');
    $specification = trim($data['specification'] ?? '');
    $unit = trim($data['unit'] ?? '');
    $price = floatval($data['price'] ?? 0);
    $bulk_discount = trim($data['bulk_discount'] ?? '');
    $minimum_order = intval($data['minimum_order'] ?? 0);
    $lead_time = trim($data['lead_time'] ?? '');
    $warranty = trim($data['warranty'] ?? '');
    $availability = trim($data['availability'] ?? '');
    $quality_rating = floatval($data['quality_rating'] ?? 0);
    $remarks = trim($data['remarks'] ?? '');
    
    if ($price_id <= 0 || empty($material_name)) {
        throw new Exception('Price ID and material name are required');
    }
    
    $sql = "UPDATE vendor_prices SET material_name = ?, category = ?, specification = ?, unit = ?, price = ?, bulk_discount = ?, minimum_order = ?, lead_time = ?, warranty = ?, availability = ?, quality_rating = ?, remarks = ?, last_updated = NOW(), updated_by = ? 
            WHERE id = ?";
    
    $params = [$material_name, $category, $specification, $unit, $price, $bulk_discount, $minimum_order, $lead_time, $warranty, $availability, $quality_rating, $remarks, $user_id, $price_id];
    $result = $db->executeQuery($sql, $params);
    
    if ($result) {
        return ['success' => true];
    } else {
        throw new Exception('Failed to update material price');
    }
}

function deleteMaterialPrice($price_id, $db) {
    if ($price_id <= 0) {
        throw new Exception('Invalid price ID');
    }
    
    // Soft delete
    $sql = "UPDATE vendor_prices SET status = 'deleted', updated_at = NOW() WHERE id = ?";
    $result = $db->executeQuery($sql, [$price_id]);
    
    if ($result) {
        return ['success' => true];
    } else {
        throw new Exception('Failed to delete material price');
    }
}

function comparePrices($data, $db) {
    $material_name = trim($data['material_name'] ?? '');
    $category = trim($data['category'] ?? '');
    
    if (empty($material_name)) {
        throw new Exception('Material name is required for comparison');
    }
    
    $sql = "SELECT vp.*, v.vendor_name, v.rating as vendor_rating,
                   CASE WHEN vp.bulk_discount IS NOT NULL THEN 
                       CASE 
                           WHEN vp.bulk_discount LIKE '%10%' THEN vp.price * 0.9
                           WHEN vp.bulk_discount LIKE '%15%' THEN vp.price * 0.85
                           WHEN vp.bulk_discount LIKE '%20%' THEN vp.price * 0.8
                           WHEN vp.bulk_discount LIKE '%25%' THEN vp.price * 0.75
                           ELSE vp.price
                       END
                   ELSE vp.price
               END as discounted_price
            FROM vendor_prices vp 
            JOIN vendors v ON vp.vendor_id = v.id 
            WHERE vp.material_name LIKE ? AND vp.status = 'active' AND v.status = 'active'
            ORDER BY discounted_price ASC";
    
    $search_term = '%' . $material_name . '%';
    $result = $db->executeQuery($sql, [$search_term]);
    
    $comparisons = [];
    if ($result) {
        while ($row = $result->fetch()) {
            $comparisons[] = [
                'vendor_id' => $row['vendor_id'],
                'vendor_name' => $row['vendor_name'],
                'vendor_rating' => floatval($row['vendor_rating']),
                'material_name' => $row['material_name'],
                'category' => $row['category'],
                'specification' => $row['specification'],
                'unit' => $row['unit'],
                'original_price' => floatval($row['price']),
                'discounted_price' => floatval($row['discounted_price']),
                'bulk_discount' => $row['bulk_discount'],
                'minimum_order' => $row['minimum_order'],
                'lead_time' => $row['lead_time'],
                'quality_rating' => floatval($row['quality_rating']),
                'availability' => $row['availability'],
                'savings' => floatval($row['price']) - floatval($row['discounted_price']),
                'savings_percentage' => $row['price'] > 0 ? (($row['price'] - $row['discounted_price']) / $row['price'] * 100) : 0
            ];
        }
    }
    
    return ['success' => true, 'comparisons' => $comparisons];
}

function generatePurchaseOrder($data, $db, $user_id) {
    $vendor_id = intval($data['vendor_id'] ?? 0);
    $po_number = trim($data['po_number'] ?? '');
    $project_name = trim($data['project_name'] ?? '');
    $delivery_address = trim($data['delivery_address'] ?? '');
    $delivery_date = trim($data['delivery_date'] ?? '');
    $payment_terms = trim($data['payment_terms'] ?? '');
    $items = json_decode($data['items'] ?? '[]', true);
    
    if ($vendor_id <= 0 || empty($po_number) || empty($items)) {
        throw new Exception('Vendor ID, PO number, and items are required');
    }
    
    $total_amount = 0;
    foreach ($items as $item) {
        $total_amount += $item['quantity'] * $item['unit_price'];
    }
    
    // Generate PO content
    $po_content = generatePOContent($vendor_id, $po_number, $project_name, $delivery_address, $delivery_date, $payment_terms, $items, $total_amount, $db);
    
    // Save PO to database
    $sql = "INSERT INTO purchase_orders (vendor_id, po_number, project_name, delivery_address, delivery_date, payment_terms, items, total_amount, status, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())";
    
    $params = [$vendor_id, $po_number, $project_name, $delivery_address, $delivery_date, $payment_terms, json_encode($items), $total_amount, $user_id];
    $result = $db->executeQuery($sql, $params);
    
    if ($result) {
        $po_id = $db->lastInsertId();
        return [
            'success' => true, 
            'po_id' => $po_id, 
            'po_content' => $po_content,
            'total_amount' => $total_amount
        ];
    } else {
        throw new Exception('Failed to generate purchase order');
    }
}

function generatePOContent($vendor_id, $po_number, $project_name, $delivery_address, $delivery_date, $payment_terms, $items, $total_amount, $db) {
    // Get vendor details
    $vendor_sql = "SELECT * FROM vendors WHERE id = ?";
    $vendor_result = $db->executeQuery($vendor_sql, [$vendor_id]);
    $vendor = $vendor_result ? $vendor_result->fetch() : null;
    
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>
        <div style='text-align: center; margin-bottom: 30px;'>
            <h1 style='color: #ffffff; margin: 0;'>PURCHASE ORDER</h1>
            <h2 style='color: #333; margin: 10px 0;'>AEC Calculator - MEP Coordination Suite</h2>
        </div>
        
        <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;'>
            <div>
                <h3 style='color: #ffffff; border-bottom: 2px solid #ffffff; padding-bottom: 5px;'>VENDOR INFORMATION</h3>
                <p><strong>Vendor:</strong> " . htmlspecialchars($vendor['vendor_name'] ?? 'N/A') . "</p>
                <p><strong>Contact:</strong> " . htmlspecialchars($vendor['contact_person'] ?? 'N/A') . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($vendor['email'] ?? 'N/A') . "</p>
                <p><strong>Phone:</strong> " . htmlspecialchars($vendor['phone'] ?? 'N/A') . "</p>
                <p><strong>Address:</strong> " . htmlspecialchars($vendor['address'] ?? 'N/A') . "</p>
            </div>
            
            <div>
                <h3 style='color: #ffffff; border-bottom: 2px solid #ffffff; padding-bottom: 5px;'>ORDER DETAILS</h3>
                <p><strong>PO Number:</strong> " . htmlspecialchars($po_number) . "</p>
                <p><strong>Date:</strong> " . date('Y-m-d') . "</p>
                <p><strong>Project:</strong> " . htmlspecialchars($project_name) . "</p>
                <p><strong>Delivery Date:</strong> " . htmlspecialchars($delivery_date) . "</p>
                <p><strong>Payment Terms:</strong> " . htmlspecialchars($payment_terms) . "</p>
            </div>
        </div>
        
        <div style='margin-bottom: 20px;'>
            <h3 style='color: #ffffff; border-bottom: 2px solid #ffffff; padding-bottom: 5px;'>DELIVERY ADDRESS</h3>
            <p>" . nl2br(htmlspecialchars($delivery_address)) . "</p>
        </div>
        
        <table style='width: 100%; border-collapse: collapse; margin-bottom: 30px;'>
            <thead>
                <tr style='background: #ffffff; color: white;'>
                    <th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Item</th>
                    <th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Specification</th>
                    <th style='padding: 10px; text-align: center; border: 1px solid #ddd;'>Qty</th>
                    <th style='padding: 10px; text-align: center; border: 1px solid #ddd;'>Unit</th>
                    <th style='padding: 10px; text-align: right; border: 1px solid #ddd;'>Unit Price</th>
                    <th style='padding: 10px; text-align: right; border: 1px solid #ddd;'>Total</th>
                </tr>
            </thead>
            <tbody>";
    
    foreach ($items as $index => $item) {
        $item_total = $item['quantity'] * $item['unit_price'];
        $html .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($item['material_name']) . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($item['specification'] ?? '') . "</td>
                    <td style='padding: 10px; text-align: center; border: 1px solid #ddd;'>" . $item['quantity'] . "</td>
                    <td style='padding: 10px; text-align: center; border: 1px solid #ddd;'>" . htmlspecialchars($item['unit']) . "</td>
                    <td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>$" . number_format($item['unit_price'], 2) . "</td>
                    <td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>$" . number_format($item_total, 2) . "</td>
                </tr>";
    }
    
    $html .= "
                <tr style='background: #f8f9fa; font-weight: bold;'>
                    <td colspan='5' style='padding: 10px; text-align: right; border: 1px solid #ddd;'>TOTAL AMOUNT:</td>
                    <td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>$" . number_format($total_amount, 2) . "</td>
                </tr>
            </tbody>
        </table>
        
        <div style='margin-top: 40px; padding-top: 20px; border-top: 2px solid #ffffff;'>
            <p><strong>Terms and Conditions:</strong></p>
            <ul style='margin-left: 20px;'>
                <li>All prices are in USD unless otherwise specified</li>
                <li>Payment terms as agreed upon order confirmation</li>
                <li>Delivery as per specified date</li>
                <li>Quality standards as per specifications</li>
                <li>Warranty as specified for each item</li>
            </ul>
        </div>
        
        <div style='margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 50px;'>
            <div style='text-align: center;'>
                <div style='height: 60px; border-bottom: 1px solid #333;'></div>
                <p style='margin: 5px 0;'>Authorized Signature</p>
                <p style='margin: 0; font-size: 12px;'>Date: _______________</p>
            </div>
            
            <div style='text-align: center;'>
                <div style='height: 60px; border-bottom: 1px solid #333;'></div>
                <p style='margin: 5px 0;'>Vendor Acceptance</p>
                <p style='margin: 0; font-size: 12px;'>Date: _______________</p>
            </div>
        </div>
    </div>";
    
    return $html;
}

function getPriceHistory($material_id, $db) {
    if ($material_id <= 0) {
        throw new Exception('Invalid material ID');
    }
    
    $sql = "SELECT * FROM price_history WHERE material_id = ? ORDER BY change_date DESC LIMIT 20";
    $result = $db->executeQuery($sql, [$material_id]);
    
    $history = [];
    if ($result) {
        while ($row = $result->fetch()) {
            $history[] = [
                'id' => $row['id'],
                'material_id' => $row['material_id'],
                'old_price' => floatval($row['old_price']),
                'new_price' => floatval($row['new_price']),
                'change_percentage' => floatval($row['change_percentage']),
                'change_date' => $row['change_date'],
                'reason' => $row['reason'],
                'changed_by' => $row['changed_by']
            ];
        }
    }
    
    return ['success' => true, 'history' => $history];
}

function searchMaterials($query, $db) {
    if (empty($query)) {
        return ['success' => true, 'materials' => []];
    }
    
    $sql = "SELECT DISTINCT material_name, category FROM vendor_prices 
            WHERE (material_name LIKE ? OR category LIKE ?) AND status = 'active' 
            ORDER BY material_name LIMIT 50";
    
    $search_term = '%' . $query . '%';
    $result = $db->executeQuery($sql, [$search_term, $search_term]);
    
    $materials = [];
    if ($result) {
        while ($row = $result->fetch()) {
            $materials[] = [
                'material_name' => $row['material_name'],
                'category' => $row['category']
            ];
        }
    }
    
    return ['success' => true, 'materials' => $materials];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Pricing System - MEP Coordination Suite</title>
    <link rel="stylesheet" href="../../../assets/css/estimation.css">
    <style>
        .vendor-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .vendor-header {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .vendor-tabs {
            display: flex;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .vendor-tab {
            flex: 1;
            padding: 15px 20px;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #666;
        }
        
        .vendor-tab.active {
            background: #ffffff;
            color: white;
        }
        
        .vendor-tab:hover {
            background: #5a6fd8;
            color: white;
        }
        
        .tab-content {
            display: none;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .tab-content.active {
            display: block;
        }
        
        .vendor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .vendor-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #ffffff;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .vendor-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .vendor-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .vendor-info {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .vendor-rating {
            color: #ffa500;
            font-weight: bold;
        }
        
        .vendor-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        
        .btn {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 11px;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
        }
        
        .materials-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .materials-table th,
        .materials-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .materials-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .materials-table tr:hover {
            background: #f8f9fa;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ffffff;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        .price-comparison {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .comparison-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .comparison-card.best-price {
            border-color: #28a745;
            background: #f8fff9;
        }
        
        .comparison-card .vendor-name {
            color: #ffffff;
            font-size: 16px;
        }
        
        .comparison-card .price {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
        }
        
        .comparison-card .savings {
            color: #28a745;
            font-weight: bold;
        }
        
        .stars {
            color: #ffa500;
        }
        
        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .search-container input {
            flex: 1;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="vendor-container">
        <div class="vendor-header">
            <h1>Vendor Pricing System</h1>
            <p>Comprehensive vendor management and pricing comparison for MEP materials</p>
        </div>
        
        <div class="vendor-tabs">
            <button class="vendor-tab active" onclick="showTab('vendors')">Vendor Management</button>
            <button class="vendor-tab" onclick="showTab('materials')">Material Pricing</button>
            <button class="vendor-tab" onclick="showTab('comparison')">Price Comparison</button>
            <button class="vendor-tab" onclick="showTab('purchase-orders')">Purchase Orders</button>
        </div>
        
        <!-- Vendor Management Tab -->
        <div id="vendors" class="tab-content active">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Vendor Management</h3>
                <button class="btn" onclick="openVendorModal()">Add New Vendor</button>
            </div>
            
            <div class="vendor-grid" id="vendorGrid"></div>
        </div>
        
        <!-- Material Pricing Tab -->
        <div id="materials" class="tab-content">
            <div class="search-container">
                <input type="text" id="materialSearch" placeholder="Search materials..." onkeyup="searchMaterials()">
                <select id="categoryFilter" onchange="filterMaterials()">
                    <option value="">All Categories</option>
                    <option value="HVAC Equipment">HVAC Equipment</option>
                    <option value="Electrical Equipment">Electrical Equipment</option>
                    <option value="Plumbing Fixtures">Plumbing Fixtures</option>
                    <option value="Fire Protection">Fire Protection</option>
                </select>
            </div>
            
            <div id="materialPricingContent"></div>
        </div>
        
        <!-- Price Comparison Tab -->
        <div id="comparison" class="tab-content">
            <h3>Price Comparison</h3>
            <div class="search-container">
                <input type="text" id="comparisonSearch" placeholder="Enter material name to compare prices...">
                <button class="btn" onclick="comparePrices()">Compare Prices</button>
            </div>
            <div id="comparisonResults"></div>
        </div>
        
        <!-- Purchase Orders Tab -->
        <div id="purchase-orders" class="tab-content">
            <h3>Purchase Orders</h3>
            <div id="poContent">
                <p>Select a vendor and materials to generate purchase orders.</p>
            </div>
        </div>
    </div>
    
    <!-- Vendor Modal -->
    <div id="vendorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeVendorModal()">&times;</span>
            <h3 id="vendorModalTitle">Add New Vendor</h3>
            <form id="vendorForm">
                <input type="hidden" id="vendorId" name="vendor_id">
                <div class="form-row">
                    <div class="form-group">
                        <label for="vendorName">Vendor Name *</label>
                        <input type="text" id="vendorName" name="vendor_name" required>
                    </div>
                    <div class="form-group">
                        <label for="contactPerson">Contact Person</label>
                        <input type="text" id="contactPerson" name="contact_person">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <select id="specialization" name="specialization">
                            <option value="">Select Specialization</option>
                            <option value="HVAC Equipment">HVAC Equipment</option>
                            <option value="Electrical Equipment">Electrical Equipment</option>
                            <option value="Plumbing Materials">Plumbing Materials</option>
                            <option value="Fire Protection">Fire Protection</option>
                            <option value="General MEP">General MEP</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rating">Rating (1-5)</label>
                        <input type="number" id="rating" name="rating" min="1" max="5" step="0.1">
                    </div>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <button type="button" class="btn" onclick="saveVendor()">Save Vendor</button>
                    <button type="button" class="btn" onclick="closeVendorModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Material Price Modal -->
    <div id="materialModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeMaterialModal()">&times;</span>
            <h3 id="materialModalTitle">Add Material Price</h3>
            <form id="materialForm">
                <input type="hidden" id="materialPriceId" name="price_id">
                <input type="hidden" id="materialVendorId" name="vendor_id">
                <div class="form-row">
                    <div class="form-group">
                        <label for="materialName">Material Name *</label>
                        <input type="text" id="materialName" name="material_name" required>
                    </div>
                    <div class="form-group">
                        <label for="materialCategory">Category</label>
                        <select id="materialCategory" name="category">
                            <option value="">Select Category</option>
                            <option value="HVAC Equipment">HVAC Equipment</option>
                            <option value="Electrical Equipment">Electrical Equipment</option>
                            <option value="Plumbing Fixtures">Plumbing Fixtures</option>
                            <option value="Fire Protection">Fire Protection</option>
                            <option value="Piping">Piping</option>
                            <option value="Wiring & Cables">Wiring & Cables</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="specification">Specification</label>
                    <textarea id="specification" name="specification" rows="2"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <select id="unit" name="unit">
                            <option value="units">Units</option>
                            <option value="m">Meters</option>
                            <option value="kg">Kilograms</option>
                            <option value="sq.m">Square Meters</option>
                            <option value="liters">Liters</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (USD) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="bulkDiscount">Bulk Discount</label>
                        <input type="text" id="bulkDiscount" name="bulk_discount" placeholder="e.g., 10% for 100+ units">
                    </div>
                    <div class="form-group">
                        <label for="minimumOrder">Minimum Order Quantity</label>
                        <input type="number" id="minimumOrder" name="minimum_order" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="leadTime">Lead Time</label>
                        <input type="text" id="leadTime" name="lead_time" placeholder="e.g., 2-3 weeks">
                    </div>
                    <div class="form-group">
                        <label for="warranty">Warranty</label>
                        <input type="text" id="warranty" name="warranty" placeholder="e.g., 2 years">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="availability">Availability</label>
                        <select id="availability" name="availability">
                            <option value="In Stock">In Stock</option>
                            <option value="Available">Available</option>
                            <option value="Limited Stock">Limited Stock</option>
                            <option value="Backorder">Backorder</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="qualityRating">Quality Rating (1-5)</label>
                        <input type="number" id="qualityRating" name="quality_rating" min="1" max="5" step="0.1">
                    </div>
                </div>
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="2"></textarea>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <button type="button" class="btn" onclick="saveMaterialPrice()">Save Price</button>
                    <button type="button" class="btn" onclick="closeMaterialModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentVendors = [];
        let currentMaterials = [];
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadVendors();
        });
        
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.vendor-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
            
            // Load content based on tab
            switch(tabName) {
                case 'vendors':
                    loadVendors();
                    break;
                case 'materials':
                    loadAllMaterials();
                    break;
                case 'comparison':
                    // Already loaded
                    break;
                case 'purchase-orders':
                    loadPurchaseOrders();
                    break;
            }
        }
        
        function loadVendors() {
            fetch('', {
                method: 'POST',
                body: new URLSearchParams({action: 'get_vendors'})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentVendors = data.vendors;
                    displayVendors();
                } else {
                    showNotification('Error loading vendors: ' + data.error);
                }
            })
            .catch(error => {
                showNotification('Error: ' + error.message, 'error');
            });
        }
        
        function displayVendors() {
            const grid = document.getElementById('vendorGrid', 'info');
            grid.innerHTML = '';
            
            currentVendors.forEach(vendor => {
                const card = document.createElement('div');
                card.className = 'vendor-card';
                card.innerHTML = `
                    <div class="vendor-name">${vendor.vendor_name}</div>
                    <div class="vendor-info"><strong>Contact:</strong> ${vendor.contact_person || 'N/A'}</div>
                    <div class="vendor-info"><strong>Email:</strong> ${vendor.email || 'N/A'}</div>
                    <div class="vendor-info"><strong>Phone:</strong> ${vendor.phone || 'N/A'}</div>
                    <div class="vendor-info"><strong>Specialization:</strong> ${vendor.specialization || 'N/A'}</div>
                    <div class="vendor-info">
                        <strong>Rating:</strong> 
                        <span class="vendor-rating">${vendor.rating ? '★'.repeat(Math.floor(vendor.rating)) + ' (' + vendor.rating + ')' : 'Not rated'}</span>
                    </div>
                    <div class="vendor-info"><strong>Materials:</strong> ${vendor.material_count} items</div>
                    <div class="vendor-actions">
                        <button class="btn btn-small" onclick="viewVendorMaterials(${vendor.id})">View Materials</button>
                        <button class="btn btn-small" onclick="editVendor(${vendor.id})">Edit</button>
                        <button class="btn btn-small btn-warning" onclick="deleteVendor(${vendor.id})">Delete</button>
                    </div>
                `;
                grid.appendChild(card);
            });
        }
        
        function viewVendorMaterials(vendorId) {
            const vendor = currentVendors.find(v => v.id === vendorId);
            if (!vendor) return;
            
            fetch('', {
                method: 'POST',
                body: new URLSearchParams({action: 'get_materials', vendor_id: vendorId})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayVendorMaterials(vendor, data.materials);
                } else {
                    showNotification('Error loading materials: ' + data.error);
                }
            })
            .catch(error => {
                showNotification('Error: ' + error.message, 'error');
            });
        }
        
        function displayVendorMaterials(vendor, materials) {
            const modal = document.getElementById('materialModal', 'info');
            const modalTitle = document.getElementById('materialModalTitle');
            const materialVendorId = document.getElementById('materialVendorId');
            
            modalTitle.textContent = `Materials for ${vendor.vendor_name}`;
            materialVendorId.value = vendor.id;
            
            // Display materials list
            let materialsHtml = '<div style="max-height: 300px; overflow-y: auto; margin: 20px 0;">';
            materialsHtml += '<table class="materials-table">';
            materialsHtml += '<tr><th>Material</th><th>Category</th><th>Price</th><th>Unit</th><th>Availability</th><th>Actions</th></tr>';
            
            materials.forEach(material => {
                materialsHtml += `
                    <tr>
                        <td>${material.material_name}</td>
                        <td>${material.category}</td>
                        <td>$${material.price.toFixed(2)}</td>
                        <td>${material.unit}</td>
                        <td>${material.availability}</td>
                        <td>
                            <button class="btn btn-small" onclick="editMaterialPrice(${material.id})">Edit</button>
                            <button class="btn btn-small btn-warning" onclick="deleteMaterialPrice(${material.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
            
            materialsHtml += '</table></div>';
            materialsHtml += '<button class="btn" onclick="openMaterialModal(' + vendor.id + ')">Add New Material</button>';
            
            // Add materials HTML to modal content
            const form = document.getElementById('materialForm');
            form.insertAdjacentHTML('afterend', materialsHtml);
            
            modal.style.display = 'block';
        }
        
        function loadAllMaterials() {
            if (currentVendors.length === 0) {
                loadVendors();
                return;
            }
            
            let allMaterials = [];
            let promises = currentVendors.map(vendor => 
                fetch('', {
                    method: 'POST',
                    body: new URLSearchParams({action: 'get_materials', vendor_id: vendor.id})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.materials.forEach(material => {
                            allMaterials.push({
                                ...material,
                                vendor: currentVendors.find(v => v.id === material.vendor_id)
                            });
                        });
                    }
                })
            );
            
            Promise.all(promises).then(() => {
                currentMaterials = allMaterials;
                displayAllMaterials();
            });
        }
        
        function displayAllMaterials() {
            const content = document.getElementById('materialPricingContent');
            let html = '<table class="materials-table">';
            html += '<tr><th>Material</th><th>Category</th><th>Vendor</th><th>Price</th><th>Unit</th><th>Availability</th><th>Quality</th><th>Actions</th></tr>';
            
            currentMaterials.forEach(material => {
                html += `
                    <tr>
                        <td>${material.material_name}</td>
                        <td>${material.category}</td>
                        <td>${material.vendor.vendor_name}</td>
                        <td>$${material.price.toFixed(2)}</td>
                        <td>${material.unit}</td>
                        <td>${material.availability}</td>
                        <td>${'★'.repeat(Math.floor(material.quality_rating))} (${material.quality_rating})</td>
                        <td>
                            <button class="btn btn-small" onclick="editMaterialPrice(${material.id})">Edit</button>
                            <button class="btn btn-small btn-warning" onclick="deleteMaterialPrice(${material.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</table>';
            content.innerHTML = html;
        }
        
        function searchMaterials() {
            const query = document.getElementById('materialSearch').value.toLowerCase();
            const filteredMaterials = currentMaterials.filter(material => 
                material.material_name.toLowerCase().includes(query) ||
                material.category.toLowerCase().includes(query) ||
                material.vendor.vendor_name.toLowerCase().includes(query)
            );
            
            displayFilteredMaterials(filteredMaterials);
        }
        
        function filterMaterials() {
            const category = document.getElementById('categoryFilter').value;
            const query = document.getElementById('materialSearch').value.toLowerCase();
            
            let filteredMaterials = currentMaterials.filter(material => {
                const matchesQuery = !query || 
                    material.material_name.toLowerCase().includes(query) ||
                    material.category.toLowerCase().includes(query) ||
                    material.vendor.vendor_name.toLowerCase().includes(query);
                    
                const matchesCategory = !category || material.category === category;
                
                return matchesQuery && matchesCategory;
            });
            
            displayFilteredMaterials(filteredMaterials);
        }
        
        function displayFilteredMaterials(materials) {
            const content = document.getElementById('materialPricingContent');
            let html = '<table class="materials-table">';
            html += '<tr><th>Material</th><th>Category</th><th>Vendor</th><th>Price</th><th>Unit</th><th>Availability</th><th>Quality</th><th>Actions</th></tr>';
            
            materials.forEach(material => {
                html += `
                    <tr>
                        <td>${material.material_name}</td>
                        <td>${material.category}</td>
                        <td>${material.vendor.vendor_name}</td>
                        <td>$${material.price.toFixed(2)}</td>
                        <td>${material.unit}</td>
                        <td>${material.availability}</td>
                        <td>${'★'.repeat(Math.floor(material.quality_rating))} (${material.quality_rating})</td>
                        <td>
                            <button class="btn btn-small" onclick="editMaterialPrice(${material.id})">Edit</button>
                            <button class="btn btn-small btn-warning" onclick="deleteMaterialPrice(${material.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</table>';
            content.innerHTML = html;
        }
        
        function comparePrices() {
            const query = document.getElementById('comparisonSearch').value.trim();
            if (!query) {
                showNotification('Please enter a material name to compare prices', 'info');
                return;
            }
            
            fetch('', {
                method: 'POST',
                body: new URLSearchParams({
                    action: 'compare_prices',
                    material_name: query
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPriceComparison(data.comparisons);
                } else {
                    showNotification('Error comparing prices: ' + data.error);
                }
            })
            .catch(error => {
                showNotification('Error: ' + error.message, 'error');
            });
        }
        
        function displayPriceComparison(comparisons) {
            const resultsDiv = document.getElementById('comparisonResults', 'info');
            
            if (comparisons.length === 0) {
                resultsDiv.innerHTML = '<p>No prices found for this material.</p>';
                return;
            }
            
            // Find the best price
            const bestPrice = Math.min(...comparisons.map(c => c.discounted_price));
            
            let html = '<h4>Price Comparison Results</h4>';
            html += '<div class="price-comparison">';
            
            comparisons.forEach(comparison => {
                const isBest = comparison.discounted_price === bestPrice;
                html += `
                    <div class="comparison-card ${isBest ? 'best-price' : ''}">
                        <div class="vendor-name">${comparison.vendor_name}</div>
                        <div class="vendor-info">${comparison.specification}</div>
                        <div class="price">$${comparison.discounted_price.toFixed(2)} <span style="font-size: 14px;">/ ${comparison.unit}</span></div>
                        ${comparison.savings > 0 ? `<div class="savings">Save $${comparison.savings.toFixed(2)} (${comparison.savings_percentage.toFixed(1)}%)</div>` : ''}
                        <div class="vendor-info">Quality: ${'★'.repeat(Math.floor(comparison.quality_rating))} (${comparison.quality_rating})</div>
                        <div class="vendor-info">Lead Time: ${comparison.lead_time}</div>
                        <div class="vendor-info">Availability: ${comparison.availability}</div>
                        ${comparison.bulk_discount ? `<div class="vendor-info">Bulk Discount: ${comparison.bulk_discount}</div>` : ''}
                        <button class="btn btn-small" style="margin-top: 10px;" onclick="generatePO(${comparison.vendor_id}, '${comparison.material_name}', ${comparison.discounted_price}, '${comparison.unit}')">Generate PO</button>
                    </div>
                `;
            });
            
            html += '</div>';
            resultsDiv.innerHTML = html;
        }
        
        function generatePO(vendorId, materialName, price, unit) {
            // This would open a PO generation form with the selected material
            showNotification(`Generate PO for ${materialName} from vendor ${vendorId} at $${price}/${unit}`, 'info');
        }
        
        function openVendorModal(vendorId = null) {
            const modal = document.getElementById('vendorModal');
            const modalTitle = document.getElementById('vendorModalTitle');
            const form = document.getElementById('vendorForm');
            
            if (vendorId) {
                // Edit mode
                const vendor = currentVendors.find(v => v.id === vendorId);
                if (!vendor) return;
                
                modalTitle.textContent = 'Edit Vendor';
                document.getElementById('vendorId').value = vendor.id;
                document.getElementById('vendorName').value = vendor.vendor_name;
                document.getElementById('contactPerson').value = vendor.contact_person || '';
                document.getElementById('email').value = vendor.email || '';
                document.getElementById('phone').value = vendor.phone || '';
                document.getElementById('address').value = vendor.address || '';
                document.getElementById('specialization').value = vendor.specialization || '';
                document.getElementById('rating').value = vendor.rating || '';
            } else {
                // Add mode
                modalTitle.textContent = 'Add New Vendor';
                form.reset();
                document.getElementById('vendorId').value = '';
            }
            
            modal.style.display = 'block';
        }
        
        function openMaterialModal(vendorId = null) {
            const modal = document.getElementById('materialModal');
            const modalTitle = document.getElementById('materialModalTitle');
            const form = document.getElementById('materialForm');
            
            modalTitle.textContent = 'Add Material Price';
            form.reset();
            document.getElementById('materialPriceId').value = '';
            document.getElementById('materialVendorId').value = vendorId || '';
            
            modal.style.display = 'block';
        }
        
        function saveVendor() {
            const formData = new FormData(document.getElementById('vendorForm'));
            const vendorId = formData.get('vendor_id');
            
            const action = vendorId ? 'update_vendor' : 'add_vendor';
            formData.append('action', action);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Vendor saved successfully!', 'info');
                    closeVendorModal();
                    loadVendors();
                } else {
                    showNotification('Error saving vendor: ' + data.error);
                }
            })
            .catch(error => {
                showNotification('Error: ' + error.message, 'error');
            });
        }
        
        function saveMaterialPrice() {
            const formData = new FormData(document.getElementById('materialForm', 'info'));
            const priceId = formData.get('price_id');
            
            const action = priceId ? 'update_material_price' : 'add_material_price';
            formData.append('action', action);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Material price saved successfully!', 'info');
                    closeMaterialModal();
                    if (document.getElementById('materials').classList.contains('active')) {
                        loadAllMaterials();
                    }
                } else {
                    showNotification('Error saving material price: ' + data.error);
                }
            })
            .catch(error => {
                showNotification('Error: ' + error.message, 'error');
            });
        }
        
        function editVendor(vendorId) {
            openVendorModal(vendorId);
        }
        
        function editMaterialPrice(priceId) {
            // Load material price data and open modal in edit mode
            openMaterialModal();
            document.getElementById('materialPriceId', 'info').value = priceId;
        }
        
        function deleteVendor(vendorId) {
            showConfirmModal('Delete Vendor', 'Are you sure you want to delete this vendor?', function() {
                fetch('', {
                    method: 'POST',
                    body: new URLSearchParams({
                        action: 'delete_vendor',
                        vendor_id: vendorId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Vendor deleted successfully!', 'success');
                        loadVendors();
                    } else {
                        showNotification('Error deleting vendor: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error: ' + error.message, 'error');
                });
            });
        }
        
        function deleteMaterialPrice(priceId) {
            showConfirmModal('Delete Material Price', 'Are you sure you want to delete this material price?', function() {
                fetch('', {
                    method: 'POST',
                    body: new URLSearchParams({
                        action: 'delete_material_price',
                        price_id: priceId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Material price deleted successfully!', 'success');
                        loadAllMaterials();
                    } else {
                        showNotification('Error deleting material price: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error: ' + error.message, 'error');
                });
            });
        }
        
        function closeVendorModal() {
            document.getElementById('vendorModal', 'info').style.display = 'none';
        }
        
        function closeMaterialModal() {
            document.getElementById('materialModal').style.display = 'none';
        }
        
        function loadPurchaseOrders() {
            document.getElementById('poContent').innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <h4>Purchase Order Management</h4>
                    <p>Select a vendor and materials to generate purchase orders.</p>
                    <button class="btn" onclick="generateNewPO()">Generate New PO</button>
                </div>
            `;
        }
        
        function generateNewPO() {
            showNotification('Purchase order generation feature coming soon!', 'info');
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const vendorModal = document.getElementById('vendorModal');
            const materialModal = document.getElementById('materialModal');
            
            if (event.target === vendorModal) {
                vendorModal.style.display = 'none';
            }
            if (event.target === materialModal) {
                materialModal.style.display = 'none';
            }
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>


