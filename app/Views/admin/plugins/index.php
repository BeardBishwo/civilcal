<?php
$content = '
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Plugins Management</h2>
            <p class="text-muted mb-0">Extend functionality with plugins</p>
        </div>
        <div class="quick-actions">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadPluginModal">
                <i class="bi bi-upload me-2"></i>Upload Plugin
            </button>
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-cloud-download me-2"></i>Marketplace
            </button>
        </div>
    </div>

    <!-- Active Plugins -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Active Plugins</h6>
                </div>
                <div class="card-body">
                    <div class="row">';
                    
                    // Mock active plugins data
                    $activePlugins = [
                        [
                            'name' => 'PDF Export',
                            'description' => 'Export calculation results to PDF',
                            'version' => '2.1.0',
                            'author' => 'Bishwo Team',
                            'status' => 'active'
                        ],
                        [
                            'name' => 'User Analytics',
                            'description' => 'Track user behavior and calculator usage',
                            'version' => '1.5.2',
                            'author' => 'Analytics Team',
                            'status' => 'active'
                        ]
                    ];
                    
                    if (empty($activePlugins)) {
                        $content .= '
                        <div class="col-12 text-center py-4">
                            <i class="bi bi-plug fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No active plugins</p>
                        </div>';
                    } else {
                        foreach ($activePlugins as $plugin) {
                            $content .= '
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">' . htmlspecialchars($plugin['name']) . '</h6>
                                            <span class="badge bg-success">Active</span>
                                        </div>
                                        <p class="card-text small text-muted">' . htmlspecialchars($plugin['description']) . '</p>
                                        <div class="plugin-meta small text-muted">
                                            <div><strong>Version:</strong> ' . ($plugin['version']) . '</div>
                                            <div><strong>Author:</strong> ' . ($plugin['author']) . '</div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent py-2">
                                        <div class="btn-group w-100">
                                            <button class="btn btn-outline-warning btn-sm toggle-plugin" 
                                                    data-plugin="' . $plugin['name'] . '" data-action="disable">
                                                <i class="bi bi-pause me-1"></i>Disable
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-gear me-1"></i>Settings
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                        }
                    }
                    
                    $content .= '
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Plugins -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Plugins</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Plugin Name</th>
                                    <th>Description</th>
                                    <th>Version</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';
                            
                            // Mock all plugins data
                            $plugins = [
                                [
                                    'name' => 'PDF Export',
                                    'folder' => 'pdf-export',
                                    'description' => 'Export calculation results to PDF',
                                    'version' => '2.1.0',
                                    'author' => 'Bishwo Team',
                                    'status' => 'active'
                                ],
                                [
                                    'name' => 'User Analytics',
                                    'folder' => 'user-analytics',
                                    'description' => 'Track user behavior and calculator usage',
                                    'version' => '1.5.2',
                                    'author' => 'Analytics Team',
                                    'status' => 'active'
                                ],
                                [
                                    'name' => 'Email Notifications',
                                    'folder' => 'email-notifications',
                                    'description' => 'Send email notifications for important events',
                                    'version' => '1.0.3',
                                    'author' => 'Communication Team',
                                    'status' => 'inactive'
                                ],
                                [
                                    'name' => 'Social Sharing',
                                    'folder' => 'social-sharing',
                                    'description' => 'Share calculations on social media',
                                    'version' => '1.2.0',
                                    'author' => 'Social Team',
                                    'status' => 'inactive'
                                ]
                            ];
                            
                            foreach ($plugins as $plugin) {
                                $isActive = $plugin['status'] === 'active';
                                
                                $content .= '
                                <tr>
                                    <td>
                                        <div class="fw-bold">' . htmlspecialchars($plugin['name']) . '</div>
                                        <small class="text-muted">' . ($plugin['folder']) . '</small>
                                    </td>
                                    <td>' . htmlspecialchars($plugin['description']) . '</td>
                                    <td><span class="badge bg-secondary">' . ($plugin['version']) . '</span></td>
                                    <td>' . htmlspecialchars($plugin['author']) . '</td>
                                    <td>
                                        <span class="badge ' . ($isActive ? 'bg-success' : 'bg-secondary') . '">
                                            ' . ucfirst($plugin['status']) . '
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">';
                                        
                                        if ($isActive) {
                                            $content .= '
                                            <button class="btn btn-outline-warning toggle-plugin" 
                                                    data-plugin="' . $plugin['name'] . '" data-action="disable"
                                                    title="Disable Plugin">
                                                <i class="bi bi-pause"></i>
                                            </button>';
                                        } else {
                                            $content .= '
                                            <button class="btn btn-outline-success toggle-plugin" 
                                                    data-plugin="' . $plugin['name'] . '" data-action="enable"
                                                    title="Enable Plugin">
                                                <i class="bi bi-play"></i>
                                            </button>';
                                        }
                                        
                                        $content .= '
                                            <button class="btn btn-outline-info" title="Plugin Settings">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" title="Delete Plugin">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>';
                            }
                            
                            $content .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Plugin Modal -->
