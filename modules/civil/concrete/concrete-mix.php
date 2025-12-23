<?php
/**
 * Concrete Mix Design Calculator - Migrated to Calculator Engine
 * 
 * Calculate material quantities for concrete mix (cement, sand, aggregate, water)
 * 
 * URL: /civil/concrete/concrete-mix
 * Engine: CalculatorEngine
 */

require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
use App\Engine\CalculatorEngine;

$page_title = 'Concrete Mix Design Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Concrete Mix', 'url' => '#']
];

$engine = new CalculatorEngine();
$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $inputs = [
            'volume' => $_POST['volume'] ?? '',
            'mix_ratio' => $_POST['mix_ratio'] ?? '1:2:4'
        ];
        
        $result = $engine->execute('concrete-mix', $inputs);
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

require_once dirname(__DIR__, 3) . '/themes/default/views/partials/header.php';
?>

<div class="container">
    <div class="calculator-wrapper">
        <h1>Concrete Mix Design Calculator</h1>
        <p class="text-muted">Calculate material quantities for concrete mix (cement, sand, aggregate, water)</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="volume">Concrete Volume (m³)</label>
                <input type="number" id="volume" name="volume" class="form-control" step="0.01" 
                    value="<?php echo htmlspecialchars($_POST['volume'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="mix_ratio">Mix Ratio</label>
                <select id="mix_ratio" name="mix_ratio" class="form-control">
                    <option value="1:1.5:3" <?php echo ($_POST['mix_ratio'] ?? '') === '1:1.5:3' ? 'selected' : ''; ?>>1:1.5:3 (M25)</option>
                    <option value="1:2:4" <?php echo ($_POST['mix_ratio'] ?? '1:2:4') === '1:2:4' ? 'selected' : ''; ?>>1:2:4 (M20)</option>
                    <option value="1:3:6" <?php echo ($_POST['mix_ratio'] ?? '') === '1:3:6' ? 'selected' : ''; ?>>1:3:6 (M15)</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Calculate Mix</button>
        </form>
        
        <?php if ($result && $result['success']): ?>
            <div class="result-area" style="display: block;">
                <h3>Material Quantities</h3>
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
                        Mix Ratio: <?php echo htmlspecialchars($_POST['mix_ratio']); ?> | 
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
