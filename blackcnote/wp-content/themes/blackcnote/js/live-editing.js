/**
 * BlackCnote Live Editing System
 * Real-time content editing and synchronization between WordPress and React
 *
 * @package BlackCnote
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // BlackCnote Live Editing System
    window.BlackCnoteLiveEditing = {
        
        // Configuration
        config: {
            enabled: false,
            autoSave: true,
            autoSaveDelay: 2000,
            syncInterval: 5000,
            debug: false
        },
        
        // State
        state: {
            editing: false,
            pendingChanges: [],
            lastSync: null,
            connected: false
        },
        
        // Elements
        elements: {
            editable: '[data-live-edit]',
            styleEditable: '[data-style-edit]',
            componentEditable: '[data-component-edit]'
        },
        
        // Initialize the live editing system
        init: function() {
            this.config.enabled = blackcnote_ajax.is_development;
            this.config.debug = blackcnote_ajax.is_development;
            
            if (!this.config.enabled) {
                return;
            }
            
            this.log('Initializing BlackCnote Live Editing System...');
            
            // Setup event listeners
            this.setupEventListeners();
            
            // Initialize connections
            this.initializeConnections();
            
            // Setup auto-save
            if (this.config.autoSave) {
                this.setupAutoSave();
            }
            
            // Setup sync timer
            this.setupSyncTimer();
            
            // Add development indicators
            this.addDevelopmentIndicators();
            
            this.log('Live Editing System initialized successfully');
        },
        
        // Setup event listeners
        setupEventListeners: function() {
            var self = this;
            
            // Content editing
            $(document).on('click', this.elements.editable, function(e) {
                if (self.state.editing) return;
                self.startContentEditing($(this));
            });
            
            $(document).on('blur', this.elements.editable, function(e) {
                self.stopContentEditing($(this));
            });
            
            // Style editing
            $(document).on('change', this.elements.styleEditable, function(e) {
                self.handleStyleChange($(this));
            });
            
            // Component editing
            $(document).on('click', this.elements.componentEditable, function(e) {
                self.handleComponentEdit($(this));
            });
            
            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                self.handleKeyboardShortcuts(e);
            });
            
            // Window events
            $(window).on('beforeunload', function(e) {
                if (self.state.pendingChanges.length > 0) {
                    return 'You have unsaved changes. Are you sure you want to leave?';
                }
            });
            
            $(window).on('focus', function() {
                self.onWindowFocus();
            });
            
            $(window).on('blur', function() {
                self.onWindowBlur();
            });
        },
        
        // Initialize connections
        initializeConnections: function() {
            this.checkWordPressConnection();
            this.checkReactConnection();
            this.checkBrowsersyncConnection();
        },
        
        // Check WordPress connection
        checkWordPressConnection: function() {
            var self = this;
            $.ajax({
                url: blackcnote_ajax.rest_url + 'blackcnote/v1/health',
                method: 'GET',
                timeout: 5000,
                success: function(response) {
                    self.state.connected = true;
                    self.log('WordPress connection established');
                },
                error: function() {
                    self.state.connected = false;
                    self.log('WordPress connection failed', 'error');
                }
            });
        },
        
        // Check React connection
        checkReactConnection: function() {
            var self = this;
            $.ajax({
                url: blackcnote_ajax.canonical_paths.react_url,
                method: 'GET',
                timeout: 5000,
                success: function() {
                    self.log('React connection established');
                },
                error: function() {
                    self.log('React connection failed', 'warn');
                }
            });
        },
        
        // Check Browsersync connection
        checkBrowsersyncConnection: function() {
            var self = this;
            $.ajax({
                url: blackcnote_ajax.canonical_paths.browsersync_url,
                method: 'GET',
                timeout: 5000,
                success: function() {
                    self.log('Browsersync connection established');
                },
                error: function() {
                    self.log('Browsersync connection failed', 'warn');
                }
            });
        },
        
        // Start content editing
        startContentEditing: function($element) {
            if (this.state.editing) return;
            
            this.state.editing = true;
            $element.attr('contenteditable', 'true');
            $element.addClass('live-editing-active');
            $element.focus();
            
            // Store original content
            $element.data('original-content', $element.html());
            
            this.log('Started editing: ' + $element.attr('data-live-edit'));
        },
        
        // Stop content editing
        stopContentEditing: function($element) {
            if (!this.state.editing) return;
            
            this.state.editing = false;
            $element.attr('contenteditable', 'false');
            $element.removeClass('live-editing-active');
            
            var newContent = $element.html();
            var originalContent = $element.data('original-content');
            
            if (newContent !== originalContent) {
                this.saveContentChange($element.attr('data-live-edit'), newContent);
            }
            
            this.log('Stopped editing: ' + $element.attr('data-live-edit'));
        },
        
        // Handle style change
        handleStyleChange: function($element) {
            var property = $element.attr('data-style-property');
            var value = $element.val();
            
            // Update CSS custom property
            document.documentElement.style.setProperty('--' + property, value);
            
            // Save style change
            this.saveStyleChange(property, value);
            
            this.log('Style changed: ' + property + ' = ' + value);
        },
        
        // Handle component edit
        handleComponentEdit: function($element) {
            var componentName = $element.attr('data-component');
            var componentData = $element.data('component-data') || {};
            
            // Show component editor (could be a modal or sidebar)
            this.showComponentEditor(componentName, componentData);
        },
        
        // Save content change
        saveContentChange: function(id, content) {
            var change = {
                id: id,
                content: content,
                type: 'content',
                timestamp: new Date().toISOString()
            };
            
            this.state.pendingChanges.push(change);
            
            // Send to WordPress immediately
            this.sendChangeToWordPress(change);
            
            this.log('Content change saved: ' + id);
        },
        
        // Save style change
        saveStyleChange: function(property, value) {
            var change = {
                property: property,
                value: value,
                type: 'style',
                timestamp: new Date().toISOString()
            };
            
            this.state.pendingChanges.push(change);
            
            // Send to WordPress immediately
            this.sendStyleChangeToWordPress(change);
            
            this.log('Style change saved: ' + property);
        },
        
        // Send change to WordPress
        sendChangeToWordPress: function(change) {
            var self = this;
            $.ajax({
                url: blackcnote_ajax.rest_url + 'blackcnote/v1/content/' + change.id,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': blackcnote_ajax.rest_nonce
                },
                data: JSON.stringify(change),
                success: function(response) {
                    self.log('Change sent to WordPress: ' + change.id);
                    self.removePendingChange(change);
                },
                error: function(xhr, status, error) {
                    self.log('Failed to send change to WordPress: ' + error, 'error');
                }
            });
        },
        
        // Send style change to WordPress
        sendStyleChangeToWordPress: function(change) {
            var self = this;
            var styles = {};
            styles[change.property] = change.value;
            
            $.ajax({
                url: blackcnote_ajax.rest_url + 'blackcnote/v1/styles',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': blackcnote_ajax.rest_nonce
                },
                data: JSON.stringify({ styles: styles }),
                success: function(response) {
                    self.log('Style change sent to WordPress: ' + change.property);
                    self.removePendingChange(change);
                },
                error: function(xhr, status, error) {
                    self.log('Failed to send style change to WordPress: ' + error, 'error');
                }
            });
        },
        
        // Remove pending change
        removePendingChange: function(change) {
            var index = this.state.pendingChanges.indexOf(change);
            if (index > -1) {
                this.state.pendingChanges.splice(index, 1);
            }
        },
        
        // Setup auto-save
        setupAutoSave: function() {
            var self = this;
            setInterval(function() {
                if (self.state.pendingChanges.length > 0) {
                    self.autoSave();
                }
            }, this.config.autoSaveDelay);
        },
        
        // Auto-save
        autoSave: function() {
            if (this.state.pendingChanges.length === 0) return;
            
            this.log('Auto-saving ' + this.state.pendingChanges.length + ' changes...');
            
            // Send all pending changes
            this.state.pendingChanges.forEach(function(change) {
                if (change.type === 'content') {
                    this.sendChangeToWordPress(change);
                } else if (change.type === 'style') {
                    this.sendStyleChangeToWordPress(change);
                }
            }.bind(this));
        },
        
        // Setup sync timer
        setupSyncTimer: function() {
            var self = this;
            setInterval(function() {
                self.syncWithWordPress();
            }, this.config.syncInterval);
        },
        
        // Sync with WordPress
        syncWithWordPress: function() {
            var self = this;
            $.ajax({
                url: blackcnote_ajax.rest_url + 'blackcnote/v1/files/changes',
                method: 'GET',
                headers: {
                    'X-WP-Nonce': blackcnote_ajax.rest_nonce
                },
                success: function(response) {
                    self.handleWordPressChanges(response);
                },
                error: function() {
                    // Silently fail for sync
                }
            });
        },
        
        // Handle WordPress changes
        handleWordPressChanges: function(changes) {
            if (!changes || changes.length === 0) return;
            
            changes.forEach(function(change) {
                this.applyWordPressChange(change);
            }.bind(this));
            
            this.state.lastSync = new Date().toISOString();
        },
        
        // Apply WordPress change
        applyWordPressChange: function(change) {
            if (change.type === 'content') {
                var $element = $('[data-live-edit="' + change.id + '"]');
                if ($element.length > 0) {
                    $element.html(change.content);
                }
            } else if (change.type === 'style') {
                // Apply style changes
                if (change.styles) {
                    Object.keys(change.styles).forEach(function(property) {
                        document.documentElement.style.setProperty('--' + property, change.styles[property]);
                    });
                }
            }
        },
        
        // Show component editor
        showComponentEditor: function(componentName, componentData) {
            // This could open a modal or sidebar with component editing interface
            this.log('Opening component editor for: ' + componentName);
            
            // For now, just show an alert
            alert('Component Editor: ' + componentName + '\nThis would open a full editing interface.');
        },
        
        // Handle keyboard shortcuts
        handleKeyboardShortcuts: function(e) {
            // Ctrl+S: Save all changes
            if (e.ctrlKey && e.keyCode === 83) {
                e.preventDefault();
                this.autoSave();
                this.log('Manual save triggered');
            }
            
            // Ctrl+Shift+R: Reload page
            if (e.ctrlKey && e.shiftKey && e.keyCode === 82) {
                e.preventDefault();
                window.location.reload();
            }
            
            // Ctrl+Shift+D: Toggle development info
            if (e.ctrlKey && e.shiftKey && e.keyCode === 68) {
                e.preventDefault();
                this.toggleDevelopmentInfo();
            }
        },
        
        // Window focus
        onWindowFocus: function() {
            this.log('Window focused - checking for changes');
            this.syncWithWordPress();
        },
        
        // Window blur
        onWindowBlur: function() {
            this.log('Window blurred - auto-saving changes');
            this.autoSave();
        },
        
        // Add development indicators
        addDevelopmentIndicators: function() {
            // Add development banner
            var $banner = $('<div class="blackcnote-dev-banner">' +
                '<span>🛠️ BlackCnote Development Mode</span>' +
                '<span class="dev-status">Live Editing: Active</span>' +
                '<span class="dev-connections">' +
                '<span class="wp-conn">WP</span>' +
                '<span class="react-conn">React</span>' +
                '<span class="bs-conn">BS</span>' +
                '</span>' +
                '</div>');
            
            $('body').prepend($banner);
            
            // Add context menu
            this.addContextMenu();
        },
        
        // Add context menu
        addContextMenu: function() {
            var self = this;
            $(document).on('contextmenu', function(e) {
                if (!self.config.enabled) return;
                
                e.preventDefault();
                self.showContextMenu(e.pageX, e.pageY);
            });
        },
        
        // Show context menu
        showContextMenu: function(x, y) {
            var $menu = $('<div class="blackcnote-context-menu">' +
                '<div class="menu-item" data-action="reload">🔄 Reload Page</div>' +
                '<div class="menu-item" data-action="clear-cache">🗑️ Clear Cache</div>' +
                '<div class="menu-item" data-action="open-react">⚛️ Open React App</div>' +
                '<div class="menu-item" data-action="open-browsersync">🔄 Open Browsersync</div>' +
                '<div class="menu-item" data-action="toggle-dev-info">ℹ️ Toggle Dev Info</div>' +
                '</div>');
            
            $menu.css({
                position: 'fixed',
                left: x,
                top: y,
                zIndex: 10000
            });
            
            $('body').append($menu);
            
            // Handle menu clicks
            $menu.on('click', '.menu-item', function() {
                var action = $(this).data('action');
                self.handleContextMenuAction(action);
                $menu.remove();
            });
            
            // Remove menu on click outside
            $(document).one('click', function() {
                $menu.remove();
            });
        },
        
        // Handle context menu action
        handleContextMenuAction: function(action) {
            switch (action) {
                case 'reload':
                    window.location.reload();
                    break;
                case 'clear-cache':
                    this.clearCache();
                    break;
                case 'open-react':
                    window.open(blackcnote_ajax.canonical_paths.react_url, '_blank');
                    break;
                case 'open-browsersync':
                    window.open(blackcnote_ajax.canonical_paths.browsersync_url, '_blank');
                    break;
                case 'toggle-dev-info':
                    this.toggleDevelopmentInfo();
                    break;
            }
        },
        
        // Clear cache
        clearCache: function() {
            var self = this;
            $.ajax({
                url: blackcnote_ajax.rest_url + 'blackcnote/v1/dev/clear-cache',
                method: 'POST',
                headers: {
                    'X-WP-Nonce': blackcnote_ajax.rest_nonce
                },
                success: function(response) {
                    self.log('Cache cleared successfully');
                    window.location.reload();
                },
                error: function() {
                    self.log('Failed to clear cache', 'error');
                }
            });
        },
        
        // Toggle development info
        toggleDevelopmentInfo: function() {
            $('.blackcnote-dev-banner').toggle();
            this.log('Development info toggled');
        },
        
        // Log function
        log: function(message, level) {
            if (!this.config.debug) return;
            
            level = level || 'info';
            var timestamp = new Date().toISOString();
            var logMessage = '[BlackCnote Live Editing] ' + message;
            
            switch (level) {
                case 'error':
                    console.error(logMessage);
                    break;
                case 'warn':
                    console.warn(logMessage);
                    break;
                default:
                    console.log(logMessage);
            }
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        window.BlackCnoteLiveEditing.init();
    });
    
})(jQuery); 