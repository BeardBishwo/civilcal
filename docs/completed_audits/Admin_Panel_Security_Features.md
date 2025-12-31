# Admin Panel Security Features: User Management, Security Alerts & IP Restrictions
https://windsurf.com/codemaps/661da41f-562c-4ef5-919b-fe30cc2311f4-27eaae4aec448b64
**Date:** 2025-12-30  
**Purpose:** Deep-dive implementation guide for the admin security subsystem.  
**Scope:** User CRUD with security controls, real-time threat detection, IP access enforcement, and audit trails.

---

## 1. Overview

The admin panel implements a defense-in-depth security architecture:

1. **User Management** – CRUD with self-protection, role enforcement, ban/unban, and audit logging.
2. **Security Alerts** – Real-time detection of impossible travel, rapid location changes, new devices, and high-risk countries.
3. **IP Restrictions** – Whitelist/blacklist enforcement with optional expiration and admin override.
4. **Session Tracking** – Full device/browser/geolocation audit trail for every login.

All flows are protected by CSRF tokens, admin-only gates, and comprehensive logging.

---

## 2. User Management System

### 2.1. Core Features

| Feature | Implementation | Security Controls |
|---------|----------------|-------------------|
| Create User | `UserManagementController::store()` | CSRF, email uniqueness, password hashing, credential email |
| Update User | `UserManagementController::update()` | CSRF, self-modification protection (role/status lock) |
| Delete User | `UserManagementController::delete()` | CSRF, self-deletion prevention |
| Ban/Unban | `UserManagementController::ban()`, `UserManagementController::unban()` | CSRF, self-ban prevention, reason logging |
| View Users | `UserManagementController::index()` | Admin auth, pagination, search/filter |

### 2.2. Self-Protection Logic

```php
// Prevent admin from modifying own role/status
if ($id == $_SESSION['user_id']) {
    $role = $currentUser['role']; // Preserve existing role
    $isActive = 1; // Always keep active
}
```

```php
// Prevent self-ban
if ($id == $_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You cannot ban yourself']);
    return;
}
```

### 2.3. Ban/Unban Flow

1. **Ban**
   - CSRF validation
   - Self-ban check
   - `UPDATE users SET is_banned=1, ban_reason=?, banned_at=NOW()`
   - Deactivate account (`is_active=0`)
   - Return JSON response

2. **Unban**
   - CSRF validation
   - `UPDATE users SET is_banned=0, ban_reason=NULL, banned_at=NULL`
   - Reactivate account (`is_active=1`)

### 2.4. Audit & Logging

- All admin actions are logged via `error_log()`.
- Ban/unban includes reason and timestamp.
- Login sessions are tracked in `login_sessions` table.

---

## 3. Security Alerts System

### 3.1. Threat Detection Engine

`SuspiciousActivityDetector::analyzeLogin()` runs on every successful login and checks:

| Threat | Logic | Threshold |
|--------|-------|-----------|
| Impossible Travel | Cross-country logins within < 24h | Configurable hours |
| Rapid Location Changes | ≥3 distinct cities in 24h | Fixed |
| New Device + New Location | First-time device from unknown location | Always |
| High-Risk Country | Login from configured high-risk country | Config list |

### 3.2. Alert Creation

```php
if (!empty($alerts)) {
    foreach ($alerts as $alert) {
        $this->securityAlertService->createAlert(
            $userId,
            $alert['type'],
            $alert['risk_level'],
            $alert['details']
        );
    }
}
```

### 3.3. Admin Alert Dashboard

- **View**: `SecurityAlertsController::index()`
- **Filters**: All / Unresolved / Risk level
- **Statistics**: Total, unresolved, high-risk counts
- **Resolution**: AJAX `SecurityAlertsController::resolve()` marks alert as resolved with admin ID and timestamp.

### 3.4. New Location Email Alerts

`SecurityAlertService::checkNewLocation()`:

1. Query `user_login_locations` for known locations.
2. If new location detected:
   - Record location (`INSERT user_login_locations`).
   - Check user preference and global setting.
   - Send HTML email via `EmailManager`.

---

## 4. IP Restrictions System

### 4.1. Enforcement Flow

