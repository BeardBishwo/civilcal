<?php
// themes/admin/views/library/requests.php
// Assuming Admin Theme structure
?>
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0 text-dark">Library Requests</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pending Approvals</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped projects">
                    <thead>
                        <tr>
                            <th>File Info</th>
                            <th>Uploader</th>
                            <th>Stats</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="requests-table">
                        <!-- Loaded via AJAX -->
                        <tr><td colspan="4" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadRequests();
});

function loadRequests() {
    // Current browse API doesn't support fetching pending files for security.
    // We need an admin specific API endpoint or a param to browse.
    // Let's assume we implement `api/admin/library/pending` functionality.
    // Or reuse browse with status=pending if admin.
    
    // For now, I'll update the API to support this or create a new endpoint.
    // Let's assume `/api/library/browse?status=pending` works for Authorized Admins 
    // (though I haven't implemented that check yet, I should).
    // Or I'll just use a placeholder here and fix the API next.
    
    fetch('/api/library/browse?status=pending&admin_mode=true') 
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('requests-table');
            if(!data.files || data.files.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No pending requests.</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.files.map(file => `
                <tr>
                    <td>
                        <strong>${file.title}</strong><br>
                        <small>${file.description || ''}</small><br>
                        <span class="badge badge-info">${file.file_type}</span>
                        <a href="/api/library/download_preview?id=${file.id}" target="_blank" class="text-xs ml-2">Download to Review</a>
                    </td>
                    <td>${file.uploader_name}</td>
                    <td>${file.file_size_kb} KB</td>
                    <td class="project-actions text-right">
                        <button class="btn btn-success btn-sm" onclick="approveFile(${file.id})">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="rejectFile(${file.id})">
                            <i class="fas fa-trash"></i> Reject
                        </button>
                    </td>
                </tr>
            `).join('');
        });
}

function approveFile(id) {
    if(!confirm('Approve this file and award 100 coins?')) return;
    
    fetch('/api/admin/library/approve', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ file_id: id, action: 'approve' })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Approved!');
            loadRequests();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function rejectFile(id) {
    const reason = prompt("Enter rejection reason:");
    if(reason === null) return;
    
    fetch('/api/admin/library/approve', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ file_id: id, action: 'reject', reason: reason })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Rejected!');
            loadRequests();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>
