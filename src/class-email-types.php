<?php
/**
 * Email_Types class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

use Fieldmanager_Field;

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
	public function maybe_register_settings_page(): void {
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
	public function register_fields(): void {
		$plugin_settings = new Settings();
		$from_names      = $plugin_settings->get_from_names();

		$children = [
			'uuid4'     => new class() extends \Fieldmanager_Hidden {
				/**
				 * Ensure that each group has a unique ID.
				 *
				 * @param mixed         $value         Submitted value.
				 * @param array<string> $current_value The current values.
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
				],
			),
			'templates' => new \Fieldmanager_Checkboxes(
				'Templates',
				[
					'datasource' => new \Fieldmanager_Datasource_Post(
						[
							'query_args' => [
								'post_type'      => 'nb_template',
								'posts_per_page' => - 1,
								'orderby'        => 'title',
							],
							'use_ajax'   => false,
						],
					),
				],
			),
			'from_name' => new \Fieldmanager_Select(
				__( 'From Name', 'wp-newsletter-builder' ),
				[
					'options' => $from_names,
				],
			),
		];

		/**
		 * Filters the additional Fieldmanager group children that can be used to configure email types.
		 *
		 * @param Fieldmanager_Field[] $additional_fields Associative array of field names and fields to add to the Email Type settings group.
		 */
		$additional_fields = apply_filters( 'wp_newsletter_builder_email_type_additional_fields', [] );

		if ( [] !== $additional_fields ) {
			$children += $additional_fields;
		}

		$settings = new \Fieldmanager_Group(
			[
				'name'           => static::SETTINGS_KEY,
				'children'       => $children,
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
				'group_is_empty' => fn ( $values ) => ! is_array( $values ) || ( empty( $values['label'] ) && empty( $values['templates'] ) ),
			],
		);

		$settings->activate_submenu_page();
	}

	/**
	 * Sort the email types on save so that they are always alphabetical.
	 *
	 * @param mixed $new_value The value being saved.
	 * @return mixed
	 */
	public function sort_settings_on_save( $new_value ): mixed {
		if ( ! is_array( $new_value ) ) {
			return $new_value;
		}
		usort(
			$new_value,
			function ( $a, $b ) {
				$a_label = is_array( $a ) && is_string( $a['label'] ?? null ) ? $a['label'] : '';
				$b_label = is_array( $b ) && is_string( $b['label'] ?? null ) ? $b['label'] : '';
				return strcasecmp( $a_label, $b_label );
			}
		);

		return $new_value;
	}

	/**
	 * Gets the configured email types.
	 *
	 * Email types without a string `uuid4` are skipped; all other stored fields are returned as-is.
	 *
	 * @TODO: Add caching that works on Pantheon and WordPress VIP.
	 *
	 * @return array<array<mixed>>
	 */
	public static function get_email_types(): array {
		$settings = get_option( static::SETTINGS_KEY );
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return [];
		}

		return array_values(
			array_filter(
				$settings,
				fn ( $email_type ) => is_array( $email_type ) && ! empty( $email_type['uuid4'] ) && is_string( $email_type['uuid4'] ),
			)
		);
	}
}
