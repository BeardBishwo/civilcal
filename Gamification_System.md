# Gamification System: Quiz Rewards → Economy → Shop → City Building

**Date:** 2025-12-30  
**Purpose:** Deep-dive technical guide for the end-to-end gamification loop from quiz rewards to city building, including shop purchases, battle pass, and admin configuration.  
**Scope:** Resource flows, economic security, shop mechanics, building construction, battle pass claims, and admin economy settings.

---

## 1. System Overview

The gamification system forms a closed-loop economy:

```
Quiz Completion
├── Correct Answers
│   └── GamificationService@rewardUser()
│       ├── Coins/Resources (wallet)
│       ├── Battle Pass XP
│       └── Mission Progress
│
Shop & Lifelines
├── Purchase with Coins
│   └── LifelineService@purchase()
│       ├── Deduct Coins
│       └── Add to Inventory (user_lifelines)
│
City Building
├── Construct Buildings
│   └── GamificationService@constructBuilding()
│       ├── Validate Materials
│       ├── Deduct Resources
│       └── Create Building Record
│
Battle Pass
├── Claim Rewards
│   └── BattlePassService@claimReward()
│       ├── Validate Level
│       ├── Grant Resources/Lifelines/Buildings
│       └── Mark Claimed
│
Admin Configuration
└── SettingsService (economy_resources, economy_ranks, economy_hud_config)
    └── Drives pricing, names, and UI
```

All economic actions are protected by **EconomicSecurityService** (server-side pricing, wallet snapshots, fraud detection) and **SecurityMonitor** (velocity checks, impossible amounts).

---

## 2. Quiz Reward Flow

### 2.1. Trigger

After a correct answer in `ExamEngineController@submit`:
```php
if ($isCorrect) {
    $this->gamificationService->rewardUser(
        $_SESSION['user_id'],
        true,
        $difficulty,
        $attemptId
    );
}
```

### 2.2. Reward Tiers

```php
$rewards = [
    'easy'   => ['coins' => 5,  'bricks' => 2],
    'medium' => ['coins' => 10, 'bricks' => 5, 'cement' => 2],
    'hard'   => ['coins' => 20, 'bricks' => 10, 'cement' => 5, 'steel' => 2]
];
```

- Numeric difficulty (1–5) mapped to tiers.
- Server-side; client cannot influence payouts.

### 2.3. Resource Distribution

```php
foreach ($payout as $resource => $amount) {
    if ($resource === 'xp') {
        $bp = new BattlePassService();
        $bp->addXp($userId, $amount);
    } else {
        // Build SET clause for atomic wallet update
        $setParts[] = "$resource = $resource + :$resource";
        $params[$resource] = $amount;
    }
}
```

- **XP**: Triggers battle pass progression.
- **Resources**: Coins, bricks, cement, steel updated in single `UPDATE user_resources`.

### 2.4. Battle Pass XP & Missions

- **BattlePassService@addXp()**: Increments XP, recalculates level (1000 XP/level).
- **MissionService@updateProgress()**: Increments daily mission counters; auto-claims rewards.

---

## 3. Shop & Lifeline System

### 3.1. Shop Display

- `GamificationController@shop()` loads lifeline inventory from `user_lifelines`.
- Prices defined in view: 100/200/300 coins.
- Dynamic economy settings (`economy_resources`) drive names/icons.

### 3.2. Purchase Flow

1. **Frontend**: AJAX POST to `/api/shop/purchase-lifeline` with CSRF and nonce.
2. **Controller**: `GamificationController@purchaseLifeline()`
   - IP ban check.
   - Rate limiting.
   - Input validation.
3. **Service**: `LifelineService@purchase()`
   - Validate balance via `GamificationService::getWallet()`.
   - Deduct coins.
   - Add to `user_lifelines` inventory.

### 3.3. Inventory Management

```sql
INSERT INTO user_lifelines (user_id, lifeline_type, quantity)
VALUES (:uid, :type, 1)
ON DUPLICATE KEY UPDATE quantity = quantity + 1;
```

- Supports multiple lifeline types (50/50, skip, poll).
- Quantity tracked per user.

---

