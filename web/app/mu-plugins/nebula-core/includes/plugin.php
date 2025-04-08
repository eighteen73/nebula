<?php
/**
 * Bootstrap all of the plugin classes.
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core;

use Eighteen73\Nebula\Core\Tools\Config;

/**
 * Mini container.  This allows us to set up single instances of our objects
 * without using the singleton pattern and gives third-party devs easy access to
 * the objects if they need to unhook actions/filters added by the classes.
 *
 * Developers can access the objects via `plugin( $class_name )`.
 *
 * @access public
 * @param  string $class_name The class name
 * @return mixed
 */
function plugin( string $class_name = '' ): mixed {
	static $classes = null;

	// On first run, create new components and boot them.
	if ( is_null( $classes ) ) {
		$bindings = Config::get( 'bindings', NEBULA_CORE_PATH );

		foreach ( $bindings as $binding ) {
			$classes[ $binding ] = new $binding();

			if ( $classes[ $binding ]->can_boot() ) {
				$classes[ $binding ]->boot();
			}
		}
	}

	return $class_name ? $classes[ $class_name ] : $classes;
}

/**
 * Bootstrap plugin.
 * Run a small bootstrapping routine.
 */
do_action( 'nebula_core/booted', plugin() );
