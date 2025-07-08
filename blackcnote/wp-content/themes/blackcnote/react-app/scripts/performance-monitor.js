#!/usr/bin/env node

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { spawn, exec } from 'child_process';
import os from 'os';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Performance Monitor for BlackCnote Development Environment
 * 
 * Tracks and reports on various performance metrics including
 * build times, memory usage, response times, and system resources.
 */
class PerformanceMonitor {
    
    constructor() {
        this.config = {
            // Performance thresholds
            thresholds: {
                buildTime: {
                    warning: 30000,  // 30 seconds
                    critical: 60000  // 60 seconds
                },
                memoryUsage: {
                    warning: 300,    // 300MB
                    critical: 500    // 500MB
                },
                responseTime: {
                    warning: 2000,   // 2 seconds
                    critical: 5000   // 5 seconds
                },
                cpuUsage: {
                    warning: 70,     // 70%
                    critical: 90     // 90%
                }
            },
            
            // Monitoring intervals
            monitoring: {
                metrics: 5000,       // 5 seconds
                reporting: 60000,    // 1 minute
                alerting: 10000      // 10 seconds
            },
            
            // Performance targets
            targets: {
                buildTime: 15000,    // 15 seconds
                memoryUsage: 200,    // 200MB
                responseTime: 1000,  // 1 second
                cpuUsage: 50         // 50%
            }
        };
        
        this.metrics = {
            build: {
                times: [],
                average: 0,
                peak: 0,
                lastBuild: null
            },
            memory: {
                usage: [],
                average: 0,
                peak: 0,
                current: 0
            },
            response: {
                times: [],
                average: 0,
                peak: 0,
                lastCheck: null
            },
            system: {
                cpu: [],
                load: [],
                disk: [],
                network: []
            }
        };
        
        this.alerts = [];
        this.isMonitoring = false;
        this.monitoringInterval = null;
        this.reportingInterval = null;
        this.alertingInterval = null;
    }
    
    /**
     * Start performance monitoring
     */
    start() {
        console.log('📊 Starting Performance Monitor...');
        
        this.isMonitoring = true;
        
        // Start monitoring intervals
        this.monitoringInterval = setInterval(() => {
            this.collectMetrics();
        }, this.config.monitoring.metrics);
        
        this.reportingInterval = setInterval(() => {
            this.generateReport();
        }, this.config.monitoring.reporting);
        
        this.alertingInterval = setInterval(() => {
            this.checkAlerts();
        }, this.config.monitoring.alerting);
        
        // Initial metrics collection
        this.collectMetrics();
        
        console.log('✅ Performance monitoring started');
    }
    
    /**
     * Stop performance monitoring
     */
    stop() {
        console.log('🛑 Stopping Performance Monitor...');
        
        this.isMonitoring = false;
        
        if (this.monitoringInterval) {
            clearInterval(this.monitoringInterval);
        }
        
        if (this.reportingInterval) {
            clearInterval(this.reportingInterval);
        }
        
        if (this.alertingInterval) {
            clearInterval(this.alertingInterval);
        }
        
        console.log('✅ Performance monitoring stopped');
    }
    
    /**
     * Collect performance metrics
     */
    async collectMetrics() {
        // Collect system metrics
        await this.collectSystemMetrics();
        
        // Collect memory metrics
        this.collectMemoryMetrics();
        
        // Collect response time metrics
        await this.collectResponseMetrics();
        
        // Update averages and peaks
        this.updateMetrics();
    }
    
    /**
     * Collect system metrics
     */
    async collectSystemMetrics() {
        // CPU usage
        const cpuUsage = this.getCpuUsage();
        this.metrics.system.cpu.push(cpuUsage);
        
        // Load average
        const loadAvg = os.loadavg();
        this.metrics.system.load.push(loadAvg);
        
        // Disk usage
        const diskUsage = await this.getDiskUsage();
        this.metrics.system.disk.push(diskUsage);
        
        // Network usage (simplified)
        this.metrics.system.network.push({
            timestamp: Date.now(),
            bytesIn: 0,
            bytesOut: 0
        });
        
        // Keep only last 100 samples
        this.metrics.system.cpu = this.metrics.system.cpu.slice(-100);
        this.metrics.system.load = this.metrics.system.load.slice(-100);
        this.metrics.system.disk = this.metrics.system.disk.slice(-100);
        this.metrics.system.network = this.metrics.system.network.slice(-100);
    }
    
