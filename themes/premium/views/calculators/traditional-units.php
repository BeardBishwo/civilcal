<div class="calculator-container premium-calculator" data-calculator-type="<?php echo $calculatorType ?? 'default'; ?>">
    
    <!-- Calculator Header -->
    <div class="calculator-header">
        <h2 class="calculator-title"><?php echo $calculatorTitle ?? 'Calculator'; ?></h2>
        <?php if (isset($calculatorDescription)): ?>
            <p class="calculator-description"><?php echo $calculatorDescription; ?></p>
        <?php endif; ?>
        
        <!-- Calculator Actions -->
        <div class="calculator-actions">
            <button class="btn-icon" onclick="resetCalculator()" title="Reset Calculator">
                <span>🔄</span> Reset
            </button>
            <button class="btn-icon" onclick="saveCalculation()" title="Save Calculation">
                <span>💾</span> Save
            </button>
            <button class="btn-icon" onclick="exportResults()" title="Export Results">
                <span>📊</span> Export
            </button>
            <button class="btn-icon premium-toggle" onclick="togglePremiumMode()" title="Premium Mode">
                <span>⭐</span> Premium
            </button>
        </div>
    </div>
    
    <!-- Calculator Form -->
    <form class="calculator-form" id="calculatorForm" data-auto-save="true">
        
        <!-- Input Sections -->
        <div class="input-sections">
            
            <?php if (isset($inputSections) && is_array($inputSections)): ?>
                <?php foreach ($inputSections as $sectionId => $section): ?>
                    <div class="input-section" id="section-<?php echo $sectionId; ?>">
                        <h3 class="section-title">
                            <?php echo $section['title'] ?? 'Section'; ?>
                            <?php if (isset($section['required']) && $section['required']): ?>
                                <span class="required-indicator">*</span>
                            <?php endif; ?>
                        </h3>
                        
                        <?php if (isset($section['description'])): ?>
                            <p class="section-description"><?php echo $section['description']; ?></p>
                        <?php endif; ?>
                        
                        <div class="form-grid">
                            <?php foreach ($section['fields'] as $fieldId => $field): ?>
                                <div class="form-group <?php echo $field['wrapper_class'] ?? ''; ?>">
                                    <label for="<?php echo $fieldId; ?>" class="form-label">
                                        <?php echo $field['label'] ?? $fieldId; ?>
                                        <?php if (isset($field['required']) && $field['required']): ?>
                                            <span class="required">*</span>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($field['tooltip'])): ?>
                                            <span class="tooltip-trigger" data-tooltip="<?php echo htmlspecialchars($field['tooltip']); ?>">ℹ️</span>
                                        <?php endif; ?>
                                    </label>
                                    
                                    <!-- Input Field -->
                                    <?php
                                    $fieldType = $field['type'] ?? 'text';
                                    $fieldValue = $field['value'] ?? '';
                                    $fieldOptions = $field['options'] ?? [];
                                    $fieldAttributes = $field['attributes'] ?? [];
                                    $fieldName = $field['name'] ?? $fieldId;
                                    $fieldIdAttr = $field['id'] ?? $fieldId;
                                    
                                    // Build attributes string
                                    $attributes = [];
                                    foreach ($fieldAttributes as $attrName => $attrValue) {
                                        $attributes[] = $attrName . '="' . htmlspecialchars($attrValue) . '"';
                                    }
                                    $attributesString = implode(' ', $attributes);
                                    ?>
                                    
                                    <?php if ($fieldType === 'select'): ?>
                                        <select 
                                            name="<?php echo $fieldName; ?>" 
                                            id="<?php echo $fieldIdAttr; ?>" 
                                            class="form-input form-select"
                                            <?php echo $attributesString; ?>
                                        >
                                            <?php foreach ($fieldOptions as $optionValue => $optionLabel): ?>
                                                <option value="<?php echo htmlspecialchars($optionValue); ?>" 
                                                        <?php echo ($fieldValue == $optionValue) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($optionLabel); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        
                                    <?php elseif ($fieldType === 'textarea'): ?>
                                        <textarea 
                                            name="<?php echo $fieldName; ?>" 
                                            id="<?php echo $fieldIdAttr; ?>" 
                                            class="form-input form-textarea"
                                            placeholder="<?php echo htmlspecialchars($field['placeholder'] ?? ''); ?>"
                                            rows="<?php echo $field['rows'] ?? 4; ?>"
                                            <?php echo $attributesString; ?>
                                        ><?php echo htmlspecialchars($fieldValue); ?></textarea>
                                        
                                    <?php elseif ($fieldType === 'checkbox'): ?>
                                        <div class="checkbox-group">
                                            <?php if (is_array($fieldValue)): ?>
                                                <?php foreach ($fieldOptions as $optionValue => $optionLabel): ?>
                                                    <label class="checkbox-label">
                                                        <input 
                                                            type="checkbox" 
                                                            name="<?php echo $fieldName; ?>[]" 
                                                            value="<?php echo htmlspecialchars($optionValue); ?>"
                                                            <?php echo in_array($optionValue, $fieldValue) ? 'checked' : ''; ?>
                                                            class="form-checkbox"
                                                            <?php echo $attributesString; ?>
                                                        >
                                                        <span class="checkbox-text"><?php echo htmlspecialchars($optionLabel); ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <label class="checkbox-label">
                                                    <input 
                                                        type="checkbox" 
                                                        name="<?php echo $fieldName; ?>" 
                                                        value="1"
                                                        <?php echo $fieldValue ? 'checked' : ''; ?>
                                                        class="form-checkbox"
                                                        <?php echo $attributesString; ?>
                                                    >
                                                    <span class="checkbox-text"><?php echo htmlspecialchars($field['checkbox_label'] ?? 'Enable'); ?></span>
                                                </label>
                                            <?php endif; ?>
                                        </div>
                                        
                                    <?php else: ?>
                                        <input 
                                            type="<?php echo $fieldType; ?>" 
                                            name="<?php echo $fieldName; ?>" 
                                            id="<?php echo $fieldIdAttr; ?>" 
                                            class="form-input"
                                            value="<?php echo htmlspecialchars($fieldValue); ?>"
                                            placeholder="<?php echo htmlspecialchars($field['placeholder'] ?? ''); ?>"
                                            <?php echo $attributesString; ?>
                                        >
                                    <?php endif; ?>
                                    
                                    <!-- Field Error Display -->
                                    <div class="field-error" id="error-<?php echo $fieldIdAttr; ?>"></div>
                                    
                                    <!-- Field Unit (if applicable) -->
                                    <?php if (isset($field['unit'])): ?>
                                        <div class="field-unit"><?php echo htmlspecialchars($field['unit']); ?></div>
                                    <?php endif; ?>
                                    
                                    <!-- Field Help Text -->
                                    <?php if (isset($field['help'])): ?>
                                        <div class="field-help"><?php echo htmlspecialchars($field['help']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Calculate Button -->
        <div class="calculate-section">
            <button type="submit" class="btn-calc btn-primary premium-btn" id="calculateBtn">
                <span class="btn-icon">🧮</span>
                <span class="btn-text">Calculate</span>
                <span class="btn-loading" style="display: none;">Calculating...</span>
            </button>
            
            <div class="calculation-options">
                <label class="option-label">
                    <input type="checkbox" id="autoCalculate" checked>
                    <span>Auto Calculate</span>
                </label>
                
                <label class="option-label">
                    <input type="checkbox" id="showSteps">
                    <span>Show Calculation Steps</span>
                </label>
                
                <label class="option-label">
                    <input type="checkbox" id="advancedMode">
                    <span>Advanced Mode</span>
                </label>
            </div>
        </div>
        
    </form>
    
    <!-- Results Section -->
    <div class="results-section" id="resultsSection" style="display: none;">
        <div class="results-header">
            <h3>Calculation Results</h3>
            <div class="results-actions">
                <button class="btn-icon" onclick="copyResults()">📋 Copy</button>
                <button class="btn-icon" onclick="printResults()">🖨️ Print</button>
                <button class="btn-icon" onclick="shareResults()">🔗 Share</button>
            </div>
        </div>
        
        <div class="results-content" id="resultsContent">
            <!-- Results will be populated here -->
        </div>
        
        <!-- Calculation Steps (if enabled) -->
        <div class="calculation-steps" id="calculationSteps" style="display: none;">
            <h4>Calculation Steps</h4>
            <div class="steps-content" id="stepsContent">
                <!-- Steps will be populated here -->
            </div>
        </div>
    </div>
    
    <!-- Saved Calculations -->
    <div class="saved-calculations" id="savedCalculations" style="display: none;">
        <h3>Saved Calculations</h3>
        <div class="saved-list" id="savedList">
            <!-- Saved calculations will be listed here -->
        </div>
    </div>
    
    <!-- Calculator History -->
    <div class="calculator-history" id="calculatorHistory">
        <h4>Recent Calculations</h4>
        <div class="history-list" id="historyList">
            <!-- History will be populated here -->
        </div>
    </div>
    
</div>

<!-- Calculator-specific JavaScript -->
<script>
// Calculator Configuration
window.calculatorConfig = {
    type: '<?php echo $calculatorType ?? "default"; ?>',
    autoCalculate: true,
    showSteps: false,
    advancedMode: false,
    premiumMode: <?php echo isset($premiumMode) ? 'true' : 'false'; ?>,
    precision: <?php echo $precision ?? 2; ?>,
    units: <?php echo json_encode($units ?? []); ?>,
    validation: <?php echo json_encode($validation ?? []); ?>
};

// Initialize calculator
document.addEventListener('DOMContentLoaded', function() {
    initializeCalculator();
});

function initializeCalculator() {
    const form = document.getElementById('calculatorForm');
    const autoCalculate = document.getElementById('autoCalculate');
    const showSteps = document.getElementById('showSteps');
    const advancedMode = document.getElementById('advancedMode');
    
    // Form submission
    if (form) {
        form.addEventListener('submit', handleCalculation);
    }
    
    // Auto-calculate toggle
    if (autoCalculate) {
        autoCalculate.addEventListener('change', function() {
            window.calculatorConfig.autoCalculate = this.checked;
        });
    }
    
    // Show steps toggle
    if (showSteps) {
        showSteps.addEventListener('change', function() {
            window.calculatorConfig.showSteps = this.checked;
        });
    }
    
    // Advanced mode toggle
    if (advancedMode) {
        advancedMode.addEventListener('change', function() {
            window.calculatorConfig.advancedMode = this.checked;
        });
    }
    
    // Auto-calculate on input change
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (window.calculatorConfig.autoCalculate) {
                debounce(handleCalculation, 500)();
            }
        });
    });
}

