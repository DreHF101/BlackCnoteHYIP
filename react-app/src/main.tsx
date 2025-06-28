import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.tsx'
import './index.css'
import { mountComponents } from './utils/mount.tsx'

// Check if we're in development mode (Vite dev server)
const isDevelopment = import.meta.env.DEV;

const rootElement = document.getElementById('root');

if (rootElement) {
  if (isDevelopment) {
    // Development mode - render the full app with default settings
    const defaultSettings = {
      homeUrl: 'http://localhost:5173',
      isDevelopment: true,
      apiUrl: 'http://localhost/blackcnote/wp-json/wp/v2/',
      nonce: 'dummy-nonce',
      isLoggedIn: false,
      userId: 0,
      baseUrl: 'http://localhost/blackcnote',
      themeUrl: 'http://localhost/blackcnote/wp-content/themes/blackcnote',
      ajaxUrl: 'http://localhost/blackcnote/wp-admin/admin-ajax.php',
      environment: 'development',
      themeActive: true,
      pluginActive: true,
    };
    
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
