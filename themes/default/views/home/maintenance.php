<?php
http_response_code(503);
require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="display-1 mb-4">503</h1>
            <h2 class="mb-4">Maintenance Mode</h2>
            <p class="lead mb-4">We're currently performing scheduled maintenance. We'll be back shortly!</p>
            <div id="maintenance-timer" class="alert alert-info mb-4" role="alert">
                Estimated completion time: <span id="completion-time">Loading...</span>
            </div>
            <div class="mb-4">
                <button onclick="location.reload()" class="btn btn-primary">Check Again</button>
            </div>
        </div>
    </div>
</div>

<script>
// If maintenance end time is set in meta tags, show countdown
document.addEventListener('DOMContentLoaded', function() {
    const metaMaintenanceEnd = document.querySelector('meta[name="maintenance-end"]');
    if (metaMaintenanceEnd) {
        const endTime = new Date(metaMaintenanceEnd.content);
        updateTimer(endTime);
        setInterval(() => updateTimer(endTime), 1000);
    } else {
        document.getElementById('maintenance-timer').style.display = 'none';
    }
});

function updateTimer(endTime) {
    const now = new Date();
    const diff = endTime - now;
    
    if (diff <= 0) {
        location.reload();
        return;
    }
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    document.getElementById('completion-time').textContent = 
        `${hours}h ${minutes}m ${seconds}s`;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>