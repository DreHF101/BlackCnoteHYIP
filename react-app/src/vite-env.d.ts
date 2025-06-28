/// <reference types="vite/client" />
import type { WordPressSettings } from './src/types';

interface ImportMetaEnv {
  readonly VITE_APP_NAME?: string;
  readonly VITE_APP_ENV?: string;
  readonly VITE_APP_URL?: string;
  readonly VITE_API_BASE_URL?: string;
  readonly VITE_WP_API_URL?: string;
  readonly VITE_WP_NONCE?: string;
  readonly VITE_STRIPE_PUBLIC_KEY?: string;
  readonly VITE_PAYPAL_CLIENT_ID?: string;
  readonly VITE_COINGATE_ENVIRONMENT?: string;
  readonly VITE_ENABLE_CRYPTO_PAYMENTS?: string;
  readonly VITE_ENABLE_SMS_VERIFICATION?: string;
  readonly VITE_ENABLE_KYC?: string;
  readonly VITE_GOOGLE_ANALYTICS_ID?: string;
  readonly VITE_FACEBOOK_PIXEL_ID?: string;
  readonly VITE_FACEBOOK_APP_ID?: string;
  readonly VITE_TWITTER_HANDLE?: string;
  readonly VITE_SUPPORT_EMAIL?: string;
  readonly VITE_SUPPORT_PHONE?: string;
  readonly VITE_LIVE_CHAT_ENABLED?: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}

interface BlackCnoteSettings {
  apiUrl: string;
  nonce: string;
  homeUrl: string;
  isLoggedIn: boolean;
  userId: number;
  baseUrl: string;
}

// Debug-related interfaces
interface BlackCnoteDebug {
  ajaxUrl: string;
  nonce: string;
}

interface BlackCnoteDebugAPI {
  checkXAMPPStatus(): Promise<unknown>;
  getDebugInfo(): Promise<unknown>;
}

interface BlackCnoteDebugUtils {
  checkElement(selector: string, description: string): boolean;
  checkScript(scriptName: string): boolean;
  checkCSS(cssName: string): boolean;
  measurePerformance(name: string, fn: () => unknown): unknown;
}

interface BlackCnoteDebugLogger {
  debug(message: string, data?: unknown): void;
  info(message: string, data?: unknown): void;
  warn(message: string, data?: unknown): void;
  error(message: string, data?: unknown): void;
}

interface Window {
  blackcnoteSettings?: BlackCnoteSettings;
  blackcnoteApiSettings?: WordPressSettings;
  investmentPlans: Array<{
    id: number;
    name: string;
    return_rate: number;
    min_investment: number;
    max_investment: number;
    duration: number;
  }>;
  // Debug-related globals (only available when WP_DEBUG is enabled)
  blackcnoteDebug?: {
    enabled?: boolean;
    logger?: Record<string, (...args: unknown[]) => void>;
  };
  blackcnoteDebugAPI?: BlackCnoteDebugAPI;
  blackcnoteDebugUtils?: BlackCnoteDebugUtils;
  blackcnoteDebugLogger?: BlackCnoteDebugLogger;
}
