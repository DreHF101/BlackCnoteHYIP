import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App'
import './index.css'
import { mountComponents } from './utils/mount.tsx'

// Check if we're in development mode (Vite dev server)
const isDevelopment = import.meta.env.DEV;

const rootElement = document.getElementById('root');

declare global {
  interface Window {
    blackCnoteApiSettings?: any;
  }
}

if (rootElement) {
  if (isDevelopment) {
    // Development mode - render the full app with correct WordPress API settings
    const defaultSettings = {
      homeUrl: 'http://localhost:8888',
      isDevelopment: true,
      apiUrl: 'http://localhost:8888/wp-json/blackcnote/v1/',
      nonce: 'dummy-nonce',
      isLoggedIn: false,
      userId: 0,
      baseUrl: 'http://localhost:8888',
      themeUrl: 'http://localhost:8888/wp-content/themes/blackcnote',
      ajaxUrl: 'http://localhost:8888/wp-admin/admin-ajax.php',
      environment: 'development',
      themeActive: true,
      pluginActive: true,
    };
    
    // Set the global API settings for development
    window.blackCnoteApiSettings = defaultSettings;
    
    ReactDOM.createRoot(rootElement).render(
      <React.StrictMode>
        <App settings={defaultSettings} />
      </React.StrictMode>,
    )
  } else {
    // Production mode - get settings from WordPress and render the full app
    const apiSettings = window.blackCnoteApiSettings;
    if (apiSettings) {
      ReactDOM.createRoot(rootElement).render(
        <React.StrictMode>
          <App settings={apiSettings} />
        </React.StrictMode>,
      )
    } else {
      console.error('BlackCnote API settings not found. The app cannot be initialized.');
      rootElement.innerHTML = 'Error: Configuration settings are missing. The application cannot start.';
    }
  }
} else {
    // Fallback for component-based mounting if #root isn't found
    mountComponents();
}
