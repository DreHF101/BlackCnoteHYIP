// Environment configuration for BlackCnote Frontend

export interface EnvironmentConfig {
  // App Configuration
  APP_NAME: string;
  APP_ENV: 'development' | 'staging' | 'production';
  APP_URL: string;
  API_BASE_URL: string;
  
  // WordPress Configuration
  WP_API_URL: string;
  WP_NONCE: string;
  
  // Payment Gateway Configuration
  STRIPE_PUBLIC_KEY: string;
  PAYPAL_CLIENT_ID: string;
  COINGATE_ENVIRONMENT: 'sandbox' | 'live';
  
  // Feature Flags
  ENABLE_CRYPTO_PAYMENTS: boolean;
  ENABLE_SMS_VERIFICATION: boolean;
  ENABLE_KYC: boolean;
  
  // Analytics
  GOOGLE_ANALYTICS_ID: string;
  FACEBOOK_PIXEL_ID: string;
  
  // Social Media
  FACEBOOK_APP_ID: string;
  TWITTER_HANDLE: string;
  
  // Support
  SUPPORT_EMAIL: string;
  SUPPORT_PHONE: string;
  LIVE_CHAT_ENABLED: boolean;
  
  // Debug Configuration
  DEBUG_ENABLED: boolean;
  DEBUG_LEVEL: 'debug' | 'info' | 'warn' | 'error' | 'none';
}

// Extend EnvironmentConfig for legacy window.blackCnoteApiSettings
export type BlackCnoteApiSettings = EnvironmentConfig & {
  baseUrl?: string;
  nonce?: string;
  isDevelopment?: boolean;
  homeUrl?: string;
  themeUrl?: string;
  ajaxUrl?: string;
  pluginActive?: boolean;
  themeActive?: boolean;
  userId?: number;
  isLoggedIn?: boolean;
};

// Check for runtime config injected by WordPress
const runtimeConfig = typeof window !== 'undefined' && (window as any).BlackCnoteConfig
  ? (window as any).BlackCnoteConfig
  : null;

// Load environment variables with fallbacks
const getEnvVar = (key: string, defaultValue: string = ''): string => {
  if (runtimeConfig && runtimeConfig[key] !== undefined) return runtimeConfig[key];
  const envKey = `VITE_${key}` as keyof ImportMetaEnv;
  return import.meta.env[envKey] || defaultValue;
};

const getEnvBool = (key: string, defaultValue: boolean = false): boolean => {
  const value = getEnvVar(key);
  return value ? value.toLowerCase() === 'true' : defaultValue;
};

// Detect environment more intelligently
const detectEnvironment = (): 'development' | 'staging' | 'production' => {
  // Check for explicit environment setting
  const explicitEnv = getEnvVar('APP_ENV');
  if (explicitEnv && ['development', 'staging', 'production'].includes(explicitEnv)) {
    return explicitEnv as 'development' | 'staging' | 'production';
  }
  
  // Check for development indicators
  const isDev = import.meta.env.DEV || 
                window.location.hostname === 'localhost' ||
                window.location.hostname === '127.0.0.1' ||
                window.location.port === '3000' ||
                window.location.port === '5174';
  
  // Check for staging indicators
  const isStaging = window.location.hostname.includes('staging') ||
                   window.location.hostname.includes('test') ||
                   window.location.hostname.includes('dev');
  
  if (isDev) return 'development';
  if (isStaging) return 'staging';
  return 'production';
};

// Detect debug settings
const detectDebugSettings = () => {
  // Check if debug is enabled via environment or WordPress
  const debugEnabled = getEnvBool('DEBUG_ENABLED') || 
                      (typeof window !== 'undefined' && 
                       (window as { blackcnoteDebug?: { enabled?: boolean; logger?: Record<string, Function> } }).blackcnoteDebug?.enabled) || false;
  
  // Determine debug level
  let debugLevel: 'debug' | 'info' | 'warn' | 'error' | 'none' = 'none';
  if (debugEnabled) {
    const envLevel = getEnvVar('DEBUG_LEVEL');
    if (['debug', 'info', 'warn', 'error'].includes(envLevel)) {
      debugLevel = envLevel as 'debug' | 'info' | 'warn' | 'error';
    } else {
      // Default debug level based on environment
      const env = detectEnvironment();
      debugLevel = env === 'development' ? 'debug' : 'warn';
    }
  }
  
  return { debugEnabled, debugLevel };
};

