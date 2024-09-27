<?php
/**
 * Config Class.
 *
 * A simple class for grabbing and returning a configuration file from `/config`.
 *
 * @package NebulaBlocks
 */

namespace Eighteen73\Nebula\Blocks\Tools;

/**
 * Config class.
 */
class Config {

	/**
	 * Includes and returns a given PHP config file. The file must return
	 * an array.
	 *
	 * @param  string $name The name of the config file.
	 * @return array
	 */
	public static function get( string $name ): array {

		$file = static::path( "{$name}.php" );

		return (array) apply_filters(
			"nebula/blocks/config/{$name}/",
			file_exists( $file ) ? include $file : []
		);
	}

	/**
	 * Returns the config path or file path if set.
	 *
	 * @param  string $file The file name.
	 * @return string
	 */
	public static function path( string $file = '' ): string {

		$file = trim( $file, '/' );

		return NEBULA_BLOCKS_PATH . ( $file ? "config/{$file}" : 'config' );
	}
}