<div class="modal fade" id="uploadPluginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload New Plugin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadPluginForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Plugin ZIP File</label>
                        <input type="file" class="form-control" name="plugin_zip" accept=".zip" required>
                        <small class="form-text text-muted">
                            Upload a ZIP file containing your plugin with plugin.json manifest.
                        </small>
                    </div>
                    <div class="upload-progress d-none" id="pluginUploadProgress">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">Uploading plugin...</span>
                            <span class="small" id="pluginUploadPercent">0%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" id="pluginUploadProgressBar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Security Notice:</strong><br>
                        Only install plugins from trusted sources. Plugins have full access to your system.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="uploadPluginForm" class="btn btn-primary">Upload Plugin</button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Plugin Result Modal -->
<div class="modal fade" id="uploadPluginResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Plugin Upload Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="pluginResultStatus" class="mb-2"></div>
                <div>
                    <div class="small"><strong>Name:</strong> <span id="pluginResultName">-</span></div>
                    <div class="small"><strong>Checksum:</strong> <span id="pluginResultChecksum">-</span></div>
                    <div class="small"><strong>Size:</strong> <span id="pluginResultSize">-</span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="pluginResultReloadBtn">Reload</button>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle plugin status
document.querySelectorAll(".toggle-plugin").forEach(button => {
    button.addEventListener("click", function() {
        const pluginName = this.dataset.plugin;
        const action = this.dataset.action;
        const actionText = action === "enable" ? "enable" : "disable";
        
        if (confirm("Are you sure you want to " + actionText + " this plugin?")) {
            const formData = new FormData();
            formData.append("plugin_name", pluginName);
            formData.append("action", action);
            
            const headers = { "X-Requested-With": "XMLHttpRequest" };
            const csrfMeta = document.querySelector("meta[name=\"csrf-token\"]");
            const csrf = csrfMeta ? csrfMeta.getAttribute("content") : null;
            if (csrf) { headers["X-CSRF-Token"] = csrf; }
            fetch("/admin/plugins/toggle", {
                method: "POST",
                headers: headers,
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            });
        }
    });
});
</script>
<script>
// Handle plugin upload with progress and result modal
(function(){
  const form = document.getElementById("uploadPluginForm");
  if (!form) return;
  form.addEventListener("submit", function(e){
    e.preventDefault();
    const data = new FormData(form);
    const xhr = new XMLHttpRequest();
    const prog = document.getElementById("pluginUploadProgress");
    const bar = document.getElementById("pluginUploadProgressBar");
    const pct = document.getElementById("pluginUploadPercent");

    if (prog) prog.classList.remove("d-none");
    if (bar) bar.style.width = "0%";
    if (pct) pct.textContent = "0%";

    xhr.upload.addEventListener("progress", function(ev){
      if (ev.lengthComputable) {
        const percent = Math.round((ev.loaded / ev.total) * 100);
        if (bar) bar.style.width = percent + "%";
        if (pct) pct.textContent = percent + "%";
      }
    });

    xhr.addEventListener("load", function(){
      if (prog) prog.classList.add("d-none");
      const modalEl = document.getElementById("uploadPluginResultModal");
      const statusEl = document.getElementById("pluginResultStatus");
      const nameEl = document.getElementById("pluginResultName");
      const checksumEl = document.getElementById("pluginResultChecksum");
      const sizeEl = document.getElementById("pluginResultSize");
      const reloadBtn = document.getElementById("pluginResultReloadBtn");
      if (reloadBtn) reloadBtn.onclick = () => location.reload();

      if (xhr.status === 200) {
        try {
          const res = JSON.parse(xhr.responseText);
          if (res.success) {
            const d = res.data || {};
            statusEl.className = "alert alert-success";
            statusEl.textContent = res.message || "Plugin uploaded successfully";
            nameEl.textContent = d.plugin_name || "-";
            checksumEl.textContent = d.checksum || "-";
            sizeEl.textContent = d.file_size ? (Math.round((d.file_size/1024)*10)/10) + " KB" : "-";
          } else {
            statusEl.className = "alert alert-danger";
            statusEl.textContent = res.message || "Upload failed";
          }
        } catch (e) {
          statusEl.className = "alert alert-danger";
          statusEl.textContent = "Invalid response from server";
        }
        if (modalEl && window.bootstrap) new bootstrap.Modal(modalEl).show();
      } else {
        alert("Upload failed with status " + xhr.status);
      }
    });

    xhr.addEventListener("error", function(){
      if (prog) prog.classList.add("d-none");
      alert("Upload failed due to network error");
    });

    xhr.open("POST", "/admin/plugins/upload");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    const csrfMeta = document.querySelector("meta[name=\"csrf-token\"]");
    const csrf = csrfMeta ? csrfMeta.getAttribute("content") : null;
    if (csrf) xhr.setRequestHeader("X-CSRF-Token", csrf);
    xhr.send(data);
  });
})();
</script>
';

include __DIR__ . '/../../layouts/admin.php';
?>
