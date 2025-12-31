# Security Services Implementation: Detection to Admin Panel Alerts

## Overview

This document provides an in-depth analysis of the complete security implementation, covering the flow from user login detection through suspicious activity analysis, event logging, honeypot traps, and admin alert management. The system demonstrates sophisticated security patterns including real-time threat detection, automated response mechanisms, and comprehensive admin monitoring capabilities.

## Core Security Architecture

### 1. Login Security Flow Pipeline

The primary security pipeline activates during user authentication, implementing multi-layered threat detection:

```
User Login → Password Verification → Geolocation → Security Analysis → Session Logging
```

**Key Components:**
- **AuthController**: Orchestrates security checks during login
- **GeolocationService**: IP-based location detection and analysis
- **SecurityAlertService**: New location detection and alerting
- **SuspiciousActivityDetector**: Pattern analysis and threat identification
- **SecurityMonitor**: Event logging and automated responses

### 2. Security Event Management System

Centralized security event handling with severity-based automated responses:

```
Event Trigger → Severity Assessment → Automated Response → Database Logging → Admin Notification
```

**Response Tiers:**
- **Critical**: Immediate IP bans and user flagging
- **High**: Enhanced monitoring and admin alerts
- **Medium**: Pattern tracking and rate limiting
- **Low**: Logging and analytics collection

## Detailed Security Flow Analysis

### Phase 1: User Authentication Security Pipeline

**Location:** `app/Controllers/AuthController.php:71-176`

#### 1.1 Password Verification Success Hook
```php
if (password_verify($password, $user->password)) {
    // Security pipeline triggered
}
```

#### 1.2 Geolocation Service Integration
**Location:** `AuthController.php:136-137`

```php
$geoService = new GeolocationService();
$locationData = $geoService->getLocationDetails();
```

**Geolocation Data Structure:**
```php
$locationData = [
    'ip' => '192.168.1.100',
    'country_code' => 'US',
    'country' => 'United States',
    'region' => 'California',
    'city' => 'San Francisco',
    'timezone' => 'America/Los_Angeles',
    'is_vpn' => false,
    'is_proxy' => false
];
```

#### 1.3 New Location Detection
**Location:** `AuthController.php:171-172`

```php
$securityAlertService = new SecurityAlertService();
$securityAlertService->checkNewLocation($user->id, $locationData);
```

**New Location Logic:**
- Query historical login locations for user
- Compare current location with known locations
- Send email alert for first-time locations
- Store location in user login history

#### 1.4 Suspicious Activity Analysis
**Location:** `AuthController.php:175-176`

```php
$suspiciousDetector = new SuspiciousActivityDetector();
$analysis = $suspiciousDetector->analyzeLogin($user->id, $locationData, $deviceInfo);
```

### Phase 2: Suspicious Activity Detection Engine

**Location:** `app/Services/SuspiciousActivityDetector.php`

#### 2.1 Multi-Algorithm Threat Detection

The detector implements four primary detection algorithms:

##### 2.1.1 Impossible Travel Detection
**Location:** `SuspiciousActivityDetector.php:46`

```php
$impossibleTravel = $this->checkImpossibleTravel($userId, $locationData);
```

**Algorithm Logic:**
```php
public function checkImpossibleTravel($userId, $locationData) {
    // Query login_sessions for previous locations within threshold
    $sql = "SELECT country_code, created_at FROM login_sessions 
            WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? HOUR)";
    
    // Calculate distance and travel time
    foreach ($previousLogins as $login) {
        $distance = $this->calculateDistance($login['country_code'], $locationData['country_code']);
        $timeDiff = $this->calculateTimeDifference($login['created_at'], $locationData['timestamp']);
        
        // Flag if travel speed > 800 km/h (commercial aircraft limit)
        if ($distance / $timeDiff > 800) {
            return [
                'type' => 'impossible_travel',
                'risk_level' => 'high',
                'description' => "Login from {$locationData['country']} impossible travel from {$login['country_code']}"
            ];
        }
    }
}
```

