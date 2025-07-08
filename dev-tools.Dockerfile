# BlackCnote Dev Tools - Fixed Dockerfile
FROM node:18-alpine

# Install global tools
RUN npm install -g nodemon concurrently http-server

# Set working directory
WORKDIR /app

# Create a simple dev tools server
RUN echo 'const http = require("http"); const server = http.createServer((req, res) => { res.writeHead(200, {"Content-Type": "text/html"}); res.end("<h1>BlackCnote Dev Tools</h1><p>Development tools are available</p>"); }); server.listen(9229, "0.0.0.0", () => console.log("Dev Tools server running on port 9229"));' > /app/dev-tools-server.js

# Expose port
EXPOSE 9229

# Start dev tools server
CMD ["node", "/app/dev-tools-server.js"]
