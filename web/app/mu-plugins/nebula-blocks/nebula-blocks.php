<?php
/**
 * Plugin Name:       Nebula Blocks
 * Description:       Custom blocks for a Nebula project.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Requires PHP:      8.2
 * Author:            eighteen73
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       nebula-blocks
 *
 * @package           NebulaBlocks
 */

namespace Eighteen73\Nebula\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Useful global constants.
define( 'NEBULA_BLOCKS_URL', plugin_dir_url( __FILE__ ) );
define( 'NEBULA_BLOCKS_PATH', plugin_dir_path( __FILE__ ) );

// Initialise the plugin.
require_once NEBULA_BLOCKS_PATH . 'includes/plugin.php';
