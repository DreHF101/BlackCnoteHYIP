/**
 * Browsersync Configuration - Optimized for Live Editing
 *
 * This configuration serves the React app directly from the theme directory
 * and watches for file changes in both WordPress theme/plugin and React source files.
 * The React app handles environment detection internally.
 */
module.exports = {
  // Proxy your local WordPress server (Docker setup)
  proxy: 'http://localhost:8888',

  // Start the browser at the correct path.
  startPath: '/',

  // Serve static files from the project root.
  serveStatic: ['.'],

  // Watch for changes in all relevant files with improved patterns
  files: [
    // WordPress theme files (local development)
    './blackcnote/**/*.php',
    './blackcnote/**/*.css',
    './blackcnote/**/*.js',
    './blackcnote/**/*.html',
    
    // Plugin files (exclude debug plugin to avoid conflicts)
    './hyiplab/**/*.php',
    '!./blackcnote-debug-plugin/**/*.php',
    
    // React source files (for development)
    './src/**/*.{js,jsx,ts,tsx,css,scss}',
    './src/**/*.html',
    
    // Configuration files
    './bs-config.cjs',
    './vite.config.ts',
    './package.json'
  ],

  // Standard Browsersync ports (avoid conflicts with debug system)
  port: 3006,
  ui: {
    port: 3007,
  },

  // Open the browser automatically
  open: true,
  
  // Ghost mode synchronizes clicks, scrolls, and forms across browsers
  ghostMode: {
    clicks: true,
    scroll: true,
    forms: {
      submit: true,
      inputs: true,
      toggles: true
    }
  },

  // Add CORS headers for better compatibility
  middleware: function (req, res, next) {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, X-WP-Nonce, Authorization');
    res.setHeader('Access-Control-Allow-Credentials', 'true');
    
    // Handle preflight requests
    if (req.method === 'OPTIONS') {
      res.writeHead(200);
      res.end();
      return;
    }
    
    // Skip debug-related requests to avoid conflicts
    if (req.url && (
      req.url.includes('/wp-admin/admin-ajax.php') && 
      (req.url.includes('blackcnote_debug') || req.url.includes('blackcnote_send_to_cursor'))
    )) {
      return next();
    }
    
    next();
  },

  // Improved watch options
  watchOptions: {
    ignore: [
      'node_modules',
      'vendor', 
      '.git',
      'dist',
      '*.log',
      '*.tmp',
      // Ignore debug log files to prevent conflicts
      '**/blackcnote-unified-debug.log',
      '**/debug.log',
      '**/wp-content/debug.log'
    ],
    // Use polling for better file detection on Windows
    usePolling: true,
    interval: 1000,
    binaryInterval: 3000
  },
  
  // Add a small delay to prevent rapid-fire reloads
  reloadDelay: 300,
  
  // Don't show the Browsersync notification in the browser
  notify: false,
  
  // Enable logging for debugging
  logLevel: 'info',
  
  // Add custom error handling
  callbacks: {
    ready: function(err, bs) {
      console.log('✅ Browsersync is ready!');
      console.log('🌐 Local: http://localhost:3002');
      console.log('🎛️  UI: http://localhost:3003');
      console.log('📱 External: http://' + bs.options.host + ':' + bs.options.port);
      
      // Log that debug system is compatible
      console.log('🔧 Debug system integration: Compatible');
    },
    
    // Add error handling for conflicts
    error: function(err) {
      console.error('❌ Browsersync error:', err.message);
      
      // Check for common conflicts
      if (err.message.includes('EADDRINUSE')) {
        console.error('💡 Port conflict detected. Try:');
        console.error('   - Kill existing processes: npx kill-port 3000 3001 5174');
        console.error('   - Or use different ports in bs-config.cjs');
      }
    }
  },
  
  // Add script injection for debug compatibility
  snippetOptions: {
    ignorePaths: [
      '/wp-admin/**',
      '/wp-content/debug.log',
      '/blackcnote-unified-debug.log'
    ]
  }
};
