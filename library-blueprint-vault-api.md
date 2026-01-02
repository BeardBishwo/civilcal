# Library API — Blueprint Vault File Marketplace (Deep-Dive)

## Purpose
Define the current/target behavior of the Blueprint Vault marketplace APIs covering browse, upload, preview watermarking, admin approval, unlock transactions, and secure download.

## Scope
- Public browsing of approved library files.
- Authenticated uploads with duplicate detection.
- Optional preview uploads with watermarking.
- Admin approval workflow with coin rewards.
- Coin-based unlock (purchase) with uploader commission.
- Download with permission checks and analytics.
- Out of scope: UI design details, pricing strategy changes, external payment rails.

## Actors & Roles
- **Guest**: Can browse approved items.
- **Authenticated User**: Can upload files, purchase/unlock paid files, download if permitted.
- **Admin**: Can view pending uploads, approve/reject, and move files.
- **Uploader**: Original file owner; always allowed to download own files.

## Data Model (key tables)
- `library_files`: `id`, `uploader_id`, `title`, `description`, `file_path`, `file_type`, `price`, `status` (`pending|approved`), `hash`, `downloads_count`, timestamps.
- `library_unlocks`: `id`, `user_id`, `file_id`, `price_paid`, timestamps (unique on `user_id + file_id`).
- `users`: includes `coins`, `role/admin flag`, `username`.
- `user_transactions`: records coin debits/credits with reason.
- Storage paths:
  - Upload quarantine: `storage/library/quarantine/{filename}`
  - Approved: `storage/library/approved/{file_type}/{filename}`
  - Previews: `storage/library/previews/{previewFilename}` (watermarked)

## Endpoints (current behavior)
- **GET `/api/library/browse`**
  - Query: `page`, `type`, optional `status` (admin-mode only).
  - Guest: only `approved`. Admin: `pending|approved`.
  - Returns paginated files with uploader username.
- **POST `/api/library/upload`** (auth required)
  - Accepts file + metadata (`title`, `description`, `price`, optional `preview`).
  - Validates extension: `dwg,dxf,pdf,xlsx,docx,jpg,png`.
  - SHA-256 duplicate detection (per hash). Same uploader re-uploads get short-circuit; different uploader blocked.
  - Stores main file to quarantine; optional preview stored and watermarked.
  - Creates DB row with `status=pending`.
- **POST `/api/admin/library/approve`** (admin only)
  - Moves file from quarantine → approved/{type}/
  - Updates status to `approved`.
  - Awards coins to uploader: CAD=200, PDF=30, else default 100.
- **POST `/api/library/unlock`** (auth required)
  - Checks file exists/approved, not already unlocked by user.
  - Validates coin balance.
  - Transaction: deduct buyer coins; insert `library_unlocks`; pay 50% commission to uploader; commit.
- **GET `/api/library/download`** (auth required)
  - Loads file metadata; requires: uploader OR admin OR (approved & paid → unlocked).
  - For paid files, verifies unlock record.
  - Increments `downloads_count`; streams file with headers.

## Flows (detailed)
### Browse (public)
1) Parse `page`, `type`, `status`.
2) If non-admin, force `status=approved`.
3) Query `library_files` + `users.username`.
4) Return `{success, files}`.

### Upload + Preview
1) Auth check; require `file`.
2) Extension allowlist check.
3) Hash file; `findByHash`:
   - If existing by same uploader → short-circuit success (optional enhancement: return existing id).
   - If existing by others → reject duplicate.
4) Move main file to quarantine path.
5) Optional `preview` (image only): move to previews dir; watermark with "PREVIEW ONLY - UNPAID" (center, 48px, red, 50%, 45°); overwrite preview.
6) Insert DB row: status `pending`, store hash, file/preview paths, price.

### Admin Approval + Reward
1) Admin loads pending via `status=pending&admin_mode=true`.
2) Approve: check admin; move file quarantine → approved/{type}; update status.
3) Reward uploader coins (type-based); log transaction.

### Unlock (purchase)
1) Auth; fetch file; ensure approved.
2) Reject if already unlocked.
3) Ensure coins >= price.
4) Begin transaction:
   - Deduct buyer coins (+ transaction log).
   - Insert `library_unlocks`.
   - Pay uploader 50% commission (+ transaction log).
5) Commit; respond success.

### Download
1) Auth; find file; ensure approved.
2) Permission: uploader? admin? else require unlock if price > 0.
3) Increment download counter.
4) Stream file with correct headers; fail if missing file.

## Validation & Error Handling
- Input validation: required fields, extension allowlist, price numeric/non-negative.
- Upload limits: rely on PHP upload_max_filesize/post_max_size; consider explicit size caps per type.
- Duplicate handling: hash-based; consider surfacing existing file info.
- Transactions: wrap unlock flow in DB transaction; rollback on any failure.
- File operations: check existence before move/rename/read; fail with 4xx/5xx as appropriate.

## Security & Access Control
- Auth guard on upload/unlock/download; admin guard on approve/browse pending.
- Path construction: sanitize filenames; prevent directory traversal.
- Paid download: enforce unlock check before streaming.
- Disable PHP error display in API responses; log exceptions.
- Consider rate limiting uploads/unlocks to mitigate abuse.
- Ensure previews are watermarked and are the only public-facing asset for paid CAD/PDF.

## Performance & Pagination
- Browse uses paginated query with optional type filter.
- Add indexes: `library_files.status`, `library_files.hash`, `library_unlocks.user_id+file_id`.
- Consider caching popular browse queries if needed.

## Analytics & Logging
- `downloads_count` increment on successful download.
- `user_transactions` captures coin debits/credits for audit.
- Add structured logging for approvals, unlock failures, and permission denials.

## Risks / Edge Cases
- Missing file on disk after approval → return 500; consider repair job.
- Concurrent unlocks: unique constraint on `library_unlocks` prevents double-purchase; ensure proper error surface.
- Watermark dependency: Intervention Image v3 primary; v2 fallback. Validate presence in deployment.
- Large files: streaming with proper headers; consider chunked or range support if needed later.

## Open Questions for the Team
1) Pricing rules: allow zero-price files? dynamic tiers?
2) Refunds/chargebacks: any reversal path for unlocks?
3) Reject flow: how are pending uploads rejected/notified?
4) Preview enforcement: should preview be mandatory for CAD/PDF?
5) Quota & moderation: limits per user/day? virus scanning?
6) Admin audit trail: do we need explicit approval logs?

## Suggested Next Steps
- Confirm open questions and update acceptance rules.
- Add tests for: duplicate upload, watermark creation, approval moves file, unlock transaction rollback, paid download permission.
- Validate DB indexes and unique constraints (`library_unlocks`).
- Harden error responses (consistent JSON, no raw PHP errors).
