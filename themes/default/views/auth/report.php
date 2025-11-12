<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<div class="container py-4">
    <div class="app-header">
        <h1 class="app-title">Report an Issue</h1>
        <p class="app-subtitle">Report bugs, incorrect calculations or security concerns.</p>
    </div>

    <div class="glass-card p-4">
        <form id="reportForm">
            <div class="mb-3">
                <label for="reportName" class="form-label">Name</label>
                <input type="text" id="reportName" name="name" class="form-control">
            </div>
            <div class="mb-3">
                <label for="reportEmail" class="form-label">Email (optional)</label>
                <input type="email" id="reportEmail" name="email" class="form-control">
            </div>
            <div class="mb-3">
                <label for="reportDetails" class="form-label">Details</label>
                <textarea id="reportDetails" name="message" class="form-control" rows="6" required></textarea>
            </div>
            <div id="reportFeedback" role="status" aria-live="polite" style="margin-bottom:1rem"></div>
            <button type="submit" class="btn btn-danger">Submit Report</button>
            <a href="contact.php" class="btn btn-link ms-2">Contact us</a>
        </form>
    </div>
</div>

<script>
document.getElementById('reportForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const btn = this.querySelector('button[type=submit]');
    const feedback = document.getElementById('reportFeedback');
    feedback.textContent = '';
    btn.disabled = true;
    const payload = {
        name: document.getElementById('reportName').value,
        email: document.getElementById('reportEmail').value,
        message: document.getElementById('reportDetails').value,
        type: 'report'
    };
    try{
        const res = await fetch('/aec-calculator/api/save_contact.php', { method: 'POST', headers: { 'Content-Type':'application/json' }, body: JSON.stringify(payload) });
        const data = await res.json();
        if (data && data.success) {
            feedback.innerHTML = '<div class="alert alert-success">Thank you — report received.</div>';
            this.reset();
        } else {
            feedback.innerHTML = '<div class="alert alert-danger">Error submitting report.</div>';
        }
    }catch(err){
        feedback.innerHTML = '<div class="alert alert-danger">Network error. Try again later.</div>';
    }
    btn.disabled = false;
});
</script>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>