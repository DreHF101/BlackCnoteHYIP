#!/bin/bash

# BlackCnote Production Deployment Script
# This script handles the complete production deployment process

set -e  # Exit on any error

# Configuration
ENVIRONMENT=${1:-"production"}
DOMAIN=${2:-"localhost"}
SKIP_SSL=${3:-"false"}
SKIP_BACKUP=${4:-"false"}

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
BACKUP_DIR="$PROJECT_ROOT/backups"
LOG_DIR="$PROJECT_ROOT/logs"
SSL_DIR="$PROJECT_ROOT/ssl"

echo -e "${GREEN}🚀 Starting BlackCnote Production Deployment...${NC}"
echo -e "${BLUE}Environment:${NC} $ENVIRONMENT"
echo -e "${BLUE}Domain:${NC} $DOMAIN"

# Function to log messages
log() {
    local level=$1
    shift
    local message="$*"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    case $level in
        "ERROR")
            echo -e "${RED}[$timestamp] [ERROR] $message${NC}"
            ;;
        "WARNING")
            echo -e "${YELLOW}[$timestamp] [WARNING] $message${NC}"
            ;;
        "INFO")
            echo -e "${GREEN}[$timestamp] [INFO] $message${NC}"
            ;;
        *)
            echo -e "${BLUE}[$timestamp] [INFO] $message${NC}"
            ;;
    esac
    
    # Write to log file
    echo "[$timestamp] [$level] $message" >> "$LOG_DIR/deployment-$(date +%Y-%m-%d).log"
}

# Function to check if Docker is running
check_docker() {
    if ! docker info >/dev/null 2>&1; then
        log "ERROR" "Docker is not running. Please start Docker and try again."
        exit 1
    fi
    log "INFO" "Docker is running"
}

# Function to create necessary directories
create_directories() {
    local dirs=("$BACKUP_DIR" "$LOG_DIR" "$SSL_DIR")
    
    for dir in "${dirs[@]}"; do
        if [[ ! -d "$dir" ]]; then
            mkdir -p "$dir"
            log "INFO" "Created directory: $dir"
        fi
    done
}

# Function to create backup
create_backup() {
    if [[ "$SKIP_BACKUP" == "true" ]]; then
        log "WARNING" "Skipping backup as requested"
        return
    fi
    
    log "INFO" "Creating backup before deployment..."
    local backup_file="$BACKUP_DIR/blackcnote-backup-$(date +%Y-%m-%d-%H%M%S).sql"
    
    # Check if MySQL container is running
    if docker ps --format '{{.Names}}' | grep -q "blackcnote_mysql"; then
        docker exec blackcnote_mysql_1 mysqldump -u root -pblackcnote_password blackcnote > "$backup_file" 2>/dev/null || {
            log "WARNING" "Could not create database backup"
        }
        log "INFO" "Database backup created: $backup_file"
    else
        log "WARNING" "MySQL container not running, skipping backup"
    fi
}

# Function to generate SSL certificates
generate_ssl_certificates() {
    if [[ "$SKIP_SSL" == "true" ]]; then
        log "WARNING" "Skipping SSL certificate generation as requested"
        return
    fi
    
    log "INFO" "Generating SSL certificates for $DOMAIN..."
    
    if [[ "$DOMAIN" == "localhost" ]]; then
        local cert_file="$SSL_DIR/blackcnote.crt"
        local key_file="$SSL_DIR/blackcnote.key"
        
        # Generate self-signed certificate
        openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
            -keyout "$key_file" -out "$cert_file" \
            -subj "/C=US/ST=State/L=City/O=BlackCnote/CN=$DOMAIN" 2>/dev/null || {
            log "WARNING" "Could not generate SSL certificates"
            return
        }
        
        log "INFO" "Self-signed SSL certificates generated"
    else
        log "WARNING" "For production domains, please obtain SSL certificates from a trusted CA"
        log "INFO" "Place certificates in: $SSL_DIR"
    fi
}

