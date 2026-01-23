<?php
/**
 * WP_Newsletter_Builder class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder\Email_Providers;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Beehiiv Client class
 */
class Beehiiv implements Email_Provider {
	/**
	 * Settings key.
	 *
	 * @var string
	 */
	public const SETTINGS_KEY = 'nb_beehiiv_settings';

	/**
	 * API base URL.
	 *
	 * @var string
	 */
	private const API_BASE_URL = 'https://api.beehiiv.com/v2';

	/**
	 * Environment variable name for the API key on VIP.
	 *
	 * @var string
	 */
	private const VIP_API_KEY_ENV_VAR = 'BEEHIIV_API_KEY';

	/**
	 * Network option name for the publication ID on VIP multisite.
	 *
	 * @var string
	 */
	private const VIP_PUBLICATION_ID_OPTION = 'beehiiv_publication_id';

	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function setup(): void {
		add_action( 'init', [ $this, 'maybe_register_settings_page' ] );
	}

	/**
	 * Registers the submenu settings page for the Beehiiv options.
	 *
	 * @return void
	 */
	public function maybe_register_settings_page(): void {
		if ( function_exists( 'fm_register_submenu_page' ) && \current_user_can( 'manage_options' ) ) {
			\fm_register_submenu_page( static::SETTINGS_KEY, 'edit.php?post_type=nb_newsletter', __( 'Beehiiv Settings', 'wp-newsletter-builder' ), __( 'Beehiiv Settings', 'wp-newsletter-builder' ) );
			\add_action( 'fm_submenu_' . static::SETTINGS_KEY, [ $this, 'register_fields' ] );
		}
	}

	/**
	 * Checks if the API key is configured via VIP environment variable.
	 *
	 * @return bool
	 */
	private function is_using_vip_env_var(): bool {
		if ( ! function_exists( 'vip_get_env_var' ) ) {
			return false;
		}

		$api_key = vip_get_env_var( self::VIP_API_KEY_ENV_VAR );
		return ! empty( $api_key );
	}

	/**
	 * Gets the API key from VIP environment variable.
	 *
	 * @return string|false
	 */
	private function get_vip_api_key(): string|false {
		if ( ! function_exists( 'vip_get_env_var' ) ) {
			return false;
		}

		$api_key = vip_get_env_var( self::VIP_API_KEY_ENV_VAR );
		return ! empty( $api_key ) ? $api_key : false;
	}

	/**
	 * Gets the publication ID from network option (for VIP multisite).
	 *
	 * @return string|false
	 */
	private function get_vip_publication_id(): string|false {
		if ( is_multisite() ) {
			$publication_id = get_site_option( self::VIP_PUBLICATION_ID_OPTION );
		} else {
			$publication_id = get_option( self::VIP_PUBLICATION_ID_OPTION );
		}

		return ! empty( $publication_id ) ? $publication_id : false;
	}

	/**
	 * Registers the fields on the settings page for the Beehiiv options.
	 *
	 * @return void
	 */
	public function register_fields(): void {
		$children = [];

		if ( $this->is_using_vip_env_var() ) {
			// When using VIP env var, show a message and only allow from name/email configuration.
			$children['env_var_notice'] = new \Fieldmanager_Checkbox(
				[
					'label'       => __( 'API Key is configured via environment variable (BEEHIIV_API_KEY) and is not shown for security reasons. Publication ID is managed via network settings.', 'wp-newsletter-builder' ),
					'attributes'  => [
						'disabled' => 'disabled',
						'checked'  => 'checked',
					],
					'description' => $this->get_vip_configuration_status(),
				]
			);
		} else {
			// Standard configuration via settings UI.
			$children['api_key']        = new \Fieldmanager_Password( __( 'API Key', 'wp-newsletter-builder' ) );
			$children['publication_id'] = new \Fieldmanager_TextField( __( 'Publication ID', 'wp-newsletter-builder' ) );
		}

		// From name and email are always configurable.
		$children['from_name']  = new \Fieldmanager_TextField( __( 'From Name', 'wp-newsletter-builder' ) );
		$children['from_email'] = new \Fieldmanager_TextField( __( 'From Email', 'wp-newsletter-builder' ) );

		$settings = new \Fieldmanager_Group(
			[
				'name'     => static::SETTINGS_KEY,
				'children' => $children,
			]
		);

		$settings->activate_submenu_page();
	}

