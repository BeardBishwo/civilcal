<?php
// Only show if user is logged in
if (isset($_SESSION['user'])): 
    // Fetch projects directly here to avoid controller dependency for this global partial
    $db = \App\Core\Database::getInstance()->getPdo();
    $stmt = $db->prepare("SELECT id, name FROM projects WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user']['id']]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="project-selector-template" style="display:none;">
    <div class="mb-3 project-selector-group">
        <label class="form-label d-flex justify-content-between align-items-center">
            <span><i class="fas fa-folder me-1"></i> Save to Project</span>
            <small class="text-muted"><a href="<?php echo app_base_url('/projects'); ?>" target="_blank" class="text-decoration-none">+ New</a></small>
        </label>
        <select name="project_id" class="form-select">
            <option value="">-- Select Project --</option>
            <?php foreach ($projects as $proj): ?>
                <option value="<?php echo $proj['id']; ?>"><?php echo htmlspecialchars($proj['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Find all calculator forms
    // Strategy: Look for forms that have a submit button
    const forms = document.querySelectorAll('form');
    const template = document.getElementById('project-selector-template');
    
    if (!template) return;

    forms.forEach(form => {
        // Try to identify if it's a calculator form. 
        // Can check if it has inputs or specific classes. 
        // For now, let's look for a submit button.
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            // Clone the selector
            const selectorHtml = template.innerHTML;
            const wrapper = document.createElement('div');
            wrapper.innerHTML = selectorHtml;
            
            // Insert before the submit button's container or the button itself
            submitBtn.parentNode.insertBefore(wrapper.firstElementChild, submitBtn);
        }
    });
});
</script>
<?php endif; ?>
