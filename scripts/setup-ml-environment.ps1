# BlackCnote Machine Learning & AI Environment Setup Script
# This script sets up the complete ML/AI environment for BlackCnote
# Created: 2025-06-27
# Version: 1.0.0

param(
    [switch]$SkipDocker,
    [switch]$SkipDirectories,
    [switch]$Verbose
)

# Color output functions
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { param([string]$Message) Write-ColorOutput "✅ $Message" "Green" }
function Write-Error { param([string]$Message) Write-ColorOutput "❌ $Message" "Red" }
function Write-Warning { param([string]$Message) Write-ColorOutput "⚠️ $Message" "Yellow" }
function Write-Info { param([string]$Message) Write-ColorOutput "ℹ️ $Message" "Cyan" }

# Get project root
$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

Write-ColorOutput "===============================================" "Magenta"
Write-ColorOutput "BlackCnote ML/AI Environment Setup" "Magenta"
Write-ColorOutput "===============================================" "Magenta"
Write-Output ""

# Step 1: Create ML/AI directories
if (-not $SkipDirectories) {
    Write-Info "Step 1: Creating ML/AI directories..."
    
    $mlDirectories = @(
        "ml-notebooks",
        "ml-models",
        "ml-data",
        "ml-experiments",
        "ml-config",
        "ml-api",
        "ml-dashboard",
        "ml-pipelines",
        "ml-ray",
        "ml-transformers",
        "ml-kubeflow",
        "ml-sql",
        "ml-dashboards",
        "ml-datasources",
        "ml-prometheus"
    )
    
    foreach ($dir in $mlDirectories) {
        $fullPath = Join-Path $projectRoot $dir
        if (-not (Test-Path $fullPath)) {
            try {
                New-Item -ItemType Directory -Path $fullPath -Force | Out-Null
                Write-Success "Created: $dir"
            } catch {
                Write-Warning "Could not create: $dir - $($_.Exception.Message)"
            }
        } else {
            Write-Info "Directory exists: $dir"
        }
    }
}

# Step 2: Create ML configuration files
Write-Info "Step 2: Creating ML configuration files..."

# Redis ML configuration
$redisMLConfig = @"
# Redis Configuration for ML/AI workloads
bind 0.0.0.0
port 6379
timeout 0
tcp-keepalive 300
daemonize no
supervised no
pidfile /var/run/redis_6379.pid
loglevel notice
logfile ""
databases 16
save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir ./
slave-serve-stale-data yes
slave-read-only yes
repl-diskless-sync no
repl-diskless-sync-delay 5
repl-ping-slave-period 10
repl-timeout 60
repl-disable-tcp-nodelay no
slave-priority 100
maxmemory 2gb
maxmemory-policy allkeys-lru
appendonly yes
appendfilename "appendonly.aof"
appendfsync everysec
no-appendfsync-on-rewrite no
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb
aof-load-truncated yes
lua-time-limit 5000
slowlog-log-slower-than 10000
slowlog-max-len 128
latency-monitor-threshold 0
notify-keyspace-events ""
hash-max-ziplist-entries 512
hash-max-ziplist-value 64
list-max-ziplist-size -2
list-compress-depth 0
set-max-intset-entries 512
zset-max-ziplist-entries 128
zset-max-ziplist-value 64
hll-sparse-max-bytes 3000
activerehashing yes
client-output-buffer-limit normal 0 0 0
client-output-buffer-limit slave 256mb 64mb 60
client-output-buffer-limit pubsub 32mb 8mb 60
hz 10
aof-rewrite-incremental-fsync yes
"@

$redisMLConfigPath = Join-Path $projectRoot "config\redis-ml.conf"
try {
    $redisMLConfig | Out-File -FilePath $redisMLConfigPath -Encoding UTF8
    Write-Success "Created Redis ML configuration"
} catch {
    Write-Warning "Could not create Redis ML configuration: $($_.Exception.Message)"
}

# Prometheus ML configuration
$prometheusMLConfig = @"
global:
  scrape_interval: 15s
  evaluation_interval: 15s

rule_files:
  # - "first_rules.yml"
  # - "second_rules.yml"

