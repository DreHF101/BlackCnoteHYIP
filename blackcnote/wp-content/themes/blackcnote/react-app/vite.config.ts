import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

// Vite configuration for React app running from WordPress theme directory
export default defineConfig({
  plugins: [react()],
  css: {
    postcss: './postcss.config.js',
  },
  base: '/wp-content/themes/blackcnote/react-app/',
  build: {
    // Build to theme's dist directory
    outDir: resolve(__dirname, '../dist'),
    manifest: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'index.html'),
      },
      output: {
        // Ensure proper asset paths for WordPress theme
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
          ui: ['lucide-react']
        }
      },
    },
    sourcemap: process.env.NODE_ENV !== 'production',
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: process.env.NODE_ENV === 'production',
        drop_debugger: process.env.NODE_ENV === 'production',
      },
    },
    chunkSizeWarningLimit: 1000,
  },
  server: {
    host: '0.0.0.0',
    port: 5174, // Canonical React port
    strictPort: false,
    cors: {
      origin: [
        'http://localhost:8888',
        'http://host.docker.internal:8888',
        'http://localhost:5175',
        'http://host.docker.internal:5175',
        '*'
      ],
      credentials: true,
    },
    hmr: {
      overlay: true,
      port: 5178,
      host: '0.0.0.0'
    },
    // Proxy for WordPress API calls
    proxy: {
      '/wp-json': {
        target: 'http://localhost:8888',
        changeOrigin: true,
        secure: false,
        timeout: 10000,
      },
      '/wp-admin/admin-ajax.php': {
        target: 'http://localhost:8888',
        changeOrigin: true,
        secure: false,
        timeout: 10000,
      },
      '/wp-content': {
        target: 'http://localhost:8888',
        changeOrigin: true,
        secure: false,
        timeout: 10000
      }
    }
  },
  preview: {
    port: 4174,
    host: true,
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, './src'),
      '@theme': resolve(__dirname, '../'),
      '@wp-content': resolve(__dirname, '../../'),
    },
  },
  optimizeDeps: {
    include: ['react', 'react-dom', 'react-router-dom', 'lucide-react'],
    force: false,
  },
  define: {
    __DEBUG_ENABLED__: JSON.stringify(process.env.VITE_DEBUG_ENABLED === 'true'),
    __DEBUG_LEVEL__: JSON.stringify(process.env.VITE_DEBUG_LEVEL || 'warn'),
    __THEME_MODE__: JSON.stringify('theme-directory'),
  },
  esbuild: {
    treeShaking: true,
    target: 'es2020',
    sourcemap: process.env.NODE_ENV === 'development',
  }
}); 


