#!/bin/bash

echo "🐳 Setting up BlackCnote Docker Environment..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Create necessary directories
echo "📁 Creating directories..."
mkdir -p db
mkdir -p blackcnote/wp-content/uploads
mkdir -p blackcnote/wp-content/plugins
mkdir -p blackcnote/wp-content/themes
mkdir -p blackcnote/wp-content/mu-plugins

# Copy database dump if it exists
if [ -f "hyiplab/db/hyiplab.sql" ]; then
    echo "🗄️ Copying database dump..."
    cp hyiplab/db/hyiplab.sql db/blackcnote.sql
fi

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 blackcnote/
chmod -R 777 blackcnote/wp-content/uploads/

# Build and start containers
echo "🚀 Starting Docker containers..."
docker-compose up -d --build

echo "✅ Docker environment setup complete!"
echo ""
echo "🌐 Access your applications:"
echo "   WordPress: http://localhost:8888"
echo "   React App: http://localhost:5174"
echo "   PHPMyAdmin: http://localhost:8080"
echo "   MailHog: http://localhost:8025"
echo ""
echo "📝 Useful commands:"
echo "   View logs: docker-compose logs -f"
echo "   Stop services: docker-compose down"
echo "   Restart services: docker-compose restart"
echo "   Update containers: docker-compose pull && docker-compose up -d" 