function handleCalculation(e) {
    if (e) e.preventDefault();
    
    const calculateBtn = document.getElementById('calculateBtn');
    const btnText = calculateBtn.querySelector('.btn-text');
    const btnLoading = calculateBtn.querySelector('.btn-loading');
    
    // Show loading state
    calculateBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    
    // Collect form data
    const formData = new FormData(document.getElementById('calculatorForm'));
    const data = Object.fromEntries(formData);
    
    // Send calculation request
    fetch('/api/calculate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            calculatorType: window.calculatorConfig.type,
            data: data,
            options: {
                showSteps: window.calculatorConfig.showSteps,
                advancedMode: window.calculatorConfig.advancedMode
            }
        })
    })
    .then(response => response.json())
    .then(result => {
        displayResults(result);
        saveCalculationToHistory(result);
    })
    .catch(error => {
        console.error('Calculation error:', error);
        displayError('Calculation failed. Please check your inputs.');
    })
    .finally(() => {
        // Reset button state
        calculateBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
    });
}

function displayResults(results) {
    const resultsSection = document.getElementById('resultsSection');
    const resultsContent = document.getElementById('resultsContent');
    
    if (results.success) {
        resultsContent.innerHTML = generateResultsHTML(results);
        resultsSection.style.display = 'block';
        
        // Show calculation steps if enabled
        if (window.calculatorConfig.showSteps && results.steps) {
            const stepsSection = document.getElementById('calculationSteps');
            const stepsContent = document.getElementById('stepsContent');
            stepsContent.innerHTML = generateStepsHTML(results.steps);
            stepsSection.style.display = 'block';
        }
        
        // Scroll to results
        resultsSection.scrollIntoView({ behavior: 'smooth' });
    } else {
        displayError(results.message || 'Calculation failed');
    }
}

