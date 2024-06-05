<?php
/**
 * Adds a separator to a block.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Registers assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_heading_scripts(): void {
	wp_register_script(
		'plugin-newsletter-heading',
		get_entry_asset_url( 'wp-newsletter-builder-heading' ),
		get_asset_dependency_array( 'wp-newsletter-builder-heading' ),
		get_asset_version( 'wp-newsletter-builder-heading' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-heading' );
}
add_action( 'init', __NAMESPACE__ . '\register_heading_scripts' );

/**
 * Enqueue block editor assets for separator.
 */
function action_enqueue_heading_assets(): void {
	$post_type = get_edit_post_type();
	if ( 'nb_newsletter' !== $post_type ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-heading' );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\action_enqueue_heading_assets' );
