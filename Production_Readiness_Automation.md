# Production Readiness Automation: Daily Resets, Leaderboards & Gamification (Current State vs. Planned)

**Date:** 2025-12-30  
**Purpose:** Deep-dive analysis of missing automation infrastructure and current gamification systems. Highlights gaps between implemented real-time features and required batch/cron jobs for production scale.  
**Scope:** Daily resets, leaderboard caching, mission/battle pass progression, economic security, and required cron infrastructure.

---

## 1. Executive Summary

### 1.1. Current State
- **Real-time systems**: Daily login bonuses, leaderboard updates, battle pass XP, mission progress—all triggered by user actions.
- **No batch automation**: All time-based features (daily resets, leaderboard caching, cleanup) are documented but **not implemented**.
- **Scalability risks**: Real-time leaderboard calculation will crash under load; daily limits never reset.

### 1.2. Critical Gaps
| Feature | Current Implementation | Production Requirement |
|--------|------------------------|------------------------|
| Daily Resets | User-triggered only (login bonus) | Cron at 3 AM to reset limits |
| Leaderboard Caching | Real-time calculation per read | Pre-computed cache refreshed every 2 hours |
| Mission/Battle Pass | Real-time progression | Daily reset of mission progress |
| Data Cleanup | None | Weekly archival of logs |
| Tool Rotation | Static | Daily rotation via cron |

---

## 2. Current Gamification Systems (Real-Time)

### 2.1. Daily Login Bonus & Streak

**Trigger**: User visits quiz portal (`PortalController@index`).

**Flow**:
```php
$gs = new \App\Services\GamificationService();
$dailyBonus = $gs->processDailyLoginBonus($_SESSION['user_id']);
```

**Logic**:
- Compare `last_login_reward_at` with today.
- If yesterday: increment streak; else reset to 1.
- Day 7: Grant 10 steel; else: grant 1 log.
- Update `users.last_login_reward_at` and `login_streak`.

**Issues**:
- No automated reset at midnight.
- Streak logic depends on user visiting portal; if they skip a day, streak breaks even if they login later.

### 2.2. Leaderboard Real-Time Updates

**Trigger**: Exam completion (`ExamEngineController@submit`).

**Flow**:
```php
$lbService = new \App\Services\LeaderboardService();
$lbService->updateUserRank($userId, $score, $totalQuestions, $correctAnswers);
```

**Logic**:
- Generate periods (weekly/monthly/yearly).
- Upsert aggregates with moving average accuracy.
- On read (`LeaderboardController@index`): calculate ranks on-the-fly.

**Performance Issue**:
- `getLeaderboard()` runs `ORDER BY total_score DESC` and computes ranks per request.
- No caching; will degrade with >10k users.

### 2.3. Battle Pass XP & Rewards

**Trigger**: Quiz reward (`GamificationService@rewardUser`).

**Flow**:
```php
if ($resource === 'xp') {
    $bp = new BattlePassService();
    $bp->addXp($userId, $amount);
}
```

**Logic**:
- Add XP to current total.
- Recalculate level (1000 XP/level).
- Update `user_battle_pass`.

**Missing**:
- Daily mission reset.
- Seasonal reset automation.

### 2.4. Daily Mission Progress

**Trigger**: Quiz reward (`GamificationService@rewardUser`).

**Flow**:
```php
$ms = new MissionService();
$ms->updateProgress($userId, 'solve_questions');
```

**Logic**:
- Increment progress for active missions.
- Auto-claim rewards when completed.
- Grant XP and coins.

**Missing**:
- Daily reset of mission progress.
- Mission rotation.

---

## 3. Economic Security & Transaction Validation

### 3.1. Security Layers

| Layer | Implementation |
|-------|----------------|
| IP Ban | Checked in controllers |
| Rate Limiting | `RateLimiter` per endpoint |
| Nonce Validation | `NonceService` for state changes |
| Input Sanitization | `SecurityValidator` |
| Server-side Pricing | `EconomicSecurityService` |
| Fraud Detection | `SecurityMonitor` (impossible amounts, rapid-fire) |

### 3.2. Transaction Flow Example (Purchase)

1. **Controller** (`GamificationController@purchaseResource`)
   - IP ban check.
   - Rate limit (10/min).
   - Nonce validation.
2. **Service** (`GamificationService@purchaseResource`)
   - Calls `EconomicSecurityService@validatePurchase()`.
