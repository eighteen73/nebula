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
 * @package NebulaBlocks
 */

spl_autoload_register(
	function ( $class ) {
		$namspaces = [
			'Eighteen73\\Nebula\\Blocks' => __DIR__ . '/classes/',
		];
		foreach ( $namspaces as $prefix => $base_dir ) {
			$len = strlen( $prefix );
			if ( 0 !== strncmp( $prefix, $class, $len ) ) {
				return;
			}
			$relative_class = substr( $class, $len );
			$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
			if ( file_exists( $file ) ) {
				require $file;
			}
		}
	}
);
