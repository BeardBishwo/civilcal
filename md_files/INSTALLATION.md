# Bishwo Calculator - Installation Guide

## Quick Start Instructions

### 1. Access the Installation Debug Tool
Open your web browser and go to: 
```
http://localhost/Bishwo_Calculator/install_test_installation.php
```

This will show you the current status and help diagnose any issues.

### 2. If Not Installed
If the debug tool shows "Not Installed", click the "Start Installation Wizard" button, which will take you to:
```
http://localhost/Bishwo_Calculator/install/index.php
```

### 3. Installation Steps
Follow the installation wizard with these settings:

**Database Configuration:**
- Host: `localhost`
- Database Name: `bishwo_calculator` (or your preferred name)
- Username: `root` (or your DB username)
- Password: (your DB password, usually empty in Laragon)

**Admin Account:**
- Name: Your name
- Email: Your email
- Password: Choose a secure password

**Email Configuration:**
- You can skip this step for now by clicking "Skip Email Setup"

### 4. After Installation
Once complete, the installation will show two buttons:
- "Go to Application" - Takes you to the main calculator
- "Admin Dashboard" - Takes you to the admin panel

### 5. If You Get Errors
1. Check the debug tool at `http://localhost/Bishwo_Calculator/install_test_installation.php`
2. Make sure your MySQL/MariaDB service is running in Laragon
3. Ensure the `storage` directory is writable

### 6. Direct URLs
After successful installation, you can access:
- Main App: `http://localhost/Bishwo_Calculator/public/index.php`
- Admin Panel: `http://localhost/Bishwo_Calculator/public/index.php?url=admin`

## Troubleshooting
- **Database Connection Failed**: Check your MySQL/MariaDB service and credentials
- **Permission Denied**: Make sure the `storage` directory is writable
- **404 Errors**: Make sure Laragon is serving from the correct directory
- **Missing Extensions**: Install required PHP extensions (PDO, PDO_MySQL, etc.)

## Need Help?
Run the debug tool first - it will show exactly what's working and what needs attention.
