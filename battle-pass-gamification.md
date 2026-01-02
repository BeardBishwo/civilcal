# Battle Pass Gamification System — Deep Dive & Findings

## Purpose
Give the dev team a single detailed reference for the Battle Pass feature (UI, API, services, data) plus gaps/bugs to address.

## Scope
- Page render: GET `/quiz/battle-pass` (auth).
- Reward claim: POST `/api/battle-pass/claim` (auth, nonce, rate-limit, honeypot).
- XP accrual via quiz flow → BattlePassService.addXp.
- Data: seasons, rewards, user progress, claimed rewards, granted resources.

## Key Components
- **Routes**: `app/routes.php` @ lines ~2037-2051.
- **Controller**: `app/Controllers/Quiz/GamificationController.php` (battlePass, claimReward).
- **Service**: `app/Services/BattlePassService.php` (getProgress, addXp, claimReward, grantReward).
- **View**: `themes/default/views/quiz/gamification/battle_pass.php` (page, claim button JS).
- **Security helpers**: `NonceService` (one-time nonce per user+type), `RateLimiter`, `SecurityMonitor` (honeypot/nonce logs).

## Data Model (in use)
- **battle_pass_seasons**: `id`, `is_active`, metadata.
- **battle_pass_rewards**: `id`, `season_id`, `level`, `is_premium`, `reward_type`, `reward_value`.
- **user_battle_pass**: `user_id`, `season_id`, `current_xp`, `current_level`, `claimed_rewards` (JSON array), `is_premium_unlocked` (implied, not always present!).
- **quiz_sessions**: stores nonce/session for CSRF-style protection; columns include `nonce`, `user_id`, `quiz_type`, `is_consumed`, `created_at`.
- **user_resources**: bricks/cement/steel/coins columns updated on reward grant.
- **user_lifelines**: lifeline inventory (INSERT/UPSERT).
- **user_city_buildings**: buildings granted.

## Flows (current behavior)
### Page load (GET `/quiz/battle-pass`)
1) Controller calls `BattlePassService->getProgress(userId)`.
2) Service fetches active season; if none, returns null (controller does not handle null — see findings).
3) Service ensures `user_battle_pass` row exists (auto-insert if missing) and decodes `claimed_rewards` JSON.
4) Controller renders view with `progress`, `rewards`, `season`, and `claimNonce` (generated via `NonceService` with type `battle_pass_claim`).

### XP earning (quiz completion)
1) `GamificationService->rewardUser` (after correct answers) calls `BattlePassService->addXp(userId, amount)`.
2) `addXp` loads active season and user progress, increments `current_xp`, computes `current_level = floor(xp/1000)+1`, and updates row. No level-up side effects implemented (placeholder comment only).

### Reward claim (POST `/api/battle-pass/claim`)
1) Inputs: `reward_id`, `nonce`, `trap_answer` (honeypot).
2) Reject if honeypot filled → log critical.
3) Validate nonce (one-time, 30m expiry, user + type bound).
4) Rate limit: 5 requests / 60s per user.
5) `BattlePassService->claimReward`:
   - Requires active season (no explicit null check).
   - Fetch reward; must belong to active season.
   - Checks level requirement vs `current_level`.
   - Checks already claimed.
   - Checks premium flag vs `is_premium_unlocked` (field may be missing).
   - Calls `grantReward` to deliver resources.
   - Appends reward_id to claimed_rewards JSON and updates row.
6) On success, controller issues new nonce for next claim.

### Grant reward (resource distribution)
- For resource types `bricks|cement|steel|coins`: `UPDATE user_resources SET {col} = {col} + :val WHERE user_id = :uid` (no row existence check).
- `lifeline`: UPSERT quantity in `user_lifelines`.
- `building`: INSERT into `user_city_buildings` with level=1.
- No DB transaction around grant + claimed update.

## Security & Anti-abuse
- Auth middleware implied on routes.
- Nonce (one-time), honeypot, and rate-limiter on claim API.
- SecurityMonitor logging on honeypot and nonce issues.

## Observed Gaps / Bug & Error Findings
1) **No active season handling**
   - `getProgress` returns null when no active season; controller dereferences `$data['progress']` → fatal error. Need graceful empty state.
   - `claimReward` assumes season exists; would throw/notice.
2) **Missing/nullable `is_premium_unlocked`**
   - `claimReward` checks `$progress['is_premium_unlocked']` but `getProgress` never ensures this key exists; DB column may be null/absent → PHP notice and premium check bypass risk. Should default to `0`/false.
3) **No transaction on claim**
   - Grant + claimed update not wrapped in a DB transaction; partial writes can occur if grant succeeds but update fails (or vice versa), causing double-claim or lost reward consistency.
4) **Resource grant assumes existing rows**
   - `user_resources` update will fail silently/no-op if user row missing; need ensure row exists or use UPSERT.
5) **Season mismatch edge**
   - `claimReward` uses `findOne` on rewards but only checks season equality; if season null or stale nonce, error messaging is generic. Consider clearer 404/410 responses.
6) **Level/XP integrity**
   - `addXp` uses `floor(xp/1000)+1`; no cap or season reset guard. Could over-level beyond available rewards; not inherently wrong but may need cap or carry-over rules.
7) **Rate limit scope**
   - Fixed 5/60s per user; no IP dimension. Might be okay but consider IP-based abuse for unauth flows (not needed here since auth required).
8) **Nonce table growth**
   - Cleanup exists (`NonceService::cleanup`) but requires cron; ensure scheduled.
9) **Honeypot value not validated for emptiness in view**
   - Relies on hidden field; ensure front-end keeps it empty. (Minor.)
10) **No premium purchase/unlock path documented here**
    - `is_premium_unlocked` gating exists but unlocking flow not in scope; document/implement elsewhere.
11) **No concurrency guard on claim**
    - Multiple rapid claims could race: without transaction and unique constraint on claimed_rewards, duplicate grant risk (e.g., double insert for building if array not updated in time). Consider DB-level constraint per user+reward.
12) **Error surfacing**
    - Exceptions in claim return raw message; could leak internal text. Consider standardized error codes/messages.
13) **Progress init JSON**
    - `claimed_rewards` seeded as `'[]'`; JSON decode result is array, but update uses `json_encode` without `JSON_UNESCAPED_UNICODE`—fine; ensure DB column large enough.

## Recommendations (high level)
- Add graceful empty-state when no active season (both page and claim).
- Ensure `user_battle_pass` has `is_premium_unlocked` default 0; in PHP, coalesce to false.
- Wrap `claimReward` grant + claimed update in a DB transaction; re-query/relock to prevent races.
- Ensure `user_resources` row exists or convert to UPSERT before resource updates.
- Add unique constraint or separate table for claimed rewards to prevent duplicates; or serialize claims with FOR UPDATE.
- Harden error responses (no raw exception), add logging with context.
- Document/implement premium unlock flow or remove premium gate until ready.
- Add cron entry for `NonceService::cleanup` if not present.
- Optional: add telemetry for claim success/fail by reason; add level-cap logic if desired.

## Test Ideas / Coverage
- No active season: page renders empty state; claim returns clear error.
- Claim with missing/false `is_premium_unlocked` for premium reward → blocked.
- Duplicate claim same reward → blocked.
- Level too low → blocked.
- Race: two concurrent claims same reward → exactly one succeeds (transaction + constraint).
- Resource grant when user_resources row absent → row created/updated correctly.
- XP add and level calc across boundaries (999→1000, 1999→2000).
- Nonce reuse/replay → rejected; rate limit exceeded → 429.
- Honeypot filled → blocked and logged.
