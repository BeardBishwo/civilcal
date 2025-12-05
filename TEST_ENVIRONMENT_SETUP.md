# Bishwo Calculator API Test Environment Setup

## 1. Test Environment Architecture

### 1.1 Environment Overview
```
┌───────────────────────────────────────────────────────┐
│                 PRODUCTION ENVIRONMENT                  │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐  │
│  │  Web Server  │    │  API Server  │    │  Database    │  │
│  └─────────────┘    └─────────────┘    └─────────────┘  │
└───────────────────────────────────────────────────────┘
                            ↑
                            │ (Data replication)
                            ↓
┌───────────────────────────────────────────────────────┐
│                  STAGING ENVIRONMENT                    │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐  │
│  │  Web Server  │    │  API Server  │    │  Database    │  │
│  └─────────────┘    └─────────────┘    └─────────────┘  │
└───────────────────────────────────────────────────────┘
                            ↑
                            │ (Configuration sync)
                            ↓
┌───────────────────────────────────────────────────────┐
│                DEVELOPMENT ENVIRONMENT                  │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐  │
│  │  Web Server  │    │  API Server  │    │  Database    │  │
│  └─────────────┘    └─────────────┘    └─────────────┘  │
└───────────────────────────────────────────────────────┘
```

### 1.2 Environment Specifications

| Environment | Purpose | URL | Database | Data Volume |
|-------------|---------|-----|----------|------------|
| Development | Local testing, debugging | `http://localhost:8000` | SQLite/MySQL | Small (100-500 records) |
| Staging | Integration testing | `https://staging.bishwo-calculator.com` | MySQL | Medium (1,000-5,000 records) |
| Production | Live system | `https://bishwo-calculator.com` | MySQL (replicated) | Large (5,000+ records) |

## 2. Environment Setup Procedures

### 2.1 Development Environment Setup

#### 2.1.1 Prerequisites
- PHP 8.1+
- MySQL 5.7+ or SQLite 3.35+
- Composer 2.0+
- Node.js 16+ (for frontend testing)
- Git 2.30+

#### 2.1.2 Setup Commands
```bash
# Clone repository
git clone https://github.com/bishwo-calculator/bishwo-calculator.git
cd bishwo-calculator

# Install dependencies
composer install --dev
npm install

# Configure environment
cp .env.example .env
php -r "file_exists('.env') || copy('.env.example', '.env');"

# Generate application key
php -r "echo 'APP_KEY='.base64_encode(random_bytes(32)).PHP_EOL;"

# Setup database
php database/setup_db.php
php database/run_migration.php

# Seed test data
php database/seed_test_data.php --environment=development
```

### 2.2 Staging Environment Setup

#### 2.2.1 Infrastructure Requirements
- 2 vCPU, 4GB RAM minimum
- 20GB SSD storage
- Ubuntu 22.04 LTS
- Nginx/Apache web server
- PHP-FPM configuration
- MySQL 8.0+

#### 2.2.2 Deployment Process
```bash
# Server provisioning
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php8.1-fpm php8.1-mysql composer git

# Application deployment
git clone https://github.com/bishwo-calculator/bishwo-calculator.git /var/www/bishwo-calculator
cd /var/www/bishwo-calculator

# Configure web server
sudo cp config/nginx.conf /etc/nginx/sites-available/bishwo-calculator
sudo ln -s /etc/nginx/sites-available/bishwo-calculator /etc/nginx/sites-enabled/
sudo systemctl restart nginx

# Setup database
mysql -u root -p < database/install/database.sql
php database/run_migration.php --environment=staging

# Configure cron jobs
(crontab -l 2>/dev/null; echo "*/5 * * * * php /var/www/bishwo-calculator/cron/cleanup.php") | crontab -
```

## 3. Test Data Requirements

### 3.1 Data Categories

| Category | Description | Environment | Volume |
|----------|-------------|-------------|--------|
| User Accounts | Test users with different roles | All | 10-50 per environment |
| Calculator Inputs | Sample calculation parameters | All | 50-200 test cases |
| Admin Data | Module configurations, settings | Staging/Dev | 5-20 configurations |
| Edge Cases | Boundary values, invalid inputs | All | 30-100 scenarios |

