<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enable Two-Factor Authentication - Bishwo Calculator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo app_base_url('/assets/css/global-notifications.css'); ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .header i {
            font-size: 3rem;
            color: #4cc9f0;
            margin-bottom: 1rem;
        }
        
        h1 {
            color: #f9fafb;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            color: #9ca3af;
            font-size: 0.95rem;
        }
        
        .step {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #4cc9f0;
        }
        
        .step-number {
            display: inline-block;
            width: 32px;
            height: 32px;
            background: #4cc9f0;
            color: #0f172a;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            font-weight: bold;
            margin-right: 0.75rem;
        }
        
        .step-title {
            color: #f9fafb;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .step-content {
            color: #d1d5db;
            line-height: 1.6;
        }
        
        .qr-container {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            margin: 1rem 0;
        }
        
        .qr-code {
            max-width: 200px;
            height: auto;
            margin: 0 auto;
        }
        
        .secret-code {
            background: rgba(15, 23, 42, 0.8);
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            text-align: center;
        }
        
        .secret-text {
            font-family: 'Courier New', monospace;
            color: #4cc9f0;
            font-size: 1.1rem;
            letter-spacing: 2px;
            word-break: break-all;
        }
        
        .copy-btn {
            background: rgba(76, 201, 240, 0.1);
            border: 1px solid #4cc9f0;
            color: #4cc9f0;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .copy-btn:hover {
            background: rgba(76, 201, 240, 0.2);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            color: #e5e7eb;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 0.875rem;
            background: rgba(15, 23, 42, 0.6);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #f9fafb;
            font-size: 1rem;
            transition: all 0.3s;
            text-align: center;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #4cc9f0;
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.1);
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            flex: 1;
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4cc9f0 0%, #3b82f6 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(76, 201, 240, 0.3);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #e5e7eb;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }
        
        .alert-warning {
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: #fbbf24;
        }
        
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .app-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .app-item {
            background: rgba(15, 23, 42, 0.6);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .app-item i {
            font-size: 2rem;
            color: #4cc9f0;
            margin-bottom: 0.5rem;
        }
        
        .app-name {
            color: #d1d5db;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-shield-alt"></i>
            <h1>Enable Two-Factor Authentication</h1>
            <p class="subtitle">Add an extra layer of security to your account</p>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>What is 2FA?</strong> Two-factor authentication adds an extra security step to your login. Even if someone knows your password, they won't be able to access your account without your authentication code.
        </div>
        
        <!-- Step 1: Install App -->
        <div class="step">
            <div class="step-title">
                <span class="step-number">1</span>
                Install an Authenticator App
            </div>
            <div class="step-content">
                <p>Download and install one of these authenticator apps on your mobile device:</p>
                <div class="app-list">
                    <div class="app-item">
                        <i class="fab fa-google"></i>
                        <div class="app-name">Google Authenticator</div>
                    </div>
                    <div class="app-item">
                        <i class="fab fa-microsoft"></i>
                        <div class="app-name">Microsoft Authenticator</div>
                    </div>
                    <div class="app-item">
                        <i class="fas fa-lock"></i>
                        <div class="app-name">Authy</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Step 2: Scan QR Code -->
        <div class="step">
            <div class="step-title">
                <span class="step-number">2</span>
                Scan QR Code
            </div>
            <div class="step-content">
                <p>Open your authenticator app and scan this QR code:</p>
                <div class="qr-container">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($qr_code_url); ?>" 
                         alt="QR Code" 
                         class="qr-code">
                </div>
                
                <p style="margin-top: 1rem;">Or manually enter this secret key:</p>
                <div class="secret-code">
                    <div class="secret-text" id="secretKey"><?php echo htmlspecialchars($secret); ?></div>
                    <button type="button" class="copy-btn" onclick="copySecret()">
                        <i class="fas fa-copy"></i> Copy Secret Key
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Step 3: Verify Code -->
        <div class="step">
            <div class="step-title">
                <span class="step-number">3</span>
                Verify Authentication Code
            </div>
            <div class="step-content">
                <p>Enter the 6-digit code shown in your authenticator app:</p>
                
                <form id="verifyForm">
                    <div class="form-group">
                        <label for="code">Authentication Code</label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               maxlength="6" 
                               pattern="[0-9]{6}" 
                               placeholder="000000" 
                               required 
                               autocomplete="off">
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> After enabling 2FA, you'll receive recovery codes. Save them in a secure location. You'll need them to access your account if you lose your device.
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo app_base_url('/user/profile'); ?>'">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="verifyBtn">
                            <span id="btnText">Enable 2FA</span>
                            <div class="spinner" id="spinner"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const baseUrl = window.location.origin + (window.appConfig ? window.appConfig.baseUrl : '/Bishwo_Calculator');
        
        function copySecret() {
            const secretKey = document.getElementById('secretKey').textContent;
            navigator.clipboard.writeText(secretKey).then(() => {
                showNotification('Secret key copied to clipboard!', 'success');
            });
        }
        
        // Auto-focus code input
        document.getElementById('code').focus();
        
        // Auto-format code input
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        document.getElementById('verifyForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const code = document.getElementById('code').value;
            const btn = document.getElementById('verifyBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');
            
            // Validate code
            if (code.length !== 6) {
                showNotification('Please enter a 6-digit code', 'error');
                return;
            }
            
            btn.disabled = true;
            btnText.textContent = 'Verifying...';
            spinner.style.display = 'inline-block';
            
            try {
                const response = await fetch(baseUrl + '/2fa/enable', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({code}),
                    credentials: 'include'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show recovery codes
                    const codes = data.recovery_codes.join('\n');
                    showAlert(
                        '2FA Enabled Successfully!',
                        '<strong>IMPORTANT:</strong> Save these recovery codes in a secure location:<br><br>' +
                        '<div style="font-family: monospace; background: #f3f4f6; padding: 1rem; border-radius: 4px; margin: 1rem 0;">' +
                        codes.replace(/\n/g, '<br>') +
                        '</div>' +
                        'You can use these codes to access your account if you lose your device.',
                        'success'
                    );
                    
                    window.location.href = baseUrl + '/user/profile';
                } else {
                    showNotification('Error: ' + (data.error || 'Failed to enable 2FA'), 'error');
                    btn.disabled = false;
                    btnText.textContent = 'Enable 2FA';
                    spinner.style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Failed to enable 2FA. Please try again.', 'error');
                btn.disabled = false;
                btnText.textContent = 'Enable 2FA';
                spinner.style.display = 'none';
            }
        });
    </script>
    <script src="<?php echo app_base_url('/assets/js/global-notifications.js'); ?>"></script>
</body>
</html>
