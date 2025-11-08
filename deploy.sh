#!/bin/bash

# Bishwo Calculator Deployment Script
echo "🚀 Starting Bishwo Calculator Deployment..."

# Configuration
APP_NAME="bishwo-calculator"
APP_DIR="/var/www/$APP_NAME"
BACKUP_DIR="/var/backups/$APP_NAME"
DATE=$(date +%Y%m%d_%H%M%S)

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Logging
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARN:${NC} $1"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR:${NC} $1"
    exit 1
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    error "Please run as root"
fi

# Create backup
create_backup() {
    log "Creating backup..."
    mkdir -p $BACKUP_DIR
    
    if [ -d "$APP_DIR" ]; then
        tar -czf "$BACKUP_DIR/backup_$DATE.tar.gz" -C $(dirname $APP_DIR) $(basename $APP_DIR) 2>/dev/null
        if [ $? -eq 0 ]; then
            log "Backup created: $BACKUP_DIR/backup_$DATE.tar.gz"
        else
            warn "Failed to create backup"
        fi
    fi
}

# Deploy application
deploy_app() {
    log "Deploying application..."
    
    # Create app directory if it doesn't exist
    mkdir -p $APP_DIR
    
    # Copy application files (excluding development files)
    rsync -av --delete \
        --exclude='.git' \
        --exclude='.env.local' \
        --exclude='storage/logs/*' \
        --exclude='storage/cache/*' \
        --exclude='storage/sessions/*' \
        --exclude='storage/uploads/tmp/*' \
        --exclude='node_modules' \
        --exclude='vendor' \
        ./ $APP_DIR/
    
    # Set permissions
    chown -R www-data:www-data $APP_DIR
    chmod -R 755 $APP_DIR
    chmod -R 777 $APP_DIR/storage
    chmod 600 $APP_DIR/.env
    
    log "Application files deployed"
}

# Install dependencies
install_dependencies() {
    log "Installing PHP dependencies..."
    cd $APP_DIR
    composer install --no-dev --optimize-autoloader
    
    log "Installing frontend dependencies..."
    if [ -f "package.json" ]; then
        npm install --production
        npm run build
    fi
}

# Setup database
setup_database() {
    log "Setting up database..."
    cd $APP_DIR
    
    # Run migrations
    php database/migrate.php
    
    # Seed initial data if needed
    if [ -f "database/seed.php" ]; then
        php database/seed.php
    fi
}

# Configure web server
configure_webserver() {
    log "Configuring web server..."
    
    # Create nginx configuration
    cat > /etc/nginx/sites-available/$APP_NAME << EOF
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root $APP_DIR/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains";

    # Main location
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known) {
        deny all;
    }

    location ~ /(vendor|storage|config|debug) {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
}
EOF

    # Enable site
    ln -sf /etc/nginx/sites-available/$APP_NAME /etc/nginx/sites-enabled/
    
    # Test configuration
    nginx -t || error "Nginx configuration test failed"
    
    # Reload nginx
    systemctl reload nginx
    log "Nginx configuration updated"
}

# Setup SSL (Let's Encrypt)
setup_ssl() {
    if command -v certbot &> /dev/null; then
        log "Setting up SSL certificate..."
        certbot --nginx -d yourdomain.com -d www.yourdomain.com --non-interactive --agree-tos -m admin@yourdomain.com
        log "SSL certificate configured"
    else
        warn "Certbot not found. SSL setup skipped."
    fi
}

# Configure cron jobs
setup_cron() {
    log "Setting up cron jobs..."
    
    # Add cleanup job
    (crontab -l 2>/dev/null; echo "0 2 * * * php $APP_DIR/artisan schedule:run >> /dev/null 2>&1") | crontab -
    
    # Add backup job
    (crontab -l 2>/dev/null; echo "0 3 * * 0 $APP_DIR/scripts/backup.sh >> /dev/null 2>&1") | crontab -
    
    log "Cron jobs configured"
}

# Setup logging
setup_logging() {
    log "Setting up logging..."
    
    # Create logrotate configuration
    cat > /etc/logrotate.d/$APP_NAME << EOF
$APP_DIR/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
EOF
    
    log "Logging configured"
}

# Main deployment process
main() {
    log "Starting deployment of Bishwo Calculator..."
    
    create_backup
    deploy_app
    install_dependencies
    setup_database
    configure_webserver
    setup_ssl
    setup_cron
    setup_logging
    
    log "🎉 Deployment completed successfully!"
    log "📝 Next steps:"
    log "   1. Update .env file with production values"
    log "   2. Test the application"
    log "   3. Set up monitoring"
    log "   4. Configure backups"
}

# Run deployment
main
