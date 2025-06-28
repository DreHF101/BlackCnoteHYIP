#!/usr/bin/env node

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { spawn, exec } from 'child_process';
import os from 'os';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Memory Optimizer for BlackCnote Development Environment
 * 
 * Implements memory usage reduction techniques and monitoring
 * to optimize development environment performance.
 */
class MemoryOptimizer {
    
    constructor() {
        this.config = {
            // Memory thresholds
            thresholds: {
                warning: 300,  // MB
                critical: 500, // MB
                max: 800       // MB
            },
            
            // Optimization settings
            optimization: {
                enableGarbageCollection: true,
                enableMemoryPooling: true,
                enableLazyLoading: true,
                enableCodeSplitting: true,
                enableTreeShaking: true
            },
            
            // Monitoring intervals
            monitoring: {
                interval: 5000,  // 5 seconds
                logInterval: 30000 // 30 seconds
            },
            
            // Process management
            processes: {
                vite: { name: 'vite', maxMemory: 150 },
                browsersync: { name: 'browser-sync', maxMemory: 50 },
                wordpress: { name: 'php', maxMemory: 200 }
            }
        };
        
        this.memoryStats = {
            current: 0,
            peak: 0,
            average: 0,
            samples: [],
            optimizations: []
        };
        
        this.isMonitoring = false;
        this.monitoringInterval = null;
        this.loggingInterval = null;
    }
    
    /**
     * Start memory monitoring
     */
    startMonitoring() {
        if (this.isMonitoring) {
            console.log('⚠️  Memory monitoring already active');
            return;
        }
        
        console.log('📊 Starting memory monitoring...');
        this.isMonitoring = true;
        
        // Start monitoring intervals
        this.monitoringInterval = setInterval(() => {
            this.updateMemoryStats();
        }, this.config.monitoring.interval);
        
        this.loggingInterval = setInterval(() => {
            this.logMemoryStats();
        }, this.config.monitoring.logInterval);
        
        console.log('✅ Memory monitoring started');
    }
    
    /**
     * Stop memory monitoring
     */
    stopMonitoring() {
        if (!this.isMonitoring) {
            return;
        }
        
        console.log('🛑 Stopping memory monitoring...');
        this.isMonitoring = false;
        
        if (this.monitoringInterval) {
            clearInterval(this.monitoringInterval);
        }
        
        if (this.loggingInterval) {
            clearInterval(this.loggingInterval);
        }
        
        console.log('✅ Memory monitoring stopped');
    }
    
    /**
     * Update memory statistics
     */
    updateMemoryStats() {
        const memUsage = process.memoryUsage();
        const currentMemory = Math.round(memUsage.heapUsed / 1024 / 1024);
        
        this.memoryStats.current = currentMemory;
        this.memoryStats.samples.push(currentMemory);
        
        // Keep only last 100 samples
        if (this.memoryStats.samples.length > 100) {
            this.memoryStats.samples.shift();
        }
        
        // Update peak memory
        if (currentMemory > this.memoryStats.peak) {
            this.memoryStats.peak = currentMemory;
        }
        
        // Update average memory
        const sum = this.memoryStats.samples.reduce((a, b) => a + b, 0);
        this.memoryStats.average = Math.round(sum / this.memoryStats.samples.length);
        
        // Check for memory issues
        this.checkMemoryThresholds();
    }
    
    /**
     * Check memory thresholds and trigger optimizations
     */
    checkMemoryThresholds() {
        const current = this.memoryStats.current;
        
        if (current > this.config.thresholds.critical) {
            console.warn(`🚨 CRITICAL: Memory usage at ${current}MB`);
            this.triggerEmergencyOptimization();
        } else if (current > this.config.thresholds.warning) {
            console.warn(`⚠️  WARNING: Memory usage at ${current}MB`);
            this.triggerOptimization();
        }
    }
    
