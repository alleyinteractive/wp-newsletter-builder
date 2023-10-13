<?php
/**
 * Media class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Media class
 */
class Media {
	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'disable_lazyload_on_newsletters' ], 8, 1 );
	}

	/**
	 * Disables Jetpack on newsletters.
	 */
	public function disable_lazyload_on_newsletters() {
		if ( is_singular( 'nb_newsletter' ) ) {
			add_filter( 'lazyload_is_enabled', '__return_false' );
		}
	}
}