##### 2.1.2 Rapid Location Changes Detection
**Location:** `SuspiciousActivityDetector.php:53`

```php
$rapidChanges = $this->checkRapidLocationChanges($userId, $locationData);
```

**Algorithm Logic:**
```php
public function checkRapidLocationChanges($userId, $locationData) {
    // Query for distinct cities in last 24 hours
    $sql = "SELECT COUNT(DISTINCT city) as city_count FROM login_sessions 
            WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    
    // Flag if 3+ different cities within 24 hours
    if ($result['city_count'] >= 3) {
        return [
            'type' => 'rapid_location_changes',
            'risk_level' => 'medium',
            'description' => "Login from {$locationData['city']} - {$result['city_count']} cities in 24 hours"
        ];
    }
}
```

##### 2.1.3 New Device + New Location Detection
**Location:** `SuspiciousActivityDetector.php:60`

```php
$newDeviceLocation = $this->checkNewDeviceAndLocation($userId, $locationData, $deviceInfo);
```

**Algorithm Logic:**
```php
public function checkNewDeviceAndLocation($userId, $locationData, $deviceInfo) {
    // Check if device fingerprint seen before
    $deviceSeen = $this->hasUserSeenDevice($userId, $deviceInfo['fingerprint']);
    
    // Check if location seen before
    $locationSeen = $this->hasUserSeenLocation($userId, $locationData['city']);
    
    // Flag if both device and location are new
    if (!$deviceSeen && !$locationSeen) {
        return [
            'type' => 'new_device_new_location',
            'risk_level' => 'medium',
            'description' => "First login from new device in new location: {$locationData['city']}"
        ];
    }
}
```

##### 2.1.4 High-Risk Country Detection
**Location:** `SuspiciousActivityDetector.php:67`

```php
$highRiskCountry = $this->checkHighRiskCountry($locationData);
```

**Algorithm Logic:**
```php
public function checkHighRiskCountry($locationData) {
    // Get high-risk countries from settings
    $highRiskCountries = $this->getHighRiskCountries();
    
    if (in_array($locationData['country_code'], $highRiskCountries)) {
        return [
            'type' => 'high_risk_country',
            'risk_level' => 'high',
            'description' => "Login from high-risk country: {$locationData['country']}"
        ];
    }
}
```

#### 2.2 Alert Creation and Storage
**Location:** `SuspiciousActivityDetector.php:74-78`

```php
if (!empty($alerts)) {
    foreach ($alerts as $alert) {
        $this->securityAlertService->createAlert(
            $userId,
            $alert['type'],
            $alert['risk_level'],
            $alert['description'],
            array_merge($locationData, $deviceInfo)
        );
    }
}
```

**Alert Data Structure:**
```php
$alertData = [
    'user_id' => 123,
    'alert_type' => 'impossible_travel',
    'risk_level' => 'high',
    'description' => 'Login from US impossible travel from UK',
    'metadata' => json_encode([
        'ip' => '192.168.1.100',
        'country' => 'United States',
        'city' => 'New York',
        'device_fingerprint' => 'abc123',
        'user_agent' => 'Mozilla/5.0...'
    ])
];
```

### Phase 3: Security Event Logging System

**Location:** `app/Services/SecurityMonitor.php`

#### 3.1 Event Logging Pipeline
**Location:** `SecurityMonitor.php:11-18`

```php
public static function log($userId, $eventType, $endpoint, $details = [], $severity = 'medium') {
    $ip = SecurityValidator::getClientIp();
    
    $db = Database::getInstance();
    $db->query(
        "INSERT INTO security_logs (user_id, ip_address, event_type, endpoint, details, severity, created_at)
         VALUES (:uid, :ip, :event, :endpoint, :details, :severity, NOW())",
        [
            'uid' => $userId,
            'ip' => $ip,
            'event' => $eventType,
            'endpoint' => $endpoint,
            'details' => json_encode($details),
            'severity' => $severity
        ]
    );
}
```

