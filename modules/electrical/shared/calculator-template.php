<?php
/**
 * Shared Calculator Template for Electrical Calculators
 * 
 * This template provides a consistent interface for all electrical calculators
 * using the Calculator Engine. It handles form rendering, calculation execution,
 * and result display.
 * 
 * Usage: renderCalculator('calculator-id');
 */

use App\Engine\CalculatorEngine;

function renderCalculator($calculatorId) {
    // Initialize Calculator Engine
    $engine = new CalculatorEngine();
    $result = null;
    $error = null;
    
    // Get calculator metadata
    $metadata = $engine->getMetadata($calculatorId);
    
    if (!$metadata['success']) {
        die("Calculator not found: $calculatorId");
    }
    
    $page_title = $metadata['name'];
    $breadcrumb = [
        ['name' => 'Home', 'url' => app_base_url('/')],
        ['name' => 'Electrical', 'url' => app_base_url('electrical')],
        ['name' => $metadata['name'], 'url' => '#']
    ];
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $inputs = [];
            foreach ($metadata['inputs'] as $input) {
                $inputs[$input['name']] = $_POST[$input['name']] ?? '';
            }
            
            $result = $engine->execute($calculatorId, $inputs);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }
    
    // Load header
    require_once dirname(__DIR__, 3) . '/themes/default/views/partials/header.php';
    ?>
    
    <div class="container">
        <div class="calculator-wrapper">
            <h1><?php echo htmlspecialchars($metadata['name']); ?></h1>
            <p class="text-muted"><?php echo htmlspecialchars($metadata['description']); ?></p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="calculator-form">
                <?php foreach ($metadata['inputs'] as $input): ?>
                    <div class="form-group">
                        <label for="<?php echo htmlspecialchars($input['name']); ?>">
                            <?php echo htmlspecialchars($input['label']); ?>
                            <?php if (isset($input['unit']) && $input['unit']): ?>
                                (<?php echo htmlspecialchars($input['unit']); ?>)
                            <?php endif; ?>
                        </label>
                        
                        <?php if (isset($input['options']) && is_array($input['options'])): ?>
                            <!-- Dropdown for options -->
                            <select 
                                id="<?php echo htmlspecialchars($input['name']); ?>" 
                                name="<?php echo htmlspecialchars($input['name']); ?>"
                                class="form-control"
                                <?php echo ($input['required'] ?? false) ? 'required' : ''; ?>>
                                <?php foreach ($input['options'] as $option): ?>
                                    <option value="<?php echo htmlspecialchars($option); ?>"
                                        <?php echo (isset($_POST[$input['name']]) && $_POST[$input['name']] == $option) || 
                                                   (!isset($_POST[$input['name']]) && isset($input['default']) && $input['default'] == $option) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input 
                                type="<?php echo ($input['type'] === 'integer' || $input['type'] === 'number') ? 'number' : 'text'; ?>" 
                                id="<?php echo htmlspecialchars($input['name']); ?>" 
                                name="<?php echo htmlspecialchars($input['name']); ?>"
                                class="form-control"
                                <?php if (isset($input['step'])): ?>
                                    step="<?php echo htmlspecialchars($input['step']); ?>"
                                <?php elseif ($input['type'] === 'number' || $input['type'] === 'integer'): ?>
                                    step="any"
                                <?php endif; ?>
                                <?php if (isset($input['min'])): ?>min="<?php echo htmlspecialchars($input['min']); ?>"<?php endif; ?>
                                <?php if (isset($input['max'])): ?>max="<?php echo htmlspecialchars($input['max']); ?>"<?php endif; ?>
                                value="<?php echo htmlspecialchars($_POST[$input['name']] ?? $input['default'] ?? ''); ?>"
                                <?php echo ($input['required'] ?? false) ? 'required' : ''; ?>>
                        <?php endif; ?>
                        
                        <?php if (isset($input['help'])): ?>
                            <small class="form-text text-muted"><?php echo htmlspecialchars($input['help']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
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
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 4px;
        background: rgba(0, 0, 0, 0.2);
        color: #fff;
        font-size: 1rem;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #4ecdc4;
        box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.1);
    }
    
    .btn-primary {
        padding: 0.75rem 2rem;
        background: linear-gradient(45deg, #4ecdc4, #44a08d);
        border: none;
        border-radius: 4px;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
    }
    
    .alert {
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    
    .alert-danger {
        background: rgba(244, 67, 54, 0.1);
        border: 1px solid rgba(244, 67, 54, 0.3);
        color: #f44336;
    }
    </style>
    
    <?php
    require_once dirname(__DIR__, 3) . '/themes/default/views/partials/footer.php';
}
