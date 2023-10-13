<?php
/**
 * Trait file for Singletons.
 *
 * @package WP_Newsletter_Builder
 */

namespace WP_Newsletter_Builder;

/**
 * Make a class into a singleton.
 */
trait Singleton {
	/**
	 * Existing instances.
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * Get class instance.
	 *
	 * @return static
	 */
	public static function instance() {
		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ] ) ) {
			static::$instances[ $class ] = new static();
		}

		return self::$instances[ $class ];
	}
}
