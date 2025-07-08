#!/usr/bin/env node

/**
 * BlackCnote Development Environment Fix Script
 * 
 * This script addresses:
 * 1. Browsersync not running
 * 2. React Router basename conflicts
 * 3. CORS issues between ports
 * 4. HYIPLab API unavailability
 */

const { execSync, spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const http = require('http');

console.log('🔧 BlackCnote Development Environment Fix Script');
console.log('================================================');

// Configuration
const config = {
  reactPort: 5174,
  browsersyncPort: 3000,
  wordpressPort: 8888,
  apiBaseUrl: 'http://localhost:8888/wp-json',
  hyiplabApiUrl: 'http://localhost:8888/wp-json/hyiplab/v1'
};

// Utility functions
function log(message, type = 'info') {
  const timestamp = new Date().toISOString();
  const colors = {
    info: '\x1b[36m',    // Cyan
    success: '\x1b[32m', // Green
    warning: '\x1b[33m', // Yellow
    error: '\x1b[31m',   // Red
    reset: '\x1b[0m'     // Reset
  };
  console.log(`${colors[type]}[${timestamp}] ${message}${colors.reset}`);
}

function checkPort(port) {
  return new Promise((resolve) => {
    const server = http.createServer();
    server.listen(port, () => {
      server.close();
      resolve(true);
    });
    server.on('error', () => {
      resolve(false);
    });
  });
}

function killProcessOnPort(port) {
  try {
    if (process.platform === 'win32') {
      execSync(`netstat -ano | findstr :${port}`, { stdio: 'pipe' });
      execSync(`for /f "tokens=5" %a in ('netstat -ano ^| findstr :${port}') do taskkill /f /pid %a`, { stdio: 'pipe' });
    } else {
      execSync(`lsof -ti:${port} | xargs kill -9`, { stdio: 'pipe' });
    }
    log(`Killed process on port ${port}`, 'success');
  } catch (error) {
    log(`No process found on port ${port}`, 'info');
  }
}

function testApiEndpoint(url) {
  return new Promise((resolve) => {
    const req = http.request(url, { method: 'GET', timeout: 5000 }, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        resolve({ status: res.statusCode, data });
      });
    });
    
    req.on('error', () => resolve({ status: 0, error: 'Connection failed' }));
    req.on('timeout', () => resolve({ status: 0, error: 'Timeout' }));
    req.setTimeout(5000);
    req.end();
  });
}

// Main fix functions
async function fixBrowsersync() {
  log('🔧 Fixing Browsersync...', 'info');
  
  // Kill any existing Browsersync processes
  killProcessOnPort(config.browsersyncPort);
  killProcessOnPort(3001); // Browsersync UI port
  
  // Check if Browsersync is installed
  try {
    require.resolve('browser-sync');
    log('Browsersync is installed', 'success');
  } catch (error) {
    log('Installing Browsersync...', 'warning');
    execSync('npm install browser-sync --save-dev', { stdio: 'inherit' });
  }
  
  // Start Browsersync in background
  log('Starting Browsersync...', 'info');
  const browsersync = spawn('npx', ['browser-sync', 'start', '--config', 'bs-config.cjs'], {
    stdio: 'pipe',
    detached: true
  });
  
  // Wait for Browsersync to start
  await new Promise(resolve => setTimeout(resolve, 3000));
  
  // Test if Browsersync is running
  const isRunning = await checkPort(config.browsersyncPort);
  if (isRunning) {
    log(`✅ Browsersync running on http://localhost:${config.browsersyncPort}`, 'success');
  } else {
    log('❌ Browsersync failed to start', 'error');
  }
}

async function fixReactRouterBasename() {
  log('🔧 Fixing React Router basename...', 'info');
  
  const appFile = path.join(__dirname, 'src', 'App.tsx');
  if (!fs.existsSync(appFile)) {
    log('❌ App.tsx not found', 'error');
    return;
  }
  
  let appContent = fs.readFileSync(appFile, 'utf8');
  
  // Check current basename configuration
  const basenameMatch = appContent.match(/basename\s*=\s*([^}]+)/);
  if (basenameMatch) {
    log('Current basename configuration found', 'info');
    
    // Update basename to work with both development and production
    const newBasename = `basename={process.env.NODE_ENV === 'development' ? '/' : new URL(settings.homeUrl).pathname}`;
    appContent = appContent.replace(/basename\s*=\s*[^}]+/, newBasename);
    
    fs.writeFileSync(appFile, appContent);
    log('✅ React Router basename updated for development compatibility', 'success');
  }
}