scrape_configs:
  - job_name: 'prometheus'
    static_configs:
      - targets: ['localhost:9090']

  - job_name: 'mlflow'
    static_configs:
      - targets: ['mlflow:5000']

  - job_name: 'tensorflow-serving'
    static_configs:
      - targets: ['tensorflow-serving:8501']

  - job_name: 'torchserve'
    static_configs:
      - targets: ['torchserve:8082']

  - job_name: 'ml-api'
    static_configs:
      - targets: ['ml-api:8000']

  - job_name: 'ray'
    static_configs:
      - targets: ['ray-head:10001']

  - job_name: 'airflow'
    static_configs:
      - targets: ['airflow-webserver:8080']
"@

$prometheusMLConfigPath = Join-Path $projectRoot "ml-prometheus\prometheus.yml"
try {
    if (-not (Test-Path (Split-Path $prometheusMLConfigPath -Parent))) {
        New-Item -ItemType Directory -Path (Split-Path $prometheusMLConfigPath -Parent) -Force | Out-Null
    }
    $prometheusMLConfig | Out-File -FilePath $prometheusMLConfigPath -Encoding UTF8
    Write-Success "Created Prometheus ML configuration"
} catch {
    Write-Warning "Could not create Prometheus ML configuration: $($_.Exception.Message)"
}

# Step 3: Create sample ML API
Write-Info "Step 3: Creating sample ML API..."

$mlAPIMain = @"
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import numpy as np
import redis
import psycopg2
import json
import os
from typing import List, Dict, Any

app = FastAPI(title="BlackCnote ML API", version="1.0.0")

# Redis connection
redis_client = redis.Redis.from_url(os.getenv("REDIS_URL", "redis://localhost:6379"))

# PostgreSQL connection
postgres_url = os.getenv("POSTGRES_URL", "postgresql://ml_user:ml_password@localhost:5432/ml_metadata")

class PredictionRequest(BaseModel):
    data: List[float]
    model_name: str = "default"

class PredictionResponse(BaseModel):
    prediction: List[float]
    confidence: float
    model_used: str
    timestamp: str

@app.get("/")
async def root():
    return {"message": "BlackCnote ML API", "status": "running"}

@app.get("/health")
async def health():
    try:
        # Test Redis
        redis_client.ping()
        # Test PostgreSQL
        conn = psycopg2.connect(postgres_url)
        conn.close()
        return {"status": "healthy", "redis": "connected", "postgres": "connected"}
    except Exception as e:
        return {"status": "unhealthy", "error": str(e)}