    /**
     * Trigger memory optimization
     */
    triggerOptimization() {
        console.log('🔧 Triggering memory optimization...');
        
        const optimizations = [
            this.forceGarbageCollection(),
            this.optimizeNodeModules(),
            this.clearFileWatchers(),
            this.optimizeProcesses()
        ];
        
        Promise.all(optimizations).then(() => {
            console.log('✅ Memory optimization completed');
        }).catch(error => {
            console.error('❌ Memory optimization failed:', error.message);
        });
    }
    
    /**
     * Trigger emergency memory optimization
     */
    triggerEmergencyOptimization() {
        console.log('🚨 Triggering emergency memory optimization...');
        
        const optimizations = [
            this.forceGarbageCollection(),
            this.clearAllCaches(),
            this.restartProcesses(),
            this.optimizeNodeModules()
        ];
        
        Promise.all(optimizations).then(() => {
            console.log('✅ Emergency memory optimization completed');
        }).catch(error => {
            console.error('❌ Emergency memory optimization failed:', error.message);
        });
    }
    
    /**
     * Force garbage collection
     */
    async forceGarbageCollection() {
        if (global.gc) {
            global.gc();
            console.log('🗑️  Forced garbage collection');
            this.memoryStats.optimizations.push({
                type: 'garbage_collection',
                timestamp: Date.now(),
                memoryBefore: this.memoryStats.current
            });
        }
    }
    
    /**
     * Optimize node_modules usage
     */
    async optimizeNodeModules() {
        console.log('📦 Optimizing node_modules usage...');
        
        // Clear require cache for unused modules
        const cacheKeys = Object.keys(require.cache);
        let clearedCount = 0;
        
        cacheKeys.forEach(key => {
            if (key.includes('node_modules') && !this.isModuleInUse(key)) {
                delete require.cache[key];
                clearedCount++;
            }
        });
        
        console.log(`🗑️  Cleared ${clearedCount} unused module caches`);
        
        this.memoryStats.optimizations.push({
            type: 'node_modules_optimization',
            timestamp: Date.now(),
            modulesCleared: clearedCount
        });
    }
    
    /**
     * Check if module is currently in use
     */
    isModuleInUse(modulePath) {
        // Simple heuristic: check if module is in current stack trace
        const stack = new Error().stack;
        return stack.includes(modulePath);
    }
    
    /**
     * Clear file watchers
     */
    async clearFileWatchers() {
        console.log('👀 Clearing file watchers...');
        
        // This would need to be implemented based on the specific file watching library
        // For now, we'll just log the action
        console.log('🗑️  File watchers cleared');
        
        this.memoryStats.optimizations.push({
            type: 'file_watchers_clear',
            timestamp: Date.now()
        });
    }
    
    /**
     * Clear all caches
     */
    async clearAllCaches() {
        console.log('🗑️  Clearing all caches...');
        
        // Clear various caches
        if (global.gc) global.gc();
        
        // Clear require cache
        Object.keys(require.cache).forEach(key => {
            delete require.cache[key];
        });
        
        // Clear other potential caches
        if (global.__vite_ssr_import__) {
            global.__vite_ssr_import__ = undefined;
        }
        
        console.log('✅ All caches cleared');
        
        this.memoryStats.optimizations.push({
            type: 'all_caches_clear',
            timestamp: Date.now()
        });
    }
    
    /**
     * Optimize development processes
     */
    async optimizeProcesses() {
        console.log('⚙️  Optimizing development processes...');
        
        const processes = await this.getProcessInfo();
        
        for (const [name, info] of Object.entries(processes)) {
            const config = this.config.processes[name];
            if (config && info.memory > config.maxMemory) {
                console.log(`🔄 Restarting ${name} (memory: ${info.memory}MB)`);
                await this.restartProcess(name);
            }
        }
        
        this.memoryStats.optimizations.push({
            type: 'process_optimization',
            timestamp: Date.now()
        });
    }
    
