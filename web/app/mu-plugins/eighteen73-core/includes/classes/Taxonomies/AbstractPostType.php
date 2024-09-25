<?php
/**
 * AbstractTaxonomy
 *
 * @package Eighteen73Core
 */

namespace Eighteen73\Core\Taxonomies;

use Eighteen73\Core\Singleton;

/**
 * Abstract Base Class for Taxonomies.
 *
 * Usage:
 *
 * class TestimonialType extends AbstractTaxonomy {
 *
 *     public function get_name() {
 *         return 'testimonial_type';
 *     }
 *
 *     public function get_singular_label() {
 *         return 'Testimonial Type';
 *     }
 *
 *     public function get_plural_label() {
 *         return 'Testimonial Types';
 *     }
 *
 *     public function can_register() {
 *         return true;
 *     }
 * }
 */
abstract class AbstractTaxonomy extends Singleton {

	/**
	 * Get the taxonomy name.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get the singular taxonomy label.
	 *
	 * @return string
	 */
	abstract public function get_singular_label();

	/**
	 * Get the plural taxonomy label.
	 *
	 * @return string
	 */
	abstract public function get_plural_label();

	/**
	 * Is the taxonomy hierarchical?
	 *
	 * @return bool
	 */
	public function is_hierarchical() {
		return false;
	}

	/**
	 * Register hooks and actions.
	 *
	 * @uses $this->get_name() to get the taxonomy's slug.
	 * @return bool
	 */
	public function register() {
		\register_taxonomy(
			$this->get_name(),
			$this->get_post_types(),
			$this->get_options()
		);

		$this->after_register();

		return true;
	}

	/**
	 * Get the options for the taxonomy.
	 *
	 * @return array
	 */
	public function get_options() {
		return [
			'labels'            => $this->get_labels(),
			'hierarchical'      => $this->is_hierarchical(),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'public'            => true,
		];
	}

	/**
	 * Get the labels for the taxonomy.
	 *
	 * @return array
	 */
	public function get_labels() {
		$plural_label   = $this->get_plural_label();
		$singular_label = $this->get_singular_label();

		// phpcs:disable
		$labels = [
			'name'                       => $plural_label, // Already translated via get_plural_label().
			'singular_name'              => $singular_label, // Already translated via get_singular_label().
			'search_items'               => sprintf( __( 'Search %s', 'eighteen73-core' ), $plural_label ),
			'popular_items'              => sprintf( __( 'Popular %s', 'eighteen73-core' ), $plural_label ),
			'all_items'                  => sprintf( __( 'All %s', 'eighteen73-core' ), $plural_label ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'eighteen73-core' ), $singular_label ),
			'update_item'                => sprintf( __( 'Update %s', 'eighteen73-core' ), $singular_label ),
			'add_new_item'               => sprintf( __( 'Add %s', 'eighteen73-core' ), $singular_label ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'eighteen73-core' ), $singular_label ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'eighteen73-core' ), strtolower( $plural_label ) ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'eighteen73-core' ), strtolower( $plural_label ) ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'eighteen73-core' ), strtolower( $plural_label ) ),
			'not_found'                  => sprintf( __( 'No %s found.', 'eighteen73-core' ), strtolower( $plural_label ) ),
			'not_found_in_trash'         => sprintf( __( 'No %s found in Trash.', 'eighteen73-core' ), strtolower( $plural_label ) ),
			'view_item'                  => sprintf( __( 'View %s', 'eighteen73-core' ), $singular_label ),
		];
		// phpcs:enable

		return $labels;
	}

	/**
	 * Setting the post types to null to ensure no post type is registered with
	 * this taxonomy. Post Type classes declare their supported taxonomies.
	 *
	 * @return array|null
	 */
	public function get_post_types() {
		return null;
	}

	/**
	 * Run any code after the taxonomy has been registered.
	 *
	 * @return void
	 */
	public function after_register() {
		// Do nothing.
	}
}
