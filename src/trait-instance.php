<?php
/**
 * Trait file for Instance.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Make a class into a singleton.
 */
trait Instance {
	/**
	 * Existing instances.
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * Get class instance.
	 *
	 * @return static The instance of the called class.
	 */
	public static function instance(): static {
		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ] ) ) {
			static::$instances[ $class ] = new static();
		}

		return static::$instances[ $class ];
	}
}
