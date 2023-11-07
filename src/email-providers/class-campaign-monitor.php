<?php
/**
 * WP_Newsletter_Builder class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder\Email_Providers;

use WP_Newsletter_Builder\Singleton;

/**
 * Campaign Monitor Client class
 */
class Campaign_Monitor implements Email_Provider {
	use Singleton;

	/**
	 * Settings key.
	 *
	 * @var string
	 */
	public const SETTINGS_KEY = 'nb_campaign_monitor_settings';

	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function setup() {
		add_action( 'init', [ $this, 'maybe_register_settings_page' ] );
	}

	/**
	 * Registers the submenu settings page for the Campaign Monitor options.
	 *
	 * @return void
	 */
	public function maybe_register_settings_page() {
		if ( function_exists( 'fm_register_submenu_page' ) && \current_user_can( 'manage_options' ) ) {
			\fm_register_submenu_page( static::SETTINGS_KEY, 'edit.php?post_type=nb_newsletter', __( 'Campaign Monitor Settings', 'wp-newsletter-builder' ), __( 'Campaign Monitor Settings', 'wp-newsletter-builder' ) );
			\add_action( 'fm_submenu_' . static::SETTINGS_KEY, [ $this, 'register_fields' ] );
		}
	}

	/**
	 * Registers the fields on the settings page for the Campaign Monitor options.
	 *
	 * @return void
	 */
	public function register_fields() {
		$settings = new \Fieldmanager_Group(
			[
				'name'     => static::SETTINGS_KEY,
				'children' => [
					'api_key'            => new \Fieldmanager_TextField( __( 'API Key', 'wp-newsletter-builder' ) ),
					'client_id'          => new \Fieldmanager_TextField( __( 'Client ID', 'wp-newsletter-builder' ) ),
					'confirmation_email' => new \Fieldmanager_TextField( __( 'Confirmation Email', 'wp-newsletter-builder' ) ),
				],
			]
		);

		$settings->activate_submenu_page();
	}

