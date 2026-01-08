# Admin Panel Media Management System

## Overview

The admin panel media management system provides two complementary interfaces: a reusable MediaModal for quick image selection within editors, and a full Media Library page for comprehensive file management. Both interfaces share a common backend API, security model, and image optimization pipeline, ensuring consistency across the admin experience. This document details the UI components, API endpoints, upload/optimization flow, usage tracking, and maintenance operations.

## 1. MediaModal Component (Reusable Selection Interface)

### 1.1 Purpose & Integration
- **Location**: `themes/admin/views/partials/media_modal.php`.
- **Usage**: Embedded in admin layout; invoked by TinyMCE, menu editor, and other rich text components.
- **Callback Pattern**: Opens with a callback function that receives the selected media URL and metadata.

### 1.2 Opening & Loading Flow
1. **Trigger**: TinyMCE button or custom UI calls `MediaModal.open(callback)` (`menu_edit.php`@325-329).
2. **Modal Initialization**: Stores callback, shows Bootstrap modal, and triggers `loadMedia(1)` (`media_modal.php`@114-118).
3. **API Fetch**: AJAX GET to `/admin/api/media` with pagination (`media_modal.php`@128-132).
4. **Controller**: `MediaApiController@index` builds paginated query (`MediaApiController.php`@44-50) and returns JSON with media items.
5. **Rendering**: JavaScript creates grid items, attaches click handlers (`media_modal.php`@148-152), and displays thumbnails.

### 1.3 Quick Upload Within Modal
1. **File Input**: Hidden file input triggered by “Upload to Media Library” button (`media_modal.php`@15-19).
2. **FormData Preparation**: Packages file and CSRF token (`media_modal.php`@166-170).
3. **POST Request**: Sends to `/admin/api/media/upload` (`media_modal.php`@183-187).
4. **Backend Processing**:
   - Moves file to uploads directory (`MediaApiController.php`@94).
   - Inserts basic metadata into media table (`MediaApiController.php`@96).
5. **Auto-Select**: On success, immediately selects the uploaded file and executes callback (`media_modal.php`@191-195).

### 1.4 Selection & Callback
- **Click Handler**: Each media item’s click event calls `selectItem(item)` (`media_modal.php`@148).
- **Callback Execution**: Passes `item.url` and metadata to stored callback, then hides modal (`media_modal.php`@206-210).

## 2. Full Media Library Page

### 2.1 Page Load & Usage Tracking
- **Route**: `GET /admin/content/media` → `ContentController@media()` (`routes.php`@842-846).
- **Data Fetching**: Retrieves paginated media with filters (`ContentController.php`@139-146).
- **Usage Detection**: Calls `Media::getUsageInfo()` to scan pages, menus, and settings for references (`ContentController.php`@144).
- **View Rendering**: Displays grid with Used/Unused badges and sidebar details (`ContentController.php`@175-179).

### 2.2 Usage Tracking Logic
- **Pages Search**: LIKE queries on `content` and `meta` columns for filename or path (`Media.php`@150-154).
- **Menus Search**: JSON `items` column searched for media URLs (`Media.php`@159-163).
- **Settings Search**: `setting_value` column scanned for references (`Media.php`@168-172).
- **Result**: Returns map keyed by media ID with `is_used` boolean and usage details.

### 2.3 Comprehensive Upload with Optimization
1. **Frontend**: Drag-and-drop or file input supports multiple files (`media.php`@798-802).
2. **Validation**: MIME type whitelist enforced (`ContentController.php`@485-489).
3. **Storage**: Files moved to categorized folders (`ContentController.php`@537-541).
4. **Optimization Pipeline**:
   - **Original Compression**: `ImageOptimizer->optimize()` reduces file size (`ContentController.php`@555-559).
   - **Thumbnail Generation**: 150px thumbnail created (`ContentController.php`@572-576).
   - **WebP Conversion**: Modern format generated for better compression (`ContentController.php`@593-597).
5. **Database Record**: Stores metadata including dimensions, optimization flags (`ContentController.php`@603-607).

### 2.4 Deletion (Single & Bulk)
- **Single Delete**: Sidebar delete button triggers POST with CSRF token (`media.php`@734-738).
- **Process**:
  1. Retrieve media record (`ContentController.php`@686-690).
  2. Resolve physical file path (`ContentController.php`@693-699).
  3. Delete file from disk (`ContentController.php`@697).
  4. Delete database record (`ContentController.php`@703-707).