function generateResultsHTML(results) {
    let html = '<div class="result-grid">';
    
    if (results.primary) {
        html += `<div class="result-item primary-result">
                    <div class="result-value">${results.primary.value}</div>
                    <div class="result-label">${results.primary.label}</div>
                </div>`;
    }
    
    if (results.secondary && Array.isArray(results.secondary)) {
        results.secondary.forEach(item => {
            html += `<div class="result-item">
                        <div class="result-value">${item.value}</div>
                        <div class="result-label">${item.label}</div>
                    </div>`;
        });
    }
    
    html += '</div>';
    
    if (results.notes) {
        html += `<div class="result-notes">${results.notes}</div>`;
    }
    
    return html;
}

function generateStepsHTML(steps) {
    let html = '<ol class="steps-list">';
    steps.forEach(step => {
        html += `<li class="step-item">
                    <div class="step-description">${step.description}</div>
                    <div class="step-formula">${step.formula || ''}</div>
                    <div class="step-result">${step.result || ''}</div>
                </li>`;
    });
    html += '</ol>';
    return html;
}

function displayError(message) {
    const resultsContent = document.getElementById('resultsContent');
    resultsContent.innerHTML = `<div class="error-message">${message}</div>`;
    document.getElementById('resultsSection').style.display = 'block';
}

