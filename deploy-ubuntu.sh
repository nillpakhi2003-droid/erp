#!/bin/bash

###############################################################################
# ERP System - Ubuntu VPS Complete Deployment Script
# This script will set up everything needed to run the ERP system on Ubuntu
###############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "╔════════════════════════════════════════════════════════════╗"
echo "║     ERP System - Ubuntu VPS Deployment Script             ║"
echo "║     Complete Setup for Production Environment             ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}❌ Please run as root (use sudo)${NC}"
    exit 1
fi

# Get deployment directory
DEPLOY_DIR="/var/www/html/erp"
read -p "Enter deployment directory [${DEPLOY_DIR}]: " input_dir
DEPLOY_DIR=${input_dir:-$DEPLOY_DIR}

# Get domain name (optional)
read -p "Enter your domain name (leave empty for IP-only access): " DOMAIN

# Database configuration
DB_NAME="erp_production"
DB_USER="erp_user"
DB_PASSWORD=$(openssl rand -base64 32)

echo ""
echo -e "${YELLOW}📋 Deployment Configuration:${NC}"
echo "  Directory: $DEPLOY_DIR"
echo "  Domain: ${DOMAIN:-'IP Address Only'}"
echo "  Database: $DB_NAME"
echo "  DB User: $DB_USER"
echo "  DB Password: $DB_PASSWORD (will be saved in .env)"
echo ""
read -p "Continue with this configuration? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    exit 1
fi

