# BlackCnote Enhanced Debug System

## Overview

The BlackCnote Enhanced Debug System provides **24/7, project-wide monitoring** of the entire BlackCnote ecosystem, including WordPress, React, HYIPLab, Docker containers, and system resources. The system operates independently of WordPress and continues monitoring even when the main application is down.

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    BlackCnote Debug System                  │
├─────────────────────────────────────────────────────────────┤
│ 1. Standalone Debug Daemon (24/7 monitoring)               │
│ 2. Metrics Exporter (Prometheus integration)               │
│ 3. Enhanced Logging (structured JSON)                      │
│ 4. File Change Monitoring (real-time)                      │
│ 5. System Resource Monitoring (memory, disk, etc.)         │
│ 6. Docker Integration (container status)                   │
│ 7. Prometheus/Grafana Integration (visualization)          │
└─────────────────────────────────────────────────────────────┘
```

## Components

### 1. Standalone Debug Daemon (`bin/blackcnote-debug-daemon.php`)

**Purpose**: Provides continuous monitoring of the entire BlackCnote project.

**Features**:
- **File Change Monitoring**: Tracks changes to PHP, JS, CSS, JSON, YAML, MD, TXT, and LOG files
- **System Resource Monitoring**: Memory usage, disk space, load average
- **Docker Integration**: Container status monitoring (when Docker is available)
- **Log File Analysis**: Scans log files for errors and exceptions
- **Structured Logging**: JSON-formatted logs with timestamps and context
- **Graceful Shutdown**: Handles SIGTERM and SIGINT signals

**Usage**:
```bash
# Start the debug daemon
php bin/blackcnote-debug-daemon.php

# Run in background
nohup php bin/blackcnote-debug-daemon.php > /dev/null 2>&1 &
```

### 2. Metrics Exporter (`bin/blackcnote-metrics-exporter.php`)

**Purpose**: Exports debug system metrics for Prometheus monitoring.

**Features**:
- **Prometheus Format**: Exports metrics in Prometheus text format
- **HTTP Server**: Serves metrics on configurable port (default: 9091)
- **Real-time Metrics**: Live metrics from debug logs
- **Multiple Metric Types**: Counters, gauges, and histograms

**Usage**:
```bash
# Export metrics to stdout
php bin/blackcnote-metrics-exporter.php

# Start HTTP server on port 9091
php bin/blackcnote-metrics-exporter.php --serve 9091

# Access metrics
curl http://localhost:9091/metrics
```

### 3. Enhanced Debug System Class (`hyiplab/app/Log/BlackCnoteDebugSystem.php`)

**Purpose**: Core logging functionality used by both WordPress and standalone components.

**Features**:
- **Standalone Operation**: No WordPress dependencies
- **Configurable Logging**: Customizable log levels and file paths
- **Error Handling**: Custom error, exception, and shutdown handlers
- **JSON Logging**: Structured log entries with context
- **Memory Management**: Efficient memory usage tracking

## Monitoring Integration

### Prometheus Configuration

The debug system integrates with Prometheus through the metrics exporter:

```yaml
# monitoring/prometheus.yml
scrape_configs:
  - job_name: 'blackcnote-debug-metrics'
    static_configs:
      - targets: ['blackcnote-debug-exporter:9091']
    scrape_interval: 30s
    metrics_path: '/metrics'
    scrape_timeout: 10s
```

### Grafana Dashboard

A comprehensive dashboard is available at `monitoring/grafana/dashboards/blackcnote-debug-dashboard.json` with panels for:

- **Log Level Distribution**: Pie chart showing error, warning, info, debug, and system logs
- **File Changes Over Time**: Graph showing file modification rates
- **System Resources**: Memory usage, disk space, and load average
- **Docker Status**: Container count and health
- **Error Rate**: Real-time error monitoring
- **Daemon Uptime**: Debug system uptime tracking

### Available Metrics

| Metric Name | Type | Description |
|-------------|------|-------------|
| `blackcnote_debug_log_entries_total` | Counter | Total number of log entries |
| `blackcnote_debug_errors_total` | Counter | Total number of error logs |
| `blackcnote_debug_warnings_total` | Counter | Total number of warning logs |
| `blackcnote_debug_info_total` | Counter | Total number of info logs |
| `blackcnote_debug_debug_total` | Counter | Total number of debug logs |
| `blackcnote_debug_system_total` | Counter | Total number of system logs |
| `blackcnote_file_changes_total` | Counter | Total number of file changes detected |
| `blackcnote_docker_containers_running` | Gauge | Number of running Docker containers |
| `blackcnote_memory_usage_bytes` | Gauge | Current memory usage in bytes |
| `blackcnote_disk_free_bytes` | Gauge | Available disk space in bytes |
| `blackcnote_daemon_uptime_seconds` | Gauge | Debug daemon uptime in seconds |

## Docker Integration

### Docker Compose Services

```yaml
# docker-compose.yml
services:
  blackcnote_debug:
    image: php:8.1-cli
    container_name: blackcnote_debug
    volumes:
      - .:/app
      - ./logs:/app/logs
    working_dir: /app
    command: ["php", "bin/blackcnote-debug-daemon.php"]
    restart: always

  blackcnote_debug_exporter:
    image: php:8.1-cli
    container_name: blackcnote_debug_exporter
    volumes:
      - .:/app
      - ./logs:/app/logs
    working_dir: /app
    command: ["php", "bin/blackcnote-metrics-exporter.php", "--serve", "9091"]
    ports:
      - "9091:9091"
    restart: always
    depends_on:
      - blackcnote_debug
