# Email Manager - Quick Start Guide

## Access Email Manager
1. Log in to admin panel
2. Navigate to: **Admin > Email Manager**
3. Or visit: `/admin/email-manager`

---

## Main Features

### 📊 Dashboard
- View statistics (total, unread, resolved, high priority)
- See recent threads
- Quick access to templates and settings

### 📧 Email Threads
- **View:** Click on any thread to see full conversation
- **Reply:** Add responses with rich text editor
- **Filter:** By status, priority, assignee
- **Search:** By subject, email, sender name
- **Assign:** Assign to admin staff
- **Update:** Change status and priority

### 📝 Email Templates
- **Create:** New templates for quick replies
- **Use:** Select template when replying
- **Variables:** Use {{name}}, {{email}}, etc. for personalization
- **Manage:** Edit, delete, activate/deactivate

### ⚙️ Settings
- **SMTP Configuration:** Host, port, credentials
- **Email Settings:** From email, from name
- **Test:** Send test email to verify setup

---

## Common Tasks

### Replying to an Email
1. Click on thread in list
2. Scroll to "Add Response" section
3. (Optional) Select template from dropdown
4. Type your reply
5. Check "Internal Note" if not sending to customer
6. Click "Send Response"

### Creating an Email Template
1. Go to **Email Manager > Templates**
2. Click **Create Template**
3. Fill in:
   - Template Name
   - Subject Line
   - Content
   - Category
   - Variables (optional)
4. Click **Save**

### Configuring SMTP
1. Go to **Email Manager > Settings**
2. Enter SMTP details:
   - SMTP Host: smtp.gmail.com (for Gmail)
   - SMTP Port: 587 (for TLS)
   - Username: your-email@gmail.com
   - Password: your-app-password
   - Encryption: TLS
3. Set From Email and From Name
4. Click **Save Changes**
5. Click **Test Email** to verify

### Filtering Threads
1. Select filters:
   - Status: new, in_progress, resolved, closed
   - Priority: low, medium, high, urgent
   - Assignee: specific admin
2. Enter search term (optional)
3. Results update automatically

---

## Status & Priority

### Thread Status
- **New:** Unread, just arrived
- **In Progress:** Being handled
- **Resolved:** Issue fixed, awaiting confirmation
- **Closed:** Complete, archived

### Priority Levels
- **Low:** Can wait, non-urgent
- **Medium:** Normal priority
- **High:** Important, needs attention soon
- **Urgent:** Critical, needs immediate attention

---

## Tips & Tricks

✅ **Use Templates** - Save time with pre-written responses
✅ **Internal Notes** - Add notes without emailing customer
✅ **Assign Threads** - Distribute work among team
✅ **Search** - Find old threads quickly
✅ **Test SMTP** - Verify settings before going live
✅ **Variables** - Personalize templates with {{name}}, {{email}}

---

## Keyboard Shortcuts

- `Ctrl+Shift+R` - Hard refresh (clear cache)
- `Ctrl+F` - Search on page
- `Tab` - Navigate form fields
- `Enter` - Submit forms

---

## Troubleshooting

**Emails not sending?**
- Check SMTP settings
- Verify credentials
- Test with "Test Email" button
- Check server error logs

**Templates not showing?**
- Refresh page (Ctrl+Shift+R)
- Check browser console for errors
- Verify templates exist in database

**Threads not loading?**
- Clear filters
- Check database connection
- Verify permissions

---

## Support

For issues or questions:
1. Check the full guide: `EMAIL_SYSTEM_COMPLETE_GUIDE.md`
2. Review error logs in admin panel
3. Check browser console (F12)
4. Contact system administrator

---

## Quick Links

- Dashboard: `/admin/email-manager`
- Threads: `/admin/email-manager/threads`
- Templates: `/admin/email-manager/templates`
- Settings: `/admin/email-manager/settings`
- Stats API: `/admin/email-manager/stats`
