<?php
/**
 * EDIT SUBSCRIPTION PLAN - PREMIUM DESIGN
 * Matching the Create Plan Interface Style
 */

// Get plan data (passed from controller)
$plan = $plan ?? [
    'id' => 0,
    'name' => '',
    'description' => '',
    'price_monthly' => 0,
    'price_yearly' => 0,
    'features' => [],
    'is_active' => 1
];

// Ensure features is an array
if (is_string($plan['features'])) {
    $plan['features'] = json_decode($plan['features'], true) ?? [];
}
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-edit"></i>
                    <h1>Edit Subscription Plan</h1>
                </div>
                <div class="header-subtitle">Update and modify your subscription plan settings</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/subscriptions'); ?>" class="btn btn-light btn-compact">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Plans</span>
                </a>
            </div>
        </div>

        <!-- Main Form Content -->
        <div class="form-content-area">
            <form id="editPlanForm" method="POST" action="<?php echo app_base_url('/admin/subscriptions/update/' . $plan['id']); ?>">
                <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                
                <div class="form-grid">
                    <!-- Left Column -->
                    <div class="form-column-left">
                        
                        <!-- Basic Information Card -->
                        <div class="form-card-compact">
                            <div class="form-card-header">
                                <div class="form-card-title">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Basic Information</span>
                                </div>
                            </div>
                            <div class="form-card-body">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        Plan Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-input-compact" 
                                           name="name" 
                                           value="<?php echo htmlspecialchars($plan['name']); ?>"
                                           placeholder="e.g., Professional Plan" 
                                           required>
                                    <small class="form-hint">Choose a clear, descriptive name for your plan</small>
                                </div>

                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        Description <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-input-compact" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Describe what makes this plan unique..." 
                                              required><?php echo htmlspecialchars($plan['description']); ?></textarea>
                                    <small class="form-hint">Brief description shown to customers</small>
                                </div>

                                <div class="form-group-compact">
                                    <label class="form-label-compact">Plan Status</label>
                                    <div class="status-toggle-group">
                                        <label class="status-option">
                                            <input type="radio" name="is_active" value="1" <?php echo $plan['is_active'] ? 'checked' : ''; ?>>
                                            <span class="status-option-label status-active">
                                                <i class="fas fa-check-circle"></i>
                                                Active
                                            </span>
                                        </label>
                                        <label class="status-option">
                                            <input type="radio" name="is_active" value="0" <?php echo !$plan['is_active'] ? 'checked' : ''; ?>>
                                            <span class="status-option-label status-inactive">
                                                <i class="fas fa-pause-circle"></i>
                                                Inactive
                                            </span>
                                        </label>
                                    </div>
                                    <small class="form-hint">Inactive plans won't be visible to customers</small>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Card -->
                        <div class="form-card-compact">
                            <div class="form-card-header">
                                <div class="form-card-title">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>Pricing</span>
                                </div>
                            </div>
                            <div class="form-card-body">
                                <div class="pricing-grid">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact">
                                            Monthly Price <span class="text-danger">*</span>
                                        </label>
                                        <div class="price-input-group">
                                            <span class="price-currency">$</span>
                                            <input type="number" 
                                                   class="form-input-compact price-input" 
                                                   name="price_monthly" 
                                                   step="0.01" 
                                                   min="0" 
                                                   value="<?php echo number_format($plan['price_monthly'], 2, '.', ''); ?>"
                                                   placeholder="0.00"
                                                   id="price_monthly"
                                                   required>
                                            <span class="price-period">/month</span>
                                        </div>
                                    </div>

                                    <div class="form-group-compact">
                                        <label class="form-label-compact">
                                            Yearly Price <span class="text-danger">*</span>
                                        </label>
                                        <div class="price-input-group">
                                            <span class="price-currency">$</span>
                                            <input type="number" 
                                                   class="form-input-compact price-input" 
                                                   name="price_yearly" 
                                                   step="0.01" 
                                                   min="0" 
                                                   value="<?php echo number_format($plan['price_yearly'], 2, '.', ''); ?>"
                                                   placeholder="0.00"
                                                   id="price_yearly"
                                                   required>
                                            <span class="price-period">/year</span>
                                        </div>
                                        <div class="savings-indicator" id="savings-indicator" style="display: none;">
                                            <i class="fas fa-tag"></i>
                                            <span id="savings-text">Save $0.00 per year</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column -->
                    <div class="form-column-right">
                        
                        <!-- Features Card -->
                        <div class="form-card-compact">
                            <div class="form-card-header">
                                <div class="form-card-title">
                                    <i class="fas fa-list-check"></i>
                                    <span>Plan Features</span>
                                </div>
                                <button type="button" class="btn-add-feature" id="addFeatureBtn">
                                    <i class="fas fa-plus"></i>
                                    Add Feature
                                </button>
                            </div>
                            <div class="form-card-body">
                                <div id="featuresList" class="features-list">
                                    <!-- Features will be populated by JavaScript -->
                                </div>
                                <small class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    Add features that make this plan valuable
                                </small>
                            </div>
                        </div>

                        <!-- Preview Card -->
                        <div class="form-card-compact preview-card">
                            <div class="form-card-header">
                                <div class="form-card-title">
                                    <i class="fas fa-eye"></i>
                                    <span>Plan Preview</span>
                                </div>
                            </div>
                            <div class="form-card-body">
                                <div class="plan-preview">
                                    <div class="preview-header">
                                        <h3 class="preview-plan-name" id="previewName"><?php echo htmlspecialchars($plan['name']) ?: 'Plan Name'; ?></h3>
                                        <div class="preview-status" id="previewStatus">
                                            <span class="status-badge <?php echo $plan['is_active'] ? 'status-published' : 'status-draft'; ?>">
                                                <i class="fas fa-<?php echo $plan['is_active'] ? 'check-circle' : 'pause-circle'; ?>"></i>
                                                <?php echo $plan['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <p class="preview-description" id="previewDescription"><?php echo htmlspecialchars($plan['description']) ?: 'Plan description will appear here...'; ?></p>
                                    <div class="preview-pricing">
                                        <div class="preview-price-item">
                                            <span class="preview-price-label">Monthly</span>
                                            <span class="preview-price-value" id="previewMonthly">$<?php echo number_format($plan['price_monthly'], 2); ?></span>
                                        </div>
                                        <div class="preview-price-item">
                                            <span class="preview-price-label">Yearly</span>
                                            <span class="preview-price-value" id="previewYearly">$<?php echo number_format($plan['price_yearly'], 2); ?></span>
                                        </div>
                                    </div>
                                    <div class="preview-features">
                                        <div class="preview-features-title">Features:</div>
                                        <ul id="previewFeaturesList">
                                            <?php if (!empty($plan['features'])): ?>
                                                <?php foreach ($plan['features'] as $feature): ?>
                                                    <li><?php echo htmlspecialchars($feature); ?></li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li>Add features to see them here</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions-bar">
                    <div class="form-actions-content">
                        <a href="<?php echo app_base_url('/admin/subscriptions'); ?>" class="btn btn-light btn-compact">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-compact">
                            <i class="fas fa-save"></i>
                            Update Subscription Plan
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

<!-- Include the same styles from create-plan.php -->
<style>
    /* Reusing all styles from create-plan.php */
    .admin-wrapper-container { max-width: 1400px; margin: 0 auto; padding: 1rem; background: var(--admin-gray-50, #f8f9fa); min-height: calc(100vh - 70px); }
    .admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); overflow: hidden; }
    .compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-bottom: 1px solid var(--admin-gray-200, #e5e7eb); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .header-left { flex: 1; }
    .header-title { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem; }
    .header-title h1 { margin: 0; font-size: 1.75rem; font-weight: 700; color: white; }
    .header-title i { font-size: 1.5rem; opacity: 0.9; }
    .header-subtitle { font-size: 0.875rem; opacity: 0.85; margin: 0; color: rgba(255,255,255,0.9); }
    .header-actions { flex-shrink: 0; }
    .btn-compact { padding: 0.625rem 1.25rem; font-size: 0.875rem; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-compact:hover { transform: translateY(-1px); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .btn-light { background: white; color: #374151; border: 1px solid #d1d5db; }
    .btn-light:hover { background: #f3f4f6; }
    .btn-primary { background: #667eea; color: white; }
    .btn-primary:hover { background: #5a67d8; }
    .form-content-area { padding: 2rem; background: #f8f9fa; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    .form-column-left, .form-column-right { display: flex; flex-direction: column; gap: 1.5rem; }
    .form-card-compact { background: white; border-radius: 10px; border: 1px solid var(--admin-gray-200, #e5e7eb); overflow: hidden; transition: all 0.2s ease; }
    .form-card-compact:hover { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
    .form-card-header { padding: 1rem 1.25rem; background: linear-gradient(to right, #f8f9fa, #ffffff); border-bottom: 1px solid var(--admin-gray-200, #e5e7eb); display: flex; justify-content: space-between; align-items: center; }
    .form-card-title { display: flex; align-items: center; gap: 0.5rem; font-size: 1rem; font-weight: 600; color: #1f2937; }
    .form-card-title i { color: #667eea; font-size: 0.9rem; }
    .form-card-body { padding: 1.5rem 1.25rem; }
    .form-group-compact { margin-bottom: 1.25rem; }
    .form-group-compact:last-child { margin-bottom: 0; }
    .form-label-compact { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
    .text-danger { color: #ef4444; }
    .form-input-compact { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s; background: white; color: #1f2937; }
    .form-input-compact:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
    .form-input-compact::placeholder { color: #9ca3af; }
    .form-hint { display: block; margin-top: 0.375rem; font-size: 0.75rem; color: #6b7280; }
    .status-toggle-group { display: flex; gap: 0.75rem; }
    .status-option { flex: 1; cursor: pointer; }
    .status-option input[type="radio"] { display: none; }
    .status-option-label { display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; background: white; }
    .status-option input[type="radio"]:checked + .status-option-label.status-active { border-color: #48bb78; background: rgba(72, 187, 120, 0.1); color: #48bb78; }
    .status-option input[type="radio"]:checked + .status-option-label.status-inactive { border-color: #ed8936; background: rgba(237, 137, 54, 0.1); color: #ed8936; }
    .status-option-label:hover { border-color: #667eea; }
    .pricing-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .price-input-group { position: relative; display: flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 8px; background: white; transition: all 0.2s; }
    .price-input-group:focus-within { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
    .price-currency { padding: 0 0.75rem; font-weight: 600; color: #6b7280; font-size: 1.1rem; }
    .price-input { flex: 1; border: none !important; padding: 0.75rem 0.5rem !important; font-weight: 600; font-size: 1rem; box-shadow: none !important; }
    .price-period { padding: 0 0.75rem; font-size: 0.875rem; color: #9ca3af; }
    .savings-indicator { margin-top: 0.5rem; padding: 0.5rem 0.75rem; background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; border-radius: 6px; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.375rem; }
    .btn-add-feature { padding: 0.5rem 0.75rem; background: #667eea; color: white; border: none; border-radius: 6px; font-size: 0.75rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.375rem; transition: all 0.2s; }
    .btn-add-feature:hover { background: #5a67d8; transform: translateY(-1px); }
    .features-list { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 0.75rem; }
    .feature-item { display: flex; gap: 0.5rem; align-items: center; }
    .feature-input-wrapper { flex: 1; display: flex; align-items: center; gap: 0.5rem; background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.5rem 0.75rem; transition: all 0.2s; }
    .feature-input-wrapper:focus-within { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
    .feature-drag { color: #d1d5db; cursor: grab; font-size: 0.875rem; }
    .feature-input { flex: 1; border: none; outline: none; font-size: 0.875rem; color: #1f2937; background: transparent; }
    .feature-input::placeholder { color: #9ca3af; }
    .btn-remove-feature { width: 2rem; height: 2rem; border: 1px solid #fecaca; background: #fef2f2; color: #ef4444; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; font-size: 0.75rem; }
    .btn-remove-feature:hover { background: #fee2e2; border-color: #fca5a5; }
    .preview-card { position: sticky; top: 1rem; }
    .plan-preview { background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 8px; padding: 1.25rem; border: 2px dashed #e5e7eb; }
    .preview-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; }
    .preview-plan-name { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 0; }
    .preview-status .status-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; }
    .status-published { background: rgba(72, 187, 120, 0.1); color: #48bb78; }
    .status-draft { background: rgba(237, 137, 54, 0.1); color: #ed8936; }
    .preview-description { font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem; line-height: 1.5; }
    .preview-pricing { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem; }
    .preview-price-item { background: white; padding: 0.75rem; border-radius: 6px; border: 1px solid #e5e7eb; text-align: center; }
    .preview-price-label { display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem; }
    .preview-price-value { display: block; font-size: 1.25rem; font-weight: 700; color: #667eea; }
    .preview-features-title { font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
    .preview-features ul { list-style: none; padding: 0; margin: 0; }
    .preview-features li { font-size: 0.875rem; color: #6b7280; padding: 0.375rem 0; padding-left: 1.5rem; position: relative; }
    .preview-features li::before { content: "\f00c"; font-family: "Font Awesome 5 Free"; font-weight: 900; position: absolute; left: 0; color: #48bb78; font-size: 0.75rem; }
    .form-actions-bar { background: white; border-top: 1px solid #e5e7eb; padding: 1.25rem 2rem; margin: 0 -2rem -2rem; }
    .form-actions-content { display: flex; justify-content: flex-end; gap: 0.75rem; }
    @media (max-width: 1024px) { .form-grid { grid-template-columns: 1fr; } .pricing-grid { grid-template-columns: 1fr; } .preview-card { position: static; } }
    @media (max-width: 768px) { .compact-header { flex-direction: column; align-items: stretch; gap: 1rem; padding: 1.25rem; } .form-content-area { padding: 1rem; } .form-actions-bar { padding: 1rem; margin: 0 -1rem -1rem; } .form-actions-content { flex-direction: column; } .btn-compact { width: 100%; justify-content: center; } }
</style>

<script>
// Reuse the same JavaScript from create-plan.php with modifications for edit mode
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editPlanForm');
    const featuresList = document.getElementById('featuresList');
    const addFeatureBtn = document.getElementById('addFeatureBtn');
    
    // Existing features from PHP
    const existingFeatures = <?php echo json_encode($plan['features']); ?>;
    
    // Live Preview Updates (same as create page)
    const nameInput = form.querySelector('[name="name"]');
    const descInput = form.querySelector('[name="description"]');
    const monthlyInput = form.querySelector('[name="price_monthly"]');
    const yearlyInput = form.querySelector('[name="price_yearly"]');
    const statusInputs = form.querySelectorAll('[name="is_active"]');
    
    nameInput.addEventListener('input', function() {
        document.getElementById('previewName').textContent = this.value || 'Plan Name';
    });
    
    descInput.addEventListener('input', function() {
        document.getElementById('previewDescription').textContent = this.value || 'Plan description will appear here...';
    });
    
    monthlyInput.addEventListener('input', function() {
        const value = parseFloat(this.value) || 0;
        document.getElementById('previewMonthly').textContent = '$' + value.toFixed(2);
        updateSavings();
    });
    
    yearlyInput.addEventListener('input', function() {
        const value = parseFloat(this.value) || 0;
        document.getElementById('previewYearly').textContent = '$' + value.toFixed(2);
        updateSavings();
    });
    
    statusInputs.forEach(input => {
        input.addEventListener('change', function() {
            const statusBadge = document.querySelector('#previewStatus .status-badge');
            if (this.value === '1') {
                statusBadge.className = 'status-badge status-published';
                statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Active';
            } else {
                statusBadge.className = 'status-badge status-draft';
                statusBadge.innerHTML = '<i class="fas fa-pause-circle"></i> Inactive';
            }
        });
    });
    
    function updateSavings() {
        const monthly = parseFloat(monthlyInput.value) || 0;
        const yearly = parseFloat(yearlyInput.value) || 0;
        const savingsIndicator = document.getElementById('savings-indicator');
        const savingsText = document.getElementById('savings-text');
        
        if (monthly > 0 && yearly > 0) {
            const savings = (monthly * 12) - yearly;
            if (savings > 0) {
                savingsIndicator.style.display = 'flex';
                savingsText.textContent = 'Save $' + savings.toFixed(2) + ' per year';
            } else {
                savingsIndicator.style.display = 'none';
            }
        } else {
            savingsIndicator.style.display = 'none';
        }
    }
    
    // Feature Management
    function updatePreviewFeatures() {
        const features = [];
        featuresList.querySelectorAll('.feature-input').forEach(input => {
            if (input.value.trim()) {
                features.push(input.value.trim());
            }
        });
        
        const previewList = document.getElementById('previewFeaturesList');
        if (features.length > 0) {
            previewList.innerHTML = features.map(f => `<li>${f}</li>`).join('');
        } else {
            previewList.innerHTML = '<li>Add features to see them here</li>';
        }
    }
    
    featuresList.addEventListener('input', function(e) {
        if (e.target.classList.contains('feature-input')) {
            updatePreviewFeatures();
        }
    });
    
    addFeatureBtn.addEventListener('click', function() {
        const newFeature = document.createElement('div');
        newFeature.className = 'feature-item';
        newFeature.innerHTML = `
            <div class="feature-input-wrapper">
                <i class="fas fa-grip-vertical feature-drag"></i>
                <input type="text" class="feature-input" placeholder="Enter feature description">
            </div>
            <button type="button" class="btn-remove-feature" onclick="removeFeature(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        featuresList.appendChild(newFeature);
        newFeature.querySelector('.feature-input').focus();
    });
    
    window.removeFeature = function(btn) {
        const featureItem = btn.closest('.feature-item');
        featureItem.remove();
        updatePreviewFeatures();
        
        if (featuresList.children.length === 0) {
            addFeatureBtn.click();
        }
    };
    
    // Load existing features
    existingFeatures.forEach(feature => {
        const featureItem = document.createElement('div');
        featureItem.className = 'feature-item';
        featureItem.innerHTML = `
            <div class="feature-input-wrapper">
                <i class="fas fa-grip-vertical feature-drag"></i>
                <input type="text" class="feature-input" value="${feature}" placeholder="Enter feature description">
            </div>
            <button type="button" class="btn-remove-feature" onclick="removeFeature(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        featuresList.appendChild(featureItem);
    });
    
    // If no features, add one empty input
    if (existingFeatures.length === 0) {
        addFeatureBtn.click();
    }
    
    // Initialize savings display
    updateSavings();
    
    // Form Submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const features = [];
        featuresList.querySelectorAll('.feature-input').forEach(input => {
            if (input.value.trim()) {
                features.push(input.value.trim());
            }
        });
        
        if (features.length === 0) {
            showNotification('Please add at least one feature to the plan.', 'warning');
            return;
        }
        
        const formData = new FormData(form);
        formData.set('features', JSON.stringify(features));
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Plan updated successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '<?php echo app_base_url('/admin/subscriptions'); ?>';
                }, 1000);
            } else {
                showNotification('Error: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while updating the plan.', 'error');
        });
    });
    
});
</script>
