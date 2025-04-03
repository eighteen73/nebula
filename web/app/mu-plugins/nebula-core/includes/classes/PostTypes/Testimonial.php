<?php
/**
 * PostType contract.
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\PostTypes;

use Eighteen73\Nebula\Core\Contracts\PostType;

/**
 * Class for testimonial post type registration.
 *
 * @return PostType
 */
class Testimonial extends PostType {
	/**
	 * Get post type name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'testimonial';
	}

	/**
	 * Get post type menu icon.
	 *
	 * @return string
	 */
	public function get_menu_icon(): string {
		return 'dashicons-admin-post';
	}

	/**
	 * Post type labels configuration.
	 *
	 * @return array
	 */
	protected function get_labels(): array {
		return [
			'singular' => 'Testimonial',
			'plural'   => 'Testimonials',
			'slug'     => 'testimonial',
		];
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
			'thumbnail',
		];
	}
}
