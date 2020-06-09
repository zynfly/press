<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENV}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

declare( strict_types=1 );

use Roots\WPConfig\Config;

if ( ! function_exists( 'env' ) ) {
	/**
	 * Gets the value of an environment variable.
	 *
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	function env( $key, $default = null ) {
		$value = $_ENV[ $key ] ?? false;

		if ( $value === false ) {
			return $default;
		}

		switch ( strtolower( $value ) ) {
			case 'true':
			case '(true)':
				return true;
			case 'false':
			case '(false)':
				return false;
			case 'empty':
			case '(empty)':
				return '';
			case 'null':
			case '(null)':
				return;
		}

		if ( preg_match( '/\A([\'"])(.*)\1\z/', $value, $matches ) ) {
			return $matches[2];
		}

		return $value;
	}
}
/**
 * Directory containing all of the site's files
 *
 * @var string
 */
$root_dir = dirname( __DIR__ );

/**
 * Document Root
 *
 * @var string
 */
$webroot_dir = $root_dir . '/public';


/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = new \Symfony\Component\Dotenv\Dotenv();
if ( file_exists( $root_dir . '/.env' ) ) {
	$dotenv->load( $root_dir . '/.env' );
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define( 'WP_ENV', env( 'WP_ENV', 'production' ) );

/**
 * URLs
 */
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
Config::define( 'WP_HOME', $request->getSchemeAndHttpHost() );
Config::define( 'WP_SITEURL', Config::get( 'WP_HOME' ) . '/core' );

/**
 * Custom Content Directory
 */
Config::define( 'CONTENT_DIR', '' );
Config::define( 'WP_CONTENT_DIR', $webroot_dir . Config::get( 'CONTENT_DIR' ) );
Config::define( 'WP_CONTENT_URL', Config::get( 'WP_HOME' ) . Config::get( 'CONTENT_DIR' ) );

/**
 * DB settings
 */
Config::define( 'DB_NAME', env( 'DB_NAME' ) );
Config::define( 'DB_USER', env( 'DB_USER' ) );
Config::define( 'DB_PASSWORD', env( 'DB_PASSWORD' ) );
Config::define( 'DB_HOST', env( 'DB_HOST', 'localhost' ) );
Config::define( 'DB_CHARSET', 'utf8mb4' );
Config::define( 'DB_COLLATE', '' );
$table_prefix = env( 'DB_PREFIX', 'wp_' );

if ( env( 'DATABASE_URL' ) ) {
	$dsn = (object) parse_url( env( 'DATABASE_URL' ) );

	Config::define( 'DB_NAME', substr( $dsn->path, 1 ) );
	Config::define( 'DB_USER', $dsn->user );
	Config::define( 'DB_PASSWORD', isset( $dsn->pass ) ? $dsn->pass : null );
	Config::define( 'DB_HOST', isset( $dsn->port ) ? "{$dsn->host}:{$dsn->port}" : $dsn->host );
}

/**
 * Authentication Unique Keys and Salts
 */
Config::define( 'AUTH_KEY', env( 'AUTH_KEY' ) );
Config::define( 'SECURE_AUTH_KEY', env( 'SECURE_AUTH_KEY' ) );
Config::define( 'LOGGED_IN_KEY', env( 'LOGGED_IN_KEY' ) );
Config::define( 'NONCE_KEY', env( 'NONCE_KEY' ) );
Config::define( 'AUTH_SALT', env( 'AUTH_SALT' ) );
Config::define( 'SECURE_AUTH_SALT', env( 'SECURE_AUTH_SALT' ) );
Config::define( 'LOGGED_IN_SALT', env( 'LOGGED_IN_SALT' ) );
Config::define( 'NONCE_SALT', env( 'NONCE_SALT' ) );

/**
 * Custom Settings
 */
Config::define( 'AUTOMATIC_UPDATER_DISABLED', env( 'AUTOMATIC_UPDATER_DISABLED', true ) );
Config::define( 'WP_AUTO_UPDATE_CORE', env( 'WP_AUTO_UPDATE_CORE', 'minor' ) );
Config::define( 'DISABLE_WP_CRON', env( 'DISABLE_WP_CRON', false ) );
// Disable the plugin and theme file editor in the admin
Config::define( 'DISALLOW_FILE_EDIT', env( 'DISALLOW_FILE_EDIT', true ) );
// Disable plugin and theme updates and installation from the admin
Config::define( 'DISALLOW_FILE_MODS', env( 'DISALLOW_FILE_MODS', true ) );

/**
 * Debugging Settings
 */
Config::define( 'WP_DEBUG_DISPLAY', false );
Config::define( 'WP_DEBUG_LOG', env( 'WP_DEBUG_LOG' ) ? $root_dir . '/log/debug.log' : false );
Config::define( 'SCRIPT_DEBUG', false );
ini_set( 'display_errors', '0' );

/**
 * Default Theme
 */
if ( env( 'WP_DEFAULT_THEME' ) ) {
	Config::define( 'WP_DEFAULT_THEME', env( 'WP_DEFAULT_THEME' ) );
}

/**
 * WP Cache
 */
Config::define( 'WP_CACHE', env( 'WP_CACHE', true ) );

/**
 * Multisite
 */
if ( env( 'WP_ALLOW_MULTISITE' ) ) {
	Config::define( 'WP_ALLOW_MULTISITE', true );
}
if ( env( 'MULTISITE' ) ) {
	Config::define( 'MULTISITE', env( 'MULTISITE' ) );

	Config::define( 'DOMAIN_CURRENT_SITE', $_SERVER['HTTP_HOST'] );
	$multisite_configs = [
		'SUBDOMAIN_INSTALL',
		'PATH_CURRENT_SITE',
		'SITE_ID_CURRENT_SITE',
		'BLOG_ID_CURRENT_SITE',
		'NOBLOGREDIRECT'
	];
	foreach ( $multisite_configs as $multisite_config ) {
		if ( env( $multisite_config ) ) {
			Config::define( $multisite_config, env( $multisite_config ) );
		}
	}
	unset( $multisite_configs );
	unset( $multisite_config );
}

/**
 * Cookies
 */
$cookies_configs = [
	'COOKIEHASH',
	'COOKIE_DOMAIN',
	'ADMIN_COOKIE_PATH',
	'COOKIEPATH',
	'SITECOOKIEPATH',
	'TEST_COOKIE',
	'AUTH_COOKIE',
	'USER_COOKIE',
	'PASS_COOKIE',
	'SECURE_AUTH_COOKIE',
	'LOGGED_IN_COOKIE'
];
foreach ( $cookies_configs as $cookies_config ) {
	if ( env( $cookies_config ) ) {
		Config::define( $cookies_config, env( $cookies_config ) );
	}
}

unset( $cookies_configs );
unset( $cookies_config );

// Set the trash to less days to optimize WordPress.
Config::define( 'EMPTY_TRASH_DAYS', env( 'EMPTY_TRASH_DAYS', 7 ) );

// Specify the number of post revisions.
Config::define( 'WP_POST_REVISIONS', env( 'WP_POST_REVISIONS', 2 ) );

// Cleanup WordPress image edits.
Config::define( 'IMAGE_EDIT_OVERWRITE', env( 'IMAGE_EDIT_OVERWRITE', true ) );

//WP Repair
Config::define( 'WP_ALLOW_REPAIR', env( 'WP_ALLOW_REPAIR', false ) );

// Disable technical issues emails.
// https://make.wordpress.org/core/2019/04/16/fatal-error-recovery-mode-in-5-2/
Config::define( 'WP_DISABLE_FATAL_ERROR_HANDLER', env( 'WP_DISABLE_FATAL_ERROR_HANDLER', false ) );


/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if ( file_exists( $env_config ) ) {
	require_once $env_config;
}
Config::apply();

//var_dump( WP_DEBUG_LOG );
//exit;
/**
 * Bootstrap WordPress
 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', $webroot_dir . '/core/' );
}