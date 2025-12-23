<?php
/**
 * Shared Calculator Template - Uses Calculator Engine
 * 
 * This template is used by all migrated calculators for consistent behavior.
 * Each calculator only needs to specify: ID, title, description, breadcrumbs
 */

function renderCalculator($calculatorId, $pageTitle, $description, $breadcrumb) {
    require_once dirname(__DIR__, 4) . '/app/bootstrap.php';
    
    $engine = new \App\Engine\CalculatorEngine();
    $result = null;
    $error = null;
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $result = $engine->execute($calculatorId, $_POST);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }
    
    // Get calculator metadata for form fields
    $metadata = $engine->getMetadata($calculatorId);
    
    // Load header
    require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';
    ?>
    
    <div class="container">
        <div class="calculator-wrapper">
            <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            <p class="text-muted"><?php echo htmlspecialchars($description); ?></p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <?php foreach ($metadata['inputs'] as $input): ?>
                    <div class="form-group">
                        <label for="<?php echo $input['name']; ?>">
                            <?php echo htmlspecialchars($input['label']); ?>
                            <?php if (isset($input['unit'])): ?>
                                (<?php echo htmlspecialchars($input['unit']); ?>)
                            <?php endif; ?>
                        </label>
                        
                        <?php if (isset($input['options'])): ?>
                            <select id="<?php echo $input['name']; ?>" name="<?php echo $input['name']; ?>" class="form-control">
                                <?php foreach ($input['options'] as $option): ?>
                                    <option value="<?php echo htmlspecialchars($option); ?>" 
                                        <?php echo ($_POST[$input['name']] ?? $input['default'] ?? '') === $option ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input 
                                type="<?php echo $input['type'] === 'integer' ? 'number' : 'text'; ?>" 
                                id="<?php echo $input['name']; ?>" 
                                name="<?php echo $input['name']; ?>"
                                class="form-control" 
                                step="<?php echo $input['type'] === 'integer' ? '1' : '0.01'; ?>"
                                value="<?php echo htmlspecialchars($_POST[$input['name']] ?? $input['default'] ?? ''); ?>"
                                <?php echo $input['required'] ? 'required' : ''; ?>>
                        <?php endif; ?>
                        
                        <?php if (isset($input['help_text'])): ?>
                            <small class="form-text text-muted"><?php echo htmlspecialchars($input['help_text']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <button type="submit" class="btn btn-primary">Calculate</button>
            </form>
            
            <?php if ($result && $result['success']): ?>
                <div class="result-area" style="display: block;">
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
    
    <?php
    require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php';
}
?>
