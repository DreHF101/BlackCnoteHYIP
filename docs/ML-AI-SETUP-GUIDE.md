# BlackCnote Machine Learning & AI Setup Guide

## 🚀 **Overview**

The BlackCnote project now includes a comprehensive Machine Learning and AI environment with Docker containers for development, training, deployment, and monitoring of ML models.

## 🎯 **What's Included**

### **Core ML/AI Services**

1. **Jupyter Notebooks** - Interactive development environment
2. **TensorFlow Serving** - Model deployment and serving
3. **PyTorch Serve** - PyTorch model serving
4. **MLflow** - Experiment tracking and model management
5. **MinIO** - Object storage for model artifacts
6. **Redis** - Caching and session management
7. **PostgreSQL** - Metadata storage
8. **Grafana** - Monitoring dashboards
9. **Prometheus** - Metrics collection
10. **FastAPI** - REST API for ML services
11. **Streamlit** - Interactive dashboards
12. **Apache Airflow** - ML pipeline orchestration
13. **Ray** - Distributed computing
14. **Hugging Face Transformers** - NLP models
15. **Kubeflow Jupyter** - Enterprise ML platform

## 📁 **Directory Structure**

```
BlackCnote/
├── ml-notebooks/          # Jupyter notebooks
├── ml-models/            # Trained models
├── ml-data/              # Datasets
├── ml-experiments/       # MLflow experiments
├── ml-config/            # Configuration files
├── ml-api/               # FastAPI ML service
├── ml-dashboard/         # Streamlit dashboard
├── ml-pipelines/         # Airflow DAGs
├── ml-ray/               # Ray distributed computing
├── ml-transformers/      # Hugging Face models
├── ml-kubeflow/          # Kubeflow notebooks
├── ml-sql/               # Database scripts
├── ml-dashboards/        # Grafana dashboards
├── ml-datasources/       # Grafana datasources
└── ml-prometheus/        # Prometheus config
```

## 🔧 **Setup Instructions**

### **1. Prerequisites**

- Docker Desktop installed and running
- At least 8GB RAM available
- 50GB free disk space
- Windows 10/11 with WSL2

### **2. Quick Setup**

```powershell
# Run as Administrator
.\scripts\setup-ml-environment.ps1
```

### **3. Start ML/AI Services**

```bash
# Start all ML services
docker-compose -f config/docker/docker-compose-ml.yml up -d

# Check service status
docker-compose -f config/docker/docker-compose-ml.yml ps
```

### **4. Access Services**

| Service | URL | Credentials |
|---------|-----|-------------|
| Jupyter Notebook | http://localhost:8888 | Token: `blackcnote_ml` |
| TensorFlow Serving | http://localhost:8501 | - |
| PyTorch Serve | http://localhost:8080 | - |
| MLflow | http://localhost:5000 | - |
| MinIO Console | http://localhost:9001 | admin/blackcnote_password |
| Grafana | http://localhost:3001 | admin/blackcnote |
| Prometheus | http://localhost:9091 | - |
| ML API | http://localhost:8000 | - |
| Streamlit Dashboard | http://localhost:8502 | - |
| Airflow | http://localhost:8083 | - |
| Ray Dashboard | http://localhost:10001 | - |
| Transformers API | http://localhost:8001 | - |
| Kubeflow Jupyter | http://localhost:8889 | Token: `blackcnote_kubeflow` |

## 🛠️ **Configuration**

### **Docker Daemon Configuration**

The Docker daemon has been optimized for ML workloads:

```json
{
  "data-root": "D:\\BlackCnote\\Docker\\Data",
  "exec-root": "D:\\BlackCnote\\Docker\\Exec",
  "default-shm-size": "2G",
  "max-concurrent-downloads": 20,
  "max-concurrent-uploads": 10,
  "experimental": true
}
```

### **Key Optimizations**

- **Increased shared memory** for large datasets
- **Higher concurrent operations** for faster model loading
- **Experimental features** enabled for latest capabilities
- **Custom data root** for better performance
- **NVIDIA runtime** support for GPU acceleration

## 📊 **Usage Examples**

### **1. Jupyter Notebook Development**

```python
# Access Jupyter at http://localhost:8888
# Use token: blackcnote_ml

import numpy as np
import pandas as pd
import requests

# Test ML API
response = requests.post("http://localhost:8000/predict", json={
    "data": [1.0, 2.0, 3.0, 4.0, 5.0],
    "model_name": "default"
})
print(response.json())
```

### **2. ML API Integration**

```python
import requests

# Health check
health = requests.get("http://localhost:8000/health")
print(health.json())

# Make prediction
prediction = requests.post("http://localhost:8000/predict", json={
    "data": [1.0, 2.0, 3.0, 4.0, 5.0],
    "model_name": "scikit_model"
})
print(prediction.json())
```

### **3. Model Training with MLflow**