3. **Security** (`EconomicSecurityService`)
   - Resource key sanitization.
   - Server-side price lookup.
   - Wallet snapshot check.
   - `SecurityMonitor@validateTransaction()`.
4. **Database**
   - Atomic `UPDATE user_resources`.
   - `logTransaction()`.

**Strengths**:
- Multiple overlapping defenses.
- Full audit trail.
- Server authority.

**Considerations**:
- Row locks not used; acceptable for current load.
- Rate limits stored in DB; could be in-memory for scale.

---

## 4. Missing Automation Infrastructure

### 4.1. Cron Directory (Absent)

- **Current**: No `cron/` directory exists.
- **Planned**: Scripts for daily reset, leaderboard cache, cleanup.

### 4.2. Daily Reset Script (Not Implemented)

**Planned Logic** (`cron/daily_reset.php`):
```php
// Reset daily limits
$db->query("UPDATE user_resources SET daily_ads_watched = 0");
$db->query("UPDATE user_resources SET daily_login_claimed = 0");

// Reset daily missions
$db->query("DELETE FROM user_mission_progress WHERE DATE(created_at) < CURDATE()");

// Rotate Tool of the Day
$tools = ['brickwork', 'concrete', 'earthwork'];
$newTool = $tools[array_rand($tools)];
SettingsService::set('tool_of_the_day', $newTool);
```

**Schedule**: `0 3 * * *` (3 AM daily).

### 4.3. Leaderboard Cache Script (Not Implemented)

**Planned Schema**:
```sql
CREATE TABLE leaderboard_cache (
    id INT PRIMARY KEY AUTO_INCREMENT,
    period_type ENUM('weekly','monthly','yearly') NOT NULL,
    period_value VARCHAR(20) NOT NULL,
    category_id INT NULL,
    top_users JSON NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Planned Logic** (`cron/update_leaderboard_cache.php`):
- Query top 100 per period/category.
- Store as JSON in `leaderboard_cache`.
- Update `rank_current`/`rank_previous` in aggregates.

**Schedule**: `0 */2 * * *` (every 2 hours).

### 4.4. Data Cleanup Script (Not Implemented)

**Planned Logic** (`cron/cleanup.php`):
- Archive `user_resource_logs` older than 30 days.
- Purge expired nonces.
- Clear old security logs.

**Schedule**: Weekly.

---

## 5. Production Readiness Checklist

| Component | Status | Action Required |
|-----------|--------|-----------------|
| Daily Reset | ❌ Not implemented | Create `cron/daily_reset.php` and schedule |
| Leaderboard Cache | ❌ Not implemented | Create cache table and update script |
| Mission Reset | ❌ Not implemented | Include in daily reset |
| Tool Rotation | ❌ Not implemented | Include in daily reset |
| Log Cleanup | ❌ Not implemented | Create cleanup script |
| Cron Infrastructure | ❌ No directory | Create `cron/` directory and scripts |
| Economic Security | ✅ Implemented | Monitor for high traffic |
| Battle Pass Reset | ❌ Not implemented | Plan seasonal reset |

---

## 6. Implementation Plan

### 6.1. Phase 1: Core Automation (Priority: Critical)

1. **Create `cron/` directory**.
2. **Daily Reset Script**:
   - Reset `daily_ads_watched`, `daily_login_claimed`.
   - Clear `user_mission_progress`.
   - Rotate `tool_of_the_day`.
3. **Schedule**: 3 AM daily via system cron.

### 6.2. Phase 2: Performance (Priority: High)

1. **Leaderboard Cache Table**:
   ```sql
   CREATE TABLE leaderboard_cache (...)
   ```
2. **Cache Update Script**:
   - Pre-compute top 100 per period.
   - Update `rank_current`/`rank_previous`.
3. **Update LeaderboardService**:
   - Read from cache.
   - Fallback to real-time if cache stale.

### 6.3. Phase 3: Maintenance (Priority: Medium)

1. **Cleanup Script**:
   - Archive logs >30 days.
   - Purge expired nonces.
2. **Seasonal Reset**:
   - Reset battle pass progress.
   - Archive old season data.

---

## 7. Security Considerations for Cron Scripts

- **Access Control**: Scripts should be executable only by web server user.
- **Error Handling**: Log errors; do not expose stack traces.
- **Transaction Safety**: Wrap multi-step operations in DB transactions.
- **Idempotency**: Scripts should be safe to re-run.

---

## 8. Monitoring & Alerting

### 8.1. Cron Job Monitoring

- **Success**: Log completion with timestamp.
- **Failure**: Alert if script does not complete within expected time.
- **Missed Runs**: Alert if no log entry for expected run time.

### 8.2. Performance Metrics

- **Leaderboard Cache**: Track query time and cache hit rate.
- **Daily Reset**: Monitor execution time and rows affected.
- **Economic Security**: Alert on spike in violations.

---

## 9. Database Schema Adjustments

### 9.1. Add Missing Fields

```sql
-- For daily reset tracking
ALTER TABLE user_resources ADD COLUMN daily_ads_watched INT DEFAULT 0;
ALTER TABLE user_resources ADD COLUMN daily_login_claimed TINYINT(1) DEFAULT 0;

