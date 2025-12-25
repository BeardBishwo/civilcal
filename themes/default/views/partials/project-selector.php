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
    <!-- This will be injected as a column -->
    <div class="project-selector-wrapper h-100 d-flex align-items-end">
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-light">
                <i class="fas fa-folder text-primary"></i>
            </span>
            <select name="project_id" class="form-select bg-dark text-light border-secondary">
                <option value="">-- Save to Project (Optional) --</option>
                <?php foreach ($projects as $proj): ?>
                    <option value="<?php echo $proj['id']; ?>"><?php echo htmlspecialchars($proj['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <a href="<?php echo app_base_url('/projects'); ?>" target="_blank" class="btn btn-outline-secondary" title="Create New Project">
                <i class="fas fa-plus"></i>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const template = document.getElementById('project-selector-template');
    if (!template) return;

    // TARGET ONLY CALCULATOR FORMS
    // We strictly look for the specific ID we added to the template
    const calcForm = document.getElementById('calculator-form');
    
    if (calcForm) {
        const submitBtn = calcForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            const btnContainer = submitBtn.closest('.col-12'); // The standard container in our template
            
            if (btnContainer && btnContainer.parentNode.classList.contains('row')) {
                // 1. Create a new column for the project selector
                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-5 mt-4'; 
                colDiv.innerHTML = template.innerHTML;
                
                // 2. Adjust the button container
                btnContainer.classList.remove('col-12');
                btnContainer.classList.add('col-md-7');
                
                // 3. Insert selector BEFORE the button container
                btnContainer.parentNode.insertBefore(colDiv, btnContainer);
            } else {
                // Fallback Layout
                const wrapper = document.createElement('div');
                wrapper.className = 'mb-3';
                wrapper.innerHTML = template.innerHTML;
                submitBtn.parentNode.insertBefore(wrapper, submitBtn);
            }
        }
    }
});
</script>
<?php endif; ?>
