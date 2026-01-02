# Bounty and Shop System Implementation: Dual Marketplace Architecture

## 0. Quick Map (code anchors)
- Bounty create (escrow lock): POST `/api/bounty/create` @app/Controllers/Api/BountyApiController.php#30-68 → model @app/Models/BountyRequest.php#16-33; frontend form @themes/default/views/bounty/create.php
- Bounty submit (engineer upload): POST `/api/bounty/submit` @app/Controllers/Api/BountyApiController.php#70-166 → model @app/Models/BountySubmission.php#16-67; watermark @app/Services/WatermarkService.php#16-75
- Bounty decide (client accept/reject): POST `/api/bounty/decide` (clientDecide) @app/Controllers/Api/BountyApiController.php#168-218 → updates submission + bounty status, releases coins via User::addCoins
- Bounty browse: GET `/api/bounty/browse` @app/Controllers/Api/BountyApiController.php#14-28 → lists open bounties
- Shop resource purchase: POST `/api/shop/purchase-resource` @app/Controllers/Quiz/GamificationController.php#175-230 → service @app/Services/GamificationService.php#247-282; security guards (rate limit, nonce, IP ban)
- Shop bundle purchase: POST `/api/shop/purchase-bundle` @app/Controllers/Quiz/GamificationController.php#286-330 → service @app/Services/GamificationService.php#324-361
- Shop item (badges/perks): POST `/api/shop/purchase` @app/Controllers/ShopController.php#77-123 (not in excerpt but referenced in codemap)
- Security services: RateLimiter @app/Services/RateLimiter.php, SecurityMonitor @app/Services/SecurityMonitor.php, EconomicSecurityService @app/Services/EconomicSecurityService.php, NonceService @app/Services/NonceService.php

## 1. Purpose & Scope
- Two coin-backed marketplaces:
  - **Bounty System**: Clients post paid requests with coins escrowed; engineers submit work with protected previews; clients accept/reject to release funds.
  - **Shop System**: Users buy resources, bundles, badges/perks using coins with strong anti-abuse checks.
- Shared wallet/ledger via `User::addCoins` and `User::deductCoins` with transaction logging.

## 2. Actors
| Actor | Capabilities | Guards |
| --- | --- | --- |
| Client (bounty requester) | Create bounty (locks coins), review submissions, accept/reject to release escrow. | Auth required; balance check on create; ownership enforced on decision. |
| Engineer (bounty submitter) | Upload work files with previews; protected via watermarking; duplicate detection. | Auth required; file validation + hash dedupe. |
| Shopper | Purchase resources/bundles/perks; coins deducted, inventory updated. | Auth, nonce, rate limiting, IP ban checks. |
| System security services | Monitor anomalies, rate limit, enforce nonce, log events. | Cross-cutting on shop/bounty endpoints. |

## 3. Data Model (implied tables)
- `bounty_requests`: requester_id, title, description, bounty_amount, status (`open|filled|closed`), timestamps.
- `bounty_submissions`: bounty_id, uploader_id, file_path, preview_path, file_hash, admin_status, client_status, rejection_reason, timestamps.
- `user_transactions`: coin ledger for all adds/deductions (used by User model).
- `ad_impressions` (shop unrelated; mention omitted). For shop, primary tables include `user_resources`, `user_resource_logs`, `shop_items`, `user_purchases` (badges/perks), and possibly `settings` for bundle configs.

## 4. Storage/Layout
- Bounty uploads: `storage/bounty/quarantine/` for originals; previews saved under `public/previews/` (watermarked/degraded).
- Shop: Uses database-backed inventories (`user_resources`), no binary assets.

## 5. Bounty System Flows
### 5.1 Create Bounty (Escrow Lock)
1) Client POST `/api/bounty/create` with title/description/amount.  
2) Auth required; amount > 0 check; balance check via `User::getCoins`.  
3) Coins deducted immediately (`User::deductCoins`) to escrow.  
4) Insert `bounty_requests` with status `open`.  
5) Respond with bounty id.

### 5.2 Submit Work (Engineer)
1) Auth required; multipart POST `/api/bounty/submit` with `bounty_id`, `file`, optional `preview_file`.  
2) Validate extension (`dwg,dxf,pdf,xlsx,xls,docx,doc,zip,rar,jpg,png`).  
3) Compute SHA-256 hash; `BountySubmission::findByHash` blocks duplicates (even cross-user).  
4) Save to `storage/bounty/quarantine/bounty_{uniqid}.ext`.  
5) Generate preview: auto-watermark for jpg/png/pdf via `WatermarkService::createDirtyPreview`; fallback to user-provided screenshot for CAD/Excel if provided.  
6) Insert `bounty_submissions` with admin_status/client_status `pending`, store preview path if generated.  
7) Respond JSON success.

