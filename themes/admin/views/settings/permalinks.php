<?php
/**
 * Permalink Settings Page - Using Proper Admin Layout
 * Features: Card-based layout, better visual hierarchy, enhanced UX
 */

use App\Core\Database;
use App\Helpers\UrlHelper;

// Set page title for layout
$page_title = 'Permalink Settings';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Settings', 'url' => app_base_url('admin/settings')],
    ['title' => 'Permalinks']
];

// Messages are passed from the controller
$message = $message ?? '';
$messageType = $messageType ?? '';

// Get data from controller or fetch directly
$currentStructure = $currentStructure ?? 'calculator-only';
$settings = $settings ?? [
    'permalink_php_extension' => '0',
    'permalink_base_path' => 'tools',
    'permalink_custom_pattern' => '',
    'permalink_redirect_old_urls' => '1'
];
$sampleCalculator = $sampleCalculator ?? [
    'calculator_id' => 'concrete-volume',
    'category' => 'civil',
    'subcategory' => 'concrete',
    'slug' => 'concrete-volume'
];
$message = $message ?? '';
$messageType = $messageType ?? '';

// Get permalink structures
$structures = UrlHelper::getAvailableStructures();

// Start output buffering for content
ob_start();
?>

<style>
    /* Clean Single Page Permalink Settings */
    .permalink-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .permalink-header {
        background: var(--admin-primary);
        color: white;
        padding: 2rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: var(--admin-shadow);
    }

    .permalink-header h1 {
        margin: 0 0 0.5rem 0;
        font-size: 1.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .permalink-header p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .permalink-content {
        background: white;
        border-radius: 8px;
        box-shadow: var(--admin-shadow);
        border: 1px solid var(--admin-border);
        overflow: hidden;
    }

    /* Structure Selection */
    .structure-section {
        padding: 2rem;
        border-bottom: 1px solid var(--admin-border);
    }

    .structure-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .structure-item {
        background: white;
        border: 2px solid var(--admin-border);
        border-radius: 8px;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .structure-item:hover {
        border-color: var(--admin-primary);
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }

    .structure-item.active {
        border-color: var(--admin-primary);
        background: #f0f4ff;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
    }

    .structure-item input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .structure-content {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .structure-title h3 {
        margin: 0 0 0.25rem 0;
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--admin-dark);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .structure-title p {
        margin: 0;
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .structure-preview {
        background: #f8fafc;
        border: 1px solid var(--admin-border);
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        font-family: monospace;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--admin-primary);
        text-align: right;
        min-width: 160px;
    }

    .structure-item.active .structure-preview {
        background: var(--admin-primary);
        color: white;
        border-color: var(--admin-primary);
    }

    .feature-badge {
        background: var(--admin-success);
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
        text-transform: uppercase;
    }

    /* Options Section */
    .options-section {
        padding: 2rem;
    }

    .options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .option-card {
        background: white;
        border: 1px solid var(--admin-border);
        border-radius: 6px;
        padding: 1.25rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 700;
        color: var(--admin-dark);
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
    }

    .form-group input[type="text"] {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--admin-border);
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-group input[type="text"]:focus {
        outline: none;
        border-color: var(--admin-primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        transform: translateY(-1px);
    }

    .form-text {
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        line-height: 1.5;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: #f8fafc;
        border-radius: 10px;
        border: 2px solid var(--admin-border);
        transition: all 0.2s ease;
    }

    .checkbox-group:hover {
        border-color: var(--admin-primary);
        background: #f0f4ff;
    }

    .checkbox-group input[type="checkbox"] {
        width: 1.25rem;
        height: 1.25rem;
        accent-color: var(--admin-primary);
        cursor: pointer;
    }

    .php-extension-toggle {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: #f8fafc;
        border-radius: 10px;
        border: 2px solid var(--admin-border);
        transition: all 0.2s ease;
    }

    .php-extension-toggle:hover {
        border-color: var(--admin-primary);
        background: #f0f4ff;
    }

    /* Preview Section */
    .preview-section {
        padding: 2rem;
        background: linear-gradient(135deg, #fef3c7, #fed7aa);
        border-left: 4px solid var(--admin-primary);
    }

    .preview-section h3 {
        margin: 0 0 1.5rem 0;
        color: #92400e;
        font-size: 1.25rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .preview-url {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        border: 2px solid #f59e0b;
        font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
        font-size: 1.125rem;
        font-weight: 700;
        color: #92400e;
        text-align: center;
        word-break: break-all;
        box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);
    }

    /* SEO Tips */
    .seo-tips {
        padding: 2rem;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-top: 1px solid var(--admin-border);
    }

    .seo-tips h4 {
        margin: 0 0 1rem 0;
        color: #1e40af;
        font-size: 1.25rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .seo-tips ul {
        margin: 0;
        padding-left: 1.5rem;
        color: #1e3a8a;
    }

    .seo-tips li {
        margin-bottom: 0.75rem;
        line-height: 1.6;
        font-size: 0.9rem;
    }

    /* Actions */
    .actions-section {
        padding: 2rem;
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        border-radius: 0 0 12px 12px;
    }

    .btn {
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
        color: white;
        box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    }

    .btn-secondary {
        background: white;
        color: var(--admin-dark);
        border: 2px solid var(--admin-border);
    }

    .btn-secondary:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        border-color: var(--admin-primary);
    }

    /* Alerts */
    .alert {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        border-left: 4px solid;
        font-weight: 500;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .alert.success {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-color: var(--admin-success);
        color: #065f46;
    }

    .alert.error {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border-color: var(--admin-danger);
        color: #991b1b;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .structure-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }
        
        .options-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .permalink-settings {
            padding: 0 0.5rem;
        }
        
        .permalink-header {
            padding: 2rem 1rem;
        }
        
        .permalink-header h1 {
            font-size: 1.5rem;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .structure-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            padding: 1rem;
        }
        
        .structure-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .structure-preview {
            width: 100%;
            text-align: left;
        }
        
        .options-section,
        .preview-section,
        .seo-tips {
            padding: 1.5rem;
        }
        
        .actions-section {
            flex-direction: column;
            align-items: stretch;
        }
        
        .btn {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .structure-item {
            padding: 1.25rem;
        }
        
        .structure-title h3 {
            font-size: 1.1rem;
        }
        
        .preview-url {
            font-size: 0.875rem;
            padding: 1rem;
        }
    }
</style>

<!-- Clean Permalink Settings -->
<div class="permalink-settings">
    <div class="permalink-header">
        <h1><i class="fas fa-link"></i> Permalink Settings</h1>
        <p>Configure how calculator URLs appear throughout your website</p>
    </div>
    
    <div class="admin-header">
        <h1>🔗 Permalink Settings</h1>
        <p>Configure how calculator URLs appear throughout your website. Choose from 7 different structures.</p>
    </div>

    <?php if ($message): ?>
        <div class="message-container">
            <div class="alert <?= $messageType === 'success' ? 'success' : 'error' ?>">
                <span class="alert-icon"><?= $messageType === 'success' ? '✅' : '❌' ?></span>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <!-- Structure Selection Section -->
        <div class="permalink-content">
            <div class="structure-section">
                <div class="structure-grid">
                <?php foreach ($structures as $key => $info): ?>
                    <div class="structure-item <?= $currentStructure === $key ? 'active' : '' ?>" onclick="selectStructure('<?= $key ?>')">
                        <input type="radio" name="permalink_structure" value="<?= $key ?>" <?= $currentStructure === $key ? 'checked' : '' ?>>
                        <div class="structure-content">
                            <div class="structure-header">
                                <div class="structure-title">
                                    <h3>
                                        <?= htmlspecialchars($info['label']) ?>
                                        <?php if (in_array($key, ['calculator-only'])): ?>
                                            <span class="feature-badge">SEO</span>
                                        <?php endif; ?>
                                    </h3>
                                    <p><?= htmlspecialchars($info['description']) ?></p>
                                </div>
                                <div class="structure-preview" id="preview-<?= $key ?>">
                                    <?= htmlspecialchars($info['example']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>

            <!-- Options Section -->
            <div class="options-section">
                <div class="options-grid">
                    <!-- PHP Extension Toggle -->
                    <div class="option-card">
                        <div class="php-extension-toggle">
                            <input type="checkbox" id="permalink_php_extension" name="permalink_php_extension" value="1" <?= $settings['permalink_php_extension'] == '1' ? 'checked' : '' ?>>
                            <label for="permalink_php_extension">
                                <strong>Show .php Extension</strong>
                                <br><small>Add .php extension to all URLs (e.g., /concrete-volume.php instead of /concrete-volume)</small>
                            </label>
                        </div>
                    </div>

                    <!-- 301 Redirects Option -->
                    <div class="option-card">
                        <div class="checkbox-group">
                            <input type="checkbox" id="redirect_old_urls" name="permalink_redirect_old_urls" value="1" <?= $settings['permalink_redirect_old_urls'] == '1' ? 'checked' : '' ?>>
                            <label for="redirect_old_urls">
                                <strong>Enable 301 Redirects</strong>
                                <br><small>Automatically redirect old URLs to new structure (recommended for SEO)</small>
                            </label>
                        </div>
                    </div>

                    <!-- Custom Pattern Field -->
                    <div class="option-card">
                        <div class="form-group">
                            <label for="permalink_custom_pattern">Custom URL Pattern</label>
                            <input type="text" id="permalink_custom_pattern" name="permalink_custom_pattern" value="<?= htmlspecialchars($settings['permalink_custom_pattern']) ?>" placeholder="{category}/{slug}" oninput="updatePreview()">
                            <div class="form-text">
                                <strong>Available placeholders:</strong><br>
                                • <code>{category}</code> - Calculator category (e.g., "civil", "electrical")<br>
                                • <code>{subcategory}</code> - Calculator subcategory (e.g., "concrete", "steel")<br>
                                • <code>{slug}</code> - Calculator name (e.g., "concrete-volume")
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Preview Section -->
            <div class="preview-section">
                <h3><i class="fas fa-eye"></i> Live URL Preview</h3>
                <div class="preview-url" id="url-preview">
                    <?= htmlspecialchars(UrlHelper::calculator($sampleCalculator['calculator_id'] ?? 'concrete-volume')) ?>
                </div>
            </div>

            <!-- SEO Tips -->
            <div class="seo-tips">
                <h4><i class="fas fa-search"></i> SEO Best Practices</h4>
                <ul>
                    <li><strong>🏆 Best for SEO:</strong> "Calculator Only" structure creates the cleanest, most SEO-friendly URLs</li>
                    <li><strong>⚡ Clean URLs:</strong> Disable ".php Extension" for modern, clean URLs (recommended for better rankings)</li>
                    <li><strong>🔧 For Compatibility:</strong> Enable ".php Extension" if your server requires it or for legacy systems</li>
                    <li><strong>📁 For Organization:</strong> Use "Category + Calculator" or "Subcategory + Calculator" to group related calculators</li>
                    <li><strong>🔄 Always enable:</strong> 301 redirects to preserve SEO rankings when changing structures</li>
                    <li><strong>✨ Flexibility:</strong> Toggle .php extension on/off anytime - the system handles both formats seamlessly</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="actions-section">
                <a href="<?= app_base_url('admin/settings') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Settings
                </a>
                <button type="submit" name="save_permalinks" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Single Page Permalink Settings JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        initializePermalinkSettings();
    });
    
    function initializePermalinkSettings() {
        // Structure selection functionality
        const structureItems = document.querySelectorAll('.structure-item');
        structureItems.forEach(item => {
            item.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    selectStructure(radio.value);
                }
            });
        });
        
        // Initialize
        updatePreview();
        
        // Add event listeners for form inputs
        const customPatternInput = document.getElementById('permalink_custom_pattern');
        if (customPatternInput) {
            customPatternInput.addEventListener('input', updatePreview);
        }
        
        // Add event listener for PHP extension checkbox
        const phpExtensionCheckbox = document.getElementById('permalink_php_extension');
        if (phpExtensionCheckbox) {
            phpExtensionCheckbox.addEventListener('change', updatePreview);
        }
    }

    let currentStructure = '<?= $currentStructure ?>';
    
    function selectStructure(structure) {
        currentStructure = structure;
        
        // Update active state
        document.querySelectorAll('.structure-option').forEach(card => {
            card.classList.remove('active');
        });
        
        const selectedCard = document.querySelector(`input[value="${structure}"]`)?.closest('.structure-option');
        if (selectedCard) {
            selectedCard.classList.add('active');
        }
        
        // Show/hide conditional fields
        toggleConditionalFields(structure);
        
        // Update preview
        updatePreview();
    }

    function toggleConditionalFields(structure) {
        const customPatternField = document.getElementById('custom_pattern_field');
        
        if (customPatternField) {
            if (structure === 'custom') {
                customPatternField.classList.add('show');
            } else {
                customPatternField.classList.remove('show');
            }
        }
    }

    function updatePreview() {
        const sampleId = '<?= $sampleCalculator['calculator_id'] ?? 'concrete-volume' ?>';
        const category = '<?= $sampleCalculator['category'] ?? 'civil' ?>';
        const subcategory = '<?= $sampleCalculator['subcategory'] ?? 'concrete' ?>';
        const slug = '<?= $sampleCalculator['slug'] ?? 'concrete-volume' ?>';
        const baseUrl = '<?= app_base_url() ?>';
        
        // Get custom pattern value
        const customPatternInput = document.getElementById('permalink_custom_pattern');
        const customPattern = customPatternInput ? customPatternInput.value : '';
        
        // Check if PHP extension is enabled
        const phpExtensionCheckbox = document.getElementById('permalink_php_extension');
        const phpExtension = phpExtensionCheckbox && phpExtensionCheckbox.checked ? '.php' : '';
        
        let url = '';
        
        switch(currentStructure) {
            case 'full-path':
                url = `${baseUrl}/${sampleId}`; // Virtual Route
                break;
            case 'category-calculator':
                url = `${baseUrl}/${category}/${sampleId}${phpExtension}`;
                break;
            case 'subcategory-calculator':
                url = `${baseUrl}/${subcategory}/${sampleId}${phpExtension}`;
                break;
            case 'calculator-only':
                url = `${baseUrl}/${slug}${phpExtension}`;
                break;
            case 'custom':
                if (customPattern) {
                    let pattern = customPattern;
                    pattern = pattern.replace('{category}', category);
                    pattern = pattern.replace('{subcategory}', subcategory);
                    pattern = pattern.replace('{slug}', slug);
                    // Ensure pattern starts with /
                    if (!pattern.startsWith('/')) {
                        pattern = '/' + pattern;
                    }
                    url = `${baseUrl}${pattern}${phpExtension}`;
                } else {
                    url = `${baseUrl}/${slug}${phpExtension}`;
                }
                break;
            default:
                url = `${baseUrl}/${slug}${phpExtension}`;
                break;
        }
        
        // Remove double slashes
        url = url.replace(/\/\//g, '/');
        
        const previewElement = document.getElementById('url-preview');
        if (previewElement) {
            previewElement.textContent = url;
        }
    }
</script>