@app.post("/predict", response_model=PredictionResponse)
async def predict(request: PredictionRequest):
    try:
        # Simple ML prediction (replace with actual model)
        data = np.array(request.data)
        prediction = np.mean(data) + np.random.normal(0, 0.1)
        confidence = 0.95
        
        # Store in Redis cache
        cache_key = f"prediction:{hash(str(request.data))}"
        redis_client.setex(cache_key, 3600, json.dumps({
            "prediction": prediction,
            "confidence": confidence,
            "model": request.model_name
        }))
        
        # Store metadata in PostgreSQL
        try:
            conn = psycopg2.connect(postgres_url)
            cursor = conn.cursor()
            cursor.execute("""
                INSERT INTO predictions (model_name, input_data, prediction, confidence, timestamp)
                VALUES (%s, %s, %s, %s, NOW())
            """, (request.model_name, json.dumps(request.data), prediction, confidence))
            conn.commit()
            conn.close()
        except Exception as e:
            print(f"PostgreSQL error: {e}")
        
        return PredictionResponse(
            prediction=[float(prediction)],
            confidence=confidence,
            model_used=request.model_name,
            timestamp=str(np.datetime64('now'))
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/models")
async def list_models():
    return {
        "available_models": [
            "default",
            "tensorflow_model",
            "pytorch_model",
            "scikit_model"
        ]
    }

@app.get("/cache/stats")
async def cache_stats():
    try:
        info = redis_client.info()
        return {
            "connected_clients": info.get("connected_clients", 0),
            "used_memory": info.get("used_memory_human", "0B"),
            "total_commands_processed": info.get("total_commands_processed", 0)
        }
    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
"@

$mlAPIPath = Join-Path $projectRoot "ml-api\main.py"
try {
    if (-not (Test-Path (Split-Path $mlAPIPath -Parent))) {
        New-Item -ItemType Directory -Path (Split-Path $mlAPIPath -Parent) -Force | Out-Null
    }
    $mlAPIMain | Out-File -FilePath $mlAPIPath -Encoding UTF8
    Write-Success "Created ML API main file"
} catch {
    Write-Warning "Could not create ML API main file: $($_.Exception.Message)"
}

# Step 4: Create sample Streamlit dashboard
Write-Info "Step 4: Creating sample Streamlit dashboard..."

$streamlitApp = @"
import streamlit as st
import pandas as pd
import numpy as np
import plotly.express as px
import plotly.graph_objects as go
import requests
import json
from datetime import datetime

st.set_page_config(
    page_title="BlackCnote ML Dashboard",
    page_icon="🤖",
    layout="wide"
)

st.title("🤖 BlackCnote Machine Learning Dashboard")
st.markdown("---")

# Sidebar
st.sidebar.header("Configuration")
api_url = st.sidebar.text_input("API URL", value="http://localhost:8000")
model_name = st.sidebar.selectbox("Model", ["default", "tensorflow_model", "pytorch_model", "scikit_model"])

# Main content
col1, col2 = st.columns(2)

with col1:
    st.header("📊 Model Prediction")
    
    # Input data
    st.subheader("Input Data")
    data_input = st.text_area("Enter data (comma-separated numbers)", value="1.0, 2.0, 3.0, 4.0, 5.0")
    
    if st.button("Make Prediction"):
        try:
            # Parse input data
            data = [float(x.strip()) for x in data_input.split(",")]
            
            # Make API call
            response = requests.post(f"{api_url}/predict", json={
                "data": data,
                "model_name": model_name
            })
            
            if response.status_code == 200:
                result = response.json()
                st.success("Prediction successful!")
                st.json(result)
                
                # Store in session state for visualization
                if "predictions" not in st.session_state:
                    st.session_state.predictions = []
                st.session_state.predictions.append({
                    "timestamp": datetime.now(),
                    "prediction": result["prediction"][0],
                    "confidence": result["confidence"],
                    "model": result["model_used"]
                })
            else:
                st.error(f"API Error: {response.text}")
        except Exception as e:
            st.error(f"Error: {str(e)}")

with col2:
    st.header("📈 Model Performance")
    
    # Health check
    try:
        health_response = requests.get(f"{api_url}/health")
        if health_response.status_code == 200:
            health_data = health_response.json()
            st.success("✅ API Health Check")
            st.json(health_data)
        else:
            st.error("❌ API Health Check Failed")
    except Exception as e:
        st.error(f"❌ API Health Check Error: {str(e)}")

# Cache statistics
st.header("💾 Cache Statistics")
try:
    cache_response = requests.get(f"{api_url}/cache/stats")
    if cache_response.status_code == 200:
        cache_data = cache_response.json()
        col1, col2, col3 = st.columns(3)
        with col1:
            st.metric("Connected Clients", cache_data.get("connected_clients", 0))
        with col2:
            st.metric("Memory Used", cache_data.get("used_memory", "0B"))
        with col3:
            st.metric("Commands Processed", cache_data.get("total_commands_processed", 0))
    else:
        st.error("Could not retrieve cache statistics")
except Exception as e:
    st.error(f"Cache statistics error: {str(e)}")

# Prediction history
if "predictions" in st.session_state and st.session_state.predictions:
    st.header("📋 Prediction History")
    
    df = pd.DataFrame(st.session_state.predictions)
    df["timestamp"] = pd.to_datetime(df["timestamp"])
    
    # Line chart of predictions
    fig = px.line(df, x="timestamp", y="prediction", 
                  title="Prediction Values Over Time",
                  labels={"prediction": "Prediction Value", "timestamp": "Time"})
    st.plotly_chart(fig, use_container_width=True)
    
    # Confidence distribution
    fig2 = px.histogram(df, x="confidence", 
                       title="Confidence Distribution",
                       labels={"confidence": "Confidence", "count": "Frequency"})
    st.plotly_chart(fig2, use_container_width=True)
    
    # Data table
    st.subheader("Recent Predictions")
    st.dataframe(df.tail(10))

# Model information
st.header("🔧 Available Models")
try:
    models_response = requests.get(f"{api_url}/models")
    if models_response.status_code == 200:
        models_data = models_response.json()
        for model in models_data["available_models"]:
            st.write(f"• {model}")
    else:
        st.error("Could not retrieve model information")
except Exception as e:
    st.error(f"Model information error: {str(e)}")

st.markdown("---")
st.markdown("*BlackCnote ML Dashboard - Powered by Streamlit*")
"@

$streamlitAppPath = Join-Path $projectRoot "ml-dashboard\app.py"
try {
    if (-not (Test-Path (Split-Path $streamlitAppPath -Parent))) {
        New-Item -ItemType Directory -Path (Split-Path $streamlitAppPath -Parent) -Force | Out-Null
    }
    $streamlitApp | Out-File -FilePath $streamlitAppPath -Encoding UTF8
    Write-Success "Created Streamlit dashboard"
} catch {
    Write-Warning "Could not create Streamlit dashboard: $($_.Exception.Message)"
}

# Step 5: Create sample Jupyter notebook
Write-Info "Step 5: Creating sample Jupyter notebook..."

$jupyterNotebook = @"
{
 "cells": [
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "# BlackCnote Machine Learning Notebook\n",
    "\n",
    "This notebook demonstrates ML/AI capabilities in the BlackCnote environment."
   ]
  },
  {
   "cell_type": "code",
   "execution_count": null,
   "metadata": {},
   "outputs": [],
   "source": [
    "import numpy as np\n",
    "import pandas as pd\n",
    "import matplotlib.pyplot as plt\n",
    "import seaborn as sns\n",
    "from sklearn.model_selection import train_test_split\n",
    "from sklearn.ensemble import RandomForestRegressor\n",
    "from sklearn.metrics import mean_squared_error, r2_score\n",
    "import requests\n",
    "import json\n",
    "\n",
    "print(\"BlackCnote ML Environment Ready!\")"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## Data Generation and Analysis"
   ]
  },
  {
   "cell_type": "code",
   "execution_count": null,
   "metadata": {},
   "outputs": [],
   "source": [
    "# Generate sample data\n",
    "np.random.seed(42)\n",
    "n_samples = 1000\n",
    "\n",
    "X = np.random.randn(n_samples, 5)\n",
    "y = np.sum(X, axis=1) + np.random.normal(0, 0.1, n_samples)\n",
    "\n",
    "df = pd.DataFrame(X, columns=['feature_1', 'feature_2', 'feature_3', 'feature_4', 'feature_5'])\n",
    "df['target'] = y\n",
    "\n",
    "print(f\"Dataset shape: {df.shape}\")\n",
    "df.head()"
   ]
  },
  {
   "cell_type": "code",
   "execution_count": null,
   "metadata": {},
   "outputs": [],
   "source": [
    "# Data visualization\n",
    "plt.figure(figsize=(12, 8))\n",
    "\n",
    "plt.subplot(2, 2, 1)\n",
    "plt.scatter(df['feature_1'], df['target'], alpha=0.6)\n",
    "plt.xlabel('Feature 1')\n",
    "plt.ylabel('Target')\n",
    "plt.title('Feature 1 vs Target')\n",
    "\n",
    "plt.subplot(2, 2, 2)\n",
    "plt.hist(df['target'], bins=30, alpha=0.7)\n",
    "plt.xlabel('Target')\n",
    "plt.ylabel('Frequency')\n",
    "plt.title('Target Distribution')\n",
    "\n",
    "plt.subplot(2, 2, 3)\n",
    "correlation_matrix = df.corr()\n",
    "sns.heatmap(correlation_matrix, annot=True, cmap='coolwarm', center=0)\n",
    "plt.title('Correlation Matrix')\n",
    "\n",
    "plt.subplot(2, 2, 4)\n",
    "df.boxplot(column=['feature_1', 'feature_2', 'feature_3', 'feature_4', 'feature_5'])\n",
    "plt.title('Feature Distributions')\n",
    "plt.xticks(rotation=45)\n",
    "\n",
    "plt.tight_layout()\n",
    "plt.show()"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## Model Training"
   ]
  },
  {
   "cell_type": "code",
   "execution_count": null,
   "metadata": {},
   "outputs": [],
   "source": [
    "# Split data\n",
    "X_train, X_test, y_train, y_test = train_test_split(\n",
    "    df.drop('target', axis=1), df['target'], \n",
    "    test_size=0.2, random_state=42\n",
    ")\n",
    "\n",
    "# Train model\n",
    "model = RandomForestRegressor(n_estimators=100, random_state=42)\n",
    "model.fit(X_train, y_train)\n",
    "\n",
    "# Make predictions\n",
    "y_pred = model.predict(X_test)\n",
    "\n",
    "# Evaluate model\n",
    "mse = mean_squared_error(y_test, y_pred)\n",
    "r2 = r2_score(y_test, y_pred)\n",
    "\n",
    "print(f\"Mean Squared Error: {mse:.4f}\")\n",
    "print(f\"R² Score: {r2:.4f}\")"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## API Integration"
   ]
  },
  {
   "cell_type": "code",
   "execution_count": null,
   "metadata": {},
   "outputs": [],
   "source": [
    "# Test the ML API\n",
    "api_url = \"http://localhost:8000\"\n",
    "\n",
    "try:\n",
    "    # Health check\n",
    "    health_response = requests.get(f\"{api_url}/health\")\n",
    "    print(f\"API Health: {health_response.json()}\")\n",
    "    \n",
    "    # Make prediction\n",
    "    test_data = [1.0, 2.0, 3.0, 4.0, 5.0]\n",
    "    prediction_response = requests.post(f\"{api_url}/predict\", json={\n",
    "        \"data\": test_data,\n",
    "        \"model_name\": \"scikit_model\"\n",
    "    })\n",
    "    \n",
    "    if prediction_response.status_code == 200:\n",
    "        result = prediction_response.json()\n",
    "        print(f\"Prediction Result: {result}\")\n",
    "    else:\n",
    "        print(f\"Prediction failed: {prediction_response.text}\")\n",
    "        \n",
    "except Exception as e:\n",
    "    print(f\"API Error: {str(e)}\")"
   ]
  }
 ],
 "metadata": {
  "kernelspec": {
   "display_name": "Python 3",
   "language": "python",
   "name": "python3"
  },
  "language_info": {
   "codemirror_mode": {
    "name": "ipython",
    "version": 3
   },
   "file_extension": ".py",
   "mimetype": "text/x-python",
   "name": "python",
   "nbconvert_exporter": "python",
   "pygments_lexer": "ipython3",
   "version": "3.9.0"
  }
 },
 "nbformat": 4,
 "nbformat_minor": 4
}
"@

