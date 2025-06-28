module.exports = {
  // Browsersync configuration for BlackCnote development environment
  proxy: "localhost:8888", // Proxy to WordPress
  port: 3000, // Browsersync port
  ui: {
    port: 3001 // Browsersync UI port
  },
  files: [
    // Watch WordPress files
    "../blackcnote/**/*.php",
    "../blackcnote/**/*.js",
    "../blackcnote/**/*.css",
    "../blackcnote/**/*.html",
    
    // Watch React app files
    "../react-app/src/**/*.{js,jsx,ts,tsx}",
    "../react-app/src/**/*.{css,scss}",
    "../react-app/public/**/*.html",
    
    // Watch theme files
    "../blackcnote/wp-content/themes/**/*",
    "../blackcnote/wp-content/plugins/**/*",
    
    // Exclude node_modules and other build files
    "!../react-app/node_modules/**/*",
    "!../react-app/dist/**/*",
    "!../blackcnote/wp-content/uploads/**/*"
  ],
  ignore: [
    "node_modules",
    "dist",
    "build",
    ".git",
    "*.log"
  ],
  reloadDelay: 100,
  reloadDebounce: 250,
  reloadThrottle: 0,
  notify: true,
  open: false, // Don't auto-open browser
  ghostMode: {
    clicks: true,
    forms: true,
    scroll: true
  },
  snippetOptions: {
    ignorePaths: ["/wp-admin/**", "/wp-login.php"]
  },
  middleware: function(req, res, next) {
    // Add CORS headers for development
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    next();
  },
  rewriteRules: [
    // Rewrite rules for development
    {
      match: /localhost:8888/g,
      replace: "localhost:3000"
    }
  ],
  // Logging
  logLevel: "info",
  logPrefix: "BlackCnote Browsersync",
  logConnections: true,
  logFileChanges: true,
  
  // HTTPS (optional for development)
  https: false,
  
  // Browser compatibility
  browser: [
    "chrome",
    "firefox",
    "edge"
  ],
  
  // Additional options
  timestamps: true,
  injectChanges: true,
  minify: false,
  
  // Custom events
  plugins: [
    // Add any Browsersync plugins here if needed
  ],
  
  // Server configuration
  server: false, // We're using proxy mode
  serveStatic: [
    {
      route: "/wp-content",
      dir: "../blackcnote/wp-content"
    },
    {
      route: "/react-assets",
      dir: "../react-app/dist"
    }
  ]
}; 