###############################################################################
# 1. System Update
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}📦 Step 1: Updating System${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

apt-get update
apt-get upgrade -y

echo -e "${GREEN}✅ System updated${NC}"

###############################################################################
# 2. Install Required Packages
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}📦 Step 2: Installing Required Packages${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Add PHP repository
add-apt-repository ppa:ondrej/php -y
apt-get update

# Install packages
apt-get install -y \
    nginx \
    mysql-server \
    redis-server \
    php8.2 \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-redis \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-curl \
    php8.2-zip \
    php8.2-gd \
    php8.2-intl \
    composer \
    git \
    unzip \
    curl \
    certbot \
    python3-certbot-nginx \
    apache2-utils \
    htop \
    ufw

echo -e "${GREEN}✅ Packages installed${NC}"

###############################################################################
# 3. Configure Firewall
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}🔥 Step 3: Configuring Firewall${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

ufw --force enable
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw allow 8888/tcp

echo -e "${GREEN}✅ Firewall configured${NC}"

###############################################################################
# 4. Setup MySQL Database
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}🗄️  Step 4: Setting Up MySQL Database${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Secure MySQL installation
MYSQL_ROOT_PASSWORD=$(openssl rand -base64 32)

mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${MYSQL_ROOT_PASSWORD}';"
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "DELETE FROM mysql.user WHERE User='';"
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "DROP DATABASE IF EXISTS test;"
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "FLUSH PRIVILEGES;"

# Create application database
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" << EOF
CREATE DATABASE ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
EOF

# Optimize MySQL
cat > /etc/mysql/conf.d/production.cnf << 'MYSQLEOF'
[mysqld]
# Performance Settings
max_connections = 200
max_allowed_packet = 64M
thread_cache_size = 50
table_open_cache = 4000

# InnoDB Settings
innodb_buffer_pool_size = 512M
innodb_log_file_size = 128M
innodb_log_buffer_size = 16M
innodb_flush_log_at_trx_commit = 2
innodb_file_per_table = 1

# Query Cache
query_cache_type = 1
query_cache_size = 32M
query_cache_limit = 2M

# Binary Logging
log_bin = /var/log/mysql/mysql-bin.log
expire_logs_days = 7
max_binlog_size = 100M

# Slow Query Log
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2

# Character Set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

[client]
default-character-set = utf8mb4
MYSQLEOF

systemctl restart mysql
systemctl enable mysql

# Save MySQL root password
echo "$MYSQL_ROOT_PASSWORD" > /root/.mysql_root_password
chmod 600 /root/.mysql_root_password

echo -e "${GREEN}✅ MySQL configured${NC}"
echo -e "${YELLOW}   MySQL root password saved to: /root/.mysql_root_password${NC}"

###############################################################################
# 5. Configure Redis
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}⚡ Step 5: Configuring Redis${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Configure Redis
sed -i 's/# maxmemory <bytes>/maxmemory 256mb/' /etc/redis/redis.conf
sed -i 's/# maxmemory-policy noeviction/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf

systemctl restart redis-server
systemctl enable redis-server

echo -e "${GREEN}✅ Redis configured${NC}"

###############################################################################
# 6. Deploy Application
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}📁 Step 6: Deploying Application${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Create directory
mkdir -p "$DEPLOY_DIR"

# If running from the ERP directory, copy files
if [ -f "/workspaces/ERP/composer.json" ]; then
    cp -r /workspaces/ERP/* "$DEPLOY_DIR/"
    echo -e "${GREEN}✅ Files copied from /workspaces/ERP${NC}"
else
    echo -e "${YELLOW}⚠️  Source files not found. Please copy your files to ${DEPLOY_DIR}${NC}"
fi

cd "$DEPLOY_DIR"

# Install dependencies
if [ -f "composer.json" ]; then
    composer install --optimize-autoloader --no-dev
    echo -e "${GREEN}✅ Composer dependencies installed${NC}"
fi

# Create .env file
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# Generate application key
php artisan key:generate --force

# Configure .env
cat > .env << ENVEOF
APP_NAME="ERP System"
APP_ENV=production
APP_KEY=$(grep APP_KEY .env | cut -d '=' -f2)
APP_DEBUG=false
APP_URL=${DOMAIN:+https://$DOMAIN}${DOMAIN:-http://$(curl -s ifconfig.me)}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@${DOMAIN:-localhost}"
MAIL_FROM_NAME="ERP System"
ENVEOF

# Set permissions
chown -R www-data:www-data "$DEPLOY_DIR"
chmod -R 755 "$DEPLOY_DIR"
chmod -R 775 "$DEPLOY_DIR/storage"
chmod -R 775 "$DEPLOY_DIR/bootstrap/cache"

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "${GREEN}✅ Application deployed${NC}"

###############################################################################
# 7. Configure Nginx
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}🌐 Step 7: Configuring Nginx${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Create Nginx configuration
cat > /etc/nginx/sites-available/erp << NGINXEOF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN:-_} ${DOMAIN:+www.$DOMAIN};

    root ${DEPLOY_DIR}/public;
    index index.php index.html;

    # Logs
    access_log /var/log/nginx/erp-access.log;
    error_log /var/log/nginx/erp-error.log;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    client_max_body_size 20M;
}
NGINXEOF

# Enable site
ln -sf /etc/nginx/sites-available/erp /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test and reload Nginx
nginx -t
systemctl restart nginx
systemctl enable nginx

echo -e "${GREEN}✅ Nginx configured${NC}"

###############################################################################
# 8. Setup SSL (if domain provided)
###############################################################################
if [ ! -z "$DOMAIN" ]; then
    echo ""
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}🔐 Step 8: Setting Up SSL Certificate${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

    read -p "Enter email for SSL certificate: " SSL_EMAIL
    
    certbot --nginx -d "$DOMAIN" -d "www.$DOMAIN" \
        --non-interactive --agree-tos \
        --email "${SSL_EMAIL}" \
        --redirect

    echo -e "${GREEN}✅ SSL certificate installed${NC}"
else
    echo -e "${YELLOW}⚠️  Skipping SSL setup (no domain provided)${NC}"
fi

###############################################################################
# 9. Setup Cron for Scheduled Tasks
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}⏰ Step 9: Setting Up Cron Jobs${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Add Laravel scheduler to cron
(crontab -l 2>/dev/null; echo "* * * * * cd ${DEPLOY_DIR} && php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo -e "${GREEN}✅ Cron jobs configured${NC}"

###############################################################################
# 10. Create Systemd Service for Queue Worker
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}🔄 Step 10: Setting Up Queue Worker${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

cat > /etc/systemd/system/erp-worker.service << SERVICEEOF
[Unit]
Description=ERP Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=5s
ExecStart=/usr/bin/php ${DEPLOY_DIR}/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
SERVICEEOF

systemctl daemon-reload
systemctl enable erp-worker
systemctl start erp-worker

echo -e "${GREEN}✅ Queue worker configured${NC}"

###############################################################################
# 11. Create Deployment Info File
###############################################################################
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}📄 Step 11: Creating Deployment Info${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

cat > /root/erp-deployment-info.txt << INFOEOF
╔════════════════════════════════════════════════════════════╗
║          ERP System - Deployment Information               ║
╚════════════════════════════════════════════════════════════╝

Deployment Date: $(date)

APPLICATION
-----------
Directory: ${DEPLOY_DIR}
URL: ${DOMAIN:+https://$DOMAIN}${DOMAIN:-http://$(curl -s ifconfig.me)}
Environment: Production

DATABASE
--------
Database: ${DB_NAME}
Username: ${DB_USER}
Password: ${DB_PASSWORD}
MySQL Root Password: ${MYSQL_ROOT_PASSWORD}
  (also saved in /root/.mysql_root_password)

SERVICES
--------
✓ Nginx: http://localhost
✓ MySQL: Port 3306
✓ Redis: Port 6379
✓ PHP-FPM: Version 8.2
✓ Queue Worker: erp-worker.service

IMPORTANT COMMANDS
------------------
# Restart Services
sudo systemctl restart nginx
sudo systemctl restart mysql
sudo systemctl restart redis-server
sudo systemctl restart php8.2-fpm
sudo systemctl restart erp-worker

# View Logs
sudo tail -f /var/log/nginx/erp-error.log
sudo tail -f ${DEPLOY_DIR}/storage/logs/laravel.log
sudo journalctl -u erp-worker -f

# Laravel Commands
cd ${DEPLOY_DIR}
php artisan migrate
php artisan cache:clear
php artisan config:cache
php artisan queue:restart

# Database Backup (Manual)
php artisan db:backup --telegram

# Load Testing
cd ${DEPLOY_DIR}
./scripts/load-test.sh

MONITORING
----------
# Check Queue Worker
sudo systemctl status erp-worker

# Check Nginx Status
sudo systemctl status nginx

# Check Database
mysql -u ${DB_USER} -p'${DB_PASSWORD}' -e "SHOW DATABASES;"

# Check Redis
redis-cli ping

# System Resources
htop

SECURITY
--------
Firewall: UFW enabled
Ports Open: 22, 80, 443, 8888
SSL: ${DOMAIN:+Enabled}${DOMAIN:-Not configured}

NEXT STEPS
----------
1. Configure email settings in .env
2. Setup Telegram backup (see BACKUP_SYSTEM.md)
3. Configure Sentry for error monitoring
4. Test application: ${DOMAIN:+https://$DOMAIN}${DOMAIN:-http://$(curl -s ifconfig.me)}
5. Create admin user: php artisan tinker

SUPPORT FILES
-------------
- Full documentation: ${DEPLOY_DIR}/PRODUCTION_FEATURES.md
- Backup system guide: ${DEPLOY_DIR}/BACKUP_SYSTEM.md
- Environment config: ${DEPLOY_DIR}/.env

═══════════════════════════════════════════════════════════════
⚠️  SECURITY WARNING: Keep this file secure!
This file contains sensitive passwords and should be protected.
═══════════════════════════════════════════════════════════════

INFOEOF

chmod 600 /root/erp-deployment-info.txt

echo -e "${GREEN}✅ Deployment info saved to: /root/erp-deployment-info.txt${NC}"

###############################################################################
# Final Summary
###############################################################################
echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                 🎉 DEPLOYMENT COMPLETE! 🎉                ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${YELLOW}📋 Quick Summary:${NC}"
echo -e "   Application URL: ${DOMAIN:+https://$DOMAIN}${DOMAIN:-http://$(curl -s ifconfig.me)}"
echo -e "   Deployment Path: ${DEPLOY_DIR}"
echo -e "   Database: ${DB_NAME}"
echo ""
echo -e "${YELLOW}🔑 Important Files:${NC}"
echo -e "   Deployment Info: ${GREEN}/root/erp-deployment-info.txt${NC}"
echo -e "   MySQL Root Pass: ${GREEN}/root/.mysql_root_password${NC}"
echo -e "   Application .env: ${GREEN}${DEPLOY_DIR}/.env${NC}"
echo ""
echo -e "${YELLOW}✅ Services Running:${NC}"
systemctl is-active --quiet nginx && echo -e "   ✓ Nginx" || echo -e "   ✗ Nginx"
systemctl is-active --quiet mysql && echo -e "   ✓ MySQL" || echo -e "   ✗ MySQL"
systemctl is-active --quiet redis-server && echo -e "   ✓ Redis" || echo -e "   ✗ Redis"
systemctl is-active --quiet php8.2-fpm && echo -e "   ✓ PHP-FPM" || echo -e "   ✗ PHP-FPM"
systemctl is-active --quiet erp-worker && echo -e "   ✓ Queue Worker" || echo -e "   ✗ Queue Worker"
echo ""
echo -e "${YELLOW}📚 Next Steps:${NC}"
echo -e "   1. Visit your application: ${DOMAIN:+https://$DOMAIN}${DOMAIN:-http://$(curl -s ifconfig.me)}"
echo -e "   2. Review deployment info: ${GREEN}cat /root/erp-deployment-info.txt${NC}"
echo -e "   3. Configure email & Telegram in: ${GREEN}${DEPLOY_DIR}/.env${NC}"
echo -e "   4. Create admin user: ${GREEN}cd ${DEPLOY_DIR} && php artisan tinker${NC}"
echo ""
echo -e "${BLUE}════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}Thank you for using ERP System! 🚀${NC}"
echo -e "${BLUE}════════════════════════════════════════════════════════════${NC}"
echo ""
