# Bounty System and Library Resource Management – Dual Marketplace Architecture

## Overview
Two coin-driven marketplaces: (1) Bounty System for paid work requests with escrowed coins, submissions, admin/client review, and payouts; (2) Library Resource Management for paid resource uploads, admin approval, royalties, and downloads. Both share coin economy, duplicate detection via SHA-256 hashes, watermarked previews, and security (auth, CSRF, nonce, honeypot, rate limits).

## URLs (Base: http://localhost/Bishwo_Calculator)
**Bounty (public/auth):**
- View bounties: `/bounty`
- Create bounty: `/bounty/create` (form), API: `POST /api/bounty/create`
- View bounty: `/bounty/view/{id}`
- Submit work: button on bounty view → API: `POST /api/bounty/submit`
- Client decision (accept/reject): API: `POST /api/bounty/decide`

**Bounty Admin:**
- Admin requests page: `/admin/bounty/requests`
- Pending list API: `GET /api/admin/bounty/pending`
- Review decision API: `POST /api/admin/bounty/review`

**Library (public/auth):**
- Library index: `/library`
- Upload form: `/library/upload`, API: `POST /api/library/upload`
- Download API: `GET /api/library/download` (auth; checks purchase/royalty)

**Library Admin:**
- Pending files page: `/admin/library/requests`
- Approve API: `POST /api/admin/library/approve`

## Bounty System Flows
### 1) Create Bounty (Escrow)
- Controller: `Api\BountyApiController::create`
- Steps:
  1) Auth user; parse title/desc/amount.
  2) Balance check via `User::getCoins`.
  3) Deduct coins (escrow) via `User::deductCoins(reason="Created Bounty: {title}")`.
  4) Insert `bounty_requests` (status='open').
- Data: `bounty_requests` (title, desc, bounty_amount, requester_id, status, created_at), `user_transactions` log.
- Security: auth, CSRF, validation of amount; dedupe not needed here but rate limiting recommended.

### 2) Submit Work (With Hash & Preview)
- Controller: `Api\BountyApiController::submit`
- Steps:
  1) Validate file upload; compute SHA-256 hash.
  2) Duplicate check via `BountySubmission::findByHash(hash)`.
  3) Move file to `/storage/bounty/quarantine/`.
  4) Generate watermarked/low-res preview (if jpg/png/pdf) via `WatermarkService` → `/public/previews/`.
  5) Insert `bounty_submissions` with `admin_status='pending'`, `client_status='pending'`.
- Data: `bounty_submissions` (hash, paths, statuses), `user_transactions` untouched until acceptance.
- Security: auth, file validation, hash dedupe, CSRF.

### 3) Admin Review
- Routes: `/admin/bounty/requests` (view) → `/api/admin/bounty/pending` (list) → `/api/admin/bounty/review` (decision).
- Model: `BountySubmission::getPendingAdminReview`, `updateAdminStatus`.
- Decision: approve/reject; rejection reason optional (if supported); status update in `bounty_submissions`.

### 4) Client Decision & Payout
- Controller: `Api\BountyApiController::clientDecide`
- Steps:
  1) Validate client owns bounty; bounty status must be 'open'.
  2) Accept: release escrow to uploader via `User::addCoins(uploader_id, bounty_amount, reason)`, mark submission `client_status='accepted'`, set `bounty_requests.status='filled'`.
  3) Reject: update `client_status` (optionally store reason).
- Data: `user_transactions` logs payout; bounty/submission statuses updated.

## Library System Flows
### 5) Upload Resource (Pending Approval)
- Controller: `Api\LibraryApiController::upload`
- Steps:
  1) Auth + file type/size validation.
  2) Compute SHA-256 hash; `LibraryFile::findByHash` for dupes.
  3) Move to `/storage/library/quarantine/`.
  4) Insert `library_files` with `status='pending'`.
- Data: `library_files` (uploader_id, title, desc, file_path, hash, status, price_coins, downloads_count).

### 6) Admin Approval & Reward
- Admin view: `/admin/library/requests`; Approve API: `/api/admin/library/approve`.
- Steps:
  1) Validate admin; fetch pending.
  2) Move file from quarantine to approved dir.
  3) Update status='approved'.
  4) Reward uploader (e.g., 30–100 coins based on type) via `User::addCoins`.

### 7) Download with Payment & Royalty
- Controller: `Api\LibraryApiController::download`
- Steps:
  1) Auth; ensure file approved.
  2) Check purchase history in `user_transactions` (avoid double charge).
  3) If not purchased and not uploader: deduct `price_coins` from buyer; pay royalty (5 coins) to uploader.
  4) Increment downloads; stream file.
- Data: `user_transactions` for purchase and royalty; `library_files.downloads_count` increment.

## Coin Economy (Shared)
- `User::addCoins` / `User::deductCoins`: DB transaction with `user_transactions` audit.
- Used by: bounty escrow/payout, library rewards/royalties/purchases, other economy features.
- Balance checks before deductions; rollback on failure.

## Security & Validation
- Auth + CSRF on state-changing APIs.
- File upload validation + SHA-256 dedupe (bounty/library submissions).
- Watermark previews to protect assets pre-purchase.
- Honeypot endpoints (global) and security logging via `SecurityMonitor`.
- Rate limiting recommended on bounty submit, decision, library upload/download.

## Data Tables (key fields)
- `bounty_requests`: id, requester_id, title, description, bounty_amount, status, created_at.
- `bounty_submissions`: id, bounty_id, uploader_id, file_path, preview_path, hash, admin_status, client_status, created_at, updated_at.
- `library_files`: id, uploader_id, title, description, file_path, hash, status, price_coins, downloads_count, created_at.
- `user_transactions`: id, user_id, amount, reference_id, reason, created_at.
- `users`: coins balance, updated_at.

## Quick QA Checklist
- Bounty create: coin escrow deducted; record open.
- Bounty submit: hash dedupe; preview generated; submission pending admin.
- Admin review: pending list loads; approve/reject updates statuses.
- Client accept: escrow released to uploader; bounty closed; submission accepted.
- Library upload: hash dedupe; status pending; file in quarantine.
- Library approve: file moved; status approved; uploader rewarded.
- Library download: charges once; royalty paid; downloads increment; file served.

## Future Hardening
- Add per-user rate limits and daily caps on uploads/submissions.
- Quarantine virus scanning step before approval.
- Stronger preview degradation for PDFs/CAD (watermark + low-res conversion).
- Admin dashboards for duplicate and fraud detection (hash collisions, repeated buyers/uploader pairs).
