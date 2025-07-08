#!/usr/bin/env node

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import http from 'http';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Development environment configuration
const config = {
  // WordPress local development URL
  wordpressUrl: process.env.WORDPRESS_URL || 'http://localhost',
  
  // Browsersync ports
  bsPort: process.env.BS_PORT || 3000,
  bsUIPort: process.env.BS_UI_PORT || 3001,
  
  // Vite dev server port
  vitePort: process.env.VITE_PORT || 5173,
  
  // File watching options
  watchFiles: [
    'blackcnote/**/*.php',
    'blackcnote/**/*.css',
    'blackcnote/**/*.js',
    'blackcnote/**/*.html',
    'hyiplab/**/*.php',
    'hyiplab/**/*.css',
    'hyiplab/**/*.js',
    'src/**/*.tsx',
    'src/**/*.ts',
    'src/**/*.css',
    'src/**/*.js',
    'public/**/*'
  ],
  
  // Ignore patterns
  ignorePatterns: [
    'node_modules',
    'vendor',
    'hyiplab/vendor',
    '.git',
    '*.log',
    'wordpress/wp-admin/**',
    'wordpress/wp-includes/**'
  ]
};

// Generate Browsersync configuration
function generateBSConfig() {
  const bsConfig = {
    proxy: config.wordpressUrl,
    files: config.watchFiles,
    ignore: config.ignorePatterns,
    port: config.bsPort,
    ui: {
      port: config.bsUIPort
    },
    reloadDelay: 0,
    reloadDebounce: 250,
    reloadThrottle: 0,
    open: true,
    browser: "default",
    notify: true,
    ghostMode: {
      clicks: true,
      scroll: true,
      forms: {
        submit: true,
        inputs: true,
        toggles: true
      }
    },
    logLevel: "info",
    middleware: function(req, res, next) {
      res.setHeader('Access-Control-Allow-Origin', '*');
      next();
    },
    snippetOptions: {
      ignorePaths: ["wp-admin/**"]
    },
    serveStatic: [{
      route: '/wp-content/themes/blackcnote',
      dir: path.join(process.cwd(), 'blackcnote')
    }],
    rewriteRules: [
      {
        match: new RegExp(config.wordpressUrl.replace('http://', ''), 'g'),
        replace: ''
      }
    ],
    injectChanges: true,
    watchEvents: ["change", "add", "unlink", "addDir", "unlinkDir"],
    watchOptions: {
      ignored: config.ignorePatterns,
      usePolling: true,
      interval: 1000
    }
  };

  return bsConfig;
}

// Write configuration to file
function writeConfig() {
  const configContent = `module.exports = ${JSON.stringify(generateBSConfig(), null, 2)};`;
  fs.writeFileSync('bs-config.js', configContent);
  console.log('✅ Browsersync configuration generated successfully!');
  console.log(`🌐 WordPress URL: ${config.wordpressUrl}`);
  console.log(`🔄 Browsersync Port: ${config.bsPort}`);
  console.log(`🎛️  Browsersync UI Port: ${config.bsUIPort}`);
  console.log(`⚡ Vite Dev Server Port: ${config.vitePort}`);
}

// Check if WordPress is running
async function checkWordPress() {
  return new Promise((resolve) => {
    http.get(config.wordpressUrl, (res) => {
      resolve(res.statusCode === 200);
    }).on('error', () => {
      resolve(false);
    });
  });
}

// Main setup function
async function setup() {
  console.log('🚀 Starting development environment setup...');

  // Check WordPress
  const wpRunning = await checkWordPress();
  if (!wpRunning) {
    console.error('❌ WordPress is not running at ' + config.wordpressUrl);
    console.log('Please make sure:');
    console.log('1. XAMPP Apache and MySQL services are running');
    console.log('2. WordPress is properly installed at ' + config.wordpressUrl);
    process.exit(1);
  }

  // Generate configuration
  writeConfig();

  console.log('\n📝 Development commands:');
  console.log('  npm run dev        - Start Vite development server');
  console.log('  npm run dev:sync   - Start Browsersync server');
  console.log('  npm run dev:full   - Start both Vite and Browsersync');
}

setup().catch(console.error); 