	/**
	 * Get the API key and instantiate a client using the API key.
	 *
	 * @return \CS_REST_General
	 */
	public function get_client() {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['api_key'] ) ) {
			return false;
		}
		$auth = [ 'api_key' => $settings['api_key'] ];
		$wrap = new \CS_REST_General( $auth );

		return $wrap;
	}

	/**
	 * Gets the lists for the client.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return array|false
	 */
	public function get_lists() {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['api_key'] ) || empty( $settings['client_id'] ) ) {
			return false;
		}
		$auth = [ 'api_key' => $settings['api_key'] ];

		$wrap = new \CS_REST_Clients(
			$settings['client_id'],
			$auth
		);

		return $wrap->get_lists()->response;
	}

	/**
	 * Creates an email campaign.
	 *
	 * @param int    $newsletter_id The id of the nb_newsletter post.
	 * @param array  $list_ids    The list ids to send the campaign to.
	 * @param string $campaign_id Optional campaign id to update.
	 * @return array The response from the API.
	 */
	public function create_campaign( $newsletter_id, $list_ids, $campaign_id = null ) {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['api_key'] ) || empty( $settings['client_id'] ) ) {
			return false;
		}
		$auth = [ 'api_key' => $settings['api_key'] ];

		$wrap = new \CS_REST_Campaigns( $campaign_id, $auth );

		$newsletter = get_post( $newsletter_id );
		if ( ! $newsletter || is_wp_error( $newsletter ) ) {
			return false;
		}

		// Newsletter from name.
		$nl_from_name = get_post_meta( $newsletter_id, 'nb_newsletter_from_name', true );

		// If newsletter from name is not set try to fill from email type.
		if ( empty( $nl_from_name ) ) {
			$nl_email_type = get_post_meta( $newsletter_id, 'nb_newsletter_email_type', true );
			$email_types   = get_option( 'nb_email_types' );
			$type_key      = array_search( $nl_email_type, array_column( $email_types, 'uuid4' ), true );
			if ( false !== $type_key ) {
				$nl_from_name = $email_types[ $type_key ]['from_name'] ?? '';
			}
		}
		$url = add_query_arg(
			[
				'post_type' => 'nb_newsletter',
				'p'         => $newsletter->ID,
			],
			home_url(),
		);

		/**
		 * Filter the URL for the HTML version of the newsletter.
		 *
		 * @param string $url The URL.
		 */
		$url = apply_filters( 'wp_newsletter_builder_html_url', $url );

		$params = [
			'Subject'    => get_post_meta( $newsletter->ID, 'nb_newsletter_subject', true ),
			'Name'       => sprintf( '%s - Post %d - %s', $newsletter->post_title, $newsletter->ID, get_post_modified_time( 'Y-m-d H:i:s', false, $newsletter->ID ) ),
			'FromName'   => $nl_from_name,
			'FromEmail'  => $settings['from_email'],
			'ReplyTo'    => $settings['reply_to_email'],
			'HtmlUrl'    => $url,
			'ListIDs'    => $list_ids,
			'SegmentIDs' => [], // TODO: Possibly add this to the GUI.
		];

		$result = $wrap->create( $settings['client_id'], $params );

		return [
			'response'         => $result->response,
			'http_status_code' => $result->http_status_code,
		];
	}

	/**
	 * Sends a campaign.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array|false The response from the API.
	 */
	public function send_campaign( $campaign_id ) {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['api_key'] ) || empty( $settings['client_id'] ) ) {
			return false;
		}
		$auth = [ 'api_key' => $settings['api_key'] ];

		$wrap = new \CS_REST_Campaigns( $campaign_id, $auth );

		$result = $wrap->send(
			[
				'ConfirmationEmail' => $settings['confirmation_email'],
				'SendDate'          => 'immediately',
			]
		);

		return [
			'response'         => $result->response,
			'http_status_code' => $result->http_status_code,
		];
	}

	/**
	 * Gets campaign summary.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array|false The response from the API.
	 */
	public function get_campaign_summary( $campaign_id ) {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['api_key'] ) || empty( $settings['client_id'] ) ) {
			return false;
		}
		$auth = [ 'api_key' => $settings['api_key'] ];

		$wrap = new \CS_REST_Campaigns( $campaign_id, $auth );

		$result = $wrap->get_summary();
		return [
			'response'         => $result->response,
			'http_status_code' => $result->http_status_code,
		];
	}

	/**
	 * Add subscriber to list
	 *
	 * @param string $list_id The list id.
	 * @param string $email The email address.
	 * @param array  $custom_fields The custom fields.
	 * @return array|false
	 */
	public function add_subscriber( $list_id, $email, $custom_fields = [] ) {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['api_key'] ) || empty( $settings['client_id'] ) ) {
			return false;
		}
		$auth = [ 'api_key' => $settings['api_key'] ];

		$wrap = new \CS_REST_Subscribers( $list_id, $auth );

		$result = $wrap->add(
			[
				'EmailAddress'   => $email,
				'Resubscribe'    => true,
				'ConsentToTrack' => 'yes',
				'CustomFields'   => $custom_fields,
			]
		);

		return [
			'response'         => $result->response,
			'http_status_code' => $result->http_status_code,
		];
	}

	/**
	 * Remove subscriber from list.
	 *
	 * @param string $list_id The list id.
	 * @param string $email The email address.
	 * @return array|false
	 */
	public function remove_subscriber( $list_id, $email ) {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['api_key'] ) || empty( $settings['client_id'] ) ) {
			return false;
		}
		$auth = [ 'api_key' => $settings['api_key'] ];

		$wrap = new \CS_REST_Subscribers( $list_id, $auth );

		$result = $wrap->unsubscribe( $email );

		return [
			'response'         => $result->response,
			'http_status_code' => $result->http_status_code,
		];
	}
}
