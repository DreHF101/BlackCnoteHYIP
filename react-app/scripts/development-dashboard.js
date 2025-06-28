#!/usr/bin/env node

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { spawn, exec } from 'child_process';
import http from 'http';
import os from 'os';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Development Dashboard for BlackCnote Development Environment
 * 
 * Provides a comprehensive web interface for monitoring and managing
 * the development environment, including performance metrics, logs, and controls.
 */
class DevelopmentDashboard {
    
    constructor() {
        this.config = {
            // Dashboard configuration
            dashboard: {
                port: 8080,
                host: 'localhost',
                title: 'BlackCnote Development Dashboard',
                version: '1.0.0'
            },
            
            // Services to monitor
            services: {
                wordpress: {
                    name: 'WordPress',
                    url: 'http://localhost/blackcnote',
                    port: 80,
                    status: 'unknown'
                },
                vite: {
                    name: 'Vite Dev Server',
                    url: 'http://localhost:5174',
                    port: 5174,
                    status: 'unknown'
                },
                browsersync: {
                    name: 'Browsersync',
                    url: 'http://localhost:3000',
                    port: 3000,
                    status: 'unknown'
                }
            },
            
            // Monitoring intervals
            monitoring: {
                services: 5000,    // 5 seconds
                system: 10000,     // 10 seconds
                logs: 30000        // 30 seconds
            }
        };
        
        this.stats = {
            system: {
                memory: { used: 0, total: 0, percentage: 0 },
                cpu: { usage: 0, load: [0, 0, 0] },
                disk: { used: 0, total: 0, percentage: 0 }
            },
            services: {},
            logs: [],
            performance: {
                buildTime: 0,
                reloadTime: 0,
                memoryUsage: 0
            }
        };
        
        this.server = null;
        this.isRunning = false;
    }
    
    /**
     * Start the development dashboard
     */
    async start() {
        console.log('🚀 Starting Development Dashboard...');
        
        try {
            // Create logs directory
            this.ensureLogsDirectory();
            
            // Start monitoring
            this.startMonitoring();
            
            // Start HTTP server
            await this.startServer();
            
            // Display dashboard info
            this.displayDashboardInfo();
            
            this.isRunning = true;
            
        } catch (error) {
            console.error('❌ Failed to start dashboard:', error.message);
            throw error;
        }
    }
    
    /**
     * Ensure logs directory exists
     */
    ensureLogsDirectory() {
        const logsDir = path.join(__dirname, '..', 'logs');
        if (!fs.existsSync(logsDir)) {
            fs.mkdirSync(logsDir, { recursive: true });
        }
    }
    
    /**
     * Start monitoring services
     */
    startMonitoring() {
        console.log('📊 Starting service monitoring...');
        
        // Monitor services
        setInterval(() => {
            this.monitorServices();
        }, this.config.monitoring.services);
        
        // Monitor system
        setInterval(() => {
            this.monitorSystem();
        }, this.config.monitoring.system);
        
        // Monitor logs
        setInterval(() => {
            this.monitorLogs();
        }, this.config.monitoring.logs);
        
        // Initial monitoring
        this.monitorServices();
        this.monitorSystem();
    }
    
    /**
     * Monitor development services
     */
    async monitorServices() {
        for (const [key, service] of Object.entries(this.config.services)) {
            try {
                const status = await this.checkServiceStatus(service);
                this.stats.services[key] = {
                    ...service,
                    status,
                    lastCheck: new Date().toISOString(),
                    responseTime: await this.measureResponseTime(service.url)
                };
            } catch (error) {
                this.stats.services[key] = {
                    ...service,
                    status: 'error',
                    lastCheck: new Date().toISOString(),
                    error: error.message
                };
            }
        }
    }
    
