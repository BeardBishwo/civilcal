<?php
/**
 * Concrete Volume Calculator - Migrated to Calculator Engine
 * 
 * This file now uses the new Calculator Engine while maintaining
 * the same URL and user experience as the original implementation.
 * 
 * Old URL: /civil/concrete/concrete-volume
 * New Backend: Uses CalculatorEngine
 */

// Bootstrap application
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

use App\Engine\CalculatorEngine;

$page_title = 'Concrete Volume Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Concrete Volume', 'url' => '#']
];

// Initialize Calculator Engine
$engine = new CalculatorEngine();
$result = null;
$error = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $inputs = [
            'length' => $_POST['length'] ?? '',
            'width' => $_POST['width'] ?? '',
            'depth' => $_POST['depth'] ?? ''
        ];
        
        $result = $engine->execute('concrete-volume', $inputs);
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

// Load header
require_once dirname(__DIR__, 3) . '/themes/default/views/partials/header.php';
?>

<div class="container">
    <div class="calculator-wrapper">
        <h1>Concrete Volume Calculator</h1>
        <p class="text-muted">Calculate volume of concrete required for slabs, beams, and columns</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="concrete-volume-form">
            <div class="form-group">
                <label for="length">Length (m)</label>
                <input 
                    type="number" 
                    id="length" 
                    name="length"
                    class="form-control" 
                    step="0.01" 
                    value="<?php echo htmlspecialchars($_POST['length'] ?? ''); ?>"
                    required>
            </div>
            
            <div class="form-group">
                <label for="width">Width (m)</label>
                <input 
                    type="number" 
                    id="width" 
                    name="width"
                    class="form-control" 
                    step="0.01" 
                    value="<?php echo htmlspecialchars($_POST['width'] ?? ''); ?>"
                    required>
            </div>
            
            <div class="form-group">
                <label for="depth">Depth (m)</label>
                <input 
                    type="number" 
                    id="depth" 
                    name="depth"
                    class="form-control" 
                    step="0.01" 
                    value="<?php echo htmlspecialchars($_POST['depth'] ?? ''); ?>"
                    required>
            </div>
            
            <button type="submit" class="btn btn-primary">Calculate</button>
        </form>
        
        <?php if ($result && $result['success']): ?>
            <div class="result-area" id="result-area" style="display: block;">
                <h3>Results</h3>
                <div class="results-grid">
                    <?php foreach ($result['results'] as $key => $data): ?>
                        <div class="result-item">
                            <div class="result-label"><?php echo htmlspecialchars($data['label']); ?></div>
                            <div class="result-value"><?php echo htmlspecialchars($data['formatted']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="calculation-metadata">
                    <small class="text-muted">
                        Calculated in <?php echo $result['metadata']['execution_time']; ?> | 
                        Formula Version: <?php echo $result['metadata']['formula_version']; ?>
                    </small>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.result-item {
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.result-label {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 0.5rem;
}

.result-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #fff;
}

.calculation-metadata {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}
</style>

<?php
require_once dirname(__DIR__, 3) . '/themes/default/views/partials/footer.php';
?>