- **Bulk Cleanup**: Scans all media, checks usage, and deletes unused items in batch.

### 2.5 Sync (Discover Untracked Files)
- **Trigger**: Sync button initiates scan (`media.php`@677-681).
- **Scanning**: `Media::findUntrackedFiles()` recursively scans storage and theme directories (`Media.php`@197-201).
- **Comparison**: For each file, queries database to see if already tracked (`Media.php`@237-241).
- **Registration**: Creates database records for discovered files (`ContentController.php`@903-907).

## 3. Backend API (`MediaApiController`)

### 3.1 Authentication & Authorization
- All routes require `auth` and `admin` middleware.
- CSRF validation enforced for POST requests.

### 3.2 Index Action (`/admin/api/media`)
- **Parameters**: `page`, `search`, `type` filters.
- **Query Building**: Dynamic WHERE clause, pagination, ordering (`MediaApiController.php`@44-50).
- **Response**: JSON with `data` array and pagination metadata.

### 3.3 Upload Action (`/admin/api/media/upload`)
- **File Handling**: Moves uploaded file to target directory.
- **Metadata**: Inserts filename, type, size, timestamp.
- **Response**: Returns success flag and media data for immediate selection.

## 4. Image Optimization Pipeline (`ImageOptimizer`)

### 4.1 Optimize Method
- **Entry**: `optimize($sourcePath, $quality = 80)` (`ImageOptimizer.php`@8-12).
- **Process**:
  1. Detect MIME type.
  2. Load image resource (`ImageOptimizer.php`@20-26).
  3. Re-save with specified quality (`ImageOptimizer.php`@24).
  4. Strip metadata to reduce size.

### 4.2 Resize Method
- **Resampling**: High-quality resize using `imagecopyresampled()` (`ImageOptimizer.php`@103-107).
- **Usage**: Thumbnails (150px) and medium (800px) variants.

### 4.3 WebP Conversion
- **Conversion**: `convertToWebP()` creates modern format version (`ImageOptimizer.php`@155-159).
- **Benefit**: Better compression for supported browsers.

## 5. Security Considerations

- **CSRF Protection**: All state-changing requests validate CSRF tokens.
- **File Validation**: MIME type whitelist and size limits enforced.
- **Path Sanitization**: Filenames sanitized; storage paths validated.
- **Authentication/Admin**: API endpoints require authenticated admin users.
- **File Permissions**: Upload directories configured to prevent script execution.

## 6. Performance Optimizations

- **Pagination**: Media library paginated to limit memory usage.
- **Thumbnails**: Smaller variants for grid display reduce bandwidth.
- **WebP Support**: Modern format reduces payload.
- **Lazy Loading**: Frontend can lazy-load grid items.
- **Usage Caching**: Usage info could be cached per media item.

## 7. Extensibility Points

- **Custom Media Types**: Extend whitelist and optimization pipeline.
- **Additional Usage Sources**: Extend `getUsageInfo()` to scan more tables.
- **Alternative Storage**: Swap local storage for cloud object storage.
- **Bulk Operations**: Add bulk tagging, categorization, or metadata editing.
- **Editor Integration**: Register MediaModal in other rich editors beyond TinyMCE.

## 8. Error Handling

- **Controller-Level**: Try/catch around file operations; user-friendly messages.
- **API Responses**: Consistent JSON with success flag and message.
- **Filesystem Errors**: Logged and reported; graceful degradation.
- **Validation Errors**: Detailed per-file error messages.

## 9. Frontend Enhancements

- **Drag-and-Drop**: Native drag-and-drop upload support.
- **Progress Indicators**: Upload progress bars for large files.
- **Search/Filter**: Real-time search and type filtering in modal and library.
- **Keyboard Navigation**: Accessible keyboard shortcuts for modal.

## 10. Future Improvements

- **Media Tags/Categories**: Organize files with metadata.
- **Versioning**: Keep history of media files.
- **CDN Integration**: Serve media via CDN.
- **Image Editor**: Basic crop/rotate within admin.
- **Batch Operations**: Bulk delete, tag, or move.
- **Audit Log**: Track all media operations.
- **Advanced Search**: Full-text search across metadata.

## Conclusion

The admin media management system delivers a robust, secure, and user-friendly experience for handling digital assets. By combining a reusable modal for quick selection with a full-featured library page, it supports both rapid content creation and comprehensive media stewardship. The shared optimization pipeline and usage tracking ensure efficient storage and safe deletion, while the modular design allows for future enhancements such as cloud storage integration and advanced metadata management.