#### 3.2 Critical Event Automated Response
**Location:** `SecurityMonitor.php:28-33`

```php
// Auto-ban on critical events
if ($severity === 'critical') {
    self::handleCriticalEvent($userId, $ip, $eventType);
}
```

**Critical Event Handler:**
```php
public static function handleCriticalEvent($userId, $ip, $eventType) {
    // Auto-ban for honeypot access
    if ($eventType === 'honeypot_accessed') {
        SecurityValidator::banIp($ip, 'Honeypot trap triggered', 86400 * 7);
    }
    
    // Flag user account for critical violations
    if ($userId) {
        $db = Database::getInstance();
        $db->query(
            "UPDATE users SET is_flagged = 1, flag_reason = :reason WHERE id = :uid",
            ['uid' => $userId, 'reason' => $eventType]
        );
    }
}
```

### Phase 4: Honeypot Trap System

**Location:** `app/Controllers/HoneypotController.php`

#### 4.1 Honeypot Endpoint Architecture

The system implements multiple fake endpoints designed to attract and trap malicious actors:

##### 4.1.1 Free Coins Honeypot
**Location:** `HoneypotController.php:12`

```php
public function freeCoins() {
    $ip = SecurityValidator::getClientIp();
    $userId = $_SESSION['user_id'] ?? null;
    
    // Log honeypot access (critical)
    SecurityMonitor::log($userId, 'honeypot_accessed', '/api/shop/free-coins', [
        'ip' => $ip,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ], 'critical');
    
    // Ban IP immediately
    SecurityValidator::banIp($ip, 'Accessed honeypot endpoint: free-coins', 86400 * 7);
    
    // Return fake success to not alert the bot
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Processing request...',
        'coins_added' => 10000
    ]);
}
```

##### 4.1.2 Grant Resources Honeypot (Permanent Ban)
**Location:** `HoneypotController.php:39`

```php
public function grantResources() {
    $ip = SecurityValidator::getClientIp();
    
    // Log and permanently ban
    SecurityMonitor::log(null, 'honeypot_accessed', '/api/admin/grant-resources', [
        'ip' => $ip
    ], 'critical');
    
    SecurityValidator::banIp($ip, 'Honeypot: Admin endpoint access', 0, true);
}
```

##### 4.1.3 Unlimited Coins Honeypot (30-Day Ban)
**Location:** `HoneypotController.php:66`

```php
public function unlimitedCoins() {
    $ip = SecurityValidator::getClientIp();
    
    SecurityMonitor::log(null, 'honeypot_accessed', '/api/shop/unlimited-coins', [
        'ip' => $ip
    ], 'critical');
    
    SecurityValidator::banIp($ip, 'Honeypot: Unlimited coins attempt', 86400 * 30);
}
```

#### 4.2 IP Ban Implementation
**Location:** `app/Services/SecurityValidator.php:136-140`

```php
public static function banIp($ip, $reason, $duration = 86400 * 7, $permanent = false) {
    $expiresAt = $permanent ? null : date('Y-m-d H:i:s', time() + $duration);
    
    $db = Database::getInstance();
    $db->query(
        "INSERT INTO banned_ips (ip_address, reason, expires_at, is_permanent, created_at)
         VALUES (:ip, :reason, :expires, :permanent, NOW())
         ON DUPLICATE KEY UPDATE reason = :reason, expires_at = :expires, is_permanent = :permanent",
        [
            'ip' => $ip,
            'reason' => $reason,
            'expires' => $expiresAt,
            'permanent' => $permanent ? 1 : 0
        ]
    );
}
```

### Phase 5: Admin Security Alerts Management

**Location:** `app/Controllers/Admin/SecurityAlertsController.php`

#### 5.1 Alert Display Pipeline

##### 5.1.1 Route Definition and Authentication
**Location:** `routes.php:1933`

```php
$router->add("GET", "/admin/security/alerts", "Admin\\SecurityAlertsController@index");
```

##### 5.1.2 Controller Implementation
**Location:** `SecurityAlertsController.php:22-44`

