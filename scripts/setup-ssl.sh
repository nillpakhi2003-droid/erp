#!/bin/bash

# SSL/TLS Setup Script for ERP System
# This script helps set up Let's Encrypt SSL certificates

set -e

echo "🔐 ERP System SSL/TLS Setup"
echo "=============================="

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Please run as root (use sudo)"
    exit 1
fi

# Get domain name
read -p "Enter your domain name (e.g., example.com): " DOMAIN

if [ -z "$DOMAIN" ]; then
    echo "❌ Domain name is required"
    exit 1
fi

echo ""
echo "📦 Installing Certbot..."

# Install Certbot
if ! command -v certbot &> /dev/null; then
    apt-get update
    apt-get install -y certbot python3-certbot-nginx
    echo "✅ Certbot installed"
else
    echo "✅ Certbot already installed"
fi

echo ""
echo "🔧 Configuring Nginx..."

# Backup existing config
if [ -f /etc/nginx/sites-available/erp ]; then
    cp /etc/nginx/sites-available/erp /etc/nginx/sites-available/erp.backup.$(date +%Y%m%d_%H%M%S)
    echo "✅ Existing config backed up"
fi

# Update nginx config with domain
sed "s/your-domain.com/$DOMAIN/g" /workspaces/ERP/nginx-ssl.conf > /etc/nginx/sites-available/erp

# Create symbolic link
ln -sf /etc/nginx/sites-available/erp /etc/nginx/sites-enabled/erp

# Remove default config if exists
rm -f /etc/nginx/sites-enabled/default

# Test nginx config
nginx -t

echo ""
echo "🎫 Obtaining SSL Certificate from Let's Encrypt..."

# Stop nginx temporarily
systemctl stop nginx

# Obtain certificate
certbot certonly --standalone -d $DOMAIN -d www.$DOMAIN \
    --non-interactive --agree-tos \
    --email admin@$DOMAIN \
    --rsa-key-size 4096

echo ""
echo "🔄 Setting up auto-renewal..."

# Create renewal hook
cat > /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh << 'EOF'
#!/bin/bash
systemctl reload nginx
EOF

chmod +x /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh

# Test renewal
certbot renew --dry-run

echo ""
echo "🚀 Starting Nginx..."
systemctl start nginx
systemctl enable nginx

echo ""
echo "✅ SSL/TLS Setup Complete!"
echo ""
echo "📋 Summary:"
echo "  Domain: $DOMAIN"
echo "  Certificate: /etc/letsencrypt/live/$DOMAIN/fullchain.pem"
echo "  Private Key: /etc/letsencrypt/live/$DOMAIN/privkey.pem"
echo "  Auto-renewal: Configured (renews every 60 days)"
echo ""
echo "🔍 Test your SSL configuration:"
echo "  https://www.ssllabs.com/ssltest/analyze.html?d=$DOMAIN"
echo ""
echo "🔐 Your site should now be accessible at: https://$DOMAIN"
