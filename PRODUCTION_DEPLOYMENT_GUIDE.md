# Bishwo Calculator - Production Deployment Guide

**Version:** 1.0.0
**Date:** December 5, 2025
**Status:** Production Ready ✅

## 🚀 Quick Start Deployment

### Prerequisites
- **Web Server:** Apache 2.4+ or Nginx 1.18+
- **PHP:** 8.3.16 or higher
- **Database:** MySQL 5.7+ or MariaDB 10.3+
- **SSL Certificate:** Required for production

### One-Click Deployment Steps

#### 1. Server Setup
```bash
# Upload files to web root
scp -r /local/path/Bishwo_Calculator user@server:/var/www/html/

# Set proper permissions
chown -R www-data:www-data /var/www/html/Bishwo_Calculator
chmod -R 755 /var/www/html/Bishwo_Calculator
chmod -R 777 /var/www/html/Bishwo_Calculator/debug/logs/
```

#### 2. Database Configuration
```sql
-- Create database
CREATE DATABASE bishwo_calculator;

-- Import schema
mysql -u username -p bishwo_calculator < install/database.sql

-- Run migrations
php database/migrate.php
```

#### 3. Environment Configuration
```bash
# Copy and configure environment file
cp .env.example .env.production
nano .env.production

# Required settings:
APP_ENV=production
APP_URL=https://your-domain.com
DB_HOST=localhost
DB_NAME=bishwo_calculator
DB_USER=your_db_user
DB_PASS=your_db_password
```

#### 4. Web Server Configuration

**Apache (.htaccess already configured)**
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/Bishwo_Calculator

    <Directory /var/www/html/Bishwo_Calculator>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/Bishwo_Calculator;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

#### 5. SSL Configuration
```bash
# Install certbot
apt install certbot python3-certbot-apache

# Get SSL certificate
certbot --apache -d your-domain.com

# Force HTTPS redirect (add to .htaccess)
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 🔧 Post-Deployment Configuration

### Admin Setup
1. Visit `https://your-domain.com/install/`
2. Follow the installation wizard
3. Create admin account
4. Configure email settings

### Security Hardening
```bash
# Secure PHP configuration
php.ini settings:
memory_limit = 256M
max_execution_time = 30
upload_max_filesize = 10M
post_max_size = 10M

# Disable dangerous functions
disable_functions = exec,passthru,shell_exec,system,proc_open,popen
```

### Performance Optimization
```bash
# Enable OPcache
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=7963

# Database optimization
CREATE INDEX idx_calculations_user ON calculations_history(user_id);
CREATE INDEX idx_sessions_expiry ON sessions(expires_at);
```

---

## 🧪 Testing & Validation

### Pre-Production Testing
```bash
# Run full test suite
cd /var/www/html/Bishwo_Calculator
npm install
npm test

# Health check
curl https://your-domain.com/api/v1/health

# Calculator test
curl https://your-domain.com/api/calculators
```

### Production Validation Checklist
- [ ] All calculators load correctly
- [ ] User registration works
- [ ] Admin panel accessible
- [ ] Email notifications send
- [ ] SSL certificate valid
- [ ] Response times < 2 seconds
- [ ] No PHP errors in logs

---

## 📊 Monitoring & Maintenance

### Daily Monitoring
```bash
# Check application health
curl https://your-domain.com/api/v1/health

# Monitor logs
tail -f debug/logs/error.log
tail -f debug/logs/access.log

# Database connections
mysql -e "SHOW PROCESSLIST;" bishwo_calculator
```

### Weekly Maintenance
```bash
# Clear old logs
find debug/logs/ -name "*.log" -mtime +7 -delete

# Optimize database
mysql -e "OPTIMIZE TABLE calculations_history, users, sessions;" bishwo_calculator

# Update dependencies
composer update --no-dev
npm update --production
```

### Monthly Backups
```bash
# Database backup
mysqldump bishwo_calculator > backup_$(date +%Y%m%d).sql

# File backup
tar -czf files_backup_$(date +%Y%m%d).tar.gz /var/www/html/Bishwo_Calculator

# Upload to remote storage
scp backup_*.sql user@backup-server:/backups/
scp files_backup_*.tar.gz user@backup-server:/backups/
```

---

## 🚨 Troubleshooting Guide

### Common Issues & Solutions

#### Issue: 500 Internal Server Error
**Symptoms:** White page, PHP errors
**Solutions:**
```bash
# Check PHP error logs
tail -f /var/log/php8.3-fpm.log

# Verify file permissions
ls -la /var/www/html/Bishwo_Calculator

# Check .htaccess syntax
apachectl -t
```

