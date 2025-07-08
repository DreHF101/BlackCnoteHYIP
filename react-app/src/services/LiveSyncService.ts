/**
 * BlackCnote Live Sync Service
 * Real-time synchronization between React and WordPress
 *
 * @package BlackCnote
 * @since 1.0.0
 */

export interface LiveSyncConfig {
  wordpressUrl: string;
  restUrl: string;
  syncInterval: number;
  autoSave: boolean;
  debug: boolean;
}

export interface ContentChange {
  id: string;
  content: string;
  type: 'content' | 'style' | 'component';
  timestamp: string;
}

export interface StyleChange {
  property: string;
  value: string;
  timestamp: string;
}

export interface ComponentChange {
  name: string;
  data: any;
  timestamp: string;
}

export interface SyncStatus {
  connected: boolean;
  lastSync: string | null;
  pendingChanges: number;
  errors: string[];
}

export interface ServiceHealth {
  wordpress: boolean;
  react: boolean;
  browsersync: boolean;
  phpmyadmin: boolean;
  mailhog: boolean;
}

class LiveSyncService {
  private config: LiveSyncConfig;
  private syncInterval: NodeJS.Timeout | null = null;
  private status: SyncStatus = {
    connected: false,
    lastSync: null,
    pendingChanges: 0,
    errors: []
  };
  private listeners: Map<string, Function[]> = new Map();
  private pendingChanges: (ContentChange | StyleChange | ComponentChange)[] = [];

  constructor(config: LiveSyncConfig) {
    this.config = config;
    this.initialize();
  }

  /**
   * Initialize the live sync service
   */
  private initialize(): void {
    if (this.config.debug) {
      console.log('[LiveSyncService] Initializing...');
    }

    // Check initial connection
    this.checkWordPressConnection();

    // Setup sync interval
    if (this.config.syncInterval > 0) {
      this.syncInterval = setInterval(() => {
        this.syncWithWordPress();
      }, this.config.syncInterval);
    }

    // Setup auto-save
    if (this.config.autoSave) {
      this.setupAutoSave();
    }

    // Setup window events
    this.setupWindowEvents();

    if (this.config.debug) {
      console.log('[LiveSyncService] Initialized successfully');
    }
  }

  /**
   * Check WordPress connection
   */
  private async checkWordPressConnection(): Promise<void> {
    try {
      const response = await fetch(`${this.config.restUrl}blackcnote/v1/health`);
      if (response.ok) {
        this.status.connected = true;
        this.emit('connection', { connected: true });
        if (this.config.debug) {
          console.log('[LiveSyncService] WordPress connection established');
        }
      } else {
        throw new Error(`HTTP ${response.status}`);
      }
    } catch (error) {
      this.status.connected = false;
      this.status.errors.push(`WordPress connection failed: ${error}`);
      this.emit('connection', { connected: false, error });
      if (this.config.debug) {
        console.error('[LiveSyncService] WordPress connection failed:', error);
      }
    }
  }

  /**
   * Sync with WordPress
   */
  private async syncWithWordPress(): Promise<void> {
    if (!this.status.connected) {
      return;
    }

    try {
      // Get file changes from WordPress
      const response = await fetch(`${this.config.restUrl}blackcnote/v1/files/changes`);
      if (response.ok) {
        const changes = await response.json();
        this.handleWordPressChanges(changes);
        this.status.lastSync = new Date().toISOString();
        this.emit('sync', { success: true, changes });
      }
    } catch (error) {
      this.status.errors.push(`Sync failed: ${error}`);
      this.emit('sync', { success: false, error });
      if (this.config.debug) {
        console.error('[LiveSyncService] Sync failed:', error);
      }
    }
  }

  /**
   * Handle changes from WordPress
   */
  private handleWordPressChanges(changes: any[]): void {
    if (!changes || changes.length === 0) return;

    changes.forEach(change => {
      this.emit('wordpress-change', change);
    });

    if (this.config.debug) {
      console.log('[LiveSyncService] Applied', changes.length, 'changes from WordPress');
    }
  }

  /**
   * Setup auto-save functionality
   */
  private setupAutoSave(): void {
    // Auto-save on window blur
    window.addEventListener('blur', () => {
      this.autoSave();
    });

    // Auto-save on beforeunload
    window.addEventListener('beforeunload', (event) => {
      if (this.pendingChanges.length > 0) {
        event.preventDefault();
        event.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        this.autoSave();
      }
    });
  }

