# Debug System - Quick Reference Card

## 🎯 What Was Fixed

### ✅ Error Logs Now Working
- **Problem**: Pages showed no errors despite 988 log entries
- **Solution**: Updated to read daily JSON log files (`YYYY-MM-DD.log`)
- **Result**: All 988 entries now visible with full context

### ✅ System Tests Now Accurate  
- **Problem**: Admin Panel test showed false "missing files" warnings
- **Solution**: Fixed file path detection in `testAdminPanel()` method
- **Result**: All tests show correct status

### ✅ Clear User Interface
- **Problem**: "Waiting to run..." was confusing
- **Solution**: Changed to "Click 'Run' to test this component"
- **Result**: Users understand they need to click buttons

## 📊 Quick Stats

**Current Log Entries**: 988
- 🔴 Errors: 76
- 🟡 Warnings: 228  
- 🔵 Info: 684

**Log Format**: JSON (one entry per line)
**Log Rotation**: Daily files (`2025-11-20.log`)
**Retention**: Last 7 days displayed

## 🔗 Debug Pages

| Page | URL | Purpose |
|------|-----|---------|
| **Dashboard** | `/admin/debug` | Overview of system health |
| **Error Logs** | `/admin/debug/error-logs` | Browse all logs with pagination |
| **Live Monitor** | `/admin/debug/live-errors` | Real-time error stream |
| **System Tests** | `/admin/debug/tests` | Test system components |

## 🎮 How to Use

### View Error Logs
1. Go to `/admin/debug/error-logs`
2. Filter by level (All/Error/Warning/Info)
3. Click expand icon to see full message + context
4. Use pagination to browse older entries

### Monitor Live Errors
1. Go to `/admin/debug/live-errors`
2. Leave page open - polls every 3 seconds
3. New errors appear automatically at top
4. Connection status shown in badge

### Run System Tests
1. Go to `/admin/debug/tests`
2. Click **"Run All Tests"** or individual **"Run"** buttons
3. View results instantly:
   - 🟢 Green = Passed
   - 🟡 Yellow = Warning
   - 🔴 Red = Failed
   - ⚪ Gray = Not Run

## 📂 Log File Locations

```
storage/logs/
├── 2025-11-20.log          # Today's app logs (JSON)
├── 2025-11-19.log          # Yesterday's logs
├── audit-2025-11-20.log    # Audit trail
└── php_error.log           # PHP/Xdebug errors
```

## 🔧 Common Tasks

### Clear Old Logs
```php
// Via UI: /admin/debug/error-logs -> Click "Clear Logs"
// Clears today + deletes last 7 days
```

### Export Logs
```php
// Via UI: /admin/debug/error-logs -> Click "Download"
// Downloads current filtered view
```

### Filter by Error Level
```php
// Use dropdown: All / Error / Warning / Notice / Info
// URL: ?filter=error&page=1
```

## 📝 Log Entry Structure

```json
{
  "timestamp": "2025-11-20 04:49:52",
  "level": "warning",
  "message": "plugin_entry_undefined",
  "context": {
    "slug": "green-building-tools"
  }
}
```

## 🎯 Test Categories

1. **System Requirements** - PHP version & extensions
2. **Database Connection** - MySQL connectivity
3. **Module System** - Module manager status
4. **Authentication** - User model & admin count
5. **Services** - GeoLocation & Installer
6. **File Permissions** - Writable directories

## ⚠️ Important Notes

### "0 modules loaded" is OK
- Modules use lazy loading
- Shown as loaded when actually used
- Not an error condition

### "Not Run" vs "Pending"
- **Not Run**: Test hasn't been executed (click to run)
- **Pending**: Would mean waiting for something (confusing, removed)

### Xdebug Warnings Filtered
- PHP error log has many Xdebug timeout messages
- These are automatically filtered from live monitor
- They're just noise (debug client not listening)

### Test Result Caching
- Results cached for 5 seconds
- Wait 5+ seconds between runs for fresh results
- Improves performance for frequent checks

## 🚀 Performance

- **Log Reading**: Reads last 7 days on demand
- **Pagination**: 50 entries per page
- **Live Polling**: Every 3 seconds
- **Cache**: 5 second TTL for test results

## 📚 Full Documentation

- **Complete Fix Guide**: `md_files/DEBUG_ERROR_LOGS_FIX_SUMMARY.md`
- **Tests Details**: `md_files/SYSTEM_TESTS_FIX_SUMMARY.md`

## 🆘 Troubleshooting

**No logs showing?**
- Check `storage/logs/YYYY-MM-DD.log` exists
- Verify file has JSON entries
- Check file permissions

**Tests not running?**
- Click "Run All Tests" or "Run" button
- Check browser console for errors
- Verify CSRF token in session

**Admin panel test fails?**
- Verify files exist in `themes/admin/`
- Check file permissions
- See paths in test messages

---

**Last Updated**: November 20, 2025
**Version**: 1.0
**Status**: ✅ All Systems Operational
