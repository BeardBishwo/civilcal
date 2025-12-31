# Admin Panel Content Management System - Pages, Menus & Media

## Overview

This document provides a comprehensive view of the admin panel’s content management architecture, covering page creation/editing, media upload with optimization, usage tracking, menu management, and maintenance operations. It details request routing, middleware enforcement, controller logic, model interactions, and security safeguards to help developers understand and extend the system.

## 1. Request Routing & Middleware Pipeline

### 1.1 Router Dispatch
- **Entry Point**: `public/index.php` calls `Router->dispatch()` (`Router.php`@58-62).
- **Pattern Matching**: Router iterates routes, matching `/admin/content/*` patterns (`Router.php`@74-80).
- **Route Execution**: Matched route triggers `callRoute()` with parameters (`Router.php`@78).

### 1.2 Middleware Chain
Routes for content management require:
- **MaintenanceMiddleware** – optional maintenance mode.
- **SecurityMiddleware** – sets security headers (CSP, HSTS, etc.).
- **AuthMiddleware** – ensures user session exists.
- **AdminMiddleware** – checks admin role via `requireAdmin()` (`Controller.php`@156-160).
- **CsrfMiddleware** – validates CSRF tokens for POST actions.

Middleware is built in order and executed in reverse, wrapping the controller call (`Router.php`@140-144).

### 1.3 Controller Instantiation
- `ContentController` constructed with model dependencies (`ContentController.php`@15-22):
  - `$pageModel = new Page()`
  - `$menuModel = new Menu()`
  - `$mediaModel = new Media()`

## 2. Page Management

### 2.1 Page Creation Flow
1. **GET /admin/content/pages/create** (`routes.php`@816-818) → `ContentController@create()`.
2. **Controller Action** (`ContentController.php`@183-204):
   - Calls `Auth::user()` for context.
   - Renders `admin/content/create` view with CKEditor integration.
3. **Form Rendering**: CKEditor instance for rich content editing.

### 2.2 Page Save Flow
1. **POST /admin/content/pages/save** (`routes.php`@871-875) → `ContentController@save()`.
2. **CSRF Validation** (`ContentController.php`@241-245):
   - Token extracted from POST; invalid tokens trigger HTTP 403.
3. **Data Collection** (`ContentController.php`@249-263):
   - Collects title, content, meta, status, slug.
   - Auto-generates slug if empty.
4. **Persistence**:
   - If `id` present → `$pageModel->update($id, $data)`.
   - Else → `$pageModel->create($data)` (`Page.php`@105-109).
5. **Redirect**: After success, redirects to pages list (`ContentController.php`@265-273).

### 2.3 Page Model (`app/Models/Page.php`)
- **create()**: Builds dynamic INSERT, executes prepared statement, returns insert ID.
- **update()**: Dynamic UPDATE with given data.
- **getAll()**: Supports filtering, pagination, ordering.
- **find()**: Retrieves single page by ID.
- **delete()**: Soft/hard delete with usage checks.

## 3. Media Management

### 3.1 Media Library View
- **GET /admin/content/media** → `ContentController@media()` (`ContentController.php`@139-167).
- **Pagination**: `getAll()` with filters (`type`, `search`) and page/perPage.
- **Usage Tracking**: Calls `$mediaModel->getUsageInfo()` to merge usage status into items.
- **View Rendering**: Grid display with Used/Unused badges.

### 3.2 Media Upload & Optimization Pipeline
1. **POST /admin/content/media/upload** (`routes.php`@848) → `ContentController@uploadMedia()`.
2. **CSRF Validation** (`ContentController.php`@432).
3. **File Processing Loop** (`ContentController.php`@448-452):
   - MIME validation (`mime_content_type()`) against whitelist (`ContentController.php`@485-489).
   - Size check (max 10MB) (`ContentController.php`@498).
   - Filename sanitization and duplicate detection.
   - Move to storage directory (`ContentController.php`@539).
4. **Image Optimization** (`ContentController.php`@552-603):
   - **Original Compression**: `ImageOptimizer->optimize()` compresses in-place (`ImageOptimizer.php`@22-26).
   - **Thumbnail Generation**: 150px thumbnail via `ImageOptimizer->resize()` (`ContentController.php`@572-576).
   - **Medium Size**: 800px variant for gallery display.
   - **WebP Conversion**: Modern format for bandwidth savings (`ContentController.php`@593-597).
5. **Database Persistence** (`ContentController.php`@603-607):
   - Inserts record with original filename, optimized paths, dimensions, hash.
   - Returns new media ID for immediate selection.

