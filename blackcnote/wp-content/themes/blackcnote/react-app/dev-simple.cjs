#!/usr/bin/env node

const { spawn } = require('child_process');
const path = require('path');

console.log('🚀 Starting BlackCnote Development Environment...');

// Start Vite dev server on port 5177
const vite = spawn('npx', ['vite', '--port', '5177', '--host', 'localhost'], {
  stdio: 'inherit',
  shell: true,
  cwd: __dirname
});

vite.on('error', (err) => {
  console.error('❌ Vite error:', err.message);
});

vite.on('close', (code) => {
  console.log(`Vite process exited with code ${code}`);
});

console.log('✅ Vite development server starting on http://localhost:5177');
console.log('📝 Edit files in src/ directory to see live changes');
console.log('🛑 Press Ctrl+C to stop');

process.on('SIGINT', () => {
  console.log('\n🛑 Shutting down...');
  process.exit(0);
});
