<?php
/**
 * Core paragraph block modifications.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Registers assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_paragraph_scripts(): void {
	wp_register_script(
		'plugin-newsletter-paragraph',
		get_entry_asset_url( 'wp-newsletter-builder-paragraph' ),
		get_asset_dependency_array( 'wp-newsletter-builder-paragraph' ),
		get_asset_version( 'wp-newsletter-builder-paragraph' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-paragraph' );
}
add_action( 'init', __NAMESPACE__ . '\register_paragraph_scripts' );

/**
 * Enqueue block editor assets for paragraph.
 */
function action_enqueue_paragraph_assets(): void {
	$post_type = get_edit_post_type();
	if ( ( 'nb_newsletter' !== $post_type ) && ( 'nb_template' !== $post_type ) ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-paragraph' );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\action_enqueue_paragraph_assets' );
