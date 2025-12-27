<?php
/**
 * Shared Calculator Template - Uses Calculator Engine
 * 
 * This template is used by all migrated calculators for consistent behavior.
 * Each calculator only needs to specify: ID, title, description, breadcrumbs
 */

use App\Engine\CalculatorEngine;

/**
 * Global Shared Calculator Template
 * 
 * Unified template for all Civil and Electrical calculators.
 * Supports both legacy (4 args) and modern (1 arg) calls.
 */
function renderCalculator($calculatorId, $pageTitle = null, $description = null, $breadcrumb = null) {
    // Ensure bootstrap is loaded (if not already)
    // Using dirname logic compatible with both civil and electrical locations
    // This file is in themes/default/views/shared/
    // So app is in ../../../../app
    if (!defined('APP_BASE_TABLE')) {
        require_once dirname(__DIR__, 4) . '/app/bootstrap.php';
    }
    
    $engine = new CalculatorEngine();
    $result = null;
    $error = null;
    
    // Get calculator metadata
    $metadata = $engine->getMetadata($calculatorId);
    
    // Auto-fill metadata if not provided arguments
    if (!$pageTitle && isset($metadata['name'])) {
        $page_title = $metadata['name']; // For header
        $pageTitle = $metadata['name'];
    }
    if (!$description && isset($metadata['description'])) {
        $description = $metadata['description'];
    }
    if (!$breadcrumb) {
        // Auto-generate breadcrumb based on category
        $cat = ucfirst($metadata['category'] ?? 'Calculator');
        $breadcrumb = [
            ['name' => 'Home', 'url' => app_base_url('/')],
            ['name' => $cat, 'url' => app_base_url(strtolower($cat))],
            ['name' => $pageTitle, 'url' => '#']
        ];
    }

    // Prepare inputs from metadata if available, to ensure order and types
    $formInputs = $metadata['inputs'] ?? [];
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $inputs = $_POST;
            $result = $engine->execute($calculatorId, $inputs);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }
    
    // Load header
    // We assume this file is included from a module file, so we need to find header relative to THIS file
    // "themes/default/views/shared" -> header is in "../partials/header.php"
    require_once __DIR__ . '/../partials/header.php';
    ?>
    
    <div class="container mt-4">
        <div class="calculator-wrapper ultra-card p-4">
            <h1 class="mb-2"><?php echo htmlspecialchars($pageTitle); ?></h1>
            <p class="text-muted mb-4"><?php echo htmlspecialchars($description); ?></p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger shadow-sm">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="calculator-form" class="row g-3">
                <?php foreach ($formInputs as $input): ?>
                    <div class="col-md-6 form-group">
                        <label for="<?php echo $input['name']; ?>" class="form-label fw-bold">
                            <?php echo htmlspecialchars($input['label']); ?>
                            <?php if (isset($input['unit']) && $input['unit']): ?>
                                <span class="text-muted small">(<?php echo htmlspecialchars($input['unit']); ?>)</span>
                            <?php endif; ?>
                        </label>
                        
                        <?php if (isset($input['options']) && is_array($input['options'])): ?>
                            <select id="<?php echo $input['name']; ?>" name="<?php echo $input['name']; ?>" class="form-select bg-dark text-light border-secondary">
                                <?php foreach ($input['options'] as $key => $option): 
                                    $optValue = is_numeric($key) ? $option : $key;
                                    $optLabel = $option;
                                ?>
                                    <option value="<?php echo htmlspecialchars($optValue); ?>" 
                                        <?php echo ($_POST[$input['name']] ?? $input['default'] ?? '') == $optValue ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($optLabel); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input 
                                type="<?php echo ($input['type'] ?? 'string') === 'integer' ? 'number' : 'text'; ?>" 
                                id="<?php echo $input['name']; ?>" 
                                name="<?php echo $input['name']; ?>"
                                class="form-control bg-dark text-light border-secondary" 
                                step="<?php echo ($input['type'] ?? 'string') === 'integer' ? '1' : 'any'; ?>"
                                value="<?php echo htmlspecialchars($_POST[$input['name']] ?? $input['default'] ?? ''); ?>"
                                <?php echo ($input['required'] ?? false) ? 'required' : ''; ?>>
                        <?php endif; ?>
                        
                        <?php if (isset($input['help']) || isset($input['help_text'])): ?>
                            <div class="form-text text-muted small">
                                <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($input['help'] ?? $input['help_text']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                        <i class="fas fa-calculator me-2"></i> Calculate Result
                    </button>
                </div>
            </form>
            
            <?php if ($result && $result['success']): ?>
                <div class="result-area mt-5 animate__animated animate__fadeIn" id="result-area">
                    <?php if (isset($result['results']['analysis'])): 
                        $analysis = $result['results']['analysis']['rows'] ?? [];
                    ?>
                        <div class="rate-analysis-card mt-4 p-4 rounded bg-dark border border-primary animate__animated animate__zoomIn">
                            <h4 class="text-primary mb-4 border-bottom pb-2"><i class="fas fa-file-invoice-dollar me-2"></i>Rate Analysis Detailed Breakdown</h4>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover border-secondary">
                                    <thead class="table-primary text-dark">
                                        <tr>
                                            <th>Description</th>
                                            <th>Detail</th>
                                            <th class="text-end">Amount (NPR)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><i class="fas fa-boxes me-2"></i>Materials Total</td>
                                            <td class="text-muted small">Sum of all material line items</td>
                                            <td class="text-end fw-bold"><?php echo number_format($analysis['mat_total'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-users me-2"></i>Labor Total</td>
                                            <td class="text-muted small">Sum of mason and laborer wages</td>
                                            <td class="text-end fw-bold"><?php echo number_format($analysis['lab_total'], 2); ?></td>
                                        </tr>
                                        <tr class="table-active">
                                            <td>Direct Cost</td>
                                            <td class="text-muted small">Materials + Labor</td>
                                            <td class="text-end fw-bold"><?php echo number_format($analysis['direct'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">Overhead & Profit</td>
                                            <td class="text-muted small"><?php echo $_POST['overhead'] ?? 15; ?>% of Direct Cost</td>
                                            <td class="text-end"><?php echo number_format($analysis['final'] - $analysis['direct'], 2); ?></td>
                                        </tr>
                                        <tr class="h4">
                                            <td class="text-success fw-bold">Grand Total</td>
                                            <td></td>
                                            <td class="text-end text-success fw-bold">NPR <?php echo number_format($analysis['final'], 2); ?></td>
                                        </tr>
                                        <tr class="h5 border-top border-primary">
                                            <td class="text-info">Rate per Unit</td>
                                            <td class="text-muted small"><?php echo htmlspecialchars($_POST['item_type']); ?></td>
                                            <td class="text-end text-info fw-bold">NPR <?php echo number_format($analysis['per_unit'], 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i> Export to PDF
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="results-grid">
                            <?php foreach ($result['results'] as $key => $data): ?>
                                <div class="result-item p-3 rounded mb-3 bg-dark border border-secondary">
                                    <div class="result-label text-muted small text-uppercase"><?php echo htmlspecialchars($data['label']); ?></div>
                                    <div class="result-value h2 mb-0 text-success"><?php echo htmlspecialchars($data['formatted']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="calculation-metadata text-end mt-2">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i> <?php echo $result['metadata']['execution_time']; ?>
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <style>
    .result-area {
        display: block !important;
        opacity: 1 !important;
    }
    .ultra-card {
        background: #1e1e1e; /* Dark theme compatible */
        border: 1px solid #333;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }
    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const resultArea = document.getElementById('result-area');
        if (resultArea && <?php echo ($result && $result['success']) ? 'true' : 'false'; ?>) {
            resultArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
    </script>
    
    <?php
    require_once __DIR__ . '/../partials/footer.php';
}
?>
