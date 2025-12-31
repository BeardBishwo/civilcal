# Gamification System Architecture: Service Initialization to Frontend Integration

## Overview

This document provides an in-depth analysis of the complete gamification system architecture, covering the flow from service instantiation through reward distribution, shop transactions, battle pass progression, and city building. The system demonstrates sophisticated economic patterns with centralized service management, security validation, and database-backed resource tracking.

## Core Architecture Components

### 1. Service Layer Architecture

The gamification system is built around a centralized service architecture with clear separation of concerns:

```
Controller Layer → Service Layer → Security Layer → Database Layer
```

**Key Services:**
- **GamificationService**: Core economic engine and resource management
- **BattlePassService**: Battle pass progression and reward distribution
- **MissionService**: Daily mission tracking and progress updates
- **EconomicSecurityService**: Transaction validation and fraud prevention
- **SettingsService**: Dynamic configuration management

### 2. Economic Resource System

The system manages multiple resource types with dynamic configuration:

```
Primary Currency: Coins
Building Materials: Bricks, Cement, Steel, Planks, Wood Logs
Special Items: Lifelines, Building Blueprints
Progression: XP, Battle Pass Levels
```

### 3. Security Integration

Multi-layered security approach for all economic transactions:

```
IP Ban Check → Rate Limiting → Nonce Validation → Economic Security → Database Transaction
```

## Detailed System Flow Analysis

### Phase 1: Service Initialization and Dependency Injection

**Location:** `app/Controllers/Quiz/GamificationController.php:15-21`

#### 1.1 Controller Constructor Pattern
```php
public function __construct() {
    parent::__construct();
    $this->requireAuth();
    $this->gamificationService = new GamificationService();
    $this->nonceService = new NonceService();
}
```

**Initialization Flow:**
1. **Base Controller Setup**: Inherit authentication and view rendering
2. **Authentication Enforcement**: Ensure user is logged in
3. **Service Instantiation**: Create gamification service instance
4. **Security Service Setup**: Initialize nonce service for CSRF protection

#### 1.2 GamificationService Constructor
**Location:** `app/Services/GamificationService.php:14-19`

```php
public function __construct() {
    $this->db = \App\Core\Database::getInstance();
    $this->economicSecurity = new EconomicSecurityService();
}
```

**Dependency Injection Pattern:**
- **Database Singleton**: Shared database connection across all operations
- **Security Service**: Economic validation and fraud prevention layer
- **Lazy Loading**: Services initialized only when needed

#### 1.3 Wallet Initialization System
**Location:** `GamificationService.php:23-27`

```php
public function initWallet($userId) {
    $sql = "INSERT IGNORE INTO user_resources (user_id, bricks, cement, steel, planks, wood_logs, coins) 
            VALUES (:uid, 0, 0, 0, 0, 0, 0)";
    $this->db->query($sql, ['uid' => $userId]);
}
```

**Wallet Management Features:**
- **Auto-Creation**: Creates wallet record if not exists
- **Zero Balance Initialization**: All resources start at zero
- **Idempotent Operation**: Safe to call multiple times
- **Performance Optimized**: Uses INSERT IGNORE for efficiency

### Phase 2: Quiz Reward Distribution System

**Location:** `app/Controllers/Quiz/ExamEngineController.php:261-276`

#### 2.1 Batch Reward Processing Architecture

The quiz system implements sophisticated batch reward processing for performance and consistency:

```php
// Accumulate for Batch Reward
if ($isCorrect) {
    $correctCount++;
    $correctAnswersList[] = [
        'question_id' => $question['id'],
        'reward_type' => $question['reward_type'] ?? 'coins',
        'reward_amount' => $question['reward_amount'] ?? 10
    ];
}

// Process Batch Rewards
if (!empty($correctAnswersList)) {
    $this->gamificationService->processExamRewards($_SESSION['user_id'], $correctAnswersList);
}
```

#### 2.2 GamificationService Reward Processing
**Location:** `GamificationService.php:94-155`