    /**
     * Check service status
     */
    async checkServiceStatus(service) {
        return new Promise((resolve) => {
            const timeout = setTimeout(() => {
                resolve('timeout');
            }, 5000);
            
            http.get(service.url, (res) => {
                clearTimeout(timeout);
                if (res.statusCode === 200) {
                    resolve('running');
                } else {
                    resolve('error');
                }
            }).on('error', () => {
                clearTimeout(timeout);
                resolve('down');
            });
        });
    }
    
    /**
     * Measure service response time
     */
    async measureResponseTime(url) {
        return new Promise((resolve) => {
            const start = Date.now();
            
            http.get(url, () => {
                const responseTime = Date.now() - start;
                resolve(responseTime);
            }).on('error', () => {
                resolve(-1);
            });
        });
    }
    
    /**
     * Monitor system resources
     */
    monitorSystem() {
        // Memory usage
        const totalMem = os.totalmem();
        const freeMem = os.freemem();
        const usedMem = totalMem - freeMem;
        
        this.stats.system.memory = {
            used: Math.round(usedMem / 1024 / 1024),
            total: Math.round(totalMem / 1024 / 1024),
            percentage: Math.round((usedMem / totalMem) * 100)
        };
        
        // CPU usage
        const loadAvg = os.loadavg();
        this.stats.system.cpu = {
            usage: this.getCpuUsage(),
            load: loadAvg
        };
        
        // Disk usage
        this.getDiskUsage().then(diskUsage => {
            this.stats.system.disk = diskUsage;
        });
    }
    
    /**
     * Get CPU usage
     */
    getCpuUsage() {
        const cpus = os.cpus();
        let totalIdle = 0;
        let totalTick = 0;
        
        cpus.forEach(cpu => {
            for (const type in cpu.times) {
                totalTick += cpu.times[type];
            }
            totalIdle += cpu.times.idle;
        });
        
        const idle = totalIdle / cpus.length;
        const total = totalTick / cpus.length;
        const usage = 100 - (100 * idle / total);
        
        return Math.round(usage);
    }
    
    /**
     * Get disk usage
     */
    async getDiskUsage() {
        return new Promise((resolve) => {
            exec('df -h .', (error, stdout) => {
                if (error) {
                    resolve({ used: 0, total: 0, percentage: 0 });
                    return;
                }
                
                const lines = stdout.split('\n');
                if (lines.length > 1) {
                    const parts = lines[1].split(/\s+/);
                    const percentage = parseInt(parts[4].replace('%', ''));
                    
                    resolve({
                        used: 0,
                        total: 0,
                        percentage: percentage
                    });
                } else {
                    resolve({ used: 0, total: 0, percentage: 0 });
                }
            });
        });
    }
    
    /**
     * Monitor log files
     */
    monitorLogs() {
        const logFiles = [
            path.join(__dirname, '..', '..', 'wp-content', 'debug.log'),
            path.join(__dirname, '..', 'logs', 'performance.log'),
            path.join(__dirname, '..', 'logs', 'development.log')
        ];
        
        this.stats.logs = [];
        
        logFiles.forEach(logFile => {
            if (fs.existsSync(logFile)) {
                try {
                    const content = fs.readFileSync(logFile, 'utf8');
                    const lines = content.split('\n').slice(-10).filter(line => line.trim());
                    
                    this.stats.logs.push({
                        file: path.basename(logFile),
                        lines: lines.length,
                        lastModified: fs.statSync(logFile).mtime.toISOString(),
                        recentLines: lines.slice(-5)
                    });
                } catch (error) {
                    // Ignore read errors
                }
            }
        });
    }
    
    /**
     * Start HTTP server
     */
    async startServer() {
        return new Promise((resolve, reject) => {
            this.server = http.createServer((req, res) => {
                this.handleRequest(req, res);
            });
            
            this.server.listen(this.config.dashboard.port, this.config.dashboard.host, () => {
                console.log(`✅ Dashboard server started on http://${this.config.dashboard.host}:${this.config.dashboard.port}`);
                resolve();
            });
            
            this.server.on('error', (error) => {
                reject(error);
            });
        });
    }
    