  /**
   * Setup window events
   */
  private setupWindowEvents(): void {
    // Sync on window focus
    window.addEventListener('focus', () => {
      this.syncWithWordPress();
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (event) => {
      // Ctrl+S: Save all changes
      if (event.ctrlKey && event.key === 's') {
        event.preventDefault();
        this.autoSave();
      }

      // Ctrl+Shift+R: Reload page
      if (event.ctrlKey && event.shiftKey && event.key === 'R') {
        event.preventDefault();
        window.location.reload();
      }
    });
  }

  /**
   * Auto-save pending changes
   */
  public async autoSave(): Promise<void> {
    if (this.pendingChanges.length === 0) return;

    if (this.config.debug) {
      console.log('[LiveSyncService] Auto-saving', this.pendingChanges.length, 'changes...');
    }

    const changes = [...this.pendingChanges];
    this.pendingChanges = [];

    for (const change of changes) {
      try {
        await this.sendChangeToWordPress(change);
      } catch (error) {
        this.status.errors.push(`Auto-save failed: ${error}`);
        if (this.config.debug) {
          console.error('[LiveSyncService] Auto-save failed:', error);
        }
      }
    }

    this.status.pendingChanges = this.pendingChanges.length;
    this.emit('auto-save', { success: true, count: changes.length });
  }

  /**
   * Send change to WordPress
   */
  private async sendChangeToWordPress(change: ContentChange | StyleChange | ComponentChange): Promise<void> {
    if (!this.status.connected) {
      throw new Error('Not connected to WordPress');
    }

    let url: string;
    let method: string;
    let data: any;

    if ('type' in change && change.type === 'content' && 'id' in change) {
      // Content change
      url = `${this.config.restUrl}blackcnote/v1/content/${change.id}`;
      method = 'POST';
      data = change;
    } else if ('property' in change && 'value' in change) {
      // Style change
      url = `${this.config.restUrl}blackcnote/v1/styles`;
      method = 'POST';
      data = { styles: { [change.property]: change.value } };
    } else if ('name' in change && 'data' in change) {
      // Component change
      url = `${this.config.restUrl}blackcnote/v1/components`;
      method = 'POST';
      data = { name: change.name, data: change.data };
    } else {
      throw new Error(`Unknown change type: ${JSON.stringify(change)}`);
    }

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': this.getNonce()
      },
      body: JSON.stringify(data)
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    if (this.config.debug) {
      console.log('[LiveSyncService] Change sent to WordPress:', change);
    }
  }

  /**
   * Get WordPress nonce
   */
  private getNonce(): string {
    // Try to get nonce from WordPress global
    if (typeof window !== 'undefined' && (window as any).blackcnote_ajax) {
      return (window as any).blackcnote_ajax.rest_nonce || '';
    }
    return '';
  }

  /**
   * Add content change
   */
  public addContentChange(id: string, content: string): void {
    const change: ContentChange = {
      id,
      content,
      type: 'content',
      timestamp: new Date().toISOString()
    };

    this.pendingChanges.push(change);
    this.status.pendingChanges = this.pendingChanges.length;
    this.emit('change-added', change);

    if (this.config.debug) {
      console.log('[LiveSyncService] Content change added:', id);
    }
  }

  /**
   * Add style change
   */
  public addStyleChange(property: string, value: string): void {
    const change: StyleChange = {
      property,
      value,
      timestamp: new Date().toISOString()
    };

    this.pendingChanges.push(change);
    this.status.pendingChanges = this.pendingChanges.length;
    this.emit('change-added', change);

    if (this.config.debug) {
      console.log('[LiveSyncService] Style change added:', property, '=', value);
    }
  }

  /**
   * Add component change
   */
  public addComponentChange(name: string, data: any): void {
    const change: ComponentChange = {
      name,
      data,
      timestamp: new Date().toISOString()
    };

    this.pendingChanges.push(change);
    this.status.pendingChanges = this.pendingChanges.length;
    this.emit('change-added', change);

    if (this.config.debug) {
      console.log('[LiveSyncService] Component change added:', name);
    }
  }

  /**
   * Get sync status
   */
  public getStatus(): SyncStatus {
    return { ...this.status };
  }

  /**
   * Get service health
   */
  public async getHealth(): Promise<ServiceHealth> {
    try {
      const response = await fetch(`${this.config.restUrl}blackcnote/v1/health`);
      if (response.ok) {
        const health = await response.json();
        return health.services;
      }
    } catch (error) {
      if (this.config.debug) {
        console.error('[LiveSyncService] Health check failed:', error);
      }
    }

    return {
      wordpress: false,
      react: false,
      browsersync: false,
      phpmyadmin: false,
      mailhog: false
    };
  }

