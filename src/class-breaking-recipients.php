<?php
/**
 * Breaking_Recipients class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Breaking Recipients class
 */
class Breaking_Recipients {
	/**
	 * Settings key.
	 *
	 * @var string
	 */
	public const SETTINGS_KEY = 'nb_breaking_recipients';

	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'maybe_register_settings_page' ] );
	}

	/**
	 * Registers the submenu settings page for the Breaking Recipients.
	 *
	 * @return void
	 */
	public function maybe_register_settings_page(): void {
		if ( function_exists( 'fm_register_submenu_page' ) && \current_user_can( 'manage_options' ) ) {
			\fm_register_submenu_page( static::SETTINGS_KEY, 'edit.php?post_type=nb_newsletter', __( 'Breaking Recipients', 'wp-newsletter-builder' ), __( 'Breaking Recipients', 'wp-newsletter-builder' ) );
			\add_action( 'fm_submenu_' . static::SETTINGS_KEY, [ $this, 'register_fields' ] );
		}
	}

	/**
	 * Registers the fields on the settings page for the Breaking Recipients options.
	 *
	 * @return void
	 */
	public function register_fields(): void {
		$settings = new \Fieldmanager_Group(
			[
				'name'           => static::SETTINGS_KEY,
				'label'          => __( 'List', 'wp-newsletter-builder' ),
				'children'       => [
					'list' => new \Fieldmanager_Autocomplete(
						[
							'datasource' => new \Fieldmanager_Datasource(
								[
									'options' => $this->get_options(),
								]
							),
						]
					),
				],
				'limit'          => 0,
				'add_more_label' => __( 'Add Another List', 'wp-newsletter-builder' ),
				'sortable'       => true,
			]
		);

		$settings->activate_submenu_page();
	}

	/**
	 * Gets the Breaking News Recipient lists.
	 *
	 * @return array<int|string, string>|false
	 */
	public function get_breaking_recipients(): array|false {
		$options  = $this->get_options();
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return false;
		}
		$lists = [];
		foreach ( $settings as $recipient_list ) {
			if ( ! is_array( $recipient_list ) || ! isset( $recipient_list['list'] ) ) {
				continue;
			}
			$list_id = $recipient_list['list'];
			if ( ! is_string( $list_id ) && ! is_int( $list_id ) ) {
				continue;
			}
			$lists[ $list_id ] = $options[ $list_id ] ?? '';
		}
		return $lists;
	}

	/**
	 * Gets the options for the Breaking News Recipient lists.
	 *
	 * @return array<int|string, string> List names keyed by list ID.
	 */
	private function get_options(): array {
		global $newsletter_builder_email_provider;
		$options = [];
		if ( empty( $newsletter_builder_email_provider ) || ! $newsletter_builder_email_provider instanceof Email_Providers\Email_Provider ) {
			return $options;
		}
		$lists = $newsletter_builder_email_provider->get_lists();
		if ( empty( $lists ) || ! is_array( $lists ) ) {
			return $options;
		}
		foreach ( $lists as $list ) {
			if ( is_object( $list ) ) {
				$list_id   = $list->ListID ?? null; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$list_name = $list->Name ?? null; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			} elseif ( is_array( $list ) ) {
				$list_id   = $list['ListID'] ?? null;
				$list_name = $list['Name'] ?? null;
			} else {
				continue;
			}
			if ( ( is_string( $list_id ) || is_int( $list_id ) ) && is_string( $list_name ) ) {
				$options[ $list_id ] = $list_name;
			}
		}
		return $options;
	}
}
