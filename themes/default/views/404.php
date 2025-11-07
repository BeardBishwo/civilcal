<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="text-center mt-5">
                <h1 class="display-1 text-muted">404</h1>
                <h2 class="mb-4"><?php echo htmlspecialchars($title ?? 'Page Not Found'); ?></h2>
                <p class="lead text-muted"><?php echo htmlspecialchars($subtitle ?? 'The page you are looking for does not exist'); ?></p>
                <p class="mb-4"><?php echo htmlspecialchars($description ?? 'Sorry, the page you requested could not be found on our server.'); ?></p>
                
                <div class="mt-4">
                    <a href="/" class="btn btn-primary btn-lg">Go Home</a>
                    <a href="/calculator/civil" class="btn btn-secondary btn-lg">Civil Tools</a>
                    <a href="/calculator/electrical" class="btn btn-success btn-lg">Electrical Tools</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <h3>Popular Engineering Tools</h3>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Civil Engineering</h5>
                            <p class="card-text">Concrete, structural, and earthwork calculations</p>
                            <a href="/calculator/civil" class="btn btn-primary">Explore Tools</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Electrical Engineering</h5>
                            <p class="card-text">Load calculations, wire sizing, and design tools</p>
                            <a href="/calculator/electrical" class="btn btn-primary">Explore Tools</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Structural Engineering</h5>
                            <p class="card-text">Beam, column, and foundation design tools</p>
                            <a href="/calculator/structural" class="btn btn-primary">Explore Tools</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
