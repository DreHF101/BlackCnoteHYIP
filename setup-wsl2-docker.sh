#!/bin/bash

echo "=== BlackCnote WSL2 Docker Setup ==="

# Create project directory
echo "Creating project directory..."
mkdir -p ~/blackcnote

# Copy project files from Windows to WSL2
echo "Copying project files from Windows to WSL2..."
cp -r /mnt/c/Users/CASH\ AMERICA\ PAWN/Desktop/BlackCnote/* ~/blackcnote/

# Set proper permissions
echo "Setting proper permissions..."
chmod -R 755 ~/blackcnote

# Navigate to project directory
cd ~/blackcnote

# Check if Docker is running
echo "Checking Docker status..."
if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running. Please start Docker Desktop and ensure WSL2 integration is enabled."
    echo "Then re-run this script."
    exit 1
fi

# Stop any existing containers
echo "Stopping existing containers..."
docker-compose -f config/docker/docker-compose.yml down

# Start containers with WSL2
echo "Starting Docker containers with WSL2..."
docker-compose -f config/docker/docker-compose.yml up -d

# Wait for containers to be ready
echo "Waiting for containers to be ready..."
sleep 10

# Check container status
echo "Checking container status..."
docker-compose -f config/docker/docker-compose.yml ps

# Test WordPress accessibility
echo "Testing WordPress accessibility..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8888 | grep -q "200"; then
    echo "✅ WordPress is accessible at http://localhost:8888"
else
    echo "❌ WordPress is not accessible. Checking container logs..."
    docker logs blackcnote-wordpress --tail 20
fi

echo "=== Setup Complete ==="
echo "WordPress: http://localhost:8888"
echo "WordPress Admin: http://localhost:8888/wp-admin/"
echo "phpMyAdmin: http://localhost:8080"
echo "MailHog: http://localhost:8025"
echo "Redis Commander: http://localhost:8081" 