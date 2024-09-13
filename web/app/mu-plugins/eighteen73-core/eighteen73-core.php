<?php
/**
 * Plugin Name:       Eighteen73 core
 * Description:       Core functionality for an eighteen73 project.
 * Version:           0.1.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            eighteen73
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       eighteen73-core
 *
 * @package           Eighteen73Core
 */

namespace Eighteen73\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Useful global constants.
define( 'EIGHTEEN73_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'EIGHTEEN73_CORE_PATH', plugin_dir_path( __FILE__ ) );

// Require the autoloader.
require_once 'autoload.php';
