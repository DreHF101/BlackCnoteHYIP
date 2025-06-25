/**
 * BlackCnote React Sync API
 * Comprehensive real-time synchronization between WordPress, React, and GitHub
 */

class BlackCnoteSyncAPI {
    constructor() {
        this.config = {
            wordpressUrl: 'http://localhost:8888',
            reactUrl: 'http://localhost:5174',
            githubRepo: 'https://github.com/DreHF101/BlackCnoteHYIP.git',
            syncInterval: 1000,
            autoSave: true,
            fileWatching: true,
            dockerMode: true,
            websocketEnabled: false
        };
        
        this.state = {
            connected: false,
            syncing: false,
            lastSync: null,
            pendingChanges: [],
            gitStatus: 'unknown',
            services: {
                wordpress: false,
                react: false,
                docker: false,
                git: false
            }
        };
        
        this.handlers = {};
        this.websocket = null;
        this.syncTimer = null;
        this.fileWatcher = null;
        
        this.init();
    }

    /**
     * Initialize the sync API
     */
    async init() {
        console.log('BlackCnote Sync API: Initializing...');
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Initialize connections
        await this.initializeConnections();
        
        // Setup file watching
        if (this.config.fileWatching) {
            this.setupFileWatching();
        }
        
        // Setup auto save
        if (this.config.autoSave) {
            this.setupAutoSave();
        }
        
        // Setup service monitoring
        this.setupServiceMonitoring();
        
        // Setup WebSocket if enabled
        if (this.config.websocketEnabled) {
            this.setupWebSocket();
        }
        
        console.log('BlackCnote Sync API: Initialized successfully');
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Listen for window focus/blur for sync optimization
        window.addEventListener('focus', () => this.onWindowFocus());
        window.addEventListener('blur', () => this.onWindowBlur());
        
        // Listen for beforeunload to save pending changes
        window.addEventListener('beforeunload', () => this.onBeforeUnload());
        
        // Listen for online/offline events
        window.addEventListener('online', () => this.onOnline());
        window.addEventListener('offline', () => this.onOffline());
    }

    /**
     * Initialize connections to all services
     */
    async initializeConnections() {
        // Check WordPress connection
        await this.checkWordPressConnection();
        
        // Check React dev server
        await this.checkReactConnection();
        
        // Check Docker services
        if (this.config.dockerMode) {
            await this.checkDockerServices();
        }
        
        // Check Git status
        await this.checkGitStatus();
        
        // Start sync timer
        this.startSyncTimer();
    }