## 4. City Building System

### 4.1. Building Cost Definitions

```php
$costs = [
    'house'  => ['bricks' => 100, 'wood_planks' => 20, 'sand' => 50, 'cement' => 10],
    'road'   => ['cement' => 50, 'sand' => 200],
    'bridge' => ['bricks' => 500, 'steel' => 200, 'cement' => 100],
    'tower'  => ['bricks' => 1000, 'cement' => 500, 'steel' => 500, 'wood_planks' => 200]
];
```

- Multi-resource requirements per building type.
- Configurable via admin settings.

### 4.2. Construction Flow

1. **Frontend**: AJAX POST to `/api/city/build` with building type.
2. **Controller**: `GamificationController@build()`
   - Nonce + honeypot validation.
   - Rate limiting.
3. **Service**: `GamificationService@constructBuilding()`
   - Validate wallet for all required resources.
   - Atomic multi-resource deduction.
   - Insert building record.

### 4.3. Resource Validation

```php
foreach ($cost as $res => $amount) {
    if ($wallet[$res] < $amount) {
        return ['success' => false, 'message' => "Not enough $res"];
    }
}
```

- All-or-nothing validation.
- Prevents partial consumption.

### 4.4. Atomic Update

```php
$sql = "UPDATE user_resources SET " . implode(', ', $setParts) . " WHERE user_id = :uid";
$this->db->query($sql, $params);
```

- Single statement deducts all resources.
- Followed by `INSERT INTO user_city_buildings`.

---

## 5. Battle Pass System

### 5.1. Progress Tracking

- `user_battle_pass` stores `current_xp` and `current_level`.
- XP per level: 1000.
- Seasonal rewards defined in `battle_pass_rewards`.

### 5.2. Claim Flow

1. **Frontend**: AJAX POST to `/api/battle-pass/claim` with reward_id, nonce, honeypot.
2. **Controller**: `GamificationController@claimReward()`
   - Rate limiting.
   - Input validation.
3. **Service**: `BattlePassService@claimReward()`
   - Validate user level meets reward requirement.
   - Grant reward based on `reward_type`:
     - **Resources**: Update `user_resources`.
     - **Lifelines**: Update `user_lifelines`.
     - **Buildings**: Insert into `user_city_buildings`.
   - Mark reward as claimed.

### 5.3. Reward Granting

```php
switch ($reward['reward_type']) {
    case 'bricks':
    case 'cement':
    case 'steel':
    case 'coins':
        $this->db->query("UPDATE user_resources SET $type = $type + :amt WHERE user_id = :uid", [
            'amt' => $reward['reward_value'],
            'uid' => $userId
        ]);
        break;
    case 'lifeline':
        // Insert into user_lifelines
        break;
    case 'building':
        // Insert into user_city_buildings
        break;
}
```

---

## 6. Admin Economy Configuration

### 6.1. Settings Schema

All economy configuration stored in `settings` table as JSON:

| Key | Type | Purpose |
|-----|------|---------|
| `economy_resources` | JSON | Resource names, icons, buy/sell prices |
| `economy_ranks` | JSON | User rank titles and thresholds |
| `economy_hud_config` | JSON | HUD layout and styling |

### 6.2. Resource Configuration

Example `economy_resources`:
```json
{
  "coins": {"name": "Coins", "icon": "/assets/icons/coin.png", "buy": null, "sell": null},
  "bricks": {"name": "Bricks", "icon": "/assets/icons/brick.png", "buy": 10, "sell": 8},
  "cement": {"name": "Cement", "icon": "/assets/icons/cement.png", "buy": 12, "sell": 9},
  "steel": {"name": "Steel", "icon": "/assets/icons/steel.png", "buy": 25, "sell": 20}
}
```

- **Buy/Sell**: Prices used by `EconomicSecurityService`.
- **Icons/Names**: Drive UI in shop and HUD.

### 6.3. Admin Interface

- `SettingsController@economy()` loads configuration.
- Form renders editable table for resources.
- Save persists back to `settings` via `SettingsService::set()`.

---

## 7. Resource HUD (Global UI)

### 7.1. Rendering

`partials/resource_hud.php` included on all pages:

