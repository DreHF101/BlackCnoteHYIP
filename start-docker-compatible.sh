#!/bin/bash
# BlackCnote Docker-Compatible Startup Script

echo "🚀 Starting BlackCnote with Docker-compatible configuration..."

# Set Docker-compatible environment variables
export WORDPRESS_URL="http://wordpress"
export REACT_URL="http://react-app:5176"
export MYSQL_URL="mysql://mysql:3306"
export REDIS_URL="redis://redis:6379"

# Run Docker health check
echo "Checking Docker services..."
php docker-health-check.php

# Run Docker-compatible server test
echo "Running server tests..."
php test-docker-compatible.php

echo "✅ BlackCnote Docker startup completed"
