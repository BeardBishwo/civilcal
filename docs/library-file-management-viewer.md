# Library File Management System with Viewer Integration

## 0. Quick Map (code anchors)
- Upload form (AJAX) → `/api/library/upload` @themes/default/views/library/upload.php#33-128 → handler @app/Controllers/Api/LibraryApiController.php#63-215 → DB insert @app/Models/LibraryFile.php#16-39
- Admin pending list → `/api/library/browse?status=pending` (admin-only) @app/Controllers/Api/LibraryApiController.php#19-60 → view @themes/admin/views/library/requests.php#37-124
- Approve/Reject → POST `/api/admin/library/approve` @app/Controllers/Api/LibraryApiController.php#218-285 → rewards via User::addCoins
- Browse approved → GET `/api/library/browse` @app/Controllers/Api/LibraryApiController.php#19-60 → grid UI @themes/default/views/library/index.php#40-249
- Unlock (paid) → POST `/api/library/unlock` @app/Controllers/Api/LibraryApiController.php#288-350
- Download → GET `/api/library/download` @app/Controllers/Api/LibraryApiController.php#352-410 (auth); preview image → `/api/library/preview` @app/Controllers/Api/LibraryApiController.php#438-455
- Viewer entry → `/library/view/{id}` route @app/routes.php#2076 → controller @app/Controllers/ViewerController.php#10-108 → PDF template @themes/default/views/library/viewer/pdf.php

## 1. Purpose & Scope
- End-to-end blueprint/library system with upload, admin approval, monetization (coin unlocks), and multi-format viewing (PDF.js, PhpSpreadsheet, images).
- Security goals: dedupe by hash, quarantine uploads, watermark previews, gate premium downloads/unlocks, and guard admin-only operations.

## 2. Actors
| Actor | Capabilities | Guards |
| --- | --- | --- |
| Authenticated User | Upload files, browse approved items, unlock paid files, download owned/free items, view previews. | Auth required for upload/unlock/download. Pricing enforced server-side. |
| Administrator | Review pending uploads, approve/reject, move files, reward uploaders. | Admin middleware + controller check. |
| Viewer Layer | Renders previews (PDF.js, PhpSpreadsheet, images). | Consumes stream/preview endpoints; relies on backend permission checks. |

## 3. Data Model (key tables)
- `library_files`: uploader_id, title, description, file_path, file_type, file_size_kb, price_coins, status, file_hash, preview_path, downloads_count, timestamps.
- `library_unlocks`: user_id, file_id, cost (prevents re-purchase).
- `user_transactions`: logs coin adds/deductions (used by User model).
- `library_reviews` / `library_reports`: ratings/reporting (used in find()).

## 4. Storage Layout
- `storage/library/quarantine/` — raw uploads pending approval.
- `storage/library/approved/{type}/` — published files per type (cad/pdf/excel/etc.).
- `storage/library/previews/` — watermarked preview images.

## 5. API & Routes (frontend surfaces)
- `GET /api/library/browse` — approved list (pending only if admin). Returns JSON {files}.
- `POST /api/library/upload` — multipart: title, description, type, price, file, preview (required for cad).
- `POST /api/admin/library/approve` — JSON {file_id, action:'approve'|'reject', reason?}.
- `POST /api/library/unlock` — JSON {file_id}; deducts coins; grants unlock + uploader royalty.
- `GET /api/library/download?id=` — attachment stream; checks auth + unlock.
- `GET /api/library/preview?id=` — inline preview image stream.
- Viewer: `GET /library/view/{id}` → auto-routes to PDF/Excel/Image renderer; PDF.js template uses stream endpoint for pages.
- Frontend views: grid @themes/default/views/library/index.php; upload form @themes/default/views/library/upload.php; admin table @themes/admin/views/library/requests.php.

