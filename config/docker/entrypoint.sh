#!/bin/bash
set -euo pipefail

# BlackCnote WordPress Container Entrypoint
# This script ensures proper startup of WordPress with Apache

echo "Starting BlackCnote WordPress container..."

# Set proper permissions for WordPress files
chown -R www-data:www-data /var/www/html
find /var/www/html -type d -exec chmod 755 {} \;
find /var/www/html -type f -exec chmod 644 {} \;

# Ensure wp-content directory exists and has proper permissions
if [ ! -d "/var/www/html/wp-content" ]; then
    mkdir -p /var/www/html/wp-content
    chown -R www-data:www-data /var/www/html/wp-content
fi

# Create necessary WordPress directories if they don't exist
for dir in uploads plugins themes mu-plugins; do
    if [ ! -d "/var/www/html/wp-content/$dir" ]; then
        mkdir -p "/var/www/html/wp-content/$dir"
        chown -R www-data:www-data "/var/www/html/wp-content/$dir"
    fi
done

# Enable Apache modules
a2enmod rewrite
a2enmod headers
a2enmod expires
a2enmod deflate

# Start WordPress with the official entrypoint logic
echo "Starting Apache via official WordPress entrypoint..."
exec docker-entrypoint.sh apache2-foreground 