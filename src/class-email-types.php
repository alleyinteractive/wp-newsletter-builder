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
		add_action( 'init', [ $this, 'maybe_register_settings_page' ] );
		add_filter( 'pre_update_option_' . static::SETTINGS_KEY, [ $this, 'sort_settings_on_save' ], 10, 1 );
	}

	/**
	 * Registers the submenu settings page for the Email Types.
	 *
	 * @return void
	 */
	public function maybe_register_settings_page() {
		if ( function_exists( 'fm_register_submenu_page' ) && \current_user_can( 'manage_options' ) ) {
			\fm_register_submenu_page( static::SETTINGS_KEY, 'edit.php?post_type=nb_newsletter', __( 'Email Types', 'wp-newsletter-builder' ), __( 'Email Types', 'wp-newsletter-builder' ) );
			\add_action( 'fm_submenu_' . static::SETTINGS_KEY, [ $this, 'register_fields' ] );
		}
	}

	/**
	 * Registers the fields on the settings page for the Campaign Monitor options.
	 *
	 * @return void
	 */
	public function register_fields() {
		$plugin_settings = new Settings();
		$from_names      = $plugin_settings->get_from_names();
		$settings        = new \Fieldmanager_Group(
			[
				'name'           => static::SETTINGS_KEY,
				'children'       => [
					'uuid4'      => new class() extends \Fieldmanager_Hidden {
						/**
						 * Ensure that each group has a unique ID.
						 *
						 * @param mixed $value          Submitted value.
						 * @param array $current_value  The current values.
						 * @return array The sanitized values.
						 */
						public function presave( $value, $current_value = [] ) {
							return $current_value ?: wp_generate_uuid4();
						}
					},
					'label'      => new \Fieldmanager_TextField( __( 'Label', 'wp-newsletter-builder' ) ),
					'image'      => new \Fieldmanager_Media(
						[
							'label'        => __( 'Image', 'wp-newsletter-builder' ),
							'preview_size' => 'full',
						]
					),
					'templates'  => new \Fieldmanager_Checkboxes(
						'Checkboxes',
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
					'from_name'  => new \Fieldmanager_Select(
						__( 'From Name', 'wp-newsletter-builder' ),
						[
							'options' => $from_names,
						]
					),
					'safe_rtb'   => new \Fieldmanager_TextArea(
						[
							'label'    => __( 'SafeRTB Ad Tag', 'wp-newsletter-builder' ),
							'sanitize' => function ( $value ) {
								return $value;
							},
						],
					),
					'ad_tags'    => new \Fieldmanager_Group(
						[
							'label'          => __( 'Tags', 'wp-newsletter-builder' ),
							'children'       => [
								'tag_code' => new \Fieldmanager_TextArea(
									[
										'label'    => __( 'Ad Tag', 'wp-newsletter-builder' ),
										'sanitize' => function ( $value ) {
											return $value;
										},
									],
								),
							],
							'limit'          => 0,
							'add_more_label' => __( 'Add another tag', 'wp-newsletter-builder' ),
						]
					),
					'roadblock'  => new \Fieldmanager_Checkbox(
						[
							'label' => __( 'Enable Ad Roadblock', 'wp-newsletter-builder' ),
						]
					),
					'key_values' => new \Fieldmanager_Group(
						[
							'label'              => __( 'Key/Value Pairs', 'wp-newsletter-builder' ),
							'children'           => [
								'key'   => new \Fieldmanager_TextField(
									[
										'label' => __( 'Key', 'wp-newsletter-builder' ),
									]
								),
								'value' => new \Fieldmanager_TextField(
									[
										'label' => __( 'Value', 'wp-newsletter-builder' ),
									]
								),
							],
							'limit'              => 0,
							'add_more_label'     => __( 'Add another key/value pair', 'wp-newsletter-builder' ),
							'one_label_per_item' => false,
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
	 * @param array $new_value The value being saved.
	 * @return array
	 */
	public function sort_settings_on_save( $new_value ) {
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
	 * @return array
	 */
	public function get_email_types() {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) ) {
			return [];
		}

		return $settings;
	}
}
