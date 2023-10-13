<?php
/**
 * Newsletter_Builder class file
 *
 * @package newsletter-builder
 */

namespace Newsletter_Builder;

use Newsletter_Builder\Singleton;

/**
 * Campaign Monitor Client class
 */
class Campaign_Monitor_Client {
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
	 * Displays an error message if Fieldmanager is not installed.
	 *
	 * @return void
	 */
	public function fieldmanager_not_found_error() {
		?>
		<div class="error notice">
			<p>
			<?php
			printf(
				/* translators: %1$s: Opening a tag to Fieldmanager plugin, %2$s closing a tag. */
				esc_html__( 'The Newsletter Builder plugin requires the %1$sWordPress Fieldmanager plugin%2$s', 'newsletter-builder' ),
				'<a href="https://github.com/alleyinteractive/wordpress-fieldmanager">',
				'</a>',
			);
			?>
			</p>
		</div>
		<?php
	}

	/**
	 * Registers the submenu settings page for the Campaign Monitor options.
	 *
	 * @return void
	 */
	public function maybe_register_settings_page() {
		if ( ! defined( 'FM_VERSION' ) ) {
			add_action( 'admin_notices', [ $this, 'fieldmanager_not_found_error' ] );
			return;
		}

		if ( function_exists( 'fm_register_submenu_page' ) && \current_user_can( 'manage_options' ) ) {
			\fm_register_submenu_page( static::SETTINGS_KEY, 'edit.php?post_type=nb_newsletter', __( 'Settings', 'newsletter-builder' ), __( 'Settings', 'newsletter-builder' ) );
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
					'api_key'            => new \Fieldmanager_TextField( __( 'API Key', 'newsletter-builder' ) ),
					'client_id'          => new \Fieldmanager_TextField( __( 'Client ID', 'newsletter-builder' ) ),
					'confirmation_email' => new \Fieldmanager_TextField( __( 'Confirmation Email', 'newsletter-builder' ) ),
					'google_api_key'     => new \Fieldmanager_TextField( __( 'Google API Key', 'newsletter-builder' ) ),
					'from_email'         => new \Fieldmanager_TextField( __( 'From Email', 'newsletter-builder' ) ),
					'reply_to_email'     => new \Fieldmanager_TextField( __( 'Reply To Email', 'newsletter-builder' ) ),
					'from_names'         => new \Fieldmanager_TextField(
						[
							'label'              => __( 'From Names', 'newsletter-builder' ),
							'limit'              => 0,
							'add_more_label'     => __( 'Add From Name', 'newsletter-builder' ),
							'one_label_per_item' => false,
						]
					),
					'dev_settings'       => new \Fieldmanager_Group(
						[
							'label'       => __( 'Development Settings', 'newsletter-builder' ),
							'collapsed'   => true,
							'collapsible' => true,
							'children'    => [
								'static_preview_url'  => new \Fieldmanager_Link(
									[
										'label'       => __( 'Static Preview URL', 'newsletter-builder' ),
										'description' => __( 'For local development, provide an internet accessible file to use for the newsletter content.', 'newsletter-builder' ),
									]
								),
								'basic_auth_username' => new \Fieldmanager_TextField(
									[
										'label'       => __( 'Basic Auth Username', 'newsletter-builder' ),
										'description' => __( 'For protected staging sites, provide a username for basic auth.', 'newsletter-builder' ),
									]
								),
								'basic_auth_password' => new \Fieldmanager_TextField(
									[
										'label'       => __( 'Basic Auth Password', 'newsletter-builder' ),
										'description' => __( 'For protected staging sites, provide a password for basic auth.', 'newsletter-builder' ),
									]
								),
							],
						]
					),
					'footer_settings'    => new \Fieldmanager_Group(
						[
							'label'       => __( 'Footer Settings', 'newsletter-builder' ),
							'collapsed'   => true,
							'collapsible' => true,
							'children'    => [
								'facebook_url'  => new \Fieldmanager_Link(
									[
										'label' => __( 'Facebook URL', 'newsletter-builder' ),
									]
								),
								'twitter_url'   => new \Fieldmanager_Link(
									[
										'label' => __( 'Twitter URL', 'newsletter-builder' ),
									]
								),
								'instagram_url' => new \Fieldmanager_Link(
									[
										'label' => __( 'Instagram URL', 'newsletter-builder' ),
									]
								),
								'youtube_url'   => new \Fieldmanager_Link(
									[
										'label' => __( 'YouTube URL', 'newsletter-builder' ),
									]
								),
								'image'         => new \Fieldmanager_Media(
									[
										'label'        => __( 'Footer Image', 'newsletter-builder' ),
										'preview_size' => 'medium',
									]
								),
								'address'       => new \Fieldmanager_TextField(
									[
										'label' => __( 'Company Address', 'newsletter-builder' ),
									]
								),
							],
						]
					),
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
	 * Gets footer settings.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return array|false
	 */
	public function get_footer_settings() {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['footer_settings'] ) ) {
			return false;
		}

		return $settings['footer_settings'];
	}

	/**
	 * Gets From Names.
	 *
	 * @return array|false
	 */
	public function get_from_names() {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['from_names'] ) ) {
			return false;
		}

		return $settings['from_names'];
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
		$url = apply_filters( 'newsletter_builder_html_url', $url );

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
