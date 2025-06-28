#!/bin/bash

# BlackCnote React App Development Startup Script
# This script starts the Vite dev server

echo "🚀 Starting BlackCnote React App in Docker..."

# Set environment variables for development
export NODE_ENV=development
export CHOKIDAR_USEPOLLING=true
export WATCHPACK_POLLING=true
export FAST_REFRESH=true

echo "✅ Environment configured for Docker development"
echo "🌐 Starting Vite dev server on http://localhost:5174..."

# Start the Vite development server using the Docker-specific script
exec npm run dev:docker 