```

### Docker Status Monitoring

The debug daemon automatically detects and monitors Docker containers:

- **Container Discovery**: Scans for running BlackCnote containers
- **Status Tracking**: Monitors container health and status
- **Error Detection**: Logs container failures and issues
- **Metrics Export**: Provides container count to Prometheus

## Log File Structure

### Debug Log Format

Logs are stored in JSON format for easy parsing and analysis:

```json
{
  "timestamp": "2025-06-26 18:30:38",
  "level": "SYSTEM",
  "message": "BlackCnote Debug System (standalone) initialized",
  "context": {
    "base_path": "/app",
    "files_monitored": 1250,
    "memory_usage": 5242880,
    "uptime": 3600
  }
}
```

### Log Levels

- **SYSTEM**: System initialization and configuration
- **ERROR**: Errors and exceptions
- **WARNING**: Warning conditions
- **INFO**: General information
- **DEBUG**: Detailed debugging information

## Configuration

### Debug Daemon Configuration

```php
$config = [
    'base_path' => dirname(__DIR__),
    'log_file' => dirname(__DIR__) . '/logs/blackcnote-debug.log',
    'debug_enabled' => true,
    'log_level' => 'ALL',
];
```

### Monitoring Intervals

- **File Changes**: Checked every 30 seconds
- **System Status**: Checked every 60 seconds
- **Docker Status**: Checked every 120 seconds
- **Log Analysis**: Checked every 30 seconds
- **Heartbeat**: Logged every 60 seconds

## Alerting

### AlertManager Rules

Alert rules are defined in `monitoring/blackcnote-rules.yml`:

```yaml
groups:
  - name: blackcnote-debug
    rules:
      - alert: HighErrorRate
        expr: rate(blackcnote_debug_errors_total[5m]) > 0.1
        for: 2m
        labels:
          severity: warning
        annotations:
          summary: "High error rate detected"
          description: "Error rate is {{ $value }} errors per second"

      - alert: DebugDaemonDown
        expr: blackcnote_daemon_uptime_seconds == 0
        for: 1m
        labels:
          severity: critical
        annotations:
          summary: "Debug daemon is down"
          description: "The BlackCnote debug daemon has stopped running"
```

## Troubleshooting

### Common Issues

1. **Docker Not Available**
   - The debug daemon will log warnings when Docker is not accessible
   - This is normal when Docker Desktop is not running
   - Other monitoring continues to work

2. **High Memory Usage**
   - The daemon automatically logs warnings when memory usage exceeds 100MB
   - Consider adjusting the monitoring intervals if needed

3. **File Permission Issues**
   - Ensure the logs directory is writable
   - Check file permissions on the project directory

### Debug Commands

```bash
# Check debug daemon status
ps aux | grep blackcnote-debug-daemon

# View recent logs
tail -f logs/blackcnote-debug.log

# Test metrics exporter
curl http://localhost:9091/metrics

# Check log file size
ls -lh logs/blackcnote-debug.log
```

## Performance Considerations

### Resource Usage

- **Memory**: Typically 10-50MB depending on file count
- **CPU**: Low impact, mostly idle between checks
- **Disk I/O**: Minimal, only when writing logs
- **Network**: Only when Docker integration is active

### Optimization

- **File Monitoring**: Only monitors relevant file extensions
- **Log Rotation**: Implemented to prevent log file growth
- **Buffered Writing**: Efficient log writing with file locking
- **Conditional Checks**: Only performs expensive operations when needed

## Security

### Data Protection

- **No Sensitive Data**: Passwords and tokens are not logged
- **Path Sanitization**: File paths are validated and sanitized
- **Error Boundaries**: Exceptions are caught and logged safely
- **Access Control**: Log files should have appropriate permissions

### Network Security

- **Metrics Exporter**: Only exposes metrics endpoint
- **No Admin Interface**: No web-based configuration interface
- **Local Access**: Designed for local monitoring only

## Future Enhancements

### Planned Features

1. **WebSocket Integration**: Real-time log streaming
2. **Custom Dashboards**: Additional Grafana dashboard templates
3. **Log Aggregation**: Integration with ELK stack
4. **Performance Profiling**: Detailed performance metrics
5. **Plugin System**: Extensible monitoring modules

### Integration Opportunities

- **WordPress Admin**: Integration with WordPress admin interface
- **CI/CD Pipelines**: Deployment monitoring
- **External Monitoring**: Integration with external monitoring services
- **Backup Monitoring**: Database and file backup tracking

## Conclusion

The BlackCnote Enhanced Debug System provides comprehensive, 24/7 monitoring of the entire project ecosystem. It operates independently of WordPress and integrates seamlessly with modern monitoring tools like Prometheus and Grafana. The system is designed to be lightweight, secure, and extensible, providing valuable insights into system health and performance. 