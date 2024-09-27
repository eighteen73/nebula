<?php
/**
 * PostType
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\Contracts;

use Exception;
use function register_extended_post_type;

/**
 * Abstract class for post types.
 *
 * The `$args` parameter accepts all the standard arguments for `register_post_type()` in addition to several custom
 * arguments that provide extended functionality. Some of the default arguments differ from the defaults in
 * `register_post_type()`.
 *
 * @link https://github.com/johnbillion/extended-cpts/wiki/Registering-Post-Types
 * @see register_post_type() for default arguments.
 *
 * @param string   $post_type The post type name.
 * @param mixed[]  $args {
 *     Optional. The post type arguments.
 *
 *     @type array  $admin_cols         Associative array of admin screen columns to show for this post type.
 *     @type array  $admin_filters      Associative array of admin screen filters to show for this post type.
 *     @type array  $archive            Associative array of query vars to override on this post type's archive.
 *     @type bool   $block_editor       Force the use of the block editor for this post type. Must be used in
 *                                      combination with the `show_in_rest` argument. The primary use of this argument
 *                                      is to prevent the block editor from being used by setting it to false when
 *                                      `show_in_rest` is set to true.
 *     @type bool   $dashboard_glance   Whether to show this post type on the 'At a Glance' section of the admin
 *                                      dashboard. Default true.
 *     @type bool   $dashboard_activity Whether to show this post type on the 'Recently Published' section of the
 *                                      admin dashboard. Default true.
 *     @type string $enter_title_here   Placeholder text which appears in the title field for this post type.
 *     @type string $featured_image     Text which replaces the 'Featured Image' phrase for this post type.
 *     @type bool   $quick_edit         Whether to show Quick Edit links for this post type. Default true.
 *     @type bool   $show_in_feed       Whether to include this post type in the site's main feed. Default false.
 *     @type array  $site_filters       Associative array of query vars and their parameters for front end filtering.
 *     @type array  $site_sortables     Associative array of query vars and their parameters for front end sorting.
 * }
 * @param string[] $names {
 *     Optional. The plural, singular, and slug names.
 *
 *     @type string $plural   The plural form of the post type name.
 *     @type string $singular The singular form of the post type name.
 *     @type string $slug     The slug used in the permalinks for this post type.
 * }
 *
 * @return PostType
 */
abstract class PostType implements Bootable {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Post type arguments.
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * Post type names.
	 *
	 * @var array
	 */
	protected $names = [];

	/**
	 * Boots the class by running `add_action()` and `add_filter()` calls.
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
	 * Validate the post type name format.
	 *
	 * @param string $post_type The post type name.
	 * @return void
	 * @throws Exception If the taxonomy name is invalid.
	 */
	protected function validate( string $post_type ): void {
		if ( ! preg_match( '/^[a-z_]+$/', $post_type ) ) {
			throw new Exception( 'Invalid post type name: ' . esc_html( $post_type ) . '. Must be lowercase, contain no spaces or hyphens, and underscores between words.' );
		}
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->validate( $this->post_type );

		register_extended_post_type(
			$this->post_type,
			$this->args,
			$this->names
		);
	}
}
