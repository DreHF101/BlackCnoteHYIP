@echo off
REM BlackCnote ML/AI Environment Quick Start
REM This script sets up and starts the complete ML/AI environment
REM Created: 2025-06-27
REM Version: 1.0.0

echo ========================================
echo BlackCnote ML/AI Environment Quick Start
echo ========================================
echo Starting at: %date% %time%
echo.

REM Check if running as Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator!
    echo Please right-click and select "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo Running with administrator privileges
echo.

REM Check if Docker is running
echo Checking Docker status...
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker is not running!
    echo Starting Docker API fix script...
    powershell -ExecutionPolicy Bypass -File "scripts\fix-docker-api-engine.ps1"
    if %errorlevel% neq 0 (
        echo ERROR: Failed to fix Docker API!
        pause
        exit /b 1
    )
)

echo Docker is running.
echo.

REM Setup ML environment
echo Setting up ML environment...
powershell -ExecutionPolicy Bypass -File "scripts\setup-ml-environment.ps1"
if %errorlevel% neq 0 (
    echo WARNING: ML environment setup had issues, continuing...
)

echo.

REM Stop any existing ML services
echo Stopping existing ML services...
docker-compose -f config\docker\docker-compose-ml.yml down --remove-orphans 2>nul
if %errorlevel% neq 0 (
    echo WARNING: Could not stop existing services, continuing...
)

echo.

REM Start ML services
echo Starting ML/AI services...
docker-compose -f config\docker\docker-compose-ml.yml up -d
if %errorlevel% neq 0 (
    echo ERROR: Failed to start ML services!
    pause
    exit /b 1
)

echo.

REM Wait for services to initialize
echo Waiting for services to initialize...
timeout /t 30 /nobreak >nul

REM Check service status
echo Checking service status...
docker-compose -f config\docker\docker-compose-ml.yml ps

echo.
echo ========================================
echo ML/AI Services Available:
echo ========================================
echo.
echo Jupyter Notebook:     http://localhost:8888
echo   Token: blackcnote_ml
echo.
echo TensorFlow Serving:   http://localhost:8501
echo PyTorch Serve:        http://localhost:8080
echo MLflow:               http://localhost:5000
echo.
echo MinIO Console:        http://localhost:9001
echo   Username: admin
echo   Password: blackcnote_password
echo.
echo Grafana:              http://localhost:3001
echo   Username: admin
echo   Password: blackcnote
echo.
echo Prometheus:           http://localhost:9091
echo ML API:               http://localhost:8000
echo Streamlit Dashboard:  http://localhost:8502
echo Airflow:              http://localhost:8083
echo Ray Dashboard:        http://localhost:10001
echo Transformers API:     http://localhost:8001
echo.
echo Kubeflow Jupyter:     http://localhost:8889
echo   Token: blackcnote_kubeflow
echo.
echo ========================================
echo ML/AI Environment Started Successfully!
echo ========================================
echo.
echo To stop services, run:
echo   docker-compose -f config\docker\docker-compose-ml.yml down
echo.
echo To view logs, run:
echo   docker-compose -f config\docker\docker-compose-ml.yml logs -f
echo.
echo Startup completed at: %date% %time%
echo.
timeout /t 5 /nobreak >nul 