### 5.3 Client Decision (Release or Reject)
1) Auth + ownership enforced; POST JSON to `clientDecide` with `submission_id` and `decision` (accept/reject).  
2) Accept path: ensure bounty still `open`; `User::addCoins` pays uploader bounty_amount; mark submission client_status `accepted`; set bounty status `filled`.  
3) Reject path: mark client_status `rejected` with reason.  
4) (Future) Provide secure download link for accepted work.

### 5.4 Browse/Open Listings
- GET `/api/bounty/browse` returns open bounties with requester info; pagination (limit 20).

## 6. Shop System Flows
### 6.1 Purchase Resource
1) POST `/api/shop/purchase-resource` with `resource`, `amount`, `nonce`.  
2) Guards: IP ban check, RateLimiter (`/api/shop/purchase-resource`), Nonce validation, resource key + amount validation, suspicious activity check.  
3) Service `purchaseResource` validates pricing via EconomicSecurityService, computes total cost, updates `user_resources` (coins--, resource++), logs transactions, returns new nonce.

### 6.2 Sell Resource
1) POST `/api/shop/sell-resource` with resource/amount.  
2) Guards: IP ban, rate limit, resource key & amount validation.  
3) Service `sellResource` validates ownership/limits, updates `user_resources` (coins++, resource--), logs transactions, returns new nonce.

### 6.3 Purchase Bundle
1) POST `/api/shop/purchase-bundle` with `bundle` key and nonce.  
2) Guards: IP ban, rate limit, bundle key validation, nonce.  
3) Service `purchaseBundle` loads bundle config, checks coins, updates `user_resources` (coins--, resource += qty), logs transactions.

### 6.4 Purchase Badge/Perk (ShopController)
1) POST `/api/shop/purchase` with `item_id`.  
2) Auth check; fetch shop item; prevent duplicate via `user_purchases`; ensure coin balance.  
3) Transaction: deduct coins (`User::deductCoins`), insert `user_purchases`, commit; respond success.

## 7. Security Layers (Cross-Cutting)
- **RateLimiter**: endpoint-level throttling to block abuse (`/api/shop/purchase-resource`, `/api/shop/purchase-bundle`, etc.).
- **NonceService**: one-time tokens consumed per shop action; prevents replay. 
- **SecurityMonitor / SecurityValidator**: IP bans, honeypot fields, suspicious activity detection, impossible transaction checks.
- **EconomicSecurityService**: server-side pricing, resource key allowlist, amount bounds, wallet snapshot; prevents client-side tampering.
- **WatermarkService**: degrades/marks previews to protect engineer IP in bounty submissions.

## 8. Status/Business Rules & Invariants
- Bounties: status transitions `open` → `filled` (on accept) | potential `closed` (manual). Coins remain escrowed until accept; rejection leaves bounty open (coins still locked; future enhancement: refund flow).
- Submissions: `admin_status` and `client_status` start `pending`; client decision only when bounty open; duplicate hashes blocked.
- Shop: All coin mutations must log; resource/bundle purchases require valid nonce and pass rate limiter; amounts must be within validated bounds.
- Wallet integrity: `User::addCoins` / `User::deductCoins` log to `user_transactions`; shop services also log to `user_resource_logs`.

## 9. Gaps / Risks / TODOs
- No admin review layer for bounty submissions (only client decision); no quarantine approval or malware scan.  
- No refund path on bounty rejection/expiry; escrow remains deducted.  
- No secure download endpoint for accepted bounty files; needs gated access.  
- Shop endpoints rely on POST form data; JSON variants and consistent error schema would help.  
- Bundle/resource configs are server-side but no dynamic pricing refresh endpoint.  
- No click/impression security for shop/bundle views (mostly server-side actions).  
- RateLimiter/Nonce not applied to bounty endpoints—consider adding to upload/decision.

## 10. Future Enhancements
1) Add admin moderation + virus scan for bounty submissions; auto-flag on duplicate or high-risk file types.  
2) Implement escrow refund/expiry flow and reminders; allow closing without accept.  
3) Secure download endpoint for accepted submissions with signed URLs and audit log.  
4) Add pagination/search to bounty browse; per-user dashboard (created bounties, submissions).  
5) Add click tracking and analytics for shop banners/items similar to impressions.  
6) Extend EconomicSecurityService to shop item purchases; unify error schema across endpoints.  
7) Add global rate limiting + captcha/honeypot on bounty submit/create to deter spam.

## 11. QA Checklist
- Bounty creation deducts coins and inserts `bounty_requests` with status `open`.  
- Submission upload enforces allowed extensions, dedup by hash, stores in quarantine, generates watermarked preview when possible.  
- Client accept releases escrow to uploader and marks bounty `filled`; reject updates status without payout.  
- Shop purchase flows reject invalid nonce/IP-banned/rate-limited requests; deduct coins and increment resources/bundles; logs written.  
- Selling resources increases coins and decrements inventory with logs.  
- Bundle purchases validate server-side config and wallet balance before atomic update.
