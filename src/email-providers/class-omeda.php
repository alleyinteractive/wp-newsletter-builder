<?php
/**
 * Interface for email provider.
 *
 * @package WP_Newsletter_Builder
 */

// Remove once implemented.
// phpcs:disable Squiz.Commenting.FunctionComment.InvalidNoReturn

namespace WP_Newsletter_Builder\Email_Providers;

use \WP_Newsletter_Builder\Omeda_Client;

/**
 * Interface Email_Provider
 */
class Omeda implements Email_Provider {
	/**
	 * Settings key.
	 *
	 * @var string
	 */
	public const SETTINGS_KEY = 'nb_omeda_settings';

	/**
	 * The client.
	 *
	 * @var Omeda_Client
	 */
	private $client;

	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function setup() {
		$settings = get_option( static::SETTINGS_KEY, [] );
		$config = [
			'license_key' => $settings['license_key'] ?? '',
			'user'        => $settings['user'] ?? '',
			'app_id'      => $settings['app_id'] ?? '',
			'brand'       => $settings['brand'] ?? '',
			'client_abbr' => $settings['client_abbrev'] ?? '',
			'from_name'   => $settings['from_name'] ?? '',
			'input_id'    => $settings['input_id'] ?? '',
			'mailbox'     => $settings['mailbox'] ?? '',
			'namespace'   => $settings['namespace'] ?? '',
			'reply_to'    => $settings['reply_to'] ?? '',
			'use_staging' => $settings['use_staging'] ?? false,
		];
		$client = new Omeda_Client( $config );
		$this->client = $client;
		add_action( 'init', [ $this, 'maybe_register_settings_page' ] );
	}

