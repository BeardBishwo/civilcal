<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class EstimationController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * Display the full-width spreadsheet interface
     */
    public function sheet($projectId = null)
    {
        // If no project ID is provided, show a list or create a "Default Project"
        if (!$projectId) {
            // Check if there's a default project for this user
            $user = Auth::user();
            $userId = $user ? $user->id : 0;
            
            $project = $this->db->findOne('est_projects', ['name' => 'Demo Project']);
            if (!$project) {
                $this->db->insert('est_projects', [
                    'name' => 'Demo Project',
                    'client_name' => 'Sample Client',
                    'location' => 'Kathmandu, Nepal'
                ]);
                $projectId = $this->db->lastInsertId();
            } else {
                $projectId = $project['id'];
            }
        }

        $project = $this->db->findOne('est_projects', ['id' => $projectId]);
        $boqData = $this->db->findOne('est_boq_data', ['project_id' => $projectId]);

        $this->view->render('estimation/sheet', [
            'title' => 'Building Estimation & BOQ - ' . ($project['name'] ?? 'New Project'),
            'project' => $project,
            'gridData' => $boqData ? $boqData['grid_data'] : null
        ]);
    }

    /**
     * API: Get Item Master list for dropdowns
     */
    public function getItems()
    {
        header('Content-Type: application/json');
        $items = $this->db->find('est_item_master');
        echo json_encode(['success' => true, 'data' => $items]);
    }

    /**
     * API: Save Grid Data (JSON)
     */
    public function saveGrid()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['project_id']) || !isset($input['data'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid data']);
            return;
        }

        $projectId = $input['project_id'];
        $gridData = json_encode($input['data']);

        $existing = $this->db->findOne('est_boq_data', ['project_id' => $projectId]);
        
        if ($existing) {
            $this->db->update('est_boq_data', ['grid_data' => $gridData], 'project_id = :project_id', ['project_id' => $projectId]);
        } else {
            $this->db->insert('est_boq_data', [
                'project_id' => $projectId,
                'grid_data' => $gridData
            ]);
        }

        // Save version history
        $this->db->insert('est_boq_versions', [
            'project_id' => $projectId,
            'grid_data' => $gridData,
            'changed_by' => $_SESSION['user_id'] ?? 1,
            'change_description' => 'Auto-save'
        ]);

        echo json_encode(['success' => true]);
    }

    /**
     * UI: Rate Manager (Bulk Edit)
     */
    public function rates_manager()
    {
        $this->view->render('estimation/rates_manager', [
            'title' => 'District Rate Manager',
            'provinces' => $this->db->find('est_locations', ['type' => 'PROVINCE'])
        ]);
    }

    /**
     * API: Get Rates for Location
     */
    public function get_location_rates()
    {
        header('Content-Type: application/json');
        
        $locationId = $_GET['location_id'] ?? null;
        $muni = $_GET['muni'] ?? null;
        $district = $_GET['district'] ?? null;

        // Resolve ID if names provided
        if (!$locationId && $muni && $district) {
             $distRow = $this->db->findOne('est_locations', ['name' => $district, 'type' => 'DISTRICT']);
             if ($distRow) {
                 $muniRow = $this->db->findOne('est_locations', ['name' => $muni, 'type' => 'LOCAL_BODY', 'parent_id' => $distRow['id']]);
                 if ($muniRow) $locationId = $muniRow['id'];
             }
        }

        if (!$locationId) { echo json_encode(['success' => false, 'error' => 'Location not found']); return; }

        // Fetch all items and left join rates
        $sql = "
            SELECT 
                i.dudbc_code, 
                i.item_name, 
                i.unit, 
                COALESCE(r.rate, 0) as rate, 
                r.id as rate_id 
            FROM est_item_master i
            LEFT JOIN est_local_rates r ON i.dudbc_code = r.item_code AND r.location_id = :loc
            ORDER BY i.dudbc_code ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['loc' => $locationId]);
        $data = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'location_id' => $locationId, 'data' => $data]);
    }

    /**
     * API: Get Project Specific Rates (Key-Value Pair)
     * Returns: { "C-01": 850, "C-02": 5000 ... }
     */
    public function get_project_rates()
    {
        header('Content-Type: application/json');
        $projectId = $_GET['project_id'] ?? null;
        if (!$projectId) { echo json_encode([]); return; }

        $project = $this->db->findOne('est_projects', ['id' => $projectId]);
        if (!$project || !$project['location_id']) {
            echo json_encode([]); // No location set, return empty (use defaults)
            return;
        }

        // Fetch rates for this location
        $rates = $this->db->find('est_local_rates', ['location_id' => $project['location_id']]);
        
        $map = [];
        foreach ($rates as $r) {
            $map[$r['item_code']] = (float)$r['rate'];
        }
        
        echo json_encode($map);
    }

    /**
     * EXCEL EXPORT ENGINE
     * Generates a multi-sheet .xlsx file with formulas
     */
    public function export_excel()
    {
        $projectId = $_GET['project_id'] ?? null;
        if (!$projectId) die("Project ID required");

        $project = $this->db->findOne('est_projects', ['id' => $projectId]);
        $boqData = $this->db->findOne('est_boq_data', ['project_id' => $projectId]);

        if (!$boqData) die("No data found");

        $json = json_decode($boqData['grid_data'], true);
        $mbData = $json['mb'] ?? [];
        $absData = $json['abstract'] ?? [];
        $rateData = $json['rate'] ?? [];

        // Create Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // 1. Measurement Book Sheet
        $sheetMB = $spreadsheet->getActiveSheet();
        $sheetMB->setTitle('Measurement Book');
        $headersMB = ['S.N.', 'Description', 'No.', 'Length (m)', 'Breadth (m)', 'Height (m)', 'Quantity', 'Remarks'];
        $sheetMB->fromArray($headersMB, NULL, 'A1');
        
        $row = 2;
        foreach ($mbData as $r) {
            // JSpreadsheet: [SN, Desc, No, L, B, H, Qty, Rem, Code]
            // We map only visible columns A-H
            $sheetMB->setCellValue("A$row", $r[0]);
            $sheetMB->setCellValue("B$row", $r[1]);
            $sheetMB->setCellValue("C$row", $r[2]);
            $sheetMB->setCellValue("D$row", $r[3]);
            $sheetMB->setCellValue("E$row", $r[4]);
            $sheetMB->setCellValue("F$row", $r[5]);
            // Formula: No * L * B * H
            // If values conform, use formula, else value
            $sheetMB->setCellValue("G$row", "=PRODUCT(C$row:F$row)"); 
            $sheetMB->setCellValue("H$row", $r[7]);
            $row++;
        }
        // Styling
        $sheetMB->getStyle('A1:H1')->getFont()->setBold(true);
        $sheetMB->getColumnDimension('B')->setWidth(40);

        // 2. Abstract Sheet
        $sheetAbs = $spreadsheet->createSheet();
        $sheetAbs->setTitle('Abstract of Cost');
        $headersAbs = ['Code', 'Description', 'Unit', 'Quantity', 'Rate', 'Amount'];
        $sheetAbs->fromArray($headersAbs, NULL, 'A1');

        $row = 2;
        foreach ($absData as $r) {
            // [Code, Desc, Unit, Qty, Rate, Amount]
            $sheetAbs->setCellValue("A$row", $r[0]);
            $sheetAbs->setCellValue("B$row", $r[1]);
            $sheetAbs->setCellValue("C$row", $r[2]);
            $sheetAbs->setCellValue("D$row", $r[3]);
            $sheetAbs->setCellValue("E$row", $r[4]);
            // Formula: Qty * Rate
            $sheetAbs->setCellValue("F$row", "=D$row*E$row");
            $row++;
        }
        $sheetAbs->getStyle('A1:F1')->getFont()->setBold(true);
        $sheetAbs->getColumnDimension('B')->setWidth(40);

        // 3. Rate Analysis Sheet
        $sheetRate = $spreadsheet->createSheet();
        $sheetRate->setTitle('Rate Analysis');
        $sheetRate->fromArray(['Resource', 'Coeff', 'Market Rate'], NULL, 'A1');
        if($rateData) $sheetRate->fromArray($rateData, NULL, 'A2');

        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Estimate_' . $projectId . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * PDF EXPORT ENGINE (Government Standard)
     */
    public function export_pdf()
    {
        $projectId = $_GET['project_id'] ?? null;
        if (!$projectId) die("Project ID required");

        $project = $this->db->findOne('est_projects', ['id' => $projectId]);
        $boqData = $this->db->findOne('est_boq_data', ['project_id' => $projectId]);

        if (!$boqData) die("No data found");

        $json = json_decode($boqData['grid_data'], true);
        $mbData = $json['mb'] ?? [];
        $absData = $json['abstract'] ?? [];

        // Initialize mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        // Build HTML
        $html = $this->buildPdfHtml($project, $mbData, $absData);
        
        $mpdf->WriteHTML($html);
        $mpdf->Output('BOQ_' . $project['name'] . '.pdf', 'D');
    }

    private function buildPdfHtml($project, $mbData, $absData)
    {
        $html = '<style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; }
            h1 { text-align: center; color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
            h2 { background: #667eea; color: white; padding: 8px; margin-top: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background: #f0f0f0; font-weight: bold; padding: 8px; border: 1px solid #ddd; }
            td { padding: 6px; border: 1px solid #ddd; }
            tr:nth-child(even) { background: #f9f9f9; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .total-row { background: #667eea !important; color: white; font-weight: bold; }
            .header-info { margin-bottom: 20px; }
            .header-info p { margin: 5px 0; }
        </style>';

        $html .= '<h1>BILL OF QUANTITIES</h1>';
        
        $html .= '<div class="header-info">';
        $html .= '<p><strong>Project Name:</strong> ' . htmlspecialchars($project['name']) . '</p>';
        $html .= '<p><strong>Location:</strong> ' . htmlspecialchars($project['location'] ?? 'Not Set') . '</p>';
        $html .= '<p><strong>Date:</strong> ' . date('d/m/Y') . '</p>';
        $html .= '</div>';

        // Measurement Book
        $html .= '<h2>MEASUREMENT BOOK</h2>';
        $html .= '<table>';
        $html .= '<thead><tr>
            <th>S.N.</th>
            <th>Description</th>
            <th>No.</th>
            <th>L (m)</th>
            <th>B (m)</th>
            <th>H (m)</th>
            <th>Qty</th>
            <th>Remarks</th>
        </tr></thead><tbody>';
        
        foreach ($mbData as $row) {
            if (empty(array_filter($row))) continue;
            $html .= '<tr>';
            $html .= '<td class="text-center">' . ($row[0] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($row[1] ?? '') . '</td>';
            $html .= '<td class="text-right">' . ($row[2] ?? '') . '</td>';
            $html .= '<td class="text-right">' . ($row[3] ?? '') . '</td>';
            $html .= '<td class="text-right">' . ($row[4] ?? '') . '</td>';
            $html .= '<td class="text-right">' . ($row[5] ?? '') . '</td>';
            $html .= '<td class="text-right"><strong>' . ($row[6] ?? '') . '</strong></td>';
            $html .= '<td>' . htmlspecialchars($row[7] ?? '') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        // Abstract of Cost
        $html .= '<h2>ABSTRACT OF COST</h2>';
        $html .= '<table>';
        $html .= '<thead><tr>
            <th>Code</th>
            <th>Description</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Rate (Rs.)</th>
            <th>Amount (Rs.)</th>
        </tr></thead><tbody>';
        
        $grandTotal = 0;
        foreach ($absData as $row) {
            if (empty(array_filter($row))) continue;
            $qty = $row[3] ?? 0;
            $rate = $row[4] ?? 0;
            $amount = $qty * $rate;
            $grandTotal += $amount;
            
            $html .= '<tr>';
            $html .= '<td class="text-center">' . ($row[0] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($row[1] ?? '') . '</td>';
            $html .= '<td class="text-center">' . ($row[2] ?? '') . '</td>';
            $html .= '<td class="text-right">' . number_format($qty, 2) . '</td>';
            $html .= '<td class="text-right">' . number_format($rate, 2) . '</td>';
            $html .= '<td class="text-right">' . number_format($amount, 2) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '<tr class="total-row">';
        $html .= '<td colspan="5" class="text-right">GRAND TOTAL</td>';
        $html .= '<td class="text-right">Rs. ' . number_format($grandTotal, 2) . '</td>';
        $html .= '</tr>';
        $html .= '</tbody></table>';

        // Signature Section
        $html .= '<div style="margin-top: 40px;">';
        $html .= '<table style="border: none;"><tr>';
        $html .= '<td style="border: none; width: 50%;">Prepared by: _____________________</td>';
        $html .= '<td style="border: none; width: 50%;">Approved by: _____________________</td>';
        $html .= '</tr></table>';
        $html .= '</div>';
        return $html;
    }

    /**
     * API: Save Current BOQ as Template
     */
    public function save_template()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        $name = $input['name'] ?? null;
        $description = $input['description'] ?? '';
        $structureJson = json_encode($input['structure'] ?? []);
        
        if (!$name) {
            echo json_encode(['success' => false, 'error' => 'Template name required']);
            return;
        }
        
        $this->db->insert('est_templates', [
            'name' => $name,
            'description' => $description,
            'structure_json' => $structureJson,
            'created_by' => $_SESSION['user_id'] ?? 1
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Template saved successfully']);
    }

    /**
     * API: Get All Templates
     */
    public function get_templates()
    {
        header('Content-Type: application/json');
        $templates = $this->db->find('est_templates', [], 'created_at DESC');
        echo json_encode(['success' => true, 'templates' => $templates]);
    }

    /**
     * API: Load Template
     */
    public function load_template()
    {
        header('Content-Type: application/json');
        $templateId = $_GET['template_id'] ?? null;
        
        if (!$templateId) {
            echo json_encode(['success' => false, 'error' => 'Template ID required']);
            return;
        }
        
        $template = $this->db->findOne('est_templates', ['id' => $templateId]);
        
        if (!$template) {
            echo json_encode(['success' => false, 'error' => 'Template not found']);
            return;
        }
        
        $structure = json_decode($template['structure_json'], true);
        echo json_encode(['success' => true, 'structure' => $structure, 'name' => $template['name']]);
    }

    /**
     * API: Get Version History
     */
    public function get_versions()
    {
        header('Content-Type: application/json');
        $projectId = $_GET['project_id'] ?? null;
        
        if (!$projectId) {
            echo json_encode(['success' => false, 'error' => 'Project ID required']);
            return;
        }
        
        $versions = $this->db->find('est_boq_versions', ['project_id' => $projectId], 'created_at DESC', 20);
        echo json_encode(['success' => true, 'versions' => $versions]);
    }

    /**
     * API: Restore Version
     */
    public function restore_version()
    {
        header('Content-Type: application/json');
        $versionId = $_POST['version_id'] ?? null;
        
        if (!$versionId) {
            echo json_encode(['success' => false, 'error' => 'Version ID required']);
            return;
        }
        
        $version = $this->db->findOne('est_boq_versions', ['id' => $versionId]);
        
        if (!$version) {
            echo json_encode(['success' => false, 'error' => 'Version not found']);
            return;
        }
        
        // Restore to current
        $this->db->update('est_boq_data', 
            ['grid_data' => $version['grid_data']], 
            'project_id = :project_id', 
            ['project_id' => $version['project_id']]
        );
        
        $data = json_decode($version['grid_data'], true);
        echo json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * EXCEL IMPORT ENGINE
     * Parses uploaded .xlsx and converts to JSpreadsheet JSON
     */
    public function import_excel()
    {
        header('Content-Type: application/json');
        
        if (!isset($_FILES['excel_file']) || !isset($_POST['project_id'])) {
            echo json_encode(['success' => false, 'error' => 'Missing file or project ID']);
            return;
        }

        $projectId = $_POST['project_id'];
        $file = $_FILES['excel_file']['tmp_name'];

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            
            // Parse Measurement Book
            $sheetMB = $spreadsheet->getSheetByName('Measurement Book');
            $mbData = [];
            if ($sheetMB) {
                foreach ($sheetMB->getRowIterator(2) as $row) { // Skip header
                    $rowData = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    // Only add non-empty rows
                    if (array_filter($rowData)) {
                        $mbData[] = array_slice($rowData, 0, 9); // Limit to 9 cols (including hidden)
                    }
                }
            }

            // Parse Abstract
            $sheetAbs = $spreadsheet->getSheetByName('Abstract of Cost');
            $absData = [];
            if ($sheetAbs) {
                foreach ($sheetAbs->getRowIterator(2) as $row) {
                    $rowData = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    if (array_filter($rowData)) {
                        $absData[] = array_slice($rowData, 0, 6);
                    }
                }
            }

            // Parse Rate Analysis
            $sheetRate = $spreadsheet->getSheetByName('Rate Analysis');
            $rateData = [];
            if ($sheetRate) {
                foreach ($sheetRate->getRowIterator(2) as $row) {
                    $rowData = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    if (array_filter($rowData)) {
                        $rateData[] = array_slice($rowData, 0, 3);
                    }
                }
            }

            // Save to database
            $data = [
                'mb' => $mbData,
                'abstract' => $absData,
                'rate' => $rateData
            ];
            
            $gridData = json_encode($data);
            $existing = $this->db->findOne('est_boq_data', ['project_id' => $projectId]);
            
            if ($existing) {
                $this->db->update('est_boq_data', ['grid_data' => $gridData], 'project_id = :project_id', ['project_id' => $projectId]);
            } else {
                $this->db->insert('est_boq_data', [
                    'project_id' => $projectId,
                    'grid_data' => $gridData
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Data imported successfully']);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * API: Save Bulk Rates
     */
    public function save_bulk_rates()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $locationId = $input['location_id'];
        $rates = $input['rates']; // [{dudbc_code, rate}, ...]

        if (!$locationId || !$rates) {
            echo json_encode(['success' => false]);
            return;
        }

        foreach ($rates as $r) {
            // Check if exists
            $existing = $this->db->findOne('est_local_rates', ['item_code' => $r['dudbc_code'], 'location_id' => $locationId]);
            if ($existing) {
                $this->db->update('est_local_rates', ['rate' => $r['rate']], 'id = :id', ['id' => $existing['id']]);
            } else {
                $this->db->insert('est_local_rates', [
                    'item_code' => $r['dudbc_code'],
                    'location_id' => $locationId,
                    'rate' => $r['rate']
                ]);
            }
        }
        
        echo json_encode(['success' => true]);
    }

    /**
     * API: Update Project Location
     */
    public function update_location()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['project_id']) || !isset($input['muni'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid data']);
            return;
        }

        $projectId = $input['project_id'];
        $locationName = $input['location']; // "Muni, District"
        $muniName = $input['muni'];
        $districtName = $input['district'];

        // 1. Resolve Location ID from est_locations
        // We look for a LOCAL_BODY with this name, child of a DISTRICT with that name
        // Ideally we search by exact hierarchy, but name + type is usually safe enough for now.
        // Or we use the parent_id logic if we want to be strict.
        
        $locationId = null;
        
        // Find District ID first
        $dist = $this->db->findOne('est_locations', ['name' => $districtName, 'type' => 'DISTRICT']);
        if ($dist) {
            $muni = $this->db->findOne('est_locations', ['name' => $muniName, 'type' => 'LOCAL_BODY', 'parent_id' => $dist['id']]);
            if ($muni) {
                $locationId = $muni['id'];
            }
        }

        // 2. Update Project
        $updateData = ['location' => $locationName];
        if ($locationId) {
            $updateData['location_id'] = $locationId;
        }

        $this->db->update('est_projects', $updateData, 'id = :id', ['id' => $projectId]);

        echo json_encode(['success' => true, 'resolved_id' => $locationId]);
    }
}
