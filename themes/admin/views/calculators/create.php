<?php
/**
 * Calculator Builder - Create New Calculator
 * Premium SaaS-grade visual builder interface
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Create Calculator' ?></title>
    <link rel="stylesheet" href="<?= APP_BASE ?>/themes/admin/assets/css/admin.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .builder-container {
            display: grid;
            grid-template-columns: 300px 1fr 400px;
            gap: 1.5rem;
            height: calc(100vh - 200px);
        }
        
        .field-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            cursor: move;
            transition: all 0.3s ease;
        }
        
        .field-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .drop-zone {
            border: 2px dashed #cbd5e0;
            border-radius: 0.5rem;
            padding: 2rem;
            min-height: 200px;
            background: #f7fafc;
            transition: all 0.3s ease;
        }
        
        .drop-zone.drag-over {
            border-color: #667eea;
            background: #edf2f7;
        }
        
        .preview-panel {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .formula-editor {
            font-family: 'Courier New', monospace;
            background: #2d3748;
            color: #68d391;
            padding: 1rem;
            border-radius: 0.5rem;
            min-height: 100px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Create Calculator</h1>
                        <p class="text-gray-600 mt-2">Build your calculator with our visual builder</p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="saveCalculator()" class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                            💾 Save Calculator
                        </button>
                        <a href="<?= APP_BASE ?>/admin/calculators" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Basic Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Calculator Name *</label>
                        <input type="text" id="calc-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., Concrete Volume Calculator">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                        <input type="text" id="calc-slug" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., concrete-volume">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select id="calc-category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat ?>"><?= ucwords(str_replace('-', ' ', $cat)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subcategory</label>
                        <input type="text" id="calc-subcategory" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., concrete">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea id="calc-description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Describe what this calculator does..."></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Template Library -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">📚 Start from Template</h2>
                <div class="grid grid-cols-3 gap-4">
                    <?php foreach ($templates as $key => $template): ?>
                        <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-purple-500 cursor-pointer transition" onclick="loadTemplate('<?= $key ?>')">
                            <h3 class="font-bold text-lg mb-2"><?= $template['name'] ?></h3>
                            <p class="text-sm text-gray-600"><?= $template['description'] ?></p>
                            <div class="mt-3 text-xs text-gray-500">
                                <?= count($template['inputs']) ?> inputs • <?= count($template['outputs']) ?> outputs
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Visual Builder -->
            <div class="builder-container">
                <!-- Left: Field Types -->
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="font-bold text-lg mb-4">📦 Field Types</h3>
                    <div class="space-y-3">
                        <div class="field-item" draggable="true" data-type="number">
                            <div class="font-semibold">🔢 Number</div>
                            <div class="text-xs opacity-75">Numeric input</div>
                        </div>
                        <div class="field-item" draggable="true" data-type="dropdown">
                            <div class="font-semibold">📋 Dropdown</div>
                            <div class="text-xs opacity-75">Select options</div>
                        </div>
                        <div class="field-item" draggable="true" data-type="slider">
                            <div class="font-semibold">🎚️ Slider</div>
                            <div class="text-xs opacity-75">Range input</div>
                        </div>
                        <div class="field-item" draggable="true" data-type="radio">
                            <div class="font-semibold">⚫ Radio</div>
                            <div class="text-xs opacity-75">Single choice</div>
                        </div>
                    </div>
                </div>
                
                <!-- Center: Builder Area -->
                <div class="space-y-4">
                    <!-- Inputs -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-lg mb-4">📥 Input Fields</h3>
                        <div id="inputs-zone" class="drop-zone">
                            <p class="text-gray-400 text-center">Drag field types here</p>
                        </div>
                    </div>
                    
                    <!-- Formulas -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-lg mb-4">🧮 Formulas</h3>
                        <div id="formulas-container">
                            <button onclick="addFormula()" class="w-full bg-purple-100 text-purple-700 py-3 rounded-lg font-semibold hover:bg-purple-200 transition">
                                + Add Formula
                            </button>
                        </div>
                    </div>
                    
                    <!-- Outputs -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-lg mb-4">📤 Output Fields</h3>
                        <div id="outputs-zone" class="drop-zone">
                            <p class="text-gray-400 text-center">Define output fields</p>
                        </div>
                        <button onclick="addOutput()" class="mt-4 w-full bg-green-100 text-green-700 py-3 rounded-lg font-semibold hover:bg-green-200 transition">
                            + Add Output
                        </button>
                    </div>
                </div>
                
                <!-- Right: Live Preview -->
                <div class="preview-panel">
                    <h3 class="font-bold text-lg mb-4">👁️ Live Preview</h3>
                    <div id="preview-area" class="border-2 border-gray-200 rounded-lg p-4 min-h-[400px]">
                        <p class="text-gray-400 text-center">Preview will appear here</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        const templates = <?= json_encode($templates) ?>;
        let calculatorData = {
            inputs: [],
            formulas: [],
            outputs: []
        };
        
        // Auto-generate slug from name
        document.getElementById('calc-name').addEventListener('input', (e) => {
            const slug = e.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            document.getElementById('calc-slug').value = slug;
        });
        
        // Load template
        function loadTemplate(key) {
            const template = templates[key];
            if (!template) return;
            
            calculatorData.inputs = template.inputs;
            calculatorData.formulas = Object.entries(template.formulas).map(([name, formula]) => ({name, formula}));
            calculatorData.outputs = template.outputs;
            
            renderBuilder();
            updatePreview();
        }
        
        // Add formula
        function addFormula() {
            const name = prompt('Formula name (e.g., result):');
            if (!name) return;
            
            const formula = prompt('Formula expression (e.g., value1 + value2):');
            if (!formula) return;
            
            calculatorData.formulas.push({name, formula});
            renderBuilder();
        }
        
        // Add output
        function addOutput() {
            const name = prompt('Output name:');
            if (!name) return;
            
            const label = prompt('Output label:');
            const unit = prompt('Unit (optional):') || '';
            
            calculatorData.outputs.push({name, label, unit, precision: 2});
            renderBuilder();
            updatePreview();
        }
        
        // Render builder
        function renderBuilder() {
            // Render inputs
            const inputsZone = document.getElementById('inputs-zone');
            inputsZone.innerHTML = calculatorData.inputs.map((input, i) => `
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold">${input.label || input.name}</div>
                            <div class="text-sm text-gray-600">${input.type} ${input.unit ? `(${input.unit})` : ''}</div>
                        </div>
                        <button onclick="removeInput(${i})" class="text-red-500 hover:text-red-700">🗑️</button>
                    </div>
                </div>
            `).join('') || '<p class="text-gray-400 text-center">No inputs yet</p>';
            
            // Render formulas
            const formulasContainer = document.getElementById('formulas-container');
            const formulasHTML = calculatorData.formulas.map((f, i) => `
                <div class="mb-3">
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-semibold">${f.name} =</label>
                        <button onclick="removeFormula(${i})" class="text-red-500 hover:text-red-700">🗑️</button>
                    </div>
                    <div class="formula-editor">${f.formula}</div>
                </div>
            `).join('');
            
            formulasContainer.innerHTML = formulasHTML + `
                <button onclick="addFormula()" class="w-full bg-purple-100 text-purple-700 py-3 rounded-lg font-semibold hover:bg-purple-200 transition mt-3">
                    + Add Formula
                </button>
            `;
            
            // Render outputs
            const outputsZone = document.getElementById('outputs-zone');
            outputsZone.innerHTML = calculatorData.outputs.map((output, i) => `
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold">${output.label || output.name}</div>
                            <div class="text-sm text-gray-600">${output.unit || 'No unit'}</div>
                        </div>
                        <button onclick="removeOutput(${i})" class="text-red-500 hover:text-red-700">🗑️</button>
                    </div>
                </div>
            `).join('') || '<p class="text-gray-400 text-center">No outputs yet</p>';
        }
        
        // Update preview
        function updatePreview() {
            const preview = document.getElementById('preview-area');
            const name = document.getElementById('calc-name').value || 'Calculator Preview';
            
            let html = `<h2 class="text-2xl font-bold mb-4">${name}</h2>`;
            
            // Inputs
            calculatorData.inputs.forEach(input => {
                html += `
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">${input.label || input.name}</label>
                        <input type="${input.type}" class="w-full px-4 py-2 border rounded-lg" placeholder="${input.unit || ''}">
                    </div>
                `;
            });
            
            // Calculate button
            if (calculatorData.inputs.length > 0) {
                html += `<button class="w-full gradient-bg text-white py-3 rounded-lg font-semibold mb-4">Calculate</button>`;
            }
            
            // Outputs
            calculatorData.outputs.forEach(output => {
                html += `
                    <div class="mb-4 bg-green-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-600">${output.label || output.name}</div>
                        <div class="text-2xl font-bold text-green-700">-- ${output.unit || ''}</div>
                    </div>
                `;
            });
            
            preview.innerHTML = html || '<p class="text-gray-400 text-center">Add inputs and outputs to see preview</p>';
        }
        
        // Remove functions
        function removeInput(i) {
            calculatorData.inputs.splice(i, 1);
            renderBuilder();
            updatePreview();
        }
        
        function removeFormula(i) {
            calculatorData.formulas.splice(i, 1);
            renderBuilder();
        }
        
        function removeOutput(i) {
            calculatorData.outputs.splice(i, 1);
            renderBuilder();
            updatePreview();
        }
        
        // Save calculator
        async function saveCalculator() {
            const data = {
                name: document.getElementById('calc-name').value,
                slug: document.getElementById('calc-slug').value,
                category: document.getElementById('calc-category').value,
                subcategory: document.getElementById('calc-subcategory').value,
                description: document.getElementById('calc-description').value,
                inputs: calculatorData.inputs,
                formulas: Object.fromEntries(calculatorData.formulas.map(f => [f.name, f.formula])),
                outputs: calculatorData.outputs
            };
            
            if (!data.name || !data.slug || !data.category || !data.description) {
                alert('Please fill in all required fields');
                return;
            }
            
            try {
                const response = await fetch('<?= APP_BASE ?>/admin/calculators/store', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Calculator created successfully!');
                    window.location.href = '<?= APP_BASE ?>/admin/calculators';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error saving calculator: ' + error.message);
            }
        }
        
        // Initialize
        updatePreview();
    </script>
</body>
</html>