1. **Global Toggle**: `SettingsService::get('enable_ip_restrictions')`
2. **Whitelist Priority**: If whitelist exists, IP must be in it.
3. **Blacklist Check**: Block if IP is blacklisted.
4. **Result**: Return `['allowed' => bool, 'reason' => string|null]`

### 4.2. Admin Management

- **Add**: `IPRestrictionsController::store()` – validates IP, type, reason, optional expiration.
- **List**: `IPRestrictionsController::index()` – paginated, filter by type.
- **Delete**: `IPRestrictionsController::destroy()` – remove restriction.

### 4.3. Database Schema

```sql
CREATE TABLE ip_restrictions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    restriction_type ENUM('whitelist', 'blacklist') NOT NULL,
    reason TEXT,
    expires_at DATETIME NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 5. Login Session Tracking

### 5.1. Data Captured

- `user_id`, `ip_address`, `user_agent`
- `device_type`, `browser`, `os`
- `country`, `region`, `city`, `timezone`
- `login_at`

### 5.2. Implementation

`User::logLoginSession()`:

1. Auto-create `login_sessions` table if missing.
2. Resolve geolocation via `GeolocationService` (MaxMind GeoLite2).
3. Detect device type and browser.
4. Insert full session record.

### 5.3. Admin Logs View

`UserManagementController::loginLogs()`:
- Paginated list with user details.
- Search/filter by IP, user, date range.

---

## 6. Security Controls Checklist

| Control | Implemented? | Notes |
|---------|---------------|-------|
| CSRF on all admin actions | ✅ | `Security::validateCsrfToken()` |
| Admin-only gates | ✅ | `$this->auth->isAdmin()` |
| Self-protection (ban/delete/role) | ✅ | Explicit checks |
| Input validation | ✅ | Email uniqueness, IP format |
| Rate limiting on admin actions | ⚠️ | **TODO** – add to ban/unban endpoints |
| Audit logging | ✅ | `error_log()`, DB timestamps |
| Session/device/geolocation tracking | ✅ | `login_sessions` table |
| Real-time threat detection | ✅ | `SuspiciousActivityDetector` |
| IP whitelist/blacklist | ✅ | `IPRestrictionService` |
| Alert resolution workflow | ✅ | Admin dashboard with AJAX |
| Email alerts for new locations | ✅ | `SecurityAlertService` |

---

## 7. Implementation Files

| File | Purpose |
|------|---------|
| `app/Controllers/Admin/UserManagementController.php` | CRUD, ban/unban, login logs |
| `app/Controllers/Admin/SecurityAlertsController.php` | Alert dashboard, resolution |
| `app/Controllers/Admin/IPRestrictionsController.php` | IP restriction CRUD |
| `app/Services/SuspiciousActivityDetector.php` | Threat detection engine |
| `app/Services/SecurityAlertService.php` | Alert creation and email notifications |
| `app/Services/IPRestrictionService.php` | IP enforcement logic |
| `app/Services/GeolocationService.php` | MaxMind GeoLite2 lookup |
| `app/Models/User.php` | Session logging (`logLoginSession`) |
| `app/Core/Security.php` | CSRF helpers |
| Database tables: `users`, `security_alerts`, `ip_restrictions`, `login_sessions`, `user_login_locations` |

---

## 8. Production Deployment Checklist

- [ ] Ensure MaxMind GeoLite2 database is present and auto-updated via cron.
- [ ] Set `enable_ip_restrictions` and `location_alert_sensitivity` in settings.
- [ ] Configure SMTP for alert emails.
- [ ] Assign at least one admin user.
- [ ] Test: create user, ban/unban, IP restriction, alert resolution.
- [ ] Verify login session tracking after a few logins.
- [ ] Add rate limiting to admin endpoints (recommended).

---

## 9. Optional Enhancements

- **Bulk admin actions** (ban multiple users) with audit log.
- **Threat intelligence feed** integration for high-risk IPs.
- **Periodic cleanup** of old `login_sessions` (e.g., > 1 year).
- **MFA** for admin sessions.
- **Webhook alerts** for critical threats.

---

**Conclusion:** This subsystem provides enterprise-grade admin security with real-time threat detection, strict access controls, and full auditability. It is ready for production deployment with the checklist items above addressed.
