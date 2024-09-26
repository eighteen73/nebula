<?php
/**
 * Register the autoloader for the plugin's plugin classes.
 *
 * Based off the official PSR-4 autoloader example found here:
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 *
 * @param string $class_name The fully-qualified class name
 *
 * @return void
 *
 * @package NebulaCore
 */

spl_autoload_register(
	function ( $class_name ) {
		$namspaces = [
			'Eighteen73\\Nebula\\Core\\' => __DIR__ . '/includes/classes/',
		];
		foreach ( $namspaces as $prefix => $base_dir ) {
			$len = strlen( $prefix );
			if ( 0 !== strncmp( $prefix, $class_name, $len ) ) {
				return;
			}
			$relative_class = substr( $class_name, $len );
			$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
			if ( file_exists( $file ) ) {
				require $file;
			}
		}
	}
);
