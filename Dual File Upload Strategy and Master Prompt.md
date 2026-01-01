# Dual File Upload Strategy and Master Prompt

## Executive Summary
Browsers cannot render DWG/XLS natively on shared hosting. The solution: enforce a dual-file upload (source + preview) and shift rendering work to uploaders. Use existing libraries for watermarking and PDF-to-image conversion, keeping server load low and previews safe.

## 1) Dual-File Upload Rule
- **Main Asset (hidden)**: The paid source file (e.g., `.dwg`, `.xlsx`, `.doc`). Stored in secure, non-public storage.
- **Preview File (visible)**: A PDF or JPG snapshot shown publicly (watermarked). Required for Blueprint Vault and Bounty System.
- **Benefits**: Mobile-friendly previews, no heavy server-side CAD/XLS rendering, predictable performance on shared hosting.

## 2) Watermark Engine Workflow (api/submit_bounty.php)
1) User uploads `Hospital_Plan.dwg` (asset) + `Hospital_View.pdf` (preview).
2) Storage:
   - Asset → safe folder outside webroot (e.g., `/home/user/safe_storage/`).
   - Preview → temp folder for processing.
3) Processing:
   - Take Page 1 of preview PDF.
   - Convert to JPG (Spatie PDF-to-Image or ImageMagick) and overlay tiled watermark: "PREVIEW ONLY / UNPAID".
   - Save as `preview_hospital.jpg` in public previews folder.
4) Display: Show watermarked JPG.
5) Purchase: After payment (e.g., 500 coins), generate secure download link for original asset.

## 3) Required Libraries (Composer)
| Purpose | Library | Command |
| --- | --- | --- |
| Image watermarking | intervention/image | `composer require intervention/image` |
| PDF → Image | spatie/pdf-to-image | `composer require spatie/pdf-to-image` |
| Excel rendering (if needed elsewhere) | phpoffice/phpspreadsheet | `composer require phpoffice/phpspreadsheet` |

## 4) Storage & Security
- Store originals strictly outside `public_html`.
- Use SHA-256 hash for deduplication of source files.
- Serve only watermarked previews publicly.
- Use PDO prepared statements for all DB operations; sanitize outputs for XSS.

## 5) Database Tables (from prior blueprint)
- `shop_items`: id, name, category, price_coins, price_cash, image_path.
- `library_files`: id, uploader_id, title, file_path, file_hash, price_coins, status.
- `bounty_requests`: id, requester_id, title, bounty_amount, status.
- `bounty_submissions`: id, bounty_id, uploader_id, file_path, file_hash, status.
- `notifications`: id, user_id, message, is_read.
- Additional human-element tables (if not yet created): `resource_reviews`, `notifications` (detailed), `referrals`.

## 6) API Endpoints (Key Tasks)
- **api/buy_item.php**
  - Input: item_id.
  - Transaction: begin; deduct coins from user_resources; add item to inventory; log transaction; commit.

- **api/upload_file.php** (Vault & Bounty)
  - Inputs: `source_file` + `preview_file`.
  - Validation: extension whitelist; SHA-256 hash on source; reject duplicates.
  - Processing:
    1) Move source to safe storage.
    2) Convert preview PDF page 1 to JPG.
    3) Apply tiled watermark via Intervention Image.
    4) Save watermarked JPG to `public/previews/`.
  - Record: insert DB row with paths, hash, price/status metadata as needed.

- **api/decide_bounty.php**
  - When client accepts submission: transfer locked coins client → uploader; unlock download permission for client.

## 7) Cron Job
- **cron/daily_reset.php**
  - Reset daily limits (ads watched, etc.).
  - Compute Top 10 Engineers leaderboard; cache to JSON file for quick reads.

## 8) Preview Pipeline Details
- **PDF previews**: Spatie PDF-to-Image or ImageMagick `convert -density 150 input.pdf[0] -quality 90 output.jpg`.
- **Watermarking**: Intervention Image to overlay tiled/semi-transparent text/logo across preview JPG.
- **DWG**: Always rely on uploader-provided PDF/JPG snapshot; no server-side DWG rendering.
- **Excel**: If preview needed, render limited HTML via PhpSpreadsheet; otherwise require PDF/JPG snapshot per dual-upload rule.

## 9) Action Checklist
- Enforce dual-upload form fields (asset + preview) for Vault/Bounty.
- Install Composer packages above; ensure ImageMagick available.
- Implement SHA-256 dedupe for source files.
- Implement watermark pipeline and cache previews.
- Create/verify DB tables listed.
- Secure download links post-purchase/acceptance.

## 10) Optional: Master Prompt (ready to use)
You can feed the following to an AI coding agent:
- Role: Senior Backend Engineer (PHP 8, MySQL, secure architecture).
- Constraints: Shared hosting (LiteSpeed), low CPU, utf8mb4, secure storage outside webroot.
- Security: PDO prepared statements; XSS sanitization; SHA-256 dedupe; CSRF/rate limits as per existing middleware.
- Libraries: intervention/image, spatie/pdf-to-image, phpoffice/phpspreadsheet.
- Tasks: implement APIs (buy_item, upload_file with dual-upload and watermarking, decide_bounty), cron/daily_reset, DB tables above.
- Deliver: working endpoints with secure file handling and watermarked previews.