	/**
	 * Gets a status message about the VIP configuration.
	 *
	 * @return string
	 */
	private function get_vip_configuration_status(): string {
		$publication_id = $this->get_vip_publication_id();
		$messages       = [];

		if ( $publication_id ) {
			$messages[] = sprintf(
				/* translators: %s: publication ID */
				__( 'Publication ID: %s', 'wp-newsletter-builder' ),
				esc_html( $publication_id )
			);
		} else {
			$messages[] = __( 'Warning: Publication ID is not configured. Please contact a network super-admin to set the "beehiiv_publication_id" option.', 'wp-newsletter-builder' );
		}

		return implode( '<br>', $messages );
	}

	/**
	 * Get the API key and instantiate a client using the API key.
	 * For Beehiiv, we return the settings array since there's no SDK.
	 *
	 * Supports two configuration modes:
	 * 1. VIP environment variable mode: API key from env var, publication ID from network option.
	 * 2. Standard mode: Both values from plugin settings.
	 *
	 * @return array{api_key: string, publication_id: string}|false
	 */
	public function get_client(): array|false {
		// Check for VIP environment variable configuration first.
		$vip_api_key = $this->get_vip_api_key();

		if ( $vip_api_key ) {
			$publication_id = $this->get_vip_publication_id();

			if ( empty( $publication_id ) ) {
				return false;
			}

			return [
				'api_key'        => $vip_api_key,
				'publication_id' => $publication_id,
			];
		}

		// Fall back to standard settings configuration.
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || ! is_array( $settings ) || empty( $settings['api_key'] ) || empty( $settings['publication_id'] ) ) {
			return false;
		}

