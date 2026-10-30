<?php
/**
 * Null_Provider class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder\Email_Providers;

/**
 * Null email provider.
 *
 * Enables all Newsletter Builder functionality except sending email. Editors can
 * build newsletters with templates, email types and blocks, then copy or download
 * the rendered HTML from the editor to use elsewhere. Nothing is connected to an
 * email service provider: there are no lists, no settings page, and publishing a
 * newsletter never makes an outbound request.
 *
 * Select it with the `wp_newsletter_builder_selected_provider` filter:
 *
 *     add_filter(
 *         'wp_newsletter_builder_selected_provider',
 *         fn() => 'WP_Newsletter_Builder\Email_Providers\Null_Provider'
 *     );
 */
class Null_Provider implements Email_Provider {
	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function setup(): void {}

	/**
	 * There are no settings to register.
	 *
	 * @return void
	 */
	public function maybe_register_settings_page(): void {}

	/**
	 * There are no settings fields to register.
	 *
	 * @return void
	 */
	public function register_fields(): void {}

	/**
	 * There is no client.
	 *
	 * @return null
	 */
	public function get_client(): mixed {
		return null;
	}

	/**
	 * There are no lists.
	 *
	 * @return array{}
	 */
	public function get_lists(): mixed {
		return [];
	}

	/**
	 * Campaigns are never created.
	 *
	 * @param int           $newsletter_id The id of the nb_newsletter post.
	 * @param array<string> $list_ids    The list ids to send the campaign to.
	 * @param string|null   $campaign_id Optional campaign id to update.
	 * @param string        $from_name   The from name.
	 * @return array{response: mixed, http_status_code: int}|false
	 */
	public function create_campaign( int $newsletter_id, array $list_ids, ?string $campaign_id, string $from_name ): array|false {
		return false;
	}

	/**
	 * Campaigns are never sent.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array{response: mixed, http_status_code: int}|false
	 */
	public function send_campaign( string $campaign_id ): array|false {
		return false;
	}

	/**
	 * There are no campaign summaries.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array{response: mixed, success: boolean}|false
	 */
	public function get_campaign_summary( string $campaign_id ): array|false {
		return false;
	}

	/**
	 * Campaigns are never created.
	 *
	 * @param array|false $result The response from the creation request.
	 * @phpstan-param array{response: mixed, http_status_code: int}|false $result
	 * @return bool
	 */
	public function campaign_created_successfully( array|false $result ): bool {
		return false;
	}

	/**
	 * There is no campaign id.
	 *
	 * @param array|false $result The response from the creation request.
	 * @phpstan-param array{response: mixed, http_status_code: int}|false $result
	 * @return null
	 */
	public function get_campaign_id_from_create_result( array|false $result ): mixed {
		return null;
	}

	/**
	 * Subscribers are never added.
	 *
	 * @param string                       $list_id The list id.
	 * @param string                       $email The email address.
	 * @param array<array<string, string>> $custom_fields The custom fields.
	 * @return array{response: mixed, http_status_code: int}|false
	 */
	public function add_subscriber( string $list_id, string $email, array $custom_fields = [] ): array|false {
		return false;
	}

	/**
	 * Subscribers are never removed.
	 *
	 * @param string $list_id The list id.
	 * @param string $email The email address.
	 * @return array{response: mixed, http_status_code: int}|false
	 */
	public function remove_subscriber( string $list_id, string $email ): array|false {
		return false;
	}

	/**
	 * From names are managed by the plugin.
	 *
	 * @return boolean
	 */
	public function provider_manages_from_names(): bool {
		return false;
	}

	/**
	 * There are no suppression lists.
	 *
	 * @return boolean
	 */
	public function uses_suppression_lists(): bool {
		return false;
	}

	/**
	 * There are no suppression lists.
	 *
	 * @return array{}
	 */
	public function get_suppression_lists(): mixed {
		return [];
	}

	/**
	 * This provider never sends email.
	 *
	 * Not part of the Email_Provider interface; see \WP_Newsletter_Builder\provider_sends_email().
	 *
	 * @return boolean
	 */
	public function sends_email(): bool {
		return false;
	}
}
