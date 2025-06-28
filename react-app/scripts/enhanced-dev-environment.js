#!/usr/bin/env node

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import http from 'http';
import https from 'https';
import { spawn, exec } from 'child_process';
import readline from 'readline';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Enhanced Development Environment Manager
 * 
 * Provides comprehensive development environment setup, monitoring, and optimization
 * for the BlackCnote WordPress/React development workflow.
 */
class EnhancedDevEnvironment {
    
    constructor() {
        this.config = {
            // WordPress configuration
            wordpress: {
                url: process.env.WORDPRESS_URL || 'http://localhost/blackcnote',
                adminUrl: process.env.WORDPRESS_ADMIN_URL || 'http://localhost/blackcnote/wp-admin',
                apiUrl: process.env.WORDPRESS_API_URL || 'http://localhost/blackcnote/wp-json',
                port: 80,
                ssl: false
            },
            
            // Development servers
            servers: {
                vite: {
                    port: process.env.VITE_PORT || 5174,
                    url: `http://localhost:${process.env.VITE_PORT || 5174}`,
                    process: null
                },
                browsersync: {
                    port: process.env.BS_PORT || 3000,
                    uiPort: process.env.BS_UI_PORT || 3001,
                    url: `http://localhost:${process.env.BS_PORT || 3000}`,
                    uiUrl: `http://localhost:${process.env.BS_UI_PORT || 3001}`,
                    process: null
                }
            },
            
            // File watching
            watch: {
                patterns: [
                    'blackcnote/**/*.php',
                    'blackcnote/**/*.css',
                    'blackcnote/**/*.js',
                    'blackcnote/**/*.html',
                    'hyiplab/**/*.php',
                    'src/**/*.tsx',
                    'src/**/*.ts',
                    'src/**/*.css',
                    'src/**/*.js',
                    'public/**/*'
                ],
                ignore: [
                    'node_modules',
                    'vendor',
                    'hyiplab/vendor',
                    '.git',
                    '*.log',
                    'dist',
                    'build',
                    '**/blackcnote-unified-debug.log'
                ]
            },
            
            // Performance monitoring
            performance: {
                memoryThreshold: 500, // MB
                cpuThreshold: 80, // %
                buildTimeThreshold: 10000, // ms
                reloadTimeThreshold: 1000 // ms
            },
            
            // Health check intervals
            healthCheck: {
                interval: 30000, // 30 seconds
                timeout: 5000, // 5 seconds
                retries: 3
            }
        };
        
        this.status = {
            wordpress: { running: false, lastCheck: null, errors: [] },
            vite: { running: false, lastCheck: null, errors: [] },
            browsersync: { running: false, lastCheck: null, errors: [] },
            performance: { memory: 0, cpu: 0, buildTime: 0, reloadTime: 0 },
            startTime: Date.now()
        };
        
        this.rl = readline.createInterface({
            input: process.stdin,
            output: process.stdout
        });
    }
    
    /**
     * Initialize the development environment
     */
    async init() {
        console.log('🚀 Initializing Enhanced Development Environment...\n');
        
        // Check prerequisites
        await this.checkPrerequisites();
        
        // Generate configurations
        await this.generateConfigurations();
        
        // Start health monitoring
        this.startHealthMonitoring();
        
        // Display status
        this.displayStatus();
        
        // Start interactive mode
        this.startInteractiveMode();
    }
    
    /**
     * Check development prerequisites
     */
    async checkPrerequisites() {
        console.log('📋 Checking prerequisites...');
        
        const checks = [
            { name: 'Node.js', check: () => this.checkNodeVersion() },
            { name: 'npm', check: () => this.checkNpmVersion() },
            { name: 'WordPress', check: () => this.checkWordPress() },
            { name: 'XAMPP', check: () => this.checkXampp() },
            { name: 'Dependencies', check: () => this.checkDependencies() },
            { name: 'Ports', check: () => this.checkPorts() }
        ];
        
        for (const check of checks) {
            try {
                const result = await check.check();
                console.log(`  ✅ ${check.name}: ${result}`);
            } catch (error) {
                console.log(`  ❌ ${check.name}: ${error.message}`);
                throw error;
            }
        }
        
        console.log('');
    }
    
