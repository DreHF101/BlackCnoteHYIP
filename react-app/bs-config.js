export default {
  "proxy": "http://localhost/blackcnote",
  "files": [
    "blackcnote/**/*.php",
    "blackcnote/**/*.css",
    "blackcnote/**/*.js",
    "blackcnote/**/*.html",
    "hyiplab/**/*.php",
    "hyiplab/**/*.css",
    "hyiplab/**/*.js",
    "src/**/*.tsx",
    "src/**/*.ts",
    "src/**/*.css",
    "src/**/*.js",
    "public/**/*"
  ],
  "ignore": [
    "node_modules",
    "vendor",
    "hyiplab/vendor",
    ".git",
    "*.log"
  ],
  "port": 3000,
  "ui": {
    "port": 3001
  },
  "reloadDelay": 0,
  "reloadDebounce": 250,
  "reloadThrottle": 0,
  "open": true,
  "browser": "default",
  "notify": true,
  "ghostMode": {
    "clicks": true,
    "scroll": true,
    "forms": {
      "submit": true,
      "inputs": true,
      "toggles": true
    }
  },
  "logLevel": "info",
  "snippetOptions": {
    "ignorePaths": [
      "/wp-admin/**"
    ]
  },
  "rewriteRules": [
    {
      "match": {},
      "replace": "localhost/blackcnote"
    }
  ],
  "injectChanges": true,
  "watchEvents": [
    "change",
    "add",
    "unlink",
    "addDir",
    "unlinkDir"
  ],
  "watchOptions": {
    "ignored": [
      "node_modules",
      "vendor",
      "hyiplab/vendor",
      ".git",
      "*.log"
    ],
    "usePolling": true,
    "interval": 1000
  }
};