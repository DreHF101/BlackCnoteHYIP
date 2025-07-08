/**
 * React Debug Logger
 * 
 * Provides React-specific logging that integrates with the WordPress debug system
 * and avoids conflicts with development console logs.
 */

import { isDebugEnabled } from '../config/environment';

interface LogEntry {
  level: 'debug' | 'info' | 'warn' | 'error';
  message: string;
  data?: unknown;
  component?: string;
  timestamp: string;
  url: string;
}

class ReactDebugLogger {
  private logBuffer: LogEntry[] = [];
  private maxBufferSize = 100;
  private flushInterval: number | null = null;
  private logger: Record<string, (...args: unknown[]) => void> = {};

  constructor() {
    // Start periodic flushing if debug is enabled
    if (isDebugEnabled()) {
      this.startPeriodicFlush();
    }
  }

  /**
   * Log a debug message
   */
  debug(message: string, data?: unknown, component?: string) {
    if (process.env.NODE_ENV === 'development') {
      this.log('debug', message, data, component);
    }
  }

  /**
   * Log an info message
   */
  info(message: string, data?: unknown, component?: string) {
    if (process.env.NODE_ENV === 'development') {
      this.log('info', message, data, component);
    }
  }

  /**
   * Log a warning message
   */
  warn(message: string, data?: unknown, component?: string) {
    if (process.env.NODE_ENV === 'development') {
      this.log('warn', message, data, component);
    }
  }

  /**
   * Log an error message
   */
  error(message: string, data?: unknown, component?: string) {
    if (process.env.NODE_ENV === 'development') {
      this.log('error', message, data, component);
    }
  }

  /**
   * Internal logging method
   */
  private log(level: 'debug' | 'info' | 'warn' | 'error', message: string, data?: unknown, component?: string) {
    const entry: LogEntry = {
      level,
      message,
      data,
      component,
      timestamp: new Date().toISOString(),
      url: window.location.href
    };

    // Add to buffer
    this.logBuffer.push(entry);

    // Keep buffer size manageable
    if (this.logBuffer.length > this.maxBufferSize) {
      this.logBuffer.shift();
    }

    // Send to WordPress debug system if available
    this.sendToWordPressDebug(entry);

    // Also log to console in development (but with prefix to distinguish from React logs)
    if (import.meta.env.DEV) {
      const prefix = '[React Debug]';
      const componentPrefix = component ? `[${component}]` : '';
      
      switch (level) {
        case 'debug':
          console.log(`${prefix}${componentPrefix}`, message, data);
          break;
        case 'info':
          console.info(`${prefix}${componentPrefix}`, message, data);
          break;
        case 'warn':
          console.warn(`${prefix}${componentPrefix}`, message, data);
          break;
        case 'error':
          console.error(`${prefix}${componentPrefix}`, message, data);
          break;
      }
    }
  }

  /**
   * Send log entry to WordPress debug system
   */
  private sendToWordPressDebug(entry: LogEntry) {
    // Check if WordPress debug system is available
    if (typeof window !== 'undefined' && (window as { blackcnoteDebug?: { enabled?: boolean; logger?: Record<string, Function> } }).blackcnoteDebug?.logger) {
      try {
        const dataToSend = typeof entry.data === 'object' && entry.data !== null ? entry.data : {};
        (window as { blackcnoteDebug?: { enabled?: boolean; logger?: Record<string, Function> } }).blackcnoteDebug?.logger?.[entry.level]?.(
          `[React] ${entry.message}`,
          {
            ...dataToSend,
            component: entry.component,
            timestamp: entry.timestamp,
            url: entry.url
          }
        );
      } catch (error) {
        // Fallback to console if WordPress debug fails
        console.error('Failed to send to WordPress debug:', error);
      }
    }
  }

  /**
   * Start periodic flushing of log buffer
   */
  private startPeriodicFlush() {
    if (this.flushInterval) {
      clearInterval(this.flushInterval);
    }

    this.flushInterval = window.setInterval(() => {
      this.flushBuffer();
    }, 5000); // Flush every 5 seconds
  }

  /**
   * Flush the log buffer
   */
  private flushBuffer() {
    if (this.logBuffer.length === 0) return;

    // Send all buffered logs to WordPress debug system
    this.logBuffer.forEach(entry => {
      this.sendToWordPressDebug(entry);
    });

    // Clear buffer
    this.logBuffer = [];
  }

  /**
   * Get all buffered logs
   */
  getLogs(): LogEntry[] {
    return [...this.logBuffer];
  }

  /**
   * Clear the log buffer
   */
  clearLogs() {
    this.logBuffer = [];
  }

  /**
   * Get log statistics
   */
  getStats() {
    const stats = {
      total: this.logBuffer.length,
      debug: 0,
      info: 0,
      warn: 0,
      error: 0
    };

    this.logBuffer.forEach(entry => {
      stats[entry.level]++;
    });

    return stats;
  }

  /**
   * Destroy the logger and clean up
   */
  destroy() {
    if (this.flushInterval) {
      clearInterval(this.flushInterval);
      this.flushInterval = null;
    }
    this.flushBuffer();
  }
}

// Create singleton instance
const reactDebugLogger = new ReactDebugLogger();

// Export the logger instance
export default reactDebugLogger;

// Export individual methods for convenience
export const debug = (message: string, data?: unknown, component?: string) => 
  reactDebugLogger.debug(message, data, component);

export const info = (message: string, data?: unknown, component?: string) => 
  reactDebugLogger.info(message, data, component);

export const warn = (message: string, data?: unknown, component?: string) => 
  reactDebugLogger.warn(message, data, component);

export const error = (message: string, data?: unknown, component?: string) => 
  reactDebugLogger.error(message, data, component);

// Export utility functions
export const getLogs = () => reactDebugLogger.getLogs();
export const clearLogs = () => reactDebugLogger.clearLogs();
export const getStats = () => reactDebugLogger.getStats(); 