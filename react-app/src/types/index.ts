export interface WordPressSettings {
  homeUrl: string;
  isDevelopment?: boolean;
  apiUrl: string;
  nonce: string;
  isLoggedIn: boolean;
  userId: number;
  baseUrl: string;
  themeUrl: string;
  ajaxUrl: string;
  environment: string;
  themeActive: boolean;
  pluginActive: boolean;
} 