<?php
/**
 * Scaffold Post Type CLI Command
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\CLI;

use WP_CLI;
use WP_Filesystem_Base;

/**
 * Class CreatePostType
 *
 * @package Eighteen73\Nebula\Core\CLI
 */
class CreatePostType {
	/**
	 * Stores the initialized WordPress filesystem.
	 *
	 * @access private
	 * @var WP_Filesystem_Base
	 */
	private $filesystem;

	/**
	 * Creates a new post type class
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : The name of the post type (in snake_case, e.g. team_member)
	 *
	 * [--taxonomy=<taxonomy>]
	 * : Create a taxonomy for this post type (optional, e.g. portfolio_category)
	 *
	 * ## EXAMPLES
	 *
	 *     # Create a new team member post type
	 *     $ wp nebula create post-type team_member
	 *     Success: Created post type class TeamMember
	 *
	 *     # Create a post type with an associated taxonomy
	 *     $ wp nebula create post-type portfolio --taxonomy=portfolio_category
	 *     Success: Created post type class Portfolio
	 *     Success: Created taxonomy class PortfolioCategory
	 *
	 * @param array $args Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 */
	public function __invoke( $args, $assoc_args ) {
		$input_name = $args[0];

		// Initialize filesystem
		$this->init_filesystem();

		// Validate input format
		if ( ! preg_match( '/^[a-z][a-z0-9_]*$/', $input_name ) ) {
			WP_CLI::error( 'Post type name must be in snake_case format (lowercase with underscores between words). Example: team_member' );
			return;
		}

		// Convert to PascalCase for class name
		$class_name = $this->snake_to_pascal( $input_name );
		$slug       = $input_name;

		$template  = $this->get_template( $class_name, $slug );
		$file_path = $this->get_file_path( $class_name );

		if ( file_exists( $file_path ) ) {
			WP_CLI::error( "Post type file already exists at: $file_path" );
			return;
		}

		if ( ! is_dir( dirname( $file_path ) ) ) {
			$this->filesystem->mkdir( dirname( $file_path ), 0755, true );
		}

		if ( $this->filesystem->put_contents( $file_path, $template ) ) {
			$this->add_to_bindings( $class_name );
			WP_CLI::success( "Created post type class $class_name at: $file_path" );

			// Create taxonomy if requested
			if ( isset( $assoc_args['taxonomy'] ) && ! empty( $assoc_args['taxonomy'] ) ) {
				$this->create_taxonomy( $assoc_args['taxonomy'], $input_name );
			}
		} else {
			WP_CLI::error( 'Failed to create post type file' );
		}
	}

	/**
	 * Initialize the WordPress filesystem
	 *
	 * @return void
	 */
	private function init_filesystem() {
		global $wp_filesystem;

		// If the filesystem is already initialized, use it
		if ( $wp_filesystem instanceof WP_Filesystem_Base ) {
			$this->filesystem = $wp_filesystem;
			return;
		}

		// Initialize the filesystem
		require_once ABSPATH . 'wp-admin/includes/file.php';

		// For CLI commands, we can use direct filesystem
		WP_Filesystem();

		$this->filesystem = $wp_filesystem;
	}

	/**
	 * Convert snake_case to PascalCase
	 *
	 * @param string $name The snake_case name.
	 * @return string The PascalCase name.
	 */
	private function snake_to_pascal( $name ) {
		return str_replace( ' ', '', ucwords( str_replace( '_', ' ', $name ) ) );
	}

	/**
	 * Get the file path for the new post type
	 *
	 * @param string $name The post type name.
	 * @return string The file path.
	 */
	private function get_file_path( $name ) {
		return NEBULA_CORE_PATH . 'includes/classes/PostTypes/' . $name . '.php';
	}

	/**
	 * Get the template content with variables replaced
	 *
	 * @param string $name The post type name.
	 * @param string $slug The post type slug.
	 * @return string The processed template.
	 */
	private function get_template( $name, $slug ) {
		$template_path = NEBULA_CORE_PATH . 'includes/classes/CLI/templates/post-type.php.template';
		$template      = $this->filesystem->get_contents( $template_path );

		return str_replace(
			[ '%name%', '%slug%' ],
			[ $name, $slug ],
			$template
		);
	}

