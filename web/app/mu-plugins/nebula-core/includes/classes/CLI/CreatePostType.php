<?php
/**
 * Scaffold Post Type CLI Command
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\CLI;

use WP_CLI;

/**
 * Class CreatePostType
 *
 * @package Eighteen73\Nebula\Core\CLI
 */
class CreatePostType {
	/**
	 * Creates a new post type class
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : The name of the post type (in snake_case, e.g. team_member)
	 *
	 * ## EXAMPLES
	 *
	 *     # Create a new team member post type
	 *     $ wp nebula scaffold post-type team_member
	 *     Success: Created post type class TeamMember
	 *
	 * @param array $args Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 */
	public function __invoke( $args, $assoc_args ) {
		$input_name = $args[0];

		// Validate input format
		if ( ! preg_match( '/^[a-z][a-z0-9_]*$/', $input_name ) ) {
			WP_CLI::error( 'Post type name must be in snake_case format (lowercase with underscores between words). Example: team_member' );
			return;
		}

		// Convert to PascalCase for class name
		$class_name = $this->snake_to_pascal( $input_name );
		$slug = $input_name;

		$template = $this->get_template( $class_name, $slug );
		$file_path = $this->get_file_path( $class_name );

		if ( file_exists( $file_path ) ) {
			WP_CLI::error( "Post type file already exists at: $file_path" );
			return;
		}

		if ( ! is_dir( dirname( $file_path ) ) ) {
			mkdir( dirname( $file_path ), 0755, true );
		}

		if ( file_put_contents( $file_path, $template ) ) {
			$this->add_to_bindings( $class_name );
			WP_CLI::success( "Created post type class $class_name at: $file_path" );
		} else {
			WP_CLI::error( 'Failed to create post type file' );
		}
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
		$template = file_get_contents( $template_path );

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
		$bindings_path = NEBULA_CORE_PATH . 'config/bindings.php';
		$bindings_content = file_get_contents( $bindings_path );

		// Parse the PHP file to get the array content
		preg_match( '/return\s*\[(.*?)\];/s', $bindings_content, $matches );
		if ( empty( $matches[1] ) ) {
			WP_CLI::error( 'Could not parse bindings file' );
			return;
		}

		// Get the array content and the indentation level
		$array_content = $matches[1];
		preg_match( '/^(\s*)/', $array_content, $indent_matches );
		$indent = $indent_matches[1] ?? "\t";

		// Split the array content into lines and clean them
		$lines = array_map( 'trim', explode( "\n", $array_content ) );
		$lines = array_filter( $lines ); // Remove empty lines

		// Add the new binding with proper indentation
		$new_binding = $indent . "Eighteen73\\Nebula\\Core\\PostTypes\\{$name}::class,";
		$lines[] = $new_binding;

		// Sort the lines alphabetically
		sort( $lines );

		// Rebuild the array content
		$array_content = implode( "\n", $lines );
		$new_content = preg_replace(
			'/return\s*\[(.*?)\];/s',
			"return [\n{$array_content}\n];",
			$bindings_content
		);

		file_put_contents( $bindings_path, $new_content );
	}
}
