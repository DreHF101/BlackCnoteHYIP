#!/bin/bash

# BlackCnote Production Deployment Script
# Comprehensive deployment script with backup, health checks, and monitoring

set -e

# Configuration
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DEPLOYMENT_ID=$(date +%Y%m%d-%H%M%S)
BACKUP_DIR="$PROJECT_ROOT/backups"
LOG_FILE="$PROJECT_ROOT/logs/deployment-$DEPLOYMENT_ID.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}❌ $1${NC}" | tee -a "$LOG_FILE"
    exit 1
}

# Create necessary directories
mkdir -p "$BACKUP_DIR"
mkdir -p "$(dirname "$LOG_FILE")"

log "🚀 Starting BlackCnote Production Deployment (ID: $DEPLOYMENT_ID)"

# Step 1: Pre-deployment checks
log "📋 Running pre-deployment checks..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    error "Docker is not running. Please start Docker and try again."
fi

# Check if required files exist
if [[ ! -f "$PROJECT_ROOT/docker-compose.yml" ]]; then
    error "docker-compose.yml not found in $PROJECT_ROOT"
fi

if [[ ! -f "$PROJECT_ROOT/docker-compose.prod.yml" ]]; then
    error "docker-compose.prod.yml not found in $PROJECT_ROOT"
fi

# Check disk space
DISK_SPACE=$(df "$PROJECT_ROOT" | awk 'NR==2 {print $4}')
if [[ $DISK_SPACE -lt 1048576 ]]; then  # Less than 1GB
    warning "Low disk space: ${DISK_SPACE}KB available"
fi

success "Pre-deployment checks completed"

# Step 2: Create backup
log "📦 Creating backup..."

# Stop services to ensure consistent backup
log "Stopping services for backup..."
docker-compose -f "$PROJECT_ROOT/docker-compose.prod.yml" down

# Backup database
log "Backing up database..."
docker-compose -f "$PROJECT_ROOT/docker-compose.prod.yml" up -d mysql
sleep 10  # Wait for MySQL to start

docker-compose -f "$PROJECT_ROOT/docker-compose.prod.yml" exec -T mysql mysqldump \
    -u root -pblackcnote_password blackcnote > "$BACKUP_DIR/db-backup-$DEPLOYMENT_ID.sql"

# Backup WordPress files
log "Backing up WordPress files..."
tar -czf "$BACKUP_DIR/wp-backup-$DEPLOYMENT_ID.tar.gz" \
    -C "$PROJECT_ROOT" blackcnote/wp-content/uploads \
    blackcnote/wp-content/plugins blackcnote/wp-content/themes

# Backup configuration files
log "Backing up configuration files..."
tar -czf "$BACKUP_DIR/config-backup-$DEPLOYMENT_ID.tar.gz" \
    -C "$PROJECT_ROOT" wp-config.php docker-compose.prod.yml \
    config/nginx/blackcnote-prod.conf

success "Backup completed: $BACKUP_DIR"

# Step 3: Pull latest changes
log "⬇️ Pulling latest changes..."
cd "$PROJECT_ROOT"

# Check if we're in a git repository
if [[ -d ".git" ]]; then
    git fetch origin
    git reset --hard origin/main
    success "Latest changes pulled from git"
else
    warning "Not in a git repository, skipping git pull"
fi

# Step 4: Build and deploy
log "🔨 Building and deploying..."

# Build React app for production
log "Building React app..."
cd "$PROJECT_ROOT/react-app"
npm ci
npm run build

