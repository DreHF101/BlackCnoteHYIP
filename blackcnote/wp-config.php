<?php
/**
 * BlackCnote WordPress Configuration
 * 
 * EXCLUSIVELY configured for: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
 * 
 * This file has been modified for use in Docker with the BlackCnote project.
 * All WordPress content is served exclusively from the blackcnote directory.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 * @package BlackCnote
 */

// IMPORTANT: This file is EXCLUSIVELY for the BlackCnote project
// All WordPress content is served from: ./blackcnote/wp-content

// a helper function to lookup "env_FILE", "env", then fallback
if (!function_exists('getenv_docker')) {
	// https://github.com/docker-library/wordpress/issues/588 (WP-CLI will load this file 2x)
	function getenv_docker($env, $default) {
		if ($fileEnv = getenv($env . '_FILE')) {
			return rtrim(file_get_contents($fileEnv), "\r\n");
		}
		else if (($val = getenv($env)) !== false) {
			return $val;
		}
		else {
			return $default;
		}
	}
}

// ** Database settings - BlackCnote Configuration ** //
/** The name of the database for BlackCnote */
define( 'DB_NAME', getenv_docker('WORDPRESS_DB_NAME', 'blackcnote') );

/** Database username */
define( 'DB_USER', getenv_docker('WORDPRESS_DB_USER', 'root') );

/** Database password */
define( 'DB_PASSWORD', getenv_docker('WORDPRESS_DB_PASSWORD', 'blackcnote_password') );

/** Database hostname */
define( 'DB_HOST', getenv_docker('WORDPRESS_DB_HOST', 'mysql') );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', getenv_docker('WORDPRESS_DB_CHARSET', 'utf8mb4') );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', getenv_docker('WORDPRESS_DB_COLLATE', '') );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         getenv_docker('WORDPRESS_AUTH_KEY',         'k8J#mP9$vL2@nQ7&hF4*wE1!cR5%tY8^uI3(oA6)sD9') );
define( 'SECURE_AUTH_KEY',  getenv_docker('WORDPRESS_SECURE_AUTH_KEY',  'xK4#jH7$mN2@qW5&pL8*vB1!fC6%gM9^tR3(oE6)sA9') );
define( 'LOGGED_IN_KEY',    getenv_docker('WORDPRESS_LOGGED_IN_KEY',    'zL5#kI8$nO3@rX6&qM9*wC2!gD7%hN0^uS4(pF7)tB0') );
define( 'NONCE_KEY',        getenv_docker('WORDPRESS_NONCE_KEY',        'aM6#lJ9$oP4@sY7&rN0*xD3!hE8%iO1^vT5(qG8)uC1') );
define( 'AUTH_SALT',        getenv_docker('WORDPRESS_AUTH_SALT',        'bN7#mK0$oQ5@tZ8&sO1*xE4!hF9%jP2^vU6(qH9)uD2') );
define( 'SECURE_AUTH_SALT', getenv_docker('WORDPRESS_SECURE_AUTH_SALT', 'cO8#nL1$pR6@uA9&tP2*yF5!iG0%kQ3^wV7(rI0)vE3') );
define( 'LOGGED_IN_SALT',   getenv_docker('WORDPRESS_LOGGED_IN_SALT',   'dP9#oM2$qS7@vB0&uQ3*zG6!jH1%lR4^xW8(sJ1)wF4') );
define( 'NONCE_SALT',       getenv_docker('WORDPRESS_NONCE_SALT',       'eQ0#pN3$rT8@wC1&vR4*aH7!kI2%mS5^yX9(tK2)xG5') );
/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = getenv_docker('WORDPRESS_TABLE_PREFIX', 'wp_');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define( 'WP_DEBUG', getenv_docker('WP_DEBUG', true) );
define( 'WP_DEBUG_DISPLAY', getenv_docker('WP_DEBUG_DISPLAY', true) );
define( 'WP_DEBUG_LOG', getenv_docker('WP_DEBUG_LOG', true) );
define( 'SCRIPT_DEBUG', getenv_docker('SCRIPT_DEBUG', true) );
define( 'SAVEQUERIES', getenv_docker('SAVEQUERIES', true) );

/* Add any custom values between this line and the "stop editing" line. */

// BLACKCNOTE EXCLUSIVE CONFIGURATION
// ===================================

// EXCLUSIVE CONTENT DIRECTORY: ./blackcnote/wp-content
define('WP_CONTENT_DIR', __DIR__ . '/wp-content');
define('WP_CONTENT_URL', getenv_docker('WP_CONTENT_URL', 'http://localhost:8888/wp-content'));

// WordPress URLs - EXCLUSIVELY for BlackCnote - FIXED FOR DOCKER
define('WP_HOME', getenv_docker('WP_HOME', 'http://localhost:8888'));
define('WP_SITEURL', getenv_docker('WP_SITEURL', 'http://localhost:8888'));

// Enable direct file system access for BlackCnote development
define('FS_METHOD', 'direct');

// Force HTTP for local development - PREVENT HTTPS REDIRECTS
$_SERVER['HTTPS'] = 'off';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'http';
$_SERVER['HTTP_X_FORWARDED_SSL'] = 'off';

// Docker-specific configurations
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
}
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $_SERVER['HTTPS'] = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'on' : 'off';
}

// Performance and Security Settings for BlackCnote
define('WP_CACHE', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);

// Security Settings - DISABLE HTTPS FOR LOCAL DEVELOPMENT
define('FORCE_SSL_ADMIN', false);
define('FORCE_SSL_LOGIN', false);

// Additional Security Settings
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', false); // Allow plugin/theme updates for development
define('DISALLOW_UNFILTERED_HTML', true);

// Memory limits for BlackCnote - INCREASED FOR BETTER PERFORMANCE
define('WP_MEMORY_LIMIT', getenv_docker('WP_MEMORY_LIMIT', '512M'));
define('WP_MAX_MEMORY_LIMIT', getenv_docker('WP_MAX_MEMORY_LIMIT', '1024M'));

// Performance optimizations
define('WP_POST_REVISIONS', 3); // Reduce post revisions
define('AUTOSAVE_INTERVAL', 300); // Increase autosave interval
define('EMPTY_TRASH_DAYS', 7); // Reduce trash retention
define('WP_CRON_LOCK_TIMEOUT', 120); // Increase cron lock timeout

// Database optimizations
define('WP_USE_EXT_MYSQL', false);
define('CUSTOM_USER_TABLE', $table_prefix . 'users');
define('CUSTOM_USER_META_TABLE', $table_prefix . 'usermeta');

// Cache optimizations
define('WP_CACHE_KEY_SALT', 'blackcnote_' . md5(__FILE__));
define('WP_CACHE_HTTP_HOST', 'localhost:8888');

// Disable unnecessary features for better performance
define('DISABLE_WP_CRON', false); // Keep cron for now
define('WP_HTTP_BLOCK_EXTERNAL', false);

// Custom uploads directory within blackcnote
define('UPLOADS', 'wp-content/uploads');

// If we're behind a proxy server and using HTTPS, we need to alert WordPress of that fact
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
	$_SERVER['HTTPS'] = 'on';
}

if ($configExtra = getenv_docker('WORDPRESS_CONFIG_EXTRA', '')) {
	eval($configExtra);
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
