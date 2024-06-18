<?php
/**
 * Core image block modifications.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Registers assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_image_scripts(): void {
	wp_register_script(
		'plugin-newsletter-image',
		get_entry_asset_url( 'wp-newsletter-builder-image' ),
		get_asset_dependency_array( 'wp-newsletter-builder-image' ),
		get_asset_version( 'wp-newsletter-builder-image' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-image' );
}
add_action( 'init', __NAMESPACE__ . '\register_image_scripts' );

/**
 * Enqueue block editor assets for image.
 */
function action_enqueue_image_assets(): void {
	$post_type = get_edit_post_type();
	if ( ( 'nb_newsletter' !== $post_type ) && ( 'nb_template' !== $post_type ) ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-image' );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\action_enqueue_image_assets' );
