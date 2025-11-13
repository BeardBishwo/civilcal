<?php
require_once '../../../app/Config/config.php';

class AutoCADLayerMapper {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function createLayerMapping($mappingData) {
        try {
            $sql = "INSERT INTO mep_autocad_mappings (mapping_name, mep_category, autocad_layer, 
                    layer_properties, color_code, linetype, lineweight, is_active, created_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            // ensure bind_param receives variables (passed by reference)
            $m_name = $mappingData['name'];
            $m_category = $mappingData['category'];
            $m_layer = $mappingData['layer'];
            $m_properties = json_encode($mappingData['properties']);
            $m_color = $mappingData['color'];
            $m_linetype = $mappingData['linetype'];
            $m_lineweight = $mappingData['lineweight'];
            $m_active = $mappingData['active'] ?? 1;

            $stmt->bind_param("ssssssii",
                $m_name,
                $m_category,
                $m_layer,
                $m_properties,
                $m_color,
                $m_linetype,
                $m_lineweight,
                $m_active
            );
            
            $stmt->execute();
            $mappingId = $this->db->insert_id;
            
            return [
                'success' => true,
                'mapping_id' => $mappingId,
                'message' => 'Layer mapping created successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function mapMEPToAutoCAD($projectId, $mepElements, $userId) {
        try {
            // Create mapping session
            $sessionId = $this->createMappingSession($projectId, count($mepElements), $userId);
            
            // Apply layer mappings
            $mappedElements = [];
            $unmappedElements = [];
            
            foreach ($mepElements as $element) {
                $mapping = $this->getMappingForElement($element);
                
                if ($mapping) {
                    $mappedElement = [
                        'original_element' => $element,
                        'auto_cad_data' => $this->generateAutoCADData($element, $mapping),
                        'mapping_id' => $mapping['id']
                    ];
                    $mappedElements[] = $mappedElement;
                } else {
                    $unmappedElements[] = $element;
                }
            }
            
            // Update session with results
            $this->updateMappingSession($sessionId, $mappedElements, $unmappedElements);
            
            return [
                'success' => true,
                'session_id' => $sessionId,
                'mapped_count' => count($mappedElements),
                'unmapped_count' => count($unmappedElements),
                'mapped_elements' => $mappedElements,
                'unmapped_elements' => $unmappedElements
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function createMappingSession($projectId, $totalElements, $userId) {
        $sql = "INSERT INTO mep_mapping_sessions (project_id, total_elements, mapped_elements, 
                user_id, session_status, created_date) 
                VALUES (?, ?, ?, ?, 'active', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiii", $projectId, $totalElements, 0, $userId);
        $stmt->execute();
        
        return $this->db->insert_id;
    }
    
    private function getMappingForElement($element) {
        $category = $element['category'] ?? 'Generic';
        
        $sql = "SELECT * FROM mep_autocad_mappings 
                WHERE mep_category = ? AND is_active = 1 
                ORDER BY created_date DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $mapping = $result->fetch_assoc();
        if ($mapping) {
            $mapping['layer_properties'] = json_decode($mapping['layer_properties'], true);
        }
        
        return $mapping;
    }
    
    private function generateAutoCADData($element, $mapping) {
        $autoCADData = [
            'layer' => $mapping['autocad_layer'],
            'color' => $mapping['color_code'],
            'linetype' => $mapping['linetype'],
            'lineweight' => $mapping['lineweight'],
            'properties' => $mapping['layer_properties']
        ];
        
        // Add element-specific properties
        switch ($element['category']) {
            case 'HVAC':
                $autoCADData['geometry'] = [
                    'type' => 'DUCT',
                    'width' => $element['width'] ?? '600mm',
                    'height' => $element['height'] ?? '400mm',
                    'insulation' => $element['insulation'] ?? '50mm'
                ];
                break;
                
            case 'ELECTRICAL':
                $autoCADData['geometry'] = [
                    'type' => 'CABLE',
                    'diameter' => $element['diameter'] ?? '25mm',
                    'voltage' => $element['voltage'] ?? '480V',
                    'conduit' => $element['conduit'] ?? 'EMT'
                ];
                break;
                
            case 'PLUMBING':
                $autoCADData['geometry'] = [
                    'type' => 'PIPE',
                    'diameter' => $element['diameter'] ?? '100mm',
                    'material' => $element['material'] ?? 'Copper',
                    'insulation' => $element['insulation'] ?? '25mm'
                ];
                break;
                
            case 'FIRE':
                $autoCADData['geometry'] = [
                    'type' => 'SPRINKLER_LINE',
                    'diameter' => $element['diameter'] ?? '150mm',
                    'pressure' => $element['pressure'] ?? '8 bar',
                    'coverage' => $element['coverage'] ?? '12m'
                ];
                break;
        }
        
        return $autoCADData;
    }
    
    private function updateMappingSession($sessionId, $mappedElements, $unmappedElements) {
        $sql = "UPDATE mep_mapping_sessions 
                SET mapped_elements = ?, unmapped_elements = ?, 
                session_status = 'completed', completion_date = NOW() 
                WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    // bind_param requires variables (passed by reference)
    $mapped_count = count($mappedElements);
    $unmapped_count = count($unmappedElements);
    $s_id = $sessionId;
    $stmt->bind_param("iii", $mapped_count, $unmapped_count, $s_id);
    $stmt->execute();
    }
    
    public function exportToAutoCAD($sessionId, $format = 'DWG') {
        try {
            $session = $this->getMappingSession($sessionId);
            if (!$session) {
                throw new Exception('Mapping session not found');
            }
            
            // Get mapped elements for this session
            $mappedElements = $this->getMappedElementsForSession($sessionId);
            
            $exportPath = $this->generateExportPath($sessionId, $format);
            
            switch ($format) {
                case 'DWG':
                    return $this->exportToDWG($mappedElements, $exportPath);
                case 'DXF':
                    return $this->exportToDXF($mappedElements, $exportPath);
                case 'CSV':
                    return $this->exportToCSV($mappedElements, $exportPath);
                default:
                    throw new Exception('Unsupported export format');
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function getMappingSession($sessionId) {
        $sql = "SELECT * FROM mep_mapping_sessions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    private function getMappedElementsForSession($sessionId) {
        $sql = "SELECT * FROM mep_mapped_elements WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $elements = [];
        while ($row = $result->fetch_assoc()) {
            $row['auto_cad_data'] = json_decode($row['auto_cad_data'], true);
            $elements[] = $row;
        }
        
        return $elements;
    }
    
    private function generateExportPath($sessionId, $format) {
        $exportDir = '../../../exports/autocad/';
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }
        return $exportDir . "mapping_session_{$sessionId}.{$format}";
    }
    
    private function exportToDWG($elements, $filePath) {
        // Simulate DWG file creation
        $dwgContent = "DWG AutoCAD Drawing\n";
        $dwgContent .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $dwgContent .= "Elements: " . count($elements) . "\n\n";
        
        foreach ($elements as $element) {
            $autoCAD = $element['auto_cad_data'];
            $dwgContent .= "ELEMENT: {$element['element_id']}\n";
            $dwgContent .= "LAYER: {$autoCAD['layer']}\n";
            $dwgContent .= "COLOR: {$autoCAD['color']}\n";
            $dwgContent .= "LINETYPE: {$autoCAD['linetype']}\n";
            $dwgContent .= "LINEWEIGHT: {$autoCAD['lineweight']}\n";
            
            if (isset($autoCAD['geometry'])) {
                foreach ($autoCAD['geometry'] as $prop => $value) {
                    $dwgContent .= strtoupper($prop) . ": {$value}\n";
                }
            }
            $dwgContent .= "\n";
        }
        
        file_put_contents($filePath, $dwgContent);
        
        return [
            'success' => true,
            'file_path' => $filePath,
            'elements_exported' => count($elements)
        ];
    }
    
    private function exportToDXF($elements, $filePath) {
        // Simulate DXF file creation with proper formatting
        $dxfContent = "0\nSECTION\n2\nHEADER\n9\n\$ACADVER\n1\nAC1021\n0\nENDSEC\n0\nSECTION\n2\nTABLES\n0\nENDSEC\n0\nSECTION\n2\nENTITIES\n";
        
        foreach ($elements as $element) {
            $autoCAD = $element['auto_cad_data'];
            $dxfContent .= "0\nLINE\n8\n{$autoCAD['layer']}\n10\n0.0\n20\n0.0\n30\n0.0\n11\n100.0\n21\n0.0\n31\n0.0\n";
        }
        
        $dxfContent .= "0\nENDSEC\n0\nEOF\n";
        file_put_contents($filePath, $dxfContent);
        
        return [
            'success' => true,
            'file_path' => $filePath,
            'elements_exported' => count($elements)
        ];
    }
    
    private function exportToCSV($elements, $filePath) {
        $csvContent = "Element ID,Category,Layer,Color,Linetype,Lineweight,Geometry Properties\n";
        
        foreach ($elements as $element) {
            $autoCAD = $element['auto_cad_data'];
            $geometry = isset($autoCAD['geometry']) ? json_encode($autoCAD['geometry']) : '';
            
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,\"%s\"\n",
                $element['element_id'],
                $element['category'],
                $autoCAD['layer'],
                $autoCAD['color'],
                $autoCAD['linetype'],
                $autoCAD['lineweight'],
                $geometry
            );
        }
        
        file_put_contents($filePath, $csvContent);
        
        return [
            'success' => true,
            'file_path' => $filePath,
            'elements_exported' => count($elements)
        ];
    }
    
    public function getAvailableMappings() {
        $sql = "SELECT * FROM mep_autocad_mappings WHERE is_active = 1 ORDER BY mep_category, mapping_name";
        $result = $this->db->query($sql);
        
        $mappings = [];
        while ($row = $result->fetch_assoc()) {
            $row['layer_properties'] = json_decode($row['layer_properties'], true);
            $mappings[] = $row;
        }
        
        return $mappings;
    }
    
    public function getMappingHistory($projectId, $limit = 10) {
        $sql = "SELECT * FROM mep_mapping_sessions 
                WHERE project_id = ? 
                ORDER BY created_date DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $projectId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        
        return $history;
    }
    
    public function updateElementMapping($elementId, $mappingId) {
        try {
            $sql = "UPDATE mep_mapped_elements 
                    SET mapping_id = ? 
                    WHERE element_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("is", $mappingId, $elementId);
            $stmt->execute();
            
            return [
                'success' => true,
                'message' => 'Element mapping updated successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function validateLayerStandards($mappings) {
        $validation = [
            'valid' => true,
            'errors' => [],
            'warnings' => []
        ];
        
        foreach ($mappings as $mapping) {
            // Validate layer name format
            if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $mapping['autocad_layer'])) {
                $validation['errors'][] = "Invalid layer name format: {$mapping['autocad_layer']}";
                $validation['valid'] = false;
            }
            
            // Validate color code (1-256)
            if ($mapping['color_code'] < 1 || $mapping['color_code'] > 256) {
                $validation['errors'][] = "Invalid color code for layer: {$mapping['autocad_layer']}";
                $validation['valid'] = false;
            }
            
            // Check for standard linetypes
            $standardLinetypes = ['CONTINUOUS', 'DASHED', 'DOTTED', 'DASHDOT'];
            if (!in_array(strtoupper($mapping['linetype']), $standardLinetypes)) {
                $validation['warnings'][] = "Non-standard linetype: {$mapping['linetype']}";
            }
        }
        
        return $validation;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $mapper = new AutoCADLayerMapper();
    
    switch ($action) {
        case 'create_mapping':
            $mappingData = [
                'name' => $_POST['mapping_name'] ?? '',
                'category' => $_POST['mep_category'] ?? '',
                'layer' => $_POST['autocad_layer'] ?? '',
                'properties' => [
                    'visible' => $_POST['visible'] ?? '1',
                    'plottable' => $_POST['plottable'] ?? '1',
                    'description' => $_POST['description'] ?? ''
                ],
                'color' => $_POST['color_code'] ?? '1',
                'linetype' => $_POST['linetype'] ?? 'CONTINUOUS',
                'lineweight' => $_POST['lineweight'] ?? '0.25mm',
                'active' => $_POST['active'] ?? 1
            ];
            $result = $mapper->createLayerMapping($mappingData);
            echo json_encode($result);
            break;
            
        case 'map_to_autocad':
            $projectId = $_POST['project_id'] ?? 0;
            $mepElements = json_decode($_POST['mep_elements'] ?? '[]', true);
            $userId = $_SESSION['user_id'] ?? 1;
            $result = $mapper->mapMEPToAutoCAD($projectId, $mepElements, $userId);
            echo json_encode($result);
            break;
            
        case 'export_autocad':
            $sessionId = $_POST['session_id'] ?? 0;
            $format = $_POST['format'] ?? 'DWG';
            $result = $mapper->exportToAutoCAD($sessionId, $format);
            echo json_encode($result);
            break;
            
        case 'get_mappings':
            $result = $mapper->getAvailableMappings();
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'get_mapping_history':
            $projectId = $_POST['project_id'] ?? 0;
            $limit = $_POST['limit'] ?? 10;
            $result = $mapper->getMappingHistory($projectId, $limit);
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'update_mapping':
            $elementId = $_POST['element_id'] ?? '';
            $mappingId = $_POST['mapping_id'] ?? 0;
            $result = $mapper->updateElementMapping($elementId, $mappingId);
            echo json_encode($result);
            break;
    }
    exit;
}
?>
