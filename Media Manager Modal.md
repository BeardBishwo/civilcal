# Media Manager Modal: From UI Interaction to Backend API Integration

## Overview

The Media Manager Modal delivers a seamless experience for selecting, uploading, and inserting media within the admin interface. This document explains the end-to-end architecture covering UI components, JavaScript controllers, HTTP routing, middleware security, backend controllers, and database interactions. It also highlights performance considerations, extensibility points, and future enhancements.

## 1. User Interface Layer

### 1.1 Modal Inclusion in Admin Layout
- **Location**: `themes/admin/layouts/main.php` includes the media modal partial globally (`main.php`@1142-1146).
- **TinyMCE Integration**: Provided in the same layout; registers a custom toolbar button launching the modal (`main.php`@1155-1166).

### 1.2 TinyMCE Toolbar Button Flow
1. `editor.ui.registry.addButton('media-library', {...})` registers custom button (`main.php`@1158).
2. Button `onAction` triggers `MediaModal.open(callback)` (`main.php`@1163).
3. Callback inserts selected image using `editor.insertContent()` (`main.php`@1164), enabling quick embedding.

### 1.3 Modal Structure (Partial)
- **View File**: `themes/admin/views/partials/media_modal.php`.
- **Components**:
  - Toolbar with Upload button and search filters (`media_modal.php`@15-70).
  - Grid container for media thumbnails (`media_modal.php`@96-110).
  - Pagination controls for navigating pages (`media_modal.php`@214-316).
- **JavaScript Namespace**: `MediaModal` object encapsulates modal logic (`media_modal.php`@108 onwards).

## 2. Frontend JavaScript Logic

### 2.1 Modal Lifecycle
- **Initialization**: `MediaModal.init()` binds DOM references, events, and loads the Bootstrap modal instance.
- **Opening**: `MediaModal.open(callback)` stores callback, loads media page 1, then shows modal (`media_modal.php`@114-118).
- **Closing**: On selection or cancel, modal hides and callback executed if applicable (`media_modal.php`@205-210).

### 2.2 Media Loading (`loadMedia`)
1. Constructs API URL with `page` and optional `search` query (`media_modal.php`@128-132).
2. Displays loading spinner while awaiting response (`media_modal.php`@128).
3. Fetches JSON from `/admin/api/media` via `fetch()`.
4. Renders media items into grid (`media_modal.php`@137-151).
5. Updates pagination UI using metadata from response (`media_modal.php`@220-256).

### 2.3 Upload Handling (`handleUpload`)
1. Triggered when hidden file input changes (`media_modal.php`@165-169).
2. Builds `FormData`, appends file, CSRF token, and optional folder data (`media_modal.php`@176-180).
3. Posts to `/admin/api/media/upload` with spinner overlay (`media_modal.php`@183-187).
4. On success, selects returned item immediately (`media_modal.php`@191-195).
5. On failure, alerts user without closing modal.

### 2.4 Selection (`selectItem`)
- Executes stored callback with selected item’s URL and metadata, then hides modal (`media_modal.php`@205-210).
- For TinyMCE, this inserts image HTML at cursor position.

## 3. Routing and Middleware

### 3.1 Routes (`app/routes.php`@620-624)
- `GET /admin/api/media` → `Admin\MediaApiController@index` (middleware: `auth`, `admin`).
- `POST /admin/api/media/upload` → `Admin\MediaApiController@upload` (middleware: `auth`, `admin`, `csrf`).

### 3.2 Middleware Pipeline (Request Flow)
1. **MaintenanceMiddleware** (optional) – denies access during maintenance.
2. **SecurityMiddleware** – sets headers like CSP, X-Frame-Options.
3. **AuthMiddleware** – verifies user session or HTTP basic auth (`AuthMiddleware.php`@47-51, 89-93).
4. **AdminMiddleware** – ensures user has admin privileges (`AdminMiddleware.php`@64-68).
5. **CsrfMiddleware** (for POST) – validates CSRF tokens from request (`CsrfMiddleware.php`@38-42).

### 3.3 Dispatch Mechanics
- `public/index.php` starts secure session, sets headers, then dispatches router (`index.php`@14-43).
- Router builds pipeline and executes middleware in reverse order before hitting controller (`Router.php`@140-192).

## 4. Backend Controllers

### 4.1 `MediaApiController@index`
- **Auth Check**: Ensures `current_user()` exists (`MediaApiController.php`@11-15).
- **Pagination**: Accepts `page` parameter (default 1) and supports `search` filters.
- **Query**: Builds SQL with optional search across filename/original filename (`MediaApiController.php`@23-47).
- **Execution**: Uses PDO prepared statements via database singleton (`Database::getInstance()`).
- **Response**: Returns JSON with `data` (media items) and `pagination` metadata (`MediaApiController.php`@58-62).
- **Item Transformation**: Adds `url`, `thumb`, `is_image` fields to each record for frontend display (`MediaApiController.php`@44-55).

