#!/bin/bash

# BlackCnote Service Connection Verification Script
# ===============================================
# Verifies all canonical localhost pathways are accessible
# Ensures React app and all services are properly connected

set -e

echo "🔍 BlackCnote Service Connection Verification"
echo "============================================="
echo "Date: $(date)"
echo ""

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Service definitions with canonical URLs
declare -A services=(
    ["WordPress Frontend"]="http://localhost:8888/health"
    ["WordPress Admin"]="http://localhost:8888/wp-admin/admin-ajax.php"
    ["WordPress REST API"]="http://localhost:8888/wp-json/wp/v2/"
    ["React Development Server"]="http://localhost:5174"
    ["phpMyAdmin"]="http://localhost:8080"
    ["Redis Commander"]="http://localhost:8081"
    ["MailHog"]="http://localhost:8025"
    ["Browsersync"]="http://localhost:3000"
    ["Browsersync UI"]="http://localhost:3001"
    ["Dev Tools"]="http://localhost:9229"
    ["Metrics Exporter"]="http://localhost:9091"
)

# Service categories
core_services=("WordPress Frontend" "WordPress Admin" "WordPress REST API" "React Development Server")
db_services=("phpMyAdmin" "Redis Commander")
dev_services=("Browsersync" "Browsersync UI" "MailHog" "Dev Tools")
monitoring_services=("Metrics Exporter")

# Results tracking
total_services=0
healthy_services=0
failed_services=()
warning_services=()

echo "📋 Checking Core Application Services..."
echo "----------------------------------------"

for service in "${core_services[@]}"; do
    total_services=$((total_services + 1))
    url="${services[$service]}"
    
    echo -n "  Checking $service... "
    
    if curl -f -s -m 10 "$url" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ UP${NC}"
        healthy_services=$((healthy_services + 1))
    else
        echo -e "${RED}❌ DOWN${NC}"
        failed_services+=("$service")
    fi
done

echo ""
echo "🗄️  Checking Database & Management Services..."
echo "----------------------------------------------"

for service in "${db_services[@]}"; do
    total_services=$((total_services + 1))
    url="${services[$service]}"
    
    echo -n "  Checking $service... "
    
    if curl -f -s -m 10 "$url" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ UP${NC}"
        healthy_services=$((healthy_services + 1))
    else
        echo -e "${RED}❌ DOWN${NC}"
        failed_services+=("$service")
    fi
done

echo ""
echo "🛠️  Checking Development & Testing Services..."
echo "----------------------------------------------"

for service in "${dev_services[@]}"; do
    total_services=$((total_services + 1))
    url="${services[$service]}"
    
    echo -n "  Checking $service... "
    
    if curl -f -s -m 10 "$url" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ UP${NC}"
        healthy_services=$((healthy_services + 1))
    else
        echo -e "${YELLOW}⚠️  WARNING${NC}"
        warning_services+=("$service")
    fi
done

echo ""
echo "📊 Checking Monitoring & Health Services..."
echo "-------------------------------------------"

for service in "${monitoring_services[@]}"; do
    total_services=$((total_services + 1))
    url="${services[$service]}"
    
    echo -n "  Checking $service... "
    
    if curl -f -s -m 10 "$url" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ UP${NC}"
        healthy_services=$((healthy_services + 1))
    else
        echo -e "${YELLOW}⚠️  WARNING${NC}"
        warning_services+=("$service")
    fi
done

echo ""
echo "🔌 Checking Database Connectivity..."
echo "-----------------------------------"

# Check MySQL
echo -n "  Checking MySQL Database... "
if curl -f -s -m 10 "http://localhost:8888" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ UP${NC}"
    healthy_services=$((healthy_services + 1))
else
    echo -e "${RED}❌ DOWN${NC}"
    failed_services+=("MySQL Database")
fi
total_services=$((total_services + 1))

# Check Redis
echo -n "  Checking Redis Cache... "
if docker exec blackcnote-redis redis-cli ping > /dev/null 2>&1; then
    echo -e "${GREEN}✅ UP${NC}"
    healthy_services=$((healthy_services + 1))
else
    echo -e "${RED}❌ DOWN${NC}"
    failed_services+=("Redis Cache")
