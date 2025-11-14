<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Bishwo Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .help-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .category-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
            text-decoration: none;
            color: inherit;
        }
        .category-card:hover {
            transform: translateY(-2px);
            color: inherit;
            text-decoration: none;
        }
        .search-box {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="help-header">
        <div class="container text-center">
            <h1><i class="fas fa-question-circle me-3"></i>Help Center</h1>
            <p class="lead mb-0">Find answers to your questions about engineering calculations</p>
        </div>
    </div>

    <div class="container">
        <!-- Search Box -->
        <div class="search-box">
            <h4 class="mb-3">How can we help you?</h4>
            <div class="input-group">
                <input type="text" class="form-control form-control-lg" placeholder="Search for help articles...">
                <button class="btn btn-primary btn-lg" type="button">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>

        <!-- Help Categories -->
        <div class="row">
            <div class="col-md-4">
                <a href="#" class="category-card d-block">
                    <div class="text-center">
                        <i class="fas fa-calculator fa-3x text-primary mb-3"></i>
                        <h5>Using Calculators</h5>
                        <p class="text-muted">Learn how to use our engineering calculation tools</p>
                        <span class="badge bg-primary">12 articles</span>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4">
                <a href="#" class="category-card d-block">
                    <div class="text-center">
                        <i class="fas fa-user fa-3x text-success mb-3"></i>
                        <h5>Account Management</h5>
                        <p class="text-muted">Manage your profile, settings, and preferences</p>
                        <span class="badge bg-success">8 articles</span>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4">
                <a href="#" class="category-card d-block">
                    <div class="text-center">
                        <i class="fas fa-cog fa-3x text-warning mb-3"></i>
                        <h5>Technical Support</h5>
                        <p class="text-muted">Troubleshooting and technical assistance</p>
                        <span class="badge bg-warning">15 articles</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Popular Articles -->
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="mb-3">Popular Articles</h4>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            How to calculate concrete volume
                        </h6>
                        <p class="card-text text-muted">Step-by-step guide to calculating concrete volume for construction projects.</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-file-alt text-success me-2"></i>
                            Understanding steel beam calculations
                        </h6>
                        <p class="card-text text-muted">Learn about structural steel beam calculations and load analysis.</p>
                        <a href="#" class="btn btn-sm btn-outline-success">Read More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-file-alt text-warning me-2"></i>
                            Creating an account
                        </h6>
                        <p class="card-text text-muted">How to register and set up your engineering calculator account.</p>
                        <a href="#" class="btn btn-sm btn-outline-warning">Read More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-file-alt text-info me-2"></i>
                            API documentation
                        </h6>
                        <p class="card-text text-muted">Access our calculation tools programmatically through our API.</p>
                        <a href="/bishwo_calculator/developers" class="btn btn-sm btn-outline-info">Read More</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="fas fa-headset me-2"></i>Need More Help?</h5>
                    <p class="mb-2">Can't find what you're looking for? Our support team is here to help!</p>
                    <a href="mailto:support@bishwocalculator.com" class="btn btn-info me-2">
                        <i class="fas fa-envelope me-1"></i> Contact Support
                    </a>
                    <a href="/bishwo_calculator/admin" class="btn btn-outline-info">
                        <i class="fas fa-tachometer-alt me-1"></i> Admin Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
