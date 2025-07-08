/**
 * React Router Configuration for BlackCnote
 * Handles basename conflicts between WordPress and React Router
 */

// Type declarations for WordPress and BlackCnote globals
declare global {
  interface Window {
    wp?: any;
    wpApiSettings?: any;
    blackCnoteApiSettings?: any;
    blackCnoteRouterBasename?: string;
  }
}

export interface RouterConfig {
  basename: string;
  isWordPress: boolean;
  isDevelopment: boolean;
  apiBaseUrl: string;
  environment: 'wordpress' | 'development' | 'production';
}

/**
 * Detect the correct basename for React Router
 */
export function detectBasename(): string {
  // Check if we're running in WordPress context
  if (typeof window !== 'undefined' && window.blackCnoteRouterBasename) {
    return window.blackCnoteRouterBasename;
  }
  
  // Check if we're in WordPress admin or frontend
  const pathname = window.location.pathname;
  const hostname = window.location.hostname;
  const port = window.location.port;
  
  // WordPress environment detection
  if (hostname === 'localhost' && port === '8888') {
    // WordPress frontend - no basename needed
    return '';
  }
  
  // React development server
  if (hostname === 'localhost' && (port === '5174' || port === '5176')) {
    return '';
  }
  
  // Production WordPress integration
  if (pathname.includes('/wp-content/themes/blackcnote/')) {
    return '/wp-content/themes/blackcnote';
  }
  
  // Default to no basename
  return '';
}

/**
 * Detect if running in WordPress environment
 */
export function detectWordPressEnvironment(): boolean {
  if (typeof window === 'undefined') return false;
  
  // Check for WordPress-specific globals
  if (window.wp || window.wpApiSettings) {
    return true;
  }
  
  // Check URL patterns
  const pathname = window.location.pathname;
  if (pathname.includes('/wp-admin/') || pathname.includes('/wp-content/')) {
    return true;
  }
  
  // Check for BlackCnote specific indicators
  if (window.blackCnoteApiSettings || window.blackCnoteRouterBasename) {
    return true;
  }
  
  return false;
}

/**
 * Get API base URL
 */
export function getApiBaseUrl(): string {
  if (typeof window === 'undefined') return '';
  
  // WordPress environment
  if (detectWordPressEnvironment()) {
    return '/wp-json';
  }
  
  // Development environment
  const hostname = window.location.hostname;
  const port = window.location.port;
  
  if (hostname === 'localhost' && (port === '5174' || port === '5176')) {
    return 'http://localhost:8888/wp-json';
  }
  
  // Production fallback
  return '/wp-json';
}

/**
 * Get router configuration
 */
export function getRouterConfig(): RouterConfig {
  const isWordPress = detectWordPressEnvironment();
  const basename = detectBasename();
  const apiBaseUrl = getApiBaseUrl();
  
  return {
    basename,
    isWordPress,
    isDevelopment: process.env.NODE_ENV === 'development',
    apiBaseUrl,
    environment: isWordPress ? 'wordpress' : (process.env.NODE_ENV === 'development' ? 'development' : 'production')
  };
}

/**
 * Enhanced basename resolver with conflict detection
 */
export function resolveBasenameConflicts(): string {
  const config = getRouterConfig();
  
  // WordPress integration - no basename conflicts
  if (config.isWordPress) {
    return '';
  }
  
  // Development server - check for port conflicts
  if (config.isDevelopment) {
    const port = window.location.port;
    
    // React dev server on canonical port
    if (port === '5174') {
      return '';
    }
    
    // Fallback port
    if (port === '5176') {
      return '';
    }
  }
  
  return config.basename;
}

/**
 * Router configuration for different environments
 */
export const routerConfigs = {
  development: {
    basename: '',
    strictMode: true,
    future: {
      v7_startTransition: true,
    }
  },
  wordpress: {
    basename: '',
    strictMode: false, // Disable strict mode in WordPress to avoid double rendering
    future: {
      v7_startTransition: false,
    }
  },
  production: {
    basename: '',
    strictMode: false,
    future: {
      v7_startTransition: true,
    }
  }
};

/**
 * Get environment-specific router configuration
 */
export function getEnvironmentRouterConfig() {
  const config = getRouterConfig();
  
  switch (config.environment) {
    case 'wordpress':
      return routerConfigs.wordpress;
    case 'development':
      return routerConfigs.development;
    case 'production':
      return routerConfigs.production;
    default:
      return routerConfigs.development;
  }
}

/**
 * Debug router configuration
 */
export function debugRouterConfig(): void {
  if (process.env.NODE_ENV === 'development') {
    const config = getRouterConfig();
    console.log('🔧 BlackCnote Router Configuration:', {
      basename: config.basename,
      isWordPress: config.isWordPress,
      environment: config.environment,
      apiBaseUrl: config.apiBaseUrl,
      currentPath: window.location.pathname,
      currentPort: window.location.port,
      canonicalPath: process.env.CANONICAL_ROOT || 'Not set'
    });
  }
}

// Export default configuration
export const defaultRouterConfig = getRouterConfig();

/**
 * Create router configuration for App component
 * This function is imported by App.tsx
 */
export function createRouterConfig() {
  const config = getRouterConfig();
  
  return {
    basename: config.basename,
    strictMode: config.environment === 'development',
    future: {
      v7_startTransition: config.environment === 'development',
    }
  };
} 