```python
import mlflow
import mlflow.sklearn
from sklearn.ensemble import RandomForestRegressor

# Set tracking URI
mlflow.set_tracking_uri("http://localhost:5000")

# Start experiment
with mlflow.start_run():
    # Train model
    model = RandomForestRegressor()
    model.fit(X_train, y_train)
    
    # Log model
    mlflow.sklearn.log_model(model, "random_forest")
    
    # Log metrics
    mlflow.log_metric("mse", mse)
    mlflow.log_metric("r2", r2)
```

### **4. TensorFlow Model Serving**

```python
import requests
import json

# Deploy model to TensorFlow Serving
model_data = {
    "instances": [[1.0, 2.0, 3.0, 4.0, 5.0]]
}

response = requests.post(
    "http://localhost:8501/v1/models/blackcnote_model:predict",
    json=model_data
)
print(response.json())
```

## 🔍 **Monitoring & Observability**

### **Grafana Dashboards**

Access Grafana at http://localhost:3001 (admin/blackcnote) for:

- Model performance metrics
- API response times
- Resource utilization
- Prediction accuracy trends

### **Prometheus Metrics**

Access Prometheus at http://localhost:9091 for:

- Container metrics
- Custom ML metrics
- System performance
- Alert management

### **MLflow Experiment Tracking**

Access MLflow at http://localhost:5000 for:

- Experiment history
- Model versioning
- Parameter tracking
- Artifact management

## 🚨 **Troubleshooting**

### **Common Issues**

1. **Docker API Engine Stopped**
   ```powershell
   .\scripts\fix-docker-api-engine.ps1
   ```

2. **Port Conflicts**
   ```bash
   # Check port usage
   netstat -ano | findstr :8888
   
   # Stop conflicting services
   docker-compose -f config/docker/docker-compose-ml.yml down
   ```

3. **Memory Issues**
   ```bash
   # Increase Docker memory limit in Docker Desktop settings
   # Recommended: 8GB minimum, 16GB for large models
   ```

4. **Storage Issues**
   ```bash
   # Clean up Docker volumes
   docker system prune -a --volumes
   
   # Check disk space
   docker system df
   ```

### **Performance Optimization**

1. **GPU Acceleration**
   ```bash
   # Install NVIDIA Container Toolkit
   # Add GPU runtime to docker-compose services
   runtime: nvidia
   environment:
     - NVIDIA_VISIBLE_DEVICES=all
   ```

2. **Memory Optimization**
   ```bash
   # Adjust container memory limits
   deploy:
     resources:
       limits:
         memory: 4G
       reservations:
         memory: 2G
   ```

3. **Storage Optimization**
   ```bash
   # Use volume mounts for persistent data
   volumes:
     - ./ml-models:/models:delegated
     - ./ml-data:/data:cached
   ```

## 🔒 **Security Considerations**

### **Network Security**

- All services run on localhost by default
- No external network access without explicit configuration
- Use reverse proxy for production deployment

### **Authentication**

- Jupyter notebooks use token-based authentication
- MinIO uses username/password authentication
- Grafana uses admin credentials
- Consider adding SSL/TLS for production

### **Data Protection**

- Models and data stored in local volumes
- No data sent to external services by default
- Implement backup strategies for important models

## 📈 **Scaling Considerations**

### **Horizontal Scaling**

```yaml
# Scale services as needed
docker-compose -f config/docker/docker-compose-ml.yml up -d --scale ml-api=3
```

### **Load Balancing**

```yaml
# Add load balancer for API services
nginx:
  image: nginx:alpine
  ports:
    - "80:80"
  volumes:
    - ./nginx.conf:/etc/nginx/nginx.conf
```

### **Resource Limits**

```yaml
# Set resource limits for production
deploy:
  resources:
    limits:
      cpus: '2.0'
      memory: 4G
    reservations:
      cpus: '1.0'
      memory: 2G
```

## 🔄 **Updates & Maintenance**

### **Regular Updates**

```bash
# Update all images
docker-compose -f config/docker/docker-compose-ml.yml pull

# Restart services
docker-compose -f config/docker/docker-compose-ml.yml up -d
```

### **Backup Strategy**

```bash
# Backup models
tar -czf ml-models-backup.tar.gz ml-models/

# Backup databases
docker exec blackcnote-postgres-ml pg_dump -U ml_user ml_metadata > ml-db-backup.sql
```

### **Monitoring Health**

```bash
# Check service health
docker-compose -f config/docker/docker-compose-ml.yml ps

# View logs
docker-compose -f config/docker/docker-compose-ml.yml logs -f
```

## 📚 **Additional Resources**

### **Documentation**

- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [MLflow Documentation](https://mlflow.org/docs/)
- [TensorFlow Serving](https://www.tensorflow.org/tfx/guide/serving)
- [PyTorch Serve](https://pytorch.org/serve/)
- [Ray Documentation](https://docs.ray.io/)

### **Community**

- [BlackCnote GitHub Repository](https://github.com/blackcnote)
- [Docker Community](https://community.docker.com/)
- [MLflow Community](https://github.com/mlflow/mlflow)

---

**Last Updated**: June 27, 2025  
**Version**: 1.0.0  
**Compatibility**: Docker Desktop, Windows 10/11, WSL2 