#!/bin/bash
set -e

echo "=== BlackCnote Volume Mapping Test ==="
echo "Testing different volume mapping approaches..."

# Test 1: Check if we're in WSL2
echo "1. Checking WSL2 environment..."
if grep -q Microsoft /proc/version; then
    echo "✅ Running in WSL2"
else
    echo "❌ Not running in WSL2"
fi

# Test 2: Check Docker Desktop WSL2 integration
echo "2. Checking Docker Desktop WSL2 integration..."
if docker info 2>/dev/null | grep -q "WSL"; then
    echo "✅ Docker Desktop is using WSL2 backend"
else
    echo "❌ Docker Desktop may not be using WSL2 backend"
fi

# Test 3: Test different path formats
echo "3. Testing path formats..."

# Get current directory
CURRENT_DIR=$(pwd)
echo "Current directory: $CURRENT_DIR"

# Test WSL2 absolute path
echo "Testing WSL2 absolute path: /root/blackcnote"
if [ -f "/root/blackcnote/wp-blog-header.php" ]; then
    echo "✅ WSL2 absolute path works"
    WSL2_ABS_PATH="/root/blackcnote"
else
    echo "❌ WSL2 absolute path not accessible"
    WSL2_ABS_PATH=""
fi

# Test Windows WSL2 path
echo "Testing Windows WSL2 path: \\\\wsl.localhost\\Ubuntu\\root\\blackcnote"
WINDOWS_WSL2_PATH="\\\\wsl.localhost\\Ubuntu\\root\\blackcnote"
echo "✅ Windows WSL2 path format ready"

# Test relative path
echo "Testing relative path: ../../"
if [ -f "../../wp-blog-header.php" ]; then
    echo "✅ Relative path works"
    RELATIVE_PATH="../../"
else
    echo "❌ Relative path not accessible"
    RELATIVE_PATH=""
fi

# Test 4: Create optimized docker-compose file
echo "4. Creating optimized docker-compose file..."

# Create a test container to verify volume mapping
echo "Creating test container to verify volume mapping..."

# Use the working path format
if [ -n "$WSL2_ABS_PATH" ]; then
    WORKING_PATH="$WSL2_ABS_PATH"
    echo "Using WSL2 absolute path: $WORKING_PATH"
elif [ -n "$RELATIVE_PATH" ]; then
    WORKING_PATH="$RELATIVE_PATH"
    echo "Using relative path: $WORKING_PATH"
else
    WORKING_PATH="$WINDOWS_WSL2_PATH"
    echo "Using Windows WSL2 path: $WORKING_PATH"
fi

# Create optimized docker-compose file
cat > docker-compose-optimized.yml << EOF
# BlackCnote Optimized Docker Compose
# Generated by volume mapping test script

services:
  wordpress:
    image: wordpress:6.8-apache
    container_name: blackcnote-wordpress-test
    environment:
      WORDPRESS_DB_HOST: mysql
      WORDPRESS_DB_NAME: blackcnote
      WORDPRESS_DB_USER: root
      WORDPRESS_DB_PASSWORD: blackcnote_password
    volumes:
      - "$WORKING_PATH:/var/www/html:delegated"
    networks:
      - test-network
    restart: unless-stopped

  mysql:
    image: mysql:8.0
    container_name: blackcnote-mysql-test
    environment:
      MYSQL_ROOT_PASSWORD: blackcnote_password
      MYSQL_DATABASE: blackcnote
    networks:
      - test-network
    restart: unless-stopped

networks:
  test-network:
    driver: bridge
EOF

echo "✅ Created optimized docker-compose file: docker-compose-optimized.yml"

# Test 5: Start test containers
echo "5. Starting test containers..."
docker-compose -f docker-compose-optimized.yml up -d

# Wait for containers to be ready
echo "Waiting for containers to be ready..."
sleep 10

# Test 6: Verify volume mapping
echo "6. Verifying volume mapping..."
if docker exec blackcnote-wordpress-test ls -la /var/www/html/wp-blog-header.php >/dev/null 2>&1; then
    echo "✅ Volume mapping successful! wp-blog-header.php is accessible in container"
    VOLUME_MAPPING_WORKS=true
else
    echo "❌ Volume mapping failed! wp-blog-header.php not accessible in container"
    VOLUME_MAPPING_WORKS=false
fi

# Test 7: Test WordPress accessibility
echo "7. Testing WordPress accessibility..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8888 2>/dev/null || echo "000")
if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ WordPress is accessible (HTTP 200)"
    WORDPRESS_ACCESSIBLE=true
else
    echo "❌ WordPress returned HTTP $HTTP_CODE"
    WORDPRESS_ACCESSIBLE=false
fi

# Cleanup test containers
echo "8. Cleaning up test containers..."
docker-compose -f docker-compose-optimized.yml down

# Summary
echo "=== Volume Mapping Test Summary ==="
echo "Working path format: $WORKING_PATH"
echo "Volume mapping works: $VOLUME_MAPPING_WORKS"
echo "WordPress accessible: $WORDPRESS_ACCESSIBLE"

if [ "$VOLUME_MAPPING_WORKS" = true ]; then
    echo "✅ Volume mapping is working correctly!"
    echo "You can now use the optimized docker-compose file or update your main compose file."
else
    echo "❌ Volume mapping issues detected."
    echo "Troubleshooting steps:"
    echo "1. Restart Docker Desktop"
    echo "2. Ensure WSL2 integration is enabled in Docker Desktop settings"
    echo "3. Try running Docker Compose from Windows PowerShell instead of WSL2"
fi

echo "=== Test Complete ===" 