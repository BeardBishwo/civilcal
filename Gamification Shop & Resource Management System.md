# Gamification Shop & Resource Management System

## Overview
Server-authoritative economy for buying/selling resources, lifelines, and bundles, plus crafting and building with multi-layer security (IP ban, rate limits, nonces, validation). Covers shop flows, rewards, construction, crafting, selling, and security pipeline.

## 1) Routes (Base: http://localhost/Bishwo_Calculator)
- Shop UI: `/quiz/shop` (auth)
- Buy lifeline: `POST /api/shop/purchase`
- Buy resource: `POST /api/shop/purchase-resource`
- Sell resource: `POST /api/shop/sell-resource`
- Buy bundle: `POST /api/shop/purchase-bundle`
- Build city asset: `POST /api/city/build`
- Craft planks (sawmill): `POST /api/city/craft`

## 2) Resource Purchase Flow (Shop → Validation → Transaction)
- Frontend: `themes/default/views/quiz/gamification/shop.php` (confirm → AJAX POST `/api/shop/purchase-resource`).
- Controller: `GamificationController@purchaseResource`.
- Security layers:
  1) IP ban check (`SecurityValidator::isIpBanned`).
  2) Rate limit (10 req/min via `RateLimiter`).
  3) Nonce validation/consume (`NonceService::validateAndConsume`).
  4) Resource key whitelist & amount validation (`SecurityValidator`, `EconomicSecurityService`).
- Service: `GamificationService->purchaseResource(userId, resource)`.
- Economic validation: `EconomicSecurityService->validatePurchase()` checks whitelist, balance, price.
- Transaction: deduct coins, add resource (SQL transaction), log coin/resource changes.
- Response: returns new nonce for next request.

## 3) Lifeline Purchase
- Endpoint: `POST /api/shop/purchase` → `GamificationController@purchaseLifeline` → `LifelineService->purchase`.
- Server-side pricing table prevents client tampering.
- Transaction: deduct coins (user_resources), increment user_lifelines.

## 4) Bundle Purchase (Bulk Offers)
- Endpoint: `POST /api/shop/purchase-bundle` → `GamificationController@purchaseBundle` → `GamificationService->purchaseBundle`.
- Fetch bundle config from `settings` (economy_bundles).
- Validate balance, deduct coins, add bulk resources, log transaction.
- Admin config: `SettingsController@saveEconomy` updates bundle definitions.

## 5) Resource Selling (Materials → Coins)
- Endpoint: `POST /api/shop/sell-resource` → `GamificationController@sellResource` → `EconomicSecurityService->validateSell` → `GamificationService->sellResource`.
- Validations: resource key whitelist, inventory balance check, sell price calc, rate limit, IP ban, nonce.
- Transaction: remove resource, credit coins, log.

## 6) Crafting (Sawmill: Logs + Coins → Planks)
- Endpoint: `POST /api/city/craft` → `GamificationController@craft` → `GamificationService->craftPlanks`.
- Costs: logCost = qty, coinCost = qty*10, gain = qty*4 planks.
- Validate logs/coins; atomic update: deduct logs & coins, add planks; log all three movements.

## 7) Building Construction (City)
- Endpoint: `POST /api/city/build` → `GamificationController@build` → `GamificationService->constructBuilding`.
- Costs defined per type (house/road/bridge/tower) across multiple resources.
- Validate wallet for each resource; atomic deduction; insert `user_city_buildings` record.

## 8) Exam Rewards (Earned Resources)
- Trigger: `ExamEngineController` on submit → `GamificationService->processExamRewards`.
- Loop correct answers, map difficulty (easy/medium/hard) to rewards (coins, bricks, steel, etc.).
- Aggregate rewards, apply in one transaction (user_resources), log transactions.
- Side effects: `BattlePassService->addXp`, `MissionService->updateProgress`.

## 9) Security Pipeline (Defense-in-Depth)
- IP ban check (first gate).
- Rate limiting (per endpoint, e.g., 10 req/min) via `RateLimiter`.
- Nonce validation/consumption (quiz_sessions, 30m expiry) via `NonceService`.
- Resource key whitelist (`SecurityValidator::$validResources`).
- Amount validation (e.g., 1–1000) and impossible transaction checks (`SecurityMonitor::validateTransaction`).
- Replay/abuse logging to `security_logs` with auto-ban on critical events.

## 10) Data Touchpoints
- Tables: `user_resources`, `user_resource_logs`, `user_lifelines`, `settings` (economy_bundles), `quiz_sessions` (nonces), `security_logs`, `rate_limits`, `user_city_buildings`, mission/battle pass tables, bot_profiles (noted elsewhere).

## 11) What to Test
- Purchase resource: nonce required, rate limit enforced, coin deduction + resource increment, new nonce returned.
- Sell resource: rejects invalid key/insufficient inventory; credits coins, deducts resource.
- Lifeline purchase: correct server-side pricing; inventory increments.
- Bundle purchase: uses settings pricing; applies bulk; logs transaction.
- Crafting: fails without logs/coins; succeeds with correct deltas and logs.
- Build: per-type cost validation; atomic deduction; building record created.
- Exam rewards: aggregated payout; correct difficulty mapping; logs; XP/mission updates.
- Security: replay nonce rejected; rate limits trigger; security logs written; IP ban blocks.

## 12) Quick URLs
- Shop: http://localhost/Bishwo_Calculator/quiz/shop
- Buy resource: http://localhost/Bishwo_Calculator/api/shop/purchase-resource (POST)
- Sell resource: http://localhost/Bishwo_Calculator/api/shop/sell-resource (POST)
- Buy lifeline: http://localhost/Bishwo_Calculator/api/shop/purchase (POST)
- Buy bundle: http://localhost/Bishwo_Calculator/api/shop/purchase-bundle (POST)
- Build: http://localhost/Bishwo_Calculator/api/city/build (POST)
- Craft planks: http://localhost/Bishwo_Calculator/api/city/craft (POST)

## 13) Future Hardening
- Add per-user daily caps on buy/sell volume; dynamic pricing to deter arbitrage.
- WebSocket/long-poll for shop inventory updates if needed.
- More granular audit trails (geo/IP/device) for high-value transactions.
- Admin dashboards for anomaly detection (sudden spikes, repeat failures).
