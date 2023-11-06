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
		add_action( 'init', [ $this, 'maybe_register_settings_page' ] );
	}

	/**
	 * Registers the submenu settings page for Newsletter Builder.
	 *
	 * @return void
	 */
	public function maybe_register_settings_page() {
		if ( function_exists( 'fm_register_submenu_page' ) && \current_user_can( 'manage_options' ) ) {
			\fm_register_submenu_page( static::SETTINGS_KEY, 'edit.php?post_type=nb_newsletter', __( 'Settings', 'wp-newsletter-builder' ), __( 'Settings', 'wp-newsletter-builder' ) );
			\add_action( 'fm_submenu_' . static::SETTINGS_KEY, [ $this, 'register_fields' ] );
		}
	}

	/**
	 * Registers the fields on the settings page for Newsletter Builder.
	 *
	 * @return void
	 */
	public function register_fields() {
		$settings = new \Fieldmanager_Group(
			[
				'name'     => static::SETTINGS_KEY,
				'children' => [
					'from_email'      => new \Fieldmanager_TextField( __( 'From Email', 'wp-newsletter-builder' ) ),
					'reply_to_email'  => new \Fieldmanager_TextField( __( 'Reply To Email', 'wp-newsletter-builder' ) ),
					'from_names'      => new \Fieldmanager_TextField(
						[
							'label'              => __( 'From Names', 'wp-newsletter-builder' ),
							'limit'              => 0,
							'add_more_label'     => __( 'Add From Name', 'wp-newsletter-builder' ),
							'one_label_per_item' => false,
						]
					),
					'footer_settings' => new \Fieldmanager_Group(
						[
							'label'       => __( 'Footer Settings', 'wp-newsletter-builder' ),
							'collapsed'   => true,
							'collapsible' => true,
							'children'    => [
								'facebook_url'  => new \Fieldmanager_Link(
									[
										'label' => __( 'Facebook URL', 'wp-newsletter-builder' ),
									]
								),
								'twitter_url'   => new \Fieldmanager_Link(
									[
										'label' => __( 'Twitter URL', 'wp-newsletter-builder' ),
									]
								),
								'instagram_url' => new \Fieldmanager_Link(
									[
										'label' => __( 'Instagram URL', 'wp-newsletter-builder' ),
									]
								),
								'youtube_url'   => new \Fieldmanager_Link(
									[
										'label' => __( 'YouTube URL', 'wp-newsletter-builder' ),
									]
								),
								'image'         => new \Fieldmanager_Media(
									[
										'label'        => __( 'Footer Image', 'wp-newsletter-builder' ),
										'preview_size' => 'medium',
									]
								),
								'address'       => new \Fieldmanager_TextField(
									[
										'label' => __( 'Company Address', 'wp-newsletter-builder' ),
									]
								),
							],
						]
					),
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
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['api_key'] ) ) {
			return false;
		}
		$auth = [ 'api_key' => $settings['api_key'] ];
		$wrap = new \CS_REST_General( $auth );

		return $wrap;
	}

	/**
	 * Gets the lists for the client.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return array|false
	 */
	public function get_lists() {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['api_key'] ) || empty( $settings['client_id'] ) ) {
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
	 * Gets footer settings.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return array|false
	 */
	public function get_footer_settings() {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['footer_settings'] ) ) {
			return false;
		}

		return $settings['footer_settings'];
	}

	/**
	 * Gets From Names.
	 *
	 * @return array|false
	 */
	public function get_from_names() {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || empty( $settings['from_names'] ) ) {
			return false;
		}

		return $settings['from_names'];
	}
}
