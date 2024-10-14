<?php
/**
 * Email_Types class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Email Types class
 */
class Email_Types {
	/**
	 * Settings key.
	 *
	 * @var string
	 */
	public const SETTINGS_KEY = 'nb_email_types';

	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function __construct() {
		// add_action( 'init', [ $this, 'maybe_register_settings_page' ] );
		// add_filter( 'pre_update_option_' . static::SETTINGS_KEY, [ $this, 'sort_settings_on_save' ], 10, 1 );

		add_action( 'admin_init', [ $this, 'register_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_menu', [ $this, 'register_submenu_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'rest_api_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Register scripts for the settings page.
	 */
	public function register_scripts() {
		wp_register_script(
			'wp-newsletter-builder-admin-email-types',
			get_entry_asset_url( 'wp-newsletter-builder-admin-email-types' ),
			array_merge( get_asset_dependency_array( 'wp-newsletter-builder-admin-email-types' ), [ 'wp-editor' ] ),
			get_asset_version( 'wp-newsletter-builder-admin-email-types' ),
			true
		);
		wp_set_script_translations( 'wp-newsletter-builder-admin-email-types' );
	}

	/**
	 * Enqueue scripts and styles for the settings page.
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'wp-newsletter-builder-admin-email-types' );

		// Enqueue styles for the settings page.
		wp_enqueue_style(
			'wp-newsletter-builder-admin-email-types',
			get_entry_asset_url( 'wp-newsletter-builder-admin-email-types', 'index.css' ),
			[],
			get_asset_version( 'wp-newsletter-builder-admin-email-types' ),
		);

		// Enqueue styles for all settings pages.
		wp_enqueue_style(
			'wp-newsletter-builder-admin-settings',
			get_entry_asset_url( 'admin-settings', 'index.css' ),
			get_asset_dependency_array( 'admin-settings' ),
			get_asset_version( 'admin-settings' ),
		);

		// Core component styles.
		wp_enqueue_style( 'wp-components' );

		// Media functionality for Media Library button.
		wp_enqueue_media();
	}

	/**
	 * Register the settings submenu page.
	 */
	public function register_submenu_page(): void {
		add_submenu_page(
			'edit.php?post_type=nb_newsletter',
			__( 'Email Types', 'wp-newsletter-builder' ),
			__( 'Email Types', 'wp-newsletter-builder' ),
			'manage_options',
			'email-types',
			[ $this, 'options_menu_callback' ],
		);
	}

	/**
	 * Callback function for add_submenu_page. Renders react entrypoint.
	 */
	public function options_menu_callback(): void {
		echo '<div id="wp-newsletter-builder-settings__page-email-types"></div>';
	}

	public function maybe_register_settings_page(): void {
		if ( function_exists( 'fm_register_submenu_page' ) && \current_user_can( 'manage_options' ) ) {
		\fm_register_submenu_page( static::SETTINGS_KEY, 'edit.php?post_type=nb_newsletter', __( 'Email Types', 'wp-newsletter-builder' ), __( 'Email Types', 'wp-newsletter-builder' ) );
		\add_action( 'fm_submenu_' . static::SETTINGS_KEY, [ $this, 'register_fields' ] );
		}
	}

	/**
	 * Register the settings for the page.
	 */
	public function register_settings(): void {
		register_setting(
			'options',
			static::SETTINGS_KEY,
			[
				'type'         => 'object',
				'show_in_rest' => [
					'schema' => [
						'type'       => 'array',
						'properties' => [
							'uuid4'     => [
								'type' => 'string',
							],
							'label'     => [
								'type' => 'string',
							],
							'image'     => [
								'type' => 'number',
							],
							'templates' => [
								'type' => 'object',
								'properties' => [
									'id' => [
										'type' => 'number',
									],
									'label' => [
										'type' => 'string',
									],
								],
							],
							'from_name' => [
								'type' => 'string',
							],
						],
					],
				],
			],
		);
	}

	/**
	 * Registers the fields on the settings page for the Campaign Monitor options.
	 *
	 * @return void
	 */
	public function register_fields(): void {
		$plugin_settings = new Settings();
		$from_names      = $plugin_settings->get_from_names();
		$settings        = new \Fieldmanager_Group(
			[
				'name'           => static::SETTINGS_KEY,
				'children'       => [
					'uuid4'     => new class() extends \Fieldmanager_Hidden {
						/**
						 * Ensure that each group has a unique ID.
						 *
						 * @param mixed         $value          Submitted value.
						 * @param array<string> $current_value  The current values.
						 * @return array<string>|string The sanitized values.
						 */
						public function presave( $value, $current_value = [] ) {
							return $current_value ?: wp_generate_uuid4();
						}
					},
					'label'     => new \Fieldmanager_TextField( __( 'Label', 'wp-newsletter-builder' ) ),
					'image'     => new \Fieldmanager_Media(
						[
							'label'        => __( 'Image', 'wp-newsletter-builder' ),
							'preview_size' => 'full',
						]
					),
					'templates' => new \Fieldmanager_Checkboxes(
						'Templates',
						[
							'datasource' => new \Fieldmanager_Datasource_Post(
								[
									'query_args' => [
										'post_type'      => 'nb_template',
										'posts_per_page' => -1,
										'orderby'        => 'title',
									],
									'use_ajax'   => false,
								]
							),
						]
					),
					'from_name' => new \Fieldmanager_Select(
						__( 'From Name', 'wp-newsletter-builder' ),
						[
							'options' => $from_names,
						]
					),
				],
				'limit'          => 0,
				'add_more_label' => __( 'Add Another Email Type', 'wp-newsletter-builder' ),
				'collapsible'    => true,
				'collapsed'      => true,
				'label'          => __( 'New Email Type', 'wp-newsletter-builder' ),
				'label_macro'    => [
					/* translators: %s is the label for the email type. */
					__( 'Email Type: %s', 'wp-newsletter-builder' ),
					'label',
				],
				// We need to specify this condition since there will always be a UUID in the group.
				'group_is_empty' => fn ( $values ) => empty( $values['label'] ) && empty( $values['template'] ),
			]
		);

		$settings->activate_submenu_page();
	}

	/**
	 * Sort the email types on save so that they are always alphabetical.
	 *
	 * @param array<array{uuid4: string, label: string, image: int, templates: array<int>, from_name: string, safe_rtb?: string, ad_tags?: array<array{tag_code: string}>, roadblock?: bool, key_values?: array<array{key: string, value: string}>}> $new_value The value being saved.
	 * @return array<array{uuid4: string, label: string, image: int, templates: array<int>, from_name: string, safe_rtb?: string, ad_tags?: array<array{tag_code: string}>, roadblock?: bool, key_values?: array<array{key: string, value: string}>}>
	 */
	public function sort_settings_on_save( array $new_value ): array {
		usort(
			$new_value,
			function ( $a, $b ) {
				return strcasecmp( $a['label'], $b['label'] );
			}
		);

		return $new_value;
	}

	/**
	 * Gets the lists for the client.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return array<array{uuid4: string, label: string, image: int, templates: array<int>, from_name: string, safe_rtb?: string, ad_tags?: array<array{tag_code: string}>, roadblock?: bool, key_values?: array<array{key: string, value: string}>}>
	 */
	public function get_email_types(): array {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return [];
		}

		return $settings;
	}
}