### 3.2 Data Generation Strategies

#### 3.2.1 User Data Generation
```php
// Sample user data factory
function generateTestUsers($count = 10) {
    $users = [];
    $roles = ['user', 'engineer', 'admin'];

    for ($i = 0; $i < $count; $i++) {
        $users[] = [
            'username' => 'test_user_' . uniqid(),
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => password_hash('TestPassword123!', PASSWORD_DEFAULT),
            'first_name' => 'Test',
            'last_name' => 'User ' . ($i + 1),
            'role' => $roles[array_rand($roles)],
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    return $users;
}
```

#### 3.2.2 Calculator Test Data
```json
{
  "civil_brickwork": {
    "valid": {
      "length": 10,
      "width": 5,
      "height": 3,
      "brick_size": "standard",
      "mortar_thickness": 10
    },
    "edge_cases": {
      "zero_values": {
        "length": 0,
        "width": 0,
        "height": 0
      },
      "maximum_values": {
        "length": 99999,
        "width": 99999,
        "height": 99999
      },
      "invalid_types": {
        "length": "ten",
        "width": "five",
        "height": "three"
      }
    }
  }
}
```

## 4. Environment Configuration

### 4.1 Configuration Files

#### 4.1.1 Database Configuration (`config/database.php`)
```php
return [
    'development' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'bishwo_calculator_dev',
        'username' => 'dev_user',
        'password' => 'secure_password_dev',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => ''
    ],
    'staging' => [
        'driver' => 'mysql',
        'host' => 'db.staging.internal',
        'database' => 'bishwo_calculator_staging',
        'username' => 'staging_user',
        'password' => 'secure_password_staging',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => ''
    ],
    'production' => [
        'driver' => 'mysql',
        'host' => 'db.production.internal',
        'database' => 'bishwo_calculator_prod',
        'username' => 'prod_user',
        'password' => 'secure_password_prod',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => ''
    ]
];
```

### 4.2 Environment Variables (`.env`)
```
# Application
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=bishwo_calculator_dev
DB_USERNAME=dev_user
DB_PASSWORD=secure_password_dev

# Testing
TESTING=true
TEST_DATABASE_PREFIX=test_
TEST_DATA_SEEDING=true

# Security
APP_KEY=base64:generated_key_here
JWT_SECRET=secure_jwt_secret_here
SESSION_LIFETIME=120
```

## 5. Test Data Management

### 5.1 Data Isolation Strategies

#### 5.1.1 Database Prefixing
```sql
-- Create test database with prefix
CREATE DATABASE `test_bishwo_calculator` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use test database for testing
USE `test_bishwo_calculator`;
```

#### 5.1.2 Transactional Testing
```php
// PHPUnit transactional test example
class DatabaseTest extends TestCase
{
    use DatabaseTransactions;

    public function testUserCreation()
    {
        // This test runs in a transaction and rolls back automatically
        $user = User::create([
            'username' => 'test_user',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }
}
```

### 5.2 Data Cleanup Procedures
```bash
# Cleanup test data between runs
php artisan test:cleanup

# Reset database to known state
php database/reset_test_db.php

# Remove temporary files
rm -rf storage/framework/testing/*
rm -rf bootstrap/cache/*
```

## 6. Environment Validation

### 6.1 Health Check Endpoints
- `GET /api/health` - Basic health check
- `GET /api/admin/health` - Comprehensive system health (admin only)
- `GET /api/version` - API version information

### 6.2 Validation Scripts
```bash
# Environment validation script
#!/bin/bash

# Check PHP version
php_version=$(php -r "echo PHP_VERSION;")
if [[ ! $php_version =~ ^8\.[1-9] ]]; then
    echo "ERROR: PHP version $php_version not supported. Requires 8.1+"
    exit 1
fi

# Check extensions
required_extensions=("pdo" "pdo_mysql" "mbstring" "json" "session")
for ext in "${required_extensions[@]}"; do
    if ! php -m | grep -q "^$ext$"; then
        echo "ERROR: PHP extension $ext not installed"
        exit 1
    fi
done

# Check database connection
if ! mysql -u${DB_USERNAME} -p${DB_PASSWORD} -e "SHOW DATABASES;" > /dev/null 2>&1; then
    echo "ERROR: Database connection failed"
    exit 1
fi

echo "Environment validation passed!"
exit 0
```

