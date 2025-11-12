<?php
$categoryTitle = ucwords(str_replace(['-','_'], ' ', $category ?? ''));
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><?php echo htmlspecialchars($categoryTitle); ?> Calculators</h1>
    <a class="btn btn-outline-secondary" href="/calculators">
      <i class="bi bi-arrow-left"></i> All Categories
    </a>
  </div>

  <?php if (!empty($calculators)): ?>
    <div class="row g-3">
      <?php foreach ($calculators as $calc): ?>
        <?php
          $slug = $calc['slug'] ?? '';
          $name = $calc['name'] ?? $slug;
          $href = "/calculator/" . rawurlencode((string)$category) . "/" . rawurlencode((string)$slug);
        ?>
        <div class="col-12 col-md-6 col-lg-4">
          <a class="card h-100 text-decoration-none" href="<?php echo $href; ?>">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2" style="gap: .5rem;">
                <i class="bi bi-calculator"></i>
                <strong class="text-body"><?php echo htmlspecialchars((string)$name); ?></strong>
              </div>
              <?php if (!empty($calc['subcategory'])): ?>
                <div class="text-muted small">Group: <?php echo htmlspecialchars((string)$calc['subcategory']); ?></div>
              <?php endif; ?>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No calculators found for this category.</div>
  <?php endif; ?>
</div>
