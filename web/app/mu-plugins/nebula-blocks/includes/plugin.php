<?php
/**
 * Bootstrap all of the plugin classes.
 *
 * @package Pulsar
 */

namespace Eighteen73\Nebula\Blocks;

use Eighteen73\Nebula\Blocks\Tools\Config;

/**
 * Mini container.  This allows us to set up single instances of our objects
 * without using the singleton pattern and gives third-party devs easy access to
 * the objects if they need to unhook actions/filters added by the classes.
 *
 * Developers can access the objects via `plugin( $abstract )`.
 *
 * @access public
 * @param  string $abstract The class abstract
 * @return mixed
 */
function plugin( string $abstract = '' ): mixed {
	static $classes = null;

	// On first run, create new components and boot them.
	if ( is_null( $classes ) ) {
		$bindings = Config::get( 'bindings' );

		foreach ( $bindings as $binding ) {
			$classes[ $binding ] = new $binding();

			if ( $classes[ $binding ]->can_boot() ) {
				$classes[ $binding ]->boot();
			}
		}
	}

	return $abstract ? $classes[ $abstract ] : $classes;
}

/**
 * Bootstrap plugin.
 * Run a small bootstrapping routine.
 */
do_action( 'nebula_core/booted', plugin() );
