<?php
/**
 * Trait file for Api_Response.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder\Email_Providers;

/**
 * Helpers for safely reading decoded JSON API responses.
 */
trait Api_Response {
	/**
	 * Gets a nested property from a decoded JSON object.
	 *
	 * Walks the given property path, returning null if any step is not an
	 * object or does not have the property. Integer path segments index into
	 * arrays.
	 *
	 * @param mixed      $data    The decoded JSON data.
	 * @param string|int ...$path The property names (or array indexes) to walk.
	 * @return mixed The value at the end of the path, or null if it does not exist.
	 */
	protected function get_response_value( mixed $data, string|int ...$path ): mixed {
		foreach ( $path as $key ) {
			if ( is_int( $key ) && is_array( $data ) ) {
				$data = $data[ $key ] ?? null;
			} elseif ( is_string( $key ) && is_object( $data ) && isset( $data->$key ) ) {
				$data = $data->$key;
			} else {
				return null;
			}
		}

		return $data;
	}
}
