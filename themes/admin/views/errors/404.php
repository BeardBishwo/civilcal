<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - Bishwo Calculator</title>
    <link rel="stylesheet" href="<?php echo app_base_url('public/assets/css/admin.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: #667eea;
            line-height: 1;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-message {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .error-description {
            color: #718096;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .btn-home, .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-family: inherit;
        }
        .btn-home {
            background: #667eea;
        }
        .btn-home:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.25);
        }
        .btn-back {
            background: #e2e8f0;
            color: #4a5568;
        }
        .btn-back:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-message">Page Not Found</div>
        <p class="error-description">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>
        <div class="btn-container">
            <button onclick="window.history.back()" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i> Go Back
            </button>
            <a href="<?php echo app_base_url('/'); ?>" class="btn-home">
                <i class="fas fa-home me-2"></i> Homepage
            </a>
        </div>
    </div>
</body>
</html>
