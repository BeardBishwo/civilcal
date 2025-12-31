# Gamification Service Architecture: Resource Management & Economic Security

**Date:** 2025-12-30  
**Purpose:** Deep-dive technical guide for the gamification economy and security subsystem.  
**Scope:** Resource transactions, server-side validation, anti-fraud layers, audit trails, and integration with battle pass/missions.

---

## 1. High-Level Architecture

The gamification system is built as a multi-layered stack:

```
Controller Layer
├── IP Ban Check
├── Rate Limiting
├── CSRF/Nonce Validation
└── Input Sanitization
        ↓
GamificationService (Core Logic)
├── Reward Distribution
├── Crafting & Building
├── Purchase/Sell
└── Transaction Logging
        ↓
EconomicSecurityService (Validation)
├── Server-side Pricing
├── Wallet Snapshots
├── Amount/Resource Validation
└── Fraud Pattern Checks
        ↓
SecurityMonitor (Anomaly Detection)
├── Impossible Amount Detection
├── Rapid-Fire Transaction Detection
├── Suspicious Pattern Aggregation
└── Security Event Logging
        ↓
Database Layer
├── user_resources (wallet)
├── user_resource_logs (audit)
├── rate_limits
├── security_logs
└── honeypot_bans
```

All economic actions flow through every layer, ensuring defense-in-depth.

---

## 2. Core Services

### 2.1. GamificationService

**Responsibilities:**
- Orchestrate rewards, crafting, building, purchases, and sales.
- Maintain wallet consistency.
- Trigger side effects (XP, missions, battle pass).
- Emit audit logs.

**Key Methods:**
- `rewardUser($userId, $isCorrect, $difficulty, $referenceId)`
- `purchaseResource($userId, $resource, $amount)`
- `sellResource($userId, $resource, $amount)`
- `craftPlanks($userId, $quantity)`
- `constructBuilding($userId, $type)`

### 2.2. EconomicSecurityService

**Responsibilities:**
- Enforce server-side pricing and resource costs.
- Validate wallet balances before transactions.
- Detect and block impossible transactions.
- Provide transaction safety checks.

**Key Methods:**
- `validatePurchase($userId, $resource, $amount)`
- `validateSell($userId, $resource, $amount)`
- `canReward($userId)` – cooldown enforcement
- `getWalletSnapshot($userId, $resource = null)`

### 2.3. SecurityMonitor

**Responsibilities:**
- Detect impossible amounts (>1M).
- Detect rapid-fire transactions (>5/sec).
- Aggregate suspicious patterns (3+ violations in 5 minutes).
- Log all security events.

**Key Methods:**
- `validateTransaction($userId, $amount, $resource, $context)`
- `detectSuspicious($userId)`
- `log($userId, $event, $endpoint, $details, $severity)`

### 2.4. RateLimiter

**Responsibilities:**
- Per-user/per-endpoint throttling.
- Configurable windows and limits.
- Auto-expiration of old records.

**Key Methods:**
- `check($userId, $endpoint, $maxRequests, $windowSeconds)`

### 2.5. NonceService

**Responsibilities:**
- Generate one-time tokens for state-changing actions.
- Validate and consume nonces to prevent replay.
- Auto-cleanup of expired nonces.

**Key Methods:**
- `generate($userId, $scope)`
- `validateAndConsume($nonce, $userId, $scope)`

---

## 3. Transaction Flows

### 3.1. Purchase Flow (Resource Shop)

1. **Controller** (`GamificationController@purchaseResource`)
   - IP ban check.
   - Rate limit: 10 requests/minute.
   - SecurityValidator caps amount (1–1000).
   - Nonce validation.

2. **GamificationService@purchaseResource**
   - Calls `EconomicSecurityService::validatePurchase()`.

3. **EconomicSecurityService@validatePurchase**
   - Sanitize resource key (regex: `^[a-z0-9_]+$`).
   - Fetch server-side price from `economy_resources` settings.
   - Wallet snapshot check: ensure sufficient coins.
   - SecurityMonitor: validate amount/velocity.