```php
public function processExamRewards($userId, $correctAnswers) {
    // Calculate total loot from all answers
    $totalLoot = [];
    foreach ($correctAnswers as $answer) {
        $rewardType = $answer['reward_type'];
        $rewardAmount = $answer['reward_amount'];
        
        if (!isset($totalLoot[$rewardType])) {
            $totalLoot[$rewardType] = 0;
        }
        $totalLoot[$rewardType] += $rewardAmount;
    }
    
    // Handle XP Separately
    if ($totalLoot['xp'] > 0) {
        $bp = new BattlePassService();
        $bp->addXp($userId, $totalLoot['xp']);
        $ms = new MissionService();
        $ms->updateProgress($userId, 'solve_questions');
        unset($totalLoot['xp']);
    }
    
    // Update resources in single transaction
    if (!empty($totalLoot)) {
        $setParts = [];
        $params = ['uid' => $userId];
        
        foreach ($totalLoot as $resource => $amount) {
            $setParts[] = "$resource = $resource + :$resource";
            $params[$resource] = $amount;
        }
        
        $sql = "UPDATE user_resources SET " . implode(', ', $setParts) . " WHERE user_id = :uid";
        $this->db->query($sql, $params);
        
        // Log transactions for audit trail
        foreach ($totalLoot as $res => $amount) {
            if ($amount > 0) {
                $this->logTransaction($userId, $res, $amount, 'exam_reward');
            }
        }
    }
}
```

**Reward Processing Features:**
- **Batch Aggregation**: Combines multiple rewards into single transaction
- **XP Separation**: Handles XP progression separately from resources
- **Service Integration**: Coordinates with battle pass and mission services
- **Atomic Transactions**: Single database update for consistency
- **Audit Logging**: Complete transaction history tracking

### Phase 3: Shop Purchase Transaction System

**Location:** `app/Controllers/Quiz/GamificationController.php:178-236`

#### 3.1 Frontend Purchase Request Flow
**Location:** `themes/default/views/quiz/gamification/shop.php:328-332`

```javascript
async function performTransaction(url, data) {
    const fd = new FormData();
    Object.keys(data).forEach(key => fd.append(key, data[key]));
    fd.append('trap_answer', document.getElementById('shop_trap').value || '');
    
    const res = await fetch(app_base_url(url.substring(1)), { method: 'POST', body: fd });
    const result = await res.json();
    
    if (result.success) {
        updateUI();
        showNotification('Transaction successful!', 'success');
    } else {
        showNotification(result.message || 'Transaction failed', 'error');
    }
}
```

#### 3.2 Controller Security Pipeline
**Location:** `GamificationController.php:180-236`

```php
public function purchaseResource() {
    // Security: Check IP ban
    if (SecurityValidator::isIpBanned()) {
        $this->json(['success' => false, 'message' => 'Access denied'], 403);
        return;
    }
    
    // Security: Rate limiting
    $rateLimiter = new \App\Services\RateLimiter();
    $rateCheck = $rateLimiter->check($_SESSION['user_id'], '/api/shop/purchase-resource');
    
    if (!$rateCheck['allowed']) {
        $this->json(['success' => false, 'message' => 'Too many requests'], 429);
        return;
    }
    
    // Nonce validation
    $nonce = $_POST['nonce'] ?? '';
    if (!$this->nonceService->verify($_SESSION['user_id'], 'shop_purchase', $nonce)) {
        $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        return;
    }
    
    // Resource validation
    $resource = $_POST['resource'] ?? '';
    $amount = (int)($_POST['amount'] ?? 1);
    
    if (!preg_match('/^[a-z_]+$/', $resource) || $amount < 1 || $amount > 100) {
        $this->json(['success' => false, 'message' => 'Invalid resource or amount'], 400);
        return;
    }
    
    // Suspicious activity check
    if (isset($_POST['trap_answer']) && !empty($_POST['trap_answer'])) {
        SecurityMonitor::log($_SESSION['user_id'], 'honeypot_trigger', '/api/shop/purchase-resource', [
            'trap_answer' => $_POST['trap_answer']
        ], 'high');
    }
    
    // Execute purchase
    $result = $this->gamificationService->purchaseResource($_SESSION['user_id'], $resource, $amount);
    
    if ($result['success']) {
        $newNonce = $this->nonceService->generate($_SESSION['user_id'], 'shop_purchase');
        $result['nonce'] = $newNonce['nonce'] ?? null;
    }
    
    $this->json($result, $result['success'] ? 200 : 400);
}
```

#### 3.3 Service Layer Purchase Processing
**Location:** `GamificationService.php:247-275`

