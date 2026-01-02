<?php
// themes/admin/views/bounty/requests.php
?>
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0 text-dark">Bounty Submissions Review</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pending Safety Checks</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped projects">
                    <thead>
                        <tr>
                            <th>Submission Info</th>
                            <th>Bounty</th>
                            <th>Uploader</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="bounty-requests-table">
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
    // We need an endpoint for this. 
    // I'll add `getPendingAdminReview` to BountyApiController or logic here?
    // Let's add specific endpoint `api/admin/bounty/pending` or reuse a pattern.
    // I'll quickly implement `Api\BountyApiController::pendingSubmissions`
    
    fetch('<?= app_base_url("/api/admin/bounty/pending") ?>') 
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('bounty-requests-table');
            if(!data.success || !data.submissions.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No pending submissions.</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.submissions.map(sub => `
                <tr>
                    <td>
                    <td>
                        <span class="badge badge-warning">Pending</span><br>
                        <small>Submitted: ${new Date(sub.created_at).toLocaleDateString()}</small><br>
                        <div class="mt-2 text-xs">
                             <a href="<?= app_base_url("/api/bounty/download_preview?id=") ?>${sub.id}" target="_blank" class="text-blue-600 font-bold mb-1 block">⬇ Download Original</a>
                             ${sub.preview_path ? 
                                 `<a href="<?= app_base_url("/") ?>${sub.preview_path}" target="_blank" class="text-red-500 font-bold block">👁 View Generated Preview</a>` : 
                                 '<span class="text-gray-400">No Preview Gen</span>'
                             }
                        </div>
                    </td>
                    <td>
                        <strong>${sub.bounty_title}</strong>
                    </td>
                    <td>${sub.uploader_name}</td>
                    <td class="project-actions text-right">
                        <button class="btn btn-success btn-sm" onclick="approveSubmission(${sub.id})">
                            <i class="fas fa-check"></i> Safe
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="rejectSubmission(${sub.id})">
                            <i class="fas fa-trash"></i> Unsafe
                        </button>
                    </td>
                </tr>
            `).join('');
        });
}

function approveSubmission(id) {
    if(!confirm('Mark this file as SAFE for the client?')) return;
    
    fetch('<?= app_base_url("/api/admin/bounty/review") ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ submission_id: id, action: 'approve' })
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

function rejectSubmission(id) {
    const reason = prompt("Enter Rejection Reason (Virus, Spam, etc):");
    if(!reason) return;
    
    fetch('<?= app_base_url("/api/admin/bounty/review") ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ submission_id: id, action: 'reject', reason: reason })
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