    /**
     * Collect memory metrics
     */
    collectMemoryMetrics() {
        const memUsage = process.memoryUsage();
        const memoryMB = Math.round(memUsage.heapUsed / 1024 / 1024);
        
        this.metrics.memory.usage.push({
            timestamp: Date.now(),
            heapUsed: memoryMB,
            heapTotal: Math.round(memUsage.heapTotal / 1024 / 1024),
            external: Math.round(memUsage.external / 1024 / 1024),
            rss: Math.round(memUsage.rss / 1024 / 1024)
        });
        
        this.metrics.memory.current = memoryMB;
        
        // Keep only last 100 samples
        this.metrics.memory.usage = this.metrics.memory.usage.slice(-100);
    }
    
    /**
     * Collect response time metrics
     */
    async collectResponseMetrics() {
        const services = [
            { name: 'Vite Dev Server', url: 'http://localhost:5174' },
            { name: 'WordPress', url: 'http://localhost' }
        ];
        
        for (const service of services) {
            try {
                const responseTime = await this.measureResponseTime(service.url);
                
                this.metrics.response.times.push({
                    timestamp: Date.now(),
                    service: service.name,
                    responseTime
                });
                
                this.metrics.response.lastCheck = Date.now();
                
            } catch (error) {
                this.metrics.response.times.push({
                    timestamp: Date.now(),
                    service: service.name,
                    responseTime: -1,
                    error: error.message
                });
            }
        }
        
        // Keep only last 100 samples
        this.metrics.response.times = this.metrics.response.times.slice(-100);
    }
    
