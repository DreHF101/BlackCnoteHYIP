# BlackCnote Platform Environment Configuration
# Copy this file to .env and update with your actual values

# =============================================================================
# WORDPRESS CONFIGURATION
# =============================================================================
WORDPRESS_URL=http://localhost/blackcnote
BS_PORT=3000
BS_UI_PORT=3001
VITE_PORT=5173

# Database Configuration
DB_NAME=blackcnote
DB_USER=root
DB_PASSWORD=
DB_HOST=localhost
DB_CHARSET=utf8mb4
DB_COLLATE=utf8mb4_unicode_ci

# WordPress Security Keys (generate at https://api.wordpress.org/secret-key/1.1/salt/)
AUTH_KEY='put your unique phrase here'
SECURE_AUTH_KEY='put your unique phrase here'
LOGGED_IN_KEY='put your unique phrase here'
NONCE_KEY='put your unique phrase here'
AUTH_SALT='put your unique phrase here'
SECURE_AUTH_SALT='put your unique phrase here'
LOGGED_IN_SALT='put your unique phrase here'
NONCE_SALT='put your unique phrase here'

# WordPress Settings
WP_DEBUG=true
WP_DEBUG_LOG=true
WP_DEBUG_DISPLAY=false
SCRIPT_DEBUG=true
SAVEQUERIES=true

# =============================================================================
# DEVELOPMENT CONFIGURATION
# =============================================================================
NODE_ENV=development
VITE_API_URL=http://localhost/blackcnote/wp-json
VITE_WP_URL=http://localhost/blackcnote

# =============================================================================
# BROWSERSYNC CONFIGURATION
# =============================================================================
BS_OPEN=true
BS_NOTIFY=true
BS_GHOST_MODE=true

# =============================================================================
# HYIPLAB PLATFORM CONFIGURATION
# =============================================================================
APP_NAME=BlackCnote Platform
APP_ENV=development
APP_DEBUG=true
APP_VERSION=1.0.0
APP_URL=http://localhost/blackcnote
APP_TIMEZONE=UTC

# Database Configuration for HyipLab
HYIPLAB_DB_HOST=localhost
HYIPLAB_DB_NAME=blackcnote_hyiplab
HYIPLAB_DB_USER=hyiplab_user
HYIPLAB_DB_PASSWORD=your_hyiplab_password_here

# =============================================================================
# PAYMENT GATEWAY CONFIGURATION
# =============================================================================

# Stripe Configuration
STRIPE_PUBLIC_KEY=pk_test_your_stripe_public_key
STRIPE_SECRET_KEY=sk_test_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# PayPal Configuration
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_client_secret
PAYPAL_MODE=sandbox

# CoinGate Configuration
COINGATE_API_TOKEN=your_coingate_api_token
COINGATE_ENVIRONMENT=sandbox

# Mollie Configuration
MOLLIE_API_KEY=test_your_mollie_api_key

# =============================================================================
# EMAIL CONFIGURATION
# =============================================================================

# SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@blackcnote.com
MAIL_FROM_NAME="${APP_NAME}"

# SendGrid Configuration
SENDGRID_API_KEY=your_sendgrid_api_key

# Mailjet Configuration
MAILJET_API_KEY=your_mailjet_api_key
MAILJET_API_SECRET=your_mailjet_api_secret

# =============================================================================
# SMS CONFIGURATION
# =============================================================================

# Twilio Configuration
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM_NUMBER=+1234567890

# MessageBird Configuration
MESSAGEBIRD_API_KEY=your_messagebird_api_key

# TextMagic Configuration
TEXTMAGIC_USERNAME=your_textmagic_username
TEXTMAGIC_API_KEY=your_textmagic_api_key

# =============================================================================
# SECURITY CONFIGURATION
# =============================================================================
SESSION_SECURE_COOKIES=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Rate Limiting
RATE_LIMIT_REQUESTS=60
RATE_LIMIT_MINUTES=1

# =============================================================================
# FILE UPLOAD CONFIGURATION
# =============================================================================
MAX_FILE_SIZE=10485760
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf,doc,docx
UPLOAD_PATH=uploads

# =============================================================================
# CRYPTO CONFIGURATION
# =============================================================================

# Bitcoin Configuration
BTC_NODE_URL=http://localhost:8332
BTC_RPC_USER=your_btc_rpc_user
BTC_RPC_PASSWORD=your_btc_rpc_password

# Ethereum Configuration
ETH_NODE_URL=https://mainnet.infura.io/v3/your_project_id
ETH_PRIVATE_KEY=your_ethereum_private_key

# =============================================================================
# CACHE CONFIGURATION
# =============================================================================
CACHE_DRIVER=file
CACHE_TTL=3600

# =============================================================================
# LOGGING CONFIGURATION
# =============================================================================
LOG_CHANNEL=stack
LOG_LEVEL=debug
LOG_DAYS=14

# =============================================================================
# DEVELOPMENT TOOLS
# =============================================================================
XDEBUG_MODE=develop,debug
XDEBUG_CLIENT_PORT=9003 