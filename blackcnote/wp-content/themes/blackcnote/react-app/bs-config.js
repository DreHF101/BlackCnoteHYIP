module.exports = {
  proxy: "http://wordpress:80",
  port: 3000,
  ui: {
    port: 3001
  },
  files: [
    "/var/www/html/**/*.php",
    "/var/www/html/**/*.js", 
    "/var/www/html/**/*.css",
    "/app/dist/**/*.{js,jsx,ts,tsx}"
  ],
  notify: true,
  open: false,
  logLevel: "info"
};