$jupyterNotebookPath = Join-Path $projectRoot "ml-notebooks\blackcnote-ml-demo.ipynb"
try {
    if (-not (Test-Path (Split-Path $jupyterNotebookPath -Parent))) {
        New-Item -ItemType Directory -Path (Split-Path $jupyterNotebookPath -Parent) -Force | Out-Null
    }
    $jupyterNotebook | Out-File -FilePath $jupyterNotebookPath -Encoding UTF8
    Write-Success "Created sample Jupyter notebook"
} catch {
    Write-Warning "Could not create Jupyter notebook: $($_.Exception.Message)"
}

# Step 6: Create ML startup script
Write-Info "Step 6: Creating ML startup script..."

$mlStartupScript = @"
# BlackCnote ML/AI Environment Startup Script
# This script starts the ML/AI services

echo "Starting BlackCnote ML/AI Environment..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running. Please start Docker Desktop first."
    exit 1
fi

# Navigate to project directory
cd "$projectRoot"

# Start ML services
echo "Starting ML services..."
docker-compose -f config/docker/docker-compose-ml.yml up -d

# Wait for services to be ready
echo "Waiting for services to be ready..."
sleep 30

# Check service status
echo "Service Status:"
docker-compose -f config/docker/docker-compose-ml.yml ps

