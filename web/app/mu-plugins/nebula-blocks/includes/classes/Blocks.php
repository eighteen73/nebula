<?php
/**
 * Handles block registration.
 *
 * @package NebulaBlocks
 */

namespace Eighteen73\Nebula\Blocks;

use Eighteen73\Nebula\Core\Contracts\Bootable;

/**
 * Handles block registration.
 */
class Blocks implements Bootable {
	/**
	 * Run on init
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Determines if the class can be booted.
	 *
	 * @return bool
	 */
	public function can_boot(): bool {
		return true;
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
	public function register(): void {
		$blocks_directory = trailingslashit( NEBULA_BLOCKS_PATH . 'build' );

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
