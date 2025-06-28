#!/bin/bash
set -e

# BlackCnote WSL2 Full Startup Script
echo "=== BlackCnote WSL2 Full Startup ==="

# Ensure we're in the project root
dirname=$(dirname "$0")
cd "$dirname"

# Set up directories
mkdir -p logs backups ssl blackcnote/wp-content/uploads blackcnote/wp-content/plugins blackcnote/wp-content/themes blackcnote/wp-content/mu-plugins

# Set permissions
chmod -R 755 blackcnote

# Build React app (dev server and build assets)
echo "[1/6] Installing React dependencies..."
cd react-app
npm install

echo "[2/6] Building React app..."
npm run build

# Copy React build to WordPress theme
echo "[3/6] Copying React build to WordPress theme..."
cp -r dist/* ../blackcnote/wp-content/themes/blackcnote/dist/ 2>/dev/null || true
cd ..

# Start all Docker Compose services (WSL2-optimized)
echo "[4/6] Stopping any existing containers..."
docker-compose -f config/docker/docker-compose-wsl2.yml down || true

echo "[5/6] Starting all services..."
docker-compose -f config/docker/docker-compose-wsl2.yml up -d --build

# Wait for services to be ready
echo "[6/6] Waiting for services to be ready..."
sleep 20

echo "=== BlackCnote Services Started ==="
echo "WordPress:      http://localhost:8888"
echo "WordPress Admin: http://localhost:8888/wp-admin"
echo "React App:      http://localhost:5174"
echo "phpMyAdmin:     http://localhost:8080"
echo "MailHog:        http://localhost:8025"
echo "Redis Commander:http://localhost:8081"
echo "Prometheus:     http://localhost:9090"
echo "Grafana:        http://localhost:3000 (admin/admin)"
echo "Browsersync:    http://localhost:3000"
echo "Metrics Exporter: http://localhost:9091"

echo "All services are up!"

# Detect if running in WSL2
if grep -qEi "(Microsoft|WSL)" /proc/version &> /dev/null; then
  echo "[BlackCnote] Detected WSL2 environment."
  # Sync React app files from Windows if needed
  WIN_REACT_APP="/mnt/c/Users/CASH AMERICA PAWN/Desktop/BlackCnote/react-app"
  LINUX_REACT_APP="$HOME/blackcnote/react-app"
  if [ -d "$WIN_REACT_APP" ]; then
    echo "[BlackCnote] Syncing React app files from Windows to WSL2..."
    mkdir -p "$LINUX_REACT_APP"
    cp -ru "$WIN_REACT_APP/"* "$LINUX_REACT_APP/"
  fi
  # Start WSL2 Docker Compose
  cd "$HOME/blackcnote"
  echo "[BlackCnote] Starting Docker Compose (WSL2)..."
  docker-compose -f config/docker/docker-compose-wsl2.yml up -d
else
  echo "[BlackCnote] Not running in WSL2. Please use the Windows PowerShell script."
  exit 1
fi 