```php
public function index() {
    // Verify admin permission
    if (!$this->auth->isAdmin()) {
        $this->view->render('errors/403');
        return;
    }
    
    // Get filter parameters
    $filter = $_GET['filter'] ?? 'unresolved';
    $riskLevel = $_GET['risk'] ?? null;
    
    // Fetch alerts based on filter
    if ($filter === 'unresolved') {
        $alerts = $this->detector->getUnresolvedAlerts($riskLevel);
    } else {
        $alerts = $this->getAllAlerts($riskLevel);
    }
    
    // Get alert statistics
    $stats = $this->getAlertStatistics();
    
    // Render view
    $this->view->render('admin/security/alerts', [
        'page_title' => 'Security Alerts',
        'alerts' => $alerts,
        'stats' => $stats,
        'filter' => $filter,
        'riskLevel' => $riskLevel
    ]);
}
```

##### 5.1.3 Alert Query with User Information
**Location:** `SuspiciousActivityDetector.php:297-301`

```php
public function getUnresolvedAlerts($riskLevel = null) {
    $sql = "
        SELECT sa.*, u.username, u.email
        FROM security_alerts sa
        LEFT JOIN users u ON sa.user_id = u.id
        WHERE sa.is_resolved = 0";
    
    if ($riskLevel) {
        $sql .= " AND sa.risk_level = :risk_level";
    }
    
    $sql .= " ORDER BY sa.created_at DESC";
    
    return $this->db->query($sql, ['risk_level' => $riskLevel])->fetchAll();
}
```

##### 5.1.4 Alert Statistics Calculation
**Location:** `SecurityAlertsController.php:117-140`

```php
public function getAlertStatistics() {
    $db = Database::getInstance();
    
    $stats = [];
    
    // Total alerts
    $stats['total'] = $db->query("SELECT COUNT(*) as count FROM security_alerts")->fetch()['count'];
    
    // Unresolved alerts
    $stats['unresolved'] = $db->query("SELECT COUNT(*) as count FROM security_alerts WHERE is_resolved = 0")->fetch()['count'];
    
    // High-risk alerts
    $stats['high_risk'] = $db->query("SELECT COUNT(*) as count FROM security_alerts WHERE risk_level = 'high' AND is_resolved = 0")->fetch()['count'];
    
    // Breakdown by alert type
    $stats['by_type'] = $db->query("
        SELECT alert_type, COUNT(*) as count 
        FROM security_alerts 
        WHERE is_resolved = 0 
        GROUP BY alert_type
    ")->fetchAll();
    
    return $stats;
}
```

#### 5.2 Alert Resolution System

##### 5.2.1 AJAX Resolution Request
**Location:** `themes/admin/views/security/alerts.php:324-339`

```javascript
function resolveAlert(alertId) {
    if (!confirm('Mark this alert as resolved?')) return;
    
    const formData = new FormData();
    formData.append('alert_id', alertId);
    formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
    
    fetch('<?= app_base_url('/admin/security/alerts/resolve') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Alert resolved successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to resolve alert', 'error');
        }
    });
}
```

##### 5.2.2 Server-Side Resolution Handler
**Location:** `SecurityAlertsController.php:54-82`

```php
public function resolve() {
    header('Content-Type: application/json');
    
    // Verify admin permission
    if (!$this->auth->isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }
    
    $alertId = $_POST['alert_id'] ?? null;
    $userId = $_SESSION['user_id'];
    
    if (!$alertId) {
        echo json_encode(['success' => false, 'message' => 'Alert ID required']);
        exit;
    }
    
    // Resolve alert
    $result = $this->detector->resolveAlert($alertId, $userId);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to resolve alert']);
    }
}
```

##### 5.2.3 Database Resolution Update
**Location:** `SuspiciousActivityDetector.php:328-332`

```php
public function resolveAlert($alertId, $resolvedBy) {
    $stmt = $this->db->getPdo()->prepare("
        UPDATE security_alerts
        SET is_resolved = 1, resolved_by = ?, resolved_at = NOW()
        WHERE id = ?
    ");
    
    return $stmt->execute([$resolvedBy, $alertId]);
}
```

