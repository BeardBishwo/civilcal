<?php
// Remove the variable assignment approach and use the themes/admin layout system
$page_title = 'Media Library - Admin Panel';
$currentPage = 'content';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Media']
];

// Stats calculation
$totalFiles = isset($media) ? count($media) : 0;
$totalImages = isset($media) ? count(array_filter($media, fn($m) => strpos($m['type'], 'image') === 0)) : 0;
$totalSize = 0; // Would need raw bytes to calculate accurately
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-images"></i>
                    <h1>Media Library</h1>
                </div>
                <div class="header-subtitle">Manage images, documents, and other media files</div>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary btn-compact" onclick="document.getElementById('upload-form').click()">
                    <i class="fas fa-upload"></i>
                    <span>Upload Files</span>
                </button>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalFiles; ?></div>
                    <div class="stat-label">Total Files</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-image"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalImages; ?></div>
                    <div class="stat-label">Images</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">--</div>
                    <div class="stat-label">Total Storage</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search media..." class="form-control" id="media-search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                        <button class="search-clear" onclick="window.location.href='<?php echo app_base_url('admin/content/media'); ?>'">
                            <i class="fas fa-times"></i>
                        </button>
                    <?php endif; ?>
                </div>
                <select class="filter-compact" onchange="window.location.href='<?php echo app_base_url('admin/content/media'); ?>?type='+this.value">
                    <option value="">All Types</option>
                    <option value="image" <?php echo (isset($_GET['type']) && $_GET['type'] === 'image') ? 'selected' : ''; ?>>Images</option>
                    <option value="document" <?php echo (isset($_GET['type']) && $_GET['type'] === 'document') ? 'selected' : ''; ?>>Documents</option>
                </select>
            </div>
            <div class="toolbar-right">
                <div class="view-controls">
                    <button class="view-btn active" data-view="grid" title="Grid View">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <!-- List view could be added later -->
                </div>
            </div>
        </div>

        <!-- Hidden upload form -->
        <form id="upload-form" style="display: none;" enctype="multipart/form-data">
            <input type="file" name="files[]" multiple onchange="handleFileUpload(this)">
        </form>

        <!-- Media Content Area -->
        <div class="media-content">
            <div class="grid-container">
                <?php if (empty($media)): ?>
                    <div class="empty-state-compact">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h3>No Media Files</h3>
                        <p>Upload your first file to get started</p>
                        <button class="btn btn-primary btn-compact" onclick="document.getElementById('upload-form').click()">
                            <i class="fas fa-upload"></i>
                            Upload Files
                        </button>
                    </div>
                <?php else: ?>
                    <div class="media-grid-compact">
                        <?php foreach ($media as $item): ?>
                            <?php
                            $extension = pathinfo($item['filename'], PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            ?>
                            <div class="media-card-compact">
                                <div class="media-preview-container">
                                    <?php if ($isImage): ?>
                                        <img src="<?php echo $item['url']; ?>" alt="<?php echo htmlspecialchars($item['filename']); ?>" loading="lazy">
                                    <?php else: ?>
                                        <div class="file-icon">
                                            <i class="fas fa-file-<?php echo ($extension === 'pdf') ? 'pdf' : 'alt'; ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="media-card-content">
                                    <div class="media-card-title" title="<?php echo htmlspecialchars($item['filename']); ?>">
                                        <?php echo htmlspecialchars($item['filename']); ?>
                                    </div>
                                    <div class="media-card-meta">
                                        <span><?php echo $item['type']; ?></span>
                                        <span>•</span>
                                        <span><?php echo $item['size']; ?></span>
                                    </div>
                                </div>
                                <div class="media-card-footer">
                                    <button class="action-btn-sm" title="View" onclick="viewMedia('<?php echo $item['url']; ?>')">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn-sm text-danger" title="Delete" onclick="confirmDelete('<?php echo htmlspecialchars($item['filename'], ENT_QUOTES); ?>', <?php echo $item['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Compact Pagination -->
        <?php if (!empty($media) && isset($pagination) && $pagination['last_page'] > 1): ?>
            <div class="pagination-compact">
                <?php
                $currentPage = $pagination['current_page'];
                $lastPage = $pagination['last_page'];
                $baseUrl = app_base_url('admin/content/media');

                // Build query string
                $queryParams = [];
                if (isset($_GET['search'])) {
                    $queryParams['search'] = $_GET['search'];
                }
                if (isset($_GET['type'])) {
                    $queryParams['type'] = $_GET['type'];
                }

                function buildPageUrl($baseUrl, $queryParams, $page)
                {
                    $queryParams['page'] = $page;
                    return $baseUrl . '?' . http_build_query($queryParams);
                }
                ?>

                <div class="pagination-info">
                    Showing page <?php echo $currentPage; ?> of <?php echo $lastPage; ?>
                </div>

                <div class="pagination-controls">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?php echo buildPageUrl($baseUrl, $queryParams, $currentPage - 1); ?>" class="page-btn">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++): ?>
                        <a href="<?php echo buildPageUrl($baseUrl, $queryParams, $i); ?>"
                            class="page-btn <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $lastPage): ?>
                        <a href="<?php echo buildPageUrl($baseUrl, $queryParams, $currentPage + 1); ?>" class="page-btn">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    // CSRF Token
    const csrfToken = '<?php echo $this->generateCsrfToken(); ?>';

    // File Upload Handler (Reused logic with updated UI references if needed)
    function handleFileUpload(input) {
        if (input.files.length === 0) return;

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);

        for (let i = 0; i < input.files.length; i++) {
            formData.append('files[]', input.files[i]);
        }

        // Show loading state on upload button
        const uploadBtns = document.querySelectorAll('.btn-primary');
        uploadBtns.forEach(btn => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            btn.disabled = true;
            btn.dataset.originalHtml = originalHtml;
        });

        fetch('<?php echo app_base_url('admin/content/media/upload'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification('error', data.message || 'Upload failed');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showNotification('error', 'Upload failed. Please try again.');
            })
            .finally(() => {
                uploadBtns.forEach(btn => {
                    btn.innerHTML = btn.dataset.originalHtml;
                    btn.disabled = false;
                });
                input.form.reset();
            });
    }

    function viewMedia(url) {
        window.open(url, '_blank');
    }

    function confirmDelete(filename, mediaId) {
        if (!confirm(`Are you sure you want to delete "${filename}"?`)) {
            return;
        }

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);

        fetch(`<?php echo app_base_url('admin/content/media/delete/'); ?>${mediaId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message);
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showNotification('error', data.message || 'Delete failed');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showNotification('error', 'Delete failed. Please try again.');
            });
    }

    // Debounced Search
    let searchTimeout;
    const searchInput = document.getElementById('media-search');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value;
                const currentUrl = new URL(window.location.href);
                if (searchTerm) {
                    currentUrl.searchParams.set('search', searchTerm);
                } else {
                    currentUrl.searchParams.delete('search');
                }
                currentUrl.searchParams.set('page', '1');
                window.location.href = currentUrl.toString();
            }, 500);
        });
    }

    function showNotification(type, message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type === 'success' ? 'success' : 'error'}`; // Match new CSS class names if possible
        // Or ensure .notification class handles it. Pages.php used .notification-info/success

        // Use the structure from pages.php CSS
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        // Add style to ensure it floats (if not already handled by common CSS)
        // But we added .notification styles to admin.css now!

        document.body.appendChild(notification);

        // Trigger generic "visible" class
        setTimeout(() => notification.classList.add('visible'), 10);

        setTimeout(() => {
            notification.classList.remove('visible');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
</script>