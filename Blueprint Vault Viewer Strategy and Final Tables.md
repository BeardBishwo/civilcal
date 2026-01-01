# Blueprint Vault Viewer Strategy and Final Tables

## Executive Summary
Browsers cannot render `.dwg` (AutoCAD) or `.xlsx` files natively; they will download them. To deliver in-browser previews on shared hosting, rely on proven libraries instead of custom rendering. This document outlines recommended viewers for Excel and PDF, a pragmatic DWG strategy, required Composer packages, and the final database tables (reviews, notifications, referrals).

## 1) Excel Viewer Strategy (`.xlsx`, `.xls`)
- **Backend Library:** `phpoffice/phpspreadsheet` (Composer) — industry standard in PHP.
- **How it works:** Read workbook, convert active sheet to HTML table for safe preview (can limit rows/columns to protect value).
- **Sample (backend render):**
  ```php
  use PhpOffice\PhpSpreadsheet\IOFactory;

  $reader = IOFactory::createReader('Xlsx');
  $spreadsheet = $reader->load('storage/safe_folder/estimate.xlsx');
  $writer = IOFactory::createWriter($spreadsheet, 'Html');
  $html = $writer->save('php://output'); // echoes HTML table
  ```
- **Why backend:** Control visibility (e.g., show first 10 rows), avoid heavy client CPU, keep data server-side.
- **Frontend alternative:** `SheetJS` (JS) to render in-browser if you must offload CPU; less control over data exposure.

## 2) PDF Viewer Strategy (`.pdf`)
- **Viewer:** Mozilla **PDF.js** (HTML5 Canvas renderer).
- **Why:** Better control than default browser PDF viewer; can disable easy save UI.
- **Dirty Preview / Watermark:** Convert page 1 to JPG and watermark.
  - Command: `convert -density 150 input.pdf[0] -quality 90 output.jpg` (ImageMagick; `[0]` = first page).
- **Flow:**
  1) On upload, store original PDF securely.
  2) For preview, either embed PDF.js or serve watermarked JPG of page 1.

## 3) AutoCAD DWG Strategy (`.dwg`)
- **Reality:** No free PHP/shared-hosting-friendly DWG renderer.
- **Option A (Cloud)**: Autodesk Forge API — paid per view. Not recommended for cost.
- **Option B (Recommended):** Require uploader to provide a JPEG/PNG snapshot when submitting DWG.
  - Admin verifies snapshot matches DWG during approval.
  - Show watermarked snapshot as the preview to buyers.
- **Option C (Experimental):** Accept DXF (open format) and attempt parse/convert — heavy and unreliable on shared hosting.
- **Verdict:** Use **Option B** (user-provided snapshot) for predictable cost and simplicity.

## 4) Required Libraries / Commands (Composer)
| File Type | Library | Command | Purpose |
| --- | --- | --- | --- |
| Excel | phpoffice/phpspreadsheet | `composer require phpoffice/phpspreadsheet` | Read Excel, render HTML preview |
| Images | intervention/image | `composer require intervention/image` | Resize & watermark images/snapshots |
| PDF | spatie/pdf-to-image | `composer require spatie/pdf-to-image` | Convert PDF page to JPG for preview |
| Zip | PHP ZipArchive | (built-in) | Bundle multiple files |

## 5) Preview Implementation Notes (Shared Hosting)
- **Security:** Store originals outside webroot; serve only derived previews (HTML tables, watermarked images).
- **Rate/Size Control:** Limit rows/cols for Excel previews; throttle large PDFs; require snapshot for DWG.
- **Watermarking:** Use Intervention Image on PDF-to-image output and DWG snapshots.
- **Caching:** Cache generated previews (HTML, JPG) to reduce repeated CPU work.
- **Fallbacks:** If preview fails, gracefully offer download with warning.

## 6) Database Tables (Human Element)
Run in MySQL (phpMyAdmin):
```sql
-- 1. REVIEWS (Quality Control)
CREATE TABLE resource_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resource_id INT NOT NULL, -- The file being rated
    reviewer_id INT NOT NULL, -- The user giving the rating
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resource_id) REFERENCES library_files(id),
    FOREIGN KEY (reviewer_id) REFERENCES users(id)
);

-- 2. NOTIFICATIONS (The "Bell" Icon)
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    link VARCHAR(255), -- Where clicking takes them (e.g., "bounty_view.php?id=5")
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 3. REFERRALS (Viral Growth)
CREATE TABLE referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inviter_id INT NOT NULL,
    new_user_id INT NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending', -- 'Completed' after 5 quizzes
    reward_paid TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inviter_id) REFERENCES users(id),
    FOREIGN KEY (new_user_id) REFERENCES users(id)
);
```

## 7) Suggested Preview Flow per File Type
- **Excel:** On request → read with PhpSpreadsheet → render limited HTML table → send to frontend (optionally inside iframe/component) → cache.
- **PDF:** On upload → generate watermarked JPG of page 1 for quick preview; on demand, load PDF.js viewer with restricted UI.
- **DWG:** On upload → enforce snapshot upload → watermark snapshot → display snapshot preview; keep DWG for download/post-purchase.

## 8) Action Checklist
- Install Composer packages (PhpSpreadsheet, Intervention Image, Spatie PDF-to-Image).
- Ensure ImageMagick available (for convert command).
- Enforce DWG snapshot requirement in upload form and validation.
- Add preview caching and watermarking steps.
- Create or verify the three DB tables above.

## 9) Optional Next Step
If you want a one-shot “master prompt” for an AI coding agent to implement the Blueprint Vault with these viewers and tables, I can generate it next.