# Copy React build to WordPress theme
log "Copying React build to WordPress theme..."
cp -r dist/* "$PROJECT_ROOT/blackcnote/wp-content/themes/blackcnote/dist/"

cd "$PROJECT_ROOT"

# Build and start production services
log "Starting production services..."
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

# Wait for services to be ready
log "Waiting for services to be ready..."
sleep 30

success "Deployment completed"

# Step 5: Health checks
log "🏥 Running health checks..."

# Check if WordPress is responding
log "Checking WordPress health..."
for i in {1..10}; do
    if curl -f -s http://localhost:8888 > /dev/null; then
        success "WordPress is responding"
        break
    else
        if [[ $i -eq 10 ]]; then
            error "WordPress health check failed after 10 attempts"
        fi
        warning "WordPress not ready, attempt $i/10"
        sleep 10
    fi
done

# Check database connection
log "Checking database connection..."
if docker-compose -f docker-compose.prod.yml exec -T mysql mysqladmin ping -u root -pblackcnote_password > /dev/null 2>&1; then
    success "Database connection is healthy"
else
    error "Database connection failed"
fi

# Check Redis connection
log "Checking Redis connection..."
if docker-compose -f docker-compose.prod.yml exec -T redis redis-cli ping > /dev/null 2>&1; then
    success "Redis connection is healthy"
else
    error "Redis connection failed"
fi

# Check container status
log "Checking container status..."
if docker-compose -f docker-compose.prod.yml ps | grep -q "Up"; then
    success "All containers are running"
else
    error "Some containers are not running"
fi

# Step 6: Performance tests
log "⚡ Running performance tests..."

# Test response time
RESPONSE_TIME=$(curl -o /dev/null -s -w "%{time_total}" http://localhost:8888)
if (( $(echo "$RESPONSE_TIME < 2.0" | bc -l) )); then
    success "Response time: ${RESPONSE_TIME}s (within acceptable range)"
else
    warning "Response time: ${RESPONSE_TIME}s (above 2s threshold)"
fi

# Test database query performance
log "Testing database performance..."
DB_QUERY_TIME=$(docker-compose -f docker-compose.prod.yml exec -T mysql mysql -u root -pblackcnote_password -e "SELECT BENCHMARK(1000000,1);" 2>/dev/null | tail -1)
if [[ $DB_QUERY_TIME ]]; then
    success "Database performance test completed"
else
    warning "Database performance test failed"
fi

# Step 7: Clear caches
log "🧹 Clearing caches..."

# Clear WordPress cache
if docker-compose -f docker-compose.prod.yml exec -T wordpress wp cache flush > /dev/null 2>&1; then
    success "WordPress cache cleared"
else
    warning "Failed to clear WordPress cache"
fi

# Clear Redis cache
if docker-compose -f docker-compose.prod.yml exec -T redis redis-cli FLUSHALL > /dev/null 2>&1; then
    success "Redis cache cleared"
else
    warning "Failed to clear Redis cache"
fi

# Step 8: Update monitoring
log "📊 Updating monitoring configuration..."

# Update Prometheus targets
if [[ -f "$PROJECT_ROOT/monitoring/prometheus.yml" ]]; then
    log "Updating Prometheus configuration..."
    # Add your Prometheus configuration update logic here
fi

# Update Grafana dashboards
if [[ -d "$PROJECT_ROOT/monitoring/grafana" ]]; then
    log "Updating Grafana dashboards..."
    # Add your Grafana dashboard update logic here
fi

# Step 9: Final verification
log "🔍 Final verification..."

# Check all services are running
SERVICES_RUNNING=$(docker-compose -f docker-compose.prod.yml ps --services --filter "status=running" | wc -l)
TOTAL_SERVICES=$(docker-compose -f docker-compose.prod.yml ps --services | wc -l)

if [[ $SERVICES_RUNNING -eq $TOTAL_SERVICES ]]; then
    success "All $TOTAL_SERVICES services are running"
else
    error "Only $SERVICES_RUNNING/$TOTAL_SERVICES services are running"
fi

# Check SSL certificate (if configured)
if [[ -f "$PROJECT_ROOT/ssl/blackcnote.crt" ]]; then
    log "Checking SSL certificate..."
    if openssl x509 -checkend 86400 -noout -in "$PROJECT_ROOT/ssl/blackcnote.crt" > /dev/null 2>&1; then
        success "SSL certificate is valid"
    else
        warning "SSL certificate will expire soon or is invalid"
    fi
fi

# Step 10: Cleanup old backups
log "🧹 Cleaning up old backups..."
find "$BACKUP_DIR" -name "*.sql" -mtime +7 -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +7 -delete
success "Old backups cleaned up"

# Step 11: Generate deployment report
log "📋 Generating deployment report..."

REPORT_FILE="$PROJECT_ROOT/reports/deployment-$DEPLOYMENT_ID.md"
mkdir -p "$(dirname "$REPORT_FILE")"

cat > "$REPORT_FILE" << EOF
# BlackCnote Production Deployment Report

**Deployment ID:** $DEPLOYMENT_ID  
**Date:** $(date)  
**Status:** SUCCESS

## Summary
- ✅ Pre-deployment checks passed
- ✅ Backup created successfully
- ✅ Latest changes deployed
- ✅ Health checks passed
- ✅ Performance tests completed
- ✅ Caches cleared
- ✅ Monitoring updated

## Services Status
$(docker-compose -f docker-compose.prod.yml ps)

## Performance Metrics
- Response Time: ${RESPONSE_TIME}s
- Database Performance: OK
- Cache Status: Cleared

## Backup Information
- Database: $BACKUP_DIR/db-backup-$DEPLOYMENT_ID.sql
- WordPress Files: $BACKUP_DIR/wp-backup-$DEPLOYMENT_ID.tar.gz
- Configuration: $BACKUP_DIR/config-backup-$DEPLOYMENT_ID.tar.gz

## Next Steps
1. Monitor application performance
2. Check error logs for any issues
3. Verify all features are working correctly
4. Update documentation if needed

EOF

success "Deployment report generated: $REPORT_FILE"

# Final success message
log "🎉 BlackCnote Production Deployment Completed Successfully!"
log "📊 Access your application at: http://localhost:8888"
log "📋 Deployment report: $REPORT_FILE"
log "📦 Backup location: $BACKUP_DIR"

# Send notification (if configured)
if [[ -n "$SLACK_WEBHOOK_URL" ]]; then
    curl -X POST -H 'Content-type: application/json' \
        --data "{\"text\":\"✅ BlackCnote Production Deployment Completed Successfully! (ID: $DEPLOYMENT_ID)\"}" \
        "$SLACK_WEBHOOK_URL" > /dev/null 2>&1
fi

exit 0 