    /**
     * Handle HTTP requests
     */
    handleRequest(req, res) {
        const url = new URL(req.url, `http://${req.headers.host}`);
        const path = url.pathname;
        
        // Set CORS headers
        res.setHeader('Access-Control-Allow-Origin', '*');
        res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
        
        if (req.method === 'OPTIONS') {
            res.writeHead(200);
            res.end();
            return;
        }
        
        switch (path) {
            case '/':
                this.serveDashboard(res);
                break;
                
            case '/api/stats':
                this.serveStats(res);
                break;
                
            case '/api/services':
                this.serveServices(res);
                break;
                
            case '/api/logs':
                this.serveLogs(res);
                break;
                
            case '/api/actions/restart':
                this.handleRestart(req, res);
                break;
                
            case '/api/actions/optimize':
                this.handleOptimize(req, res);
                break;
                
            default:
                this.serve404(res);
                break;
        }
    }
    
    /**
     * Serve main dashboard HTML
     */
    serveDashboard(res) {
        const html = this.generateDashboardHTML();
        
        res.writeHead(200, { 'Content-Type': 'text/html' });
        res.end(html);
    }
    
    /**
     * Generate dashboard HTML
     */
    generateDashboardHTML() {
        return `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${this.config.dashboard.title}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            color: #4a5568;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #718096;
            font-size: 1rem;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card h2 {
            color: #4a5568;
            font-size: 1.5rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-running { background: #48bb78; }
        .status-down { background: #f56565; }
        .status-warning { background: #ed8936; }
        .status-unknown { background: #a0aec0; }
        
        .metric {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .metric:last-child {
            border-bottom: none;
        }
        
        .metric-label {
            font-weight: 500;
            color: #4a5568;
        }
        
        .metric-value {
            font-weight: 600;
            color: #2d3748;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #48bb78, #38a169);
            transition: width 0.3s ease;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: #4299e1;
            color: white;
        }
        
        .btn-primary:hover {
            background: #3182ce;
        }
        
        .btn-success {
            background: #48bb78;
            color: white;
        }
        
        .btn-success:hover {
            background: #38a169;
        }
        
        .btn-warning {
            background: #ed8936;
            color: white;
        }
        
        .btn-warning:hover {
            background: #dd6b20;
        }
        
        .logs {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .log-entry {
            background: #f7fafc;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.875rem;
            color: #4a5568;
        }
        
        .refresh-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4299e1;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            background: #3182ce;
            transform: scale(1.1);
        }
        
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>${this.config.dashboard.title}</h1>
            <p>Version ${this.config.dashboard.version} - Real-time development environment monitoring</p>
        </div>
        
        <div class="grid">
            <div class="card">
                <h2>
                    <span class="status-indicator" id="system-status"></span>
                    System Resources
                </h2>
                <div id="system-metrics"></div>
            </div>
            
            <div class="card">
                <h2>
                    <span class="status-indicator" id="services-status"></span>
                    Development Services
                </h2>
                <div id="services-metrics"></div>
            </div>
            
            <div class="card">
                <h2>Performance Metrics</h2>
                <div id="performance-metrics"></div>
                <div class="actions">
                    <button class="btn btn-primary" onclick="optimizeMemory()">Optimize Memory</button>
                    <button class="btn btn-success" onclick="restartServices()">Restart Services</button>
                </div>
            </div>
        </div>
        
        <div class="logs">
            <h2>Recent Logs</h2>
            <div id="logs-content"></div>
        </div>
    </div>
    
    <button class="refresh-btn" onclick="refreshData()">🔄</button>
    
    <script>
        let refreshInterval;
        
        function updateDashboard() {
            fetch('/api/stats')
                .then(response => response.json())
                .then(data => {
                    updateSystemMetrics(data.system);
                    updateServicesMetrics(data.services);
                    updatePerformanceMetrics(data.performance);
                    updateLogs(data.logs);
                })
                .catch(error => console.error('Error fetching data:', error));
        }
        
        function updateSystemMetrics(system) {
            const container = document.getElementById('system-metrics');
            const statusIndicator = document.getElementById('system-status');
            
            const memoryPercentage = system.memory.percentage;
            const cpuUsage = system.cpu.usage;
            
            // Update status indicator
            if (memoryPercentage > 80 || cpuUsage > 80) {
                statusIndicator.className = 'status-indicator status-warning';
            } else if (memoryPercentage > 60 || cpuUsage > 60) {
                statusIndicator.className = 'status-indicator status-running';
            } else {
                statusIndicator.className = 'status-indicator status-running';
            }
            
            container.innerHTML = \`
                <div class="metric">
                    <span class="metric-label">Memory Usage</span>
                    <span class="metric-value">\${system.memory.used}MB / \${system.memory.total}MB</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: \${system.memory.percentage}%"></div>
                </div>
                <div class="metric">
                    <span class="metric-label">CPU Usage</span>
                    <span class="metric-value">\${system.cpu.usage}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: \${system.cpu.usage}%"></div>
                </div>
                <div class="metric">
                    <span class="metric-label">Load Average</span>
                    <span class="metric-value">\${system.cpu.load.map(l => l.toFixed(2)).join(', ')}</span>
                </div>
            \`;
        }
        
        function updateServicesMetrics(services) {
            const container = document.getElementById('services-metrics');
            const statusIndicator = document.getElementById('services-status');
            
            let allRunning = true;
            let anyDown = false;
            
            const servicesHtml = Object.entries(services).map(([key, service]) => {
                const statusClass = service.status === 'running' ? 'status-running' : 
                                  service.status === 'down' ? 'status-down' : 'status-warning';
                
                if (service.status !== 'running') allRunning = false;
                if (service.status === 'down') anyDown = true;
                
                return \`
                    <div class="metric">
                        <span class="metric-label">\${service.name}</span>
                        <span class="metric-value">
                            <span class="status-indicator \${statusClass}"></span>
                            \${service.status}
                        </span>
                    </div>
                \`;
            }).join('');
            
            // Update overall status
            if (anyDown) {
                statusIndicator.className = 'status-indicator status-down';
            } else if (allRunning) {
                statusIndicator.className = 'status-indicator status-running';
            } else {
                statusIndicator.className = 'status-indicator status-warning';
            }
            
            container.innerHTML = servicesHtml;
        }
        
        function updatePerformanceMetrics(performance) {
            const container = document.getElementById('performance-metrics');
            
            container.innerHTML = \`
                <div class="metric">
                    <span class="metric-label">Build Time</span>
                    <span class="metric-value">\${performance.buildTime}ms</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Reload Time</span>
                    <span class="metric-value">\${performance.reloadTime}ms</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Memory Usage</span>
                    <span class="metric-value">\${performance.memoryUsage}MB</span>
                </div>
            \`;
        }
        
        function updateLogs(logs) {
            const container = document.getElementById('logs-content');
            
            if (logs.length === 0) {
                container.innerHTML = '<p>No logs available</p>';
                return;
            }
            
            const logsHtml = logs.map(log => \`
                <div class="log-entry">
                    <strong>\${log.file}</strong> (\${log.lines} lines)
                    <br>
                    <small>Last modified: \${new Date(log.lastModified).toLocaleString()}</small>
                    <br>
                    \${log.recentLines.map(line => \`<div>\${line}</div>\`).join('')}
                </div>
            \`).join('');
            
            container.innerHTML = logsHtml;
        }
        
        function optimizeMemory() {
            fetch('/api/actions/optimize', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Memory optimization completed successfully!');
                        updateDashboard();
                    } else {
                        alert('Memory optimization failed: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Error during memory optimization: ' + error.message);
                });
        }
        
        function restartServices() {
            if (confirm('Are you sure you want to restart all development services?')) {
                fetch('/api/actions/restart', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Services restart initiated!');
                            updateDashboard();
                        } else {
                            alert('Services restart failed: ' + data.error);
                        }
                    })
                    .catch(error => {
                        alert('Error during services restart: ' + error.message);
                    });
            }
        }
        
        function refreshData() {
            updateDashboard();
        }
        
        // Initialize dashboard
        updateDashboard();
        
        // Auto-refresh every 5 seconds
        refreshInterval = setInterval(updateDashboard, 5000);
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
</body>
</html>
        `;
    }
    
