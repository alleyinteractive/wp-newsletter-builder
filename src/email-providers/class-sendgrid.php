<?php
/**
 * WP_Newsletter_Builder class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder\Email_Providers;

use SendGrid\Mail\Mail;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Sendgrid Client class
 */
class Sendgrid implements Email_Provider {
	/**
	 * Settings key.
	 *
	 * @var string
	 */
	public const SETTINGS_KEY = 'nb_sendgrid_settings';

	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function setup(): void {
		add_action( 'init', [ $this, 'maybe_register_settings_page' ] );
		add_filter( 'wp_newsletter_builder_from_names', [ $this, 'filter_from_names' ] );
	}

	/**
	 * Registers the submenu settings page for the Sendgrid options.
	 *
	 * @return void
	 */
	public function maybe_register_settings_page(): void {
		if ( function_exists( 'fm_register_submenu_page' ) && \current_user_can( 'manage_options' ) ) {
			\fm_register_submenu_page( static::SETTINGS_KEY, 'edit.php?post_type=nb_newsletter', __( 'Sendgrid Settings', 'wp-newsletter-builder' ), __( 'Sendgrid Settings', 'wp-newsletter-builder' ) );
			\add_action( 'fm_submenu_' . static::SETTINGS_KEY, [ $this, 'register_fields' ] );
		}
	}

	/**
	 * Registers the fields on the settings page for the Sendgrid options.
	 *
	 * @return void
	 */
	public function register_fields(): void {
		$settings = new \Fieldmanager_Group(
			[
				'name'     => static::SETTINGS_KEY,
				'children' => [
					'api_key' => new \Fieldmanager_Password( __( 'API Key', 'wp-newsletter-builder' ) ),
				],
			]
		);

		$settings->activate_submenu_page();
	}

