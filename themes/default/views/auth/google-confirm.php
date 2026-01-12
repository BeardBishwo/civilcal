<?php

/**
 * Interactive Google Registration Confirmation
 * Premium GitHub-style username selection
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile - <?= \App\Services\SettingsService::get('site_title', 'Bishwo Calculator') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--bg);
            background-image:
                radial-gradient(at 0% 0%, hsla(253, 16%, 7%, 1) 0, transparent 50%),
                radial-gradient(at 50% 0%, hsla(225, 39%, 30%, 1) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(339, 49%, 30%, 1) 0, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .avatar-wrap {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
        }

        .avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            padding: 3px;
            background: var(--bg);
        }

        .google-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-muted);
        }

        .input-wrap {
            position: relative;
        }

        input {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
            color: white;
            font-size: 16px;
            transition: all 0.3s;
            outline: none;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        .status-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            display: none;
        }

        .suggestions {
            margin-top: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
        }

        .chip:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .validation-msg {
            font-size: 12px;
            margin-top: 6px;
            display: none;
        }

        .val-error {
            color: #f87171;
        }

        .val-success {
            color: #4ade80;
        }

        .loader {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <div class="avatar-wrap">
                <img src="<?= $picture ?? 'https://www.gravatar.com/avatar/' . md5($email) . '?d=mp' ?>" class="avatar" alt="Profile">
                <div class="google-icon"><i class="fab fa-google" style="color: #4285F4;"></i></div>
            </div>
            <h1>Welcome, <?= htmlspecialchars($first_name) ?>!</h1>
            <p class="subtitle">Complete your registration by choosing a unique username.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-box">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= app_base_url('/user/login/google/confirm') ?>" method="POST" id="confirmForm">
            <div class="form-group">
                <label for="username">Pick a username</label>
                <div class="input-wrap">
                    <input type="text" id="username" name="username" placeholder="e.g. johndoe"
                        value="<?= htmlspecialchars($suggestions[0] ?? '') ?>" autocomplete="off" required>
                    <div class="status-icon" id="statusIcon"></div>
                    <div class="loader" id="checkLoader" style="position: absolute; right: 16px; top: 18px;"></div>
                </div>

                <div id="validation-msg" class="validation-msg"></div>

                <div class="suggestions">
                    <span style="font-size: 12px; width: 100%; color: var(--text-muted); margin-bottom: 4px;">Suggestions:</span>
                    <?php foreach ($suggestions as $sugg): ?>
                        <div class="chip" onclick="setUsername('<?= htmlspecialchars($sugg) ?>')"><?= htmlspecialchars($sugg) ?></div>
                        <input type="hidden" name="cached_suggestions[]" value="<?= htmlspecialchars($sugg) ?>">
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span>Complete Registration</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>

    <script>
        const usernameInput = document.getElementById('username');
        const statusIcon = document.getElementById('statusIcon');
        const loader = document.getElementById('checkLoader');
        const msg = document.getElementById('validation-msg');
        const submitBtn = document.getElementById('submitBtn');
        let checkTimeout;

        function setUsername(val) {
            usernameInput.value = val;
            checkUsername(val);
        }

        usernameInput.addEventListener('input', (e) => {
            const val = e.target.value;
            clearTimeout(checkTimeout);

            if (val.length < 3) {
                hideStatus();
                return;
            }

            loader.style.display = 'block';
            statusIcon.style.display = 'none';

            checkTimeout = setTimeout(() => {
                checkUsername(val);
            }, 500);
        });

        async function checkUsername(username) {
            try {
                // Updated to use POST and new endpoint as per Phase 27 spec
                const formData = new FormData();
                formData.append('username', username);

                const response = await fetch('<?= app_base_url('/api/check-username') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                loader.style.display = 'none';
                statusIcon.style.display = 'block';
                msg.style.display = 'block';

                if (data.available) {
                    statusIcon.innerHTML = '<i class="fas fa-check-circle" style="color: #4ade80;"></i>';
                    msg.innerHTML = '<i class="fas fa-check"></i> ' + data.message;
                    msg.className = 'validation-msg val-success';
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                } else {
                    statusIcon.innerHTML = '<i class="fas fa-times-circle" style="color: #f87171;"></i>';
                    msg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + data.message;
                    msg.className = 'validation-msg val-error';
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.7';
                }
            } catch (e) {
                console.error(e);
                loader.style.display = 'none';
            }
        }

        function hideStatus() {
            statusIcon.style.display = 'none';
            loader.style.display = 'none';
            msg.style.display = 'none';
        }

        // Initial check
        if (usernameInput.value) {
            checkUsername(usernameInput.value);
        }
    </script>

</body>

</html>