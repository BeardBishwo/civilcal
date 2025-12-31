# Media Upload and Storage System

## Overview

This document provides an in-depth analysis of the media upload and storage architecture, covering everything from HTTP entry points and validation safeguards to image optimization, database persistence, retrieval pathways, and deletion workflows. The system demonstrates disciplined separation of concerns across controllers, services, and models while maintaining strict security guarantees for every uploaded asset.

## 1. Main Media Upload Flow

### 1.1 Upload Entry Point: `ContentController@uploadMedia`
- **Route**: `POST /admin/content/media/upload` registered with `auth` and `admin` middleware (`app/routes.php`@848-854).
- **Controller**: `app/Controllers/Admin/ContentController.php` handles request lifecycle.

### 1.2 Request Pre-Processing
- **CSRF Validation**: Token extracted from `$_POST['csrf_token']` and verified (`ContentController.php`@433). Failure returns HTTP 403.
- **File Enumeration**: Controller iterates over `$_FILES['media']` building a normalized structure for processing (`ContentController.php`@451).
- **Error Accumulation**: Validation failures appended to `$errors` array; successful uploads collected separately for batch reporting.

### 1.3 File Validation Pipeline
- **Filename Sanitization**: `sanitizeFilename()` removes unsafe characters, collapses repeated underscores, and enforces length limits (`ContentController.php`@460-464, 827-831).
- **MIME Detection**: Actual type detected via `mime_content_type()` or `finfo` to prevent spoofing (`ContentController.php`@471-489).
- **Type Whitelisting**: Allowed MIME types enforced (e.g., JPEG, PNG, GIF, WebP, PDF). Disallowed types logged and rejected (`ContentController.php`@487-495).
- **File Size Check**: Ensures uploads remain under configured max (10 MB) to prevent resource exhaustion (`ContentController.php`@496-500).
- **Duplicate Detection**: `md5_file()` hash compared against existing media records to avoid redundant storage (`ContentController.php`@531-535).

### 1.4 Storage Preparation
- **Directory Resolution**: File stored under `public/storage/media/<year>/<month>/` organized by upload date (`ContentController.php`@520-524).
- **Secure Directory Creation**: `mkdir(..., 0755, true)` ensures directories exist with safe permissions and handles nested creation gracefully.
- **File Move**: `move_uploaded_file()` atomic operation moves file from temp to target path (`ContentController.php`@537-541).

### 1.5 Metadata Enrichment
- **Dimension Detection**: For images, `getimagesize()` captures width/height for metadata storage (`ContentController.php`@543-551).
- **Hash Storage**: File hash persisted enabling dedupe checks and quick integrity verification.
- **Optimization Flags**: Optimization results stored (e.g., `optimized`, `has_webp`, `thumbnail_path`).

## 2. Image Optimization Pipeline

### 2.1 Optimizer Overview
- **Service**: `App\Services\ImageOptimizer` orchestrates compression, thumbnail generation, and WebP conversion.
- **Instantiation**: Controller creates optimizer instance when handling image uploads (`ContentController.php`@552-559).

### 2.2 Optimization Steps
1. **Optimize Original** (`ImageOptimizer::optimize`)
   - Determines handler based on MIME (`imagecreatefromjpeg`, `imagecreatefrompng`, etc.).
   - Applies compression quality (configurable) while stripping metadata (`ImageOptimizer.php`@20-50).
   - Saves optimized file overwriting original.

2. **Thumbnail Generation** (`ImageOptimizer::resize`)
   - Creates thumbnails (default width 150px) for admin galleries (`ContentController.php`@572-576).
   - Uses aspect-ratio preserving calculations (`ImageOptimizer.php`@60-115).
   - High-quality resampling via `imagecopyresampled`; outputs JPEG/PNG with optimized quality settings.

3. **WebP Conversion** (`ImageOptimizer::convertToWebP`)
   - Converts originals to WebP for modern browsers (`ContentController.php`@593-597).
   - Ensures palette images converted to truecolor before encoding.
   - Supports configurable WebP quality (`ImageOptimizer.php`@129-159).

### 2.3 Optimization Metadata
- `optimized`: indicates primary file compressed.
- `thumbnail_path`: relative path to generated thumbnail.
- `has_webp`: flag for presence of WebP variant.
- `thumbnail_width` / `thumbnail_height`: resizing outputs stored for UI layout.

