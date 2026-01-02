# Library File Management Complete Flow

## 0. Quick Map (with code anchors)
- Upload form submit → AJAX POST `/api/library/upload` @themes/default/views/library/upload.php#94-128  
- Upload handler → hash, dedupe, quarantine, preview watermark, DB insert @app/Controllers/Api/LibraryApiController.php#63-215 @app/Models/LibraryFile.php#16-39  
- Admin queue (pending) → `/api/library/browse?status=pending` @app/Controllers/Api/LibraryApiController.php#19-60; table view @themes/admin/views/library/requests.php#37-124  
- Approve/reject → moves file, updates path/status, rewards uploader @app/Controllers/Api/LibraryApiController.php#218-285 @app/Models/LibraryFile.php#124-134  
- Unlock (purchase) → coin deduction + royalty @app/Controllers/Api/LibraryApiController.php#288-350  
- Download → permission + unlock check, counter increment, stream @app/Controllers/Api/LibraryApiController.php#352-410  
- Preview/image stream → @app/Controllers/Api/LibraryApiController.php#438-455; Viewer routing @app/Controllers/ViewerController.php#10-84  
- Frontend catalog grid (filters, download/unlock buttons) @themes/default/views/library/index.php#40-249

## 1. Context & Goals
- Provide a secure, coin-based blueprint marketplace where users upload engineering resources and the admin team vets every asset before it becomes purchasable.
- Maintain a dual-directory storage strategy (quarantine vs. approved) with watermarking to discourage piracy and duplicate detection to protect the catalog.
- Integrate tightly with the shared coin economy so uploaders are rewarded and purchasers are charged exactly once.

## 2. Primary Actors
| Actor | Description | Key Permissions |
| --- | --- | --- |
| Authenticated User | Can upload resources, browse approved files, purchase unlocks, and download owned/free assets. | Must be logged in; uploads limited to allowed extensions & size. |
| Administrator | Reviews pending uploads, approves/rejects files, triggers file moves, and rewards uploaders. | Requires `admin` middleware. |
| Viewer (frontend layer) | Renders previews in-browser (PDF.js, image previews, table conversions). | Uses public endpoints gated by auth/unlock checks. |

## 3. Core Data & Storage
### 3.1 Database Tables (refer to schema under `database/`)
- `library_files`: Master record (uploader, metadata, file_path, preview_path, price_coins, status, hash, counters).
- `library_unlocks`: Tracks successful purchases (user_id, file_id, cost, timestamp) preventing double-charges.
- `library_reviews` / `library_reports`: Community feedback loops (ratings, reports) that can impact status.
- `user_transactions`: Ledger updated by `User::addCoins` / `User::deductCoins` to audit coin flow.

### 3.2 Storage Layout (under `storage/library/`)
| Directory | Purpose |
| --- | --- |
| `quarantine/` | Temporary holding area for newly uploaded binaries awaiting admin action.
| `approved/{type}/` | Publicly accessible files after approval (`cad`, `pdf`, `excel`, etc.).
| `previews/` | Watermarked thumbnails/screenshots displayed in catalog.

## 4. API Entry Points & Views
| Endpoint | Method | Controller Action | Notes |
| --- | --- | --- | --- |
| `/api/library/browse` | GET | `Api\LibraryApiController::browse()` | Returns approved (or pending for admins) resources with pagination. |
| `/api/library/upload` | POST | `Api\LibraryApiController::upload()` | Handles multipart uploads, hashing, quarantine storage, DB insert. |
| `/api/admin/library/approve` | POST | `Api\LibraryApiController::approve()` | Admin-only approve/reject operations; moves files and issues rewards. |
| `/api/library/unlock` | POST | `Api\LibraryApiController::unlock()` | Deducts coins, records unlock, credits uploader royalty. |
| `/api/library/download` | GET | `Api\LibraryApiController::download()` | Streams approved files after permission checks. |
| `/api/library/preview` | GET | `Api\LibraryApiController::previewImage()` | Serves stored preview images inline. |
| `/viewer/{id}` (router dependent) | GET | `ViewerController::view()` | Routes to PDF/Excel/image renderers. |

Frontend surfaces:
- `themes/default/views/library/index.php`: Catalog grid, filters, unlock & download buttons.
- `themes/default/views/library/upload.php`: Upload form with dynamic preview requirement for CAD.
- `themes/admin/views/library/requests.php`: Admin approval table fetching pending submissions.

