<?php
/**
 * Taxonomy contract.
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\Contracts;

use Exception;
use function register_extended_taxonomy;

/**
 * Abstract class for taxonomy registration.
 *
 * @return Taxonomy
 */
abstract class Taxonomy implements Bootable {

	/**
	 * Boot method from Bootable interface
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
		add_action( 'init', [ $this, 'register_meta' ] );
	}

	/**
	 * Taxonomies can be booted by default.
	 *
	 * @return bool
	 */
	public function can_boot(): bool {
		return true;
	}

	/**
	 * Get taxonomy name.
	 *
	 * @return string
	 */
	abstract public function get_name(): string;

	/**
	 * Get post types this taxonomy should be registered for.
	 *
	 * @return string|array
	 */
	abstract public function get_post_types(): string|array;

	/**
	 * Taxonomy options configuration.
	 *
	 * @return array
	 */
	protected function get_options(): array {
		return [
			'public'            => true,
			'show_ui'           => true,
			'hierarchical'      => true,
			'query_var'         => true,
			'allow_hierarchy'   => false,
			'dashboard_glance'  => false,
			'meta_box'          => 'simple',
			'show_in_rest'      => true,
		];
	}

	/**
	 * Taxonomy labels configuration.
	 *
	 * @example [
	 * 'singular' => 'Genre',
	 * 'plural'   => 'Genres',
	 * 'slug'     => 'story-genre',
	 * ]
	 *
	 * @return array
	 */
	protected function get_labels(): array {
		return [];
	}

	/**
	 * Get custom term meta.
	 *
	 * Child classes should return an associative array where the key is the meta key and the value is an array of arguments for that meta key.
	 *
	 * @return array
	 */
	public function get_meta(): array {
		return [];
	}

	/**
	 * Register post type using Extended CPTs.
	 *
	 * @return void
	 */
	public function register_taxonomy(): void {
		$this->validate( $this->get_name() );

		register_extended_taxonomy(
			$this->get_name(),
			$this->get_post_types(),
			$this->get_options(),
			$this->get_labels(),
		);

		$this->after_register();
	}

	/**
	 * Run any code after the taxonomy has been registered.
	 *
	 * @return void
	 */
	public function after_register() {}

	/**
	 * Automatically register meta fields from child class.
	 */
	public function register_meta(): void {
		foreach ( $this->get_meta() as $meta_key => $meta_args ) {
			$final_key = $this->should_prefix_meta_keys() ? "{$this->get_name()}_$meta_key" : $meta_key;
			register_term_meta( $this->get_name(), $final_key, $meta_args );
		}
	}

	/**
	 * Validate the taxonomy name format.
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return void
	 * @throws Exception If the taxonomy name is invalid.
	 */
	protected function validate( string $taxonomy ): void {
		if ( ! preg_match( '/^[a-z_]+$/', $taxonomy ) ) {
			throw new Exception( 'Invalid taxonomy name: ' . esc_html( $this->get_name() ) . '. Taxonomy name should be lowercase, contain no spaces or hyphens, and only include underscores between words.' );
		}
	}

	/**
	 * Should meta keys be prefixed with the taxonomy name.
	 * This is useful to avoid conflicts with other meta and is good practice.
	 * Child classes can override this method to disable prefixing.
	 *
	 * @return bool
	 */
	protected function should_prefix_meta_keys(): bool {
		return true;
	}
}