### Phase 6: Admin UI Implementation

**Location:** `themes/admin/views/security/alerts.php`

#### 6.1 Alert Display Interface

##### 6.1.1 Statistics Dashboard
**Location:** `alerts.php:56`

```php
<div class="stats-strip">
    <div class="stat-card">
        <h3><?= $stats['total'] ?></h3>
        <p>Total Alerts</p>
    </div>
    <div class="stat-card urgent">
        <h3><?= $stats['unresolved'] ?></h3>
        <p>Unresolved</p>
    </div>
    <div class="stat-card high-risk">
        <h3><?= $stats['high_risk'] ?></h3>
        <p>High Risk</p>
    </div>
</div>
```

##### 6.1.2 Alert Cards Display
**Location:** `alerts.php:102-106`

```php
<?php foreach ($alerts as $alert): ?>
    <?php
    $metadata = json_decode($alert['metadata'], true);
    $icon = $alertTypeIcons[$alert['alert_type']] ?? 'fa-shield-alt';
    $riskColor = $riskColors[$alert['risk_level']] ?? 'blue';
    ?>
    
    <div class="alert-card <?= $riskColor ?>">
        <div class="alert-header">
            <i class="fas <?= $icon ?>"></i>
            <span class="alert-type"><?= ucfirst(str_replace('_', ' ', $alert['alert_type'])) ?></span>
            <span class="risk-badge <?= $alert['risk_level'] ?>"><?= strtoupper($alert['risk_level']) ?></span>
        </div>
        
        <div class="alert-content">
            <p class="alert-description"><?= htmlspecialchars($alert['description']) ?></p>
            
            <div class="alert-details">
                <div class="detail-item">
                    <strong>User:</strong> <?= htmlspecialchars($alert['username'] ?? 'Unknown') ?>
                </div>
                <div class="detail-item">
                    <strong>IP:</strong> <?= htmlspecialchars($metadata['ip'] ?? 'Unknown') ?>
                </div>
                <div class="detail-item">
                    <strong>Location:</strong> <?= htmlspecialchars(($metadata['city'] ?? '') . ', ' . ($metadata['country'] ?? '')) ?>
                </div>
                <div class="detail-item">
                    <strong>Time:</strong> <?= date('M j, Y H:i', strtotime($alert['created_at'])) ?>
                </div>
            </div>
        </div>
        
        <div class="alert-actions">
            <button class="btn btn-success" onclick="resolveAlert(<?= $alert['id'] ?>)">
                <i class="fas fa-check"></i> Mark as Resolved
            </button>
            <button class="btn btn-danger" onclick="blockUser(<?= $alert['user_id'] ?>)">
                <i class="fas fa-ban"></i> Block User
            </button>
        </div>
    </div>
<?php endforeach; ?>
```

### Phase 7: Economic Security Validation

**Location:** `app/Services/EconomicSecurityService.php`

#### 7.1 Transaction Security Pipeline

##### 7.1.1 Purchase Validation
**Location:** `EconomicSecurityService.php:22-69`

```php
public function validatePurchase($userId, $resource, $quantity = 1) {
    // Validate resource key format
    if (!preg_match('/^[a-z0-9_]+$/', $resource)) {
        SecurityMonitor::log($userId, 'invalid_purchase_resource_key', $_SERVER['REQUEST_URI'], [
            'resource' => $resource,
        ], 'high');
        
        return [
            'success' => false,
            'message' => 'Invalid resource format'
        ];
    }
    
    // Get resource configuration and calculate cost
    $resourceConfig = $this->getResourceConfig($resource);
    $totalCost = $resourceConfig['cost'] * $quantity;
    
    // Check wallet balance
    if (!$this->hasSufficientBalance($userId, $totalCost)) {
        return [
            'success' => false,
            'message' => 'Insufficient balance'
        ];
    }
    
    // Security validation
    if (!SecurityMonitor::validateTransaction($userId, $totalCost, $resource)) {
        return [
            'success' => false,
            'message' => 'Transaction security check failed'
        ];
    }
    
    return ['success' => true];
}
```

