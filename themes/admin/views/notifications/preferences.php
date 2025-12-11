<?php
$preferences = $preferences ?? [];
?>

<div class="admin-content">
    <div class="content-header">
        <h1>Notification Preferences</h1>
        <p>Manage how and when you receive notifications</p>
    </div>

    <div class="preferences-container">
        <!-- Email Notifications -->
        <div class="preference-card">
            <div class="card-header">
                <i class="fas fa-envelope"></i>
                <h3>Email Notifications</h3>
            </div>
            <div class="card-body">
                <div class="preference-item">
                    <div class="preference-info">
                        <label>Enable Email Notifications</label>
                        <p>Receive notifications via email</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="email_notifications" 
                               <?= ($preferences['email_notifications'] ?? 1) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="preference-item">
                    <div class="preference-info">
                        <label>Email Frequency</label>
                        <p>How often to send email notifications</p>
                    </div>
                    <select id="email_frequency" class="form-select">
                        <option value="instant" <?= ($preferences['email_frequency'] ?? 'instant') === 'instant' ? 'selected' : '' ?>>Instant</option>
                        <option value="hourly" <?= ($preferences['email_frequency'] ?? 'instant') === 'hourly' ? 'selected' : '' ?>>Hourly Digest</option>
                        <option value="daily" <?= ($preferences['email_frequency'] ?? 'instant') === 'daily' ? 'selected' : '' ?>>Daily Digest</option>
                        <option value="weekly" <?= ($preferences['email_frequency'] ?? 'instant') === 'weekly' ? 'selected' : '' ?>>Weekly Digest</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Push Notifications -->
        <div class="preference-card">
            <div class="card-header">
                <i class="fas fa-bell"></i>
                <h3>Push Notifications</h3>
            </div>
            <div class="card-body">
                <div class="preference-item">
                    <div class="preference-info">
                        <label>Enable Push Notifications</label>
                        <p>Receive browser push notifications</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="push_notifications" 
                               <?= ($preferences['push_notifications'] ?? 1) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Notification Types -->
        <div class="preference-card">
            <div class="card-header">
                <i class="fas fa-filter"></i>
                <h3>Notification Types</h3>
            </div>
            <div class="card-body">
                <div class="preference-item">
                    <div class="preference-info">
                        <label>System Notifications</label>
                        <p>Updates, maintenance, and system alerts</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="system_notifications" 
                               <?= ($preferences['system_notifications'] ?? 1) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="preference-item">
                    <div class="preference-info">
                        <label>User Action Notifications</label>
                        <p>Comments, mentions, and user interactions</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="user_action_notifications" 
                               <?= ($preferences['user_action_notifications'] ?? 1) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Quiet Hours -->
        <div class="preference-card">
            <div class="card-header">
                <i class="fas fa-moon"></i>
                <h3>Quiet Hours</h3>
            </div>
            <div class="card-body">
                <p class="card-description">Set hours when you don't want to receive notifications</p>
                
                <div class="time-range">
                    <div class="time-input">
                        <label>Start Time</label>
                        <input type="time" id="quiet_hours_start" 
                               value="<?= htmlspecialchars($preferences['quiet_hours_start'] ?? '') ?>">
                    </div>
                    <div class="time-input">
                        <label>End Time</label>
                        <input type="time" id="quiet_hours_end" 
                               value="<?= htmlspecialchars($preferences['quiet_hours_end'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="preferences-footer">
            <button id="savePreferences" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Preferences
            </button>
            <button id="resetPreferences" class="btn btn-outline-secondary">
                <i class="fas fa-undo"></i> Reset to Defaults
            </button>
        </div>
    </div>
</div>

<style>
.preferences-container {
    max-width: 800px;
    margin: 0 auto;
}

.preference-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-header i {
    font-size: 24px;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.card-body {
    padding: 24px;
}

.card-description {
    color: #6b7280;
    margin-bottom: 20px;
    font-size: 14px;
}

.preference-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid #e5e7eb;
}

.preference-item:last-child {
    border-bottom: none;
}

.preference-info label {
    font-weight: 500;
    color: #111827;
    display: block;
    margin-bottom: 4px;
}

.preference-info p {
    color: #6b7280;
    font-size: 14px;
    margin: 0;
}

/* Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: 0.3s;
    border-radius: 28px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

/* Form Select */
.form-select {
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    min-width: 200px;
    cursor: pointer;
}

/* Time Range */
.time-range {
    display: flex;
    gap: 20px;
}

.time-input {
    flex: 1;
}

.time-input label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
}

.time-input input[type="time"] {
    width: 100%;
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
}

/* Footer */
.preferences-footer {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 32px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-outline-secondary {
    background: white;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.btn-outline-secondary:hover {
    background: #f3f4f6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const saveBtn = document.getElementById('savePreferences');
    const resetBtn = document.getElementById('resetPreferences');

    saveBtn.addEventListener('click', async function() {
        const preferences = {
            email_notifications: document.getElementById('email_notifications').checked ? 1 : 0,
            email_frequency: document.getElementById('email_frequency').value,
            push_notifications: document.getElementById('push_notifications').checked ? 1 : 0,
            system_notifications: document.getElementById('system_notifications').checked ? 1 : 0,
            user_action_notifications: document.getElementById('user_action_notifications').checked ? 1 : 0,
            quiet_hours_start: document.getElementById('quiet_hours_start').value || null,
            quiet_hours_end: document.getElementById('quiet_hours_end').value || null
        };

        try {
            const response = await fetch('<?= app_base_url('/notifications/preferences/update') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(preferences)
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Preferences saved successfully', 'success');
            } else {
                showNotification(data.message || 'Failed to save preferences', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
        }
    });

    resetBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to reset to default preferences?')) {
            document.getElementById('email_notifications').checked = true;
            document.getElementById('email_frequency').value = 'instant';
            document.getElementById('push_notifications').checked = true;
            document.getElementById('system_notifications').checked = true;
            document.getElementById('user_action_notifications').checked = true;
            document.getElementById('quiet_hours_start').value = '';
            document.getElementById('quiet_hours_end').value = '';
        }
    });
});
</script>
