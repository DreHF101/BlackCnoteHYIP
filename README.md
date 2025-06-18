# BlackCnote WordPress Theme

A custom WordPress theme for BlackCnote investment platform.

## Development Setup

### Prerequisites
- Docker
- Docker Compose
- Git

### Installation Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/DreHF101/BlackCnoteHYIP.git
   cd BlackCnoteHYIP
   ```

2. Start the Docker containers:
   ```bash
   docker-compose up -d
   ```

3. Access WordPress:
   - URL: http://localhost:8000
   - Default credentials will be created on first run

4. Activate the theme:
   - Log in to WordPress admin
   - Go to Appearance > Themes
   - Activate the BlackCnote theme

### Development Workflow

1. Theme files are located in `wp-content/themes/blackcnote/`
2. Changes to theme files will be reflected immediately
3. Use `docker-compose down` to stop the containers
4. Use `docker-compose up -d` to start the containers again

### Database

- Database name: wordpress
- Username: wordpress
- Password: wordpress
- Host: db
- Port: 3306

### File Structure

```
wp-content/
└── themes/
    └── blackcnote/
        ├── assets/
        ├── inc/
        ├── template-parts/
        ├── style.css
        ├── functions.php
        └── index.php
```

## Contributing

1. Create a new branch for your feature
2. Make your changes
3. Submit a pull request

## License

This project is licensed under the GPL v2 or later.
