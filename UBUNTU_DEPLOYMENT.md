# Ubuntu VPS Deployment Guide

## 🚀 Quick Deployment (One Command)

এক command-এ সবকিছু setup হয়ে যাবে:

```bash
# Root user হিসেবে run করুন
sudo su
cd /path/to/your/erp/folder
chmod +x deploy-ubuntu.sh
./deploy-ubuntu.sh
```

Script automatically করবে:
- ✅ System update
- ✅ Install করবে: Nginx, MySQL, Redis, PHP 8.2, Composer
- ✅ Firewall configure করবে
- ✅ MySQL database তৈরি করবে
- ✅ Redis configure করবে
- ✅ Application deploy করবে
- ✅ Nginx configure করবে
- ✅ SSL certificate setup করবে (domain থাকলে)
- ✅ Cron jobs setup করবে
- ✅ Queue worker service তৈরি করবে

## 📋 Requirements

- Ubuntu 20.04/22.04/24.04 LTS
- Root access
- Minimum 1GB RAM (2GB recommended)
- 20GB disk space

## 🔧 Manual Deployment (Step by Step)

যদি manually deploy করতে চান:

### Step 1: System Update
```bash
sudo apt-get update
sudo apt-get upgrade -y
```

### Step 2: Install Required Packages
```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update

# Install packages
sudo apt-get install -y nginx mysql-server redis-server \
    php8.2 php8.2-fpm php8.2-mysql php8.2-redis \
    php8.2-mbstring php8.2-xml php8.2-bcmath \
    php8.2-curl php8.2-zip php8.2-gd php8.2-intl \
    composer git unzip curl certbot python3-certbot-nginx
```

### Step 3: Configure MySQL
```bash
# Secure MySQL
sudo mysql_secure_installation

# Create database
sudo mysql -u root -p
```

MySQL console-এ:
```sql
CREATE DATABASE erp_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'erp_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON erp_production.* TO 'erp_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 4: Configure Redis
```bash
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

### Step 5: Deploy Application
```bash
# Create directory
sudo mkdir -p /var/www/html/erp
cd /var/www/html/erp

# Copy your files (or clone from git)
# git clone your-repo .

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup .env
cp .env.example .env
nano .env  # Edit করুন

# Generate key
php artisan key:generate

# Set permissions
sudo chown -R www-data:www-data /var/www/html/erp
sudo chmod -R 755 /var/www/html/erp
sudo chmod -R 775 /var/www/html/erp/storage
sudo chmod -R 775 /var/www/html/erp/bootstrap/cache

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Configure Nginx
```bash
sudo nano /etc/nginx/sites-available/erp
```

এই content যোগ করুন:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/erp/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable করুন:
```bash
sudo ln -s /etc/nginx/sites-available/erp /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

### Step 7: Setup SSL
```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### Step 8: Setup Cron
```bash
crontab -e
```

এই line যোগ করুন:
```
* * * * * cd /var/www/html/erp && php artisan schedule:run >> /dev/null 2>&1
```

### Step 9: Setup Queue Worker
```bash
sudo nano /etc/systemd/system/erp-worker.service
```

এই content যোগ করুন:
```ini
[Unit]
Description=ERP Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/html/erp/artisan queue:work redis

[Install]
WantedBy=multi-user.target
```

Enable করুন:
```bash
sudo systemctl daemon-reload
sudo systemctl enable erp-worker
sudo systemctl start erp-worker
```

## 🔧 Post-Deployment Configuration

### Configure Email
Edit `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### Configure Telegram Backup
Edit `.env`:
```env
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_chat_id
```

### Configure Sentry (Optional)
```bash
composer require sentry/sentry-laravel
```

Edit `.env`:
```env
SENTRY_LARAVEL_DSN=your-sentry-dsn
```

## 🎯 Testing

### Test Website
```bash
curl http://your-domain.com
```

### Test Database
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### Test Redis
```bash
redis-cli ping
# Should return: PONG
```

### Test Queue
```bash
sudo systemctl status erp-worker
```

## 📊 Monitoring Commands

### Check Services
```bash
sudo systemctl status nginx
sudo systemctl status mysql
sudo systemctl status redis-server
sudo systemctl status php8.2-fpm
sudo systemctl status erp-worker
```

### View Logs
```bash
# Nginx
sudo tail -f /var/log/nginx/erp-error.log

# Laravel
sudo tail -f /var/www/html/erp/storage/logs/laravel.log

# Queue Worker
sudo journalctl -u erp-worker -f
```

### Check Resources
```bash
htop
df -h
free -m
```

## 🔄 Update Application

```bash
cd /var/www/html/erp

# Pull latest code
git pull

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart erp-worker
sudo systemctl restart php8.2-fpm
```

## 🛠️ Troubleshooting

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/html/erp
sudo chmod -R 755 /var/www/html/erp
sudo chmod -R 775 /var/www/html/erp/storage
sudo chmod -R 775 /var/www/html/erp/bootstrap/cache
```

### 502 Bad Gateway
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Database Connection Failed
Check `.env` credentials:
```bash
cat /var/www/html/erp/.env | grep DB_
```

Test connection:
```bash
mysql -u erp_user -p erp_production
```

### Queue Not Processing
```bash
sudo systemctl restart erp-worker
sudo systemctl status erp-worker
```

## 🔐 Security Checklist

- [ ] Firewall enabled (UFW)
- [ ] SSL certificate installed
- [ ] Strong database passwords
- [ ] `.env` file permissions: 600
- [ ] Debug mode disabled (`APP_DEBUG=false`)
- [ ] File permissions correct
- [ ] Regular backups enabled
- [ ] Redis password set (if exposed)

## 📞 Support

For deployment info after successful deployment:
```bash
cat /root/erp-deployment-info.txt
```

Database passwords:
```bash
cat /root/.mysql_root_password
```

## 🎉 Success!

Your application should now be running at:
- HTTP: `http://your-domain.com` or `http://your-server-ip`
- HTTPS: `https://your-domain.com` (if SSL configured)

Default port without domain: `http://your-server-ip`
