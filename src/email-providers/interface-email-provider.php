<?php
/**
 * Interface for email provider.
 *
 * @package wp-newsletter-builder
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
	public function setup(): void;

	/**
	 * Registers the submenu settings page for the Email Provider options.
	 *
	 * @return void
	 */
	public function maybe_register_settings_page(): void;

	/**
	 * Registers the fields on the settings page for the Email Provider.
	 *
	 * @return void
	 */
	public function register_fields(): void;

	/**
	 * Get the API key and instantiate a client using the API key.
	 *
	 * @return mixed
	 */
	public function get_client(): mixed;

	/**
	 * Gets the lists for the client.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return mixed
	 */
	public function get_lists(): mixed;

	/**
	 * Creates an email campaign.
	 *
	 * @param int           $newsletter_id The id of the nb_newsletter post.
	 * @param array<string> $list_ids    The list ids to send the campaign to.
	 * @param string        $campaign_id Optional campaign id to update.
	 * @param string        $from_name   The from name.
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function create_campaign( int $newsletter_id, array $list_ids, string $campaign_id = null, string $from_name ): array|false;

	/**
	 * Sends a campaign.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function send_campaign( string $campaign_id ): array|false;

	/**
	 * Gets campaign summary.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function get_campaign_summary( string $campaign_id ): array|false;

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
	public function campaign_created_successfully( array|false $result ): bool;

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
	public function get_campaign_id_from_create_result( array|false $result ): mixed;

	/**
	 * Add subscriber to list
	 *
	 * @param string                       $list_id The list id.
	 * @param string                       $email The email address.
	 * @param array<array<string, string>> $custom_fields The custom fields.
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function add_subscriber( string $list_id, string $email, array $custom_fields = [] ): array|false;
	/**
	 * Remove subscriber from list.
	 *
	 * @param string $list_id The list id.
	 * @param string $email The email address.
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function remove_subscriber( string $list_id, string $email ): array|false;

	/**
	 * Whether the provider manages from names or if the plugin should handle it.
	 *
	 * @return boolean
	 */
	public function provider_manages_from_names(): bool;

	/**
	 * Whether or not the provider uses suppression lists.
	 *
	 * @return boolean
	 */
	public function uses_suppression_lists(): bool;

	/**
	 * Gets the suppression lists.
	 *
	 * @return mixed
	 */
	public function get_suppression_lists(): mixed;
}
