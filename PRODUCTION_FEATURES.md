# Production Features Documentation

This document covers all production-ready features implemented in the ERP system.

## 📋 Table of Contents

1. [Error Monitoring (Sentry)](#1-error-monitoring-sentry)
2. [CDN for Images](#2-cdn-for-images)
3. [Email Notifications](#3-email-notifications)
4. [Redis-based Rate Limiting](#4-redis-based-rate-limiting)
5. [SSL/TLS Configuration](#5-ssltls-configuration)
6. [Load Testing](#6-load-testing)
7. [Production Database](#7-production-database)

---

## 1. Error Monitoring (Sentry)

### Setup

1. **Create a Sentry account**: Visit [sentry.io](https://sentry.io) and create a project

2. **Install Sentry SDK**:
   ```bash
   composer require sentry/sentry-laravel
   ```

3. **Configure in `.env`**:
   ```env
   SENTRY_LARAVEL_DSN=https://your-key@sentry.io/project-id
   SENTRY_RELEASE=1.0.0
   SENTRY_TRACES_SAMPLE_RATE=0.2
   ```

4. **Publish config**:
   ```bash
   php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
   ```

### Features
- ✅ Automatic exception tracking
- ✅ Performance monitoring
- ✅ Breadcrumbs for debugging context
- ✅ SQL query tracking
- ✅ Ignores common exceptions (404, validation, etc.)

### Configuration File
See [config/sentry.php](config/sentry.php)

---

## 2. CDN for Images

### Setup

**Option A: Local Storage (Default)**
- Images stored in `storage/app/public/images`
- Accessible via `/storage/images`

**Option B: AWS S3/CloudFront CDN**

1. **Install AWS SDK**:
   ```bash
   composer require league/flysystem-aws-s3-v3 "^3.0"
   ```

2. **Configure in `.env`**:
   ```env
   CDN_ENABLED=true
   CDN_DRIVER=s3
   CDN_URL=https://cdn.your-domain.com
   CDN_ACCESS_KEY_ID=your-access-key
   CDN_SECRET_ACCESS_KEY=your-secret-key
   CDN_REGION=us-east-1
   CDN_BUCKET=erp-images
   ```

### Usage

```php
use App\Services\ImageService;

$imageService = new ImageService();

// Upload image
$url = $imageService->upload($request->file('image'), 'products');

// Upload with thumbnail
$urls = $imageService->uploadWithThumbnail($request->file('image'), 'products');

// Delete image
$imageService->delete($imageUrl);
```

### Features
- ✅ Automatic image optimization
- ✅ Thumbnail generation
- ✅ Quality compression (80% default)
- ✅ Automatic resizing for large images
- ✅ CDN support for fast delivery

### Configuration File
See [config/filesystems.php](config/filesystems.php)

---

## 3. Email Notifications

### Setup

1. **Configure mail in `.env`**:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@your-domain.com"
   MAIL_FROM_NAME="ERP System"
   ```

2. **For Gmail**: Generate an App Password from Google Account settings

### Available Notifications

#### Database Backup Notification
```php
use App\Notifications\DatabaseBackupCompleted;

$user->notify(new DatabaseBackupCompleted([
    'date' => now()->format('Y-m-d H:i:s'),
    'database' => 'erp_production',
    'size' => '25 MB',
    'location' => 'Telegram',
]));
```

#### Low Stock Alert
```php
use App\Notifications\LowStockAlert;

$user->notify(new LowStockAlert($product, $currentStock));
```

#### Payment Due Reminder
```php
use App\Notifications\PaymentDueReminder;

$user->notify(new PaymentDueReminder($sale, $daysOverdue));
```

### Features
- ✅ Queued notifications (won't slow down requests)
- ✅ Email and database channels
- ✅ Automatic retry on failure
- ✅ Professional email templates

### Notification Files
- [DatabaseBackupCompleted](app/Notifications/DatabaseBackupCompleted.php)
- [LowStockAlert](app/Notifications/LowStockAlert.php)
- [PaymentDueReminder](app/Notifications/PaymentDueReminder.php)

---

## 4. Redis-based Rate Limiting

### Setup

1. **Install Redis**:
   ```bash
   sudo apt-get install redis-server
   sudo systemctl enable redis-server
   ```

2. **Install PHP Redis extension**:
   ```bash
   sudo apt-get install php-redis
   ```

3. **Configure in `.env`**:
   ```env
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   RATE_LIMITER_STORE=redis
   
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   REDIS_PASSWORD=null
   
   RATE_LIMIT_API=60
   RATE_LIMIT_AUTH=5
   RATE_LIMIT_GLOBAL=1000
   ```

### Usage

Apply to routes in `routes/web.php`:
```php
use App\Http\Middleware\RateLimitMiddleware;

Route::middleware(['throttle:60,1'])->group(function () {
    // 60 requests per minute
});

Route::middleware([RateLimitMiddleware::class . ':5,1'])->group(function () {
    // Custom rate limiting
});
```

### Features
- ✅ Persistent across restarts
- ✅ Works in multi-server environments
- ✅ Per-user and per-IP limiting
- ✅ Custom response headers
- ✅ Configurable limits

### Configuration File
See [config/ratelimit.php](config/ratelimit.php)

---

## 5. SSL/TLS Configuration

### Automated Setup

Run the setup script:
```bash
sudo chmod +x scripts/setup-ssl.sh
sudo ./scripts/setup-ssl.sh
```

### Manual Setup

1. **Install Certbot**:
   ```bash
   sudo apt-get update
   sudo apt-get install certbot python3-certbot-nginx
   ```

2. **Obtain SSL Certificate**:
   ```bash
   sudo certbot --nginx -d your-domain.com -d www.your-domain.com
   ```

3. **Copy Nginx configuration**:
   ```bash
   sudo cp nginx-ssl.conf /etc/nginx/sites-available/erp
   sudo ln -s /etc/nginx/sites-available/erp /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl restart nginx
   ```

### Features
- ✅ TLS 1.2 and 1.3 support
- ✅ Strong cipher suites
- ✅ OCSP stapling
- ✅ HTTP/2 enabled
- ✅ Security headers (HSTS, CSP, etc.)
- ✅ Automatic certificate renewal
- ✅ A+ SSL rating ready

### Configuration Files
- [nginx-ssl.conf](nginx-ssl.conf)
- [scripts/setup-ssl.sh](scripts/setup-ssl.sh)

### Test SSL Configuration
Visit: https://www.ssllabs.com/ssltest/analyze.html?d=your-domain.com

---

## 6. Load Testing

### Tools Included
- **Apache Bench (ab)** - Simple HTTP load testing
- **K6** - Advanced performance testing

### Running Tests

**Quick test with Apache Bench**:
```bash
chmod +x scripts/load-test.sh
./scripts/load-test.sh
```

**Custom test**:
```bash
BASE_URL=https://your-domain.com CONCURRENT_USERS=100 TOTAL_REQUESTS=5000 ./scripts/load-test.sh
```

**K6 test only**:
```bash
k6 run load-test.js
```

### Test Scenarios
- Homepage load test
- Login page stress test
- Static assets performance
- API endpoint testing

### Results
Results are saved in `load-test-results/TIMESTAMP/`:
- `SUMMARY.md` - Test summary report
- `homepage.txt` - Homepage test results
- `login.txt` - Login page results
- `*.tsv` - Timing data for graphs

### Performance Targets
- ✅ Response time < 500ms (95th percentile)
- ✅ Error rate < 5%
- ✅ Support 100+ concurrent users
- ✅ 60+ requests/second

### Configuration Files
- [load-test.js](load-test.js)
- [scripts/load-test.sh](scripts/load-test.sh)

---

## 7. Production Database

### Automated Setup

Run the setup script:
```bash
sudo chmod +x scripts/setup-production-db.sh
sudo ./scripts/setup-production-db.sh
```

### Manual Setup

1. **Create database and user**:
   ```sql
   CREATE DATABASE erp_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'erp_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT ALL PRIVILEGES ON erp_production.* TO 'erp_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. **Optimize MySQL** (`/etc/mysql/conf.d/production.cnf`):
   ```ini
   [mysqld]
   max_connections = 200
   innodb_buffer_pool_size = 1G
   query_cache_size = 64M
   slow_query_log = 1
   log_bin = /var/log/mysql/mysql-bin.log
   ```

3. **Configure in `.env`**:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_DATABASE=erp_production
   DB_USERNAME=erp_user
   DB_PASSWORD=your_strong_password
   ```

4. **Run migrations**:
   ```bash
   php artisan migrate --force
   ```

### Features
- ✅ Read/Write separation support
- ✅ SSL/TLS connection support
- ✅ Optimized InnoDB settings
- ✅ Query caching enabled
- ✅ Binary logging for backups
- ✅ Slow query logging
- ✅ Connection pooling
- ✅ Prepared statements

### Configuration File
See [config/database.php](config/database.php)

### Monitoring

Check database performance:
```bash
mysql -u erp_user -p -e "SHOW PROCESSLIST;"
mysql -u erp_user -p -e "SHOW ENGINE INNODB STATUS\G"
```

View slow queries:
```bash
sudo tail -f /var/log/mysql/slow-query.log
```

---

## 🚀 Quick Start Checklist

### Essential Setup (Required)

- [ ] Configure database (run `setup-production-db.sh`)
- [ ] Set up SSL certificate (run `setup-ssl.sh`)
- [ ] Configure `.env` file with production values
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate application key: `php artisan key:generate`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`

### Recommended Setup

- [ ] Install and configure Redis
- [ ] Set up Sentry error monitoring
- [ ] Configure email notifications
- [ ] Set up automated database backups (see BACKUP_SYSTEM.md)
- [ ] Configure CDN for images (optional)
- [ ] Run load tests
- [ ] Set up monitoring (htop, netdata, etc.)

### Security Checklist

- [ ] SSL/TLS enabled and tested
- [ ] Strong database passwords
- [ ] Redis password set (if exposed)
- [ ] Firewall configured (ufw)
- [ ] Rate limiting enabled
- [ ] Security headers configured
- [ ] File permissions correct (755 for directories, 644 for files)
- [ ] `.env` file secured (600 permissions)
- [ ] Disable directory listing

---

## 📊 Monitoring & Maintenance

### Daily Tasks
- Check error logs: `tail -f storage/logs/laravel.log`
- Monitor Sentry dashboard
- Review slow queries
- Check disk space: `df -h`

### Weekly Tasks
- Review load test results
- Check backup status
- Update dependencies: `composer update`
- Review database performance

### Monthly Tasks
- Security updates
- Certificate renewal check
- Database optimization: `php artisan db:optimize`
- Clean old logs: `php artisan log:clear`

---

## 🆘 Troubleshooting

### High CPU Usage
1. Check slow queries
2. Enable query caching
3. Optimize database indexes
4. Increase PHP-FPM workers

### High Memory Usage
1. Increase Redis maxmemory
2. Optimize InnoDB buffer pool
3. Check for memory leaks in code
4. Review queue workers

### Slow Response Times
1. Enable Redis caching
2. Use CDN for static assets
3. Optimize database queries
4. Enable OPcache for PHP

### Database Connection Issues
1. Check max_connections in MySQL
2. Verify credentials in `.env`
3. Check firewall rules
4. Review connection pooling settings

---

## 📞 Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review Sentry error reports
3. Run diagnostics: `php artisan about`
4. Check system resources: `htop`

---

## 📚 Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [MySQL Performance Tuning](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)
- [Nginx Security Best Practices](https://nginx.org/en/docs/http/configuring_https_servers.html)
- [Redis Configuration](https://redis.io/docs/management/config/)
- [Sentry Documentation](https://docs.sentry.io/platforms/php/guides/laravel/)