# Function to update configuration files
update_configurations() {
    log "INFO" "Updating configuration files for production..."
    
    # Update wp-config.php for production
    local wp_config="$PROJECT_ROOT/wp-config.php"
    if [[ -f "$wp_config" ]]; then
        sed -i 's/define( '\''WP_DEBUG'\'', true );/define( '\''WP_DEBUG'\'', false );/g' "$wp_config"
        sed -i 's/define( '\''WP_DEBUG_DISPLAY'\'', true );/define( '\''WP_DEBUG_DISPLAY'\'', false );/g' "$wp_config"
        sed -i 's/define( '\''SCRIPT_DEBUG'\'', true );/define( '\''SCRIPT_DEBUG'\'', false );/g' "$wp_config"
        log "INFO" "Updated wp-config.php for production"
    fi
    
    # Update Nginx configuration
    local nginx_config="$PROJECT_ROOT/config/nginx/blackcnote-prod.conf"
    if [[ -f "$nginx_config" ]]; then
        sed -i "s/server_name localhost;/server_name $DOMAIN;/g" "$nginx_config"
        log "INFO" "Updated Nginx configuration for domain: $DOMAIN"
    fi
}

# Function to deploy with Docker Compose
deploy_services() {
    log "INFO" "Starting production deployment with Docker Compose..."
    
    # Stop existing containers
    log "INFO" "Stopping existing containers..."
    docker-compose down --remove-orphans || true
    
    # Build and start production services
    log "INFO" "Building and starting production services..."
    docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
    
    # Wait for services to be ready
    log "INFO" "Waiting for services to be ready..."
    sleep 30
    
    # Check service health
    log "INFO" "Checking service health..."
    local services=("wordpress" "mysql" "redis" "nginx-proxy" "prometheus" "grafana")
    
    for service in "${services[@]}"; do
        local health=$(docker inspect --format='{{.State.Health.Status}}' "blackcnote_${service}_1" 2>/dev/null || echo "unknown")
        if [[ "$health" == "healthy" ]]; then
            log "INFO" "Service $service is healthy"
        else
            log "WARNING" "Service $service health status: $health"
        fi
    done
    
    log "INFO" "Production deployment completed successfully!"
}

# Function to run post-deployment checks
test_deployment() {
    log "INFO" "Running post-deployment checks..."
    
    local checks=(
        "WordPress Frontend:http://localhost:8888"
        "WordPress Admin:http://localhost:8888/wp-admin"
        "Prometheus:http://localhost:9090"
        "Grafana:http://localhost:3000"
        "AlertManager:http://localhost:9093"
    )
    
    for check in "${checks[@]}"; do
        local name="${check%:*}"
        local url="${check#*:}"
        
        if curl -f -s "$url" >/dev/null 2>&1; then
            log "INFO" "$name is accessible"
        else
            log "WARNING" "$name is not accessible"
        fi
    done
}

# Function to display deployment summary
show_summary() {
    echo ""
    echo -e "${GREEN}🎉 BlackCnote Production Deployment Summary${NC}"
    echo -e "${GREEN}=============================================${NC}"
    echo -e "${BLUE}Application URLs:${NC}"
    echo -e "  • WordPress Frontend: ${GREEN}http://localhost:8888${NC}"
    echo -e "  • WordPress Admin: ${GREEN}http://localhost:8888/wp-admin${NC}"
    echo -e "  • React App: ${GREEN}http://localhost:3001${NC}"
    echo ""
    echo -e "${BLUE}Monitoring URLs:${NC}"
    echo -e "  • Prometheus: ${GREEN}http://localhost:9090${NC}"
    echo -e "  • Grafana: ${GREEN}http://localhost:3000${NC} (admin/admin)"
    echo -e "  • AlertManager: ${GREEN}http://localhost:9093${NC}"
    echo ""
    echo -e "${BLUE}Management Commands:${NC}"
    echo -e "  • View logs: ${YELLOW}docker-compose logs -f${NC}"
    echo -e "  • Stop services: ${YELLOW}docker-compose down${NC}"
    echo -e "  • Restart services: ${YELLOW}docker-compose restart${NC}"
    echo ""
    echo -e "${BLUE}Next Steps:${NC}"
    echo -e "  1. Configure your domain DNS to point to this server"
    echo -e "  2. Update SSL certificates for your domain"
    echo -e "  3. Configure email settings in WordPress"
    echo -e "  4. Set up automated backups"
    echo -e "  5. Configure monitoring alerts"
    echo ""
}

# Main deployment process
main() {
    log "INFO" "Starting BlackCnote production deployment..."
    
    # Check prerequisites
    check_docker
    
    # Create directories
    create_directories
    
    # Create backup
    create_backup
    
    # Generate SSL certificates
    generate_ssl_certificates
    
    # Update configurations
    update_configurations
    
    # Deploy services
    deploy_services
    
    # Run health checks
    test_deployment
    
    # Show summary
    show_summary
    
    log "INFO" "Production deployment completed successfully!"
}

# Run main function
main "$@" 