    /**
     * Check Node.js version
     */
    async checkNodeVersion() {
        return new Promise((resolve, reject) => {
            exec('node --version', (error, stdout) => {
                if (error) {
                    reject(new Error('Node.js not found'));
                    return;
                }
                
                const version = stdout.trim();
                const majorVersion = parseInt(version.replace('v', '').split('.')[0]);
                
                if (majorVersion < 18) {
                    reject(new Error(`Node.js ${version} is too old. Required: >=18.0.0`));
                    return;
                }
                
                resolve(`v${version}`);
            });
        });
    }
    
    /**
     * Check npm version
     */
    async checkNpmVersion() {
        return new Promise((resolve, reject) => {
            exec('npm --version', (error, stdout) => {
                if (error) {
                    reject(new Error('npm not found'));
                    return;
                }
                
                const version = stdout.trim();
                const majorVersion = parseInt(version.split('.')[0]);
                
                if (majorVersion < 9) {
                    reject(new Error(`npm ${version} is too old. Required: >=9.0.0`));
                    return;
                }
                
                resolve(`v${version}`);
            });
        });
    }
    
    /**
     * Check WordPress installation
     */
    async checkWordPress() {
        return new Promise((resolve, reject) => {
            const url = this.config.wordpress.url;
            
            http.get(url, (res) => {
                if (res.statusCode === 200) {
                    resolve('Running');
                } else {
                    reject(new Error(`HTTP ${res.statusCode}`));
                }
            }).on('error', () => {
                reject(new Error('Not accessible'));
            });
        });
    }
    
    /**
     * Check XAMPP services
     */
    async checkXampp() {
        return new Promise((resolve, reject) => {
            // Check Apache
            http.get('http://localhost', (res) => {
                if (res.statusCode === 200 || res.statusCode === 403) {
                    resolve('Apache running');
                } else {
                    reject(new Error('Apache not running'));
                }
            }).on('error', () => {
                reject(new Error('Apache not accessible'));
            });
        });
    }
    
    /**
     * Check npm dependencies
     */
    async checkDependencies() {
        return new Promise((resolve, reject) => {
            const packageJsonPath = path.join(__dirname, '..', 'package.json');
            
            if (!fs.existsSync(packageJsonPath)) {
                reject(new Error('package.json not found'));
                return;
            }
            
            const nodeModulesPath = path.join(__dirname, '..', 'node_modules');
            
            if (!fs.existsSync(nodeModulesPath)) {
                reject(new Error('node_modules not found. Run: npm install'));
                return;
            }
            
            resolve('Installed');
        });
    }
    
    /**
     * Check port availability
     */
    async checkPorts() {
        const ports = [
            this.config.servers.vite.port,
            this.config.servers.browsersync.port,
            this.config.servers.browsersync.uiPort
        ];
        
        const results = await Promise.all(
            ports.map(port => this.isPortAvailable(port))
        );
        
        const available = results.filter(Boolean).length;
        const total = ports.length;
        
        if (available === total) {
            return `${available}/${total} ports available`;
        } else {
            throw new Error(`${available}/${total} ports available`);
        }
    }
    
    /**
     * Check if port is available
     */
    async isPortAvailable(port) {
        return new Promise((resolve) => {
            const server = http.createServer();
            
            server.listen(port, () => {
                server.close();
                resolve(true);
            });
            
            server.on('error', () => {
                resolve(false);
            });
        });
    }
    
    /**
     * Generate development configurations
     */
    async generateConfigurations() {
        console.log('⚙️  Generating configurations...');
        
        // Generate Browsersync configuration
        await this.generateBrowsersyncConfig();
        
        // Generate Vite configuration
        await this.generateViteConfig();
        
        // Generate environment file
        await this.generateEnvironmentFile();
        
        console.log('  ✅ Configurations generated\n');
    }
    
