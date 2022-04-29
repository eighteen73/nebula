<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENVIRONMENT_TYPE}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 *
 * @package Nebula
 */

namespace Eighteen73\Nebula;

use Roots\WPConfig\Config;
use Dotenv\Dotenv;

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
$webroot_dir = $root_dir . '/web';

/**
 * Use Dotenv to set required environment variables
 */
$dotenv = Dotenv::createMutable( $root_dir );
$dotenv->load();
$dotenv->required( [ 'WP_HOME', 'WP_SITEURL' ] );
if ( ! isset( $_ENV['DATABASE_URL'] ) ) {
	$dotenv->required( [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD' ] );
}

/**
 * Set up our global environment constant and load its config first
 * Fails of misconfiguration to ensure proper configuration
 */
switch ( $_ENV['WP_ENVIRONMENT_TYPE'] ) {
	case 'development':
	case 'local':
		Config::define( 'WP_ENVIRONMENT_TYPE', 'development' );
		break;
	case 'staging':
		Config::define( 'WP_ENVIRONMENT_TYPE', 'staging' );
		break;
	case 'production':
		Config::define( 'WP_ENVIRONMENT_TYPE', 'production' );
		break;
	default:
		echo 'Missing or invalid WP_ENVIRONMENT_TYPE setting.';
		die;
}

/**
 * URLs
 */
Config::define( 'WP_HOME', $_ENV['WP_HOME'] );
Config::define( 'WP_SITEURL', $_ENV['WP_SITEURL'] );

/**
 * Custom Content Directory
 */
Config::define( 'CONTENT_DIR', '/app' );
Config::define( 'WP_CONTENT_DIR', $webroot_dir . Config::get( 'CONTENT_DIR' ) );
Config::define( 'WP_CONTENT_URL', Config::get( 'WP_HOME' ) . Config::get( 'CONTENT_DIR' ) );

/**
 * DB settings
 */
Config::define( 'DB_NAME', $_ENV['DB_NAME'] );
Config::define( 'DB_USER', $_ENV['DB_USER'] );
Config::define( 'DB_PASSWORD', $_ENV['DB_PASSWORD'] );
Config::define( 'DB_HOST', $_ENV['DB_HOST'] ?? 'localhost' );
Config::define( 'DB_CHARSET', 'utf8mb4' );
Config::define( 'DB_COLLATE', '' );
$table_prefix = $_ENV['DB_PREFIX'] ?? 'wp_';

if ( isset( $_ENV['DATABASE_URL'] ) ) {
	$dsn = (object) wp_parse_url( $_ENV['DATABASE_URL'] );

	Config::define( 'DB_NAME', substr( $dsn->path, 1 ) );
	Config::define( 'DB_USER', $dsn->user );
	Config::define( 'DB_PASSWORD', isset( $dsn->pass ) ? $dsn->pass : null );
	Config::define( 'DB_HOST', isset( $dsn->port ) ? "{$dsn->host}:{$dsn->port}" : $dsn->host );
}

/**
 * Authentication Unique Keys and Salts
 */
Config::define( 'AUTH_KEY', $_ENV['AUTH_KEY'] );
Config::define( 'SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY'] );
Config::define( 'LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY'] );
Config::define( 'NONCE_KEY', $_ENV['NONCE_KEY'] );
Config::define( 'AUTH_SALT', $_ENV['AUTH_SALT'] );
Config::define( 'SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT'] );
Config::define( 'LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT'] );
Config::define( 'NONCE_SALT', $_ENV['NONCE_SALT'] );

/**
 * Custom Settings
 */
Config::define( 'AUTOMATIC_UPDATER_DISABLED', true );
Config::define( 'DISABLE_WP_CRON', $_ENV['DISABLE_WP_CRON'] ?? false );
// Disable the plugin and theme file editor in the admin
Config::define( 'DISALLOW_FILE_EDIT', true );
// Disable plugin and theme updates and installation from the admin
Config::define( 'DISALLOW_FILE_MODS', true );
// Limit the number of post revisions that WordPress stores (true (default WP): store every revision)
Config::define( 'WP_POST_REVISIONS', $_ENV['WP_POST_REVISIONS'] ?? true );

/**
 * Debugging Settings
 */
Config::define( 'WP_DEBUG_DISPLAY', false );
Config::define( 'WP_DEBUG_LOG', false );
Config::define( 'SCRIPT_DEBUG', false );
ini_set( 'display_errors', '0' ); // PHPCS:ignore:WordPress.PHP.IniSet.display_errors_Blacklisted

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

$env_config = __DIR__ . '/environments/' . Config::get( 'WP_ENVIRONMENT_TYPE' ) . '.php';

if ( file_exists( $env_config ) ) {
	require_once $env_config;
}

Config::apply();

/**
 * Bootstrap WordPress
 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', $webroot_dir . '/wp/' );
}
