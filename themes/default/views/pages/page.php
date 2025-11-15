<div class="container">
    <div class="hero">
        <h1><?= htmlspecialchars($page['title'] ?? '') ?></h1>
    </div>
    <div class="content-body">
        <?= $page['body'] ?? '' ?>
    </div>
</div>