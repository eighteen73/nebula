<?php
/**
 * Configuration overrides for WP_ENVIRONMENT_TYPE === 'development'
 *
 * @package Nebula
 */

namespace Eighteen73\Nebula;

use Roots\WPConfig\Config;

Config::define( 'SAVEQUERIES', true );
Config::define( 'WP_DEBUG', true );
Config::define( 'WP_DEBUG_DISPLAY', true );
Config::define( 'WP_DEBUG_LOG', $_ENV['WP_DEBUG_LOG'] ?? true );
Config::define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );
Config::define( 'SCRIPT_DEBUG', true );
Config::define( 'DISALLOW_INDEXING', true );

ini_set( 'display_errors', '1' ); // PHPCS:ignore:WordPress.PHP.IniSet.display_errors_Blacklisted

// Enable plugin and theme updates and installation from the admin
Config::define( 'DISALLOW_FILE_MODS', false );