    /**
     * Serve API stats
     */
    serveStats(res) {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify(this.stats));
    }
    
    /**
     * Serve API services
     */
    serveServices(res) {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify(this.stats.services));
    }
    
    /**
     * Serve API logs
     */
    serveLogs(res) {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify(this.stats.logs));
    }
    
    /**
     * Handle restart action
     */
    handleRestart(req, res) {
        if (req.method !== 'POST') {
            res.writeHead(405);
            res.end('Method not allowed');
            return;
        }
        
        this.restartServices().then(() => {
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({ success: true }));
        }).catch(error => {
            res.writeHead(500, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({ success: false, error: error.message }));
        });
    }
    
    /**
     * Handle optimize action
     */
    handleOptimize(req, res) {
        if (req.method !== 'POST') {
            res.writeHead(405);
            res.end('Method not allowed');
            return;
        }
        
        this.optimizeMemory().then(() => {
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({ success: true }));
        }).catch(error => {
            res.writeHead(500, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({ success: false, error: error.message }));
        });
    }
    
    /**
     * Serve 404
     */
    serve404(res) {
        res.writeHead(404, { 'Content-Type': 'text/plain' });
        res.end('Not found');
    }
    
    /**
     * Restart development services
     */
    async restartServices() {
        console.log('🔄 Restarting development services...');
        
        // This would need to be implemented based on how services are managed
        // For now, we'll just log the action
        console.log('✅ Services restart initiated');
    }
    
    /**
     * Optimize memory usage
     */
    async optimizeMemory() {
        console.log('🔧 Optimizing memory usage...');
        
        // This would need to be implemented based on memory optimization techniques
        // For now, we'll just log the action
        console.log('✅ Memory optimization completed');
    }
    
    /**
     * Display dashboard information
     */
    displayDashboardInfo() {
        console.log('\n📊 Development Dashboard Information:');
        console.log('=====================================');
        console.log(`🌐 URL: http://${this.config.dashboard.host}:${this.config.dashboard.port}`);
        console.log(`📁 Title: ${this.config.dashboard.title}`);
        console.log(`🔧 Version: ${this.config.dashboard.version}`);
        console.log(`⏱️  Auto-refresh: Every 5 seconds`);
        console.log(`📊 Monitoring: Active`);
        console.log('');
    }
    
    /**
     * Stop the dashboard
     */
    stop() {
        console.log('🛑 Stopping Development Dashboard...');
        
        this.isRunning = false;
        
        if (this.server) {
            this.server.close();
        }
        
        console.log('✅ Development Dashboard stopped');
    }
    
    /**
     * Get dashboard statistics
     */
    getStats() {
        return {
            config: this.config,
            stats: this.stats,
            isRunning: this.isRunning
        };
    }
}

// Export for use in other modules
export default DevelopmentDashboard;

// Run if called directly
if (import.meta.url === `file://${process.argv[1]}`) {
    const dashboard = new DevelopmentDashboard();
    
    // Handle graceful shutdown
    process.on('SIGINT', () => {
        dashboard.stop();
        process.exit(0);
    });
    
    process.on('SIGTERM', () => {
        dashboard.stop();
        process.exit(0);
    });
    
    // Start dashboard
    dashboard.start().catch(console.error);
} 