### 4.1 Request/Response Contracts (API layer)
- **Upload** `POST /api/library/upload` (multipart): `title` (string, required), `description` (string), `type` (cad|excel|pdf|doc|image|other), `price` (int ≥0), `file` (required), `preview` (required for cad).  
  Responses: `200 {success:true,file_id}` or `4xx/5xx {success:false,message}`.
- **Browse** `GET /api/library/browse?type=&page=&status=`: `status=pending` respected only for admin; otherwise forced to `approved`. Returns `{success:true,files:[...]}`.
- **Approve/Reject** `POST /api/admin/library/approve` (JSON): `{file_id:int, action:'approve'|'reject', reason?}`.
- **Unlock** `POST /api/library/unlock` (JSON): `{file_id:int}`; returns `{success:true}` or `{success:false,message}`.
- **Download** `GET /api/library/download?id=`: attachment stream; server enforces auth + unlock.
- **Preview** `GET /api/library/preview?id=`: inline preview image stream; 404-style `die()` on missing.

## 5. Detailed Flow Narratives
### 5.1 Upload Pipeline (User → Quarantine)
1. **Auth & Form Input**
   - Upload form (`upload.php`) submits `FormData` to `/api/library/upload` with `title`, `description`, `type`, `price`, and files (`file`, optional `preview`).
2. **Validation & Constraints** (`LibraryApiController::upload()`)
   - Rejects unauthenticated calls (401).
   - Title required; file must be <15 MB and extension in `[dwg,dxf,pdf,xlsx,xls,xlsm,docx,doc,jpg,jpeg,png]`.
   - CAD uploads must include a preview image; JS toggles `required` attribute client-side and server double-checks.
3. **Duplicate Detection**
   - Computes SHA-256 hash; `LibraryFile::findByHash()` ensures no prior identical upload across all users.
4. **Quarantine Storage**
   - Moves binary to `storage/library/quarantine/lib_{uniqid}.ext`; resilient to Windows by retrying with `rename()`.
5. **Preview Handling & Watermark**
   - Preview images saved under `storage/library/previews/preview_{uniqid}.ext`.
   - Attempts text watermark via Intervention Image (v3 driver fallback to v2 static API).
6. **Persistence**
   - Inserts `library_files` row with status `pending`, price, hash, preview path.
7. **Response**
   - Returns JSON success, enabling frontend to redirect back to catalog.

### 5.2 Admin Approval Workflow
1. **Admin Queue**
   - Admin view hits `/api/library/browse?status=pending`; controller restricts to admins, otherwise defaults to approved-only list.
2. **Approve Action** (`LibraryApiController::approve()`)
   - Verifies admin privileges and pending status.
   - Moves file from `quarantine/` to `approved/{file_type}/` maintaining unique filename.
   - Direct SQL updates `library_files.file_path` because model lacks dedicated mutator.
   - Marks record `approved` via `LibraryFile::approve()` updating timestamps.
   - Rewards uploader coins based on type (CAD 200, default 100, PDF 30) using `User::addCoins()`.
3. **Reject Action**
   - Captures reason string, sets status `rejected`, stores admin note (for follow-up messaging).

### 5.3 Unlock / Purchase Flow
1. **Initiation**
   - Catalog UI may call `unlockFile(id)` (currently unused in grid but ready for locked content) sending JSON to `/api/library/unlock`.
2. **Server Sequence**
   - Ensures auth; fetches library file from DB.
   - If price ≤ 0: returns success (treated as free) and short-circuits.
   - Checks `library_unlocks` for existing record to prevent re-billing.
   - Validates coin balance; wraps deduction + unlock insert + royalty credit in DB transaction.
   - Uploader royalty = `floor(price * 0.5)` credited via `User::addCoins()`.
3. **Outcome**
   - On success, client typically redirects to download endpoint.
   - On failure (insufficient balance/transaction error) returns JSON error and rolls back.

### 5.4 Download Flow
1. **Entry**
   - GET `/api/library/download?id={id}` triggered by UI (download button always available; server enforces rules).
2. **Permission Checks**
   - User must be authenticated.
   - File must be `approved`.
   - If requester is uploader or admin → bypass coin check.
   - For priced assets: verifies matching row in `library_unlocks`; otherwise aborts with instructional message.
3. **Delivery**
   - Increments `downloads_count` via `LibraryFile::incrementDownloads()`.
   - Streams file with attachment headers (filename derived from title + extension).
4. **Error Handling**
   - Missing IDs or files terminate with plaintext errors; improvement opportunity for JSON consistency.

### 5.5 Preview & Viewer
1. **Previews**
   - `LibraryApiController::previewImage()` streams stored preview images inline (used by grid `<img>` tags referencing `/storage/library/{preview_path}`).