	/**
	 * Registers the submenu settings page for the Email Provider options.
	 */
	public function maybe_register_settings_page() {
		if ( function_exists( 'fm_register_submenu_page' ) && \current_user_can( 'manage_options' ) ) {
			\fm_register_submenu_page( static::SETTINGS_KEY, 'edit.php?post_type=nb_newsletter', __( 'Omeda Settings', 'wp-newsletter-builder' ), __( 'Omeda Settings', 'wp-newsletter-builder' ) );
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
					'license_key'   => new \Fieldmanager_TextField( __( 'License Key', 'wp-newsletter-builder' ) ),
					'user'          => new \Fieldmanager_TextField( __( 'User', 'wp-newsletter-builder' ) ),
					'app_id'        => new \Fieldmanager_TextField( __( 'App ID', 'wp-newsletter-builder' ) ),
					'brand'         => new \Fieldmanager_TextField( __( 'Brand', 'wp-newsletter-builder' ) ),
					'client_abbrev' => new \Fieldmanager_TextField( __( 'Client Abbreviation', 'wp-newsletter-builder' ) ),
					'from_name'     => new \Fieldmanager_TextField( __( 'From Name', 'wp-newsletter-builder' ) ),
					'input_id'      => new \Fieldmanager_TextField( __( 'Input ID', 'wp-newsletter-builder' ) ),
					'mailbox'       => new \Fieldmanager_TextField( __( 'Mailbox', 'wp-newsletter-builder' ) ),
					'namespace'     => new \Fieldmanager_TextField( __( 'Namespace', 'wp-newsletter-builder' ) ),
					'reply_to'      => new \Fieldmanager_TextField( __( 'Reply To', 'wp-newsletter-builder' ) ),
					'use_staging'   => new \Fieldmanager_Checkbox( __( 'Use Staging', 'wp-newsletter-builder' ) ),
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
		return $this->client;
	}

	/**
	 * Gets the lists for the client.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @see https://training.omeda.com/knowledge-base/deployment-type-lookup-by-brand-api/
	 *
	 * @return array{
	 *   ListID: string,
	 *   Name: string,
	 * }|false
	 */
	public function get_lists() {
		$response = $this->client->call( 'deploymenttypes', null, 'brand', 'GET' );
		$lists = array_map(
			function( $list ) {
				return [
					'ListID' => (string) $list['Id'],
					'Name'   => $list['Name'],
				];
			},
			$response['DeploymentTypes']
		);
		// Sort lists by Name.
		usort(
			$lists,
			function( $a, $b ) {
				return strcmp( $a['Name'], $b['Name'] );
			}
		);
		return $lists;
	}

	/**
	 * Creates an email campaign.
	 *
	 * @see https://training.omeda.com/knowledge-base/api-email-deployment-service/
	 * @see https://training.omeda.com/knowledge-base/email-deployment-content/
	 *
	 * @param int    $newsletter_id The id of the nb_newsletter post.
	 * @param array  $list_ids    The list ids to send the campaign to.
	 * @param string $campaign_id Optional campaign id to update.
	 * @return array The response from the API.
	 */
	public function create_campaign( $newsletter_id, $list_ids, $campaign_id = null ) {
		// TODO: Move non-email provider code to the core plugin.
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
			'DeploymentName'   => sprintf( '%s - Post %d - %s', $newsletter->post_title, $newsletter->ID, get_post_modified_time( 'Y-m-d H:i:s', false, $newsletter->ID ) ),
			'DeploymentTypeId' => intval( $list_ids[0] ), // array not allowed - how do we handle properly?
			'DeploymentDate'   => date( 'Y-m-d H:i', strtotime( '+1 minute' ) ), // Must be in the future. 'yyyy-MM-dd HH:mm' format.
			'OwnerUserId'      => 'nalley', // Need to figure this out.
			'Splits'           => 1,
			'TrackOpens'       => 1,
			'TrackLinks'       => 1,
			'Testers'          => [
				[
					'FirstName' => 'Greg',
					'LastName'  => 'Marshall',
					'EmailAddress' => 'greg@alley.com'
				]
			]
		];

		$response = $this->client->call( 'omail/deployment', $params, 'brand', 'POST' );

		// get the trackId.
		$track_id = $response['TrackId'];
		// Get the content from $url.
		$html_content = file_get_contents( $url );

		$settings = get_option( static::SETTINGS_KEY, [] );

		$content_params = [
			'UserId'      => 'nalley', // Need to figure this out.'
			'TrackId'     => $track_id,
			'Subject'     => get_post_meta( $newsletter->ID, 'nb_newsletter_subject', true ),
			'FromName'    => $nl_from_name,
			'Mailbox'     => $settings['mailbox'],
			'ReplyTo'     => $settings['reply_to'],
			'HtmlContent' => $html_content, // we could also use HtmlContentUrl.
			'Preheader'   => get_post_meta( $newsletter->ID, 'nb_newsletter_preview', true ),
			'SplitNumber' => 1,
		];

		// Convert params to xml.
		$content_xml = new \SimpleXMLElement( '<Deployment />' );
		$this->array_to_xml( $content_params, $content_xml );

		$content_response = $this->client->call( 'omail/deployment/content', $content_xml->asXML(), 'brand', 'POST' );

		if ( is_set( $content_response['TrackId'] ) ) {

		}
die();
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
		// TODO.
	}

	/**
	 * Gets campaign summary.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array|false The response from the API.
	 */
	public function get_campaign_summary( $campaign_id ) {
		// TODO.
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
		// TODO.
	}

	/**
	 * Remove subscriber from list.
	 *
	 * @param string $list_id The list id.
	 * @param string $email The email address.
	 * @return array|false
	 */
	public function remove_subscriber( $list_id, $email ) {
		// TODO.
	}

	/**
	 * Converts an array to an XML string
	 *
	 * @param array $array
	 * @param \Simple_XML $root_element
	 * @return string
	 */
	private function array_to_xml( $array, &$root_element ) {
		foreach ( $array as $key => $value ) {
			// If $value contains html, wrap it in a CDATA tag.
			if ( is_string( $value ) && ( 1 === preg_match( '/[&<>]/', $value ) ) ) {
				$value = sprintf( '<![CDATA[%s]]>', $value );
			}
			if ( is_array( $value ) ) {
				if ( ! is_numeric( $key ) ) {
					$subnode = $root_element->addChild( "$key" );
					$this->array_to_xml( $value, $subnode );
				} else {
					$subnode = $root_element->addChild( "item$key" );
					$this->array_to_xml( $value, $subnode );
				}
			} else {
				$root_element->addChild( "$key", htmlspecialchars( "$value" ) );
			}
		}
	}
}
// Remove once implemented.
// phpcs:enable Squiz.Commenting.FunctionComment.InvalidNoReturn
