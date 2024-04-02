<?php
/**
 * Additional REST API Fields class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

use function WP_Newsletter_Builder\get_byline;

/**
 * Adds additional fields to the REST API.
 */
class Rest_API_Fields {
	/**
	 * Set everything up.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_field' ] );
		add_action( 'rest_api_init', [ $this, 'register_search_fields' ] );
	}

	/**
	 * Register the rest field.
	 */
	public function register_field(): void {
		register_rest_field(
			'post',
			'wp_newsletter_builder_byline',
			[
				'get_callback' => [ $this, 'call_get_byline' ],
			]
		);
	}

	/**
	 * Calls the get_bylines function with the post id.
	 *
	 * @param array<string, mixed> $post The post array.
	 * @return string
	 */
	public function call_get_byline( array $post ): string {
		if ( ! is_int( $post['id'] ) && ! $post['id'] instanceof \WP_Post ) {
			return '';
		}
		return get_byline( $post['id'] );
	}

	/**
	 * Register the search rest fields.
	 */
	public function register_search_fields(): void {
		register_rest_field(
			'search-result',
			'featured_image',
			[
				'get_callback' => [ $this, 'get_featured_image' ],
			]
		);
		register_rest_field(
			'search-result',
			'post_date',
			[
				'get_callback' => [ $this, 'get_post_date' ],
			]
		);
	}

	/**
	 * Gets the featured image url
	 *
	 * @param array $post {.
	 *  @type int $id The post id.
	 *  @type string $title The post title.
	 *  @type string $url The post url.
	 *  @type string $type The post type.
	 *  @type string $subtype The post subtype.
	 * } The array of post data.
	 * @phpstan-param array{id: int, title: string, url: string, type: string, subtype: string} $post
	 * @return string|false
	 */
	public function get_featured_image( array $post ): string|false {
		return get_the_post_thumbnail_url( $post['id'], 'small-thumb' );
	}

	/**
	 * Gets the post date
	 *
	 * @param array $post {.
	 *  @type int $id The post id.
	 *  @type string $title The post title.
	 *  @type string $url The post url.
	 *  @type string $type The post type.
	 *  @type string $subtype The post subtype.
	 * } The array of post data.
	 * @phpstan-param array{id: int, title: string, url: string, type: string, subtype: string} $post
	 * @return string|int|false
	 */
	public function get_post_date( array $post ): string|int|false {
		return get_the_date( 'F j, Y', $post['id'] );
	}
}
