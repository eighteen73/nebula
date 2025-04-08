<?php
/**
 * CreateCommand contract.
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\Contracts;

use WP_Filesystem_Base;

/**
 * Abstract class for create commands.
 *
 * @return CreateCommand
 */
abstract class CreateCommand {

	/**
	 * Stores the initialized WordPress filesystem.
	 *
	 * @access protected
	 * @var WP_Filesystem_Base
	 */
	protected $filesystem;

	/**
	 * Initialize the WordPress filesystem
	 *
	 * @return void
	 */
	protected function init_filesystem(): void {
		$creds = request_filesystem_credentials( '', '', false, false, null );

		if ( ! WP_Filesystem( $creds ) ) {
			WP_CLI::error( 'Failed to initialize filesystem' );
			return;
		}

		global $wp_filesystem;

		$this->filesystem = $wp_filesystem;
	}

	/**
	 * Convert snake_case to PascalCase
	 *
	 * @param string $name The snake_case name.
	 * @return string The PascalCase name.
	 */
	protected function snake_to_pascal( string $name ): string {
		return str_replace( ' ', '', ucwords( str_replace( '_', ' ', $name ) ) );
	}

	/**
	 * Get the file path for the new component
	 *
	 * @param string $name The component name.
	 * @return string The file path.
	 */
	abstract protected function get_file_path( string $name ): string;

	/**
	 * Get the template content with variables replaced
	 *
	 * @param string $name The component name.
	 * @param string $slug The component slug.
	 * @param mixed  ...$args Additional arguments for the template.
	 * @return string The processed template.
	 */
	abstract protected function get_template( string $name, string $slug, ...$args ): string;

	/**
	 * Add the new component to the bindings array
	 *
	 * @param string $name The component name.
	 * @return void
	 */
	abstract protected function add_to_bindings( string $name ): void;

	/**
	 * Execute the scaffold command
	 *
	 * @param array $args Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 * @return void
	 */
	abstract public function __invoke( $args, $assoc_args = [] ): void;
}
