<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #f9fafb;
        }
        .container {
            max-width: 450px;
            width: 100%;
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        .header i {
            font-size: 3.5rem;
            color: #4cc9f0;
            margin-bottom: 1.5rem;
        }
        h1 { font-size: 1.75rem; margin-bottom: 0.5rem; }
        .subtitle { color: #9ca3af; font-size: 0.95rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #d1d5db; }
        input[type="text"] {
            width: 100%;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.6);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #f9fafb;
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 0.5rem;
            font-family: 'Courier New', monospace;
            transition: all 0.3s;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #4cc9f0;
            box-shadow: 0 0 0 4px rgba(76, 201, 240, 0.1);
        }
        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #4cc9f0 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(76, 201, 240, 0.3); }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: left;
        }
        .alert-danger { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }
        .links { margin-top: 2rem; font-size: 0.9rem; }
        .links a { color: #4cc9f0; text-decoration: none; transition: color 0.3s; }
        .links a:hover { color: #3b82f6; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-user-shield"></i>
            <h1>Two-Factor Verification</h1>
            <p class="subtitle">Enter the 6-digit code or a recovery code</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= app_base_url('/login/2fa') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="form-group">
                <input type="text" name="code" id="code" maxlength="10" placeholder="000000" required autofocus autocomplete="one-time-code">
            </div>
            <button type="submit" class="btn">Verify & Log In</button>
        </form>

        <div class="links">
            <p>Lost your device? <a href="<?= app_base_url('/forgot-password') ?>">Reset your password</a></p>
            <p style="margin-top: 0.5rem;"><a href="<?= app_base_url('/login') ?>">← Back to login</a></p>
        </div>
    </div>

    <script>
        document.getElementById('code').addEventListener('input', function(e) {
            // Allow numbers and recovery code characters
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>