4. **Database**
   - Atomic `UPDATE user_resources SET coins = coins - :cost, resource = resource + :amt`.

5. **Audit**
   - `GamificationService::logTransaction()` records both sides.

**Security Guarantees:**
- Client cannot set price or manipulate amounts.
- Replay attacks blocked by nonce.
- Rapid abuse throttled by rate limit and velocity checks.

---

### 3.2. Sell Flow (Inventory Liquidation)

Mirrors purchase flow but validates inventory instead of coins.

1. **Controller** (`GamificationController@sellResource`)
   - Same pre-checks (IP ban, rate limit, nonce).

2. **GamificationService@sellResource**
   - Calls `EconomicSecurityService::validateSell()`.

3. **EconomicSecurityService@validateSell**
   - Resource key sanitization.
   - Server-side sell price from settings.
   - Wallet snapshot: ensure sufficient resource quantity.
   - SecurityMonitor checks.

4. **Database**
   - Atomic `UPDATE user_resources SET coins = coins + :gain, resource = resource - :amt`.

5. **Audit**
   - Dual log entries (coins +, resource -).

---

### 3.3. Crafting Flow (Sawmill)

Fixed-cost transformation with multiple inputs/outputs.

1. **Controller** (`GamificationController@craft`)
   - Nonce + honeypot checks.
   - Rate limiting.

2. **GamificationService@craftPlanks**
   - Cost: 1 log + 10 coins → 4 planks.
   - Wallet snapshot validation.
   - Atomic multi-resource update.

3. **Audit**
   - Triple log entries (logs -, coins -, planks +).

---

### 3.4. Building Construction Flow

Multi-resource cost with building record creation.

1. **Controller** (`GamificationController@build`)
   - Nonce + rate limit.

2. **GamificationService@constructBuilding`
   - Cost definitions per building type (e.g., house: 100 bricks, 20 planks, 50 sand, 10 cement).
   - Loop validation for all required resources.
   - Atomic `UPDATE user_resources` deducting all costs.
   - `INSERT INTO user_city_buildings`.

3. **Audit**
   - Log each resource deduction.

---

### 3.5. Reward Flow (Quiz Completion)

1. **Controller** (`ExamEngineController@submit`)
   - Nonce + honeypot.
   - Rate limiting.

2. **GamificationService@rewardUser`
   - `EconomicSecurityService::canReward()` enforces cooldown (default 30s).
   - XP triggers BattlePassService.
   - Mission progress update.
   - Wallet update for coins/resources.

3. **Audit**
   - Log reward with reference ID (attempt ID).

---

## 4. Security Controls in Depth

### 4.1. Server-Side Pricing

All resource prices/costs are stored in `economy_resources` settings:

```json
{
  "bricks": {"buy": 10, "sell": 8},
  "cement": {"buy": 12, "sell": 9},
  "steel": {"buy": 25, "sell": 20},
  "coins": {"buy": null, "sell": null}
}
```

- Client cannot influence price.
- Prices can be tuned without code deployment.

### 4.2. Wallet Snapshots

Before any transaction, `EconomicSecurityService` reads current balances:

```php
$wallet = $this->getWalletSnapshot($userId);
if (!$wallet || $wallet['coins'] < $totalCost) {
    return ['success' => false, 'message' => 'Insufficient balance'];
}
```

- Prevents race conditions where balance changes between validation and update.
- For ultra-high concurrency, consider `SELECT ... FOR UPDATE`.

### 4.3. Amount & Resource Validation

- **Amounts**: Capped to 1–1000 (configurable) via `SecurityValidator::validateInteger()`.
- **Resource keys**: Regex `^[a-z0-9_]+$` prevents injection.
- **Impossible amounts**: SecurityMonitor blocks >1,000,000.

### 4.4. Velocity & Pattern Detection

- **Rapid-fire**: >5 transactions/sec triggers alert.
- **Suspicious pattern**: 3+ high/critical violations in 5 minutes triggers escalation.
- **Honeypot**: Access to `/api/shop/free-coins` auto-bans IP for 7 days.

