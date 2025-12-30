<?php
$page_title = 'Media Library - Admin Panel';
$currentPage = 'content';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Media']
];

$totalFiles = isset($pagination) ? $pagination['total'] : count($media);
?>

<style>
    /* Scope Media Manager variables to its container and map to global theme */
    .media-manager-container {
        --media-gray-100: var(--admin-gray-50);
        --media-gray-200: var(--admin-gray-200);
        --media-gray-300: var(--admin-gray-300);
        --media-gray-600: var(--media-gray-600);
        --media-primary: var(--admin-primary);
        --media-primary-dark: var(--admin-primary-dark);
        --media-success: var(--admin-success);
        --media-warning: var(--admin-warning);
        --media-sidebar-width: 350px;
        
        display: flex;
        height: calc(100vh - 120px);
        margin: -1.5rem;
        background: var(--admin-white);
        overflow: hidden;
        border-radius: 8px;
        box-shadow: var(--admin-shadow);
    }

    /* Main Browser Area */
    .media-browser {
        flex: 1;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--media-gray-200);
        background: var(--media-gray-100);
        position: relative;
    }

    .media-toolbar {
        padding: 1rem 1.5rem;
        background: var(--admin-white);
        border-bottom: 1px solid var(--media-gray-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        z-index: 10;
        flex-wrap: wrap;
    }

    .toolbar-left, .toolbar-right {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        flex-wrap: wrap;
    }

    .toolbar-right {
        flex: 1;
        justify-content: flex-end;
        min-width: 300px;
    }

    .media-grid-container {
        flex: 1;
        padding: 1.5rem;
        overflow-y: auto;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1.2rem;
        align-content: start;
    }

    /* Media Item/Card */
    .media-item {
        position: relative;
        width: 100%;
        padding-top: 100%; /* Fallback for aspect-ratio */
        background: var(--admin-white);
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        overflow: hidden;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
        border-radius: 4px;
    }

    /* Wrap content in a container to absolute-fill the padded item */
    .media-item-content {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .media-item:hover {
        box-shadow: inset 0 0 0 1px var(--media-primary);
    }

    .media-item.selected {
        border-color: var(--media-primary);
        box-shadow: 0 0 0 2px var(--media-primary);
    }

    .media-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* SVG specific fitting to prevent "overflowing" in some browsers */
    .media-item img[src$=".svg"] {
        object-fit: contain;
        padding: 10px;
    }

    /* Checkbox for Bulk Select */
    .item-checkbox-wrapper {
        position: absolute;
        top: 8px;
        left: 8px;
        z-index: 15;
        display: none;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 3px;
        padding: 2px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Show checkbox on hover (Windows-style) */
    .media-item:hover .item-checkbox-wrapper {
        display: block;
    }

    /* Always show in selection mode */
    #media-manager.selection-mode .item-checkbox-wrapper {
        display: block;
    }

    /* Show checkbox if it's checked */
    .item-checkbox:checked + .item-checkbox-wrapper,
    .item-checkbox-wrapper:has(.item-checkbox:checked) {
        display: block;
    }

    .item-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--media-primary);
        display: block;
    }

    /* Usage Badge */
    .usage-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: white;
        z-index: 5;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    #media-manager.selection-mode .usage-badge {
        display: none;
    }

    .badge-used { background: var(--media-success); }
    .badge-unused { background: var(--media-warning); }

    .optimized-badge {
        position: absolute;
        top: 5px;
        left: 5px;
        background: var(--media-primary);
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .media-item .file-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--media-gray-100);
        color: var(--media-gray-600);
        gap: 0.5rem;
    }

    .media-item .file-placeholder i {
        font-size: 2.5rem;
        color: var(--media-gray-300);
    }

    /* Scoped Sidebar Details */
    .media-info-sidebar {
        width: var(--media-sidebar-width);
        background: var(--media-gray-50);
        display: flex;
        flex-direction: column;
        border-left: 1px solid var(--media-gray-200);
        transform: translateX(0);
        transition: transform 0.3s ease;
    }

    .media-info-sidebar-header {
        padding: 1rem 1.5rem;
        background: var(--admin-white);
        border-bottom: 1px solid var(--media-gray-200);
        font-weight: 700;
        color: var(--media-gray-600);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .media-info-sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
    }

    .media-detail-preview {
        width: 100%;
        aspect-ratio: 16/10;
        background: var(--admin-white);
        border: 1px solid var(--media-gray-200);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 4px;
    }

    .media-detail-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Native-feeling Buttons */
    .btn-media {
        padding: 0.5rem 1rem;
        background: var(--media-primary);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
    }

    .btn-media:hover:not(:disabled) { background: var(--media-primary-dark); transform: translateY(-1px); }
    .btn-media:disabled { opacity: 0.6; cursor: not-allowed; }

    .btn-media-secondary {
        background: var(--admin-white);
        color: var(--media-primary);
        border: 1px solid var(--media-primary);
    }

    .btn-media-secondary:hover { background: var(--media-gray-100); }

    .btn-media-danger {
        background: var(--admin-white);
        color: var(--admin-danger);
        border: 1px solid var(--admin-danger);
    }

    .btn-media-danger:hover { background: rgba(239, 68, 68, 0.05); }

    /* Selection Mode Active UI */
    .btn-select-mode.active {
        background: var(--media-primary);
        color: white;
    }

    .bulk-actions-toolbar {
        display: none;
        background: var(--media-gray-100);
        padding: 0.5rem 1.5rem;
        border-bottom: 1px solid var(--media-gray-200);
        align-items: center;
        gap: 1rem;
        font-size: 13px;
    }

    #media-manager.selection-mode .bulk-actions-toolbar {
        display: flex;
    }

    /* Loading Spinner */
    .spinner {
        display: none;
        animation: rotate 1s linear infinite;
        margin-right: 5px;
    }
    .loading .spinner { display: inline-block; }
    .loading .btn-text { display: none; }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .form-control-media {
        padding: 0.4rem 0.6rem;
        border: 1px solid var(--media-gray-300);
        border-radius: 6px;
        font-size: 13px;
        outline: none;
    }

    /* Drag & Drop Overlay */
    .upload-overlay {
        position: absolute;
        inset: 0;
        background: rgba(79, 70, 229, 0.95);
        display: none;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 100;
        border: 4px dashed white;
        margin: 1rem;
        border-radius: 8px;
    }

    .upload-overlay.active {
        display: flex;
    }

    .upload-overlay-content {
        text-align: center;
    }

    .upload-overlay-content i {
        font-size: 4rem;
        margin-bottom: 1rem;
        display: block;
    }

    .upload-overlay-content h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
