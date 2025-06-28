#!/bin/bash
# BlackCnote Docker Health Check

echo "Checking Docker services..."

# Check if all containers are running
if docker-compose ps | grep -q "Up"; then
    echo "✅ All containers are running"
else
    echo "❌ Some containers are not running"
    exit 1
fi

# Check WordPress accessibility
if curl -f http://localhost:8888 > /dev/null 2>&1; then
    echo "✅ WordPress is accessible"
else
    echo "❌ WordPress is not accessible"
    exit 1
fi

# Check React app accessibility
if curl -f http://localhost:5174 > /dev/null 2>&1; then
    echo "✅ React app is accessible"
else
    echo "❌ React app is not accessible"
    exit 1
fi

# Check database connection
if docker-compose exec mysql mysql -uroot -pblackcnote_password -e "SELECT 1;" > /dev/null 2>&1; then
    echo "✅ Database is accessible"
else
    echo "❌ Database is not accessible"
    exit 1
fi

echo "🎉 All health checks passed!"
exit 0