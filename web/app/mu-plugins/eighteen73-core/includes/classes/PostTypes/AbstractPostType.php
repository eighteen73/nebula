<?php
/**
 * AbstractPostType
 *
 * @package Eighteen73Core
 */

namespace Eighteen73\Core\PostTypes;

use Eighteen73\Core\Singleton;

/**
 * Abstract class for post types.
 *
 *  Usage:
 *
 *  class Testimonial extends AbstractPostType {
 *
 *      public function get_name() {
 *          return 'testimonial';
 *      }
 *
 *      public function get_singular_label() {
 *          return 'Testimonial';
 *      }
 *
 *      public function get_plural_label() {
 *          return 'Testomonials';
 *      }
 *
 *      public function get_menu_icon() {
 *          return 'embed-post';
 *      }
 *
 *      public function can_register() {
 *          return true;
 *      }
 *  }
 */
abstract class AbstractPostType extends Singleton {

	/**
	 * Get the post type name.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get the singular post type label.
	 *
	 * @return string
	 */
	abstract public function get_singular_label();

	/**
	 * Get the plural post type label.
	 *
	 * @return string
	 */
	abstract public function get_plural_label();

	/**
	 * Get the menu icon for the post type.
	 *
	 * This can be a base64 encoded SVG, a dashicons class or 'none' to leave it empty so it can be filled with CSS.
	 *
	 * @see https://developer.wordpress.org/resource/dashicons/
	 *
	 * @return string
	 */
	abstract public function get_menu_icon();

	/**
	 * Get the menu position for the post type.
	 *
	 * @return int|null
	 */
	public function get_menu_position() {
		return null;
	}

	/**
	 * Is the post type hierarchical?
	 *
	 * @return bool
	 */
	public function is_hierarchical() {
		return false;
	}

	/**
	 * Default post type supported feature names.
	 *
	 * @return array
	 */
	public function get_editor_supports() {
		$supports = [
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'revisions',
		];

		return $supports;
	}

	/**
	 * Get the options for the post type.
	 *
	 * @return array
	 */
	public function get_options() {
		return [
			'labels'            => $this->get_labels(),
			'public'            => true,
			'has_archive'       => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => false,
			'show_in_rest'      => true,
			'supports'          => $this->get_editor_supports(),
			'menu_icon'         => $this->get_menu_icon(),
			'menu_position'     => $this->get_menu_position(),
			'hierarchical'      => $this->is_hierarchical(),
		];
	}

	/**
	 * Get the labels for the post type.
	 *
	 * @return array
	 */
	public function get_labels() {
		$plural_label   = $this->get_plural_label();
		$singular_label = $this->get_singular_label();

		// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment -- ignoring template strings without translators placeholder since this is dynamic
		$labels = [
			'name'                     => $plural_label,
			// Already translated via get_plural_label().
			'singular_name'            => $singular_label,
			// Already translated via get_singular_label().
			'add_new'                  => sprintf( __( 'Add New %s', 'eighteen73-core' ), $singular_label ),
			'add_new_item'             => sprintf( __( 'Add New %s', 'eighteen73-core' ), $singular_label ),
			'edit_item'                => sprintf( __( 'Edit %s', 'eighteen73-core' ), $singular_label ),
			'new_item'                 => sprintf( __( 'New %s', 'eighteen73-core' ), $singular_label ),
			'view_item'                => sprintf( __( 'View %s', 'eighteen73-core' ), $singular_label ),
			'view_items'               => sprintf( __( 'View %s', 'eighteen73-core' ), $plural_label ),
			'search_items'             => sprintf( __( 'Search %s', 'eighteen73-core' ), $plural_label ),
			'not_found'                => sprintf( __( 'No %s found.', 'eighteen73-core' ), strtolower( $plural_label ) ),
			'not_found_in_trash'       => sprintf( __( 'No %s found in Trash.', 'eighteen73-core' ), strtolower( $plural_label ) ),
			'parent_item_colon'        => sprintf( __( 'Parent %s:', 'eighteen73-core' ), $plural_label ),
			'all_items'                => sprintf( __( 'All %s', 'eighteen73-core' ), $plural_label ),
			'archives'                 => sprintf( __( '%s Archives', 'eighteen73-core' ), $singular_label ),
			'attributes'               => sprintf( __( '%s Attributes', 'eighteen73-core' ), $singular_label ),
			'insert_into_item'         => sprintf( __( 'Insert into %s', 'eighteen73-core' ), strtolower( $singular_label ) ),
			'uploaded_to_this_item'    => sprintf( __( 'Uploaded to this %s', 'eighteen73-core' ), strtolower( $singular_label ) ),
			'filter_items_list'        => sprintf( __( 'Filter %s list', 'eighteen73-core' ), strtolower( $plural_label ) ),
			'items_list_navigation'    => sprintf( __( '%s list navigation', 'eighteen73-core' ), $plural_label ),
			'items_list'               => sprintf( __( '%s list', 'eighteen73-core' ), $plural_label ),
			'item_published'           => sprintf( __( '%s published.', 'eighteen73-core' ), $singular_label ),
			'item_published_privately' => sprintf( __( '%s published privately.', 'eighteen73-core' ), $singular_label ),
			'item_reverted_to_draft'   => sprintf( __( '%s reverted to draft.', 'eighteen73-core' ), $singular_label ),
			'item_scheduled'           => sprintf( __( '%s scheduled.', 'eighteen73-core' ), $singular_label ),
			'item_updated'             => sprintf( __( '%s updated.', 'eighteen73-core' ), $singular_label ),
			'menu_name'                => $plural_label,
			'name_admin_bar'           => $singular_label,
		];
		// phpcs:enable WordPress.WP.I18n.MissingTranslatorsComment

		return $labels;
	}

	/**
	 * Registers a post type and associates its taxonomies.
	 *
	 * @uses $this->get_name() to get the post's type name.
	 * @return Bool Whether this theme has supports for this post type.
	 */
	public function register() {
		$this->register_post_type();
		$this->register_taxonomies();

		$this->after_register();

		return true;
	}

	/**
	 * Registers the current post type with WordPress.
	 *
	 * @return void
	 */
	public function register_post_type() {
		register_post_type(
			$this->get_name(),
			$this->get_options()
		);
	}

	/**
	 * Registers the taxonomies declared with the current post type.
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		$taxonomies = $this->get_supported_taxonomies();

		$object_type = $this->get_name();

		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				register_taxonomy_for_object_type(
					$taxonomy,
					$object_type
				);
			}
		}
	}

	/**
	 * Returns the default supported taxonomies. The subclass should declare the
	 * Taxonomies that it supports here if required.
	 *
	 * @return array
	 */
	public function get_supported_taxonomies() {
		return [];
	}

	/**
	 * Run any code after the post type has been registered.
	 *
	 * @return void
	 */
	public function after_register() {
		// Do nothing.
	}
}