</style>

<div class="media-manager-container" id="media-manager">
    <!-- Main Browser Area -->
    <div class="media-browser">
        <!-- Drag & Drop Overlay -->
        <div class="upload-overlay" id="upload-overlay">
            <div class="upload-overlay-content" style="text-align:center;">
                <i class="fas fa-cloud-upload-alt" style="font-size:4rem; margin-bottom:1rem;"></i>
                <h2>Drop files to upload</h2>
            </div>
        </div>

        <div class="media-toolbar">
            <div class="toolbar-left">
                <button class="btn-media" onclick="document.getElementById('upload-input').click()">
                    <i class="fas fa-plus"></i> Add New
                </button>
                
                <button class="btn-media btn-media-secondary btn-select-mode" id="btn-bulk-select" onclick="toggleSelectionMode()">
                    <i class="fas fa-check-square"></i> <span class="btn-text">Bulk Select</span>
                </button>

                <div style="width: 1px; height: 24px; background: var(--media-gray-200); margin: 0 0.5rem;"></div>

                <button class="btn-media btn-media-secondary" id="btn-sync" onclick="syncMedia()" title="Scan for images in storage and theme folders">
                    <i class="fas fa-sync spinner"></i> <i class="fas fa-sync static-icon"></i> <span class="btn-text">Sync Folder</span>
                </button>
                <button class="btn-media btn-media-danger" id="btn-cleanup" onclick="confirmBulkCleanup()" title="Delete all unused images">
                    <i class="fas fa-broom spinner"></i> <i class="fas fa-broom static-icon"></i> <span class="btn-text">Cleanup</span>
                </button>
            </div>
            
            <div class="toolbar-right">
                <select class="form-control-media" onchange="changePerPage(this.value)" title="Items per page">
                    <option value="20" <?php echo ($_GET['per_page'] ?? 50) == 20 ? 'selected' : ''; ?>>20 per page</option>
                    <option value="50" <?php echo ($_GET['per_page'] ?? 50) == 50 ? 'selected' : ''; ?>>50 per page</option>
                    <option value="100" <?php echo ($_GET['per_page'] ?? 50) == 100 ? 'selected' : ''; ?>>100 per page</option>
                    <option value="200" <?php echo ($_GET['per_page'] ?? 50) == 200 ? 'selected' : ''; ?>>200 per page</option>
                </select>
                <select class="form-control-media" onchange="filterType(this.value)">
                    <option value="">All items</option>
                    <option value="images" <?php echo ($_GET['type'] ?? '') === 'images' ? 'selected' : ''; ?>>Images</option>
                    <option value="documents" <?php echo ($_GET['type'] ?? '') === 'documents' ? 'selected' : ''; ?>>Docs</option>
                </select>
                <input type="text" placeholder="Search..." class="form-control-media" id="media-search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="width: 150px;">
                
                <?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-left: 1rem;">
                        <button class="btn-media btn-media-secondary" onclick="changePage(1)" <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?> title="First page">
                            <i class="fas fa-angle-double-left"></i>
                        </button>
                        <button class="btn-media btn-media-secondary" onclick="changePage(<?php echo $pagination['current_page'] - 1; ?>)" <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?> title="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span style="font-size: 13px; color: var(--media-gray-600); white-space: nowrap;">
                            Page <?php echo $pagination['current_page']; ?> of <?php echo $pagination['last_page']; ?>
                        </span>
                        <button class="btn-media btn-media-secondary" onclick="changePage(<?php echo $pagination['current_page'] + 1; ?>)" <?php echo $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : ''; ?> title="Next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="btn-media btn-media-secondary" onclick="changePage(<?php echo $pagination['last_page']; ?>)" <?php echo $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : ''; ?> title="Last page">
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                    </div>
                <?php endif; ?>
                <span style="font-size: 13px; color: var(--media-gray-600); margin-left: 1rem;"><?php echo $totalFiles; ?> items</span>
            </div>
        </div>

        <!-- Bulk Actions Bar (Visible only in Selection Mode) -->
        <div class="bulk-actions-toolbar">
            <strong id="selected-count">0 items selected</strong>
            <button class="btn-media btn-media-danger btn-sm" id="btn-delete-selected" disabled onclick="deleteSelected()">
                <i class="fas fa-trash"></i> Delete Selected
            </button>
            <button class="btn-media btn-media-secondary btn-sm" onclick="toggleSelectionMode()">Cancel</button>
        </div>

        <div class="media-grid-container" id="media-grid">
            <?php if (empty($media)): ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 4rem 0;">
                    <i class="fas fa-images" style="font-size: 4rem; color: var(--media-gray-200); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--media-gray-600);">No media found.</h3>
                    <p style="color: var(--media-gray-300);">Upload files or sync folder to see images.</p>
                </div>
            <?php else: ?>
                <?php foreach ($media as $item): ?>
                    <?php 
                    $isImage = strpos($item['type'], 'image') === 0;
                    $isUsed = $item['usage']['is_used'];
                    ?>
                    <div class="media-item" 
                         data-id="<?php echo $item['id']; ?>" 
                         data-filename="<?php echo htmlspecialchars($item['filename']); ?>"
                         data-url="<?php echo $item['url']; ?>"
                         data-type="<?php echo $item['type']; ?>"
                         data-size="<?php echo $item['size']; ?>"
                         data-date="<?php echo date('M j, Y', strtotime($item['uploaded_at'])); ?>"
                         data-width="<?php echo $item['width'] ?? ''; ?>"
                         data-height="<?php echo $item['height'] ?? ''; ?>"
                         data-usage='<?php echo json_encode($item['usage']); ?>'
                         data-optimized="<?php echo $item['optimized'] ?? 0; ?>"
                         data-ratio="<?php echo $item['compression_ratio'] ?? 0; ?>"
                         data-webp="<?php echo $item['has_webp'] ?? 0; ?>"
                         onclick="handleItemClick(this, event)">
                        
                        <div class="item-checkbox-wrapper">
                            <input type="checkbox" class="item-checkbox" value="<?php echo $item['id']; ?>" onclick="handleCheckboxClick(event)">
                        </div>

                        <div class="usage-badge <?php echo $isUsed ? 'badge-used' : 'badge-unused'; ?>">
                            <?php echo $isUsed ? 'Used' : 'Unused'; ?>
                        </div>

                        <?php if(!empty($item['optimized'])): ?>
                        <div class="optimized-badge" title="Optimized (-<?php echo $item['compression_ratio']; ?>%)">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <?php endif; ?>

                        <div class="media-item-content">
                            <?php if ($isImage): ?>
                                <img src="<?php echo $item['url']; ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <div class="file-placeholder">
                                    <i class="fas fa-file-alt"></i>
                                    <span style="font-size:0.75rem; text-align:center; padding:0 0.5rem; word-break:break-all;"><?php echo htmlspecialchars($item['filename']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details Sidebar -->
    <div class="media-info-sidebar" id="media-info-sidebar" style="display: none;">
        <div class="media-info-sidebar-header">
            <span>Attachment Details</span>
            <button onclick="closeSidebar()" style="background:none; border:none; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div class="media-info-sidebar-content">
            <div class="media-detail-preview" id="sidebar-preview"></div>
            <div class="media-detail-info">
                <div id="sidebar-optimization" style="margin-bottom:0.5rem; font-size:0.8rem; color:var(--media-success); display:none;"></div>
                <h3 id="sidebar-filename" style="font-size:0.9rem; margin-bottom:0.2rem; word-break: break-all;"></h3>
                <p id="sidebar-date" style="font-size:0.8rem; color: var(--media-gray-600);"></p>
                <p id="sidebar-file-meta" style="font-size:0.8rem; color: var(--media-gray-600);"></p>
            </div>

            <div id="usage-container" class="usage-details">
                <h4><i class="fas fa-link"></i> Usage Frequency</h4>
                <ul id="usage-list" class="usage-list"></ul>
            </div>

            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--media-gray-200);">
                <label style="display:block; font-size:0.8rem; font-weight:600; margin-bottom:0.5rem;">File URL:</label>
                <div style="display:flex; border-radius: 6px; overflow: hidden; border: 1px solid var(--media-gray-200);">
                    <input type="text" id="sidebar-url-input" readonly style="flex:1; padding:0.5rem; border:none; font-size:0.75rem; background: var(--admin-white);">
                    <button class="btn-media" onclick="copyUrlToClipboard()" style="border-radius:0; padding: 0.5rem 1rem;">Copy</button>
                </div>
            </div>
            
            <div style="margin-top: 2rem;">
                <button class="btn-media btn-media-danger" id="btn-delete-item" style="width:100%; justify-content:center;">
                    <i class="fas fa-trash-alt"></i> Delete Permanently
                </button>
            </div>
        </div>
    </div>