## 3. Media Database Operations

### 3.1 Media Model (`App\Models\Media`)
- **create()**: Inserts media metadata (paths, dimensions, hash, optimization flags) using prepared statements (`Media.php`@68-78).
- **getAll()**: Supports filtered, paginated retrieval with dynamic WHERE clauses (`Media.php`@17-49).
- **find()**: Retrieves single media record by ID.
- **delete()**: Removes entry via `DELETE` statement (`Media.php`@102-106).
- **getUsageInfo()**: Inspects pages, menus, and settings for references preventing accidental deletion of in-use assets (`Media.php`@133-170).
- **findUntrackedFiles()**: Scans filesystem and identifies files lacking DB registration for reconciliation (`Media.php`@190-241).

### 3.2 Media Table Schema (Key Columns)
- `id`, `original_filename`, `filename`, `file_path`, `file_size`, `mime_type`.
- `width`, `height`, `thumbnail_path`, `webp_path`, `hash`.
- `optimized`, `has_webp`, `created_at`, `updated_at`.
- Indexes on `hash`, `mime_type`, and temporal columns for efficient lookups.

## 4. Validation and Security Layers

### 4.1 CSRF Protection
- **Middleware**: `App\Middleware\CsrfMiddleware` intercepts state-changing requests.
- **Token Verification**: `Security::validateCsrfToken()` invoked; rejects untrusted submissions with HTTP 419 (`CsrfMiddleware.php`@38-42).

### 4.2 File Validation Defense-in-Depth
- **MIME Whitelist**: Enforces allowed types post-finfo detection.
- **Extension Cross-Check**: Ensures filename extension aligns with MIME.
- **Size Limits**: Configurable per asset type to prevent oversized uploads.
- **Dimension Checks**: `getimagesize()` ensures actual image file and validates min/max dimensions when needed.
- **Filename Sanitization**: Prevents directory traversal and special character abuse.
- **Duplicate Detection**: Hash identification stops repeated uploads and potential fingerprinting attacks.
- **Secure Permissions**: Uploaded directories contain `.htaccess` disabling PHP execution and ensuring read-only file perms (0644).

### 4.3 Logging and Monitoring
- **Audit Trail**: Errors and invalid attempts logged via controller logging methods (`logError`, `logInfo`).
- **Security Alerts**: Integration with `SecurityMonitor` for high-severity anomalies (e.g., suspicious MIME attempts).
- **Rate Limiting**: Admin upload endpoints benefit from global rate limiting middleware to deter brute-force attempts.

## 5. Specialized Upload Services