async function fixCorsIssues() {
  log('🔧 Fixing CORS issues...', 'info');
  
  // Test current CORS configuration
  const corsTest = await testApiEndpoint(`${config.apiBaseUrl}/blackcnote/v1/settings`);
  if (corsTest.status === 200) {
    log('✅ CORS is working correctly', 'success');
  } else {
    log('❌ CORS issues detected', 'error');
    
    // Check if BlackCnote CORS Handler plugin is active
    const pluginTest = await testApiEndpoint(`${config.apiBaseUrl}/blackcnote-cors-handler/v1/status`);
    if (pluginTest.status === 200) {
      log('BlackCnote CORS Handler plugin is active', 'info');
    } else {
      log('Activating BlackCnote CORS Handler plugin...', 'warning');
      
      // Create activation script
      const activationScript = `
<?php
// Activate BlackCnote CORS Handler plugin
require_once('${path.join(__dirname, '..', 'blackcnote', 'wp-load.php')}');

if (!is_plugin_active('blackcnote-cors-handler/blackcnote-cors-handler.php')) {
    activate_plugin('blackcnote-cors-handler/blackcnote-cors-handler.php');
    echo "Plugin activated successfully\\n";
} else {
    echo "Plugin already active\\n";
}

// Flush rewrite rules
flush_rewrite_rules();
echo "Rewrite rules flushed\\n";
?>
      `;
      
      fs.writeFileSync(path.join(__dirname, 'activate-cors-plugin.php'), activationScript);
      
      try {
        execSync(`php ${path.join(__dirname, 'activate-cors-plugin.php')}`, { stdio: 'inherit' });
        log('✅ CORS plugin activated', 'success');
      } catch (error) {
        log('❌ Failed to activate CORS plugin', 'error');
      }
    }
  }
}

async function fixHyiplabApi() {
  log('🔧 Fixing HYIPLab API...', 'info');
  
  // Test HYIPLab API endpoints
  const endpoints = [
    '/status',
    '/plans',
    '/users',
    '/investments'
  ];
  
  for (const endpoint of endpoints) {
    const response = await testApiEndpoint(`${config.hyiplabApiUrl}${endpoint}`);
    if (response.status === 200) {
      log(`✅ HYIPLab API ${endpoint} is working`, 'success');
    } else if (response.status === 404) {
      log(`⚠️  HYIPLab API ${endpoint} returns 404 - endpoint may not exist`, 'warning');
    } else {
      log(`❌ HYIPLab API ${endpoint} failed: ${response.status}`, 'error');
    }
  }
  
  // Check if HYIPLab plugin is active
  const pluginTest = await testApiEndpoint(`${config.apiBaseUrl}/hyiplab/v1/status`);
  if (pluginTest.status === 200) {
    log('✅ HYIPLab plugin is active and responding', 'success');
  } else {
    log('❌ HYIPLab plugin may not be active', 'error');
    
    // Create HYIPLab activation script
    const hyiplabActivationScript = `
<?php
// Activate HYIPLab plugin
require_once('${path.join(__dirname, '..', 'blackcnote', 'wp-load.php')}');

$plugin_path = 'hyiplab/hyiplab.php';
if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_path)) {
    if (!is_plugin_active($plugin_path)) {
        activate_plugin($plugin_path);
        echo "HYIPLab plugin activated successfully\\n";
    } else {
        echo "HYIPLab plugin already active\\n";
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
    echo "Rewrite rules flushed\\n";
} else {
    echo "HYIPLab plugin not found\\n";
}
?>
    `;
    
    fs.writeFileSync(path.join(__dirname, 'activate-hyiplab.php'), hyiplabActivationScript);
    
    try {
      execSync(`php ${path.join(__dirname, 'activate-hyiplab.php')}`, { stdio: 'inherit' });
      log('✅ HYIPLab plugin activation attempted', 'success');
    } catch (error) {
      log('❌ Failed to activate HYIPLab plugin', 'error');
    }
  }
}