function resetCalculator() {
    const form = document.getElementById('calculatorForm');
    form.reset();
    document.getElementById('resultsSection').style.display = 'none';
    document.getElementById('calculationSteps').style.display = 'none';
    clearErrors();
}

function saveCalculation() {
    // Implementation for saving calculation
    console.log('Save calculation');
}

function exportResults() {
    // Implementation for exporting results
    console.log('Export results');
}

function copyResults() {
    // Implementation for copying results
    console.log('Copy results');
}

function printResults() {
    // Implementation for printing results
    window.print();
}

function shareResults() {
    // Implementation for sharing results
    console.log('Share results');
}

function togglePremiumMode() {
    // Implementation for premium mode toggle
    console.log('Toggle premium mode');
}

function saveCalculationToHistory(result) {
    // Save to local storage
    const history = JSON.parse(localStorage.getItem('calculatorHistory') || '[]');
    history.unshift({
        timestamp: new Date().toISOString(),
        calculatorType: window.calculatorConfig.type,
        result: result
    });
    
    // Keep only last 10 calculations
    if (history.length > 10) {
        history.splice(10);
    }
    
    localStorage.setItem('calculatorHistory', JSON.stringify(history));
    updateHistoryDisplay();
}

function updateHistoryDisplay() {
    const history = JSON.parse(localStorage.getItem('calculatorHistory') || '[]');
    const historyList = document.getElementById('historyList');
    
    if (history.length === 0) {
        historyList.innerHTML = '<p class="no-history">No recent calculations</p>';
        return;
    }
    
    let html = '';
    history.forEach((item, index) => {
        const date = new Date(item.timestamp).toLocaleDateString();
        html += `<div class="history-item">
                    <div class="history-calc">${item.calculatorType}</div>
                    <div class="history-date">${date}</div>
                    <button class="btn-small" onclick="loadHistoryItem(${index})">Load</button>
                </div>`;
    });
    
    historyList.innerHTML = html;
}

function loadHistoryItem(index) {
    // Implementation for loading history item
    console.log('Load history item:', index);
}

function clearErrors() {
    const errorElements = document.querySelectorAll('.field-error');
    errorElements.forEach(element => {
        element.textContent = '';
    });
}

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize history on page load
document.addEventListener('DOMContentLoaded', updateHistoryDisplay);
</script>