## 7. Continuous Integration Setup

### 7.1 CI Pipeline Configuration (`.github/workflows/test.yml`)
```yaml
name: API Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-22.04

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: bishwo_calculator_test
          MYSQL_USER: test_user
          MYSQL_PASSWORD: test_password
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: mbstring, pdo, pdo_mysql, json, session

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress

      - name: Setup database
        run: |
          mysql -h 127.0.0.1 -P 3306 -uroot -proot -e "CREATE DATABASE IF NOT EXISTS bishwo_calculator_test;"
          php database/setup_db.php --environment=testing
          php database/run_migration.php --environment=testing

      - name: Run API tests
        run: ./vendor/bin/phpunit tests/Api/

      - name: Run integration tests
        run: ./vendor/bin/phpunit tests/Integration/

      - name: Generate coverage report
        run: ./vendor/bin/phpunit --coverage-clover=coverage.xml

      - name: Upload coverage to Codecov
        uses: codecov/codecov-action@v3
        with:
          file: coverage.xml
```

## 8. Performance Testing Environment

### 8.1 Load Testing Setup
```bash
# Install k6 for load testing
sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys C5AD17C747E3415A3642D57D77C6C491D6AC1D69
echo "deb https://dl.k6.io/deb stable main" | sudo tee /etc/apt/sources.list.d/k6.list
sudo apt update
sudo apt install k6

# Sample load test script
k6 run --vus 50 --duration 30s scripts/load_test.js
```

### 8.2 Performance Monitoring
```json
{
  "performance_metrics": {
    "response_time": {
      "threshold": 500, // ms
      "warning": 300 // ms
    },
    "throughput": {
      "minimum_rps": 100, // requests per second
      "target_rps": 500
    },
    "error_rate": {
      "maximum": 0.1 // 0.1%
    },
    "memory_usage": {
      "warning": 512, // MB
      "critical": 1024 // MB
    }
  }
}
```

## 9. Security Testing Environment

### 9.1 Security Testing Tools
- OWASP ZAP for vulnerability scanning
- SQLMap for injection testing
- Burp Suite for manual security testing
- PHPStan for static code analysis

### 9.2 Security Test Configuration
```yaml
# OWASP ZAP configuration
zap:
  target: "https://staging.bishwo-calculator.com"
  contexts:
    - name: "API Context"
      include:
        - ".*/api/.*"
      exclude:
        - ".*/api/admin/.*"
  policies:
    - name: "High Risk Scan"
      strength: "HIGH"
      thresholds:
        high: 0
        medium: 5
        low: 10
```

## 10. Cross-Environment Considerations

### 10.1 Environment-Specific Testing

| Test Type | Development | Staging | Production |
|-----------|-------------|---------|------------|
| Unit Tests | ✅ Full | ✅ Full | ❌ None |
| Integration Tests | ✅ Full | ✅ Full | ❌ None |
| API Tests | ✅ Full | ✅ Full | ✅ Limited |
| Load Tests | ❌ None | ✅ Full | ✅ Monitoring |
| Security Tests | ✅ Basic | ✅ Full | ✅ Monitoring |
| Smoke Tests | ✅ Basic | ✅ Full | ✅ Continuous |

### 10.2 Environment Synchronization
```bash
# Sync configuration from staging to development
rsync -avz --exclude='.env' staging_user@staging:/var/www/bishwo-calculator/config/ ./config/

# Database schema synchronization
mysqldump -h staging_db -u staging_user -p --no-data bishwo_calculator_staging | mysql -u dev_user -p bishwo_calculator_dev
```

## 11. Disaster Recovery Testing

### 11.1 Backup & Restore Procedures
```bash
# Create backup
php api/admin/backup.php --output=backup_$(date +%Y%m%d_%H%M%S).sql

# Restore from backup
mysql -u root -p bishwo_calculator_dev < backup_20231205_143022.sql

# Test backup integrity
php tests/verify_backup.php --backup-file=backup_20231205_143022.sql
```