### 3.3 Media Usage Tracking
- **Method**: `Media::getUsageInfo(array $items)` (`Media.php`@133-184).
- **Scans**:
  - **Pages**: `content` and `meta` columns for file references (`Media.php`@150-154).
  - **Menus**: JSON `items` column for URLs (`Media.php`@159-163).
  - **Settings**: `setting_value` for media references (`Media.php`@168-172).
- **Result**: Returns map keyed by media ID with `is_used` boolean and `details` array.

### 3.4 Media Sync (Untracked Files)
- **POST /admin/content/media/sync** → `ContentController@syncMedia()`.
- **Process**:
  1. CSRF validation.
  2. Calls `$mediaModel->findUntrackedFiles()` (`Media.php`@190-241).
  3. Scans storage directories (`images/`, `documents/`, `themes/`) recursively.
  4. For each file, checks if `media` table contains record (`Media.php`@237-241).
  5. Creates DB records for untracked files.

### 3.5 Bulk Cleanup (Unused Media)
- **POST /admin/content/media/bulk-cleanup** → `ContentController@bulkDeleteUnused()`.
- **Safety Checks**:
  1. Fetches all media via `getAll()`.
  2. Calls `getUsageInfo()` to identify unused items.
  3. For each unused item:
     - Deletes physical file (`unlink()`).
     - Deletes DB record (`Media::delete()`).
  4. Returns deleted count.

## 4. Menu Management

### 4.1 Menu Creation/Update
- **POST /admin/content/menus/save** → `ContentController@saveMenus()`.
- **Data**:
  - `name`, `location` (header/footer), `items` array, `is_active`.
- **Persistence**:
  - JSON-encodes `items` array (`Menu.php`@94-98).
  - Calls `Menu::update()` or `Menu::create()`.

### 4.2 Menu Model (`app/Models/Menu.php`)
- **create()**: Inserts new menu with items JSON.
- **update()**: Updates name, location, items, active status.
- **findByLocation()**: Retrieves active menu for a location (`Menu.php`@60-64).
- **delete()**: Soft/hard delete with usage checks.

### 4.3 Quick Location Assignment (AJAX)
- **POST /admin/content/menus/quick-assign** → `ContentController@quickAssignLocation()`.
- Updates menu’s `location` field for header/footer placement.

## 5. Security Considerations

- **CSRF Protection**: All state-changing POST actions validate CSRF tokens.
- **Authentication + Authorization**: Auth and admin middleware enforce session and role checks.
- **File Upload Security**:
  - MIME type validation.
  - File size limits.
  - Filename sanitization.
  - Duplicate detection via hash.
  - Storage directories have `.htaccess` to disable script execution.
- **SQL Injection Prevention**: All queries use prepared statements.

## 6. Performance & Optimization

- **Image Optimization**: Automatic compression and WebP conversion reduce bandwidth.
- **Pagination**: Media and pages lists paginate to limit memory usage.
- **Usage Caching**: Usage tracking could be cached per media item to avoid repeated scans.
- **Lazy Loading**: Frontend can lazy-load media grid items.

## 7. Extensibility

- **Custom Media Types**: Extend whitelist and optimization pipeline for new formats.
- **Additional Usage Sources**: Extend `getUsageInfo()` to scan more tables or JSON fields.
- **Menu Placement**: Add more locations beyond header/footer.
- **Bulk Operations**: Add bulk edit, tag, or categorize for media.
- **Versioning**: Add page/media version history and rollback.

## 8. Error Handling

- **Controller-Level**: Try/catch around critical operations; user-friendly messages.
- **Model-Level**: Prepared statements; exceptions bubble up.
- **Upload Errors**: Detailed error messages per file; continue processing others.
- **Filesystem Errors**: Logged and reported; graceful degradation.

## 9. Frontend Integration

- **CKEditor**: Rich text editing for page content.
- **Media Modal**: For selecting media in pages or other content.
- **AJAX**: Used for quick location assignment, sync, and cleanup.

## 10. Future Enhancements

- **Media Tags/Categories**: For better organization.
- **Media Search**: Full-text search across metadata.
- **Page Templates**: Reusable page templates.
- **Workflow**: Draft/review/publish workflow for pages.
- **Audit Log**: Track all content changes.
- **CDN Integration**: Serve media via CDN.
- **Image Editor**: Basic crop/rotate within admin.
- **Bulk Import/Export**: Media and content import/export tools.

## Conclusion

The admin content management system provides a secure, performant, and extensible foundation for managing pages, menus, and media. By leveraging middleware for security, models for data integrity, and optimization pipelines for media, the platform delivers a professional content authoring experience. Future enhancements can build upon the existing patterns to introduce richer workflows, improved search, and advanced media handling.
