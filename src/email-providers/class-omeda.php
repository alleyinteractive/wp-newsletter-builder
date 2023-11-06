<?php
/**
 * Interface for email provider.
 *
 * @package WP_Newsletter_Builder
 */

// Remove once implemented.
// phpcs:disable Squiz.Commenting.FunctionComment.InvalidNoReturn

namespace WP_Newsletter_Builder\Email_Providers;

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
		// TODO.
	}

	/**
	 * Registers the submenu settings page for the Email Provider options.
	 */
	public function maybe_register_settings_page() {
		// TODO.
	}

	/**
	 * Registers the fields on the settings page for the Email Provider.
	 *
	 * @return void
	 */
	public function register_fields() {
		// TODO.
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