</div>

<form id="hidden-upload-form" style="display: none;">
    <input type="file" id="upload-input" name="files[]" multiple accept="image/*,application/pdf">
</form>

<script>
    const csrfToken = '<?php echo $this->csrfToken(); ?>';
    let isSelectionMode = false;

    function toggleSelectionMode() {
        isSelectionMode = !isSelectionMode;
        const container = document.getElementById('media-manager');
        const btn = document.getElementById('btn-bulk-select');
        
        if (isSelectionMode) {
            container.classList.add('selection-mode');
            btn.classList.add('active');
            btn.innerHTML = '<i class="fas fa-times"></i> Cancel Selection';
            closeSidebar();
        } else {
            container.classList.remove('selection-mode');
            btn.classList.remove('active');
            btn.innerHTML = '<i class="fas fa-check-square"></i> Bulk Select';
            resetSelection();
        }
    }

    function resetSelection() {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
        updateSelectedCount();
    }

    function handleItemClick(element, event) {
        // Don't trigger if clicking on checkbox itself
        if (event.target.classList.contains('item-checkbox')) {
            return;
        }
        
        if (isSelectionMode) {
            const cb = element.querySelector('.item-checkbox');
            cb.checked = !cb.checked;
            updateSelectedCount();
        } else {
            // Check if any items are selected
            const anySelected = document.querySelectorAll('.item-checkbox:checked').length > 0;
            if (anySelected) {
                // If items are selected, clicking should toggle checkbox
                const cb = element.querySelector('.item-checkbox');
                cb.checked = !cb.checked;
                updateSelectedCount();
            } else {
                // Otherwise, open sidebar
                selectMedia(element);
            }
        }
    }

    function handleCheckboxClick(event) {
        event.stopPropagation();
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        const count = checked.length;
        document.getElementById('selected-count').innerText = `${count} item${count !== 1 ? 's' : ''} selected`;
        document.getElementById('btn-delete-selected').disabled = count === 0;
        
        // Show bulk actions bar if items are selected (even outside selection mode)
        const bulkBar = document.querySelector('.bulk-actions-toolbar');
        if (count > 0) {
            bulkBar.style.display = 'flex';
        } else if (!isSelectionMode) {
            bulkBar.style.display = 'none';
        }
    }

    function selectMedia(element) {
        document.querySelectorAll('.media-item').forEach(item => item.classList.remove('selected'));
        element.classList.add('selected');

        const data = element.dataset;
        const usage = JSON.parse(data.usage);

        document.getElementById('media-info-sidebar').style.display = 'flex';
        document.getElementById('sidebar-filename').innerText = data.filename;
        document.getElementById('sidebar-date').innerText = data.date;
        document.getElementById('sidebar-file-meta').innerText = data.size + (data.width ? ` (${data.width}x${data.height})` : '');
        document.getElementById('sidebar-url-input').value = data.url;

        const optDiv = document.getElementById('sidebar-optimization');
        if (data.optimized == '1') {
             optDiv.style.display = 'block';
             optDiv.innerHTML = '<i class="fas fa-check-circle"></i> Optimized ' + (data.ratio > 0 ? `(-${data.ratio}%)` : '') + (data.webp == '1' ? ' <span style="margin-left:5px; padding:1px 4px; background:var(--media-primary); color:white; border-radius:3px; font-size:9px;">WEBP</span>' : '');
        } else {
             optDiv.style.display = 'none';
        }

        const previewContainer = document.getElementById('sidebar-preview');
        previewContainer.innerHTML = data.type.startsWith('image/') ? `<img src="${data.url}" alt="">` : `<i class="fas fa-file-alt" style="font-size: 4rem; color: var(--media-gray-300);"></i>`;

        const usageList = document.getElementById('usage-list');
        usageList.innerHTML = '';
        if (usage.is_used) {
            usage.details.forEach(u => usageList.innerHTML += `<li class="usage-item"><span class="usage-type">${u.type}:</span><span class="usage-name">${u.name}</span></li>`);
        } else {
            usageList.innerHTML = `<li class="usage-item" style="color: var(--admin-danger); font-weight:600;">Not used anywhere</li>`;
        }

        document.getElementById('btn-delete-item').onclick = () => confirmDeleteItem(data.filename, data.id);
    }

    function closeSidebar() {
        document.getElementById('media-info-sidebar').style.display = 'none';
        document.querySelectorAll('.media-item').forEach(item => item.classList.remove('selected'));
    }

    function syncMedia() {
        const btn = document.getElementById('btn-sync');
        btn.classList.add('loading');
        btn.disabled = true;
        
        showNotification('Scanning storage and theme folders...', 'info');
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);

        fetch('<?php echo app_base_url("admin/content/media/sync"); ?>', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(data => {
            showNotification(data.message, data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => window.location.reload(), 1000);
            else { btn.classList.remove('loading'); btn.disabled = false; }
        });
    }

    function confirmBulkCleanup() {
        if (confirm('CAUTION: This will permanently delete ALL files marked as "Unused". Proceed?')) {
            const btn = document.getElementById('btn-cleanup');
            btn.classList.add('loading');
            btn.disabled = true;
            
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            fetch('<?php echo app_base_url("admin/content/media/bulk-cleanup"); ?>', {
                method: 'POST',
                body: formData
            }).then(r => r.json()).then(data => {
                showNotification(data.message, data.success ? 'success' : 'error');
                if (data.success) setTimeout(() => window.location.reload(), 1000);
                else { btn.classList.remove('loading'); btn.disabled = false; }
            });
        }
    }

    function deleteSelected() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value);
        
        if (confirm(`Are you sure you want to delete ${ids.length} selected items?`)) {
            const btn = document.getElementById('btn-delete-selected');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            ids.forEach(id => formData.append('ids[]', id));
            
            fetch('<?php echo app_base_url("admin/content/media/bulk-delete"); ?>', {
                method: 'POST',
                body: formData
            }).then(r => r.json()).then(data => {
                showNotification(data.message, data.success ? 'success' : 'error');
                if (data.success) setTimeout(() => window.location.reload(), 1000);
                else btn.disabled = false;
            });
        }
    }

    function confirmDeleteItem(filename, id) {
        if (confirm(`Delete "${filename}" permanently?`)) {
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            fetch(`<?php echo app_base_url("admin/content/media/delete/"); ?>${id}`, {
                method: 'POST',
                body: formData
            }).then(r => r.json()).then(data => {
                if (data.success) window.location.reload();
                else showNotification(data.message, 'error');
            });
        }
    }

    function copyUrlToClipboard() {
        const input = document.getElementById('sidebar-url-input');
        input.select();
        document.execCommand('copy');
        showNotification('Link copied!', 'success');
    }

    function filterType(type) {
        const url = new URL(window.location.href);
        if (type) url.searchParams.set('type', type); else url.searchParams.delete('type');
        window.location.href = url.toString();
    }

    function changePage(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }

    function changePerPage(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to page 1 when changing per-page
        window.location.href = url.toString();
    }

    document.getElementById('media-search').addEventListener('input', (e) => {
        clearTimeout(window.searchTimer);
        window.searchTimer = setTimeout(() => {
            const url = new URL(window.location.href);
            if (e.target.value) url.searchParams.set('search', e.target.value); else url.searchParams.delete('search');
            window.location.href = url.toString();
        }, 500);
    });

    const dropZone = document.getElementById('media-manager');
    const overlay = document.getElementById('upload-overlay');
    dropZone.addEventListener('dragenter', (e) => { e.preventDefault(); if(!isSelectionMode) overlay.classList.add('active'); });
    overlay.addEventListener('dragleave', (e) => { e.preventDefault(); overlay.classList.remove('active'); });
    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        overlay.classList.remove('active');
        if(!isSelectionMode) handleUpload(e.dataTransfer.files);
    });

    document.getElementById('upload-input').addEventListener('change', (e) => handleUpload(e.target.files));

    function handleUpload(files) {
        if (files.length === 0) return;
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        for(let f of files) formData.append('files[]', f);
        showNotification(`Uploading ${files.length} file(s)...`, 'info');
        fetch('<?php echo app_base_url("admin/content/media/upload"); ?>', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.success) window.location.reload(); else showNotification(data.message, 'error');
        });
    }
</script>