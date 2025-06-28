# Deployment Guide

This guide explains how to deploy the BlackCnote project to production.

## Prerequisites
- Docker (production server)
- Domain name and SSL certificate

## Steps
1. Update environment variables and secrets.
2. Build the React app for production:
   ```bash
   cd react-app
   npm run build
   ```
3. Use the production Docker Compose file:
   ```bash
   docker-compose -f config/docker/docker-compose.prod.yml up -d
   ```
4. Configure your domain and SSL (see Nginx/Apache configs).
5. Test all services and endpoints.

## Post-Deployment
- Monitor logs and performance.
- Set up backups and security updates.
- See `docs/deployment/DEPLOYMENT-GUIDE.md` for advanced options. 