		return [
			'api_key'        => $settings['api_key'],
			'publication_id' => $settings['publication_id'],
		];
	}

	/**
	 * Makes an API request to Beehiiv.
	 *
	 * @param string               $endpoint The API endpoint (without base URL).
	 * @param string               $method   The HTTP method.
	 * @param array<string, mixed> $body     The request body.
	 * @return array{body: mixed, status_code: int}|false
	 */
	private function api_request( string $endpoint, string $method = 'GET', array $body = [] ): array|false {
		$client = $this->get_client();
		if ( ! $client ) {
			return false;
		}

		$url = self::API_BASE_URL . $endpoint;

		$args = [
			'method'  => $method,
			'headers' => [
				'Authorization' => 'Bearer ' . $client['api_key'],
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
			],
			'timeout' => 30,
		];

		if ( ! empty( $body ) && in_array( $method, [ 'POST', 'PUT', 'PATCH' ], true ) ) {
			$args['body'] = wp_json_encode( $body );
		}

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return [
			'body'        => json_decode( wp_remote_retrieve_body( $response ) ),
			'status_code' => wp_remote_retrieve_response_code( $response ),
		];
	}

	/**
	 * Gets the segments for the client.
	 * In Beehiiv, we target segments instead of lists.
	 *
	 * @return mixed
	 */
	public function get_lists(): mixed {
		$client = $this->get_client();
		if ( ! $client ) {
			return [];
		}

		$endpoint = '/publications/' . $client['publication_id'] . '/segments?limit=100&status=completed';

		try {
			$response = $this->api_request( $endpoint );
			if ( ! $response || empty( $response['body']->data ) ) {
				return [];
			}
			$result = $response['body']->data;
		} catch ( \Exception $ex ) {
			$result = [];
		}

		if ( ! is_array( $result ) ) {
			return [];
		}

		$lists = [];
		foreach ( $result as $segment ) {
			$lists[] = [
				'ListID' => $segment->id,
				'Name'   => $segment->name,
			];
		}

		// Sort by the Name field.
		usort(
			$lists,
			function ( $a, $b ) {
				return strcasecmp( $a['Name'], $b['Name'] );
			}
		);

		return $lists;
	}

	/**
	 * Creates an email campaign (post) in Beehiiv.
	 *
	 * @param int           $newsletter_id The id of the nb_newsletter post.
	 * @param array<string> $list_ids      The segment ids to send the campaign to.
	 * @param string        $campaign_id   Optional campaign id to update.
	 * @param string        $from_name     The from name.
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function create_campaign( int $newsletter_id, array $list_ids, string $campaign_id = null, string $from_name ): array|false {
		$client = $this->get_client();
		if ( ! $client ) {
			return false;
		}

		$html_content = $this->get_content( $newsletter_id );
		if ( ! $html_content ) {
			$html_content = '';
		}

		$css_to_inline_styles = new CssToInlineStyles();
		$html_content         = $css_to_inline_styles->convert(
			$html_content,
			''
		);

		$subject = get_post_meta( $newsletter_id, 'nb_newsletter_subject', true );
		$preview = get_post_meta( $newsletter_id, 'nb_newsletter_preview', true );

		$request_body = [
			'title'        => get_the_title( $newsletter_id ),
			'subtitle'     => ! empty( $preview ) ? $preview : null,
			'body_content' => $html_content,
			'status'       => 'draft',
		];

		// Add email settings with segment targeting.
		if ( ! empty( $list_ids ) ) {
			$request_body['email_settings'] = [
				'subject_line'        => $subject,
				'preview_text'        => ! empty( $preview ) ? $preview : null,
				'include_segment_ids' => $list_ids,
			];
		}

		$endpoint = '/publications/' . $client['publication_id'] . '/posts';
		$response = $this->api_request( $endpoint, 'POST', $request_body );

		if ( ! $response ) {
			return false;
		}

		return [
			'response'         => $response['body'],
			'http_status_code' => $response['status_code'],
		];
	}

	/**
	 * Sends a campaign.
	 *
	 * @param string $campaign_id The campaign id (post id in Beehiiv).
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function send_campaign( string $campaign_id ): array|false {
		$client = $this->get_client();
		if ( ! $client ) {
			return false;
		}

		// Update the post status to 'confirmed' to send immediately.
		$request_body = [
			'status' => 'confirmed',
		];

		$endpoint = '/publications/' . $client['publication_id'] . '/posts/' . $campaign_id;
		$response = $this->api_request( $endpoint, 'PATCH', $request_body );

		if ( ! $response ) {
			return false;
		}

		return [
			'response'         => $response['body'],
			'http_status_code' => $response['status_code'],
		];
	}

	/**
	 * Gets campaign summary.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array{
	 *   response: mixed,
	 *   success: boolean,
	 * }|false  The response from the API.
	 */
	public function get_campaign_summary( string $campaign_id ): array|false {
		$client = $this->get_client();
		if ( ! $client ) {
			return false;
		}

		$endpoint = '/publications/' . $client['publication_id'] . '/posts/' . $campaign_id . '?expand[]=stats';
		$response = $this->api_request( $endpoint );

		if ( ! $response || 200 !== $response['status_code'] ) {
			return false;
		}

		$post_data = $response['body']->data ?? $response['body'];

		return [
			'response' => [
				'Status'       => $post_data->status ?? '',
				'Name'         => $post_data->title ?? '',
				'Recipients'   => $post_data->stats->email->delivered ?? '',
				'TotalOpened'  => $post_data->stats->email->opens ?? '',
				'UniqueOpened' => $post_data->stats->email->unique_opens ?? '',
			],
			'success'  => true,
		];
	}

	/**
	 * Determine if the campaign was created successfully.
	 *
	 * @param array|false $result {.
	 *   @type mixed $response The deserialised result of the API call.
	 *   @type int $http_status_code The http status code of the API call.
	 * } The response from the creation request.
	 * @phpstan-param array{response: mixed, http_status_code: int}|false $result
	 * @return bool
	 */
	public function campaign_created_successfully( array|false $result ): bool {
		if ( empty( $result['http_status_code'] ) ) {
			return false;
		}
		// Beehiiv returns 200 or 201 for successful creation.
		return in_array( $result['http_status_code'], [ 200, 201 ], true );
	}

	/**
	 * Gets the campaign id from the result.
	 *
	 * @param array|false $result {.
	 *   @type mixed $response The deserialised result of the API call.
	 *   @type int $http_status_code The http status code of the API call.
	 * } The response from the creation request.
	 * @phpstan-param array{response: mixed, http_status_code: int}|false $result
	 * @return mixed
	 */
	public function get_campaign_id_from_create_result( array|false $result ): mixed {
		if ( empty( $result ) ) {
			return false;
		}
		$response = $result['response'];
		if ( ! empty( $response ) && is_object( $response ) ) {
			// Handle both direct response and nested data response.
			return $response->data->id ?? $response->id ?? false;
		}
		return false;
	}

	/**
	 * Add subscriber to publication.
	 *
	 * @param string                       $list_id       The list id (not used in Beehiiv, subscriptions are publication-wide).
	 * @param string                       $email         The email address.
	 * @param array<array<string, string>> $custom_fields The custom fields.
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function add_subscriber( string $list_id, string $email, array $custom_fields = [] ): array|false {
		$client = $this->get_client();
		if ( ! $client ) {
			return false;
		}

		$request_body = [
			'email'              => $email,
			'reactivate_existing' => true,
			'send_welcome_email' => false,
		];

		// Add custom fields if provided.
		if ( ! empty( $custom_fields ) ) {
			$request_body['custom_fields'] = $custom_fields;
		}

		$endpoint = '/publications/' . $client['publication_id'] . '/subscriptions';
		$response = $this->api_request( $endpoint, 'POST', $request_body );

		if ( ! $response ) {
			return false;
		}

		return [
			'response'         => $response['body'],
			'http_status_code' => $response['status_code'],
		];
	}

	/**
	 * Remove subscriber from publication.
	 *
	 * @param string $list_id The list id (not used in Beehiiv).
	 * @param string $email   The email address.
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function remove_subscriber( string $list_id, string $email ): array|false {
		$client = $this->get_client();
		if ( ! $client ) {
			return false;
		}

		// First, get the subscription ID by email.
		$endpoint     = '/publications/' . $client['publication_id'] . '/subscriptions/by_email/' . rawurlencode( $email );
		$get_response = $this->api_request( $endpoint );

		if ( ! $get_response || empty( $get_response['body']->data->id ) ) {
			return false;
		}

		$subscription_id = $get_response['body']->data->id;

		// Update subscription status to unsubscribed.
		$request_body = [
			'unsubscribe' => true,
		];

		$endpoint = '/publications/' . $client['publication_id'] . '/subscriptions/' . $subscription_id;
		$response = $this->api_request( $endpoint, 'PATCH', $request_body );

		if ( ! $response ) {
			return false;
		}

		return [
			'response'         => $response['body'],
			'http_status_code' => $response['status_code'],
		];
	}

	/**
	 * Whether the provider manages from names.
	 * Beehiiv uses publication-level settings for from name.
	 *
	 * @return boolean
	 */
	public function provider_manages_from_names(): bool {
		return false;
	}

	/**
	 * Whether or not the provider uses suppression lists.
	 * Beehiiv does not use suppression lists; segment membership determines eligibility.
	 *
	 * @return boolean
	 */
	public function uses_suppression_lists(): bool {
		return false;
	}

	/**
	 * Gets the suppression lists.
	 * Not applicable for Beehiiv.
	 *
	 * @return mixed
	 */
	public function get_suppression_lists(): mixed {
		return [];
	}

	/**
	 * Gets the html content for the newsletter.
	 *
	 * @param int $post_id The post id.
	 * @return string|false
	 */
	private function get_content( $post_id ) {
		global $wp_query;
		// Back up globals.
		$old_wp_query     = $wp_query;
		$old_current_user = wp_get_current_user();

		// Render anonymously.
		wp_set_current_user( 0 );

		// Set up a new global query for the post.
		$args     = [
			'p'         => $post_id,
			'post_type' => 'nb_newsletter',
		];
		$wp_query = new \WP_Query( $args ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		// Capture template output for the new query.
		ob_start();
		load_template( WP_PLUGIN_DIR . '/wp-newsletter-builder/single-nb_newsletter.php' );
		$content = ob_get_clean();

		// Restore globals.
		$wp_query = $old_wp_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		wp_set_current_user( $old_current_user->ID );

		return $content;
	}
}
