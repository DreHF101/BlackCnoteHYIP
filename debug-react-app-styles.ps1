Write-Host "==== BlackCnote React App Style Debugger ====" -ForegroundColor Cyan

# 1. Check Tailwind directives in index.css
$indexCss = Get-Content "react-app\src\index.css" -Raw
if ($indexCss -match "@tailwind base;" -and $indexCss -match "@tailwind components;" -and $indexCss -match "@tailwind utilities;") {
    Write-Host "✓ index.css contains Tailwind directives" -ForegroundColor Green
} else {
    Write-Host "✗ index.css is missing Tailwind directives!" -ForegroundColor Red
}

# 2. Check for CSS import in main.tsx
$mainTsx = Get-Content "react-app\src\main.tsx" -Raw
if ($mainTsx -match "import './index.css'") {
    Write-Host "✓ main.tsx imports index.css" -ForegroundColor Green
} else {
    Write-Host "✗ main.tsx does NOT import index.css!" -ForegroundColor Red
}

# 3. Check PostCSS and Tailwind config syntax
$postcss = Get-Content "react-app\postcss.config.js" -Raw
$tailwind = Get-Content "react-app\tailwind.config.js" -Raw
$pkg = Get-Content "react-app\package.json" -Raw
if ($postcss -match "export default") { Write-Host "✓ postcss.config.js uses ES module syntax" -ForegroundColor Green } else { Write-Host "✗ postcss.config.js does NOT use ES module syntax!" -ForegroundColor Red }
if ($tailwind -match "export default") { Write-Host "✓ tailwind.config.js uses ES module syntax" -ForegroundColor Green } else { Write-Host "✗ tailwind.config.js does NOT use ES module syntax!" -ForegroundColor Red }
if ($pkg -match '"type"\s*:\s*"module"') { Write-Host "✓ package.json has type: module" -ForegroundColor Green } else { Write-Host "✗ package.json missing type: module!" -ForegroundColor Red }

# 4. Get last 20 lines of React app logs
Write-Host "`n[LOGS] Last 20 lines of React app logs:" -ForegroundColor Yellow
docker-compose -f "docker-compose.yml" logs --tail=20 react-app

# 5. Run build inside container
Write-Host "`n[BUILD] Running npm run build inside container:" -ForegroundColor Yellow
docker-compose -f "docker-compose.yml" exec react-app npm run build

# 6. Test asset URLs from host
$urls = @("http://localhost:5174/", "http://localhost:5174/index.html", "http://localhost:5174/src/index.css")
foreach ($url in $urls) {
    try {
        $response = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 5
        Write-Host "✓ $url - Status: $($response.StatusCode)" -ForegroundColor Green
    } catch {
        Write-Host "✗ $url - Failed: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n[STEP 7] Please check your browser's Console and Network tabs for errors." -ForegroundColor Cyan
Read-Host "Press Enter to exit"