    /**
     * Measure response time for a service
     */
    async measureResponseTime(url) {
        return new Promise((resolve) => {
            const start = Date.now();
            
            const http = require('http');
            const https = require('https');
            
            const client = url.startsWith('https') ? https : http;
            
            const req = client.get(url, () => {
                const responseTime = Date.now() - start;
                resolve(responseTime);
            });
            
            req.on('error', () => {
                resolve(-1);
            });
            
            req.setTimeout(5000, () => {
                req.destroy();
                resolve(-1);
            });
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
     * Update metrics averages and peaks
     */
    updateMetrics() {
        // Update memory metrics
        if (this.metrics.memory.usage.length > 0) {
            const memoryValues = this.metrics.memory.usage.map(m => m.heapUsed);
            this.metrics.memory.average = Math.round(memoryValues.reduce((a, b) => a + b, 0) / memoryValues.length);
            this.metrics.memory.peak = Math.max(...memoryValues);
        }
        
        // Update response metrics
        if (this.metrics.response.times.length > 0) {
            const responseValues = this.metrics.response.times
                .filter(r => r.responseTime > 0)
                .map(r => r.responseTime);
            
            if (responseValues.length > 0) {
                this.metrics.response.average = Math.round(responseValues.reduce((a, b) => a + b, 0) / responseValues.length);
                this.metrics.response.peak = Math.max(...responseValues);
            }
        }
        
        // Update build metrics
        if (this.metrics.build.times.length > 0) {
            this.metrics.build.average = Math.round(this.metrics.build.times.reduce((a, b) => a + b, 0) / this.metrics.build.times.length);
            this.metrics.build.peak = Math.max(...this.metrics.build.times);
        }
    }
    
    /**
     * Record build time
     */
    recordBuildTime(buildTime) {
        this.metrics.build.times.push(buildTime);
        this.metrics.build.lastBuild = Date.now();
        
        // Keep only last 50 builds
        this.metrics.build.times = this.metrics.build.times.slice(-50);
        
        // Check for performance issues
        this.checkBuildPerformance(buildTime);
    }
    
    /**
     * Check build performance
     */
    checkBuildPerformance(buildTime) {
        if (buildTime > this.config.thresholds.buildTime.critical) {
            this.addAlert('CRITICAL', 'Build Performance', `Build took ${buildTime}ms (threshold: ${this.config.thresholds.buildTime.critical}ms)`);
        } else if (buildTime > this.config.thresholds.buildTime.warning) {
            this.addAlert('WARNING', 'Build Performance', `Build took ${buildTime}ms (threshold: ${this.config.thresholds.buildTime.warning}ms)`);
        }
    }
    
    /**
     * Check for performance alerts
     */
    checkAlerts() {
        // Check memory usage
        if (this.metrics.memory.current > this.config.thresholds.memoryUsage.critical) {
            this.addAlert('CRITICAL', 'Memory Usage', `Memory usage at ${this.metrics.memory.current}MB (threshold: ${this.config.thresholds.memoryUsage.critical}MB)`);
        } else if (this.metrics.memory.current > this.config.thresholds.memoryUsage.warning) {
            this.addAlert('WARNING', 'Memory Usage', `Memory usage at ${this.metrics.memory.current}MB (threshold: ${this.config.thresholds.memoryUsage.warning}MB)`);
        }
        
        // Check CPU usage
        if (this.metrics.system.cpu.length > 0) {
            const currentCpu = this.metrics.system.cpu[this.metrics.system.cpu.length - 1];
            
            if (currentCpu > this.config.thresholds.cpuUsage.critical) {
                this.addAlert('CRITICAL', 'CPU Usage', `CPU usage at ${currentCpu}% (threshold: ${this.config.thresholds.cpuUsage.critical}%)`);
            } else if (currentCpu > this.config.thresholds.cpuUsage.warning) {
                this.addAlert('WARNING', 'CPU Usage', `CPU usage at ${currentCpu}% (threshold: ${this.config.thresholds.cpuUsage.warning}%)`);
            }
        }
        
        // Check response times
        if (this.metrics.response.times.length > 0) {
            const recentResponses = this.metrics.response.times.slice(-5);
            const avgResponse = recentResponses.reduce((sum, r) => sum + (r.responseTime > 0 ? r.responseTime : 0), 0) / recentResponses.length;
            
            if (avgResponse > this.config.thresholds.responseTime.critical) {
                this.addAlert('CRITICAL', 'Response Time', `Average response time ${avgResponse}ms (threshold: ${this.config.thresholds.responseTime.critical}ms)`);
            } else if (avgResponse > this.config.thresholds.responseTime.warning) {
                this.addAlert('WARNING', 'Response Time', `Average response time ${avgResponse}ms (threshold: ${this.config.thresholds.responseTime.warning}ms)`);
            }
        }
    }
    
    /**
     * Add performance alert
     */
    addAlert(level, category, message) {
        const alert = {
            id: Date.now(),
            level,
            category,
            message,
            timestamp: new Date().toISOString(),
            metrics: {
                memory: this.metrics.memory.current,
                cpu: this.metrics.system.cpu.length > 0 ? this.metrics.system.cpu[this.metrics.system.cpu.length - 1] : 0,
                responseTime: this.metrics.response.average
            }
        };
        
        this.alerts.push(alert);
        
        // Keep only last 100 alerts
        this.alerts = this.alerts.slice(-100);
        
        // Log alert
        const emoji = level === 'CRITICAL' ? '🚨' : '⚠️';
        console.log(`${emoji} ${level} ${category}: ${message}`);
    }
    
    /**
     * Generate performance report
     */
    generateReport() {
        const report = {
            timestamp: new Date().toISOString(),
            summary: this.generateSummary(),
            metrics: this.metrics,
            alerts: this.alerts.slice(-10), // Last 10 alerts
            recommendations: this.generateRecommendations()
        };
        
        // Save report
        const reportFile = path.join(__dirname, '..', 'logs', `performance-report-${Date.now()}.json`);
        fs.writeFileSync(reportFile, JSON.stringify(report, null, 2));
        
        // Display summary
        this.displaySummary();
        
        return report;
    }
    
    /**
     * Generate performance summary
     */
    generateSummary() {
        const summary = {
            build: {
                average: this.metrics.build.average,
                peak: this.metrics.build.peak,
                lastBuild: this.metrics.build.lastBuild,
                status: this.getBuildStatus()
            },
            memory: {
                current: this.metrics.memory.current,
                average: this.metrics.memory.average,
                peak: this.metrics.memory.peak,
                status: this.getMemoryStatus()
            },
            response: {
                average: this.metrics.response.average,
                peak: this.metrics.response.peak,
                lastCheck: this.metrics.response.lastCheck,
                status: this.getResponseStatus()
            },
            system: {
                cpu: this.metrics.system.cpu.length > 0 ? this.metrics.system.cpu[this.metrics.system.cpu.length - 1] : 0,
                load: this.metrics.system.load.length > 0 ? this.metrics.system.load[this.metrics.system.load.length - 1] : [0, 0, 0],
                status: this.getSystemStatus()
            }
        };
        
        return summary;
    }
    
    /**
     * Get build performance status
     */
    getBuildStatus() {
        if (this.metrics.build.average > this.config.thresholds.buildTime.critical) {
            return 'CRITICAL';
        } else if (this.metrics.build.average > this.config.thresholds.buildTime.warning) {
            return 'WARNING';
        } else if (this.metrics.build.average <= this.config.targets.buildTime) {
            return 'EXCELLENT';
        } else {
            return 'GOOD';
        }
    }
    
    /**
     * Get memory usage status
     */
    getMemoryStatus() {
        if (this.metrics.memory.current > this.config.thresholds.memoryUsage.critical) {
            return 'CRITICAL';
        } else if (this.metrics.memory.current > this.config.thresholds.memoryUsage.warning) {
            return 'WARNING';
        } else if (this.metrics.memory.current <= this.config.targets.memoryUsage) {
            return 'EXCELLENT';
        } else {
            return 'GOOD';
        }
    }
    
    /**
     * Get response time status
     */
    getResponseStatus() {
        if (this.metrics.response.average > this.config.thresholds.responseTime.critical) {
            return 'CRITICAL';
        } else if (this.metrics.response.average > this.config.thresholds.responseTime.warning) {
            return 'WARNING';
        } else if (this.metrics.response.average <= this.config.targets.responseTime) {
            return 'EXCELLENT';
        } else {
            return 'GOOD';
        }
    }
    
    /**
     * Get system status
     */
    getSystemStatus() {
        const currentCpu = this.metrics.system.cpu.length > 0 ? this.metrics.system.cpu[this.metrics.system.cpu.length - 1] : 0;
        
        if (currentCpu > this.config.thresholds.cpuUsage.critical) {
            return 'CRITICAL';
        } else if (currentCpu > this.config.thresholds.cpuUsage.warning) {
            return 'WARNING';
        } else if (currentCpu <= this.config.targets.cpuUsage) {
            return 'EXCELLENT';
        } else {
            return 'GOOD';
        }
    }
    
    /**
     * Generate performance recommendations
     */
    generateRecommendations() {
        const recommendations = [];
        
        // Build performance recommendations
        if (this.metrics.build.average > this.config.targets.buildTime) {
            recommendations.push({
                priority: 'MEDIUM',
                category: 'Build Performance',
                issue: 'Build times are above target',
                action: 'Consider enabling incremental builds and build caching',
                impact: 'Development productivity'
            });
        }
        
        // Memory usage recommendations
        if (this.metrics.memory.average > this.config.targets.memoryUsage) {
            recommendations.push({
                priority: 'HIGH',
                category: 'Memory Usage',
                issue: 'Memory usage is above target',
                action: 'Enable memory optimization and garbage collection',
                impact: 'System stability'
            });
        }
        
        // Response time recommendations
        if (this.metrics.response.average > this.config.targets.responseTime) {
            recommendations.push({
                priority: 'MEDIUM',
                category: 'Response Time',
                issue: 'Response times are above target',
                action: 'Check service health and optimize API calls',
                impact: 'User experience'
            });
        }
        
        // CPU usage recommendations
        const currentCpu = this.metrics.system.cpu.length > 0 ? this.metrics.system.cpu[this.metrics.system.cpu.length - 1] : 0;
        if (currentCpu > this.config.targets.cpuUsage) {
            recommendations.push({
                priority: 'LOW',
                category: 'CPU Usage',
                issue: 'CPU usage is above target',
                action: 'Monitor for resource-intensive processes',
                impact: 'System performance'
            });
        }
        
        return recommendations;
    }
    
    /**
     * Display performance summary
     */
    displaySummary() {
        console.log('\n📊 Performance Summary:');
        console.log('========================');
        console.log(`Build Time: ${this.metrics.build.average}ms (${this.getBuildStatus()})`);
        console.log(`Memory Usage: ${this.metrics.memory.current}MB (${this.getMemoryStatus()})`);
        console.log(`Response Time: ${this.metrics.response.average}ms (${this.getResponseStatus()})`);
        console.log(`CPU Usage: ${this.metrics.system.cpu.length > 0 ? this.metrics.system.cpu[this.metrics.system.cpu.length - 1] : 0}% (${this.getSystemStatus()})`);
        
        if (this.alerts.length > 0) {
            console.log(`\n⚠️  Recent Alerts: ${this.alerts.length}`);
            this.alerts.slice(-3).forEach(alert => {
                const emoji = alert.level === 'CRITICAL' ? '🚨' : '⚠️';
                console.log(`  ${emoji} ${alert.category}: ${alert.message}`);
            });
        }
        
        console.log('');
    }
    
    /**
     * Get current metrics
     */
    getMetrics() {
        return {
            config: this.config,
            metrics: this.metrics,
            alerts: this.alerts,
            isMonitoring: this.isMonitoring
        };
    }
}

// Export for use in other modules
export default PerformanceMonitor;

// Run if called directly
if (import.meta.url === `file://${process.argv[1]}`) {
    const monitor = new PerformanceMonitor();
    
    const args = process.argv.slice(2);
    const command = args[0];
    
    switch (command) {
        case 'start':
            monitor.start();
            
            // Handle graceful shutdown
            process.on('SIGINT', () => {
                monitor.stop();
                process.exit(0);
            });
            break;
            
        case 'report':
            monitor.generateReport();
            break;
            
        case 'metrics':
            console.log(JSON.stringify(monitor.getMetrics(), null, 2));
            break;
            
        default:
            console.log('Usage:');
            console.log('  node performance-monitor.js start  - Start performance monitoring');
            console.log('  node performance-monitor.js report - Generate performance report');
            console.log('  node performance-monitor.js metrics - Show current metrics');
            break;
    }
} 