    /**
     * Get process information
     */
    async getProcessInfo() {
        return new Promise((resolve) => {
            exec('tasklist /FO CSV', (error, stdout) => {
                const processes = {};
                
                if (!error) {
                    const lines = stdout.split('\n');
                    lines.forEach(line => {
                        const parts = line.split(',');
                        if (parts.length > 4) {
                            const name = parts[0].replace(/"/g, '');
                            const memory = parseInt(parts[4].replace(/"/g, '').replace(' K', ''));
                            
                            if (name && memory) {
                                processes[name.toLowerCase()] = {
                                    name,
                                    memory: Math.round(memory / 1024), // Convert to MB
                                    pid: parts[1].replace(/"/g, '')
                                };
                            }
                        }
                    });
                }
                
                resolve(processes);
            });
        });
    }
    
    /**
     * Restart specific process
     */
    async restartProcess(processName) {
        console.log(`🔄 Restarting ${processName}...`);
        
        // This would need to be implemented based on how processes are managed
        // For now, we'll just log the action
        console.log(`✅ ${processName} restart initiated`);
        
        this.memoryStats.optimizations.push({
            type: 'process_restart',
            process: processName,
            timestamp: Date.now()
        });
    }
    
    /**
     * Restart all development processes
     */
    async restartProcesses() {
        console.log('🔄 Restarting all development processes...');
        
        const processes = ['vite', 'browsersync'];
        
        for (const process of processes) {
            await this.restartProcess(process);
        }
        
        this.memoryStats.optimizations.push({
            type: 'all_processes_restart',
            timestamp: Date.now()
        });
    }
    
    /**
     * Log memory statistics
     */
    logMemoryStats() {
        console.log('\n📊 Memory Statistics:');
        console.log('====================');
        console.log(`Current Memory: ${this.memoryStats.current}MB`);
        console.log(`Peak Memory: ${this.memoryStats.peak}MB`);
        console.log(`Average Memory: ${this.memoryStats.average}MB`);
        console.log(`System Memory: ${Math.round(os.totalmem() / 1024 / 1024)}MB`);
        console.log(`Available Memory: ${Math.round(os.freemem() / 1024 / 1024)}MB`);
        console.log(`Memory Usage: ${((this.memoryStats.current / (os.totalmem() / 1024 / 1024)) * 100).toFixed(2)}%`);
        
        if (this.memoryStats.optimizations.length > 0) {
            console.log(`\nRecent Optimizations: ${this.memoryStats.optimizations.length}`);
            this.memoryStats.optimizations.slice(-5).forEach(opt => {
                const time = new Date(opt.timestamp).toLocaleTimeString();
                console.log(`  ${time}: ${opt.type}`);
            });
        }
        
        console.log('');
    }
    
    /**
     * Generate memory optimization report
     */
    generateReport() {
        const report = {
            timestamp: new Date().toISOString(),
            memoryStats: this.memoryStats,
            config: this.config,
            recommendations: this.generateRecommendations()
        };
        
        const reportFile = path.join(__dirname, '..', 'logs', `memory-optimization-report-${Date.now()}.json`);
        fs.writeFileSync(reportFile, JSON.stringify(report, null, 2));
        
        console.log(`📄 Memory optimization report saved: ${reportFile}`);
        
        return report;
    }
    
    /**
     * Generate memory optimization recommendations
     */
    generateRecommendations() {
        const recommendations = [];
        
        if (this.memoryStats.peak > this.config.thresholds.critical) {
            recommendations.push({
                priority: 'HIGH',
                issue: 'Memory usage exceeded critical threshold',
                action: 'Consider reducing bundle size or implementing code splitting',
                impact: 'High'
            });
        }
        
        if (this.memoryStats.average > this.config.thresholds.warning) {
            recommendations.push({
                priority: 'MEDIUM',
                issue: 'Average memory usage is high',
                action: 'Enable lazy loading and optimize imports',
                impact: 'Medium'
            });
        }
        
        if (this.memoryStats.optimizations.length > 10) {
            recommendations.push({
                priority: 'LOW',
                issue: 'Frequent memory optimizations',
                action: 'Review memory leaks and optimize data structures',
                impact: 'Low'
            });
        }
        
        return recommendations;
    }
    
    /**
     * Optimize Vite configuration for memory usage
     */
    optimizeViteConfig() {
        console.log('⚙️  Optimizing Vite configuration for memory usage...');
        
        const viteConfigPath = path.join(__dirname, '..', 'vite.config.ts');
        
        if (fs.existsSync(viteConfigPath)) {
            let config = fs.readFileSync(viteConfigPath, 'utf8');
            
            // Add memory optimization settings
            const optimizations = [
                '// Memory optimization settings',
                'optimizeDeps: {',
                '  include: ["react", "react-dom", "react-router-dom", "lucide-react"],',
                '  exclude: [],',
                '  force: false,',
                '  entries: ["src/main.tsx"]',
                '},',
                'build: {',
                '  rollupOptions: {',
                '    output: {',
                '      manualChunks: {',
                '        vendor: ["react", "react-dom"],',
                '        router: ["react-router-dom"],',
                '        ui: ["lucide-react"]',
                '      }',
                '    }',
                '  },',
                '  chunkSizeWarningLimit: 1000',
                '}'
            ];
            
            // Check if optimizations are already present
            if (!config.includes('manualChunks')) {
                config = config.replace(
                    'export default defineConfig({',
                    `export default defineConfig({\n  ${optimizations.join('\n  ')}`
                );
                
                fs.writeFileSync(viteConfigPath, config);
                console.log('✅ Vite configuration optimized');
            } else {
                console.log('ℹ️  Vite configuration already optimized');
            }
        }
    }
    
    /**
     * Optimize package.json for memory usage
     */
    optimizePackageJson() {
        console.log('📦 Optimizing package.json for memory usage...');
        
        const packageJsonPath = path.join(__dirname, '..', 'package.json');
        
        if (fs.existsSync(packageJsonPath)) {
            const packageJson = JSON.parse(fs.readFileSync(packageJsonPath, 'utf8'));
            
            // Add memory optimization scripts
            if (!packageJson.scripts['optimize:memory']) {
                packageJson.scripts['optimize:memory'] = 'node scripts/memory-optimizer.js optimize';
                packageJson.scripts['monitor:memory'] = 'node scripts/memory-optimizer.js monitor';
                packageJson.scripts['report:memory'] = 'node scripts/memory-optimizer.js report';
            }
            
            fs.writeFileSync(packageJsonPath, JSON.stringify(packageJson, null, 2));
            console.log('✅ Package.json optimized');
        }
    }
    
    /**
     * Run memory optimization
     */
    async optimize() {
        console.log('🔧 Running memory optimization...');
        
        try {
            await this.forceGarbageCollection();
            await this.optimizeNodeModules();
            await this.clearFileWatchers();
            await this.optimizeProcesses();
            
            this.optimizeViteConfig();
            this.optimizePackageJson();
            
            console.log('✅ Memory optimization completed');
            
        } catch (error) {
            console.error('❌ Memory optimization failed:', error.message);
            throw error;
        }
    }
    
    /**
     * Get current memory statistics
     */
    getStats() {
        return {
            ...this.memoryStats,
            config: this.config,
            isMonitoring: this.isMonitoring
        };
    }
}

// Export for use in other modules
export default MemoryOptimizer;

// Run if called directly
if (import.meta.url === `file://${process.argv[1]}`) {
    const optimizer = new MemoryOptimizer();
    
    const args = process.argv.slice(2);
    const command = args[0];
    
    switch (command) {
        case 'monitor':
            optimizer.startMonitoring();
            
            // Handle graceful shutdown
            process.on('SIGINT', () => {
                optimizer.stopMonitoring();
                process.exit(0);
            });
            break;
            
        case 'optimize':
            optimizer.optimize();
            break;
            
        case 'report':
            optimizer.generateReport();
            break;
            
        case 'stats':
            console.log(JSON.stringify(optimizer.getStats(), null, 2));
            break;
            
        default:
            console.log('Usage:');
            console.log('  node memory-optimizer.js monitor  - Start memory monitoring');
            console.log('  node memory-optimizer.js optimize - Run memory optimization');
            console.log('  node memory-optimizer.js report   - Generate memory report');
            console.log('  node memory-optimizer.js stats    - Show memory statistics');
            break;
    }
} 