2. **ViewerController::view($id)`**
   - Prefers manual preview image; otherwise routes by `file_type`:
     - `pdf` → `renderPdf()` using PDF.js frontend (`themes/default/views/library/viewer/pdf.php`).
     - `excel` → PhpSpreadsheet converts to HTML via `renderExcel()`.
     - Default → Inline image renderer.
   - Additional fallback `stream()` endpoint serves binary for inline viewing when required.

### 5.6 Status Lifecycle & Invariants
- `library_files.status` transitions: `pending` → (`approved` | `rejected` | `flagged`).  
  - `approved`: file_path rewritten to `approved/{type}/...` and coins rewarded.
  - `rejected`: stored with admin note; file remains in quarantine until cleanup.
  - `flagged`: set by report threshold; should be hidden from browse (future).
- Invariants to maintain:
  - Every stored binary path must map to an existing file; approval must move/rename before status flip.
  - Every priced download must have a corresponding `library_unlocks` row unless requester is uploader/admin.
  - Coin mutations must log to `user_transactions` via `User::addCoins`/`deductCoins`.

### 5.7 Failure Modes & Handling (current vs. target)
- Upload: returns JSON with HTTP codes; duplicate or invalid type/size yields 4xx. Target: standardize error schema `{success:false, code, message}`.
- Approve/Reject: JSON errors; missing file or non-pending → message. Target: add audit log and quarantine cleanup on reject.
- Unlock: wrapped in DB transaction; rolls back on failure. Target: expose error codes for insufficient funds vs. already unlocked.
- Download/Preview: uses `die()` plaintext; target: JSON or themed error page and HTTP status.

## 6. Coin Economy Touchpoints
- **Upload Rewards**: Admin approval triggers `User::addCoins` with reasons like “Reward for uploading: {title}”.
- **Unlock Purchases**: `User::deductCoins` debits buyer; `User::addCoins` credits uploader royalty.
- **Ledger Integrity**: Every coin move logs into `user_transactions` enabling audits and preventing silent balance changes.

## 7. Security & Compliance Considerations
- Authentication enforced on all state-changing endpoints (`upload`, `unlock`, `download`, `approve`).
- Admin-specific guards prevent non-admins from accessing pending lists or approval actions.
- Hash-based dedupe blocks repeat uploads (even across users) but could be expanded with additional metadata heuristics.
- Preview watermarking mitigates screenshot leakage; PDFs currently rely on preview images or inline rendering without degradation.
- Input validation ensures safe extensions and size limits; consider server-side MIME checks for stronger assurance.
- Error responses sometimes leak plaintext details; aligning all APIs to JSON would improve client handling.

## 8. Operational & Edge Case Notes
- **Windows Compatibility**: Upload pipeline retries with `rename()` if `move_uploaded_file` fails (important for local dev on Windows/Laragon).
- **Missing Preview on CAD**: Server enforces requirement; frontend toggles `required`, preventing silent bypass.
- **Storage Cleanup**: Rejected files remain in quarantine unless separate cleanup job runs; consider scheduled purge.
- **Pending Pagination**: Admin browse currently returns full list; fine now but may need pagination later.
- **Download Error UX**: `die()` messages can expose raw strings; replacing with structured JSON or themed error pages is a future polish task.
- **Royalties**: Commission currently fixed at 50%; configurable constants would allow easy tuning.

## 9. Future Enhancements (Backlog)
1. Automated antivirus scanning in quarantine before approval.
2. Richer admin review UI with preview thumbnails and metadata sorting.
3. Unlock button integration on catalog cards (currently `downloadFile` is default even for priced assets).
4. Replace plaintext `die()` with JSON/API error wrappers and consistent HTTP codes.
5. Rate limiting on upload/unlock endpoints to curb abuse.
6. Signed URLs or expiring tokens for download streaming to prevent direct link sharing.

## 10. Quick Reference Checklist (QA)
- Uploads land in `library_files` with `status=pending`, unique hash, preview path set (CAD enforced).
- Approval moves binaries to `approved/` and updates DB path + status; uploader coin reward logged.
- Unlock deducts coins once, inserts `library_unlocks`, and credits uploader royalty atomically.
- Download denies access unless file is approved AND user is uploader/admin OR has unlock record (or file is free).
- Previews render correctly in catalog (watermark visible) and viewer routes match file type.

This document should equip developers to trace every step in the library lifecycle, debug coin inconsistencies, and plan enhancements without re-reading scattered controllers or views.
