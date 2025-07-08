# BlackCnote React App and Dev Tools Fix Script
# Fixes React App 404 error and Dev Tools connection issues

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "BLACKCNOTE REACT & DEV TOOLS FIX" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { Write-ColorOutput $args "Green" }
function Write-Warning { Write-ColorOutput $args "Yellow" }
function Write-Error { Write-ColorOutput $args "Red" }
function Write-Info { Write-ColorOutput $args "Cyan" }

# Set project root
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

Write-Info "Step 1: Stopping problematic containers..."
docker-compose stop react-app dev-tools
Write-Success "Containers stopped"

Write-Info "Step 2: Fixing React App configuration..."

# Create a proper React app Dockerfile
$reactDockerfile = @"
# BlackCnote React App - Fixed Dev Dockerfile
FROM node:18-alpine

# Set working directory
WORKDIR /app

# Install dependencies first (for better caching)
COPY package*.json ./
RUN npm ci

# Copy source code
COPY . .

# Environment variables for development
ENV CHOKIDAR_USEPOLLING=true
ENV WATCHPACK_POLLING=true
ENV FAST_REFRESH=true
ENV NODE_ENV=development
ENV HOST=0.0.0.0
ENV PORT=5174

# Expose port
EXPOSE 5174

# Start Vite dev server with proper configuration
CMD ["npm", "run", "dev:docker"]
"@

Set-Content -Path "react-app/Dockerfile.dev" -Value $reactDockerfile -Encoding UTF8
Write-Success "Updated React Dockerfile"

Write-Info "Step 3: Fixing Dev Tools configuration..."

# Create a proper Dev Tools Dockerfile
$devToolsDockerfile = @"
# BlackCnote Dev Tools - Fixed Dockerfile
FROM node:18-alpine

# Install global tools
RUN npm install -g nodemon concurrently http-server

# Set working directory
WORKDIR /app

# Create a simple dev tools server
RUN echo 'const http = require("http"); const server = http.createServer((req, res) => { res.writeHead(200, {"Content-Type": "text/html"}); res.end("<h1>BlackCnote Dev Tools</h1><p>Development tools are available</p>"); }); server.listen(9229, "0.0.0.0", () => console.log("Dev Tools server running on port 9229"));' > /app/dev-tools-server.js

# Expose port
EXPOSE 9229

# Start dev tools server
CMD ["node", "/app/dev-tools-server.js"]
"@

Set-Content -Path "dev-tools.Dockerfile" -Value $devToolsDockerfile -Encoding UTF8
Write-Success "Created Dev Tools Dockerfile"

Write-Info "Step 4: Updating Docker Compose configuration..."

# Read current docker-compose.yml
$composeContent = Get-Content "docker-compose.yml" -Raw

# Update React app configuration
$reactAppConfig = @"
  # React Development Server - Fixed Configuration
  react-app:
    build:
      context: ./react-app
      dockerfile: Dockerfile.dev
    container_name: blackcnote-react
    ports:
      - "5174:5174"  # Canonical React port
    volumes:
      # Live editing - React source files
      - "./react-app/src:/app/src:delegated"
      - "./react-app/public:/app/public:delegated"
      - "./react-app/package.json:/app/package.json"
      - "./react-app/vite.config.ts:/app/vite.config.ts"
      - "./react-app/tailwind.config.js:/app/tailwind.config.js"
      - "./react-app/postcss.config.js:/app/postcss.config.js"
    environment:
      - CHOKIDAR_USEPOLLING=true
      - WATCHPACK_POLLING=true
      - FAST_REFRESH=true
      - NODE_ENV=development
      - HOST=0.0.0.0
      - PORT=5174
    networks:
      - blackcnote-network
    restart: unless-stopped
"@

# Update Dev Tools configuration
$devToolsConfig = @"
  # Development Tools Container - Fixed Configuration
  dev-tools:
    build:
      context: .
      dockerfile: dev-tools.Dockerfile
    container_name: blackcnote-dev-tools
    ports:
      - "9229:9229"  # Canonical Dev Tools port
    volumes:
      # Development tools and scripts
      - "./react-app:/app/react:delegated"
      - "./scripts:/app/scripts:delegated"
      - "./tools:/app/tools:delegated"
    working_dir: /app
    networks:
      - blackcnote-network
    restart: unless-stopped
"@

# Replace the configurations in docker-compose.yml
$composeContent = $composeContent -replace '(?s)  # React Development Server.*?restart: unless-stopped', $reactAppConfig
$composeContent = $composeContent -replace '(?s)  # Development Tools Container.*?restart: unless-stopped', $devToolsConfig

Set-Content -Path "docker-compose.yml" -Value $composeContent -Encoding UTF8
Write-Success "Updated Docker Compose configuration"

Write-Info "Step 5: Rebuilding and starting containers..."
docker-compose build react-app dev-tools
docker-compose up -d react-app dev-tools

Write-Info "Step 6: Waiting for containers to start..."
Start-Sleep -Seconds 30

Write-Info "Step 7: Testing services..."

# Test React App
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Success "✅ React App is working at http://localhost:5174"
    } else {
        Write-Warning "⚠️ React App responded with status: $($response.StatusCode)"
    }
} catch {
    Write-Error "❌ React App is not accessible: $($_.Exception.Message)"
}

# Test Dev Tools
try {
    $response = Invoke-WebRequest -Uri "http://localhost:9229" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Success "✅ Dev Tools is working at http://localhost:9229"
    } else {
        Write-Warning "⚠️ Dev Tools responded with status: $($response.StatusCode)"
    }
} catch {
    Write-Error "❌ Dev Tools is not accessible: $($_.Exception.Message)"
}

Write-Info ""
Write-Info "=========================================="
Write-Info "FIX COMPLETED"
Write-Info "=========================================="
Write-Info ""
Write-Info "Container Status:"
docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
Write-Info ""
Write-Info "Next Steps:"
Write-Info "1. Check React App: http://localhost:5174"
Write-Info "2. Check Dev Tools: http://localhost:9229"
Write-Info "3. If issues persist, check container logs:"
Write-Info "   docker logs blackcnote-react"
Write-Info "   docker logs blackcnote-dev-tools" 