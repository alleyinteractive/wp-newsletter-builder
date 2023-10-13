<?php
/**
 * Additional REST API Endpoints class file
 *
 * @package Newsletter_Builder
 */

namespace Newsletter_Builder;

use function Newsletter_Builder\get_byline;

/**
 * Adds additional Endpoints to the REST API.
 */
class Rest_API_Endpoints {
	/**
	 * Set everything up.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
	}

	/**
	 * Sets up the endpoint.
	 *
	 * @return void
	 */
	public function register_endpoints() {
		register_rest_route(
			'newsletter-builder/v1',
			'/lists/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_lists' ],
				'permission_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			]
		);
		register_rest_route(
			'newsletter-builder/v1',
			'/email-types/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_email_types' ],
				'permission_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			]
		);
		register_rest_route(
			'newsletter-builder/v1',
			'/footer_settings/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_footer_settings' ],
				'permission_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			]
		);
		register_rest_route(
			'newsletter-builder/v1/',
			'/status/(?P<post_id>[a-f0-9]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_status' ],
				'permission_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			]
		);
		register_rest_route(
			'newsletter-builder/v1/',
			'/subscribe/',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'subscribe' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Gets the lists from the Campaign Monitor API.
	 *
	 * @return array
	 */
	public function get_lists() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to access this endpoint.', 'newsletter-builder' ), [ 'status' => 401 ] );
		}
		$lists = Campaign_Monitor_Client::instance()->get_lists();
		return $lists;
	}

	/**
	 * Gets the email types from options.
	 *
	 * @return array
	 */
	public function get_email_types() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to access this endpoint.', 'newsletter-builder' ), [ 'status' => 401 ] );
		}
		$types_class = new Email_Types();
		$types       = $types_class->get_email_types();
		$new_types   = [];
		foreach ( $types as $type ) {
			if ( ! empty( $type['uuid4'] ) ) {
				$new_types[ $type['uuid4'] ] = (object) $type;
			}
		}
		return $new_types;
	}

	/**
	 * Gets the settings from options.
	 *
	 * @return array
	 */
	public function get_footer_settings() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to access this endpoint.', 'newsletter-builder' ), [ 'status' => 401 ] );
		}
		$footer_settings = Campaign_Monitor_Client::instance()->get_footer_settings();
		return $footer_settings;
	}

	/**
	 * Gets the status for a newsletter.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return array
	 */
	public function get_status( $request ) {
		$post_id = $request->get_param( 'post_id' );
		if ( empty( $post_id ) ) {
			return [];
		}
		$cache_key = 'nb_status_' . $post_id;
		$cache     = wp_cache_get( $cache_key );
		if ( false !== $cache ) {
			return $cache;
		}
		$campaign_id = get_post_meta( $post_id, 'nb_newsletter_campaign_id', true );
		if ( empty( $campaign_id ) ) {
			$next = wp_next_scheduled( 'newsletter_builder_send_newsletter', [ $post_id ] );
			if ( ! empty( $next ) ) {
				return [
					'Status' => __( 'Queued', 'newsletter-builder' ),
				];
			}
			return [
				'Status' => __( 'Not sent', 'newsletter-builder' ),
			];
		}
		$status = Campaign_Monitor_Client::instance()->get_campaign_summary( $campaign_id );
		if ( ! empty( $status ) && 200 === $status['http_status_code'] ) {
			$status['response']->Status = __( 'Sent' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			wp_cache_set( $cache_key, $status['response'], null, 5 * MINUTE_IN_SECONDS );
			return $status['response'];
		}
		$send_result = get_post_meta( $post_id, 'nb_newsletter_send_result', true );
		if ( ! empty( $send_result ) ) {
			if ( 200 > $send_result->http_status_code || 300 <= $send_result->http_status_code ) {
				$result           = [];
				$result['Status'] = sprintf( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					'%s: %s',
					__( 'Error' ),
					$send_result['response']
				);
				return $result;
			}
		}
		$campaign_result = get_post_meta( $post_id, 'nb_newsletter_campaign_result', true );
		if ( ! empty( $campaign_result ) ) {
			if ( 200 > $campaign_result->http_status_code || 300 <= $campaign_result->http_status_code ) {
				$result           = [];
				$result['Status'] = sprintf( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					'%s: %s',
					__( 'Error' ),
					$campaign_result['response']
				);
				return $result;         }
		}
		return [];
	}

	/**
	 * Subscribes a user to a list.
	 *
	 * @param \WP_Rest_Request $request The request object.
	 * @return array
	 */
	public function subscribe( $request ) {
		$email = $request->get_param( 'email' );
		if ( empty( $email ) ) {
			return [
				'success' => false,
				'message' => __( 'No email address provided.', 'newsletter-builder' ),
			];
		}
		$list_ids = explode( ',', $request->get_param( 'listIds' ) );
		if ( empty( $list_ids ) ) {
			return [
				'success' => false,
				'message' => __( 'No lists selected.', 'newsletter-builder' ),
			];
		}
		$list_results = [];
		foreach ( $list_ids as $list_id ) {
			$result = Campaign_Monitor_Client::instance()->add_subscriber( $list_id, $email );
			if ( ! empty( $result ) && 200 === $result['http_status_code'] ) {
				$list_results[ $list_id ] = [
					'success' => true,
					'message' => __( 'Successfully subscribed.', 'newsletter-builder' ),
				];
			} else {
				$list_results[ $list_id ] = [
					'success' => false,
					'message' => $result,
				];
			}
		}
		return [
			'success' => true,
			'message' => __( 'Successfully subscribed.', 'newsletter-builder' ),
			'results' => $list_results,
		];
	}
}
