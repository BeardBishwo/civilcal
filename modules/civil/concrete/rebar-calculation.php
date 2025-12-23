<?php
/**
 * Rebar Calculation Calculator - Migrated to Calculator Engine
 * 
 * Calculate steel reinforcement quantity and weight
 * 
 * URL: /civil/concrete/rebar-calculation
 * Engine: CalculatorEngine
 */

require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
use App\Engine\CalculatorEngine;

$page_title = 'Rebar/Reinforcement Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Rebar Calculation', 'url' => '#']
];

$engine = new CalculatorEngine();
$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $inputs = [
            'diameter' => $_POST['diameter'] ?? '',
            'length' => $_POST['length'] ?? '',
            'quantity' => $_POST['quantity'] ?? 1
        ];
        
        $result = $engine->execute('rebar-calculation', $inputs);
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

require_once dirname(__DIR__, 3) . '/themes/default/views/partials/header.php';
?>

<div class="container">
    <div class="calculator-wrapper">
        <h1>Rebar/Reinforcement Calculator</h1>
        <p class="text-muted">Calculate steel reinforcement quantity and weight for construction</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="diameter">Bar Diameter (mm)</label>
                <input type="number" id="diameter" name="diameter" class="form-control" step="1" min="6" max="50"
                    value="<?php echo htmlspecialchars($_POST['diameter'] ?? ''); ?>" required>
                <small class="form-text text-muted">Common sizes: 8, 10, 12, 16, 20, 25, 32 mm</small>
            </div>
            
            <div class="form-group">
                <label for="length">Total Length (m)</label>
                <input type="number" id="length" name="length" class="form-control" step="0.01"
                    value="<?php echo htmlspecialchars($_POST['length'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="quantity">Number of Bars</label>
                <input type="number" id="quantity" name="quantity" class="form-control" step="1" min="1"
                    value="<?php echo htmlspecialchars($_POST['quantity'] ?? 1); ?>" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Calculate Weight</button>
        </form>
        
        <?php if ($result && $result['success']): ?>
            <div class="result-area" style="display: block;">
                <h3>Steel Reinforcement Results</h3>
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
                        Calculated in <?php echo $result['metadata']['execution_time']; ?>
                    </small>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.results-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
.result-item { background: rgba(255, 255, 255, 0.05); padding: 1rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1); }
.result-label { font-size: 0.875rem; color: rgba(255, 255, 255, 0.7); margin-bottom: 0.5rem; }
.result-value { font-size: 1.5rem; font-weight: 600; color: #fff; }
.calculation-metadata { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1); }
</style>

<?php require_once dirname(__DIR__, 3) . '/themes/default/views/partials/footer.php'; ?>