// Environment configuration
export const config: EnvironmentConfig = {
  // App Configuration
  APP_NAME: getEnvVar('APP_NAME', 'BlackCnote'),
  APP_ENV: detectEnvironment(),
  APP_URL: getEnvVar('APP_URL', window.location.origin),
  API_BASE_URL: getEnvVar('API_BASE_URL', `${window.location.origin}/wp-json`),
  
  // WordPress Configuration
  WP_API_URL: getEnvVar('WP_API_URL', `${window.location.origin}/wp-json`),
  WP_NONCE: getEnvVar('WP_NONCE', ''),
  
  // Payment Gateway Configuration
  STRIPE_PUBLIC_KEY: getEnvVar('STRIPE_PUBLIC_KEY', ''),
  PAYPAL_CLIENT_ID: getEnvVar('PAYPAL_CLIENT_ID', ''),
  COINGATE_ENVIRONMENT: (getEnvVar('COINGATE_ENVIRONMENT', 'sandbox') as 'sandbox' | 'live'),
  
  // Feature Flags
  ENABLE_CRYPTO_PAYMENTS: getEnvBool('ENABLE_CRYPTO_PAYMENTS', true),
  ENABLE_SMS_VERIFICATION: getEnvBool('ENABLE_SMS_VERIFICATION', false),
  ENABLE_KYC: getEnvBool('ENABLE_KYC', true),
  
  // Analytics
  GOOGLE_ANALYTICS_ID: getEnvVar('GOOGLE_ANALYTICS_ID', ''),
  FACEBOOK_PIXEL_ID: getEnvVar('FACEBOOK_PIXEL_ID', ''),
  
  // Social Media
  FACEBOOK_APP_ID: getEnvVar('FACEBOOK_APP_ID', ''),
  TWITTER_HANDLE: getEnvVar('TWITTER_HANDLE', '@blackcnote'),
  
  // Support
  SUPPORT_EMAIL: getEnvVar('SUPPORT_EMAIL', 'support@blackcnote.com'),
  SUPPORT_PHONE: getEnvVar('SUPPORT_PHONE', '+1-800-BLACKCNOTE'),
  LIVE_CHAT_ENABLED: getEnvBool('LIVE_CHAT_ENABLED', false),
  
  // Debug Configuration
  DEBUG_ENABLED: detectDebugSettings().debugEnabled,
  DEBUG_LEVEL: detectDebugSettings().debugLevel,
};

// Helper functions
export const isDevelopment = (): boolean => config.APP_ENV === 'development';
export const isProduction = (): boolean => config.APP_ENV === 'production';
export const isStaging = (): boolean => config.APP_ENV === 'staging';

// Debug helper functions
export const isDebugEnabled = (): boolean => config.DEBUG_ENABLED;
export const getDebugLevel = (): string => config.DEBUG_LEVEL;
export const shouldLog = (level: 'debug' | 'info' | 'warn' | 'error'): boolean => {
  if (!config.DEBUG_ENABLED) return false;
  
  const levels = ['none', 'error', 'warn', 'info', 'debug'];
  const currentLevel = levels.indexOf(config.DEBUG_LEVEL);
  const messageLevel = levels.indexOf(level);
  
  return messageLevel <= currentLevel;
};

export const getApiUrl = (endpoint: string): string => {
  return `${config.API_BASE_URL}/${endpoint.replace(/^\/+/, '')}`;
};

export const getWpApiUrl = (endpoint: string): string => {
  return `${config.WP_API_URL}/${endpoint.replace(/^\/+/, '')}`;
};

// Feature flag helpers
export const isFeatureEnabled = (feature: keyof Pick<EnvironmentConfig, 'ENABLE_CRYPTO_PAYMENTS' | 'ENABLE_SMS_VERIFICATION' | 'ENABLE_KYC' | 'LIVE_CHAT_ENABLED'>): boolean => {
  return config[feature];
};

// Payment gateway helpers
export const getStripeConfig = () => ({
  publicKey: config.STRIPE_PUBLIC_KEY,
  isTestMode: config.APP_ENV !== 'production',
});

export const getPayPalConfig = () => ({
  clientId: config.PAYPAL_CLIENT_ID,
  environment: config.APP_ENV === 'production' ? 'production' : 'sandbox',
});

export const getCoinGateConfig = () => ({
  environment: config.COINGATE_ENVIRONMENT,
});

// Analytics helpers
export const getAnalyticsConfig = () => ({
  googleAnalyticsId: config.GOOGLE_ANALYTICS_ID,
  facebookPixelId: config.FACEBOOK_PIXEL_ID,
  isEnabled: config.APP_ENV === 'production',
});

export default config; 