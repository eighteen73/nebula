<?php
/**
 * Config Class.
 *
 * A simple class for grabbing and returning a configuration file from `/config`.
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\Tools;

/**
 * Config class.
 */
class Config {

	/**
	 * Includes and returns a given PHP config file. The file must return
	 * an array.
	 *
	 * @param  string $name The name of the config file.
	 * @param  string $base_path The base file path.
	 * @return array
	 */
	public static function get( string $name, string $base_path ): array {

		$file = static::path( $base_path, "{$name}.php" );

		return (array) file_exists( $file ) ? include $file : [];
	}

	/**
	 * Returns the config path or file path if set.
	 *
	 * @param  string $base_path The base file path.
	 * @param  string $file The file name.
	 * @return string
	 */
	public static function path( string $base_path, string $file = '' ): string {

		$file = trim( $file, '/' );

		return $base_path . ( $file ? "/config/{$file}" : 'config' );
	}
}