async function createDevelopmentScript() {
  log('🔧 Creating enhanced development script...', 'info');
  
  const devScript = `
#!/usr/bin/env node

/**
 * Enhanced BlackCnote Development Script
 * Starts all development services with proper error handling
 */

const { spawn } = require('child_process');
const http = require('http');

const services = [
  {
    name: 'React Dev Server',
    command: 'npm',
    args: ['run', 'dev'],
    port: 5174,
    url: 'http://localhost:5174'
  },
  {
    name: 'Browsersync',
    command: 'npx',
    args: ['browser-sync', 'start', '--config', 'bs-config.cjs'],
    port: 3000,
    url: 'http://localhost:3000'
  }
];

function checkService(url) {
  return new Promise((resolve) => {
    const req = http.request(url, { method: 'GET', timeout: 3000 }, (res) => {
      resolve(res.statusCode === 200);
    });
    req.on('error', () => resolve(false));
    req.on('timeout', () => resolve(false));
    req.setTimeout(3000);
    req.end();
  });
}

async function startServices() {
  console.log('🚀 Starting BlackCnote Development Environment...');
  
  const processes = [];
  
  for (const service of services) {
    console.log(\`Starting \${service.name}...\`);
    
    const proc = spawn(service.command, service.args, {
      stdio: 'pipe',
      detached: false
    });
    
    proc.stdout.on('data', (data) => {
      console.log(\`[\${service.name}] \${data.toString().trim()}\`);
    });
    
    proc.stderr.on('data', (data) => {
      console.error(\`[\${service.name}] ERROR: \${data.toString().trim()}\`);
    });
    
    processes.push({ process: proc, service });
    
    // Wait a bit before starting next service
    await new Promise(resolve => setTimeout(resolve, 2000));
  }
  
  // Monitor services
  setInterval(async () => {
    for (const { service } of processes) {
      const isHealthy = await checkService(service.url);
      if (!isHealthy) {
        console.warn(\`⚠️  \${service.name} may not be responding\`);
      }
    }
  }, 10000);
  
  console.log('✅ All development services started');
  console.log('📱 React App: http://localhost:5174');
  console.log('🔄 Browsersync: http://localhost:3000');
  console.log('🌐 WordPress: http://localhost:8888');
  
  // Handle graceful shutdown
  process.on('SIGINT', () => {
    console.log('\\n🛑 Shutting down development services...');
    processes.forEach(({ process: proc }) => {
      proc.kill('SIGTERM');
    });
    process.exit(0);
  });
}

startServices().catch(console.error);
  `;
  
  fs.writeFileSync(path.join(__dirname, 'dev-enhanced.cjs'), devScript);
  fs.chmodSync(path.join(__dirname, 'dev-enhanced.cjs'), '755');
  
  // Update package.json scripts
  const packageJsonPath = path.join(__dirname, 'package.json');
  const packageJson = JSON.parse(fs.readFileSync(packageJsonPath, 'utf8'));
  
  packageJson.scripts['dev:enhanced'] = 'node dev-enhanced.cjs';
  packageJson.scripts['dev:full:fixed'] = 'node dev-enhanced.cjs';
  
  fs.writeFileSync(packageJsonPath, JSON.stringify(packageJson, null, 2));
  
  log('✅ Enhanced development script created', 'success');
}

async function runAllFixes() {
  try {
    log('🚀 Starting comprehensive development environment fix...', 'info');
    
    // 1. Fix Browsersync
    await fixBrowsersync();
    
    // 2. Fix React Router basename
    await fixReactRouterBasename();
    
    // 3. Fix CORS issues
    await fixCorsIssues();
    
    // 4. Fix HYIPLab API
    await fixHyiplabApi();
    
    // 5. Create enhanced development script
    await createDevelopmentScript();
    
    log('✅ All fixes completed successfully!', 'success');
    log('', 'info');
    log('📋 Next Steps:', 'info');
    log('1. Run: npm run dev:enhanced', 'info');
    log('2. Access React app: http://localhost:5174', 'info');
    log('3. Access Browsersync: http://localhost:3000', 'info');
    log('4. Access WordPress: http://localhost:8888', 'info');
    log('', 'info');
    log('🔧 If issues persist, check the logs above for specific error messages.', 'warning');
    
  } catch (error) {
    log(`❌ Error during fix process: ${error.message}`, 'error');
    process.exit(1);
  }
}

// Run the fixes
runAllFixes(); 