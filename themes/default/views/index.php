<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="jumbotron bg-light rounded-3">
                <div class="container-fluid py-5">
                    <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($title ?? 'Welcome to Bishwo Calculator'); ?></h1>
                    <p class="col-md-8 fs-4"><?php echo htmlspecialchars($description ?? 'Professional engineering calculation tools'); ?></p>
                    
                    <?php if (isset($subtitle)): ?>
                    <h3 class="text-muted"><?php echo htmlspecialchars($subtitle); ?></h3>
                    <?php endif; ?>
                    
                    <a href="/calculator/civil" class="btn btn-primary btn-lg me-2">Civil Engineering</a>
                    <a href="/calculator/electrical" class="btn btn-secondary btn-lg me-2">Electrical Engineering</a>
                    <a href="/calculator/structural" class="btn btn-success btn-lg">Structural Engineering</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <h2>Engineering Calculator Categories</h2>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Civil Engineering</h5>
                            <p class="card-text">Concrete mix design, structural analysis, earthwork calculations, and more.</p>
                            <a href="/calculator/civil" class="btn btn-primary">Explore Tools</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Electrical Engineering</h5>
                            <p class="card-text">Load calculations, wire sizing, voltage drop analysis, and electrical design.</p>
                            <a href="/calculator/electrical" class="btn btn-primary">Explore Tools</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Structural Engineering</h5>
                            <p class="card-text">Beam analysis, column design, slab design, and foundation calculations.</p>
                            <a href="/calculator/structural" class="btn btn-primary">Explore Tools</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Plumbing Engineering</h5>
                            <p class="card-text">Water supply design, drainage calculations, and plumbing system analysis.</p>
                            <a href="/calculator/plumbing" class="btn btn-primary">Explore Tools</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">HVAC Engineering</h5>
                            <p class="card-text">HVAC load calculations, equipment sizing, and energy analysis tools.</p>
                            <a href="/calculator/hvac" class="btn btn-primary">Explore Tools</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Fire Protection</h5>
                            <p class="card-text">Sprinkler system design, hydraulic calculations, and fire safety analysis.</p>
                            <a href="/calculator/fire" class="btn btn-primary">Explore Tools</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
