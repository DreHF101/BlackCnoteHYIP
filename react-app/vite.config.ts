import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

// Canonical pathway configuration for BlackCnote
const CANONICAL_ROOT = 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote';
const CANONICAL_REACT_APP = `${CANONICAL_ROOT}/react-app`;
const CANONICAL_WP_CONTENT = `${CANONICAL_ROOT}/blackcnote/wp-content`;
const CANONICAL_THEME = `${CANONICAL_WP_CONTENT}/themes/blackcnote`;

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  css: {
    postcss: './postcss.config.js',
  },
  base: '/',
  build: {
    // Build to canonical WordPress theme dist directory
    outDir: resolve(CANONICAL_THEME, 'dist'),
    manifest: true,
    target: 'es2020',
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: process.env.NODE_ENV === 'production',
        drop_debugger: process.env.NODE_ENV === 'production',
        pure_funcs: process.env.NODE_ENV === 'production' ? ['console.log'] : [],
      },
      mangle: {
        toplevel: true,
      },
    },
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'index.html'),
      },
      output: {
        // Ensure proper asset paths for WordPress with canonical pathways
        assetFileNames: (assetInfo) => {
          const name = assetInfo.name || '';
          const extType = name.split('.')[1] || 'asset';
          if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
            return `assets/img/[name]-[hash][extname]`;
          }
          return `assets/${extType}/[name]-[hash][extname]`;
        },
        chunkFileNames: 'assets/js/[name]-[hash].js',
        entryFileNames: 'assets/js/[name]-[hash].js',
        manualChunks: {
          vendor: ['react', 'react-dom'],
          router: ['react-router-dom'],
          ui: ['lucide-react'],
          utils: ['lodash', 'axios'],
        }
      },
    },
    // Generate source maps for better debugging (but not in production)
    sourcemap: process.env.NODE_ENV !== 'production',
    // Optimize chunk size
    chunkSizeWarningLimit: 1000,
  },
  server: {
    host: '0.0.0.0', // Allow connections from Docker and host.docker.internal
    port: 5174,
    strictPort: false, // Allow fallback to next port if busy
    cors: {
      origin: [
        'http://localhost:8888',
        'http://host.docker.internal:8888',
        'http://localhost:5174',
        'http://host.docker.internal:5174',
        'http://blackcnote-react:5174',
        'http://blackcnote-wordpress:80',
        '*'
      ],
      credentials: true,
    },
    // Performance optimizations for dev server
    hmr: {
      overlay: true,
      port: 5178,
      // Ensure HMR works with Docker
      host: '0.0.0.0'
    },
    // Allow all hosts for Docker development
    allowedHosts: ['all'],
    // Add proxy for WordPress API calls with canonical pathway support
    proxy: {
      '/wp-json': {
        target: 'http://wordpress:80',
        changeOrigin: true,
        secure: false,
        timeout: 10000,
      },
      '/wp-admin/admin-ajax.php': {
        target: 'http://wordpress:80',
        changeOrigin: true,
        secure: false,
        timeout: 10000,
      },
      '/wp-content': {
        target: 'http://wordpress:80',
        changeOrigin: true,
        secure: false,
        timeout: 10000
      }
    }
  },
  preview: {
    port: 4173,
    host: true,
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, './src'),
      // Canonical pathway aliases
      '@canonical': resolve(CANONICAL_ROOT),
      '@react-app': resolve(CANONICAL_REACT_APP),
      '@wp-content': resolve(CANONICAL_WP_CONTENT),
      '@theme': resolve(CANONICAL_THEME),
    },
  },
  optimizeDeps: {
    include: ['react', 'react-dom', 'react-router-dom', 'lucide-react'],
    // Enable dependency pre-bundling
    force: false,
  },
  // Add environment variable handling for debug system and canonical pathways
  define: {
    __DEBUG_ENABLED__: JSON.stringify(process.env.VITE_DEBUG_ENABLED === 'true'),
    __DEBUG_LEVEL__: JSON.stringify(process.env.VITE_DEBUG_LEVEL || 'warn'),
    __CANONICAL_ROOT__: JSON.stringify(CANONICAL_ROOT),
    __CANONICAL_REACT_APP__: JSON.stringify(CANONICAL_REACT_APP),
    __CANONICAL_WP_CONTENT__: JSON.stringify(CANONICAL_WP_CONTENT),
    __CANONICAL_THEME__: JSON.stringify(CANONICAL_THEME),
  },
  // Performance optimizations
  esbuild: {
    // Enable tree shaking
    treeShaking: true,
    // Optimize for development
    target: 'es2020',
    // Enable source maps for better debugging
    sourcemap: process.env.NODE_ENV === 'development',
    // Production optimizations
    minifyIdentifiers: process.env.NODE_ENV === 'production',
    minifySyntax: process.env.NODE_ENV === 'production',
    minifyWhitespace: process.env.NODE_ENV === 'production',
  }
});
