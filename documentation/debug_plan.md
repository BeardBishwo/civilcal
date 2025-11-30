# Bishwo Calculator - 135 Diagnostic Issues Debug Plan

## Issue Categories Analysis

### 🔴 Critical Issues (Fix First)
1. **Database Class Missing Methods** - ~40 errors
   - `App\Core\Database::prepare()` 
   - `App\Core\Database::insert()`
   - `App\Core\Database::update()`

2. **PHP Syntax Errors** - 3 files
   - `modules/mep/integration/bim-integration.php:318`
   - `modules/mep/reports-documentation/clash-detection-report.php:83`
   - `includes/header.php:1406-1407` (CSS)

### 🟡 Missing Model Methods - ~50 errors
- EmailThread: getRecentThreads(), getThreadsByStatus(), etc.
- EmailTemplate: getAllTemplates(), createTemplate(), etc.
- Comment: getCommentById(), createComment(), etc.
- Share: createShare(), getShareByToken(), etc.
- Vote: voteOnComment(), getUserVoteForComment(), etc.

### 🟠 Undefined Constants - ~30 errors
- LOG_LEVEL_ERROR, LOG_LEVEL_WARNING, LOG_LEVEL_INFO, LOG_LEVEL_DEBUG
- DISPLAY_ERRORS, CURRENT_LOG_LEVEL
- DEBUG_LOG_PATH, ERROR_LOG_PATH, etc.
- typography, font_family in ThemeBuilder

### 🟢 Minor Issues
- Namespace issues
- Unused variables
- Code style improvements

## Debugging Priority Order

1. **Fix Database Class** (Resolves ~40 errors)
2. **Fix PHP Syntax Errors** (Resolves 3 errors)
3. **Add Missing Constants** (Resolves ~30 errors)
4. **Add Missing Model Methods** (Resolves ~50 errors)
5. **Fix Namespace Issues** (Resolves ~10 errors)
6. **Test and Validate** (Final verification)

## Implementation Strategy
- Fix one category at a time
- Test each fix before moving to next
- Use systematic approach to avoid creating new errors
- Document all changes made
