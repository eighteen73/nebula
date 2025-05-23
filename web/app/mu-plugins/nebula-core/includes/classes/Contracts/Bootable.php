<?php
/**
 * Bootable interface.
 *
 * Defines the contract that bootable classes should utilize. Bootable classes
 * should have a `boot()` method with the singular purpose of "booting" the
 * action and filter hooks for that class. This keeps action/filter calls out of
 * the class constructor. Most bootable classes are meant to be single-instance
 * classes that get loaded once per page request.
 *
 * @package NebulaCore
 */

namespace Eighteen73\Nebula\Core\Contracts;

/**
 * Bootable interface.
 */
interface Bootable {

	/**
	 * Boots the class by running `add_action()` and `add_filter()` calls.
	 *
	 * @return void
	 */
	public function boot(): void;

	/**
	 * Determines if the class can be booted.
	 *
	 * @return bool
	 */
	public function can_boot(): bool;
}