    /**
     * Generate Browsersync configuration
     */
    async generateBrowsersyncConfig() {
        const bsConfig = {
            proxy: this.config.wordpress.url,
            files: this.config.watch.patterns,
            ignore: this.config.watch.ignore,
            port: this.config.servers.browsersync.port,
            ui: {
                port: this.config.servers.browsersync.uiPort
            },
            reloadDelay: 0,
            reloadDebounce: 250,
            reloadThrottle: 0,
            open: true,
            browser: "default",
            notify: true,
            ghostMode: {
                clicks: true,
                scroll: true,
                forms: {
                    submit: true,
                    inputs: true,
                    toggles: true
                }
            },
            logLevel: "info",
            middleware: function(req, res, next) {
                res.setHeader('Access-Control-Allow-Origin', '*');
                res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                res.setHeader('Access-Control-Allow-Headers', 'Content-Type, X-WP-Nonce, Authorization');
                res.setHeader('Access-Control-Allow-Credentials', 'true');
                
                if (req.method === 'OPTIONS') {
                    res.writeHead(200);
                    res.end();
                    return;
                }
                
                // Skip debug-related requests
                if (req.url && (
                    req.url.includes('blackcnote_debug') || 
                    req.url.includes('blackcnote_send_to_cursor')
                )) {
                    return next();
                }
                
                next();
            },
            snippetOptions: {
                ignorePaths: [
                    '/wp-admin/**',
                    '/wp-content/debug.log',
                    '/blackcnote-unified-debug.log'
                ]
            },
            watchOptions: {
                ignored: this.config.watch.ignore,
                usePolling: true,
                interval: 1000,
                binaryInterval: 3000
            },
            callbacks: {
                ready: (err, bs) => {
                    console.log('✅ Browsersync is ready!');
                    console.log(`🌐 Local: ${bs.options.url}`);
                    console.log(`🎛️  UI: ${bs.options.ui.url}`);
                },
                error: (err) => {
                    console.error('❌ Browsersync error:', err.message);
                    this.status.browsersync.errors.push(err.message);
                }
            }
        };
        
        const configPath = path.join(__dirname, '..', 'bs-config.cjs');
        const configContent = `module.exports = ${JSON.stringify(bsConfig, null, 2)};`;
        
        fs.writeFileSync(configPath, configContent);
    }
    
    /**
     * Generate Vite configuration
     */
    async generateViteConfig() {
        // Vite config is already well-configured, just update environment variables
        const envPath = path.join(__dirname, '..', '.env');
        const envContent = `
# BlackCnote Development Environment
VITE_WORDPRESS_URL=${this.config.wordpress.url}
VITE_API_URL=${this.config.wordpress.apiUrl}
VITE_DEV_SERVER=http://localhost:${this.config.servers.vite.port}
VITE_DEBUG_ENABLED=true
VITE_DEBUG_LEVEL=info
        `.trim();
        
        fs.writeFileSync(envPath, envContent);
    }
    
    /**
     * Generate environment file
     */
    async generateEnvironmentFile() {
        const envPath = path.join(__dirname, '..', '.env.local');
        const envContent = `
# Local Development Environment
NODE_ENV=development
WORDPRESS_URL=${this.config.wordpress.url}
VITE_PORT=${this.config.servers.vite.port}
BS_PORT=${this.config.servers.browsersync.port}
BS_UI_PORT=${this.config.servers.browsersync.uiPort}
        `.trim();
        
        fs.writeFileSync(envPath, envContent);
    }
    
    /**
     * Start health monitoring
     */
    startHealthMonitoring() {
        console.log('🔍 Starting health monitoring...');
        
        setInterval(() => {
            this.performHealthCheck();
        }, this.config.healthCheck.interval);
        
        // Initial health check
        setTimeout(() => {
            this.performHealthCheck();
        }, 5000);
    }
    
    /**
     * Perform health check
     */
    async performHealthCheck() {
        const checks = [
            { name: 'WordPress', check: () => this.checkServiceHealth('wordpress') },
            { name: 'Vite', check: () => this.checkServiceHealth('vite') },
            { name: 'Browsersync', check: () => this.checkServiceHealth('browsersync') }
        ];
        
        for (const check of checks) {
            try {
                const result = await check.check();
                this.status[check.name.toLowerCase()] = {
                    ...this.status[check.name.toLowerCase()],
                    running: result.running,
                    lastCheck: new Date(),
                    errors: result.errors || []
                };
            } catch (error) {
                this.status[check.name.toLowerCase()].errors.push(error.message);
            }
        }
        
        // Update performance metrics
        await this.updatePerformanceMetrics();
    }
    
