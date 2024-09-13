<?php
/**
 * Plugin Name:       eighteen73 Blocks
 * Description:       Custom blocks for an eighteen73 project.
 * Version:           0.1.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            eighteen73
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       eighteen73-blocks
 *
 * @package           Eighteen73Blocks
 */

namespace Eighteen73\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Useful global constants.
define( 'EIGHTEEN73_BLOCKS_URL', plugin_dir_url( __FILE__ ) );
define( 'EIGHTEEN73_BLOCKS_PATH', plugin_dir_path( __FILE__ ) );

// Require the autoloader.
require_once 'autoload.php';

// Initialise classes.
Blocks::instance()->setup();
