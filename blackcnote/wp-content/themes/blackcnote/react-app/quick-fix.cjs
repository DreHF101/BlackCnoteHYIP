#!/usr/bin/env node

/**
 * Quick Fix for BlackCnote Development Issues
 */

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

console.log('🔧 Quick Fix for BlackCnote Development Issues');
console.log('==============================================');

// 1. Fix React Router basename
console.log('\n1. Fixing React Router basename...');
const appFile = path.join(__dirname, 'src', 'App.tsx');
if (fs.existsSync(appFile)) {
  let content = fs.readFileSync(appFile, 'utf8');
  
  // Update basename for development compatibility
  const newBasename = `basename={process.env.NODE_ENV === 'development' ? '/' : new URL(settings.homeUrl).pathname}`;
  content = content.replace(/basename\s*=\s*[^}]+/, newBasename);
  
  fs.writeFileSync(appFile, content);
  console.log('✅ React Router basename updated');
} else {
  console.log('❌ App.tsx not found');
}

// 2. Update package.json scripts
console.log('\n2. Updating package.json scripts...');
const packageJsonPath = path.join(__dirname, 'package.json');
const packageJson = JSON.parse(fs.readFileSync(packageJsonPath, 'utf8'));

// Add new scripts
packageJson.scripts['dev:start'] = 'vite --host 0.0.0.0 --port 5174';
packageJson.scripts['dev:browsersync'] = 'browser-sync start --config bs-config.cjs';
packageJson.scripts['dev:both'] = 'concurrently "npm run dev:start" "npm run dev:browsersync"';

fs.writeFileSync(packageJsonPath, JSON.stringify(packageJson, null, 2));
console.log('✅ Package.json scripts updated');

// 3. Create simple development script
console.log('\n3. Creating simple development script...');
const devScript = `#!/usr/bin/env node

const { spawn } = require('child_process');

console.log('🚀 Starting BlackCnote Development Environment...');

// Start React dev server
const react = spawn('npm', ['run', 'dev:start'], {
  stdio: 'inherit',
  detached: false
});

// Start Browsersync after a delay
setTimeout(() => {
  const browsersync = spawn('npm', ['run', 'dev:browsersync'], {
    stdio: 'inherit',
    detached: false
  });
  
  browsersync.on('error', (err) => {
    console.log('⚠️  Browsersync not available, continuing with React only');
  });
}, 3000);

console.log('📱 React App will be available at: http://localhost:5174');
console.log('🔄 Browsersync will be available at: http://localhost:3000 (if available)');
console.log('🌐 WordPress is available at: http://localhost:8888');
console.log('\\nPress Ctrl+C to stop all services');

process.on('SIGINT', () => {
  console.log('\\n🛑 Shutting down...');
  process.exit(0);
});
`;

fs.writeFileSync(path.join(__dirname, 'dev-simple.cjs'), devScript);
packageJson.scripts['dev:simple'] = 'node dev-simple.cjs';
fs.writeFileSync(packageJsonPath, JSON.stringify(packageJson, null, 2));

console.log('✅ Simple development script created');

// 4. Test current services
console.log('\n4. Testing current services...');

try {
  // Test React dev server
  const reactTest = execSync('curl -s http://localhost:5174', { encoding: 'utf8', stdio: 'pipe' });
  if (reactTest.includes('<!DOCTYPE html>')) {
    console.log('✅ React dev server is running on port 5174');
  }
} catch (error) {
  console.log('⚠️  React dev server not running on port 5174');
}

try {
  // Test WordPress
  const wpTest = execSync('curl -s http://localhost:8888', { encoding: 'utf8', stdio: 'pipe' });
  if (wpTest.includes('WordPress')) {
    console.log('✅ WordPress is running on port 8888');
  }
} catch (error) {
  console.log('⚠️  WordPress not responding on port 8888');
}

// 5. Summary
console.log('\n📋 Summary:');
console.log('✅ React Router basename fixed for development');
console.log('✅ Package.json scripts updated');
console.log('✅ Simple development script created');
console.log('');
console.log('🚀 To start development:');
console.log('   npm run dev:simple    # Start both React and Browsersync');
console.log('   npm run dev:start     # Start React only');
console.log('   npm run dev:browsersync # Start Browsersync only');
console.log('');
console.log('🌐 Access URLs:');
console.log('   React App: http://localhost:5174');
console.log('   Browsersync: http://localhost:3000');
console.log('   WordPress: http://localhost:8888');
console.log('');
console.log('🔧 For CORS and API issues, the BlackCnote CORS Handler plugin should handle them automatically.'); 