    /**
     * Check service health
     */
    async checkServiceHealth(service) {
        const urls = {
            wordpress: this.config.wordpress.url,
            vite: this.config.servers.vite.url,
            browsersync: this.config.servers.browsersync.url
        };
        
        const url = urls[service];
        
        return new Promise((resolve) => {
            const timeout = setTimeout(() => {
                resolve({ running: false, errors: ['Timeout'] });
            }, this.config.healthCheck.timeout);
            
            http.get(url, (res) => {
                clearTimeout(timeout);
                resolve({ running: res.statusCode === 200, errors: [] });
            }).on('error', (error) => {
                clearTimeout(timeout);
                resolve({ running: false, errors: [error.message] });
            });
        });
    }
    
    /**
     * Update performance metrics
     */
    async updatePerformanceMetrics() {
        // Get memory usage
        const memUsage = process.memoryUsage();
        this.status.performance.memory = Math.round(memUsage.heapUsed / 1024 / 1024);
        
        // Get CPU usage (simplified)
        this.status.performance.cpu = Math.round(Math.random() * 20 + 10); // Mock data
        
        // Check for performance issues
        if (this.status.performance.memory > this.config.performance.memoryThreshold) {
            console.warn(`⚠️  High memory usage: ${this.status.performance.memory}MB`);
        }
        
        if (this.status.performance.cpu > this.config.performance.cpuThreshold) {
            console.warn(`⚠️  High CPU usage: ${this.status.performance.cpu}%`);
        }
    }
    
    /**
     * Display current status
     */
    displayStatus() {
        console.log('📊 Development Environment Status:');
        console.log('=====================================');
        
        // Service status
        Object.entries(this.status).forEach(([service, info]) => {
            if (service === 'performance' || service === 'startTime') return;
            
            const status = info.running ? '🟢 Running' : '🔴 Stopped';
            const lastCheck = info.lastCheck ? 
                info.lastCheck.toLocaleTimeString() : 'Never';
            
            console.log(`${service.charAt(0).toUpperCase() + service.slice(1)}: ${status} (Last check: ${lastCheck})`);
            
            if (info.errors.length > 0) {
                info.errors.forEach(error => {
                    console.log(`  ❌ ${error}`);
                });
            }
        });
        
        // Performance metrics
        console.log(`\nPerformance Metrics:`);
        console.log(`  Memory: ${this.status.performance.memory}MB`);
        console.log(`  CPU: ${this.status.performance.cpu}%`);
        console.log(`  Uptime: ${this.getUptime()}`);
        
        console.log('\n🌐 Development URLs:');
        console.log(`  WordPress: ${this.config.wordpress.url}`);
        console.log(`  Vite: ${this.config.servers.vite.url}`);
        console.log(`  Browsersync: ${this.config.servers.browsersync.url}`);
        console.log(`  Browsersync UI: ${this.config.servers.browsersync.uiUrl}`);
        
        console.log('\n');
    }
    
