/**
 * Profile Management JavaScript
 * Handles all profile-related AJAX operations and UI interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all profile functionality
    initializeProfileForm();
    initializeNotificationForm();
    initializePrivacyForm();
    initializePasswordForm();
    initializeDeleteAccountForm();
    initializeAvatarUpload();
    updateProfileCompletion();
});

/**
 * Initialize Profile Form
 */
function initializeProfileForm() {
    const profileForm = document.getElementById('profileForm');
    if (!profileForm) return;

    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveProfile();
    });
}

/**
 * Save Profile Information
 */
function saveProfile() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);

    // Add loading state
    const saveBtn = document.getElementById('saveProfileBtn');
    const loading = document.getElementById('profileLoading');
    toggleLoading(saveBtn, loading, true);

    // Collect social links
    const socialLinks = {
        linkedin: formData.get('linkedin_url') || '',
        twitter: formData.get('twitter_url') || '',
        github: formData.get('github_url') || '',
        facebook: formData.get('facebook_url') || ''
    };
    formData.set('social_links', JSON.stringify(socialLinks));

    fetch('/profile/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            toggleLoading(saveBtn, loading, false);

            if (data.success) {
                showAlert('Profile updated successfully!', 'success');
                updateProfileCompletion(data.profile_completion);

                // Update page info
                updatePageInfo();
            } else {
                showAlert(data.error || 'Failed to update profile', 'error');
            }
        })
        .catch(error => {
            console.error('Profile update error:', error);
            toggleLoading(saveBtn, loading, false);
            showAlert('An error occurred while updating profile', 'error');
        });
}

/**
 * Initialize Notification Form
 */
function initializeNotificationForm() {
    const notificationForm = document.getElementById('notificationsForm');
    if (!notificationForm) return;

    notificationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveNotificationPreferences();
    });
}

/**
 * Save Notification Preferences
 */
function saveNotificationPreferences() {
    const form = document.getElementById('notificationsForm');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('saveNotificationsBtn');
    const loading = document.getElementById('notificationsLoading');

    toggleLoading(saveBtn, loading, true);

    fetch('/profile/notifications', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            toggleLoading(saveBtn, loading, false);

            if (data.success) {
                showAlert('Notification preferences updated!', 'success');
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('notificationsModal'));
                modal.hide();
            } else {
                showAlert(data.error || 'Failed to update preferences', 'error');
            }
        })
        .catch(error => {
            console.error('Notification update error:', error);
            toggleLoading(saveBtn, loading, false);
            showAlert('An error occurred while updating preferences', 'error');
        });
}

/**
 * Initialize Privacy Form
 */
function initializePrivacyForm() {
    const privacyForm = document.getElementById('privacyForm');
    if (!privacyForm) return;

    privacyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        savePrivacySettings();
    });
}

/**
 * Save Privacy Settings
 */
function savePrivacySettings() {
    const form = document.getElementById('privacyForm');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('savePrivacyBtn');
    const loading = document.getElementById('privacyLoading');

    toggleLoading(saveBtn, loading, true);

    fetch('/profile/privacy', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            toggleLoading(saveBtn, loading, false);

            if (data.success) {
                showAlert('Privacy settings updated!', 'success');
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('privacyModal'));
                modal.hide();
            } else {
                showAlert(data.error || 'Failed to update privacy settings', 'error');
            }
        })
        .catch(error => {
            console.error('Privacy update error:', error);
            toggleLoading(saveBtn, loading, false);
            showAlert('An error occurred while updating privacy settings', 'error');
        });
}

/**
 * Initialize Password Form
 */
function initializePasswordForm() {
    const passwordForm = document.getElementById('passwordForm');
    if (!passwordForm) return;

    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        changePassword();
    });

    // Real-time password validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');

    if (newPassword && confirmPassword) {
        [newPassword, confirmPassword].forEach(field => {
            field.addEventListener('input', validatePasswords);
        });
    }
}