### 4.2 `MediaApiController@upload`
- **Input**: Expects `file` in multipart/form-data.
- **Validation**: Leverages shared validation utilities (size, mime, extension) typically via helper methods or service calls.
- **Storage**:
  1. Determines target directory (e.g., `public/uploads/` or variant).
  2. Generates unique filename (timestamp + random string).
  3. Moves file using `move_uploaded_file()` (`MediaApiController.php`@92-98).
- **Database Insert**: Inserts filename, mime type, size, timestamps into `media` table (`MediaApiController.php`@96-98).
- **Response**: Returns JSON with success flag and newly created media data for immediate selection.

## 5. Database Layer

### 5.1 Media Table (schema excerpt)
- **Location**: `install/database.sql`@164-175.
- Columns: `id`, `original_filename`, `filename`, `type`, `size`, `file_path`, `mime_type`, `folder`, `uploaded_by`, `created_at`, `updated_at`.
- Indexes on key columns for fast retrieval and filtering.

### 5.2 Model Responsibilities (`App\Models\Media`)
- **create**: Inserts metadata using prepared statements (`Media.php`@71-78).
- **getAll**: Filtered, paginated queries for listing (`Media.php`@45-49).
- **find**/**delete**: Single record operations (`Media.php`@63, 102-106).
- **getUsageInfo**: Cross-check references in pages, menus, settings to prevent deletion of in-use media (`Media.php`@133-170).
- **findUntrackedFiles**: Reconciles filesystem with database to detect orphaned assets (`Media.php`@190-241).

## 6. Security Considerations

1. **Authentication & Authorization**: All API routes require authenticated admin users (middleware enforced).
2. **CSRF Protection**: POST upload uses CSRF token appended to FormData, validated server-side.
3. **Secure Sessions**: `Security::startSession()` sets secure cookie parameters and CSRF token generation (`Security.php`@47-179).
4. **Input Validation**: Upload requests validated for MIME, size, extension to prevent malicious files.
5. **Rate Limiting**: Global middleware can throttle requests; additional service-level checks recommended for upload abuse.
6. **File Permissions**: Uploaded files should be stored with `0644` permissions; directories `0755` with `.htaccess` blocking script execution.

## 7. Performance & UX Enhancements

### 7.1 Current Optimizations
- Pagination reduces response payload size.
- Lazy rendering of thumbnails with spinner feedback.
- Immediate selection of newly uploaded media for seamless TinyMCE integration.

### 7.2 Potential Enhancements
1. **Thumbnail Caching**: Serve actual thumbnails to reduce load time (currently `thumb` points to original).
2. **Search Indexing**: Add indexes or full-text search to speed up large libraries.
3. **Asynchronous Uploads**: Show progress bars with XHR upload progress events.
4. **Batch Uploads**: Allow multiple file selections with queue management.
5. **Filtering & Sorting**: Filter by type, date range, or metadata tags.
6. **Integration with Optimization Pipeline**: Trigger background image optimization (WebP, thumbnails) for uploaded files.
7. **Access Control**: Fine-grained permissions (e.g., restrict certain folders to specific roles).
8. **CDN Support**: Generate CDN-prefixed URLs when available.

## 8. Error Handling & Logging

- AJAX responses include `success` boolean and `message` for UI feedback.
- Upload failures alert user; additional UI enhancements could provide inline error display.
- Server-side logging records invalid attempts, filesystem errors, and database failures.
- Consider integrating with centralized logging/monitoring for production visibility.

## 9. Extensibility Points

1. **Custom Callbacks**: `MediaModal.open(callback)` pattern allows reuse in other contexts beyond TinyMCE.
2. **Plugin Architecture**: Additional buttons or integrations can hook into modal with minimal setup.
3. **Service Abstraction**: Replace local storage with object storage (S3, Azure Blob) by modifying upload controller and retrieval URL generation.
4. **Metadata Extensions**: Store additional metadata (alt text, captions, tags) and expose in API.
5. **Audit Trails**: Track upload actions per user for compliance.

## 10. Future Improvements

- **Client-Side Validation**: Validate file size/type before upload for instant feedback.
- **Drag-and-Drop Uploads**: Enhance UX with drop zones inside modal.
- **Inline Editing**: Allow setting alt text, caption, or cropping within modal.
- **Versioning**: Keep history for frequently updated assets (logos, banners).
- **Localization**: Multi-language labels and search across translated metadata.
- **User Quotas**: Enforce per-user storage limits with dashboards.
- **Accessibility**: Keyboard navigation, ARIA labels, and screen reader support for modal grid.

## Conclusion

The Media Manager Modal tightly integrates frontend UX, secure backend APIs, and database persistence to provide a robust media library solution. By leveraging modular JavaScript, strict middleware enforcement, and consistent controller-model interactions, the system maintains both usability and security. The architecture is extensible, making it straightforward to add features such as advanced search, batch processing, and cloud storage support while preserving the existing workflow.
