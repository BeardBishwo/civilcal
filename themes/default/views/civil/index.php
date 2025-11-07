<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><?php echo htmlspecialchars($title ?? 'Civil Engineering Tools'); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($description ?? 'Professional civil engineering calculation tools'); ?></p>
        </div>
    </div>
    
    <?php if (isset($calculators) && is_array($calculators)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <h3>Available Tools</h3>
            <div class="row">
                <?php foreach ($calculators as $key => $name): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($name); ?></h5>
                            <p class="card-text">Professional calculation tool for <?php echo htmlspecialchars(strtolower($name)); ?>.</p>
                            <a href="/calculator/<?php echo htmlspecialchars($key); ?>" class="btn btn-primary">Open Tool</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h4>Civil Engineering Categories</h4>
                <p>Explore our comprehensive suite of civil engineering calculation tools:</p>
                <ul>
                    <li><strong>Concrete Mix Design & Volume</strong> - Calculate concrete proportions and volumes</li>
                    <li><strong>Structural Analysis & Design</strong> - Analyze and design structural elements</li>
                    <li><strong>Earthwork & Excavation</strong> - Calculate cut and fill volumes</li>
                    <li><strong>Masonry & Brickwork</strong> - Estimate brick and mortar quantities</li>
                    <li><strong>Plastering & Finishing</strong> - Calculate plaster quantities and costs</li>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