/**
 * Change Password
 */
function changePassword() {
    const form = document.getElementById('passwordForm');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('changePasswordBtn');
    const loading = document.getElementById('passwordLoading');

    // Client-side validation
    if (!validatePasswords()) {
        return;
    }

    toggleLoading(saveBtn, loading, true);

    fetch('/profile/password', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            toggleLoading(saveBtn, loading, false);

            if (data.success) {
                showAlert('Password changed successfully!', 'success');
                // Close modal and reset form
                const modal = bootstrap.Modal.getInstance(document.getElementById('passwordModal'));
                modal.hide();
                form.reset();
            } else {
                showAlert(data.error || 'Failed to change password', 'error');
            }
        })
        .catch(error => {
            console.error('Password change error:', error);
            toggleLoading(saveBtn, loading, false);
            showAlert('An error occurred while changing password', 'error');
        });
}

/**
 * Initialize Delete Account Form
 */
function initializeDeleteAccountForm() {
    const deleteForm = document.getElementById('deleteAccountForm');
    if (!deleteForm) return;

    deleteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        deleteAccount();
    });
}

/**
 * Delete Account
 */
function deleteAccount() {
    const form = document.getElementById('deleteAccountForm');
    const formData = new FormData(form);
    const deleteBtn = document.getElementById('deleteAccountBtn');
    const loading = document.getElementById('deleteLoading');

    // Confirmation check
    const confirmText = formData.get('confirm_delete');
    if (confirmText !== 'DELETE') {
        showAlert('Please type "DELETE" to confirm account deletion', 'error');
        return;
    }

    if (!confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
        return;
    }

    toggleLoading(deleteBtn, loading, true);

    fetch('/profile/delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Account deleted successfully. Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || '/';
                }, 2000);
            } else {
                toggleLoading(deleteBtn, loading, false);
                showAlert(data.error || 'Failed to delete account', 'error');
            }
        })
        .catch(error => {
            console.error('Account deletion error:', error);
            toggleLoading(deleteBtn, loading, false);
            showAlert('An error occurred while deleting account', 'error');
        });
}

/**
 * Initialize Avatar Upload
 */
function initializeAvatarUpload() {
    const avatarInput = document.getElementById('avatarInput');
    if (!avatarInput) return;

    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleAvatarUpload(e.target);
        }
    });
}

/**
 * Handle Avatar Upload
 */