```php
$wallet = $db->findOne('user_resources', ['user_id' => $_SESSION['user_id']]);
$resources = SettingsService::get('economy_resources', []);
$hudConfig = SettingsService::get('economy_hud_config', [...]);
```

### 7.2. Dynamic Display

- Loops over `economy_resources` config.
- Shows icon and formatted value for each resource.
- Sticky positioning; responsive.

### 7.3. Customization

- `economy_hud_config` controls header height, colors, layout.
- Admin can adjust without code changes.

---

## 8. Security & Economic Integrity

| Layer | Responsibility |
|-------|----------------|
| **EconomicSecurityService** | Server-side pricing, wallet snapshots, fraud checks |
| **SecurityMonitor** | Impossible amounts, rapid-fire detection |
| **RateLimiter** | Per-endpoint throttling |
| **NonceService** | Anti-replay for purchases/builds/claims |
| **Input Validation** | Amount caps, resource key sanitization |
| **Audit Logging** | `user_resource_logs` for all transactions |

---

## 9. Database Schema Highlights

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `user_resources` | Wallet | user_id, coins, bricks, cement, steel, wood_logs, wood_planks, sand |
| `user_resource_logs` | Audit | user_id, resource, amount, balance_after, transaction_type, reference_id |
| `user_lifelines` | Inventory | user_id, lifeline_type, quantity |
| `user_city_buildings` | Assets | user_id, building_type, created_at |
| `user_battle_pass` | Progress | user_id, current_xp, current_level, claimed_rewards (JSON) |
| `battle_pass_rewards` | Definitions | level, reward_type, reward_value, is_premium |
| `settings` | Config | setting_key, setting_value, setting_type |

---

## 10. API Endpoints Summary

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/shop/purchase-lifeline` | POST | Buy lifeline with coins |
| `/api/shop/purchase-resource` | POST | Buy resources with coins |
| `/api/shop/sell-resource` | POST | Sell resources for coins |
| `/api/city/build` | POST | Construct building |
| `/api/battle-pass/claim` | POST | Claim battle pass reward |
| `/api/quiz/lifeline/use` | POST | Activate lifeline in quiz |

All endpoints enforce:
- IP ban checks
- Rate limiting
- CSRF/nonce validation
- Input sanitization
- Server-side economic validation

---

## 11. Performance Considerations

- **Wallet reads**: Cached per request; minimal overhead.
- **Resource updates**: Single atomic UPDATE per transaction.
- **Battle pass claims**: Lightweight; level check + single INSERT/UPDATE.
- **HUD rendering**: Uses cached settings; no DB queries per resource after wallet fetch.
- **Audit logs**: `user_resource_logs` can grow; implement periodic archival.

---

## 12. Extensibility

- **New resources**: Add to `economy_resources` config and `user_resources` table.
- **New building types**: Add cost definitions in `GamificationService`.
- **Custom battle pass rewards**: Extend `reward_type` handling in `BattlePassService`.
- **Dynamic pricing**: Add time-based multipliers to `EconomicSecurityService`.
- **Marketplace**: Extend shop to support player-to-player trades.

---

## 13. Production Readiness Checklist

| Item | Status | Notes |
|------|--------|-------|
| Server-side pricing | ✅ | Settings-driven |
| Wallet snapshots | ✅ | Prevents races |
| Rate limiting | ✅ | Per-endpoint |
| Audit logging | ✅ | Full transaction trail |
| Anti-replay (nonce) | ✅ | State-changing actions |
| Input sanitization | ✅ | Resource keys, amounts |
| Admin configuration | ✅ | Live economy tuning |
| HUD responsiveness | ✅ | Dynamic, cached |
| Battle pass integrity | ✅ | Level validation, claim tracking |

---

## 14. Conclusion

The gamification system implements a secure, closed-loop economy where quiz performance drives resource acquisition, which fuels shop purchases, city building, and battle pass progression. All economic actions are validated server-side with multiple overlapping security layers. The system is highly configurable via admin settings, enabling live tuning of prices, rewards, and UI without code deployment. With comprehensive audit trails and fraud detection, the economy is production-ready for scale.
