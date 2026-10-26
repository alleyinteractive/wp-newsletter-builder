<?php
/**
 * Additional REST API Query class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Modifies the query for the REST API.
 */
class Rest_API_Query {
	/**
	 * Set everything up.
	 */
	public function __construct() {
		add_filter( 'rest_post_search_query', [ $this, 'add_after_param' ], 10, 2 );
		add_filter( 'rest_post_search_query', [ $this, 'check_for_url_match' ], 10, 2 );
		add_filter( 'rest_post_search_query', [ $this, 'check_for_post_id_match' ], 10, 2 );
	}

	/**
	 * Add date query to reset search query if the after param is set.
	 *
	 * @param mixed $query_args The existing query args.
	 * @param mixed $request    The REST request.
	 * @return array<mixed>
	 */
	public function add_after_param( $query_args, $request ): array {
		if ( ! is_array( $query_args ) || ! $request instanceof \WP_REST_Request ) {
			return (array) $query_args;
		}
		if ( ! empty( $request->get_param( 'after' ) ) ) {
			$query_args['date_query'] = [
				'after' => $request->get_param( 'after' ),
			];
		}
		return $query_args;
	}

	/**
	 * Checks if the search string is a url and if it is, returns the appropriate post.
	 *
	 * @param mixed $query_args The existing query args.
	 * @param mixed $request    The REST request.
	 * @return array<mixed>
	 */
	public function check_for_url_match( $query_args, $request ): array {
		if ( ! is_array( $query_args ) || ! $request instanceof \WP_REST_Request ) {
			return (array) $query_args;
		}
		$search_param = $request->get_param( 'search' );
		if ( ! is_string( $search_param ) ) {
			return $query_args;
		}
		$search_url = wp_http_validate_url( $search_param );
		if ( ! $search_url ) {
			return $query_args;
		}
		if ( strpos( $search_url, home_url() ) === 0 ) {
			if ( function_exists( 'wpcom_vip_url_to_postid' ) ) {
				$post_id = wpcom_vip_url_to_postid( $search_url );
			} else {
				$post_id = url_to_postid( $search_url ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.url_to_postid_url_to_postid
			}
			$query_args['post__in'] = [ $post_id ];
			$query_args['s']        = '';
		}
		return $query_args;
	}

	/**
	 * Checks if the search string is a Post ID and if it is, returns the appropriate post.
	 *
	 * @param mixed $query_args The existing query args.
	 * @param mixed $request    The REST request.
	 * @return array<mixed>
	 */
	public function check_for_post_id_match( $query_args, $request ): array {
		if ( ! is_array( $query_args ) || ! $request instanceof \WP_REST_Request ) {
			return (array) $query_args;
		}
		$search_post_id = $request->get_param( 'search' );
		if ( ! is_int( $search_post_id ) && ! $search_post_id instanceof \WP_Post ) {
			return $query_args;
		}
		if ( ! get_post( $search_post_id ) ) {
			return $query_args;
		}
		$query_args['post__in'] = [ $search_post_id ];
		$query_args['s']        = '';
		return $query_args;
	}
}