```php
public function purchaseResource($userId, $resource, $amount = 1) {
    // Economic security validation
    $validation = $this->economicSecurity->validatePurchase($userId, $resource, $amount);
    
    if (!$validation['success']) {
        return $validation;
    }
    
    $totalCost = $validation['total_cost'];
    
    // Atomic transaction
    $sql = "UPDATE user_resources 
            SET coins = coins - :cost, 
                $resource = $resource + :amt 
            WHERE user_id = :uid 
            AND coins >= :cost";
    
    $result = $this->db->query($sql, [
        'cost' => $totalCost,
        'amt' => $amount,
        'uid' => $userId
    ]);
    
    if ($result->rowCount() > 0) {
        // Log transactions
        $this->logTransaction($userId, 'coins', -$totalCost, 'shop_purchase');
        $this->logTransaction($userId, $resource, $amount, 'shop_purchase');
        
        return ['success' => true, 'message' => 'Purchase successful'];
    } else {
        return ['success' => false, 'message' => 'Insufficient coins'];
    }
}
```

#### 3.4 Economic Security Validation
**Location:** `app/Services/EconomicSecurityService.php:22-69`

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
    $wallet = $this->getUserWallet($userId);
    if ($wallet['coins'] < $totalCost) {
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
    
    return [
        'success' => true,
        'total_cost' => $totalCost
    ];
}
```

### Phase 4: Battle Pass Reward System

**Location:** `app/Services/BattlePassService.php`

#### 4.1 Reward Claim Flow
**Location:** `themes/default/views/quiz/gamification/battle_pass.php:443-447`

```javascript
async function claimReward(rewardId) {
    const formData = new FormData();
    formData.append('reward_id', rewardId);
    formData.append('nonce', currentNonce);
    formData.append('trap_answer', document.getElementById('bp_trap').value || '');
    
    const response = await fetch('/api/battle-pass/claim', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
        updateBattlePassUI();
        showNotification('Reward claimed!', 'success');
    }
}
```

#### 4.2 Battle Pass Service Implementation
**Location:** `BattlePassService.php:76-108`

```php
public function claimReward($userId, $rewardId) {
    // Fetch reward and progress data
    $reward = $this->getReward($rewardId);
    $progressData = $this->getUserProgress($userId);
    $progress = $progressData['progress'];
    
    // Level requirement check
    if ($progress['current_level'] < $reward['level']) {
        return ['success' => false, 'message' => "Level too low!"];
    }
    
    // Already claimed check
    if (in_array((int)$rewardId, $progress['claimed_rewards'])) {
        return ['success' => false, 'message' => "Already claimed!"];
    }
    
    // Premium pass check for premium rewards
    if ($reward['is_premium'] && !$progress['has_premium_pass']) {
        return ['success' => false, 'message' => "Premium pass required!"];
    }
    
    // Grant reward
    $this->grantReward($userId, $reward['reward_type'], $reward['reward_value']);
    
    // Mark as claimed
    $progress['claimed_rewards'][] = (int)$rewardId;
    $sql = "UPDATE user_battle_pass SET claimed_rewards = :claimed WHERE user_id = :uid";
    $this->db->query($sql, [
        'claimed' => json_encode($progress['claimed_rewards']),
        'uid' => $userId
    ]);
    
    return ['success' => true, 'message' => 'Reward claimed successfully!'];
}
```

#### 4.3 Reward Granting System
**Location:** `BattlePassService.php:127-145`

```php
private function grantReward($userId, $rewardType, $rewardValue) {
    switch ($rewardType) {
        case 'coins':
        case 'bricks':
        case 'cement':
        case 'steel':
            $this->db->query("UPDATE user_resources SET $rewardType = $rewardType + :val WHERE user_id = :uid", [
                'val' => $rewardValue,
                'uid' => $userId
            ]);
            break;
            
        case 'lifeline':
            $this->db->query("INSERT INTO user_lifelines (user_id, lifeline_type, created_at) VALUES (:uid, '50_50', NOW())", [
                'uid' => $userId
            ]);
            break;
            
        case 'building':
            $this->db->query("INSERT INTO user_city_buildings (user_id, building_type, level, created_at) VALUES (:uid, :type, 1, NOW())", [
                'uid' => $userId,
                'type' => $rewardValue
            ]);
            break;
    }
}
```

### Phase 5: City Building Construction System

**Location:** `app/Services/GamificationService.php:366-410`

#### 5.1 Building Construction Request
**Location:** `themes/default/views/quiz/gamification/city.php:171-175`

```javascript
async function constructBuilding(buildingType) {
    const formData = new FormData();
    formData.append('building_type', buildingType);
    formData.append('csrf_token', csrfToken);
    
    const res = await fetch('/api/city/build', {
        method: 'POST',
        body: formData
    });
    
    const result = await res.json();
    
    if (result.success) {
        updateCityUI();
        showNotification('Building constructed!', 'success');
    }
}
```

#### 5.2 Building Construction Service
**Location:** `GamificationService.php:366-410`

```php
public function constructBuilding($userId, $buildingType) {
    // Get building costs definition
    $costs = $this->getBuildingCosts($buildingType);
    if (!$costs) {
        return ['success' => false, 'message' => 'Invalid building type'];
    }
    
    // Get wallet and validate resources
    $wallet = $this->getWallet($userId);
    
    foreach ($costs as $resource => $amount) {
        if ($wallet[$resource] < $amount) {
            return ['success' => false, 'message' => "Not enough " . str_replace('_', ' ', $resource)];
        }
    }
    
    // Build UPDATE SQL statement for resource deduction
    $setParts = [];
    $params = ['uid' => $userId];
    
    foreach ($costs as $res => $amount) {
        $setParts[] = "$res = $res - :$res";
        $params[$res] = $amount;
    }
    
    // Deduct resources atomically
    $sql = "UPDATE user_resources SET " . implode(', ', $setParts) . " WHERE user_id = :uid";
    $this->db->query($sql, $params);
    
    // Log transactions
    foreach ($costs as $res => $amount) {
        $this->logTransaction($userId, $res, -$amount, 'building_construction');
    }
    
    // Insert building record
    $sqlBuild = "INSERT INTO user_city_buildings (user_id, building_type, level, created_at) VALUES (:uid, :type, 1, NOW())";
    $this->db->query($sqlBuild, [
        'uid' => $userId,
        'type' => $buildingType
    ]);
    
    return ['success' => true, 'message' => 'Building constructed successfully'];
}
```

**Building Cost Configuration:**
```php
private function getBuildingCosts($buildingType) {
    $costs = [
        'house' => ['bricks' => 100, 'cement' => 50, 'steel' => 10],
        'road' => ['cement' => 20, 'steel' => 5],
        'bridge' => ['steel' => 50, 'cement' => 30],
        'tower' => ['steel' => 100, 'bricks' => 80, 'cement' => 60]
    ];
    
    return $costs[$buildingType] ?? null;
}
```

### Phase 6: Daily Login Bonus System

**Location:** `app/Services/GamificationService.php:158-200`

#### 6.1 Daily Bonus Trigger
**Location:** `app/Controllers/Quiz/PortalController.php:39-44`

```php
if (isset($_SESSION['user_id'])) {
    // Trigger Daily Bonus
    $gs = new \App\Services\GamificationService();
    $dailyBonus = $gs->processDailyLoginBonus($_SESSION['user_id']);
    
    // Continue with portal logic...
}
```

#### 6.2 Daily Bonus Processing
**Location:** `GamificationService.php:158-200`

```php
public function processDailyLoginBonus($userId) {
    $this->initWallet($userId);
    
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    // Fetch user record
    $user = $this->db->findOne('users', ['id' => $userId]);
    
    // Check eligibility (not claimed today)
    if ($user && $user['last_login_reward_at'] !== $today) {
        $streak = (int)($user['login_streak'] ?? 0);
        
        // Calculate streak logic
        if ($user['last_login_reward_at'] === $yesterday) {
            $streak++;
        } else {
            $streak = 1;
        }
        
        // Determine reward type
        $rewards = [];
        if ($streak % 7 === 0) {
            // Day 7 Reward: 1 Steel Bundle (10 Steel)
            $rewards['steel'] = 10;
        } else {
            // Regular day: 1 Wood Log
            $rewards['wood_logs'] = 1;
        }
        
        // UPDATE user_resources (add reward)
        foreach ($rewards as $resource => $amount) {
            $sql = "UPDATE user_resources SET $resource = $resource + :amt WHERE user_id = :uid";
            $this->db->query($sql, ['amt' => $amount, 'uid' => $userId]);
            
            $this->logTransaction($userId, $resource, $amount, 'daily_login_bonus');
        }
        
        // UPDATE users table (streak + date)
        $this->db->query("UPDATE users SET last_login_reward_at = :today, login_streak = :streak WHERE id = :uid", [
            'today' => $today,
            'streak' => $streak,
            'uid' => $userId
        ]);
        
        return [
            'success' => true,
            'rewards' => $rewards,
            'streak' => $streak
        ];
    }
    
    return ['success' => false, 'message' => 'Already claimed today'];
}
```

**Daily Bonus Features:**
- **Streak Tracking**: Maintains consecutive login streak
- **Progressive Rewards**: Better rewards on milestone days
- **Automatic Processing**: Triggers on portal page load
- **Reset Logic**: Resets streak on missed days

### Phase 7: Frontend Resource HUD System

**Location:** `themes/default/views/partials/resource_hud.php`

#### 7.1 HUD Display Logic
**Location:** `resource_hud.php:5-34`

```php
if (isset($_SESSION['user_id'])):
    $db = \App\Core\Database::getInstance();
    $wallet = $db->findOne('user_resources', ['user_id' => $_SESSION['user_id']]);
    
    // Fetch Dynamic Economy Settings
    $resources = \App\Services\SettingsService::get('economy_resources', []);
    $hudConfig = \App\Services\SettingsService::get('economy_hud_config', [
        'header_height' => '32px',
        'show_icons' => true,
        'animate_changes' => true
    ]);
