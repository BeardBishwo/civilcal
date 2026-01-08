# Admin Media Management Workflow – Upload, Storage, and Modal Interface

## Overview
End-to-end admin media system: full library page, reusable media modal, upload/optimization, usage detection, sync of untracked files, bulk cleanup, and routes/APIs with localhost URLs. Includes separate WatermarkService note for bounty previews.

## URLs (Base: http://localhost/Bishwo_Calculator)
- Admin media library page: `http://localhost/Bishwo_Calculator/admin/content/media` (auth+admin)
- Full-page upload: `POST http://localhost/Bishwo_Calculator/admin/content/media/upload` (auth+admin)
- Sync untracked files: `POST http://localhost/Bishwo_Calculator/admin/content/media/sync` (auth+admin)
- Bulk cleanup unused: `POST http://localhost/Bishwo_Calculator/admin/content/media/bulk-cleanup` (auth+admin)
- Media modal browse API: `http://localhost/Bishwo_Calculator/admin/api/media` (auth+admin)
- Media modal upload API: `POST http://localhost/Bishwo_Calculator/admin/api/media/upload` (auth+admin)

## 1) Admin Media Library Page (Grid + Usage)
- Route: GET `/admin/content/media` → `ContentController@media`.
- Data flow:
  1. Fetch media with filters/pagination: `Media::getAll()` builds WHERE + LIMIT.
  2. Usage detection: `Media::getUsageInfo()` scans pages (content/meta), menus JSON, settings values to flag Used/Unused.
  3. Transform for view: formats sizes, builds URLs, attaches usage map.
  4. Render `themes/admin/views/content/media.php` grid with badges and actions.

## 2) Full-Page Upload Flow (Drag & Drop / Button)
- Frontend: `media.php` posts FormData (`files[]`) to `/admin/content/media/upload`.
- Controller: `ContentController@uploadMedia`.
- Steps:
  1. Validate auth/admin + CSRF; check mime/size whitelist.
  2. Save to `public/storage/media/{type}/` via `move_uploaded_file`.
  3. Image optimization pipeline:
     - Instantiate `ImageOptimizer`.
     - Compress original (JPEG quality).
     - Generate 150px thumbnail.
     - Generate medium (800px) and optional WebP.
  4. Insert DB record via `Media::create` with metadata (paths, dimensions, hashes if present).
  5. Return JSON for UI refresh.

## 3) Media Modal (Reusable Picker)
- Used in editors (e.g., menu editor TinyMCE button).
- Flow:
  1. `MediaModal.open(callback)` (e.g., menu_edit.php) → modal init → `loadMedia(1)`.
  2. AJAX GET `/admin/api/media?page=N&search=...` → `MediaApiController@index` paginates and returns JSON.
  3. Modal renders grid and click selects item → `callback(item.url)`.
  4. Quick upload inside modal: hidden file input posts to `/admin/api/media/upload`; on success auto-selects uploaded item.

## 4) Sync Untracked Files (Disk → DB)
- Route: `POST /admin/content/media/sync` → `ContentController@syncMedia`.
- Logic:
  - `Media::findUntrackedFiles()` scans storage folders (`media/images`, `media/documents`, themes assets) recursively.
  - For each file not in DB, create media record (relative path, type inferred, size).
  - Returns count of synced items.

## 5) Bulk Cleanup (Unused Media)
- Route: `POST /admin/content/media/bulk-cleanup` → `ContentController@bulkDeleteUnused`.
- Steps:
  1. Fetch up to 1000 media via `Media::getAll`.
  2. `Media::getUsageInfo()` to mark Used/Unused.
  3. For each unused: delete physical file (`unlink`) then delete DB record.
  4. Respond with deleted count.

## 6) Data Model Touchpoints
- `media` table: filename, type, size, paths (original/thumbnail/webp), created_at.
- Usage lookup across: `pages` (content/meta), `menus` (items JSON), `settings` (values).
- File storage: `public/storage/media/{images,documents,...}`; previews/thumbnails alongside.

## 7) Security & Validation
- Auth + admin middleware on all admin routes/APIs.
- CSRF on POSTs.
- Mime/size whitelist on uploads; sanitize filenames.
- Optional hash checking (if enabled) to detect duplicates.
- For modal upload, same validations apply.

## 8) WatermarkService (Separate, Bounty Use)
- `WatermarkService` (Imagick) used in bounty submissions to generate low-res watermarked previews (not part of admin media flow). Generates 800px previews, tiled watermark, diagonal banner; saves to public previews.

## 9) Quick Test Checklist
- Library page loads, shows usage badges; pagination works.
- Upload (full page): valid files accepted; thumbnail/medium generated; DB row created; UI updates.
- Modal browse: loads items, pagination/search works; select returns URL; upload in modal auto-selects new item.
- Sync: adds missing disk files to DB.
- Cleanup: removes unused media physically and in DB; used files remain.

## 10) Future Hardening
- Virus scan on upload/quarantine for documents.
- Rate limits on upload/sync/cleanup endpoints.
- Hash-based dedupe for images/docs; expose duplicate warnings in UI.
- Optional CDN paths and signed URLs for restricted assets.
- Soft-delete media with restore window before physical deletion.
