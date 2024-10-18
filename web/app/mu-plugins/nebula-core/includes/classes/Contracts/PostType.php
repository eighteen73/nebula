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
	protected string $post_type;

	/**
	 * Boot method from Bootable interface
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'init', [ $this, 'register_meta' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	/**
	 * Post types can be booted by default.
	 *
	 * @return bool
	 */
	public function can_boot(): bool {
		return true;
	}

	/**
	 * Get post type name.
	 *
	 * @return string
	 */
	abstract public function get_name(): string;

	/**
	 * Get post type singular label.
	 *
	 * @return string
	 */
	abstract public function get_singular_label(): string;

	/**
	 * Get post type plural label.
	 *
	 * @return string
	 */
	abstract public function get_plural_label(): string;

	/**
	 * Get post type menu icon.
	 *
	 * @return string
	 */
	abstract public function get_menu_icon(): string;

	/**
	 * Editor supports.
	 *
	 * @return array
	 */
	abstract public function get_editor_supports(): array;

	/**
	 * Post type options configuration.
	 *
	 * @return array
	 */
	protected function get_options(): array {
		return [
			'menu_icon'    => $this->get_menu_icon(),
			'supports'     => $this->get_editor_supports(),
			'show_in_rest' => true,
			'public'       => true,
		];
	}

	/**
	 * Post type labels configuration.
	 *
	 * @return array
	 */
	protected function get_labels(): array {
		return [
			'singular' => $this->get_singular_label(),
			'plural'   => $this->get_plural_label(),
			'slug'     => $this->get_name(),
		];
	}

	/**
	 * Get custom post meta.
	 *
	 * Child classes should return an associative array where the key is the meta key and the value is an array of arguments for that meta key.
	 *
	 * @return array
	 */
	public function get_meta(): array {
		return [];
	}

	/**
	 * Child class can override this to provide REST route definitions.
	 *
	 * @return array
	 */
	public function get_rest_routes(): array {
		return [];
	}

	/**
	 * Register post type using Extended CPTs.
	 */
	public function register_post_type(): void {
		$this->validate( $this->get_name() );

		register_extended_post_type(
			$this->get_name(),
			$this->get_options(),
			$this->get_labels()
		);
	}

	/**
	 * Automatically register meta fields from child class.
	 */
	public function register_meta(): void {
		foreach ( $this->get_meta() as $meta_key => $meta_args ) {
			$final_key = $this->should_prefix_meta_keys() ? "{$this->get_name()}_$meta_key" : $meta_key;
			register_post_meta( $this->get_name(), $final_key, $meta_args );
		}
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_rest_routes(): void {
		foreach ( $this->get_rest_routes() as $route ) {
			register_rest_route( $route['namespace'], $route['route'], $route['args'] );
		}
	}

	/**
	 * Validate the post type name format.
	 *
	 * @param string $post_type The post type name.
	 * @return void
	 * @throws Exception If the post type name is invalid.
	 */
	protected function validate( string $post_type ): void {
		if ( ! preg_match( '/^[a-z_]+$/', $post_type ) ) {
			throw new Exception( 'Invalid post type name: ' . esc_html( $this->get_name() ) . '. Post type name should be lowercase, contain no spaces or hyphens, and only include underscores between words.' );
		}
	}

	/**
	 * Should meta keys be prefixed with post type name.
	 * This is useful to avoid conflicts with other post types  and is good practice.
	 * Child classes can override this method to disable prefixing.
	 *
	 * @return bool
	 */
	protected function should_prefix_meta_keys(): bool {
		return true;
	}
}