	/**
	 * Add the new post type to the bindings array
	 *
	 * @param string $name The post type name.
	 * @return void
	 */
	private function add_to_bindings( $name ) {
		$bindings_path    = NEBULA_CORE_PATH . 'config/bindings.php';
		$bindings_content = $this->filesystem->get_contents( $bindings_path );

		// Parse the PHP file to get the array content
		preg_match( '/return\s*\[(.*?)\];/s', $bindings_content, $matches );
		if ( empty( $matches[1] ) ) {
			WP_CLI::error( 'Could not parse bindings file' );
			return;
		}

		// Get the array content
		$array_content = $matches[1];

		// Split the array content into lines and clean them
		$lines = array_map( 'trim', explode( "\n", $array_content ) );
		$lines = array_filter( $lines ); // Remove empty lines

		// Add the new binding with proper indentation
		$new_binding = "\tEighteen73\\Nebula\\Core\\PostTypes\\{$name}::class,";
		$lines[]     = $new_binding;

		// Sort the lines alphabetically
		sort( $lines );

		// Rebuild the array content with proper indentation
		$array_content = implode( "\n", $lines );
		$new_content   = preg_replace(
			'/return\s*\[(.*?)\];/s',
			"return [\n{$array_content}\n];",
			$bindings_content
		);

		$this->filesystem->put_contents( $bindings_path, $new_content );
	}

	/**
	 * Create a taxonomy for the post type
	 *
	 * @param string $taxonomy_name The taxonomy name.
	 * @param string $post_type The post type name.
	 * @return void
	 */
	private function create_taxonomy( $taxonomy_name, $post_type ) {
		// Validate taxonomy name format
		if ( ! preg_match( '/^[a-z][a-z0-9_]*$/', $taxonomy_name ) ) {
			WP_CLI::error( 'Taxonomy name must be in snake_case format (lowercase with underscores between words). Example: portfolio_category' );
			return;
		}

		// Convert to PascalCase for class name
		$class_name = $this->snake_to_pascal( $taxonomy_name );
		$slug       = $taxonomy_name;

		// Create the taxonomy file
		$template_path = NEBULA_CORE_PATH . 'includes/classes/CLI/templates/taxonomy.php.template';
		$template      = $this->filesystem->get_contents( $template_path );

		// Replace placeholders in the template
		$template = str_replace(
			[ '%name%', '%slug%', '%post_types%' ],
			[ $class_name, $slug, "'" . $post_type . "'" ],
			$template
		);

		$file_path = NEBULA_CORE_PATH . 'includes/classes/Taxonomies/' . $class_name . '.php';

		if ( file_exists( $file_path ) ) {
			WP_CLI::error( "Taxonomy file already exists at: $file_path" );
			return;
		}

		if ( ! is_dir( dirname( $file_path ) ) ) {
			$this->filesystem->mkdir( dirname( $file_path ), 0755, true );
		}

		if ( $this->filesystem->put_contents( $file_path, $template ) ) {
			$this->add_taxonomy_to_bindings( $class_name );
			WP_CLI::success( "Created taxonomy class $class_name" );
		} else {
			WP_CLI::error( 'Failed to create taxonomy file' );
		}
	}

	/**
	 * Add the new taxonomy to the bindings array
	 *
	 * @param string $name The taxonomy name.
	 * @return void
	 */
	private function add_taxonomy_to_bindings( $name ) {
		$bindings_path    = NEBULA_CORE_PATH . 'config/bindings.php';
		$bindings_content = $this->filesystem->get_contents( $bindings_path );

		// Parse the PHP file to get the array content
		preg_match( '/return\s*\[(.*?)\];/s', $bindings_content, $matches );
		if ( empty( $matches[1] ) ) {
			WP_CLI::error( 'Could not parse bindings file' );
			return;
		}

		// Get the array content
		$array_content = $matches[1];

		// Split the array content into lines and clean them
		$lines = array_map( 'trim', explode( "\n", $array_content ) );
		$lines = array_filter( $lines ); // Remove empty lines

		// Add the new binding with proper indentation
		$new_binding = "\tEighteen73\\Nebula\\Core\\Taxonomies\\{$name}::class,";
		$lines[]     = $new_binding;

		// Sort the lines alphabetically
		sort( $lines );

		// Rebuild the array content with proper indentation
		$array_content = implode( "\n", $lines );
		$new_content   = preg_replace(
			'/return\s*\[(.*?)\];/s',
			"return [\n{$array_content}\n];",
			$bindings_content
		);

		$this->filesystem->put_contents( $bindings_path, $new_content );
	}
}