---

## 5. Audit & Observability

### 5.1. Transaction Logs

`user_resource_logs` schema:
```sql
CREATE TABLE user_resource_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resource VARCHAR(50) NOT NULL,
    amount INT NOT NULL,
    balance_after INT NOT NULL,
    transaction_type VARCHAR(50) NOT NULL,
    reference_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id, created_at)
);
```

- Every resource movement is logged.
- Enables forensic reconstruction.
- Supports analytics (e.g., economic velocity).

### 5.2. Security Logs

`security_logs` captures:
- Invalid resource keys.
- Rate limit violations.
- Impossible transactions.
- Suspicious patterns.
- Honeypot triggers.

### 5.3. Rate Limits

`rate_limits` table tracks per-user/endpoint request counts in sliding windows.

---

## 6. Integration Points

### 6.1. Battle Pass & XP

- Quiz rewards call `BattlePassService::addXp()`.
- XP updates level (1000 XP/level) and unlocks rewards.
- Mission progress may also grant XP.

### 6.2. Daily Missions

- `MissionService::updateProgress()` increments counters.
- Auto-claims rewards when thresholds met.
- Rewards may include XP or resources.

### 6.3. Nonce/Honeypot Frontend

- Shop, quiz, multiplayer, and firms include hidden honeypot fields.
- Nonce values are generated per scope and refreshed on success.
- AJAX requests include both nonce and trap values.

---

## 7. Production Readiness Checklist

| Item | Status | Notes |
|------|--------|-------|
| Server-side pricing | ✅ | Settings-driven |
| Wallet snapshots | ✅ | Prevents races |
| Amount caps | ✅ | Configurable |
| Rate limiting | ✅ | Per-endpoint |
| Audit logging | ✅ | Full transaction trail |
| Nonce/replay protection | ✅ | Per-scope tokens |
| Honeypot traps | ✅ | Auto-ban |
| Security monitoring | ✅ | Pattern detection |
| DB transactions | ⚠️ | Single atomic updates; consider explicit transactions for multi-step |
| Row locks | ⚠️ | Not required for current load; add if high concurrency |

---

## 8. Performance Considerations

- **Read-heavy**: Wallet snapshots are cheap; consider caching for extreme scale.
- **Write-heavy**: All resource updates are single-row atomic operations.
- **Logs**: `user_resource_logs` can grow; implement periodic archival.
- **Rate limits**: In-memory table with sliding window; efficient for moderate traffic.

---

## 9. Threat Model Coverage

| Threat | Mitigation |
|--------|------------|
| Price manipulation | Server-side pricing in `EconomicSecurityService` |
| Amount tampering | Amount caps + SecurityMonitor |
| Resource injection | Regex validation + prepared statements |
| Replay attacks | NonceService + cooldowns |
| Rapid abuse | RateLimiter + velocity checks |
| Bot scraping | Honeypot + IP bans |
| Race conditions | Wallet snapshots (add row locks if needed) |
| Fraudulent transactions | Impossible amount detection + pattern aggregation |

---

## 10. Extensibility

- **New resources**: Add to `economy_resources` settings.
- **New transaction types**: Add methods to `GamificationService` + validation in `EconomicSecurityService`.
- **Custom rate limits**: Configure per-endpoint in controllers.
- **Additional fraud rules**: Extend `SecurityMonitor` with new pattern checks.

---

## 11. Code Quality & Maintainability

- **Separation of concerns**: Each service has a single responsibility.
- **Consistent error handling**: All methods return structured success/error arrays.
- **Prepared statements**: Used throughout; no SQL injection.
- **Configurable thresholds**: Cooldowns, rate limits, and caps are not hardcoded.

---

## 12. Conclusion

This gamification/economic subsystem is engineered for security, integrity, and observability. Multiple overlapping defenses ensure that client-side manipulation, replay attacks, and automated abuse are reliably blocked. The architecture is modular, allowing easy extension of new resources, transaction types, and fraud detection rules. With the addition of explicit database transactions for multi-step operations, the system is fully production-ready.
