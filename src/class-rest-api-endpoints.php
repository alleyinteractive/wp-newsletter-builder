<?php
/**
 * Additional REST API Endpoints class file
 *
 * @package wp-newsletter-builder
 */

declare(strict_types=1);

namespace WP_Newsletter_Builder;

use WP_Error;
use WP_REST_Request;

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
	public function register_endpoints(): void {
		register_rest_route(
			'wp-newsletter-builder/v1',
			'/lists/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_lists' ],
				'permission_callback' => function () {
					return true;
					// return current_user_can( 'edit_posts' ); TODO.
				},
			]
		);
		register_rest_route(
			'wp-newsletter-builder/v1',
			'/email-types/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_email_types' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
		register_rest_route(
			'wp-newsletter-builder/v1',
			'/footer_settings/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_footer_settings' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
		register_rest_route(
			'wp-newsletter-builder/v1',
			'/status/(?P<post_id>[a-f0-9]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_status' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
		register_rest_route(
			'wp-newsletter-builder/v1',
			'/subscribe/',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'subscribe' ],
				'permission_callback' => '__return_true',
			]
		);
		register_rest_route(
			'wp-newsletter-builder/v1',
			'/export/(?P<post_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_export' ],
				'permission_callback' => function ( WP_REST_Request $request ) {
					$post_id = $request->get_param( 'post_id' );
					return is_numeric( $post_id ) && current_user_can( 'edit_post', (int) $post_id );
				},
			]
		);
		register_rest_route(
			'wp-newsletter-builder/v1',
			'/suppression-lists/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_suppression_lists' ],
				'permission_callback' => function () {
					return true;
					// return current_user_can( 'edit_posts' ); TODO.
				},
			]
		);
	}

	/**
	 * Gets the lists from the Campaign Monitor API.
	 *
	 * @return mixed
	 */
	public function get_lists(): mixed {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to access this endpoint.', 'wp-newsletter-builder' ), [ 'status' => 401 ] );
		}
		global $newsletter_builder_email_provider;
		if ( empty( $newsletter_builder_email_provider ) || ! $newsletter_builder_email_provider instanceof Email_Providers\Email_Provider ) {
			return new \WP_Error( 'no_email_provider_selected', esc_html__( 'No email provider selected for WP Newsletter Builder.', 'wp-newsletter-builder' ), [ 'status' => 400 ] );
		}

		return $newsletter_builder_email_provider->get_lists();
	}

	/**
	 * Gets the email types from options.
	 *
	 * @return WP_Error|array<int|string, \stdClass>
	 */
	public function get_email_types(): WP_Error|array {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to access this endpoint.', 'wp-newsletter-builder' ), [ 'status' => 401 ] );
		}
		$types     = Email_Types::get_email_types();
		$new_types = [];
		foreach ( $types as $type ) {
			if ( ! empty( $type['uuid4'] ) && is_string( $type['uuid4'] ) ) {
				$new_types[ $type['uuid4'] ] = (object) $type;
			}
		}
		return $new_types;
	}

	/**
	 * Gets the settings from options.
	 *
	 * @return WP_Error|false|array<mixed>
	 */
	public function get_footer_settings(): WP_Error|false|array {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to access this endpoint.', 'wp-newsletter-builder' ), [ 'status' => 401 ] );
		}
		$settings = new Settings();

		return $settings->get_footer_settings();
	}

	/**
	 * Gets the status for a newsletter.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return array{
	 *   Status?: string,
	 * }  The newsletter status.
	 */
	public function get_status( WP_REST_Request $request ): array {
		$post_id_param = $request->get_param( 'post_id' );
		$post_id       = is_numeric( $post_id_param ) ? absint( $post_id_param ) : 0;
		if ( empty( $post_id ) ) {
			return [];
		}
		$cache_key = 'nb_status_' . $post_id;
		$cache     = wp_cache_get( $cache_key );
		if ( false !== $cache ) {
			if ( is_array( $cache ) && isset( $cache['Status'] ) && is_string( $cache['Status'] ) ) {
				return $cache;
			} else {
				return [];
			}
		}
		$campaign_id = get_post_meta( $post_id, 'nb_newsletter_campaign_id', true );
		if ( empty( $campaign_id ) || ! is_string( $campaign_id ) ) {
			$next = wp_next_scheduled( 'wp_newsletter_builder_send_newsletter', [ $post_id ] );
			if ( ! empty( $next ) ) {
				return [
					'Status' => __( 'Queued', 'wp-newsletter-builder' ),
				];
			}
			return [
				'Status' => __( 'Not sent', 'wp-newsletter-builder' ),
			];
		}

		global $newsletter_builder_email_provider;
		if ( empty( $newsletter_builder_email_provider ) || ! $newsletter_builder_email_provider instanceof Email_Providers\Email_Provider ) {
			return [
				'Status' => __( 'No email provider selected for WP Newsletter Builder.', 'wp-newsletter-builder' ),
			];
		}
		$status = $newsletter_builder_email_provider->get_campaign_summary( $campaign_id );
		if ( ! empty( $status ) && is_array( $status['response'] ) && $status['success'] ) {
			$status['response']['Status'] = __( 'Sent' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			wp_cache_set( $cache_key, $status['response'], '', 5 * MINUTE_IN_SECONDS );
			return $status['response'];
		}

		$send_result = get_post_meta( $post_id, 'nb_newsletter_send_result', true );
		if ( ! empty( $send_result ) && is_array( $send_result ) ) {
			if ( is_string( $send_result['response'] ) && ( 200 > $send_result['http_status_code'] || 300 <= $send_result['http_status_code'] ) ) {
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
		if ( ! empty( $campaign_result ) && is_array( $campaign_result ) ) {
			if ( is_string( $campaign_result['response'] ) && ( 200 > $campaign_result['http_status_code'] || 300 <= $campaign_result['http_status_code'] ) ) {
				$result           = [];
				$result['Status'] = sprintf( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					'%s: %s',
					__( 'Error' ),
					$campaign_result['response']
				);
				return $result;
			}
		}
		return [];
	}

	/**
	 * Subscribes a user to a list.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return array{
	 *   success: bool,
	 *   message: string,
	 *   results?: array<string, array{success: bool, message: mixed}>,
	 * }
	 */
	public function subscribe( WP_REST_Request $request ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$email = $request->get_param( 'email' );
		if ( empty( $email ) || ! is_string( $email ) ) {
			return [
				'success' => false,
				'message' => __( 'No email address provided.', 'wp-newsletter-builder' ),
			];
		}
		$list_ids_param = $request->get_param( 'listIds' );
		if ( empty( $list_ids_param ) || ! is_string( $list_ids_param ) ) {
			return [
				'success' => false,
				'message' => __( 'No lists provided.', 'wp-newsletter-builder' ),
			];
		}
		$list_ids     = explode( ',', $list_ids_param );
		$list_results = [];
		global $newsletter_builder_email_provider;
		if ( empty( $newsletter_builder_email_provider ) || ! $newsletter_builder_email_provider instanceof Email_Providers\Email_Provider ) {
			return [
				'success' => false,
				'message' => __( 'No email provider selected.', 'wp-newsletter-builder' ),
			];
		}
		foreach ( $list_ids as $list_id ) {
			$result = $newsletter_builder_email_provider->add_subscriber( $list_id, $email );
			if ( ! empty( $result ) && 200 === $result['http_status_code'] ) {
				$list_results[ $list_id ] = [
					'success' => true,
					'message' => __( 'Successfully subscribed.', 'wp-newsletter-builder' ),
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
			'message' => __( 'Successfully subscribed.', 'wp-newsletter-builder' ),
			'results' => $list_results,
		];
	}

	/**
	 * Gets the rendered HTML of a newsletter for export.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_Error|array{
	 *   html: string,
	 *   filename: string,
	 * }
	 */
	public function get_export( WP_REST_Request $request ): WP_Error|array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$post_id = $request->get_param( 'post_id' );
		$post_id = is_numeric( $post_id ) ? (int) $post_id : 0;
		if ( 'nb_newsletter' !== get_post_type( $post_id ) ) {
			return new WP_Error( 'rest_post_invalid_id', esc_html__( 'Invalid newsletter ID.', 'wp-newsletter-builder' ), [ 'status' => 404 ] );
		}

		$renderer = new Newsletter_Renderer();
		$html     = $renderer->render_for_export( $post_id );
		if ( false === $html ) {
			return new WP_Error( 'render_failed', esc_html__( 'The newsletter could not be rendered.', 'wp-newsletter-builder' ), [ 'status' => 500 ] );
		}

		return [
			'html'     => $html,
			'filename' => $renderer->get_export_filename( $post_id ),
		];
	}

	/**
	 * Gets the suppression lists from the Email Provider.
	 *
	 * @return mixed
	 */
	public function get_suppression_lists(): mixed {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to access this endpoint.', 'wp-newsletter-builder' ), [ 'status' => 401 ] );
		}
		global $newsletter_builder_email_provider;
		if ( empty( $newsletter_builder_email_provider ) || ! $newsletter_builder_email_provider instanceof Email_Providers\Email_Provider ) {
			return new \WP_Error( 'no_email_provider_selected', esc_html__( 'No email provider selected for WP Newsletter Builder.', 'wp-newsletter-builder' ), [ 'status' => 400 ] );
		}

		return $newsletter_builder_email_provider->get_suppression_lists();
	}
}
