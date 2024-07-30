<?php
/**
 * Settings class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Settings class
 */
class Settings {
	/**
	 * Settings key.
	 *
	 * @var string
	 */
	public const SETTINGS_KEY = 'nb_settings';

	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function __construct() {
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
			'wp-newsletter-builder-admin-general-settings',
			get_entry_asset_url( 'wp-newsletter-builder-admin-general-settings' ),
			array_merge( get_asset_dependency_array( 'wp-newsletter-builder-admin-general-settings' ), [ 'wp-editor' ] ),
			get_asset_version( 'wp-newsletter-builder-admin-general-settings' ),
			true
		);
		wp_set_script_translations( 'wp-newsletter-builder-admin-general-settings' );
	}

	/**
	 * Enqueue scripts and styles for the settings page.
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'wp-newsletter-builder-admin-general-settings' );

		// Enqueue styles for the settings page.
		wp_enqueue_style(
			'wp-newsletter-builder-admin-general-settings',
			get_entry_asset_url( 'wp-newsletter-builder-admin-general-settings', 'index.css' ),
			[],
			get_asset_version( 'wp-newsletter-builder-admin-general-settings' ),
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
			__( 'General Settings', 'wp-newsletter-builder' ),
			__( 'General Settings', 'wp-newsletter-builder' ),
			'manage_options',
			'general-settings',
			[ $this, 'options_menu_callback' ],
		);
	}

	/**
	 * Callback function for add_submenu_page. Renders react entrypoint.
	 */
	public function options_menu_callback(): void {
		echo '<div id="wp-newsletter-builder-settings__page"></div>';
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
						'type'       => 'object',
						'properties' => [
							'from_email' => [
								'type'   => 'string',
								'format' => 'email',
							],
							'reply_to_email' => [
								'type'   => 'string',
								'format' => 'email',
							],
							'from_names' => [
								'type'  => 'array',
								'items' => [
									'type' => 'string',
								],
							],
							'facebook_url' => [
								'type'   => 'string',
								'format' => 'uri',
							],
							'twitter_url' => [
								'type'   => 'string',
								'format' => 'uri',
							],
							'instagram_url' => [
								'type'   => 'string',
								'format' => 'uri',
							],
							'youtube_url' => [
								'type'   => 'string',
								'format' => 'uri',
							],
							'image' => [
								'type' => 'number',
							],
							'address' => [
								'type' => 'string',
							],
							'address_2' => [
								'type' => 'string',
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Get the API key and instantiate a client using the API key.
	 *
	 * @return \CS_REST_General|false
	 */
	public function get_client(): \CS_REST_General|false {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || ! is_array( $settings ) || empty( $settings['api_key'] ) ) {
			return false;
		}

		return new \CS_REST_General( [ 'api_key' => $settings['api_key'] ] );
	}

	/**
	 * Gets the lists for the client.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return mixed
	 */
	public function get_lists(): mixed {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || ! is_array( $settings ) || empty( $settings['api_key'] ) || empty( $settings['client_id'] ) ) {
			return false;
		}
		$auth = [ 'api_key' => $settings['api_key'] ];

		$wrap = new \CS_REST_Clients(
			$settings['client_id'],
			$auth
		);

		return $wrap->get_lists()->response;
	}

	/**
	 * Gets settings.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return array{
	 *   from_email?: string,
	 *   reply_to_email?: string,
	 *   from_names?: array<string>,
	 *   facebook_url?: string,
	 *   twitter_url?: string,
	 *   instagram_url?: string,
	 *   youtube_url?: string,
	 *   image?: int,
	 *   address?: string,
	 *   address_2?: string,
	 * }|false  The footer settings.
	 */
	public function get_settings(): array|false {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return false;
		}

		return $settings;
	}

	/**
	 * Gets From Names.
	 *
	 * @return array<string>|false
	 */
	public function get_from_names(): array|false {
		$settings = get_option( static::SETTINGS_KEY, [] );
		if ( empty( $settings ) || ! is_array( $settings ) || empty( $settings['from_names'] ) || ! is_array( $settings['from_names'] ) ) {
			// @phpstan-ignore-next-line
			$settings['from_names'] = [];
		}

		return apply_filters( 'wp_newsletter_builder_from_names', $settings['from_names'] );
	}
}
