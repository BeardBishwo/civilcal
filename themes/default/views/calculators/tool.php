<?php
$categoryTitle = ucwords(str_replace(['-','_'], ' ', $category ?? ''));
$toolTitle = ucwords(str_replace(['-','_'], ' ', $tool ?? ''));
$calc = $calculator ?? [];
$subcategory = $calc['subcategory'] ?? null;
$moduleUrl = '/modules/' . rawurlencode((string)$category) . '/';
if ($subcategory) { $moduleUrl = '/modules/' . rawurlencode((string)$category) . '/' . rawurlencode((string)$subcategory) . '/'; }
?>
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item"><a href="/calculator/<?php echo rawurlencode((string)$category); ?>"><?php echo htmlspecialchars($categoryTitle); ?></a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($toolTitle); ?></li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-center mb-2">
    <h1 class="h4 mb-0"><?php echo htmlspecialchars($toolTitle); ?></h1>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="/calculator/<?php echo rawurlencode((string)$category); ?>">
        <i class="bi bi-arrow-left"></i> Category
      </a>
      <a class="btn btn-primary" href="<?php echo $moduleUrl; ?>">
        <i class="bi bi-box-arrow-up-right"></i> Open Module
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <p class="text-muted mb-3">This calculator is provided by the <?php echo htmlspecialchars($categoryTitle); ?> module. Use the button above to open the detailed interactive calculator.</p>
      <?php if (!empty($calc)): ?>
        <div class="small text-muted">Slug: <code><?php echo htmlspecialchars((string)($calc['slug'] ?? '')); ?></code><?php if ($subcategory): ?> · Group: <code><?php echo htmlspecialchars((string)$subcategory); ?></code><?php endif; ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>
