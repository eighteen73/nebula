<?php
/**
 * Configuration overrides for WP_ENVIRONMENT_TYPE === 'development'
 *
 * @package Nebula
 */

namespace Eighteen73\Nebula;

use Roots\WPConfig\Config;

/**
 * Debugging settings
 */
Config::define( 'SCRIPT_DEBUG', true );
Config::define( 'WP_DEBUG', true );
Config::define( 'WP_DEBUG_DISPLAY', true );
Config::define( 'WP_DEBUG_LOG', $_ENV['WP_DEBUG_LOG'] ?? true );
Config::define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );

/**
 * Other development preferences
 */
Config::define( 'DISALLOW_FILE_MODS', false );
Config::define( 'DISALLOW_INDEXING', true );
Config::define( 'SAVEQUERIES', true );
Config::define( 'WP_CACHE', false );
Config::define( 'WP_DEVELOPMENT_MODE', 'all' );