#### Issue: Database Connection Failed
**Symptoms:** "Can't connect to database" errors
**Solutions:**
```bash
# Test database connection
mysql -h localhost -u db_user -p bishwo_calculator

# Check database credentials in config
cat config/database.php

# Verify MySQL service
systemctl status mysql
```

#### Issue: Slow Performance
**Symptoms:** Response times > 5 seconds
**Solutions:**
```bash
# Enable OPcache
php -r "var_dump(opcache_get_status());"

# Check database slow queries
mysql -e "SHOW PROCESSLIST;" bishwo_calculator

# Monitor server resources
top
free -h
df -h
```

#### Issue: CSRF Token Errors
**Symptoms:** 419 errors on form submissions
**Solutions:**
```bash
# Clear browser cache
# Check session configuration
cat config/app.php | grep session

# Verify CSRF middleware
grep -r "csrf" app/Middleware/
```

---

## 📈 Scaling Considerations

### Vertical Scaling (Single Server)
```bash
# Increase PHP workers
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20

# Database connection pooling
max_connections = 100
wait_timeout = 28800
```

### Horizontal Scaling (Multiple Servers)
1. **Load Balancer Configuration**
2. **Shared Session Storage** (Redis/Memcached)
3. **Database Read Replicas**
4. **CDN for Static Assets**

### Database Optimization
```sql
-- Add indexes for performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_calculations_created ON calculations_history(created_at);
CREATE INDEX idx_sessions_user ON sessions(user_id);

-- Partition large tables
ALTER TABLE calculations_history
PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027)
);
```

---

## 🔒 Security Best Practices

### Server Security
```bash
# Disable root login
sed -i 's/#PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config

# Install firewall
ufw enable
ufw allow 22,80,443

# Regular updates
apt update && apt upgrade

# Fail2Ban for brute force protection
apt install fail2ban
```

### Application Security
- **Regular Security Audits:** Monthly
- **Dependency Updates:** Weekly
- **Backup Verification:** Daily
- **Log Monitoring:** Real-time

### Data Protection
- **Encryption at Rest:** Database encryption
- **Backup Encryption:** AES-256
- **GDPR Compliance:** Data retention policies
- **User Data Export:** Self-service tools

---

## 📞 Support & Emergency Contacts

### Emergency Response
1. **Check Application Status:** `curl https://your-domain.com/api/v1/health`
2. **Review Error Logs:** `tail -f debug/logs/error.log`
3. **Database Connectivity:** `mysql -e "SELECT 1;" bishwo_calculator`
4. **Server Resources:** `top` and `df -h`

### Support Resources
- **Documentation:** `TEST_EXECUTION_REPORT.md`
- **Test Suite:** `npm test`
- **Health Checks:** `/api/v1/health`
- **System Status:** `/admin/system-status`

### Backup Recovery
```bash
# Emergency database restore
mysql bishwo_calculator < latest_backup.sql

# File system restore
tar -xzf latest_files_backup.tar.gz -C /var/www/html/

# Verify restoration
curl https://your-domain.com/api/v1/health
```

---

## 🎯 Success Metrics

### Performance Targets
- **Response Time:** < 2 seconds for 95% of requests
- **Uptime:** > 99.9% monthly
- **Error Rate:** < 0.1% of total requests
- **User Satisfaction:** > 4.5/5 rating

### Business Metrics
- **Active Users:** Track daily/monthly
- **Calculator Usage:** Most popular tools
- **Conversion Rate:** Free to paid users
- **Support Tickets:** Response time < 24 hours

---

## 🚀 Future Roadmap

### Phase 1 (Next 3 Months)
- [ ] Mobile app development
- [ ] Advanced calculator features
- [ ] Multi-language support
- [ ] API rate limiting

### Phase 2 (Next 6 Months)
- [ ] AI-powered calculations
- [ ] Integration with CAD software
- [ ] Advanced reporting dashboard
- [ ] White-label solutions

### Phase 3 (Next 12 Months)
- [ ] Enterprise features
- [ ] Team collaboration tools
- [ ] Advanced analytics
- [ ] Global expansion

---

*This guide ensures your Bishwo Calculator deployment is secure, performant, and maintainable. Regular updates and monitoring will keep your application running smoothly for thousands of engineering professionals worldwide.*

**Happy Deploying! 🎉**