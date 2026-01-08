# Gamification System — Battle Pass, Shop, City Builder & Sawmill

Single-file deep brief for engineers covering the four gamification pillars tied to quiz completion: rewards pipeline, battle pass, shop/economy, city builder, and sawmill crafting. Anchored to current code (GamificationService, BattlePassService, MissionService, EconomicSecurityService, GamificationController, ShopController, ExamEngineController) and codemap “Gamification System: Battle Pass, Shop, City Builder & Sawmill”.

## 1) Objectives & Success Criteria
- Convert quiz performance into multi-resource rewards (coins/materials/XP) with anti-fraud safeguards.
- Sustain progression via seasonal battle pass and missions.
- Provide buy/sell economy with pricing validation and audit logs.
- Support resource sinks: building construction and crafting transformations.
- Preserve integrity: nonce + CSRF, honeypot, rate limiting, atomic DB updates, transaction logs.

## 2) Core Components (inventory)
- **GamificationService**: reward distribution on quiz submit; wallet ops; shop purchases; city build; sawmill craft; daily login bonus; transaction logging.
- **BattlePassService**: XP add, reward claim validation, grant rewards, claimed_rewards JSON updates.
- **MissionService**: mission progress increments (solve_questions, etc.).
- **EconomicSecurityService**: server-side pricing/amount validation, balance checks, fraud detection gate.
- **Controllers**:
  - `Quiz\ExamEngineController` → `processExamRewards()` on submit.
  - `Quiz\GamificationController` → endpoints for build, craft, purchase, claim battle pass rewards.
  - `ShopController` → shop index + purchase APIs.
  - `Quiz\PortalController` → daily login bonus trigger.
- **Views/UI**: battle_pass.php (Alpine claimReward), shop.php (buy/sell), city.php (build), sawmill.php (process logs→planks).
- **Data**:
  - `user_resources` wallet (coins, bricks, cement, steel, wood_logs, wood_planks, etc.).
  - `user_battle_pass` (current_xp, current_level, claimed_rewards JSON).
  - `user_city_buildings` (building_type, level).
  - `daily_quiz_schedule` reward_coins; quiz_attempts/answers for reward trigger context.
  - Settings: economy_resources (pricing), bundles, cash_packs.

## 3) Key Flows (happy paths)
1) **Quiz completion → rewards**  
   ExamEngineController.submit → GamificationService.processExamRewards(user_id, correctAnswersList, attemptId)  
   - Computes XP/coins/materials by difficulty.  
   - Updates user_resources in batch.  
   - Calls BattlePassService.addXp; MissionService.updateProgress('solve_questions'); leaderboard handled separately.  
   - Logs transactions.
2) **Battle Pass claim**  
   Frontend claimReward(id) → POST /api/battle-pass/claim → GamificationController (nonce/trap/rate limit) → BattlePassService.claimReward  
   - Validates level, premium flag, claimed status.  
   - grantReward updates wallet (materials/coins/lifeline/building, etc.).  
   - Persists claimed_rewards array.
3) **Shop purchase**  
   shop.php trade() → POST /api/shop/purchase-resource (or general purchase) with nonce + CSRF + honeypot → GamificationController.purchaseResource → EconomicSecurityService.validatePurchase → GamificationService.purchaseResource  
   - Validates resource config and price from economy_resources; caps quantity; checks wallet.  
   - Atomic SQL: deduct coins, add resource; log audit entries; returns new nonce.
4) **City builder (construction)**  
   city.php build(type, cost) → POST /api/city/build → GamificationController.build → GamificationService.constructBuilding  
   - Ensures wallet exists; validates required materials per building type; atomic deduction; inserts user_city_buildings row; logs deductions.
5) **Sawmill crafting (logs→planks)**  
   sawmill.php process(quantity) → POST /api/city/craft → GamificationController.craftPlanks → GamificationService.craftPlanks  
   - Calculates fee (10 coins per log) and yield (1 log → 4 planks).  
   - Atomic transaction: deduct logs + coins, add planks; logs all three changes.