    /**
     * Check WordPress connection
     */
    async checkWordPressConnection() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/`);
            if (response.ok) {
                this.state.services.wordpress = true;
                this.emit('service-status', { service: 'wordpress', status: true });
                console.log('WordPress: Connected');
            } else {
                throw new Error('WordPress not responding');
            }
        } catch (error) {
            this.state.services.wordpress = false;
            this.emit('service-status', { service: 'wordpress', status: false, error: error.message });
            console.warn('WordPress: Connection failed', error);
        }
    }

    /**
     * Check React connection
     */
    async checkReactConnection() {
        try {
            const response = await fetch(`${this.config.reactUrl}`);
            if (response.ok) {
                this.state.services.react = true;
                this.emit('service-status', { service: 'react', status: true });
                console.log('React: Connected');
            } else {
                throw new Error('React dev server not responding');
            }
        } catch (error) {
            this.state.services.react = false;
            this.emit('service-status', { service: 'react', status: false, error: error.message });
            console.warn('React: Connection failed', error);
        }
    }

    /**
     * Check Docker services
     */
    async checkDockerServices() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/docker/status`);
            if (response.ok) {
                const data = await response.json();
                this.state.services.docker = data.success;
                this.emit('service-status', { service: 'docker', status: data.success, data: data.data });
                console.log('Docker: Services checked');
            } else {
                throw new Error('Docker status check failed');
            }
        } catch (error) {
            this.state.services.docker = false;
            this.emit('service-status', { service: 'docker', status: false, error: error.message });
            console.warn('Docker: Status check failed', error);
        }
    }

    /**
     * Check Git status
     */
    async checkGitStatus() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/github/status`);
            if (response.ok) {
                const data = await response.json();
                this.state.gitStatus = data;
                this.state.services.git = data.is_repo;
                this.emit('git-status', data);
                console.log('Git: Status updated');
            } else {
                throw new Error('Git status check failed');
            }
        } catch (error) {
            this.state.services.git = false;
            this.emit('service-status', { service: 'git', status: false, error: error.message });
            console.warn('Git: Status check failed', error);
        }
    }

    /**
     * Start sync timer
     */
    startSyncTimer() {
        if (this.syncTimer) {
            clearInterval(this.syncTimer);
        }
        
        this.syncTimer = setInterval(() => {
            this.performSync();
        }, this.config.syncInterval);
    }

    /**
     * Perform sync operations
     */
    async performSync() {
        if (this.state.syncing) {
            return; // Prevent concurrent syncs
        }
        
        this.state.syncing = true;
        
        try {
            // Sync pending changes
            if (this.state.pendingChanges.length > 0) {
                await this.syncPendingChanges();
            }
            
            // Check for new changes from WordPress
            await this.checkWordPressChanges();
            
            // Update service status
            await this.updateServiceStatus();
            
            this.state.lastSync = Date.now();
            this.emit('sync-complete', { timestamp: this.state.lastSync });
            
        } catch (error) {
            console.error('Sync failed:', error);
            this.emit('sync-error', error);
        } finally {
            this.state.syncing = false;
        }
    }

    /**
     * Sync pending changes
     */
    async syncPendingChanges() {
        const changes = [...this.state.pendingChanges];
        this.state.pendingChanges = [];
        
        for (const change of changes) {
            try {
                await this.sendChangeToWordPress(change);
                this.emit('change-synced', change);
            } catch (error) {
                console.error('Failed to sync change:', change, error);
                this.state.pendingChanges.push(change); // Re-add failed changes
                this.emit('change-sync-failed', { change, error });
            }
        }
    }

    /**
     * Send change to WordPress
     */
    async sendChangeToWordPress(change) {
        const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/content/${change.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': this.getNonce()
            },
            body: JSON.stringify(change)
        });
        
        if (!response.ok) {
            throw new Error(`WordPress sync failed: ${response.statusText}`);
        }
        
        return await response.json();
    }

    /**
     * Check for changes from WordPress
     */
    async checkWordPressChanges() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/changes?since=${this.state.lastSync || 0}`);
            if (response.ok) {
                const changes = await response.json();
                for (const change of changes) {
                    this.handleWordPressChange(change);
                }
            }
        } catch (error) {
            console.warn('Failed to check WordPress changes:', error);
        }
    }

    /**
     * Handle change from WordPress
     */
    handleWordPressChange(change) {
        this.emit('wordpress-change', change);
        
        // Update local state based on change type
        switch (change.type) {
            case 'content':
                this.updateLocalContent(change);
                break;
            case 'style':
                this.updateLocalStyles(change);
                break;
            case 'component':
                this.updateLocalComponent(change);
                break;
            case 'setting':
                this.updateLocalSetting(change);
                break;
        }
    }

    /**
     * Update local content
     */
    updateLocalContent(change) {
        // Find and update content in React components
        const elements = document.querySelectorAll(`[data-content-id="${change.id}"]`);
        elements.forEach(element => {
            element.innerHTML = change.content;
            element.dispatchEvent(new Event('content-updated'));
        });
    }

    /**
     * Update local styles
     */
    updateLocalStyles(change) {
        // Update CSS custom properties or inline styles
        if (change.target === 'css-variables') {
            Object.entries(change.styles).forEach(([property, value]) => {
                document.documentElement.style.setProperty(`--${property}`, value);
            });
        } else {
            // Update specific element styles
            const elements = document.querySelectorAll(`[data-style-id="${change.id}"]`);
            elements.forEach(element => {
                Object.assign(element.style, change.styles);
            });
        }
    }

    /**
     * Update local component
     */
    updateLocalComponent(change) {
        // Trigger component re-render
        this.emit('component-update', change);
    }

    /**
     * Update local setting
     */
    updateLocalSetting(change) {
        // Update local storage or state
        localStorage.setItem(`blackcnote_${change.id}`, JSON.stringify(change.value));
        this.emit('setting-updated', change);
    }

    /**
     * Setup file watching
     */
    setupFileWatching() {
        // Setup file change detection for development
        if (typeof window !== 'undefined' && window.__VITE_HMR_RUNTIME__) {
            // Vite HMR is available
            this.setupViteFileWatching();
        } else {
            // Fallback to polling
            this.setupPollingFileWatching();
        }
    }

    /**
     * Setup Vite file watching
     */
    setupViteFileWatching() {
        // Vite HMR already handles file watching
        console.log('Using Vite HMR for file watching');
    }

    /**
     * Setup polling file watching
     */
    setupPollingFileWatching() {
        // Poll for file changes every 5 seconds
        setInterval(() => {
            this.checkFileChanges();
        }, 5000);
    }

    /**
     * Check for file changes
     */
    async checkFileChanges() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/file-changes`);
            if (response.ok) {
                const changes = await response.json();
                for (const change of changes) {
                    this.handleFileChange(change);
                }
            }
        } catch (error) {
            console.warn('Failed to check file changes:', error);
        }
    }

    /**
     * Handle file change
     */
    handleFileChange(change) {
        this.emit('file-change', change);
        
        switch (change.extension) {
            case 'css':
            case 'scss':
                this.handleStyleFileChange(change);
                break;
            case 'js':
            case 'jsx':
            case 'ts':
            case 'tsx':
                this.handleScriptFileChange(change);
                break;
            case 'php':
                this.handlePhpFileChange(change);
                break;
        }
    }

    /**
     * Handle style file change
     */
    handleStyleFileChange(change) {
        // Reload stylesheets
        const links = document.querySelectorAll('link[rel="stylesheet"]');
        links.forEach(link => {
            const url = new URL(link.href);
            url.searchParams.set('v', Date.now());
            link.href = url.toString();
        });
    }

    /**
     * Handle script file change
     */
    handleScriptFileChange(change) {
        // Trigger page reload for script changes
        if (confirm('Script file changed. Reload page?')) {
            window.location.reload();
        }
    }

    /**
     * Handle PHP file change
     */
    handlePhpFileChange(change) {
        // Show notification for PHP changes
        this.showNotification('PHP file changed. WordPress may need to be refreshed.', 'info');
    }

    /**
     * Setup auto save
     */
    setupAutoSave() {
        setInterval(() => {
            this.autoSave();
        }, 30000); // Auto save every 30 seconds
    }

    /**
     * Auto save
     */
    autoSave() {
        if (this.state.pendingChanges.length > 0) {
            this.saveAllChanges();
        }
    }

    /**
     * Save all changes
     */
    async saveAllChanges() {
        if (this.state.pendingChanges.length === 0) {
            return;
        }
        
        try {
            await this.syncPendingChanges();
            this.showNotification('All changes saved automatically', 'success');
        } catch (error) {
            console.error('Auto save failed:', error);
            this.showNotification('Auto save failed', 'error');
        }
    }

    /**
     * Setup service monitoring
     */
    setupServiceMonitoring() {
        setInterval(() => {
            this.updateServiceStatus();
        }, 30000); // Check services every 30 seconds
    }

    /**
     * Update service status
     */
    async updateServiceStatus() {
        await Promise.all([
            this.checkWordPressConnection(),
            this.checkReactConnection(),
            this.checkDockerServices(),
            this.checkGitStatus()
        ]);
    }

    /**
     * Setup WebSocket connection
     */
    setupWebSocket() {
        try {
            this.websocket = new WebSocket(`ws://${this.config.wordpressUrl.replace('http://', '')}/ws/sync`);
            
            this.websocket.onopen = () => {
                console.log('WebSocket: Connected');
                this.state.connected = true;
                this.emit('websocket-connected');
            };
            
            this.websocket.onmessage = (event) => {
                const data = JSON.parse(event.data);
                this.handleWebSocketMessage(data);
            };
            
            this.websocket.onclose = () => {
                console.log('WebSocket: Disconnected');
                this.state.connected = false;
                this.emit('websocket-disconnected');
                
                // Attempt to reconnect
                setTimeout(() => {
                    this.setupWebSocket();
                }, 5000);
            };
            
            this.websocket.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.emit('websocket-error', error);
            };
            
        } catch (error) {
            console.warn('WebSocket not available, falling back to HTTP:', error);
            this.config.websocketEnabled = false;
        }
    }

    /**
     * Handle WebSocket message
     */
    handleWebSocketMessage(data) {
        switch (data.type) {
            case 'content-update':
                this.handleWordPressChange(data);
                break;
            case 'style-update':
                this.handleWordPressChange(data);
                break;
            case 'component-update':
                this.handleWordPressChange(data);
                break;
            case 'git-status':
                this.state.gitStatus = data.status;
                this.emit('git-status', data.status);
                break;
            case 'service-status':
                this.state.services[data.service] = data.status;
                this.emit('service-status', data);
                break;
        }
    }

    /**
     * Send change to WordPress via WebSocket or HTTP
     */
    async sendChange(change) {
        this.state.pendingChanges.push(change);
        
        if (this.state.connected && this.websocket) {
            // Send via WebSocket
            this.websocket.send(JSON.stringify(change));
        } else {
            // Send via HTTP (handled by sync timer)
            this.emit('change-queued', change);
        }
    }

    /**
     * Send content change
     */
    sendContentChange(id, content, type = 'content') {
        const change = {
            type: 'content',
            id: id,
            content: content,
            subtype: type,
            timestamp: Date.now()
        };
        
        this.sendChange(change);
    }

    /**
     * Send style change
     */
    sendStyleChange(id, styles, type = 'style') {
        const change = {
            type: 'style',
            id: id,
            styles: styles,
            subtype: type,
            timestamp: Date.now()
        };
        
        this.sendChange(change);
    }

    /**
     * Send component change
     */
    sendComponentChange(name, data) {
        const change = {
            type: 'component',
            name: name,
            data: data,
            timestamp: Date.now()
        };
        
        this.sendChange(change);
    }

    /**
     * Git operations
     */
    async gitCommit(message) {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/github/commit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': this.getNonce()
                },
                body: JSON.stringify({ message })
            });
            
            const result = await response.json();
            this.emit('git-commit', result);
            return result;
        } catch (error) {
            console.error('Git commit failed:', error);
            this.emit('git-error', error);
            throw error;
        }
    }

    async gitPush() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/github/push`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': this.getNonce()
                }
            });
            
            const result = await response.json();
            this.emit('git-push', result);
            return result;
        } catch (error) {
            console.error('Git push failed:', error);
            this.emit('git-error', error);
            throw error;
        }
    }

    async gitSync(message) {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/github/sync`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': this.getNonce()
                },
                body: JSON.stringify({ message })
            });
            
            const result = await response.json();
            this.emit('git-sync', result);
            return result;
        } catch (error) {
            console.error('Git sync failed:', error);
            this.emit('git-error', error);
            throw error;
        }
    }

    async gitDeploy() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/github/deploy`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': this.getNonce()
                }
            });
            
            const result = await response.json();
            this.emit('git-deploy', result);
            return result;
        } catch (error) {
            console.error('Git deploy failed:', error);
            this.emit('git-error', error);
            throw error;
        }
    }

    /**
     * Development operations
     */
    async clearCache() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/dev/clear-cache`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': this.getNonce()
                }
            });
            
            const result = await response.json();
            this.emit('cache-cleared', result);
            return result;
        } catch (error) {
            console.error('Clear cache failed:', error);
            this.emit('dev-error', error);
            throw error;
        }
    }

    async restartServices() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/dev/restart-services`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': this.getNonce()
                }
            });
            
            const result = await response.json();
            this.emit('services-restarted', result);
            return result;
        } catch (error) {
            console.error('Restart services failed:', error);
            this.emit('dev-error', error);
            throw error;
        }
    }

    async buildReact() {
        try {
            const response = await fetch(`${this.config.wordpressUrl}/wp-json/blackcnote/v1/dev/build-react`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': this.getNonce()
                }
            });
            
            const result = await response.json();
            this.emit('react-built', result);
            return result;
        } catch (error) {
            console.error('Build React failed:', error);
            this.emit('dev-error', error);
            throw error;
        }
    }

    /**
     * Event handling
     */
    on(event, handler) {
        if (!this.handlers[event]) {
            this.handlers[event] = [];
        }
        this.handlers[event].push(handler);
    }

    off(event, handler) {
        if (this.handlers[event]) {
            const index = this.handlers[event].indexOf(handler);
            if (index > -1) {
                this.handlers[event].splice(index, 1);
            }
        }
    }

    emit(event, data) {
        if (this.handlers[event]) {
            this.handlers[event].forEach(handler => {
                try {
                    handler(data);
                } catch (error) {
                    console.error(`Error in event handler for ${event}:`, error);
                }
            });
        }
    }

    /**
     * Utility methods
     */
    getNonce() {
        // Get nonce from WordPress
        return window.blackcnoteReact?.apiNonce || '';
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `blackcnote-notification ${type}`;
        notification.innerHTML = `
            <span class="message">${message}</span>
            <button class="close-btn">×</button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
        
        // Manual close
        notification.querySelector('.close-btn').addEventListener('click', () => {
            notification.remove();
        });
    }

    /**
     * Window event handlers
     */
    onWindowFocus() {
        // Resume sync when window gains focus
        this.performSync();
    }

    onWindowBlur() {
        // Pause sync when window loses focus
        // (optional optimization)
    }

    onBeforeUnload() {
        // Save pending changes before unload
        if (this.state.pendingChanges.length > 0) {
            this.saveAllChanges();
        }
    }

    onOnline() {
        console.log('Network: Online');
        this.emit('network-online');
        this.performSync();
    }

    onOffline() {
        console.log('Network: Offline');
        this.emit('network-offline');
    }

    /**
     * Get current state
     */
    getState() {
        return { ...this.state };
    }

    /**
     * Get configuration
     */
    getConfig() {
        return { ...this.config };
    }

    /**
     * Update configuration
     */
    updateConfig(newConfig) {
        this.config = { ...this.config, ...newConfig };
        this.emit('config-updated', this.config);
    }

    /**
     * Destroy instance
     */
    destroy() {
        if (this.syncTimer) {
            clearInterval(this.syncTimer);
        }
        
        if (this.websocket) {
            this.websocket.close();
        }
        
        if (this.fileWatcher) {
            this.fileWatcher.close();
        }
        
        this.handlers = {};
        console.log('BlackCnote Sync API: Destroyed');
    }
}

// Create global instance
const blackcnoteSync = new BlackCnoteSyncAPI();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BlackCnoteSyncAPI;
    module.exports.default = BlackCnoteSyncAPI;
}

// Export for ES modules
if (typeof exports !== 'undefined') {
    exports.BlackCnoteSyncAPI = BlackCnoteSyncAPI;
    exports.blackcnoteSync = blackcnoteSync;
}

// Make available globally
window.BlackCnoteSyncAPI = BlackCnoteSyncAPI;
window.blackcnoteSync = blackcnoteSync; 
} 