## 6. Flows
### 6.1 Upload → Quarantine
1) User submits FormData via AJAX (upload.php).  
2) `LibraryApiController::upload`: auth check; title required; size <15MB; extension allowlist; cad requires preview.  
3) Compute SHA-256; `LibraryFile::findByHash` blocks duplicates (self and cross-user).  
4) Save file to `storage/library/quarantine/lib_{uniqid}.ext` (fallback rename for Windows).  
5) Preview: save to `storage/library/previews/`; watermark text (Intervention Image v3; v2 fallback).  
6) Insert `library_files` status `pending`, price_coins, hash, preview_path.  
7) JSON success returned.

### 6.2 Admin Approval
1) Admin fetches pending via `/api/library/browse?status=pending` (non-admin fallback to approved).  
2) Approve: move file quarantine → `approved/{type}/`; update file_path; set status `approved`; reward uploader (cad=200, pdf=30, default=100 coins) via `User::addCoins`.  
3) Reject: set status `rejected`, store reason.  
4) Pending list currently unpaginated.

### 6.3 Unlock / Purchase
1) POST `/api/library/unlock` with file_id.  
2) If price ≤0 → success (free).  
3) If already unlocked → success.  
4) Otherwise: check balance, deduct coins (`User::deductCoins`), insert `library_unlocks`, credit uploader 50% royalty via `User::addCoins`, commit transaction.  
5) Client typically redirects to download.

### 6.4 Download
1) GET `/api/library/download?id=` with auth.  
2) Require file status `approved`.  
3) Uploader/admin bypass price; others must have unlock if price>0 (checks `library_unlocks`).  
4) Increment downloads_count; stream file with attachment headers.  
5) Error path uses `die()` plaintext (improvement area).

### 6.5 Viewer & Preview
- Preview images: `/api/library/preview?id=` serves stored preview inline.  
- ViewerController: loads file metadata; picks renderer by extension.  
  - Excel: PhpSpreadsheet `IOFactory::load` → HTML writer to browser.  
  - PDF: renders `library/viewer/pdf` template; PDF.js pulls from stream endpoint.  
  - Default: inline image render.  
- Stream endpoint `/api/library/stream?id=` returns file content with correct MIME (permissions light; could be tightened).

## 7. Business Rules & Invariants
- `library_files.status`: `pending` → (`approved` | `rejected` | `flagged` future). Approved files must reside in `approved/` before status flip.  
- Cad uploads must include preview; server enforces even if client bypasses UI.  
- Every priced download requires `library_unlocks` unless requester is uploader/admin.  
- Coin changes must go through `User::addCoins`/`deductCoins` to log `user_transactions`.  
- Duplicate file hashes blocked globally.

## 8. Security & Compliance
- Auth required: upload/unlock/download/approve. Admin guard double-checked.  
- Hash dedupe + extension/size validation on upload.  
- Watermarked previews deter piracy (text overlay, red, 45°).  
- JSON enforced on browse/upload/unlock/approve; download still uses plaintext errors.  
- Stream/preview endpoints lack strict permission checks (rely on obscurity); consider aligning with unlock rules.

## 9. Gaps / Risks / TODOs
- No antivirus scan; quarantine cleanup not automated.  
- Download/stream endpoints use `die()` with plaintext; inconsistent HTTP codes.  
- Unlock UI in grid doesn’t hide download button for priced files (download still enforced server-side).  
- Pending browse unpaginated; admin UI lacks preview thumbnails.  
- Stream endpoint lacks permission check; tighten to unlocked/owner/admin.  
- No rate limiting or captcha on upload/unlock.  
- Royalty %, rewards, price validations not configurable centrally.

## 10. QA Checklist
- Upload: allowed type/size, duplicate blocked, lands in quarantine with status `pending`, preview watermarked, hash stored.  
- Approval: moves file to `approved/{type}/`, updates path/status, awards coins, status flips to `approved`.  
- Unlock: deducts once, records `library_unlocks`, credits uploader 50%.  
- Download: requires auth; enforces unlock for priced files; increments downloads_count; streams correct file.  
- Viewer: PDF renders via PDF.js; Excel renders via PhpSpreadsheet; preview endpoint serves images; stream serves correct MIME.