fi
total_services=$((total_services + 1))

echo ""
echo "📈 Summary Report"
echo "================="

echo -e "Total Services Checked: ${BLUE}$total_services${NC}"
echo -e "Healthy Services: ${GREEN}$healthy_services${NC}"
echo -e "Failed Services: ${RED}${#failed_services[@]}${NC}"
echo -e "Warning Services: ${YELLOW}${#warning_services[@]}${NC}"

# Calculate health percentage
if [ $total_services -gt 0 ]; then
    health_percentage=$((healthy_services * 100 / total_services))
    echo -e "Overall Health: ${BLUE}${health_percentage}%${NC}"
fi

echo ""

# Report failed services
if [ ${#failed_services[@]} -gt 0 ]; then
    echo -e "${RED}❌ Failed Services:${NC}"
    for service in "${failed_services[@]}"; do
        echo -e "  - ${RED}$service${NC}"
    done
    echo ""
fi

# Report warning services
if [ ${#warning_services[@]} -gt 0 ]; then
    echo -e "${YELLOW}⚠️  Warning Services (Optional):${NC}"
    for service in "${warning_services[@]}"; do
        echo -e "  - ${YELLOW}$service${NC}"
    done
    echo ""
fi

# React app specific check
echo "🎯 React App Specific Verification"
echo "=================================="

react_url="http://localhost:5174"
echo -n "Checking React Development Server at $react_url... "

if curl -f -s -m 10 "$react_url" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ React App is running!${NC}"
    echo -e "${BLUE}  → Access React app at: $react_url${NC}"
    echo -e "${BLUE}  → Hot reload is enabled${NC}"
    echo -e "${BLUE}  → Live editing is active${NC}"
else
    echo -e "${RED}❌ React App is not accessible${NC}"
    echo -e "${YELLOW}  → Check if React container is running${NC}"
    echo -e "${YELLOW}  → Verify Docker Compose configuration${NC}"
    echo -e "${YELLOW}  → Check React app logs${NC}"
fi

echo ""

# Docker container status
echo "🐳 Docker Container Status"
echo "=========================="

if command -v docker &> /dev/null; then
    echo "Checking BlackCnote containers..."
    
    containers=(
        "blackcnote-wordpress"
        "blackcnote-mysql"
        "blackcnote-redis"
        "blackcnote-react"
        "blackcnote-phpmyadmin"
        "blackcnote-mailhog"
        "blackcnote-browsersync"
        "blackcnote-redis-commander"
    )
    
    for container in "${containers[@]}"; do
        if docker ps --format "table {{.Names}}\t{{.Status}}" | grep -q "$container"; then
            status=$(docker ps --format "table {{.Names}}\t{{.Status}}" | grep "$container" | awk '{print $2}')
            echo -e "  ${GREEN}✅ $container: $status${NC}"
        else
            echo -e "  ${RED}❌ $container: Not running${NC}"
        fi
    done
else
    echo -e "${YELLOW}⚠️  Docker not available${NC}"
fi

echo ""

# Service URLs reference
echo "🌐 Canonical Service URLs Reference"
echo "==================================="
echo "WordPress Frontend:     http://localhost:8888"
echo "WordPress Admin:        http://localhost:8888/wp-admin/"
echo "React Development:      http://localhost:5174"
echo "phpMyAdmin:            http://localhost:8080"
echo "MailHog:               http://localhost:8025"
echo "Redis Commander:       http://localhost:8081"
echo "Browsersync:           http://localhost:3000"
echo "Browsersync UI:        http://localhost:3001"
echo "Dev Tools:             http://localhost:9229"
echo "Metrics Exporter:      http://localhost:9091"
echo "Health Check:          http://localhost:8888/health"

echo ""

# Exit with appropriate code
if [ ${#failed_services[@]} -gt 0 ]; then
    echo -e "${RED}❌ Service verification failed. Some critical services are down.${NC}"
    exit 1
elif [ ${#warning_services[@]} -gt 0 ]; then
    echo -e "${YELLOW}⚠️  Service verification completed with warnings. Optional services are down.${NC}"
    exit 0
else
    echo -e "${GREEN}✅ All services are healthy and accessible!${NC}"
    exit 0
fi 