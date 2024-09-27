<?php
/**
 * Taxonomy
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\Contracts;

use Exception;
use function register_extended_taxonomy;

/**
 * Registers a custom taxonomy.
 *
 * The `$args` parameter accepts all the standard arguments for `register_taxonomy()` in addition to several custom
 * arguments that provide extended functionality. Some of the default arguments differ from the defaults in
 * `register_taxonomy()`.
 *
 * @link https://github.com/johnbillion/extended-cpts/wiki/Registering-taxonomies
 * @see register_taxonomy() for default arguments.
 *
 * @param string          $taxonomy    The taxonomy name.
 * @param string|string[] $object_type Name(s) of the object type(s) for the taxonomy.
 * @param mixed[]         $args {
 *     Optional. The taxonomy arguments.
 *
 *     @type string $meta_box         The name of the custom meta box to use on the post editing screen for this
 *                                    taxonomy. Three custom meta boxes are provided: 'radio' for a meta box with radio
 *                                    inputs, 'simple' for a meta box with a simplified list of checkboxes, and
 *                                    'dropdown' for a meta box with a dropdown menu. You can also pass the name of a
 *                                    callback function, eg my_super_meta_box(), or boolean false to remove the meta
 *                                    box. Default null, meaning the standard meta box is used.
 *     @type bool   $checked_ontop    Whether to always show checked terms at the top of the meta box. This allows you
 *                                    to override WordPress' default behaviour if necessary. Default false if you're
 *                                    using a custom meta box (see the $meta_box argument), default true otherwise.
 *     @type bool   $dashboard_glance Whether to show this taxonomy on the 'At a Glance' section of the admin dashboard.
 *                                    Default false.
 *     @type array  $admin_cols       Associative array of admin screen columns to show for this taxonomy. See the
 *                                    `TaxonomyAdmin::cols()` method for more information.
 *     @type bool   $exclusive        This parameter isn't feature complete. All it does currently is set the meta box
 *                                    to the 'radio' meta box, thus meaning any given post can only have one term
 *                                    associated with it for that taxonomy. 'exclusive' isn't really the right name for
 *                                    this, as terms aren't exclusive to a post, but rather each post can exclusively
 *                                    have only one term. It's not feature complete because you can edit a post in
 *                                    Quick Edit and give it more than one term from the taxonomy.
 *     @type bool   $allow_hierarchy  All this does currently is disable hierarchy in the taxonomy's rewrite rules.
 *                                    Default false.
 * }
 * @param string[]        $names {
 *     Optional. The plural, singular, and slug names.
 *
 *     @type string $plural   The plural form of the taxonomy name.
 *     @type string $singular The singular form of the taxonomy name.
 *     @type string $slug     The slug used in the term permalinks for this taxonomy.
 * }
 * @return Taxonomy
 */
abstract class Taxonomy implements Bootable {
	/**
	 * Taxonomy name.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * object_type Name(s) of the object type(s) for the taxonomy.
	 *
	 * @var string|string[]
	 */
	protected $object_type;

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
	 * Validate the taxonomy name format.
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return void
	 * @throws Exception If the taxonomy name is invalid.
	 */
	protected function validate( string $taxonomy ): void {
		if ( ! preg_match( '/^[a-z_]+$/', $taxonomy ) ) {
			throw new Exception( 'Invalid taxonomy name: ' . esc_html( $taxonomy ) . '. Must be lowercase, contain no spaces or hyphens, and underscores between words.' );
		}
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->validate( $this->taxonomy );

		register_extended_taxonomy(
			$this->taxonomy,
			$this->object_type,
			$this->args,
			$this->names
		);
	}
}
