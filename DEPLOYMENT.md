# Production Deployment Checklist

## Pre-Deployment

### 1. Environment Configuration
- [ ] Copy `.env.production` to `.env`
- [ ] Update `APP_URL` with your production domain
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database credentials
- [ ] Verify `APP_BASE` path is correct

### 2. Database Setup
- [ ] Create production database
- [ ] Import `database/database.sql`
- [ ] Run `php install/apply_indexes.php` for performance
- [ ] Run `php install/sync_modules.php` to populate modules
- [ ] Run `php install/activate_modules.php` to activate all modules

### 3. Security Checklist
- [ ] Verify `APP_DEBUG=false` in production `.env`
- [ ] Ensure HTTPS is enabled on your server
- [ ] Security headers are automatically applied via `SecurityMiddleware`
- [ ] CSRF protection is enabled on all forms
- [ ] Database user has minimal required permissions

### 4. File Permissions
```bash
chmod 755 public/
chmod 644 public/index.php
chmod 755 storage/
chmod 755 storage/logs/
chmod 755 storage/app/
chmod 644 .env
```

### 5. Web Server Configuration

#### Apache (.htaccess already configured)
- Ensure `mod_rewrite` is enabled
- Document root should point to `public/` directory

#### Nginx (sample configuration)
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/Bishwo_Calculator/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Post-Deployment

### 1. Verification
- [ ] Access admin panel: `/admin`
- [ ] Test module functionality
- [ ] Verify calculator pages load
- [ ] Check error logs: `storage/logs/php_error.log`

### 2. Performance
- [ ] Enable OPcache in PHP
- [ ] Configure browser caching
- [ ] Consider CDN for static assets

### 3. Monitoring
- [ ] Set up error monitoring
- [ ] Configure log rotation
- [ ] Monitor database performance

## Security Notes

**CRITICAL:**
- Never commit `.env` to version control
- Keep `APP_DEBUG=false` in production
- Use strong database passwords
- Regularly update dependencies: `composer update`
- Monitor error logs for security issues

## Troubleshooting

### White Screen / 500 Error
1. Check `storage/logs/php_error.log`
2. Verify file permissions
3. Ensure `.env` is configured correctly
4. Check database connection

### Modules Not Loading
1. Run `php install/sync_modules.php`
2. Verify `modules/` directory exists
3. Check database `modules` table

### Assets Not Loading
1. Verify document root points to `public/`
2. Check `.htaccess` is present
3. Ensure `mod_rewrite` is enabled (Apache)
