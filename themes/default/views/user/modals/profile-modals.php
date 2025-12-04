<?php
// Get current notification preferences and privacy settings
$notificationPrefs = $notification_preferences ?? [];
$currentPrivacy = $user['calculation_privacy'] ?? 'private';
?>

<!-- Notifications Modal -->
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationsModalLabel">
                    <i class="fas fa-bell me-2"></i>
                    Notification Preferences
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="notificationsForm">
                <div class="modal-body">
                    <!-- Email Notifications Toggle -->
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                                   <?php echo ($user['email_notifications'] ?? true) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="email_notifications">
                                <strong>Email Notifications</strong>
                                <br><small class="text-muted">Receive notifications via email</small>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Notification Types -->
                    <h6 class="mb-3">Notification Types</h6>
                    
                    <div class="preference-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notification_calculation_results" 
                                   name="notification_preferences[calculation_results]" 
                                   <?php echo ($notificationPrefs['calculation_results'] ?? true) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notification_calculation_results">
                                <strong>Calculation Results</strong>
                                <br><small class="text-muted">Get notified when calculations are completed or when there are results to review</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="preference-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notification_system_updates" 
                                   name="notification_preferences[system_updates]" 
                                   <?php echo ($notificationPrefs['system_updates'] ?? true) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notification_system_updates">
                                <strong>System Updates</strong>
                                <br><small class="text-muted">Important system updates, maintenance notifications, and new features</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="preference-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notification_security_alerts" 
                                   name="notification_preferences[security_alerts]" 
                                   <?php echo ($notificationPrefs['security_alerts'] ?? true) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notification_security_alerts">
                                <strong>Security Alerts</strong>
                                <br><small class="text-muted">Important security notifications, login attempts, and account activity</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="preference-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notification_marketing" 
                                   name="notification_preferences[marketing]" 
                                   <?php echo ($notificationPrefs['marketing'] ?? false) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notification_marketing">
                                <strong>Marketing Communications</strong>
                                <br><small class="text-muted">Promotional content, newsletters, and product updates (optional)</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveNotificationsBtn">
                        <span class="loading-spinner" id="notificationsLoading"></span>
                        <i class="fas fa-save me-2"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">
                    <i class="fas fa-shield-alt me-2"></i>
                    Privacy Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="privacyForm">
                <div class="modal-body">
                    <p class="text-muted mb-4">Control who can see your calculations and profile information.</p>
                    
                    <h6 class="mb-3">Calculation Privacy</h6>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="calculation_privacy" id="privacy_private" value="private" 
                                   <?php echo $currentPrivacy === 'private' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="privacy_private">
                                <strong>Private</strong>
                                <br><small class="text-muted">Only you can see your calculations</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="calculation_privacy" id="privacy_team" value="team" 
                                   <?php echo $currentPrivacy === 'team' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="privacy_team">
                                <strong>Team</strong>
                                <br><small class="text-muted">Visible to team members and collaborators</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="calculation_privacy" id="privacy_public" value="public" 
                                   <?php echo $currentPrivacy === 'public' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="privacy_public">
                                <strong>Public</strong>
                                <br><small class="text-muted">Your calculations are publicly visible (without personal information)</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Your profile information (name, company, etc.) is always private unless you choose to make it public.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="savePrivacyBtn">
                        <span class="loading-spinner" id="privacyLoading"></span>
                        <i class="fas fa-save me-2"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">
                    <i class="fas fa-key me-2"></i>
                    Change Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="passwordForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye" id="current_password_icon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                <i class="fas fa-eye" id="new_password_icon"></i>
                            </button>
                        </div>
                        <div class="form-text">Password must be at least 6 characters long</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye" id="confirm_password_icon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> You'll be logged out of all devices after changing your password.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="changePasswordBtn">
                        <span class="loading-spinner" id="passwordLoading"></span>
                        <i class="fas fa-key me-2"></i>Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Delete Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteAccountForm">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    
                    <p class="mb-3">This will permanently delete your account and all associated data including:</p>
                    <ul class="mb-3">
                        <li>All your calculations and history</li>
                        <li>Your profile information</li>
                        <li>Your favorites and bookmarks</li>
                        <li>All export templates you created</li>
                    </ul>
                    
                    <p class="text-muted mb-3">To confirm account deletion, please enter your password and type <strong>DELETE</strong> in the field below:</p>
                    
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="delete_password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('delete_password')">
                                <i class="fas fa-eye" id="delete_password_icon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_delete" class="form-label">Type "DELETE" to confirm</label>
                        <input type="text" class="form-control" id="confirm_delete" name="confirm_delete" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="deleteAccountBtn">
                        <span class="loading-spinner" id="deleteLoading"></span>
                        <i class="fas fa-trash-alt me-2"></i>Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Password visibility toggle function
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>

<style>
/* Modal specific styles */
.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}

.preference-item:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.input-group .btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}
</style>
