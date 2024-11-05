<?php
/**
 * PostType contract.
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\Contracts;

use Exception;
use function register_extended_post_type;

/**
 * Abstract class for post type registration.
 *
 * @return PostType
 */
abstract class PostType implements Bootable {

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
	 * Get post type menu icon.
	 *
	 * @return string
	 */
	public function get_menu_icon(): string {
		return 'dashicons-admin-post';
	}

	/**
	 * Editor supports.
	 *
	 * @return array
	 */
	public function get_editor_supports(): array {
		return [
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'custom-fields',
			'page-attributes',
		];
	}

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
	 * @example [
	 * 'singular' => 'Post',
	 * 'plural'   => 'Posts',
	 * 'slug'     => 'post',
	 * ]
	 *
	 * @return array
	 */
	protected function get_labels(): array {
		return [];
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
	 *
	 * @return void
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