function handleAvatarUpload(input) {
    const file = input.files[0];
    if (!file) return;

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        showAlert('Invalid file type. Only JPEG, PNG, and GIF are allowed.', 'error');
        input.value = '';
        return;
    }

    // Validate file size (2MB)
    const maxSize = 2 * 1024 * 1024;
    if (file.size > maxSize) {
        showAlert('File size too large. Maximum 2MB allowed.', 'error');
        input.value = '';
        return;
    }

    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatarPreview');
        if (preview) {
            preview.src = e.target.result;
        }
    };
    reader.readAsDataURL(file);

    // Auto-save avatar
    const form = document.getElementById('profileForm');
    const formData = new FormData();
    formData.append('avatar', file);

    const saveBtn = document.getElementById('saveProfileBtn');
    const loading = document.getElementById('profileLoading');
    toggleLoading(saveBtn, loading, true);

    fetch('/profile/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            toggleLoading(saveBtn, loading, false);

            if (data.success) {
                showAlert('Avatar updated successfully!', 'success');
                updateProfileCompletion(data.profile_completion);
            } else {
                showAlert(data.error || 'Failed to update avatar', 'error');
                // Revert preview
                const avatarUploadArea = document.getElementById('avatarUploadArea');
                if (avatarUploadArea) {
                    const defaultAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(document.querySelector('.avatar-preview')?.alt || 'User')}&size=120&background=0d6efd&color=fff`;
                    const preview = document.getElementById('avatarPreview');
                    if (preview) {
                        preview.src = defaultAvatar;
                    }
                }
            }
        })
        .catch(error => {
            console.error('Avatar upload error:', error);
            toggleLoading(saveBtn, loading, false);
            showAlert('An error occurred while uploading avatar', 'error');
        });
}

/**
 * Update Profile Completion
 */
function updateProfileCompletion(percentage = null) {
    if (percentage !== null) {
        const circle = document.getElementById('completionCircle');
        const text = document.getElementById('completionPercent');

        if (circle && text) {
            const radius = 50;
            const circumference = 2 * Math.PI * radius;
            const offset = (1 - percentage / 100) * circumference;

            circle.style.strokeDashoffset = offset;
            text.textContent = percentage + '%';
        }
    }
}

/**
 * Validate Password Fields
 */
function validatePasswords() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');

    if (!newPassword || !confirmPassword) return true;

    const newPass = newPassword.value;
    const confirmPass = confirmPassword.value;

    // Remove previous validation styling
    newPassword.classList.remove('is-invalid');
    confirmPassword.classList.remove('is-invalid');

    let isValid = true;

    // Check if passwords match
    if (newPass && confirmPass && newPass !== confirmPass) {
        confirmPassword.classList.add('is-invalid');
        isValid = false;
    }

    // Check minimum length
    if (newPass && newPass.length < 6) {
        newPassword.classList.add('is-invalid');
        isValid = false;
    }

    return isValid;
}

/**
 * Toggle Loading State
 */
function toggleLoading(button, spinner, show) {
    if (!button || !spinner) return;

    if (show) {
        button.disabled = true;
        spinner.style.display = 'inline-block';
        const icon = button.querySelector('i');
        if (icon) icon.style.display = 'none';
    } else {
        button.disabled = false;
        spinner.style.display = 'none';
        const icon = button.querySelector('i');
        if (icon) icon.style.display = 'inline-block';
    }
}

/**
 * Show Alert Message
 */
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.profile-alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show profile-alert`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insert at top of page
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

/**
 * Update Page Information
 */
function updatePageInfo() {
    // Update last updated timestamp
    const timestampElements = document.querySelectorAll('.text-muted');
    timestampElements.forEach(element => {
        if (element.textContent.includes('Last updated:')) {
            const now = new Date();
            element.innerHTML = `<i class="fas fa-calendar me-1"></i>Last updated: ${now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
        }
    });
}

// Drag and drop for avatar upload
document.addEventListener('dragover', function(e) {
    e.preventDefault();
    const avatarUploadArea = document.getElementById('avatarUploadArea');
    if (avatarUploadArea) {
        avatarUploadArea.style.borderColor = '#0d6efd';
        avatarUploadArea.style.backgroundColor = '#f8f9fa';
    }
});

document.addEventListener('dragleave', function(e) {
    e.preventDefault();
    const avatarUploadArea = document.getElementById('avatarUploadArea');
    if (avatarUploadArea) {
        avatarUploadArea.style.borderColor = '#dee2e6';
        avatarUploadArea.style.backgroundColor = '';
    }
});

document.addEventListener('drop', function(e) {
    e.preventDefault();
    const avatarUploadArea = document.getElementById('avatarUploadArea');
    if (avatarUploadArea) {
        avatarUploadArea.style.borderColor = '#dee2e6';
        avatarUploadArea.style.backgroundColor = '';
    }

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const input = document.getElementById('avatarInput');
        if (input) {
            input.files = files;
            handleAvatarUpload(input);
        }
    }
});

// Form validation on blur
document.addEventListener('blur', function(e) {
    if (e.target.matches('input[required], textarea[required], select[required]')) {
        if (!e.target.value.trim()) {
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-invalid');
        }
    }
}, true);

// Remove validation styling on focus
document.addEventListener('focus', function(e) {
    if (e.target.matches('.is-invalid')) {
        e.target.classList.remove('is-invalid');
    }
}, true);