echo ""
echo "ML/AI Services Available:"
echo "- Jupyter Notebook: http://localhost:8888 (token: blackcnote_ml)"
echo "- TensorFlow Serving: http://localhost:8501"
echo "- PyTorch Serve: http://localhost:8080"
echo "- MLflow: http://localhost:5000"
echo "- MinIO Console: http://localhost:9001 (admin/blackcnote_password)"
echo "- Grafana: http://localhost:3001 (admin/blackcnote)"
echo "- Prometheus: http://localhost:9091"
echo "- ML API: http://localhost:8000"
echo "- Streamlit Dashboard: http://localhost:8502"
echo "- Airflow: http://localhost:8083"
echo "- Ray Dashboard: http://localhost:10001"
echo "- Transformers API: http://localhost:8001"
echo "- Kubeflow Jupyter: http://localhost:8889 (token: blackcnote_kubeflow)"
echo ""
echo "ML/AI Environment started successfully!"
"@

$mlStartupScriptPath = Join-Path $projectRoot "start-ml-environment.sh"
try {
    $mlStartupScript | Out-File -FilePath $mlStartupScriptPath -Encoding UTF8
    Write-Success "Created ML startup script"
} catch {
    Write-Warning "Could not create ML startup script: $($_.Exception.Message)"
}

