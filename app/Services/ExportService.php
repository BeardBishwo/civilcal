<?php
namespace App\Services;

use App\Models\ExportTemplate;
use App\Models\CalculationHistory;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Exception;
use DateTime;

/**
 * Export Service
 * Handles export functionality for calculation history
 */
class ExportService {
    private $exportTemplateModel;
    private $calculationHistoryModel;
    private $uploadPath;
    private $downloadUrl;

    public function __construct() {
        $this->exportTemplateModel = new ExportTemplate();
        $this->calculationHistoryModel = new CalculationHistory();
        $this->uploadPath = __DIR__ . '/../../public/uploads/exports/';
        $this->downloadUrl = '/uploads/exports/';
        
        // Ensure upload directory exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Export calculation history in specified format
     */
    public function exportCalculations($userId, $options = []) {
        $format = $options['format'] ?? 'pdf';
        $templateId = $options['template_id'] ?? null;
        $startDate = $options['start_date'] ?? null;
        $endDate = $options['end_date'] ?? null;
        $calculatorType = $options['calculator_type'] ?? null;
        $recordIds = $options['record_ids'] ?? null;

        // Get calculation history data
        $calculations = $this->getCalculationData($userId, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'calculator_type' => $calculatorType,
            'record_ids' => $recordIds
        ]);

        if (empty($calculations)) {
            throw new Exception('No calculation data found for export');
        }

        // Get template configuration
        $templateConfig = $this->getTemplateConfig($templateId, $format, $userId);

        // Generate filename
        $filename = $this->generateFilename($format, $userId);

        switch (strtolower($format)) {
            case 'pdf':
                return $this->exportToPdf($calculations, $filename, $templateConfig, $userId);
            case 'excel':
            case 'xlsx':
                return $this->exportToExcel($calculations, $filename, $templateConfig, $userId);
            case 'csv':
                return $this->exportToCsv($calculations, $filename, $templateConfig, $userId);
            case 'json':
                return $this->exportToJson($calculations, $filename, $templateConfig, $userId);
            default:
                throw new Exception("Unsupported export format: {$format}");
        }
    }