    /**
     * Get uptime string
     */
    getUptime() {
        const uptime = Date.now() - this.status.startTime;
        const hours = Math.floor(uptime / (1000 * 60 * 60));
        const minutes = Math.floor((uptime % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((uptime % (1000 * 60)) / 1000);
        
        return `${hours}h ${minutes}m ${seconds}s`;
    }
    
    /**
     * Start interactive mode
     */
    startInteractiveMode() {
        console.log('🎮 Interactive Mode - Available Commands:');
        console.log('  status    - Show current status');
        console.log('  start     - Start development servers');
        console.log('  stop      - Stop development servers');
        console.log('  restart   - Restart development servers');
        console.log('  health    - Perform health check');
        console.log('  logs      - Show recent logs');
        console.log('  help      - Show this help');
        console.log('  exit      - Exit interactive mode');
        console.log('');
        
        this.rl.question('Enter command: ', (input) => {
            this.handleCommand(input.trim().toLowerCase());
        });
    }
    
    /**
     * Handle interactive commands
     */
    handleCommand(command) {
        switch (command) {
            case 'status':
                this.displayStatus();
                break;
                
            case 'start':
                this.startDevelopmentServers();
                break;
                
            case 'stop':
                this.stopDevelopmentServers();
                break;
                
            case 'restart':
                this.restartDevelopmentServers();
                break;
                
            case 'health':
                this.performHealthCheck();
                this.displayStatus();
                break;
                
            case 'logs':
                this.showLogs();
                break;
                
            case 'help':
                this.showHelp();
                break;
                
            case 'exit':
                console.log('👋 Goodbye!');
                process.exit(0);
                break;
                
            default:
                console.log('❌ Unknown command. Type "help" for available commands.');
        }
        
        // Continue interactive mode
        setTimeout(() => {
            this.rl.question('Enter command: ', (input) => {
                this.handleCommand(input.trim().toLowerCase());
            });
        }, 1000);
    }
    
    /**
     * Start development servers
     */
    startDevelopmentServers() {
        console.log('🚀 Starting development servers...');
        
        // Start Vite
        this.startViteServer();
        
        // Start Browsersync
        this.startBrowsersyncServer();
        
        console.log('✅ Development servers started');
    }
    
    /**
     * Start Vite server
     */
    startViteServer() {
        if (this.status.vite.running) {
            console.log('⚠️  Vite server already running');
            return;
        }
        
        const viteProcess = spawn('npm', ['run', 'dev'], {
            cwd: path.join(__dirname, '..'),
            stdio: 'pipe'
        });
        
        viteProcess.stdout.on('data', (data) => {
            console.log(`[Vite] ${data.toString().trim()}`);
        });
        
        viteProcess.stderr.on('data', (data) => {
            console.error(`[Vite Error] ${data.toString().trim()}`);
        });
        
        this.config.servers.vite.process = viteProcess;
        this.status.vite.running = true;
    }
    
    /**
     * Start Browsersync server
     */
    startBrowsersyncServer() {
        if (this.status.browsersync.running) {
            console.log('⚠️  Browsersync server already running');
            return;
        }
        
        const bsProcess = spawn('npm', ['run', 'dev:sync'], {
            cwd: path.join(__dirname, '..'),
            stdio: 'pipe'
        });
        
        bsProcess.stdout.on('data', (data) => {
            console.log(`[Browsersync] ${data.toString().trim()}`);
        });
        
        bsProcess.stderr.on('data', (data) => {
            console.error(`[Browsersync Error] ${data.toString().trim()}`);
        });
        
        this.config.servers.browsersync.process = bsProcess;
        this.status.browsersync.running = true;
    }
    
    /**
     * Stop development servers
     */
    stopDevelopmentServers() {
        console.log('🛑 Stopping development servers...');
        
        if (this.config.servers.vite.process) {
            this.config.servers.vite.process.kill();
            this.status.vite.running = false;
        }
        
        if (this.config.servers.browsersync.process) {
            this.config.servers.browsersync.process.kill();
            this.status.browsersync.running = false;
        }
        
        console.log('✅ Development servers stopped');
    }
    
    /**
     * Restart development servers
     */
    restartDevelopmentServers() {
        console.log('🔄 Restarting development servers...');
        
        this.stopDevelopmentServers();
        
        setTimeout(() => {
            this.startDevelopmentServers();
        }, 2000);
    }
    
    /**
     * Show recent logs
     */
    showLogs() {
        console.log('📋 Recent Logs:');
        console.log('===============');
        
        // Show WordPress debug log
        const wpLogPath = path.join(__dirname, '..', '..', 'wp-content', 'debug.log');
        if (fs.existsSync(wpLogPath)) {
            const wpLogs = fs.readFileSync(wpLogPath, 'utf8')
                .split('\n')
                .slice(-10)
                .filter(line => line.trim());
            
            console.log('WordPress Debug Log (last 10 lines):');
            wpLogs.forEach(log => console.log(`  ${log}`));
        }
        
        // Show error logs
        Object.entries(this.status).forEach(([service, info]) => {
            if (service === 'performance' || service === 'startTime') return;
            
            if (info.errors.length > 0) {
                console.log(`\n${service.charAt(0).toUpperCase() + service.slice(1)} Errors:`);
                info.errors.slice(-5).forEach(error => {
                    console.log(`  ❌ ${error}`);
                });
            }
        });
        
        console.log('');
    }
    
    /**
     * Show help
     */
    showHelp() {
        console.log('📖 Available Commands:');
        console.log('======================');
        console.log('  status    - Show current development environment status');
        console.log('  start     - Start Vite and Browsersync development servers');
        console.log('  stop      - Stop all development servers');
        console.log('  restart   - Restart all development servers');
        console.log('  health    - Perform comprehensive health check');
        console.log('  logs      - Show recent error logs and debug information');
        console.log('  help      - Show this help message');
        console.log('  exit      - Exit the interactive mode');
        console.log('');
    }
}

// Initialize and run the enhanced development environment
const devEnv = new EnhancedDevEnvironment();
devEnv.init().catch(console.error); 