# Step 7: Create Windows batch file for ML startup
$mlStartupBatch = @"
@echo off
echo Starting BlackCnote ML/AI Environment...

REM Check if Docker is running
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo Docker is not running. Please start Docker Desktop first.
    pause
    exit /b 1
)

REM Navigate to project directory
cd /d "%~dp0"

REM Start ML services
echo Starting ML services...
docker-compose -f config\docker\docker-compose-ml.yml up -d

REM Wait for services to be ready
echo Waiting for services to be ready...
timeout /t 30 /nobreak >nul

REM Check service status
echo Service Status:
docker-compose -f config\docker\docker-compose-ml.yml ps

echo.
echo ML/AI Services Available:
echo - Jupyter Notebook: http://localhost:8888 ^(token: blackcnote_ml^)
echo - TensorFlow Serving: http://localhost:8501
echo - PyTorch Serve: http://localhost:8080
echo - MLflow: http://localhost:5000
echo - MinIO Console: http://localhost:9001 ^(admin/blackcnote_password^)
echo - Grafana: http://localhost:3001 ^(admin/blackcnote^)
echo - Prometheus: http://localhost:9091
echo - ML API: http://localhost:8000
echo - Streamlit Dashboard: http://localhost:8502
echo - Airflow: http://localhost:8083
echo - Ray Dashboard: http://localhost:10001
echo - Transformers API: http://localhost:8001
echo - Kubeflow Jupyter: http://localhost:8889 ^(token: blackcnote_kubeflow^)
echo.
echo ML/AI Environment started successfully!
pause
"@

$mlStartupBatchPath = Join-Path $projectRoot "start-ml-environment.bat"
try {
    $mlStartupBatch | Out-File -FilePath $mlStartupBatchPath -Encoding ASCII
    Write-Success "Created ML startup batch file"
} catch {
    Write-Warning "Could not create ML startup batch file: $($_.Exception.Message)"
}

Write-Output ""
Write-ColorOutput "===============================================" "Magenta"
Write-ColorOutput "ML/AI Environment Setup Complete!" "Magenta"
Write-ColorOutput "===============================================" "Magenta"
Write-Output ""

Write-Success "ML/AI environment has been set up successfully!"
Write-Output ""
Write-Info "Next steps:"
Write-Output "  1. Run the Docker API fix script if needed:"
Write-Output "     .\scripts\fix-docker-api-engine.ps1"
Write-Output ""
Write-Output "  2. Start the ML/AI environment:"
Write-Output "     docker-compose -f config\docker\docker-compose-ml.yml up -d"
Write-Output ""
Write-Output "  3. Access the services:"
Write-Output "     - Jupyter: http://localhost:8888"
Write-Output "     - ML API: http://localhost:8000"
Write-Output "     - Dashboard: http://localhost:8502"
Write-Output ""
Write-Info "The ML/AI environment includes:"
Write-Output "  ✅ Jupyter Notebooks for development"
Write-Output "  ✅ TensorFlow & PyTorch serving"
Write-Output "  ✅ MLflow for experiment tracking"
Write-Output "  ✅ MinIO for model storage"
Write-Output "  ✅ Redis for caching"
Write-Output "  ✅ PostgreSQL for metadata"
Write-Output "  ✅ Grafana & Prometheus for monitoring"
Write-Output "  ✅ FastAPI for ML services"
Write-Output "  ✅ Streamlit for dashboards"
Write-Output "  ✅ Airflow for pipelines"
Write-Output "  ✅ Ray for distributed computing"
Write-Output "  ✅ Hugging Face Transformers"
Write-Output "  ✅ Kubeflow Jupyter"
Write-Output "" 