-- For leaderboard caching
ALTER TABLE quiz_leaderboard_aggregates ADD COLUMN rank_current INT NULL;
ALTER TABLE quiz_leaderboard_aggregates ADD COLUMN rank_previous INT NULL;
```

### 9.2. Indexes for Performance

```sql
CREATE INDEX idx_leaderboard_cache_period ON leaderboard_cache(period_type, period_value);
CREATE INDEX idx_user_resources_daily ON user_resources(user_id);
```

---

## 10. Code Snippets for Implementation

### 10.1. Daily Reset Script (`cron/daily_reset.php`)

```php
<?php
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Services/SettingsService.php';

$db = \App\Core\Database::getInstance();

try {
    $db->getPdo()->beginTransaction();

    // Reset daily limits
    $db->query("UPDATE user_resources SET daily_ads_watched = 0");
    $db->query("UPDATE user_resources SET daily_login_claimed = 0");

    // Reset daily missions
    $db->query("DELETE FROM user_mission_progress WHERE DATE(created_at) < CURDATE()");

    // Rotate Tool of the Day
    $tools = ['brickwork', 'concrete', 'earthwork'];
    $newTool = $tools[array_rand($tools)];
    \App\Services\SettingsService::set('tool_of_the_day', $newTool);

    $db->getPdo()->commit();
    echo "Daily reset completed: " . date('Y-m-d H:i:s') . "\n";
} catch (Exception $e) {
    $db->getPdo()->rollBack();
    echo "Daily reset failed: " . $e->getMessage() . "\n";
    exit(1);
}
```

### 10.2. Leaderboard Cache Update (`cron/update_leaderboard_cache.php`)

```php
<?php
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Services/LeaderboardService.php';

$db = \App\Core\Database::getInstance();
$lbService = new \App\Services\LeaderboardService();

$periods = [
    'weekly' => date('Y-W'),
    'monthly' => date('Y-m'),
    'yearly' => date('Y')
];

foreach ($periods as $type => $value) {
    $topUsers = $lbService->getTopUsers($type, $value, 100);
    $cacheData = json_encode($topUsers);

    $db->query("
        INSERT INTO leaderboard_cache (period_type, period_value, top_users)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE top_users = VALUES(top_users), updated_at = NOW()
    ", [$type, $value, $cacheData]);
}

echo "Leaderboard cache updated: " . date('Y-m-d H:i:s') . "\n";
```

---

## 11. Testing Strategy

### 11.1. Unit Tests

- **Daily Reset**: Verify limits reset and mission progress cleared.
- **Leaderboard Cache**: Verify cache populated and read correctly.
- **Economic Security**: Ensure validation still works after batch updates.

### 11.2. Integration Tests

- **End-to-End**: Simulate user login after midnight to ensure bonus claim works.
- **Load Test**: Verify leaderboard cache improves performance under load.

### 11.3. Manual QA

- **Cron Execution**: Manually run scripts and verify DB changes.
- **Schedule**: Confirm system cron executes at expected times.
- **Failover**: Test behavior if a cron run fails.

---

## 12. Rollback Plan

- **Scripts**: Keep previous version in `cron/backup/`.
- **Database Changes**: Use migrations for safe rollback.
- **Configuration**: Revert settings via admin panel.

---

## 13. Conclusion

The gamification systems are well-implemented for real-time interaction but lack critical batch automation for production scale. Implementing the outlined cron infrastructure will eliminate scalability risks, ensure daily features reset correctly, and improve leaderboard performance. The economic security layer is robust and ready for production. Priority should be given to implementing daily resets and leaderboard caching before launch.
