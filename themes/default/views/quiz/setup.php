<style>
    .setup-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        position: relative;
        overflow: hidden;
    }

    .setup-container::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 40px 40px;
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translate(0, 0);
        }

        50% {
            transform: translate(20px, 20px);
        }
    }

    .setup-card {
        background: rgba(20, 20, 40, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 3rem;
        padding: 3rem 2rem;
        max-width: 500px;
        width: 100%;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        z-index: 1;
        animation: slideIn 0.6s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .setup-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .setup-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .setup-header p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1rem;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 1.75rem;
    }

    .form-label {
        display: block;
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-select {
        width: 100%;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        border: 2px solid rgba(102, 126, 234, 0.3);
        color: white;
        padding: 1rem;
        border-radius: 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1.25rem;
        padding-right: 2.5rem;
    }

    .form-select:hover {
        border-color: rgba(102, 126, 234, 0.6);
        background-color: rgba(102, 126, 234, 0.15);
    }

    .form-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-select option {
        background: #1a1a2e;
        color: white;
        padding: 0.5rem;
    }

    .submit-btn {
        width: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 1.1rem;
        font-weight: 700;
        padding: 1.25rem;
        border: none;
        border-radius: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        margin-top: 1rem;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .info-box {
        background: rgba(102, 126, 234, 0.1);
        border-left: 4px solid #667eea;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        align-items: center;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .step-number {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .step-label {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.6);
        text-align: center;
    }

    .step-connector {
        flex: 1;
        height: 2px;
        background: rgba(102, 126, 234, 0.2);
        margin: 0 0.5rem;
    }
</style>

<div class="setup-container">
    <div class="setup-card">
        <div class="setup-header">
            <h1>🎓 Welcome, Engineer!</h1>
            <p>Select your learning path and education level to begin your personalized journey.</p>
        </div>

        <div class="step-indicator">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-label">Course</div>
            </div>
            <div class="step-connector"></div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-label">Level</div>
            </div>
            <div class="step-connector"></div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Start</div>
            </div>
        </div>

        <div class="info-box">
            💡 Pro Tip: Choose your course and education level carefully. You can change these settings later in your profile.
        </div>

        <form id="setupForm" action="<?= app_base_url('/quiz/setup/save') ?>" method="POST" onsubmit="return setupForm.validate(event)">
            <?= $this->csrfField() ?>

            <div class="form-group">
                <label for="course_id" class="form-label">📚 Select Your Course</label>
                <select id="course_id" name="course_id" class="form-select" required>
                    <option value="">-- Choose a Course --</option>
                    <?php if (isset($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['title']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="edu_level_id" class="form-label">🎖️ Select Education Level</label>
                <select id="edu_level_id" name="edu_level_id" class="form-select" required>
                    <option value="">-- Choose a Level --</option>
                    <?php if (isset($edu_levels)): ?>
                        <?php foreach ($edu_levels as $level): ?>
                            <option value="<?= $level['id'] ?>"><?= htmlspecialchars($level['title']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                Start Learning 🚀
            </button>
        </form>
    </div>
</div>

<script>
    const setupForm = {
        validate(event) {
            const courseId = document.getElementById('course_id').value;
            const eduLevelId = document.getElementById('edu_level_id').value;

            if (!courseId || !eduLevelId) {
                event.preventDefault();
                alert('Please select both course and education level.');
                return false;
            }

            // Show loading state
            const btn = event.target.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Processing... ⏳';
            }

            return true;
        },

        handleError(response) {
            try {
                const data = JSON.parse(response);
                if (data.error) {
                    alert('Error: ' + data.error);
                }
            } catch (e) {
                alert('An error occurred. Response: ' + response);
            }
        }
    };

    // Prevent form submission errors
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.onsubmit = function(event) {
                if (!setupForm.validate(event)) {
                    return false;
                }
            };
        }
    });
</script>