<?php
/**
 * Core button block modifications.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Registers assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_button_scripts(): void {
	wp_register_script(
		'plugin-newsletter-button',
		get_entry_asset_url( 'wp-newsletter-builder-button' ),
		get_asset_dependency_array( 'wp-newsletter-builder-button' ),
		get_asset_version( 'wp-newsletter-builder-button' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-button' );
}
add_action( 'init', __NAMESPACE__ . '\register_button_scripts' );

/**
 * Enqueue block editor assets for button.
 */
function action_enqueue_button_assets(): void {
	$post_type = get_edit_post_type();
	if ( ( 'nb_newsletter' !== $post_type ) && ( 'nb_template' !== $post_type ) ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-button' );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\action_enqueue_button_assets' );
