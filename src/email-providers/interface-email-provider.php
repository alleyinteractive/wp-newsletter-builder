<?php
/**
 * Interface for email provider.
 *
 * @package WP_Newsletter_Builder
 */

namespace WP_Newsletter_Builder\Email_Providers;

/**
 * Interface Email_Provider
 */
interface Email_Provider {
	/**
	 * Settings key.
	 *
	 * @var string
	 */
	public const SETTINGS_KEY = '';

	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function setup();

	/**
	 * Registers the submenu settings page for the Email Provider options.
	 */
	public function maybe_register_settings_page();

	/**
	 * Registers the fields on the settings page for the Email Provider.
	 *
	 * @return void
	 */
	public function register_fields();

	/**
	 * Get the API key and instantiate a client using the API key.
	 *
	 * @return \CS_REST_General
	 */
	public function get_client();

	/**
	 * Gets the lists for the client.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return array{
	 *   ListID: string,
	 *   Name: string,
	 * }|false
	 */
	public function get_lists();

	/**
	 * Creates an email campaign.
	 *
	 * @param int    $newsletter_id The id of the nb_newsletter post.
	 * @param array  $list_ids    The list ids to send the campaign to.
	 * @param string $campaign_id Optional campaign id to update.
	 * @return array The response from the API.
	 */
	public function create_campaign( $newsletter_id, $list_ids, $campaign_id = null );

	/**
	 * Sends a campaign.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array|false The response from the API.
	 */
	public function send_campaign( $campaign_id );

	/**
	 * Gets campaign summary.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array|false The response from the API.
	 */
	public function get_campaign_summary( $campaign_id );

	/**
	 * Add subscriber to list
	 *
	 * @param string $list_id The list id.
	 * @param string $email The email address.
	 * @param array  $custom_fields The custom fields.
	 * @return array|false
	 */
	public function add_subscriber( $list_id, $email, $custom_fields = [] );
	/**
	 * Remove subscriber from list.
	 *
	 * @param string $list_id The list id.
	 * @param string $email The email address.
	 * @return array|false
	 */
	public function remove_subscriber( $list_id, $email );
}
