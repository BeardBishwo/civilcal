<?php
/**
 * API Settings Page
 * Fixed version that works with current system setup
 */

// Initialize container if not available
if (!isset($container)) {
    $container = \App\Core\Container::create();
}

// Get current API settings
// If controller didn't pass settings, we might need to fetch them or rely on what's available
// Ideally controller passes $settings variable.
// But this file defined $api_settings mock array. We should use real settings if available.
$settings = $settings ?? \App\Services\SettingsService::getAll('api');

$api_configs = [
    'api_enabled' => [
        'label' => 'Enable API',
        'type' => 'checkbox',
        'description' => 'Enable API access for external applications'
    ],
    'api_rate_limit' => [
        'label' => 'API Rate Limit (requests per minute)',
        'type' => 'number',
        'required' => true,
        'description' => 'Maximum API requests allowed per minute',
        'min' => '1',
        'max' => '1000',
        'default' => '60'
    ],
    'api_timeout' => [
        'label' => 'API Timeout (seconds)',
        'type' => 'number',
        'required' => true,
        'description' => 'API request timeout duration',
        'min' => '5',
        'max' => '300',
        'default' => '30'
    ],
    'api_key_expiry' => [
        'label' => 'API Key Expiry (days)',
        'type' => 'number',
        'required' => true,
        'description' => 'How long API keys remain valid',
        'min' => '1',
        'max' => '3650',
        'default' => '365'
    ],
    'oauth_enabled' => [
        'label' => 'Enable OAuth',
        'type' => 'checkbox',
        'description' => 'Enable OAuth 2.0 authentication'
    ],
    'cors_enabled' => [
        'label' => 'Enable CORS',
        'type' => 'checkbox',
        'description' => 'Allow cross-origin requests'
    ],
    'cors_origins' => [
        'label' => 'Allowed CORS Origins',
        'type' => 'textarea',
        'description' => 'Comma-separated list of allowed domains (use * for all)',
        'default' => '*'
    ],
    'webhook_enabled' => [
        'label' => 'Enable Webhooks',
        'type' => 'checkbox',
        'description' => 'Allow webhook subscriptions'
    ],
    'webhook_timeout' => [
        'label' => 'Webhook Timeout (seconds)',
        'type' => 'number',
        'required' => true,
        'description' => 'Webhook request timeout duration',
        'min' => '1',
        'max' => '60',
        'default' => '10'
    ],
    'api_documentation' => [
        'label' => 'API Documentation URL',
        'type' => 'text',
        'description' => 'URL to API documentation',
        'default' => '/docs/api'
    ],
    'api_version' => [
        'label' => 'API Version',
        'type' => 'text',
        'required' => true,
        'description' => 'Current API version',
        'default' => 'v1'
    ],
    'api_debug' => [
        'label' => 'API Debug Mode',
        'type' => 'checkbox',
        'description' => 'Enable detailed API error messages'
    ]
];
?>

<style>
    .api-settings-container {
        background: #f8fafc;
        min-height: 100vh;
        padding: 2rem;
    }

    .api-header {
        margin-bottom: 2.5rem;
        animation: slideDown 0.6s ease-out;
    }

    .api-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .api-header p {
        font-size: 1rem;
        color: #718096;
        margin-bottom: 0;
    }

    .settings-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 1.5rem;
        border-top: 4px solid #667eea;
    }

    .settings-card h3 {
        color: #2d3748;
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .form-label.required::after {
        content: "*";
        color: #e53e3e;
        margin-left: 0.25rem;
    }

    .form-control, .form-select, .form-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .form-control:focus, .form-select:focus, .form-textarea:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: #f7fafc;
        border-radius: 0.375rem;
        border: 1px solid #edf2f7;
    }

    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        accent-color: #667eea;
        cursor: pointer;
    }

    .form-text {
        font-size: 0.875rem;
        color: #718096;
        margin-top: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.375rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.2);
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="admin-content">
    <div class="api-settings-container">
        <div class="api-header">
            <h1>🔌 API Configuration</h1>
            <p>Manage API access, rate limiting, and security settings</p>
        </div>

        <form action="<?php echo app_base_url('/admin/settings/update'); ?>" method="POST" class="settings-form ajax-form" id="apiSettingsForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="setting_group" value="api">
            
            <div class="settings-card">
                <h3>General API Settings</h3>
                
                <?php foreach ($api_configs as $key => $config): ?>
                    <?php 
                        $value = $settings[$key] ?? $config['default'] ?? '';
                        $required = $config['required'] ?? false;
                    ?>
                    
                    <div class="form-group">
                        <?php if ($config['type'] === 'checkbox'): ?>
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="<?= $key ?>" 
                                       name="<?= $key ?>" 
                                       value="1" 
                                       <?= ($settings[$key] ?? '0') == '1' ? 'checked' : '' ?>>
                                <div>
                                    <label class="form-check-label" style="font-weight: 600;" for="<?= $key ?>"><?= htmlspecialchars($config['label']) ?></label>
                                    <div class="form-text" style="margin-top: 0;"><?= htmlspecialchars($config['description']) ?></div>
                                </div>
                            </div>
                        <?php elseif ($config['type'] === 'textarea'): ?>
                            <label class="form-label<?= $required ? ' required' : '' ?>" for="<?= $key ?>"><?= htmlspecialchars($config['label']) ?></label>
                            <textarea class="form-textarea" 
                                      id="<?= $key ?>" 
                                      name="<?= $key ?>" 
                                      rows="3" 
                                      <?= $required ? 'required' : '' ?>><?= htmlspecialchars($value) ?></textarea>
                            <div class="form-text"><?= htmlspecialchars($config['description']) ?></div>
                        <?php else: ?>
                            <label class="form-label<?= $required ? ' required' : '' ?>" for="<?= $key ?>"><?= htmlspecialchars($config['label']) ?></label>
                            <input type="<?= $config['type'] ?>" 
                                   class="form-control" 
                                   id="<?= $key ?>" 
                                   name="<?= $key ?>" 
                                   value="<?= htmlspecialchars($value) ?>" 
                                   <?= $required ? 'required' : '' ?>
                                   <?php if (isset($config['min'])): ?>min="<?= $config['min'] ?>"<?php endif; ?>
                                   <?php if (isset($config['max'])): ?>max="<?= $config['max'] ?>"<?php endif; ?>>
                            <div class="form-text"><?= htmlspecialchars($config['description']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <div class="form-group" style="margin-top: 2rem; pt-4; border-top: 1px solid #e2e8f0;">
                    <button type="submit" class="btn btn-primary">💾 Save API Settings</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Handle form submission with visual feedback
    document.getElementById('apiSettingsForm').addEventListener('submit', function(e) {
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = '⏳ Saving...';
        
        // Re-enable after 3 seconds (or depend on ajax-form handler if available globally)
        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.textContent = '💾 Save API Settings';
        }, 3000);
    });
</script>
