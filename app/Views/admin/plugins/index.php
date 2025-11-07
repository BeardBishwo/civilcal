<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-puzzle-piece"></i> Plugin Management</h1>
        <p>Manage calculator plugins and extensions</p>
    </div>

    <!-- Upload Plugin Card -->
    <div class="card">
        <div class="card-header">
            <h3>Upload New Plugin</h3>
        </div>
        <div class="card-body">
            <form id="uploadPluginForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Plugin ZIP File</label>
                    <input type="file" name="plugin_zip" accept=".zip" required>
                    <small>Upload a plugin in ZIP format containing plugin.json</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload & Install
                </button>
            </form>
        </div>
    </div>

    <!-- Installed Plugins -->
    <div class="card">
        <div class="card-header">
            <h3>Installed Plugins</h3>
            <button class="btn btn-sm btn-secondary" onclick="refreshPlugins()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
        <div class="card-body">
            <div class="plugins-grid">
                <?php if (empty($plugins)): ?>
                    <div class="empty-state">
                        <i class="fas fa-puzzle-piece"></i>
                        <h4>No plugins installed</h4>
                        <p>Upload a plugin ZIP file to get started</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($plugins as $plugin): ?>
                    <div class="plugin-card <?= $plugin['is_active'] ? 'active' : 'inactive' ?>">
                        <div class="plugin-header">
                            <h4><?= $plugin['name'] ?></h4>
                            <span class="plugin-version">v<?= $plugin['version'] ?></span>
                        </div>
                        
                        <p class="plugin-description"><?= $plugin['description'] ?></p>
                        
                        <div class="plugin-meta">
                            <span class="author">By: <?= $plugin['author'] ?? 'Unknown' ?></span>
                            <span class="type"><?= ucfirst($plugin['type'] ?? 'calculator') ?></span>
                        </div>
                        
                        <div class="plugin-stats">
                            <?php if (isset($plugin['calculators'])): ?>
                                <span class="calculator-count">
                                    <i class="fas fa-calculator"></i>
                                    <?= count($plugin['calculators']) ?> calculators
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="plugin-actions">
                            <?php if ($plugin['is_active']): ?>
                                <button class="btn btn-warning btn-sm" 
                                        onclick="togglePlugin('<?= $plugin['slug'] ?>', 'deactivate')">
                                    <i class="fas fa-pause"></i> Deactivate
                                </button>
                            <?php else: ?>
                                <button class="btn btn-success btn-sm" 
                                        onclick="togglePlugin('<?= $plugin['slug'] ?>', 'activate')">
                                    <i class="fas fa-play"></i> Activate
                                </button>
                            <?php endif; ?>
                            
                            <button class="btn btn-info btn-sm" 
                                    onclick="viewPluginDetails('<?= $plugin['slug'] ?>')">
                                <i class="fas fa-info"></i> Details
                            </button>
                            
                            <?php if (!$plugin['is_core']): ?>
                                <button class="btn btn-danger btn-sm" 
                                        onclick="deletePlugin('<?= $plugin['slug'] ?>')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Active Calculators Overview -->
    <div class="card">
        <div class="card-header">
            <h3>Active Calculators</h3>
            <span class="badge"><?= count($activeCalculators) ?> calculators</span>
        </div>
        <div class="card-body">
            <div class="calculators-stats">
                <?php
                $categories = [];
                foreach ($activeCalculators as $calc) {
                    $categories[$calc['discipline']][] = $calc;
                }
                ?>
                
                <?php if (empty($categories)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calculator"></i>
                        <h4>No active calculators</h4>
                        <p>Activate plugins to see calculator counts</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($categories as $discipline => $calculators): ?>
                    <div class="discipline-stats">
                        <h4><i class="fas fa-<?= $this->getDisciplineIcon($discipline) ?>"></i> <?= ucfirst($discipline) ?></h4>
                        <span class="count"><?= count($calculators) ?> calculators</span>
                        <div class="calculator-list">
                            <?php foreach (array_slice($calculators, 0, 3) as $calc): ?>
                                <span class="calc-item"><?= $calc['name'] ?></span>
                            <?php endforeach; ?>
                            <?php if (count($calculators) > 3): ?>
                                <span class="more-items">+<?= count($calculators) - 3 ?> more</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Plugin Details Modal -->
<div id="pluginDetailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Plugin Details</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" id="pluginDetailsContent">
            <!-- Plugin details will be loaded here -->
        </div>
    </div>
</div>

<style>
.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.admin-header {
    margin-bottom: 30px;
    text-align: center;
}

.admin-header h1 {
    color: #333;
    margin-bottom: 10px;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input[type="file"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
    margin-right: 5px;
}

.plugins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.plugin-card {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    background: #fff;
    transition: all 0.3s ease;
}

.plugin-card.active {
    border-color: #28a745;
    background: #f8fff9;
}

.plugin-card.inactive {
    border-color: #6c757d;
    background: #f8f9fa;
}

.plugin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.plugin-header h4 {
    margin: 0;
    color: #333;
}

.plugin-version {
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    color: #6c757d;
}

.plugin-description {
    color: #666;
    margin-bottom: 15px;
    font-size: 14px;
}

.plugin-meta {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 10px;
}

.calculator-count {
    background: #e7f3ff;
    color: #0066cc;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
}

.calculators-stats {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.discipline-stats {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.discipline-stats h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.discipline-stats .count {
    font-weight: bold;
    color: #007bff;
    display: block;
    margin-bottom: 10px;
}

.calculator-list {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.calc-item, .more-items {
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    color: #495057;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    max-height: 80%;
    overflow-y: auto;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 20px;
}

.close {
    font-size: 24px;
    cursor: pointer;
    color: #aaa;
}

.close:hover {
    color: #000;
}

.badge {
    background: #007bff;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
}
</style>

<script>
function togglePlugin(slug, action) {
    if (confirm(`Are you sure you want to ${action} this plugin?`)) {
        fetch(`/admin/plugins/toggle/${slug}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('An error occurred: ' + error.message, 'error');
        });
    }
}

function deletePlugin(slug) {
    if (confirm('Are you sure you want to delete this plugin? This action cannot be undone.')) {
        fetch(`/admin/plugins/delete/${slug}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('An error occurred: ' + error.message, 'error');
        });
    }
}

function viewPluginDetails(slug) {
    fetch(`/admin/plugins/details/${slug}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPluginDetails(data.plugin);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred: ' + error.message, 'error');
    });
}

function showPluginDetails(plugin) {
    const modal = document.getElementById('pluginDetailsModal');
    const content = document.getElementById('pluginDetailsContent');
    
    let detailsHtml = `
        <h4>${plugin.name}</h4>
        <p><strong>Version:</strong> ${plugin.version}</p>
        <p><strong>Author:</strong> ${plugin.author || 'Unknown'}</p>
        <p><strong>Type:</strong> ${plugin.type}</p>
        <p><strong>Description:</strong> ${plugin.description || 'No description'}</p>
    `;
    
    if (plugin.author_url) {
        detailsHtml += `<p><strong>Author URL:</strong> <a href="${plugin.author_url}" target="_blank">${plugin.author_url}</a></p>`;
    }
    
    if (plugin.calculators) {
        detailsHtml += '<h5>Calculators:</h5><ul>';
        for (const [key, calc] of Object.entries(plugin.calculators)) {
            detailsHtml += `<li><strong>${calc.name}</strong> - ${calc.description}</li>`;
        }
        detailsHtml += '</ul>';
    }
    
    if (plugin.requirements) {
        detailsHtml += '<h5>Requirements:</h5><ul>';
        if (plugin.requirements.php_version) {
            detailsHtml += `<li>PHP Version: ${plugin.requirements.php_version}+</li>`;
        }
        if (plugin.requirements.required_plugins) {
            detailsHtml += `<li>Required Plugins: ${plugin.requirements.required_plugins.join(', ')}</li>`;
        }
        detailsHtml += '</ul>';
    }
    
    content.innerHTML = detailsHtml;
    modal.style.display = 'block';
}

function refreshPlugins() {
    fetch('/admin/plugins/refresh', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred: ' + error.message, 'error');
    });
}

function closeModal() {
    document.getElementById('pluginDetailsModal').style.display = 'none';
}

function showMessage(message, type) {
    // Create message element
    const messageEl = document.createElement('div');
    messageEl.className = `message message-${type}`;
    messageEl.textContent = message;
    
    // Style the message
    messageEl.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 4px;
        color: white;
        z-index: 9999;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    `;
    
    if (type === 'success') {
        messageEl.style.background = '#28a745';
    } else {
        messageEl.style.background = '#dc3545';
    }
    
    document.body.appendChild(messageEl);
    
    // Remove message after 3 seconds
    setTimeout(() => {
        messageEl.remove();
    }, 3000);
}

// Handle form submission
document.getElementById('uploadPluginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/plugins/upload', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            this.reset();
            setTimeout(() => location.reload(), 2000);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        showMessage('Upload failed: ' + error.message, 'error');
    });
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('pluginDetailsModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>