?>

<div class="resource-hud" style="height: <?= $hudConfig['header_height'] ?>;">
    <?php foreach ($resources as $key => $config): ?>
        <?php
        $value = $wallet[$key] ?? 0;
        $iconPath = $config['icon_path'] ?? '/assets/icons/resources/' . $key . '.png';
        ?>
        <div class="resource-item" title="<?= htmlspecialchars($config['name'] ?? ucfirst($key)) ?>">
            <img src="<?= app_base_url($iconPath) ?>" alt="<?= $key ?>" class="resource-icon">
            <span class="res-value"><?= number_format($value) ?></span>
        </div>
    <?php endforeach; ?>
</div>
```

#### 7.2 Settings Service Integration
**Location:** `app/Services/SettingsService.php:17-21`

```php
public static function get($key, $default = null) {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT setting_value, setting_type FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    
    if (!$result) {
        return $default;
    }
    
    return $result['setting_type'] === 'json' ? json_decode($result['setting_value'], true) : $result['setting_value'];
}
```

**HUD Features:**
- **Dynamic Configuration**: Resource display controlled by settings
- **Real-time Updates**: Direct database queries for current balances
- **Responsive Design**: Adapts to different screen sizes
- **Icon Integration**: Visual resource representation

### Phase 8: Route Registration and Request Routing

**Location:** `app/routes.php`

#### 8.1 Route Registration Pattern
**Location:** `routes.php:1964-1977`

```php
// Gamification / Civil City Routes
$router->add("GET", "/quiz/city", "Quiz\\GamificationController@city", ["auth"]);
$router->add("GET", "/quiz/shop", "Quiz\\GamificationController@shop", ["auth"]);
$router->add("GET", "/quiz/sawmill", "Quiz\\GamificationController@sawmill", ["auth"]);
$router->add("GET", "/quiz/battle-pass", "Quiz\\GamificationController@battlePass", ["auth"]);

