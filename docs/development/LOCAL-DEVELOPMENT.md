# BlackCnote Local Development Guide

## Quick Start

### Option 1: Automated Setup (Recommended)
Run the automated setup script:
```bash
# PowerShell (recommended)
.\scripts\start-local-dev.ps1

# Or Batch file
.\scripts\start-local-dev.bat
```

### Option 2: Manual Setup
1. **Start XAMPP Services**
   ```bash
   # Start XAMPP Control Panel
   C:\xampp\xampp-control.exe
   
   # Or start services directly
   C:\xampp\apache_start.bat
   C:\xampp\mysql_start.bat
   ```

2. **Start React Development Server**
   ```bash
   cd react-app
   npm install  # First time only
   npm start
   ```

3. **Access Your Sites**
   - WordPress Site: http://localhost/blackcnote
   - WordPress Admin: http://localhost/blackcnote/wp-admin
   - React Dev Server: http://localhost:3000

## Project Structure

```
BlackCnote/
├── blackcnote/                    # WordPress theme
├── hyiplab-plugin/               # HYIPLab plugin
├── react-app/                    # React frontend
├── wp-content/
│   ├── mu-plugins/              # Must-use plugins
│   └── themes/                  # WordPress themes
├── scripts/                     # Development scripts
└── docs/                        # Documentation
```

## Development Workflow

### WordPress Development
- **Theme Location**: `blackcnote/` directory
- **Plugin Location**: `hyiplab-plugin/` directory
- **MU Plugins**: `wp-content/mu-plugins/` directory
- **Database**: `blackcnote` MySQL database

### React Development
- **Location**: `react-app/` directory
- **Dev Server**: http://localhost:3000
- **Build Output**: `react-app/dist/` directory

### File Synchronization
The development setup automatically copies:
- Theme files to `C:\xampp\htdocs\blackcnote\wp-content\themes\blackcnote\`
- Plugin files to `C:\xampp\htdocs\blackcnote\wp-content\plugins\hyiplab\`
- MU plugins to `C:\xampp\htdocs\blackcnote\wp-content\mu-plugins\`
- Configuration to `C:\xampp\htdocs\blackcnote\wp-config.php`

## Configuration

### WordPress Configuration
- **Database**: `blackcnote`
- **Username**: `root`
- **Password**: (empty)
- **Host**: `localhost`
- **URL**: http://localhost/blackcnote

### Environment Detection
The system automatically detects localhost and enables:
- Debug mode
- Development settings
- Local asset paths

## Available Scripts

### Development Scripts
- `scripts/start-local-dev.ps1` - Start complete development environment
- `scripts/start-local-dev.bat` - Batch version of startup script
- `scripts/load-test.js` - Load testing for performance
- `scripts/performance-optimizer.js` - Performance optimization

### Build Scripts
- `scripts/build-optimizer.js` - Optimize builds
- `scripts/dev-setup.js` - Development environment setup

## Troubleshooting

### Common Issues

1. **XAMPP Services Not Starting**
   - Check if ports 80/443 are in use
   - Run XAMPP as administrator
   - Check XAMPP error logs

2. **Database Connection Issues**
   - Verify MySQL is running
   - Check database exists: `blackcnote`
   - Verify credentials in `wp-config.php`

3. **React Server Issues**
   - Check if port 3000 is available
   - Run `npm install` in `react-app/` directory
   - Check Node.js version compatibility

4. **WordPress Not Loading**
   - Verify Apache is running
   - Check file permissions
   - Verify theme is activated in WordPress admin

### Debug Mode
Debug mode is automatically enabled on localhost:
- Error display: Enabled
- Debug logging: Enabled
- Query logging: Enabled

### Logs
- **Apache Logs**: `C:\xampp\apache\logs\`
- **MySQL Logs**: `C:\xampp\mysql\data\`
- **WordPress Logs**: `C:\xampp\htdocs\blackcnote\wp-content\debug.log`

## Performance Monitoring

### Built-in Monitoring
- Performance dashboard in WordPress admin
- Real-time metrics
- Automated alerting
- Health checks

### Load Testing
```bash
node scripts/load-test.js
```

### Performance Optimization
```bash
node scripts/performance-optimizer.js
```

## Security Features

### Development Security
- CSRF protection
- Rate limiting
- Input validation
- SQL injection prevention
- XSS protection

### Security Headers
- X-Content-Type-Options
- X-Frame-Options
- X-XSS-Protection
- Content Security Policy

## Next Steps

1. **Activate Theme**: Go to WordPress admin → Appearance → Themes → Activate BlackCnote
2. **Activate Plugin**: Go to WordPress admin → Plugins → Activate HYIPLab
3. **Configure Settings**: Set up your investment plans and payment gateways
4. **Customize Theme**: Modify theme files in `blackcnote/` directory
5. **Develop React Components**: Work on frontend in `react-app/` directory

## Support

For issues or questions:
1. Check this documentation
2. Review error logs
3. Check WordPress debug log
4. Verify service status

## Production Deployment

When ready for production:
1. Run `scripts/setup-production.sh`
2. Configure production settings
3. Set up monitoring and alerts
4. Deploy using CI/CD pipeline 