    /**
     * Export to PDF using mPDF
     */
    private function exportToPdf($calculations, $filename, $config, $userId) {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $config['page_size'] ?? 'A4',
            'orientation' => $config['orientation'] ?? 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15
        ]);

        $html = $this->generatePdfHtml($calculations, $config, $userId);
        
        $mpdf->WriteHTML($html);
        
        $filepath = $this->uploadPath . $filename;
        $mpdf->Output($filepath, \Mpdf\Output\Destination::FILE);

        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'download_url' => $this->downloadUrl . $filename,
            'size' => filesize($filepath)
        ];
    }

    /**
     * Generate PDF HTML content
     */
    private function generatePdfHtml($calculations, $config, $userId) {
        $html = '<html><head><meta charset="UTF-8">';
        $html .= '<style>';
        $html .= $this->getPdfStyles($config);
        $html .= '</style></head><body>';

        // Header
        if ($config['include_header'] ?? true) {
            $html .= $this->generatePdfHeader($config, $userId);
        }

        // Title
        $html .= '<h1 class="report-title">Calculation History Report</h1>';

        // Summary
        $html .= '<div class="summary">';
        $html .= '<p><strong>Total Records:</strong> ' . count($calculations) . '</p>';
        $html .= '<p><strong>Generated:</strong> ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '</div>';

        // Table
        $html .= '<table class="calculations-table">';
        $html .= '<thead><tr>';
        $html .= '<th>Date</th><th>Calculator Type</th><th>Input Parameters</th><th>Result</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($calculations as $calc) {
            $html .= '<tr>';
            $html .= '<td>' . date('Y-m-d H:i:s', strtotime($calc['created_at'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($calc['calculator_type']) . '</td>';
            $html .= '<td>' . $this->formatInputParameters($calc['input_parameters']) . '</td>';
            $html .= '<td>' . $this->formatResult($calc['result']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        // Footer
        if ($config['include_footer'] ?? true) {
            $html .= $this->generatePdfFooter($config);
        }

        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * Get PDF styles
     */
    private function getPdfStyles($config) {
        $fontSize = $this->getFontSize($config['font_size'] ?? 'medium');
        
        return "
            body { font-family: Arial, sans-serif; font-size: {$fontSize}px; }
            .header { text-align: center; margin-bottom: 20px; }
            .logo { max-height: 60px; margin-bottom: 10px; }
            .report-title { text-align: center; color: #333; margin-bottom: 20px; }
            .summary { background-color: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
            .calculations-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .calculations-table th, .calculations-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            .calculations-table th { background-color: #f2f2f2; font-weight: bold; }
            .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #666; }
        ";
    }

    /**
     * Generate PDF header
     */
    private function generatePdfHeader($config, $userId) {
        $html = '<div class="header">';
        
        if ($config['include_logo'] ?? true) {
            $html .= '<img src="' . $this->getLogoPath() . '" alt="Logo" class="logo">';
        }
        
        $html .= '<h2>Bishwo Calculator</h2>';
        $html .= '<p>Professional Calculation Reports</p>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Generate PDF footer
     */
    private function generatePdfFooter($config) {
        $html = '<div class="footer">';
        $html .= '<p>Generated by Bishwo Calculator on ' . date('Y-m-d H:i:s');
        if ($config['include_timestamp'] ?? true) {
            $html .= ' | Document ID: ' . uniqid();
        }
        $html .= '</p>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Export to Excel using PhpSpreadsheet
     */
    private function exportToExcel($calculations, $filename, $config, $userId) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set worksheet properties
        $sheet->setTitle('Calculation History');

        // Header row
        $headers = ['Date', 'Calculator Type', 'Input Parameters', 'Result', 'Calculation Time'];
        $sheet->fromArray($headers, null, 'A1');

        // Data rows
        $row = 2;
        foreach ($calculations as $calc) {
            $sheet->setCellValue('A' . $row, date('Y-m-d H:i:s', strtotime($calc['created_at'])));
            $sheet->setCellValue('B' . $row, $calc['calculator_type']);
            $sheet->setCellValue('C' . $row, $this->formatInputParameters($calc['input_parameters']));
            $sheet->setCellValue('D' . $row, $this->formatResult($calc['result']));
            $sheet->setCellValue('E' . $row, $this->calculateExecutionTime($calc));
            $row++;
        }

        // Apply formatting
        $this->applyExcelFormatting($sheet, $config, count($calculations));

        // Create writer and save file
        $writer = new Xlsx($spreadsheet);
        $filepath = $this->uploadPath . $filename;
        $writer->save($filepath);

        // Clean up
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'download_url' => $this->downloadUrl . $filename,
            'size' => filesize($filepath)
        ];
    }

    /**
     * Apply Excel formatting
     */
    private function applyExcelFormatting($sheet, $config, $rowCount) {
        $headerRange = 'A1:E1';
        $dataRange = 'A2:E' . ($rowCount + 1);

        // Header formatting
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->getStyle($headerRange)->applyFromArray($headerStyle);

        // Data formatting
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        $sheet->getStyle($dataRange)->applyFromArray($dataStyle);

        // Auto-size columns
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Freeze panes if enabled
        if ($config['freeze_panes'] ?? true) {
            $sheet->freezePane('A2');
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($calculations, $filename, $config, $userId) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['Date', 'Calculator Type', 'Input Parameters', 'Result', 'Calculation Time'];
        $sheet->fromArray($headers, null, 'A1');

        // Data
        $row = 2;
        foreach ($calculations as $calc) {
            $sheet->setCellValue('A' . $row, date('Y-m-d H:i:s', strtotime($calc['created_at'])));
            $sheet->setCellValue('B' . $row, $calc['calculator_type']);
            $sheet->setCellValue('C' . $row, $this->formatInputParameters($calc['input_parameters']));
            $sheet->setCellValue('D' . $row, $this->formatResult($calc['result']));
            $sheet->setCellValue('E' . $row, $this->calculateExecutionTime($calc));
            $row++;
        }

        // Create CSV writer
        $writer = new Csv($spreadsheet);
        $delimiter = $config['delimiter'] ?? ',';
        $writer->setDelimiter($delimiter);
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        
        $filepath = $this->uploadPath . $filename;
        $writer->save($filepath);

        // Clean up
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'download_url' => $this->downloadUrl . $filename,
            'size' => filesize($filepath)
        ];
    }

    /**
     * Export to JSON
     */
    private function exportToJson($calculations, $filename, $config, $userId) {
        $exportData = [];

        // Include metadata if requested
        if ($config['include_metadata'] ?? true) {
            $exportData['metadata'] = [
                'exported_by' => $userId,
                'export_date' => date('Y-m-d H:i:s'),
                'total_records' => count($calculations),
                'export_version' => '1.0',
                'template_id' => $config['template_id'] ?? null
            ];
        }

        // Include calculations
        $exportData['calculations'] = array_map(function($calc) {
            return [
                'id' => $calc['id'],
                'date' => $calc['created_at'],
                'calculator_type' => $calc['calculator_type'],
                'input_parameters' => json_decode($calc['input_parameters'], true),
                'result' => json_decode($calc['result'], true),
                'execution_time' => $this->calculateExecutionTime($calc)
            ];
        }, $calculations);

        // Add timestamp if requested
        if ($config['include_timestamp'] ?? true) {
            $exportData['generated_at'] = date('Y-m-d H:i:s');
            $exportData['document_id'] = uniqid();
        }

        // Format JSON
        $jsonOptions = 0;
        if ($config['pretty_print'] ?? true) {
            $jsonOptions = JSON_PRETTY_PRINT;
        }

        $jsonContent = json_encode($exportData, $jsonOptions);
        
        // Add UTF-8 BOM if requested
        if ($config['utf8_bom'] ?? false) {
            $jsonContent = "\xEF\xBB\xBF" . $jsonContent;
        }

        $filepath = $this->uploadPath . $filename;
        file_put_contents($filepath, $jsonContent);

        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'download_url' => $this->downloadUrl . $filename,
            'size' => filesize($filepath)
        ];
    }

    /**
     * Get calculation data based on filters
     */
    private function getCalculationData($userId, $filters = []) {
        return $this->calculationHistoryModel->getUserHistory($userId, $filters);
    }

    /**
     * Get template configuration
     */
    private function getTemplateConfig($templateId, $format, $userId) {
        if ($templateId) {
            return $this->exportTemplateModel->getConfigWithDefaults($templateId);
        }
        
        // Get default template for format
        $defaultTemplates = $this->exportTemplateModel->getDefaultTemplates($format);
        if (!empty($defaultTemplates)) {
            return $this->exportTemplateModel->getConfigWithDefaults($defaultTemplates[0]['id']);
        }
        
        // Return format-specific defaults
        return $this->getDefaultConfig($format);
    }

    /**
     * Get default configuration for format
     */
    private function getDefaultConfig($format) {
        return $this->exportTemplateModel->getConfigWithDefaults(['template_type' => $format]);
    }

    /**
     * Generate unique filename
     */
    private function generateFilename($format, $userId) {
        $timestamp = date('Y-m-d_H-i-s');
        $userPrefix = 'user_' . $userId;
        $extension = $this->getFileExtension($format);
        
        return "{$userPrefix}_calculations_{$timestamp}.{$extension}";
    }

    /**
     * Get file extension for format
     */
    private function getFileExtension($format) {
        $extensions = [
            'pdf' => 'pdf',
            'excel' => 'xlsx',
            'xlsx' => 'xlsx',
            'csv' => 'csv',
            'json' => 'json'
        ];
        
        return $extensions[strtolower($format)] ?? 'dat';
    }

    /**
     * Get logo path
     */
    private function getLogoPath() {
        $logoPath = __DIR__ . '/../../public/assets/images/logo.png';
        if (file_exists($logoPath)) {
            return $logoPath;
        }
        
        // Fallback to a simple text-based header
        return null;
    }

    /**
     * Format input parameters for display
     */
    private function formatInputParameters($inputParameters) {
        $params = json_decode($inputParameters, true);
        if (!$params) return 'N/A';
        
        $formatted = [];
        foreach ($params as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $formatted[] = $key . ': ' . json_encode($value);
            } else {
                $formatted[] = $key . ': ' . $value;
            }
        }
        
        return implode(', ', $formatted);
    }

    /**
     * Format result for display
     */
    private function formatResult($result) {
        $resultData = json_decode($result, true);
        if (!$resultData) return 'N/A';
        
        if (is_array($resultData)) {
            return implode(', ', array_slice($resultData, 0, 3)) . 
                   (count($resultData) > 3 ? '...' : '');
        }
        
        return is_string($resultData) ? $resultData : json_encode($resultData);
    }

    /**
     * Calculate execution time for a calculation
     */
    private function calculateExecutionTime($calculation) {
        // This is a simplified calculation - in reality you'd store execution time
        return 'N/A';
    }

    /**
     * Get font size based on config
     */
    private function getFontSize($size) {
        $sizes = [
            'small' => 10,
            'medium' => 12,
            'large' => 14
        ];
        
        return $sizes[$size] ?? 12;
    }

    /**
     * Clean up old export files
     */
    public function cleanupOldFiles($daysOld = 7) {
        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
        $files = glob($this->uploadPath . '*');
        
        $deleted = 0;
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                unlink($file);
                $deleted++;
            }
        }
        
        return $deleted;
    }

    /**
     * Get export statistics
     */
    public function getExportStats($userId) {
        $totalFiles = count(glob($this->uploadPath . "user_{$userId}_*"));
        $totalSize = 0;
        
        foreach (glob($this->uploadPath . "user_{$userId}_*") as $file) {
            $totalSize += filesize($file);
        }
        
        return [
            'total_exports' => $totalFiles,
            'total_size' => $this->formatBytes($totalSize),
            'average_size' => $totalFiles > 0 ? $this->formatBytes($totalSize / $totalFiles) : '0 B'
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
?>
