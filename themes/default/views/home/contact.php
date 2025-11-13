<?php ?>

<div class="container py-4">
    <div class="app-header">
        <h1 class="app-title">Contact Us</h1>
        <p class="app-subtitle">Send feedback, feature requests, or general inquiries.</p>
    </div>

    <div class="glass-card p-4">
        <form id="contactForm">
            <div class="mb-3">
                <label for="contactName" class="form-label">Name</label>
                <input type="text" id="contactName" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contactEmail" class="form-label">Email</label>
                <input type="email" id="contactEmail" name="email" class="form-control">
            </div>
            <div class="mb-3">
                <label for="contactMessage" class="form-label">Message</label>
                <textarea id="contactMessage" name="message" class="form-control" rows="6" required></textarea>
            </div>
            <div id="contactFeedback" role="status" aria-live="polite" style="margin-bottom:1rem"></div>
            <button type="submit" class="btn btn-primary">Send Message</button>
            <a href="report.php" class="btn btn-link ms-2">Report an issue</a>
        </form>
    </div>
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const btn = this.querySelector('button[type=submit]');
    const feedback = document.getElementById('contactFeedback');
    feedback.textContent = '';
    btn.disabled = true;
    const payload = {
        name: document.getElementById('contactName').value,
        email: document.getElementById('contactEmail').value,
        message: document.getElementById('contactMessage').value,
        type: 'contact'
    };
    try{
        const res = await fetch('/aec-calculator/api/save_contact.php', { method: 'POST', headers: { 'Content-Type':'application/json' }, body: JSON.stringify(payload) });
        const data = await res.json();
        if (data && data.success) {
            feedback.innerHTML = '<div class="alert alert-success">Thanks — your message was saved.</div>';
            this.reset();
        } else {
            feedback.innerHTML = '<div class="alert alert-danger">Error saving message.</div>';
        }
    }catch(err){
        feedback.innerHTML = '<div class="alert alert-danger">Network error. Try again later.</div>';
    }
    btn.disabled = false;
});
</script>

<?php require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php'; ?>