### 11.2 Failover Testing
```bash
# Simulate database failure
systemctl stop mysql

# Test application behavior
curl -I http://localhost:8000/api/health

# Restore database
systemctl start mysql
```

## 12. Compliance & Audit Testing

### 12.1 Compliance Checklist
- [ ] Data encryption at rest and in transit
- [ ] Proper authentication and authorization
- [ ] Audit logging for sensitive operations
- [ ] Regular security patching
- [ ] Backup verification procedures

### 12.2 Audit Log Testing
```php
// Test audit logging functionality
public function testAuditLogging()
{
    // Perform admin action
    $response = $this->actingAs($adminUser)
                     ->post('/api/admin/modules', [
                         'module' => 'analytics',
                         'action' => 'activate'
                     ]);

    $response->assertStatus(200);

    // Verify audit log entry
    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $adminUser->id,
        'action' => 'module_activate',
        'module' => 'analytics'
    ]);
}
```

## 13. Environment-Specific Documentation

### 13.1 Development Environment Cheat Sheet
```markdown
# Development Quick Start

1. **Start services**:
   ```bash
   sudo systemctl start mysql
   php -S localhost:8000 -t public
   ```

2. **Common commands**:
   ```bash
   # Reset database
   php database/reset_dev_db.php

   # Run specific tests
   ./vendor/bin/phpunit tests/Api/AuthTest.php

   # Debug mode
   export APP_DEBUG=true
   ```

3. **Debugging tools**:
   - Xdebug for step debugging
   - Laravel Telescope for request inspection
   - Database query logging
```

### 13.2 Staging Environment Operations
```markdown
# Staging Environment Operations

1. **Deployment**:
   ```bash
   git pull origin main
   composer install --no-dev
   php database/run_migration.php
   sudo systemctl restart php8.1-fpm
   sudo systemctl restart nginx
   ```

2. **Monitoring**:
   ```bash
   # Check logs
   tail -f /var/log/nginx/error.log
   tail -f /var/www/bishwo-calculator/storage/logs/laravel.log

   # System monitoring
   htop
   df -h
   free -m
   ```

3. **Maintenance**:
   ```bash
   # Clear caches
   php artisan cache:clear
   php artisan view:clear

   # Database optimization
   php artisan optimize
   ```
```

## 14. Environment Lifecycle Management

### 14.1 Environment Refresh Procedures
```bash
# Full environment refresh (staging)
./scripts/refresh_environment.sh --confirm

# Partial refresh (development)
php database/reset_test_data.php
composer dump-autoload
```

### 14.2 Environment Deprecation
```markdown
# Environment Deprecation Checklist

- [ ] Notify all users of environment shutdown
- [ ] Backup all data and configurations
- [ ] Export test results and metrics
- [ ] Document lessons learned
- [ ] Archive environment configuration
- [ ] Update documentation to reflect changes
```

## 15. Appendices

### 15.1 Environment Comparison Matrix

| Feature | Development | Staging | Production |
|---------|-------------|---------|------------|
| Debug Mode | Enabled | Disabled | Disabled |
| Error Reporting | Detailed | Limited | Minimal |
| Caching | Disabled | Enabled | Enabled |
| Logging Level | Debug | Info | Warning |
| Rate Limiting | Disabled | Enabled | Enabled |
| SSL | Self-signed | Valid cert | Valid cert |
| Backup Frequency | Manual | Daily | Hourly |
| Monitoring | Basic | Comprehensive | Enterprise |

### 15.2 Common Environment Issues & Solutions

| Issue | Environment | Solution |
|-------|-------------|----------|
| Database connection failed | All | Check credentials, verify service running |
| Slow response times | Staging/Prod | Optimize queries, add caching |
| Missing dependencies | Development | Run `composer install` |
| Permission errors | Staging | Check file permissions, SELinux context |
| Memory limits exceeded | All | Increase PHP memory_limit, optimize code |
| Session issues | All | Clear sessions, check cookie domains |

## 16. Version Control

This document is version controlled and should be updated whenever environment configurations change. All changes should be reviewed by the DevOps team before deployment to staging or production environments.

**Document Status**: Active
**Last Updated**: 2025-12-05
**Version**: 1.0
**Maintainer**: DevOps Team