@echo off
REM BlackCnote Enhanced Docker Startup Script (Batch Version)
REM This script starts Docker Desktop with elevated privileges

setlocal enabledelayedexpansion

echo BlackCnote Enhanced Docker Startup Script
echo =============================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator!
    echo Please right-click this file and select "Run as Administrator"
    echo Then run this script again.
    pause
    exit /b 1
)

echo SUCCESS: Running with administrator privileges
echo.

REM Check if Docker Desktop is installed
set "DOCKER_PATH=C:\Program Files\Docker\Docker\Docker Desktop.exe"
if not exist "%DOCKER_PATH%" (
    echo ERROR: Docker Desktop not found at: %DOCKER_PATH%
    echo Please install Docker Desktop first.
    pause
    exit /b 1
)

echo SUCCESS: Docker Desktop found
echo.

REM Check if Docker daemon configuration exists
set "DAEMON_CONFIG=C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\config\docker\daemon.json"
if exist "%DAEMON_CONFIG%" (
    echo SUCCESS: BlackCnote Docker daemon configuration found
    
    REM Copy daemon configuration to Docker Desktop location
    set "DOCKER_DAEMON=%USERPROFILE%\.docker\daemon.json"
    set "DOCKER_DAEMON_DIR=%USERPROFILE%\.docker"
    
    if not exist "%DOCKER_DAEMON_DIR%" (
        mkdir "%DOCKER_DAEMON_DIR%"
    )
    
    copy "%DAEMON_CONFIG%" "%DOCKER_DAEMON%" >nul
    echo SUCCESS: Docker daemon configuration applied
) else (
    echo WARNING: BlackCnote Docker daemon configuration not found
    echo Using default Docker configuration
)

echo.

REM Stop any existing Docker processes
echo INFO: Stopping existing Docker processes...
taskkill /f /im "Docker Desktop.exe" >nul 2>&1
if %errorLevel% equ 0 (
    echo SUCCESS: Docker processes stopped
    timeout /t 3 /nobreak >nul
) else (
    echo INFO: No running Docker processes found
)

echo.

REM Start Docker Desktop with elevated privileges
echo INFO: Starting Docker Desktop...
start "" /min "%DOCKER_PATH%" --verbose
if %errorLevel% equ 0 (
    echo SUCCESS: Docker Desktop started
) else (
    echo ERROR: Failed to start Docker Desktop
    pause
    exit /b 1
)

echo.

REM Wait for Docker to be ready
echo INFO: Waiting for Docker daemon to be ready...
set /a attempts=0
set /a maxAttempts=30
set dockerReady=false

:waitLoop
set /a attempts+=1
echo    Attempt %attempts%/%maxAttempts%...

docker info >nul 2>&1
if %errorLevel% equ 0 (
    set dockerReady=true
    echo SUCCESS: Docker daemon is ready!
    goto :dockerReady
) else (
    if %attempts% lss %maxAttempts% (
        timeout /t 2 /nobreak >nul
        goto :waitLoop
    ) else (
        echo ERROR: Docker daemon failed to start within %maxAttempts% attempts
        echo Please check Docker Desktop logs and try again.
        pause
        exit /b 1
    )
)

:dockerReady
echo.

REM Display Docker information
echo INFO: Docker Information:
docker info 2>nul
if %errorLevel% neq 0 (
    echo WARNING: Could not retrieve Docker information
)

echo.

REM Test Docker functionality
echo INFO: Testing Docker functionality...
docker run --rm hello-world >nul 2>&1
if %errorLevel% equ 0 (
    echo SUCCESS: Docker functionality test passed
) else (
    echo WARNING: Docker functionality test failed
)

echo.

REM Check if BlackCnote services should be started
set /p startBlackCnote="INFO: Start BlackCnote services? (y/N): "
if /i "%startBlackCnote%"=="y" (
    echo INFO: Starting BlackCnote services...
    
    set "PROJECT_ROOT=C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
    if exist "%PROJECT_ROOT%" (
        cd /d "%PROJECT_ROOT%"
        
        REM Check if docker-compose.yml exists
        if exist "docker-compose.yml" (
            echo INFO: Starting BlackCnote containers...
            docker-compose up -d
            if %errorLevel% equ 0 (
                echo SUCCESS: BlackCnote services started successfully
                
                REM Wait a moment for services to be ready
                timeout /t 5 /nobreak >nul
                
                REM Display service status
                echo INFO: Service Status:
                docker-compose ps
                
                echo.
                echo INFO: Access URLs:
                echo    WordPress: http://localhost:8888
                echo    Admin Panel: http://localhost:8888/wp-admin/
                echo    phpMyAdmin: http://localhost:8080
                echo    React Dev: http://localhost:5174
                echo    MailHog: http://localhost:8025
                echo    Redis Commander: http://localhost:8081
            ) else (
                echo ERROR: Failed to start BlackCnote services
            )
        ) else (
            echo WARNING: docker-compose.yml not found in project root
        )
    ) else (
        echo WARNING: BlackCnote project root not found: %PROJECT_ROOT%
    )
)

echo.
echo SUCCESS: Docker startup completed successfully!
echo.

REM Display helpful commands
echo INFO: Useful Commands:
echo    Check Docker status: docker info
echo    View running containers: docker ps
echo    View Docker logs: docker system df
echo    Clean up Docker: docker system prune -a
echo.

echo INFO: For more information, see:
echo    DOCKER-PRIVILEGES-FIX.md
echo    DOCKER-SETUP.md
echo    BLACKCNOTE-CANONICAL-PATHS.md
echo.

pause 