	/**
	 * Get the API key and instantiate a client using the API key.
	 *
	 * @return \SendGrid|false
	 */
	public function get_client(): \SendGrid|false {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || ! is_array( $settings ) || empty( $settings['api_key'] ) ) {
			return false;
		}
		$sendgrid = new \SendGrid( $settings['api_key'] );
		return $sendgrid;
	}

	/**
	 * Gets the lists for the client.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return mixed
	 */
	public function get_lists(): mixed {
		$sg = $this->get_client();
		if ( empty( $sg ) ) {
			return [];
		}
		$query_params = json_decode(
			'{
			"page_size": 100
		}'
		);

		try {
			$response = $sg->client->marketing()->lists()->get( null, $query_params );
			$body     = (object) json_decode( $response->body() );
			$result   = $body->result;
		} catch ( \Exception $ex ) {
			$result = [];
		}
		if ( ! is_array( $result ) ) {
			return [];
		}
		$lists = [];
		foreach ( $result as $list ) {
			$lists[] = [
				'ListID' => $list->id,
				'Name'   => $list->name,
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
	public function create_campaign( int $newsletter_id, array $list_ids, string $campaign_id = null, string $from_name ): array|false {
		$sendgrid = $this->get_client();
		if ( empty( $sendgrid ) ) {
			return false;
		}
		$response = $sendgrid->client->marketing()->senders()->get();

		$body = json_decode( $response->body() );

		if ( ! is_array( $body ) ) {
			return false;
		}
		foreach ( $body as $sender ) {
			if ( sprintf( '%s (%s)', $sender->from->name, $sender->from->email ) === $from_name ) {
				$sender_id = $sender->id;
				break;
			}
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

		/**
		 * Since CSSToInlineStyles strips out {{}} tags, we are settings up placeholders that
		 * can be replaced after the conversion.
		 *
		 * @link https://github.com/tijsverkoyen/CssToInlineStyles/issues/163
		 */
		if ( str_contains( $html_content, 'href="#unsubscribe"' ) ) {
			$html_content = str_replace( 'href="#unsubscribe"', 'href="{{unsubscribe}}"', $html_content );
		}

		if ( str_contains( $html_content, 'href="#unsubscribe_preferences"' ) ) {
			$html_content = str_replace( 'href="#unsubscribe_preferences"', 'href="{{unsubscribe_preferences}}"', $html_content );
		}

		$text_content = wp_strip_all_tags( $html_content );
		$subject      = get_post_meta( $newsletter_id, 'nb_newsletter_subject', true );

		$request_body                                       = (object) [];
		$request_body->name                                 = get_the_title( $newsletter_id );
		$request_body->categories                           = [];
		$request_body->email_config                         = (object) [];
		$request_body->send_to                              = (object) [];
		$request_body->email_config->html_content           = $html_content;
		$request_body->email_config->ip_pool                = null;
		$request_body->email_config->plain_content          = $text_content;
		$request_body->email_config->generate_plain_content = true;
		$request_body->email_config->sender_id              = $sender_id ?? 0;
		$request_body->email_config->subject                = $subject;

		// TODO: A suppression group id or a custom unsubscribe url should be options.
		$request_body->email_config->custom_unsubscribe_url = home_url() . '/account';

		$request_body->send_to->list_ids    = $list_ids;
		$request_body->send_to->segment_ids = [];

		$sg = $this->get_client();
		if ( empty( $sg ) ) {
			return false;
		}
		$response = $sg->client->marketing()->singlesends()->post( $request_body );

		return [
			'response'         => $response->body(),
			'http_status_code' => $response->statusCode(),
		];
	}

	/**
	 * Sends a campaign.
	 *
	 * @param string $campaign_id The campaign id.
	 * @return array{
	 *   response: mixed,
	 *   http_status_code: int,
	 * }|false  The response from the API.
	 */
	public function send_campaign( string $campaign_id ): array|false {
		$sg = $this->get_client();
		if ( empty( $sg ) ) {
			return false;
		}

		try {
			// @phpstan-ignore-next-line
			$response = $sg->client->marketing()->singlesends()->_( $campaign_id )->schedule()->put( [ 'send_at' => 'now' ] );
			return [
				'response'         => $response->body(),
				'http_status_code' => $response->statusCode(),
			];
		} catch ( \Exception $ex ) {
			return false;
		}
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
		$sg = $this->get_client();
		if ( empty( $sg ) ) {
			return false;
		}
		$campaign_response = $sg->client->marketing()->singlesends()->_( $campaign_id )->get();
		$stats_response    = $sg->client->marketing()->stats()->singlesends()->_( $campaign_id )->get();

		if ( 200 !== $stats_response->statusCode() || 200 !== $campaign_response->statusCode() ) {
			return false;
		}

		$campaign_body = (object) json_decode( $campaign_response->body() );
		$stats_body    = (object) json_decode( $stats_response->body() );

		return [
			'response' => [
				'Status'       => $campaign_body?->status ?? '',
				'Name'         => $campaign_body?->name ?? '',
				'Recipients'   => $stats_body->results[0]?->stats?->delivered ?? '',
				'TotalOpened'  => $stats_body->results[0]?->stats?->opens ?? '',
				'UniqueOpened' => $stats_body->results[0]?->stats?->unique_opens ?? '',
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
		return ! empty( $result['http_status_code'] ) ? 201 === $result['http_status_code'] : false;
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
		if ( ! empty( $response && is_string( $response ) ) ) {
			$body = (object) json_decode( $response );
			return $body->id;
		}
		return false;
	}


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
	public function add_subscriber( string $list_id, string $email, array $custom_fields = [] ): array|false {
		$sendgrid = $this->get_client();
		if ( empty( $sendgrid ) ) {
			return false;
		}
		$request_body           = (object) [];
		$request_body->list_ids = [ $list_id ];
		$user_object            = (object) [];
		$user_object->email     = $email;
		$request_body->contacts = [ $user_object ];
		$response               = $sendgrid->client->marketing()->contacts()->put( $request_body );

		return [
			'response'         => $response->body(),
			'http_status_code' => $response->statusCode(),
		];
	}

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
	public function remove_subscriber( string $list_id, string $email ): array|false {
		// TODO.
		return [
			'response'         => (object) [],
			'http_status_code' => 500,
		];
	}

	/**
	 * Whether the provider manages from names.
	 *
	 * @return boolean
	 */
	public function provider_manages_from_names(): bool {
		return true;
	}

	/**
	 * Get the senders from the Sendgrid API.
	 *
	 * @return array<string>
	 */
	private function get_senders(): array {
		$sendgrid = $this->get_client();
		if ( empty( $sendgrid ) ) {
			return [];
		}
		$response = $sendgrid->client->marketing()->senders()->get();

		$body = json_decode( $response->body() );

		$senders = [];
		if ( ! is_array( $body ) ) {
			return [];
		}
		foreach ( $body as $sender ) {
			$senders[] = sprintf( '%s (%s)', $sender->from->name, $sender->from->email );
		}
		return $senders;
	}

	/**
	 * Filters the from names to use Senders from the API.
	 *
	 * @param array<string> $from_names The existing from names.
	 * @return array<string>
	 */
	public function filter_from_names( array $from_names ): array {
		return $this->get_senders();
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

	/**
	 * Whether or not the provider uses suppression lists.
	 *
	 * @return boolean
	 */
	public function uses_suppression_lists(): bool {
		return true;
	}

	/**
	 * Gets the suppression lists.
	 *
	 * @return mixed
	 */
	public function get_suppression_lists(): mixed {
		$sg = $this->get_client();
		if ( empty( $sg ) ) {
			return [];
		}

		try {
			$response = $sg->client->asm()->groups()->get();
			$result   = json_decode( $response->body() );
		} catch ( \Exception $ex ) {
			$result = [];
		}
		if ( ! is_array( $result ) ) {
			return [];
		}
		$lists = [];
		foreach ( $result as $list ) {
			$lists[] = [
				'ListID' => $list->id,
				'Name'   => $list->name,
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
}
