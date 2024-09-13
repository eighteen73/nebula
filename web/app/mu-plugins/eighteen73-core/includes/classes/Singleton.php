<?php
/**
 * Singleton trait
 *
 * @package Eighteen73Core
 */

namespace Eighteen73\Eighteen73Core;

trait Singleton {

	/**
	 * The class instance
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Get the current instance
	 *
	 * @return Singleton
	 */
	final public static function instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		// Intentionally empty
	}

	/**
	 * Class clone
	 *
	 * @return void
	 */
	private function __clone() {
		// Intentionally empty
	}
}