6) **Daily login bonus**  
   PortalController loads → GamificationService.processDailyLoginBonus(user_id)  
   - Checks last_login_reward_at, maintains streak; Day 7 grants 10 steel else 1 log; updates users table; logs transaction.

## 4) Security & Integrity Mechanisms
- Auth required on all gamification endpoints; honeypot traps on forms; nonces per action (quiz/shop/battle-pass).
- Rate limiting and IP ban checks in GamificationController for shop/battle pass flows.
- EconomicSecurityService validates pricing and balances; SecurityMonitor fraud checks on transactions.
- Atomic SQL updates for wallet mutations (purchase, craft, build); transaction logs for each delta.
- Quiz reward path depends on JSON cache integrity; submit enforces nonce + user binding.

## 5) Data & Config Notes
- **Wallet (user_resources)**: coins, bricks, cement, steel, wood_logs, wood_planks, plus other materials; ensure defaults exist via initWallet.
- **Battle pass**: xp_per_level (implicit constant), claimed_rewards JSON array; rewards can be materials, lifelines, buildings, coins.
- **Economy settings**: economy_resources (buy/sell prices, icons), economy_bundles, economy_cash_packs from settings table.
- **Building costs**: hardcoded map in GamificationService (e.g., house: 100 bricks + 20 planks).
- **Crafting**: fee 10 coins/log; yield 4 planks/log; quantity capped (validation clamps).

## 6) Risks / Gaps
- Battle pass claimed_rewards stored as JSON array; risk of race causing double-claim without DB constraints.  
- Wallet operations rely on atomic UPDATE but not explicit DB transactions across multi-step flows (shop, craft, build).  
- Fraud detection relies on SecurityMonitor; coverage of edge cases unknown.  
- Economy settings integrity: missing/zero prices could bypass purchase; validation mitigates but config drift is possible.  
- No per-user rate limit on construct/craft; potential resource exploits if endpoints spammed.  
- Reward formulas hardcoded; not versioned per season; lacks audit for XP grants.  
- Quiz reward inputs trust difficulty_level from questions; tampering possible if cache compromised.

## 7) Suggested Improvements / Backlog
1) Add DB transactions around multi-statement wallet/build/craft flows.  
2) Introduce unique constraint or pessimistic lock for battle pass claim to prevent double-claim.  
3) Add per-endpoint rate limits (purchase/build/craft/claim) and idempotency tokens for claim/purchase.  
4) Sign or checksum quiz attempt JSON; optionally store incremental answer backups in DB.  
5) Externalize economy/build/craft configs to admin-managed settings with validation schema.  
6) Expand transaction logging with correlation IDs (attempt_id, nonce_id) and user agent/IP for audits.  
7) Add monitoring metrics: reward failures, fraud blocks, claim success rate, shop transaction volumes.  
8) Unit/E2E tests: battle pass claim, shop purchase validation, craft/build atomicity, processExamRewards payout correctness.

## 8) Endpoints (current)
- Quiz rewards: POST `/quiz/submit` (ExamEngineController → GamificationService.processExamRewards).  
- Battle pass: POST `/api/battle-pass/claim`.  
- Shop: GET `/shop`, POST `/api/shop/purchase` (and resource-specific), GET `/api/shop/items`.  
- City builder: POST `/api/city/build`.  
- Sawmill: POST `/api/city/craft`.  
- Daily login bonus: implicit via portal load (PortalController -> GamificationService.processDailyLoginBonus).

## 9) Open Questions
- Should wallet updates move to stored procedures or explicit SQL transactions to guarantee atomicity across multi-resource changes?  
- Should economy/battle pass rewards become versioned per season with migration support?  
- Do we need per-resource audit caps or anomaly alerts (e.g., >X planks/day)?  
- Should crafting/building respect premium tiers or cooldowns?  
- How to reconcile quiz reward multipliers with battle pass XP pacing (caps/soft caps)?
