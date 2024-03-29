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
	public function maybe_register_settings_page() {
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
	public function register_fields() {
		$settings = new \Fieldmanager_Group(
			// @phpstan-ignore-next-line the Fieldmanager doc block is incorrect.
			[
				'name'           => static::SETTINGS_KEY,
				'label'          => __( 'List', 'wp-newsletter-builder' ),
				'children'       => [
					'list' => new \Fieldmanager_Autocomplete(
						// @phpstan-ignore-next-line the Fieldmanager doc block is incorrect.
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
	 * @return array
	 */
	public function get_breaking_recipients() {
		$options  = $this->get_options();
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) ) {
			return false;
		}
		$lists = [];
		foreach ( $settings as $recipient_list ) {
			$lists[ $recipient_list['list'] ] = $options[ $recipient_list['list'] ];
		}
		return $lists;
	}

	/**
	 * Gets the options for the Breaking News Recipient lists.
	 *
	 * @return array
	 */
	private function get_options() {
		global $newsletter_builder_email_provider;
		$lists   = $newsletter_builder_email_provider->get_lists();
		$options = [];
		foreach ( $lists as $list ) {
			$options[ $list->ListID ] = $list->Name; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		return $options;
	}
}
