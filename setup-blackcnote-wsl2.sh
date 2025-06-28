#!/bin/bash
set -e

echo "=== BlackCnote WSL2 Docker Setup ==="

# Ensure we're in the right directory
cd ~/blackcnote

# Set permissions
echo "Setting permissions..."
chmod -R 755 ~/blackcnote

# Start Docker Compose
echo "Starting Docker Compose..."
docker-compose -f config/docker/docker-compose.yml up -d

# Wait for containers
echo "Waiting for containers to be ready..."
sleep 10

# Show status
docker-compose -f config/docker/docker-compose.yml ps

# Test WordPress
echo "Testing WordPress accessibility..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8888)
if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ WordPress is accessible at http://localhost:8888"
else
    echo "❌ WordPress is not accessible (HTTP $HTTP_CODE)."
    echo "Showing last 20 lines of WordPress logs:"
    docker logs blackcnote-wordpress --tail 20
fi

echo "=== Setup Complete ==="
echo "WordPress: http://localhost:8888"
echo "phpMyAdmin: http://localhost:8080"
echo "MailHog: http://localhost:8025"
echo "Redis Commander: http://localhost:8081" 