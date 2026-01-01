# Library & Bounty System – Dual Marketplace

## Overview
Two coin-based marketplaces sharing the same economy and security stack. Library = resource trading with admin approval, duplicate detection, royalties, and watermarked previews. Bounty = paid work requests with escrow, submissions, admin/client review, and payouts.

## URLs (Base: http://localhost/Bishwo_Calculator)
**Library (auth):**
- Browse: `/library`
- Upload: `/library/upload` (form) → API `POST /api/library/upload`
- Download: API `GET /api/library/download?id={fileId}`

**Library Admin:**
- Pending requests: `/admin/library/requests`
- Approve: `POST /api/admin/library/approve`

**Bounty (auth):**
- List/Dashboard: `/bounty`
- Create bounty: `/bounty/create` (form) → API `POST /api/bounty/create`
- View bounty: `/bounty/view/{id}`
- Submit work: API `POST /api/bounty/submit`
- Client decision: API `POST /api/bounty/decide`

**Bounty Admin:**
- Pending submissions: `/admin/bounty/requests`
- List API: `GET /api/admin/bounty/pending`
- Review API: `POST /api/admin/bounty/review`

## Library Flows
### 1) Upload (Hash + Quarantine)
- Controller: `Api\LibraryApiController::upload`
- Steps:
  1. Auth, validate file type/size/metadata.
  2. Compute SHA-256 hash; `LibraryFile::findByHash` to prevent duplicates.
  3. Move to `/storage/library/quarantine/`.
  4. Insert `library_files` with status='pending'.

### 2) Admin Approval
- View: `/admin/library/requests` → Approve API.
- Steps:
  1. Verify admin.
  2. Move file quarantine → approved dir.
  3. Update status='approved'.
  4. Reward uploader (e.g., 30–100 coins) via `User::addCoins`.

### 3) Download + Payment + Royalty
- Controller: `Api\LibraryApiController::download`
- Steps:
  1. Auth; ensure approved.
  2. Check purchase history (user_transactions) to avoid double-charge.
  3. If not purchased and not uploader: deduct `price_coins`; pay royalty (5 coins) to uploader.
  4. Increment downloads; stream file.

## Bounty Flows
### 4) Create Bounty (Escrow)
- Controller: `Api\BountyApiController::create`
- Steps: balance check → deduct coins (escrow) via `User::deductCoins` → create `bounty_requests` (status='open').

### 5) Submit Work (Hash + Preview)
- Controller: `Api\BountyApiController::submit`
- Steps: validate upload → hash (SHA-256) → duplicate check via `BountySubmission::findByHash` → move to `/storage/bounty/quarantine/` → generate watermarked/low-res preview (Imagick `WatermarkService`) for jpg/png/pdf → insert `bounty_submissions` with admin_status/client_status pending.

### 6) Client Decision & Payout
- Controller: `Api\BountyApiController::clientDecide`
- Steps: verify requester owns bounty and status=open → if accept: release escrow to uploader (`User::addCoins`) → mark submission accepted → close bounty (status=filled). Reject path updates client_status.

### 7) Admin Review (Safety)
- Routes: `/admin/bounty/requests`, APIs `/api/admin/bounty/pending`, `/api/admin/bounty/review`.
- Approve/reject updates `bounty_submissions.admin_status`.

## Coin Economy (Shared)
- `User::addCoins` / `User::deductCoins` use DB transactions and log to `user_transactions`.
- Used by: bounty escrow/payout, library rewards/royalties/purchases.

## Security
- Auth + CSRF on state-changing routes; admin middleware where required.
- Hash-based dedupe on uploads (library & bounty submissions).
- Watermarked previews (library via preview generation if added; bounty via `WatermarkService`).
- Rate limiting recommended on upload/review/decision endpoints.
- Storage segregation: quarantine for uploads; approved/public dirs for served files.

## Key Tables
- `library_files`: id, uploader_id, title, description, file_path, file_type, file_size_kb, price_coins, downloads_count, status, file_hash, created_at.
- `bounty_requests`: id, requester_id, title, description, bounty_amount (escrow), status, created_at.
- `bounty_submissions`: id, bounty_id, uploader_id, file_path, preview_path, file_hash, admin_status, client_status, created_at.
- `user_transactions`: audit of all coin moves.
- `users`: coins balance (updated_at).

## Quick QA Checklist
- Library upload: hash dedupe works; file in quarantine; status pending.
- Library approve: moves file; status approved; uploader rewarded.
- Library download: charges once; pays royalty; download counter increments; file streams.
- Bounty create: coins escrowed; bounty open.
- Bounty submit: hash dedupe; preview generated; submission pending admin/client.
- Bounty admin review: pending list loads; approve/reject updates status.
- Bounty accept: escrow released; submission accepted; bounty filled.

## Future Hardening
- Virus scan in quarantine; stronger preview degradation for PDFs/CAD.
- Daily caps and rate limits on uploads/submissions.
- Duplicate alerting dashboard (hash collisions, repeat patterns).
- Signed URLs/CDN for approved downloads; soft-delete with grace period.
