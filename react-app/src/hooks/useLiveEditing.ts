/**
 * BlackCnote Live Editing Hook
 * React hook for real-time content editing and synchronization
 *
 * @package BlackCnote
 * @since 1.0.0
 */

import { useState, useEffect, useCallback, useRef } from 'react';
import LiveSyncService, { 
  LiveSyncConfig, 
  defaultLiveSyncConfig,
  ContentChange,
  StyleChange,
  ComponentChange,
  SyncStatus,
  ServiceHealth
} from '../services/LiveSyncService';

export interface UseLiveEditingOptions {
  enabled?: boolean;
  autoSave?: boolean;
  syncInterval?: number;
  debug?: boolean;
  onChange?: (change: any) => void;
}

export interface LiveEditingState {
  isEditing: boolean;
  pendingChanges: number;
  connected: boolean;
  lastSync: string | null;
  errors: string[];
  health: ServiceHealth;
}

export interface LiveEditingActions {
  startEditing: (id: string) => void;
  stopEditing: (id: string, content: string) => void;
  updateStyle: (property: string, value: string) => void;
  updateComponent: (name: string, data: any) => void;
  saveChanges: () => Promise<void>;
  clearCache: () => Promise<void>;
  buildReact: () => Promise<void>;
  getGitStatus: () => Promise<any>;
  gitSync: (message: string) => Promise<void>;
  getHealth: () => Promise<ServiceHealth>;
}

