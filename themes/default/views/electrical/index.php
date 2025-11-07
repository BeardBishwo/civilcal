<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><?php echo htmlspecialchars($title ?? 'Electrical Engineering Tools'); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($description ?? 'Professional electrical engineering calculation tools'); ?></p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <h3>Electrical Engineering Categories</h3>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Load Calculations</h5>
                            <p class="card-text">Calculate electrical load requirements and demand factors.</p>
                            <a href="/calculator/load-calculation" class="btn btn-primary">Open Tool</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Wire & Cable Sizing</h5>
                            <p class="card-text">Determine proper wire gauge based on current and distance.</p>
                            <a href="/calculator/wire-sizing" class="btn btn-primary">Open Tool</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Voltage Drop Analysis</h5>
                            <p class="card-text">Calculate voltage drop for proper electrical design.</p>
                            <a href="/calculator/voltage-drop" class="btn btn-primary">Open Tool</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Conduit Sizing</h5>
                            <p class="card-text">Calculate conduit fill and sizing requirements.</p>
                            <a href="/calculator/conduit-sizing" class="btn btn-primary">Open Tool</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Short Circuit Analysis</h5>
                            <p class="card-text">Calculate available fault current and protective device sizing.</p>
                            <a href="/calculator/short-circuit" class="btn btn-primary">Open Tool</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