##### 7.1.2 Transaction Security Validation
**Location:** `SecurityMonitor.php:86-110`

```php
public static function validateTransaction($userId, $amount, $resource) {
    // Check if amount is impossibly large
    if ($amount > 1000000) {
        self::log($userId, 'impossible_transaction', '', [
            'amount' => $amount,
            'resource' => $resource
        ], 'critical');
        
        return false;
    }
    
    // Check for rapid-fire transactions
    $sql = "SELECT COUNT(*) as recent_transactions FROM economic_transactions 
            WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 SECOND)";
    
    $result = Database::getInstance()->query($sql, ['uid' => $userId])->fetch();
    
    if ($result['recent_transactions'] > 5) {
        self::log($userId, 'rapid_fire_transactions', '', [
            'count' => $result['recent_transactions']
        ], 'high');
        
        return false;
    }
    
    return true;
}
```

### Phase 8: Rate Limiting System

**Location:** `app/Services/RateLimiter.php`

#### 8.1 Rate Limiting Implementation

##### 8.1.1 Rate Limit Check
**Location:** `RateLimiter.php:27-67`

```php
public static function check($userId, $endpoint, $maxRequests = 10, $windowSeconds = 60) {
    $db = Database::getInstance();
    
    // Clean old entries
    $db->query("DELETE FROM rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL ? SECOND)", [$windowSeconds]);
    
    // Get current request count
    $result = $db->query("
        SELECT request_count, window_start 
        FROM rate_limits 
        WHERE user_id = ? AND endpoint = ?
    ", [$userId, $endpoint])->fetch();
    
    if (!$result) {
        // First request in window
        $db->query("
            INSERT INTO rate_limits (user_id, endpoint, request_count, window_start)
            VALUES (?, ?, 1, NOW())
        ", [$userId, $endpoint]);
        
        return ['allowed' => true, 'remaining' => $maxRequests - 1];
    }
    
    // Check if limit exceeded
    if ($result['request_count'] >= $maxRequests) {
        $windowStart = strtotime($result['window_start']);
        $resetIn = $windowSeconds - (time() - $windowStart);
        
        // Log rate limit violation
        SecurityMonitor::log($userId, 'rate_limit_exceeded', $endpoint, [
            'requests' => $result['request_count'],
            'limit' => $maxRequests,
            'reset_in' => $resetIn
        ], 'medium');
        
        return ['allowed' => false, 'reset_in' => $resetIn];
    }
    
    // Increment request count
    $db->query("
        UPDATE rate_limits 
        SET request_count = request_count + 1 
        WHERE user_id = ? AND endpoint = ?
    ", [$userId, $endpoint]);
    
    return ['allowed' => true, 'remaining' => $maxRequests - $result['request_count'] - 1];
}
```

## Security Data Models

### 1. Security Alerts Table Structure

```sql
CREATE TABLE security_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    alert_type VARCHAR(50) NOT NULL,
    risk_level ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    description TEXT NOT NULL,
    metadata JSON,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_by INT,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (resolved_by) REFERENCES users(id),
    INDEX idx_unresolved (is_resolved),
    INDEX idx_user_alerts (user_id, created_at),
    INDEX idx_risk_level (risk_level)
);
```

### 2. Security Logs Table Structure

```sql
CREATE TABLE security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    endpoint VARCHAR(255),
    details JSON,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_event_type (event_type),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at),
    INDEX idx_ip_address (ip_address)
);
```

### 3. Banned IPs Table Structure

```sql
CREATE TABLE banned_ips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL UNIQUE,
    reason TEXT NOT NULL,
    expires_at TIMESTAMP NULL,
    is_permanent BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_expires_at (expires_at),
    INDEX idx_ip_address (ip_address)
);
```

### 4. Login Sessions Table Structure

