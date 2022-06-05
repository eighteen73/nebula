<?php
/**
 * Plugin Name:  Nebula Carbon Fields
 * Plugin URI:   https://github.com/eighteen73/nebula/
 * Description:  Initialize Carbon Fields, so they're available for use by other plugins and themes.
 * Version:      0.1.0
 * Author:       eighteen73
 * License:      MIT License
 *
 * @package Nebula
 */

namespace Eighteen73\Nebula;

use Carbon_Fields\Carbon_Fields;

add_action(
	'after_setup_theme',
	function() {
		define( 'Carbon_Fields\URL', home_url( '/app/libraries/carbon-fields' ) );
		Carbon_Fields::boot();
	}
);
