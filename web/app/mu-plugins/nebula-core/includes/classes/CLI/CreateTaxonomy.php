<?php
/**
 * Create Taxonomy CLI Command
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\CLI;

use WP_CLI;
use Eighteen73\Nebula\Core\Contracts\CreateCommand;

/**
 * Class CreateTaxonomy
 *
 * @package Eighteen73\Nebula\Core\CLI
 */
class CreateTaxonomy extends CreateCommand {
	/**
	 * Creates a new taxonomy class
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : The name of the taxonomy (in snake_case, e.g. team_member)
	 *
	 * <post-type>
	 * : The post type to associate the taxonomy with (can be a single post type or comma-separated list)
	 *
	 * ## EXAMPLES
	 *
	 *     # Create a new team member taxonomy
	 *     $ wp nebula create taxonomy team_member_type team_member
	 *     Success: Created taxonomy class TeamMemberType
	 *
	 *     # Create a taxonomy for multiple post types
	 *     $ wp nebula create taxonomy category post,page,portfolio
	 *     Success: Created taxonomy class Category
	 *
	 * @param array $args Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 * @return void
	 */
	public function __invoke( $args, $assoc_args = [] ): void {
		$input_name = $args[0];
		$post_types = $args[1];

		// Initialize filesystem
		$this->init_filesystem();

		// Validate input format
		if ( ! preg_match( '/^[a-z][a-z0-9_]*$/', $input_name ) ) {
			WP_CLI::error( 'Taxonomy name must be in snake_case format (lowercase with underscores between words). Example: team_member_type' );
			return;
		}

		// Convert to PascalCase for class name
		$class_name = $this->snake_to_pascal( $input_name );
		$slug       = $input_name;

		// Process post types
		$post_types_array = array_map( 'trim', explode( ',', $post_types ) );

		// If there's only one post type, return it as a string, otherwise as an array
		$post_types_value = count( $post_types_array ) === 1
			? "'" . $post_types_array[0] . "'"
			: '[' . implode(
				', ',
				array_map(
					function ( $type ) {
						return "'" . $type . "'";
					},
					$post_types_array
				)
			) . ']';

		$template  = $this->get_template( $class_name, $slug, $post_types_value );
		$file_path = $this->get_file_path( $class_name );

		if ( file_exists( $file_path ) ) {
			WP_CLI::error( "Taxonomy file already exists at: $file_path" );
			return;
		}

		if ( ! is_dir( dirname( $file_path ) ) ) {
			$this->filesystem->mkdir( dirname( $file_path ), 0755, true );
		}

		if ( $this->filesystem->put_contents( $file_path, $template ) ) {
			$this->add_to_bindings( $class_name );
			WP_CLI::success( "Created taxonomy class $class_name at: $file_path" );
		} else {
			WP_CLI::error( 'Failed to create taxonomy file' );
		}
	}

	/**
	 * Get the file path for the new taxonomy
	 *
	 * @param string $name The taxonomy name.
	 * @return string The file path.
	 */
	protected function get_file_path( string $name ): string {
		return NEBULA_CORE_PATH . 'includes/classes/Taxonomies/' . $name . '.php';
	}

	/**
	 * Get the template content with variables replaced
	 *
	 * @param string $name The taxonomy name.
	 * @param string $slug The taxonomy slug.
	 * @param mixed  ...$args Additional arguments for the template.
	 * @return string The processed template.
	 */
	protected function get_template( string $name, string $slug, ...$args ): string {
		$template_path = NEBULA_CORE_PATH . 'includes/classes/CLI/templates/taxonomy.php.template';
		$template      = $this->filesystem->get_contents( $template_path );

		return str_replace(
			[ '%name%', '%slug%', '%post_types%' ],
			[ $name, $slug, $args[0] ?? '' ],
			$template
		);
	}

	/**
	 * Add the new taxonomy to the bindings array
	 *
	 * @param string $name The taxonomy name.
	 * @return void
	 */
	protected function add_to_bindings( string $name ): void {
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
