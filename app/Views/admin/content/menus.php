<div class="container">
    <h2>Primary Menu</h2>
    <p>Edit items as JSON array: [{"label":"Civil","url":"/civil","icon":"fas fa-hard-hat"}, ...]</p>
    <form action="/admin/content/menus/save" method="post">
        <textarea name="items" rows="12" class="form-control"><?= htmlspecialchars(json_encode($items ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></textarea>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Menu</button>
            <a href="/" target="_blank" class="btn btn-secondary">Preview</a>
        </div>
    </form>
</div>