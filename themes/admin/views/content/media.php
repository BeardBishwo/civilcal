<?php
// Media Library View
$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-images"></i>
            Media Library
        </h1>
        <p class="page-description">Manage images, documents, and other media files</p>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
        <button class="btn btn-primary" onclick="document.getElementById(\'upload-form\').click()">
            <i class="fas fa-upload"></i>
            Upload Files
        </button>
        <div class="toolbar-actions">
            <div class="search-box">
                <input type="text" placeholder="Search media..." class="form-control">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </div>

    <!-- Hidden upload form -->
    <form id="upload-form" style="display: none;" enctype="multipart/form-data">
        <input type="file" name="files[]" multiple onchange="handleFileUpload(this)">
    </form>

    <!-- Media Grid -->
    <div class="media-grid">
        ' . implode('', array_map(function($item) {
                            $extension = pathinfo($item['filename'], PATHINFO_EXTENSION);
                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            
                            if ($isImage) {
                                $icon = '<img src="' . $item['url'] . '" alt="' . htmlspecialchars($item['filename']) . '" class="media-thumb">';
                            } else {
                                $icon = '<div class="media-icon"><i class="fas fa-file"></i></div>';
                            }
                            
                            return '<div class="media-item">
                                <div class="media-thumb-container">
                                    ' . $icon . '
                                </div>
                                <div class="media-info">
                                    <div class="media-filename">' . htmlspecialchars($item['filename']) . '</div>
                                    <div class="media-meta">' . $item['type'] . ' • ' . $item['size'] . '</div>
                                    <div class="media-date">' . $item['uploaded_at'] . '</div>
                                </div>
                                <div class="media-actions">
                                    <button class="btn btn-sm btn-icon" title="View" onclick="viewMedia(\'' . $item['url'] . '\')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon text-danger" title="Delete" onclick="confirmDelete(\'' . htmlspecialchars($item['filename']) . '\')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>';
                        }, $media ?? [])) . '
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <a href="#" class="page-link">Previous</a>
        <a href="#" class="page-link active">1</a>
        <a href="#" class="page-link">2</a>
        <a href="#" class="page-link">3</a>
        <a href="#" class="page-link">Next</a>
    </div>
</div>

<script>
function handleFileUpload(input) {
    // Handle file upload logic here
    if (input.files.length > 0) {
        alert("Uploading " + input.files.length + " file(s)...");
        // In a real implementation, you would send the files to the server
        input.form.reset();
    }
}

function viewMedia(url) {
    // Open media in a modal or new window
    window.open(url, "_blank");
}

function confirmDelete(filename) {
    if (confirm("Are you sure you want to delete " + filename + "?")) {
        // In a real implementation, you would send delete request to the server
        alert("Deleting " + filename + "...");
    }
}
</script>

<style>
.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    margin-top: 24px;
}

.media-item {
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    overflow: hidden;
    background: white;
    transition: var(--transition);
}

.media-item:hover {
    box-shadow: var(--admin-shadow);
    border-color: var(--admin-primary);
}

.media-thumb-container {
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--admin-gray-50);
    overflow: hidden;
}

.media-thumb {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}

.media-icon {
    font-size: 48px;
    color: var(--admin-gray-400);
    padding: 30px;
}

.media-info {
    padding: 12px;
}

.media-filename {
    font-weight: 500;
    color: var(--admin-gray-800);
    margin-bottom: 4px;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.media-meta {
    font-size: 12px;
    color: var(--admin-gray-600);
    margin-bottom: 4px;
}

.media-date {
    font-size: 12px;
    color: var(--admin-gray-500);
}

.media-actions {
    display: flex;
    padding: 8px;
    border-top: 1px solid var(--admin-border);
    gap: 4px;
}

.media-actions .btn {
    flex: 1;
}

@media (max-width: 768px) {
    .media-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
}
</style>
';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Media']
];

$page_title = $page_title ?? 'Media Library - Admin Panel';
$currentPage = $currentPage ?? 'content';

// Include the layout
include __DIR__ . '/../layouts/main.php';
?>