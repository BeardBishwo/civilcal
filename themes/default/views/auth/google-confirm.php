<?php

/**
 * Phase 27: Enhanced Google Registration Confirmation
 * Premium Glassmorphism UI for Username Selection
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile - <?= \App\Services\SettingsService::get('site_title', 'Bishwo Calculator') ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php if (isset($view) && method_exists($view, 'csrfMetaTag')): ?>
        <?= $view->csrfMetaTag() ?>
    <?php else: ?>
        <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <?php endif; ?>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            background-image:
                radial-gradient(at 0% 0%, hsla(253, 16%, 7%, 1) 0, transparent 50%),
                radial-gradient(at 50% 0%, hsla(225, 39%, 30%, 1) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(339, 49%, 30%, 1) 0, transparent 50%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .avatar-glow {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
            animation: pulse-glow 2s infinite;
        }

        @keyframes pulse-glow {
            0% {
                box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(99, 102, 241, 0.6);
            }

            100% {
                box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
            }
        }

        .chip-gradient {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.1));
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .chip-gradient:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.2));
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.3);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body class="flex items-center justify-center p-4">

    <div class="max-w-md w-full glass-card rounded-3xl p-8 space-y-8 relative overflow-hidden">
        <!-- Decorative Background Circle -->
        <div class="absolute -top-12 -right-12 w-24 h-24 bg-indigo-500/10 rounded-full blur-3xl"></div>

        <div class="text-center space-y-4">
            <div class="relative inline-block">
                <img src="<?= $picture ?? 'https://www.gravatar.com/avatar/' . md5($email) . '?d=mp' ?>"
                    alt="Google Avatar"
                    class="w-24 h-24 rounded-full border-4 border-indigo-500/30 p-1 avatar-glow">
                <div class="absolute bottom-1 right-1 bg-white rounded-full p-1.5 shadow-lg">
                    <img src="https://www.google.com/favicon.ico" class="w-4 h-4" alt="Google">
                </div>
            </div>

            <div class="space-y-1">
                <h1 class="text-3xl font-bold text-white tracking-tight">
                    Welcome, <span class="text-indigo-400"><?= htmlspecialchars($first_name) ?></span>!
                </h1>
                <p class="text-slate-400 text-sm">Choose your unique username to complete your profile.</p>
            </div>
        </div>

        <form action="<?= app_base_url('/user/login/google/confirm') ?>" method="POST" id="registrationForm" class="space-y-6">
            <?php if (isset($view) && method_exists($view, 'csrfField')): ?>
                <?= $view->csrfField() ?>
            <?php else: ?>
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <?php endif; ?>

            <div class="space-y-4">
                <div class="relative">
                    <label for="username" class="text-xs font-semibold text-slate-500 uppercase tracking-wider ml-1 mb-2 block">Username</label>
                    <input type="text"
                        id="username"
                        name="username"
                        placeholder="johndoe123"
                        class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3.5 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-lg"
                        value="<?= htmlspecialchars($suggestions[0] ?? '') ?>"
                        autocomplete="off"
                        required>

                    <div id="validation-spinner" class="hidden absolute right-4 top-[3.2rem]">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Feedback Area -->
                <div id="feedback-area" class="min-h-[20px] ml-1">
                    <p id="feedback-text" class="text-xs transition-all"></p>
                </div>

                <!-- Suggestion Chips -->
                <div class="space-y-2">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Suggestions</span>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($suggestions as $sugg): ?>
                            <button type="button"
                                onclick="selectSuggestion('<?= htmlspecialchars($sugg) ?>')"
                                class="chip-gradient px-4 py-2 rounded-full text-sm text-indigo-300 hover:text-white transition-all">
                                @<?= htmlspecialchars($sugg) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <button type="submit"
                id="submit-btn"
                disabled
                class="w-full bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2 group">
                <span>Complete Registration</span>
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </button>
        </form>

        <p class="text-center text-slate-500 text-[10px] uppercase tracking-tighter">
            Instant Profile Setup &bull; Secured by Civil Cal
        </p>
    </div>

    <script>
        const usernameInput = document.getElementById('username');
        const feedbackText = document.getElementById('feedback-text');
        const spinner = document.getElementById('validation-spinner');
        const submitBtn = document.getElementById('submit-btn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        let debounceTimer;

        function selectSuggestion(val) {
            usernameInput.value = val;
            validateUsername(val);
        }

        usernameInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            const val = e.target.value.trim();

            if (val.length < 3) {
                setFeedback('Username must be at least 3 characters', 'text-amber-400');
                submitBtn.disabled = true;
                return;
            }

            setFeedback('Checking availability...', 'text-slate-400');
            spinner.classList.remove('hidden');

            debounceTimer = setTimeout(() => {
                validateUsername(val);
            }, 500);
        });

        async function validateUsername(username) {
            try {
                spinner.classList.remove('hidden');

                const response = await fetch('<?= app_base_url('/api/check-username') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: `username=${encodeURIComponent(username)}&csrf_token=${encodeURIComponent(csrfToken || '')}`
                });

                const data = await response.json();

                spinner.classList.add('hidden');

                if (data.available) {
                    setFeedback('<i class="fas fa-check-circle mr-1"></i> Username available', 'text-emerald-400');
                    submitBtn.disabled = false;
                    usernameInput.classList.add('border-emerald-500/50');
                    usernameInput.classList.remove('border-rose-500/50');
                } else {
                    setFeedback('<i class="fas fa-times-circle mr-1"></i> ' + (data.message || 'Username already taken'), 'text-rose-400');
                    submitBtn.disabled = true;
                    usernameInput.classList.add('border-rose-500/50');
                    usernameInput.classList.remove('border-emerald-500/50');
                }
            } catch (err) {
                spinner.classList.add('hidden');
                console.error('Validation error:', err);
                setFeedback('Error checking availability. Please try again.', 'text-rose-400');
            }
        }

        function setFeedback(msg, colorClass) {
            feedbackText.innerHTML = msg;
            feedbackText.className = `text-xs transition-all ${colorClass}`;
        }

        // Initial validation for current input (pre-filled with first suggestion)
        if (usernameInput.value) {
            validateUsername(usernameInput.value);
        }
    </script>
</body>

</html>