```sql
CREATE TABLE login_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    device_type VARCHAR(50),
    browser VARCHAR(50),
    os VARCHAR(50),
    country_code VARCHAR(2),
    country VARCHAR(100),
    region VARCHAR(100),
    city VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_logins (user_id, created_at),
    INDEX idx_ip_address (ip_address),
    INDEX idx_location (country_code, city)
);
```

## Security Configuration

### 1. High-Risk Countries Configuration

```php
// In settings or configuration
$highRiskCountries = [
    'CN', // China
    'RU', // Russia
    'KP', // North Korea
    'IR', // Iran
    'SY', // Syria
    // Add more as needed
];
```

### 2. Security Thresholds

```php
$securityThresholds = [
    'impossible_travel_hours' => 24,
    'rapid_location_changes_threshold' => 3,
    'rapid_location_changes_window' => 24, // hours
    'max_transaction_amount' => 1000000,
    'rapid_fire_transactions_limit' => 5,
    'rate_limit_requests' => 10,
    'rate_limit_window' => 60, // seconds
];
```

### 3. Ban Durations

```php
$banDurations = [
    'honeypot_access' => 86400 * 7,      // 7 days
    'admin_honeypot' => 0,               // Permanent
    'unlimited_coins_honeypot' => 86400 * 30, // 30 days
    'critical_violation' => 86400 * 14,  // 14 days
];
```

## Security Best Practices

### 1. Implementation Guidelines

1. **Layered Security**: Implement multiple detection algorithms
2. **Fail-Safe Defaults**: Default to secure configurations
3. **Comprehensive Logging**: Log all security events with context
4. **Automated Responses**: Implement immediate responses for critical threats
5. **Admin Oversight**: Provide comprehensive admin monitoring tools

### 2. Performance Considerations

1. **Database Indexing**: Proper indexes on security tables
2. **Query Optimization**: Efficient queries for large datasets
3. **Caching**: Cache frequently accessed security data
4. **Rate Limiting**: Prevent abuse of security endpoints
5. **Background Processing**: Offload heavy security analysis

### 3. Security Considerations

1. **Data Privacy**: Minimize personal data in logs
2. **Encryption**: Encrypt sensitive security data
3. **Access Control**: Strict access to security tools
4. **Audit Trail**: Complete audit of security actions
5. **Regular Updates**: Keep security patterns updated

## Future Enhancement Opportunities

### 1. Advanced Detection Algorithms

1. **Machine Learning**: Behavioral analysis and anomaly detection
2. **Geofencing**: Location-based access controls
3. **Device Fingerprinting**: Advanced device identification
4. **Behavioral Biometrics**: User behavior patterns
5. **Network Analysis**: IP reputation and network analysis

### 2. Enhanced Response Systems

1. **Automated Remediation**: Self-healing security responses
2. **Multi-Factor Authentication**: Enhanced authentication requirements
3. **Account Lockout**: Temporary account suspension
4. **Notification Systems**: Real-time security notifications
5. **Integration APIs**: External security service integration

### 3. Monitoring and Analytics

1. **Real-time Dashboard**: Live security monitoring
2. **Threat Intelligence**: External threat data integration
3. **Compliance Reporting**: Automated compliance reports
4. **Security Metrics**: Comprehensive security KPIs
5. **Incident Response**: Structured incident management

## Conclusion

The security services implementation demonstrates a comprehensive, multi-layered approach to application security. The system combines real-time threat detection, automated response mechanisms, and comprehensive admin monitoring to provide robust protection against various security threats.

Key strengths of the implementation include:

1. **Proactive Detection**: Multiple algorithms detect threats before they cause damage
2. **Automated Response**: Immediate action on critical security events
3. **Comprehensive Monitoring**: Detailed logging and admin oversight
4. **Flexible Configuration**: Adaptable security thresholds and policies
5. **Scalable Architecture**: Designed to handle growing security demands

The system provides an excellent foundation for security-conscious applications while maintaining usability and performance. The modular design allows for easy extension and customization based on specific security requirements.
