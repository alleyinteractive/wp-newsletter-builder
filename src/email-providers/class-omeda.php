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
	 * Sets things up.
	 *
	 * @return void
	 */
	public function setup() {
		$config = [
			'license_key' => '',
			'user'        => '',
			'app_id'      => '',
			'brand'       => '',
			'client_abbr' => '',
			'from_name'   => '',
			'input_id'    => '',
			'mailbox'     => '',
			'namespace'   => '',
			'reply_to'    => '',
		];
		$client = new Omeda_Client( $config );
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
					'license_key'            => new \Fieldmanager_TextField( __( 'License Key', 'wp-newsletter-builder' ) ),
					'user'            => new \Fieldmanager_TextField( __( 'User', 'wp-newsletter-builder' ) ),
					'app_id'        => new \Fieldmanager_TextField( __( 'App ID', 'wp-newsletter-builder' ) ),
					'brand' 	   => new \Fieldmanager_TextField( __( 'Brand', 'wp-newsletter-builder' ) ),
					'client_abbrev'      => new \Fieldmanager_TextField( __( 'Client Abbreviation', 'wp-newsletter-builder' ) ),
					'from_name'   => new \Fieldmanager_TextField( __( 'From Name', 'wp-newsletter-builder' ) ),
					'input_id'    => new \Fieldmanager_TextField( __( 'Input ID', 'wp-newsletter-builder' ) ),
					'mailbox'     => new \Fieldmanager_TextField( __( 'Mailbox', 'wp-newsletter-builder' ) ),
					'namespace'   => new \Fieldmanager_TextField( __( 'Namespace', 'wp-newsletter-builder' ) ),
					'reply_to'    => new \Fieldmanager_TextField( __( 'Reply To', 'wp-newsletter-builder' ) ),
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
		// TODO.
	}

	/**
	 * Gets the lists for the client.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return array|false
	 */
	public function get_lists() {
		// TODO.
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
		// TODO.
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
}
// Remove once implemented.
// phpcs:enable Squiz.Commenting.FunctionComment.InvalidNoReturn
