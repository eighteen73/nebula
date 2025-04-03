<?php
/**
 * CLI Commands Registration
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\CLI;

use Eighteen73\Nebula\Core\Contracts\Bootable;
use Eighteen73\Nebula\Core\CLI\CreatePostType;
use Eighteen73\Nebula\Core\CLI\CreateTaxonomy;
use WP_CLI;

/**
 * Class Commands
 *
 * @package Eighteen73\Nebula\Core\CLI
 */
class Commands implements Bootable {
	/**
	 * Boot the CLI commands
	 *
	 * @return void
	 */
	public function boot(): void {
		$this->register_commands();
	}

	/**
	 * Check if WP_CLI is defined and active
	 *
	 * @return bool True if WP_CLI is active, false otherwise
	 */
	public function can_boot(): bool {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Register the CLI commands
	 *
	 * @return void
	 */
	public function register_commands(): void {
		WP_CLI::add_command( 'nebula create post-type', CreatePostType::class );
		WP_CLI::add_command( 'nebula create taxonomy', CreateTaxonomy::class );
	}
}