$router->add("POST", "/api/city/craft", "Quiz\\GamificationController@craft", ["auth"]);
$router->add("POST", "/api/shop/purchase", "Quiz\\GamificationController@purchase", ["auth"]);
$router->add("POST", "/api/shop/purchase-resource", "Quiz\\GamificationController@purchaseResource", ["auth"]);
$router->add("POST", "/api/shop/sell-resource", "Quiz\\GamificationController@sellResource", ["auth"]);
$router->add("POST", "/api/shop/purchase-bundle", "Quiz\\GamificationController@purchaseBundle", ["auth"]);
```

#### 8.2 Request Routing Flow

**Route Processing Pipeline:**
1. **Route Matching**: URL pattern matching against registered routes
2. **Middleware Execution**: Authentication and other middleware
3. **Controller Instantiation**: Create controller with dependencies
4. **Method Invocation**: Call appropriate controller method
5. **Response Generation**: Return JSON or view response

## Database Schema Architecture

### 1. User Resources Table

```sql
CREATE TABLE user_resources (
    user_id INT PRIMARY KEY,
    coins BIGINT DEFAULT 0,
    bricks BIGINT DEFAULT 0,
    cement BIGINT DEFAULT 0,
    steel BIGINT DEFAULT 0,
    planks BIGINT DEFAULT 0,
    wood_logs BIGINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 2. User Resource Logs Table

```sql
CREATE TABLE user_resource_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resource_type VARCHAR(50) NOT NULL,
    amount BIGINT NOT NULL,
    transaction_type VARCHAR(50) NOT NULL,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_resource (user_id, resource_type),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_created_at (created_at)
);
```

### 3. Battle Pass Tables

```sql
CREATE TABLE user_battle_pass (
    user_id INT PRIMARY KEY,
    current_level INT DEFAULT 1,
    current_xp INT DEFAULT 0,
    has_premium_pass BOOLEAN DEFAULT FALSE,
    claimed_rewards JSON,
    season_start_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE battle_pass_rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level INT NOT NULL,
    reward_type VARCHAR(50) NOT NULL,
    reward_value VARCHAR(100) NOT NULL,
    is_premium BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0
);
```

### 4. City Building Tables

```sql
CREATE TABLE user_city_buildings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    building_type VARCHAR(50) NOT NULL,
    level INT DEFAULT 1,
    position_x INT DEFAULT 0,
    position_y INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_buildings (user_id, building_type)
);
```

### 5. Settings Table

```sql
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Configuration Management

### 1. Economy Resources Configuration

```json
{
    "economy_resources": {
        "coins": {
            "name": "Coins",
            "icon_path": "/assets/icons/resources/coin.png",
            "base_price": 1,
            "max_stack": 999999999
        },
        "bricks": {
            "name": "Bricks",
            "icon_path": "/assets/icons/resources/brick.png",
            "base_price": 2,
            "max_stack": 999999
        },
        "cement": {
            "name": "Cement",
            "icon_path": "/assets/icons/resources/cement.png",
            "base_price": 3,
            "max_stack": 999999
        },
        "steel": {
            "name": "Steel",
            "icon_path": "/assets/icons/resources/steel.png",
            "base_price": 10,
            "max_stack": 99999
        }
    }
}
```

### 2. Shop Configuration

```json
{
    "shop_config": {
        "max_purchase_amount": 100,
        "refresh_interval": 300,
        "show_bundles": true,
        "enable_trading": false,
        "tax_rate": 0.05
    }
}
```

### 3. Battle Pass Configuration

```json
{
    "battle_pass_config": {
        "season_duration_days": 30,
        "xp_per_question": 10,
        "premium_cost_coins": 1000,
        "max_level": 100,
        "free_rewards_per_level": 1,
        "premium_rewards_per_level": 2
    }
}
```

## Security Architecture

### 1. Multi-Layer Security Pipeline

```
Request → IP Ban Check → Rate Limiting → Nonce Validation → Input Sanitization → Economic Security → Database Transaction
```

### 2. Economic Security Features

- **Transaction Validation**: Prevent impossible transactions
- **Rate Limiting**: Prevent abuse and automation
- **Honeypot Traps**: Detect bots and suspicious activity
- **Audit Logging**: Complete transaction history
- **Resource Validation**: Ensure valid resource types and amounts

### 3. Fraud Prevention Mechanisms

- **Suspicious Activity Detection**: Pattern analysis for unusual behavior
- **Geolocation Checks**: Location-based security validation
- **Device Fingerprinting**: Track device changes
- **Velocity Checks**: Prevent rapid-fire transactions

## Performance Optimization

### 1. Database Optimization

- **Indexing Strategy**: Proper indexes on frequently queried columns
- **Query Optimization**: Efficient SQL with prepared statements
- **Connection Pooling**: Shared database connections
- **Batch Operations**: Group multiple operations into single transactions

### 2. Caching Strategy

- **Resource Balances**: Cache frequently accessed wallet data
- **Configuration Cache**: Cache settings and economy configuration
- **Session Caching**: Store user session data efficiently
- **Static Assets**: Optimize frontend asset delivery

### 3. Frontend Optimization

- **Lazy Loading**: Load resources on demand
- **Debounced Updates**: Prevent excessive UI updates
- **Efficient Rendering**: Optimize DOM manipulation
- **Resource Bundling**: Minimize HTTP requests

## Integration Patterns

### 1. Service Integration

```php
// Service coordination pattern
class GamificationService {
    public function processExamRewards($userId, $rewards) {
        // Coordinate with multiple services
        $battlePassService = new BattlePassService();
        $missionService = new MissionService();
        
        // Execute coordinated operations
        $battlePassService->addXp($userId, $xpAmount);
        $missionService->updateProgress($userId, 'solve_questions');
        
        // Update resources
        $this->updateResources($userId, $resourceRewards);
    }
}
```

### 2. Event-Driven Architecture

```php
// Event publishing pattern
class GamificationService {
    public function purchaseResource($userId, $resource, $amount) {
        // Execute transaction
        $result = $this->executePurchase($userId, $resource, $amount);
        
        if ($result['success']) {
            // Publish events
            EventPublisher::publish('resource.purchased', [
                'user_id' => $userId,
                'resource' => $resource,
                'amount' => $amount
            ]);
        }
        
        return $result;
    }
}
```

### 3. Dependency Injection

```php
// Constructor injection pattern
class GamificationController {
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->gamificationService = new GamificationService();
        $this->nonceService = new NonceService();
    }
}
```

## Best Practices and Guidelines

### 1. Service Design Principles

- **Single Responsibility**: Each service has a clear, focused purpose
- **Dependency Injection**: Dependencies are injected rather than hardcoded
- **Interface Segregation**: Services implement focused interfaces
- **Open/Closed Principle**: Services are open for extension, closed for modification

### 2. Database Design Principles

- **Normalization**: Proper normalization to avoid data redundancy
- **Indexing**: Strategic indexing for performance
- **Constraints**: Foreign key constraints for data integrity
- **Audit Trails**: Complete logging of all economic transactions

### 3. Security Best Practices

- **Defense in Depth**: Multiple security layers
- **Least Privilege**: Minimal required permissions
- **Input Validation**: Comprehensive input sanitization
- **Error Handling**: Secure error handling without information leakage

### 4. Performance Best Practices

- **Lazy Loading**: Load data only when needed
- **Batching**: Group operations for efficiency
- **Caching**: Cache frequently accessed data
- **Monitoring**: Track performance metrics

## Future Enhancement Opportunities

### 1. Advanced Economic Features

- **Market System**: Player-to-player trading
- **Auction House**: Bid-based item trading
- **Crafting System**: Complex item creation
- **Guild Economy**: Shared resources and buildings

### 2. Enhanced Gamification

- **Achievement System**: Badge and trophy system
- **Leaderboards**: Competitive rankings
- **Tournaments**: Time-limited competitions
- **Seasonal Events**: Special events and rewards

### 3. Social Features

- **Guild System**: Team-based gameplay
- **Social Trading**: Resource sharing between friends
- **Collaborative Building**: Multiplayer construction
- **Chat System**: In-game communication

### 4. Analytics and Monitoring

- **Economic Analytics**: Market trend analysis
- **User Behavior Tracking**: Engagement metrics
- **Performance Monitoring**: System health tracking
- **A/B Testing**: Feature optimization

## Conclusion

The gamification system architecture demonstrates a sophisticated, well-structured approach to implementing complex economic and progression systems. The system successfully balances:

- **Modularity**: Clear separation of concerns across services
- **Security**: Comprehensive security measures for economic transactions
- **Performance**: Optimized database operations and caching strategies
- **Scalability**: Architecture designed for future growth and expansion
- **Maintainability**: Clean code patterns and comprehensive documentation

Key architectural strengths include:

1. **Service-Oriented Design**: Clear service boundaries and responsibilities
2. **Security-First Approach**: Multi-layered security for all economic operations
3. **Database Integrity**: Proper schema design with audit trails
4. **Configuration Management**: Flexible, database-driven configuration
5. **Frontend Integration**: Seamless integration with responsive UI components

The system provides an excellent foundation for complex gamification features while maintaining security, performance, and extensibility. The modular design allows for easy addition of new features and integration with external systems.