export function useLiveEditing(options: UseLiveEditingOptions = {}): [LiveEditingState, LiveEditingActions] {
  const {
    enabled = true,
    autoSave = true,
    syncInterval = 5000,
    debug = process.env.NODE_ENV === 'development',
    onChange
  } = options;

  const [state, setState] = useState<LiveEditingState>({
    isEditing: false,
    pendingChanges: 0,
    connected: false,
    lastSync: null,
    errors: [],
    health: {
      wordpress: false,
      react: false,
      browsersync: false,
      phpmyadmin: false,
      mailhog: false
    }
  });

  const serviceRef = useRef<LiveSyncService | null>(null);
  const editingIdRef = useRef<string | null>(null);

  // Initialize service
  useEffect(() => {
    if (!enabled) return;

    const config: LiveSyncConfig = {
      ...defaultLiveSyncConfig,
      autoSave,
      syncInterval,
      debug
    };

    serviceRef.current = new LiveSyncService(config);

    // Setup event listeners
    const service = serviceRef.current;

    service.on('connection', (data: { connected: boolean; error?: any }) => {
      setState(prev => ({
        ...prev,
        connected: data.connected,
        errors: data.error ? [...prev.errors, data.error.toString()] : prev.errors
      }));
    });

    service.on('sync', (data: { success: boolean; changes?: any[]; error?: any }) => {
      setState(prev => ({
        ...prev,
        lastSync: data.success ? new Date().toISOString() : prev.lastSync,
        errors: data.error ? [...prev.errors, data.error.toString()] : prev.errors
      }));
    });

    service.on('change-added', () => {
      const status = service.getStatus();
      setState(prev => ({
        ...prev,
        pendingChanges: status.pendingChanges
      }));
    });

    service.on('auto-save', (data: { success: boolean; count: number; error?: any }) => {
      if (data.success) {
        setState(prev => ({
          ...prev,
          pendingChanges: 0
        }));
      } else if (data.error) {
        setState(prev => ({
          ...prev,
          errors: [...prev.errors, data.error.toString()]
        }));
      }
    });

    service.on('wordpress-change', (change: any) => {
      // Handle changes from WordPress
      if (debug) {
        console.log('[useLiveEditing] WordPress change received:', change);
      }
      if (onChange) {
        onChange(change);
      }
    });

    // Initial health check
    service.getHealth().then((health: ServiceHealth) => {
      setState(prev => ({ ...prev, health }));
    });

    return () => {
      service.destroy();
      serviceRef.current = null;
    };
  }, [enabled, autoSave, syncInterval, debug, onChange]);

  // Periodic health check
  useEffect(() => {
    if (!enabled || !serviceRef.current) return;

    const interval = setInterval(async () => {
      try {
        const health = await serviceRef.current!.getHealth();
        setState(prev => ({ ...prev, health }));
      } catch (error) {
        if (debug) {
          console.error('[useLiveEditing] Health check failed:', error);
        }
      }
    }, 30000); // Check every 30 seconds

    return () => clearInterval(interval);
  }, [enabled, debug]);

  // Actions
  const startEditing = useCallback((id: string) => {
    if (!serviceRef.current) return;

    setState(prev => ({ ...prev, isEditing: true }));
    editingIdRef.current = id;

    if (debug) {
      console.log('[useLiveEditing] Started editing:', id);
    }
  }, [debug]);

  const stopEditing = useCallback((id: string, content: string) => {
    if (!serviceRef.current) return;

    setState(prev => ({ ...prev, isEditing: false }));
    editingIdRef.current = null;

    // Add content change
    serviceRef.current.addContentChange(id, content);

    if (debug) {
      console.log('[useLiveEditing] Stopped editing:', id);
    }
  }, [debug]);

  const updateStyle = useCallback((property: string, value: string) => {
    if (!serviceRef.current) return;

    // Update CSS custom property immediately
    document.documentElement.style.setProperty(`--${property}`, value);

    // Add style change
    serviceRef.current.addStyleChange(property, value);

    if (debug) {
      console.log('[useLiveEditing] Style updated:', property, '=', value);
    }
  }, [debug]);

  const updateComponent = useCallback((name: string, data: any) => {
    if (!serviceRef.current) return;

    // Add component change
    serviceRef.current.addComponentChange(name, data);

    if (debug) {
      console.log('[useLiveEditing] Component updated:', name);
    }
  }, [debug]);

  const saveChanges = useCallback(async () => {
    if (!serviceRef.current) return;

    try {
      // Trigger auto-save
      await serviceRef.current.autoSave();
      setState(prev => ({ ...prev, pendingChanges: 0 }));
    } catch (error) {
      setState(prev => ({
        ...prev,
        errors: [...prev.errors, `Save failed: ${error}`]
      }));
    }
  }, []);

  const clearCache = useCallback(async () => {
    if (!serviceRef.current) return;

    try {
      await serviceRef.current.clearCache();
      if (debug) {
        console.log('[useLiveEditing] Cache cleared');
      }
    } catch (error) {
      setState(prev => ({
        ...prev,
        errors: [...prev.errors, `Cache clear failed: ${error}`]
      }));
    }
  }, [debug]);

  const buildReact = useCallback(async () => {
    if (!serviceRef.current) return;

    try {
      await serviceRef.current.buildReact();
      if (debug) {
        console.log('[useLiveEditing] React build completed');
      }
    } catch (error) {
      setState(prev => ({
        ...prev,
        errors: [...prev.errors, `React build failed: ${error}`]
      }));
    }
  }, [debug]);

  const getGitStatus = useCallback(async () => {
    if (!serviceRef.current) return null;

    try {
      return await serviceRef.current.getGitStatus();
    } catch (error) {
      setState(prev => ({
        ...prev,
        errors: [...prev.errors, `Git status failed: ${error}`]
      }));
      return null;
    }
  }, []);

  const gitSync = useCallback(async (message: string) => {
    if (!serviceRef.current) return;

    try {
      await serviceRef.current.gitSync(message);
      if (debug) {
        console.log('[useLiveEditing] Git sync completed');
      }
    } catch (error) {
      setState(prev => ({
        ...prev,
        errors: [...prev.errors, `Git sync failed: ${error}`]
      }));
    }
  }, [debug]);

  const getHealth = useCallback(async () => {
    if (!serviceRef.current) return state.health;

    try {
      const health = await serviceRef.current.getHealth();
      setState(prev => ({ ...prev, health }));
      return health;
    } catch (error) {
      if (debug) {
        console.error('[useLiveEditing] Health check failed:', error);
      }
      return state.health;
    }
  }, [state.health, debug]);

  const actions: LiveEditingActions = {
    startEditing,
    stopEditing,
    updateStyle,
    updateComponent,
    saveChanges,
    clearCache,
    buildReact,
    getGitStatus,
    gitSync,
    getHealth
  };

  return [state, actions];
}

// Hook for content editing
export function useContentEditing(id: string, initialContent: string = '') {
  const [content, setContent] = useState(initialContent);
  const [isEditing, setIsEditing] = useState(false);
  const [liveState, liveActions] = useLiveEditing();

  const startEditing = useCallback(() => {
    setIsEditing(true);
    liveActions.startEditing(id);
  }, [id, liveActions]);

  const stopEditing = useCallback(() => {
    setIsEditing(false);
    liveActions.stopEditing(id, content);
  }, [id, content, liveActions]);

  const updateContent = useCallback((newContent: string) => {
    setContent(newContent);
  }, []);

  return {
    content,
    isEditing,
    updateContent,
    startEditing,
    stopEditing,
    liveState,
    liveActions
  };
}

// Hook for style editing
export function useStyleEditing() {
  const [liveState, liveActions] = useLiveEditing();

  const updateStyle = useCallback((property: string, value: string) => {
    liveActions.updateStyle(property, value);
  }, [liveActions]);

  return {
    updateStyle,
    liveState,
    liveActions
  };
}

// Hook for component editing
export function useComponentEditing() {
  const [liveState, liveActions] = useLiveEditing();

  const updateComponent = useCallback((name: string, data: any) => {
    liveActions.updateComponent(name, data);
  }, [liveActions]);

  return {
    updateComponent,
    liveState,
    liveActions
  };
}

export default useLiveEditing; 