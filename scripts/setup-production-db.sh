#!/bin/bash

# Production Database Setup Script
# This script helps configure MySQL for production use

set -e

echo "🗄️  ERP Production Database Setup"
echo "=================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Please run as root (use sudo)"
    exit 1
fi

# Configuration
DB_NAME=${DB_NAME:-"erp_production"}
DB_USER=${DB_USER:-"erp_user"}
DB_PASSWORD=${DB_PASSWORD:-$(openssl rand -base64 32)}
ROOT_PASSWORD=""

echo "📋 Database Configuration:"
echo "  Database: $DB_NAME"
echo "  User: $DB_USER"
echo "  Password: $DB_PASSWORD (save this!)"
echo ""

read -p "Enter MySQL root password: " -s ROOT_PASSWORD
echo ""

# Test MySQL connection
if ! mysql -u root -p"$ROOT_PASSWORD" -e "SELECT 1;" &> /dev/null; then
    echo "❌ Failed to connect to MySQL. Check your root password."
    exit 1
fi

echo "✅ MySQL connection successful"
echo ""

echo "🔧 Creating database and user..."

# Create database and user
mysql -u root -p"$ROOT_PASSWORD" << EOF
-- Create database
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';

-- Grant privileges
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Show result
SELECT User, Host FROM mysql.user WHERE User = '$DB_USER';
EOF

echo "✅ Database and user created"
echo ""

echo "⚙️  Optimizing MySQL configuration..."

# Backup original my.cnf
if [ -f /etc/mysql/my.cnf ]; then
    cp /etc/mysql/my.cnf /etc/mysql/my.cnf.backup.$(date +%Y%m%d_%H%M%S)
fi

# Create optimized configuration
cat > /etc/mysql/conf.d/production-optimization.cnf << 'EOF'
[mysqld]
# Performance Settings
max_connections = 200
max_allowed_packet = 64M
thread_cache_size = 50
table_open_cache = 4000
query_cache_type = 1
query_cache_size = 64M
query_cache_limit = 2M

# InnoDB Settings
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_log_buffer_size = 16M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1

# Binary Logging (for backups and replication)
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

# Connection Settings
wait_timeout = 600
interactive_timeout = 600

[client]
default-character-set = utf8mb4
EOF

echo "✅ MySQL configuration optimized"
echo ""

echo "🔄 Restarting MySQL..."
systemctl restart mysql
systemctl enable mysql

echo "✅ MySQL restarted"
echo ""

echo "🧪 Testing database connection..."

# Test connection with new user
if mysql -u "$DB_USER" -p"$DB_PASSWORD" -D "$DB_NAME" -e "SELECT 1;" &> /dev/null; then
    echo "✅ Database connection test successful"
else
    echo "❌ Database connection test failed"
    exit 1
fi

echo ""
echo "📝 Updating .env file..."

# Update .env file
ENV_FILE="/workspaces/ERP/.env"

if [ -f "$ENV_FILE" ]; then
    # Backup .env
    cp "$ENV_FILE" "$ENV_FILE.backup.$(date +%Y%m%d_%H%M%S)"
    
    # Update database settings
    sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" "$ENV_FILE"
    sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USER/" "$ENV_FILE"
    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" "$ENV_FILE"
    
    echo "✅ .env file updated"
else
    echo "⚠️  .env file not found. Please update manually."
fi

echo ""
echo "✅ Production Database Setup Complete!"
echo ""
echo "📋 Database Details:"
echo "  Database: $DB_NAME"
echo "  Username: $DB_USER"
echo "  Password: $DB_PASSWORD"
echo ""
echo "⚠️  IMPORTANT: Save these credentials securely!"
echo ""
echo "🔍 Next Steps:"
echo "  1. Run migrations: php artisan migrate"
echo "  2. Seed database (if needed): php artisan db:seed"
echo "  3. Test application: php artisan serve"
echo "  4. Setup automated backups: See BACKUP_SYSTEM.md"
echo ""
echo "📊 Monitor database performance:"
echo "  mysql -u $DB_USER -p -e 'SHOW PROCESSLIST;'"
echo "  mysql -u $DB_USER -p -e 'SHOW ENGINE INNODB STATUS\\G'"
echo ""
