<?php
/**
 * Handles block registration.
 *
 * @package Eighteen73Blocks
 */

namespace Eighteen73\Blocks;

/**
 * Handles block registration.
 */
class Blocks {
	use Singleton;

	/**
	 * Run on init
	 *
	 * @return void
	 */
	public function setup() {
		add_action( 'init', [ $this, 'register' ] );

		add_action('wp_body_open', function() {
			echo '<div id="eighteen73-blocks"></div>';
		});
	}

	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @return void
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	public function register() {
		$blocks_directory = trailingslashit( EIGHTEEN73_BLOCKS_PATH . 'build' );

		// Register all the blocks in the plugin.
		if ( file_exists( $blocks_directory ) ) {
			$block_json_files = glob( $blocks_directory . '*/block.json' );

			// Auto register all blocks that were found.
			foreach ( $block_json_files as $filename ) {
				$block_folder = dirname( $filename );
				register_block_type( $block_folder );
			}
		}
	}
}
