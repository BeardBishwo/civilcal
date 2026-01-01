# Blueprint Vault File Management – Dual-File Upload, Watermarking & Preview Generation

## Overview
Unified dual-file upload pattern for Library (Blueprint Vault) and Bounty submissions. Users provide originals plus optional screenshots; system generates protected watermarked previews. Includes hash-based dedupe, quarantine storage, admin approval, payments/royalties, and payout flows.

## URLs (Base: http://localhost/Bishwo_Calculator)
- Library upload (auth): `POST /api/library/upload`
- Library download (auth, paid if not owner): `GET /api/library/download?id={fileId}`
- Library admin approval: `POST /api/admin/library/approve`
- Bounty submission (auth): `POST /api/bounty/submit`
- Bounty accept/reject (auth client): `POST /api/bounty/decide`
- Bounty admin review: `POST /api/admin/bounty/review`

## 1) Library Upload (Dual-File Ready)
- Frontend: `themes/default/views/library/upload.php` → POST `/api/library/upload`.
- Controller: `LibraryApiController::upload()`.
- Steps:
  1. Validate auth, file presence, size/extension.
  2. Compute SHA-256 hash; `LibraryFile::findByHash` to dedupe.
  3. Move to `storage/library/quarantine/`.
  4. Insert `library_files` with status='pending', file_hash stored.
- Preview: (optional) previews can be generated post-approval if desired; current flow relies on original for download; watermark service pattern can be reused.

## 2) Library Admin Approval & Migration
- Route: `POST /api/admin/library/approve`.
- Steps:
  1. Admin role check.
  2. Locate file in quarantine; move to `storage/library/approved/{type}`.
  3. Update status='approved'.
  4. Reward uploader (coins by type: CAD≈200, PDF≈30, default≈100) via `User::addCoins`.

## 3) Library Download with Payment & Royalty
- Controller: `LibraryApiController::download()`.
- Steps:
  1. Auth; ensure file approved.
  2. Check purchase history (user_transactions); if already purchased or uploader, skip charge.
  3. If not purchased: deduct `price_coins` from buyer via `User::deductCoins`; pay royalty (5 coins) to uploader via `User::addCoins`.
  4. Increment downloads; stream file from `storage/library/approved/{type}`.

## 4) Bounty Submission – Dual-File Upload & Preview
- Frontend: bounty/show.php form → `POST /api/bounty/submit`.
- Controller: `BountyApiController::submit()`.
- Steps:
  1. Validate upload; compute SHA-256 hash; dedupe via `BountySubmission::findByHash`.
  2. Move main file to `storage/bounty/quarantine/`.
  3. Preview generation paths:
     - **Auto-preview (Scenario A)**: if main file is jpg/png/pdf → `WatermarkService::createDirtyPreview()` to generate 800px watermarked preview.
     - **User-provided screenshot (Scenario B)**: if `preview_file` uploaded (for CAD/Excel), watermark that via `WatermarkService`.
  4. Insert `bounty_submissions` with `file_path`, `preview_path`, `file_hash`, statuses pending.

## 5) Bounty Acceptance & Payout
- Controller: `BountyApiController::clientDecide()`.
- Steps:
  1. Verify requester owns bounty; bounty status=open.
  2. If accept: release escrow to uploader via `User::addCoins`; mark submission `client_status='accepted'`; close bounty `status='filled'`.
  3. If reject: update client_status accordingly.

## 6) Watermark & Preview Generation (Shared Service)
- Service: `WatermarkService::createDirtyPreview($source, $target)`.
- Imagick path:
  - Load (PDF first page supported).
  - Resize to max 800px.
  - Tile watermark text (`CivilCity.com`) every ~150x100 px.
  - Add diagonal banner “PREVIEW ONLY / UNPAID”.
  - Write to target (e.g., `public/previews/...`).
- GD fallback if Imagick unavailable (not for PDF).
- Ensures low-res, watermarked previews to protect originals.

## 7) Data Tables
- `library_files`: id, uploader_id, title, description, file_path, file_type, file_size_kb, price_coins, status, file_hash, downloads_count, created_at.
- `bounty_requests`: id, requester_id, title, description, bounty_amount (escrow), status, created_at.
- `bounty_submissions`: id, bounty_id, uploader_id, file_path, preview_path, file_hash, admin_status, client_status, created_at.
- `user_transactions`: audit of coin movements (escrow, purchases, royalties, rewards).
- `users`: coins balance.

## 8) Security & Validation
- Auth + CSRF on all state-changing routes.
- Hash-based dedupe (library/bounty) via SHA-256.
- Watermarked previews to guard IP pre-purchase/acceptance.
- Admin approvals gate public visibility/download.
- Rate limiting recommended on upload/approve/decide/download.

## 9) Quick QA Checklist
- Library upload: duplicate blocked; file in quarantine; pending status.
- Library approve: file moved to approved; status updated; uploader rewarded.
- Library download: single charge; royalty paid; download counter increments; file streams.
- Bounty submit: duplicate blocked; preview generated (auto or screenshot); submission pending.
- Bounty accept: escrow released; submission accepted; bounty closed.

## 10) Future Hardening
- Add virus/malware scanning in quarantine.
- Stronger preview degradation for CAD/PDF (rasterize + watermark).
- Signed URLs for downloads; CDN support for approved assets.
- Daily caps and anomaly detection for repetitive uploads/hashes.