  /**
   * Clear cache
   */
  public async clearCache(): Promise<void> {
    try {
      const response = await fetch(`${this.config.restUrl}blackcnote/v1/dev/clear-cache`, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': this.getNonce()
        }
      });

      if (response.ok) {
        this.emit('cache-cleared', { success: true });
        if (this.config.debug) {
          console.log('[LiveSyncService] Cache cleared successfully');
        }
      } else {
        throw new Error(`HTTP ${response.status}`);
      }
    } catch (error) {
      this.emit('cache-cleared', { success: false, error });
      if (this.config.debug) {
        console.error('[LiveSyncService] Cache clear failed:', error);
      }
    }
  }

  /**
   * Build React app
   */
  public async buildReact(): Promise<void> {
    try {
      const response = await fetch(`${this.config.restUrl}blackcnote/v1/dev/build-react`, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': this.getNonce()
        }
      });

      if (response.ok) {
        const result = await response.json();
        this.emit('react-built', { success: true, output: result.output });
        if (this.config.debug) {
          console.log('[LiveSyncService] React build completed');
        }
      } else {
        throw new Error(`HTTP ${response.status}`);
      }
    } catch (error) {
      this.emit('react-built', { success: false, error });
      if (this.config.debug) {
        console.error('[LiveSyncService] React build failed:', error);
      }
    }
  }

  /**
   * Get Git status
   */
  public async getGitStatus(): Promise<any> {
    try {
      const response = await fetch(`${this.config.restUrl}blackcnote/v1/github/status`);
      if (response.ok) {
        return await response.json();
      }
    } catch (error) {
      if (this.config.debug) {
        console.error('[LiveSyncService] Git status failed:', error);
      }
    }

    return { is_repo: false, message: 'Failed to get Git status' };
  }

  /**
   * Git sync (commit + push)
   */
  public async gitSync(message: string): Promise<void> {
    try {
      const response = await fetch(`${this.config.restUrl}blackcnote/v1/github/sync`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': this.getNonce()
        },
        body: JSON.stringify({ message })
      });

      if (response.ok) {
        const result = await response.json();
        this.emit('git-synced', { success: true, result });
        if (this.config.debug) {
          console.log('[LiveSyncService] Git sync completed');
        }
      } else {
        throw new Error(`HTTP ${response.status}`);
      }
    } catch (error) {
      this.emit('git-synced', { success: false, error });
      if (this.config.debug) {
        console.error('[LiveSyncService] Git sync failed:', error);
      }
    }
  }

  /**
   * Add event listener
   */
  public on(event: string, callback: Function): void {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, []);
    }
    this.listeners.get(event)!.push(callback);
  }

  /**
   * Remove event listener
   */
  public off(event: string, callback: Function): void {
    const callbacks = this.listeners.get(event);
    if (callbacks) {
      const index = callbacks.indexOf(callback);
      if (index > -1) {
        callbacks.splice(index, 1);
      }
    }
  }

  /**
   * Emit event
   */
  private emit(event: string, data: any): void {
    const callbacks = this.listeners.get(event);
    if (callbacks) {
      callbacks.forEach(callback => {
        try {
          callback(data);
        } catch (error) {
          if (this.config.debug) {
            console.error('[LiveSyncService] Event callback error:', error);
          }
        }
      });
    }
  }

  /**
   * Destroy the service
   */
  public destroy(): void {
    if (this.syncInterval) {
      clearInterval(this.syncInterval);
      this.syncInterval = null;
    }

    this.listeners.clear();
    this.pendingChanges = [];

    if (this.config.debug) {
      console.log('[LiveSyncService] Destroyed');
    }
  }
}

// Create singleton instance
let liveSyncService: LiveSyncService | null = null;

/**
 * Initialize the live sync service
 */
export function initializeLiveSync(config: LiveSyncConfig): LiveSyncService {
  if (liveSyncService) {
    liveSyncService.destroy();
  }

  liveSyncService = new LiveSyncService(config);
  return liveSyncService;
}

/**
 * Get the live sync service instance
 */
export function getLiveSyncService(): LiveSyncService | null {
  return liveSyncService;
}

/**
 * Default configuration
 */
export const defaultLiveSyncConfig: LiveSyncConfig = {
  wordpressUrl: window.location.origin || 'http://localhost:8888',
  restUrl: (window.location.origin || 'http://localhost:8888') + '/wp-json/',
  syncInterval: 5000,
  autoSave: true,
  debug: process.env.NODE_ENV === 'development'
};

export default LiveSyncService; 