### 5.1 `ImageUploadService`
- Handles admin assets (logo, favicon, banners) and user profile images with tailored constraints.
- **validateUpload()**: Applies MIME, extension, size, and dimension checks; uses `finfo_open(FILEINFO_MIME_TYPE)` and `getimagesize()` (`ImageUploadService.php`@322-338).
- **generateSecureFilename()**: Produces unique filenames with timestamp and random strings to prevent collisions.
- **move_uploaded_file()`**: Moves file to target directory with immediate `chmod(..., 0644)` (`ImageUploadService.php`@184-191).
- **optimizeImage()`**: Optional resizing/optimization specific to admin image requirements (`ImageUploadService.php`@404-451).
- **deleteOldImages()`**: Cleans up previous versions to avoid orphaned files (`ImageUploadService.php`@200-204).

### 5.2 User Upload Flow
- Dedicated entry points for user avatars with quota checks, dimension enforcement, and optionally triggered background optimization tasks.

## 6. Media Retrieval & Serving

### 6.1 Image Retrieval Service (`ImageRetrievalService`)
- **Caching**: Static in-memory cache reduces filesystem hits (`ImageRetrievalService.php`@47-56).
- **Lookup Order**:
  1. Cached result.
  2. Uploaded file search via `glob()` and `usort()` selecting most recent asset (`ImageRetrievalService.php`@205-216).
  3. `site_meta.json` configuration (loosely typed configuration store) for custom references.
  4. Theme default fallback defined in `DEFAULT_IMAGES` map (`ImageRetrievalService.php`@67-71).
- **Public URL Generation**: Converts filesystem path to web-accessible URLs respecting CDN or base URL configuration.

### 6.2 ImageManager Facade
- Provides simplified interface for controllers/views (`ImageManager.php`@108-112), delegating lookup to retrieval service based on asset type (logo, favicon, etc.).

## 7. Media Deletion Workflow

### 7.1 Controller Logic
- **Route**: `POST /admin/content/media/delete/{id}` with admin middleware (`app/routes.php`@852).
- **CSRF & Auth**: Middleware ensures authenticity before deletion.
- **Record Lookup**: Fetch media record via `Media->find($id)`; absence returns error (`ContentController.php`@686-690).
- **File Removal**: `unlink()` executed for main file; associated thumbnail/WebP cleaned in same block (`ContentController.php`@694-699).
- **Database Deletion**: `Media->delete($id)` finalizes removal (`ContentController.php`@703-707).
- **Usage Safeguards**: Optional usage check prevents deleting assets referenced in pages, menus, or settings (leveraging `getUsageInfo()`).

### 7.2 Recycle/Recovery Strategy (Future Enhancement)
- Soft-delete flag or quarantine directory to allow restoration before permanent deletion.
- Version history for critical brand assets (logos/banners).

## 8. Storage Organization & File System Security

### 8.1 Directory Structure
- `public/storage/media/<year>/<month>/` for general assets.
- `public/storage/admin/<category>/` for admin-managed images (logos, favicons).
- `public/uploads/avatars/` and similar directories for user-generated content.

### 8.2 .htaccess Protections
- `.htaccess` in upload directories disables PHP execution and restricts access to known safe extensions.
- Includes caching headers for media performance optimization.

### 8.3 Permissions
- Directories: `0755` ensuring web server write access while preventing traversal.
- Files: `0644` (owner read/write, others read-only) to block script execution.

## 9. Performance Considerations

### 9.1 Optimization Techniques
- On-the-fly compression reduces payload size for downstream consumers.
- Thumbnail and medium variants minimize bandwidth for listing views.
- WebP support leverages modern browsers for better compression ratios.
- Cached retrieval results minimize disk access for frequently requested images.

### 9.2 Scalability Strategies
- **Asynchronous Processing**: Potential queue-based background optimization for very large uploads.
- **CDN Integration**: Offload serving to CDN using generated URLs.
- **Storage Abstraction**: Swap local storage for S3-compatible object storage using adapter pattern.
- **Chunked Uploads**: For extremely large media, implement chunked uploads with resumable support.

## 10. Security Best Practices

1. **Strict Validation**: MIME, extension, size, and dimension checks guard against malicious files.
2. **CSRF Protection**: Mandatory for all admin upload and deletion actions.
3. **Rate Limiting**: Shields against brute-force uploads and resource abuse.
4. **Audit Logging**: Tracks upload, optimization, deletion events for forensic purposes.
5. **Permissions Management**: Ensures uploaded files cannot execute server-side code.
6. **Duplicate Detection**: Prevents storage floods via identical file submissions.
7. **Usage Tracking**: Prevents deletion of in-use assets, avoiding broken references.
8. **Secure Filenames**: Sanitization thwarts path traversal and special character exploits.

## 11. Future Improvements

- **Virus Scanning**: Integrate ClamAV or third-party API to scan uploads.
- **Image EXIF Handling**: Strip sensitive metadata (location, camera info) during optimization.
- **Media Tagging**: Add tags/categories for better organization and search.
- **Access Control**: Fine-grained permissions on who can upload/delete specific media types.
- **Versioning**: Maintain revision history for critical assets with rollback support.
- **Watermarking**: Optional watermarking for public-facing assets.
- **Automated Cleanup**: Scheduled task to reconcile DB entries with filesystem and remove orphaned files.
- **Monitoring Dashboard**: Real-time upload stats, storage utilization charts, optimization success rates.

## Conclusion

The media upload and storage system delivers a robust, secure, and scalable pipeline for handling digital assets. By combining meticulous validation, comprehensive optimization, and structured storage with proactive security measures, the platform maintains data integrity and performance. The modular architecture and documented services provide an excellent foundation for future enhancements such as cloud storage integration, advanced metadata management, and automated compliance workflows.
